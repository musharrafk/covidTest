<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class payroll_model extends parent_model
{
	function decodeFilters($filters)
	{
		$sql = ' (';
		$objJson = json_decode($filters);
		foreach($objJson->{'rules'} as $rules)
		{
			if($rules->{'field'}=='empName')
			{
				$sql .= ' ( ';
				$expKey = explode(' ',$rules->{'data'});
				for($k=0; $k<count($expKey); $k++)
				{
					if($k>0)
					{
						$sql .= " or ";
					}
					$sql  .= "  e.empTitle like '%".$expKey[$k]."%'";
					$sql  .= " or e.empFname like '%".$expKey[$k]."%'";
					$sql  .= " or e.empLname like '%".$expKey[$k]."%'";
				}
				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
			}
			else
			{
				$sql .= $rules->{'field'}.' ';
				$sql .= $this->decodeGridOP($rules->{'op'},$rules->{'data'}).' ';
				$sql .= $objJson->{'groupOp'}.' ';
			}
		}
		$sql = rtrim($sql, $objJson->{'groupOp'}.' ');
		return $sql.') ';
	}
	function get_monthlySalaryAttendance_details($resultType='G', $param1, $param2)
	{
		if ($param2 > '0')
		{
			$param2 = " and (sal.year)='" . $param2 . "'";
		}
		else
		{
			$param2 = ' ';
		}
		if ($param1 > '0')
		{
			$param1 = " and (sal.month)='" . $param1 . "'";
		}
		else
		{
			$param1 = ' ';
		}
		if($this->input->post('filters')!='')
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select sal.*,concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName from ".TABLE_MONTHLY_SALARY." sal  LEFT JOIN ".TABLE_EMP." e on e.empId=sal.empId where 1=1 " . $addsql . " " . $param1 . "" . $param2 . " ";
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
	function get_rent_api_details($resultType='G', $param1, $param2)
	{
		//echo $param2;die;
		$year=date('Y');
		$year= FINANCIAL_YEAR."%";
		if($this->input->post('period')!='')
		{
			$period = " and (rent.period)='" . $this->input->post('period') . "'";
		}
		else
		{
			$period = " and (rent.period) like ('".$year."')";
		}
		if($this->input->post('filters')!='')
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select rent.*,concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,DATE_FORMAT(rent.periodFrom,'%d %b, %Y') as  periodFrom,DATE_FORMAT(rent.periodTo,'%d %b, %Y') as  periodTo,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName  from ".TABLE_RENT_API." rent
		LEFT JOIN ".TABLE_EMP." e on e.empId=rent.empId 
		LEFT JOIN ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id 
		where 1=1 " . $addsql . " and  e.empId!=10000 and can.empType=2 " . $period . ""; 
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
	function get_rent_api_period()
	{
		return $this->db->query("select distinct period from ".TABLE_RENT_API." order by period desc")->result_array();
	}
	function rentdataFound($empId, $period)
	{
	$sql ="select empId from ".TABLE_RENT_API." where empId='".$empId."' and  period='".$period."'";
	
		return $this->db->query($sql)->row();
	}
	function investmentdataFound($empId, $period)
	{
	$sql ="select empId from ".TABLE_INVESTMENT_DECLARE." where empId='".$empId."' and  period='".$period."'";
	
		return $this->db->query($sql)->row();
	}
	
	function get_investmentDeclaration_details($resultType='G', $param1, $param2)
	{
		$year=date('Y');
		$year= $year."%";
		if($this->input->post('period')!='')
		{
			$period = " and (inv.period)='" . $this->input->post('period') . "'";
		}
		else
		{
			$period = " and (inv.period)like ('".$year."')";
		}
		if($this->input->post('filters')!='')
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select inv.*,concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,DATE_FORMAT(inv.periodFrom,'%d %b, %Y') as  periodFrom,DATE_FORMAT(inv.periodTo,'%d %b, %Y') as  periodTo  from ".TABLE_INVESTMENT_DECLARE." inv  LEFT JOIN ".TABLE_EMP." e on e.empId=inv.empId where 1=1 " . $addsql . " " . $period . "";
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
	function get_investmentDeclaration_period()
	{
		return $this->db->query("select distinct period from ".TABLE_INVESTMENT_DECLARE." order by period desc")->result_array();
	}
	function attendanceFound($empId, $month, $year)
	{
		$sql ="select empId from tbl_monthly_salary where empId='".$empId."' and  month='".$month."' and year='".$year."'";
		return $this->db->query($sql)->row();
	}
	function get_api_log_details($resultType='G')
	{
		
		if($this->input->post('filters')!='')
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select a.*,concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,DATE_FORMAT(a.uploadedOn,'%d %b, %Y' ' %h:%i %p') as  uploadedOn  from tbl_api_log a  LEFT JOIN ".TABLE_EMP." e on e.empId=a.uploadedBy where 1=1 " . $addsql . " " . $period . "";
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



	// 16-jan
	function get_investmentTypes($resultType='G')
	{
		
		if($this->input->post('filters')!='')
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select * from tbl_investment_types where 1=1 " . $addsql . " ";
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
	


	// 08-03-2018 // 
	function get_monthly_rent_api_details($resultType='G', $param1, $param2)
	{
		// echo 'yes'; die;
		//echo $param2;die;
		$year=date('Y');
		$year= $year."%";
		if($this->input->post('period')!='')
		{
			$period = " and (rent.period)='" . $this->input->post('period') . "'";
		}
		else
		{
			$period = " and rent.period  = '".FINANCIAL_YEAR."' ";
		}
		if($this->input->post('filters')!='')
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		//SELECT MONTHNAME(STR_TO_DATE(06 , '%m')) as monthName
		  $sql = "select c.cityName as jobCityName , s.State_Name , r.name as regionName , rent.* , concat(e.empTitle,' ',e.empFname,' ',e.empLname)  as empName ,  monthname(concat(periodYear,'-',periodMonth,'-01')) as monthName from tbl_rent_api_monthly rent  
		  LEFT JOIN ".TABLE_EMP." e on e.empId=rent.empId 
		  LEFT JOIN tbl_mst_city c on c.cityId=e.jobLocation 
		  LEFT JOIN tbl_mst_state s on s.State_Id=c.state 
		  LEFT JOIN tbl_region r on r.id=s.region 
		  where 1=1 " . $addsql . " " . $period . "";  
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
}