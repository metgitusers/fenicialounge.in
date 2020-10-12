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
		$this->load->model('admin/mmembership');
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
	public function ViewDetails($reservation_id){
		$result 					= array();
		$result['zone_list']		= $this->mcommon->getDetails('master_zone',array('status' => '1'));
		$result['member_list']		= $this->mmember->get_member_list('1');
		$condition					= "reservation_id = '".$reservation_id."'";
		$reservation_list			= $this->mreservation->get_reservation_list($condition);
		//pr($reservation_list);
		if(!empty($reservation_list)){
			$result['reservation_list']	= $reservation_list[0];
		}
		else{
			$result['reservation_list']	= '';
		}
		$result['content'] 		= 'admin/reservation/view';
		$this->_load_view($result);
	}
	public function edit($reservation_id,$redirect_page  =null){
		$result 					= array();
		if(!empty($redirect_page)){
			$result['page']			= $redirect_page;
		}
		else{
			$result['page']			= '';
		}
		$result['zone_list']		= $this->mcommon->getDetails('master_zone',array('status' => '1'));
		$result['member_list']		= $this->mmember->get_member_list('1');
		$condition					= "reservation_id = '".$reservation_id."'";
		$reservation_list			= $this->mreservation->get_reservation_list($condition);
		//pr($reservation_list);
		if(!empty($reservation_list)){
			$result['reservation_list']	= $reservation_list[0];
		}
		else{
			$result['reservation_list']	= '';
		}
		$result['content'] 		= 'admin/reservation/add';
		$this->_load_view($result);
	}
	public function editReservation(){
		//pr($_POST);
		$data 	=  array();		
		$result =  array();	
		$extra_guest = 0;
		$no_of_guests 	= $this->input->post( 'no_of_guests' );
		$zone_id		= $this->input->post( 'zone_id' );	
		$condition  	= array('zone_id' => $zone_id);            
        $zone_list  	= $this->mcommon->getRow('master_zone',$condition);  
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
            if($no_of_guests>=2 && $no_of_guests<=3)
            {
              $zone_price    = '7000';
            }
            if($no_of_guests>=4 && $no_of_guests<=5)
            {
              $zone_price    = '10000';
            }
            if($no_of_guests>=6 && $no_of_guests<=8)
            {
              $zone_price    = '12000';
            }
          }
          ///////////////////////////////////////////////////////////////////////
          else
          {
              $zone_price    = $this->calculateZonePrice($basic_price,$no_of_guests,$extra_guest,$zone_list['additional_charges'],$zone_list['zone_type']);
          }
      	}     	
		/*$current_dt  	= date('d-m-Y H:i:s');       
		$rev_time 		= date('H:i:s',strtotime($this->input->post("reservation_time")));
		$rev_dt 		= str_replace('/','-',$this->input->post("reservation_date")).$rev_time;
      	if(strtotime($current_dt.'+24 hours') >= strtotime($rev_dt)){
	        
	        $this->session->set_flashdata('error_msg','Sorry! Can not reserve, Reservation date should be 24 hours before.');
			$this->session->set_flashdata('success_msg','');
			redirect('admin/Reservation/add');
      	}*/
      	/*$current_dt  	= date('d-m-Y');
		$rev_dt 		= str_replace('/','-',$this->input->post("reservation_date"));
      	if(strtotime($current_dt.'+1 day') >= strtotime($rev_dt)){
            $this->session->set_flashdata('error_msg','Sorry can not reserve.Reservation is closed for tomorrow.');
			$this->session->set_flashdata('success_msg','');
			redirect('admin/reservation/add');
      	}
      	else{ */ //(changes done on the basis of email sent on 02/03/20)

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
	        
        	$reserve_updatearry    = array('reservation_date'    => DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("reservation_date")))),
                                      'reservation_time'    	=> DATE('H:i:s',strtotime($this->input->post("reservation_time"))),
                                      'zone_id'             	=> $this->input->post( 'zone_id' ),
                                      'no_of_guests'        	=> $this->input->post( 'no_of_guests' ),
                                      'zone_price'          	=> $zone_price,
                                      'zone_price_type'     	=> 'cover',
                                      'reservation_for'     	=> $this->input->post( 'reservation_for' ),
                                      'member_id'           	=> $member_id,
                                      'first_name'          	=> $first_name,
                                      'last_name'           	=> $last_name,
                                      'email'               	=> $email,
                                      'country_code'        	=> '91',
                                      'member_mobile'       	=> $mobile,
                                      'add_from'            	=> 'admin',
                                      'message'            	 	=> $this->input->post( 'message' ),
                                      'reservation_type'       	=> $this->input->post( 'reservation_type' ),
                                      'status'              	=> $this->input->post( 'rev_status' ),
                                      'updated_by'          	=> $this->session->userdata('user_data'),
                                      'updated_on'          	=> date('Y-m-d H:i:s')
                                    );
			//pr($reserve_insrtarry);
			$reservation_id     	= $this->input->post( 'reservation_id' );
			$reserve_condarry 		= array('reservation_id' => $reservation_id); 
        	$result 				= $this->mcommon->update('reservation',$reserve_condarry,$reserve_updatearry);
        	if($result)
        	{
        		$log_data = array('action' 		=> 'Edit',
								  'statement' 	=> "Edited a reservation ID-'".$reservation_id."'",
								  'action_by'	=> $this->session->userdata('user_data'),
								  'IP'			=> getClientIP(),
								  'id'          => $reservation_id,
								  'type'        =>"RESERVATION",
								  'status'		=> '1'
								);
				$this->mcommon->insert('log',$log_data);
        		$transaction_arr    = array('reservation_id'  	=> $result,
                                            'transaction_id'  => rand(100000000000,999999999999),
                                            'payment_mode'    => 'cash',
                                            'payment_amount'  => $zone_price,
                                            'transaction_date'=> date('Y-m-d'),
                                            'payment_status'  => 'success'
                                        );
                $this->mcommon->insert('reservation_payment_transaction',$transaction_arr);

        		$zone_data		 	= $this->mcommon->getRow('master_zone',array('zone_id' =>$this->input->post( 'zone_id' )));


		/****************** Send Reservation details to the member ****************************/
                      //$link                   = base_url('api/member_activation/'.$member_id);
              	$logo                     = base_url('public/images/logo.png');
              	if(!empty($first_name)){
              		$mail['name']             = $first_name;
              	} 
              	else{
              		$mail['name']             = '';
              	}             	
              	if(!empty($email)){
					$mail['to']               = $email;    
					//$mail['to']               = 'sreelabiswas.kundu@met-technologies.com';
					$mail['zone_name']        = '';
					$mail['reservation_date'] = $this->input->post("reservation_date");
					$mail['reservation_time'] = $this->input->post("reservation_time");
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
				}
				if(!empty($mobile)){
					/**  modified by ishani on 09.09.2020 **/
					$message  = "Thank you for confirming your Reservation at Club Fenicia. Your reservation details are here: \n";
                  	$message .= "Booking ref no.".$reservation_id."\n Date: ".$mail['reservation_date']."\n Time: ".$mail['reservation_time']."\n Time: "."\n No. of Guests: ".$mail['no_of_guest']."\n Status: Confirmed." ;
                  	$message .= "WE WOULD BE HOLDING YOUR RESERVATION FOR 15 MINUTES FROM THE TIME OF RESERVATION AND IT WILL BE RELEASED WITHOUT ANY PRIOR INFORMATION.";
                  	$this->smsSend($mobile,$message);
                  	///////////////////////////////////////////////////////////////
				}
              	$this->session->set_flashdata('error_msg','');
        		$this->session->set_flashdata('success_msg','Successfully Data Updated');
        		$redirect_page = $this->input->post('page');
        		if(!empty($redirect_page) && $redirect_page =='dashboard'){
        			redirect('admin/dashboard');
        		}
        		else
        		{ 
        			redirect('admin/Reservation');
        		}
        	}
	    //}	
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: ADD NEW member  
	*/
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
	public function add(){
		$result 					= array();
		$result['reservation_list'] = '';
		$result['zone_list']		= $this->mcommon->getDetails('master_zone',array('status' => '1','is_delete' => '0'));
		//PR($result['zone_list']);
		$result['member_list']		= $this->mmembership->getMembershipDetails('1');
		$result['content'] 			= 'admin/reservation/add';
		//pr($result);
		$this->_load_view($result);
	}
	public function bookReservation(){
		//pr($_POST);
		$data 	=  array();		
		$result =  array();	
		$extra_guest = 0;
		$no_of_guests 	= $this->input->post( 'no_of_guests' );
		$zone_id		= $this->input->post( 'zone_id' );	
		$condition  	= array('zone_id' => $zone_id);            
        $zone_list  	= $this->mcommon->getRow('master_zone',$condition);  
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
      	}     	
		/*$current_dt  	= date('d-m-Y H:i:s');       
		$rev_time 		= date('H:i:s',strtotime($this->input->post("reservation_time")));
		$rev_dt 		= str_replace('/','-',$this->input->post("reservation_date")).$rev_time;
      	if(strtotime($current_dt.'+24 hours') >= strtotime($rev_dt)){
	        
	        $this->session->set_flashdata('error_msg','Sorry! Can not reserve, Reservation date should be 24 hours before.');
			$this->session->set_flashdata('success_msg','');
			redirect('admin/Reservation/add');
      	}*/
      	/*$current_dt  	= date('d-m-Y');
		$rev_dt 		= str_replace('/','-',$this->input->post("reservation_date"));
      	if(strtotime($current_dt.'+1 day') >= strtotime($rev_dt)){
            $this->session->set_flashdata('error_msg','Sorry can not reserve.Reservation is closed for tomorrow.');
			$this->session->set_flashdata('success_msg','');
			redirect('admin/reservation/add');
      	}
      	else{ */ //(changes done on the basis of email sent on 02/03/20)

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
                                      'zone_price'          	=> $zone_price,
                                      'zone_price_type'     	=> 'cover',
                                      'reservation_for'     	=> $this->input->post( 'reservation_for' ),
                                      'member_id'           	=> $member_id,
                                      'first_name'          	=> $first_name,
                                      'last_name'           	=> $last_name,
                                      'email'               	=> $email,
                                      'country_code'        	=> '91',
                                      'member_mobile'       	=> $mobile,
                                      'add_from'            	=> 'admin',
                                      'message'            	 	=> $this->input->post( 'message' ),
                                      'reservation_type'       	=> $this->input->post( 'reservation_type' ),
                                      'status'              	=> '2',
                                      'created_by'          	=> $this->session->userdata('user_data'),
                                      'created_on'          	=> date('Y-m-d H:i:s')
                                    );
			//pr($reserve_insrtarry);
        	$result 	= $this->mcommon->insert('reservation',$reserve_insrtarry);
        	
        	if($result)
        	{

        		/** added by ishani on 18.09.2020 */
                    //fn defined in common helper
        			$user_data=array();
        			$name=$first_name." ".$last_name;
                    $user_data['name']=$name;
                    $user_data['email']=$email;
                    $user_data['mobile']=$mobile;
                    insert_all_user($user_data);
                 //*************************************************//
        		$log_data = array('action' 		=> 'Add',
								  'statement' 	=> "Added a new reservation ID-'".$result."'",
								  'action_by'	=> $this->session->userdata('user_data'),
								  'IP'			=> getClientIP(),
								  'id'          => $result,
								  'type'        =>"RESERVATION",
								  'status'		=> '1'
								);
				$this->mcommon->insert('log',$log_data);
        		$transaction_arr    = array('reservation_id'  	=> $result,
                                            'transaction_id'  => rand(100000000000,999999999999),
                                            'payment_mode'    => 'cash',
                                            'payment_amount'  => $zone_price,
                                            'transaction_date'=> date('Y-m-d'),
                                            'payment_status'  => 'success'
                                        );
                $this->mcommon->insert('reservation_payment_transaction',$transaction_arr);

        		$zone_data		 	= $this->mcommon->getRow('master_zone',array('zone_id' =>$this->input->post( 'zone_id' )));
		/****************** Send Reservation details to the member ****************************/
                      //$link                   = base_url('api/member_activation/'.$member_id);
              	$logo                     = base_url('public/images/logo.png');
              	if(!empty($first_name)){
              		$mail['name']             = $first_name;
              	} 
              	else{
              		$mail['name']             = '';
              	}             	
              	if(!empty($email)){
					$mail['to']               = $email;    
					//$mail['to']               = 'sreelabiswas.kundu@met-technologies.com';
					$mail['zone_name']        = '';
					$mail['reservation_date'] = $this->input->post("reservation_date");
					$mail['reservation_time'] = $this->input->post("reservation_time");
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
					$msg 					  = registration_mail($mail);
				}
				if(!empty($mobile)){
					/** modified by ishani on 09.09.2020 **/
					$reservation_id=$result ;
					$message  = "Thank you for confirming your Reservation at Club Fenicia. Your reservation details are here: \n";
                  	$message .= "Booking ref no.".$reservation_id."\n Date: ".$mail['reservation_date']."\n Time: ".$mail['reservation_time']."\n No. of Guests: ".$mail['no_of_guest']."\n Status: Confirmed. \n";
                  	$message .= "WE WOULD BE HOLDING YOUR RESERVATION FOR 15 MINUTES FROM THE TIME OF RESERVATION AND IT WILL BE RELEASED WITHOUT ANY PRIOR INFORMATION.";
                  	$this->smsSend($mobile,$message);

                  	//////////////////////////////////////////////////////
				}
              	$this->session->set_flashdata('error_msg','');
        		$this->session->set_flashdata('success_msg','Successfully Reserved');
        		redirect('admin/Reservation');
        	}
	    //}	
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
	public function DeleteReservation($reservation_id){
		$response				= array();
		$return_response		= '';
		$id						= $reservation_id;		
		$tbl_column_name		= 'reservation_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'master_member';
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$log_data = array('action' 		=> 'Delete',
							  'statement' 	=> "Deleted reservation ID-".$reservation_id,
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          =>$reservation_id,
							  'type'        =>"RESERVATION",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
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
	        	$title 		 ="Reservation No-show";
	        	$message 	 = "Sorry to say your request for reservation is No-show by fenicia due to ".strtolower($reservation_data['cancellation_reason']).".";
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
				$mail_message = "Your request for reservation is No-show.Reason given - ".$reservation_data['cancellation_reason'].".";
				$rev_status   = 'No-show';
			}
	        elseif($status == 2){
	        	$mail_subject 	  = 'Club Fenicia - Reservation Confirmed mail';
	        	$mail_message 	  = "Thank you for confirming your reservation with Club Fenicia.";
	        	$rev_status   	  = 'Confirmed';
	        }            
	        if($status == 0 || $status == 2 || $status == 3){
	        	$logo					  = base_url('public/images/logo.png');
				$params['name']			  =	$user_name;
				$params['to']			  =	$user_email;
				$mail['zone_name']        = $zone_name;
              	$mail['reservation_date'] = $reservation_date;
              	$mail['reservation_time'] = DATE('h:i:A',strtotime($reservation_time));
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
				
				/** modified by ishani on 09.09.2020 **/
				$reservation_id=$id;
				$sms_message  = $mail_message."Details - ";
		        $sms_message .=  "Booking ref no.".$reservation_id."\n Date: ".date("d/m/y",strtotime($reservation_date))."\n Time: ".date("h:i A",strtotime($reservation_time)).", No of guest: ".$no_of_guests.", Status: ".$rev_status;
              	$this->smsSend($user_ph,$sms_message);
              	//////////////////////////////////////////
	        }        	
			$log_data = array('action' 		=> 'Edit',
							  'statement' 	=> "Made reservation ID -'".$id."' ".$rev_status,
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          =>$id,
							  'type'        =>"RESERVATION",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);	
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
		$responce_arr['mobile'] 	= '';
		$responce_arr['first_name'] = '';
		$responce_arr['last_name'] 	= '';
		$field_name					= $this->input->post("field_name");
		$field_value 				= $this->input->post("field_value");
		$pastGuestInfo  			= $this->mcommon->getRow('reservation',array($field_name=>$field_value));
		if(!empty($pastGuestInfo)){
			
			$responce_arr['mobile'] 	= $pastGuestInfo['member_mobile'];
			$responce_arr['email'] 		= $pastGuestInfo['email'];
			$responce_arr['first_name'] = $pastGuestInfo['first_name'];
			$responce_arr['last_name'] 	= $pastGuestInfo['last_name'];
		}
		echo json_encode($responce_arr);exit;
	}	
	public function smsSend($mobile,$message){
	    //echo $mobile."<br>".$message;exit;
	    //$api_key = '45DB969F6550A9';
	    $api_key = '45DA414F762394';
	    //$contacts = '97656XXXXX,97612XXXXX,76012XXXXX,80012XXXXX,89456XXXXX,88010XXXXX,98442XXXXX';
	    $contacts= $mobile;
	    $from = 'FENCIA';
	    //$from = 'TXTSMS';
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
  	public function checkInOutStatusChange(){
  		$response				= array();
		$message_data 			= array();
		$return_response		= '';
		$reservation_id			= $this->input->post('reservation_id');
		$check_in_out_status	= $this->input->post('status');
		if($check_in_out_status == '1'){
				$checkin_datetime	= date('Y-m-d H:i:s');
				$change_to 			= 'check-in';
				$update_arr 		= array('check_in_out_status' 	=> $check_in_out_status,
											'checkin_datetime' 		=> $checkin_datetime
											);
			}
			elseif($check_in_out_status == '0'){
				$checkout_datetime	= date('Y-m-d H:i:s');
				$change_to 			= 'check-out';
				$update_arr 		= array('check_in_out_status' 	=> $check_in_out_status,
											'checkout_datetime' 	=> $checkout_datetime
											);
			}
		$update_cond 			= array('reservation_id' 		=> $reservation_id);
		
		$response 				= $this->mcommon->update('reservation',$update_cond,$update_arr);
		if($response){
			
			$log_data = array('action' 		=> 'Edit',
							  'statement' 	=> "Made check in/out status change to '".$change_to."'' for reservation-'".$reservation_id."'",
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $reservation_id,
							  'type'        =>"RESERVATION",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
			echo 1;exit;
		}
		else{
			echo 0;exit;
		}
  	}
  	public function  user_list(){
  		$result 					= array();		
		$result['content'] 			= 'admin/reservation/user_list';
		//pr($result);
		$this->_load_view($result);
  	} 
  	public function getReservationBookingUserList(){
  		$responce_arr   = array();
  		$from_data      = '';
        $to_data        = '';
        $zone_id        = '';
        $status_id      = '';
        $reservation_id = '';
        $time 			= '';
        $data['reservation_user_list']    = $this->mreservation->getReservationBookingUserLists();
        //pr($result_data);
       
        
        $responce_arr['html'] = $this->load->view('admin/reservation/ajax_reservation_user_list',$data,true);
		echo json_encode($responce_arr);exit;
  	}	
}