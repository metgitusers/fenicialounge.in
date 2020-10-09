<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Requestforphotograph extends MY_Controller {
	public function __construct() {
		parent::__construct();
		//$this->redirect_guest();
		$this->admin=$this->session->userdata('admin');
		$this->load->library('imageupload');	
		$this->load->model('admin/mrequestforphotograph');
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
		$result['request_for_photograph_list'] 	= $this->mrequestforphotograph->get_request_for_photograph_list('1');
		//pr($result);	
		$result['content']='admin/request_for_photograph/list';
		$this->_load_view($result);
				
	}

	public function changeStatus()
	{
		$response				= array();
		$return_response		= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'request_for_photograph_id';
		$status     	 		= $this->input->post('change_status');
		$chng_status_colm		= 'status';
		$table 					= 'request_for_photograph';
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			if($status == '0'){
				$changed_status 	= 'inactive';
			}
			else{
				$changed_status 	= 'active';
			}
			$log_data = array('action' 		=> 'Edit',
							  'statement' 	=> "Made 'Request For Photograph' status change to sent",
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $id,
					  	  	  'type'        =>"Request for photograph",
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