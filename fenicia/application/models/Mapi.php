<?php
 class Mapi extends CI_Model {
    function __construct(){
        parent::__construct(); 
    }
    public function insert($table,$data){
        $this->db->insert($table,$data);
        //echo $this->db->last_query(); die();
        return $this->db->insert_id();
    }
	public function batch_insert($table,$data){
        $this->db->insert_batch($table,$data);
        return 1;
    } 
    public function getDashboardDetails($condition){
        $this->db->select("*,(IF(dashboard_images !='',CONCAT('".base_url()."public/upload_image/dashboard_images/',dashboard_images),'".base_url()."public/upload_image/No_Image_Available.jpg')) as dashboard_images");
        $this->db->where($condition);
        $query=$this->db->get('front_dashboard_images');
        return $query->result_array(); 
    }
    public function getDetails($table,$condition = null){
        if(!empty($condition)){
            $this->db->where($condition);
        }        
        $query=$this->db->get($table);
        return $query->result_array(); 
    }
    public function getRow($table,$condition = null){
         if(!empty($condition)){
            $this->db->where($condition);
        }    
        //$this->db->where($condition);
        $query=$this->db->get($table);
        //echo $this->db->last_query(); die();
        return $query->row_array();
    }
    public function getRow3($table,$condition){
        $this->db->where($condition);
        $query=$this->db->get($table);
        echo $this->db->last_query(); die();
        return $query->row_array();
    }
    public function getRowObject($table,$condition){
        $this->db->where($condition);
        $query=$this->db->get($table);
        return $query->row();
    }
    public function getMemberDetailsRow($condition){
        $this->db->select("mm.*,package_membership_mapping.membership_id,DATE_FORMAT(mm.dob, '%d/%m/%Y') as dob,DATE_FORMAT(mm.doa, '%d/%m/%Y') as doa,(IF(mm.profile_img !='',CONCAT('".base_url()."public/upload_image/profile_photo/',mm.profile_img),'".base_url()."public/upload_image/No_Image_Available.jpg')) as profile_image,api_token.access_token as access_token"); 
        $this->db->join('api_token', 'api_token.member_id = mm.member_id', 'inner');
        $this->db->join('package_membership_mapping', 'package_membership_mapping.member_id = mm.member_id', 'left');
       
        $this->db->where($condition);
        //$this->db->where('package_membership_mapping.status','1');
        $query=$this->db->get('master_member mm');
        //echo $this->db->last_query(); die();
        return $query->result_array(); 
    }
    
    public function getNotificationList($notif_cond){
        //pr($notif_cond);
        $this->db->select("notification.*,(IF(mm.profile_img !='',CONCAT('".base_url()."public/upload_image/profile_photo/',mm.profile_img),'".base_url()."public/upload_image/No_Image_Available.jpg')) as profile_image"); 
        $this->db->join('notification', 'notification.member_id = mm.member_id', 'left');
        $this->db->where($notif_cond);
        $this->db->order_by('notification_id','desc');
        $query=$this->db->get('master_member mm');
        //echo $this->db->last_query(); die();
        return $query->result_array(); 
    }
    public function getMemberData($condition){
        $this->db->select("mm.*,api_token.*"); 
        $this->db->join('api_token', 'api_token.member_id = mm.member_id', 'inner');       
        $this->db->where($condition);
        $query=$this->db->get('master_member mm');
        //echo $this->db->last_query(); die();
        return $query->row_array(); 
    }
    public function getCount($table,$condition){
        $this->db->where($condition);
        $query=$this->db->get($table);
        return $query->num_rows();
    } 
    
	public function delete($table,$condition){
        $this->db->where($condition);  
        $this->db->delete($table); 
        return true;
    }
    public function joinQuery($data,$condition = null,$return_type,$order_by= null,$order_type = 'ASC'){
        //pr($data,0);
        if(array_key_exists('select',$data) && $data['select'] != ""){
            $this->db->select($data['select']);
        }else{
            $this->db->select('*');
        }
        $this->db->from($data['first_table']);

        if(array_key_exists('second_table',$data) && array_key_exists('dependency1',$data) && array_key_exists('join_type1',$data)){
            if($data['second_table'] != "" && $data['dependency1'] != "" && $data['join_type1'] != ""){
                $this->db->join($data['second_table'],$data['dependency1'],$data['join_type1']);
            }
        }
        if(array_key_exists('third_table',$data) && array_key_exists('dependency2',$data) && array_key_exists('join_type2',$data)){
            if($data['third_table'] != "" && $data['dependency2'] != "" && $data['join_type2'] != ""){
                $this->db->join($data['third_table'],$data['dependency2'],$data['join_type2']);
            }
        }
        if(array_key_exists('forth_table',$data) && array_key_exists('dependency3',$data) && array_key_exists('join_type3',$data)){
            if($data['forth_table'] != "" && $data['dependency3'] != "" && $data['join_type3'] != ""){
                $this->db->join($data['forth_table'],$data['dependency3'],$data['join_type3']);
            }
        }
        if(array_key_exists('fifth_table',$data) && array_key_exists('dependency4',$data) && array_key_exists('join_type4',$data)){
            if($data['fifth_table'] != "" && $data['dependency4'] != "" && $data['join_type4'] != ""){
                $this->db->join($data['fifth_table'],$data['dependency4'],$data['join_type4']);
            }
        }
        if(array_key_exists('sixth_table',$data) && array_key_exists('dependency5',$data) && array_key_exists('join_type5',$data)){
            if($data['sixth_table'] != "" && $data['dependency5'] != "" && $data['join_type5'] != ""){
                $this->db->join($data['sixth_table'],$data['dependency5'],$data['join_type5']);
            }
        }
        if(array_key_exists('seventh_table',$data) && array_key_exists('dependency6',$data) && array_key_exists('join_type6',$data)){
            if($data['seventh_table'] != "" && $data['dependency6'] != "" && $data['join_type6'] != ""){
                $this->db->join($data['seventh_table'],$data['dependency6'],$data['join_type6']);
            }
        }
        if(array_key_exists('eighth_table',$data) && array_key_exists('dependency7',$data) && array_key_exists('join_type7',$data)){
            if($data['eighth_table'] != "" && $data['dependency7'] != "" && $data['join_type7'] != ""){
                $this->db->join($data['eighth_table'],$data['dependency7'],$data['join_type7']);
            }
        }
        if(array_key_exists('ninth_table',$data) && array_key_exists('dependency8',$data) && array_key_exists('join_type8',$data)){
            if($data['ninth_table'] != "" && $data['dependency8'] != "" && $data['join_type8'] != ""){
                $this->db->join($data['ninth_table'],$data['dependency8'],$data['join_type8']);
            }
        }
        $this->db->where($condition);
        $this->db->order_by($order_by, $order_type);
        //ORDER BY `menu_rank` ASC
        $query = $this->db->get();
//echo $this->db->last_query();
        if($query->num_rows() > 0){
            if($return_type == 'result'){
                return $query->result_array();
            }elseif($return_type == 'row'){
                return $query->row_array();
            }
        }else{
            return false;
        }
    }
    public function checkUserRegistered($condition){
        $this->db->select('mu.*');
        $this->db->where($condition);
        $query=$this->db->get('master_member mu');
        return $query->row_array(); 
    }
    public function editCheckUserRegistered($condition){
        $this->db->select('mu.*');
        $this->db->where($condition);
        $query=$this->db->get('master_member mu');
        return $query->row_array(); 
    }
    public function update($table,$condition,$data){
        $this->db->where($condition);
        $this->db->update($table,$data);
     //echo $this->db->last_query();exit;
        return 1;
    }
    public function getMembershipData(){
        $result=array();
        $query = "select mp.*,pt.package_type_name,package_price_mapping.package_price_mapping_id,package_price_mapping.price from master_package mp left join package_price_mapping on mp.package_id = package_price_mapping.package_id left join package_type as pt on pt.package_type_id = package_price_mapping.package_type_id where mp.status = '1' order by mp.package_id desc";     
       //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    /*public function getMembershipData($status =null,$condition){
        $result=array();
        if($status != null){
           $query = "select mp.*,pmm.renewal_date,pmm.package_id,pmm.added_from,pmm.buy_on,pt.package_type_name,package_price_mapping.price,pmm.status as package_mapping_status from master_member mu left join package_membership_mapping pmm on pmm.member_id = mu.member_id left join master_package mp on mp.package_id = pmm.package_id left join package_price_mapping on pmm.package_price_id = package_price_mapping.package_price_mapping_id left join package_type as pt on pt.package_type_id = package_price_mapping.package_type_id where pmm.status = '".$status."' and mu.status ='1' ".$condition." order by mu.member_id desc";     
        }
        else{
           $query = "select mp.*,pmm.renewal_date,pmm.package_id,pmm.added_from,pmm.buy_on,pt.package_type_name,package_price_mapping.price,pmm.status as package_mapping_status from master_member mu left join package_membership_mapping pmm on pmm.member_id = mu.member_id left join master_package mp on mp.package_id = pmm.package_id left join package_price_mapping on pmm.package_price_id = package_price_mapping.package_price_mapping_id left join package_type as pt on pt.package_type_id = package_price_mapping.package_type_id where mu.status ='1' ".$condition." order by mu.member_id desc"; 
        }
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }*/
    public function get_package_benefit_list($pkg_id){
        $result=array();
        $query = "select pb.* from package_benefits_mapping pbm left join package_benefits pb on pbm.package_benefit_id = pb.package_benefit_id where pbm.package_id = '".$pkg_id."' and pb.package_benefit_id !='18' order by pb.benefit_name ASC" ;
       //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function get_package_voucher_list($pkg_id){
        $result=array();
        $query = "select pv.* from package_vouchers_mapping pvm left join package_vouchers pv on pvm.package_voucher_id = pv.package_voucher_id where pvm.package_id = '".$pkg_id."' and pv.package_voucher_id !='11' order by pv.voucher_name ASC" ;
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
        $query = "select package_images.*,(IF(images !='',CONCAT('".base_url()."public/upload_image/package_image/',images),'".base_url()."public/upload_image/No_Image_Available.jpg')) as package_images from package_images where package_images.package_id = '".$pkg_id."'" ;
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result = $query1->result_array();
        return $result;
    }
    public function getReservationList($condition = NULL){
        $result=array();
        if(!empty($condition)){
            $query = "select reservation.*,ml.zone_name,reservation.status as resv_status,rt.payment_amount from reservation left join master_member mu on reservation.member_id = mu.member_id left join master_zone ml on reservation.zone_id = ml.zone_id left join reservation_payment_transaction rt on reservation.reservation_id = rt.reservation_id where ".$condition." and mu.is_delete ='0' order by reservation.reservation_id desc";  
        }
        else{
            $query = "select reservation.*,ml.zone_name,reservation.status as resv_status,rt.payment_amount  from reservation left join master_member mu on reservation.member_id = mu.member_id left join master_zone ml on reservation.zone_id = ml.zone_id left join reservation_payment_transaction rt on reservation.reservation_id = rt.reservation_id where mu.is_delete ='0' order by reservation.reservation_id desc";  
        }
     //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    /*public function getEventList($event_flag = NULL,$condition = NULL){
        $result=array();
        if($event_flag !=""){
            $query = "select me.* from master_event me where me.event_flag ='".$event_flag."' and me.status = '1' and me.is_delete = '0' order by me.event_order ASC";
        }
        else{
            if($condition !=""){
                $query = "select me.* from master_event me where me.status = '1' and ".$condition."  and me.is_delete = '0' order by me.event_order ASC";
            }
            else{
                $query = "select me.* from master_event me where me.status = '1' and me.is_delete = '0' order by me.event_order ASC";
            }            
        }
        //echo $query;exit;
        $query  =   $this->db->query($query);
        $result =   $query->result_array();
        return $result;
    }*/
    public function getEventList($event_flag = NULL,$condition = NULL){
        $result=array();
        if($event_flag !=""){
            $query = "select me.* from master_event me where me.event_flag ='".$event_flag."' and me.event_start_date >= CURDATE() and me.status = '1' and me.is_delete = '0' order by me.event_order ASC";
        }
        else{
            if($condition !=""){
                $query = "select me.* from master_event me where me.status = '1' and ".$condition." and me.event_start_date >= CURDATE() and me.is_delete = '0' order by me.event_order ASC";
            }
            else{
                $query = "select me.* from master_event me where me.status = '1' and me.event_start_date >= CURDATE() and me.is_delete = '0' order by me.event_order ASC";
            }            
        }
        //echo $query;exit;
        $query  =   $this->db->query($query);
        $result =   $query->result_array();
        return $result;
    }
     public function getEventListByMonth(){
        $sql ="SELECT *, YEAR(event_start_date) AS YEAR, MONTHNAME(event_start_date) AS MONTH, WEEK(event_start_date) AS WEEK, MINUTE(event_start_date) AS MINUTE FROM  master_event WHERE status = '1' and is_delete = '0' GROUP BY YEAR(event_start_date) order by event_start_date ASC";
        //echo $sql;exit;
        $Query = $this->db->query($sql);
        $rows = $Query->result_array();

        $result =  array(); $i=0; $temp = array();
        foreach ($rows as $row) {
            $this->db->select('MONTHNAME(event_start_date)');       
            $this->db->from('master_event');       
            $this->db->where('YEAR(event_start_date)',$row['YEAR']);
            $this->db->group_by('MONTH(event_start_date)');
            $this->db->order_by('event_start_date','ASC');
            $this->db->where('status','1');
            $query = $this->db->get();
            $temp[$i][$row['YEAR']] = $query->result_array();
            // echo "<pre>";
            // print_r($result);
            // echo "</pre>";
            // die();
            foreach ($temp[$i][$row['YEAR']] as $raw) {
              $this->db->select('*');   
              $this->db->from('master_event');       
              $this->db->where('MONTHNAME(event_start_date)',$raw['MONTHNAME(event_start_date)']);
              $this->db->where('YEAR(event_start_date)',$row['YEAR']);
              $this->db->where('status','1');
              $this->db->order_by('event_start_date','ASC');
              $query = $this->db->get();
              $result[$i][$row['YEAR']][$raw['MONTHNAME(event_start_date)']] = $query->result_array();

            }
            $i++;
        }


        return $result;
     }
    public function getEventImgList($event_id){
        $result=array();
        $query = "select (IF(event_img !='',CONCAT('".base_url()."public/upload_image/event_image/',event_img),'".base_url()."public/upload_image/No_Image_Available.jpg')) as event_img from event_images ei where ei.event_id = '".$event_id."'";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function getPastEventImgList($event_id){
        $result=array();
        $query = "select (IF(images !='',CONCAT('".base_url()."public/upload_image/past_event_images/',images),'".base_url()."public/upload_image/No_Image_Available.jpg')) as images from past_event_images ei where ei.event_id = '".$event_id."'";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function getGalleryDetails($gallery_id = null){
        $result=array();

        //echo $gallery_id;exit;
        if($gallery_id !=""){
            $query  = "select * from master_gallery where gallery_id ='".$gallery_id."' and status = '1' and is_delete = '0' order by gallery_id desc";
        }
        else{
            $query  = "select * from master_gallery where status = '1' and is_delete = '0' order by gallery_id desc";
        }
        //echo $query;exit;
        $query  =   $this->db->query($query);
        $result =   $query->result_array();
        return $result;
    }
    public function getAllGalleryImages($gallery_id,$flag = null){
        $result=array();
        if($flag =="latest"){
            $query = "select gallery_id,(IF(gallery_image !='',CONCAT('".base_url()."public/upload_image/gallery/',gallery_image),'".base_url()."public/upload_image/No_Image_Available.jpg')) as gallery_img from gallery_images where gallery_id = '".$gallery_id."' order by gallery_img_id desc";
        }
        else{
            $query = "select gallery_id,(IF(gallery_image !='',CONCAT('".base_url()."public/upload_image/gallery/',gallery_image),'".base_url()."public/upload_image/No_Image_Available.jpg')) as gallery_img from gallery_images where gallery_id = '".$gallery_id."'";
        }
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function getZoneDetails($condition){
        $this->db->select("*,(IF(zone_image !='',CONCAT('".base_url()."public/upload_image/zone_image/',zone_image),'".base_url()."public/upload_image/No_Image_Available.jpg')) as zone_image");
        $this->db->where($condition);
        $this->db->order_by('display_order','ASC');
        $query=$this->db->get('master_zone');
        return $query->result_array(); 
    }
    public function getTimeDetails($condition){
        $this->db->select("*");
        $this->db->where($condition);
        $query=$this->db->get('time_slot');
        return $query->result_array(); 
    }
    public function getMembershipDetails($member_id){
        $result=array();
        $query = "select mu.*,pmm.membership_id,mp.*,pmm.expiry_date,pmm.package_id,pmm.added_from,pmm.buy_on,pt.package_type_name,package_price_mapping.price,pmm.status as package_mapping_status,CONCAT(mu.title,' ',mu.first_name,' ',ifnull(mu.middle_name,''),' ',mu.last_name) as full_name from master_member mu left join package_membership_mapping pmm on pmm.member_id = mu.member_id left join master_package mp on mp.package_id = pmm.package_id left join package_price_mapping on pmm.package_price_id = package_price_mapping.package_price_mapping_id left join package_type as pt on pt.package_type_id = package_price_mapping.package_type_id where mu.status ='1' and pmm.member_id ='".$member_id."' and pmm.status ='1'"; 
        
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->row_array();
        return $result;
    }

    public function getMembershipPaymentCheck($member_id){
        $result=array();
        $query = "select pmm.* from package_membership_mapping pmm left join package_membership_transaction pmt on pmm.member_id = pmt.member_id where pmm.member_id ='".$member_id."' and pmm.status ='1' and pmt.payment_status ='1'"; 
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->row_array();
        return $result;
    }
}
