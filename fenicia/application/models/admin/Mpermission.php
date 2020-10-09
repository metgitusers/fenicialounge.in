<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mpermission extends CI_Model {

	var $table = 'wh_role_permission';
    var $column_order = array('wh_menu_master_id','status','date_of_creation',null); //set column field database for datatable orderable
    var $column_search = array('status','date_of_creation'); //set column field database for datatable searchable just firstname , lastname , address are searchable
    var $order = array('wh_menu_master_id' => 'asc'); // default order 
	public function __construct() {
        parent::__construct();
	}
	private function _get_datatables_query($role_id=null){
        //$this->db->select('wh_menu_master.*,wh_role_permission.menu_name');
        
        $this->db->from('wh_menu_master');
		$i = 0;
        foreach ($this->column_search as $item){ // loop column 
            if($_POST['search']['value']){ // if datatable send POST for search
                 
                if($i===0){ // first loop
					$this->db->group_start(); // open broleet. query Where with OR clause better with broleet. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                }
                else{
                    $this->db->or_like($item, $_POST['search']['value']);
                }
				if(count($this->column_search) - 1 == $i) //last loop
                $this->db->group_end(); //close broleet
            }
            $i++;
        }
        $this->db->where('wh_menu_master.manu_parent_id >',0);
        // if($role_id){
        //     $this->db->where('wh_role_permission.role_id',$role_id);
        //     $this->db->join('wh_role_permission', 'wh_role_permission.wh_menu_master_id = wh_menu_master.wh_menu_master_id', 'inner');
        // }
        
        //$this->db->join('wh_level_master', 'wh_level_master.level_id = wh_role_master.level_id', 'inner');
        if(isset($_POST['order'])){ // here order processing
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }else if(isset($this->order)){
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
	public function get_datatables($role_id){	
        $this->_get_datatables_query($role_id);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        //echo $this->db->last_query();die;
        //return $query->result_array();
        foreach ($query->result_array() as $key => $value) {
            $temp_array = $value;
            $temp_array['add_flag'] = $this->get_val($role_id,$value['wh_menu_master_id'],'add_flag');
            $temp_array['edit_flag'] = $this->get_val($role_id,$value['wh_menu_master_id'],'edit_flag');
            $temp_array['delete_flag'] = $this->get_val($role_id,$value['wh_menu_master_id'],'delete_flag');
            $temp_array['download_flag'] = $this->get_val($role_id,$value['wh_menu_master_id'],'download_flag');
            $return_array[] = $temp_array;
        }
        return $return_array;
    }
	public function count_filtered(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function count_all(){
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
	public function get_details($role_id){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('role_id',$role_id);
		$query=$this->db->get();
		return $query->row_array();
	}
	public function update($condition,$data){
		$this->db->where($condition);
		$this->db->update($this->table,$data);
		return 1;
	}
	public function add($data){
        $this->db->insert($this->table,$data);
		return true;
	}
	public function delete($condition){
		$this->db->delete($this->table,$condition);
		return 1;
    }
    
    public function change_status($role_id){
        $sql = "update wh_role_master Set status = (case when (status = 1) THEN 0 ELSE 1 END) where `role_id`='".$role_id."'";
        $query = $this->db->query($sql);
        if($query){
            return true;
        }else{
            return false;
        }
    }

    public function get_val($role_id,$wh_menu_master_id,$field_name)
    {
        $this->db->select($field_name);
        $this->db->from('wh_role_permission');
        $this->db->where(array('role_id'=>$role_id,'wh_menu_master_id'=>$wh_menu_master_id));
        $query=$this->db->get();
        return $query->row_array()[$field_name];
    }
    public function getSubmenuId($parent_id)
    {
        $result = array();
        $this->db->select('menu_id');
        $this->db->from('master_menu');
        $this->db->where('parent_id',$parent_id);
        $query = $this->db->get();
        $menu_id = $query->result_array();
        if(!empty($menu_id)){
            foreach($menu_id as $val){
                $result[] = $val['menu_id'];
            }
        }
       return $result;
    }
    public function getSubmenuDetails($condition){
        $result = array();
        $this->db->select('*');
        $this->db->from('user_permission');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result_array();        
    }
}