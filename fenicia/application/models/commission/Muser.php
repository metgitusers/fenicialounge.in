<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Muser extends CI_Model {
    public function submit_login_form($data)
    {
        $num = '';
        $query = array();

        $this->db->select('*');
        $this->db->where('email',$data['email']);
        $this->db->where('password',$data['password']);
        //$this->db->where('role_id !=',2);
        $query = $this->db->get('commission_user');
        //echo $this->db->last_query();exit;
        $num = $query->num_rows();

        $result = $query->row_array();

        //return $result;

        if($num == 1)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }
    public function get_user_list($status ='0'){
        $result=array();
        $query = "select user.*, role.role_name,CONCAT(first_name,' ',ifnull(middle_name,''),' ',last_name) as full_name from user inner join master_role as role on user.role_id = role.role_id where user.status = '".$status."'and user.is_delete = '0' and user.role_id != '1' order by user.user_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    
    public function check_email_exist($email,$user_id){

        $this->db->select('user_id');
        $this->db->from('user');
        $this->db->where('email', $email);
        $this->db->where('user_id !=', $user_id);
        $query = $this->db->get();
        //echo $this->db->last_query();exit;
        $num = $query->num_rows();
        //echo "hi".$num;exit;
        if ($num > 0) {
            return false;
        } else {
            return true;
        }
    }
    public function check_mobile_exist($mobile,$user_id){

        $this->db->select('user_id');
        $this->db->from('user');
        $this->db->where('mobile', $mobile);
        $this->db->where('user_id !=', $user_id);
        $query = $this->db->get();
        $num = $query->num_rows();
        if ($num > 0) {
            return false;
        } else {
            return true;
        }
    }

}