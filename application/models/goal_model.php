<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class goal_model extends parent_model {
	
    function fetch_cmig_list($resultType='G') {
        $addSql = "  ";
        $uId = $this->session->userdata('admin_id');
            
        if($this->input->post('filters')!='')	{   
            $filterResultsJSON = json_decode($this->input->post('filters'));
            $filterArray = get_object_vars($filterResultsJSON);               			 
            if(!empty($filterArray['rules']))
            {
                $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
            }
         }
       
        $sql = "select gc.id,g.id as cmigId,gc.company_id,g.goal,clfg.company_name,gc.goal_content,gc.year,gc.quarter,fs_content,gc.department,tmd.name as departmentName,DATE_FORMAT(gc.created_at,'%d-%b-%Y') as createdDate from goal_content as gc  inner join goals as g on gc.goal_id = g.id  
            	left join company_list_for_goals as clfg on gc.company_id = clfg.id  left join tbl_mst_dept as tmd on gc.department = tmd.id where g.type =1 ". $addSql ." group by gc.goal_id,gc.year,gc.company_id";
            
			
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

    function fetch_dmig_list($resultType='G',$deptId) {
        $addSql = "  ";
        $uId = $this->session->userdata('admin_id');
            
        if($this->input->post('filters')!='')	{   
            $filterResultsJSON = json_decode($this->input->post('filters'));
            $filterArray = get_object_vars($filterResultsJSON);               			 
            if(!empty($filterArray['rules']))
            {
                $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
            }
         }
       
        $sql = "select gc.id,g.id as cmigId,g.goal,gc.goal_content,gc.year,gc.quarter,gc.fs_content,gc.department,tmd.name as departmentName,DATE_FORMAT(gc.created_at,'%d-%b-%Y') as createdDate from goal_content as gc  inner join goals as g on gc.goal_id = g.id  
            	left join tbl_mst_dept as tmd on gc.department = tmd.id where gc.department = $deptId and g.type = 2  ". $addSql." group by gc.goal_id,gc.year " ;
             
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
   
   function getCmigList(){
        $this->db->select("*");
        $this->db->from('goals');
        $this->db->where('type',1);
        return $this->db->get()->result_array(); 
   }
   
     function getDmigList(){
        $this->db->select("*");
        $this->db->from('goals');
        $this->db->where('type',2);
        return $this->db->get()->result_array(); 
   }

   function getCmigFsList($id = '',$quarter,$year,$companyId = ''){
	   
        $this->db->select("g.id,g.goal,gf.id as fs_id,gf.fs,gc.goal_content,gc.fs_content",false);     
		$this->db->from('goals as g');	
		$this->db->join('goal_fs as gf','g.id = gf.g_id','LEFT');
		$this->db->join('goal_content as gc',"g.id = gc.goal_id and gf.id = gc.fs_id and gc.year= $year and gc.quarter= $quarter and gc.company_id = $companyId",'LEFT');
        if($id){
		  $this->db->where('g.id',$id);
		} else {
		  $this->db->where('g.id',1);
		}		
        $this->db->where('g.type',1);		
        return $this->db->get()->result_array();        
   }
   
    function getDmigFsList($id = '',$quarter,$year,$deptId){
       
		$this->db->select("g.id,g.goal,gf.id as fs_id,gf.fs,gc.goal_content,gc.fs_content",false);     
		$this->db->from('goals as g');	
		$this->db->join('goal_fs as gf','g.id = gf.g_id','LEFT');
		$this->db->join('goal_content as gc',"g.id = gc.goal_id and gf.id = gc.fs_id and gc.year= $year and gc.quarter= $quarter and gc.department=$deptId",'LEFT');
        if($id){
		  $this->db->where('g.id',$id);
		} else {
		  $this->db->where('g.id',4);
		}		
        $this->db->where('g.type',2);		
        return $this->db->get()->result_array();        
   }
   
   function get_department_details_of_emp($empId){
		$sql = "select dept.* from ".TABLE_EMP." e left join ".TABLE_DEPT." dept on e.empDept= dept.id where e.empId=$empId";      
        $result = $this->db->query($sql)->result_array();
		return $result = $result['0'];
	}
	
	
	function checkCmgDataExist($id,$year,$quarter,$companyId){
        $this->db->select("gc.goal_content,gc.fs_content",false);
		$this->db->from('goal_content as gc');
		$this->db->where('gc.goal_id',$id);
		$this->db->where('gc.year',$year);
        $this->db->where('gc.quarter',$quarter);
        $this->db->where('gc.company_id',$companyId);
        return $this->db->get()->result_array();        
    }
	
	function checkDmigDataExist($id,$year,$quarter,$deptId){
        $this->db->select("gc.goal_content,gc.fs_content",false);
		$this->db->from('goal_content as gc');
		$this->db->where('gc.goal_id',$id);
		$this->db->where('gc.year',$year);
        $this->db->where('gc.quarter',$quarter);
        $this->db->where('department',$deptId);   		
        return $this->db->get()->result_array();        
    }
	
	function deleteCmigData($id,$year,$quarter,$companyId){
		 $this ->db-> where('goal_id', $id);
		 $this->db->where('year',$year);
         $this->db->where('quarter',$quarter);
         $this->db->where('company_id',$companyId);
         $this ->db-> delete('goal_content');		
	}
	
	function deleteDmigData($id,$year,$quarter,$deptId){
		 $this ->db-> where('goal_id', $id);
		 $this->db->where('year',$year);
         $this->db->where('quarter',$quarter);
         $this->db->where('department',$deptId);         
         $this->db-> delete('goal_content');		
	}
    
	  function fetchAllCmigFsList($companyType,$quarter){
	    $year  =  date("Y");
        $this->db->select("g.id,g.goal,gf.id as fs_id,gf.fs,gc.goal_content,gc.fs_content",false);     
		$this->db->from('goals as g');	
		$this->db->join('goal_fs as gf','g.id = gf.g_id','LEFT');
		$this->db->join('goal_content as gc',"g.id = gc.goal_id and gf.id = gc.fs_id",'LEFT');		
        $this->db->where('g.type',1);
        $this->db->where('gc.year',$year);	
        $this->db->where('gc.quarter',$quarter);
        $this->db->where('gc.company_id',$companyType);	
        return $this->db->get()->result_array();        
   }
   
    function fetchAllDmigFsList($deptId,$quarter){
        $year  =  date("Y");
		$this->db->select("g.id,g.goal,gf.id as fs_id,gf.fs,gc.goal_content,gc.fs_content",false);     
		$this->db->from('goals as g');	
		$this->db->join('goal_fs as gf','g.id = gf.g_id','LEFT');
		$this->db->join('goal_content as gc',"g.id = gc.goal_id and gf.id = gc.fs_id",'LEFT');		
        $this->db->where('g.type',2);
        $this->db->where('gc.department',$deptId);
        $this->db->where('gc.quarter',$quarter);
		$this->db->where('gc.year',$year);
	
        return $this->db->get()->result_array();        
   }
   
   // api
   function getAllCmigFsData($year,$quarter,$compType){
	    $year  =  date("Y");
        $this->db->select("g.id,g.goal,gf.id as fs_id,gf.fs,gc.goal_content,gc.fs_content",false);     
		$this->db->from('goals as g');	
		$this->db->join('goal_fs as gf','g.id = gf.g_id','LEFT');
		$this->db->join('goal_content as gc',"g.id = gc.goal_id and gf.id = gc.fs_id",'LEFT');		
        $this->db->where('g.type',1);
        $this->db->where('gc.year',$year);	
        $this->db->where('gc.quarter',$quarter);
        $this->db->where('gc.company_id',$compType);			
        return $this->db->get()->result_array();        
   }
   
    function getAllDmigFsData($departmentId,$year,$quarter){
        $year  =  date("Y");
		$this->db->select("g.id,g.goal,gf.id as fs_id,gf.fs,gc.goal_content,gc.fs_content",false);     
		$this->db->from('goals as g');	
		$this->db->join('goal_fs as gf','g.id = gf.g_id','LEFT');
		$this->db->join('goal_content as gc',"g.id = gc.goal_id and gf.id = gc.fs_id",'LEFT');		
        $this->db->where('g.type',2);
		$this->db->where('gc.department',$departmentId);
		$this->db->where('gc.year',$year);
		$this->db->where('gc.quarter',$quarter);	
        return $this->db->get()->result_array();        
   }


    function  get_enable_feature(){
        $resultArray = [];
        $active = 0;
        $this->db->select("isActive");     
		$this->db->from('goal_enable_feature as gef');       		
        $resultArray = $this->db->get()->row_array();
        if(!empty($resultArray)){
            $active = $resultArray['isActive'];
        }  else {
            $active = $resultArray['isActive'];           
        }
        return $active;
    }

    function company_list_for_goals(){
        $this->db->select("*");
        $this->db->from('company_list_for_goals');
        $this->db->where('status',1);
        return $this->db->get()->result_array();
    }

}


?>