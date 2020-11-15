<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class user_model extends parent_model {

	function login($data)
	{
		
		$this->db->select(['id','first_name','last_name','role_id','token_id','email_id']);
    	$this->db->where('email_id', $this->input->post('username'));
		$this->db->where('password', md5(trim($this->input->post('Password'))));
		$this->db->where('is_active', 1);
		$this->db->from(TABLE_USER);
		$query = $this->db->get();
		$result=$query->row_array();
		
		return $result;
	}
	function getToken($token)
	{
		 
		 $this->db->select(['token_id']);
    	$this->db->where('token_id',$token);
		$this->db->from(TABLE_USER);
		$query = $this->db->get();
		$result=$query->row_array();
		
	  if($token!= $result['token_id']){
		
					 $arr['status']='failed';
					 $arr['errorMsg']='Authentication Failed Please Check ';
					 
            }
			return $arr;
	}
function insertDataSet($data)
	{
		
		$this->db->insert_batch(TABLE_TEST,($data));

		return $result;

	}
	function getItemList($data)
	{
		print_r($data);
		if($data['itemId']!="")
		{
			$addSql = 'id='.$data['itemId'];
			
			echo $addSql;die;
		}
		

		

		return $result;

	}
	function get_grievance_type($resultType='G')
	{
		
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

	//	$q = $this->db->query("select group_concat(State_Name) as grp from tbl_mst_state s inner join tbl_region_state rs on s.State_Id=rs.State_Id")->row();

		$sql = "select * from tbl_grievance_type ".$addSql;
		
		//echo $sql; exit;
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}

		return $result;

	}
	function get_cms_details($resultType='G')
	{
		
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

	//	$q = $this->db->query("select group_concat(State_Name) as grp from tbl_mst_state s inner join tbl_region_state rs on s.State_Id=rs.State_Id")->row();

		$sql = "select * from tbl_cms where 1=1 ".$addSql;
		
		//echo $sql; exit;
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}

		return $result;

	}
	function get_message_details($resultType='G')
	{
		
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

	//	$q = $this->db->query("select group_concat(State_Name) as grp from tbl_mst_state s inner join tbl_region_state rs on s.State_Id=rs.State_Id")->row();

		$sql = "select * from tbl_message where 1=1 ".$addSql;
		
		//echo $sql; exit;
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}

		return $result;

	}

	function getUserInfo($empId)
	{
		return $this->db->query("select * from ".TABLE_EMP." where empId='".$empId."'")->row();
	}
	
	function get_access_details($resultType='G')
	{
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

	//	$q = $this->db->query("select group_concat(State_Name) as grp from tbl_mst_state s inner join tbl_region_state rs on s.State_Id=rs.State_Id")->row();
		$sql = "select r.*, (select GROUP_CONCAT(m.name) from tbl_role_access a inner join tbl_module m on a.moduleId=m.id where a.roleId=r.roleId and m.parentId!='0' order by m.setOrder) as b from tbl_emp_role r where r.status='1' ".$addSql;
		//$sql = "select * from tbl_emp_role where status='1' ".$addSql;
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}

		return $result;

	}
	
	function getManagerRole()
	{
			return $this->db->query("select * from tbl_manager_role")->result_array();
	}
	function get_manager_details($resultType='G')
	{
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

	//	$q = $this->db->query("select group_concat(State_Name) as grp from tbl_mst_state s inner join tbl_region_state rs on s.State_Id=rs.State_Id")->row();

		$sql = "select m.*,r.role from tbl_managers m inner join tbl_manager_role r on m.roleId=r.roleId where 1=1 ".$addSql;
		
		//echo $sql; exit;
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}

		return $result;
	}
	
	function getTwoLevelManager()
	{
			return $this->db->query("select * from tbl_managers where roleId='1' or roleId='2' order by name")->result_array();
	}
	
	function getManagerByRole($roleId)
	{
			return $this->db->query("select * from tbl_managers where roleId='".$roleId."' order by name")->result_array();
	}
	
	function getManagerById($id)
	{
			return $this->db->query("select * from tbl_managers where managerId='".$id."'")->result_array();
	}

	function getMessageFor()
	{
		return $this->db->query("select * from tbl_message_for order by messageForId")->result_array();
	}

	function getTotEmp()
	{
		return $this->db->query("select empId from tbl_emp_master where status='1' and clients='3'")->num_rows();
	}

	function getTotEmpAttendanceType($from='',$to='')
	{
		$arr = array();
		$addSql = '';

		if($from!='')
		{
			$addSql .= " and attendanceDate>='".$from."' ";
		}
		if($to!='')
		{
			$addSql .= " and attendanceDate<='".$to."' ";
		}

		$res = $this->db->query("select count(attendanceTypeId) as tot, attendanceTypeId from tbl_emp_attendance  where 1=1 ".$addSql." group by attendanceTypeId order by attendanceTypeId")->result_array();

		$arr['Present'] = 0;
		$arr['Week Off'] = 0;
		$arr['Training'] = 0;
		$arr['Other Off'] = 0;

		foreach($res as $res)
		{
			$arr[$res['attendanceTypeId']] = $res['tot'];
		}
		return $arr;
	}
}