<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcontactinfo extends CI_Model {
/*
    AUTHOR NAME: Soma Nandi Dutta
    DATE: 17/7/20
    PURPOSE: fetch data from contact_info table for listing and update  
*/
public function getContactinfo(){
    //$sql = "select * from contact_info where status=1 limit 1";
    $sql = "select * from contact_info limit 1";
    $query = $this->db->query($sql);
    $result = $query->row_array();
    //echo $this->db->last_query();die;
    return $result; 
} 


   
}