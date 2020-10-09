<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Role extends MY_Controller {
	public function __construct() {
		parent::__construct();
		//$this->redirect_guest();
		$this->admin=$this->session->userdata('admin');		
		$this->load->model('admin/mrole');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}


	// Default load function for header and footer inculded
	private function _load_view($data) {
		$this->load->view('admin/layouts/index',$data);
	}	


	/*
	author: sreela
	purpose: Role Listing
	date: 6-12-2019
	*/
	public function index() { 
		$result = array();
		$result['active_role_list'] 	= $this->mrole->get_role_list('1');		
		$result['inactive_role_list'] 	= $this->mrole->get_role_list('0');
		//pr($result);	
		$result['content']='admin/role/list';
		$this->_load_view($result);
				
	}


	/*
	author: sreela
	purpose: add Role view
	date: 6-12-2019
	*/
	public function add()
	{	
		$result = array();			
		$result['content']='admin/role/add';
		$this->_load_view($result);
	}


	/*
	author: sreela
	purpose: add Role
	date: 6-12-2019
	*/
	public function addRole()
	{
		$data = array();
		$result =  array();
		$result['content']='admin/role/add';		
		$this->form_validation->set_rules('role_name', 'role name', 'trim|required|is_unique[master_role.role_name]',array('is_unique'=>'This %s already exists.'));			
		if($this->form_validation->run()==FALSE){
			
			$this->_load_view($result);
		
		}else{
			if($this->input->post( 'status') ==''){
	    		$status ='0';
	    	}
	    	else{
	    		$status ='1';
	    	}
			$data = array(
				'role_name' 	=> $this->input->post( 'role_name' ),
				'created_by' 	=> $this->session->userdata('user_data'),
				'status'		=> $status,
				'created_on' 	=> date('Y-m-d H:i:s')
			);

			$result = $this->mcommon->insert('master_role',$data);
			if($result)
			{
				$log_data = array('action' 	=> 'Add',
							  'statement' 	=> "Added a new role named - '".$this->input->post( 'role_name' )."'",
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $result,
					  	  	  'type'        =>"Role",
							  'status'		=> '1'
							);
				$this->mcommon->insert('log',$log_data);
				$this->session->set_flashdata('error_msg','');
				$this->session->set_flashdata('success_msg','Role added successfully');
				//$this->session->set_flashdata('msg','<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Your credentials are successfully updated</div>');			
				redirect('admin/role');
			}
			else{
				$this->session->set_flashdata('success_msg','');
				$this->session->set_flashdata('error_msg','Opps!Sorry try again.');
				//$this->session->set_flashdata('msg','<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Your credentials are successfully updated</div>');			
				redirect('admin/role/add');
			}
		}
	}

	/*
	author: sreela
	purpose: Edit role view
	date: 6-12-2019
	*/
	public function edit($role_id)
	{
		$result = array();		
		$result['role'] = $this->mcommon->getRow('master_role',array('role_id'=>$role_id));
		$result['content']='admin/role/add';
		$this->_load_view($result);

	}

	/*
	author: sreela
	purpose: Update role view
	date: 6-12-2019
	*/
	public function updateRole($role_id)
	{
		$result = array();
		if($this->input->post( 'status') ==''){
    		$status ='0';
    	}
    	else{
    		$status ='1';
    	}
		$updatedata = array(
			'role_name' 	=> $this->input->post( 'role_name' ),
			'updated_by' 	=> $this->session->userdata('user_data'),
			'status'		=> $status,
			'updated_on' 	=> date('Y-m-d H:i:s')
		);
		$condition	= array('role_id'=>$role_id);
		$result = $this->mcommon->update('master_role',$condition,$updatedata);
		if($result)
		{
			$log_data = array('action' 		=> 'Edit',
							  'statement' 	=> "Edited details of the role named -'".$this->input->post( 'role_name' )."'",
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $role_id,
					  	  	  'type'        =>"Role",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','Role details updated successfully');
			//$this->session->set_flashdata('msg','<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Your credentials are successfully updated</div>');			
			redirect('admin/role');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opps!Sorry try again.');
			//$this->session->set_flashdata('msg','<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Your credentials are successfully updated</div>');			
			redirect('admin/role/edit/'.$role_id);
		}
	}
	public function deleteRole($role_id){
		$response				= array();
		$return_response		= '';		
		$tbl_column_name		= 'role_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'master_role';
		$return_response		= getStatusCahnge($role_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$log_data = array('action' 		=> 'Delete',
							  'statement' 	=> 'Deleted a role',
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $role_id,
					  	  	  'type'        =>"Role",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','Role details successfully deleted');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
		}				
		redirect('admin/role');
	}	
	
	public function changeStatus()
	{
		$response				= array();
		$return_response		= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'role_id';
		$status     	 		= $this->input->post('change_status');
		$chng_status_colm		= 'status';
		$table 					= 'master_role';
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			if($status == '0'){
				$changed_status 	= 'inactive';
			}
			else{
				$changed_status 	= 'active';
			}
			$role_data 			= $this->mcommon->getRow('master_role',array('role_id'=>$id));
		//pr($event_data);
			if(!empty($role_data)){
				$role_name = $role_data['role_name'];
			}
			$log_data = array('action' 		=> 'Edit',
							  'statement' 	=> "Made role '".$role_name."' ".$changed_status,
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $id,
					  	  	  'type'        =>"Role",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
			echo 1;exit;
		}				
		else{
			echo 0 ;exit;
		}
	}



}