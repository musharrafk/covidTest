<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class special_leave_model extends parent_model {

	function check_pl_leave($empId,$leaveType)
	{
		$this->db->select('fromDate,toDate,regularizationDate');
		$this->db->order_by("id", "desc");
		$this->db->from("tbl_regularization");
		$this->db->where('requestFrom',$empId);
		$this->db->where('leaveType',$leaveType);
		$this->db->limit(1);
		$query = $this->db->get();
		
	}




}