<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class helpdesk_support_master extends parent_model {
	var $u_column = 'id';		

    function decodeFilters($filters)
	{
		$sql = ' (';
		$objJson = json_decode($filters);
               
		foreach($objJson->{'rules'} as $rules)
		{
		   if($rules->{'field'}!="")
		   {
			if($rules->{'field'}=='empName')
			{
                        //$sql .= 
                                /////////////
				$sql .= ' ( ';
				$expKey = explode(' ',$rules->{'data'});
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
				$sql .= $objJson->{'groupOp'}.' ';
                                //////////
			}
			else
			{
			if ((count($objJson->{rules})>=2))
			{
			$addand=4;
			}
			else
			{
			$addand=5;
			}
			$flag="";
			//new  test 18-Aug-18
				foreach ($objJson->{'rules'} as $i=>$rules)
		       {
			   if($rules->{'field'}=='e.empFname')
			{
			
			$sql .= ' ( ';
				$expKey = explode(' ',$rules->{'data'});
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
		    $sql .= $objJson->{'groupOp'}.' ';
			unset($objJson->{'rules'}[$i]);
			}
		
            if($rules->{'field'}=='hdt.isCreated')
			{
			$start="";
			$end="";
			
			$sql .= ' ( ';
				$expKey = explode('-',$rules->{'data'});
				$start=(date('Y-m-d',strtotime($expKey[0])));
				$end=(date('Y-m-d',strtotime($expKey[1])));
				 $sql  .= " DATE_FORMAT(hdt.isCreated, '%Y-%m-%d') >= '".$start."'" ;
				$sql  .= " and DATE_FORMAT(hdt.isCreated, '%Y-%m-%d') <= '".$end."'"; $sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
				unset($objJson->{'rules'}[$i]);
			}

			}
			
			//new  test 18-Aug-18
			//$aFilters[$rules->field] = $rules->data;
			foreach ($objJson->{'rules'} as $rules)
		       {
			   
				$sql .= $rules->{'field'}.' '; // field name
				$sql .= $this->decodeGridOP($rules->{'op'},$rules->{'data'}).' '; // op, val
				$sql .= $objJson->{'groupOp'}.' '; // and, or 
				
				}
			}
			
		

		$sql = rtrim($sql, $objJson->{'groupOp'}.' ');
		return $sql.') ';
		}
		}
	}

		//---------------Request Master --------//
		function get_team_details($resultType='G')
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
			
			//$where = '';
						
			$sql = "select hst.id,team_name,em.empFname as addedBy,
							DATE_FORMAT(hst.isCreated,'%d-%M-%Y') as isCreated , 
							hst.status as status , depId.name as name ,hst.departId as departId
					from 
					helpdesk_support_team hst left join tbl_emp_master em on 
					hst.addedBy = em.empid left join tbl_mst_dept depId on 
					hst.departId = depId.id where 1=1 ".$addSql."";
			//echo $sql; die;
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
		
		function duplicateTeamName($name,$departId)
		{
			$sql="select id from helpdesk_support_team where team_name='".$name."' and departId ='".$departId."'";
		//echo $sql;die;
			$result=$this->db->query($sql)->result_array();
			return $result;
		}
		
		function duplicateRequestName($name,$teamId)
		{
			$sql="select id from helpdesk_request_type where request_name='".$name."' and teamId='".$teamId."'";
			//echo $sql;die;
			$result=$this->db->query($sql)->result_array();
			return $result;
		}
		
		
		
		// Query Request
		
		function get_request_details()
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

			$sql = "select tqt.id,hst.team_name as team_name,request_name,DATE_FORMAT(tqt.isCreated,'%d-%M-%Y') as  isCreated , tqt.status as status from helpdesk_request_type tqt left join helpdesk_support_team hst on tqt.teamId = hst.id where 1=1";
			//echo $sql; die;
			return $this->db->query($sql)->result_array();	
			//return $result;
		}
		
		// Query Type
		
		function get_query_details($resultType='G')
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

			
			$sql = "select id,team_group,em.empFname as addedBy,DATE_FORMAT(tqt.isCreated,'%d-%M-%Y') as  isCreated , tqt.status as status from helpdesk_query_type tqt left join tbl_emp_master em on tqt.addedBy = em.empid where 1=1 ".$addSql."";
			//echo $sql; die;
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
		
		
		//---------------Priority  --------//
		
		function get_priority_details($resultType='G')
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
			
			$sql = "select id,priority_type,em.empFname as addedBy,DATE_FORMAT(hp.isCreated,'%d-%M-%Y') as  isCreated , hp.status as status from helpdesk_priority hp left join tbl_emp_master em on hp.addedBy = em.empid where 1=1 ".$addSql."";
			//echo $sql; die;
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
		
		function duplicatePriorityName($name)
		{
			$sql="select id from helpdesk_priority where priority_type='".$name."'";
			$result=$this->db->query($sql)->result_array();
			return $result;
		}
		
		function get_status_details($resultType='G')
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

			$sql = "select id,s_type,em.empFname as addedBy,DATE_FORMAT(hs.isCreated,'%d-%M-%Y') as  isCreated , hs.status as status from helpdesk_status hs left join tbl_emp_master em on hs.addedBy = em.empid where 1=1 ".$addSql."";
			//echo $sql; die;
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
		
		function duplicateStatusName($name)
		{
			$sql="select id from helpdesk_status where s_type='".$name."'";
			$result=$this->db->query($sql)->result_array();
			return $result;
		}
		
		function get_ticket_details($resultType='G')
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
			
			$teamId = $this->geTeamIdSuppTeam($uId);
			$teamId = isset($teamId[0]['teamId']) && $teamId[0]['teamId'] !='' ? $teamId[0]['teamId']:'';
			
			if($uId == 10000)
			{   
				$joinMapTeam = "";
				$groupby = 'group by hdt.ticked_id';
			}
			else if($teamId != '')
			{	
				$joinMapTeam  = "left join helpdesk_map_team bmt on	hdt.teamId	= bmt.teamId";	
				$where = " and bmt.empId  = '".$uId."'";
				$groupby = '';
			} else {
				$joinMapTeam = "";
				$where = " and hdt.isCreatedEmpId  = '".$uId."'";
				$groupby = '';
			}
	
			$sql = "select 
						hdt.ticked_id as id,hdt.teamId,request_name,concat(em.empFname,' ',em.empLname) as empFname,concat(tem.empFname,' ',tem.empLname) as assigneeName,hdt.fromEmailId,hdt.from_email_status,hdt.isCreatedby,
						DATE_FORMAT(hdt.isCreated,'%d-%b-%y %H:%i') as isCreated ,
						DATE_FORMAT(hdt.isUpdated,'%d-%b-%y %H:%i') as isUpdated, 
						DATE_FORMAT(hdt.isCreated,'%d-%b-%y %H:%i') as isCreatedDate,
						DATE_FORMAT(hdt.isUpdated,'%d-%b-%y %H:%i') as isUpdatedDate,
						hdt.status as status,hdt.closed_time ,s_type , subject ,team_name,
						em.jobLocation as jobLocation,
						tat,
						getLocationHelpdesk(em.jobLocation) as location
					from 
						helpdesk_ticked_records hdt left join tbl_emp_master em on 
						hdt.isCreatedby = em.empid left join helpdesk_support_team st on 
						hdt.teamId = st.id left join helpdesk_status hs on 
						hdt.status = hs.id left join helpdesk_request_type msreq on 
						hdt.requestId = msreq.id $joinMapTeam left join  tbl_emp_master tem  on hdt.handleBy = tem.empId where 1=1 ".$where." ".$addSql." ".$groupby." order by hdt.isCreated desc";
			//echo $sql;
			//exit;
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
		
		
		function getEmplistuser($id)
		{
			$uId = $this->session->userdata('admin_id');
			$sql="select empId,empFname,empMname,empLname from tbl_emp_master where empDept='".$id."'";
		
			return $this->db->query($sql)->result_array();	
		}
		
		function duplicateTeamMap($teamId)
		{
			$sql="select id from helpdesk_map_team where teamId='".$teamId."'";
			$result=$this->db->query($sql)->result_array();
			return $result;
		}
		
		function getRequestlist($id)
		{
			$uId = $this->session->userdata('admin_id');
			$sql="select id,request_name,teamId from helpdesk_request_type where teamId='".$id."'";
			return $this->db->query($sql)->result_array();	
		}
		
		function getTeamName($teamId)
		{
			$uId = $this->session->userdata('admin_id');
			$sql="select id,team_name,departId,email,type from helpdesk_support_team where id='".$teamId."'";
			return $this->db->query($sql)->result_array();	
		}
		
		function geTeamIdSuppTeam($id)
		{
				$uId = $this->session->userdata('admin_id');
				$sql="select empId , teamId , status  from helpdesk_map_team where empId='".$uId."' and status=1";				
				$result = $this->db->query($sql)->result_array();	
				//print_r($result);die;
				return $result;
		}
		
		function getviewticket($id)
		{
			$sql = "select 
						hdt.ticked_id as id,request_name,hdt.subject,hdt.teamId,hdt.fromEmailId,em.empFname as empFname,
						DATE_FORMAT(hdt.isCreated,'%d-%M-%Y') as isCreated ,
						DATE_FORMAT(htk.t_isUpdated,'%d-%M-%Y') as t_isUpdated,hdt.ticket_type,hdt.isCreatedEmpId,
						htk.t_cc,htk.t_bcc,htk.to_empId,htk.t_addedby,hdt.isCreatedby,hdt.status as status ,hs.s_type , subject ,team_name,
						em.jobLocation as jobLocation,
						st.email as email,st.team_name,	
						getLocationHelpdesk(em.jobLocation) as location
					from 
						helpdesk_ticked_records hdt left join tbl_emp_master em on 
						hdt.isCreatedby = em.empid left join helpdesk_support_team st on 
						hdt.teamId = st.id left join helpdesk_status hs on 
						hdt.status = hs.id left join helpdesk_request_type msreq on 
						hdt.requestId = msreq.id left join helpdesk_transaction_tkrecords htk on 
						hdt.ticked_id = htk.t_ticked_id where hdt.ticked_id='".$id."' order by htk.t_id desc ";
					
			$result = $this->db->query($sql)->result_array();	
			
			return $result;
		}
		
		function gettickedSummary($id)
		{
			$sql = "select 
						hdt.t_ticked_id  as t_ticked_id,hdt.t_addedby,btr.fromEmailId,em.empFname as empFname,
						DATE_FORMAT(hdt.t_isUpdated,'%d-%M-%Y %r') as t_isUpdated,hdt.t_cc,hdt.to_empId,hdt.t_fromEmail,
						hdt.t_fromEmpId,hdt.t_status  as status ,s_type , hdt.t_attach  ,hdt.t_message,
						hdt.t_id  as t_id,btr.ticket_type,hdt.to_empId				
					from 
						helpdesk_transaction_tkrecords hdt left join helpdesk_ticked_records btr
						 on hdt.t_ticked_id = btr.ticked_id
						left join tbl_emp_master em on 
						hdt.t_addedby = em.empid left join helpdesk_status hs on 
						hdt.t_status  = hs.id where hdt.t_ticked_id = '".$id."'  ORDER BY t_id DESC ";
						$result = $this->db->query($sql)->result_array();	
						
						return $result;
		}
		
		function gettickedLastSummary($id)
		{
			$sql = "select 
						hdt.t_ticked_id  as t_ticked_id ,hdt.to_empId,hdt.t_fromEmail,hdt.t_attach,hdt.t_mailType,hdt.t_fwdTo,em.empFname as empFname,
						DATE_FORMAT(hdt.t_isUpdated,'%d-%M-%Y') as t_isUpdated, 
						hdt.t_status  as status ,s_type , hdt.t_attach  ,hdt.t_message,
						hdt.t_id  as t_id					
					from 
						helpdesk_transaction_tkrecords hdt left join tbl_emp_master em on 
						hdt.t_addedby = em.empid left join helpdesk_status hs on 
						hdt.t_status  = hs.id where hdt.t_ticked_id = '".$id."' ORDER BY t_id DESC limit 0,1";
						$result = $this->db->query($sql)->result_array();	
						
						return $result;
		}
		
		function getTAT($id)
		{
			$uId = $this->session->userdata('admin_id');
			$sql="select id,request_name,teamId,tat from helpdesk_request_type where id='".$id."'";
			return $this->db->query($sql)->result_array();	
		}
		
		 function search($keyword, $conditions)
		{
			$this->db->like('empFname', $keyword, $conditions);
			return $this->db->get('tbl_emp_master')->result();
		}
		
		function getEmployeeId($email){
			 $this->db->select('empId');
			 $this->db->where('empEmailOffice',$email);
			 return $this->db->get('tbl_emp_master')->row_array();
		}
		
		function getTeamData($email){
			 $this->db->select('id');
			 $this->db->where('email',$email);
			 return $this->db->get('helpdesk_support_team')->row_array();
		} 
		
		public function checkEmailRecordExist($subject,$fromEmail){
			 $this->db->select('*');
			 $this->db->where('subject',trim($subject));
			 $this->db->where('fromEmailId',trim($fromEmail));			 
			 return $this->db->get('helpdesk_ticked_records')->row_array();
			
		}
		

		public function checkTicketSubjectExist($subject,$teamId,$uid){
			$this->db->select('*');
			$this->db->where('(subject="'.trim($subject).'" or mailUniqueNo = "'.$uid.'")');
            $this->db->where('teamId',$teamId);
			return $this->db->get('helpdesk_ticked_records')->row_array();		   
	    }


		public function checkEmailDateExist($subject,$fromEmail,$dateData){
			 $this->db->select('*');
			 $this->db->where('t_subject',$subject);
			 $this->db->where('t_fromEmail',$fromEmail);
			 $this->db->where('t_isCreated',$dateData);			 
			 return $this->db->get('helpdesk_transaction_tkrecords')->row_array();			
		}
		
		/**
		
		**/
		public function checkBrandPartnerEmailCheck($email){
			 $this->db->select('*');		 
			 $this->db->where('partner_email',$email);			 
			 return $this->db->get('helpdesk_partner')->row_array();	
		}
		
		function fetch_brand_list($brandId){
			$this->db->select('*');		 
			$this->db->where('brand_id',$brandId);	
			$this->db->where('status',1);	
			$this->db->order_by('id','desc');		 
			return $this->db->get('helpdesk_partner')->result_array();      
		}
		
		function fetch_brand_assign_employee($brandId){
			
			$this->db->select("concat(tem.empFname,' ',tem.empLname) as empFname,tem.empEmailOffice",false);
			$this->db->from('helpdesk_map_team as bmt');
			$this->db->join('tbl_emp_master as tem', 'bmt.empId = tem.empId');
			$this->db->where('bmt.teamId',$brandId);
			$this->db->where('bmt.status',1);	
			$this->db->where('tem.isActive',1);	
			$result = $this->db->get()->result_array();
			return $result;  
		}  
		
		// fetch support email data 
		public function getSupportIdData($empId){
			 $this->db->select('hmt.teamId,hst.user_name,hst.password,hst.team_name,hst.id,hst.email,type');
			 $this->db->from('helpdesk_map_team as hmt');
			 $this->db->join('helpdesk_support_team as hst', 'hmt.teamId = hst.id');
			 $this->db->where('empId',$empId);
			 $result = $this->db->get()->result_array();			
			 return $result;
		}
		// fetch employee details
		public function fetchEmployeeDetails($emailId){
			  $this->db->select("empId,concat(tem.empFname,' ',tem.empLname) as empFname,empEmailOffice,jobLocation",false);
			  $this->db->from('tbl_emp_master as tem');
			  $this->db->where('empEmailOffice',$emailId);
			  $this->db->where('isActive',1);
			  $result = $this->db->get()->row_array();			
			  return $result;
		}	
		
		// fetch employee details
		public function fetchEmployeeDataById($empId){
			  $this->db->select("empId,concat(tem.empFname,' ',tem.empLname) as empFname,empEmailOffice,jobLocation",false);
			  $this->db->from('tbl_emp_master as tem');
			  $this->db->where('empId',$empId);
			  $this->db->where('isActive',1);
			  $result = $this->db->get()->row_array();			
			  return $result;
		}	
		
		// fetch employee details
		public function fetchEmployeeState($cityId){
			  $this->db->select("state");
			  $this->db->from('tbl_mst_city as tmc');
			  $this->db->where('cityId ',$cityId);
			  $this->db->where('status',1);
			  $result = $this->db->get()->row_array();			
			  return $result;
		}
		
        public function getEmpTeamListData($empId){
		
			if($empId != '10000'){
				$this->db->select("hst.id,hst.team_name");
				$this->db->from("helpdesk_map_team as hmt");
				$this->db->join("helpdesk_support_team as hst","hmt.teamId = hst.id",'LEFT');			
				$this->db->where('hmt.empId',$empId);
			} else {
				$this->db->select("hst.id,hst.team_name");
				$this->db->from("helpdesk_support_team as hst");
				$this->db->where('hst.status','1');
			}  			
			$result = $this->db->get()->result_array();			
			return $result;
		}
       
       public function getMaxMsgNo($supportId){		   
		  $this->db->select_max('mailUniqueNo');
		  $this->db->where('teamId',$supportId);
		  $result = $this->db->get('helpdesk_ticked_records')->row();	
		  if($result->mailUniqueNo != ''){
			return $result->mailUniqueNo;
		  } else {
            return 0;
		  }	
	   }

	   public function checkGroupEmailExist($fromaddr){
			$this->db->select('email_id as empFname,id as empId');		
			$this->db->where('email_id',$fromaddr);			 
			return $this->db->get('group_email_id')->row_array();			
       }
	   
	   
	   	// fetch support email data 
		public function getAllSupportIdData(){
			 $this->db->select('hst.id as teamId,hst.user_name,hst.password,hst.team_name,hst.id,hst.email');
			 $this->db->from('helpdesk_support_team as hst');
             $this->db->where('status',1);	         			 
			 $result = $this->db->get()->result_array();			
			 return $result;
		}
	    
		public function fetchTicketRecordsById($ticketId){
			 $this->db->select('isUpdated,isCreated');
			 $this->db->where('ticked_id',$ticketId);					 
			 return $this->db->get('helpdesk_ticked_records')->row_array();			
		}

             // fetch all support email data
		public function getAllSupportEmailData(){
			$this->db->select('hst.id as 					teamId,hst.user_name,hst.password,hst.team_name,hst.id,hst.email,GET_TOTAL_OPEN_EMAILS(hst.id) as total_open,GET_TOTAL_NEW_EMAILS(hst.id) as total_new,GET_TOTAL_PENDING_EMAILS(hst.id) as total_pending');
			$this->db->from('helpdesk_support_team as hst');
			$this->db->where('status',1);
			//$this->db->where('type',1);	   	         			 
			$result = $this->db->get()->result_array();			
			return $result;
	   }
	   
	   // get all employee of support emails
	   function getSupportTeamData($id){
			$this->db->select('GROUP_CONCAT(tem.empEmailOffice SEPARATOR ",") as emailList',false);
			$this->db->from('helpdesk_map_team as hmt');
			$this->db->join('tbl_emp_master as tem','hmt.empId = tem.empId ','LEFT');
			$this->db->where('hmt.teamId',$id);	
			$this->db->where('tem.isActive',1);	  
			$this->db->where('hmt.status',1);			     			 
			$result = $this->db->get()->row_array();
			return $result; 
	   }
        
        // fetch all pending email data
        function getAllPendingEmailData($id){
			$this->db->select('ticked_id,subject,isCreated,fromEmailId',false);
			$this->db->from('helpdesk_ticked_records as htr');		
			$this->db->where('htr.teamId',$id);	
			$where = '(status="1" or status="2" or status="3")';
			$this->db->where($where);		     			 
			$result = $this->db->get()->result_array();			
			return $result; 
	   }
	   
	}	
	
