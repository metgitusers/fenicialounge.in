<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mreservationcomission extends CI_Model {

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

    public function reservationCommissionFilterSearch($condition = null,$zone_id = null){    
        $result         = array();
        $final_result   = array();
        if(!empty($condition)){
            //$query = "SELECT rev.reservation_date,count(rev.reservation_id) as no_of_reservation,SUM(rpt.payment_amount) as total_price FROM reservation as rev left join reservation_payment_transaction as rpt on rpt.reservation_id = rev.reservation_id where ".$condition." group by rev.reservation_date"; 
            //$query = "SELECT rev.reservation_date,count(rev.reservation_id) as no_of_reservation,zone.zone_name FROM reservation as rev left join master_zone as zone on zone.zone_id = rev.zone_id where ".$condition." group by rev.reservation_date"; 
            $query = "SELECT rev.reservation_date FROM reservation as rev left join master_zone as zone on zone.zone_id = rev.zone_id where ".$condition." group by rev.reservation_date";
        }
        else{
            //$query = "SELECT rev.reservation_date,count(rev.reservation_id) as no_of_reservation,SUM(rpt.payment_amount) as total_price FROM reservation as rev left join reservation_payment_transaction as rpt on rpt.reservation_id = rev.reservation_id group by rev.reservation_date";
            $query = "SELECT rev.reservation_date FROM reservation as rev left join master_zone as zone on zone.zone_id = rev.zone_id group by rev.reservation_date"; 
        }
     //echo $query;exit;
        $query1  = $this->db->query($query);
        $result = $query1->result_array();
        if(!empty($result)){
            foreach($result as $val){                
                if(!empty($val['reservation_date'])){
                    if(!empty($zone_id)){
                        $cond     = "rev.reservation_date ='".$val['reservation_date']."' and rev.zone_id = '".$zone_id."'";
                    }
                    else{
                        $cond     = "rev.reservation_date ='".$val['reservation_date']."'";
                    }  
                    //$query = "SELECT rev.reservation_date,count(rev.reservation_id) as no_of_reservation,SUM(rpt.payment_amount) as total_price FROM reservation as rev left join reservation_payment_transaction as rpt on rpt.reservation_id = rev.reservation_id where ".$condition." group by rev.reservation_date"; 
                    //$query = "SELECT rev.reservation_date,count(rev.reservation_id) as no_of_reservation,zone.zone_name FROM reservation as rev left join master_zone as zone on zone.zone_id = rev.zone_id where ".$condition." group by rev.reservation_date"; 
                    $query_rev = "SELECT rev.reservation_date,rev.zone_id,count(rev.reservation_id) as no_of_reservation,zone.zone_name  FROM reservation as rev left join master_zone as zone on zone.zone_id = rev.zone_id where ".$cond." group by rev.zone_id";
                }
                else{
                    //$query = "SELECT rev.reservation_date,count(rev.reservation_id) as no_of_reservation,SUM(rpt.payment_amount) as total_price FROM reservation as rev left join reservation_payment_transaction as rpt on rpt.reservation_id = rev.reservation_id group by rev.reservation_date";
                    $query_rev = "SELECT rev.reservation_date,rev.zone_id,count(rev.reservation_id) as no_of_reservation,zone.zone_name  FROM reservation as rev left join master_zone as zone on zone.zone_id = rev.zone_id group by rev.zone_id"; 
                }
             //echo $query_rev;
                $query_revn = $this->db->query($query_rev);
                $final_result[]    = $query_revn->result_array();
            }
        } 
        //pr($result2);      
        return $final_result;           
        
    } 
}