<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class parent_model extends CI_Model {

	public function __construct(){
		parent::__construct();
    }

	function result_grid_array($sql)
	 {
		// print_r($this->input->post()); die;
		$sort_by          = filter_mixed($this->input->post("sidx", TRUE ));
		$sort_direction   = filter_characters($this->input->post("sord", TRUE ));
		$num_rows         = filter_numeric($this->input->post("rows", TRUE ));
		$data->page       = filter_numeric($this->input->post("page", TRUE ));
		$data->records    = $this->db->query($sql)->num_rows();
		$data->total      = ceil($data->records/$this->input->post("rows", TRUE ));

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
			else $v.= "'".addslashes(trim($val))."', ";
		}

		$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";
		// echo $q;die;

		if($this->db->query($q))
		{
			return $this->db->insert_id();
		}
		else
		{
			return false;
		}

	}
function query_insert1($table, $data)
	{
		$q="INSERT INTO `".$table."` ";
		$v=''; $n='';

		foreach($data as $key=>$val) {
			$n.="`$key`, ";
			if(strtolower($val)=='null') $v.="NULL, ";
			elseif(strtolower($val)=='now()') $v.="NOW(), ";
			elseif(strpos(strtolower($key), "password") !== false) $v.="'".md5($val)."', ";
			else $v.= "'".addslashes(trim($val))."', ";
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
		else $q.= "`$key`='".addslashes(trim($val))."', ";
		}

		$q = rtrim($q, ', ') . ' WHERE '.$where.';';
		 // echo $q;die; 
		return $this->db->query($q);
	}
function query_update1($table, $data, $where='1')
	{
		$q="UPDATE `".$table."` SET ";

		foreach($data as $key=>$val) {
		if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
		elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
		elseif(strpos(strtolower($key), "password") !== false) $q.="`$key` =('".md5($val)."'), ";
		elseif(preg_match("/^increment\((\-?\d+)\)$/i",$val,$m)) $q.= "`$key` = `$key` + $m[1], ";
		else $q.= "`$key`='".addslashes(trim($val))."', ";
		}

		$q = rtrim($q, ', ') . ' WHERE '.$where.';';
		 echo $q;die;
		return $this->db->query($q);
	}
	function getCountries()
	{
		$sql="select * from ".TABLE_COUNTRY." where countryId=99 AND 1=1 order by countryName ";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	function getDepartment()
	{
		$sql="select * from ".TABLE_DEPT." where department_status='1' order by name";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	
	function getModule($module)
	{
		$sql="select * from ".TABLE_MODULE." where moduleType=".$module." AND status=1 Order By name";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	
	function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

	
	function decodeFilters($filters)
	{
		$sql = ' (';
		$objJson = json_decode($filters);
			
		foreach($objJson->{'rules'} as $rules)
		{
		if($rules->{'field'}!="")
		   {
		   foreach ($objJson->{'rules'} as $i=>$rules)
		       {
			   if($rules->{'field'}=='e.empFname')
			{
			$sql .= ' ( ';
				$expKey = explode(' ',filter_values($rules->{'data'}));
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
		 /*   if( count($objJson->{'rules'})>1)
				 { */
				 
				$sql .= $objJson->{'groupOp'}.' ';
				/* } */
			unset($objJson->{'rules'}[$i]);
			}
			
			if($rules->{'field'}=='range.attendanceDatetime')
			{
			$start="";
			$end="";
			
			$sql .= ' ( ';
				$expKey = explode('-',filter_values($rules->{'data'}));			
				$start = filter_date((date('Y-m-d',strtotime($expKey[0]))));
				$end   = filter_date((date('Y-m-d',strtotime($expKey[1]))));
				 $sql  .= "DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d') >= '".$start."'" ;
				$sql  .= "and  DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d') <= '".$end."'"; $sql .= ' ) ';
				 /* if( count($objJson->{'rules'})>1)
				 { */
				$sql .= $objJson->{'groupOp'}.' ';
				/* } */
				unset($objJson->{'rules'}[$i]);
			}
			
			//employee attendance hr part
			if($rules->{'field'}=='rangehr.attendanceDate')
			{
			$start="";
			$end="";
			$sql .= ' ( ';
				$expKey = explode('-',filter_values($rules->{'data'}));
				
				$start=(date('Y-m-d',strtotime($expKey[0])));
				$end=(date('Y-m-d',strtotime($expKey[1])));
				 $sql  .= "DATE_FORMAT(attendanceDate,'%Y-%m-%d') >= '".$start."'" ;
				$sql  .= "and  DATE_FORMAT(attendanceDate,'%Y-%m-%d') <= '".$end."'"; $sql .= ' ) ';
				 /* if( count($objJson->{'rules'})>1)
				 { */
				$sql .= $objJson->{'groupOp'}.' ';
				/* } */
			
				unset($objJson->{'rules'}[$i]);
				
			}
			
			//employee attendance hr part
			 
			}
			foreach ($objJson->{'rules'} as $rules)
		       {
			$sql .= $rules->{'field'}.' '; // field name
			$sql .= $this->decodeGridOP($rules->{'op'},filter_values($rules->{'data'})).' '; // op, val
			$sql .= $objJson->{'groupOp'}.' '; // and, or
			}
			
			$sql = rtrim($sql, $objJson->{'groupOp'}.' ');
		return $sql.') ';
			}
			}
		

	 echo $sql;
die;	 
	}

	function decodeGridOP($op, $val)
	{
	    
		if($op=='eq')
		{
			return "='".$val."'";
		}
		elseif($op=='ne')
		{
			return "!='".$val."'";
		}
		elseif($op=='lt')
		{
			return "<'".$val."'";
		}
		elseif($op=='le')
		{
			return "<='".$val."'";
		}
		elseif($op=='gt')
		{
			return ">'".$val."'";
		}
		elseif($op=='ge')
		{
			return ">='".$val."'";
		}
		elseif($op=='bw')
		{
			return "LIKE ('".$val."%')";
		}
		elseif($op=='bn')
		{
			return "NOT LIKE ('".$val."%')";
		}
		elseif($op=='in')
		{
			return "IN (".$val.")";
		}
		elseif($op=='ni')
		{
			return "NOT IN (".$val.")";
		}
		elseif($op=='ew')
		{
			return "LIKE ('%".$val."')";
		}
		elseif($op=='en')
		{
			return "NOT LIKE ('%".$val."')";
		}
		elseif($op=='cn')
		{
			return "LIKE ('%".$val."%')";
		}
		elseif($op=='nc')
		{
			return "NOT LIKE ('%".$val."%')";
		}
	}

	function delete_rec($table, $field, $value)
	{
		$sql = "delete from ".$table." where ".$field."=".$value."";
		$this->db->query($sql);
		return $this->db->affected_rows();
		
	}
	function emptyTable($table)
	{
	$this->db->truncate($table);
	}
	function holidayList($id)
	{
	$sql="select holidayDate,holiday from ".TABLE_HOLIDAY." where location=".$id." order by holidayDate";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	function duplicateEmail($email)
	{
	$sql="select id from ".TABLE_CANDIDATE." where email='".$email."'";
		$result=$this->db->query($sql)->result_array();
		return $result['0'];
	}
	
	function regionList()
	{
	$sql="select * from ".TABLE_REGION."  order by name";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	
}
