<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class mrf_model extends parent_model {
	
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
			
            
            if($rules->{'field'}=='tem1.empFname')
			{			
			    $sql .= ' ( ';
				$expKey = explode(' ',filter_values($rules->{'data'}));
				for($k=0; $k<count($expKey); $k++)
				{
					if($k>0)
					{
						$sql .= " or ";

					}


					$sql  .= "  tem1.empTitle like '%".$expKey[$k]."%'";
					$sql  .= " or tem1.empFname like '%".$expKey[$k]."%'";
					$sql  .= " or tem1.empMname like '%".$expKey[$k]."%'";
					$sql  .= " or tem1.empLname like '%".$expKey[$k]."%'";

                            //$addSql .= " or ";
				}

				$sql .= ' ) ';
		        $sql .= $objJson->{'groupOp'}.' ';
			    unset($objJson->{'rules'}[$i]);
			}


			if($rules->{'field'}=='mrfStatus')
			{
				$start="";
				$end="";
				$value  =  $rules->{'data'};
				
				if($value == 3) {
					$sql  .= "jm.status = 3 ";        
				}	
				else if($value == 0){
					$sql  .= " (jm.noOfPosition = jm.total_candidate_selected && jm.noOfPosition !=0 and  jm.total_candidate_selected !=0) or (jm.approvedStatus = 3)";

				} else if($value == 1) {
					$sql  .= "(jm.total_candidate_selected < jm.noOfPosition && jm.noOfPosition !=0 and jm.approvedStatus = 2 and jm.status != 3)";
        
				} 			
				$sql .= $objJson->{'groupOp'}.' ';				
				unset($objJson->{'rules'}[$i]);
				
			}
		   
			if($rules->{'field'}=='tmc.cityName')
			{
				
			$sql .= ' ( ';
		
			
				$sql  .= "cityName like '%".filter_values($rules->{'data'})."%'";
				
				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
				unset($objJson->{'rules'}[$i]);
			} 

			if($rules->{'field'}=='tbds.name')
			{
				
			$sql .= ' ( ';
		
			
				$sql  .= "tbds.name like '%".filter_values($rules->{'data'})."%'";
				
				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
				unset($objJson->{'rules'}[$i]);
			} 

			if($rules->{'field'}=='tmd.name')
			{
				
			$sql .= ' ( ';
		
			
				$sql  .= "tmd.name like '%".filter_values($rules->{'data'})."%'";
				
				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
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

	
	function getStores()
    {
        return $this->db->query("select * from tbl_atn_stores")->result_array();
    } //get_store_details
    
   function get_store_details($resultType='G')
    {
            $addSql = "  ";

            if($this->input->post('filters')!='') // search filters
            {
                    $addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
            }


            $sql = "select concat(zrspm.empFname,' ',zrspm.empLname) as zrspm, concat(rspm.empFname,' ',rspm.empLname) as rspm, concat(e.empFname,' ',e.empLname) as rsp, tas.*,z.name, c.cityName, ms.State_Name from tbl_atn_stores tas "
                    . " inner join tbl_mst_city c on c.cityId=tas.cityId "
                    . " inner join tbl_mst_state ms on c.state=ms.State_Id "
                    . " left join tbl_atn_store_emp se on tas.storeId=se.storeId "
                    . " left join tbl_emp_master e on e.empId=se.empId and e.empDesination=12 "
                    . " left join tbl_emp_master rspm on rspm.empId=e.reportingTo "
                    . " left join tbl_emp_master zrspm on zrspm.empId=rspm.reportingTo "
                    . " inner join tbl_region z on z.id=ms.region where 1=1 ".$addSql." group by tas.storeId";

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
    function get_candidate($resultType='G')
    {
            $addSql = "  ";

            if($this->input->post('filters')!='') // search filters
            {
                    $addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
            }

            
              if($this->input->post('id')>0)
              {
                  $sql = "select c.*, concat(c.empFname,' ',c.empLname) as can_name,(select group_concat(i.interviewerDest,'#',i.is_selected) from tbl_atn_interview i where i.mrfId=c.mrfId) as res from tbl_atn_mrf c where c.storeId='".$this->input->post('id')."' ".$addSql." ";
              }
              else
              {
                  $sql = "select c.*,concat(c.empFname,' ',c.empLname) as can_name,(select group_concat(i.interviewerDest,'#',i.is_selected) from tbl_atn_interview i where i.mrfId=c.mrfId) as res from tbl_atn_mrf c where 1=1 ".$addSql." ";
              }
            

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
    function get_allocation($resultType='G')
    {
            $addSql = "  ";

            if($this->input->post('filters')!='') // search filters
            {
                    $addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
            }

            
//(select concat(zrspm.empFname,' ',zrspm.empLname) from tbl_emp_master zrspm where zrspm.empId=rspm.reportingTo) as zrspm_res,concat(rspm.empFname,' ',rspm.empLname) as rspm_res,
            //group_concat(concat(zrspm.empFname,' ',zrspm.empLname)) as zrspm_res, group_concat(concat(rspm.empFname,' ',rspm.empLname)) as rspm_res,
            $sql = "select  s.storeId,"
                    . "(select count(ee.empId) from tbl_emp_master ee inner join tbl_atn_store_emp se on ee.empId=se.empId where se.storeId=s.storeId and ee.empDesination='12' and ee.status='1') as working, "
                    . "(select count(mrf.mrfId) from tbl_atn_mrf mrf where mrf.is_approved='Y' and mrf.storeId=s.storeId) as approved, "
                    . "(select count(mrf.mrfId) from tbl_atn_mrf mrf where mrf.storeId=s.storeId) as totcan, a.id, a.storeId, "
                    //. " sum(a.empNo) as empNo, "
                    . " (select sum(aa.empNo) from tbl_atn_allocation aa where aa.id=a.id and aa.is_removed='N') as empNo, "
                    . " z.name, ms.State_Name, c.cityName, s.storeCode, s.storeName from tbl_atn_allocation a "
                    . " inner join tbl_atn_stores s on a.storeId=s.storeId "
                    . " left join tbl_mst_city c on s.cityId=c.cityId "
                    . " left join tbl_mst_state ms on ms.State_Id=c.state "
                   // . " left join tbl_atn_store_emp ase on ase.storeId=s.storeId "
                  //  . " left join tbl_emp_master rspm on rspm.empId=ase.empId and rspm.empDesination='13' "
                  //  . " left join tbl_emp_master zrspm on zrspm.empId=rspm.reportingTo "
                    . " left join tbl_region z on z.id=ms.region where 1=1 ".$addSql." group by a.storeId ";

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
     function get_history($resultType='G')
    {
            $addSql = "  ";

            if($this->input->post('filters')!='') // search filters
            {
                    $addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
            }

            

            $sql = "select a.id, a.is_removed, date_format(a.insertedOn,'%d %b, %y') as addedDate, a.id, a.storeId, a.empNo, z.name, ms.State_Name, c.cityName, s.storeCode, s.storeAddress from tbl_atn_allocation a inner join tbl_atn_stores s on a.storeId=s.storeId left join tbl_mst_city c on s.cityId=c.cityId left join tbl_mst_state ms on ms.State_Id=c.state left join tbl_region z on z.id=ms.region where 1=1 and a.storeId='".$this->input->post('id')."' ".$addSql." ";

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
    function get_zone()
    {
        return $this->db->query("select * from tbl_region order by name")->result_array();
    }
    function get_state($zone)
    {
        return $this->db->query("select * from tbl_mst_state where region='".$zone."' order by State_Name")->result_array();
    }
    function get_city($state)
    {
        return $this->db->query("select * from tbl_mst_city where state='".$state."' order by cityName")->result_array();
    }
    
    function rate()
    {
        
        return array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5');
    }
    
    function export_store_allocation()
    {
       // $sql = "select (select count(mrf.mrfId) from tbl_atn_mrf mrf where mrf.is_approved='Y' and mrf.storeId=s.storeId) as approved, (select count(mrf.mrfId) from tbl_atn_mrf mrf where mrf.storeId=s.storeId) as totcan, a.id, a.storeId, sum(a.empNo) as empNo,z.name, ms.State_Name, c.cityName, s.storeCode, s.storeAddress from tbl_atn_allocation a inner join tbl_atn_stores s on a.storeId=s.storeId left join tbl_mst_city c on s.cityId=c.cityId left join tbl_mst_state ms on ms.State_Id=c.state left join tbl_region z on z.id=ms.region where 1=1 group by a.storeId";
        //(select count(ee.empId) from tbl_emp_master ee inner join tbl_atn_store_emp se on ee.empId=se.empId where se.storeId=s.storeId and ee.empDesination='12' and ee.status='1') as working
         $sql = "select (select count(ee.empId) from tbl_emp_master ee inner join tbl_atn_store_emp se on ee.empId=se.empId where se.storeId=s.storeId and ee.empDesination='12' and ee.status='1') as working, (select sum(a.empNo) from tbl_atn_allocation a where a.storeId=s.storeId and a.is_removed='N') as totallocation, (select count(mrf.mrfId) from tbl_atn_mrf mrf where mrf.is_approved='Y' and mrf.storeId=s.storeId) as approved, (select count(mrf.mrfId) from tbl_atn_mrf mrf where mrf.storeId=s.storeId) as totcan, concat(e.empFname,' ',e.empLname) as rsp, concat(rspm.empFname,' ',rspm.empLname) as rspm, concat(zrspm.empFname,' ',zrspm.empLname) as zrspm, s.*,z.name, ms.State_Name, c.cityName from tbl_atn_stores s "
                 . " left join tbl_mst_city c on s.cityId=c.cityId "
                 . " left join tbl_mst_state ms on ms.State_Id=c.state "
                 . " left join tbl_region z on z.id=ms.region "
                 . " left join tbl_atn_store_emp se on s.storeId=se.storeId "
                
                 . " left join tbl_emp_master e on (se.empId=e.empId and e.empDesination=12) "
                   . " left join tbl_emp_master rspm on rspm.empId=e.reportingTo "
                    . " left join tbl_emp_master zrspm on zrspm.empId=rspm.reportingTo "
                // . " left join tbl_emp_master e1 on (se.empId=e1.empId and e1.empDesination=12) "
                // . " left join tbl_atn_allocation a on a.storeId=s.storeId "
                 . " group by s.storeId ";
        return $this->db->query($sql)->result_array();
    }
	function totalallocation()
	{
			$sql = "select r.name, sum(al.empNo) as totalallocation from tbl_atn_allocation al
			Left Join tbl_atn_stores st on al.storeId=st.storeId
			Left Join ".TABLE_CITY." c on st.cityId=c.cityId
			Left Join ".TABLE_STATE." s on c.state=s.State_Id
			Left Join ".TABLE_REGION." r on s.region=r.id
			group by r.id order by r.name";
		return $this->db->query($sql)->result_array();
		
	}
	
	
	function get_job_details($resultType='G')
	{
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
		
		 	$sql = "SELECT jm.job_Id,jm.noOfPosition,tmd.name as departmentName,tbds.name as designationName,tms.state_name as stateName,tmc.cityName,CONCAT(tem.empFname, ' ', tem.empLname) as addedBy,tem.empDept,DATE_FORMAT(jm.isCreated,'%d-%b-%Y') as isCreated,jm.isUpdated,
		            jm.status,CONCAT(tem1.empFname, ' ', tem1.empLname) as mrfOpenRequestHandleBy,GET_TOTAL_CANDIDATE_SHORTLISTED(jm.job_Id)  as totalCandidateShortlist,jm.total_candidate_selected as totalCandidateSelected,GET_TOTAL_CANDIDATE_IN_MRF(jm.job_Id) as totalCandidate,
					GET_TOTAL_INTERVIEW_CONDUCTED(jm.job_Id) as totalInterviewConducted,CASE
					WHEN approvedStatus = 2 THEN 'A'
					WHEN approvedStatus = 3 THEN 'R'
					ELSE 'P'
					END as approvedStatus
		           FROM `job_mrf` jm left join tbl_mst_dept tmd on jm.departmentId = tmd.id left join tbl_mst_designation tbds on jm.proposedRoleId = tbds.id left join tbl_mst_state tms  on jm.jobState = tms.state_id left join tbl_mst_city tmc on jm.locationId = tmc.cityId left join tbl_emp_master tem on jm.requisitionAddedBy = tem.empId
				   left join tbl_emp_master tem1 on jm.mrfOpenRequestHandleBy = tem1.empId  where requisitionAddedBy='".$uId."' and jm.status != 0 ". $addSql;
		
		 
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
    
	function job_listing($table, $col='', $colVal='')
	{
		$addSql = '';

		if($col!='' and $colVal!='')
		{
			$addSql .= " where ".$col."='".$colVal."'";
		}
		$sql = "select * from ".$table.$addSql;
		return $this->db->query($sql)->result_array();
	}
	
	
	function get_mrf_request($resultType='G')
	{
		$addSql = "  ";
        $uId = $this->session->userdata('admin_id');
		if($this->input->post('filters')!='') {   
		     $filterResultsJSON = json_decode($this->input->post('filters'));
			 $filterArray           = get_object_vars($filterResultsJSON);			
			 if(!empty($filterArray['rules'])) {
			     $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			 }
		}

		$sql = "SELECT jm.job_Id,jm.noOfPosition,tmd.name as departmentName,tbds.name as designationName,tms.state_name as stateName,tmc.cityName,CONCAT(tem.empFname, ' ', tem.empLname) as addedBy,DATE_FORMAT(jm.isCreated,'%d-%b-%Y') as isCreated,jm.isUpdated,jm.requisitionApprovedBy,
		            CONCAT(tem1.empFname, ' ', tem1.empLname) as recruiterName,
					CASE
					WHEN approvedStatus = 2 THEN 'A'
					WHEN approvedStatus = 3 THEN 'R'
					ELSE 'P'
					END as approvedStatus
		           FROM tbl_emp_master temst1 inner join `job_mrf` jm on temst1.empId = jm.requisitionAddedBy left join tbl_mst_dept tmd on jm.departmentId = tmd.id left join tbl_mst_designation tbds on jm.proposedRoleId = tbds.id
				    left join tbl_mst_state tms  on jm.jobState = tms.state_id left join tbl_mst_city tmc on jm.locationId = tmc.cityId left join tbl_emp_master tem on jm.requisitionAddedBy = tem.empId
					left join tbl_emp_master tem1 on jm.mrfOpenRequestHandleBy = tem1.empId   where temst1.reportingTo='".$uId."' and jm.status != 0
                  ".$addSql;
		

		
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
	
	function get_all_mrf_listing($resultType='G'){
	   
	    $addSql = "  ";      
	    if($this->input->post('filters')!='') {   
		     $filterResultsJSON = json_decode($this->input->post('filters'));
			 $filterArray           = get_object_vars($filterResultsJSON);			
			 if(!empty($filterArray['rules'])) {
			     $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			 }
		}

		$sql = "SELECT jm.job_Id,jm.noOfPosition,tmd.name as departmentName,tbds.name as designationName,tms.state_name as stateName,tmc.cityName,CONCAT(tem.empFname, ' ', tem.empLname) as addedBy,CONCAT(tem1.empFname, ' ', tem1.empLname) as recruiterName,DATE_FORMAT(jm.isCreated,'%d-%b-%Y') as isCreated,jm.isUpdated,jm.requisitionApprovedBy,
		            GET_TOTAL_CANDIDATE_SHORTLISTED(jm.job_Id)  as totalCandidateShortlist,jm.total_candidate_selected as totalCandidateSelected,GET_TOTAL_CANDIDATE_IN_MRF(jm.job_Id) as totalCandidate,
		             GET_TOTAL_INTERVIEW_CONDUCTED(jm.job_Id) as totalInterviewConducted,CASE
					WHEN approvedStatus = 2 THEN 'A'
					WHEN approvedStatus = 3 THEN 'R'
					ELSE 'P'
					END as approvedStatus
		           FROM `job_mrf` jm left join tbl_mst_dept tmd on jm.departmentId = tmd.id left join tbl_mst_designation tbds on jm.proposedRoleId = tbds.id left join tbl_mst_state tms  on jm.jobState = tms.state_id left join tbl_mst_city tmc on jm.locationId = tmc.cityId left join tbl_emp_master tem on jm.requisitionAddedBy = tem.empId left join tbl_emp_master tem1 on jm.mrfOpenRequestHandleBy = tem1.empId
                   where jm.status != 0 ".$addSql;	
		
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
	
	function get_all_mrf_listing_to_hr($resultType='G'){
	        $addSql = "  ";      
		
		 if($this->input->post('filters')!='') {   
		     $filterResultsJSON = json_decode($this->input->post('filters'));
			 $filterArray           = get_object_vars($filterResultsJSON);			
			 if(!empty($filterArray['rules'])) {
			     $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			 }
		}		
		 		 
		$sql = "SELECT jm.job_Id,jm.noOfPosition,jm.total_candidate_selected as totalCandidateSelected,jm.status,tmd.name as departmentName,tbds.name as designationName,tms.state_name as stateName,tmc.cityName,CONCAT(tem.empFname, ' ', tem.empLname) as addedBy,CONCAT(temapp.empFname, ' ', temapp.empLname) as approvedBy,CONCAT(tem1.empFname, ' ', tem1.empLname) as mrfOpenRequestHandleBy, DATE_FORMAT(jm.isCreated,'%d-%b-%Y') as isCreated,jm.isUpdated,
						GET_TOTAL_CANDIDATE_SHORTLISTED(jm.job_Id)  as totalCandidateShortlist,jm.total_candidate_selected as totalCandidateSelected,GET_TOTAL_CANDIDATE_IN_MRF(jm.job_Id) as totalCandidate,
                      GET_TOTAL_INTERVIEW_CONDUCTED(jm.job_Id) as totalInterviewConducted,CASE
					WHEN approvedStatus = 2 THEN 'A'
					WHEN approvedStatus = 3 THEN 'R'
					ELSE 'P'
					END as approvedStatus
		           FROM `job_mrf` jm left join tbl_mst_dept tmd on jm.departmentId = tmd.id left join tbl_mst_designation tbds on jm.proposedRoleId = tbds.id left join tbl_mst_state tms  on jm.jobState = tms.state_id left join tbl_mst_city tmc on jm.locationId = tmc.cityId left join tbl_emp_master tem on jm.requisitionAddedBy = tem.empId
                   left join tbl_emp_master temapp  on   jm.requisitionApprovedBy = temapp.empId  left join tbl_emp_master tem1 on jm.mrfOpenRequestHandleBy = tem1.empId  where  jm.status != 0 ".$addSql;	
		

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
	
	function get_all_mrf_listing_to_ta($resultType='G'){
	        $addSql = "  ";      
		$uId    =  $this->session->userdata('admin_id'); 
		 if($this->input->post('filters')!='') {   
		     $filterResultsJSON = json_decode($this->input->post('filters'));
			 $filterArray           = get_object_vars($filterResultsJSON);			
			 if(!empty($filterArray['rules'])) {
			     $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			 }
		}		
		 		 
		$sql = "SELECT jm.job_Id,jm.noOfPosition,jm.total_candidate_selected as totalCandidateSelected,jm.status,tmd.name as departmentName,tbds.name as designationName,tms.state_name as stateName,tmc.cityName,CONCAT(tem.empFname, ' ', tem.empLname) as addedBy,CONCAT(temapp.empFname, ' ', temapp.empLname) as approvedBy,CONCAT(tem1.empFname, ' ', tem1.empLname) as mrfOpenRequestHandleBy, DATE_FORMAT(jm.isCreated,'%d-%b-%Y') as isCreated,jm.isUpdated,
		              GET_TOTAL_CANDIDATE_SHORTLISTED(jm.job_Id)  as totalCandidateShortlist,jm.total_candidate_selected as totalCandidateSelected,GET_TOTAL_CANDIDATE_IN_MRF(jm.job_Id) as totalCandidate,
                      GET_TOTAL_INTERVIEW_CONDUCTED(jm.job_Id) as totalInterviewConducted,CASE
					WHEN approvedStatus = 2 THEN 'A'
					WHEN approvedStatus = 3 THEN 'R'
					ELSE 'P'
					END as approvedStatus
		            FROM `job_mrf` jm left join tbl_mst_dept tmd on jm.departmentId = tmd.id left join tbl_mst_designation tbds on jm.proposedRoleId = tbds.id left join tbl_mst_state tms  on jm.jobState = tms.state_id left join tbl_mst_city tmc on jm.locationId = tmc.cityId left join tbl_emp_master tem on jm.requisitionAddedBy = tem.empId    left join tbl_emp_master temapp  on   jm.requisitionApprovedBy = temapp.empId  left join tbl_emp_master tem1 on jm.mrfOpenRequestHandleBy = tem1.empId  where jm.mrfOpenRequestHandleBy = $uId and jm.status != 0 ".$addSql;	
		

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
	
	
	function candidate_details($table, $col='', $colVal=''){
	      $addSql = '';

		  if($col!='' and $colVal!='')
		 {
			$addSql .= " where ".$col."='".$colVal."'";
		 }
		 $sql = "select * from ".$table.$addSql;
		 return $this->db->query($sql)->result_array();	     
	}
        
    function get_all_candidate_list($table, $col='', $colVal=''){
        $addSql = '';

        if($col!='' and $colVal!='')
	{
	    $addSql .= " where ".$col."='".$colVal."'";
	}
        $sql = "select cmd.*,tmc.cityName from $table as cmd left join tbl_mst_city as tmc  on cmd.currentLocation = tmc.cityId	$addSql";     
	
        return $this->db->query($sql)->result_array();
    }
    
	
	function get_all_selected_candidate_list($mrfId){  

		 $sql = "SELECT cmd.*,cif.selectionStatus,tmc.cityName FROM candidate_mrf_details as cmd inner join candidate_iaf_form as cif on cmd.candId = cif.candId left join tbl_mst_city as tmc  on cmd.currentLocation = tmc.cityId  where cmd.mrfId = $mrfId and selectionStatus = 3";	
		 return $this->db->query($sql)->result_array();	    
	}
	
    
    function get_all_mrf_candidate_list_for_ta($resultType='G',$id = '',$type = ''){            
        $addSql = "  "; 
		if($this->input->post('filters')!='') {   
		    $filterResultsJSON = json_decode($this->input->post('filters'));
			$filterArray           = get_object_vars($filterResultsJSON);			
			if(!empty($filterArray['rules'])) {
			    $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
		}	
		$cond = '';	
		
		
		if($id){
             $cond = "and cmd.mrfId = $id";
		}

		if($type == 1){
		    $cond  .= " group by cmd.candId";
		} else if($type == 2){
		    $cond  .= " and cmd.isApprovedByManager = 1 and cmd.mrfId = $id  group by cmd.candId";
		} else if($type == 3){
		    $cond  .= " and cmd.interviewDate != '0000-00-00 00:00:00' and cmd.isCandidateSelected = 0 and cmd.finalistedBhuStatus = 0 and cmd.mrfId = $id group by cmd.candId";
		} else if($type == 4){
			$cond  .= " and cmd.mrfId = $id and cmd.bhuFinalApproval = 1 and cmd.finalistedBhuStatus = 1  group by cmd.candId";
		} else if($type == 5) {
			$cond  .= " and cmd.bhuFinalApproval = 1  group by cmd.candId";
		} else if($type == 6) {
			$cond  .= " and cmd.finalistedBhuStatus = 1  group by cmd.candId";
		} else if($type == 7) {
			$cond  .= " and it.mrfId = $id and interview = 2  group by cmd.candId";
		}
	
            $sql  = "Select cmd.candId,CONCAT_WS(' ',cmd.empFname,cmd.empMname,cmd.empLname) AS empName,
        		cmd.mobile,cmd.designation,cmd.ctc,cmd.currentLocation,cmd.resume,cmd.isApprovedByManager,cmd.isVenueAdded,
        		cmd.interviewRoundCleared,cmd.interviewVenue,
        		CONCAT_WS('',cmd.expYear,'.',cmd.expMonth,' yrs') as expYear,
        		date_format(cmd.isCreated,'%d %b, %Y') as isCreated ,cmd.isUpdated,cmd.finalizedByManagerStatus,
        		tmd.name as departmentName,tbds.name as designationName,
        		tms.state_name as jobLocation,tmc.cityName,cif.iafId,cif.selectionStatus,
        		jm.job_id as jobId,
        		cmd.bhuFinalApproval,
        		cmd.finalistedBhuStatus,
        		GET_LAST_INTERVIEW_ROUND(cif.iafId) as selecton_status_round
        		from candidate_mrf_details cmd 
        		inner join job_mrf jm on cmd.mrfId 						= jm.job_id 
				left join candidate_iaf_form as cif  on  cmd.candId 	= cif.candId
				left join interview_take as it on  cmd.candId 	= it.candId
				left join  tbl_mst_dept tmd on jm.departmentId 			= tmd.id 
				left join tbl_mst_designation tbds on jm.proposedRoleId = tbds.id 
                left join tbl_mst_state tms  on jm.jobState 			= tms.state_id 
                left join tbl_mst_city tmc on jm.locationId = tmc.cityId where 1=1 $cond ".$addSql;
				

		// echo $sql; exit;
	    if($resultType=='G'){
			$result = parent::result_grid_array($sql);
		} else {
			$result = $this->db->query($sql)->result_array();
        } 
	    return $result;      
    }
   
    function get_all_mrf_candidate_list_for_hr($resultType='G',$id = '',$type =''){ 

        $addSql = " "; 
		if($this->input->post('filters')!='') {   
		    $filterResultsJSON = json_decode($this->input->post('filters'));
			$filterArray           = get_object_vars($filterResultsJSON);			
			if(!empty($filterArray['rules'])) {
			    $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
		}	
        if($id){
			$cond = "and cmd.mrfId = $id";
	   }
	
	   if($type == 1){
		    $cond  .= " group by cmd.candId";
		} else if($type == 2){
			$cond  .= " and cmd.isApprovedByManager = 1 and cmd.mrfId = $id  group by cmd.candId";
		} else if($type == 3){
			$cond  .= " and cmd.interviewDate != '0000-00-00 00:00:00' and cmd.isCandidateSelected = 0 and cmd.finalistedBhuStatus = 0 and cmd.mrfId = $id group by cmd.candId";
		} else if($type == 4){
			$cond  .= " and cmd.mrfId = $id and cmd.bhuFinalApproval = 1 and cmd.finalistedBhuStatus = 1  group by cmd.candId";
		} else if($type == 5) {
			$cond  .= " and cmd.bhuFinalApproval = 1  group by cmd.candId";
		} else if($type == 6) {
			$cond  .= " and cmd.finalistedBhuStatus = 1  group by cmd.candId";
		} else if($type == 7) {
			$cond  .= " and it.mrfId = $id and interview = 2  group by cmd.candId";
		}
	
        $sql = "Select cmd.candId,cmd.mrfId,
        		CONCAT_WS(' ',cmd.empFname,cmd.empMname,cmd.empLname) AS empName,
        		cmd.mobile,cmd.designation,cmd.ctc,cmd.currentLocation,cmd.resume,
        		cmd.isApprovedByManager,cmd.isVenueAdded,
        		cmd.interviewRoundCleared,cmd.interviewVenue,
        		CONCAT_WS('',cmd.expYear,'.',cmd.expMonth,' yrs') as expYear,
        		date_format(cmd.isCreated,'%d %b, %Y') as isCreated ,cmd.isUpdated,
        		tmd.name as departmentName,tbds.name as designationName,
        		tms.state_name as jobLocation,tmc.cityName,cif.iafId,cif.selectionStatus,
        		GET_LAST_INTERVIEW_ROUND(cif.iafId) as selecton_status_round,
        		cmd.bhuFinalApproval,
				cmd.finalistedBhuStatus,cmd.finalizedByManagerStatus,GET_TOTAL_ROUND_COMPLETE_OF_CANDIDATE(cmd.candId) as totalInterviewDone,
				GET_INTERVIEW_SCHEDULE_COUNT(cmd.candId) as interviewScheduleCount
        	    from candidate_mrf_details cmd 
        	    inner join job_mrf jm on cmd.mrfId = jm.job_id 
				left join candidate_iaf_form as cif  on  cmd.candId = cif.candId
				left join interview_take as it on  cmd.candId 	= it.candId
				left join  tbl_mst_dept tmd on jm.departmentId = tmd.id 
				left join tbl_mst_designation tbds on jm.proposedRoleId = tbds.id 
                left join tbl_mst_state tms  on jm.jobState = tms.state_id 
                left join tbl_mst_city tmc on jm.locationId = tmc.cityId 
                where 1=1 $cond ".$addSql;

		// echo $sql; exit;
		if($resultType=='G'){
			$result = parent::result_grid_array($sql);
		}else{
			$result = $this->db->query($sql)->result_array();
        } 
	    return $result;      
    }
   
   
    function interview_venue_listing($table, $col='', $colVal=''){
        $addSql = '';
        if($col!='' and $colVal!='')
        {
              $addSql .= " where ".$col."='".$colVal."'";
        }
        $sql = "select candId,interviewDate,interviewVenue from ".$table.$addSql;      
        return $this->db->query($sql)->result_array();
	}
	
	function show_all_schedule_listing($candId){
        $this->db->select("*");
		$this->db->from('interview_schedule_dates');
		$this->db->where('candId',$candId);
		return $this->db->get()->result_array();      
	}

    function save_interview_venue_details($data,$candId,$uid = ''){		
		
		if($uid != ''){
            $datas =  array(
				'candId' => $candId,
				'interviewVenue'=>  $data['interviewVenue'],
				'interviewDate' =>  $data['interviewDate'],
				'schedule_status' => 4,
				'reschedule_date' => '',
				'comments'  =>  $data['comments'],
				'status'=> 1
			);			
		  $this->db->where('id', $uid);
		  $lastId =  $this->db->update('interview_schedule_dates', $datas);
		} else {
			$datas =  array(
				'candId' => $candId,
				'interviewVenue'=>  $data['interviewVenue'],
				'interviewDate' =>  $data['interviewDate'],
				'schedule_status' => 0,
				'comments'  =>  $data['comments'],
				'status'=> 1
			);
			$checkStatus = $this->db->insert('interview_schedule_dates', $datas);
			$lastId =  $this->db->insert_id(); 
		}		
        return $lastId;
	}

    function get_all_candidate_list_for_shortlist($resultType='G',$id = '',$type = ''){
        $addSql =  "  "; 
        $uId    =  $this->session->userdata('admin_id');     
		$cond   =  '';
		if($id){
			$cond = "and jm.job_id = $id";
		}		
		
		if($type == 1){
		     $cond  .= " group by cmd.candId";
		} else if($type == 2){
		    $cond  .= " and cmd.isApprovedByManager = 1 and cmd.mrfId = $id  group by cmd.candId";
		} else if($type == 3){
		    $cond  .= " and cmd.interviewDate != '0000-00-00 00:00:00' and cmd.isCandidateSelected = 0 and cmd.finalistedBhuStatus = 0 and cmd.mrfId = $id group by cmd.candId";
		} else if($type == 4){
			$cond  .= " and cmd.mrfId = $id and cmd.bhuFinalApproval = 1 and cmd.finalistedBhuStatus = 1  group by cmd.candId";
		} else if($type == 5) {
			$cond  .= " and cmd.bhuFinalApproval = 1  group by cmd.candId";
		} else if($type == 6) {
			$cond  .= " and cmd.finalistedBhuStatus = 1  group by cmd.candId";
		} else if($type == 7) {
			$cond  .= " and it.mrfId = $id and interview = 2  group by it.candId";
		}
		


		if($this->input->post('filters')!='') {   
		     $filterResultsJSON = json_decode($this->input->post('filters'));
			 $filterArray           = get_object_vars($filterResultsJSON);			
			 if(!empty($filterArray['rules'])) {
			     $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			 }
		}	

           $sql = "Select distinct cmd.candId,jm.job_id,CONCAT_WS(' ',cmd.empFname,cmd.empMname,cmd.empLname) AS empName,cmd.mobile,cmd.currentLocation,cmd.resume,cmd.designation,cmd.ctc,cmd.isApprovedByManager,cmd.isVenueAdded,cmd.interviewDate,cmd.interviewVenue,cmd.interviewStatus,CONCAT_WS('',cmd.expYear,'.',cmd.expMonth,' yrs') as expYear,date_format(cmd.isCreated,'%d %b, %Y') as isCreated ,cmd.isUpdated,tmd.name as departmentName,tbds.name as designationName,tms.state_name as jobLocation,tmc.cityName
                   ,GET_TOTAL_ROUND_COMPLETE_OF_CANDIDATE(cmd.candId) as totalInterviewDone,GET_INTERVIEW_SCHEDULE_COUNT(cmd.candId) as interviewScheduleCount from job_mrf jm  inner join candidate_mrf_details cmd on jm.job_id = cmd.mrfId left join interview_take as it on  cmd.candId = it.candId
                   left join tbl_mst_dept tmd on jm.departmentId = tmd.id left join tbl_mst_designation tbds on jm.proposedRoleId = tbds.id 
                   left join tbl_mst_state tms  on jm.jobState = tms.state_id left join tbl_mst_city tmc on jm.locationId = tmc.cityId where jm.requisitionAddedBy='".$uId."' $cond ".$addSql;

           //echo $sql; exit;
        if($resultType=='G'){
			$result = parent::result_grid_array($sql);
		}else{
			$result = $this->db->query($sql)->result_array();
        } 
	    return $result;
    }
	
	function get_candidate_details($candId){
	      
		$addSql .= " where cmd.candId ='".$candId."'";
		
		 $sql = "select *,CONCAT_WS(' ',empFname,empMname,empLname) AS empName,CONCAT_WS('',expYear,'.',expMonth,' yrs') as expYear,tmd.name as designationName,tms.state_name as stateName,tmc.cityName
		             from candidate_mrf_details cmd left join tbl_mst_designation tmd  on cmd.designation  =  tmd.id left join tbl_mst_state tms  on cmd.currentState = tms.state_id left join tbl_mst_city tmc on cmd.currentLocation = tmc.cityId ".$addSql;
		
		 return $this->db->query($sql)->result_array();
	}
	
	/**
     *
     * @function    cand_iaf_data
     * @description to view candidate iaf data
     */
	function cand_iaf_data($candId){
	     
		 $sql = "select cmd.candId,concat(cmd.empFname,' ',cmd.empMname,' ',cmd.empLname) as empName,cmd.designation,cmd.currentOrganisation,cmd.expYear,cmd.expMonth,cmd.ctc,cmd.expCtc,
		            cmd.noticePeriod,cmd.interviewDate,cif.iafId,cif.rating,cif.assertive,cif.teamWork,cif.assertive,cif.verbalComm,cif.creativity,cif.logicalUnderstanding,cif.realistic,cif.matchEmpNeeds,cif.technicalSkill,
				    cif.selectionStatus,cif.comments,concat(tem.empFname,' ',tem.empMname,' ',tem.empLname) as recruiterName,tmd.name as designationName from candidate_mrf_details as cmd  left join candidate_iaf_form as cif on cmd.candId = cif.candId
                    left join tbl_mst_designation as tmd on cmd.designation =  tmd.id  
					inner join job_mrf as jm on cmd.mrfId = jm.job_id left join tbl_emp_master  as tem on  jm.mrfOpenRequestHandleBy = tem.empId
					where cmd.candId = $candId";

		 return $this->db->query($sql)->result_array();
	
	}

	/**
     *
     * @function    cand_iaf_data_list
     * @description to view candidate iaf data
     */
	function cand_iaf_data_list($candId,$id){
	     
		$sql = "select cmd.candId,concat(cmd.empFname,' ',cmd.empMname,' ',cmd.empLname) as empName,cmd.designation,cmd.currentOrganisation,cmd.expYear,cmd.expMonth,cmd.ctc,cmd.expCtc,
				   cmd.noticePeriod,cmd.interviewDate,cif.rating,cif.assertive,cif.teamWork,cif.assertive,cif.verbalComm,cif.creativity,cif.logicalUnderstanding,cif.realistic,cif.matchEmpNeeds,cif.technicalSkill,
				   cif.selectionStatus,cif.comments,concat(tem.empFname,' ',tem.empMname,' ',tem.empLname) as recruiterName,tmd.name as designationName from candidate_mrf_details as cmd  left join candidate_iaf_form_list as cif on cmd.candId = cif.candId
				   left join tbl_mst_designation as tmd on cmd.designation =  tmd.id  
				   inner join job_mrf as jm on cmd.mrfId = jm.job_id left join tbl_emp_master  as tem on  jm.mrfOpenRequestHandleBy = tem.empId
				   where cmd.candId = $candId and cif.id=$id";

		return $this->db->query($sql)->result_array();
   
   }
	
	
	/**
     *
     * @function    cand_iaf_data_status
     * @description to view candidate iaf data status
     */
	function cand_iaf_data_status($iafId){
	     
		 $sql = "select cmd.candId,concat(cmd.empFname,' ',cmd.empMname,'',cmd.empLname) as empName,cmd.designation,cmd.currentOrganisation,cmd.expYear,cmd.expMonth,cmd.ctc,cmd.expCtc,
		            cmd.noticePeriod,cmd.interviewDate,cif.iafId,cif.rating,cif.assertive,cif.teamWork,cif.assertive,cif.verbalComm,cif.creativity,cif.logicalUnderstanding,cif.realistic,cif.matchEmpNeeds,cif.technicalSkill,
				    cif.selectionStatus,concat(tem.empFname,' ',tem.empMname,'',tem.empLname) as recruiterName,tmd.name as designationName from candidate_mrf_details as cmd  left join candidate_iaf_form as cif on cmd.candId = cif.candId
                    left join tbl_mst_designation as tmd on cmd.designation =  tmd.id  
					inner join job_mrf as jm on cmd.mrfId = jm.job_id left join tbl_emp_master  as tem on  jm.mrfOpenRequestHandleBy = tem.empId
					where cif.iafId = $iafId";

		 return $this->db->query($sql)->result_array();
	
	}
	
	
	/**
     *
     * @function    get_interview_round_Data
     * @description to get interview round data
     */
	public function get_interview_round_Data(){	    
	      
		    $sql  =  "select * from interview_round";		    
			return $this->db->query($sql)->result_array();
	}
	
	/**
     *
     * @function    get_interview_round_Data
     * @description to get interview round cleared
     */
	public function get_selected_interview_round_Data($iafId){	    
	       $result = array();		  
	       if(!empty($iafId)){
		           $sql      =  "select ir.roundId,ir.roundName,csr.csrId,csr.iafid,csr.selectionStatus,concat(tem.empFname,' ',tem.empMname,'',tem.empLname) as interviewerName from candidate_selection_round as csr  inner join interview_round as ir on csr.roundId  =  ir.roundId 
                  				    left join tbl_emp_master as tem on csr.takenBy  = tem.empId  where csr.iafId = $iafId";
		       
				  $result  =  $this->db->query($sql)->result_array();				   
			}
		    return $result;
	}
	
	/**
     *
     * @function    check_position_left_in_mrf
     * @description check position left in mrf or not
     */
	function check_position_left_in_mrf($jobId){
            $resultData = array();	
	     	$sql      =  "SELECT jm.noOfPosition as totalCandidate, total_candidate_selected as totalCandidateSelected FROM `job_mrf` jm where jm.job_id = $jobId";
            $result  =  $this->db->query($sql)->result_array();	
			if(!empty($result)){
			               $resultData  = $result[0];
			        }
		    return $resultData;		
	}
	
	/**
     *
     * @function    insertToCandidateRecord
     * @description to save records from candidate mrf table to tbl candidate
     */
	function insertToCandidateRecord($candId){
	      $sql     =  "INSERT INTO tbl_candidate ( password,empFname,empLname,mobile,empDept,designation,jobState,jobCity,empDOB,workExp,workExpM,prevSalary,lastOrg,candidate_login_status) 
							SELECT '123456', cmd.empFname, cmd.empLname,cmd.mobile,jm.departmentId,cmd.designation,jm.jobState,jm.locationId,cmd.empDOB,cmd.expYear,expMonth,ctc,currentOrganisation,1 
							FROM candidate_mrf_details as cmd inner join job_mrf as jm on cmd.mrfId = jm.job_id
							where cmd.candId=$candId";
		  $result  =  $this->db->query($sql);
	}	  
	
	/**
     *
     * @function    get_all_employee
     * @description to get all employee
     */
	public function get_all_employee(){	    
	       $result = array();		  
	      
		           $sql      =  "select e.empId,concat(e.empFname,' ',e.empMname,'',e.empLname) as empName from tbl_emp_master as e where  e.empId > 20000001";
			       $result  =  $this->db->query($sql)->result_array();				   
		
		    return $result;
	}
	
	/**
     *
     * @function    get_mrf_info_details
     * @description to get mrf details
     */
	function get_mrf_info_details($id){
	        $resultData  =  array(); 
		         
			$sql      =  "select jm.job_id,tmg.name as designationName,tmc.cityName as locationName,tmd.name as departmentName,requisitionAddedBy,srm.salaryRange,jm.isUpdated 
			from job_mrf jm inner join tbl_mst_designation tmg on  jm.proposedRoleId = tmg.id left join 
			tbl_mst_city tmc on jm.locationId = tmc.cityId left join tbl_mst_dept tmd on jm.departmentId = tmd.id
			left join salary_range_master srm on jm.salaryRange = srm.id
			where jm.job_id=$id";	   
		    $result  =  $this->db->query($sql)->result_array();	
			if(!empty($result)){
			    $resultData  = $result[0];
			}
		    return $resultData;	   
	}
	
	/**
     *
     * @function    get_manager_mrf_data
     * @description to get mrf add by manager data
     */
	function get_manager_mrf_added_by($jid){
	        $resultData  =  array(); 
		    $sql      =  "select concat(tem.empFname,' ',tem.empLname) as empName,tem.empEmailOffice as empEmail from job_mrf jm inner join tbl_emp_master tem on jm.requisitionAddedBy = tem.empId  where jm.job_id=$jid";	   
		    $result  =  $this->db->query($sql)->result_array();	
			if(!empty($result)){
			    $resultData  = $result[0];
			}
		    return $resultData; 
	}
	 
	 /**
     *
     * @function    getSelectedCandidate
     * @description to get fetch select candidate name
     */
	function getSelectedCandidate($candId){
	       $resultData  =  array(); 
		    $sql            =  "select concat(cmf.empFname,' ',cmf.empLname) as empName from candidate_mrf_details cmf  where cmf.candId=$candId"; 
			$result        =  $this->db->query($sql)->result_array();	
			if(!empty($result)){
			    $resultData  = $result[0];
			}
		    return $resultData; 
	}

	public function get_candidate_details_status($candId)
	{
		// $addSql .= " where cmd.candId ='".$candId."'";
		// 
		 $sql = "select *,CONCAT_WS(' ',empFname,empMname,empLname) AS empName,
		 		 CONCAT_WS('',expYear,'.',expMonth,' yrs') as expYear,
		 		 tmd.name as designationName,tms.state_name as stateName,
		 		 tmc.cityName,cmd.interviewDate,cmd.interviewVenue,cmd.finalizedByManagerStatus
		         from candidate_mrf_details cmd 
		         left join tbl_mst_designation tmd  on cmd.designation  =  tmd.id 
		         left join tbl_mst_state tms  on cmd.currentState = tms.state_id 
		         left join tbl_mst_city tmc on cmd.currentLocation = tmc.cityId
		         where cmd.candId = '$candId'";
		// echo $sql;
		 return $this->db->query($sql)->result_array();	

	}

	public function getInterviewersDetails($candId)
	{	
		 $sql = "select CONCAT_WS(' ',em.empFname,em.empMname,em.empLname) AS interviewerName,em.empId,
		 		 tmd.name as designationName,
		 		 csr.selectionStatus,
		 		 ir.roundName
		         from candidate_iaf_form cif 
		         left join candidate_selection_round csr  on cif.iafid  =  csr.iafid 
		         left join interview_round ir on csr.roundId  =  ir.roundId 
		         left join tbl_emp_master em on csr.takenBy = em.empId
		         left join tbl_mst_designation tmd  on em.empDesination  =  tmd.id 
		         where cif.candId = '$candId'";
		
		 return $this->db->query($sql)->result_array();	
	}
	
	public function getTeamMemberList($loggedId)
	{
		$sql = "select CONCAT_WS(' ',empFname,empMname,empLname) AS empName,
				em.empId,
				tmd.name as empDesignationName
		        from tbl_emp_master em 
		        left join tbl_mst_designation tmd  on em.empDesination  =  tmd.id   
		        where em.reportingTo = '$loggedId'";

		// echo $sql;
		return $this->db->query($sql)->result_array();	
	}

	public function getTaInfo($mrfId)
	{
		$sql = "select CONCAT_WS(' ',em.empFname,em.empMname,em.empLname) AS taName,
				em.empId,
				em.empEmailOffice as taEmail,
				tmd.name as empDesignationName
		        from job_mrf jm 
		        left join tbl_emp_master em  on jm.mrfOpenRequestHandleBy  =  em.empId   
		        left join tbl_mst_designation tmd  on em.empDesination  =  tmd.id   
		        where jm.job_Id = '$mrfId'";

		// echo $sql;
		return $this->db->query($sql)->result_array();	
	}

	public function assignEmpInfo($assignEmpId)
	{
		$sql = "select CONCAT_WS(' ',em.empFname,em.empMname,em.empLname) AS empName,
				em.empId,
				em.empEmailOffice as empEmailOffice
		        from tbl_emp_master em   
		        where em.empId = '$assignEmpId'";

		// echo $sql;
		return $this->db->query($sql)->result_array();	
	}

	public function getBhuInfo($candId)
	{
		$sql =  "select 
		 		 tmd.name as designationName,
		 		 CONCAT_WS(' ',cmd.empFname,cmd.empMname,cmd.empLname) AS candidateName,
		 		 dept.name as departmentName,
		 		 CONCAT_WS(' ',em.empFname,em.empMname,em.empLname) AS empName,
		 		 em.reportingTo,
		 		 em.empEmailOffice as oemail,jm.job_id,
		 		 jm.requisitionAddedBy as mrfAddedByManagerID
		         from candidate_mrf_details cmd 
		         left join job_mrf jm on cmd.mrfId = jm.job_id
		         left join tbl_emp_master em on jm.requisitionAddedBy = em.empId
		         left join tbl_mst_designation tmd on jm.proposedRoleId  =  tmd.id 
		         left join tbl_mst_dept dept on jm.departmentId  =  dept.id 
		         where cmd.candId = '$candId'";
		return $this->db->query($sql)->result_array();         
	}
	public function getBhuInfoByReportingID($reportingID)
	{
		$sql ="select CONCAT_WS(' ',empFname,empMname,empLname) AS bhuManagerName,empEmailOffice as bhuManagerEmail,reportingTo from ".TABLE_EMP." WHERE empId='".$reportingID."' ";
		return $this->db->query($sql)->result_array();  
	}
	
	public function getMrfCreateBy($empID)
	{
		$sql ="select CONCAT_WS(' ',empFname,empLname) AS empName,reportingTo from ".TABLE_EMP." WHERE empId='".$empID."' ";
		return $this->db->query($sql)->result_array();  
	}


	public function getFinalistedCandidatesList($mrfId)
	{
		error_reporting(E_ALL);

		$this->db->select('cmd.*');
		// $this->db->select('cif.*');
		// $this->db->select('jm.*');
		$this->db->select('des.name as designationName');
		$this->db->select('dept.name as departmentName');
		$this->db->select('state.state_name as stateName');
		$this->db->select('city.cityName');
		$this->db->select('GET_LAST_INTERVIEW_ROUND(cif.iafId) as selecton_status_round');
		// $this->db->from('job_mrf jm');
		$this->db->from('candidate_mrf_details cmd');
		$this->db->join('job_mrf jm','cmd.mrfId = jm.job_id','left');
		// $this->db->join('candidate_mrf_details cmd','cmd.mrfId = jm.job_id','left');
		$this->db->join('candidate_iaf_form cif','cmd.candId = cif.candId','left');
		$this->db->join('tbl_mst_designation des','jm.proposedRoleId = des.id','left');
		$this->db->join('tbl_mst_dept dept','jm.departmentId = dept.id','left');
		$this->db->join('tbl_mst_state state','jm.jobState = state.state_id','left');
		$this->db->join('tbl_mst_city city','jm.locationId = city.cityId','left');
		$this->db->where('cmd.mrfId',$mrfId);	
		
		return $this->db->get()->result_array();

	
	}

	public function selectedCandidateDetails($candId)
	{
		$this->db->select('cmd.*');
		$this->db->select("CONCAT((cmd.empFname),(' '),(cmd.empMname),(' '),(cmd.empLname)) as candidateName");
		// $this->db->select('cif.*');
		$this->db->select('jm.*');
		$this->db->select('jm.mrfOpenRequestHandleBy as taID');
		$this->db->select('jm.requisitionAddedBy as hiringManagerID');
		$this->db->select('jm.dateOfJoining as tentativeDate');
		$this->db->select('jm.isCreated as mrfCreatedDate');
		$this->db->select('cmd.finalistedByBhuDate as actualCloserDate');
		$this->db->select('des.name as designationName');
		$this->db->select('dept.name as departmentName');
		$this->db->select('state.state_name as stateName');
		$this->db->select('city.cityName');
		$this->db->from('candidate_mrf_details cmd');
		$this->db->join('job_mrf jm','cmd.mrfId = jm.job_id','left');
		$this->db->join('candidate_iaf_form cif','cmd.candId = cif.candId','left');
		$this->db->join('tbl_mst_designation des','jm.proposedRoleId = des.id','left');
		$this->db->join('tbl_mst_dept dept','jm.departmentId = dept.id','left');
		$this->db->join('tbl_mst_state state','jm.jobState = state.state_id','left');
		$this->db->join('tbl_mst_city city','jm.locationId = city.cityId','left');
		$this->db->where('cmd.candId',$candId);
		return $this->db->get()->result_array();
	}

	public function empInfo($empId)
	{
		$this->db->select("CONCAT((empFname),(' '),(empMname),(' '),(empLname)) as empName,empDept");
		$this->db->select('empEmailOffice as empEmail');
		$this->db->select('reportingTo');
		$this->db->from('tbl_emp_master');
		$this->db->where('empId',$empId);
		return $this->db->get()->result_array();
	}

	public function candMrfByCandId($candId)
	{
		$this->db->select('cmd.*');
		// $this->db->select('cif.*');
		// $this->db->select('jm.*');
		$this->db->select('des.name as designationName');
		$this->db->select('dept.name as departmentName');
		$this->db->select('state.state_name as stateName');
		$this->db->select('city.cityName');
		$this->db->from('candidate_mrf_details cmd');
		$this->db->join('job_mrf jm','cmd.mrfId = jm.job_id','left');
		$this->db->join('candidate_iaf_form cif','cmd.candId = cif.candId','left');
		$this->db->join('tbl_mst_designation des','jm.proposedRoleId = des.id','left');
		$this->db->join('tbl_mst_dept dept','jm.departmentId = dept.id','left');
		$this->db->join('tbl_mst_state state','jm.jobState = state.state_id','left');
		$this->db->join('tbl_mst_city city','jm.locationId = city.cityId','left');
		$this->db->where('cmd.candId',$candId);
		return $this->db->get()->result_array();

	}
  

	public function getMrfAddedByEmpDetail($mrfId)
	{
		$this->db->select("CONCAT((tem.empFname),(' '),(tem.empLname)) as empName");
		$this->db->select('tem.empEmailOffice as empEmail');	
		$this->db->from('job_mrf as jm');
		$this->db->join('tbl_emp_master as tem','jm.requisitionAddedBy = tem.empId');
		$this->db->where('job_id',$mrfId);
		return $this->db->get()->row_array();
	}

 // fetch all employee list_for_mrf
    function fetch_all_employee_list_for_mrf(){
        $this->db->select("empId,concat(tem.empFname,' ',tem.empLname) as empName,empImage",false);
        $this->db->where('isActive',1);
        $query = $this->db->get("tbl_emp_master as tem");      
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
        } else {
            $result = array(); 
        }
        return $result;
    }
 // fetch_experience_range
   function fetch_experience_range(){
        $this->db->select("*",false);
        $query = $this->db->get("experience_range as er");      
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
        } else {
            $result = array(); 
        }
        return $result;
   }	
   
   function get_ta_team_data(){
	 $this->db->select("empId,CONCAT(tem.empFname, ' ', tem.empLname) as empName",false);
	 $this->db->from("tbl_emp_master as tem");   
	 $this->db->where('empRole',15); 
	 $query =    $this->db->get();
	 if($query->num_rows() > 0){       
		$result = $query->result_array(); 
	 } else {
		$result = array(); 
	 }
	 return $result;
   }

   function get_interview_schedule_details($candId){ 
		$this->db->select("*",false);
		$this->db->from("interview_schedule_dates");   
		$this->db->where('candId',$candId); 
		$this->db->order_by('id','desc');
		$query =    $this->db->get();
	
		if($query->num_rows() > 0){       
		$result = $query->result_array(); 
		} else {
		$result = array(); 
		}
		return $result;  
   }


   function get_view_mrf_details($resultType = 'G',$mrfId)
	{
		$addSql = " ";
        $uId = $this->session->userdata('admin_id');	
		
		$sql = "SELECT jm.job_Id,jm.noOfPosition,DATE_FORMAT(jm.isCreated,'%d-%b-%Y') as isCreated,jm.isUpdated,DATE_FORMAT(jm.buhApprovalDate,'%d-%b-%Y') as buhApprovalDate,DATE_FORMAT(jm.recruiterAcceptDate,'%d-%b-%Y') as recruiterAcceptDate,
		            jm.status,CONCAT(tem1.empFname, ' ', tem1.empLname) as mrfOpenRequestHandleBy,GET_TOTAL_CANDIDATE_SHORTLISTED(jm.job_Id)  as totalCandidateShortlist,jm.total_candidate_selected as totalCandidateSelected,GET_TOTAL_CANDIDATE_IN_MRF(jm.job_Id) as totalCandidate,
					GET_TOTAL_INTERVIEW_CONDUCTED(jm.job_Id) as totalInterviewConducted,GET_TOTAL_CANDIDATE_SHORTLIST_BY_MANAGER(jm.job_Id) as totalCandidateShortlistByManager,GET_TOTAL_CANDIDATE_SHORTLIST_BY_BHU(jm.job_Id) as bhuTotalRequest,
					GET_BHU_APPROVED_CANDIDATE(jm.job_Id) as bhuApprovedCandidate,
					CASE
					WHEN approvedStatus = 2 THEN 'A'
					WHEN approvedStatus = 3 THEN 'R'
					ELSE 'P'
					END as approvedStatus
		           FROM `job_mrf` jm left join tbl_mst_dept tmd on jm.departmentId = tmd.id left join tbl_mst_designation tbds on jm.proposedRoleId = tbds.id left join tbl_mst_state tms  on jm.jobState = tms.state_id left join tbl_mst_city tmc on jm.locationId = tmc.cityId left join tbl_emp_master tem on jm.requisitionAddedBy = tem.empId
				   left join tbl_emp_master tem1 on jm.mrfOpenRequestHandleBy = tem1.empId  where jm.job_id =$mrfId  and requisitionAddedBy='".$uId."' and jm.status != 0 ". $addSql;
	
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
	
	function get_view_mrf_details_to_ta($resultType = 'G',$mrfId)
	{
		$addSql  = "  ";
		 	$sql = "SELECT jm.job_Id,jm.noOfPosition,DATE_FORMAT(jm.isCreated,'%d-%b-%Y') as isCreated,jm.isUpdated,DATE_FORMAT(jm.buhApprovalDate,'%d-%b-%Y') as buhApprovalDate,DATE_FORMAT(jm.recruiterAcceptDate,'%d-%b-%Y') as recruiterAcceptDate,
		            jm.status,CONCAT(tem1.empFname, ' ', tem1.empLname) as mrfOpenRequestHandleBy,GET_TOTAL_CANDIDATE_SHORTLISTED(jm.job_Id)  as totalCandidateShortlist,jm.total_candidate_selected as totalCandidateSelected,GET_TOTAL_CANDIDATE_IN_MRF(jm.job_Id) as totalCandidate,
					GET_TOTAL_INTERVIEW_CONDUCTED(jm.job_Id) as totalInterviewConducted,GET_TOTAL_CANDIDATE_SHORTLIST_BY_MANAGER(jm.job_Id) as totalCandidateShortlistByManager,GET_TOTAL_CANDIDATE_SHORTLIST_BY_BHU(jm.job_Id) as bhuTotalRequest,
					GET_BHU_APPROVED_CANDIDATE(jm.job_Id) as bhuApprovedCandidate,
					CASE
					WHEN approvedStatus = 2 THEN 'A'
					WHEN approvedStatus = 3 THEN 'R'
					ELSE 'P'
					END as approvedStatus
		           FROM `job_mrf` jm left join tbl_mst_dept tmd on jm.departmentId = tmd.id left join tbl_mst_designation tbds on jm.proposedRoleId = tbds.id left join tbl_mst_state tms  on jm.jobState = tms.state_id left join tbl_mst_city tmc on jm.locationId = tmc.cityId left join tbl_emp_master tem on jm.requisitionAddedBy = tem.empId
				   left join tbl_emp_master tem1 on jm.mrfOpenRequestHandleBy = tem1.empId  where jm.job_id =$mrfId  and jm.status != 0 ". $addSql;
		
	
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

	function get_view_mrf_details_to_hr($resultType = 'G',$mrfId)
	{
		$addSql = "  ";
		 	$sql = "SELECT jm.job_Id,jm.noOfPosition,DATE_FORMAT(jm.isCreated,'%d-%b-%Y') as isCreated,jm.isUpdated,DATE_FORMAT(jm.buhApprovalDate,'%d-%b-%Y') as buhApprovalDate,DATE_FORMAT(jm.recruiterAcceptDate,'%d-%b-%Y') as recruiterAcceptDate,
		            jm.status,CONCAT(tem1.empFname, ' ', tem1.empLname) as mrfOpenRequestHandleBy,GET_TOTAL_CANDIDATE_SHORTLISTED(jm.job_Id)  as totalCandidateShortlist,jm.total_candidate_selected as totalCandidateSelected,GET_TOTAL_CANDIDATE_IN_MRF(jm.job_Id) as totalCandidate,
					GET_TOTAL_INTERVIEW_CONDUCTED(jm.job_Id) as totalInterviewConducted,GET_TOTAL_CANDIDATE_SHORTLIST_BY_MANAGER(jm.job_Id) as totalCandidateShortlistByManager,GET_TOTAL_CANDIDATE_SHORTLIST_BY_BHU(jm.job_Id) as bhuTotalRequest,
					GET_BHU_APPROVED_CANDIDATE(jm.job_Id) as bhuApprovedCandidate,
					CASE
					WHEN approvedStatus = 2 THEN 'A'
					WHEN approvedStatus = 3 THEN 'R'
					ELSE 'P'
					END as approvedStatus
		           FROM `job_mrf` jm left join tbl_mst_dept tmd on jm.departmentId = tmd.id left join tbl_mst_designation tbds on jm.proposedRoleId = tbds.id left join tbl_mst_state tms  on jm.jobState = tms.state_id left join tbl_mst_city tmc on jm.locationId = tmc.cityId left join tbl_emp_master tem on jm.requisitionAddedBy = tem.empId
				   left join tbl_emp_master tem1 on jm.mrfOpenRequestHandleBy = tem1.empId  where jm.job_id =$mrfId  and jm.status != 0 ". $addSql;
		
	
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

  function get_all_candidate_list_to_manager($mrfId,$type){
	   if($type == 1){	
		   	$addSql = " where cmd.mrfId = $mrfId and isApprovedByManager = 1";                 
	   } else if($type == 2) {
	    	$addSql = " where cmd.mrfId = $mrfId and cmd.interviewDate != '0000-00-00 00:00:00' and cmd.isCandidateSelected = 0 and cmd.finalistedBhuStatus = 0 group by cmd.candId";
	   } else if($type == 4) {
		     $addSql = " where cmd.mrfId = $mrfId ";
       } 
	   $sql = "select cmd.*,tmc.cityName from candidate_mrf_details as cmd left join tbl_mst_city as tmc  on cmd.currentLocation = tmc.cityId $addSql"; 
	  
	   return $this->db->query($sql)->result_array();
  }

  function get_selected_candidate_by_hiring_manager($mrfId,$type){
	   $sql = "select cmd.*,tmc.cityName from candidate_mrf_details as cmd left join tbl_mst_city as tmc 
	           on cmd.currentLocation = tmc.cityId left join  interview_take as it on cmd.candId = it.candId and it.mrfId = $mrfId and it.interview = 2 and it.status =1 group by it.mrfId order by id desc"; 	  
	   return $this->db->query($sql)->result_array();
  }
 
  function get_all_request_submitted_for_approval($mrfId,$type){
	$sql = "SELECT  cmd.* FROM  candidate_mrf_details as  cmd LEFT JOIN `job_mrf` jm ON `cmd`.`mrfId` = `jm`.`job_id` LEFT JOIN`candidate_iaf_form` cif 
	       ON `cmd`.`candId` = `cif`.`candId` left join tbl_mst_city as tmc 
	           on cmd.currentLocation = tmc.cityId  WHERE `cmd`.`mrfId` = $mrfId and cmd.bhuFinalApproval = 1"; 	  
	return $this->db->query($sql)->result_array();
  }

  function fetch_all_employee_dept_wise($departmentId){
	$this->db->select("empId,concat(tem.empFname,' ',tem.empLname) as empName,empImage",false);
	$this->db->where('empDept',$departmentId);
	$this->db->where('empId >=','20000001'); 	 
	$this->db->where('isActive',1);     
	$query = $this->db->get("tbl_emp_master as tem");      
	if($query->num_rows() > 0){       
		$result = $query->result_array(); 
	} else {
		$result = array(); 
	}
	return $result;
  }

  function checkCandidateEmailExist($email,$mrfid){
	$this->db->select("count(cmd.candId) as totalCount");
	$this->db->where('cmd.email_id',$email);
	$this->db->where('cmd.mrfId',$mrfid); 
	$query = $this->db->get("candidate_mrf_details as cmd");      
	$result = $query->row_array(); 
	if($result['totalCount'] > 0){
       $checkStatus  = 1;
	} else {
       $checkStatus  = 0;
	}
	return $checkStatus;
  }

  function checkCandidateMobileExist($mob,$mrfid){
	$this->db->select("count(cmd.candId) as totalCount");
	$this->db->where('cmd.mobile',$mob);
	$this->db->where('cmd.mrfId',$mrfid); 
	$query = $this->db->get("candidate_mrf_details as cmd");   
	
	$result = $query->row_array(); 
	if($result['totalCount'] > 0){
       $checkStatus  = 1;
	} else {
       $checkStatus  = 0;
	}
	return $checkStatus;
  }

  function checkMobileEmailExist($mrfid,$email,$mob){
	$where = "cmd.mrfId = $mrfid and ( cmd.mobile=$mob or cmd.email_id= '".$email."')";
	$this->db->select("*");
	$this->db->where($where); 
	$query = $this->db->get("candidate_mrf_details as cmd"); 
	$result = $query->row_array(); 
	return $result;
  }


function get_all_mrf_listing_to_bhu($resultType='G',$deptId){
	$addSql = "  ";      
  
	$uId = $this->session->userdata('admin_id');

 if($this->input->post('filters')!='') {   
	 $filterResultsJSON = json_decode($this->input->post('filters'));
	 $filterArray           = get_object_vars($filterResultsJSON);			
	 if(!empty($filterArray['rules'])) {
		 $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
	 }
}		
		  
$sql = "SELECT jm.job_Id,jm.noOfPosition,jm.total_candidate_selected as totalCandidateSelected,jm.status,tmd.name as departmentName,tbds.name as designationName,tms.state_name as stateName,tmc.cityName,CONCAT(tem.empFname, ' ', tem.empLname) as addedBy,CONCAT(temapp.empFname, ' ', temapp.empLname) as approvedBy,CONCAT(tem1.empFname, ' ', tem1.empLname) as mrfOpenRequestHandleBy, DATE_FORMAT(jm.isCreated,'%d-%b-%Y') as isCreated,jm.isUpdated,
				GET_TOTAL_CANDIDATE_SHORTLISTED(jm.job_Id)  as totalCandidateShortlist,jm.total_candidate_selected as totalCandidateSelected,GET_TOTAL_CANDIDATE_IN_MRF(jm.job_Id) as totalCandidate,
			  GET_TOTAL_INTERVIEW_CONDUCTED(jm.job_Id) as totalInterviewConducted,CASE
			WHEN approvedStatus = 2 THEN 'A'
			WHEN approvedStatus = 3 THEN 'R'
			ELSE 'P'
			END as approvedStatus
		   FROM `job_mrf` jm left join tbl_mst_dept tmd on jm.departmentId = tmd.id left join tbl_mst_designation tbds on jm.proposedRoleId = tbds.id left join tbl_mst_state tms  on jm.jobState = tms.state_id left join tbl_mst_city tmc on jm.locationId = tmc.cityId left join tbl_emp_master tem on jm.requisitionAddedBy = tem.empId
		   left join tbl_emp_master temapp  on   jm.requisitionApprovedBy = temapp.empId  left join tbl_emp_master tem1 on jm.mrfOpenRequestHandleBy = tem1.empId  where jm.departmentId = $deptId and jm.status != 0 ".$addSql;	


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

}

