<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mzone extends CI_Model {

	public function get_zone_list($status ='0'){
        $result=array();
        $query = "select zone.* from master_zone zone where zone.status = '".$status."'and zone.is_delete = '0' order by zone.display_order asc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function get_zone_club_list($status ='0'){
    	$result=array();
        $query = "select zone.club_zone_name from master_zone zone where zone.status = '".$status."'and zone.is_delete = '0' group by `club_zone_name` order by zone.display_order asc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
}