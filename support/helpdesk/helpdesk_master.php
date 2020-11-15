<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class helpdesk_master extends parent_model {
		var $u_column = 'id';
		
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
					brand_support_team hst left join tbl_emp_master em on 
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
			$sql="select id from brand_support_team where team_name='".$name."' and departId ='".$departId."'";
		//echo $sql;die;
			$result=$this->db->query($sql)->result_array();
			return $result;
		}
		
		function duplicateRequestName($name,$teamId)
		{
			$sql="select id from brand_request_type where request_name='".$name."' and teamId='".$teamId."'";
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

			$sql = "select tqt.id,hst.team_name as team_name,request_name,DATE_FORMAT(tqt.isCreated,'%d-%M-%Y') as  isCreated , tqt.status as status from brand_request_type tqt left join brand_support_team hst on tqt.teamId = hst.id where 1=1";
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

			
			$sql = "select id,team_group,em.empFname as addedBy,DATE_FORMAT(tqt.isCreated,'%d-%M-%Y') as  isCreated , tqt.status as status from brand_query_type tqt left join tbl_emp_master em on tqt.addedBy = em.empid where 1=1 ".$addSql."";
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
			
			$sql = "select id,priority_type,em.empFname as addedBy,DATE_FORMAT(hp.isCreated,'%d-%M-%Y') as  isCreated , hp.status as status from brand_priority hp left join tbl_emp_master em on hp.addedBy = em.empid where 1=1 ".$addSql."";
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
			$sql="select id from brand_priority where priority_type='".$name."'";
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

			$sql = "select id,s_type,em.empFname as addedBy,DATE_FORMAT(hs.isCreated,'%d-%M-%Y') as  isCreated , hs.status as status from brand_status hs left join tbl_emp_master em on hs.addedBy = em.empid where 1=1 ".$addSql."";
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
			$sql="select id from brand_status where s_type='".$name."'";
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
			$teamId = $teamId[0]['teamId'];
			
			if($uId == 10000)
			{
				$where = '';
			}
			else
			{				
				$where = " and bmt.empId  = '".$uId."'";
			}
		
			$sql = "select 
						hdt.ticked_id as id,request_name,concat(em.empFname,' ',em.empLname) as empFname,concat(tem.empFname,' ',tem.empLname) as assigneeName,hdt.fromEmailId,hdt.from_email_status,hdt.isCreatedby,
						DATE_FORMAT(hdt.isCreated,'%d-%b-%y %h:%i %p') as isCreated ,
						DATE_FORMAT(hdt.isUpdated,'%d-%b-%y %h:%i %p') as isUpdated, 
						hdt.status as status ,s_type , subject ,team_name,
						em.jobLocation as jobLocation,
						tat,
						getLocationHelpdesk(em.jobLocation) as location
					from 
						brand_ticked_records hdt left join tbl_emp_master em on 
						hdt.isCreatedby = em.empid left join brand_support_team st on 
						hdt.teamId = st.id left join brand_status hs on 
						hdt.status = hs.id left join brand_request_type msreq on 
						hdt.requestId = msreq.id left join brand_map_team bmt on
						hdt.teamId	= bmt.teamId left join  tbl_emp_master tem  on hdt.handleBy = tem.empId where 1=1 ".$where." ".$addSql." group by hdt.ticked_id";
		
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
			$sql="select id from brand_map_team where teamId='".$teamId."'";
			$result=$this->db->query($sql)->result_array();
			return $result;
		}
		
		function getRequestlist($id)
		{
			$uId = $this->session->userdata('admin_id');
			$sql="select id,request_name,teamId from brand_request_type where teamId='".$id."'";
			return $this->db->query($sql)->result_array();	
		}
		
		function getTeamName($teamId)
		{
			$uId = $this->session->userdata('admin_id');
			$sql="select id,team_name,departId from brand_support_team where id='".$teamId."'";
			return $this->db->query($sql)->result_array();	
		}
		
		function geTeamIdSuppTeam($id)
		{
				$uId = $this->session->userdata('admin_id');
				$sql="select empId , teamId , status  from brand_map_team where empId='".$uId."' and status=1";				
				$result = $this->db->query($sql)->result_array();	
				//print_r($result);die;
				return $result;
		}
		
		function getviewticket($id)
		{
			$sql = "select 
						hdt.ticked_id as id,request_name,hdt.subject,hdt.teamId,hdt.fromEmailId,em.empFname as empFname,
						DATE_FORMAT(hdt.isCreated,'%d-%M-%Y') as isCreated ,
						DATE_FORMAT(htk.t_isUpdated,'%d-%M-%Y') as t_isUpdated, 
						htk.t_cc,htk.t_bcc,htk.t_toEmail,htk.t_addedby,hdt.isCreatedby,hdt.status as status ,s_type , subject ,team_name,
						em.jobLocation as jobLocation,
						st.email as email,	
						getLocationHelpdesk(em.jobLocation) as location
					from 
						brand_ticked_records hdt left join tbl_emp_master em on 
						hdt.isCreatedby = em.empid left join brand_support_team st on 
						hdt.teamId = st.id left join brand_status hs on 
						hdt.status = hs.id left join brand_request_type msreq on 
						hdt.requestId = msreq.id left join brand_transaction_tkrecords htk on 
						hdt.ticked_id = htk.t_ticked_id where hdt.ticked_id='".$id."' order by htk.t_id desc ";
			$result = $this->db->query($sql)->result_array();	
			
			return $result;
		}
		
		function gettickedSummary($id)
		{
			$sql = "select 
						hdt.t_ticked_id  as t_ticked_id,hdt.t_toEmail,hdt.t_fromEmail,hdt.t_addedby,btr.fromEmailId,em.empFname as empFname,
						DATE_FORMAT(hdt.t_isUpdated,'%d-%M-%Y %r') as t_isUpdated,hdt.t_cc,
						hdt.t_fromEmpId,hdt.t_status  as status ,s_type , hdt.t_attach  ,hdt.t_message,
						hdt.t_id  as t_id					
					from 
						brand_transaction_tkrecords hdt left join brand_ticked_records btr
						 on hdt.t_ticked_id = btr.ticked_id
						left join tbl_emp_master em on 
						hdt.t_addedby = em.empid left join brand_status hs on 
						hdt.t_status  = hs.id where hdt.t_ticked_id = '".$id."'  ORDER BY t_id DESC ";
						$result = $this->db->query($sql)->result_array();	
						
						return $result;
		}
		
		function gettickedLastSummary($id)
		{
			$sql = "select 
						hdt.t_ticked_id  as t_ticked_id ,hdt.t_toEmail,hdt.t_fromEmail,hdt.t_attach,hdt.t_mailType,hdt.t_fwdTo,em.empFname as empFname,
						DATE_FORMAT(hdt.t_isUpdated,'%d-%M-%Y') as t_isUpdated, 
						hdt.t_status  as status ,s_type , hdt.t_attach  ,hdt.t_message,
						hdt.t_id  as t_id					
					from 
						brand_transaction_tkrecords hdt left join tbl_emp_master em on 
						hdt.t_addedby = em.empid left join brand_status hs on 
						hdt.t_status  = hs.id where hdt.t_ticked_id = '".$id."' ORDER BY t_id DESC limit 0,1";
						$result = $this->db->query($sql)->result_array();	
						
						return $result;
		}
		
		function getTAT($id)
		{
			$uId = $this->session->userdata('admin_id');
			$sql="select id,request_name,teamId,tat from brand_request_type where id='".$id."'";
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
			 return $this->db->get('brand_support_team')->row_array();
		} 
		
		public function checkEmailRecordExist($subject,$fromEmail){
			 $this->db->select('*');
			 $this->db->where('subject',$subject);
			 $this->db->where('fromEmailId',$fromEmail);			 
			 return $this->db->get('brand_ticked_records')->row_array();
			
		}
		
		public function checkEmailDateExist($subject,$fromEmail,$dateData){
			 $this->db->select('*');
			 $this->db->where('t_subject',$subject);
			 $this->db->where('t_fromEmail',$fromEmail);
			 $this->db->where('t_isCreated',$dateData);			 
			 return $this->db->get('brand_transaction_tkrecords')->row_array();			
		}
		
		/**
		
		**/
		public function checkBrandPartnerEmailCheck($email){
			 $this->db->select('*');		 
			 $this->db->where('partner_email',$email);			 
			 return $this->db->get('brand_partner')->row_array();	
		}
		
		function fetch_brand_list($brandId){
			$this->db->select('*');		 
			$this->db->where('brand_id',$brandId);	
			$this->db->where('status',1);	
			$this->db->order_by('id','desc');		 
			return $this->db->get('brand_partner')->result_array();      
		}
		
		function fetch_brand_assign_employee($brandId){
			
			$this->db->select("concat(tem.empFname,' ',tem.empLname) as empFname,tem.empEmailOffice",false);
			$this->db->from('brand_map_team as bmt');
			$this->db->join('tbl_emp_master as tem', 'bmt.empId = tem.empId');
			$this->db->where('bmt.teamId',$brandId);
			$this->db->where('bmt.status',1);	
			$this->db->where('tem.isActive',1);	
			$result = $this->db->get()->result_array();
			return $result;  
		}  
		
		public function checkTicketSubjectExist($subject){
			$this->db->select('*');
			$this->db->where('subject',trim($subject));				 
			return $this->db->get('brand_ticked_records')->row_array();		   
		}
		
		public function getMaxMsgNo(){		   
			$this->db->select_max('mailUniqueNo');
			//$this->db->where('teamId',$supportId);
			$result = $this->db->get('brand_ticked_records')->row();	
			if($result->mailUniqueNo != ''){
			  return $result->mailUniqueNo;
			} else {
			  return 0;
			}	
		 }
		
	}	
	
	
	