<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Zone extends MY_Controller {
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


	/*
	author: sreela
	purpose: Role Listing
	date: 6-12-2019
	*/
	public function index() { 
		$result = array();
		$result['active_zone_list'] 	= $this->mzone->get_zone_list('1');		
		$result['inactive_zone_list'] 	= $this->mzone->get_zone_list('0');
		//pr($result);	
		$result['content']='admin/zone/list';
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
		$result['content']='admin/zone/add';
		$this->_load_view($result);
	}


	/*
	author: sreela
	purpose: add zone
	date: 6-12-2019
	*/
	public function addZone()
	{
		$data 	= array();
		$result =  array();
		$img 	= '';
		$result['content']='admin/zone/add';		
		$this->form_validation->set_rules('zone_name', 'zone name', 'trim|required|is_unique[master_zone.zone_name]',array('is_unique'=>'This %s already exists.'));			
		$this->form_validation->set_rules('zone_description', 'zone description', 'trim|required');
		if($this->form_validation->run()==FALSE){
			
			$this->_load_view($result);
		
		}else{
			if(!empty($_FILES['zone_img']['name'])){
				$image_path = '/public/upload_image/zone_image';
				$file 		= $this->imageupload->image_upload2($image_path,'zone_img');
				//pr($file);
				if($file['status']==0){					
					$this->session->set_flashdata('error_msg',$file['result']);
					redirect('admin/zone/add');
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
			$data = array(
				'zone_name' 		=> $this->input->post( 'zone_name' ),
				'zone_description' 	=> $this->input->post( 'zone_description' ),
				'zone_image' 		=> $img,
				'cover_charges' 	=> $this->input->post( 'cover_price' ),
				'additional_charges'=> $this->input->post( 'additional_price' ),
				'minimum_capacity' 	=> $this->input->post( 'minimum_capacity' ),
				'maximum_capacity' 	=> $this->input->post( 'maximum_capacity' ),
				'zone_type' 		=> $this->input->post( 'zone_type' ),
				'status'			=> $status,
				'created_on' 		=> date('Y-m-d H:i:s')
			);

			$result = $this->mcommon->insert('master_zone',$data);
			if($result)
			{
				$this->session->set_flashdata('error_msg','');
				$this->session->set_flashdata('success_msg','Zone added successfully');
				
				redirect('admin/zone');
			}
			else{
				$this->session->set_flashdata('success_msg','');
				$this->session->set_flashdata('error_msg','Opps!Sorry try again.');				
				redirect('admin/zone/add');
			}
		}
	}

	/*
	author: sreela
	purpose: Edit role view
	date: 6-12-2019
	*/
	public function edit($zone_id)
	{
		$result = array();		
		$result['zone'] = $this->mcommon->getRow('master_zone',array('zone_id'=>$zone_id));
		$result['content']='admin/zone/add';
		$this->_load_view($result);

	}

	/*
	author: sreela
	purpose: Update role view
	date: 6-12-2019
	*/
	public function updateZone($zone_id)
	{
		//pr($_POST);
		$data 	= array();
		$result =  array();
		$img 	= '';
		$result['content']='admin/zone/add';		
		$this->form_validation->set_rules('zone_name', 'zone name', 'trim|required');			
		$this->form_validation->set_rules('zone_description', 'zone description', 'trim|required');
		if($this->form_validation->run()==FALSE){
			
			$this->_load_view($result);
		
		}else{
			if(!empty($_FILES['zone_img']['name'])){
				$image_path = '/public/upload_image/zone_image';
				$file 		= $this->imageupload->image_upload2($image_path,'zone_img');
				//pr($file);
				if($file['status']==0){					
					$this->session->set_flashdata('error_msg',$file['result']);
					redirect('admin/zone/add');
				}	
				else{
					$img = $file['result'];
	        	}
	    	}
	    	else{
	    		$img = $this->input->post( 'old_zone_img' );
	    	}
			if($this->input->post( 'status') ==''){
	    		$status ='0';
	    	}
	    	else{
	    		$status ='1';
	    	}
			$data = array(
				'zone_name' 		=> $this->input->post( 'zone_name' ),
				'zone_description' 	=> $this->input->post( 'zone_description' ),
				'zone_image' 		=> $img,
				'cover_charges' 	=> $this->input->post( 'cover_price' ),
				'additional_charges'=> $this->input->post( 'additional_price' ),
				'minimum_capacity' 	=> $this->input->post( 'minimum_capacity' ),
				'maximum_capacity' 	=> $this->input->post( 'maximum_capacity' ),
				'zone_type' 		=> $this->input->post( 'zone_type' ),
				'status'			=> $status,
				'updated_on' 		=> date('Y-m-d H:i:s')
			);
			$update_cond = array('zone_id' => $zone_id);
			$result = $this->mcommon->update('master_zone',$update_cond,$data);
			if($result)
			{
				$this->session->set_flashdata('error_msg','');
				$this->session->set_flashdata('success_msg','Zone details updated successfully');
				
				redirect('admin/zone');
			}
			else{
				$this->session->set_flashdata('success_msg','');
				$this->session->set_flashdata('error_msg','Opps!Sorry try again.');				
				redirect('admin/zone/edit/'.$zone_id);
			}
		}
		
	}
	public function deleteRole($member_id){
		$response				= array();
		$return_response		= '';		
		$tbl_column_name		= 'zone_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'master_zone';
		$return_response		= getStatusCahnge($member_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','Zone details successfully deleted');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
		}				
		redirect('admin/zone');
	}	
	
	public function changeStatus()
	{
		$response				= array();
		$return_response		= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'zone_id';
		$status     	 		= $this->input->post('change_status');
		$chng_status_colm		= 'status';
		$table 					= 'master_zone';
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			echo 1;exit;
		}				
		else{
			echo 0 ;exit;
		}
	}



}