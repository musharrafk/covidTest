<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class policy_model extends parent_model {
var $base_tbl = TABLE_POLICY;
var $base_tbl1 = TABLE_CATEGORY;
	var $u_column = 'id';


	function get_details($resultType='G')
	{
		$addSql = '';

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

		//$sql = "select p.*,c.categoryName from ".$this->base_tbl." p LEFT JOIN ".$this->$base_tbl1." c on p.Category=c.catId where 1=1 ".$addSql;
		 $sql = "select p.Id,p.policyTitle,c.categoryName from ".TABLE_POLICY." p LEFT JOIN ".TABLE_CATEGORY." c on p.Category=c.catId where 1=1 ".$addSql;
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
function policyList()
{

		$sql = "select p.id,p.policyTitle,c.categoryName from ".TABLE_POLICY." p LEFT JOIN ".TABLE_CATEGORY." c on p.Category=c.catId where 1=1 ";
		$result=$this->db->query($sql)->result_array();
		return $result;
	
}


}