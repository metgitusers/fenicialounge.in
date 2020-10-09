<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Changepassword extends MY_Controller {


	public function __construct() {		
		parent::__construct();
		//$this->redirect_guest();
		
		$this->load->model('commission/mchangepassword');	
		
		if($this->session->userdata('user_id') == '')
		{
			redirect('commission/login');
			die();
		}
				
	}

	public function index()
	{
		$user_id=$this->session->userdata('user_id');
		
		$data = array();
	   
	   	$this->load->view('commission/layouts/reservation_commission_header');			
		$this->load->view('commission/changepassword');
		$this->load->view('commission/layouts/footer');

	}


    public function changeuserpasswd(){
       
     	if($this->input->post()){
            $this->form_validation->set_rules('oldpassw', 'Old Password', 'trim|required|min_length[6]');
            $this->form_validation->set_rules('newpassw', 'New Password', 'trim|required|min_length[6]');
            $this->form_validation->set_rules('confpassw', 'Confirm Password', 'required|matches[newpassw]');
           
      		if($this->form_validation->run() == FALSE){

            $this->index();
      		}else{
	            $user_id=$this->session->userdata('user_id');
      			$condition=array('user_id'=>$user_id);
      				
	            $data['users'] = $this->mcommon->getRow('commission_user',$condition);
	          //  print_r($data['users']);die;
	            
	             if(sha1($this->input->post('oldpassw')) == $data['users']['password']) {
	            
	              
	                $condition=array('user_id'=>$user_id);	
				  
	                    $data=array(
	                    'password'=>sha1($this->input->post( 'newpassw' )),
	                    'original_password'=>$this->input->post( 'newpassw' ),
	                    'updated_by'=>$this->session->userdata('user_data'),
						'updated_date'=>date('Y-m-d h:i:s'),
	                     );
	                $this->mcommon->update('commission_user',$condition,$data);

	                /*$log_data = array('action' 		=> 'Edit',
									  'statement' 	=> "Own Password changed",
									  'action_by'	=> $this->session->userdata('user_data'),
									  'IP'			=> getClientIP(),
									  'id'          => $user_id,
								  	  'type'        =>"Change Password",
									  'status'		=> '1'
									);
					$this->mcommon->insert('log',$log_data);*/
	                $this->session->set_flashdata('success_msg','Password has been changed successfully');
	                redirect('commission/changepassword','refersh'); 
	            } else{
	                $this->session->set_flashdata('error_msg','Old password is not correct');
	              
	                $this->index();	 
	            }
            } 
        }else{
            $this->index();	
        }
    }
}