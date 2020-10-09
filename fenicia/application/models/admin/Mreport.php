<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mreport extends CI_Model {

	public function getMembershipDetails($cond = null){
        $result=array();
        $result=array();
   		  $query = "select mu.*,mp.*,pmm.expiry_date,pmm.package_id,pmm.membership_id,pmm.added_from,pmm.buy_on,pt.package_type_name,package_price_mapping.price,pmm.status as package_mapping_status,CONCAT(mu.first_name,' ',ifnull(mu.middle_name,''),' ',mu.last_name) as full_name from master_member mu left join package_membership_mapping pmm on pmm.member_id = mu.member_id inner join master_package mp on mp.package_id = pmm.package_id inner join package_price_mapping on pmm.package_price_id = package_price_mapping.package_price_mapping_id inner join package_type as pt on pt.package_type_id = package_price_mapping.package_type_id ".$cond." order by mu.member_id desc";
       	//$query =	"select mu.*,mp.*,pmm.renewal_date,pmm.package_id,pmm.added_from,pmm.buy_on,pt.package_type_name,package_price_mapping.price,pmm.status as package_mapping_status,CONCAT(mu.title,' ',mu.first_name,' ',ifnull(mu.middle_name,''),' ',mu.last_name) as full_name from master_member mu left join package_membership_mapping pmm on pmm.member_id = mu.member_id left join master_package mp on mp.package_id = pmm.package_id left join package_price_mapping on pmm.package_price_id = package_price_mapping.package_price_mapping_id left join package_type as pt on pt.package_type_id = package_price_mapping.package_type_id ".$cond." order by mu.member_id desc";
       //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
}