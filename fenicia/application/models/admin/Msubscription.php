<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Msubscription extends CI_Model {

	public function get_package_list($status ='0'){
        $result=array();
        $query = "select mpm.* from membership_package_master mpm
        where mpm.status = '".$status."'and mpm.is_delete = '0' order by mpm.package_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
	public function get_pck_member_list($pck){
        $result=array();
        $query = "select mu.*,pck.package_name,
        CONCAT(mu.first_name,' ',ifnull(mu.middle_name,''),' ',mu.last_name) as full_name from master_member mu left join membership_package_master pck on mu.member_type = pck.package_id
        where mu.member_type = '".$pck."' and mu.status ='1' and mu.is_delete = '0' order by mu.member_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
}