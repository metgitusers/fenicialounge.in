<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('commission/muser');
		$this->load->model('admin/mcommon');
	}
	public function index() { 
		
		$this->_load_login_view();	
			
	}	
	public function _load_login_view() {
		$data = array();
		$data['content'] = 'commission/login';
		$this->load->view('commission/layouts/login', $data);
	}

	public function submit_login_form()
	{
		
		$data 		= array();
		$result 	= array();
		$email 		= $this->input->post( 'email' );
		$password 	= $this->input->post( 'password' );
		$data = array(
			'email' => $this->input->post( 'email' ),
			'password' => sha1($this->input->post( 'password' )),
		);

		$result = $this->muser->submit_login_form($data);
		//pr($result) ;
        //echo $result['user_id'];die;
		if($result)
		{
			
		 	$this->session->set_userdata('user_details', $result);
		 	$this->session->set_userdata('user_data', $result['first_name']);
		 	$this->session->set_userdata('user_id', $result['user_id']);
		 	redirect('commission/ReservationCommission');
		}
		else
		{
			$this->session->set_flashdata('msg','<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Please check your credentials</div>');
			redirect('commission/login');
		}
		
	}
	public function forget_password(){
		//echo "sdfksjflk";exit;
		//pr($_POST,0);
	    if(!empty($_POST['email'])){
	        	 $email  = $this->input->post('email');
	             $this->form_validation->set_rules('email', 'Email', 'trim|required');           
	        if($this->form_validation->run() != FALSE){    	     
		         $condition = array('user.email'=>$email);
		         $query = $this->mcommon->getRow('user',$condition);
	     		//pr($query);
		        if(!empty($query)){
		    
		            $recovery_key			= base64_encode(rand());
		            $data['recovery_key']	= $recovery_key;
		            $condition 				= array('user.email'=>$email);
		            $update_query 			= $this->mcommon->update('user',$condition,$data);
		            $forget_link 			= base_url()."admin/reset-password/" . $recovery_key;
  					$condition 				= array('user.email'=>$email);
	      			$admin_data 			= $this->mcommon->getRow('user',$condition);
		     		//pr($admin_data);
		            if(!empty($admin_data)){
				    /*-----------------------------------SEND PASSWORD MAIL--------------------------------------*/
				  
					        $regmail['name']        =    $admin_data['first_name'];  
					        //$regmail['to']          =    'sreelabiswas.kundu@met-technologies.com';
					        $regmail['to']          =    $email;
					        $regmail['subject']     =   'FORGOT PASSWORD MAIL'; 
					        $logo                   =    base_url('public/admin_assets/app-assets/img/logo.png');
					        $mail_temp              =    file_get_contents('./global/mail/forgotpassword_template.html');
					        $mail_temp              =    str_replace("{web_url}", base_url(), $mail_temp); 
					        $mail_temp              =    str_replace("{logo}", $logo, $mail_temp);   
					        $mail_temp              =    str_replace("{link}", $forget_link, $mail_temp);   
					        $mail_temp              =    str_replace("{name}", $regmail['name'], $mail_temp);       
					        $mail_temp              =    str_replace("{current_year}", date('Y'), $mail_temp);           
					        $regmail['message']     =    $mail_temp;
					        $msg                    =    registration_mail($regmail);

				        // -----------------------------------SEND PASSWORD MAIL--------------------------------------
					   	$this ->session->set_flashdata('error_msg','');     	
					    $this ->session->set_flashdata('success_msg','Password Recovery mail has been send.Please check your inbox');
		          		 
			        }
		        }else {
		        	$this ->session->set_flashdata('success_msg','');
		        	$this ->session->set_flashdata('error_msg','This is not the admin email id.');
		        		
		        }
	        }else{
	        	$this ->session->set_flashdata('success_msg','');
	         	$this ->session->set_flashdata('error_msg','Give a valid email id.');
		        
	        }
	    }
	    else{
	    }
	    $data['content'] = 'admin/forgotpassword';
		$this->load->view('admin/layouts/login', $data);
	}   
	public function reset_newpswd(){
	    if($this->input->post()){
	     //pr($_POST);
	      	$this->form_validation->set_rules('password', 'New Password', 'trim|required|min_length[6]');
	      	$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
		    if ($this->form_validation->run() == FALSE){
		      //echo"validation error";die();
			      	$this->session->set_flashdata('error_msg',  "Error!");  
			      	$data['title']='Punjabmotor - Reset Password'; 
			        $data['content'] = 'admin/resetpassword';
			        $this->load->view('admin/resetpassword',$data);
		      }else{
		     
				$password1 = $this->input->post('password');
				$password2 = $this->input->post('confirm_password');
				$code =      $this->input->post('code');
			     
		      	$condition1 = array('user.recovery_key'=>$code);
			     
		      	$query1 = $this->mcommon->getRow('user',$condition1);
			      
		      	if($query1){
					$data['password']	   = sha1($password1);
					$data['recovery_key']= '';

					$condition = array('user.recovery_key'=>$this->input->post('code'));
					$reset = $this->mcommon->update('user',$condition, $data); 
				      
					if($reset){ 
							$this ->session->set_flashdata('error_msg','');
							$this->session->set_flashdata('success_msg',  "Password has been changed successfully ! Please Login");        
							$this->_load_login_view();	  
					} else{
							$this ->session->set_flashdata('success_msg','');
							$this->session->set_flashdata('error_msg', "Error!");
							$this->_load_login_view();	    
		      		}
		      }else{
		   			$this ->session->set_flashdata('success_msg','');
			    	$this->session->set_flashdata('error_msg', "Recovery Key Already used!");
			    	$this->_load_login_view();	  

		      }
		   
		    }
		}
	  	else{
	  		$data['content'] = 'admin/resetpassword';
	       	$this->load->view('admin/layouts/login', $data);
	  	}
	}
}
