<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->admin=$this->session->userdata('admin');
		$this->load->library('imageupload');
		$this->load->model('admin/mevent');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}
	
	public function index() { 
		//echo $this->session->userdata('email');die;
		$result 						= array();
		$active_event_cnt 				= 0;
		$inactive_event_cnt 			= 0;
		$result['content'] 				= 'admin/event/list';
		$result['event_active_list'] 	= $this->mevent->get_event_list('1');
		if(!empty($result['event_active_list'])){
			$active_event_cnt 	= count($result['event_active_list']);
			foreach($result['event_active_list'] as $evnt){
				$event_active_images 	= $this->mevent->get_event_img($evnt['event_id']);
				if(!empty($event_active_images)){
					$event_active_img[$evnt['event_id']] = $event_active_images[0];
				}
				else{
					$event_active_img[$evnt['event_id']] = '';
				}
			}
			$result['event_active_img'] 	= $event_active_img;
		}
		$result['event_inactive_list'] 	= $this->mevent->get_event_list('0');
		if(!empty($result['event_inactive_list'])){
			$inactive_event_cnt = count($result['event_inactive_list']);
			foreach($result['event_inactive_list'] as $inevnt){				
				$event_inactive_images 		= $this->mevent->get_event_img($inevnt['event_id']);
				if(!empty($event_inactive_images)){
					$event_inactive_img[$inevnt['event_id']] = $event_inactive_images[0];
				}
				else{
					$event_inactive_img[$inevnt['event_id']] = '';
				}
			}
			$result['event_inactive_img'] 	= $event_inactive_img;
		}
		
		
		// /pr($result);		
		$result['active_event_cnt'] 	= $active_event_cnt;
		$result['inactive_event_cnt'] 	= $inactive_event_cnt;
		$this->_load_view($result);		
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: Edit member  
	*/
	public function edit($event_id){
		$result 			= array();
		$condition 			= array('event_id'=>$event_id);
		$event_list			= $this->mcommon->getRow('master_event',$condition);
		$event_img_list		= $this->mcommon->getDetails('event_images',$condition);
		if(!empty($event_list)){
			$result['event_list']		= $event_list;
			$result['event_img_list']	= $event_img_list;
		}
		else{
			$result['event_list']		= '';
			$result['event_img_list']	= '';
		}
		//pr($result['event_img_list']);
		$result['content'] 	= 'admin/event/add';
		if(empty($result['event_list'])){
			redirect('admin/event');
		}else{
			$this->_load_view($result);
		}
	}
	public function UpdateEvent($event_id){
		pr($_FILES,0);
		$data 	=  array();
		$result =  array();
		$this->form_validation->set_rules('event_name', 'Event Name', 'trim|required');
		$this->form_validation->set_rules('location', 'Location', 'trim|required');
		$this->form_validation->set_rules('event_description', 'Event description', 'trim|required');
		$this->form_validation->set_rules('event_str_date', 'Event start date', 'trim|required');
		$this->form_validation->set_rules('event_str_time', 'Event start time', 'trim|required');
		$this->form_validation->set_rules('event_end_date', 'Event end date', 'trim|required');
		$this->form_validation->set_rules('event_end_time', 'Event end time', 'trim|required');
		if($this->form_validation->run()==FALSE){
			$condition 			= array('event_id'=>$event_id);
			$event_list			= $this->mcommon->getRow('master_event',$condition);
			$event_img_list		= $this->mcommon->getDetails('event_images',$condition);
			if(!empty($event_list)){
				$result['event_list']		= $event_list;
				$result['event_img_list']	= $event_img_list;
			}
			else{
				$result['event_list']		= '';
				$result['event_img_list']	= '';
			}
			//pr($result['event_img_list']);
			$result['content'] 	= 'admin/event/add';
			$this->_load_view($result);
		}
		else{
			$index	= 	array_search('0',$_FILES["event_img"]["size"]);
			//echo $index;
			if(is_numeric($index)){
				//echo "jdsdfsad";exit;
				unset($_FILES["event_img"]["name"][$index]);
				unset($_FILES["event_img"]["type"][$index]);
				unset($_FILES["event_img"]["tmp_name"][$index]);
				unset($_FILES["event_img"]["error"][$index]);
				unset($_FILES["event_img"]["size"][$index]);
			}			
			if(!empty($_FILES["event_img"]["name"][0]) || !empty($_FILES["event_img"]["name"][1])){
	    		$imageDetailArray 		= array();
				//echo  "58947";exit;
				$config = array(
					'upload_path'   => './public/upload_image/event_image/',
					'allowed_types' => '*',
					'overwrite'     => 1,  
					'max_size'      => 0
				);
				$this->load->library('upload', $config);				
				$images = array();
				
				foreach ($_FILES["event_img"]["name"] as $key => $image_list) {
					$_FILES['images[]']['name']		= $_FILES["event_img"]["name"][$key];
					$_FILES['images[]']['type']		= $_FILES["event_img"]["type"][$key];
					$_FILES['images[]']['tmp_name']	= $_FILES["event_img"]["tmp_name"][$key];
					$_FILES['images[]']['error']	= $_FILES["event_img"]['error'][$key];
					$_FILES['images[]']['size']		= $_FILES["event_img"]['size'][$key];
					$this->upload->initialize($config);

					if ($this->upload->do_upload('images[]')) {
						$imageDetailArray 		= $this->upload->data();
						$imgArry[]				= $imageDetailArray['file_name'];
						
					} else {
						//echo "kjdfh";exit;								
						$error = $this->upload->display_errors();	
						$this->session->set_flashdata('success_msg','');					
						$this->session->set_flashdata('error_msg', $error);
						redirect('admin/event/add');
					}
				}
				//pr($imgArry);
				if(!empty($imgArry)){
					foreach($imgArry as $img_key =>$img){
						$type					= explode('/', $_FILES["event_img"]["type"][$img_key]);	
						$instdata_event_media	= array('event_id'=>$event_id,'media_type'=>$type[0],'event_img'  => $img);
						$this->mcommon->insert('event_images',$instdata_event_media);
					}
				}
			}	
			else{				        	
	    	}
	    	if($this->input->post( 'status') ==''){
	    		$status ='0';
	    	}
	    	else{
	    		$status ='1';
	    	}
	    	$update_data = array(		
								'event_name' 						=> $this->input->post( 'event_name' ),			
								'event_location' 					=> $this->input->post( 'location' ),	
								'event_description' 				=> $this->input->post( 'event_description' ),
								'event_start_date' 					=> DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("event_str_date")))),	
								'event_start_time' 					=> $this->input->post( 'event_str_time_submit' ),	
								'event_end_date' 					=> DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("event_end_date")))),	
								'event_end_time' 					=> $this->input->post( 'event_end_time' ),
								'event_flag' 						=> $this->input->post( 'event_flag' ),
								'status' 							=> $status,
								'created_by' 						=> $this->session->userdata('user_data'),			
								'created_on' 						=> date('Y-m-d')				
							);
			$condition 		= array('event_id' => $event_id);
			$result 		= $this->mcommon->update('master_event',$condition,$update_data);
			$log_data = array('action' 		=> 'Edit',
							  'statement' 	=> "Edited event details named- '".$this->input->post( 'event_name' )."'",
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $event_id,
						  	  'type'        =>"Event",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','Event Updated successfully');
			redirect('admin/event');
		}
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: ADD NEW member  
	*/
	public function add(){
		$result 				= array();
		$result['event_list']	= '';
		
		$result['content'] 				= 'admin/event/add';
		$this->_load_view($result);
	}
	public function addEvent(){
		//pr($_FILES);
		$data 		=  array();
		$result 	=  array();
		$imgArry 	=  array();	
		$this->form_validation->set_rules('event_name', 'Event Name', 'trim|required');
		$this->form_validation->set_rules('location', 'Location', 'trim|required');
		$this->form_validation->set_rules('event_description', 'Event description', 'trim|required');
		$this->form_validation->set_rules('event_str_date', 'Event start date', 'trim|required');
		$this->form_validation->set_rules('event_str_time', 'Event start time', 'trim|required');
		$this->form_validation->set_rules('event_end_date', 'Event end date', 'trim|required');
		$this->form_validation->set_rules('event_end_time', 'Event end time', 'trim|required');
		if($this->form_validation->run()==FALSE){
			
			$result['event_list']		= '';
			$result['event_img_list']	= '';
			$result['content'] 	= 'admin/event/add';
			$this->_load_view($result);
		}
		else{	
			$index	= 	array_search('0',$_FILES["event_img"]["size"]);
			if(is_numeric($index)){
				unset($_FILES["event_img"]["name"][$index]);
				unset($_FILES["event_img"]["type"][$index]);
				unset($_FILES["event_img"]["tmp_name"][$index]);
				unset($_FILES["event_img"]["error"][$index]);
				unset($_FILES["event_img"]["size"][$index]);
			}
			if(!empty($_FILES["event_img"]["name"][0]) || !empty($_FILES["event_img"]["name"][1])){
	    		$imageDetailArray 		= array();
				//echo  "58947";exit;
				$config = array(
					'upload_path'   => './public/upload_image/event_image/',
					'allowed_types' => '*',
					'overwrite'     => 1,  
					'max_size'      => 0
				);
				$this->load->library('upload', $config);				
				$images = array();
				//PR($_FILES["event_img"]["name"]);
				foreach ($_FILES["event_img"]["name"] as $key => $image_list) {
					$_FILES['images[]']['name']		= $_FILES["event_img"]["name"][$key];
					$_FILES['images[]']['type']		= $_FILES["event_img"]["type"][$key];
					$_FILES['images[]']['tmp_name']	= $_FILES["event_img"]["tmp_name"][$key];
					$_FILES['images[]']['error']	= $_FILES["event_img"]['error'][$key];
					$_FILES['images[]']['size']		= $_FILES["event_img"]['size'][$key];
					$this->upload->initialize($config);

					if ($this->upload->do_upload('images[]')) {
						$imageDetailArray 		= $this->upload->data();
						$imgArry[]				= $imageDetailArray['file_name'];
						
					} else {
						//echo "kjdfh";exit;								
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('success_msg','');					
						$this->session->set_flashdata('error_msg', $error);
						redirect('admin/event/add');
					}
				}
			}
			if($this->input->post( 'status') ==''){
	    		$status ='0';
	    	}
	    	else{
	    		$status ='1';
	    	}
			$insert_data = array(		
						'event_name' 						=> $this->input->post( 'event_name' ),			
						'event_location' 					=> $this->input->post( 'location' ),	
						'event_description' 				=> $this->input->post( 'event_description' ),
						'event_start_date' 					=> DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("event_str_date")))),	
						'event_start_time' 					=> $this->input->post( 'event_str_time_submit' ),	
						'event_end_date' 					=> DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("event_end_date")))),	
						'event_end_time' 					=> $this->input->post( 'event_end_time' ),
						'status' 							=> $status,
						'event_flag' 						=> $this->input->post( 'event_flag' ),
						'created_by' 						=> $this->session->userdata('user_data'),			
						'created_on' 						=> date('Y-m-d')				
					);
			$event_id 		= $this->mcommon->insert('master_event',$insert_data);
			
			if($event_id){
				if(!empty($imgArry)){
					foreach($imgArry as $img_key =>$img){
						$type					= explode('/', $_FILES["event_img"]["type"][$img_key]);
						$instdata_event_media	= array('event_id'=>$event_id,'media_type'=>$type[0],'event_img'  => $img);
						$this->mcommon->insert('event_images',$instdata_event_media);
					}
				}
				$log_data = array('action' 	=> 'Add',
								  'statement' 	=> "Added a new event named -'".$this->input->post( 'event_name' )."'",
								  'action_by'	=> $this->session->userdata('user_data'),
								  'IP'			=> getClientIP(),
								  'id'          => $event_id,
							  	  'type'        =>"Event",
								  'status'		=> '1'
								);
				$this->mcommon->insert('log',$log_data);
				$this->session->set_flashdata('error_msg','');
				$this->session->set_flashdata('success_msg','Event added successfully');
				redirect('admin/event');
			}
			else{
				$this->session->set_flashdata('success_msg','');
				$this->session->set_flashdata('error_msg','Opp! Please try again.');
	        	redirect('admin/event/add');
			}
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
	public function DeleteImage() {
		$image_id	= $this->input->post('event_img_id');
		$event_id 	= '';
		$event_data = $this->mcommon->getRow('event_images',array('event_img_id' =>$image_id));
		if(!empty($event_data)){
			$event_id = $event_data['event_id'];
		}
		$event_name = $this->mevent->getEventNameByImageId($image_id);
		$this->db->where('event_img_id', $image_id);
   		$this->db->delete('event_images');
   		$log_data = array('action' 		=> 'Delete',
						  'statement' 	=> "Deleted event image of the event named - '".$event_name."'",
						  'action_by'	=> $this->session->userdata('user_data'),
						  'IP'			=> getClientIP(),
						  'id'          => $event_id,
					  	  'type'        =>"Event",
						  'status'		=> '1'
						);
		$this->mcommon->insert('log',$log_data);
   		echo 1;exit;
	}
	public function DeleteEvent($event_id){
		$response				= array();
		$return_response		= '';
		$event_name 			= '';		
		$tbl_column_name		= 'event_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'master_event';
		$event_data 			= $this->mcommon->getRow('master_event',array('event_id'=>$id));
		if(!empty($event_data)){
			$event_name = $event_data['event_name'];
		}
		$return_response		= getStatusCahnge($event_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){			
			$log_data = array('action' 		=> 'Delete',
							  'statement' 	=> "Deleted a event named - '".$event_name."'",
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $event_id,
					  	  	  'type'        =>"Event",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','Event details successfully deleted');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
		}				
		redirect('admin/event');
	}	
	
	public function changeStatus()
	{
		$response				= array();
		$return_response		= '';
		$event_name 			= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'event_id';
		$status     	 		= $this->input->post('change_status');
		$chng_status_colm		= 'status';
		$table 					= 'master_event';
		$event_data 			= $this->mcommon->getRow('master_event',array('event_id'=>$id));
		//pr($event_data);
		if(!empty($event_data)){
			$event_name = $event_data['event_name'];
		}
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			if($status == '0'){
				$changed_status 	= 'inactive';
			}
			else{
				$changed_status 	= 'active';
			}
			$log_data = array('action' 		=> 'Edit',
							  'statement' 	=> "Made '".$event_name."' event ".$changed_status,
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $id,
					  	  	  'type'        =>"Event",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
			echo 1;
		}				
		else{
			echo 0 ;
		}
	}
	public function jsonEventFeed(){		
		$list 	= $this->mevent->get_events_calendar();
		//$list 	= $this->mevent->get_event_list();
	//pr($list);				
		$events	= array();
		if(count($list) > 0){
			$e = array();
			foreach($list as $key => $val){
				if($val['event_name'] != ''){
					$eventname = $val['event_name']; 
				}
				
				$title = $eventname." : ".date('h:i A',strtotime($val['event_start_time']));
				if($val['event_location'] != ''){
					$desp = "Event Location: ".$val['event_location']."<br>"; 
				}				
				
				$start_dt = $val['event_start_date'];
				$end_dt   = $val['event_end_date'];
				$url = base_url().'admin/event/edit/'.$val['event_id'];
				if($val['status'] == '1'){
				
					$backgroundColor	= "#00a65a";
					$textColor			= "#000";
				}
				if($val['status'] == '0'){
					
					$backgroundColor	= "#908f8fcf";
					$textColor			= "#fff";
				}
				
				$e['title']				= $title;
				$e['description']		= $desp;
				$e['start'] 			= $start_dt;
				$e['end'] 				= $end_dt;
				$e['id']				= $val['event_id'];
				$e['url']				= $url;
				$e['backgroundColor']	= $backgroundColor;
				$e['textColor']			= $textColor;						
				$e['allDay'] 			= false;
				$events[]				= $e;
			}	
			//pr($events);		
		}
		else{
			//nothing to do
		}
		echo json_encode($events);
		exit();
	}
	public function jsonPastEventFeed(){		
		$list 	= $this->mevent->get_past_event_list();
	//pr($list);				
		$events	= array();
		if(count($list) > 0){
			$e = array();
			foreach($list as $key => $val){
				if($val['event_name'] != ''){
					$eventname = $val['event_name']; 
				}
				
				$title = $eventname." : ".date('h:i A',strtotime($val['event_start_time']));
				if($val['event_location'] != ''){
					$desp = "Event Location: ".$val['event_location']."<br>"; 
				}				
				
				$start_dt = $val['event_start_date'];
				$end_dt   = $val['event_end_date'];
				$url = base_url().'admin/event/edit/'.$val['event_id'];
				if($val['status'] == '1'){
				
					$backgroundColor	= "#00a65a";
					$textColor			= "#000";
				}
				if($val['status'] == '0'){
					
					$backgroundColor	= "#908f8fcf";
					$textColor			= "#fff";
				}
				
				$e['title']				= $title;
				$e['description']		= $desp;
				$e['start'] 			= $start_dt;
				$e['end'] 				= $end_dt;
				$e['id']				= $val['event_id'];
				$e['url']				= $url;
				$e['backgroundColor']	= $backgroundColor;
				$e['textColor']			= $textColor;						
				$e['allDay'] 			= false;
				$events[]				= $e;
			}	
			//pr($events);		
		}
		else{
			//nothing to do
		}
		echo json_encode($events);
		exit();
	}
	public function pastEventList(){
		$result 						= array();
		$active_past_event_cnt 			= 0;
		$inactive_past_event_cnt 		= 0;
		$result['content'] 				= 'admin/event/past_event_list';
		$result['event_past_active_list'] 	= $this->mevent->get_past_event_list();
		if(!empty($result['event_past_active_list'])){
			$active_past_event_cnt 	= count($result['event_past_active_list']);
			foreach($result['event_past_active_list'] as $evnt){
				$event_active_images 	= $this->mevent->get_event_img($evnt['event_id']);
				if(!empty($event_active_images)){
					$event_active_img[$evnt['event_id']] = $event_active_images[0];
				}
				else{
					$event_active_img[$evnt['event_id']] = '';
				}
			}
			$result['event_past_active_img'] 	= $event_active_img;
		}
		
		$result['active_past_event_cnt'] 	= $active_past_event_cnt;
		// /pr($result);		
		
		$this->_load_view($result);
	}
	public function viewPastEvent($event_id){
		$result 			= array();
		$condition 			= array('event_id'=>$event_id);
		$event_list			= $this->mcommon->getRow('master_event',$condition);
		$event_img_list		= $this->mcommon->getDetails('event_images',$condition);
		if(!empty($event_list)){
			$result['event_list']		= $event_list;
			$result['event_img_list']	= $event_img_list;
		}
		else{
			$result['event_list']		= '';
			$result['event_img_list']	= '';
		}
		//pr($result['event_img_list']);
		$result['content'] 	= 'admin/event/past_event_details';
		if(empty($result['event_list'])){
			redirect('admin/event');
		}else{
			$this->_load_view($result);
		}
	}
	public function viewPastEventImages($event_id){
		$result 				= array();
		$condition 				= array('event_id'=>$event_id);
		$event_list				= $this->mcommon->getRow('master_event',$condition);
		$event_img_list			= $this->mevent->getpasteventimage($event_id);
		if(!empty($event_img_list)){
			$result['event_list']		= $event_list;
			$result['event_img_list']	= $event_img_list;
		}
		else{
			$result['event_list']		= '';
			$result['event_list']	= '';
		}
		//pr($result['event_img_list']);
		$result['content'] 			= 'admin/event/past_event_images';
		$this->_load_view($result);
	}
	public function addPastEventImages(){
		//pr($_FILES,0);
		$index	= 	array_search('0',$_FILES["past_event_img"]["size"]);
		if(is_numeric($index)){
			unset($_FILES["past_event_img"]["name"][$index]);
			unset($_FILES["past_event_img"]["type"][$index]);
			unset($_FILES["past_event_img"]["tmp_name"][$index]);
			unset($_FILES["past_event_img"]["error"][$index]);
			unset($_FILES["past_event_img"]["size"][$index]);
		}
		$data 	=  	array();
		$result =  	array();
		$event_id = $this->input->post('event_id');
		$event_data 			= $this->mcommon->getRow('master_event',array('event_id'=>$event_id));
		//pr($event_data);
		if(!empty($event_data)){
			$event_name = $event_data['event_name'];
		}
		if(!empty($_FILES["past_event_img"]["name"][0]) || !empty($_FILES["past_event_img"]["name"][1])){
    		$imageDetailArray 		= array();
			//echo  "58947";exit;
			$config = array(
				'upload_path'   => './public/upload_image/past_event_images/',
				'allowed_types' => '*',
				'overwrite'     => 1,  
				'max_size'      => 0
			);
			$this->load->library('upload', $config);				
			$images = array();			
			foreach ($_FILES["past_event_img"]["name"] as $key => $image_list) {
				$_FILES['images[]']['name']		= $_FILES["past_event_img"]["name"][$key];
				$_FILES['images[]']['type']		= $_FILES["past_event_img"]["type"][$key];
				$_FILES['images[]']['tmp_name']	= $_FILES["past_event_img"]["tmp_name"][$key];
				$_FILES['images[]']['error']	= $_FILES["past_event_img"]['error'][$key];
				$_FILES['images[]']['size']		= $_FILES["past_event_img"]['size'][$key];
				$this->upload->initialize($config);
				//pr($_FILES["past_event_img"]["name"]);
				if ($this->upload->do_upload('images[]')) {
					$imageDetailArray 		= $this->upload->data();
					$imgArry[]				= $imageDetailArray['file_name'];
					
				} else {
					//echo "kjdfh";exit;								
					$error = $this->upload->display_errors();	
					$this->session->set_flashdata('success_msg','');					
					$this->session->set_flashdata('error_msg', $error);
					redirect('admin/event/viewPastEventImages/'.$event_id);
				}
			}
			//pr($imgArry);
			if(!empty($imgArry)){
				foreach($imgArry as $key_img => $img){
					$type						= explode('/', $_FILES["past_event_img"]["type"][$key_img]);	
					$instdata_past_event_media	= array('event_id'=>$event_id,'media_type'=>$type[0],'images'  => $img);
					$this->mcommon->insert('past_event_images',$instdata_past_event_media);
				}
				$log_data = array('action' 		=> 'Add',
								  'statement' 	=> "Added event image for event named -'".$event_name."'",
								  'action_by'	=> $this->session->userdata('user_data'),
								  'IP'			=> getClientIP(),
								  'id'          => $event_id,
					  	  		  'type'        =>"Past Event",
								  'status'		=> '1'
								);
				$this->mcommon->insert('log',$log_data);
				$this->session->set_flashdata('error_msg','');
				$this->session->set_flashdata('success_msg','Images upload successfully');
				redirect('admin/event/viewPastEventImages/'.$event_id);
			}
			else{
				$this->session->set_flashdata('success_msg','');
				$this->session->set_flashdata('error_msg','Opp! Please try again.');
	        	redirect('admin/event/viewPastEventImages/'.$event_id);
			}
		}	
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Please select gallery image.');
        	redirect('admin/event/viewPastEventImages/'.$event_id);     	
    	}
	}
	public function DeletePastEventImage($past_event_image_id,$event_id){		
		$response				= array();
		$return_response		= '';		
		$tbl_column_name		= 'past_event_image_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'past_event_images';
		$return_response		= getStatusCahnge($past_event_image_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$event_data 			= $this->mcommon->getRow('master_event',array('event_id'=>$event_id));
			//pr($event_data);
			if(!empty($event_data)){
				$event_name = $event_data['event_name'];
			}
			$past_event_img_data 	= $this->mcommon->getRow('past_event_images',array('past_event_image_id'=>$past_event_image_id));
			if(!empty($past_event_img_data)){
				$past_event_img_name = $past_event_img_data['images'];
			}
			$log_data = array('action' 		=> 'Delete',
							  'statement' 	=> "Deleted image of past event named - '".$event_name."'",
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $event_id,
				  	  		  'type'        =>"Past Event",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','Image successfully deleted');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
		}				
		redirect('admin/event/viewPastEventImages/'.$event_id);
	}
	public function filterSearch()
	{
		$responce_arr   		= array();
		$guest_cnt 				= 0;  
        $active_event_cnt		= 0;
        $inactive_event_cnt		= 0;
        $active_past_event_cnt	= 0;
        $inactive_past_event_cnt= 0;  
        //pr($_POST);     
        $from_data      = $this->input->post("from_date");
        $to_data        = $this->input->post("to_date");
        $event_type		= $this->input->post("event_type");
        $result_data    = eventFilterSearch($from_data,$to_data,$event_type);
        //pr($result_data);
        if($event_type =='new'){
	        if(!empty($result_data) && !empty($result_data['event_active_list'])){
	        	
	        	$active_event_cnt 	= count($result_data['event_active_list']);
	        	
	        }
	        if(!empty($result_data) && !empty($result_data['event_inactive_list'])){
	        	$inactive_event_cnt = count($result_data['event_inactive_list']);
	        }
	        $responce_arr['active_event_cnt'] 	= $active_event_cnt;
	        $responce_arr['inactive_event_cnt'] = $inactive_event_cnt;

	        $responce_arr['html'] = $this->load->view('admin/event/ajax_event_list',$result_data,true);
    	}
    	else{
    		
	        $responce_arr['html'] = $this->load->view('admin/event/ajax_past_event_list',$result_data,true);
    	}
    	//pr($responce_arr);
        echo json_encode($responce_arr);exit;
	}
}