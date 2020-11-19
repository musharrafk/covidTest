<?php

class category_model extends parent_model {
	var $base_tbl = TABLE_CATEGORY;
	var $u_column = 'category_id';


	function get_details($resultType='G')
	{
		$addSql = "  ";

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

	function get_tree($parent_id = '0', $spacing = '', $exclude = '', $cat_tree_arr = '')
	{
		if (!is_array($cat_tree_arr))
		  $cat_tree_arr = array();

		$cat_query = "select category_id, category_name from " . $this->base_tbl . " where parent_id = '" . $parent_id . "' order by category_name";
		$resArr = $this->db->query($cat_query)->result_array();

		foreach ($resArr as $key=>$value) {
		  if ($exclude != $value['category_id'])
			$cat_tree_arr[] = array('id' => $value['category_id'], 'p_id' => $parent_id, 'text' => $spacing . $value['category_name']);

		  $cat_tree_arr = $this->get_tree($value['category_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $cat_tree_arr);
		}

		return $cat_tree_arr;

    }

	function get_tree_grid()
	{
		$result = $this->get_tree();
	
		$sort_by = $this->input->post("sidx", TRUE );
		$sort_direction = $this->input->post("sord", TRUE );
		$num_rows = $this->input->post("rows", TRUE );

		$data->page = $this->input->post("page", TRUE );
		$data->records = count($result);
		$data->total = ceil($data->records/$this->input->post("rows", TRUE ));

		

		$data->rows = $result;
		//echo  json_encode($data);
		return $data;
	}

	function category_list($resultType='G', $catFor='')
	{
		$addSql = " and parent_id='0' ";

		if($catFor!='')
		{
			$addSql .= " and category_for='".$catFor."' ";
		}


		$sql = "select cat_id,category_name from ".$this->base_tbl." where status='1' ".$addSql." order by category_name ";
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

	function category_for_array()
	{
		$arrCategoryFor = array(''=>'Select','A'=>'Articles','L'=>'Listings');
		return $arrCategoryFor;
	}

	function check_category_duplicate($id)
	{
		$this->db->select('cat_id');
		$this->db->where('category_name', $this->input->post("category_name"));
		
		if($id){
			$this->db->where('cat_id !=', $id);
		}
		$this->db->from($this->base_tbl);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->num_rows();
		/*if($query->num_rows() > 0){
		  $query->free_result();
		  return 1;
		} else {
			$query->free_result();
			return 0;
		}*/
	}

	function category_byid($id)
	{
		$this->db->select('*');
		$this->db->where('cat_id', $id);
		$this->db->from($this->base_tbl);
		$query = $this->db->get();
		return $query->result_array();
	}

	function getCatIDbyCatName($catName)
	{
		$this->db->select('cat_id');
		$this->db->where('category_name',$catName);
		$this->db->where('category_for','A');
		$this->db->from(TABLE_CATEGORY);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result[0]['cat_id'];
	}

	function listing($id)
	{
		
	}

function cat_breed($id,$exclude = '', $cat_tree_arr = '')
{
 





if (!is_array($cat_tree_arr))
		  $cat_tree_arr = array();

		$cat_query = "select * from tbl_category where id=".$id;
		$resArr = $this->db->query($cat_query)->result_array();

		foreach ($resArr as $key=>$value) {
		  if ($exclude != $value['id'])
			$cat_tree_arr[] = array('id' => $value['id'], 'name' => $value['category']);

		  $cat_tree_arr = $this->cat_breed($value['parent_id'],$exclude, $cat_tree_arr);
		}

		return $cat_tree_arr;
}

}