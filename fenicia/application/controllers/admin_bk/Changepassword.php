<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Changepassword extends MY_Controller {


	public function __construct() {		
		parent::__construct();
		//$this->redirect_guest();
		
		$this->load->model('admin/mchangepassword');	
		
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
				
	}

	private function _load_view($data) {
		$this->load->view('admin/layouts/index',$data);
	}	
	


	/*
	author: soma
	purpose: change password
	date: 01-10-2019
	*/
	

	public function index()
	{
		$user_id=$this->session->userdata('user_data');
		
		$data = array();    
	     
	    $data['content']='admin/changepassword';   
	   
	    $this->load->view('admin/layouts/index', $data);

	}


    public function changeuserpasswd(){
       
     	if($this->input->post()){
            $this->form_validation->set_rules('oldpassw', 'Old Password', 'trim|required|min_length[6]');
            $this->form_validation->set_rules('newpassw', 'New Password', 'trim|required|min_length[6]');
            $this->form_validation->set_rules('confpassw', 'Confirm Password', 'required|matches[newpassw]');
           
      		if($this->form_validation->run() == FALSE){

            $this->index();
      		}else{
	            $user_id=$this->session->userdata('user_data');
      			$condition=array('user_id'=>$user_id);
      				
	            $data['users'] = $this->mcommon->getRow('user',$condition);
	          //  print_r($data['users']);die;
	            
	             if(sha1($this->input->post('oldpassw')) == $data['users']['password']) {
	            
	              
	                $condition=array('user_id'=>$user_id);	
				  
	                    $data=array(
	                    'password'=>sha1($this->input->post( 'newpassw' )),
	                    'updated_by'=>$this->session->userdata('user_data'),
						'updated_date'=>date('Y-m-d h:i:s'),
	                     );
	                $this->mcommon->update('user',$condition,$data);

	                $this->session->set_flashdata('success_msg','Password has been changed successfully');
	                redirect('admin/changepassword','refersh'); 
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