<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mrequestforphotograph extends CI_Model {

	public function get_request_for_photograph_list(){
        $result=array();
        $query = "select rfp.*,mm.profile_img from request_for_photograph rfp left join master_member mm on mm.member_id = rfp.member_id order by request_for_photograph_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
}