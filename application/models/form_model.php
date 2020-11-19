<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class form_model extends parent_model {

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
	function get_form_details($id=0, $resultType='G')
	{
		$addsql = '';
		if($id > 0)
		{
			$addsql .= " and id=".$id;
		}
		if($this->input->post('filters')!='')
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select f.*,date_format(f.isCreated,'%d-%m-%Y') as addedon, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as addedby from ".TABLE_FORM." f left join ".TABLE_EMP." e on f.addedBy=e.empId  where 1=1 ".$addsql."";
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