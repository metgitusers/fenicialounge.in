<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mlog extends CI_Model {

	public function get_log_list($status){
        $result=array();
        $query = "select log.*,CONCAT(user.first_name,' ',ifnull(user.middle_name,''),' ',user.last_name) as full_name from log left join user on user.user_id = log.action_by where log.status = '".$status."' order by log.log_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function getLogListById($id = null,$type=null){
        $result=array();
        $log_type="";
        if(!empty($type))
        {
           $log_type=" and log.type='".$type."'";
        }
        if(!empty($id)){
        	$query = "select log.*,CONCAT(user.first_name,' ',ifnull(user.middle_name,''),' ',user.last_name) as full_name from log left join user on user.user_id = log.action_by where log.id = '".$id."'".$log_type." order by log.log_id desc";
        }
        else{
        	$query = "select log.*,CONCAT(user.first_name,' ',ifnull(user.middle_name,''),' ',user.last_name) as full_name from log left join user on user.user_id = log.action_by order by log.log_id desc";
        }
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
}