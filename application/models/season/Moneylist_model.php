<?php

class Moneylist_model extends MY_Model{

    function __construct(){
        parent::__construct();
    }

    function get_totals()
    {
        return $this->db->select('sum(money_list.amount) as total')
            ->select('team_name, owner.first_name, owner.last_name')
            ->from('money_list')
            ->join('team','team.id = money_list.team_id')
            ->join('owner','owner.id = team.owner_id')
            ->where('money_list.league_id',$this->leagueid)
            ->where('money_list.year',$this->current_year)
            ->group_by('money_list.team_id')
            ->order_by('total','desc')
            ->get()->result();
    }

    function get_moneylist()
    {
        return $this->db->select('money_list.week, money_list.amount, IFNULL(money_list_type.short_text,"Misc") as short_text')
            ->select('team_name, owner.first_name, owner.last_name, money_list.text')
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

    function moneylist_is_active()
    {
        return ($this->db->from('money_list')->where('league_id',$this->leagueid)->where('year',$this->current_year)->get()->num_rows() > 0);
    }
}
?>
