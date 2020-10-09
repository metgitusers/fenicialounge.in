<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Cms extends MY_Controller {
	public function __construct() {
		parent::__construct();
		//$this->redirect_guest();
		$this->admin=$this->session->userdata('admin');
		$this->load->model('admin/mcrm');		
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}
	public function index() { 
		//echo $this->session->userdata('email');die;
		$this->_load_list_view_cms();		
	}
	
	private function _load_list_view_cms() {
		$data 						= array();
		$data['cms_active_list']	= $this->mcrm->get_page_list('1');
		$data['cms_inactive_list']	= $this->mcrm->get_page_list('0');
		$data['content']			= 'admin/cms/list_cms';
		$this->load->view('admin/layouts/index',$data);
	}
	
	public function Add(){
		//pr($_POST);
		if($this->input->post()){
			
			$this->form_validation->set_rules('page_name','Page Name','required');
			
			if($this->form_validation->run()==FALSE){
				
				$this->_load_add_cms();
			
			}else{
				//echo '<pre>'; print_r($this->input->post());die;
				if($this->input->post( 'status') ==''){
		    		$status ='0';
		    	}
		    	else{
		    		$status ='1';
		    	}	
				$idata['page_name']=$this->input->post('page_name');				
				$idata['cms_slug'] = strtolower(url_title($this->input->post('page_name'), 'dash'));
				$idata['description']=$this->input->post('cms_description');
				$idata['short_desc']=$this->input->post('short_description');
				$idata['date_of_creation']=date('Y-m-d H:i:s');
				$idata['status'] = $status;
				$page_id=$this->mcommon->insert('cms',$idata);
				
				if($page_id){
					$this->session->set_flashdata('success_msg','A new page added successfully');
						redirect(base_url('admin/cms'));

				}
				else{
						
					$this->session->set_flashdata('error_msg','Oops!Something went wrong...');
					redirect(base_url('admin/cms'));
				
				}
				
					
			}

		}
		else{

			$this->_load_add_cms();
		}
			
	}

	private function _load_add_cms(){		
		$data['content']='admin/cms/add_edit_cms';
		//$data['commission_type']=$this->mcommon->getDetails('master_commission',array('status'=>1));
		//print_r($data['commission_type']);die;
		$this->load->view('admin/layouts/index',$data);
	}
	
	

	public function change_status(){
		$category_id=$this->input->post('category_id');
        $query = $this->mcategory->change_status($category_id);
        if($query){
            echo json_encode('Updated');
        }else{
            echo json_encode('Not Updated');
        }
	}
	
	public function edit_cms($page_id){
		
		if($this->input->post()){
			
			$this->form_validation->set_rules('page_name','Page Name','required');
			
			if($this->form_validation->run()==FALSE){
				
				$this->_load_edit_cms($page_id);
			
			}else{
				//echo '<pre>'; print_r($this->input->post());die;
				if($this->input->post( 'status') ==''){
		    		$status ='0';
		    	}
		    	else{
		    		$status ='1';
		    	}	
				$udata['page_name']=$this->input->post('page_name');				
				//$udata['cms_slug'] = url_title($this->input->post('page_name'), 'underscore');
				$udata['description']=$this->input->post('cms_description');
				$udata['short_desc']=$this->input->post('short_description');
				$udata['date_of_update']=date('Y-m-d H:i:s');
				$udata['status'] = $status;
				$page_id=$this->mcommon->update('cms',array('page_id'=>$page_id),$udata);
				
				if($page_id){
					$this->session->set_flashdata('success_msg','Page content updated successfully');
						redirect(base_url('admin/cms'));

				}
				else{
						
					$this->session->set_flashdata('error_msg','Oops!Something went wrong...');
					redirect(base_url('admin/cms'));
				
				}
					
						
					}

			}
			else{

				$this->_load_edit_cms($page_id);
			}
			
		}

		private function _load_edit_cms($page_id){		
			$data['content']='admin/cms/add_edit_cms';
			//$data['commission_type']=$this->mcommon->getDetails('master_commission',array('status'=>1));
			//print_r($data['commission_type']);die;
			$data['cms_data']=$this->mcommon->getRow('cms',array('page_id'=>$page_id));
			$this->load->view('admin/layouts/index',$data);
		}

	
	public function changeStatus()
	{
		$response				= array();
		$return_response		= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'page_id';
		$status     	 		= $this->input->post('change_status');
		$chng_status_colm		= 'status';
		$table 					= 'cms';
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			echo 1;exit;
		}				
		else{
			echo 0 ;exit;
		}
	}
	public function DeletePage($page_id){

		$response				= array();
		$return_response		= '';		
		$tbl_column_name		= 'page_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'cms';
		$return_response		= getStatusCahnge($page_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','Page successfully deleted');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
		}				
		redirect('admin/Cms');
	}
}