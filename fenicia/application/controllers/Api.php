<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Api extends CI_Controller
{
  var $arr;
  var $obj;

  function __construct()
  {
    parent::__construct();
    $this->load->library('PushNotification');
    $this->load->library('imageupload');
    $this->load->model('mapi');
    $this->arr = array();
    $this->obj = new stdClass();
    $this->http_methods = array('POST', 'GET', 'PUT', 'DELETE');
    $this->logo = base_url() . 'public/images/logo_new.jpg';
    //$this->load->library('notification');
  }

  private function displayOutput($response)
  {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit(0);
  }

  public function test()
  {
  	echo 1; die;
  }
  //registration only
  public function signup()
  {
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (!empty($this->input->post())) {
        
        if (empty($this->input->post( 'first_name' ))) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'First Name field is required';
          //$response['response']   = $this->obj;
          $this->displayOutput($response);
        }
        if (empty($this->input->post( 'last_name' ))) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Last Name field is required';
          //$response['response']   = $this->obj;
          
          $this->displayOutput($response);
        }
        if (empty($this->input->post( 'email' ))) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Email field is required';
          //$response['response']   = $this->obj;
          
          $this->displayOutput($response);
        }
        if (!empty($this->input->post( 'email' ))) {

          $result = $this->mapi->checkUserRegistered(array('email' => $this->input->post( 'email' )));
          if (!empty($result)) {
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Email Already registered';
            //$response['response']   = $this->obj;
            $this->displayOutput($response);
          }
        }
        if (empty($this->input->post( 'mobile' ))) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Mobile field is required';
          //$response['response']   = $this->obj;
          $this->displayOutput($response);
        }
        if (!empty($this->input->post( 'mobile' ))) {

          $result = $this->mapi->checkUserRegistered(array('mobile' => $this->input->post( 'mobile' )));
          if (!empty($result)) {
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Mobile Already registered';
            //$response['response']   = $this->obj;
            
            $this->displayOutput($response);
          }
        }
        if (empty($this->input->post( 'dob' ))) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Date of birth field is required';
          //$response['response']   = $this->obj;
          
          $this->displayOutput($status);
        }
        if (empty($this->input->post( 'gender' ))) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Gender of birth field is required';
          //$response['response']   = $this->obj;
          
          $this->displayOutput($status);
        }
        if(empty($this->input->post('fb_id'))){
          $registration_type  = '2';
          if (empty($this->input->post('password'))) {
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Password field is required';
            $this->displayOutput($response);
          }
          if(!empty($_FILES['profile_image']['name'])){
            $image_path = '/public/upload_image/profile_photo';
            $file     = $this->imageupload->image_upload2($image_path,'profile_image');
            if($file['status'] == 1){
               $img = $file['result'];
            }
            else{
              $img = '';
            } 
          }
          else{
              $img = '';
          } 
        }       
        else{
          $registration_type  = '3';
          $img = $this->input->post('profile_image');        
        }
       /* if(!empty($_FILES['profile_img']['name'])){
          $image_path = '/public/upload_image/profile_photo';
          $file     = $this->imageupload->image_upload2($image_path,'profile_img');
          if($file['status'] == 1){
             $img = $file['result'];
          } 
        }*/ 
        if($this->input->post('doa') !=''){
          $doa              = date('Y-m-d',strtotime(str_replace('/','-',$this->input->post('doa'))));         
        }
        else{
          $doa              = '';          
        }
        $data = array(    
               
          'first_name'            => $this->input->post( 'first_name' ),  
          'middle_name'           => '', 
          'last_name'             => $this->input->post( 'last_name' ),
          'country_code'          => $this->input->post( 'country_code' ),  
          'mobile'                => $this->input->post( 'mobile' ),
          'password'              => sha1($this->input->post( 'password' )),
          'original_password'     => $this->input->post( 'password' ),
          'email'                 => $this->input->post( 'email' ), 
          'gender'                => $this->input->post( 'gender' ),
          'marriage_status'       => strtolower($this->input->post( 'marriage_status' )),
          'dob'                   => date('Y-m-d',strtotime(str_replace('/','-',$this->input->post( 'dob' )))),
          'doa'                   => $doa,
          'profile_img'           => $img,
          'status'                => '1',
          'registration_type'     => $registration_type,
          'added_form'            => 'front',
          'login_status'          => '1',
          'fb_id'                 => $this->input->post('fb_id'),
          'created_by'            => '',     
          'created_ts'            => date('Y-m-d H:i:s'),       
        );
        
        $member_id = $this->mapi->insert('master_member', $data);        
        if($member_id)
          {
            $condition              = array('member_id' => $member_id);
            $update_arr             = array('created_by' => $member_id,'login_status' =>'1');
            $update_result          = $this->mapi->update('master_member',$condition,$update_arr); 
            $user_password          = sha1($this->input->post( 'password' ));
            $check_member_condition = array('email' => $this->input->post( 'email' ));
            $memberdetails          = $this->mapi->getRow('master_member', $check_member_condition);
            $member_id              = $memberdetails['member_id'];
            
            if($update_result){
                $response['status']['error_code'] = 0;
                $response['status']['message']    = 'Login Successfully';
                $response['response']['member']   = $memberdetails;
                $api_token_details                = $this->mapi->getRow('api_token', $condition);
                $device_token_details             = $this->mapi->getRow('device_token', $condition);
                //echo $api_token_details."%%%".$device_token_details;exit;
                if (empty($api_token_details) && empty($device_token_details)) {

                  $device_token_data['member_id']          = $memberdetails['member_id'];
                  $device_token_data['device_type']        = $this->input->post( 'device_type' );
                  $device_token_data['device_token']       = '';
                  $device_token_data['fcm_token']          = $this->input->post( 'device_token' );
                  $device_token_data['login_status']       = '1';
                  $device_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                  $device_token_data['session_start_time'] = date('Y-m-d H:i:s');
                  $device_token_data['session_end_time']   = '';

                  $insert_data          = $this->mapi->insert('device_token', $device_token_data);

                  $api_token_data['member_id']          = $memberdetails['member_id'];
                  $api_token_data['device_type']        = $this->input->post( 'device_type' );
                  $api_token_data['access_token']       = md5(mt_rand() . '_' . $memberdetails['member_id']);
                  $api_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                  $api_token_data['session_start_time'] = date('Y-m-d H:i:s');
                  $api_token_data['session_end_time']   = '';

                  $insert_data      = $this->mapi->insert('api_token', $api_token_data);
                  $all_member       = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                  //pr($all_member);
                  if(!empty($all_member)){
                    if($all_member[0]['membership_id'] ==''){
                      $all_member_details = $all_member[0];
                    }
                    else{
                      foreach($all_member as $val){
                          $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                          if(!empty($all_member_datas)){
                              $all_member_details = $all_member_datas[0];
                          }
                          else{
                              $all_member_details = $all_member[0];
                              $all_member_details['membership_id'] = null;
                          }
                      }
                    }                       
                  }
                  else {
                    $response['status']['error_code'] = 1;
                    $response['status']['message']    = 'Unable to generate access token';                    
                  }                  
                  if ($all_member_details) {
                    if($all_member_details['profile_img'] !='' ){
                      $all_member_details['profile_pic_updated'] = '1';
                    }
                    else{
                      $all_member_details['profile_pic_updated'] = '0';
                    }
                    $response['status']['error_code'] = 0;
                    $response['status']['message']    = 'Login Successfully';
                    $response['response']['member']   = $all_member_details;
                    if(!empty($this->input->post( 'fb_id' ))){
                      $response['response']['member']['profile_image'] = $this->input->post('profile_image');
                    }
                    
                  } else {
                    $response['status']['error_code'] = 1;
                    $response['status']['message']    = 'Unable to generate access token';                    
                  }
                } else {
                  $condition_token                    = array('member_id' =>$member_id);

                  $api_token_updata['device_type']    = $this->input->post( 'device_type' );
                  $api_token_updata['access_token']   = $api_token_details['access_token'];
                  $update_data  = $this->mapi->update('api_token', $condition_token, $api_token_updata);

                  $device_token_updata['device_type']     = $this->input->post( 'device_type' );
                  $device_token_updata['fcm_token']       = $this->input->post( 'device_token' );
                  $update_data  = $this->mapi->update('device_token', $condition_token, $device_token_updata);

                  $all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                  //pr($all_member);
                  if(!empty($all_member)){
                    if($all_member[0]['membership_id'] ==''){
                      $all_member_details = $all_member[0];
                    }
                    else{
                      foreach($all_member as $val){
                          $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                          //pr($all_member_datas);
                          if(!empty($all_member_datas)){
                              $all_member_details = $all_member_datas[0];
                          }
                          else{
                              $all_member_details = $all_member[0];
                              $all_member_details['membership_id'] = null;
                          }
                      }
                    }                       
                  }
                  //pr($all_member_details);
                  if ($update_data) {
                    if($all_member_details['profile_img'] !='' ){
                      $all_member_details['profile_pic_updated'] = '1';
                    }
                    else{
                      $all_member_details['profile_pic_updated'] = '0';
                    }
                    $response['status']['error_code'] = 0;
                    $response['status']['message']    = 'Login Successfully';
                    $response['response']['member']   = $all_member_details;
                    if(!empty($this->input->post( 'fb_id' ))){
                      $response['response']['member']['profile_image'] = $this->input->post('profile_image');
                    }
                  } else {
                    $response['status']['error_code'] = 1;
                    $response['status']['message']    = 'Unable to update access token';                   
                  }
                }
            }
            else {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Oops!something went wrong...';
            }


  /************************* Send password to the member ****************************/
            //$link               = base_url('api/member_activation/'.$member_id);
            $logo               = base_url('public/images/logo.png');
            $mail['name']       = $this->input->post( 'first_name' );
            $mail['to']         = $this->input->post( 'email' );    
            //$params['to']     = 'sreelabiswas.kundu@met-technologies.com';
            $details            = "User ID: ".$this->input->post( 'email' );
            $mail['subject']    = 'Club Fenicia - Registration Successful Mail';                             
            $mail_temp          = file_get_contents('./global/mail/registration_template.html');
            $mail_temp          = str_replace("{web_url}", base_url(), $mail_temp);
            $mail_temp          = str_replace("{logo}", $logo, $mail_temp);
            $mail_temp          = str_replace("{shop_name}", 'Club Fenicia', $mail_temp);  
            $mail_temp          = str_replace("{name}", $mail['name'], $mail_temp);
            $mail_temp          = str_replace("{details}", $details, $mail_temp);         
            $mail_temp          = str_replace("{current_year}", date('Y'), $mail_temp);           
            $mail['message']    = $mail_temp;
            $from_email         = 'clubfenicia@fenicialounge.in';
            $this->sendMail($mail,'Club Fenicia',$from_email); 

            $message  = "User Registration successful. \n";
            $message .=  "User ID: ".$this->input->post( 'email' )."\n Team Club Fenicia";
            $this->smsSend($this->input->post( 'mobile' ),$message);
        }
      }
      else {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Please fill up all required fields.';
         //$response['response']   = $this->obj;        
      }
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;      
    }
    $this->displayOutput($response);      
  }
  // register with login
  public function signup_v1()
  {
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (!empty($this->input->post())) {
        
        if (empty($this->input->post( 'first_name' ))) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'First Name field is required';
          //$response['response']   = $this->obj;
          $this->displayOutput($response);
        }
        if (empty($this->input->post( 'last_name' ))) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Last Name field is required';
          //$response['response']   = $this->obj;
          
          $this->displayOutput($response);
        }
        if (empty($this->input->post( 'email' ))) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Email field is required';
          //$response['response']   = $this->obj;
          
          $this->displayOutput($response);
        }
        if (!empty($this->input->post( 'email' ))) {

          $result = $this->mapi->checkUserRegistered(array('email' => $this->input->post( 'email' )));
          if (!empty($result)) {
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Email Already registered';
            //$response['response']   = $this->obj;
            $this->displayOutput($response);
          }
        }
        if (empty($this->input->post( 'mobile' ))) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Mobile field is required';
          //$response['response']   = $this->obj;
          $this->displayOutput($response);
        }
        if (!empty($this->input->post( 'mobile' ))) {

          $result = $this->mapi->checkUserRegistered(array('mobile' => $this->input->post( 'mobile' )));
          if (!empty($result)) {
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Mobile Already registered';
            //$response['response']   = $this->obj;
            
            $this->displayOutput($response);
          }
        }
        if (empty($this->input->post( 'dob' ))) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Date of birth field is required';
          //$response['response']   = $this->obj;
          
          $this->displayOutput($status);
        }
        if (empty($this->input->post( 'gender' ))) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Gender of birth field is required';
          //$response['response']   = $this->obj;
          
          $this->displayOutput($status);
        }
        if(empty($this->input->post('fb_id'))){
          $registration_type  = '2';
          if (empty($this->input->post('password'))) {
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Password field is required';
            $this->displayOutput($response);
          }
          if(!empty($_FILES['profile_image']['name'])){
            $image_path = '/public/upload_image/profile_photo';
            $file     = $this->imageupload->image_upload2($image_path,'profile_image');
            if($file['status'] == 1){
               $img = $file['result'];
            }
            else{
              $img = '';
            } 
          }
          else{
              $img = '';
          } 
        }       
        else{
          $registration_type  = '3';
          $img = $this->input->post('profile_image');        
        }
       /* if(!empty($_FILES['profile_img']['name'])){
          $image_path = '/public/upload_image/profile_photo';
          $file     = $this->imageupload->image_upload2($image_path,'profile_img');
          if($file['status'] == 1){
             $img = $file['result'];
          } 
        }*/ 
        if($this->input->post('doa') !=''){
          $doa              = date('Y-m-d',strtotime(str_replace('/','-',$this->input->post('doa'))));         
        }
        else{
          $doa              = '';          
        }
        $data = array(    
               
          'first_name'            => $this->input->post( 'first_name' ),  
          'middle_name'           => '', 
          'last_name'             => $this->input->post( 'last_name' ),
          'country_code'          => $this->input->post( 'country_code' ),  
          'mobile'                => $this->input->post( 'mobile' ),
          'password'              => sha1($this->input->post( 'password' )),
          'original_password'     => $this->input->post( 'password' ),
          'email'                 => $this->input->post( 'email' ), 
          'gender'                => $this->input->post( 'gender' ),
          'marriage_status'       => strtolower($this->input->post( 'marriage_status' )),
          'dob'                   => date('Y-m-d',strtotime(str_replace('/','-',$this->input->post( 'dob' )))),
          'doa'                   => $doa,
          'profile_img'           => $img,
          'status'                => '1',
          'registration_type'     => $registration_type,
          'added_form'            => 'front',
          'login_status'          => '1',
          'fb_id'				          => $this->input->post('fb_id'),
          'created_by'            => '',     
          'created_ts'            => date('Y-m-d H:i:s'),       
        );
        
        $member_id = $this->mapi->insert('master_member', $data);        
        if($member_id)
          {
            $condition              = array('member_id' => $member_id);
            $update_arr             = array('created_by' => $member_id,'login_status' =>'1');
            $update_result          = $this->mapi->update('master_member',$condition,$update_arr); 
            $user_password          = sha1($this->input->post( 'password' ));
            $check_member_condition = array('email' => $this->input->post( 'email' ));
            $memberdetails          = $this->mapi->getRow('master_member', $check_member_condition);
            $member_id              = $memberdetails['member_id'];
            
            if($update_result){
                $response['status']['error_code'] = 0;
                $response['status']['message']    = 'Login Successfully';
                $response['response']['member']   = $memberdetails;
                $api_token_details                = $this->mapi->getRow('api_token', $condition);
                $device_token_details             = $this->mapi->getRow('device_token', $condition);
                //echo $api_token_details."%%%".$device_token_details;exit;
                if (empty($api_token_details) && empty($device_token_details)) {

                  $device_token_data['member_id']          = $memberdetails['member_id'];
                  $device_token_data['device_type']        = $this->input->post( 'device_type' );
                  $device_token_data['device_token']       = '';
                  $device_token_data['fcm_token']          = $this->input->post( 'device_token' );
                  $device_token_data['login_status']       = '1';
                  $device_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                  $device_token_data['session_start_time'] = date('Y-m-d H:i:s');
                  $device_token_data['session_end_time']   = '';

                  $insert_data          = $this->mapi->insert('device_token', $device_token_data);

                  $api_token_data['member_id']          = $memberdetails['member_id'];
                  $api_token_data['device_type']        = $this->input->post( 'device_type' );
                  $api_token_data['access_token']       = md5(mt_rand() . '_' . $memberdetails['member_id']);
                  $api_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                  $api_token_data['session_start_time'] = date('Y-m-d H:i:s');
                  $api_token_data['session_end_time']   = '';

                  $insert_data      = $this->mapi->insert('api_token', $api_token_data);
                  $all_member       = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                  //pr($all_member);
                  if(!empty($all_member)){
                    if($all_member[0]['membership_id'] ==''){
                      $all_member_details = $all_member[0];
                    }
                    else{
                      foreach($all_member as $val){
                          $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                          if(!empty($all_member_datas)){
                              $all_member_details = $all_member_datas[0];
                          }
                          else{
                              $all_member_details = $all_member[0];
                              $all_member_details['membership_id'] = null;
                          }
                      }
                    }                       
                  }
                  else {
                    $response['status']['error_code'] = 1;
                    $response['status']['message']    = 'Unable to generate access token';                    
                  }                  
                  if ($all_member_details) {
                    if($all_member_details['profile_img'] !='' ){
                      $all_member_details['profile_pic_updated'] = '1';
                    }
                    else{
                      $all_member_details['profile_pic_updated'] = '0';
                    }
                    $response['status']['error_code'] = 0;
                    $response['status']['message']    = 'Login Successfully';
                    $response['response']['member']   = $all_member_details;
                    if(!empty($this->input->post( 'fb_id' ))){
                      $response['response']['member']['profile_image'] = $this->input->post('profile_image');
                    }
                    
                  } else {
                    $response['status']['error_code'] = 1;
                    $response['status']['message']    = 'Unable to generate access token';                    
                  }
                } else {
                  $condition_token                    = array('member_id' =>$member_id);

                  $api_token_updata['device_type']    = $this->input->post( 'device_type' );
                  $api_token_updata['access_token']   = $api_token_details['access_token'];
                  $update_data  = $this->mapi->update('api_token', $condition_token, $api_token_updata);

                  $device_token_updata['device_type']     = $this->input->post( 'device_type' );
                  $device_token_updata['fcm_token']       = $this->input->post( 'device_token' );
                  $update_data  = $this->mapi->update('device_token', $condition_token, $device_token_updata);

                  $all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                  //pr($all_member);
                  if(!empty($all_member)){
                    if($all_member[0]['membership_id'] ==''){
                      $all_member_details = $all_member[0];
                    }
                    else{
                      foreach($all_member as $val){
                          $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                          //pr($all_member_datas);
                          if(!empty($all_member_datas)){
                              $all_member_details = $all_member_datas[0];
                          }
                          else{
                              $all_member_details = $all_member[0];
                              $all_member_details['membership_id'] = null;
                          }
                      }
                    }                       
                  }
                  //pr($all_member_details);
                  if ($update_data) {
                    if($all_member_details['profile_img'] !='' ){
                      $all_member_details['profile_pic_updated'] = '1';
                    }
                    else{
                      $all_member_details['profile_pic_updated'] = '0';
                    }
                    $response['status']['error_code'] = 0;
                    $response['status']['message']    = 'Login Successfully';
                    $response['response']['member']   = $all_member_details;
                    if(!empty($this->input->post( 'fb_id' ))){
                      $response['response']['member']['profile_image'] = $this->input->post('profile_image');
                    }
                  } else {
                    $response['status']['error_code'] = 1;
                    $response['status']['message']    = 'Unable to update access token';                   
                  }
                }
            }
            else {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Oops!something went wrong...';
            }


  /************************* Send password to the member ****************************/
            //$link               = base_url('api/member_activation/'.$member_id);
            $logo               = base_url('public/images/logo.png');
            $mail['name']       = $this->input->post( 'first_name' );
            $mail['to']         = $this->input->post( 'email' );    
            //$params['to']     = 'sreelabiswas.kundu@met-technologies.com';
            $details            = "User ID: ".$this->input->post( 'email' );
            $mail['subject']    = 'Club Fenicia - Registration Successful Mail';                             
            $mail_temp          = file_get_contents('./global/mail/registration_template.html');
            $mail_temp          = str_replace("{web_url}", base_url(), $mail_temp);
            $mail_temp          = str_replace("{logo}", $logo, $mail_temp);
            $mail_temp          = str_replace("{shop_name}", 'Club Fenicia', $mail_temp);  
            $mail_temp          = str_replace("{name}", $mail['name'], $mail_temp);
            $mail_temp          = str_replace("{details}", $details, $mail_temp);         
            $mail_temp          = str_replace("{current_year}", date('Y'), $mail_temp);           
            $mail['message']    = $mail_temp;
            $from_email         = 'clubfenicia@fenicialounge.in';
            $this->sendMail($mail,'Club Fenicia',$from_email); 

            $message  = "User Registration successful. \n";
            $message .=  "User ID: ".$this->input->post( 'email' )."\n Team Club Fenicia";
            $this->smsSend($this->input->post( 'mobile' ),$message);
        }
      }
      else {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Please fill up all required fields.';
         //$response['response']   = $this->obj;        
      }
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;      
    }
    $this->displayOutput($response);      
  }
  
  public function member_activation($member_id){
      $condition      = array('member_id' =>$member_id);
      $update_arr     = array('status' =>'1');
      $update_result  = $this->mapi->update('master_member',$condition,$update_arr);
      if($update_result){
          $active_member = $this->mapi->getRow('master_member',$condition);
          $response['status']['error_code'] = 0;
          $response['status']['message']    = 'Account activation done Successfully.';
          $response['response']['member']   = $active_member;
      }
      else{
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Opps!Some problem occure,please try again.';        
      }
      $this->displayOutput($response);
  }
  public function login()
  {
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        if (empty($ap['device_type'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Device type is required.';
         
          $this->displayOutput($response);
        }
        if (empty($ap['email'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Email is required.';
          $this->displayOutput($response);
        }
        if (empty($ap['password'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Password field is required';
          
          $this->displayOutput($response);
        }
        $ap['password']         = sha1($ap['password']);
        $check_member_condition = array('email' => $ap['email'], 'password' => $ap['password']);
        $memberdetails          = $this->mapi->getRow('master_member', $check_member_condition);
        if(empty($memberdetails)){
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Invalid username or password.';
            $this->displayOutput($response);
        }
        elseif($memberdetails['is_delete'] != '0'){
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Member account is no more exists';
            $this->displayOutput($response);
        }
        elseif($memberdetails['status'] == '0'){
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Member account is not active';
            $this->displayOutput($response);
        }
        else{
            $member_id      = $memberdetails['member_id'];
            $condition      = array('member_id' =>$member_id);
            $update_arr     = array('login_status' =>'1');
            $update_result  = $this->mapi->update('master_member',$condition,$update_arr);
            if($update_result){
                $response['status']['error_code'] = 0;
                $response['status']['message']    = 'Login Successfully';
                $response['response']['member']   = $memberdetails;
                $api_token_details                = $this->mapi->getRow('api_token', $condition);
                $device_token_details             = $this->mapi->getRow('device_token', $condition);
                //echo $api_token_details."%%%".$device_token_details;exit;
                if (empty($api_token_details) && empty($device_token_details)) {

                  $device_token_data['member_id']          = $memberdetails['member_id'];
                  $device_token_data['device_type']        = $ap['device_type'];
                  $device_token_data['device_token']       = '';
                  $device_token_data['fcm_token']       	 = $ap['device_token'];
                  $device_token_data['login_status']       = '1';
                  $device_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                  $device_token_data['session_start_time'] = date('Y-m-d H:i:s');
                  $device_token_data['session_end_time']   = '';

                  $insert_data          = $this->mapi->insert('device_token', $device_token_data);

                  $api_token_data['member_id']          = $memberdetails['member_id'];
                  $api_token_data['device_type']        = $ap['device_type'];
                  $api_token_data['access_token']       = md5(mt_rand() . '_' . $memberdetails['member_id']);
                  $api_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                  $api_token_data['session_start_time'] = date('Y-m-d H:i:s');
                  $api_token_data['session_end_time']   = '';

                  $insert_data      = $this->mapi->insert('api_token', $api_token_data);
                  $all_member   	= $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                  //pr($all_member);
                  if(!empty($all_member)){
                    if($all_member[0]['membership_id'] ==''){
                      $all_member_details = $all_member[0];
                    }
                    else{
                      foreach($all_member as $val){
                          $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                          if(!empty($all_member_datas)){
                              $all_member_details = $all_member_datas[0];
                          }
                          else{
                          	  $all_member_details = $all_member[0];
                          	  $all_member_details['membership_id'] = null;
                          }
                      }
                    }                       
                  }
                  else {
                    $response['status']['error_code'] = 1;
                    $response['status']['message']    = 'Unable to generate access token';                    
                  }                  
                  if ($all_member_details) {
                    if($all_member_details['profile_img'] !='' ){
                      $all_member_details['profile_pic_updated'] = '1';
                    }
                    else{
                      $all_member_details['profile_pic_updated'] = '0';
                    }
                    $response['status']['error_code'] = 0;
                    $response['status']['message']    = 'Login Successfully';
                    $response['response']['member']   = $all_member_details;
                    
                  } else {
                    $response['status']['error_code'] = 1;
                    $response['status']['message']    = 'Unable to generate access token';                    
                  }
                } else {
                  $condition_token                    = array('member_id' =>$member_id);

                  $api_token_updata['device_type']    = $ap['device_type'];
                  $api_token_updata['access_token']   = $api_token_details['access_token'];
                  $update_data 	= $this->mapi->update('api_token', $condition_token, $api_token_updata);

                  $device_token_updata['device_type']     = $ap['device_type'];
                  $device_token_updata['fcm_token']       = $ap['device_token'];
                  $update_data  = $this->mapi->update('device_token', $condition_token, $device_token_updata);

                  $all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                  //pr($all_member);
                  if(!empty($all_member)){
                    if($all_member[0]['membership_id'] ==''){
                      $all_member_details = $all_member[0];
                    }
                    else{
                      foreach($all_member as $val){
                          $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                          //pr($all_member_datas);
                          if(!empty($all_member_datas)){
                              $all_member_details = $all_member_datas[0];
                          }
                          else{
                          	  $all_member_details = $all_member[0];
                          	  $all_member_details['membership_id'] = null;
                          }
                      }
                    }                       
                  }
                  //pr($all_member_details);
                  if ($update_data) {
                    if($all_member_details['profile_img'] !='' ){
                      $all_member_details['profile_pic_updated'] = '1';
                    }
                    else{
                      $all_member_details['profile_pic_updated'] = '0';
                    }
                    $response['status']['error_code'] = 0;
                    $response['status']['message']    = 'Login Successfully';
                    $response['response']['member']   = $all_member_details;
                  } else {
                    $response['status']['error_code'] = 1;
                    $response['status']['message']    = 'Unable to update access token';                   
                  }
                }
            }
            else {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Oops!something went wrong...';
            }          
        }        
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields';        
      }
    }
    else{
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';        
    }
    $this->displayOutput($response);
  }
  public function fbLogin()
  {
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        if (empty($ap['device_type'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Device type is required.';
         
          $this->displayOutput($response);
        }
        
        if (empty($ap['fb_id'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Invalid Facebook credential.';
          
          $this->displayOutput($response);
        }
        
        $check_member_condition = array('fb_id' => $ap['fb_id']);
        $memberdetails          = $this->mapi->getRow('master_member', $check_member_condition);
        //pr($memberdetails);
        if(empty($memberdetails)){
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Invalid Facebook credential.';
            $this->displayOutput($response);
        }
        elseif($memberdetails['is_delete'] != '0'){
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Member account is no more exists';
            $this->displayOutput($response);
        }
        elseif($memberdetails['status'] == '0'){
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Member account is not active';
            $this->displayOutput($response);
        }
        else{
            $member_id      = $memberdetails['member_id'];
            $condition      = array('member_id' =>$member_id);
            $update_arr     = array('login_status' =>'1');
            $update_result  = $this->mapi->update('master_member',$condition,$update_arr);

            if($update_result){              
                $response['status']['error_code'] = 0;
                $response['status']['message']    = 'Login Successfully';
                $response['response']['member']   = $memberdetails;
                $api_token_details                = $this->mapi->getRow('api_token', $condition);
                $device_token_details             = $this->mapi->getRow('device_token', $condition);

                if (empty($api_token_details) && empty($device_token_details)) {

                  $device_token_data['member_id']          = $memberdetails['member_id'];
                  $device_token_data['device_type']        = $ap['device_type'];
                  $device_token_data['device_token']       = '';
                  $device_token_data['fcm_token']          = $ap['device_token'];
                  $device_token_data['login_status']       = '1';
                  $device_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                  $device_token_data['session_start_time'] = date('Y-m-d H:i:s');
                  $device_token_data['session_end_time']   = '';

                  $insert_data          = $this->mapi->insert('device_token', $device_token_data);

                  $api_token_data['member_id']          = $memberdetails['member_id'];
                  $api_token_data['device_type']        = $ap['device_type'];
                  $api_token_data['access_token']       = md5(mt_rand() . '_' . $memberdetails['member_id']);
                  $api_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                  $api_token_data['session_start_time'] = date('Y-m-d H:i:s');
                  $api_token_data['session_end_time']   = '';

                  $insert_data  = $this->mapi->insert('api_token', $api_token_data);
                  
                  $all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                  if(!empty($all_member)){
                    if($all_member[0]['membership_id'] ==''){
                      $all_member_details = $all_member[0];
                    }
                    else{
                      foreach($all_member as $val){
                          $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                          if(!empty($all_member_datas)){
                              $all_member_details = $all_member_datas[0];
                          }
                          else{
                          	  $all_member_details = $all_member[0];
                          	  $all_member_details['membership_id'] = null;
                          }
                      }
                    }                       
                  }
                  if ($all_member_details) {
                    if($all_member_details['profile_img'] !='' ){
                      $all_member_details['profile_image'] = $all_member_details['profile_img'];
                      $all_member_details['profile_pic_updated'] = '1';
                    }
                    else{
                      $all_member_details['profile_pic_updated'] = '0';
                    }
                    $response['status']['error_code'] = 0;
                    $response['status']['message']    = 'Login Successfully';
                    $response['response']['member']   = $all_member_details;
                    
                  } else {
                    $response['status']['error_code'] = 1;
                    $response['status']['message']    = 'Unable to generate access token';                    
                  }
                } else {
                  $condition_token                    = array('member_id' =>$member_id);
                  $api_token_updata['device_type']    = $ap['device_type'];
                  $api_token_updata['access_token']   = $api_token_details['access_token'];
                  $update_data  = $this->mapi->update('api_token', $condition_token, $api_token_updata);

                  $device_token_updata['device_type']     = $ap['device_type'];
                  $device_token_updata['fcm_token']       = $ap['device_token'];
                  $update_data  = $this->mapi->update('device_token', $condition_token, $device_token_updata);

                  $all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                  if(!empty($all_member)){
                    if($all_member[0]['membership_id'] ==''){
                      $all_member_details = $all_member[0];
                    }
                    else{
                      foreach($all_member as $val){
                          $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                          if(!empty($all_member_datas)){
                              $all_member_details = $all_member_datas[0];
                          }
                          else{
                          	  $all_member_details = $all_member[0];
                          	  $all_member_details['membership_id'] = null;
                          }
                      }
                    }                       
                  }                  
                  if ($all_member_details) {
                    if($all_member_details['profile_img'] !='' ){
                      $all_member_details['profile_image'] = $all_member_details['profile_img'];
                      $all_member_details['profile_pic_updated'] = '1';
                    }
                    else{
                      $all_member_details['profile_pic_updated'] = '0';
                    }
                    $response['status']['error_code'] = 0;
                    $response['status']['message']    = 'Login Successfully';
                    $response['response']['member']   = $all_member_details;
                  } else {
                    $response['status']['error_code'] = 1;
                    $response['status']['message']    = 'Unable to update access token';                   
                  }
                }
            }
            else {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Oops!something went wrong...';
            }          
        }        
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields';        
      }
    }
    else{
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';        
    }
    $this->displayOutput($response);
  }
  public function mobileOtpGenerate()
  {
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        if (empty($ap['country_code'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Country code is required.';
         
          $this->displayOutput($response);
        }
        if (empty($ap['mobile_no'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Mobile no is required.';
         
          $this->displayOutput($response);
        }
        
        $mobile   = $ap['country_code'].$ap['mobile_no'];
        $otp      = mt_rand(1000,9999);
        
        $member_data = $this->mapi->getRow('master_member',array('mobile' => $ap['mobile_no']));
        if(!empty($member_data)){
          
          $message  = $otp." is the OTP."."\n Team Club Fenicia";
          $response_sms = $this->smsSend($ap['mobile_no'],$message);

          $update_arr = array('otp' =>$otp,'otp_generating_datetime' =>date('Y-m-d H:i'));
          $this->mapi->update('master_member',array('mobile' => $ap['mobile_no'],'country_code' =>$ap['country_code']),$update_arr);
          //echo $response_sms;exit;
          $response['status']['error_code'] = 0;
          $response['status']['message']    = "OTP Successfully Generated.";
          $response['response']['otp']      = $otp;
        }
        else{
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'This mobile no. is not registered with the site';
        }
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields';        
      }
    }
    else{
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';        
    }
    $this->displayOutput($response);
  }
  public function smsSend($mobile,$message){
    //echo $mobile."<br>".$message;exit;
    //$api_key = '45DB969F6550A9';
    $api_key = '45DA414F762394';
    //$contacts = '97656XXXXX,97612XXXXX,76012XXXXX,80012XXXXX,89456XXXXX,88010XXXXX,98442XXXXX';
    $contacts= $mobile;
    //$from = 'FENCIA';
    $from = 'TXTSMS';
    $sms_text = urlencode($message);
    //Submit to server
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, "https://sms.hitechsms.com/app/smsapi/index.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "key=".$api_key."&campaign=0&routeid=13&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text);
    $response = curl_exec($ch);
    curl_close($ch);
    //echo $response;exit;
    if(mb_substr($response, 0, 3)=='ERR'){
        return false;
    }else{
        return $response;
    }
    //print_r($response);

  }
  public function loginMobileNoWithOtp()
  {
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        if (empty($ap['device_type'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Device type is required.';
         
          $this->displayOutput($response);
        }
        if (empty($ap['countryCode'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'countryCode field required.';
          
          $this->displayOutput($response);
        }
        if (empty($ap['phoneNo'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'phoneNo field required.';
          
          $this->displayOutput($response);
        }
        if (empty($ap['otp'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Otp required.';
          
          $this->displayOutput($response);
        }
        
        $check_member_condition = array('otp' => $ap['otp'],'country_code' =>$ap['countryCode'],'mobile'=>$ap['phoneNo']);
        $memberdetails          = $this->mapi->getRow('master_member', $check_member_condition);
        //pr($memberdetails);
        if(empty($memberdetails)){
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Invalid Otp';
            $this->displayOutput($response);
        }
        else{
            $otp_generating_time =  $memberdetails['otp_generating_datetime'];          
            $current_time = date('Y-m-d H:i');
            //echo date('Y-m-d H:i',strtotime($otp_generating_time. '+30 minutes'));exit;
            if(strtotime($current_time) > strtotime($otp_generating_time. '+30 minutes')){              
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Otp expired';
              $this->displayOutput($response);
            } 
            elseif($memberdetails['is_delete'] != '0'){
                $response['status']['error_code'] = 1;
                $response['status']['message']    = 'Member account is no more exists';
                $this->displayOutput($response);
            }
            elseif($memberdetails['status'] == '0'){
                $response['status']['error_code'] = 1;
                $response['status']['message']    = 'Member account is not active';
                $this->displayOutput($response);
            }
            else{
                $member_id      = $memberdetails['member_id'];
                $condition      = array('member_id' =>$member_id);
                $update_arr     = array('login_status' =>'1');
                $update_result  = $this->mapi->update('master_member',$condition,$update_arr);

                if($update_result){              
                    $response['status']['error_code'] = 0;
                    $response['status']['message']    = 'Login Successfully';
                    $response['response']['member']   = $memberdetails;
                    $api_token_details                = $this->mapi->getRow('api_token', $condition);
                    $device_token_details             = $this->mapi->getRow('device_token', $condition);

                    if (empty($api_token_details) && empty($device_token_details)) {

                      $device_token_data['member_id']          = $memberdetails['member_id'];
                      $device_token_data['device_type']        = $ap['device_type'];
                      $device_token_data['device_token']       = '';
                      $device_token_data['fcm_token']          = $ap['device_token'];
                      $device_token_data['login_status']       = '1';
                      $device_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                      $device_token_data['session_start_time'] = date('Y-m-d H:i:s');
                      $device_token_data['session_end_time']   = '';

                      $insert_data          = $this->mapi->insert('device_token', $device_token_data);

                      $api_token_data['member_id']          = $memberdetails['member_id'];
                      $api_token_data['device_type']        = $ap['device_type'];
                      $api_token_data['access_token']       = md5(mt_rand() . '_' . $memberdetails['member_id']);
                      $api_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                      $api_token_data['session_start_time'] = date('Y-m-d H:i:s');
                      $api_token_data['session_end_time']   = '';

                      $insert_data  = $this->mapi->insert('api_token', $api_token_data);
                      $all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                      if(!empty($all_member)){
                        if($all_member[0]['membership_id'] ==''){
                          $all_member_details = $all_member[0];
                        }
                        else{
                          foreach($all_member as $val){
                              $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                              if(!empty($all_member_datas)){
                                  $all_member_details = $all_member_datas[0];
                              }
                              else{
                              	  $all_member_details = $all_member[0];
                              	  $all_member_details['membership_id'] = null;
                              }
                          }
                        }                       
                      }
                      else {
                        $response['status']['error_code'] = 1;
                        $response['status']['message']    = 'Unable to generate access token';                    
                      }
                      if ($all_member_details) {
                        if($all_member_details['profile_img'] !='' ){
                          $all_member_details['profile_pic_updated'] = '1';
                        }
                        else{
                          $all_member_details['profile_pic_updated'] = '0';
                        }
                        $response['status']['error_code'] = 0;
                        $response['status']['message']    = 'Login Successfully';
                        $response['response']['member']   = $all_member_details;
                        
                      } else {
                        $response['status']['error_code'] = 1;
                        $response['status']['message']    = 'Unable to generate access token';                    
                      }
                    } else {
                      $condition_token                    = array('member_id' =>$member_id);
                      $api_token_updata['device_type']    = $ap['device_type'];
                      $api_token_updata['access_token']   = $api_token_details['access_token'];
                      $update_data  = $this->mapi->update('api_token', $condition_token, $api_token_updata);

                      $device_token_updata['device_type']     = $ap['device_type'];
                      $device_token_updata['fcm_token']       = $ap['device_token'];
                      $update_data  = $this->mapi->update('device_token', $condition_token, $device_token_updata);

                      $all_member_details   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                      $all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                      if(!empty($all_member)){
                        if($all_member[0]['membership_id'] ==''){
                          $all_member_details = $all_member[0];
                        }
                        else{
                          foreach($all_member as $val){
                              $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                              if(!empty($all_member_datas)){
                                  $all_member_details = $all_member_datas[0];
                              }
                              else{
                              	  $all_member_details = $all_member[0];
                              	  $all_member_details['membership_id'] = null;
                              }
                          }
                        }                       
                      }
                      else {
                        $response['status']['error_code'] = 1;
                        $response['status']['message']    = 'Unable to generate access token';                    
                      }
                      if ($all_member_details) {
                        if($all_member_details['profile_img'] !='' ){
                          $all_member_details['profile_pic_updated'] = '1';
                        }
                        else{
                          $all_member_details['profile_pic_updated'] = '0';
                        }
                        $response['status']['error_code'] = 0;
                        $response['status']['message']    = 'Login Successfully';
                        $response['response']['member']   = $all_member_details;
                      } else {
                        $response['status']['error_code'] = 1;
                        $response['status']['message']    = 'Unable to update access token';                   
                      }
                    }
                }
                else {
                  $response['status']['error_code'] = 1;
                  $response['status']['message']    = 'Oops!something went wrong...';
                }          
            }
        }        
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields';        
      }
    }
    else{
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';        
    }
    $this->displayOutput($response);  
  }
  public function forgotPassword()
  {
    $ap = json_decode(file_get_contents('php://input'), true);

    if ($this->checkHttpMethods($this->http_methods[0])) {

      if (sizeof($ap)) {
       
        if (empty($ap['email'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'DC Number field is required';
          $this->displayOutput($response);
        }

        $condition['email'] = $ap['email'];
        $member_details = $this->mapi->getRow('master_member', $condition);

        if (empty($member_details)) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Sorry! User does not exist in our database';
          $this->displayOutput($response);
        } 
        else {
          $encoded_key = base64_encode(rand());
          $member_details['recovery_key']   = $encoded_key;
          $condition_member = array('member_id' => $member_details['member_id']);
          $this->mapi->update('master_member', $condition_member, $member_details);

          
          $mail['name']     = $member_details['first_name'];
          $mail['to']       = $member_details['email'];
          $mail['subject']  = 'Club Fenicia - Recover Password';

          $link = base_url('recoverPasswordUser/' . $encoded_key);
          $mail_temp = file_get_contents('./global/mail/forgotpassword_template.html');
          $mail_temp = str_replace("{web_url}", SITEURL, $mail_temp);
          $mail_temp = str_replace("{logo}", base_url('public/images/logo.png'), $mail_temp);
          $mail_temp = str_replace("{shop_name}", 'Club Fenicia', $mail_temp);
          $mail_temp = str_replace("{name}", $mail['name'], $mail_temp);
          $mail_temp = str_replace("{link}", $link, $mail_temp);
          $mail_temp = str_replace("{current_year}", CURRENT_YEAR, $mail_temp);
          $mail['message'] = $mail_temp;
          $from_email         = 'clubfenicia@fenicialounge.in';
          //echo '<pre>';print_r($mail);die;
          if ($this->sendMail($mail,'Club Fenicia',$from_email)) {
            $response['status']['error_code'] = 0;
            $response['status']['message']    = 'Password recovery mail has been sent to your email';
          } 
          else {
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Uanble to send recovery mail.';
          }
        }
      } 
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
      }
    } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';      
    }
    $this->displayOutput($response);
  }
public function changeMobileNo()
{
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      	if (sizeof($ap)) {	
      		if (empty($ap['new_country_code'])) {
	          $response['status']['error_code'] = 1;
	          $response['status']['message']    = 'New country code is required.';
	         
	          $this->displayOutput($response);
	        }
	        if (empty($ap['new_mobile_no'])) {
	          $response['status']['error_code'] = 1;
	          $response['status']['message']    = 'New mobile no is required.';
	         
	          $this->displayOutput($response);
	        }
	        if (!empty($ap['new_mobile_no'])) {
	        	$result = $this->mapi->checkUserRegistered(array('mobile' => $ap['new_mobile_no']));
	        	if (!empty($result)) {
		            $response['status']['error_code'] = 1;
		            $response['status']['message']    = 'Mobile No. Already registered';
		            //$response['response']   = $this->obj;
		            
		            $this->displayOutput($response);
	            }
          	}
            $member_id      = $ap['member_id'];
            $access_token   = $ap['access_token'];
            $device_type   	= $ap['device_type'];      
            //$condition      = array('mm.member_id' =>$member_id,'api_token.access_token' =>$device_token);
           	$access_token_result = $this->check_access_token($access_token, $device_type,$member_id);
  	        if (empty($access_token_result)) {
  	        	  $response['status']['error_code'] = 1;
  	            $response['status']['message']    = 'Unauthorize Token';				
  				      $this->displayOutput($response);
  	        } 
          	else{
          		$update_data	= array('country_code' 	=> $ap['new_country_code'],
          								'mobile' 		=> $ap['new_mobile_no'],
          								'updated_by'	=> $member_id,
          								'updated_ts'	=> date('Y-m-d H:i:s')
          								);
          		$cond_update	= array('member_id' =>$member_id);
          		$update_mobile	= $this->mapi->update('master_member',$cond_update,$update_data);
          		if($update_mobile){
          			$response['status']['error_code'] = 0;
        				$response['status']['message']    = 'Mobile No. updated successfully.';
          		}
          		else{
          			$response['status']['error_code'] = 1;
        				$response['status']['message']    = 'Invalid Mobile No.';
          		}

          	}
              $this->displayOutput($response); 
        }
    }    
}
public function changeEmail()
{
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
        if (sizeof($ap)) {  
          if (empty($ap['new_email'])) {
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'New email is required.';
           
            $this->displayOutput($response);
          }          
          if (!empty($ap['new_email'])) {
            $result = $this->mapi->checkUserRegistered(array('email' => $ap['new_email'],'member_id !=' => $ap['member_id']));
            if (!empty($result)) {
                $response['status']['error_code'] = 1;
                $response['status']['message']    = 'Email Already registered';
                //$response['response']   = $this->obj;
                
                $this->displayOutput($response);
              }
            }
            $member_id      = $ap['member_id'];
            $access_token   = $ap['access_token'];
            $device_type    = $ap['device_type'];      
            //$condition      = array('mm.member_id' =>$member_id,'api_token.access_token' =>$device_token);
            $access_token_result = $this->check_access_token($access_token, $device_type,$member_id);
            if (empty($access_token_result)) {
                $response['status']['error_code'] = 1;
                $response['status']['message']    = 'Unauthorize Token';        
                $this->displayOutput($response);
            } 
            else{
              $update_data  = array(
                          'email'       => $ap['new_email'],
                          'updated_by'  => $member_id,
                          'updated_ts'  => date('Y-m-d H:i:s')
                          );
              $cond_update  = array('member_id' =>$member_id);
              $update_email  = $this->mapi->update('master_member',$cond_update,$update_data);
              if($update_email){
                $response['status']['error_code'] = 0;
                $response['status']['message']    = 'New Email Is updated successfully.';
              }
              else{
                $response['status']['error_code'] = 1;
                $response['status']['message']    = 'Invalid email';
              }

            }
              $this->displayOutput($response); 
        }
    }    
}
public function viewProfile()
{
	if ($this->checkHttpMethods($this->http_methods[0])) {
      	if (!empty($this->input->post())) {
      		$member_id     			= $this->input->post( 'member_id' );
            $access_token   		= $this->input->post( 'access_token');  
            $device_type   			= $this->input->post( 'device_type'); 
            $access_token_result 	= $this->check_access_token($access_token, $device_type,$member_id);
            //pr($member_details);
            if (empty($access_token_result)) {
        	  	$response['status']['error_code'] = 1;
	            $response['status']['message']    = 'Unauthorize Token';				
		      	$this->displayOutput($response);
	        }
	        else{
	        	$all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
            if(!empty($all_member)){
              if($all_member[0]['membership_id'] ==''){
                $all_member_details = $all_member[0];
              }
              else{
                foreach($all_member as $val){
                    $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                    if(!empty($all_member_datas)){
                        $all_member_details = $all_member_datas[0];
                    }
                    else{
	                  	  $all_member_details = $all_member[0];
	                  	  $all_member_details['membership_id'] = null;
	                  }
                }
              }                       
            }
            else {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Unable to generate access token';                    
            }
	        	if(!empty($all_member_details)){
    					if($all_member_details['profile_img'] !='' ){
    						$all_member_details['profile_pic_updated'] = '1';
    					}
    					else{
    						$all_member_details['profile_pic_updated'] = '0';
    					}
        			$response['status']['error_code'] = 0;
          		$response['status']['message']    = 'Member Profile';
          		$response['response']['member']   = $all_member_details;
	        	}
	        	else{
	        		$response['status']['error_code'] = 1;
	            	$response['status']['message']    = 'User does not exist';				
					
	        	}
	        }
  		}
  		else {
			$response['status']['error_code'] = 1;
			$response['status']['message']    = 'Please fill up all required fields.';
      	}
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;
      
    }
	$this->displayOutput($response);
}

public function editProfile()
{
    if ($this->checkHttpMethods($this->http_methods[0])) {
      	if (!empty($this->input->post())) {
      	      		//pr($_POST);	        
	        if (empty($this->input->post( 'first_name' ))) {
	          $response['status']['error_code'] = 1;
	          $response['status']['message']    = 'First Name field is required';
	          //$response['response']   = $this->obj;
	          $this->displayOutput($response);
	        }
	        if (empty($this->input->post( 'last_name' ))) {
	          $response['status']['error_code'] = 1;
	          $response['status']['message']    = 'Last Name field is required';
	          //$response['response']   = $this->obj;
	          
	          $this->displayOutput($response);
	        }
	        if (empty($this->input->post( 'email' ))) {
	          $response['status']['error_code'] = 1;
	          $response['status']['message']    = 'Email field is required';
	          //$response['response']   = $this->obj;
	          
	          $this->displayOutput($response);
	        }
	        if (!empty($this->input->post( 'email' ))) {

	          $result = $this->mapi->editCheckUserRegistered(array('email' => $this->input->post( 'email' ),'member_id !=' =>$this->input->post( 'member_id' )));
	          if (!empty($result)) {
	            $response['status']['error_code'] = 1;
	            $response['status']['message']    = 'Email Already registered';
	            //$response['response']   = $this->obj;
	            $this->displayOutput($response);
	          }
	        }
	        if (empty($this->input->post( 'mobile' ))) {
	          $response['status']['error_code'] = 1;
	          $response['status']['message']    = 'Mobile field is required';
	          //$response['response']   = $this->obj;
	          $this->displayOutput($response);
	        }
	        if (!empty($this->input->post( 'mobile' ))) {

	          $result = $this->mapi->editCheckUserRegistered(array('mobile' => $this->input->post( 'mobile' ),'member_id !=' =>$this->input->post( 'member_id' )));
	          if (!empty($result)) {
	            $response['status']['error_code'] = 1;
	            $response['status']['message']    = 'Mobile Already registered';
	            $this->displayOutput($response);
	          }
	        }
	        if (empty($this->input->post( 'dob' ))) {
	          $response['status']['error_code'] = 1;
	          $response['status']['message']    = 'Date of birth field is required';
	          //$response['response']   = $this->obj;
	          
	          $this->displayOutput($status);
	        }
	        if (empty($this->input->post( 'gender' ))) {
	          $response['status']['error_code'] = 1;
	          $response['status']['message']    = 'Gender of birth field is required';
	          //$response['response']   = $this->obj;
	          
	          $this->displayOutput($status);
	        }	        
	       	if(!empty($_FILES['profile_image']['name'])){
	          $image_path = '/public/upload_image/profile_photo';
	          $file     = $this->imageupload->image_upload2($image_path,'profile_image');
	          if($file['status'] == 1){
	             $img = $file['result'];
	          }
	          else{
	          	$img = '';
	          } 
	        }
	        else{
	          	$img = '';
	        }
	        if(empty($this->input->post('fb_id'))){
	          $registration_type  = '2';
	        }
	        else{
	          $registration_type  = '3';
	        }
	        if($this->input->post('doa') !=''){
	          $doa              = date('Y-m-d',strtotime(str_replace('/','-',$this->input->post('doa'))));         
	        }
	        else{
	          $doa              = '';
	        }
	          $member_id     			= $this->input->post( 'member_id' );
            $access_token   		= $this->input->post( 'access_token');  
            $device_type   			= $this->input->post( 'device_type'); 
            $access_token_result 	= $this->check_access_token($access_token, $device_type,$member_id);
            //pr($member_details);
            if (empty($access_token_result)) {
	        	$response['status']['error_code'] = 1;
	          $response['status']['message']    = 'Unauthorize Token';				
				    $this->displayOutput($response);
	        } 
          else{
		        
            $update_arr['first_name']       = $this->input->post('first_name');
            $update_arr['last_name']        = $this->input->post('last_name');     
            $update_arr['country_code']     = $this->input->post('country_code');
            $update_arr['mobile']           = $this->input->post('mobile');
            $update_arr['email']            = $this->input->post('email');
            $update_arr['gender']           = $this->input->post('gender');
            $update_arr['marriage_status']  = $this->input->post('marriage_status');
            $update_arr['dob']              = date('Y-m-d',strtotime(str_replace('/','-',$this->input->post('dob'))));
            $update_arr['doa']              = $doa;
            if(!empty($img)){
              $update_arr['profile_img']    = $img;
            }            
            $update_arr['status']           = '1';
            $update_arr['registration_type']= $registration_type;
            $update_arr['fb_id']            = $this->input->post('fb_id');
            $update_arr['added_form']       = $this->input->post('fb_id');
            $update_arr['login_status']     = $this->input->post('fb_id');
            $update_arr['updated_by']       = $member_id;
            $update_arr['updated_ts']       = date('Y-m-d H:i:s');
		        $condition  = array('member_id' => $this->input->post('member_id'));
		        $this->mapi->update('master_member',$condition,$update_arr);         
		        if($member_id)
		        {           
		            $all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                if(!empty($all_member)){
                  if($all_member[0]['membership_id'] ==''){
                    $all_member_details = $all_member[0];
                  }
                  else{
                    foreach($all_member as $val){
                        $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                        if(!empty($all_member_datas)){
                            $all_member_details = $all_member_datas[0];
                        }
                        else{
	                  	  	$all_member_details = $all_member[0];
	                  	  	$all_member_details['membership_id'] = null;
                        }
                    }
                  }                       
                }
                else {
                  $response['status']['error_code'] = 1;
                  $response['status']['message']    = 'Unable to generate access token';                    
                }
	            if(!empty($signup) && !empty($signup['dob'])){

	              $signup['dob']  = date('d/m/Y',strtotime($signup['dob']));
	            }
	            if(!empty($signup) && $signup['doa'] !='0000-00-00'){
	              $signup['doa']  = date('d/m/Y',strtotime($signup['doa']));
	            }
	            else{
	              $signup['doa']  = '';
	            }
	            if($all_member_details['profile_img'] !='' ){
                  $all_member_details['profile_pic_updated'] = '1';
                }
                else{
                  $all_member_details['profile_pic_updated'] = '0';
                }
					  $response['status']['error_code'] = 0;
  					$response['status']['message']    = 'Profile updated Successfully.';
  					$response['response']['member']   = $all_member_details;
	            } else {
	              $response['status']['error_code'] = 1;
	              $response['status']['message']    = 'Opps!Some problem occure,please try again.';
	              //$response['response']   = $this->obj;           
	            }
		    }
  	  }
      else {
  			$response['status']['error_code'] = 1;
  			$response['status']['message']    = 'Please fill up all required fields.';
    	}
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;5656
    }
    $this->displayOutput($response);      
}
public function changeNotificationStatus()
{
	$ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      	if (sizeof($ap)) {	
      		$member_id     			    = $ap['member_id'];
            $access_token   		  = $ap['access_token'];  
            $device_type   			  = $ap['device_type'];
            $notification_status  = $ap['notification_status'];  
            $access_token_result 	= $this->check_access_token($access_token, $device_type,$member_id);
            //pr($member_details);
            if (empty($access_token_result)) {
	        	$response['status']['error_code'] = 1;
	            $response['status']['message']    = 'Unauthorize Token';				
				$this->displayOutput($response);
	        }
	        else{
	        	$notification_cond		= array('member_id' =>$member_id);
	        	$update_notification	= array('notification_allow_type' 	=> $notification_status,
	        									'updated_ts'	=> strtotime(date('Y-m-d H:i:s'))
	        									);
	        	$updatenotification 	= $this->mapi->update('master_member',$notification_cond,$update_notification);
	        	if(!empty($updatenotification)){
	        		$response['status']['error_code'] = 0;
                    $response['status']['message']    = 'Updated Successfully';
	        	}
	        	else{
	        		$response['status']['error_code'] = 1;
	            	$response['status']['message']    = 'Opps!Some problem occure,please try again.';				
					
	        	}
	        }

  		}
  		else {
			$response['status']['error_code'] = 1;
			$response['status']['message']    = 'Please fill up all required fields.';
      	}
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;
      
    }
	$this->displayOutput($response);
}
public function notificationList()
{
	$ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      	if (sizeof($ap)) {	
      		  $member_id     			  = $ap['member_id'];
            $access_token   		  = $ap['access_token'];  
            $device_type   			  = $ap['device_type'];
            $access_token_result 	= $this->check_access_token($access_token, $device_type,$member_id);
            //pr($member_details);
            if (empty($access_token_result)) {
	        	$response['status']['error_code'] = 1;
            $response['status']['message']    = 'Unauthorize Token';				
				    $this->displayOutput($response);
	        }
	        else{
	        	$notif_cond		= array('notification.member_id' =>$member_id,'notification.status' =>'1','created_on >=' => date('Y-m-d'));            
	        	$notification_data		 = $this->mapi->getNotificationList($notif_cond);
            //echo $this->db->last_query(); die;
	        	//pr($notification_data);
	        	if(!empty($notification_data)){
	        		$response['status']['error_code'] 			= 0;
              $response['status']['message']    			= ' ';
              $response['response']['notification_list']  = $notification_data;
	        	}
	        	else{
	        		$response['status']['error_code'] = 1;
	            	$response['status']['message']    = 'No data available.';				
					
	        	}
	        }

  		}
  		else {
			$response['status']['error_code'] = 1;
			$response['status']['message']    = 'Please fill up all required fields.';
      	}
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;
      
    }
	$this->displayOutput($response);
}
public function buyMembership(){
  $package_type_name  = '';
  $package_name       = '';
  $package_price      = '';
  $result  = array();
  $ap      = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {  
  		$member_id          	       = $ap['member_id'];
  		$access_token                = $ap['access_token'];  
  		$device_type        	       = $ap['device_type'];
  		$package_id					         = $ap['package_id'];
  		$package_price				       = $ap['package_price'];
  		$package_price_id			       = $ap['package_price_id'];
  		$package_type				         = $ap['package_type'];
  		$membership_transaction_id	 = $ap['membership_transaction_id'];
  		$payment_mode				         = $ap['payment_mode'];
      	$access_token_result 		= $this->check_access_token($access_token, $device_type,$member_id);
        //pr($member_details);
		if (empty($access_token_result)) {
			$response['status']['error_code'] = 1;
			$response['status']['message']    = 'Unauthorize Token';        
			$this->displayOutput($response);
		}
		else{
			  $pkg_condition	= array('member_id'=>$member_id,'status' =>'1');
    		$package_data		= $this->mcommon->getRow('package_membership_mapping',$pkg_condition);
    		//pr($package_data);
    		if(!empty($package_data)){
    			$update_data	= array('status'=> '0');
    			$this->mcommon->update('package_membership_mapping',$pkg_condition,$update_data);
    		}
			if($package_type =='Yearly'){
				$expiry_date 	= date('Y-m-d', strtotime(' +1 year'));
			}
			else{
				$expiry_date = date('Y-m-d', strtotime(' +1 month'));
			}		        		
			$pck_array_data 	= array('package_id'	  => $package_id,
										'package_price_id'	    => $package_price_id,
        								'member_id' 		        => $member_id,
        								'added_from' 		        => 'front',
        								'buy_on'			          => date('Y-m-d'),
        								'expiry_date'		        => $expiry_date,
        								'status'			          => '1'
        							);
			$membership_data  = $this->mcommon->insert('package_membership_mapping',$pck_array_data);
			if(!empty($membership_transaction_id)){
				$pck_trans_array_data 	= array('transaction_id' 	  => $membership_transaction_id,
                										    'package_id'			  => $package_id,
                        								'member_id' 			  => $member_id,
                        								'added_form' 			  => 'front',
                        								'amount'				    => $package_price,
                        								'transaction_date'	=> date('Y-m-d'),        								
                        								'payment_mode'			=> $payment_mode,
                        								'payment_status'		=> '1'
                        								);
				$this->mcommon->insert('package_membership_transaction',$pck_trans_array_data);
			}
		//pr($package_membership_list);
			if(!empty($membership_data)){

        $joindata   = array('select'        =>'package_price_mapping.package_type_id,package_type.package_type_name,package_price_mapping.price,master_package.package_name',
                            'first_table'   =>'package_price_mapping',
                            'second_table'  =>'package_type',
                            'dependency1'   =>'package_price_mapping.package_type_id = package_type.package_type_id',
                            'join_type1'    =>'inner',
                            'third_table'   =>'master_package',
                            'dependency2'   =>'master_package.package_id = package_price_mapping.package_id',
                            'join_type2'    =>'inner'                     
                        );        
        $condition  = array('package_price_mapping.package_price_mapping_id'=>$package_price_id);   
        $package_type_data  = $this->mcommon->joinQuery($joindata,$condition,'row','','');  
          //pr($package_type_data);
        if(!empty($package_type_data)){            
          $package_type_name  = $package_type_data['package_type_name'];
          $package_name       = $package_type_data['package_name'];
          $package_price      = $package_type_data['price'];
        }
        $message_data         = array('title' => 'Buy membership','message' => 'Your club membership is in pending status.');
        $user_fcm_token_data  = $this->mcommon->getRow('device_token',array('member_id' => $member_id));
        //pr($user_fcm_token_data);
        if(!empty($user_fcm_token_data)){
          $member_datas   = $this->mcommon->getRow('master_member',array('member_id' => $member_id));

          $user_name      = $member_datas['first_name'];
          $user_email     = $member_datas['email'];
          $user_mobile    = $member_datas['mobile'];

          if($member_datas['notification_allow_type'] == '1'){
            if($ap['device_type'] == 1){
              $this->pushnotification->send_ios_notification($user_fcm_token_data['fcm_token'], $message_data);
            }
            else{
              $this->pushnotification->send_android_notification($user_fcm_token_data['fcm_token'], $message_data);
            }
          }          
          $notification_arr = array('member_id'                 => $member_id,
                                    'notification_title'        => 'Buy membership',
                                    'notification_description'  => 'Your club membership is in pending status.',
                                    'status'                    => '1',
                                    'created_on'                => date('Y-m-d H:i:s')
                                    );
          $insert_data      = $this->mcommon->insert('notification', $notification_arr);
        }
        $package_membership_list  = $this->mapi->getMembershipDetails($member_id);
			  $response['status']['error_code']            = 0;
			  $response['status']['message']               = 'Your club membership is in pending status.';
        $response['response']['membership_details']  = $package_membership_list;

        /****************** Send password to the member ****************************/
             
              $logo               =   base_url('public/images/logo.png');
              $params['name']     =   $user_name;
              $params['to']       =   $user_email; 
              //$params['to']     =   'sreelabiswas.kundu@met-technologies.com';
              $details            =   "Membership name: ".$package_name."<br>"."Membership type: ".$package_type_name."<br>"."Membership Price:(₹) ".$package_price."<br>"."Membership Status: Your Club Membership is under process";                     
              $params['subject']  =   'Club Fenicia - Membership subscription Mail';                             
              $mail_temp          =   file_get_contents('./global/mail/membership_subscription.html');
              $mail_temp          =   str_replace("{web_url}", base_url(), $mail_temp);
              $mail_temp          =   str_replace("{logo}", $logo, $mail_temp);
              $mail_temp          =   str_replace("{shop_name}", 'Club Fenicia', $mail_temp);  
              $mail_temp          =   str_replace("{name}", $params['name'], $mail_temp);
              $mail_temp          =   str_replace("{membership_name}", $package_name, $mail_temp);
              $mail_temp          =   str_replace("{details}", $details, $mail_temp);
              $mail_temp          =   str_replace("{current_year}", date('Y'), $mail_temp);           
              $params['message']  =   $mail_temp;
              $msg                =   registration_mail($params);


              $message  = "Thank you for purchasing the Membership package of Club Fenicia \n . Your Membership activation is under process.";
              //$message  .=   "Membership name: ".$package_name.", Membership type: ".$package_type_name.", Membership Price: ".$package_price."Membership Status: Under process";
              
              $this->smsSend($user_mobile,$message);

			}
			else{
			  $response['status']['error_code'] = 1;
			  $response['status']['message']    = 'Sorry! buy membership is unsuccessful.';
			}
		}
    }
    else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Please fill up all required fields.';
    }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
/************ New Added on 02/03/2020 ******************/

public function membershipPaymentCheck(){
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {  
        $member_id            = $ap['member_id'];
        $access_token         = $ap['access_token'];  
        $device_type          = $ap['device_type'];
        $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //$access_token_result  =1;
        //pr($member_details);
        if (empty($access_token_result)) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Unauthorize Token';        
          $this->displayOutput($response);
        }
        else{
        //$membship_cond      = "and pmm.member_id ='".$member_id."'";
          $membship_cond        = " ";
          $membership_data      = $this->mapi->getMembershipPaymentCheck($member_id);
          //pr($membership_data);
          if(!empty($membership_data)){
            ///chk expired////////
            $tomorrow = date("Y-m-d");
            if(strtotime($tomorrow)>strtotime($membership_data['expiry_date']))
            {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Membership payment not done.';        
              $this->displayOutput($response);
            }
            else
            {
              $response['status']['error_code']         = 0;
              $response['status']['message']            = 'Membership Payment Check';
              $response['response']['membership_list']  = "Membership payment done.Membership approval is pending from admin end.";
            }
            
          }
          else{
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Membership payment not done.';
          }
        }
      }
      else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Please fill up all required fields.';
        }
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;
      
    }
    $this->displayOutput($response);
}
public function membershipList()
{
	$ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      	if (sizeof($ap)) {	
      		  $member_id     		= $ap['member_id'];
            $access_token   		= $ap['access_token'];  
            $device_type   			= $ap['device_type'];
            $access_token_result 	= $this->check_access_token($access_token, $device_type,$member_id);
            //pr($member_details);
            if (empty($access_token_result)) {
	        	  $response['status']['error_code'] = 1;
	            $response['status']['message']    = 'Unauthorize Token';				
				$this->displayOutput($response);
	        }
	        else{
	        	//$membship_cond			= "and pmm.member_id ='".$member_id."'";
            $membship_cond        = " ";
	        	$membership_data		  = $this->mapi->getMembershipData();
	        	//pr($membership_data);
	        	if(!empty($membership_data)){
	        		foreach($membership_data as $key => $val){
	        			$membership_list[$key]['membership_id'] 		  = $val['package_price_mapping_id'];
                $membership_list[$key]['package_id']          = $val['package_id'];
	        			$membership_list[$key]['membership_name'] 		= $val['package_name'];
	        			$membership_list[$key]['membership_desc'] 		= $val['package_description'];
                $membership_list[$key]['package_type_name']   = $val['package_type_name'];
                $membership_list[$key]['price']               = $val['price'];

	        		}
	        		$response['status']['error_code'] 			= 0;
              $response['status']['message']    			= 'Membership List';
              $response['response']['membership_list']  = $membership_list;
	        	}
	        	else{
	        		$response['status']['error_code'] = 1;
	            	$response['status']['message']    = 'Member does not exist';
	        	}
	        }
  		}
  		else {
			$response['status']['error_code'] = 1;
			$response['status']['message']    = 'Please fill up all required fields.';
      	}
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;
      
    }
	$this->displayOutput($response);
}
public function membershipDetails()
{

	$result = array();
	$ap = json_decode(file_get_contents('php://input'), true);
  //pr($ap);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      	if (sizeof($ap)) {	
      		  $member_id     			  = $ap['member_id'];
      		  $membership_id     		= $ap['membership_id'];
            $package_id           = $ap['package_id'];
            $access_token   		  = $ap['access_token'];  
            $device_type   			  = $ap['device_type'];
            $access_token_result 	= $this->check_access_token($access_token, $device_type,$member_id);
            //pr($member_details);
            if (empty($access_token_result)) {
	        	$response['status']['error_code'] = 1;
	            $response['status']['message']    = 'Unauthorize Token';				
				$this->displayOutput($response);
	        }
	        else{	        	
    				$package_data		= $this->mapi->getRow('master_package',array('package_id'=>$package_id));
    				/*$package_data['package']             = $this->mapi->getRow('master_package',array('package_id'=>$package_id));
            $package_data['benefits']            = $this->mapi->get_package_benefit_list($package_id);
            $package_data['gift_voucher_list']   = $this->mapi->get_package_voucher_list($package_id);
            $package_data['price_list']          = $this->mapi->get_package_price_list($package_id);
            $package_data['membership_image']    = $this->mapi->get_package_image_list($package_id);*/
            //pr($package_data);
	        	//pr($package);
	        	if(!empty($package_data)){
	        		
	        		$response['status']['error_code'] 			                            = 0;
              $response['status']['message']    			                            = 'Membership Details';
              $response['response']['membership_details']                         = $package_data;
              $benefits_list                                                      = $this->mapi->get_package_benefit_list($package_id);
              $benefits_list_tandc                                                = $this->mapi->getRow('package_benefits',array('package_benefit_id' =>'18'));
              $voucher_list                                                       = $this->mapi->get_package_voucher_list($package_id);
              $voucher_list_tandc                                                 = $this->mapi->getRow('package_vouchers',array('package_voucher_id' =>'11'));
             // $benefits_list[]                                                    = array_merge($benefits_list,$benefits_list_tandc);
              //$voucher_list[]                                                     = array_merge($voucher_list,$voucher_list_tandc); 
              //PR($benefits_list);
              $response['response']['membership_details']['benefits']             = $benefits_list; 
              $response['response']['membership_details']['gift_voucher_list']    = $voucher_list; 
              $response['response']['membership_details']['price_list']           = $this->mapi->get_package_price_list($package_id);
              $response['response']['membership_details']['membership_image']     = $this->mapi->get_package_image_list($package_id);
	        	}
	        	else{
	        		$response['status']['error_code'] = 1;
            	$response['status']['message']    = 'Member does not exist';
	        	}
        }
  		}
  		else {
			$response['status']['error_code'] = 1;
			$response['status']['message']    = 'Please fill up all required fields.';
      	}
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;
      
    }
	$this->displayOutput($response);
}
public function aboutusPage()
{
	$result = array(); 	
	$about_details		= $this->mapi->getRow('cms',array('cms_slug'=>'about_us','status' =>'1'));	
	//pr($about_details);
	if(!empty($about_details)){
		
		$response['status']['error_code'] 		= 0;
    $response['status']['message']    		= 'About Us';
    $response['response']['about_details']  = $about_details;
	}
	else{
		$response['status']['error_code'] = 1;
  	$response['status']['message']    = 'Member does not exist';
	}
	$this->displayOutput($response);
}
public function termsCondition()
{
	$result = array(); 	
	$terms_condition		= $this->mapi->getRow('cms',array('cms_slug'=>'terms-condition','status' =>'1'));	
	//pr($about_details);
	if(!empty($terms_condition)){
	    $response['status']['error_code'] 			= 0;
      $response['status']['message']    			= 'Terms Condition List ';
      $response['response']['terms_condition']  	= $terms_condition;
	}
	else{
		$response['status']['error_code'] = 1;
  	$response['status']['message']    = 'Member does not exist';
	}
	$this->displayOutput($response);
} 
public function privacyPolicy()
{
	$result = array(); 	
	$privacy_policy		= $this->mapi->getRow('cms',array('cms_slug'=>'privacy-policy','status' =>'1'));	
	//pr($about_details);
	if(!empty($privacy_policy)){
		
		$response['status']['error_code'] 			= 0;
        $response['status']['message']    			= 'Privacy Policy List';
        $response['response']['terms_condition']  	= $privacy_policy;
	}
	else{
		$response['status']['error_code'] = 1;
    	$response['status']['message']    = 'Member does not exist';
	}
	$this->displayOutput($response);
}
public function PreferredZone()
{
	$result = array();
	$ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      	if (sizeof($ap)) {	
      		$member_id     			  = $ap['member_id'];
          $access_token   		  	  = $ap['access_token'];  
          $device_type   			  = $ap['device_type'];
          $access_token_result 	= $this->check_access_token($access_token, $device_type,$member_id);
            //pr($member_details);
          if (empty($access_token_result)) {
        	  $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Unauthorize Token';				
			      $this->displayOutput($response);
	        }
	        else{	        		
				      $zone_list		= $this->mapi->getZoneDetails(array('status' =>'1'));	
				//pr($zone_list);
      				if(!empty($zone_list)){
      					
  					      $response['status']['error_code'] 	= 0;
    			        $response['status']['message']    		= 'Zone List';
      			      $response['response']['zone_list']  		= $zone_list;
      				}
      				else{
      					$response['status']['error_code'] = 1;
      			    	$response['status']['message']    = 'Sorry! No data found.';
      				}
			   }
		    }
		    else {
		        $response['status']['error_code'] = 1;
		        $response['status']['message']    = 'Please fill up all required fields.';
      	}
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;      
    }
	$this->displayOutput($response);
}
public function dashboardImages()
{
	
	$result = array();
	$ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      	if (sizeof($ap)) {	
      		$member_id     			= $ap['member_id'];
            $access_token   		= $ap['access_token'];  
            $device_type   			= $ap['device_type'];
            $access_token_result 	= $this->check_access_token($access_token, $device_type,$member_id);
            //pr($member_details);
            if (empty($access_token_result)) {
	        	$response['status']['error_code'] = 1;
	            $response['status']['message']    = 'Unauthorize Token';				
				$this->displayOutput($response);
	        }
	        else{	        		
				$dashboard_images		= $this->mapi->getDashboardDetails(array('status' =>'1'));	
				//pr($dashboard_images);
				if(!empty($dashboard_images)){
					
					$response['status']['error_code'] 			= 0;
			    $response['status']['message']    			= 'Dashboard Images';
			    $response['response']['dashboard_image_list']  	= $dashboard_images;
				}
				else{
					$response['status']['error_code'] = 1;
			    	$response['status']['message']    = 'Sorry! No data found.';
				}
			}
		}
		else {
			$response['status']['error_code'] = 1;
			$response['status']['message']    = 'Please fill up all required fields.';
      	}
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;      
    }
	$this->displayOutput($response);
}
public function feniciaSocial()
{
  
  $result  = array();
  $ap      = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {  
      $member_id            = $ap['member_id'];
      $access_token         = $ap['access_token'];  
      $device_type          = $ap['device_type'];
      $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($member_details);
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
     }
      else{                     
        $fenicia_social     = $this->mapi->getRow('master_social',array());  
        //pr($fenicia_social);
        if(!empty($fenicia_social)){           
          $response['status']['error_code']           = 0;
          $response['status']['message']              = 'FeniciaSocial List';
          $response['response']['fenicia_social']   = $fenicia_social;
        }
        else{
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'No data available';
        }
      }
    }
    else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Please fill up all required fields.';
    }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
public function reservationList()
{	
	$result  = array();
	$ap      = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {	
  		$member_id     			  = $ap['member_id'];
      $access_token   		  = $ap['access_token'];  
      $device_type   			  = $ap['device_type'];
      $access_token_result 	= $this->check_access_token($access_token, $device_type,$member_id);
        //pr($member_details);
      if (empty($access_token_result)) {
  	    $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';				
	      $this->displayOutput($response);
     }
      else{	
        $condition          = "reservation.member_id ='".$member_id."'";        		
				$reservation_list		= $this->mapi->getReservationList($condition);	
				//pr($fenicia_social);
				if(!empty($reservation_list)){
					foreach($reservation_list as $key=>$val){
			            $reservation_data[$key]['reservation_id']     =  $val['reservation_id'];
			            $reservation_data[$key]['reservation_date']   =  date('d/m/Y',strtotime($val['reservation_date']));
			            $reservation_data[$key]['reservation_time']   =  DATE('h:i A',strtotime($val["reservation_time"]));
			            $reservation_data[$key]['zone_id']            =  $val['zone_id'];
			            $reservation_data[$key]['zone_name']          =  $val['zone_name'];                      
			            $reservation_data[$key]['zone_price']         =  $val['zone_price'];
                  $reservation_data[$key]['total_amount']       =  $val['payment_amount'];
			            $reservation_data[$key]['no_of_guest']        =  $val['no_of_guests'];
			            $reservation_data[$key]['first_name']         =  $val['first_name'];
			            $reservation_data[$key]['last_name']          =  $val['last_name'];
			            $reservation_data[$key]['country_code']       =  $val['country_code'];
			            $reservation_data[$key]['phone_no']           =  $val['member_mobile'];
			            $reservation_data[$key]['email']              =  $val['email'];
			            $reservation_data[$key]['reservation_for']    =  $val['reservation_for'];
			            $reservation_data[$key]['message']            =  $val['message'];
			            $reservation_data[$key]['reservation_status'] =  $val['status']; //0=>canceled 1=>pending 2=>reserved 3=>rejected
			            $reservation_data[$key]['cancellation_reason']=  $val['cancellation_reason'];
			            $reservation_data[$key]['total_amount'] =  $val['payment_amount'];
			          } 
					$response['status']['error_code'] 			    = 0;
			    $response['status']['message']    			    = 'Reservation List';
			    $response['response']['reservation_list']  	= $reservation_data;
				}
				else{
					$response['status']['error_code'] = 1;
			    $response['status']['message']    = 'Sorry! No data found ';
				}
			}
		}
		else {
			$response['status']['error_code'] = 1;
			$response['status']['message']    = 'Please fill up all required fields.';
    }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
	$this->displayOutput($response);
}
public function calculateZonePrice($basic_price,$no_of_guests,$extra_guest,$additional_charges,$zone_type){
  //echo $zone_type."<br>".$basic_price;exit;
  if($zone_type !='party'){
    $zone_price = $basic_price + ($additional_charges*$extra_guest);
  }
  else{
    $zone_price = $basic_price * $no_of_guests;
  }
  return $zone_price;
}
public function reservationCharge(){
  $result       = array();
  $extra_guest  = 0;
  $ap      = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {  
      $member_id     = $ap['member_id'];
      $access_token  = $ap['access_token'];  
      $device_type   = $ap['device_type'];
      $zone_id       = $ap['zone_id'];
      $no_of_guests  = $ap['no_of_guests'];
      $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($member_details);
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
      }
      else{ 
        $condition  = array('zone_id' => $zone_id);            
        $zone_list  = $this->mapi->getRow('master_zone',$condition);  
       //pr($zone_list);
        if(!empty($zone_list)){
          
          $basic_price = $zone_list['cover_charges'];
          
          if($zone_id==5||$zone_id==6||$zone_id==8 ||$zone_id==9)
            {
              if($no_of_guests > $zone_list['minimum_capacity']){           
                
                $extra_guest   = $no_of_guests - 5;   
                if($extra_guest < 0){
                  $extra_guest = 0;
                }        
              }
            }
          $zone_price  = '';
          ///////////////////for dome zone/////////////////////////////////////////
          if($zone_id==15||$zone_id==16||$zone_id==17)
          {
            if($no_of_guests>=2&&$no_of_guests<=3)
            {
              $zone_price    = '7000';
            }
            if($no_of_guests>=4&&$no_of_guests<=5)
            {
              $zone_price    = '10000';
            }
            if($no_of_guests>=6&&$no_of_guests<=8)
            {
              $zone_price    = '12000';
            }
          }
          ///////////////////////////////////////////////////////////////////////
          else
          {
              $zone_price    = $this->calculateZonePrice($basic_price,$no_of_guests,$extra_guest,$zone_list['additional_charges'],$zone_list['zone_type']);
          }
          $response['status']['error_code']         = 0;
          $response['status']['message']            = 'Reservation Charge';
          if($zone_list['cover_charges'] !='0'){
            $response['response']['price_deatils']['cover_charges']    = $zone_price;
            $response['response']['price_deatils']['advance_charges']  = 0;
          }
          else{
            $response['response']['price_deatils']['cover_charges']    = 0;
            $response['response']['price_deatils']['advance_charges']  = $zone_price;
          }
        }
        else{
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Sorry! No data found ';
        }
      }
    }
    else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Please fill up all required fields.';
    }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
/*public function reservationCharge(){
  $result       = array();
  $extra_guest  = 0;
  $ap      = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {  
      $member_id     = $ap['member_id'];
      $access_token  = $ap['access_token'];  
      $device_type   = $ap['device_type'];
      $zone_id       = $ap['zone_id'];
      $no_of_guests  = $ap['no_of_guests'];
      $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($member_details);
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
      }
      else{ 
        $condition      = array('zone_id' => $zone_id);            
        $zone_list      = $this->mapi->getRow('master_zone',$condition); 
        $zone_pax_list  = $this->mapi->getDetails('zone_paxs',$condition);  
       //pr($zone_list);
        if(!empty($zone_list)){
          
          $basic_price = $zone_list['cover_charges'];
          
          if($no_of_guests > $zone_list['minimum_capacity']){           
            
            $extra_guest   = $no_of_guests - 5;            
          }
          $zone_price  = '';
          ///////////////////for pax dependent zone/////////////////////////////////////////
          if(!empty($zone_pax_list))
          {
            foreach ($zone_pax_list as $key => $val) {
                if($no_of_guests>= $val['minimum_pax'] && $no_of_guests<= $val['maximum_pax'] )
                {
                  $zone_price    = $val['pax_price'];
                }
            }            
          }
          ///////////////////////////////////////////////////////////////////////
          else
          {
              $zone_price    = $this->calculateZonePrice($basic_price,$no_of_guests,$extra_guest,$zone_list['additional_charges'],$zone_list['zone_type']);
          }
          $response['status']['error_code']         = 0;
          $response['status']['message']            = 'Reservation Charge';
          if($zone_list['cover_charges'] !='0'){
            $response['response']['price_deatils']['cover_charges']    = $zone_price;
            $response['response']['price_deatils']['advance_charges']  = 0;
          }
          else{
            $response['response']['price_deatils']['cover_charges']    = 0;
            $response['response']['price_deatils']['advance_charges']  = $zone_price;
          }
        }
        else{
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Sorry! No data found ';
        }
      }
    }
    else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Please fill up all required fields.';
    }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}*/
public function checkMembership(){

  $result  = array();
  $ap      = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {  
      $member_id            = $ap['member_id'];
      $access_token         = $ap['access_token'];  
      $device_type          = $ap['device_type'];
      $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($member_details);
      //$access_token_result  =1;
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
      }
      else{                   
        $package_membership_list  = $this->mapi->getMembershipDetails($member_id);  
        //pr($package_membership_list);
        if(!empty($package_membership_list)){
          if(!empty($package_membership_list['membership_id'])){

            ///chk expired////////
            $tomorrow = date("Y-m-d");
            if(strtotime($tomorrow)>strtotime($package_membership_list['expiry_date']))
            {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Your Membership has expired.';        
              $this->displayOutput($response);
            }
            $response['status']['error_code']                                   = 0;
            $response['status']['message']                                      = 'Success';
            $response['response']['membership_details']                         = $package_membership_list;
            $benefits_list                                                      = $this->mapi->get_package_benefit_list($package_membership_list['package_id']);
            $benefits_list_tandc                                                = $this->mapi->getRow('package_benefits',array('package_benefit_id' =>'18'));
            $voucher_list                                                       = $this->mapi->get_package_voucher_list($package_membership_list['package_id']);
            $voucher_list_tandc                                                 = $this->mapi->getRow('package_vouchers',array('package_voucher_id' =>'11'));
            $benefits_list[]                                                    = array_merge($benefits_list,$benefits_list_tandc);
            $voucher_list[]                                                     = array_merge($voucher_list,$voucher_list_tandc); 
            $response['response']['membership_details']['benefits']             = $benefits_list; 
            $response['response']['membership_details']['gift_voucher_list']    = $voucher_list;
            $response['response']['membership_details']['price_list']           = $this->mapi->get_package_price_list($package_membership_list['package_id']);
            $response['response']['membership_details']['membership_image']     = $this->mapi->get_package_image_list($package_membership_list['package_id'] );
            
          }
          else{
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Your Club Membership is under process.';
          }
        }
        else{
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Sorry you are not an active member of Club Fenicia.';
        }
      }
    }
    else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Please fill up all required fields.';
    }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
public function doReservation()
{
    $zone_name  = '';
    $result  = array();
    $ap      = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        if (empty($ap['first_name'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'First Name field is required';
          //$response['response']   = $this->obj;
          $this->displayOutput($response);
        }
        if (empty($ap['last_name'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Last Name field is required';
          //$response['response']   = $this->obj;
          $this->displayOutput($response);
        }       
        if (empty($ap['email'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Email field is required';
          //$response['response']   = $this->obj;
          
          $this->displayOutput($response);
        }
        if (empty($ap['phone_no'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Phone no. field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }
        if (empty($ap['no_of_guests'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'No. of guests field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }        
        if (empty($ap['reservation_date'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Reservation date field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }
        if (empty($ap['reservation_time'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Reservation time field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }       
        if (empty($ap['zone_id'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Zone field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }
        $member_id            = $ap['member_id'];
        $access_token         = $ap['access_token'];  
        $device_type          = $ap['device_type'];

        $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);

        if (empty($access_token_result)) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Unauthorize Token';        
          $this->displayOutput($response);
        }
        else{
          	$zone_data    = $this->mapi->getRow('master_zone',array("zone_id" => $ap['zone_id']));
          	if(!empty($zone_data)){            
	            // if($zone_data['cover_charges'] !='0'){
	            //   $zone_minimum_price = $zone_data['cover_charges'];
	            //   $zone_price_type    ='cover';
	            // }
	            // else{
	            //   $zone_minimum_price = $zone_data['advance_charges'];
	            //   $zone_price_type ='advance';
	            // }

              /** added by Ishani on 23.07.2020  **/
              $zone_minimum_price = $zone_data['cover_charges'];
              $zone_price_type    ='cover';
          	}
          	else{
	            $zone_minimum_price = '';
	            $zone_price_type    = '';
          	}
  		  	/*$current_dt  	= date('d-m-Y H:i:s');       
      	  	$rev_time 		= date('H:i:s',strtotime($ap["reservation_time"]));
  			$rev_dt 		= str_replace('/','-',$ap["reservation_date"]).$rev_time;
          	if(strtotime($current_dt.'+24 hours') >= strtotime($rev_dt)){
	            $response['status']['error_code'] = 1;
	            $response['status']['message']    = "Can not reserve, Reservation date should be 24 hours before.";
          	}*/
          	$current_dt  	= date('d-m-Y');
  			    $rev_dt 		= str_replace('/','-',$ap["reservation_date"]);
          	if(strtotime($current_dt.'+1 day') >= strtotime($rev_dt)){
	            $response['status']['error_code'] = 1;
	            $response['status']['message']    = "Reservation date should be after 1 day";
          	}
          	else{
	          	//echo "JHJK";exit;
	           // $selectedTime             = $ap['reservation_time'];
	           // //$start_time_range         = date('H:i:s',strtotime("-90 minutes", strtotime($selectedTime)));
	           // //$end_time_range           = date('H:i:s',strtotime("+90 minutes", strtotime($selectedTime)));
	           // //$reservation_condition    = "reservation_date= '".DATE('Y-m-d',strtotime(str_replace('/','-',$ap["reservation_date"])))."' and zone_id = '".$ap['zone_id']."' and member_id != '".$member_id."' and reservation_time between '".$start_time_range."' and '".$end_time_range."'";
	           //  $reservation_condition    ="";
	           // $reservation_list         = $this->mapi->getRow('reservation');
	            
	           // $this->db->last_query(); die;
	           // print_r($reservation_list);
	           // if(!empty($reservation_list)){
	              
	           //   $response['status']['error_code']           = 1;
	           //   $response['status']['message']              = 'Opp!Sorry the zone is already reserved for the given date & time';
	           //   $this->displayOutput($response);
	           // }
//else{
	                $insrtarry    = array('reservation_date'    => DATE('Y-m-d',strtotime(str_replace('/','-',$ap["reservation_date"]))),
	                                      'reservation_time'    => DATE('H:i:s',strtotime($ap["reservation_time"])),
	                                      'zone_id'             => $ap['zone_id'],
	                                      'no_of_guests'        => $ap['no_of_guests'],
	                                      'zone_price'          => $zone_minimum_price,
	                                      'zone_price_type'     => $zone_price_type,
	                                      'reservation_for'     => $ap['reservation_for'],
	                                      'member_id'           => $member_id,
	                                      'first_name'          => $ap['first_name'],
	                                      'last_name'           => $ap['last_name'],
	                                      'email'               => $ap['email'],
	                                      'country_code'        => $ap['country_code'],
	                                      'member_mobile'       => $ap['phone_no'],
	                                      'add_from'            => 'front',
	                                      'message'             => $ap['message'],
	                                      'status'              => '1',
                                        'reservation_type'    => 'App',
	                                      'created_by'          => $member_id,
	                                      'created_on'          => date('Y-m-d')
	                                    );
	                $reservation_id     = $this->mapi->insert('reservation',$insrtarry);
	                
	                if($reservation_id)
	                {
	                  	$transaction_arr    = array('reservation_id'  => $reservation_id,
	                                              'transaction_id'  => $ap['tran_id'],
	                                              'payment_mode'    => $ap['tran_type'],
	                                              'payment_amount'  => $ap['tran_amount'],
	                                              'transaction_date'=> date('Y-m-d'),
	                                              'payment_status'  => 'success'
	                                        );
	                  	$this->mapi->insert('reservation_payment_transaction',$transaction_arr);
	                  	$condition          = "reservation.reservation_id ='".$reservation_id."'";            
	                  	$reservation_list   = $this->mapi->getReservationList($condition);
	                  	if(!empty($reservation_list)){
		                	foreach($reservation_list as $key=>$val){
		                      $reservation_data['reservation_id']     =  $val['reservation_id'];
		                      $reservation_data['reservation_date']   =  date('d/m/Y',strtotime($val['reservation_date']));
		                      $reservation_data['reservation_time']   =  DATE('h:i A',strtotime($val["reservation_time"]));
		                      $reservation_data['zone_id']            =  $val['zone_id'];
		                      $reservation_data['zone_name']          =  $val['zone_name'];                      
		                      $reservation_data['zone_price']         =  $val['zone_price'];
                          $reservation_data['total_amount']       =  $val['payment_amount'];
		                      $reservation_data['no_of_guest']        =  $val['no_of_guests'];
		                      $reservation_data['first_name']         =  $val['first_name'];
		                      $reservation_data['last_name']          =  $val['last_name'];
		                      $reservation_data['country_code']       =  $val['country_code'];
		                      $reservation_data['phone_no']           =  $val['member_mobile'];
		                      $reservation_data['email']              =  $val['email'];
		                      $reservation_data['reservation_for']    =  $val['reservation_for'];
		                      $reservation_data['message']            =  $val['message'];
		                      $reservation_data['reservation_status'] =  $val['status']; //0=>canceled 1=>pending 2=>reserved 3=>rejected
		                      $reservation_data['cancellation_reason']=  $val['cancellation_reason'];

		                      $zone_name          = $reservation_data['zone_name'];
                                $reservation_id     = $reservation_data['reservation_id'];
		                      $reservation_date   = $reservation_data['reservation_date'];
		                      $reservation_time   = $reservation_data['reservation_time'];
                                $name               = $reservation_data['first_name'];
		                        $no_of_guest        = $reservation_data['no_of_guest'];
		                      $reservation_status = 'Pending';
	                        $cancellation_reason= $reservation_data['cancellation_reason'];
		                    } 
		                    $response['status']['error_code']           = 0;
		                    $response['status']['message']              = 'Request for Reservation submitted Successfully.';
		                    $response['response']['reservation_details']   = $reservation_data;

		                    if($reservation_data['reservation_status'] == 0){
		                      $title      = "Reservation Cancelled";
		                      $message    = "Your request for reservation is cancelled.";
		                    }
		                    elseif($reservation_data['reservation_status'] == 1){
		                      $title      ="Reservation Pending";
		                      $message    = "Reservation done,\n pending from admin.";
		                    }            
		                    elseif($reservation_data['reservation_status'] == 2){
		                      $title     ="Reservation Confirmed";
		                      $message   = "Your request for reservation is Confirmed.";
		                    }            
		                    else{
		                      $title     ="Reservation Rejected";
		                      $message   = "Your request for reservation is rejected by Club Fenicia. \n Reason given - ".$cancellation_reason;
		                    }
		                    $message_data = array('title' => $title,'message' => $message);
		                    $user_fcm_token_data  = $this->mcommon->getRow('device_token',array('member_id' => $member_id));
		                    //pr($user_fcm_token_data);
		                    if(!empty($user_fcm_token_data)){
                          $member_datas  = $this->mcommon->getRow('master_member',array('member_id' => $member_id));
                          if($member_datas['notification_allow_type'] == '1'){
                              if($ap['device_type'] == 1){
                                $this->pushnotification->send_ios_notification($user_fcm_token_data['fcm_token'], $message_data);
                              }
                              else{
                                $this->pushnotification->send_android_notification($user_fcm_token_data['fcm_token'], $message_data);
                              }
                          } 
                          $admin_notification_details = $name.' reservation request for '.$zone_name.' on '.$reservation_date.' at '.$reservation_time.' is '.$reservation_status;
		                      $notification_arr = array('member_id'                 => $member_id,                            
                                                    	'reservation_id'            => $reservation_id,
		                                                'notification_title'        => $title,
		                                                'notification_description'  => $message,
                                                    	'admin_notification_details'=> $admin_notification_details,
		                                                'status'                    => '1',
		                                                'created_on'                => date('Y-m-d H:i:s')
		                                                );
		                      $insert_data      = $this->mcommon->insert('notification', $notification_arr);
                          //echo $this->db->last_query(); die;
		                      /****************** Send Reservation details to the member ****************************/
		                     $admin_cond               = array('role_id' => '1','status' =>'1','is_delete' =>'1');
	      				          $admin_data               = $this->mcommon->getRow('user',$admin_cond);
	      				          if(!empty($admin_data)){
	      				            $admin_email            = $admin_data['email'];
	      				            $admin_name             = $admin_data['first_name'];
	      				          }
	      				          else{
	      				            $admin_email            = 'support@fenicia.in';
	      				            $admin_name             = 'admin';
	      				          }

		                      //$link                   = base_url('api/member_activation/'.$member_id);
		                      $logo                     = base_url('public/images/logo.png');
		                      $mail['name']             = $ap['first_name'];
		                      $mail['to']               = $ap['email'];    
		                      //$mail['to']               = 'sreelabiswas.kundu@met-technologies.com';
		                      $mail['zone_name']        = '<span style="color:#f9b92d"><strong>Zone: </strong></span>'.$zone_name.'<br>';
		                      $mail['reservation_date'] = $reservation_date;
		                      $mail['reservation_time'] = $reservation_time;
		                      $mail['no_of_guest']      = $no_of_guest;
		                      $mail['reservation_status'] = $reservation_status;
		                      $mail['subject']          = 'Club Fenicia - Reservation request received Mail';                             
		                      $mail_temp                = file_get_contents('./global/mail/reservation_template.html');
		                      $mail_temp                = str_replace("{web_url}", base_url(), $mail_temp);
		                      $mail_temp                = str_replace("{logo}", $logo, $mail_temp);
		                      $mail_temp                = str_replace("{shop_name}", 'Club Fenicia', $mail_temp);  
		                      $mail_temp                = str_replace("{name}", $mail['name'], $mail_temp);
		                      $mail_temp                = str_replace("{zone_name}", $mail['zone_name'], $mail_temp);
		                      $mail_temp                = str_replace("{reservation_date}", $mail['reservation_date'], $mail_temp);
		                      $mail_temp                = str_replace("{reservation_time}", $mail['reservation_time'], $mail_temp);
		                      $mail_temp                = str_replace("{no_of_guest}", $mail['no_of_guest'], $mail_temp);
		                      $mail_temp                = str_replace("{reservation_status}", $mail['reservation_status'], $mail_temp);        
		                      $mail_temp                = str_replace("{current_year}", date('Y'), $mail_temp);           
		                      $mail['message']          = $mail_temp;
		                      $from_email         = 'clubfenicia@fenicialounge.in';
		                      $this->sendMail($mail,'Club Fenicia',$from_email);

	                   		/****************** Send Reservation details to the Admin ****************************/

	                   		  $logo                     = base_url('public/images/logo.png');
		                      $mail['name']             = $admin_name;
		                      $mail['to']               = $admin_email;    
		                      //$mail['to']               = 'sreelabiswas.kundu@met-technologies.com';
		                      $mail['zone_name']        = '<span style="color:#f9b92d"><strong>Zone: </strong></span>'.$zone_name.'<br>';
		                      $mail['reservation_date'] = $reservation_date;
		                      $mail['reservation_time'] = $reservation_time;
		                      $mail['no_of_guest']      = $no_of_guest;
		                      $mail['reservation_status'] = $reservation_status;
		                      $mail['subject']          = 'Club Fenicia - Reservation request received Mail';                             
		                      $mail_temp                = file_get_contents('./global/mail/reservation_template.html');
		                      $mail_temp                = str_replace("{web_url}", base_url(), $mail_temp);
		                      $mail_temp                = str_replace("{logo}", $logo, $mail_temp);
		                      $mail_temp                = str_replace("{shop_name}", 'Club Fenicia', $mail_temp);  
		                      $mail_temp                = str_replace("{name}", $mail['name'], $mail_temp);
		                      $mail_temp                = str_replace("{zone_name}", $mail['zone_name'], $mail_temp);
		                      $mail_temp                = str_replace("{reservation_date}", $mail['reservation_date'], $mail_temp);
		                      $mail_temp                = str_replace("{reservation_time}", $mail['reservation_time'], $mail_temp);
		                      $mail_temp                = str_replace("{no_of_guest}", $mail['no_of_guest'], $mail_temp);
		                      $mail_temp                = str_replace("{reservation_status}", $mail['reservation_status'], $mail_temp);        
		                      $mail_temp                = str_replace("{current_year}", date('Y'), $mail_temp);           
		                      $mail['message']          = $mail_temp;
		                      $from_email         		= 'clubfenicia@fenicialounge.in';
		                      $this->sendMail($mail,'Club Fenicia',$from_email);


		                      /********************************** Send reservation details in sms *************************************************/

		                      $message  = "Thank you for confirming your Reservation at Club Fenicia. Your reservation details are: \n";
		                      $message .= "Zone: ".$zone_name."\n Date: ".$reservation_date."\n Time: ".$reservation_time."\n No. of Guests: ".$no_of_guest."\n Status: Pending";
		                      $message .= "WE WOULD BE HOLDING YOUR RESERVATION FOR 15 MINUTES FROM THE TIME OF RESERVATION AND IT WILL BE RELEASED WITHOUT ANY PRIOR INFORMATION.";
                          $this->smsSend($ap['phone_no'],$message);
		                    }
	                  	}
						else{
							$response['status']['error_code']           = 1;
							$response['status']['message']              = 'No data available';
						}                
	                }
	                else{
	                  $response['status']['error_code']           = 1;
	                  $response['status']['message']              = 'No data available';
	                }
//	          	}//
	          }
        }
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
      }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);

}
public function payUPayment()
{
	if ($this->checkHttpMethods($this->http_methods[0])) {
      	if (!empty($_POST)) {      		
            foreach($_POST as $key => $value) {    
		    	$posted[$key] = $value; 				
		  	}
		  	// echo '<pre>'; 
		  	// print_r($posted); 
		  	// echo '</pre>';
		  	// die;
        	$MERCHANT_KEY = "bDkBPkgq";
			$SALT = "9ATrBjip7O";
			// Merchant Key and Salt as provided by Payu.
			$PAYU_BASE_URL 		= "https://sandboxsecure.payu.in";		// For Sandbox Mode
			//$PAYU_BASE_URL 	= "https://secure.payu.in";			// For Production Mode
			$action = '';
			$formError = 0;
			if(empty($posted['txnid'])) {
			  // Generate random transaction id
			  $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
			} else {
			  $txnid = $posted['txnid'];
			}
			$hash = '';
			// Hash Sequence
			$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
			if(empty($posted['hash']) && sizeof($posted) > 0) {
			  	if(empty($posted['key'])|| empty($posted['txnid'])||empty($posted['amount'])||empty($posted['firstname']) ||empty($posted['email'])||empty($posted['phone'])||empty($posted['productinfo'])||empty($posted['surl'])  || empty($posted['furl'])|| empty($posted['service_provider'])) {
				    $formError = 1;
			  	} else {
				    //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
					$hashVarsSeq = explode('|', $hashSequence);
				    $hash_string = '';	
					foreach($hashVarsSeq as $hash_var) {
				      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
				      $hash_string .= '|';
				    }

		    		$hash_string .= $SALT;


		    		$hash = strtolower(hash('sha512', $hash_string));
		  		}
			} elseif(!empty($posted['hash'])) {
			  $hash = $posted['hash'];
			}
			if($formError==1)
			{					
				$response['status']['error_code'] = 1;
				$response['status']['message']    = 'Error Request.';
			}
			else
			{
				$response['status']['error_code']           = 0;
              	$response['status']['message']              = 'Successfully';
              	$response['response']['hash']   			= $hash;
			}
  		}
  		else {
			$response['status']['error_code'] = 1;
			$response['status']['message']    = 'Please fill up all required fields.';
      	}
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;
      
    }
	$this->displayOutput($response);	
}
public function updateReservation()
{
    
    $result  = array();
    $ap      = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {        
        if (empty($ap['first_name'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'First Name field is required';
          //$response['response']   = $this->obj;
          $this->displayOutput($response);
        }
        if (empty($ap['last_name'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Last Name field is required';
          //$response['response']   = $this->obj;
          $this->displayOutput($response);
        }       
        if (empty($ap['email'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Email field is required';
          //$response['response']   = $this->obj;
          
          $this->displayOutput($response);
        }
        if (empty($ap['phone_no'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Phone no. field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }
        if (empty($ap['no_of_guests'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'No. of guests field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }
        
        if (empty($ap['reservation_date'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Reservation date field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }
        if (empty($ap['reservation_time'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Reservation time field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }       
        if (empty($ap['zone_id'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Zone field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }
        $member_id            = $ap['member_id'];
        $access_token         = $ap['access_token'];  
        $device_type          = $ap['device_type'];
        $reservation_id       = $ap['reservation_id'];

        $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);

        if (empty($access_token_result)) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Unauthorize Token';        
          $this->displayOutput($response);
        }
        else{
          $zone_data    = $this->mcommon->getRow('master_zone',array("zone_id" => $ap['zone_id']));
          if(!empty($zone_data)){
            // if($zone_data['cover_charges'] !='0'){
              //   $zone_minimum_price = $zone_data['cover_charges'];
              //   $zone_price_type    ='cover';
              // }
              // else{
              //   $zone_minimum_price = $zone_data['advance_charges'];
              //   $zone_price_type ='advance';
              // }

              /** added by Ishani on 23.07.2020  **/
              $zone_minimum_price = $zone_data['cover_charges'];
              $zone_price_type    ='cover';
          }
          else{
            $zone_minimum_price = '';
            $zone_price_type    = '';
          }
         /* $current_dt  	= date('d-m-Y H:i:s');       
		  $rev_time 	= date('H:i:s',strtotime($ap["reservation_time"]));
		  $rev_dt 		= str_replace('/','-',$ap["reservation_date"]).$rev_time;
          if(strtotime($current_dt.'+24 hours') >= strtotime($rev_dt)){
            $response['status']['error_code'] = 1;
            $response['status']['message']    = "Can not reserve, Reservation date should be 24 hours before.";
          }*/
      	$current_dt  	= date('d-m-Y');
		$rev_dt 		= str_replace('/','-',$ap["reservation_date"]);
      	if(strtotime($current_dt.'+1 day') >= strtotime($rev_dt)){
            $response['status']['error_code'] = 1;
            $response['status']['message']    = "Sorry can not reserve.Reservation is closed for tomorrow.";
      	}
      	else{
            $selectedTime             = $ap['reservation_time'];
            $start_time_range         = date('H:i:s',strtotime("-90 minutes", strtotime($selectedTime)));
            $end_time_range           = date('H:i:s',strtotime("+90 minutes", strtotime($selectedTime)));
            $reservation_condition    = "reservation_date= '".DATE('Y-m-d',strtotime(str_replace('/','-',$ap["reservation_date"])))."' and zone_id = '".$ap['zone_id']."' and member_id != '".$member_id."' and reservation_time between '".$start_time_range."' and '".$end_time_range."'";
            $reservation_list         = $this->mapi->getRow('reservation',$reservation_condition);

            if($reservation_list){
              
              $response['status']['error_code']           = 1;
              $response['status']['message']              = 'Opp!Sorry the zone is already reserved for the given date & time';
              $this->displayOutput($response);
            }
            else{
              $updatearry   = array('reservation_date'    => DATE('Y-m-d',strtotime(str_replace('/','-',$ap["reservation_date"]))),                                    
                                    'reservation_time'    => DATE('H:i:s',strtotime($ap["reservation_time"])),
                                    'zone_id'             => $ap['zone_id'],
                                    'no_of_guests'        => $ap['no_of_guests'],
                                    'zone_price'          => $zone_minimum_price,
                                    'reservation_for'     => $ap['reservation_for'],
                                    'member_id'           => $member_id,
                                    'first_name'          => $ap['first_name'],
                                    'last_name'           => $ap['last_name'],
                                    'email'               => $ap['email'],
                                    'country_code'        => $ap['country_code'],
                                    'member_mobile'       => $ap['phone_no'],
                                    'add_from'            => 'front',
                                    'message'             => $ap['message'],                                  
                                    'updated_by'          => $member_id,
                                    'updated_on'          => date('Y-m-d')
                                  );
              $resrv_condn    = array('member_id' => $member_id,'reservation_id' =>$reservation_id);
              $resrv_data     = $this->mapi->getRow('reservation',$resrv_condn);
              if(!empty($resrv_data) && $resrv_data['status'] =='2'){
                  $response['status']['error_code']           = 1;
                  $response['status']['message']              = 'Opps!Sorry,data can not be edit as the request of reservation is already reserved.';
              }
              else{
                  $result         = $this->mapi->update('reservation',$resrv_condn,$updatearry);          
                  if($result)
                  { 
                    $condition          = "reservation.reservation_id ='".$reservation_id."'";       
                    $reservation_list   = $this->mapi->getReservationList($condition);
                    if(!empty($reservation_list)){
                      foreach($reservation_list as $key=>$val){
                        $reservation_data[$key]['reservation_id']     =  $val['reservation_id'];
                        $reservation_data[$key]['reservation_date']   =  date('d/m/Y',strtotime($val['reservation_date']));
                        $reservation_data[$key]['reservation_time']   =  DATE('h:i A',strtotime($val["reservation_time"]));
                        $reservation_data[$key]['zone_id']            =  $val['zone_id'];
                        $reservation_data[$key]['zone_name']          =  $val['zone_name'];                      
                        $reservation_data[$key]['zone_price']         =  $val['zone_price'];
                        $reservation_data[$key]['total_amount']       =  $val['payment_amount'];
                        $reservation_data[$key]['no_of_guest']        =  $val['no_of_guests'];
                        $reservation_data[$key]['first_name']         =  $val['first_name'];
                        $reservation_data[$key]['last_name']          =  $val['last_name'];
                        $reservation_data[$key]['country_code']       =  $val['country_code'];
                        $reservation_data[$key]['phone_no']           =  $val['member_mobile'];
                        $reservation_data[$key]['email']              =  $val['email'];
                        $reservation_data[$key]['reservation_for']    =  $val['reservation_for'];
                        $reservation_data[$key]['message']            =  $val['message'];
                        $reservation_data[$key]['reservation_status'] =  $val['status']; //0=>canceled 1=>pending 2=>reserved 3=>rejected
                        $reservation_data[$key]['cancellation_reason']=  $val['cancellation_reason'];

                        $zone_name          = $reservation_data[$key]['zone_name'];
                        $reservation_date   = $reservation_data[$key]['reservation_date'];
                        $reservation_time   = $reservation_data[$key]['reservation_time'];
                        $no_of_guest        = $reservation_data[$key]['no_of_guest'];
                        $reservation_status = 'Waiting for confirmation';
                      } 
                      /****************** Send Reservation details to the member ****************************/
                      //$link                   = base_url('api/member_activation/'.$member_id);
                      $logo                     = base_url('public/images/logo.png');
                      $mail['name']             = $ap['first_name'];
                       //$mail['to']               = $ap['email'];    
                      $mail['to']               = 'sreelabiswas.kundu@met-technologies.com';
                      $mail['zone_name']        = '<span style="color:#f9b92d"><strong>Zone: </strong></span>'.$zone_name.'<br>';
                      $mail['reservation_date'] = $reservation_date;
                      $mail['reservation_time'] = $reservation_time;
                      $mail['no_of_guest']      = $no_of_guest;
                      $mail['reservation_status'] = $reservation_status;
                      $mail['subject']          = 'Club Fenicia - Reservation request received Mail';                             
                      $mail_temp                = file_get_contents('./global/mail/reservation_template.html');
                      $mail_temp                = str_replace("{web_url}", base_url(), $mail_temp);
                      $mail_temp                = str_replace("{logo}", $logo, $mail_temp);
                      $mail_temp                = str_replace("{shop_name}", 'Club Fenicia', $mail_temp);  
                      $mail_temp                = str_replace("{name}", $mail['name'], $mail_temp);
                      $mail_temp                = str_replace("{zone_name}", $mail['zone_name'], $mail_temp);
                      $mail_temp                = str_replace("{reservation_date}", $mail['reservation_date'], $mail_temp);
                      $mail_temp                = str_replace("{reservation_time}", $mail['reservation_time'], $mail_temp);
                      $mail_temp                = str_replace("{no_of_guest}", $mail['no_of_guest'], $mail_temp);
                      $mail_temp                = str_replace("{reservation_status}", $mail['reservation_status'], $mail_temp);        
                      $mail_temp                = str_replace("{current_year}", date('Y'), $mail_temp);           
                      $mail['message']          = $mail_temp;
                      $from_email               = 'clubfenicia@fenicialounge.in';
                      $this->sendMail($mail,'Club Fenicia',$from_email);

                      /********************************** Send reservation details in sms *************************************************/
                      
                      $message  = "Congratulation! Your request for reservation is waiting for Confirmation.\n Your reservation details is - \n ";
                      $message .= "Zone name: ".$zone_name.",\n Reservation date: ".$reservation_date.",\n Reservation time: ".$reservation_time.", \n No of guest: ".$no_of_guest;
                      $message .= "WE WOULD BE HOLDING YOUR RESERVATION FOR 15 MINUTES FROM THE TIME OF RESERVATION AND IT WILL BE RELEASED WITHOUT ANY PRIOR INFORMATION.";
                      $this->smsSend($ap['phone_no'],$message);

                      $response['status']['error_code']           = 0;
                      $response['status']['message']              = 'Request for reservation submitted Successfully';
                      $response['response']['reservation_list']   = $reservation_data;

                    }
                    else{
                      $response['status']['error_code']           = 1;
                      $response['status']['message']              = 'No data available';
                    }
                  }
                  else{
                    $response['status']['error_code']           = 1;
                    $response['status']['message']              = 'No data available';
                  }
              }
            }
          } 
      }
    }
    else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Please fill up all required fields.';
    }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
public function cancelReservation()
{
  
  $result  = array();
  $ap     = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {
    	if (empty($ap['cancellation_reason'])) {
	          $response['status']['error_code'] = 1;
	          $response['status']['message']    = 'cancellation reason is required';
	          $this->displayOutput($response);
	        }
      $member_id            = $ap['member_id'];
      $access_token         = $ap['access_token'];  
      $device_type          = $ap['device_type'];
      $reservation_id       = $ap['reservation_id'];

      $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($member_details);
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
      }
      else{	      	
          $condition          = "reservation.reservation_id ='".$reservation_id."'";       
          $reservation_list   = $this->mapi->getReservationList($condition);
          if(!empty($reservation_list)){
              $reservation_date   = $reservation_list[0]['reservation_date'];              
              $current_dt  = date('Y-m-d');
              if(strtotime($current_dt) >= strtotime( $reservation_date . ' -1 day' )){
                $response['status']['error_code'] = 1;
                $response['status']['message']    = "Cannot cancel the reservation. Reservation should be cancel before 24 hours";
              }
              else{
                  $updatearry   = array(
                                  'status'              => '0',
                                  'cancellation_reason' => $ap['cancellation_reason'],
                                  'updated_by'          => $member_id,
                                  'updated_on'          => date('Y-m-d')
                                );
                  $result       = $this->mapi->update('reservation',$condition,$updatearry);          
                  if($result)
                  { 
                    $condition              = "reservation.reservation_id ='".$reservation_id."'";       
                    $all_reservation_list   = $this->mapi->getReservationList($condition);
                    if(!empty($all_reservation_list)){
                      foreach($all_reservation_list as $key=>$val){
                        $reservation_data[$key]['reservation_id']     =  $val['reservation_id'];
                        $reservation_data[$key]['reservation_date']   =  date('d/m/Y',strtotime($val['reservation_date']));
                        $reservation_data[$key]['reservation_time']   =  DATE('h:i A',strtotime($val["reservation_time"]));
                        $reservation_data[$key]['zone_id']            =  $val['zone_id'];
                        $reservation_data[$key]['zone_name']          =  $val['zone_name'];                      
                        $reservation_data[$key]['zone_price']         =  $val['zone_price'];
                        $reservation_data[$key]['total_amount']       =  $val['payment_amount'];
                        $reservation_data[$key]['no_of_guest']        =  $val['no_of_guests'];
                        $reservation_data[$key]['first_name']         =  $val['first_name'];
                        $reservation_data[$key]['last_name']          =  $val['last_name'];
                        $reservation_data[$key]['country_code']       =  $val['country_code'];
                        $reservation_data[$key]['phone_no']           =  $val['member_mobile'];
                        $reservation_data[$key]['email']              =  $val['email'];
                        $reservation_data[$key]['reservation_for']    =  $val['reservation_for'];
                        $reservation_data[$key]['message']            =  $val['message'];
                        $reservation_data[$key]['reservation_status'] =  $val['status']; //0=>canceled 1=>pending 2=>reserved 3=>rejected
                        $reservation_data[$key]['cancellation_reason']=  $val['cancellation_reason'];

                        $zone_name          = $reservation_data[$key]['zone_name'];
                        $reservation_id     = $reservation_data[$key]['reservation_id'];
                        $reservation_date   = $reservation_data[$key]['reservation_date'];
                        $reservation_time   = $reservation_data[$key]['reservation_time'];
                        $no_of_guests       = $reservation_data[$key]['no_of_guest'];
                        $user_name          = $reservation_data[$key]['first_name'];
                        $user_email         = $reservation_data[$key]['email'];
                        $user_ph            = $reservation_data[$key]['phone_no'];
                      }
                      $response['status']['error_code']           = 0;
                      $response['status']['message']              = 'Reservation Cancelled Successfully. Please contact help desk for further details(Contact No. :7980191955).';
                      $response['response']['reservation_list']   = $reservation_data; 
                      if($reservation_data[$key]['reservation_status'] == 0){
                        $title      = "Reservation Cancelled";
                        $message    = "Your request for reservation is cancelled.";
                      }
                      elseif($reservation_data[$key]['reservation_status'] == 1){
                        $title      ="Reservation Pending";
                        $message    = "Your request for reservation is waiting for Confirmation.";
                      }            
                      elseif($reservation_data[$key]['reservation_status'] == 2){
                        $title     = "Reservation Confirmed";
                        $message   = "Your request for reservation is Confirmed.";
                      }            
                      else{
                        $title     ="Reservation Rejected";
                        $message   = "Your request for reservation is rejected by fenicia.";
                      }
                      $message_data = array('title' => $title,'message' => $message);
                      $user_fcm_token_data  = $this->mcommon->getRow('device_token',array('member_id' => $member_id));
                      if(!empty($user_fcm_token_data)){
                        $member_datas   = $this->mcommon->getRow('master_member',array('member_id' => $member_id));
                        
                        if($member_datas['notification_allow_type'] == '1'){
                          if($ap['device_type'] == 1){
                            $this->pushnotification->send_ios_notification($user_fcm_token_data['fcm_token'], $message_data);
                          }
                          else{
                           $this->pushnotification->send_android_notification($user_fcm_token_data['fcm_token'], $message_data);
                          }
                        }                        
                        $notification_arr = array('member_id'                 => $member_id,
                                                  'notification_title'        => $title,
                                                  'notification_description'  => $message,
                                                  'status'                    => '1',
                                                  'created_on'                => date('Y-m-d H:i:s')
                                                  );
                        $insert_data        = $this->mcommon->insert('notification', $notification_arr);
                      }

                      $mail_message         = "Your request for reservation is cancelled.";
                      
                      $logo                       = base_url('public/images/logo.png');
                      $params['name']             = $user_name;
                      $params['to']               = $user_email;
                      $mail['zone_name']          = $zone_name;
                      $mail['reservation_date']   = $reservation_date;
                      $mail['reservation_time']   = $reservation_time;
                      $mail['no_of_guests']       = $no_of_guests;
                      $mail['status']             = 'Cancelled';
                            
                      $params['subject']          =   'Club Fenicia - Reservation cancellation mail';                              
                      $mail_temp                  =   file_get_contents('./global/mail/reservation_status_template.html');
                      $mail_temp                  =   str_replace("{web_url}", base_url(), $mail_temp);
                      $mail_temp                  =   str_replace("{logo}", $logo, $mail_temp);
                      $mail_temp                  =   str_replace("{shop_name}", 'Club Fenicia', $mail_temp);  
                      $mail_temp                  =   str_replace("{name}", $params['name'], $mail_temp);
                      $mail_temp                  =   str_replace("{zone_name}", $mail['zone_name'], $mail_temp);
                      $mail_temp                  =   str_replace("{body_msg}", $mail_message, $mail_temp);
                      $mail_temp                  =   str_replace("{reservation_date}", $mail['reservation_date'], $mail_temp);
                      $mail_temp                  =   str_replace("{reservation_time}", $mail['reservation_time'], $mail_temp);
                      $mail_temp                  =   str_replace("{no_of_guest}", $mail['no_of_guests'], $mail_temp);
                      $mail_temp                  =   str_replace("{status}", $mail['status'], $mail_temp);
                      $mail_temp                  =   str_replace("{current_year}", date('Y'), $mail_temp);           
                      $params['message']          =   $mail_temp;
                      $msg                        =   registration_mail($params);


                      $message  = $mail_message." Details - \n";
                      $message .=  "Zone: ".$zone_name."\n Date: ".$reservation_date."\n Time: ".$reservation_time."\n No of guest: ".$no_of_guests."\n Status: Cancelled";
                      $this->smsSend($user_ph,$message);
        
                    } 
                    else{
                      $response['status']['error_code']           = 1;
                      $response['status']['message']              = 'No data available';
                    }
                  }
                  else{
                    $response['status']['error_code']           = 1;
                    $response['status']['message']              = 'No data available';
                  }  
              }
          }
          else{
            $response['status']['error_code']           = 1;
            $response['status']['message']              = 'No data available';
          }       
      } 
    }
    else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
      }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
public function filterReservations()
{  
  $result  = array();
  $ap     = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {
      $member_id            = $ap['member_id'];
      $access_token         = $ap['access_token'];  
      $device_type          = $ap['device_type'];
      $filter_type          = $ap['filter_type'];
      $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($member_details);
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
      }
      else{
          $condition                = "reservation.status ='".$filter_type."' and reservation.member_id = '".$member_id."'";       
          $all_reservation_list     = $this->mapi->getReservationList($condition);
          if(!empty($all_reservation_list)){
              foreach($all_reservation_list as $key=>$val){
                $reservation_data[$key]['reservation_id']     =  $val['reservation_id'];
                $reservation_data[$key]['reservation_date']   =  date('d/m/Y',strtotime($val['reservation_date']));
                $reservation_data[$key]['reservation_time']   =  DATE('h:i A',strtotime($val["reservation_time"]));
                $reservation_data[$key]['zone_id']            =  $val['zone_id'];
                $reservation_data[$key]['zone_name']          =  $val['zone_name'];                      
                $reservation_data[$key]['zone_price']         =  $val['zone_price'];
                $reservation_data[$key]['total_amount']       =  $val['payment_amount'];
                $reservation_data[$key]['no_of_guest']        =  $val['no_of_guests'];
                $reservation_data[$key]['first_name']         =  $val['first_name'];
                $reservation_data[$key]['last_name']          =  $val['last_name'];
                $reservation_data[$key]['country_code']       =  $val['country_code'];
                $reservation_data[$key]['phone_no']           =  $val['member_mobile'];
                $reservation_data[$key]['email']              =  $val['email'];
                $reservation_data[$key]['reservation_for']    =  $val['reservation_for'];
                $reservation_data[$key]['message']            =  $val['message'];
                $reservation_data[$key]['reservation_status'] =  $val['status']; //0=>canceled 1=>pending 2=>reserved 3=>rejected
                $reservation_data[$key]['cancellation_reason']=  $val['cancellation_reason'];
              }
              $response['status']['error_code']           = 0;
              $response['status']['message']              = 'Reservation filter list.';
              $response['response']['reservation_list']   = $reservation_data; 
          }
          else{
            $response['status']['error_code']           = 1;
            $response['status']['message']              = 'No data available';
          }       
      } 
    }
    else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
      }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
public function eventList()
{
  	$result  = array();
  	$ap     = json_decode(file_get_contents('php://input'), true);
  	if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
      		$member_id            = $ap['member_id'];
      		$access_token         = $ap['access_token'];  
      		$device_type          = $ap['device_type'];
      		$access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
      		//pr($member_details);
      		if (empty($access_token_result)) {
      			$response['status']['error_code'] = 1;
      			$response['status']['message']    = 'Unauthorize Token';        
      			$this->displayOutput($response);
      		}
      		else{  
            $current_dt  = date('Y-m-d');
            $response['status']['error_code']           = 0;
            $response['status']['message']              = 'Event List.';     			        
      			$event_trending_list        = $this->mapi->getEventList();
      			if(!empty($event_trending_list)){
      				foreach($event_trending_list as $key2 => $evnt2){
                if(strtotime($current_dt) <= strtotime($evnt2['event_start_date'])){
                  $event_type_flag_trending  = 'new';
                }
                else{
                  $event_type_flag_trending  = 'old';
                }
                $event_trending_data[$key2]['event_type_flag']    =  $event_type_flag_trending;
      				  $event_trending_data[$key2]['event_id']           =  $evnt2['event_id'];
      				  $event_trending_data[$key2]['event_name']         =  $evnt2['event_name'];
      				  $event_trending_data[$key2]['event_desc']         =  $evnt2['event_description'];
      				  $event_trending_data[$key2]['event_date']         =  date('d/m/Y',strtotime($evnt2['event_start_date']));
                $event_trending_data[$key2]['event_end_date']     =  date('d/m/Y',strtotime($evnt2['event_end_date']));
                $event_trending_data[$key2]['event_stTime']       =  date('h:i A',strtotime($evnt2["event_start_time"]));
                $event_trending_data[$key2]['event_etTime']       =  date('h:i A',strtotime($evnt2["event_end_time"]));
      				  $event_trending_data[$key2]['event_location']     =  $evnt2['event_location'];
      				  $event_trending_data[$key2]['caledar_event_id']   =  $evnt2['caledar_event_id'];
      				 
      				 /*** added for ios calender ****/ 
      				  if($device_type==1) //for IOS
                        {
                          $event_trending_data[$key2]['caledar_event_id']   =  $evnt2['caledar_event_id_ios'];
                        }

      				  $event_trending_image                             =  $this->mapi->getEventImgList($evnt2['event_id']);             
      				  if(!empty($event_trending_image)){
      				      foreach($event_trending_image as $trd_image){

      				        $event_trending_data[$key2]['event_image']    = $trd_image['event_img'];
      				      }                 
      				  }
      				  else{
      				      $event_trending_data[$key2]['event_image']    = "";
      				  }
      				}
      				$response['response']['event']['trending_events'] = $event_trending_data;              
              $event_popular_list  = $this->mapi->getEventList('popular');
              
              if(!empty($event_popular_list)){
                      foreach($event_popular_list as $key => $evnt){
                        if(strtotime($current_dt) <= strtotime($evnt['event_start_date'])){
                          $event_type_flag_popular  = 'new';
                        }
                        else{
                          $event_type_flag_popular  = 'old';
                        }
                        $event_popular_data[$key]['event_type_flag']    =  $event_type_flag_popular;
                        $event_popular_data[$key]['event_id']           =  $evnt['event_id'];
                        $event_popular_data[$key]['event_name']         =  $evnt['event_name'];
                        $event_popular_data[$key]['event_desc']         =  $evnt['event_description'];
                        $event_popular_data[$key]['event_date']         =  date('d/m/Y',strtotime($evnt['event_start_date']));
                        $event_popular_data[$key]['event_end_date']     =  date('d/m/Y',strtotime($evnt['event_end_date']));
                        $event_popular_data[$key]['event_stTime']       =  date('h:i A',strtotime($evnt['event_start_time']));
                        $event_popular_data[$key]['event_etTime']       =  date('h:i A',strtotime($evnt['event_end_time']));            
                        $event_popular_data[$key]['event_location']     =  $evnt['event_location'];
                        $event_popular_data[$key]['caledar_event_id']   =  $evnt['caledar_event_id'];
                        $event_popular_image                            =  $this->mapi->getEventImgList($evnt['event_id']);
                        
                        /*** added for ios calrnder ***/
                        if($device_type==1) //for IOS
                        {
                          $event_popular_data[$key]['caledar_event_id']   =  $evnt['caledar_event_id_ios'];
                        }
                        
                        if(!empty($event_popular_image)){
                          foreach($event_popular_image as $popular_image){
                            $event_popular_data[$key]['event_image']    = $popular_image['event_img'];
                          }
                        }
                        else{
                            $event_popular_data[$key]['event_image']    = "";
                        }
                      }
                      $response['response']['event']['popular_events']   = $event_popular_data;
              }              
              else{
                 $response['response']['event']['popular_events']   = [];//(CHANGE DONE ON 02/03/20)
              }
      			}
      			else{
      				$response['status']['error_code'] = 1;
              $response['status']['message']    = 'No event found.';
      			}       
          }
      }
      else {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Please fill up all required fields.';
      }
    }
    else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;      
    }
    $this->displayOutput($response);
}
public function eventMonthList()
{
  $result  = array();
  $ap     = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {
      $member_id            = $ap['member_id'];
      $access_token         = $ap['access_token'];  
      $device_type          = $ap['device_type'];
      $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($member_details);
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
      }
      else{
          $current_dt  = date('Y-m-d');
          $response['status']['error_code']           = 0;
          $response['status']['message']              = 'Event Month List.';                   
          $event_list        = $this->mapi->getEventListByMonth();
          //pr($event_list);
          if(!empty($event_list)){
            foreach($event_list as $value){
              foreach($value as $keyyr => $year){
                 $event_data = array();
                foreach($year as $keymnth => $month){   
                    $tmp_event_data[] =   $year[$keymnth];                                      
                    foreach($tmp_event_data as $key => $evnt_list){
                        foreach($evnt_list as $index =>$list){
                          $event_data[$key]['month_name']        = date('F Y',strtotime($list['event_start_date']));
                          if(strtotime($current_dt) <= strtotime($list['event_start_date'])){
                            $event_type_flag  = 'new';
                          }
                          else{
                            $event_type_flag  = 'old';
                          }
                          $event_data[$key]['event_details'][$index]['event_type_flag'] =  $event_type_flag;
                          $event_data[$key]['event_details'][$index]['event_id']        =  $list['event_id'];
                          $event_data[$key]['event_details'][$index]['event_name']      =  $list['event_name'];
                          $event_data[$key]['event_details'][$index]['event_desc']      =  $list['event_description'];
                          $event_data[$key]['event_details'][$index]['event_date']      =  date('d/m/Y',strtotime($list['event_start_date']));
                          $event_data[$key]['event_details'][$index]['event_end_date']  =  date('d/m/Y',strtotime($list['event_end_date']));
                          $event_data[$key]['event_details'][$index]['event_stTime']    =  date('h:i A',strtotime($list['event_start_time']));
                          $event_data[$key]['event_details'][$index]['event_etTime']    =  date('h:i A',strtotime($list['event_end_time']));
                          $event_data[$key]['event_details'][$index]['event_location']  =  $list['event_location'];
                          $event_data[$key]['event_details'][$index]['caledar_event_id']=  $list['caledar_event_id'];
                          
                          //** added for ios calender ***/
                          if($device_type==1) //for IOS
                          {
                            $event_data[$key]['event_details'][$index]['caledar_event_id']   =  $list['caledar_event_id_ios'];
                          }
                          
                          $event_data_img                                               =  $this->mapi->getEventImgList($list['event_id']);
                          if(!empty($event_data_img)){
                            foreach($event_data_img as $data_img){
                              $event_data[$key]['event_details'][$index]['event_image'] = $data_img['event_img'];
                            }  
                            
                          }
                          else{
                              $event_data[$key]['event_details'][$index]['event_image']  = '';
                          }
                        }
                    }
                }               
              }
            }
            $response['response']['events'] = $event_data;
          }
          else{
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'No event found.';
          }         
      }
    }
    else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
      }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
public function eventSearch()
{
	$result  = array();
  $ap     = json_decode(file_get_contents('php://input'), true);
  	if ($this->checkHttpMethods($this->http_methods[0])) {
    	if (sizeof($ap)) {
			$member_id            = $ap['member_id'];
			$access_token         = $ap['access_token'];  
			$device_type          = $ap['device_type'];
			$search_keyword       = trim($ap['search_keyword']);
			$access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
	        //pr($member_details);
      //$access_token_result  = 1;
			if (empty($access_token_result)) {
				$response['status']['error_code'] = 1;
				$response['status']['message']    = 'Unauthorize Token';        
				$this->displayOutput($response);
			}
			else{
        $current_dt  = date('Y-m-d');
				$response['status']['error_code']           = 0;
				$response['status']['message']              = 'Event Search List.'; 
				$event_condition 		= "event_name LIKE '%".$search_keyword."%'";                
				$event_list        	= $this->mapi->getEventList('',$event_condition); 
	      if(!empty($event_list)){
            foreach($event_list as $key => $evnt){
              if(strtotime($current_dt) <= strtotime($evnt['event_start_date'])){
                $event_type_flag  = 'new';
              }
              else{
                $event_type_flag  = 'old';
              }
              $event_data[$key]['event_type_flag']   =  $event_type_flag;
              $event_data[$key]['event_id']           =  $evnt['event_id'];
              $event_data[$key]['event_name']         =  $evnt['event_name'];
              $event_data[$key]['event_desc']         =  $evnt['event_description'];
              $event_data[$key]['event_date']         =  date('d/m/Y',strtotime($evnt['event_start_date']));            
              $event_data[$key]['event_location']     =  $evnt['event_location'];

              //added params//////
              $event_data[$key]['event_end_date']     =  date('d/m/Y',strtotime($evnt['event_end_date']));
              $event_data[$key]['event_stTime']       =  date('h:i A',strtotime($evnt["event_start_time"]));
              $event_data[$key]['event_etTime']       =  date('h:i A',strtotime($evnt["event_end_time"]));
              $event_data[$key]['caledar_event_id']   =  $evnt['caledar_event_id'];
              
              /** added for ios calender **/
              if($device_type==1) //for IOS
              {
                $event_data[$key]['caledar_event_id']   =  $evnt['caledar_event_id_ios'];
              }

              $event_image                            =  $this->mapi->getEventImgList($evnt['event_id']);
              if(!empty($event_image)){
                foreach($event_image as $evnt_image){
                  $event_data[$key]['event_image']    = $evnt_image['event_img'];
                }
              }
              else{
                  $event_data[$key]['event_image']    = '';
              }
            }
            $response['response']['events_list']   = $event_data;
				}
				else{
					$response['status']['error_code'] = 1;
	        		$response['status']['message']    = 'No data found';
				}
			}
	    }
	    else {
	        $response['status']['error_code'] = 1;
	        $response['status']['message']    = 'Please fill up all required fields.';
	  	}
	} else {
	  $response['status']['error_code'] = 1;
	  $response['status']['message']    = 'Wrong http method type.';
	  //$response['response']   = $this->obj;      
	}
  	$this->displayOutput($response);
}
public function latestUpdatesImage()
{
  $result = array();  
  $ap     = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {
      $member_id            = $ap['member_id'];
      $access_token         = $ap['access_token'];  
      $device_type          = $ap['device_type'];
      $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($member_details);
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
      }
      else{           
        $gallery_list    = $this->mapi->getGalleryDetails();
        //pr($gallery_list);          
        if(!empty($gallery_list)){
          foreach($gallery_list as $key => $val){
              $gallery_img  = array();
              $gallery_data[$key]['lounge_id']        = $val['gallery_id'];
              $gallery_data[$key]['lounge_name']      = $val['gallery_name'];
              $gallery_data[$key]['lounge_subtitle']  = $val['gallery_sub_title'];
              $lounge_image                           = $this->mapi->getAllGalleryImages($val['gallery_id'],'latest');
              //pr($lounge_image);
              if(!empty($lounge_image)){
                foreach($lounge_image as $img){
                  $gallery_img[]  = $img['gallery_img'];
                } 
              }
             
            $gallery_data[$key]['lounge_image']  = $gallery_img;              
          }
          $response['status']['error_code']       = 0;
          $response['status']['message']          = 'Latest updates List.'; 
          $response['response']['latest_updates'] = $gallery_data;
        }
        else {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'No data found.';
        }
      }
    }
    else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
    }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
public function allGalleryList()
{
  $result     = array();  
  $ap         = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {
      $member_id            = $ap['member_id'];
      $access_token         = $ap['access_token'];  
      $device_type          = $ap['device_type'];
      $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($ap);
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
      }
      else{           
        $gallery_list    = $this->mapi->getGalleryDetails();       
       //pr($gallery_list);
        if(!empty($gallery_list)){
          foreach($gallery_list as $key => $val){
              $gallery_all_images           = array();
              $gallery_data[$key]['lounge_id']        = $val['gallery_id'];
              $gallery_data[$key]['lounge_name']      = $val['gallery_name'];
              $gallery_data[$key]['lounge_subtitle']  = $val['gallery_sub_title'];
             /* $gallery_latest_image         =  $this->mapi->getAllGalleryImages($val['gallery_id'],'latest');
              //pr($gallery_latest_image);
              if(!empty($gallery_latest_image)){
                foreach($gallery_latest_image as $img){
                  $gallery_latest_img[]  = $img['gallery_img'];
                } 
              }*/
              $gallery_all_img                    =  $this->mapi->getAllGalleryImages($val['gallery_id']);
              if(!empty($gallery_all_img)){
                foreach($gallery_all_img as $all_img){
                  $gallery_all_images[]  = $all_img['gallery_img'];
                } 
              }
              //$gallery_data['gallery_latest_img']   =  $gallery_latest_img; 
              $gallery_data[$key]['gallery_all_img']  =  $gallery_all_images;            
          }
          $response['status']['error_code']       = 0;
          $response['status']['message']          = 'gallery List.'; 
          $response['response']['gallery_data'] = $gallery_data;
        }
        else {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'No data found.';
       }
      }
    }
    else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
    }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
public function galleryList()
{
  $result     = array();  
  $ap         = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {
      $member_id            = $ap['member_id'];
      $access_token         = $ap['access_token'];  
      $device_type          = $ap['device_type'];
      $gallery_id           = $ap['gallery_id'];
      $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($ap);
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
      }
      else{           
        $gallery_list    = $this->mapi->getGalleryDetails($gallery_id);       
        //pr($gallery_list);
        if(!empty($gallery_list)){
          foreach($gallery_list as $key => $val){
              $gallery_latest_img  = array();
              $gallery_all_images  = array();
              $gallery_data['lounge_id']        = $val['gallery_id'];
              $gallery_data['lounge_name']      = $val['gallery_name'];
              $gallery_data['lounge_subtitle']  = $val['gallery_sub_title'];
              $gallery_latest_image             = $this->mapi->getAllGalleryImages($gallery_id,'latest');
              //pr($gallery_latest_image);
              if(!empty($gallery_latest_image)){
                foreach($gallery_latest_image as $img){
                  $gallery_latest_img[]  = $img['gallery_img'];
                } 
              }
              $gallery_all_img                    =  $this->mapi->getAllGalleryImages($val['gallery_id']);
              if(!empty($gallery_all_img)){
                foreach($gallery_all_img as $all_img){
                  $gallery_all_images[]  = $all_img['gallery_img'];
                } 
              }
              $gallery_data['gallery_latest_img']   =  $gallery_latest_img; 
              $gallery_data['gallery_all_img']      =  $gallery_all_images;            
          }
          $response['status']['error_code']       = 0;
          $response['status']['message']          = 'gallery List.'; 
          $response['response']['gallery_data']     = $gallery_data;
        }
        else {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'No data found.';
       }
      }
    }
    else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
    }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
/*************** NO NEED TIMESOLT FEATURE (CHANGED FROM 17/1/20)*****************/
public function timeSlot(){
	$result = array();
	$ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      	if (sizeof($ap)) {	
      		$member_id     			= $ap['member_id'];
            $access_token   		= $ap['access_token'];  
            $device_type   			= $ap['device_type'];
            $date   				= $ap['date'];
            if (empty($date)) {
	          $response['status']['error_code'] = 1;
	          $response['status']['message']    = 'date is required';
	          //$response['response']   = $this->obj;
	          $this->displayOutput($response);
	        }
	        else{
	        	$day 	= date('l',strtotime(str_replace('/','-',$date)));
	        }
	        
          $access_token_result 	= $this->check_access_token($access_token, $device_type,$member_id);
          //pr($member_details);
          if (empty($access_token_result)) {
        	  $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Unauthorize Token';				
			      $this->displayOutput($response);
          }
	        else{	        		
    				$time_slot_list		= $this->mapi->getTimeDetails(array('day_name'=>$day,'status' =>'1'));	
    				//pr($zone_list);
    				if(!empty($time_slot_list)){
    					
    					$response['status']['error_code'] 		= 0;
    			    $response['status']['message']    		= 'TIME Slot List';
    			    $response['response']['zone_list']  	= $time_slot_list;
    				}
    				else{
    					$response['status']['error_code'] = 1;
    			    	$response['status']['message']    = 'Member does not exist';
    				}
			   }
		}
		else {
			$response['status']['error_code'] = 1;
			$response['status']['message']    = 'Please fill up all required fields.';
      	}
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;      
    }
	$this->displayOutput($response);
}
/*************** END *****************/
public function requestForPhotograph()
{

  
  $result  = array();
  $ap     = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {
      if (empty($ap['name'])) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Name field is required';
        $this->displayOutput($response);
      }
      if (empty($ap['ph_countrycode'])) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Countrycode for phone number is required';
        $this->displayOutput($response);
      }
      if (empty($ap['phone_number'])) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Phone number field is required';
        $this->displayOutput($response);
      }
      if (empty($ap['whats_countrycode'])) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Countrycode for whatsapp no number is required';
        $this->displayOutput($response);
      }
      if (empty($ap['whatsappnumber'])) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'whatsapp number field is required';
        $this->displayOutput($response);
      }
      if (empty($ap['date_of_visit'])) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Date of visit field is required';
        $this->displayOutput($response);
      }
      if (empty($ap['message'])) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Message field is required';
        $this->displayOutput($response);
      }
      $member_id                 = $ap['member_id'];
      $access_token              = $ap['access_token'];  
      $device_type               = $ap['device_type'];
      $name                      = $ap['name'];
      $country_code              = $ap['ph_countrycode'];
      $phone_no                  = $ap['phone_number'];
      $country_code_whatsappno   = $ap['whats_countrycode'];
      $whatsappno                = $ap['whatsappnumber'];
      $date_of_visit             = DATE('Y-m-d',strtotime(str_replace('/','-',$ap['date_of_visit'])));
      $message                   = $ap['message'];

      $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($member_details);
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
      }
      else{
          $insrtdata  = array('member_id'               => $member_id,
                              'name'                    => $name,
                              'country_code'            => $country_code,
                              'phoneno'                 => $phone_no,
                              'country_code_whatsappno' => $country_code_whatsappno,
                              'whatsappno'              => $whatsappno,
                              'date_of_visit'           => $date_of_visit,
                              'message'                 => $message,
                              'created_on'              => date('Y-m-d')
                        );
          $request_for_photograph_id     = $this->mapi->insert('request_for_photograph',$insrtdata);
          if($request_for_photograph_id)
          {
            $condition                     = "request_for_photograph_id ='".$request_for_photograph_id."'";            
            $request_for_photograph_list   = $this->mapi->getDetails('request_for_photograph',$condition);
            //pr($request_for_photograph_list);
            if(!empty($request_for_photograph_list)){
              foreach($request_for_photograph_list as $key=>$val){
                $request_for_photograph_data[$key]['request_for_photograph_id'] =  $val['request_for_photograph_id'];
                $request_for_photograph_data[$key]['name']                      =  $val['name'];
                $request_for_photograph_data[$key]['country_code']              =  $val['country_code'];
                $request_for_photograph_data[$key]['phoneno']                   =  $val['phoneno'];
                $request_for_photograph_data[$key]['country_code_whatsappno']   =  $val['country_code_whatsappno'];
                $request_for_photograph_data[$key]['whatsappno']                =  $val['whatsappno'];                      
                $request_for_photograph_data[$key]['date_of_visit']             =  date('d/m/Y',strtotime($val['date_of_visit']));
                $request_for_photograph_data[$key]['message']                   =  $val['message'];
                
              }
              $response['status']['error_code']                       = 0;
              $response['status']['message']                          = 'Request for photograph submitted Successfully';
              $response['response']['request_for_photograph_lists']   = $request_for_photograph_data;
            }
            else{
              $response['status']['error_code']           = 1;
              $response['status']['message']              = 'No data available';
            } 
          }
          else{
              $response['status']['error_code']           = 1;
              $response['status']['message']              = 'No data available';
          } 
      } 
    }
    else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
      }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
public function requestForPhotographList()
{
    $result     = array();  
    $ap         = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        $member_id            = $ap['member_id'];
        $access_token         = $ap['access_token'];  
        $device_type          = $ap['device_type'];
        $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
          //pr($ap);
        if (empty($access_token_result)) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Unauthorize Token';        
          $this->displayOutput($response);
        }
        else{           
          $request_for_photograph_list    = $this->mapi->getRequestForPhotographList($member_id);       
          //pr($request_for_photograph_list);
          if(!empty($request_for_photograph_list)){
            foreach($request_for_photograph_list as $key => $val){
                $request_list[$key]['id']                       = $val['request_for_photograph_id'];
                $request_list[$key]['name']                     = $val['name'];
                $request_list[$key]['country_code']             = $val['country_code'];
                $request_list[$key]['phone_number']             = $val['phoneno'];
                $request_list[$key]['country_code_whatsappno']  = $val['country_code_whatsappno'];
                $request_list[$key]['whatsapp_number']          = $val['whatsappno'];
                $request_list[$key]['date_of_visit']            = date('d/m/Y',strtotime($val['date_of_visit']));
                $request_list[$key]['message']                  = $val['message'];
                $request_list[$key]['status']                   = $val['status'];
                $request_list[$key]['profile_image']            = $val['profile_image'];
                $request_list[$key]['date_of_submit']           = date('d/m/Y',strtotime($val['created_on']));
            }
            $response['status']['error_code']     = 0;
            $response['status']['message']        = 'Request for photograph list';
            $response['response']['request_list'] = $request_list;
          }
          else{
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'No data found';
          }
        }
      }
      else {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Please fill up all required fields.';
      }
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;      
    }
    $this->displayOutput($response);
}
public function pastEventGalleryList()
{
  $result     = array();
  $img_list   = array();  
  $ap         = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {
    if (sizeof($ap)) {
      $member_id            = $ap['member_id'];
      $access_token         = $ap['access_token'];  
      $device_type          = $ap['device_type'];
      $event_id             = $ap['event_id'];
      $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($ap);
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
      }
      else{           
        $images_list    = $this->mapi->getPastEventImgList($event_id);       
        //pr($images_list);
        if(!empty($images_list)){
          foreach($images_list as $key => $val){
             $img_list[$key]['past_event_images']   =  $val['images'];         
          }
          $response['status']['error_code']       = 0;
          $response['status']['message']          = 'past event image List.'; 
          $response['response']['image_data']       = $img_list;
        }
        else {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'No data found.';
       }
      }
    }
    else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
    }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
public function doInquiry(){

  $result  = array();
  $ap      = json_decode(file_get_contents('php://input'), true);
  if ($this->checkHttpMethods($this->http_methods[0])) {    
    if (sizeof($ap)) {  
      $member_id          = $ap['member_id'];
      $access_token       = $ap['access_token'];  
      $device_type        = $ap['device_type'];
      $event_id           = $ap['event_id'];
      $name               = $ap['name'];
      $email              = $ap['email'];
      $country_code       = $ap['country_code'];
      $phone_no           = $ap['phone_no'];
      $subject            = $ap['subject'];
      $message            = $ap['message'];
      $access_token_result    = $this->check_access_token($access_token, $device_type,$member_id);
        //pr($member_details);
      if (empty($access_token_result)) {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Unauthorize Token';        
        $this->displayOutput($response);
      }
      else{
                       
        $inquiry_data   = array('event_id'      => $event_id,
                                'name'          => $name,
                                'email'         => $email,
                                'country_code'  => $country_code,
                                'phone_no'      => $phone_no,
                                'subject'       => $subject,
                                'message'       => $message,
                                'created_on'    => date('Y-m-d')
                        );
        $inquiry_id  = $this->mcommon->insert('inquiry',$inquiry_data);
        
        if(!empty($inquiry_id)){
        /****************** Send inquiry to the admin ****************************/
                      //$link                   = base_url('api/member_activation/'.$member_id);
          $admin_cond               = array('role_id' => '1','status' =>'1','is_delete' =>'1');
          $admin_data               = $this->mcommon->getRow('user',$admin_cond);
          if(!empty($admin_data)){
            $admin_email            = $admin_data['email'];
            $admin_name             = $admin_data['first_name'];
          }
          else{
            $admin_email            = 'support@fenicia.in';
            $admin_name             = 'admin';
          }     
          $logo                     = base_url('public/images/logo.png');
          $mail['name']             = $admin_name;
          $mail['to']               = $admin_email;    
          //$mail['to']               = 'sreelabiswas.kundu@met-technologies.com';
          $mail['message']          = ucfirst($message);
          $mail['subject']          = ucfirst($ap['subject']);                             
          $mail_temp                = file_get_contents('./global/mail/inquiry_template.html');
          $mail_temp                = str_replace("{web_url}", base_url(), $mail_temp);
          $mail_temp                = str_replace("{logo}", $logo, $mail_temp);
          $mail_temp                = str_replace("{shop_name}", 'Club Fenicia', $mail_temp);  
          $mail_temp                = str_replace("{name}", $mail['name'], $mail_temp);
          $mail_temp                = str_replace("{inquiry_person_name}", $name, $mail_temp);
          $mail_temp                = str_replace("{message}", $mail['message'], $mail_temp);    
          $mail_temp                = str_replace("{current_year}", date('Y'), $mail_temp);           
          $mail['message']          = $mail_temp;
          $this->sendMail($mail,$name,$email);
          $response['status']['error_code']         = 0;
          $response['status']['message']            = 'Your inquiry successfully submitted.';
        }
        else{
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Sorry! inquiry is not submitted successfully.';
        }
      }
    }
    else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Please fill up all required fields.';
    }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);
}
public function testmail(){
        $logo					  = base_url('public/images/logo.png');
		$params['name']			  =	'Test';
		$params['to']			  =	'sreelabiswas.kundu@met-technologies.com';
		$mail['zone_name']        = 'AAA';
      	$mail['reservation_date'] = '2020-10-20';
      	$mail['reservation_time'] = '5:30';
      	$mail['no_of_guests']     = 1;
      	$mail['status'] 		  = 2;
      	$mail_message              ="FHDSFJ";
	    $params['subject']		=   'Test';															
		$mail_temp 				= 	file_get_contents('./global/mail/reservation_status_template.html');
		$mail_temp				=	str_replace("{web_url}", base_url(), $mail_temp);
		$mail_temp				=	str_replace("{logo}", $logo, $mail_temp);
		$mail_temp				=	str_replace("{shop_name}", 'Club Fenicia', $mail_temp);	
		$mail_temp				=	str_replace("{name}", $params['name'], $mail_temp);
		$mail_temp              = 	str_replace("{zone_name}", $mail['zone_name'], $mail_temp);
		$mail_temp              = 	str_replace("{body_msg}", $mail_message, $mail_temp);
      	$mail_temp              = 	str_replace("{reservation_date}", $mail['reservation_date'], $mail_temp);
      	$mail_temp              =   str_replace("{reservation_time}", $mail['reservation_time'], $mail_temp);
      	$mail_temp              =   str_replace("{no_of_guest}", $mail['no_of_guests'], $mail_temp);
      	$mail_temp              =   str_replace("{status}", $mail['status'], $mail_temp);
		$mail_temp				=	str_replace("{current_year}", date('Y'), $mail_temp);						
		$params['message']		=	$mail_temp;
		$msg 					= 	registration_mail($params);		
}
public function synchronizeEventWithCalendar(){
  	$result  = array();
  	$ap     = json_decode(file_get_contents('php://input'), true);
  	if ($this->checkHttpMethods($this->http_methods[0])) {
	    if (sizeof($ap)) {
		    $member_id            = $ap['member_id'];
		    $access_token         = $ap['access_token'];  
		    $device_type          = $ap['device_type'];
		    $event_id             = $ap['event_id'];
		    $caledar_event_id     = $ap['caledar_event_id'];
		    $caledar_event_flag   = $ap['caledar_event_flag'];
		    $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
		        //pr($member_details);
		    if (empty($access_token_result)) {
		      $response['status']['error_code'] = 1;
		      $response['status']['message']    = 'Unauthorize Token';        
		      $this->displayOutput($response);
		    }
		    else{
		        //** added for ios calender **/
		      	if($device_type==1)  ///for IOS
                      {
                        if($caledar_event_flag == 0){
                          $update_cond_arr   = array('event_id' => $event_id);
                          $update_evt_arr    = array('caledar_event_id_ios' => $ap['caledar_event_id']);
                          $this->mcommon->update('master_event',$update_cond_arr,$update_evt_arr);
                          $event_message  = 'Event added in calendar successfully.';
                        }
                        else{
                          $update_cond_arr   = array('event_id' => $event_id);
                          $update_evt_arr    = array('caledar_event_id_ios' => '');
                          $this->mcommon->update('master_event',$update_cond_arr,$update_evt_arr);
                          $event_message  = 'Event removed from calendar successfully.';
                        } 
                      }
                      else  // for android
                      {
                        if($caledar_event_flag == 0){
                          $update_cond_arr   = array('event_id' => $event_id);
                          $update_evt_arr    = array('caledar_event_id' => $ap['caledar_event_id']);
                          $this->mcommon->update('master_event',$update_cond_arr,$update_evt_arr);
                          $event_message  = 'Event added in calendar successfully.';
                        }
                        else{
                          $update_cond_arr   = array('event_id' => $event_id);
                          $update_evt_arr    = array('caledar_event_id' => '');
                          $this->mcommon->update('master_event',$update_cond_arr,$update_evt_arr);
                          $event_message  = 'Event removed from calendar successfully.';
                        } 
                      }    
				$current_dt  = date('Y-m-d');
				$response['status']['error_code']      						     = 0;
				$response['status']['message']         						     = $event_message; 
				$response['response']['event_details']['message'] 			     = $event_message;
				$response['response']['event_details']['caledar_event_id'] 	 = $caledar_event_id;
	      	}      	
	    }
	    else {
	        $response['status']['error_code'] = 1;
	        $response['status']['message']    = 'Please fill up all required fields.';
	    }
  	} 
	else {
		$response['status']['error_code'] = 1;
		$response['status']['message']    = 'Wrong http method type.';
		//$response['response']   = $this->obj;      
	}
  	$this->displayOutput($response);
}
  private function checkHttpMethods($http_method_type)
  {
    if ($_SERVER['REQUEST_METHOD'] == $http_method_type) {
      return 1;
    }
  }
  private function check_access_token($access_token, $device_type,$member_id)
  {

    $condition_token = array('access_token' => $access_token, 'device_type' => $device_type,'member_id'=>$member_id);
    //pr($condition_token);
    $access_token_result = $this->mapi->getRow('api_token', $condition_token);
    return $access_token_result;
  }
  private function sendMail($data,$name,$email)
  {

    $config['protocol']       = 'smtp';
    $config['smtp_host']      = 'ssl://mail.fitser.com';
    $config['smtp_port']      = '465';  
   //$config['smtp_user']     = 'test123@fitser.com';
      //$config['smtp_pass']  = 'Test123@';
      $config['smtp_user']    = 'clubfenicia@fenicialounge.in';
      $config['smtp_pass']    = 'Club123@';
    $config['charset']        = 'utf-8';
    $config['newline']        = "\r\n";
    $config['mailtype']       = 'html';
    $config['validation']     = TRUE;  

    $this->email->initialize($config);

    $this->email->set_crlf("\r\n");    
    $this->email->from($email, $name);
    
    $this->email->to($data['to']);

    $this->email->subject($data['subject']);
    $this->email->message($data['message']);

    $this->email->send();
    //echo $this->email->print_debugger(); die;
    return true;
  }

  /////////////////////////////added by ishani on 04.02.2019/////////////////////////////////////
  public function checkReservation()
{
    $zone_name  = '';
    $result  = array();
    $ap      = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
       
        if (empty($ap['no_of_guests'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'No. of guests field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }        
        if (empty($ap['reservation_date'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Reservation date field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }
        if (empty($ap['reservation_time'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Reservation time field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }       
        if (empty($ap['zone_id'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Zone field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }
        $member_id            = $ap['member_id'];
        $access_token         = $ap['access_token'];  
        $device_type          = $ap['device_type'];

        $access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);

        if (empty($access_token_result)) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Unauthorize Token';        
          $this->displayOutput($response);
        }
        else{
    //       	$zone_data    = $this->mapi->getRow('master_zone',array("zone_id" => $ap['zone_id']));
    //       	if(!empty($zone_data)){            
	   //         if($zone_data['cover_charges'] !='0'){
	   //           $zone_minimum_price = $zone_data['cover_charges'];
	   //           $zone_price_type    ='cover';
	   //         }
	   //         else{
	   //           $zone_minimum_price = $zone_data['advance_charges'];
	   //           $zone_price_type ='advance';
	   //         }
    //       	}
    //       	else{
	   //         $zone_minimum_price = '';
	   //         $zone_price_type    = '';
    //       	}
  		//   	/*$current_dt  	= date('d-m-Y H:i:s');       
    //   	  	$rev_time 		= date('H:i:s',strtotime($ap["reservation_time"]));
  		// 	$rev_dt 		= str_replace('/','-',$ap["reservation_date"]).$rev_time;
    //       	if(strtotime($current_dt.'+24 hours') >= strtotime($rev_dt)){
	   //         $response['status']['error_code'] = 1;
	   //         $response['status']['message']    = "Can not reserve, Reservation date should be 24 hours before.";
    //       	}*/
    //       	$current_dt  	= date('d-m-Y');
  		// 	$rev_dt 		= str_replace('/','-',$ap["reservation_date"]);
    //       	if(strtotime($current_dt.'+1 day') >= strtotime($rev_dt)){
	   //         $response['status']['error_code'] = 1;
	   //         $response['status']['message']    = "Sorry can not reserve.Reservation is closed for tomorrow.";
    //       	}
    //       	else{
	   //       	//echo "JHJK";exit;
	   //         $selectedTime             = $ap['reservation_time'];
	   //         $start_time_range         = date('H:i:s',strtotime("-90 minutes", strtotime($selectedTime)));
	   //         $end_time_range           = date('H:i:s',strtotime("+90 minutes", strtotime($selectedTime)));
	   //         $reservation_condition    = "reservation_date= '".DATE('Y-m-d',strtotime(str_replace('/','-',$ap["reservation_date"])))."' and zone_id = '".$ap['zone_id']."' and member_id != '".$member_id."' and reservation_time between '".$start_time_range."' and '".$end_time_range."'";
	   //         $reservation_list         = $this->mapi->getRow('reservation',$reservation_condition);
	   //         if(!empty($reservation_list)){
	              
	   //           $response['status']['error_code']           = 1;
	   //           $response['status']['message']              = 'Opp!Sorry the zone is already reserved for the given date & time';
	   //           $this->displayOutput($response);
	   //         }
	   //         else{
	   //             $response['status']['error_code'] = 0;
    //     			$response['status']['message']    = 'Success.You can proceed for reservation';
	   //       	}
	   //       }
	            $response['status']['error_code'] = 0;
        			$response['status']['message']    = 'Success.You can proceed for reservation';
        }
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
      }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);

}
  //////////////////////////////////reservation checking api//////////////////////////////////////
  
  ///////////////////////////////new otp/////////////////////////////////////
  public function mobileOtpverification()
  {
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        if (empty($ap['country_code'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Country code is required.';
         
          $this->displayOutput($response);
        }
        if (empty($ap['mobile_no'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Mobile no is required.';
         
          $this->displayOutput($response);
        }
        
        $mobile   = $ap['country_code'].$ap['mobile_no'];
        $otp      = mt_rand(1000,9999);
        $message="Your OTP - ".$otp;
    
          $response_sms = $this->smsSend($ap['mobile_no'],$message);
          //echo $response_sms;exit;
          $response['status']['error_code'] = 0;
          $response['status']['message']    = "OTP Successfully Generated.";
          $response['response']['otp_verification']['otp']      = $otp;
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields';        
      }
    }
    else{
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';        
    }
    $this->displayOutput($response);
  }
  //////////////////added on 01.07.2020//////////////////////////////////////////////////
  //////////////////////////API added for PAYTM Checksum //////////////////////////////////
  public function paytm_checksum()
  {
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        if (empty($ap['order_id'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Order Id is required.';
         
          $this->displayOutput($response);
        }
        if (empty($ap['merchant_key'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Merchant Key is required.';
          $this->displayOutput($response);
        }
        if (empty($ap['MID'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'MID is required';
          
          $this->displayOutput($response);
        }
        if (empty($ap['amount'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Amount is required';
          
          $this->displayOutput($response);
        }
        if (empty($ap['currency'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Currency is required';
          
          $this->displayOutput($response);
        }
        if (empty($ap['customerId'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'customerId is required';
          
          $this->displayOutput($response);
        }
        require_once("PaytmChecksum.php");

		// $mid="gjcQuy61011549477922";
		// $order_id="21321321321";
		// $merchant_key="p1%6_VpqqH_4EeeP";

		$mid=$ap['MID'];
		$order_id=$ap['order_id'];
		$merchant_key=$ap['merchant_key'];
		$amount=$ap['amount'];
		$currency=$ap['currency'];
		$customerId=$ap['customerId'];

		/* for sandbox */
		//$callbackUrl="https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=".$order_id;

		/* for production */
		$callbackUrl="https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=".$order_id;

		$paytmParams = array();

		$paytmParams["body"] = array(
		    "requestType"   => "Payment",
		    "mid"           => $mid,
		    "websiteName"   => "WEBSTAGING",
		    "orderId"       => $order_id,
		    "callbackUrl"   => "https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=".$order_id,
		    "txnAmount"     => array(
		        "value"     => $amount,
		        "currency"  => $currency,
		    ),
		    "userInfo"      => array(
		        "custId"    => $customerId,
		    ),
		);

		/*
		* Generate checksum by parameters we have in body
		* Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
		*/
		$checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $merchant_key);

		$paytmParams["head"] = array(
		    "signature"	=> $checksum
		);

		$post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

		/* for Staging */
		//$url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=".$mid."&orderId=".$order_id;

		/* for Production */
		 $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=".$mid."&orderId=".$order_id;

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
		$responsePaytm = curl_exec($ch);
		//echo $responsePaytm; die;
		$responsePaytm_arr=json_decode($responsePaytm);
		$response_body=$responsePaytm_arr->body;
		$status=$response_body->resultInfo->resultStatus;
		if($status=="S") /////success
		{
		    $token=$response_body->txnToken;

		    $response['status']['error_code'] = 0;
         	$response['status']['message']    = 'Success';
         	$response['response']['txnToken']    = $token;
		}
		else   /////error
		{
		    $error_code=$response_body->resultInfo->resultCode;
		    $error_msg=$response_body->resultInfo->resultMsg;
		    $response['status']['error_code'] = $error_code;
         	$response['status']['message']    = $error_msg;
		}
		 
         
         $this->displayOutput($response);
             
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields';        
      }
    }
    else{
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';        
    }
    $this->displayOutput($response);
  }

  /////////////////////////version control/////////////////////////////
  
  public function version_control()
  {
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        if (empty($ap['version'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Version is required.';
         
          $this->displayOutput($response);
        }
        
       	$check_version_condition = array('id' => 1);
        $versiondetails          = $this->mapi->getRow('version_control', $check_version_condition);
        $updateResponseArr=array();
        if($versiondetails['version_app']<=$ap['version'])
        {
        	$response['status']['error_code'] = 0;
            $response['status']['message']    = '';
            $updateResponseArr['updateRequired']='no';
            $updateResponseArr['severity']='';
            $updateResponseArr['dialog_message']='';
        }
        else
        {
        	$response['status']['error_code'] = 0;
           	$response['status']['message']    = '';
            $updateResponseArr['updateRequired']="Yes";
           	if($versiondetails['is_mandatory']==1)
        	{
        		$updateResponseArr['severity']="critical";
            	$updateResponseArr['dialog_message']=$versiondetails['msg_mandatory'];
        	}
        	else
        	{
        		$updateResponseArr['severity']="nonCritical";
            	$updateResponseArr['dialog_message']=$versiondetails['msg_not_mandatory'];
        	}
        }
		 
         $response['response']['updateResponse']    = $updateResponseArr;
         $this->displayOutput($response);
             
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields';        
      }
    }
    else{
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';        
    }
    $this->displayOutput($response);
  }

  /////////////////////////version control IOS/////////////////////////////
  
  public function version_control_ios()
  {
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        if (empty($ap['version'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Version is required.';
         
          $this->displayOutput($response);
        }
        
       	$check_version_condition = array('id' => 1);
        $versiondetails          = $this->mapi->getRow('version_control', $check_version_condition);
        $updateResponseArr=array();
        if($versiondetails['version_ios']==$ap['version'])
        {
        	$response['status']['error_code'] = 0;
            $response['status']['message']    = '';
            $updateResponseArr['updateRequired']='no';
            $updateResponseArr['severity']='';
            $updateResponseArr['dialog_message']='';
        }
        else
        {
        	$response['status']['error_code'] = 0;
           	$response['status']['message']    = '';
            $updateResponseArr['updateRequired']="Yes";
           	if($versiondetails['is_mandatory']==1)
        	{
        		$updateResponseArr['severity']="critical";
            	$updateResponseArr['dialog_message']=$versiondetails['msg_mandatory'];
        	}
        	else
        	{
        		$updateResponseArr['severity']="nonCritical";
            	$updateResponseArr['dialog_message']=$versiondetails['msg_not_mandatory'];
        	}
        }
		 
         $response['response']['updateResponse']    = $updateResponseArr;
         $this->displayOutput($response);
             
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields';        
      }
    }
    else{
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';        
    }
    $this->displayOutput($response);
  }

  ////////////////////////////////for comment card ////////////////////////////////////////////////
  public function saveComment(){

	  $result  = array();
	  $ap      = json_decode(file_get_contents('php://input'), true);
	  if ($this->checkHttpMethods($this->http_methods[0])) {    
	    if (sizeof($ap)) {
	      if(empty($ap['date_of_visit'])) {
          		$response['status']['error_code'] = 1;
          		$response['status']['message']    = 'Date of visit is required.';
         
          		$this->displayOutput($response);
        	}

	        if(!empty($ap['date_of_visit'])) {
	        	$date=$ap['date_of_visit'];
				    $format="Y-m-d";

    				if(!$this->validateDate($date,$format))
    				{
    					$response['status']['error_code'] = 1;
              			$response['status']['message']    = 'Date of visit format is wrong.It should be Y-m-d';
             
              			$this->displayOutput($response);
    				}           		
        	} 
        // 	if(empty($ap['user_id'])) {
        //   		$response['status']['error_code'] = 1;
        //   		$response['status']['message']    = 'User id is required.';
         
        //   		$this->displayOutput($response);
        // 	}
        $member_id            = 0;
        if(isset($ap['member_id']))
        {
            $member_id            = $ap['member_id'];
        }
	        $comment_data   = array(
	                                'member_id'    => $member_id,
	                                'visit_date'    => $ap['date_of_visit'],
	                                'food_varity'   => $ap['food_varity'],
	                                'food_quality'  => $ap['food_quality'],
	                                'food_serving'  => $ap['food_serving'],
	                                'food_presentation' => $ap['food_presentation'],
	                                'service_speed'   => $ap['service_speed'],
	                                'service_courtesy'   => $ap['service_courtesy'],
	                                'service_knowledge'   => $ap['service_knowledge'],
	                                'venue_atmosphere'   => $ap['venue_atmosphere'],
	                                'venue_cleanliness'   => $ap['venue_cleanliness'],
	                                'staff'   => $ap['staff'],
	                                'suggestion'   => $ap['suggestion']
	                        );
	        $comment_id  = $this->mcommon->insert('comment_card',$comment_data);
	        
	        if(!empty($comment_id)){	        
	          $response['status']['error_code']         = 0;
	          $response['status']['message']            = 'Your comment successfully submitted.';
	        }
	        else{
	          $response['status']['error_code'] = 1;
	          $response['status']['message']    = 'Sorry! comment is not submitted successfully.';
	        }
	    }
	    else {
	      $response['status']['error_code'] = 1;
	      $response['status']['message']    = 'Please fill up all required fields.';
	    }
	  } else {
	      $response['status']['error_code'] = 1;
	      $response['status']['message']    = 'Wrong http method type.';
	      //$response['response']   = $this->obj;      
	  }
	  $this->displayOutput($response);
	}

	function validateDate($date, $format = 'Y-m-d')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	    return $d && $d->format($format) === $date;
	}

	//////////////////update version////////////////////////
	public function versionUpdate()
	{
	  $result  = array();
	  $ap      = json_decode(file_get_contents('php://input'), true);
	  $version_ios="";
	  $version_android="";
	  $version_data   = array();
	  if ($this->checkHttpMethods($this->http_methods[0])) {    
	    if (sizeof($ap)) {
	      if(!empty($ap['version_ios'])) {
          		$version_ios = $ap['version_ios'];
          		$version_data['version_ios']=$version_ios;

        	}
        	if(!empty($ap['version_android'])) {
          		$version_android = $ap['version_android'];
          		$version_data['version_app']=$version_android;
        	}

	    
	       $update_version_where    = array('id' => '1');
		   $this->mcommon->update('version_control',$update_version_where,$version_data);
		   
	        
	        $response['status']['error_code']         = 0;
	        $response['status']['message']            = 'Version successfully updated.';
	    }
	    else {
	      $response['status']['error_code'] = 1;
	      $response['status']['message']    = 'Please fill up all required fields.';
	    }
	  } else {
	      $response['status']['error_code'] = 1;
	      $response['status']['message']    = 'Wrong http method type.';
	      //$response['response']   = $this->obj;      
	  }
	  $this->displayOutput($response);
	}

  ///////////////////////////added fpor contact API on 20_07_2020///////////////////////////
  public function contactinfo()
  {
        $contact_condition = array('id' => 1);
        $contact_info          = $this->mapi->getRow('contact_info', $contact_condition);
        if(empty($contact_info)){
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Contact info Not Available';
            $this->displayOutput($response);
        }
        else{
            $response['status']['error_code'] = 0;
            $response['status']['message']    = 'Success';
            $response['response']['contact_info']   = $contact_info;
                   
            }        
      
    $this->displayOutput($response);
  }
  
  ///reservation test
  public function doReservationTest()
{
    $zone_name  = '';
    $result  = array();
    $ap      = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        if (empty($ap['first_name'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'First Name field is required';
          //$response['response']   = $this->obj;
          $this->displayOutput($response);
        }
        if (empty($ap['last_name'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Last Name field is required';
          //$response['response']   = $this->obj;
          $this->displayOutput($response);
        }       
        if (empty($ap['email'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Email field is required';
          //$response['response']   = $this->obj;
          
          $this->displayOutput($response);
        }
        if (empty($ap['phone_no'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Phone no. field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }
        if (empty($ap['no_of_guests'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'No. of guests field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }        
        if (empty($ap['reservation_date'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Reservation date field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }
        if (empty($ap['reservation_time'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Reservation time field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }       
        if (empty($ap['zone_id'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Zone field is required';
          //$response['response']   = $this->obj;          
          $this->displayOutput($response);
        }
        $member_id            = $ap['member_id'];
        $access_token         = $ap['access_token'];  
        $device_type          = $ap['device_type'];

        //$access_token_result  = $this->check_access_token($access_token, $device_type,$member_id);
$access_token_result=1;
        if (empty($access_token_result)) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Unauthorize Token';        
          $this->displayOutput($response);
        }
        else{
          	$zone_data    = $this->mapi->getRow('master_zone',array("zone_id" => $ap['zone_id']));
          	if(!empty($zone_data)){            
	            // if($zone_data['cover_charges'] !='0'){
	            //   $zone_minimum_price = $zone_data['cover_charges'];
	            //   $zone_price_type    ='cover';
	            // }
	            // else{
	            //   $zone_minimum_price = $zone_data['advance_charges'];
	            //   $zone_price_type ='advance';
	            // }

              /** added by Ishani on 23.07.2020  **/
              $zone_minimum_price = $zone_data['cover_charges'];
              $zone_price_type    ='cover';
          	}
          	else{
	            $zone_minimum_price = '';
	            $zone_price_type    = '';
          	}
  		  	/*$current_dt  	= date('d-m-Y H:i:s');       
      	  	$rev_time 		= date('H:i:s',strtotime($ap["reservation_time"]));
  			$rev_dt 		= str_replace('/','-',$ap["reservation_date"]).$rev_time;
          	if(strtotime($current_dt.'+24 hours') >= strtotime($rev_dt)){
	            $response['status']['error_code'] = 1;
	            $response['status']['message']    = "Can not reserve, Reservation date should be 24 hours before.";
          	}*/
          	$current_dt  	= date('d-m-Y');
  			    $rev_dt 		= str_replace('/','-',$ap["reservation_date"]);
          	if(strtotime($current_dt.'+1 day') >= strtotime($rev_dt)){
	            $response['status']['error_code'] = 1;
	            $response['status']['message']    = "Reservation date should be after 1 day";
          	}
          	else{
	          	//echo "JHJK";exit;
	           // $selectedTime             = $ap['reservation_time'];
	           // //$start_time_range         = date('H:i:s',strtotime("-90 minutes", strtotime($selectedTime)));
	           // //$end_time_range           = date('H:i:s',strtotime("+90 minutes", strtotime($selectedTime)));
	           // //$reservation_condition    = "reservation_date= '".DATE('Y-m-d',strtotime(str_replace('/','-',$ap["reservation_date"])))."' and zone_id = '".$ap['zone_id']."' and member_id != '".$member_id."' and reservation_time between '".$start_time_range."' and '".$end_time_range."'";
	           //  $reservation_condition    ="";
	           // $reservation_list         = $this->mapi->getRow('reservation');
	            
	           // $this->db->last_query(); die;
	           // print_r($reservation_list);
	           // if(!empty($reservation_list)){
	              
	           //   $response['status']['error_code']           = 1;
	           //   $response['status']['message']              = 'Opp!Sorry the zone is already reserved for the given date & time';
	           //   $this->displayOutput($response);
	           // }
//else{
	                $insrtarry    = array('reservation_date'    => DATE('Y-m-d',strtotime(str_replace('/','-',$ap["reservation_date"]))),
	                                      'reservation_time'    => DATE('H:i:s',strtotime($ap["reservation_time"])),
	                                      'zone_id'             => $ap['zone_id'],
	                                      'no_of_guests'        => $ap['no_of_guests'],
	                                      'zone_price'          => $zone_minimum_price,
	                                      'zone_price_type'     => $zone_price_type,
	                                      'reservation_for'     => $ap['reservation_for'],
	                                      'member_id'           => $member_id,
	                                      'first_name'          => $ap['first_name'],
	                                      'last_name'           => $ap['last_name'],
	                                      'email'               => $ap['email'],
	                                      'country_code'        => $ap['country_code'],
	                                      'member_mobile'       => $ap['phone_no'],
	                                      'add_from'            => 'front',
	                                      'message'             => $ap['message'],
	                                      'status'              => '1',
                                        'reservation_type'    => 'App',
	                                      'created_by'          => $member_id,
	                                      'created_on'          => date('Y-m-d')
	                                    );
	                $reservation_id     = $this->mapi->insert('reservation',$insrtarry);
	                
	                if($reservation_id)
	                {
	                  	$transaction_arr    = array('reservation_id'  => $reservation_id,
	                                              'transaction_id'  => $ap['tran_id'],
	                                              'payment_mode'    => $ap['tran_type'],
	                                              'payment_amount'  => $ap['tran_amount'],
	                                              'transaction_date'=> date('Y-m-d'),
	                                              'payment_status'  => 'success'
	                                        );
	                  	//$this->mapi->insert('reservation_payment_transaction',$transaction_arr);
	                  	$condition          = "reservation.reservation_id ='".$reservation_id."'";            
	                  	$reservation_list   = $this->mapi->getReservationList($condition);
	                  	if(!empty($reservation_list)){
		                	foreach($reservation_list as $key=>$val){
		                      $reservation_data['reservation_id']     =  $val['reservation_id'];
		                      $reservation_data['reservation_date']   =  date('d/m/Y',strtotime($val['reservation_date']));
		                      $reservation_data['reservation_time']   =  DATE('h:i A',strtotime($val["reservation_time"]));
		                      $reservation_data['zone_id']            =  $val['zone_id'];
		                      $reservation_data['zone_name']          =  $val['zone_name'];                      
		                      $reservation_data['zone_price']         =  $val['zone_price'];
                          $reservation_data['total_amount']       =  $val['payment_amount'];
		                      $reservation_data['no_of_guest']        =  $val['no_of_guests'];
		                      $reservation_data['first_name']         =  $val['first_name'];
		                      $reservation_data['last_name']          =  $val['last_name'];
		                      $reservation_data['country_code']       =  $val['country_code'];
		                      $reservation_data['phone_no']           =  $val['member_mobile'];
		                      $reservation_data['email']              =  $val['email'];
		                      $reservation_data['reservation_for']    =  $val['reservation_for'];
		                      $reservation_data['message']            =  $val['message'];
		                      $reservation_data['reservation_status'] =  $val['status']; //0=>canceled 1=>pending 2=>reserved 3=>rejected
		                      $reservation_data['cancellation_reason']=  $val['cancellation_reason'];

		                      $zone_name          = $reservation_data['zone_name'];
                                $reservation_id     = $reservation_data['reservation_id'];
		                      $reservation_date   = $reservation_data['reservation_date'];
		                      $reservation_time   = $reservation_data['reservation_time'];
                                $name               = $reservation_data['first_name'];
		                        $no_of_guest        = $reservation_data['no_of_guest'];
		                      $reservation_status = 'Pending';
	                        $cancellation_reason= $reservation_data['cancellation_reason'];
		                    } 
		                    $response['status']['error_code']           = 0;
		                    $response['status']['message']              = 'Request for Reservation submitted Successfully.';
		                    $response['response']['reservation_details']   = $reservation_data;

		                    if($reservation_data['reservation_status'] == 0){
		                      $title      = "Reservation Cancelled";
		                      $message    = "Your request for reservation is cancelled.";
		                    }
		                    elseif($reservation_data['reservation_status'] == 1){
		                      $title      ="Reservation Pending";
		                      $message    = "Reservation done,\n pending from admin.";
		                    }            
		                    elseif($reservation_data['reservation_status'] == 2){
		                      $title     ="Reservation Confirmed";
		                      $message   = "Your request for reservation is Confirmed.";
		                    }            
		                    else{
		                      $title     ="Reservation Rejected";
		                      $message   = "Your request for reservation is rejected by Club Fenicia. \n Reason given - ".$cancellation_reason;
		                    }
		                    $message_data = array('title' => $title,'message' => $message);
		                    $user_fcm_token_data  = $this->mcommon->getRow('device_token',array('member_id' => $member_id));
		                    //pr($user_fcm_token_data);
		                    if(!empty($user_fcm_token_data)){
                          $member_datas  = $this->mcommon->getRow('master_member',array('member_id' => $member_id));
                          if($member_datas['notification_allow_type'] == '1'){
                              if($ap['device_type'] == 1){
                                $this->pushnotification->send_ios_notification($user_fcm_token_data['fcm_token'], $message_data);
                              }
                              else{
                                $this->pushnotification->send_android_notification($user_fcm_token_data['fcm_token'], $message_data);
                              }
                          } 
                          $admin_notification_details = $name.' reservation request for '.$zone_name.' on '.$reservation_date.' at '.$reservation_time.' is '.$reservation_status;
		                      $notification_arr = array('member_id'                 => $member_id,                            
                                                    	'reservation_id'            => $reservation_id,
		                                                'notification_title'        => $title,
		                                                'notification_description'  => $message,
                                                    	'admin_notification_details'=> $admin_notification_details,
		                                                'status'                    => '1',
		                                                'created_on'                => date('Y-m-d H:i:s')
		                                                );
		                     // $insert_data      = $this->mcommon->insert('notification', $notification_arr);
                          //echo $this->db->last_query(); die;
		                      /****************** Send Reservation details to the member ****************************/
		                     $admin_cond               = array('role_id' => '1','status' =>'1','is_delete' =>'1');
	      				          $admin_data               = $this->mcommon->getRow('user',$admin_cond);
	      				          if(!empty($admin_data)){
	      				            $admin_email            = $admin_data['email'];
	      				            $admin_name             = $admin_data['first_name'];
	      				          }
	      				          else{
	      				            $admin_email            = 'support@fenicia.in';
	      				            $admin_name             = 'admin';
	      				          }

		                      //$link                   = base_url('api/member_activation/'.$member_id);
		                      $logo                     = base_url('public/images/logo.png');
		                      $mail['name']             = $ap['first_name'];
		                      $mail['to']               = $ap['email'];    
		                      //$mail['to']               = 'sreelabiswas.kundu@met-technologies.com';
		                      $mail['zone_name']        = '<span style="color:#f9b92d"><strong>Zone: </strong></span>'.$zone_name.'<br>';
		                      $mail['reservation_date'] = $reservation_date;
		                      $mail['reservation_time'] = $reservation_time;
		                      $mail['no_of_guest']      = $no_of_guest;
		                      $mail['reservation_status'] = $reservation_status;
		                      $mail['subject']          = 'Club Fenicia - Reservation request received Mail';                             
		                      $mail_temp                = file_get_contents('./global/mail/reservation_template.html');
		                      $mail_temp                = str_replace("{web_url}", base_url(), $mail_temp);
		                      $mail_temp                = str_replace("{logo}", $logo, $mail_temp);
		                      $mail_temp                = str_replace("{shop_name}", 'Club Fenicia', $mail_temp);  
		                      $mail_temp                = str_replace("{name}", $mail['name'], $mail_temp);
		                      $mail_temp                = str_replace("{zone_name}", $mail['zone_name'], $mail_temp);
		                      $mail_temp                = str_replace("{reservation_date}", $mail['reservation_date'], $mail_temp);
		                      $mail_temp                = str_replace("{reservation_time}", $mail['reservation_time'], $mail_temp);
		                      $mail_temp                = str_replace("{no_of_guest}", $mail['no_of_guest'], $mail_temp);
		                      $mail_temp                = str_replace("{reservation_status}", $mail['reservation_status'], $mail_temp);        
		                      $mail_temp                = str_replace("{current_year}", date('Y'), $mail_temp);           
		                      $mail['message']          = $mail_temp;
		                      $from_email         = 'clubfenicia@fenicialounge.in';
		                      $this->sendMail($mail,'Club Fenicia',$from_email);

	                   		/****************** Send Reservation details to the Admin ****************************/

	                   		  $logo                     = base_url('public/images/logo.png');
		                      $mail['name']             = $admin_name;
		                      $mail['to']               = $admin_email;    
		                      //$mail['to']               = 'sreelabiswas.kundu@met-technologies.com';
		                      $mail['zone_name']        = '<span style="color:#f9b92d"><strong>Zone: </strong></span>'.$zone_name.'<br>';
		                      $mail['reservation_date'] = $reservation_date;
		                      $mail['reservation_time'] = $reservation_time;
		                      $mail['no_of_guest']      = $no_of_guest;
		                      $mail['reservation_status'] = $reservation_status;
		                      $mail['subject']          = 'Club Fenicia - Reservation request received Mail';                             
		                      $mail_temp                = file_get_contents('./global/mail/reservation_template.html');
		                      $mail_temp                = str_replace("{web_url}", base_url(), $mail_temp);
		                      $mail_temp                = str_replace("{logo}", $logo, $mail_temp);
		                      $mail_temp                = str_replace("{shop_name}", 'Club Fenicia', $mail_temp);  
		                      $mail_temp                = str_replace("{name}", $mail['name'], $mail_temp);
		                      $mail_temp                = str_replace("{zone_name}", $mail['zone_name'], $mail_temp);
		                      $mail_temp                = str_replace("{reservation_date}", $mail['reservation_date'], $mail_temp);
		                      $mail_temp                = str_replace("{reservation_time}", $mail['reservation_time'], $mail_temp);
		                      $mail_temp                = str_replace("{no_of_guest}", $mail['no_of_guest'], $mail_temp);
		                      $mail_temp                = str_replace("{reservation_status}", $mail['reservation_status'], $mail_temp);        
		                      $mail_temp                = str_replace("{current_year}", date('Y'), $mail_temp);           
		                      $mail['message']          = $mail_temp;
		                      $from_email         		= 'clubfenicia@fenicialounge.in';
		                      //$this->sendMail($mail,'Club Fenicia',$from_email);


		                      /********************************** Send reservation details in sms *************************************************/

		                      $message  = "Thank you for confirming your Reservation at Club Fenicia. Your reservation details are: \n";
		                      $message .= "Zone: ".$zone_name."\n Date: ".$reservation_date."\n Time: ".$reservation_time."\n No. of Guests: ".$no_of_guest."\n Status: Pending";
		                      $message .= "WE WOULD BE HOLDING YOUR RESERVATION FOR 15 MINUTES FROM THE TIME OF RESERVATION AND IT WILL BE RELEASED WITHOUT ANY PRIOR INFORMATION.";
                          $this->smsSend($ap['phone_no'],$message);
		                    }
	                  	}
						else{
							$response['status']['error_code']           = 1;
							$response['status']['message']              = 'No data available';
						}                
	                }
	                else{
	                  $response['status']['error_code']           = 1;
	                  $response['status']['message']              = 'No data available';
	                }
//	          	}//
	          }
        }
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
      }
  } else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Wrong http method type.';
      //$response['response']   = $this->obj;      
  }
  $this->displayOutput($response);

}

/** added on 09.08.2020  **/

  ////////////////////////////////QR code scan data save ////////////////////////////////////////////////
  public function saveQrdata(){

    $result  = array();
    $ap      = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {    
      if (sizeof($ap)) {
        if(empty($ap['member_id'])) {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Member id is required.';
         
              $this->displayOutput($response);
          }

          if(empty($ap['price'])) {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Price is required.';
         
              $this->displayOutput($response);         
          } 
          if(empty($ap['transaction_id'])) {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Transaction id is required.';
         
              $this->displayOutput($response);         
          } 
          if(empty($ap['invoice_id'])) {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Invoice id is required.';
         
              $this->displayOutput($response);         
          } 
          if(empty($ap['date'])) {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Date is required.';
         
              $this->displayOutput($response);         
          } 
          if(empty($ap['time'])) {
              $response['status']['error_code'] = 1;
              $response['status']['message']    = 'Time is required.';
         
              $this->displayOutput($response);         
          } 
                  
          $insert_data   = array(
                                  'member_id'    => $ap['member_id'],
                                  'price'    => $ap['price'],
                                  'transaction_id'   => $ap['transaction_id'],
                                  'invoice_id'  => $ap['invoice_id'],
                                  'invoice_date'  => $ap['date'],
                                  'invoice_time' => $ap['time']
                          );
          $insert_id  = $this->mcommon->insert('qrcode_data',$insert_data);
          
          if(!empty($insert_id)){          
            $response['status']['error_code']         = 0;
            $response['status']['message']            = 'Data successfully submitted.';
          }
          else{
            $response['status']['error_code'] = 1;
            $response['status']['message']    = 'Sorry! data is not submitted successfully.';
          }
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields.';
      }
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;      
    }
    $this->displayOutput($response);
  }

  /**********************added by ishani on 25.09.2020 Apple sign up login *****************************/
  //apple login
  public function appleLogin()
  {
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        if (empty($ap['device_type'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Device type is required.';
         
          $this->displayOutput($response);
        }
        
        
        if (empty($ap['device_token'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Device token is required.';
          $this->displayOutput($response);
        }
        if (empty($ap['apple_id'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Apple id is required.';
          $this->displayOutput($response);
        }
        $apple_id=$ap['apple_id'];
        $email="";
        $memberdetails          = array(); 
        $loggedIn=0;
        /***** chk with email id *************************/
        if(isset($ap['email'])&&$ap['email']!='')
        {
          $email=$ap['email'];
          $check_member_condition = array('email' => $email);
          $memberdetails          = $this->mcommon->getRow('master_member', $check_member_condition);

          if(!empty($memberdetails))
          {
            //////////////update apple id in member row
            $update_data=array();
            $update_data['apple_id']=$apple_id;
            $this->mcommon->update('master_member', $check_member_condition,$update_data);
            $loggedIn=1;
          }
          else
          {
             $check_member_appleid = array('apple_id' => $apple_id);
              $memberdetails          = $this->mcommon->getRow('master_member', $check_member_appleid);
          }
        }
        else
        {
           $check_member_appleid = array('apple_id' => $apple_id);
            $memberdetails          = $this->mcommon->getRow('master_member', $check_member_appleid);
        }

        /*****chk with apple id *************************/
        if(empty($memberdetails))
        {
          
          $check_member_appleid = array('apple_id' => $apple_id);
          $memberdetails          = $this->mcommon->getRow('master_member', $check_member_appleid);
          //echo $this->db->last_query(); die;
            if(empty($memberdetails)){
              // $response['status']['error_code'] = 1;
              // $response['status']['message']    = 'Invalid Apple id';
              // $this->displayOutput($response);

              /********************signup user*******************************************/
              $data=array();
              $data['email']=$email;
              $data['apple_id']=$apple_id;
              $data = array(    
      
                'email'                 => $email, 
               
                'status'                => '1',
                'registration_type'     => 2,
                'added_form'            => 'front',
                'login_status'          => '1',
                'apple_id'              => $apple_id,
                'created_by'            => '',     
                'created_ts'            => date('Y-m-d H:i:s'),       
              );
              
              $member_id = $this->mapi->insert('master_member', $data); 
              if($member_id>0)
              {
                   
                    $condition      = array('member_id' =>$member_id);

                     $memberdetails          = $this->mcommon->getRow('master_member', $condition);
                    $update_arr     = array('login_status' =>'1');
                    $update_result  = $this->mapi->update('master_member',$condition,$update_arr);

                    if($update_result){              
                        $response['status']['error_code'] = 0;
                        $response['status']['message']    = 'Login Successfully';
                        $response['response']['member']   = $memberdetails;
                        $api_token_details                = $this->mapi->getRow('api_token', $condition);
                        $device_token_details             = $this->mapi->getRow('device_token', $condition);

                        if (empty($api_token_details) && empty($device_token_details)) {

                          $device_token_data['member_id']          = $memberdetails['member_id'];
                          $device_token_data['device_type']        = $ap['device_type'];
                          $device_token_data['device_token']       = '';
                          $device_token_data['fcm_token']          = $ap['device_token'];
                          $device_token_data['login_status']       = '1';
                          $device_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                          $device_token_data['session_start_time'] = date('Y-m-d H:i:s');
                          $device_token_data['session_end_time']   = '';

                          $insert_data          = $this->mapi->insert('device_token', $device_token_data);

                          $api_token_data['member_id']          = $memberdetails['member_id'];
                          $api_token_data['device_type']        = $ap['device_type'];
                          $api_token_data['access_token']       = md5(mt_rand() . '_' . $memberdetails['member_id']);
                          $api_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                          $api_token_data['session_start_time'] = date('Y-m-d H:i:s');
                          $api_token_data['session_end_time']   = '';

                          $insert_data  = $this->mapi->insert('api_token', $api_token_data);
                          $all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                          if(!empty($all_member)){
                            if($all_member[0]['membership_id'] ==''){
                              $all_member_details = $all_member[0];
                            }
                            else{
                              foreach($all_member as $val){
                                  $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                                  if(!empty($all_member_datas)){
                                      $all_member_details = $all_member_datas[0];
                                  }
                                  else{
                                      $all_member_details = $all_member[0];
                                      $all_member_details['membership_id'] = null;
                                  }
                              }
                            }                       
                          }
                          else {
                            $response['status']['error_code'] = 1;
                            $response['status']['message']    = 'Unable to generate access token';                    
                          }
                          if ($all_member_details) {
                            if($all_member_details['profile_img'] !='' ){
                              $all_member_details['profile_pic_updated'] = '1';
                            }
                            else{
                              $all_member_details['profile_pic_updated'] = '0';
                            }
                            $response['status']['error_code'] = 0;
                            $response['status']['message']    = 'Login Successfully';
                            $response['response']['member']   = $all_member_details;
                            
                          } else {
                            $response['status']['error_code'] = 1;
                            $response['status']['message']    = 'Unable to generate access token';                    
                          }
                        } else {
                          $condition_token                    = array('member_id' =>$member_id);
                          $api_token_updata['device_type']    = $ap['device_type'];
                          $api_token_updata['access_token']   = $api_token_details['access_token'];
                          $update_data  = $this->mapi->update('api_token', $condition_token, $api_token_updata);

                          $device_token_updata['device_type']     = $ap['device_type'];
                          $device_token_updata['fcm_token']       = $ap['device_token'];
                          $update_data  = $this->mapi->update('device_token', $condition_token, $device_token_updata);

                          $all_member_details   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                          $all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                          if(!empty($all_member)){
                            if($all_member[0]['membership_id'] ==''){
                              $all_member_details = $all_member[0];
                            }
                            else{
                              foreach($all_member as $val){
                                  $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                                  if(!empty($all_member_datas)){
                                      $all_member_details = $all_member_datas[0];
                                  }
                                  else{
                                      $all_member_details = $all_member[0];
                                      $all_member_details['membership_id'] = null;
                                  }
                              }
                            }                       
                          }
                          else {
                            $response['status']['error_code'] = 1;
                            $response['status']['message']    = 'Unable to generate access token';                    
                          }
                          if ($all_member_details) {
                            if($all_member_details['profile_img'] !='' ){
                              $all_member_details['profile_pic_updated'] = '1';
                            }
                            else{
                              $all_member_details['profile_pic_updated'] = '0';
                            }
                            $response['status']['error_code'] = 0;
                            $response['status']['message']    = 'Login Successfully';
                            $response['response']['member']   = $all_member_details;
                          } else {
                            $response['status']['error_code'] = 1;
                            $response['status']['message']    = 'Unable to update access token';                   
                          }
                        }
                    }
                    else {
                      $response['status']['error_code'] = 1;
                      $response['status']['message']    = 'Oops!something went wrong to login';
                    }   
                
                // $response['status']['error_code'] = 0;
                // $response['status']['message']    = 'Sign up Successfully';
                // $response['response']['member']   = $memberdetails;
              }
              /*****************************************************************************/
          }
          else
          {
            $loggedIn=1;
          }
        }
        else
        {
          
            if($memberdetails['is_delete'] != '0'){
                $response['status']['error_code'] = 1;
                $response['status']['message']    = 'Member account is removed by admin';
                $this->displayOutput($response);
            }
            elseif($memberdetails['status'] == '0'){
                $response['status']['error_code'] = 1;
                $response['status']['message']    = 'Member account is not inactive status';
                $this->displayOutput($response);
            }
          
           
            else{
                    $member_id      = $memberdetails['member_id'];
                    $condition      = array('member_id' =>$member_id);
                    $update_arr     = array('login_status' =>'1');
                    $update_result  = $this->mapi->update('master_member',$condition,$update_arr);

                    if($update_result){              
                        $response['status']['error_code'] = 0;
                        $response['status']['message']    = 'Login Successfully';
                        $response['response']['member']   = $memberdetails;
                        $api_token_details                = $this->mapi->getRow('api_token', $condition);
                        $device_token_details             = $this->mapi->getRow('device_token', $condition);

                        if (empty($api_token_details) && empty($device_token_details)) {

                          $device_token_data['member_id']          = $memberdetails['member_id'];
                          $device_token_data['device_type']        = $ap['device_type'];
                          $device_token_data['device_token']       = '';
                          $device_token_data['fcm_token']          = $ap['device_token'];
                          $device_token_data['login_status']       = '1';
                          $device_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                          $device_token_data['session_start_time'] = date('Y-m-d H:i:s');
                          $device_token_data['session_end_time']   = '';

                          $insert_data          = $this->mapi->insert('device_token', $device_token_data);

                          $api_token_data['member_id']          = $memberdetails['member_id'];
                          $api_token_data['device_type']        = $ap['device_type'];
                          $api_token_data['access_token']       = md5(mt_rand() . '_' . $memberdetails['member_id']);
                          $api_token_data['date_of_creation']   = date('Y-m-d H:i:s');
                          $api_token_data['session_start_time'] = date('Y-m-d H:i:s');
                          $api_token_data['session_end_time']   = '';

                          $insert_data  = $this->mapi->insert('api_token', $api_token_data);
                          $all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                          if(!empty($all_member)){
                            if($all_member[0]['membership_id'] ==''){
                              $all_member_details = $all_member[0];
                            }
                            else{
                              foreach($all_member as $val){
                                  $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                                  if(!empty($all_member_datas)){
                                      $all_member_details = $all_member_datas[0];
                                  }
                                  else{
                                      $all_member_details = $all_member[0];
                                      $all_member_details['membership_id'] = null;
                                  }
                              }
                            }                       
                          }
                          else {
                            $response['status']['error_code'] = 1;
                            $response['status']['message']    = 'Unable to generate access token';                    
                          }
                          if ($all_member_details) {
                            if($all_member_details['profile_img'] !='' ){
                              $all_member_details['profile_pic_updated'] = '1';
                            }
                            else{
                              $all_member_details['profile_pic_updated'] = '0';
                            }
                            $response['status']['error_code'] = 0;
                            $response['status']['message']    = 'Login Successfully';
                            $response['response']['member']   = $all_member_details;
                            
                          } else {
                            $response['status']['error_code'] = 1;
                            $response['status']['message']    = 'Unable to generate access token';                    
                          }
                        } else {
                          $condition_token                    = array('member_id' =>$member_id);
                          $api_token_updata['device_type']    = $ap['device_type'];
                          $api_token_updata['access_token']   = $api_token_details['access_token'];
                          $update_data  = $this->mapi->update('api_token', $condition_token, $api_token_updata);

                          $device_token_updata['device_type']     = $ap['device_type'];
                          $device_token_updata['fcm_token']       = $ap['device_token'];
                          $update_data  = $this->mapi->update('device_token', $condition_token, $device_token_updata);

                          $all_member_details   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                          $all_member   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $member_id));
                          if(!empty($all_member)){
                            if($all_member[0]['membership_id'] ==''){
                              $all_member_details = $all_member[0];
                            }
                            else{
                              foreach($all_member as $val){
                                  $all_member_datas   = $this->mapi->getMemberDetailsRow(array('mm.member_id' => $val['member_id'],'package_membership_mapping.status' => '1'));
                                  if(!empty($all_member_datas)){
                                      $all_member_details = $all_member_datas[0];
                                  }
                                  else{
                                      $all_member_details = $all_member[0];
                                      $all_member_details['membership_id'] = null;
                                  }
                              }
                            }                       
                          }
                          else {
                            $response['status']['error_code'] = 1;
                            $response['status']['message']    = 'Unable to generate access token';                    
                          }
                          if ($all_member_details) {
                            if($all_member_details['profile_img'] !='' ){
                              $all_member_details['profile_pic_updated'] = '1';
                            }
                            else{
                              $all_member_details['profile_pic_updated'] = '0';
                            }
                            $response['status']['error_code'] = 0;
                            $response['status']['message']    = 'Login Successfully';
                            $response['response']['member']   = $all_member_details;
                          } else {
                            $response['status']['error_code'] = 1;
                            $response['status']['message']    = 'Unable to update access token';                   
                          }
                        }
                    }
                    else {
                      $response['status']['error_code'] = 1;
                      $response['status']['message']    = 'Oops!something went wrong...';
                    }          
                }
            }
              
      }
      else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Please fill up all required fields';        
      }
    }
    else{
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';        
    }
    $this->displayOutput($response);  
  }

    public function viewProfilebyEmail()
  {
    $ap = json_decode(file_get_contents('php://input'), true);
    if ($this->checkHttpMethods($this->http_methods[0])) {
      if (sizeof($ap)) {
        if (empty($ap['email'])) {
          $response['status']['error_code'] = 1;
          $response['status']['message']    = 'Email is required.';
         
          $this->displayOutput($response);
        }
          $email          = $ap['email'];
           
          $member_details   = $this->mapi->getMemberDetailsRow(array('email' => $email));
            
          if(!empty($member_details)){
          
          $response['status']['error_code'] = 0;
            $response['status']['message']    = 'Member Details';
            $response['response']['member']   = $member_details;
          }
          else{
            $response['status']['error_code'] = 1;
              $response['status']['message']    = 'User does not exist';        
        
          }
      }
      else {
      $response['status']['error_code'] = 1;
      $response['status']['message']    = 'Please fill up all required fields.';
        }
    } else {
        $response['status']['error_code'] = 1;
        $response['status']['message']    = 'Wrong http method type.';
        //$response['response']   = $this->obj;
      
    }
  $this->displayOutput($response);
  }
}