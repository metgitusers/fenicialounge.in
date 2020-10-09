<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PackageVoucher extends MY_Controller {

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
		$result['content'] 							= 'admin/package_voucher/list';
		$result['package_voucher_active_list'] 		= $this->Mpackage->all_package_voucher_list('1');
		$result['package_voucher_inactive_list'] 	= $this->Mpackage->all_package_voucher_list('0');
		//pr($result);		
		
		$this->_load_view($result);		
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: Edit member  
	*/
	public function edit($package_voucher_id){
		$result 			= array();
		$condition 			= array('package_voucher_id'=>$package_voucher_id);
		$result['pck_voucher_data']	= $this->mcommon->getRow('package_vouchers',$condition);
		//pr($result['pck_benefit_data']);
		$result['content'] 	= 'admin/package_voucher/add_edit_voucher';
		if(empty($result['pck_voucher_data'])){
			redirect('admin/PackageVoucher');
		}else{
			$this->_load_view($result);
		}
	}
	public function UpdateVoucher($package_voucher_id){
		//pr($_POST);
		$data		= array();		
    	if($this->input->post()){			
							
			$update_data['voucher_name']		= $this->input->post('voucher_name');
			$update_data['voucher_description']	= $this->input->post('voucher_description');
			$update_data['modified_on']			= date('Y-m-d');
			$voucher_cond						= array('package_voucher_id' => $package_voucher_id);
			$update_id							= $this->mcommon->update('package_vouchers',$voucher_cond,$update_data);
			
			if($update_id){
				$log_data = array('action' 		=> 'Edit',
								  'statement' 	=> "Edited details of package voucher named -'".$this->input->post('voucher_name')."'",
								  'action_by'	=> $this->session->userdata('user_data'),
								  'IP'			=> getClientIP(),
								  'id'          => $package_voucher_id,
					  	  		  'type'        =>"Package Voucher",
								  'status'		=> '1'
								);
				$this->mcommon->insert('log',$log_data);
				$this->session->set_flashdata('success_msg','A package voucher updated successfully');
				redirect(base_url('admin/PackageVoucher'));
			}
			else{
					
				$this->session->set_flashdata('error_msg','Oops!Something went wrong...');
				redirect(base_url('admin/PackageVoucher'));				
			}
		}
		else{

			$this->_load_view($data);
		}
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: ADD NEW member  
	*/
	public function add(){
		$result 						= array();
		$result['pck_voucher_data']		= '';
		$result['content'] 			 	= 'admin/package_voucher/add_edit_voucher';		
		$this->_load_view($result);
	}
	public function Save(){
		//pr($_POST);
		$result 						= array();
		$result['content'] 				= 'admin/package_voucher/add_edit_voucher';
		if($this->input->post()){			
			$this->form_validation->set_rules('voucher_name','Voucher Name','required|is_unique[package_vouchers.voucher_name]',array('is_unique'=>'This %s already exists.'));
			$this->form_validation->set_rules('voucher_description','Voucher Description','required');
			if($this->form_validation->run()==FALSE){
				
				$this->_load_view($result);
			
			}else{
				//echo '<pre>'; print_r($this->input->post());die;
					
				$insert_data['voucher_name']		= $this->input->post('voucher_name');
				$insert_data['voucher_description']	= $this->input->post('voucher_description');
				$insert_data['created_on']			= date('Y-m-d');
				$insert_data['status'] 				= 1;
				$insert_id	=	$this->mcommon->insert('package_vouchers',$insert_data);
				
				if($insert_id){
					$log_data = array('action' 		=> 'Add',
									  'statement' 	=> "Added a new package voucher named -'".$this->input->post('voucher_name')."'",
									  'action_by'	=> $this->session->userdata('user_data'),
									  'IP'			=> getClientIP(),
									  'id'          => $insert_id,
					  	  		  	  'type'        => "Package Voucher",
									  'status'		=> '1'
									);
					$this->mcommon->insert('log',$log_data);
					$this->session->set_flashdata('success_msg','A new package voucher added successfully');
					redirect(base_url('admin/PackageVoucher'));

				}
				else{
						
					$this->session->set_flashdata('error_msg','Oops!Something went wrong...');
					redirect(base_url('admin/PackageVoucher'));				
				}	
			}
		}
		else{

			$this->_load_view($result);
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
	public function DeleteVoucher($package_voucher_id){

		$response				= array();
		$return_response		= '';
		$voucher_name   		= '';
		$tbl_column_name		= 'package_voucher_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'package_vouchers';
		$return_response		= getStatusCahnge($package_voucher_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$voucher_data 		= $this->mcommon->getRow('package_vouchers', array('package_voucher_id' => $package_voucher_id));
			if(!empty($voucher_data)){
				$voucher_name   = $voucher_data['voucher_name'];
			}
			$log_data = array('action' 		=> 'Delete',
							  'statement' 	=> "Deleted a package voucher named - '".$voucher_name."'",
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $package_voucher_id,
					  	  	  'type'        => "Package Voucher",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','Voucher successfully deleted');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
		}				
		redirect('admin/PackageVoucher');
	}
	public function changeStatus()
	{
		$response				= array();
		$return_response		= '';
		$voucher_name   		= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'package_voucher_id';
		$status     	 		= $this->input->post('change_status');
		$chng_status_colm		= 'status';
		$table 					= 'package_vouchers';
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$voucher_data 		= $this->mcommon->getRow('package_vouchers', array('package_voucher_id' => $id));
			if(!empty($voucher_data)){
				$voucher_name   = $voucher_data['voucher_name'];
			}
			if($status == '0'){
				$changed_status 	= 'inactive';
			}
			else{
				$changed_status 	= 'active';
			}
			$log_data = array('action' 		=> 'Edit',
							  'statement' 	=> "Made package voucher named - '".$voucher_name."' ".$changed_status,
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $id,
					  	  	  'type'        => "Package Voucher",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
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