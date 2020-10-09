<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->admin=$this->session->userdata('admin');
		$this->load->library('imageupload');
		$this->load->model('admin/mmember');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}
	public function generateRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}	
	public function index() { 
		//echo $this->session->userdata('email');die;
		$result 						= array();
		$result['content'] 				= 'admin/member/list';
		$result['member_active_list'] 	= $this->mmember->get_member_list('1');
		$result['member_inactive_list'] = $this->mmember->get_member_list('0');
		//pr($data);		
		
		$this->_load_view_member($result);		
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: Edit member  
	*/
	public function edit($member_id){
		$result 			= array();
		$condition 			='and mu.member_id = "'.$member_id.'" ';
		$pck_cond 			= array('status'=>'1','is_delete' =>'0');
		$result['package']	= $this->mcommon->getDetails('master_package',$pck_cond);
		$result['member']	= $this->mmember->getMemberDetails($condition);
		$result['member_id']=$member_id;
		$result['member_package']	= $this->mcommon->getRow('package_membership_mapping', array('member_id' =>$member_id,'status' =>'1'));
		if(!empty($result['member_package'])){
			$result['package_type']		= $this->mmember->get_package_type($result['member_package']['package_id']);
		}
		//$result['member']	= $this->mcommon->getRow('master_member',$condition);
		//pr($result['member']);
		$result['content'] 	= 'admin/member/add';
		if(empty($result['member'])){
			redirect('admin/member');
		}else{
			$this->_load_view_member($result);
		}
	}
	public function UpdateMember($member_id){
		$old_membership_id      = '';
		$membership_data	 	= $this->mcommon->getRow("package_membership_mapping",array('member_id' => $member_id,'status' =>'1'));
		if(!empty($membership_data) && !empty($membership_data['membership_id'])){
			$old_membership_id  = $membership_data['membership_id'];
		}
		//pr($_POST);
		$data			= array();
		$package_price 	= '0.00';
		$this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required');		
		$this->form_validation->set_rules('mobile', 'Mobile', 'trim|required');
		$this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required');
		
		if($this->form_validation->run()==FALSE){
			$condition 			='and mu.member_id = "'.$member_id.'" ';
			$result['member']	= $this->mmember->getMemberDetails($condition);
			$result['member_package']	= $this->mcommon->getRow('package_membership_mapping', array('member_id' =>$member_id,'status' =>'1'));
			if(!empty($result['member_package'])){
				$result['package_type']		= $this->mmember->get_package_type($result['member_package']['package_id']);
			}
			$result['content'] 	= 'admin/member/add';
			$this->_load_view_member($result);
		}
		else{
			if(!empty($_FILES['profile_img']['name'])){
					$image_path = '/public/upload_image/profile_photo';
					$file 		= $this->imageupload->image_upload2($image_path,'profile_img');
					//pr($file);
					if($file['status']==0){
						$this->session->set_flashdata('error_msg',$file['result']);
						redirect('admin/member/edit/'.$member_id);
					}	
					else{
			        	$profile_img 	= $file['result'];
		        	}
	    	}
	    	else{
	    		$profile_img 	= $this->input->post( 'old_profile_img' );    		
	    	}

	    	if($this->input->post( 'status') ==''){
	    		$status ='0';
	    	}
	    	else{
	    		$status ='1';
	    	}
	    	if($this->input->post( 'marriage_status' ) =='married' && !empty($this->input->post( 'doa' ))){
	    		$doa	= date('Y-m-d',strtotime(str_replace('/','-',$this->input->post("doa"))));
	    	}
	    	else{
	    		$doa	= '';
	    	}
	    	//echo "hzj".$doa;exit;
	    	$data = array(	
	    	        'title' 						=> $this->input->post( 'title' ),
					'first_name' 						=> $this->input->post( 'first_name' ),	
					'middle_name' 						=> $this->input->post( 'middle_name' ),	
					'last_name' 						=> $this->input->post( 'last_name' ),
					'country_code'						=>'91',
					'mobile' 							=> $this->input->post( 'mobile' ),
					'email' 							=> $this->input->post( 'email' ),	
					'gender' 							=> $this->input->post( 'gender' ),
					'marriage_status' 					=> $this->input->post( 'marriage_status' ),
					'dob' 								=> DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("dob")))),
					'doa' 								=> $doa,
					'profile_img'						=> $profile_img,
					'status' 							=> $status,
					'created_by' 						=> $this->session->userdata('user_data'),			
					'created_ts' 						=> date('Y-m-d H:i:s'),				
					);
	    	//pr($data);
					$condition	= array('member_id'=>$member_id);
		        	$result = $this->mcommon->update('master_member',$condition,$data);	        	
		        	if($result)
		        	{
		        		if($this->input->post( 'package_id' ) !=''){
		        			$pkg_condition		= array('member_id'=>$member_id,'status' =>'1');
			        		$package_data		= $this->mcommon->getRow('package_membership_mapping',$pkg_condition);
			        		//pr($package_data);
			        		if(!empty($package_data)){
			        			$update_data	= array('status'=> '0');
			        			$this->mcommon->update('package_membership_mapping',$pkg_condition,$update_data);
			        		}
			        		$joindata   = array('select' 		=>'package_price_mapping.package_type_id,package_type.package_type_name,package_price_mapping.price,master_package.package_name',
						                        'first_table'   =>'package_price_mapping',
							                    'second_table'  =>'package_type',
							                    'dependency1'   =>'package_price_mapping.package_type_id = package_type.package_type_id',
							                    'join_type1'    =>'inner',
							                    'third_table'  	=>'master_package',
							                    'dependency2'   =>'master_package.package_id = package_price_mapping.package_id',
							                    'join_type2'    =>'inner'	                    
					              				);				
							$condition 			= array('package_price_mapping.package_price_mapping_id'=>$this->input->post('package_type_id'));		
							$package_type_data	= $this->mcommon->joinQuery($joindata,$condition,'row','','');		        		
			        		
			        		//pr($package_type_data);
			        		if(!empty($package_type_data)){
			        			if($package_type_data['package_type_name'] =='Yearly'){
			        				$expiry_date 	= date('Y-m-d', strtotime(' +1 year'));
			        			}
			        			else{
			        				$expiry_date = date('Y-m-d', strtotime(' +1 month'));
			        			}
			        			$package_type_name  = $package_type_data['package_type_name'];
	        					$package_name  		= $package_type_data['package_name'];
	        					$package_price  	= $package_type_data['price'];
			        		}			        		
		        			$pck_array_data 	= array('package_id'		=> $this->input->post( 'package_id' ),
		        										'membership_id'		=> $this->input->post( 'membership_id' ),
		        										'package_price_id'	=> $this->input->post( 'package_type_id' ),
				        								'member_id' 		=> $member_id,
				        								'added_from' 		=> 'admin',
				        								'buy_on'			=> date('Y-m-d'),
				        								'expiry_date'		=> $expiry_date,
				        								'status'			=> '1'
				        							);
		        			$this->mcommon->insert('package_membership_mapping',$pck_array_data);
		        			if(!empty($this->input->post( 'package_type_id' ))){
		        				$package_price_mapping_data		= $this->mcommon->getRow('package_price_mapping',array('package_price_mapping_id' =>$this->input->post( 'package_type_id' )));
		        				if(!empty($package_price_mapping_data)){
		        					$package_price 	= $package_price_mapping_data['price'];
		        				}	        				
		        			}
		        			$pck_trans_array_data 	= array('transaction_id' 	=> $this->generateRandomString(),
		        										'package_id'		=> $this->input->post( 'package_id' ),
				        								'member_id' 		=> $member_id,
				        								'added_form' 		=> 'admin',
				        								'amount'			=> $package_price,
				        								'transaction_date'	=> date('Y-m-d'),
				        								'payment_status'	=> '1'
				        								);
		        			$this->mcommon->insert('package_membership_transaction',$pck_trans_array_data);
		        		}	
    			/****************** Send password to the member ****************************/
		        		if($this->input->post('membership_id') != $old_membership_id){

		        			$logo					= 	base_url('public/images/logo.png');
							$params['name']			=	$this->input->post( 'first_name' );
							$params['to']			=	$this->input->post( 'email' );
							//$params['to']			=	'sreelabiswas.kundu@met-technologies.com';
							$details 				=   "Membership Id: ".$this->input->post( 'membership_id' )."<br>"."Membership name: ".$package_name."<br>"."Membership type: ".$package_type_name."<br>"."Membership Price:(₹) ".$package_price;											
							$params['subject']		=   'Club Fenicia - Membership Confirmation Mail';															
							$mail_temp 				= 	file_get_contents('./global/mail/new_membership_template.html');
							$mail_temp				=	str_replace("{web_url}", base_url(), $mail_temp);
							$mail_temp				=	str_replace("{logo}", $logo, $mail_temp);
							$mail_temp				=	str_replace("{shop_name}", 'Club Fenicia', $mail_temp);	
							$mail_temp				=	str_replace("{name}", $params['name'], $mail_temp);
							$mail_temp				=	str_replace("{membership_name}", $package_name, $mail_temp);
							$mail_temp				=	str_replace("{details}", $details, $mail_temp);
							$mail_temp				=	str_replace("{current_year}", date('Y'), $mail_temp);						
							$params['message']		=	$mail_temp;
							$msg 					= 	registration_mail($params);

                                
							$message  = "Congratulations! You are now an active Member of Club Fenicia. Membership Details - "."\n";
			              	$message  .=   " ID: ".$this->input->post( 'membership_id' )."\n Name: ".$package_name."\n Type: ".$package_type_name;
							
			              	$this->smsSend($this->input->post('mobile'),$message);
			              	//echo $this->input->post('mobile');exit;
		        		}
			        		        		
		        		$this->session->set_flashdata('success_msg','User details updated successfully');
		        		redirect('admin/member');
		        	}
			$this->session->set_flashdata('error_msg','User details updated successfully');
			redirect('admin/member/edit/'.$member_id);
		}
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: ADD NEW member  
	*/
	public function add(){
		$result 				= array();
		$condition				= array('status' => '1','is_delete'=>'0');		
		$pkg_list				= $this->mcommon->getDetails('master_package',$condition);
		if(!empty($pkg_list)){
			$result['pkg_list']	= $pkg_list;
		}
		else{
			$result['pkg_list']	= '';
		}
		$pck_cond 			= array('status'=>'1','is_delete' =>'0');
		$result['package']	= $this->mcommon->getDetails('master_package',$pck_cond);
		$result['content'] 				= 'admin/member/add';
		$this->_load_view_member($result);
	}
	public function addMember(){
		//pr($_POST);
		$img 	='';
		$data =  array();
		$result =  array();
		$this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[master_member.email]',array('is_unique'=>'This %s already exists.'));
		$this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|is_unique[master_member.mobile]',array('is_unique'=>'This %s already exists.'));
		$this->form_validation->set_rules('gender', 'gender', 'trim|required');
		$this->form_validation->set_rules('dob', 'date of Birth', 'trim|required');
		
		if($this->form_validation->run()==FALSE){
			$pck_cond 			= array('status'=>'1','is_delete' =>'0');
			$result['package']	= $this->mcommon->getDetails('master_package',$pck_cond);
			$result['content'] 	= 'admin/member/add';
			$this->_load_view_member($result);
		
		}
		else{
			if(!empty($_FILES['profile_img']['name'])){
				$image_path = '/public/upload_image/profile_photo';
				$file 		= $this->imageupload->image_upload2($image_path,'profile_img');
				//pr($file);
				if($file['status']==0){
					$this->session->set_flashdata('error_msg',$file['result']);
					redirect('admin/member/add');
				}	
				else{
					$img = $file['result'];
	        	}
	    	}
	    	if($this->input->post( 'status') ==''){
	    		$status ='0';
	    	}
	    	else{
	    		$status ='1';
	    	}
	    	$password			= mt_rand();
	    	if($this->input->post( 'doa' ) !=''){
	    		$doa	= date('Y-m-d',strtotime(str_replace('/','-',$this->input->post("doa"))));
	    	}
	    	else{
	    		$doa	= '';
	    	}
        	$data = array(		
			'title' 						    => $this->input->post( 'title' ),			
			'first_name' 						=> $this->input->post( 'first_name' ),	
			'middle_name' 						=> $this->input->post( 'middle_name' ),	
			'last_name' 						=> $this->input->post( 'last_name' ),
			'country_code'						=>'91',	
			'mobile' 							=> $this->input->post( 'mobile' ),
			'password'							=> sha1($password),
			'original_password'					=> $password,
			'email' 							=> $this->input->post( 'email' ),	
			'gender' 							=> $this->input->post( 'gender' ),
			'marriage_status' 					=> $this->input->post( 'marriage_status' ),
			'dob' 								=> DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("dob")))),				
			'doa' 								=> $doa,
			'profile_img'						=> $img,
			'status' 							=> $status,
			'registration_type'					=> '2',
			'added_form'						=> 'admin',
			'login_status'						=> '1',
			'created_by' 						=> $this->session->userdata('user_data'),			
			'created_ts' 						=> date('Y-m-d H:i:s'),				
			);
        	$member_id = $this->mcommon->insert('master_member',$data);

        	if($member_id)
        	{
        		if($this->input->post( 'package_id' ) !=''){

	        		$joindata   = array('select' 		=>'package_price_mapping.package_type_id,package_type.package_type_name,package_price_mapping.price,master_package.package_name',
				                        'first_table'   =>'package_price_mapping',
					                    'second_table'  =>'package_type',
					                    'dependency1'   =>'package_price_mapping.package_type_id = package_type.package_type_id',
					                    'join_type1'    =>'inner',
					                    'third_table'  	=>'master_package',
					                    'dependency2'   =>'master_package.package_id = package_price_mapping.package_id',
					                    'join_type2'    =>'inner'		                    
			              				);				
					$condition 			= array('package_price_mapping.package_price_mapping_id'=>$this->input->post('package_type_id'));		
					$package_type_data	= $this->mcommon->joinQuery($joindata,$condition,'row','','');		        		
	        		
	        		//pr($package_type_data);
	        		if(!empty($package_type_data)){
	        			if($package_type_data['package_type_name'] =='Yearly'){
	        				$expiry_date 	= date('Y-m-d', strtotime(' +1 year'));
	        			}
	        			else{
	        				$expiry_date = date('Y-m-d', strtotime(' +1 month'));
	        			}
	        			$package_type_name  = $package_type_data['package_type_name'];
	        			$package_name  		= $package_type_data['package_name'];
	        			$package_price  	= $package_type_data['price'];
	        		}			        		
	    			$pck_array_data 	= array('package_id'		=> $this->input->post( 'package_id' ),
	    										'membership_id'		=> $this->input->post( 'membership_id' ),
	    										'package_price_id'	=> $this->input->post( 'package_type_id' ),
		        								'member_id' 		=> $member_id,
		        								'added_from' 		=> 'admin',
		        								'buy_on'			=> date('Y-m-d'),
		        								'expiry_date'		=> $expiry_date,
		        								'status'			=> '1'
		        							);
	    			$this->mcommon->insert('package_membership_mapping',$pck_array_data);
	    			if(!empty($this->input->post( 'package_type_id' ))){
	    				$package_price_mapping_data		= $this->mcommon->getRow('package_price_mapping',array('package_price_mapping_id' =>$this->input->post( 'package_type_id' )));
	    				if(!empty($package_price_mapping_data)){
	    					$package_price 	= $package_price_mapping_data['price'];
	    				}	        				
	    			}
	    			$pck_trans_array_data 	= array('transaction_id' 	=> $this->generateRandomString(),
	    										'package_id'		=> $this->input->post( 'package_id' ),
		        								'member_id' 		=> $member_id,
		        								'added_form' 		=> 'admin',
		        								'amount'			=> $package_price,
		        								'transaction_date'	=> date('Y-m-d'),
		        								'payment_status'	=> '1'
		        								);
	    			$this->mcommon->insert('package_membership_transaction',$pck_trans_array_data);
    			}
        		/****************** Send membership ID to the member ****************************/
        		$logo					= 	base_url('public/images/logo.png');
				$params['name']			=	$this->input->post( 'first_name' );
				$params['to']			=	$this->input->post( 'email' );	
				$params['password']		=	$password;	
				//$params['to']			=	'sreelabiswas.kundu@met-technologies.com';
				$details 				=   "username: ".$this->input->post( 'email' );
				if(!empty($this->input->post( 'membership_id' ))){
					$details 			.=   "Membership Id: ".$this->input->post( 'membership_id' )."<br>"."Membership name: ".$package_name."<br>"."Membership type: ".$package_type_name."<br>"."Membership Price:(₹) ".$package_price;
				}				
				$params['subject']		=   'Club Fenicia - Registration Successful Mail';															
				$mail_temp 				= 	file_get_contents('./global/mail/registration_template.html');
				$mail_temp				=	str_replace("{web_url}", base_url(), $mail_temp);
				$mail_temp				=	str_replace("{logo}", $logo, $mail_temp);
				$mail_temp				=	str_replace("{shop_name}", 'Club Fenicia', $mail_temp);	
				$mail_temp				=	str_replace("{name}", $params['name'], $mail_temp);
				$mail_temp				=	str_replace("{details}", $details, $mail_temp);
				$mail_temp				=	str_replace("{current_year}", date('Y'), $mail_temp);						
				$params['message']		=	$mail_temp;
				$msg 					= 	registration_mail($params);


				$message  = "Congratulation! You become a active club menber of Finicia. Your details are - ";
              	$message .=  "username: ".$this->input->post( 'email' );
              	if(!empty($this->input->post( 'membership_id' ))){
					$message .=   "\n ID: ".$this->input->post( 'membership_id' )."\n Name: ".$package_name."\n Type: ".$package_type_name."\n Price: ".$package_price;
				}
              	$this->smsSend($this->input->post( 'mobile' ),$message);
        		$this->session->set_flashdata('success_msg','New User added successfully');
        		redirect('admin/member');
        	}
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
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: DELETE member Permanently   
	*/
	private function _load_view_member($data) {
		   // FUNCTION WRIITTEN IN COMMON HELPER
		$this->load->view('admin/layouts/index', $data);
		
	}
	public function DeleteMember($member_id){

		$response				= array();
		$return_response		= '';		
		$tbl_column_name		= 'member_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'master_member';
		$return_response		= getStatusCahnge($member_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','User successfully deleted');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
		}				
		redirect('admin/member');
	}	
	
	public function  changeStatus()
	{
		$response				= array();
		$return_response		= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'member_id';
		$status     	 		= $this->input->post('change_status');
		$chng_status_colm		= 'status';
		$table 					= 'master_member';
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			echo 1;exit;
		}				
		else{
			echo 0 ;exit;
		}
	}
	public function ajaxGetPackageType(){
		$response				= array();
		//$return_response		= '';
		$package_id				= $this->input->post('package_id');
		$response				= $this->mmember->get_package_type($package_id);
		echo json_encode($response);exit;
	}
	public function uniqueMembershipId(){
		//pr($_POST);
		
		$membership_id	= $this->input->post('membership_id');
		//$member_id		= $this->input->post('member_id');
		$response_data	= $this->mcommon->getRow('package_membership_mapping', array('membership_id' =>$membership_id));
		if(!empty($response_data)){
			echo 1;exit;
		}
		else{
			echo 0;exit;
		}
	}
	public function editUniqueMembershipId(){
		//pr($_POST);
		
		$membership_id	= $this->input->post('membership_id');
		$member_id		= $this->input->post('member_id');
		$response_data	= $this->mcommon->getRow('package_membership_mapping', array('membership_id' =>$membership_id,'member_id !=' =>$member_id));
		if(!empty($response_data)){
			echo 1;exit;
		}
		else{
			echo 0;exit;
		}
	}
}