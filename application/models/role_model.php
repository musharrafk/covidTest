<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class role_model extends parent_model {
	var $base_tbl = TABLE_ROLE;
	var $u_column = 'roleId';
	
	function get_details($resultType='G')
	{
		$addSql = "";
		
		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}
		$sql = "select * from ".$this->base_tbl." where 1=1 AND  roleId !='1' ".$addSql;
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
	function getRole($rid)
	{
	$sql ="select * from ".$this->base_tbl." where roleId=".$rid;
	 return $result = $this->db->query($sql)->result_array();
	}
	function roleList()
	{
	$sql ="select * from ".$this->base_tbl." where roleId!=1";
	 return $result = $this->db->query($sql)->result_array();
	}

}