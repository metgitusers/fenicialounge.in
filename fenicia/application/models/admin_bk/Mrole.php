<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mrole extends CI_Model {

	public function get_role_list($status ='0'){
        $result=array();
        $query = "select role.* from master_role role where role.status = '".$status."'and role.is_delete = '0' order by role.role_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
}