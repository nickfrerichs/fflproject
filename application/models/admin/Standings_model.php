<?php

class Standings_model extends MY_Model{


    function standings_notation_defs($year = 0)
    {
        if ($year == 0)
            $year = $this->current_year;
        $y = $this->standings_notation_def_year($year);

        return $this->db->select('id,year,symbol,text')->from('standings_notation_def')
                ->where('league_id',$this->leagueid)->where('year <=',$year)->get()->result();
    }

    function standings_notation_def_year($year = 0)
    {
        if ($year == 0)
            $year = $this->current_year;
        $data = $this->db->select('max(year) as y')->from('standings_notation_def')->where('league_id',$this->leagueid)
                ->where('year <=',$year)->get()->row();
        if (count($data) == 0)
            return 0;
        return $data->y;
    }

    function add_notation_def($text,$symbol)
    {
        // I think I want to always store using the current year.  If defs already exist but where defined in previous years
        // they will be saved, but no longer used for the current year because of the logic in standings_notation_defs.

        $data = array('league_id'=>$this->leagueid,
                      'year'=>$this->current_year,
                      'symbol'=>$symbol,
                      'text'=>$text);
        $this->db->insert('standings_notation_def',$data);
    }

    function delete_notation_def($id)
    {
        $this->db->where("id",$id)->delete('standings_notation_def');
    }

    function get_league_team_data()
    {
        return $this->db->select('team.team_name,owner.first_name,owner.last_name, team.id as team_id')
            ->select('standings_notation_team.standings_notation_def_id as notation_id, ifnull(standings_notation_def.symbol,"none") as notation_symbol',false)
            ->select('standings_notation_def.text as notation_text')
            ->from('team')->join('owner','owner.id = team.owner_id')
            ->join('standings_notation_team','standings_notation_team.team_id = team.id and standings_notation_team.year = '.$this->current_year,'left')
            ->join('standings_notation_def','standings_notation_def.id = standings_notation_team.standings_notation_def_id','left')
            ->where('team.active',1)->where('team.league_id',$this->leagueid)
            ->order_by('team_name','asc')
            ->get()->result();
    }

    function set_team_notation($teamid, $notationid)
    {
        if ($notationid == 0)
        {
            $this->db->where('team_id',$teamid)->where('year',$this->current_year)->delete('standings_notation_team');
            return;
        }

        $count = $this->db->from('standings_notation_team')->where('team_id',$teamid)->where('year',$this->current_year)->count_all_results();
        $data = array('team_id' => $teamid,'year' => $this->current_year, 'standings_notation_def_id' => $notationid);
        if($count == 0)
        {
            $this->db->insert('standings_notation_team',$data);
        }
        else
        {
            $this->db->where('team_id',$teamid)->where('year',$this->current_year);
            $this->db->update('standings_notation_team',$data);
        }
    }

}

?>
