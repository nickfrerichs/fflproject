<?php

class Player_search_model extends CI_Model{
    function __construct(){
        parent::__construct();
        $this->leagueid = $this->session->userdata('league_id');
    }

    protected $leagueid;

    // This is the swiss army knife method for getting a list of league players, works for various needs
    function get_nfl_players($limit = 100000, $start = 0, $nfl_pos = 0, $order_by = array('last_name','asc'),$search='',
            $show_owned = true, $show_inactive = false, $hide_non_lea = true)
    {

        if ($hide_non_lea)
            $pos_list = $this->common_model->league_nfl_position_id_array();
        if (count($pos_list) < 1)
            $pos_list = array(-1);
        if ($show_owned == false)
        {
            $owned = array();
            $data = $this->db->select('player_id')->from('roster')->where('league_id',$this->leagueid)->get()->result();
            foreach($data as $row)
                $owned[] = $row->player_id;
        }
        $this->db->select('SQL_CALC_FOUND_ROWS null as rows',FALSE);
        $this->db->select('player.id as player_id, player.id, player.first_name, player.last_name, player.nfl_position_id')
                ->select('IFNULL(sum(fs_w.points),0) as points',false)
                ->select('nfl_position.short_text as position,')
                ->select('IFNULL(nfl_team.club_id,"NONE") as club_id')
                ->select('team.team_name')
                ->select('TRUNCATE(IFNULL(draft_player_rank.rank,999),2) as draft_rank')
                ->from('player')
                ->join('fantasy_statistic_week as fs_w','fs_w.player_id = player.id and fs_w.year = '.
                        $this->session->userdata('current_year').' and fs_w.league_id = '.$this->leagueid.
                        ' and fs_w.nfl_week_type_id = (select id from nfl_week_type where text_id="'.$this->session->userdata['week_type'].'")','left')
                ->join('nfl_team', 'nfl_team.id = player.nfl_team_id','left')
                ->join('nfl_position', 'nfl_position.id = player.nfl_position_id')
                ->join('roster','roster.player_id = player.id and roster.league_id='.$this->leagueid,'left')
                ->join('team','team.id = roster.team_id','left')
                ->join('draft_player_rank','draft_player_rank.player_id = player.id','left');
               // ->where('fantasy_statistic.year',$this->current_year,'left');
        if ($search != '')
            $this->db->where('(`last_name` like "%'.$search.'%" or `first_name` like "%'.$search.'%")',NULL,FALSE);
        if (($nfl_pos != 0) && (is_numeric($nfl_pos)))
            $this->db->where('nfl_position.id', $nfl_pos);
        if ($hide_non_lea && count($pos_list) > 0)
            $this->db->where_in('nfl_position_id', $pos_list);
        if (!$show_owned)
            $this->db->where_not_in('player.id',$owned);
        $this->db->group_by('player.id')
            ->order_by($order_by[0],$order_by[1])
            ->order_by('last_name','asc')
            ->order_by('first_name','asc')
            ->order_by('player.id','asc');

        if (!$show_inactive)
            $this->db->where('player.active', true);
        $this->db->limit($limit, $start);
        $data = $this->db->get();
        $returndata['count'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $returndata['result'] = $data->result();

        return $returndata;
    }

    // Used by player_stats
    function get_best_week_data($year = 0, $starter="all", $nfl_pos = 0, $limit=10)
    {
        //$starter: all, starter, bench
        //$this->db->select('SQL_CALC_FOUND_ROWS null as rows',FALSE);
        $this->db->select('player.id, player.first_name, player.last_name')
            ->select('fantasy_statistic_week.points, fantasy_statistic_week.year, fantasy_statistic_week.week')
            ->select('team.team_name, owner.first_name as owner_first_name, owner.last_name as owner_last_name')
            ->select('nfl_position.short_text as position')
            ->from('fantasy_statistic_week')
            ->join('player','fantasy_statistic_week.player_id = player.id')
            ->join('nfl_position','nfl_position.id = player.nfl_position_id')
            ->join('team','team.id = fantasy_statistic_week.team_id','left')
            ->join('owner','owner.id = team.owner_id','left')
            ->where('fantasy_statistic_week.league_id',$this->leagueid);
        if($year > 0)
            $this->db->where('fantasy_statistic_week.year',$year);
        if($starter == "starter")
            $this->db->where('fantasy_statistic_week.team_id <>',0);
        if($starter == "bench")
            $this->db->where('fantasy_statistic_week.team_id',0);
        if($nfl_pos >0)
            $this->db->where('player.nfl_position_id',$nfl_pos);
        $this->db->order_by('points','desc')
            ->order_by('year','asc')
            ->order_by('week','asc')
            ->limit($limit);
            return $this->db->get()->result();
    }

    function get_career_data($year = 0, $starter="all", $nfl_pos = 0,$order_by = array('avg_points','desc'),$limit=10)
    {
        //$starter: all, starter, bench
        //$this->db->select('SQL_CALC_FOUND_ROWS null as rows',FALSE);
        $this->db->select('player.id, player.first_name, player.last_name')
            ->select('AVG(fantasy_statistic_week.points) as avg_points, fantasy_statistic_week.year')
            ->select('SUM(fantasy_statistic_week.points) as total_points')
            ->select('nfl_position.short_text as position, count(player.id) as games')
            ->from('fantasy_statistic_week')
            ->join('player','fantasy_statistic_week.player_id = player.id')
            ->join('nfl_position','nfl_position.id = player.nfl_position_id')
            ->where('fantasy_statistic_week.league_id',$this->leagueid);
        if($year > 0)
            $this->db->where('fantasy_statistic_week.year',$year);
        if($starter == "starter")
            $this->db->where('fantasy_statistic_week.team_id <>',0);
        if($starter == "bench")
            $this->db->where('fantasy_statistic_week.team_id',0);
        if($nfl_pos >0)
            $this->db->where('player.nfl_position_id',$nfl_pos);
        $this->db->group_by('player.id')
            ->order_by($order_by[0],$order_by[1])
            ->order_by('player.id')
            ->having('count(player.id) > 3')
            ->limit($limit);
        return $this->db->get()->result();
    }

    function get_league_years()
    {
        return $this->common_model->get_league_years();
    }

    function get_nfl_positions_data($include_all_pos = false)
    {
        if (!$include_all_pos)
        {
            $pos_list = $this->common_model->league_nfl_position_id_array();
        }
        if (count($pos_list) < 1)
            $pos_list = array(-1);
        $this->db->select('nfl_position.id, nfl_position.short_text as text_id, nfl_position.long_text')
                ->from('nfl_position');
        if (!$include_all_pos)
            $this->db->where_in('id', $pos_list);

        $this->db->order_by('type','asc')
                ->order_by('nfl_position.text_id', 'asc');
        $data = $this->db->get();
        return $data->result();
    }

}
