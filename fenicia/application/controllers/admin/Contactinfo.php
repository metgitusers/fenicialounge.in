<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
		AUTHOR NAME: Soma Nandi Dutta
		DATE: 17/7/20
	    PURPOSE: Contact info listing and update
*/
class Contactinfo extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->admin=$this->session->userdata('admin');
		//$this->load->library('imageupload');
		$this->load->model('admin/mcontactinfo');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}
	
	public function index() { 
		
		$result 						= array();
		$result['contact_info'] 	    = $this->mcontactinfo->getContactinfo();
		//print_r($result['contact_info'] );die;
		$result['content'] 				= 'admin/contact_info/edit';
	    $this->_load_view_contactinfo($result);		
	}
	 public function contact_info_update()
	{    
	    $id=$this->input->post('id');  
		//$this->form_validation->set_rules('email','Email','required|is_unique[contact_info.email]|valid_email'); 
		$this->form_validation->set_rules('email','Email','required|valid_email');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required');
		$this->form_validation->set_rules('land_line', 'Land Line', 'trim|required');
		$this->form_validation->set_rules('address', 'Address', 'trim|required');
	
		if ($this->form_validation->run() == FALSE) {
		//echo "validation error";die;
		$this->session->set_flashdata('error_msg','Not updated.Something went wrong');
		$this->index();
		} else {
           
          
		 	$udata = array(
		        'email' => $this->input->post('email'),
		        'phone' => $this->input->post('phone'),
		        'land_line' => $this->input->post('land_line'),
		        'address' => $this->input->post('address'),
		        'date_of_update' => date('Y-m-d'),
            );
            
            $condition=array('id' => $id);
            $this->mcommon->update('contact_info',$condition, $udata);
		 	//echo $this->db->last_query();die;
		 	$this->session->set_flashdata('success_msg','Contact Info Updated successfully.');
		 	redirect('admin/contactinfo');
		 	
	   }
    }

	
	
	private function _load_view_contactinfo($data) {
		   // FUNCTION WRIITTEN IN COMMON HELPER
		$this->load->view('admin/layouts/index', $data);
		
	}
	

}