<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcommentcard extends CI_Model {
/*
    AUTHOR NAME: Soma Nandi Dutta
    DATE: 13/7/20
    PURPOSE: fetch data from command_card table for listing and details  
*/

public function get_comandcard_list(){
    $from_dt=array();
    $to_dt=array();
    if(!empty($_GET['from_dt'])){
       //$from_dt =date('Y-m-d',strtotime($_GET['from_dt']));
        $dte  = $_GET['from_dt'];
        $dt   = new DateTime();
        $date = $dt->createFromFormat('d/m/Y', $dte);
        $from_dt=$date->format('Y-m-d');
    }
    if(!empty($_GET['to_dt'])){
        $dte  = $_GET['to_dt'];
        $dt   = new DateTime();
        $date = $dt->createFromFormat('d/m/Y', $dte);
        $to_dt=$date->format('Y-m-d'); 
        //$to_dt =date('Y-m-d',strtotime($_GET['to_dt']));
    }
       
    $this->db->select('comment_card.*,master_member.first_name,master_member.last_name,master_member.mobile');
    $this->db->from('comment_card');
     $this->db->join('master_member', 'master_member.member_id = comment_card.member_id', 'left'); 
    if(!empty($from_dt) && !empty($to_dt) ){
    $this->db->where('comment_card.visit_date between "'.$from_dt.'" and "'.$to_dt.'"');
    }
    $this->db->order_by("comment_card.commet_id", "desc");
    $query=$this->db->get();
    //echo $this->db->last_query();die;
    return $query->result_array();
}

public function get_details($table,$condition){
    $this->db->select('comment_card.*,master_member.first_name,master_member.last_name,master_member.mobile');
    $this->db->from('comment_card');
     $this->db->join('master_member', 'master_member.member_id = comment_card.member_id', 'left'); 
    $this->db->where($condition);
    $query=$this->db->get();
    //echo $this->db->last_query();die;
    return $query->row_array();
}

   
}