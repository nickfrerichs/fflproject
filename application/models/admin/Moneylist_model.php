<?php

class Moneylist_model extends MY_Model
{

    function get_league_teams_data()
    {
        return $this->db->select('team.id as team_id, team.owner_id, team.team_name')
            ->select('owner.first_name, owner.last_name')
            ->from('team')
            ->join ('owner','owner.id = team.owner_id')
            ->where('team.active = 1')->where('league_id',$this->leagueid)->get()->result();
    }

    function get_types()
    {
        return $this->db->select('id, short_text')->from('money_list_type')->get()->result();
    }

    function get_num_weeks()
    {
        return $this->db->select('count(distinct(week)) as weeks')->from('schedule')
            ->where('league_id',$this->leagueid)->where('year',$this->current_year)->get()->row()->weeks;
    }

    function add_entry($teamid, $week, $amount, $typeid, $text="")
    {
        $data = array('league_id' => $this->leagueid,
                      'amount' => $amount,
                      'year' => $this->current_year,
                      'week' => $week,
                      'team_id' => $teamid,
                      'type_id'=> $typeid,
                      'text' => $text);
        $this->db->insert('money_list',$data);
    }

    function get_moneylist()
    {
        return $this->db->select('money_list.week, money_list.amount, IFNULL(money_list_type.short_text,"Misc") as short_text')
            ->select('money_list.text, team_name, owner.first_name, owner.last_name')
            ->select('schedule_result.team_score')
            ->from('money_list')
            ->join('money_list_type','money_list_type.id = money_list.type_id','left')
            ->join('team','team.id = money_list.team_id')
            ->join('owner','owner.id = team.owner_id')
            ->join('schedule_result','schedule_result.team_id = money_list.team_id and schedule_result.year = money_list.year '
                    .'and schedule_result.week = money_list.week','left')
            ->where('money_list.league_id',$this->leagueid)
            ->where('money_list.year',$this->current_year)
            ->order_by('week','asc')
            ->get()->result();
    }

}
