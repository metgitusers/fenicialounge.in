<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->admin=$this->session->userdata('admin');
		$this->load->model('admin/Msubscription');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}
	
	public function index() { 
		//echo $this->session->userdata('email');die;
		$result 						= array();
		$result['content'] 				= 'admin/subscription/list';
		$result['package_active_list'] 	= $this->Msubscription->get_package_list('1');
		$result['package_inactive_list'] = $this->Msubscription->get_package_list('0');
		//pr($result);		
		
		$this->_load_view_member($result);		
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: Edit member  
	*/
	public function edit($package_id){
		$result 			= array();
		$condition 			= array('package_id'=>$package_id);
		$result['package']	= $this->mcommon->getRow('membership_package_master',$condition);
		//pr($result['member']);
		$result['content'] 	= 'admin/Subscription/add';
		if(empty($result['package'])){
			redirect('admin/Subscription');
		}else{
			$this->_load_view_member($result);
		}
	}
	public function UpdateMember($package_id){
		//pr($_POST);
		$data		= array();
		
    	$data = array(
			'package_name' 						=> $this->input->post( 'package_name' ),			
			'monthly_price' 					=> $this->input->post( 'month_price' ),			
			'yearly_price' 						=> $this->input->post( 'yearly_price' ),
			'unit_price' 						=> $this->input->post( 'unit_price' ),
			'status' 							=> '1',			
			'update_on' 						=> date('Y-m-d H:i:s'),				
			);
		$condition	= array('package_id'=>$package_id);
    	$result = $this->mcommon->update('membership_package_master',$condition,$data);

    	if($result)
    	{
    		$this->session->set_flashdata('success_msg','Subscription updated successfully');
    		redirect('admin/Subscription');
    	}
		$this->session->set_flashdata('error_msg','Subscription updated successfully');
		redirect('admin/Subscription/edit/'.$package_id);
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: ADD NEW member  
	*/
	public function add(){
		$result 						= array();
		$result['content'] 				= 'admin/subscription/add';
		$this->_load_view_member($result);
	}
	public function addMember(){
		$data =  array();
		$result =  array();
		$this->form_validation->set_rules('package_name', 'package name', 'trim|required|is_unique[membership_package_master.package_name]',array('is_unique'=>'This %s already exists.'));
		
		if($this->form_validation->run()==FALSE){
			
			$this->_load_view_member($data);
		
		}
		else{
			
        	$data = array(
			'package_name' 						=> $this->input->post( 'package_name' ),			
			'monthly_price' 					=> $this->input->post( 'month_price' ),			
			'yearly_price' 						=> $this->input->post( 'yearly_price' ),
			'unit_price' 						=> $this->input->post( 'unit_price' ),
			'status' 							=> '1',			
			'created_on' 						=> date('Y-m-d H:i:s'),				
			);
        	$result = $this->mcommon->insert('membership_package_master',$data);

        	if($result)
        	{
        		$this->session->set_flashdata('error_msg','');
        		$this->session->set_flashdata('success_msg','New subscription added successfully');
        		redirect('admin/Subscription');
        	}
	        else{
	        	$this->session->set_flashdata('success_msg','');
	        	$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
        		redirect('admin/Subscription/add');
	        }	
    	}
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
	public function DeletePackage($package_id){
		$response				= array();
		$return_response		= '';		
		$tbl_column_name		= 'package_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'membership_package_master';
		$return_response		= getStatusCahnge($package_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','Subscription successfully deleted');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
		}				
		redirect('admin/Subscription');
	}
	public function changeStatus()
	{
		$response				= array();
		$return_response		= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'package_id';
		$status     	 		= $this->input->post('change_status');
		$chng_status_colm		= 'status';
		$table 					= 'membership_package_master';
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			echo 1;exit;
		}				
		else{
			echo 0 ;exit;
		}
	}
	public function PackageMemberList() { 
		//echo $this->session->userdata('email');die;
		$result 						= array();
		$result['content'] 				= 'admin/subscription/package_mem_list';
		$pck_cond 			= array('status' => '1','is_delete'=>'0');
		$pkg_list			= $this->mcommon->getDetails('membership_package_master',$pck_cond);
		if(!empty($pkg_list)){
			$result['pkg_list']	= $pkg_list;
		}
		else{
			$result['pkg_list']	= '';
		}
		$result['premium_mem_list'] 	= $this->Msubscription->get_pck_member_list('2');
		$result['normal_mem_list'] 		= $this->Msubscription->get_pck_member_list('1');
		//pr($result);		
		
		$this->_load_view_member($result);		
	}
}