<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->admin=$this->session->userdata('admin');
		$this->load->library('imageupload');
		//$this->load->library('form_validation');
		$this->load->model('admin/Muser');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}
	public function generateRandomString($length = 4) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
	public function index() { 
		//echo $this->session->userdata('email');die;
		$result 						= array();
		$result['content'] 				= 'admin/users/list';
		$result['user_active_list'] 	= $this->Muser->get_user_list('1');
		$result['user_inactive_list'] 	= $this->Muser->get_user_list('0');
		//pr($result);		
		
		$this->_load_view($result);		
	}
	public function add(){
		$result 				= array();
		$result['users']		= '';
		$result['role']			= $this->mcommon->getDetails('master_role', array('status' =>'1','is_delete' =>'0','role_id !=' =>'1'));
		$result['content'] 		= 'admin/users/add';
		$this->_load_view($result);
	}
	public function addUsers(){
		//pr($_POST);
		$img 	= '';
		$data 	=  array();
		$result =  array();
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[user.email]',array('is_unique'=>'This %s already exists.'));
		$this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|is_unique[user.mobile]',array('is_unique'=>'This %s already exists.'));
		
		if($this->form_validation->run()==FALSE){
			$result['role']			= $this->mcommon->getDetails('master_role', array('status' =>'1','is_delete' =>'0','role_id !=' =>'1'));
			$result['content'] 		= 'admin/users/add';
			$this->_load_view($result);
		
		}
		else{
			if(!empty($_FILES['profile_img']['name'])){
				$image_path = '/public/upload_image/profile_photo';
				$file 		= $this->imageupload->image_upload2($image_path,'profile_img');
				if($file['status']==0){
					$this->session->set_flashdata('error_msg',$file['result']);
					redirect('admin/users/add');
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
	    	if($this->input->post( 'role_id' ) =='1'){
	    		$pin = $this->generateRandomString();
	    	}
	    	else{
	    		$pin = '';
	    	}
        	$data = array(		
			'first_name' 						=> $this->input->post( 'first_name' ),	
			'middle_name' 						=> $this->input->post( 'middle_name' ),	
			'last_name' 						=> $this->input->post( 'last_name' ),
			'role_id'							=> $this->input->post( 'role_id' ),
			'mobile' 							=> $this->input->post( 'mobile' ),	
			'email' 							=> $this->input->post( 'email' ),
			'password'							=> sha1($this->input->post( 'password' )),
			'original_password'					=> $this->input->post( 'password' ),
			'code'								=> $pin,
			'profile_photo'						=> $img,
			'status' 							=> $status,
			'login_status'						=> '1',
			'created_by' 						=> $this->session->userdata('user_data'),			
			'created_date' 						=> date('Y-m-d H:i:s')			
			);
        	$result = $this->mcommon->insert('user',$data);

        	if($result)
        	{
        		$role_data 			= $this->mcommon->getRow('master_role',array('role_id'=>$this->input->post( 'role_id' )));
		//pr($event_data);
				if(!empty($role_data)){
					$role_name = $role_data['role_name'];
				}
        		$log_data = array('action' 		=> 'Add',
								  'statement' 	=> "Added a new sub administrator named - '".$this->input->post( 'first_name' )."' with role -'".$role_name."'",
								  'action_by'	=> $this->session->userdata('user_data'),
								  'IP'			=> getClientIP(),
								  'id'          => $result,
							      'type'        => "User",
								  'status'		=> '1'
								);
				$this->mcommon->insert('log',$log_data);
        		$this->session->set_flashdata('success_msg','New sub administrator added successfully');
        		redirect('admin/users');
        	}
    	}
	}
	public function edit($user_id){
		$result 			= array();
		$result['role']			= $this->mcommon->getDetails('master_role', array('status' =>'1','role_id !=' =>'1'));
		$joindata   = array('select' => 'user.*,role.role_name',
	                        'first_table'   =>'user',
		                    'second_table'  =>'master_role role',
		                    'dependency1'   =>'role.role_id = user.role_id',
		                    'join_type1'    =>'inner'		                    
              				);				
		$condition 			= array('user_id'=>$user_id);		
		$result['users']	= $this->mcommon->joinQuery($joindata,$condition,'row','','');
		//pr($result['users']);
		$result['user_id'] 	= $user_id;
		$result['content'] 	= 'admin/users/add';
		if(empty($result['users'])){
			redirect('admin/users');
		}else{
			$this->_load_view($result);
		}
	}
	public function UpdateUsers($user_id){
		//pr($_POST);
		$data		= array();
		$this->form_validation->set_rules('email', 'email', 'trim|required|valid_email|callback_check_email',array('check_email'=>'This %s already exists.'));
		$this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|callback_check_mobile',array('check_mobile'=>'This %s already exists.'));
		//echo validation_errors(); die();
		if($this->form_validation->run()==FALSE){
 			$result['role']			= $this->mcommon->getDetails('master_role', array('status' =>'1','is_delete' =>'0','role_id !=' =>'1'));
 			$joindata   = array('select' => 'user.*,role.role_name',
	                        'first_table'   =>'user',
		                    'second_table'  =>'master_role role',
		                    'dependency1'   =>'role.role_id = user.role_id',
		                    'join_type1'    =>'inner'		                    
              				);				
		    $condition 			= array('user_id'=>$user_id);		
		    $result['users']	= $this->mcommon->joinQuery($joindata,$condition,'row','','');
		    $result['user_id'] 	= $user_id;
 			$result['content'] 		= 'admin/users/add';
 			$this->_load_view($result);
            //redirect('admin/users/edit/'.$user_id);
		
		}
		else{
    		if(!empty($_FILES['profile_img']['name'])){
    				$image_path = '/public/upload_image/profile_photo';
    				$file 		= $this->imageupload->image_upload2($image_path,'profile_img');
    				if($file['status']==0){
    					$this->session->set_flashdata('error_msg',$file['result']);
    					redirect('admin/users/edit/'.$user_id);
    				}	
    				else{
    		        	$profile_img 	= $file['result'];
    	        	}
        	}
        	else{
        		$profile_img 	= $this->input->post( 'old_profile_img' );    		
        	}
        	if($this->input->post( 'status') ==''){
        		$status ='0';
        	}
        	else{
        		$status ='1';
        	}
        	
        	$data = array(		
    					'first_name' 						=> $this->input->post( 'first_name' ),	
    					'middle_name' 						=> $this->input->post( 'middle_name' ),	
    					'last_name' 						=> $this->input->post( 'last_name' ),
    					'role_id'							=> $this->input->post( 'role_id' ),
    					'mobile' 							=> $this->input->post( 'mobile' ),	
    					'email' 							=> $this->input->post( 'email' ),
    					'password'							=> sha1($this->input->post( 'password' )),
    					'original_password'					=> $this->input->post( 'password' ),
    					'profile_photo'						=> $profile_img,
    					'status' 							=> $status,
    					'updated_by' 						=> $this->session->userdata('user_data'),			
    					'updated_date' 						=> date('Y-m-d H:i:s')			
    					);
			$condition	= array('user_id'=>$user_id);
        	$result = $this->mcommon->update('user',$condition,$data);
        	if($result)
        	{
        		$log_data = array('action' 		=> 'Edit',
								  'statement' 	=> "Edited details of the sub administrator named -'".$this->input->post( 'first_name' )."'",
								  'action_by'	=> $this->session->userdata('user_data'),
								  'IP'			=> getClientIP(),
								   'id'          =>$user_id,
							       'type'        =>"User",
								  'status'		=> '1'
								);
				$this->mcommon->insert('log',$log_data);
        		$this->session->set_flashdata('success_msg','User updated successfully');
        		redirect('admin/users');
        	}
        	else{
        		$this->session->set_flashdata('error_msg','Opps!Sorry Try again.');
    			redirect('admin/users/edit/'.$user_id);
        	}
		}
	}
	public  function check_email() {
       $email = $this->input->post('email');
       $user_id = $this->input->post('user_id');
       $result = $this->Muser->check_email_exist($email,$user_id);
       return $result;
    }
    public  function check_mobile() {
       $mobile = $this->input->post('mobile');
       $user_id = $this->input->post('user_id');
       $result = $this->Muser->check_mobile_exist($mobile,$user_id);
       //echo $result;die;
       return $result;
    }
	public function DeleteUser($user_id){
		$response				= array();
		$return_response		= '';		
		$tbl_column_name		= 'user_id';
		$chng_status_colm		= 'is_delete';
		$status     	 		= '1';
		$table 					= 'user';
		$return_response		= getStatusCahnge($user_id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			$log_data = array('action' 		=> 'Delete',
							  'statement' 	=> 'Deleted a sub admin',
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $user_id,
							  'type'        => "User",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
			$this->session->set_flashdata('error_msg','');
			$this->session->set_flashdata('success_msg','User details successfully deleted');
		}
		else{
			$this->session->set_flashdata('success_msg','');
			$this->session->set_flashdata('error_msg','Opp! Some problem,Try again.');
		}				
		redirect('admin/users');
	}	
	
	public function changeStatus()
	{
		$response				= array();
		$return_response		= '';
		$id						= $this->input->post('id');
		$tbl_column_name		= 'user_id';
		$status     	 		= $this->input->post('change_status');
		$chng_status_colm		= 'status';
		$table 					= 'user';
		$return_response		= getStatusCahnge($id,$table,$tbl_column_name,$chng_status_colm,$status);//function definein commonhelper
		if($return_response){
			if($status == '0'){
				$changed_status 	= 'inactive';
			}
			else{
				$changed_status 	= 'active';
			}
			$user_data 			= $this->mcommon->getRow('user',array('user_id'=>$id));
		
			if(!empty($user_data)){
				$user_name = $user_data['first_name']." ".$user_data['last_name'];
			}
			$log_data = array('action' 		=> 'Edit',
							  'statement' 	=> "Made sub administrator named - '".$user_name."' ".$changed_status,
							  'action_by'	=> $this->session->userdata('user_data'),
							  'IP'			=> getClientIP(),
							  'id'          => $id,
							   'type'       => "User",
							  'status'		=> '1'
							);
			$this->mcommon->insert('log',$log_data);
			echo 1;exit;
		}				
		else{
			echo 0 ;exit;
		}
	}
	public function checkCodeVarification(){
		$code			= $this->input->post('code');
		$user_id		= $this->input->post('user_id');

		$user_data 		= $this->mcommon->getRow('user',array('code'=>$code,'user_id' =>$user_id));
		//pr($user_data);
		if(!empty($user_data)){			
			echo "varified";exit;			
		}
		else{
			echo "notvarified";exit;
		}		
	}
	private function _load_view($data) {
		   // FUNCTION WRIITTEN IN COMMON HELPER
		$this->load->view('admin/layouts/index', $data);
		
	}
}