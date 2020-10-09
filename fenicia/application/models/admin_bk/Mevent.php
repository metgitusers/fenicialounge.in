<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mevent extends CI_Model {
    
    public function get_events_calendar($status = null){
        $result=array();
        if($status !=""){
            $query = "select me.* from master_event me where me.status = '".$status."' and me.is_delete = '0' order by me.event_id desc";
        }
        else{
            $query = "select me.* from master_event me where me.is_delete = '0' order by me.event_id desc";
        }
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
	public function get_event_list($status = null){
        $result=array();
        if($status !=""){
            $query = "select me.* from master_event me where me.status = '".$status."' and CURDATE() <= me.event_start_date and me.is_delete = '0' order by me.event_start_date desc";
        }
        else{
            $query = "select me.* from master_event me where me.is_delete = '0' and CURDATE() <= me.event_start_date order by me.event_start_date desc";
        }
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function get_filter_event_list($condition,$status = null){
        $result=array();
        $query = "select me.* from master_event me where me.status = '".$status."' ".$condition." and CURDATE() <= me.event_start_date and me.is_delete = '0' order by me.event_id desc";
        
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
	public function get_event_img($event_id){
        $result=array();
        $query = "select ei.* from event_images ei where ei.event_id = '".$event_id."'";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function get_past_event_list($status = null){
        $result=array();
        if($status !=""){
            $query = "select me.* from master_event me where me.status = '".$status."' and CURDATE() >= me.event_start_date and me.is_delete = '0' order by me.event_id desc";
        }
        else{
            $query = "select me.* from master_event me where me.is_delete = '0' and CURDATE() >= event_start_date order by me.event_id desc";
        }
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function get_filter_past_event_list($condition,$status = null){
        $result=array();
        $query = "select me.* from master_event me where me.status = '".$status."' ".$condition." and CURDATE() >= me.event_start_date and me.is_delete = '0' order by me.event_id desc";
        
       //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function getpasteventimage($event_id){

        $query = "select pei.* from past_event_images pei where pei.event_id = '".$event_id."' and pei.is_delete = '0' order by pei.past_event_image_id desc";
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
}