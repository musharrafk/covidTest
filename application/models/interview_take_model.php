<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class interview_take_model extends parent_model {
    
    /**
     * assign to the interviwer for the interview
     */
    public function insertInterviewAssign($data)
    {
        error_reporting(E_ALL);
        $this->db->insert('interview_take',$data);
        return ($this->db->affected_rows() != 1) ? false : true;
    }

    
    /** 
     * Interview Info
     */
    public function interviewTaken($candId = '')
    {
        $empId = $this->session->userdata('empId');
        // echo $empId;
        $this->db->select('*');
        $this->db->from('interview_take');
        $this->db->where('interviewerId',$empId);
        if(!empty($candId)){
        $this->db->where('candId',$candId);   
        }
        $res = $this->db->get()->result_array();
        return $res;
    }
    /** 
     * get all interview detials
     */
    public function getInterviewsDetails($resultType='G')
    {
        $addSql = "  "; 
        
        $uId = $this->session->userdata('empId');     
        
		if($this->input->post('filters')!='') {   
		     $filterResultsJSON = json_decode($this->input->post('filters'));
			 $filterArray           = get_object_vars($filterResultsJSON);			
			 if(!empty($filterArray['rules'])) {
			     $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			 }
		}	

        $sql = "Select it.*,cmd.mrfId,cmd.candId,
                it.id as interviewID,
                cmd.mobile,cmd.currentLocation,
                cmd.resume,cmd.isApprovedByManager,
                cmd.isVenueAdded,
                cmd.interviewVenue,
                cmd.designation,
                CONCAT_WS('',cmd.expYear,'.',cmd.expMonth,' yrs') as expYear,
                date_format(cmd.interviewDate,'%d %b,%Y %h:%i %p') as interviewDate, 
                
                CONCAT_WS(' ',cmd.empFname,cmd.empMname,cmd.empLname) AS candidateName,
                CONCAT_WS(' ',em.empFname,em.empMname,em.empLname) AS managerName,
                ir.roundName
                from interview_take it
                left join candidate_mrf_details cmd on it.candId = cmd.candId 
                left join tbl_emp_master em on it.managerId = em.empId
                left join interview_round ir on it.roundId = ir.roundId
                where it.interviewerId='".$uId."' ".$addSql.
                "ORDER BY it.id DESC";
           // echo $sql; exit;
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


    public function getInterviewsDetailsTakenList($resultType='G',$candId)
    {
        $addSql = "  "; 
        
        $uId = $this->session->userdata('empId');     
        
		if($this->input->post('filters')!='') {   
		     $filterResultsJSON = json_decode($this->input->post('filters'));
			 $filterArray           = get_object_vars($filterResultsJSON);			
			 if(!empty($filterArray['rules'])) {
			     $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			 }
		}	

        $sql = "Select it.*,cmd.mrfId,cmd.candId,
                it.id as interviewID,
                cmd.mobile,cmd.currentLocation,
                cmd.resume,cmd.isApprovedByManager,
                cmd.isVenueAdded,
                cmd.interviewVenue,
                cmd.designation,
                CONCAT_WS('',cmd.expYear,'.',cmd.expMonth,' yrs') as expYear,
                date_format(cmd.interviewDate,'%d %b,%Y %h:%i %p') as interviewDate, 
                
                CONCAT_WS(' ',cmd.empFname,cmd.empMname,cmd.empLname) AS candidateName,
                CONCAT_WS(' ',em.empFname,em.empMname,em.empLname) AS managerName,
                CONCAT_WS(' ',emp.empFname,emp.empMname,emp.empLname) AS interviewerName,
                ir.roundName
                from interview_take it
                left join candidate_mrf_details cmd on it.candId = cmd.candId 
                left join tbl_emp_master em on it.managerId = em.empId
                left join tbl_emp_master emp on it.interviewerId  = emp.empId
                left join interview_round ir on it.roundId = ir.roundId
                where cmd.candId = $candId  ".$addSql.
                "ORDER BY it.id DESC";
           // echo $sql; exit;
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

    public function assignedInterviewTaken($candId)
    {
        $this->db->select('it.*,cifl.id as ciflId');
        $this->db->select('ir.roundName');
        $this->db->select('tmd.name as designationName,');
        $this->db->select("CONCAT((emp.empFname),(' '),(emp.empMname),(' '),(emp.empLname)) as interviewerName");
        $this->db->from('interview_take it');
        $this->db->join('candidate_iaf_form cif','it.candId = cif.candId','left');
        $this->db->join('candidate_selection_round csr','cif.iafId = csr.iafId and it.interviewerId = csr.takenBy and it.roundId = csr.roundId','left');        
        $this->db->join('candidate_iaf_form_list cifl','csr.csrId = cifl.csrId','left'); 
        $this->db->join('tbl_emp_master emp','emp.empId = it.interviewerId','left');
        $this->db->join('tbl_mst_designation tmd','emp.empDesination = tmd.id','left');
        $this->db->join('interview_round ir','ir.roundId=it.roundId','left');
        // if(!empty($candId)){
        $this->db->where('it.candId',$candId);   
       // $this->db->where('it.interview',2);  
        // }       
        $res = $this->db->get()->result_array();
       
        return $res;
    }



    public function interviewRoundStatus($candId)
    {
        $this->db->select('csr.*');
        $this->db->select('ir.roundName');
        $this->db->select("CONCAT((emp.empFname),(' '),(emp.empMname),(' '),(emp.empLname)) as interviewerName");
        $this->db->select("cifl.id as ciflId");
        $this->db->from('candidate_iaf_form as cif');
        $this->db->join('candidate_selection_round csr','cif.iafId = csr.iafId','left');
        $this->db->join('candidate_iaf_form_list cifl','csr.csrId = cifl.csrId','left');        
        $this->db->join('interview_round ir','csr.roundId=ir.roundId','left');
        $this->db->join('tbl_emp_master emp','csr.takenBy = emp.empId','left'); 
        $this->db->where('cif.candId',$candId); 
        $this->db->order_by('csr.csrId'); 
        $res = $this->db->get()->result_array();        
        return $res;
    }

    public function existInterviewAssigned($candId,$mrfId,$assignEmpId,$roundId,$managerId)
    {
        $this->db->select();
        $this->db->from('interview_take');
        $this->db->where('candId',$candId);
        $this->db->where('mrfId',$mrfId);
        $this->db->where('interviewerId',$assignEmpId);
        $this->db->where('roundId',$roundId);
        $this->db->where('managerId',$managerId);
        $res = $this->db->get()->num_rows();
        // $res = $this->db->last_query();
        return $res;
    }


    public function getInterviewerInfo($candId,$interviewID)
    {
        $this->db->select('it.*,emp.empId');
        $this->db->select('ir.roundName');
        $this->db->select('tmd.name as designationName,tmd1.name as jobDesignationName');
        $this->db->select("CONCAT((emp.empFname),(' '),(emp.empMname),(' '),(emp.empLname)) as interviewerName");
        $this->db->from('interview_take it');
        $this->db->join('tbl_emp_master emp','emp.empId = it.interviewerId','left');
        $this->db->join('tbl_mst_designation tmd','emp.empDesination = tmd.id','left');
        $this->db->join('job_mrf jm','it.mrfId = jm.job_id','left');
        $this->db->join('tbl_mst_designation tmd1','jm.proposedRoleId = tmd1.id','left');
        $this->db->join('interview_round ir','ir.roundId=it.roundId','left');
        // if(!empty($candId)){
        $this->db->where('it.id',$interviewID);   
        // $this->db->where('it.interview',1);   
        // }
        $res = $this->db->get()->result_array();
        return $res;
    }


    public function getInterviewerInfoIafList($candId,$id)
    {       
        $this->db->select('emp.empId');
        $this->db->select('ir.roundName,csr.*');
        $this->db->select('tmd.name as designationName,tmd1.name as jobDesignationName');
        $this->db->select("CONCAT((emp.empFname),(' '),(emp.empMname),(' '),(emp.empLname)) as interviewerName");       
        $this->db->from('candidate_iaf_form_list cifl');
        $this->db->join('candidate_selection_round csr','cifl.csrId = csr.csrId','left');
        $this->db->join('candidate_mrf_details cmd','cifl.candId = cmd.candId','left');
        $this->db->join('job_mrf jm',' cmd.mrfId = jm.job_id','left');
        $this->db->join('tbl_mst_designation tmd1','jm.proposedRoleId = tmd1.id','left');         
        $this->db->join('tbl_emp_master emp','emp.empId = csr.takenBy','left');
        $this->db->join('tbl_mst_designation tmd','emp.empDesination = tmd.id','left');     
        $this->db->join('interview_round ir','ir.roundId=csr.roundId','left');      
        $this->db->where('cifl.id',$id); 
        $res = $this->db->get()->result_array();    
         
        return $res;
    }

    
	
}