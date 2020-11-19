<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class apiexport_model extends parent_model
{
	var $base_tbl = TABLE_MONTHLY_SALARY;
	var $u_column = 'id';
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
	function get_apiExport_details($resultType='G', $param1, $param2)
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
		$sql = "select sal.*,concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName from ".TABLE_MONTHLY_SALARY." sal  LEFT JOIN ".TABLE_EMP." e on e.empId=sal.empId where 1=1 " . $addSql . " " . $param1 . "" . $param2 . " ";

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
		if($this->input->post('period')!='')
		{
			$period = " and (rent.period)='" . $this->input->post('period') . "'";
		}
		if($this->input->post('filters')!='')
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select rent.*,concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,DATE_FORMAT(rent.periodFrom,'%d %b, %Y') as  periodFrom,DATE_FORMAT(rent.periodTo,'%d %b, %Y') as  periodTo  from ".TABLE_RENT_API." rent  LEFT JOIN ".TABLE_EMP." e on e.empId=rent.empId where 1=1 " . $addSql . " " . $period . "";
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
	function get_period()
	{
		return $this->db->query('select distinct period from tbl_rent_api order by period desc')->result_array();
	}
}