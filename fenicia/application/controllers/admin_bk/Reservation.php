<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reservation extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->admin=$this->session->userdata('admin');
		$this->load->library('PushNotification');
		$this->load->model('admin/mreservation');
		$this->load->model('admin/mzone');
		$this->load->model('admin/mmember');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}	
	public function index() { 
		//echo $this->session->userdata('email');die;
		$result 			= array();
		
		$result['content'] 				= 'admin/reservation/list';
		$result['zone_list'] 			= $this->mzone->get_zone_list('1');
		$result['reservation_list'] 	= $this->mreservation->get_reservation_list();
		
		$this->_load_view($result);		
	}
	public function getReservationTimeSlot(){
		$return_new_array	= array();
		$reservation_date	= $this->input->post('reservation_date');
		$day 	= date('l',strtotime(str_replace('/','-',$reservation_date)));
		$time_slot_list		= $this->mreservation->getTimeDetails(array('day_name'=>$day,'status' =>'1'));
		//pr($time_slot_list);
		if (!empty($time_slot_list)) {
			$return_new_array =	$time_slot_list;
			//pr($return_new_array);
		} else {
			$return_new_array = 'Blank';
		}
		echo json_encode($return_new_array);
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: Edit reservation  
	*/
	public function edit($reservation_id){
		$result 					= array();
		$result['zone_list']		= $this->mcommon->getDetails('master_zone',array('status' => '1'));
		$result['member_list']		= $this->mmember->get_member_list('1');
		$condition					= "reservation_id = '".$reservation_id."'";
		$reservation_list			= $this->mreservation->get_reservation_list($condition);
		//pr($reservation_list);
		if(!empty($reservation_list)){
			$result['reservation_list']	= $reservation_list;
		}
		else{
			$result['reservation_list']	= '';
		}
		$result['content'] 		= 'admin/reservation/add';
		$this->_load_view($result);
	}
	public function editReservation($reservation_id){
		//pr($_POST);
		$data 	=  array();		
		$result =  array();
		$result['content'] 		= 'admin/reservation/add';
		$member_id 				= $this->input->post('member_id');
		$zone_data    			= $this->mcommon->getRow('master_zone',array("zone_id" => $this->input->post('zone_id')));
      	if(!empty($zone_data)){            
	        if($zone_data['cover_charges'] !='0'){
	          $zone_minimum_price = $zone_data['cover_charges'];
	          $zone_price_type    ='cover';
	        }
	        else{
	          $zone_minimum_price = $zone_data['advance_charges'];
	          $zone_price_type ='advance';
	        }
      	}
		else{
			$zone_minimum_price = '';
			$zone_price_type    = '';
		}      	
		$current_dt  	= date('d-m-Y');
		$rev_dt 		= str_replace('/','-',$this->input->post("reservation_date"));
      	if(strtotime($current_dt.'+1 day') >= strtotime($rev_dt)){
            $this->session->set_flashdata('error_msg','Sorry can not reserve.Reservation is closed for tomorrow.');
			$this->session->set_flashdata('success_msg','');
			redirect('admin/Reservation/add');
      	}
      	else{
			$selectedTime             = $ap['reservation_time'];
			$start_time_range         = date('H:i:s',strtotime("-90 minutes", strtotime($selectedTime)));
			$end_time_range           = date('H:i:s',strtotime("+90 minutes", strtotime($selectedTime)));
			$reservation_condition    = "reservation_date= '".DATE('Y-m-d',strtotime(str_replace('/','-',$ap["reservation_date"])))."' and zone_id = '".$ap['zone_id']."' and member_id != '".$member_id."' and reservation_time between '".$start_time_range."' and '".$end_time_range."'";
			$reservation_list         = $this->mcommon->getRow('reservation',$reservation_condition);

			if($reservation_list){
			  
			  	$response['status']['error_code']           = 1;
			  	$response['status']['message']              = 'Opp!Sorry the zone is already reserved for the given date & time';
			  	$this->displayOutput($response);
			}
			else{		
				if($this->input->post('reservation_for') !='My self'){	
					$member_id 	= '';			
			    	$first_name = $this->input->post('new_first_name');
			    	$last_name 	= $this->input->post('new_last_name');
			    	$email 		= $this->input->post('new_email');
			    	$mobile 	= $this->input->post('new_mobile');
		        }	 
		        else{
		        	
		        	$member_id 	= $this->input->post('member_id');
		        	$comd_mem	= " and mu.member_id ='".$member_id."'";
		        	$member_data= $this->mmember->getMemberDetails($comd_mem);
		        	if(!empty($member_data)){
		        		$first_name = $member_data['first_name'];
		        		$last_name 	= $member_data['last_name'];
		        		$email 		= $this->input->post('email');
		        		$mobile 	= $this->input->post('mobile');
		        	}
		        	
		        }
	        	$reserve_updatearry    	= array('reservation_date'    	=> DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("reservation_date")))),
												'reservation_time'    	=> DATE('H:i:s',strtotime($this->input->post("reservation_time"))),
												'zone_id'             	=> $this->input->post( 'zone_id' ),
												'no_of_guests'        	=> $this->input->post( 'no_of_guests' ),
												'zone_price'          	=> $zone_minimum_price,
												'zone_price_type'     	=> $zone_price_type,
												'reservation_for'     	=> $this->input->post( 'reservation_for' ),
												'member_id'           	=> $member_id,
												'first_name'          	=> $first_name,
												'last_name'           	=> $last_name,
												'email'               	=> $email,
												'country_code'        	=> '91',
												'member_mobile'       	=> $mobile,
												'add_from'            	=> 'admin',
												'message'            	=> $this->input->post( 'message' ),
												'status'              	=> '2',
												'created_by'          	=> $this->session->userdata('user_data'),
												'created_on'          	=> date('Y-m-d')
	                                    );
	 
	        	$resvn_condition = array('reservation_id'=>$reservation_id);
	        	$result 	= $this->mcommon->update('reservation',$resvn_condition,$reserve_updatearry);
	        	if($result)
	        	{
	        		$this->session->set_flashdata('success_msg','Successfully reservation request details edited.');
	        		redirect('admin/Reservation');
	        	}
	    	}
    	}
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: ADD NEW member  
	*/
	public function add(){
		$result 					= array();
		$result['reservation_list'] = '';
		$result['zone_list']		= $this->mcommon->getDetails('master_zone',array('status' => '1','is_delete' => '0'));
		//PR($result['zone_list']);
		$result['member_list']		= $this->mmember->get_member_list('1');
		$result['content'] 			= 'admin/reservation/add';
		//pr($result);
		$this->_load_view($result);
	}
	public function bookReservation(){
		//pr($_POST);
		$data 	=  array();		
		$result =  array();		
		$zone_data    = $this->mcommon->getRow('master_zone',array("zone_id" => $this->input->post('zone_id')));
      	if(!empty($zone_data)){            
	        if($zone_data['cover_charges'] !='0'){
	          $zone_minimum_price = $zone_data['cover_charges'];
	          $zone_price_type    ='cover';
	        }
	        else{
	          $zone_minimum_price = $zone_data['advance_charges'];
	          $zone_price_type ='advance';
	        }
      	}
		else{
			$zone_minimum_price = '';
			$zone_price_type    = '';
		}      	
		/*$current_dt  	= date('d-m-Y H:i:s');       
		$rev_time 		= date('H:i:s',strtotime($this->input->post("reservation_time")));
		$rev_dt 		= str_replace('/','-',$this->input->post("reservation_date")).$rev_time;
      	if(strtotime($current_dt.'+24 hours') >= strtotime($rev_dt)){
	        
	        $this->session->set_flashdata('error_msg','Sorry! Can not reserve, Reservation date should be 24 hours before.');
			$this->session->set_flashdata('success_msg','');
			redirect('admin/Reservation/add');
      	}*/
      	$current_dt  	= date('d-m-Y');
		$rev_dt 		= str_replace('/','-',$this->input->post("reservation_date"));
      	if(strtotime($current_dt.'+1 day') >= strtotime($rev_dt)){
            $this->session->set_flashdata('error_msg','Sorry can not reserve.Reservation is closed for tomorrow.');
			$this->session->set_flashdata('success_msg','');
			redirect('admin/reservation/add');
      	}
      	else{

			if($this->input->post('reservation_for') !='My self'){	
				$member_id 	= '';			
		    	$first_name = $this->input->post('new_first_name');
		    	$last_name 	= $this->input->post('new_last_name');
		    	$email 		= $this->input->post('new_email');
		    	$mobile 	= $this->input->post('new_mobile');
	        }	 
	        else{
	        	
	        	$member_id 	= $this->input->post('member_id');
	        	$comd_mem	= " and mu.member_id ='".$member_id."'";
	        	$member_data= $this->mmember->getMemberDetails($comd_mem);
	        	if(!empty($member_data)){
	        		$first_name = $member_data['first_name'];
	        		$last_name 	= $member_data['last_name'];
	        		$email 		= $this->input->post('email');
	        		$mobile 	= $this->input->post('mobile');
	        	}
	        	
	        }
	        
        	$reserve_insrtarry    = array('reservation_date'    => DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("reservation_date")))),
                                      'reservation_time'    	=> DATE('H:i:s',strtotime($this->input->post("reservation_time"))),
                                      'zone_id'             	=> $this->input->post( 'zone_id' ),
                                      'no_of_guests'        	=> $this->input->post( 'no_of_guests' ),
                                      'zone_price'          	=> $zone_minimum_price,
                                      'zone_price_type'     	=> $zone_price_type,
                                      'reservation_for'     	=> $this->input->post( 'reservation_for' ),
                                      'member_id'           	=> $member_id,
                                      'first_name'          	=> $first_name,
                                      'last_name'           	=> $last_name,
                                      'email'               	=> $email,
                                      'country_code'        	=> '91',
                                      'member_mobile'       	=> $mobile,
                                      'add_from'            	=> 'admin',
                                      'message'            	 	=> $this->input->post( 'message' ),
                                      'status'              	=> '2',
                                      'created_by'          	=> $this->session->userdata('user_data'),
                                      'created_on'          	=> date('Y-m-d')
                                    );
			//pr($reserve_insrtarry);
        	$result 	= $this->mcommon->insert('reservation',$reserve_insrtarry);
        	if($result)
        	{
        		$transaction_arr    = array('reservation_id'  	=> $result,
                                            'transaction_id'  => rand(100000000000,999999999999),
                                            'payment_mode'    => 'cash',
                                            'payment_amount'  => $zone_minimum_price,
                                            'transaction_date'=> date('Y-m-d'),
                                            'payment_status'  => 'success'
                                        );
                $this->mcommon->insert('reservation_payment_transaction',$transaction_arr);

        		$zone_data		 	= $this->mcommon->getRow('master_zone',array('zone_id' =>$this->input->post( 'zone_id' )));
		/****************** Send Reservation details to the member ****************************/
                      //$link                   = base_url('api/member_activation/'.$member_id);
                  $logo                     = base_url('public/images/logo.png');
                  $mail['name']             = $first_name;
                  $mail['to']               = $email;    
                  //$mail['to']               = 'sreelabiswas.kundu@met-technologies.com';
                  $mail['zone_name']        = $zone_data['zone_name'];
                  $mail['reservation_date'] = DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("reservation_date"))));
                  $mail['reservation_time'] = DATE('H:i:s',strtotime($this->input->post("reservation_time")));
                  $mail['no_of_guest']      = $this->input->post( 'no_of_guests' );
                  $mail['reservation_status'] = 'Confirmed';
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
                  $msg 						= registration_mail($mail);

              	  $message  = "Thank you for confirming your Reservation at Club Fenicia. Your reservation details are here: \n";
                  $message .=  "Zone: ".$zone_data['zone_name']."\n Date: ".$mail['reservation_date']."\n Time: ".$mail['reservation_time']."\n No. of Guests: ".$mail['no_of_guest']."\n Status: Confirmed";
                  $this->smsSend($mobile,$message);

                  $this->session->set_flashdata('error_msg','');
        		$this->session->set_flashdata('success_msg','Successfully Reserved');
        		redirect('admin/Reservation');
        	}
	    }	
	}
	public function getMemberDetails(){
		$memberarr				= array();
		$member_id 				= $this->input->post('member_id');
		$member_data			= $this->mcommon->getRow('master_member',array('member_id'=>$member_id));
		//pr($member_data);
		$memberarr['email']		= $member_data['email'];
		$memberarr['mobile']	= $member_data['mobile'];
		echo json_encode($memberarr);
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: DELETE member Permanently   
	*/
	private function _load_view($data) {
		   // FUNCTION WRIITTEN IN COMMON HELPER
		$this->load->view('admin/layouts/index', $data);
		
	}
	public function DeleteReservation($member_id){
		$response				= array();
		$return_response		= '';		
		$tbl_column_name		= 'member_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'master_member';
		$return_response		= getStatusCahnge($member_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','Member details successfully deleted');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
		}				
		redirect('admin/member');
	}	
	
	public function changeStatus()
	{
		$response				= array();
		$message_data 			= array();
		$return_response		= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'reservation_id';
		$status     	 		= $this->input->post('change_status');
		$reason     	 		= $this->input->post('reason');
		$chng_status_colm		= 'status';
		$table 					= 'reservation';
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status,$reason);//function definein commonhelper
		if($return_response){
			$reservation_data		= $this->mcommon->getRow('reservation',array('reservation_id' => $id));
			if(!empty($reservation_data)){
				$zone_data		 	= $this->mcommon->getRow('master_zone',array('zone_id' =>$reservation_data['zone_id']));
				$member_id			= $reservation_data['member_id'];
				$zone_name          = $zone_data['zone_name'];
              	$reservation_id     = $reservation_data['reservation_id'];
          		$reservation_date   = $reservation_data['reservation_date'];
              	$reservation_time   = $reservation_data['reservation_time'];
              	$no_of_guests       = $reservation_data['no_of_guests'];
              	$no_of_guests       = $reservation_data['no_of_guests'];
              	$user_name			= $reservation_data['first_name'];
	      		$user_email			= $reservation_data['email'];
	      		$user_ph			= $reservation_data['member_mobile'];
				$user_fcm_token_data	= $this->mcommon->getRow('device_token',array('member_id' => $member_id));
				//pr($user_fcm_token_data);
			}			
			if($status == 0){
	            $title 		 	= "Reservation Cancelled";
	            $message 		= "Your request for reservation is cancelled on your request.Reason given - ".$reservation_data['cancellation_reason'].".";
	        }
	        elseif($status == 1){
	        	$title 		 	="Reservation Pending";
	        	$message 		= "Your request for reservation is waiting for Confirmation.";
	        }            
	        elseif($status == 2){
	        	$title 		 ="Reservation Confirmed";
	        	$message 	 = "Your request for reservation is Confirmed.";
	        }            
	        else{
	        	$title 		 ="Reservation Rejected";
	        	$message 	 = "Sorry to say your request for reservation is rejected by fenicia due to ".strtolower($reservation_data['cancellation_reason']).".";
	        }
	        $message_data = array('title' => $title,'message' => $message);
	        $member_datas  = $this->mcommon->getRow('master_member',array('member_id' => $member_id));
        
	        if(!empty($user_fcm_token_data)){	        	
              	if($member_datas['notification_allow_type'] == '1'){
                  	if($user_fcm_token_data['device_type'] == 1){
				       $this->pushnotification->send_ios_notification($user_fcm_token_data['fcm_token'], $message_data);
			      	}
			      	else{
				       $this->pushnotification->send_android_notification($user_fcm_token_data['fcm_token'], $message_data);
			      	}			      	
              	}	        	
		      	$notification_data  = $this->mcommon->getRow('notification',array('reservation_id' => $id));
		      	$notification_arr	= array('member_id' 				=> $member_id,
		      								'reservation_id' 			=> $id, //reservation_id
		      								'notification_title'		=> $title,
		      								'notification_description'	=> $message,
		      								'status'					=> '1',
		      								'created_on'				=>date('Y-m-d H:i:s')
	      									);
		      	if(!empty($notification_data)){
		      		$notification_cond 	= array('notification_id'=> $notification_data['notification_id'],'reservation_id'=> $id);
		      		$this->mcommon->update('notification',$notification_cond, $notification_arr);
		      	}
		      	else{
		      		$insert_data        = $this->mcommon->insert('notification', $notification_arr);
		      	}
		      	
	      	}	       
			/****************** Send Reservation cancellation /confirmation to the member ****************************/
			if($status == 0){

				$mail_subject = 'Club Fenicia - Reservation cancellation mail';
				$mail_message = "Your request for reservation is cancelled.Reason given - ".$reservation_data['cancellation_reason'].".";
				$rev_status   = 'Cancelled';
			}
			if($status == 3){

				$mail_subject = 'Club Fenicia - Reservation rejection mail';
				$mail_message = "Your request for reservation is Rejected.Reason given - ".$reservation_data['cancellation_reason'].".";
				$rev_status   = 'Rejected';
			}
	        elseif($status == 2){
	        	$mail_subject 	  = 'Club Fenicia - Reservation Confirmed mail';
	        	$mail_message 	  = "Thank you for confirming your reservation with Club Fenicia";
	        	$rev_status   	  = 'Confirmed';
	        }            
	        if($status == 0 || $status == 2 || $status == 3){
	        	$logo					  = base_url('public/images/logo.png');
				$params['name']			  =	$user_name;
				$params['to']			  =	$user_email;
				$mail['zone_name']        = $zone_name;
              	$mail['reservation_date'] = $reservation_date;
              	$mail['reservation_time'] = DATE('H:i:s',strtotime($reservation_time));
              	$mail['no_of_guests']     = $no_of_guests;
              	$mail['status'] 		  = $rev_status;
							
				$params['subject']		=   $mail_subject;															
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
				
				$sms_message  = $mail_message."Details - ";
		        $sms_message .=  "Zone: ".$zone_name.", Date: ".$reservation_date.", Time: ".$reservation_time.", No of guest: ".$no_of_guests.", Status: ".$rev_status;
              	$this->smsSend($user_ph,$sms_message);
	        }
        		
			echo 1;exit;
		}				
		else{
			echo 0 ;exit;
		}
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

	public function getMaxMinCapacity(){
		$responce_arr   = array();
		$max_capacity 	= 1;  
        $min_capacity	= 300; 
		$zone_id      	= $this->input->post("zone_id");
		$cond_zone 		= array('zone_id'=>$zone_id);
		$zone_data	  	= $this->mcommon->getRow('master_zone',$cond_zone);
		//pr($zone_data);
		if(!empty(($zone_data))){
			$max_capacity = $zone_data['maximum_capacity'];
			$min_capacity = $zone_data['minimum_capacity'];
		}
		$responce_arr['max'] = $max_capacity;
		$responce_arr['min'] = $min_capacity;
		echo json_encode($responce_arr);exit;
	}
	public function filterSearch()
	{
		$responce_arr   = array();
		$guest_cnt 		= 0;  
        $reservation_cnt= 0; 
        //pr($_POST);     
        $from_data      = $this->input->post("from_date");
        $to_data        = $this->input->post("to_date");
        $zone_id        = $this->input->post("zone_id");
        $status_id      = $this->input->post("status_id");
        $reservation_id = $this->input->post("reservation_id");
        $time 			= '';
        $result_data    = reservationFilterSearch($from_data,$to_data,$zone_id,$status_id,$time,$reservation_id);
        //pr($result_data);
       
        
        $responce_arr['html'] = $this->load->view('admin/reservation/ajax_reservation_list',$result_data,true);
        echo json_encode($responce_arr);exit;
	}
	public function getReservationDetails($reservation_id)
	{
		$reservation_date	= '';
		$status_id 			= '';
		$zone_id 			= '';
		$result 			= array();
		if(!empty($reservation_id)){
			$reservation_data = $this->mcommon->getRow('reservation',array('reservation_id' =>$reservation_id));
			if(!empty($reservation_data)){
				$reservation_date	= $reservation_data['reservation_date'];
				$zone_id			= $reservation_data['zone_id'];
				$status_id			= '1';
			}
		}
		$result['resv_from_date'] 	= DATE('d/m/Y',strtotime($reservation_date));
		$result['resv_to_date'] 	= DATE('d/m/Y',strtotime($reservation_date));
		$result['zone_id'] 			= $zone_id;
		$result['status_id'] 		= $status_id;

		$result['content'] 			= 'admin/reservation/list';
		$result['zone_list'] 		= $this->mzone->get_zone_list('1');
		$result['reservation_id'] 	= $reservation_id;
		$result['reservation_list'] = $this->mreservation->get_reservation_list();
		
		$this->_load_view($result);	
	}
	public function getPastGuestInfo(){
		$responce_arr   			= array();
		$responce_arr['email'] 		= '';
		$responce_arr['first_name'] = '';
		$responce_arr['last_name'] 	= '';
		$mobile 		= $this->input->post("mobile");
		$pastGuestInfo  = $this->mcommon->getRow('reservation',array('member_mobile'=>$mobile));
		if(!empty($pastGuestInfo)){
			$responce_arr['email'] 		= $pastGuestInfo['email'];
			$responce_arr['first_name'] = $pastGuestInfo['first_name'];
			$responce_arr['last_name'] 	= $pastGuestInfo['last_name'];
		}
		echo json_encode($responce_arr);exit;
	}
	public function reservationCommissionList(){
		if($this->session->userdata('role_id') == '2'){

		}
		else{
			redirect('admin/reservation');
		}
	}
	public function smsSend($mobile,$message){
	    //echo $mobile."<br>".$message;exit;
	    $api_key = '45DB969F6550A9';
	    //$contacts = '97656XXXXX,97612XXXXX,76012XXXXX,80012XXXXX,89456XXXXX,88010XXXXX,98442XXXXX';
	    $contacts= $mobile;
	    $from = 'FENCIA';
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
  	 	
}