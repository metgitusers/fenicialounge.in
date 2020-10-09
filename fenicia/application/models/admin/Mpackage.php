<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mpackage extends CI_Model {

	public function get_package_list($status ='0'){
        $result=array();
        $query = "select pm.* from master_package pm where pm.status = '".$status."'and pm.is_delete = '0' order by pm.package_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function get_package_benefit_list($pkg_id){
        $result=array();
        $query = "select pb.* from package_benefits_mapping pbm left join package_benefits pb on pbm.package_benefit_id = pb.package_benefit_id where pbm.package_id = '".$pkg_id."'" ;
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function get_package_voucher_list($pkg_id){
        $result=array();
        $query = "select pv.* from package_vouchers_mapping pvm left join package_vouchers pv on pvm.package_voucher_id = pv.package_voucher_id where pvm.package_id = '".$pkg_id."'" ;
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result = $query1->result_array();
        return $result;
    }
    public function get_package_price_list($pkg_id){
        $result=array();
        $query = "select pt.*,ppm.* from package_price_mapping ppm left join package_type pt on ppm.package_type_id = pt.package_type_id where ppm.package_id = '".$pkg_id."'" ;
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result = $query1->result_array();
        return $result;
    }
    public function get_package_image_list($pkg_id){
        $result=array();
        $query = "select package_images.* from package_images where package_images.package_id = '".$pkg_id."'" ;
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result = $query1->result_array();
        return $result;
    }    
	public function get_pck_member_list($pck){
        $result=array();
        $query = "select mu.*,pck.package_name,
        CONCAT(mu.first_name,' ',ifnull(mu.middle_name,''),' ',mu.last_name) as full_name from master_member mu left join master_package pck on mu.member_type = pck.package_id
        where mu.member_type = '".$pck."' and mu.status ='1' and mu.is_delete = '0' order by mu.member_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function all_package_benefit_list($status ='0'){
        $result=array();
        $query = "select * from package_benefits where status = '".$status."' AND is_delete ='0' order by package_benefit_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function all_package_voucher_list($status ='0'){
        $result=array();
        $query = "select * from package_vouchers where status = '".$status."' AND is_delete ='0' order by package_voucher_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function getPackageDataByImgId($image_id){
        $result=array();
        $package_name ='';
        $query = "select master_package.package_name from package_images left join master_package on master_package.package_id = package_images.package_id where package_images.package_img_id = '".$image_id."'";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->row_array();
        if(!empty($result)){
            $package_name   = $result['package_name'];
        }
        return $package_name;
    }   
}