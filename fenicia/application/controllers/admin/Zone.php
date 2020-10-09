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
				//'club_zone_name' 	=> $this->input->post( 'club_zone_name' ),
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
				$log_data = array('action' 	=> 'Add',
							  'statement' 	=> "Added a new zone named - '".$this->input->post( 'zone_name' )."'" ,
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $result,
					  	  	  'type'        => "Zone",
							  'status'		=> '1'
							);
				$this->mcommon->insert('log',$log_data);
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
				//'club_zone_name' 	=> $this->input->post( 'club_zone_name' ),
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
				$log_data = array('action' 		=> 'Edit',
								  'statement' 	=> "Edited details of the zone named -'".$this->input->post( 'zone_name' )."'",
								  'action_by'	=> $this->session->userdata('user_data'),
								  'IP'			=> getClientIP(),
								  'id'          => $zone_id,
					  	  	  	  'type'        => "Zone",
								  'status'		=> '1'
								);
				$this->mcommon->insert('log',$log_data);
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
	public function deleteRole($zone_id){
		$response				= array();
		$return_response		= '';		
		$tbl_column_name		= 'zone_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'master_zone';
		$return_response		= getStatusCahnge($zone_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$log_data = array('action' 		=> 'Delete',
							  'statement' 	=> 'Deleted a zone',
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $zone_id,
					  	  	  'type'        => "Zone",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
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
			if($status == '0'){
				$changed_status 	= 'inactive';
			}
			else{
				$changed_status 	= 'active';
			}
			$zone_data 			= $this->mcommon->getRow('master_zone',array('zone_id'=>$id));
		
			if(!empty($zone_data)){
				$zone_name = $zone_data['zone_name'];
			}
			$log_data = array('action' 		=> 'Edit',
							  'statement' 	=> "Made zone '".$zone_name."' ".$changed_status,
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $id,
					  	  	  'type'        => "Zone",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
			echo 1;exit;
		}				
		else{
			echo 0 ;exit;
		}
	}
	public function zonePaxs($zone_id){
		$zone_paxs_details 	= $this->mcommon->getDetails('zone_paxs',array('zone_id' =>$zone_id));
		//pr($zone_paxs_details);
		$result 						= array();
		$result['zone_paxs_details']	= $zone_paxs_details;		
		$result['zone_id']				= $zone_id;	
		$result['content']				= 'admin/zone/zone_paxs';
		$this->_load_view($result);
	}
	public function addZonePaxs(){
		$response_arr = array();
		if(!empty($_POST)){
			$zone_paxs_id 	= $this->input->post( 'zone_paxs_id' );
			$zone_id 		= $this->input->post( 'zone_id' );
			$min_pax 		= $this->input->post( 'min_pax' );
			$max_pax 		= $this->input->post( 'max_pax' );
			$pax_price 		= $this->input->post( 'pax_price' );
			
			$data = array(
				'zone_id' 			=> $zone_id,
				'minimum_pax' 		=> $min_pax,
				'maximum_pax' 		=> $max_pax,
				'pax_price' 		=> $pax_price,
				'status'			=> '1',
				'created_on' 		=> date('Y-m-d')
			);
			if(empty($zone_paxs_id)){
				$result = $this->mcommon->insert('zone_paxs',$data);
				if($result)
				{
					$response_arr['result'] = "1";
					$response_arr['msg'] 	= "zone paxs details added successfully";
				}
				else{
					$response_arr['result'] = "0";
					$response_arr['msg'] 	= "";
				}
			}
			else{
				$update_cond	= array('zone_paxs_id' =>$zone_paxs_id);
				$result 		= $this->mcommon->update('zone_paxs',$update_cond,$data);
				if($result)
				{
					$response_arr['result'] = "1";
					$response_arr['msg'] 	= "zone paxs details Updated successfully";
				}
				else{
					$response_arr['result'] = "0";
					$response_arr['msg'] 	= "";
				}
			}
		}
		else{
			$response_arr['result'] = "0";
			$response_arr['msg'] 	= "";		
		}
		echo json_encode($response_arr);exit;
	}

}