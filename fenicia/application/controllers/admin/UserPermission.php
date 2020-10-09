<?php
/*
	author: SREELA
	date: 21-9-2019
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class UserPermission extends MY_Controller {

	public function __construct() {		
		parent::__construct();
		//$this->redirect_guest();
		
		$this->load->model('admin/muser');	
		$this->load->model('admin/mpermission');
		if($this->session->userdata('role_id') == '' || $this->session->userdata('role_id') != '1')
		{
			redirect('admin');
			die();
		}
				
	}
	public function index(){
		$this->_load_view();
	}
	// Default load function for header and footer inculded
	private function _load_view() {
		$data 					= array();
		$user_permission_details= array();
		$menu_list 				= array();
		$role_cond 				= array('status' => '1','is_delete' =>'0','role_id !=' =>'1');
		$role_data				= $this->mcommon->getDetails('master_role',$role_cond);
		//pr($role_data);
		if(!empty($role_data)){
			$data['role_data']	= $role_data;
		}
		else{
			$data['role_data']	= '';
		}	
		$menu_data				= $this->mcommon->getAllMenuList('master_menu');
		//pr($menu_data);			
		if(!empty($menu_data)){
			foreach($menu_data as $list){
				if(!empty($list['menu_link'])){
					$menu_list[]	= $list;
				}
			}
			$data['menu_data']	= $menu_list;

		}
		else{
			$data['menu_data']	= '';
		}
		
		//pr($data);
		$data['content']	=	'admin/users/user_permission_list';
		$this->load->view('admin/layouts/index',$data);
	}	

	public function saveUserPermitionSingle(){
		$responseArr		= array();
		$updatearr			= array();
		$insertarr			= array();
		if($this->input->post()){			
			//pr($_POST);
			$add_flag		= '0';
			$edit_flag		= '0';
			$view_flag	= '0';
			$download_flag	= '0';
			$menu_id		= $this->input->post('menu_id');
			$menu_name		= $this->input->post('menu_name');
			$role_id		= $this->input->post('role_id');
			$parent_id		= $this->input->post('parent_id');
			if(!empty($this->input->post('add'))){
				$add_flag		= $this->input->post('add');
			}
			if(!empty($this->input->post('edit'))){
				$edit_flag		= $this->input->post('edit');
			}
			if(!empty($this->input->post('view_acn'))){
				$view_flag		= $this->input->post('view_acn');
			}
			if(!empty($this->input->post('download'))){
				$download_flag		= $this->input->post('download');
			}			
			$permission_id  		= $this->input->post('permission_id');
			$user_permission_data	= $this->getUserPermissionList($menu_id,$role_id);
			//pr($user_permission_data);
			if(empty($user_permission_data)){
				$insertarr 	= array(
									'role_id' 		=>$role_id,
									'menu_id' 		=>$menu_id,
									'menu_name'		=>$menu_name,
									'add_flag' 		=>$add_flag,
									'edit_flag' 	=>$edit_flag,
									'view_flag' 	=>$view_flag,
									'download_flag' =>$download_flag,
									'is_active'		=> '1',
									'created_by'	=>$this->user_id,
									'created_ts'	=>date('Y-m-d h:i:s')
								);
			//pr($insertarr);
				$this->mcommon->insert('user_permission',$insertarr);
				//echo $parent_id;exit;
				$parent_cond		= array('menu_id' => $parent_id,'role_id' =>$role_id);				
				$parent_data 		= $this->mcommon->getRow('user_permission',$parent_cond);
				//pr($parent_data);
				if(empty($parent_data)){
					//echo $parent_id;exit;
					$parent_condition		= array('menu_id' => $parent_id);				
					$parent_details 		= $this->mcommon->getRow('master_menu',$parent_condition);
					//pr($parent_details);
					if(!empty($parent_details)){
						$insertarr 	= array(
									'role_id' 		=>$role_id,
									'menu_id' 		=>$parent_details['menu_id'],
									'menu_name'		=>$parent_details['menu_name'],
									'add_flag' 		=>$add_flag,
									'edit_flag' 	=>$edit_flag,
									'view_flag' 	=>$view_flag,
									'download_flag' =>$download_flag,
									'is_active'		=> '1',
									'created_by'	=>$this->user_id,
									'created_ts'	=>date('Y-m-d h:i:s')
								);
			//pr($insertarr);
						$this->mcommon->insert('user_permission',$insertarr);
					}
				}
				$user_permission_details	= $this->getUserPermissionDataOnRole($role_id);
				$responseArr['html']		= $this->load->view('admin/users/ajax_user_permission_list',$user_permission_details,true);
				$responseArr['process']		= 'success';
			}
			else{
				//echo "fsjaflk";
				$update_cond	= array('permission_id' => $permission_id);
				$updatearr 		= array(
										'role_id' 		=>$role_id,
										'menu_id' 		=>$menu_id,
										'menu_name'		=>$menu_name,
										'add_flag' 		=>$add_flag,
										'edit_flag' 	=>$edit_flag,
										'view_flag' 	=>$view_flag,
										'download_flag' =>$download_flag,
										'is_active'		=> '1',
										'updated_by'	=>$this->user_id,
										'updated_ts'	=>date('Y-m-d h:i:s')
									);

				//pr($update_cond);
				$this->mcommon->update('user_permission',$update_cond,$updatearr);				
				$sub_menu_id 		= $this->mpermission->getSubmenuId($parent_id);
				//pr($sub_menu_id);
				if(!empty($sub_menu_id)){
					$submenu_id_list 	= count($sub_menu_id);
					if($submenu_id_list > 1){
						$submenu_id 	= implode(',',$sub_menu_id);
					}
					else{
						$submenu_id 	= $sub_menu_id;
					}
					$cond_submenu 		= "menu_id in (".$submenu_id.") and add_flag = '0'";
					$sub_menu_id 		= $this->mpermission->getSubmenuDetails($cond_submenu);
					$all_submenu_permission	= count($sub_menu_id);
					if($submenu_id_list == $all_submenu_permission){
						$prent_updatearr 	= array('add_flag' 		=>$add_flag,
													'edit_flag' 	=>$edit_flag,
													'view_flag' 	=>$view_flag,
													'download_flag' =>$download_flag,
													'is_active'		=> '1',
													'updated_by'	=>$this->user_id,
													'updated_ts'	=>date('Y-m-d h:i:s')
										);
					}
					else{
							$prent_updatearr 	= array('add_flag' 		=>'1',
														'edit_flag' 	=>$edit_flag,
														'view_flag' 	=>$view_flag,
														'download_flag' =>$download_flag,
														'is_active'		=> '1',
														'updated_by'	=>$this->user_id,
														'updated_ts'	=>date('Y-m-d h:i:s')
							);
					}
					$menu_prnt_cond		= array('role_id' => $role_id,'menu_id' => $parent_id);
					$this->mcommon->update('user_permission',$menu_prnt_cond,$prent_updatearr);
				}				
				$user_permission_details	= $this->getUserPermissionDataOnRole($role_id);
				$responseArr['html']		= $this->load->view('admin/users/ajax_user_permission_list',$user_permission_details,true);

				$responseArr['process']		= 'success';
				
			}
		}
		else{
			$responseArr['process']		= 'failed';
		}
		echo json_encode($responseArr);exit;
	}
	public function ajaxGetUserPermissionDataOnRole($role_id){		
		$user_permission_details			= array();	
		$user_permission_details			= $this->getUserPermissionDataOnRole($role_id);
		$user_permission_details['role_id']	= $role_id;
		//pr($user_permission_details);
		echo $this->load->view('admin/users/ajax_user_permission_list',$user_permission_details,true);exit;
	}
	public function getUserPermissionDataOnRole($role_id){
		$data 					= array();	
		$menu_data				= $this->mcommon->getAllMenuList('master_menu');
		//pr($menu_data,0);			
		if(!empty($menu_data)){
			foreach($menu_data as $val){
				if(!empty($val['menu_link'])){
					$user_permission_condn	= array('menu_id' =>$val['menu_id'],'role_id' => $role_id);
					$user_permission_data	= $this->getUserPermissionList($val['menu_id'],$role_id);
					//pr($user_permission_data,0);
					if(!empty($user_permission_data)){
						$user_permission_details[$val['menu_id']]	= $user_permission_data;
					}
					else{
						$user_permission_details[$val['menu_id']]	= '';
					}
					$menu_list[]	= $val;
					
				}
				
			}
			$data['menu_data']	= $menu_list;
		}
		else{
			$data['menu_data']	= '';
		}
		if(!empty($user_permission_details)){
			$data['user_permission_data']	= $user_permission_details;
		}
		else{
			$data['user_permission_data']	= '';
		}
		//pr($data['menu_data']);
		
		return $data;
	}
	 
    public function mutilyUserPermission(){
    	if($this->input->post()){    		
    		//pr($_POST);
    		$add_flag		= '0';
    		$edit_flag		= '0';
    		$view_flag	= '0';
    		$download_flag	= '0';
    		$add 	 = $this->input->post('add');
    		$edit 	 = $this->input->post('edit');
    		$view  = $this->input->post('view');
    		$download= $this->input->post('download');
    		$role_id = $this->input->post('role_id');
    		$parent_id = $this->input->post('parent_id');
    		//pr($add);
    		$user_permission_details 	= $this->getUserPermissionDataOnRole($role_id);
    		if(!empty($user_permission_details['menu_data'])){
    			foreach($user_permission_details['menu_data'] as $val){    				
    				if(!empty($add) && array_key_exists($val['menu_id'], $add)){
    					$add_flag	= '1';
    				}
    				else{
    					$add_flag	= '0';
    				}
    				if(!empty($edit) && array_key_exists($val['menu_id'], $edit)){
    					$edit_flag	= '1';
    				}
    				else{
    					$edit_flag	= '0';
    				}
    				if(!empty($view) && array_key_exists($val['menu_id'], $view)){
    					$view_flag	= '1';
    				}
    				else{
    					$view_flag	= '0';
    				}
    				if(!empty($download) && array_key_exists($val['menu_id'], $download)){
    					$download_flag	= '1';
    				}
    				else{
    					$download_flag	= '0';
    				}
    				$condition	= array('role_id' => $role_id,'menu_id'=>$val['menu_id']);
    				$user_permission_data = $this->mcommon->getDetails('user_permission',$condition);
    				//pr($user_permission_data);
    				if(empty($user_permission_data)){
						$insertarr 	= array(
											'role_id' 		=>$role_id,
											'menu_id' 		=>$val['menu_id'],
											'menu_name'		=>$val['menu_name'],
											'add_flag' 		=>$add_flag,
											'edit_flag' 	=>$edit_flag,
											'view_flag' 	=>$view_flag,
											'download_flag' =>$download_flag,
											'is_active'		=> '1',
											'created_by'	=>$this->user_id,
											'created_ts'	=>date('Y-m-d h:i:s')
										);
					//pr($insertarr);
						$this->mcommon->insert('user_permission',$insertarr);
						if(!empty($parent_id) && array_key_exists($val['menu_id'], $parent_id)){
							
							$parent_cond		= array('menu_id' => $parent_id[$val['menu_id']],'role_id' =>$role_id);				
							$parent_data 		= $this->mcommon->getRow('user_permission',$parent_cond);
							//pr($parent_data);
							if(empty($parent_data)){
								//echo $parent_id;exit;
								$parent_condition		= array('menu_id' => $parent_id[$val['menu_id']]);				
								$parent_details 		= $this->mcommon->getRow('master_menu',$parent_condition);
								//pr($parent_details);
								if(!empty($parent_details)){
									$insertarr 	= array(
												'role_id' 		=>$role_id,
												'menu_id' 		=>$parent_details['menu_id'],
												'menu_name'		=>$parent_details['menu_name'],
												'add_flag' 		=>$add_flag,
												'edit_flag' 	=>$edit_flag,
												'view_flag' 	=>$view_flag,
												'download_flag' =>$download_flag,
												'is_active'		=> '1',
												'created_by'	=>$this->user_id,
												'created_ts'	=>date('Y-m-d h:i:s')
											);
						//pr($insertarr);
									$this->mcommon->insert('user_permission',$insertarr);
								}
							}
						}
						
						$this->session->set_flashdata('success_msg','User permission given successfully');
					}
					else{
						
						$updatearr 		= array(
												'role_id' 		=>$role_id,
												'menu_id' 		=>$val['menu_id'],
												'menu_name'		=>$val['menu_name'],
												'add_flag' 		=>$add_flag,
												'edit_flag' 	=>$edit_flag,
												'view_flag' 	=>$view_flag,
												'download_flag' =>$download_flag,
												'is_active'		=> '1',
												'updated_by'	=>$this->user_id,
												'updated_ts'	=>date('Y-m-d h:i:s')
											);

						//pr($updatearr);
						$this->mcommon->update('user_permission',$condition,$updatearr);
						$this->session->set_flashdata('success_msg','User permission updated successfully');
					}
    			}
    		}
    		else{
    			$this->session->set_flashdata('error_msg','Some issue found, please try again.');
    		}
		}
		redirect('admin/user-permission');
    }
}