<?php
 class Mcommon extends CI_Model {
    function __construct(){
        parent::__construct();
    }
    public function insert($table,$data){
        $this->db->insert($table,$data);
       //echo $this->db->last_query(); exit();
        return $this->db->insert_id();
    }
    public function batch_insert($table,$data){
        $this->db->insert_batch($table,$data); 
        return 1; 
    }
    /*
    author: soma
    purpose: multiple roeupdate
    date: 25-9-2019
    */
    public function batch_update($table,$data,$condition){
        $this->db->update_batch($table,$data,$condition); 
     // echo $this->db->last_query();

        return 1; 
    }

   
    public function getDetails($table,$condition){
        $this->db->where($condition);
        $query=$this->db->get($table);
        //echo $this->db->last_query();
        return $query->result_array(); 
    }
     
    public function getNumRows($table,$condition){
        $this->db->where($condition);
        $query=$this->db->get($table);
      //echo $this->db->last_query(); exit();
        $res = array();
        
        return $query->num_rows();            
    }
    public function getRow($table,$condition){
        $this->db->where($condition);
        $query=$this->db->get($table);
      //echo $this->db->last_query(); exit();
        return $query->row_array();
    } 
    public function getRow2($table,$condition){
        $this->db->where($condition);
        $query=$this->db->get($table);
       echo $this->db->last_query(); exit();
        return $query->row_array();
    } 
    public function checkUser($table,$condition){
        $this->db->where($condition);
        $query=$this->db->get($table);
        return $query->row_array(); 
    } 
    public function update($table,$condition,$data){
        $this->db->where($condition);
        $this->db->update($table,$data);
  //echo $this->db->last_query();
        return 1;
    }
    public function delete($table,$condition){ 
        $this->db->where($condition);
        $this->db->delete($table);
        return 1;
    }

    public function getFullDetails($table){
        $query=$this->db->get($table);
        return $query->result_array();
    }

    public function user_check($table,$email, $password) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where(array('email' => $email, 'password' => md5($password)));
        $query = $this->db->get();
        return $query->row_array();
    }
     public function getDetailsFiltered($table,$params){
         $this->db->from($table);
        if(!empty($params['date_min']))
            {
                $this->db->where('date_of_creation >', $params['date_min']);
            }
            if(!empty($params['date_max']))
            {
                $stop_date = date('Y-m-d', strtotime($params['date_max'].' +1 day'));

                $this->db->where('date_of_creation <=', $stop_date);
            }
        $query=$this->db->get();
        return $query->result_array(); 
    }

    ////get transaction data.................................
     public function getTransaction($table,$condition){
        $this->db->where($condition);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(10, 0);
        $query=$this->db->get($table);
        return $query->result_array(); 
    }


    ////////old transaction data////////////////////////////////////////////////////////////
     public function getOldTransaction($table,$membership_id) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where("membership_id = '".$membership_id."'");
        $this->db->where("redeem_type = 1");
        //$this->db->where("DATEDIFF(NOW(), date_of_creation)>730");  
        $this->db->where("DATEDIFF(NOW(), date_of_creation)>30");  
        $query = $this->db->get();
        return $query->result_array();
    }

/////////////////////////////////date range last balance////////////////////////////////
     public function getDateLastBalance($table,$membership_id) {
        $present_year=date('Y');
        $last_year=$present_year-2;
        $last_date=$last_year.'-07-01 00:00:00';
        //$last_date='2019-01-01 00:00:00';
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where("membership_id = '".$membership_id."'");
        //$this->db->where("redeem_type = 1");
        //$this->db->where("DATEDIFF(NOW(), date_of_creation)>730");  
        $this->db->where("date_of_creation < ",$last_date);  
        $this->db->where("present_balance != ''");
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1, 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_parent_category(){
		$this->db->select('*');
        $this->db->from('categories'); 	
        //$this->db->where('parent_id=0');
        $query=$this->db->get();
        return $query->result_array();
    }
    public function get_deals_product(){
		$this->db->select('*');
        $this->db->from('product'); 	
        $this->db->where('offer_rate !=','');
        $this->db->or_where('offer_rate !=',NULL);
        $this->db->or_where('offer_rate >',0);
        $query=$this->db->get();
        return $query->result_array();
    }
    public function get_all_category(){
		$this->db->select('*');
        $this->db->from('categories'); 	
        //$this->db->where('parent_id=0');
        $this->db->where('category_status',1);
        $query=$this->db->get();
        return $query->result_array();
    }
    public function get_all_brands(){
		$this->db->select('*');
        $this->db->from('brand'); 	
        //$this->db->where('parent_id=0');
        $this->db->where('brand_status',1);
        $query=$this->db->get();
        return $query->result_array();
    }

    public function get_all_post_codes(){
		$this->db->select('*');
        $this->db->from('post_code'); 	
        //$this->db->where('parent_id=0');
        $this->db->where('post_code_status',1);
        $query=$this->db->get();
        return $query->result_array();
    }

    public function get_all_vendors(){
		$this->db->select('*');
        $this->db->from('admins'); 	
        //$this->db->where('parent_id=0');
        $this->db->where('status',1);
        $this->db->where('role_id',3);
        $query=$this->db->get();
        return $query->result_array();
    }
    
    public function get_all_rates(){
		$this->db->select('*');
        $this->db->from('rate'); 
        $this->db->where('rate_id',1);
        $query=$this->db->get();
        return $query->row_array();
    }
    
    public function get_all_attr_value(){
		$this->db->select('*');
        $this->db->from('attribute_mapping'); 
        $this->db->where('attribute_id',1);
        $query=$this->db->get();
        return $query->result_array();
    }
    
    function check_unique_user_email($id = '', $email) {
        $this->db->where('email', $email);
        if($id) {
            $this->db->where_not_in('user_id', $id);
        }
        return $this->db->get('users')->num_rows();
    }

    function check_unique_admin_email($id = '', $email) {
        $this->db->where('email', $email);
        if($id) {
            $this->db->where_not_in('user_id', $id);
        }
        return $this->db->get('users')->num_rows();
    }

//////////////////////////////////////// Permition ///////////////////////////////////////

public function get_master_menus($role_id){
    $this->db->select('wh_menu_master.wh_menu_master_id,wh_menu_master.menu_name,wh_menu_master.menu_code,wh_menu_master.controller_name,wh_menu_master.manu_parent_id');
    $this->db->from('wh_menu_master');
    $this->db->join('wh_role_permission', 'wh_menu_master.wh_menu_master_id = wh_role_permission.wh_menu_master_id', 'inner');
    $this->db->where("role_id",$role_id);
    // $this->db->where("add_flag !=",0);
    // $this->db->where("edit_flag !=",0);
    // $this->db->where("delete_flag !=",0);
    // $this->db->where("download_flag !=",0);
    $this->db->where("manu_parent_id",1);
    $this->db->order_by('wh_menu_master.menu_order', 'ASC');
    $query=$this->db->get();
    //echo $this->db->last_query();die;
    return $query->result_array(); 
}
public function get_storage_menus($role_id){
    $this->db->select('wh_menu_master.wh_menu_master_id,wh_menu_master.menu_name,wh_menu_master.menu_code,wh_menu_master.controller_name,wh_menu_master.manu_parent_id');
    $this->db->from('wh_menu_master');
    $this->db->join('wh_role_permission', 'wh_menu_master.wh_menu_master_id = wh_role_permission.wh_menu_master_id', 'inner');
    $this->db->where("role_id",$role_id);
    $this->db->where("add_flag !=",0);
    $this->db->where("edit_flag !=",0);
    $this->db->where("delete_flag !=",0);
    $this->db->where("download_flag !=",0);
    $this->db->where("manu_parent_id",2);
    $this->db->order_by('wh_menu_master.menu_order', 'ASC');
    $query=$this->db->get();
    //echo $this->db->last_query();die;
    return $query->result_array(); 
}

public function get_page_permition($controller,$role_id){
    $this->db->select('wh_role_permission.add_flag,wh_role_permission.edit_flag,wh_role_permission.delete_flag,wh_role_permission.download_flag');
    $this->db->from('wh_role_permission');
    $this->db->join('wh_menu_master', 'wh_menu_master.wh_menu_master_id = wh_role_permission.wh_menu_master_id', 'inner');
    $this->db->where("wh_menu_master.controller_name",$controller);
    $this->db->where("role_id",$role_id);
    $query=$this->db->get();
    //echo $this->db->last_query();die;
    return $query->row_array(); 
}

public function get_rate(){
    $this->db->select('*');
    $this->db->from('rate');
    //$this->db->where("rate_id",1);
    $query=$this->db->get();
    //echo $this->db->last_query();die;
    return $query->row_array(); 
}

    public function check_recovery_key($recovery_key)
    {
        $this->db->select('member_id');
        $this->db->where('recovery_key',$recovery_key);
        $query = $this->db->get('master_member');
        //echo $this->db->last_query(); die();
        if ($query->num_rows() > 0){
            return $query->row_array();
        }
        else{
            return false;
        }
    }

    public function getorderDetails($order_id){
        $this->db->select('order_details_id,order_details_tbl.user_id,order_details_tbl.product_id,order_details_tbl.prouct_attribute_id,order_details_tbl.quantity,order_details_tbl.attribute_price, concat("'.base_url("uploads/product_images/").'",image_path) as file_path'); 
        $this->db->join('product', 'product.product_id = order_details_tbl.product_id', 'inner');
        $this->db->join('product_image', 'product_image.product_id = product.product_id', 'inner');
        $this->db->where('order_id',$order_id); 
        $this->db->group_by('order_details_id'); 
        $query=$this->db->get('order_details_tbl');
        //echo $this->db->last_query(); die();
        return $query->result_array(); 
    }

    public function get_details($product_id){
		$this->db->select('*');
		$this->db->from('product');
		$this->db->where('product_id',$product_id);
		$query=$this->db->get();
		return $query->row_array();
    }

    public function get_all_cart_product_attribute_details($product_attribute_id){
		$this->db->select('product_attribute.price,attribute_mapping.attribute_value');
        $this->db->from('product_attribute');
        $this->db->join('attribute_mapping', 'attribute_mapping.attribute_mapping_id = product_attribute.attribute_mapping_id', 'inner');
		$this->db->where('product_attribute_id',$product_attribute_id);
        $query=$this->db->get();
        //echo $this->db->last_query();die;
		return $query->row_array();
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
//echo $this->db->last_query();exit;
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
    /*
    author: Sreela
    purpose: Get ALL menu details list order by  menu_rank
    date: 26-9-2019
    */
    public function getAllMenuList($table,$condition = array()){
        $this->db->select('*');
        if(!empty($condition)){
            $this->db->where($condition);
        }        
        $this->db->order_by('menu_rank', 'ASC');
        $query=$this->db->get($table);
        //echo $this->db->last_query();
        return $query->result_array();
    }
    public function getMenuDependingpermission($condition){
        $this->db->select('master_menu.parent_id,master_menu.menu_link,master_menu.menu_icon,user_permission.permission_id,user_permission.menu_id,user_permission.menu_name,user_permission.add_flag,user_permission.edit_flag,user_permission.delete_flag,user_permission.download_flag'); 
        $this->db->join('user_permission', 'user_permission.menu_id = master_menu.menu_id', 'inner');
        $this->db->where($condition);         
        //$this->db->group_by('master_menu.menu_id');
        $this->db->order_by('menu_rank', 'ASC');
        $query=$this->db->get('master_menu');
        //echo $this->db->last_query(); die();
        return $query->row_array(); 
    }
    
    public function getLogDetails(){
        $this->db->select('*'); 
        $this->db->from('master_log'); 
        $this->db->order_by('log_id', 'DESC');
        $query = $this->db->get();
        //echo $this->db->last_query(); die();
        $result = $query->result_array(); 
        //pr($result);
        return $result;
    }
    public function getInquiryDetails(){
        $this->db->select('inquiry.*,master_event.event_name'); 
        $this->db->from('inquiry');
        $this->db->join('master_event','inquiry.event_id = master_event.event_id');
        $this->db->where('inquiry.is_delete','0');
        $this->db->order_by('inquiry_id', 'DESC');
        $this->db->where('master_event.status','1');
        $this->db->where('master_event.is_delete','0');

        $query = $this->db->get();
        //echo $this->db->last_query(); die();
        $result = $query->result_array(); 
        //pr($result);
        return $result;
    }
    public function getNotificationList($condition =''){

        $this->db->select('*'); 
        $this->db->from('notification'); 
        if(!empty($condition)){
            $this->db->where($condition);
        }
        $this->db->order_by('notification_id', 'DESC');
        $query = $this->db->get();
       //echo $this->db->last_query(); exit;
        $result = $query->result_array(); 
        //pr($result);
        return $result;
    }
    public function get_blocking_list($cond){
        $this->db->select('zone_blocking.blocking_date'); 
        $this->db->join('master_zone', 'master_zone.zone_id = zone_blocking.zone_id', 'left');
        $this->db->where($cond); 
        $this->db->group_by('zone_blocking.blocking_date'); 
        $query=$this->db->get('zone_blocking');
       //echo $this->db->last_query(); die();
        $rows = $query->result_array(); 
        //pr($rows);
        $result =  array(); $i=0; $temp = array();
        foreach ($rows as $row) {
            $this->db->select('zone_blocking.blocking_date,zone_blocking.blocking_time,master_zone.zone_name'); 
            $this->db->join('master_zone', 'master_zone.zone_id = zone_blocking.zone_id', 'left');
            $this->db->where($cond); 
            $this->db->where('zone_blocking.blocking_date',$row['blocking_date']);
            $query=$this->db->get('zone_blocking');
           //echo $this->db->last_query(); die();
           $result[$i][$row['blocking_date']] = $query->result_array(); 
        }
        $i++;
    
        return $result;
    }
    /*public function get_blocking_list($cond){
        $sql ="SELECT zone_blocking.*,master_zone.zone_name, master_zone.club_zone_name, YEAR(zone_blocking.blocking_date) AS YEAR, MONTHNAME(zone_blocking.blocking_date) AS MONTH, WEEK(zone_blocking.blocking_date) AS WEEK, MINUTE(zone_blocking.blocking_date) AS MINUTE FROM  zone_blocking left join master_zone on zone_blocking.zone_id = master_zone.zone_id GROUP BY YEAR(zone_blocking.blocking_date) ";
        echo $sql;exit;
        $Query = $this->db->query($sql);
        $rows = $Query->result_array();

       
    }*/
}


