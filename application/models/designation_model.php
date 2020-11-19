<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class designation_model extends parent_model {

	public function __construct()
	{
		parent::__construct();
		error_reporting(1);
	}
/*
	var $designation_tbl = TABLE_DESIGNATION_MASTER;
	var $empBase_tbl = TABLE_EMP;*/

	/**
	 * [ajaxDesgination designation Name]
	 * @param  [type] $did [department Id]
	 * @return [type]      [array]
	 */
	function ajaxDesgination($did)
	{
		try {
			$this->db->distinct();
			$this->db->select(TABLE_EMP.'.empDept as departmentId');
			$this->db->select(TABLE_EMP.'.empDesination as designationId');
			$this->db->select(TABLE_DESIGNATION_MASTER.'.name as designationName');
			$this->db->from(TABLE_EMP);
			$this->db->join(TABLE_DESIGNATION_MASTER,TABLE_DESIGNATION_MASTER.'.id' .'='. TABLE_EMP.'.empDesination');
			$this->db->where(TABLE_EMP.'.empDept',$did);
			// $this->db->group_by(TABLE_EMP.'.empDesination');
			$result = $this->db->get()->result_array();
			// pre($result);
			// exit();
			return $result;
		} catch (Exception $e) {
			pre($e);die();
		}
	}

}

/* End of file designation_model.php */
/* Location: ./application/models/designation_model.php */