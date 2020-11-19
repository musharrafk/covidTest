<?php

class cms_model extends parent_model {



	function get_details($resultType='G')
	{
		$addSql = '';

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

		$sql = "select * from ".$this->base_tbl." where 1=1 ".$addSql;
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

function cmsDesc($id)
	{
		$sql = "select page_description from tbl_cms_content where page_id='".$id."'";
		return $this->db->query($sql)->row();
	}

}