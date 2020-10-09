<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('test_method'))
{
    function registration_mail($params){
       $params['config']=email_settings();
       sendmail($params);
       return 1;
    } 
    function forgotpassword_mail($params){
       $params['config']=email_settings();
       sendmail($params);
       return 1; 
    }
    function driver_agreement($params, $attach){
       $params['config']=email_settings();
       sendmail($params, $attach);
       return 1;
    } 

    ////////////fn for send attachment in email/////////////////////////////

     function attachment_mail($params, $attach){
       $params['config']=email_settings();
       sendmail($params, $attach);
       return 1;
    } 

    ////////////fn for send attachment in email/////////////////////////////

    function email_settings(){
      	$config['protocol']    = 'smtp';
        $config['smtp_host']    = 'mail.met-technologies.com';
        $config['smtp_port']    = '25';        
        $config['smtp_user']    = 'developer.net@met-technologies.com';
        $config['smtp_pass']    = 'Dot123@#$%';
        $config['charset']    = 'utf-8';
        $config['newline']    = "\r\n";
        $config['mailtype'] = 'html'; // or html
        $config['validation'] = TRUE; // bool whether to validate email or not     
        return $config; 
    } 
    // function sendmail($params){
    // 	  $obj =get_object();
    // 	  $obj->load->library('email');
    //     $obj->email->initialize($params['config']);
    //     $obj->email->from('developer.net@met-technologies.com',$params['name']); 
    //     $obj->email->to($params['to']); 
    //     $obj->email->subject($params['subject']);
    //     $obj->email->message($params['message']);  
    //     $obj->email->set_crlf( "\r\n" );
    //     return $obj->email->send();
    // }
    function sendmail($data,$attach=''){
      $obj =get_object();
      $obj->load->library('email');
      //print_r($data);die;
      $config['protocol']      = 'smtp';
      $config['smtp_host']     = 'ssl://mail.fitser.com';
      $config['smtp_port']     = '465';  
      //$config['smtp_user']     = 'test123@fitser.com';
      //$config['smtp_pass']     = 'Test123@';
      $config['smtp_user']     = 'clubfenicia@fenicialounge.in';
      $config['smtp_pass']     = 'Club123@';
      $config['charset']     = 'utf-8';
      $config['newline']     = "\r\n";
      $config['mailtype']  = 'html';
      $config['validation']  = TRUE;   

      $obj->email->initialize($config);

      
      if($attach!=''){
        $obj->email->attach($attach);
      }

      $obj->email->set_crlf( "\r\n" );

      $obj->email->from('clubfenicia@fenicialounge.in', 'Club Fenicia');
      $obj->email->to($data['to']); 

      $obj->email->subject($data['subject']);
      $obj->email->message($data['message']);  

      $obj->email->send();
              //echo $obj->email->print_debugger(); die; 
      return true;    
    }
    function getRandomString($length = 6) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $string = '';

      for ($i = 0; $i < $length; $i++) {
          $string .= $characters[mt_rand(0, strlen($characters) - 1)];
      }

      return $string;
    }
    function get_object(){
      $obj =& get_instance();
      return $obj;
    }

    //getRow
    function getRow($table, $condition){
        $obj =& get_instance();
        $obj->db->where($condition);
        $query=$obj->db->get($table);
        return $query->row_array();
    } 
    /*
      author: SREELA
      date: 21-9-2019
    */
    if (!function_exists('pr')) {
        function pr($arr,$e=1) {
            if(is_array($arr)) {
                echo "<pre>";
                print_r($arr);
                echo "</pre>";
            } else {
                echo "<br>Not an array...<br>";
                echo "<pre>";
                var_dump($arr);
                echo "</pre>";
            }
            if($e==1)
                exit();
            else
                echo "<br>";
        }
    }
    function get_menu_tree($parent_id=0) 
    {
      $CI = get_instance(); 
      $menu           = '';
      $menu_lists     = array();
      $menu_data_list = array();
      $order_type     = '';
      if($CI->role_id != 1){
          $joindata       = array('select'    =>'master_menu.parent_id,master_menu.menu_link,master_menu.action,master_menu.is_active,master_menu.menu_icon,user_permission.permission_id,user_permission.menu_id,user_permission.menu_name,user_permission.add_flag,user_permission.edit_flag,user_permission.view_flag,user_permission.download_flag',
                              'first_table'   =>'master_menu',
                              'second_table'  =>'user_permission',
                              'dependency1'   =>'user_permission.menu_id = master_menu.menu_id',
                              'join_type1'    =>'LEFT JOIN',
                          );
          //pr($joindata);
          //$condition          = array('user_permission.role_id' => $CI->role_id,'master_menu.is_active'=>'1','master_menu.parent_id'=>$parent_id);
          $condition          = array('user_permission.role_id' => $CI->role_id,'master_menu.parent_id'=>$parent_id);
          $menu_lists         = $CI->mcommon->joinQuery($joindata,$condition,'result','menu_rank',$order_type);
      }
      else{
          //$condition          = array('master_menu.is_active'=>'1','master_menu.parent_id'=>$parent_id);
          $condition          = array('master_menu.parent_id'=>$parent_id);
          $menu_lists         = $CI->mcommon->getAllMenuList('master_menu',$condition);
      }
//pr($menu_lists);
      if(!empty($menu_lists)){
        foreach($menu_lists as $main_list){
            if($CI->role_id != 1){
                if($main_list['add_flag'] !='0' || $main_list['edit_flag'] !='0' || $main_list['view_flag'] !='0' || $main_list['download_flag'] !='0'){
                    $menu_arr = array();
                    $menu_arr['menu_id']          = $main_list['menu_id'];
                    $menu_arr['menu_name']        = $main_list['menu_name'];
                    $menu_arr['menu_link']        = $main_list['menu_link'];
                    $menu_arr['menu_icon']        = $main_list['menu_icon'];
                    $menu_arr['is_active']        = $main_list['is_active'];
                    $menu_arr['action']           = $main_list['action'];
                    $menu_arr['sub_menu']         = get_menu_tree($main_list['menu_id']);        

                    $menu_data_list[] = $menu_arr;
                }
            }
            else{
                $menu_arr = array();
                $menu_arr['menu_id']          = $main_list['menu_id'];
                $menu_arr['menu_name']        = $main_list['menu_name'];
                $menu_arr['menu_link']        = $main_list['menu_link'];
                $menu_arr['menu_icon']        = $main_list['menu_icon'];
                $menu_arr['is_active']        = $main_list['is_active'];
                $menu_arr['action']           = $main_list['action'];
                $menu_arr['sub_menu']         = get_menu_tree($main_list['menu_id']);        

                $menu_data_list[] = $menu_arr;
            }
        }
        //pr($menu_data_list);
        return $menu_data_list;
      }
       
    }
   
  function getStatusCahnge($id,$tbl,$tbl_column_name,$chng_status_colm,$status,$reason = null) {    
        //echo $id."<br>".$tbl."<br>".$tbl_column_name."<br>".$chng_status_colm."<br>".$status;exit;
        $CI = get_instance();
        $condition                      = array();
        $udata                          = array();
        $resonse                        = '';
        $condition[$tbl_column_name]    = $id;
        $udata[$chng_status_colm]       = $status;
        if($reason != null){
          $udata['cancellation_reason'] = $reason;
        }
        $resonse                        = $CI->mcommon->update($tbl,$condition,$udata);   
        return $resonse;
  }
  function checPkermission($url,$role_id){
    $CI = get_instance();
    $permission_flag  = '';
    $condition = array('menu_link' => $url);
    $menu_data = $CI->mcommon->getRow('master_menu',$condition);
    if(!empty($menu_data)){
      $menu_condition  = array('menu_id' => $menu_data['menu_id'],'role_id' =>$role_id);
      $permission_data = $CI->mcommon->getRow('user_permission',$menu_condition);
      if(!empty($permission_data)){
        if($permission_data['add_flag'] == '1' || $permission_data['edit_flag'] =='1'|| $permission_data['view_flag'] =='1'){
          $permission_flag  = '1';
        }
        else{
          $permission_flag  = '0';
        }
      }
    }
    return $permission_flag;
  }
  /*function reservationFilterSearch($from_dt = null,$to_dt = null,$zone_id = null,$status_id = null){
    $CI     = get_instance();
    $data   = array();
    $cond   ='';
    //echo $from_dt.'%%'.$to_dt ;exit;
    if($from_dt !='' && $to_dt !='' && $zone_id !='' && $status_id !=''){
      $from_date  = date('Y-m-d',strtotime(str_replace('/','-',$from_dt)));
      $to_date    = date('Y-m-d',strtotime(str_replace('/','-',$to_dt)));
      $cond =" where reservation.reservation_date between '".$from_date."' and '".$to_date."' and reservation.zone_id ='".$zone_id."' and reservation.status = '".$status_id."'";
    }
    if($from_dt !='' && $to_dt !=''){
      $from_date  = date('Y-m-d',strtotime(str_replace('/','-',$from_dt)));
      $to_date    = date('Y-m-d',strtotime(str_replace('/','-',$to_dt)));
      $cond .=" where reservation.reservation_date between '".$from_date."' and '".$to_date."'";      
    }
    if($zone_id !=''){
      $cond .= " and reservation.zone_id ='".$zone_id."'";
    }
    if($status_id !=''){
      $cond .= " and reservation.status = '".$status_id."'";
    }
            
    $reservation_list           = $CI->mreservation->get_reservation_list($cond);
   //pr($reservation_list);
    if(!empty($reservation_list)){
        
        $data['reservation_list']     = $reservation_list;
    }
    else{
        $data['reservation_list']     = '';
    }
    //pr($data);
    return $data;
  }*/
  function reservationFilterSearch($from_dt = null,$to_dt = null,$zone_id = null,$status_id = null,$resv_time = null,$reservation_id = null){
    $CI     = get_instance();
    $data   = array();
    $cond   = '1';
    //echo $from_dt.'%%'.$to_dt ;exit;
    /*if($from_dt !='' && $to_dt !='' && $zone_id !='' && $status_id !=''){
      $from_date  = date('Y-m-d',strtotime(str_replace('/','-',$from_dt)));
      $to_date    = date('Y-m-d',strtotime(str_replace('/','-',$to_dt)));
      $cond =" where reservation.reservation_date between '".$from_date."' and '".$to_date."' and reservation.zone_id ='".$zone_id."' and reservation.status = '".$status_id."'";
    }*/
    if($from_dt !='' && $to_dt !=''){
      $from_date  = date('Y-m-d',strtotime(str_replace('/','-',$from_dt)));
      $to_date    = date('Y-m-d',strtotime(str_replace('/','-',$to_dt)));
      $cond .=  " and reservation.reservation_date between '".$from_date."' and '".$to_date."'";      
    }
    if($zone_id !=''){
      $cond .= " and reservation.zone_id ='".$zone_id."'";
    }
    if($status_id !=''){
      $cond .= " and reservation.status = '".$status_id."'";
    }
    if($resv_time !=''){
      $cond .= " and reservation.reservation_time = '".$resv_time."'";
    }   
    if($reservation_id !=''){
      $cond .= " and reservation.reservation_id = '".$reservation_id."'";
    }     
    $reservation_list           = $CI->mreservation->get_reservation_list($cond);
   //pr($reservation_list);
    if(!empty($reservation_list)){
        
        $data['reservation_list']     = $reservation_list;
    }
    else{
        $data['reservation_list']     = '';
    }
    //pr($data);
    return $data;
  }
function eventFilterSearch($from_dt = null,$to_dt = null,$event_type){
    $CI     = get_instance();
    $result = array();
    $cond   = '';
    if($from_dt !='' && $to_dt !=''){
      $from_date  = date('Y-m-d',strtotime(str_replace('/','-',$from_dt)));
      $to_date    = date('Y-m-d',strtotime(str_replace('/','-',$to_dt)));
      $cond .=  " and me.event_start_date between '".$from_date."' and '".$to_date."'";      
    }
    //echo $event_type;exit;
    if($event_type =='new'){
      $result['event_active_list']   = $CI->mevent->get_filter_event_list($cond,'1');
      if(!empty($result['event_active_list'])){
        foreach($result['event_active_list'] as $evnt){
          $event_active_images  = $CI->mevent->get_event_img($evnt['event_id']);
          if(!empty($event_active_images)){
            $event_active_img[$evnt['event_id']] = $event_active_images[0];
          }
          else{
            $event_active_img[$evnt['event_id']] = '';
          }
        }
        $result['event_active_img']   = $event_active_img;
      }
      $result['event_inactive_list']  = $CI->mevent->get_filter_event_list($cond,'0');
      if(!empty($result['event_inactive_list'])){
        foreach($result['event_inactive_list'] as $inevnt){       
          $event_inactive_images    = $CI->mevent->get_event_img($inevnt['event_id']);
          if(!empty($event_inactive_images)){
            $event_inactive_img[$inevnt['event_id']] = $event_inactive_images[0];
          }
          else{
            $event_inactive_img[$inevnt['event_id']] = '';
          }
        }
        $result['event_inactive_img']   = $event_inactive_img;
      }
    }
    else{
      $result['event_past_active_list']   = $CI->mevent->get_filter_past_event_list($cond,'1');
      if(!empty($result['event_past_active_list'])){
        foreach($result['event_past_active_list'] as $evnt){
          $event_active_images  = $CI->mevent->get_event_img($evnt['event_id']);
          if(!empty($event_active_images)){
            $event_active_img[$evnt['event_id']] = $event_active_images[0];
          }
          else{
            $event_active_img[$evnt['event_id']] = '';
          }
        }
        $result['event_past_active_img']  = $event_active_img;
      }
      $result['event_past_inactive_list']   = $CI->mevent->get_filter_past_event_list($cond,'0');
      if(!empty($result['event_past_inactive_list'])){        
        foreach($result['event_past_inactive_list'] as $inevnt){        
          $event_inactive_images    = $CI->mevent->get_event_img($inevnt['event_id']);
          if(!empty($event_inactive_images)){
            $event_inactive_img[$inevnt['event_id']] = $event_inactive_images[0];
          }
          else{
            $event_inactive_img[$inevnt['event_id']] = '';
          }
        }
        $result['event_past_inactive_img']  = $event_inactive_img;
      }
    }
    return $result;
  }
  function membershipFilterSearch($registration_filter = null,$expiry_filter = null,$membership_name = null){
    $CI     = get_instance();
    $result = array();
    $cond   = '';
    if($registration_filter != null){
      if($registration_filter == '2'){
        $monday = strtotime("last monday");
        $monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;

        $sunday = strtotime(date("Y-m-d",$monday)." +6 days");

        $this_week_sd = date("Y-m-d",$monday);
        $this_week_ed = date("Y-m-d",$sunday);  
        
        $cond .=  " and DATE_FORMAT(pmm.buy_on,'%Y-%m-%d')>= '".$this_week_sd."' and DATE_FORMAT(pmm.buy_on,'%Y-%m-%d')<= '".$this_week_ed."'";      
      }
      elseif($registration_filter == '3'){      
        $cond .=  " and Month(pmm.buy_on) = MONTH(CURRENT_DATE())";
      }
      elseif($registration_filter == '4'){
        $current_month = date('m');
        $current_year = date('Y');

        if($current_month>=1 && $current_month<=3)
        {
          $s_date = $current_year.'-01-01';  
          $e_date = $current_year.'-03-01';
        }
        elseif($current_month>=4 && $current_month<=6)
        {
          $s_date = '2019-04-01';  
          $e_date = '2019-06-01';          
        }
        elseif($current_month>=7 && $current_month<=9)
        {
          $s_date = '2019-07-01';  
          $e_date = '2019-09-01';       
        }
        elseif($current_month>=10 && $current_month<=12)
        {
          $s_date = '2019-10-01';  
          $e_date = '2019-12-01';          
        }
        $cond .=  " and DATE_FORMAT(pmm.buy_on,'%Y-%m-%d')>= '".$s_date."' and DATE_FORMAT(pmm.buy_on,'%Y-%m-%d')<= '".$e_date."'";
      }
      elseif($registration_filter == '5'){      
        $cond .=  " and Year(pmm.buy_on) = Year(CURRENT_DATE())";
      }      
    }
    if($expiry_filter != null){
      if($expiry_filter == '2'){
        $monday = strtotime("last monday");
        $monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;

        $sunday = strtotime(date("Y-m-d",$monday)." +6 days");

        $this_week_sd = date("Y-m-d",$monday);
        $this_week_ed = date("Y-m-d",$sunday);  
        
        $cond .=  " and DATE_FORMAT(pmm.expiry_date,'%Y-%m-%d')>= '".$this_week_sd."' and DATE_FORMAT(pmm.expiry_date,'%Y-%m-%d')<= '".$this_week_ed."'";      
      }
      elseif($expiry_filter == '3'){      
        $cond .=  " and Month(pmm.expiry_date) = MONTH(CURRENT_DATE())";
      }
      elseif($expiry_filter == '4'){
        $current_month = date('m');
        $current_year = date('Y');

        if($current_month>=1 && $current_month<=3)
        {
          $s_date = $current_year.'-01-01';  
          $e_date = $current_year.'-03-01';
        }
        elseif($current_month>=4 && $current_month<=6)
        {
          $s_date = '2019-04-01';  
          $e_date = '2019-06-01';          
        }
        elseif($current_month>=7 && $current_month<=9)
        {
          $s_date = '2019-07-01';  
          $e_date = '2019-09-01';       
        }
        elseif($current_month>=10 && $current_month<=12)
        {
          $s_date = '2019-10-01';  
          $e_date = '2019-12-01';          
        }
        $cond .=  " and DATE_FORMAT(pmm.expiry_date,'%Y-%m-%d')>= '".$s_date."' and DATE_FORMAT(pmm.expiry_date,'%Y-%m-%d')<= '".$e_date."'";
      }
      elseif($expiry_filter == '5'){      
        $cond .=  " and Year(pmm.expiry_date) = Year(CURRENT_DATE())";
      }      
    }
    if($membership_name != null){
      $cond .= " and pmm.package_id = '".$membership_name."'";      
    }
    $result['active_membership_list']   = $CI->Mmembership->getMembershipDetails('1',$cond);
    $result['inactive_membership_list'] = $CI->Mmembership->getMembershipDetails('0',$cond);
    //pr($result);
    return $result;
  }
  function getClientIP(){
    $ip_address ='';
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   
    {
      $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    }
    //whether ip is from proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
    {
      $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    //whether ip is from remote address
    else
    {
      $ip_address = $_SERVER['REMOTE_ADDR'];
    }
    return  $ip_address;
  }
  
  //** added by ishani on 21.09.2020 for insert all user data **//
  //insert new user data by phone no unique
  function insert_all_user($user_data) 
    {
      $CI = get_instance(); 
      if(!empty($user_data))
      {
        $mobile=$user_data['mobile'];
        $email=$user_data['email'];
        $name=$user_data['name'];

        $condition['mobile']= $mobile;

        $existing_row_count=$CI->mcommon->getNumRows("all_user_data",$condition);
        if($existing_row_count==0)
        {
          ////insert user data
          $CI->mcommon->insert("all_user_data",$user_data);
        }

      }
      return 1;

    }
    
    
}