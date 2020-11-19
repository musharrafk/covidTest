<?php

class front_model extends CI_Model {
	
	public function __construct(){
		parent::__construct();
    }
	
	function result_grid_array($sql)
	{
		$sort_by = $this->input->post("sidx", TRUE );
		$sort_direction = $this->input->post("sord", TRUE );
		$num_rows = $this->input->post("rows", TRUE ); 
		
		$data->page = $this->input->post("page", TRUE );
		$data->records = $this->db->query($sql)->num_rows();
		$data->total = ceil($data->records/$this->input->post("rows", TRUE ));
		
		if((($num_rows * $data->page) >= 0 && $num_rows > 0))
		{
			if($sort_by)
			{
				$sql = 	$sql." order by ".$sort_by." ".$sort_direction; // set order
			}
			if($data->page != "all")
			{
				$sql = 	$sql." limit ".$num_rows*($data->page - 1).", ".$num_rows;
			}
		}
		//echo $sql;exit;

		$data->rows = $this->db->query($sql)->result_array();
		//echo  json_encode($data);
		return $data;
	}

	function listing($table, $col='', $colVal='')
	{
		$addSql = '';

		if($col!='' and $colVal!='')
		{
			$addSql .= " where ".$col."='".$colVal."'";
		}
		$sql = "select * from ".$table.$addSql;
		return $this->db->query($sql)->result_array();
	}

	function query_insert($table, $data) 
	{
		$q="INSERT INTO `".$table."` ";
		$v=''; $n='';

		foreach($data as $key=>$val) {
			$n.="`$key`, ";
			if(strtolower($val)=='null') $v.="NULL, ";
			elseif(strtolower($val)=='now()') $v.="NOW(), ";
			elseif(strpos(strtolower($key), "password") !== false) $v.="'".md5($val)."', ";
			else $v.= "'".$val."', ";
		}

		$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";
		//echo $q;die;

		if($this->db->query($q))
		{
			return $this->db->insert_id();
		}
		else
		{	
			return false;
		}

	}

	function query_update($table, $data, $where='1') 
	{
		$q="UPDATE `".$table."` SET ";

		foreach($data as $key=>$val) {
		if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
		elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
		elseif(strpos(strtolower($key), "password") !== false) $q.="`$key` =('".md5($val)."'), ";
		elseif(preg_match("/^increment\((\-?\d+)\)$/i",$val,$m)) $q.= "`$key` = `$key` + $m[1], ";
		else $q.= "`$key`='".$val."', ";
		}

		$q = rtrim($q, ', ') . ' WHERE '.$where.';';
		//echo $q;die;
		return $this->db->query($q);
	}
	
	
}