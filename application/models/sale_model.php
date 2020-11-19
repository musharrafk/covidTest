<?php
class sale_model extends parent_model {
	var $base_tbl = TABLE_SALES_TARGET;
	var $u_column = 'id';


	function get_sale_details($resultType='G')
	{
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

		

		$sql = "select s.*,DATE_FORMAT(s.month,'%b %Y')as month,concat(e.empTitle,' ',e.empFname,' ',e.empLname,'(',s.empId,')') as empName,  de.name as desination, r.name as region,st.State_Name as state,c.cityName as city  from ".$this->base_tbl." s
				LEFT JOIN ".TABLE_EMP." e on s.empId=e.empId
				Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
				Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
				Left Join ".TABLE_STATE." st on c.state=st.State_Id
				Left Join ".TABLE_REGION." r on st.region=r.id
			 where 1=1 ".$addSql;
		
		//echo $sql; exit;
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		//pre($result);
		return $result;
	}

}
