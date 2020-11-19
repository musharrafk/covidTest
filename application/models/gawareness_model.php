<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class gawareness_model extends parent_model {

    function getGeneralAwarenessList($search = '',$dept = '',$dateSearch = ''){
     
        $where = '';        
        if($search != '' && $dept != '' && $dateSearch != ''){
            $where = "g.name like '%".filter_var($search, FILTER_SANITIZE_STRING)."%' and g.deptId  = ".intval($dept)." and DATE_FORMAT(g.isCreated,'%Y-%m-%d')  = '".date("Y-m-d",strtotime($dateSearch))."'";
        } else if($search != '' && $dept != ''){
            $where = "g.name like '%".filter_var($search, FILTER_SANITIZE_STRING)."%' and g.deptId  = ".intval($dept);
        } else if($search != '' &&  $dateSearch != ''){
            $where = "g.name like '%".filter_var($search, FILTER_SANITIZE_STRING)."%' and DATE_FORMAT(g.isCreated,'%Y-%m-%d')  = '".date("Y-m-d",strtotime($dateSearch))."'";
        }  else if($dept != '' &&  $dateSearch != ''){
            $where = "DATE_FORMAT(g.isCreated,'%Y-%m-%b')  = '".date("Y-m-d",strtotime($dateSearch))."'";
        } else if($search != ''){
            $where = "g.name like '%".filter_var($search, FILTER_SANITIZE_STRING)."%'";
        } else if($dept != ''){
            $where = " g.deptId  = ".intval($dept);
        } else if($dateSearch != ''){
            $where = " DATE_FORMAT(g.isCreated,'%Y-%m-%d')  = '".date("Y-m-d",strtotime($dateSearch))."'";
        }        

        $this->db->select("g.*,concat(tem.empFname,' ',tem.empLname) as empName,tmd.name as departmentName",false);
        $this->db->from('general_awareness as g');
        $this->db->join('tbl_emp_master as tem','g.addedBy = tem.empId','LEFT');
        $this->db->join('tbl_mst_dept as tmd','g.deptId = tmd.id','LEFT');
        if($where !=''){
           $this->db->where("(".$where.")");
        }
        $this->db->where('g.isDisplayed',1);
        $this->db->order_by('g.id','desc');
        $sql = $this->db->get();

        $result = $sql->result_array();
      
        return $result;
    }
    
    function fetch_employee_department($empId){
		$sql = "select dept.* from ".TABLE_EMP." e left join ".TABLE_DEPT." dept on e.empDept= dept.id where e.empId=$empId";      
        $result = $this->db->query($sql)->result_array();
		return $result = $result['0'];
	}
}