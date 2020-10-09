<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mchangeprofile extends CI_Model {

    /*
    author: soma
    purpose: Get profile by id
    date: 27-9-2019
    */
    
    public function get_profile($user_id){
        $result=array();
        $query = "select mu.*,up.*,date_format(up.dr_licence_expiry,'%d/%m/%Y') dr_licence_expiry,date_format(up.dr_dc_expiry,'%d/%m/%Y') dr_dc_expiry from master_user mu
        inner join user_profile up  on mu.user_id=up.user_id 
        where mu.user_id='".$user_id."'";
        $query1 = $this->db->query($query);
        $result=$query1->row_array();
        //echo $this->db->last_query();die;
        return $result;
    }  
   
    /*
    author: soma
    purpose: Update profile
    date: 27-9-2019
    */

    public function update_profile($data,$user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('user_profile', $data);
        return true;
    }

   




}