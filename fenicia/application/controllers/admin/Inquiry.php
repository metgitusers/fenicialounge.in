<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Inquiry extends MY_Controller {
	public function __construct() {
		parent::__construct();
		//$this->redirect_guest();
		$this->admin=$this->session->userdata('admin');
		$this->load->library('imageupload');	
		$this->load->model('admin/mzone');
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
	public function index() { 
		$result = array();
		$result['inquiry_list'] 	= $this->mcommon->getInquiryDetails();
		//pr($result);	
		$result['content']			= 'admin/inquiry_list';
		$this->_load_view($result);
				
	}
	public function changeStatus()
	{
		$response				= array();
		$return_response		= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'inquiry_id';
		$status     	 		= $this->input->post('change_status');
		$chng_status_colm		= 'status';
		$table 					= 'inquiry';
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			if($status == '1'){
				$log_data = array('action' 	=> 'Edit',
								  'statement' 	=> "Inquiry id - '".$id."' pending status change to answered",
								  'action_by'	=> $this->session->userdata('user_data'),
								  'IP'			=> getClientIP(),
								  'id'          => $id,
					  	  		  'type'        =>"Inquiry",
								  'status'		=> '1'
							);
				
			}
			else{
				$log_data = array('action' 	=> 'Edit',
							  'statement' 	=> "Inquiry id - '".$id."' answered status change to pending",
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $id,
					  	  	  'type'        =>"Inquiry",
							  'status'		=> '1'
							);
			}
			$this->mcommon->insert('log',$log_data);
			echo 1;exit;
		}				
		else{
			echo 0 ;exit;
		}
	}
}