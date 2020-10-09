<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mreservation extends CI_Model {

	/*public function get_member_list($status ='0'){
        $result=array();
        $query = "select mu.*,CONCAT(mu.title,' ',mu.first_name,' ',ifnull(mu.middle_name,''),' ',mu.last_name) as full_name from master_member mu where mu.status = '".$status."'and mu.is_delete = '0' order by mu.member_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }*/
	public function get_reservation_list($condition = NULL){
        $result=array();
        if(!empty($condition)){
            $query = "select reservation.*,ml.zone_name,reservation.status as resv_status,CONCAT(reservation.first_name,' ',reservation.last_name) as full_name from reservation left join master_zone ml on reservation.zone_id = ml.zone_id where ".$condition." order by reservation.reservation_id desc";  
        }
        else{
            $query = "select reservation.*,ml.zone_name,reservation.status as resv_status,CONCAT(reservation.first_name,' ',reservation.last_name) as full_name from reservation left join master_zone ml on reservation.zone_id = ml.zone_id order by reservation.reservation_id desc";  
        }
     //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function get_reservation_list2($condition = NULL){
        $result=array();
        if(!empty($condition)){
            $query = "select reservation.*,ml.zone_name,reservation.status as resv_status,CONCAT(reservation.first_name,' ',reservation.last_name) as full_name from reservation left join master_zone ml on reservation.zone_id = ml.zone_id where ".$condition." order by reservation.reservation_id desc";  
        }
        else{
            $query = "select reservation.*,ml.zone_name,reservation.status as resv_status,CONCAT(reservation.first_name,' ',reservation.last_name) as full_name from reservation left join master_zone ml on reservation.zone_id = ml.zone_id order by reservation.reservation_id desc";  
        }
     echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }    
    public function getTimeDetails($condition){
        $this->db->select("*");
        $this->db->where($condition);
        $query=$this->db->get('time_slot');
        return $query->result_array(); 
    }
}