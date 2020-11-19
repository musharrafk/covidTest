<?php

class admin_model extends CI_Model {

	public function __construct(){
		parent::__construct();
    }

	function authenticate_user()
    {
    	$this->db->select('username');
    	$this->db->where('username', $this->input->post('username'));
		$this->db->where('password', md5($this->input->post('password')));
		$this->db->where('admin_access', 'Y');
		$this->db->from(TABLE_USER);

		$query = $this->db->get();

		if($query->num_rows() > 0){
		 $user =  $query->row();
		  $query->free_result();
		  return $user;
		}else{
			$query->free_result();
			return false;
		}
    }

	function result_grid_array($sql)
	{
		$sort_by = $this->input->post("sidx", TRUE );
		$sort_direction = $this->input->post("sord", TRUE );
		$num_rows = $this->input->post("rows", TRUE );

		$data->page = $this->input->post("page", TRUE );
		$data->records = $this->db->query($sql)->num_rows();
		$data->total = ceil($data->records/$this->input->post("rows", TRUE ));

		if((($num_rows * $data->page) >= 0 && $num_rows > 0))
		{
			if($sort_by)
			{
				$sql = 	$sql." order by ".$sort_by." ".$sort_direction; // set order
			}
			if($data->page != "all")
			{
				$sql = 	$sql." limit ".$num_rows*($data->page - 1).", ".$num_rows;
			}
		}
		//echo $sql;exit;

		$data->rows = $this->db->query($sql)->result_array();
		//echo  json_encode($data);
		return $data;
	}

	function listing($table, $col='', $colVal='')
	{
		$addSql = '';

		if($col!='' and $colVal!='')
		{
			$addSql .= " where ".$col."='".$colVal."'";
		}
		$sql = "select * from ".$table.$addSql;
		return $this->db->query($sql)->result_array();
	}



	function get_admin_details($resultType='G')
	{
		$addSql = '';

		if($this->input->post('status')!='')
		{
			$addSql .= " and status='".$this->input->post('status')."' ";
		}
		else
		{
			$addSql .= " and status='1' ";
		}

		$sql = "select *, concat(fname,' ',lname) as admin_name, if(role_id=1,'Admin','Sub Admin') as admin_type from ".TABLE_ADMIN." where 1=1 ".$addSql;
		//echo $sql; exit;

		if($resultType=='G')
		{
			$result = $this->result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}

		return $result;
	}

	function get_login_admin_info($admin_id='')
	{
		$this->db->from(TABLE_ADMIN);
		$this->db->where('status', '1');
		$this->db->where('id', $admin_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
	    return $query->row_array();
	}

	function isValidUserName($username)
	{
	    $this->db->select('id');
		$this->db->from(TABLE_ADMIN);
		$this->db->where('status', '1');
		$this->db->where('username', $username);
		$query = $this->db->get();
		//echo $this->db->last_query();
	    $data = $query->row_array();
	    return $data['id'];
    }

	function check_sadmin_oldpass()
	{
		$this->db->select('username');
		$this->db->where('email', $this->session->userdata('admin_email'));
		$this->db->where('password', md5($this->input->post('old_password')));
		//$this->db->where('status', '1');
		$this->db->from(TABLE_USER);

		$query = $this->db->get();

		if($query->num_rows() > 0){
		  $query->free_result();
		  return 1;
		} else {
			$query->free_result();
			return 0;
		}
    }

	function check_sadmin_username()
	{
		$this->db->select('username');
		$this->db->where('username', $this->input->post("username"));
		$this->db->where('status', '1');
		$this->db->from(TABLE_ADMIN);
        //echo $this->db->last_query();
		$query = $this->db->get();

		if($query->num_rows() > 0){
		  $query->free_result();
		  return 1;
		} else {
			$query->free_result();
			return 0;
		}
    }

	function check_username_duplicate($id)
	{
		$this->db->select('id');
		$this->db->where('username', $this->input->post("username"));
		$this->db->where('id !=', $id);
		$this->db->from(TABLE_ADMIN);
		//echo $this->db->last_query();

		$query = $this->db->get();
		if($query->num_rows() > 0){
		  $query->free_result();
		  return 1;
		} else {
			$query->free_result();
			return 0;
		}
    }

	function check_email_duplicate($id='')
	{
		$this->db->select('id');
		$this->db->where('email', $this->input->post("email"));
		if($id<>""){
			$this->db->where('id !=', $id);
		}
		$this->db->from(TABLE_ADMIN);
		$query = $this->db->get();
		if($query->num_rows() > 0){
		  $query->free_result();
		  return 1;
		} else {
			$query->free_result();
			return 0;
		}
    }


}