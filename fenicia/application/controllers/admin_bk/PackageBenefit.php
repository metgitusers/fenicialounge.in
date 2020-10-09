<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PackageBenefit extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->admin=$this->session->userdata('admin');
		$this->load->model('admin/Mpackage');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}	
	public function index() { 
		//echo $this->session->userdata('email');die;
		$result 									= array();
		$result['content'] 							= 'admin/package_benefite/list';
		$result['package_benefit_active_list'] 		= $this->Mpackage->all_package_benefit_list('1');
		$result['package_benefit_inactive_list'] 	= $this->Mpackage->all_package_benefit_list('0');
		//pr($result);		
		
		$this->_load_view($result);		
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: Edit member  
	*/
	public function edit($package_benefit_id){
		$result 			= array();
		$condition 			= array('package_benefit_id'=>$package_benefit_id);
		$result['pck_benefit_data']	= $this->mcommon->getRow('package_benefits',$condition);
		//pr($result['pck_benefit_data']);
		$result['content'] 	= 'admin/package_benefite/add_edit_benefit';
		if(empty($result['pck_benefit_data'])){
			redirect('admin/PackageBenefit');
		}else{
			$this->_load_view($result);
		}
	}
	public function UpdateBenefit($package_benefit_id){
		//pr($_POST);
		$data		= array();		
    	if($this->input->post()){	
							
			$update_data['benefit_name']		= $this->input->post('benefit_name');
			$update_data['benefit_description']	= $this->input->post('benefit_description');
			$update_data['modified_on']			= date('Y-m-d');
			$benifit_cond						= array('package_benefit_id' => $package_benefit_id);
			$update_id	=	$this->mcommon->update('package_benefits',$benifit_cond,$update_data);
			
			if($update_id){
				$this->session->set_flashdata('success_msg','A package benefit updated successfully');
				redirect(base_url('admin/PackageBenefit'));
			}
			else{
					
				$this->session->set_flashdata('error_msg','Oops!Something went wrong...');
				redirect(base_url('admin/PackageBenefit'));				
			}
		}
		else{

			$this->_load_view();
		}
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: ADD NEW member  
	*/
	public function add(){
		$result 						= array();
		$result['pck_benefit_data']		= '';
		$result['content'] 			 	= 'admin/package_benefite/add_edit_benefit';		
		$this->_load_view($result);
	}
	public function Save(){
		
		$result 					= array();		
		if($this->input->post()){			
			$this->form_validation->set_rules('benefit_name','Benefit Name','required|is_unique[package_benefits.benefit_name]',array('is_unique'=>'This %s already exists.'));
			$this->form_validation->set_rules('benefit_description','Benefit Description','trim|required');
			if($this->form_validation->run()==FALSE){
				$result['pck_benefit_data']	= '';
				$result['content'] 			= 'admin/package_benefite/add_edit_benefit';
				$this->_load_view($result);
			
			}else{
				//echo '<pre>'; print_r($this->input->post());die;
					
				$insert_data['benefit_name']		= $this->input->post('benefit_name');
				$insert_data['benefit_description']	= $this->input->post('benefit_description');
				$insert_data['created_on']			= date('Y-m-d');
				$insert_data['status'] 				= 1;
				$insert_id	=	$this->mcommon->insert('package_benefits',$insert_data);
				
				if($insert_id){
					$this->session->set_flashdata('success_msg','A new package benefit added successfully');
					redirect(base_url('admin/PackageBenefit'));

				}
				else{
						
					$this->session->set_flashdata('error_msg','Oops!Something went wrong...');
					redirect(base_url('admin/PackageBenefit'));				
				}	
			}
		}
		else{

			$this->_load_view();
		}
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
	public function DeleteBenefit($package_benefit_id){
		$response				= array();
		$return_response		= '';		
		$tbl_column_name		= 'package_benefit_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'package_benefits';
		$return_response		= getStatusCahnge($package_benefit_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','package benefit successfully deleted');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
		}				
		redirect('admin/PackageBenefit');
	}
	public function changeStatus()
	{
		$response				= array();
		$return_response		= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'package_benefit_id';
		$status     	 		= $this->input->post('change_status');
		$chng_status_colm		= 'status';
		$table 					= 'package_benefits';
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
		$result['content'] 				= 'admin/package/package_mem_list';
		$pck_cond 			= array('status' => '1','is_delete'=>'0');
		$pkg_list			= $this->mcommon->getDetails('membership_package_master',$pck_cond);
		if(!empty($pkg_list)){
			$result['pkg_list']	= $pkg_list;
		}
		else{
			$result['pkg_list']	= '';
		}
		$result['premium_mem_list'] 	= $this->Mpackage->get_pck_member_list('2');
		$result['normal_mem_list'] 		= $this->Mpackage->get_pck_member_list('1');
		//pr($result);		
		
		$this->_load_view_member($result);		
	}
}