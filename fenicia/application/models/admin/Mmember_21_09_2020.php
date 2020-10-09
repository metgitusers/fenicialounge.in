<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mmember extends CI_Model {

	public function get_member_list($status ='0'){
        $result=array();
        $query = "select mu.*,CONCAT(mu.first_name,' ',ifnull(mu.middle_name,''),' ',mu.last_name) as full_name from master_member mu where mu.status = '".$status."'and mu.is_delete = '0' order by mu.member_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
	public function getMemberDetails($condition = null){
        $result=array();
        $query = "select mu.*,CONCAT(mu.first_name,' ',ifnull(mu.middle_name,''),' ',mu.last_name) as full_name from master_member mu where mu.is_delete = '0'".$condition." order by mu.member_id desc";
       //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->row_array();
        return $result;
    }
    public function get_package_type($package_id){
        $result=array();
        $this->db->select('ppm.*,pt.*'); 
        $this->db->join('package_type pt', 'ppm.package_type_id = pt.package_type_id', 'inner');
        $this->db->where('ppm.package_id',$package_id);
        $query=$this->db->get('package_price_mapping ppm');
       //echo $this->db->last_query(); die();
        return $query->result_array(); 

    }
}