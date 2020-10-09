<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mmember extends CI_Model {

	public function get_member_list($status ='0',$user_type=null,$start_date=null,$end_date=null){
        $result=array();
        /** added by ishani on 09.08.2020 **/
       
        
        $this->db->select("master_member.*,CONCAT(master_member.first_name,' ',master_member.last_name) as full_name"); 

        $this->db->where("status",$status);
        $this->db->where("is_delete","0");
        if($user_type!=null)
        {
            if($user_type=="App")
            {
                $this->db->where("added_form","front");
            }
            if($user_type=="Web")
            {
                $this->db->where("added_form != ","front");
            }
        }

        if(!empty($start_date)){
            if($start_date != '')
            {
                $start_date = str_replace('/', '-', $start_date);
                 $start_date=date('Y-m-d',strtotime($start_date));
                
            }
        }
        if(!empty($end_date)){
            if($end_date != '')
            {
                $end_date = str_replace('/', '-', $end_date);
                $end_date=date('Y-m-d',strtotime($end_date.' + 1 day'));
                //$end_date=date('Y-m-d',strtotime($end_date));
                  
            }
        }
         if(!empty($start_date) && !empty($end_date) ){
           //$this->db->where('reservation_date between "'.$start_dt.'" and "'.$end_dt.'"');
           $this->db->where('master_member.created_ts between "'.$start_date.'" and "'.$end_date.'"');
        }
         $this->db->order_by("master_member.member_id","DESC");
        $query=$this->db->get("master_member");
       // echo $this->db->last_query(); die;
    /**...............................................................................**/
    //$query = "select mu.*,CONCAT(mu.first_name,' ',ifnull(mu.middle_name,''),' ',mu.last_name) as full_name from master_member mu where mu.status = '".$status."'and mu.is_delete = '0' order by mu.member_id desc";
        //echo $query;exit;
       // $query1 = $this->db->query($query);
        
        $result=$query->result_array();
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