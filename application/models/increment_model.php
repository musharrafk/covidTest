<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class increment_model extends parent_model {
	//var $base_tbl = TABLE_INCREMENT_ACTIVITY;
	var $u_column = 'id';

	function get_details($eid=0, $resultType='G')
	{
		if($eid){
			$addsql .= " AND a.empId=".$eid."";
		}
		 
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		
		 $sql = "select a.*, DATE_FORMAT(a.effectiveFrom,'%d %b, %Y') as  effectiveFrom,tbl_increment_type.name as incrementType, DATE_FORMAT(a.actionOn,'%d %b, %Y') as actionOn  from   tbl_increment a LEFT JOIN tbl_increment_type on a.incrementType=tbl_increment_type.id
		where 1=1 ".$addsql." ";
	
		/*$sql = "select a.*, DATE_FORMAT(a.effectiveFrom,'%d %b, %Y') as  effectiveFrom, DATE_FORMAT(a.actionOn,'%d %b, %Y') as actionOn,  inty.name as increment_type, nd.name as newDesignation, od.name as newDesignation   from  tbl_salarybreakup_history a 
		left join tbl_increment_type  inty on a.incrementType=inty.id
		left join ".TABLE_DESIGNATION_MASTER."  nd on a.newDesignation=nd.id
		left join ".TABLE_DESIGNATION_MASTER."  od on a.oldDesignation=od.id
		where 1=1 ".$addsql." ";*/
	
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
	function get_increment_history($uid)
	{
	if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		
		 $sql = "select a.*, DATE_FORMAT(a.effectiveFrom,'%d %b, %Y') as  effectiveFrom, DATE_FORMAT(a.actionOn,'%d %b, %Y') as actionOn  from   tbl_increment a 
		where 1=1 and a.empId=".$uid." ".$addsql." ";
		$result = parent::result_grid_array($sql);
		return $result;
		
	}
	
	function get_employee_increment_activity()
	{
	
	if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		 $sql = "select a.id, a.totalEmployee, a.incrementPercent, a.remarks,  DATE_FORMAT(a.effectiveFrom,'%d %b, %Y') as  effectiveFrom, DATE_FORMAT(a.isCreated,'%d %b, %Y') as isCreated, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as addedBy    from  ".TABLE_INCREMENT_ACTIVITY." a 
		left join ".TABLE_EMP."  e on a.addedby = e.empId
		where 1=1 ".$addsql." ";
			$result = parent::result_grid_array($sql);
		return $result;
		
	}
	
	function get_employee_list($resultType)
{
		

	$addsql = '';
	$ids =$this->input->post('ids');
	$region = explode(',',$this->session->userdata('admin_region')); 
	if($ids)
	{
		$addsql .= " and e.empId IN(".$ids.")";
	}
	if($this->input->post('empDept')!='')
	{
		$addsql .= " and e.empDept='".$this->input->post('empDept')."' ";
	}
	if($this->input->post('designation'))
	{
		$addsql .= " and de.id=".$this->input->post('designation');
	}
	if($this->input->post('jobLocation')!='')
	{
		$addsql .= " and e.jobLocation='".$this->input->post('jobLocation')."' ";
	}
	if($this->input->post('status')!='')
	{
		$addsql .= " and e.status='".$this->input->post('status')."' ";
	}
	if($this->input->post('state')!='')
	{
		$addsql .= " and s.State_Id='".$this->input->post('state')."' ";
	}
	if($this->input->post('region')!='')
	{
		$addsql .= " and r.id='".$this->input->post('region')."' ";
	}

		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select  e.empId,e.empEmailOffice,e.status,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ, e.empEmailPersonal, de.name as desination,r.name as region,s.State_Name as state,c.cityName as jobLocation, sa.inhandSalary, sa.ctc, sa.grossSalary, sa.basic, sa.hra, sa.specialAllow, sa.conveyance, sa.medicalAllow, sa.fuelReimbursement, sa.variable, sa.statutoryBonus, sa.esi, sa.contributionOfesi, sa.pf, sa.pfEmployer, sa.pfadminCharge  from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLE_SALARYBREAKUP." sa on e.empId=sa.empId
		where 1=1 ".$addsql." and e.status='1'";
		//echo $sql;die; 
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

function get_employee_listforIncrement($resultType,$ids)
{
		

	$addsql = '';
	
	$region = explode(',',$this->session->userdata('admin_region')); 
	if($ids)
	{
		$addsql .= " and e.empId IN(".$ids.")";
	}
	if($this->input->post('empDept')!='')
	{
		$addsql .= " and e.empDept='".$this->input->post('empDept')."' ";
	}
	if($this->input->post('designation'))
	{
		$addsql .= " and de.id=".$this->input->post('designation');
	}
	if($this->input->post('jobLocation')!='')
	{
		$addsql .= " and e.jobLocation='".$this->input->post('jobLocation')."' ";
	}
	if($this->input->post('status')!='')
	{
		$addsql .= " and e.status='".$this->input->post('status')."' ";
	}
	if($this->input->post('state')!='')
	{
		$addsql .= " and s.State_Id='".$this->input->post('state')."' ";
	}
	if($this->input->post('region')!='')
	{
		$addsql .= " and r.id='".$this->input->post('region')."' ";
	}

		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select  e.empId,e.empEmailOffice,e.status,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ, e.empEmailPersonal, de.name as desination,r.name as region,s.State_Name as state,c.cityName as jobLocation, sa.inhandSalary, sa.ctc, sa.grossSalary, sa.basic, sa.hra, sa.specialAllow, sa.conveyance, sa.medicalAllow, sa.fuelReimbursement, sa.variable, sa.statutoryBonus, sa.esi, sa.contributionOfesi, sa.pf, sa.pfEmployer, sa.pfadminCharge  from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLE_SALARYBREAKUP." sa on e.empId=sa.empId
		where 1=1 ".$addsql." and e.status='1'";
		//echo $sql;die; 
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
	
	
	function incrementType()
	{
		$sql = "select * from tbl_increment_type where 1=1 ";
		$result = $this->db->query($sql)->result_array();
		return $result;
		
	}
	function incrementNo()
	{
		$sql = "select * from tbl_increment_no where 1=1 ";
		$result = $this->db->query($sql)->result_array();
		return $result;
		
	}
	function getLastincrement($id)
	{
	$sql ="select s.*, DATE_FORMAT(s.effectiveFrom,'%d-%b-%Y') effectiveFrom, d.name as designation,i.ctc as oldctc,if(ISNULL(od.name),e.empdesination,od.name) as oldDesination, st.State_Id as state, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, dept.name as department, DATE_FORMAT(e.empDOJ,'%d-%b-%Y') joiningDate, r.name as region,st.State_Name as state,c.cityName as jobLocation  from ".TABLE_SALARYBREAKUP." s
				left join ".TABLE_EMP." e on s.empId=e.empId
				left join tbl_increment i on e.empId=i.empId
				Left Join ".TABLE_DEPT." dept on e.empDept=dept.id
				left join ".TABLE_DESIGNATION_MASTER."  d on e.empDesination=d.id
				left join ".TABLE_DESIGNATION_MASTER."  od on i.oldDesignation=od.id
				Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
				Left Join ".TABLE_STATE." st on c.state=st.State_Id
				Left Join ".TABLE_REGION." r on st.region=r.id
				where s.empId=".$id." ";
	$result = $this->db->query($sql)->result_array();
	return $result =$result['0'];
	}
	function get_increment_details($eid)
	{
		 $sql = "select a.*, DATE_FORMAT(a.effectiveFrom,'%d %M, %Y') as  effectiveFrom, nd.name as newDesignation, od.name as oldDesignation, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, DATE_FORMAT(e.empDOJ,'%d-%b-%Y') as joiningDate, de.name designation, d.name as department, r.name as region, s.State_Name as state,c.cityName as jobLocation  from ".TABLE_INCREMENT." a 
		left join ".TABLE_DESIGNATION_MASTER."  nd on a.newDesignation=nd.id
		left join ".TABLE_DESIGNATION_MASTER."  od on a.oldDesignation=od.id
		left join ".TABLE_EMP."  e on a.empId=e.empId
		left join ".TABLE_DESIGNATION_MASTER."  de on e.empDesination=de.id
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		where 1=1 and a.empId='".$eid."' order by a.id desc limit 0,2 ";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}	
}
