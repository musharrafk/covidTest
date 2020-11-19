<?php

class project_model extends parent_model {

	function get_details($resultType='G')
	{
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

		//$q = $this->db->query("select group_concat(State_Name) as grp from tbl_mst_state s inner join tbl_region_state rs on s.State_Id=rs.State_Id")->row();

		$sql = "select p.*,c.name as client from tbl_project p inner join tbl_client c on p.clients=c.id
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

	function getProject($id)
	{
		$sql = "select * from tbl_project where id='".$id."'";
		return $this->db->query($sql)->result_array();
	}
}
?>