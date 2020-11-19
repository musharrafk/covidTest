<?php

class location_model extends parent_model {
	var $base_tbl = TABLE_REGION_MASTER;
	var $u_column = 'region';


	function get_details($resultType='G')
	{
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

		$q = $this->db->query("select group_concat(State_Name) as grp from tbl_mst_state s inner join tbl_region_state rs on s.State_Id=rs.State_Id")->row();

		$sql = "select *, (select group_concat(State_Name) as grp from tbl_mst_state s inner join tbl_region_state rs on s.State_Id=rs.State_Id where rs.regionId=tbl_region_master.regionId) as grp from ".TABLE_REGION_MASTER."
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

		return $result;
	}

	
	function getAvilableState()
	{
		$state = $this->db->query("select group_concat(State_Id) as res from tbl_region_state")->row();
		if($state->res)
		{
			return $this->db->query("SELECT * FROM tbl_mst_state WHERE State_Id not in(".$state->res.")")->result_array();
		}
		else
		{
			return $this->db->query("SELECT * FROM tbl_mst_state ")->result_array();
		}
	}
	
	function getStates()
	{
			return $this->db->query("SELECT * FROM tbl_mst_state order by State_Name")->result_array();
	}
}
