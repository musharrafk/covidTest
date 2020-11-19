<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class form16_model extends parent_model {

	function decodeFilters($filters)
	{
		$sql = ' (';
		$objJson = json_decode($filters);
              //  pre($objJson);
		foreach($objJson->{'rules'} as $rules)
		{
			if($rules->{'field'}=='empName')
			{
                        //$sql .= 
                                /////////////
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

                            //$addSql .= " or ";
				}

				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
                                //////////
			}
			else
			{
			$sql .= $rules->{'field'}.' '; // field name
			$sql .= $this->decodeGridOP($rules->{'op'},$rules->{'data'}).' '; // op, val
			$sql .= $objJson->{'groupOp'}.' '; // and, or
		}
	}

	$sql = rtrim($sql, $objJson->{'groupOp'}.' ');
	return $sql.') ';
}

	function get_details($resultType='G', $param1)
	{

		$addsql = "";
		if($empId > 0)
		{
			$addsql .= " and f.empId=".$empId;
		}
		

		/////////////////////
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilterssalary($this->input->post('filters'));
		}
		if($param1){
			$addsql .="  and f.period='".$param1."' ";
		}
//
		$sql ="select f.*,concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, concat(em.empTitle,' ',em.empFname,' ',em.empLname) as 	addedBy, DATE_FORMAT(f.isCreated, '%d-%b-%Y') as isCreated from tbl_form16 f 
		left join  ".TABLE_EMP." e on f.empId=e.empId
		left join  ".TABLE_EMP." em on f.addedBy=em.empId
		WHERE 1=1  ".$addsql."";
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

	
	function getdetails($id)
	{
		$sql ="select * from ".TABLE_FORM16." WHERE id='".$id."'";
		$result = $this->db->query($sql)->result_array();
	return $result[0];
	}
	function form16list($empId)
	{
		$sql ="select * from ".TABLE_FORM16." WHERE empId='".$this->session->userdata('admin_id')."'";
		return $result = $this->db->query($sql)->result_array();

	}

}
