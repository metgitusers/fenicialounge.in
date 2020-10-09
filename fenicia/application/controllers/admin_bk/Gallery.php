<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gallery extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->admin=$this->session->userdata('admin');
		$this->load->library('imageupload');
		$this->load->model('admin/mgallery');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}
	
	public function index() { 
		//echo $this->session->userdata('email');die;
		$result 						= array();
		$result['content'] 				= 'admin/Gallery/list_gallery';
		$result['gallery_active_list'] 	= $this->mgallery->get_gallery_list('1');
		if(!empty($result['gallery_active_list'])){
			foreach($result['gallery_active_list'] as $evnt){
				$gallery_actv_img	= $this->mgallery->get_gallery_img($evnt['gallery_id']);
				if(!empty($gallery_actv_img)){
					$gallery_active_img[$evnt['gallery_id']] = $gallery_actv_img[0];
				}
				else{
					$gallery_active_img[$evnt['gallery_id']] = '';
				}
			}
			$result['gallery_active_img'] 	= $gallery_active_img;
		}
		$result['gallery_inactive_list'] 	= $this->mgallery->get_gallery_list('0');
		if(!empty($result['gallery_inactive_list'])){
			foreach($result['gallery_inactive_list'] as $inevnt){
				$gallery_inatv_img 	= $this->mgallery->get_gallery_img($inevnt['gallery_id']);
				if(!empty($gallery_inatv_img)){
					$gallery_inatv_img[$inevnt['gallery_id']]  = $gallery_inatv_img[0];
				}
				else{
					$gallery_inatv_img[$evnt['gallery_id']] = '';
				}

			}
			$result['gallery_inactive_img'] 	= $gallery_inatv_img;
		}
		// /pr($result);		
		
		$this->_load_view($result);		
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: Edit member  
	*/
	public function edit($gallery_id){
		$result 			= array();
		$condition 			= array('gallery_id'=>$gallery_id);
		$gallery_list			= $this->mcommon->getRow('master_gallery',$condition);
		$gallery_img_list		= $this->mcommon->getDetails('gallery_images',$condition);
		if(!empty($gallery_list)){
			$result['gallery_list']		= $gallery_list;
			$result['gallery_img_list']	= $gallery_img_list;
		}
		else{
			$result['gallery_list']		= '';
			$result['gallery_img_list']	= '';
		}
		//pr($result['event_img_list']);
		$result['content'] 	= 'admin/Gallery/add_edit_gallery';
		if(empty($result['gallery_list'])){
			redirect('admin/gallery');
		}else{
			$this->_load_view($result);
		}
	}
	public function UpdateGallery($gallery_id){
		//pr($_FILES);
		$data =  array();
		$result =  array();
		if(!empty($_FILES["gallery_img"]["name"][0])){
    		$imageDetailArray 		= array();
			//echo  "58947";exit;
			$config = array(
				'upload_path'   => './public/upload_image/gallery/',
				'allowed_types' => '*',
				'overwrite'     => 1,  
				'max_size'      => 0
			);
			$this->load->library('upload', $config);				
			$images = array();
			foreach ($_FILES["gallery_img"]["name"] as $key => $image_list) {
				$_FILES['images[]']['name']		= $_FILES["gallery_img"]["name"][$key];
				$_FILES['images[]']['type']		= $_FILES["gallery_img"]["type"][$key];
				$_FILES['images[]']['tmp_name']	= $_FILES["gallery_img"]["tmp_name"][$key];
				$_FILES['images[]']['error']	= $_FILES["gallery_img"]['error'][$key];
				$_FILES['images[]']['size']		= $_FILES["gallery_img"]['size'][$key];
				$this->upload->initialize($config);

				if ($this->upload->do_upload('images[]')) {
					$imageDetailArray 		= $this->upload->data();
					$imgArry[]				= $imageDetailArray['file_name'];
					
				} else {
					//echo "kjdfh";exit;								
					$error = $this->upload->display_errors();	
					$this->session->set_flashdata('success_msg','');					
					$this->session->set_flashdata('error_msg', $error);
					redirect('admin/gallery/add');
				}
			}
			//pr($imgArry);
			if(!empty($imgArry)){
				foreach($imgArry as $img){	
					$instdata_gallery_media	= array('gallery_id'=>$gallery_id,'gallery_image'  => $img);
					$this->mcommon->insert('gallery_images',$instdata_gallery_media);
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
							'gallery_name' 						=> $this->input->post( 'gallery_name' ),			
							/*'gallery_text' 						=> $this->input->post( 'gallery_text' ),	
							'gallery_link' 						=> $this->input->post( 'gallery_link' ),*/
							'status' 							=> $status,			
							'created_on' 						=> date('Y-m-d')				
						);
    	//pr($update_data);
		$condition 		= array('gallery_id' => $gallery_id);
		$event_id 		= $this->mcommon->update('master_gallery',$condition,$update_data);
		$this->session->set_flashdata('error_msg','');
		$this->session->set_flashdata('success_msg','Album Updated successfully');
		redirect('admin/gallery');
	}
	/*
		AUTHOR NAME: Sreela Biswas Kundu
		Date: 28/8/19
		purpose: ADD NEW member  
	*/
	public function add(){
		$result 				= array();
		$result['gallery_list']	= '';
		
		$result['content'] 				= 'admin/Gallery/add_edit_gallery';
		$this->_load_view($result);
	}
	public function addGallery(){
		//pr($_POST);
		$data =  array();
		$result =  array();
		if(!empty($_FILES["gallery_img"]["name"][0])){
    		$imageDetailArray 		= array();
			//echo  "58947";exit;
			$config = array(
				'upload_path'   => './public/upload_image/gallery/',
				'allowed_types' => '*',
				'overwrite'     => 1,  
				'max_size'      => 0
			);
			$this->load->library('upload', $config);				
			$images = array();
			foreach ($_FILES["gallery_img"]["name"] as $key => $image_list) {
				$_FILES['images[]']['name']		= $_FILES["gallery_img"]["name"][$key];
				$_FILES['images[]']['type']		= $_FILES["gallery_img"]["type"][$key];
				$_FILES['images[]']['tmp_name']	= $_FILES["gallery_img"]["tmp_name"][$key];
				$_FILES['images[]']['error']	= $_FILES["gallery_img"]['error'][$key];
				$_FILES['images[]']['size']		= $_FILES["gallery_img"]['size'][$key];
				$this->upload->initialize($config);

				if ($this->upload->do_upload('images[]')) {
					$imageDetailArray 		= $this->upload->data();
					$imgArry[]				= $imageDetailArray['file_name'];
					
				} else {
					//echo "kjdfh";exit;								
					$error = $this->upload->display_errors();	
					$this->session->set_flashdata('success_msg','');					
					$this->session->set_flashdata('error_msg', $error);
					redirect('admin/gallery/add');
				}
			}
			//pr($imgArry);
			if(!empty($imgArry)){
				if($this->input->post( 'status') ==''){
		    		$status ='0';
		    	}
		    	else{
		    		$status ='1';
		    	}
				$insert_data = array(		
							'gallery_name' 						=> $this->input->post( 'gallery_name' ),			
							/*'gallery_text' 					=> $this->input->post( 'gallery_text' ),	
							'gallery_link' 						=> $this->input->post( 'gallery_link' ),*/
							'status' 							=> $status,			
							'created_on' 						=> date('Y-m-d')				
						);
				//pr($insert_data);
				$gallery_id 		= $this->mcommon->insert('master_gallery',$insert_data);
				if($gallery_id){
					foreach($imgArry as $img){	
						$instdata_gallery_media	= array('gallery_id'=>$gallery_id,'gallery_image'  => $img);
						$this->mcommon->insert('gallery_images',$instdata_gallery_media);
					}
					$this->session->set_flashdata('error_msg','');
					$this->session->set_flashdata('success_msg','Album added successfully');
					redirect('admin/gallery');
				}
				else{
					$this->session->set_flashdata('success_msg','');
					$this->session->set_flashdata('error_msg','Opp! Please try again.');
		        	redirect('admin/gallery/add');
				}
			}
		}	
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Please select gallery image.');
        	redirect('admin/gallery/add');	        	
    	}
	}
	public function ViewGalleryImgs($gallery_id){
		$result 				= array();
		$condition 				= array('gallery_id'=>$gallery_id);
		$gallery_list			= $this->mcommon->getRow('master_gallery',$condition);
		$gallery_img_list		= $this->mcommon->getDetails('gallery_images',$condition);
		if(!empty($gallery_list)){
			$result['gallery_list']		= $gallery_list;
			$result['gallery_img_list']	= $gallery_img_list;
		}
		else{
			$result['gallery_list']		= '';
			$result['gallery_img_list']	= '';
		}
		//pr($result['event_img_list']);
		$result['content'] 			= 'admin/Gallery/gallery_images';
		$this->_load_view($result);
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
		$image_id	= $this->input->post('gallery_img_id');
		
		$this->db->where('gallery_img_id', $image_id);
   		$this->db->delete('gallery_images');

   		echo 1;exit;
	}
	public function DeleteGallery($gallery_id){
		$response				= array();
		$return_response		= '';		
		$tbl_column_name		= 'gallery_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'master_gallery';
		$return_response		= getStatusCahnge($gallery_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','Album details successfully deleted');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
		}				
		redirect('admin/Gallery');
	}	
	
	public function changeStatus()
	{
		$response				= array();
		$return_response		= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'gallery_id';
		$status     	 		= $this->input->post('change_status');
		$chng_status_colm		= 'status';
		$table 					= 'master_gallery';
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			echo 1;exit;
		}				
		else{
			echo 0 ;exit;
		}
	}
	
}