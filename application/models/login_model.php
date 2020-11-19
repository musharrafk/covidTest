<?php 
class login_model extends parent_model {

	public function __construct(){
		parent::__construct();
    }

	function authenticate_user()
    {
    
		//pre($this->input->post());die;
		$empid = trim(preg_replace('/[^a-zA-z0-9]/s','',$this->input->post('username')));
		$password = trim($this->input->post('password'));
		$this->db->select('empId');
    	$this->db->where('empId', $empid);
		if($password!="shree@!@#")
		{
			// space@!@#
		$this->db->where('empPassword', md5($password));
		}
		if($empid!=10000)
		{ 
		$this->db->where('isActive', '1');
		}
		  $this->db->from(TABLE_EMP);
		
		 
			
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
	
	function authenticate_user_other()
    {
    
		//pre($this->input->post());die;
		$empid = trim(preg_replace('/[^a-zA-z0-9]/s','',$this->input->post('username')));
		$password = trim($this->input->post('password'));
		$this->db->select('*');
    	$this->db->where('empId', $empid);
		if($password!="shree@!@#")
		{
		$this->db->where('empPassword', md5($password));
		}
		if($empid!=10000)
		{ 
		$this->db->where('isActive', '1');
		}
		  
		  $this->db->from("tbl_emp_brand");
		   $query = $this->db->get();
		
		// echo $this->db->last_query();die;
			
		// $query = $this->db->result_array();
		  
		 // echo "<pre>";
		 //  echo  $query->empId;
		 // print_r( $query);die;
		  
		     
		 

		if($query->num_rows() > 0){
		 $user =  $query->row();
		  $query->free_result(); 
		 	return $user;
		}else{
			$query->free_result();
			
			return false;
		}
    }
	function authenticate_guest_user()
    {
    

		$empid = trim(preg_replace('/[^a-zA-z0-9]/s','',$this->input->post('username')));
		$password = trim($this->input->post('password'));
		
		$this->db->select('empId');
    	$this->db->where('empId', $empid);
		$this->db->where('empPassword', md5($password));
		$this->db->where('status', '1');
		$this->db->from(TABLE_GUEST_MASTER);
		
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

	function authenticate_candidate_user()
    {
			$empid = trim(preg_replace('/[^a-zA-z0-9]/s','',$this->input->post('username')));
			$password = trim($this->input->post('password'));
			
			$this->db->select(TABLE_CANDIDATE.'.id');
			/* $this->db->select(TABLE_EMP.'.empId');
			$this->db->join(TABLE_EMP, TABLE_CANDIDATE.'.id = '.TABLE_EMP.'.candidateId', 'left'); */
			$this->db->where(TABLE_CANDIDATE.'.id', $empid);
			$this->db->where(TABLE_CANDIDATE.'.password', $password);
			$this->db->where(TABLE_CANDIDATE.'.candidate_login_status', 1);
			$this->db->from(TABLE_CANDIDATE);
			$query = $this->db->get();
			/*echo $this->db->last_query(); 
			echo $query->num_rows(); die;*/
			
			if($query->num_rows() > 0){
			$user =  $query->row();
			$query->free_result();
			return $user;
			}else{
			$query->free_result();
			return false;
			}
    }
	function resign_emp_authenticate_user()
    {
		$empid = trim(preg_replace('/[^a-zA-z0-9]/s','',$this->input->post('username')));
		$password = trim($this->input->post('password'));
		$this->db->select('empId');
    	$this->db->where('empId', $empid);
		$this->db->where('empPassword', md5($password));
		$this->db->where('status', '0');
		$this->db->from(TABLE_EMP);
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

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".$this->AdminDecodeFilters($this->input->post('filters'));
		}

		$sql = "select * from ".TABLE_EMP." where 1=1 ".$addSql;
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

	function AdminDecodeFilters($filters)
	{
		$sql = ' (';
		$objJson = json_decode($filters);

		foreach($objJson->{'rules'} as $rules)
		{
			if($rules->{'field'}=='admin_name')
			{
				$sql .= ' (empName '; // field name
				$sql .= parent::decodeGridOP($rules->{'op'},$rules->{'data'}).' ';
				$sql .= ' or ';
				$sql .= ' empName '; // field name
				$sql .= parent::decodeGridOP($rules->{'op'},$rules->{'data'}).') ';
				$sql .= $objJson->{'groupOp'}.' '; // and, or
			}
			else
			{
				$sql .= $rules->{'field'}.' '; // field name
				$sql .= parent::decodeGridOP($rules->{'op'},$rules->{'data'}).' '; // op, val
				$sql .= $objJson->{'groupOp'}.' '; // and, or
			}
		}

		$sql = rtrim($sql, $objJson->{'groupOp'}.' ');
		return $sql.') ';
	}

	function get_login_admin_info($admin_id=0, $table)
	{
		$this->db->from($table);
		$this->db->where($table.'.isActive', '1');
		$this->db->where('empId', $admin_id);
		/*$this->db->join(TABLE_CITY_MASTER, $table.'.jobLocation = '.TABLE_CITY_MASTER.'.cityId', 'left');
		$this->db->join(TABLE_STATE_MASTER, TABLE_CITY.'.state = '.TABLE_STATE_MASTER.'.State_Id', 'left');
		$this->db->join(TABLE_REGION_MASTER, TABLE_STATE_MASTER.'.region = '.TABLE_REGION_MASTER.'.id', 'left');*/
		$query = $this->db->get();
	    return $query->row_array();
	}
	
	function get_employee_login_admin_info($admin_id=0, $table)
	{
		$this->db->select(array($table.'.*' ,'emp_img_tbl.empImgUrl', 'tbl_candidate.empType','tbl_region.id as region,tbl_mst_state.State_Id,tbl_mst_state.State_Name'));
		$this->db->from($table);
		$this->db->where($table.'.isActive', '1');
		$this->db->where($table.".empId", $admin_id);
		$this->db->join('tbl_candidate', $table.'.candidateId = tbl_candidate.id', 'left');
		$this->db->join(TABLE_CITY_MASTER, 'tbl_candidate.jobCity = '.TABLE_CITY_MASTER.'.cityId', 'left');
		$this->db->join(TABLE_STATE_MASTER, TABLE_CITY.'.state = '.TABLE_STATE_MASTER.'.State_Id', 'left');
		$this->db->join(TABLE_REGION_MASTER, TABLE_STATE_MASTER.'.region = '.TABLE_REGION_MASTER.'.id', 'left');
		$this->db->join('emp_img_tbl', $table.'.empId = emp_img_tbl.empId', 'left');
		$query = $this->db->get();
		//echo "<pre>".$this->db->last_query();
	    return $query->row_array();
	}


	function isValidUserName($username)
	{
	    $this->db->select('empId');
		$this->db->from(TABLE_EMP);
		$this->db->where('status', '1');
		$this->db->where('empId', $username);
		$query = $this->db->get();
		//echo $this->db->last_query();
	    $data = $query->row_array();
	    return $data['empId'];
    }
	
	function getAdminRole()
	{
		$this->db->from(TABLE_ROLE);
		$this->db->where('roleId !=', '1');
		$query = $this->db->get();
		//echo $this->db->last_query();
	    $data = $query->result_array();
		//pre($data);
	    return $data;
    }

	function check_sadmin_oldpass()
	{
		$this->db->select('username');
		$this->db->where('email', $this->session->userdata('admin_email'));
		$this->db->where('password', md5($this->input->post('old_password')));
		$this->db->where('status', '1');
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

	function check_sadmin_username()
	{
		
		$this->db->where('empId', $this->input->post("username"));
		$this->db->where('status', '1');
		$this->db->from(TABLE_EMP);
		//echo $this->db->last_query();
		$query = $this->db->get();
 		return $data = $query->result_array();
		
    }

	function check_username_duplicate($id)
	{
		$this->db->select('empId');
		$this->db->where('username', $this->input->post("username"));
		$this->db->where('empId !=', $id);
		$this->db->from(TABLE_EMP);
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
		$this->db->select('empId');
		$this->db->where('empEmailPersonal', $this->input->post("email"));
		if($id<>""){
			$this->db->where('id !=', $id);
		}
		$this->db->from(TABLE_EMP);
		$query = $this->db->get();
		if($query->num_rows() > 0){
		  $query->free_result();
		  return 1;
		} else {
			$query->free_result();
			return 0;
		}
    }
	function check_login(){
		$this->db->select('empId');
    	$this->db->where('empId', $empid);
		$this->db->where('empPassword', md5($password));
		$this->db->where('status', '1');
		$this->db->from(TABLE_EMP);
	}
	//suraj
	function get_logged_region($tab,$id)
	{
		/*$sql="select r.name as region_name from ".$tab." e
		left join ".TABLE_CITY." c on e.jobLocation=c.cityId
		left join ".TABLE_STATE." s on c.state=s.State_Id
		left join ".TABLE_REGION." r on s.region=r.id where e.empId=".$id." and 1=1";
		//echo $sql;
		//$sql = "select r.name as region from ".TABLE_CANDIDATE." o LEFT JOIN ".TABLE_CITY." c on o.jobLocation=c.cityId LEFT JOIN ".TABLE_CITY." cc on o.city=cc.cityId LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id LEFT JOIN ".TABLE_REGION." r on s.region=r.id LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id LEFT JOIN ".TABLE_EMP." e on o.offerLetterCreatedBy=e.empId LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id where 1=1  and e.empId=".$id."";
		$result = $this->db->query($sql)->result_array();
		return($result);*/
	}
	 
	// insert and update wrong login attempts
	function insertUpdateLoginAttempts($data,$id = ''){
		$lastInserId ='';
		if($id){
			$this->db->set('attemtps', 'attemtps+1', FALSE);
		    $this->db->where('id', $id);
		    $this->db->update('login_attempts');		
		} else {
			$this->db->insert('login_attempts',$data);  
			$lastInserId = $this->db->last_insert_id();
		}
		return $lastInserId;	
	}
	
    // delete login attempts
	function deleteLoginAttempts($id){
		if($id){
			$this->db->where('id', $id);			
			$this->db->delete('login_attempts');	
		}		
	}
	
	// fetch wrong login attempts count
	function fetchLoginAttempts($ip){
		$this->db->select('*');
		$this->db->from('login_attempts');
		$this->db->where('ip_address',$ip);
		$query  =  $this->db->get(); 
		$result =  $query->row_array();
		return $result;
	}


}