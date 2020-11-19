<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class employee_model extends parent_model {

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
		   $sql .= $objJson->{'groupOp'}.' ';
			unset($objJson->{'rules'}[$i]);
			}

			if($rules->{'field'}=='em.empFname')
			{			
			$sql .= ' ( ';
				$expKey = explode(' ',filter_values($rules->{'data'}));
				for($k=0; $k<count($expKey); $k++)
				{
					if($k>0)
					{
						$sql .= " or ";

					}


					$sql  .= "  em.empTitle like '%".$expKey[$k]."%'";
					$sql  .= " or em.empFname like '%".$expKey[$k]."%'";
					$sql  .= " or em.empLname like '%".$expKey[$k]."%'";

                            //$addSql .= " or ";
				}

				$sql .= ' ) ';
		   $sql .= $objJson->{'groupOp'}.' ';
			unset($objJson->{'rules'}[$i]);
			}
			
			if($rules->{'field'}=='tbl_emp_master.empFname')
			{			
			    $sql .= ' ( ';
				$expKey = explode(' ',filter_values($rules->{'data'}));
				for($k=0; $k<count($expKey); $k++)
				{
					if($k>0)
					{
						$sql .= " or ";

					}


					$sql  .= "  tbl_emp_master.empTitle like '%".$expKey[$k]."%'";
					$sql  .= " or tbl_emp_master.empFname like '%".$expKey[$k]."%'";
					$sql  .= " or tbl_emp_master.empMname like '%".$expKey[$k]."%'";
					$sql  .= " or tbl_emp_master.empLname like '%".$expKey[$k]."%'";

                            //$addSql .= " or ";
				}

				$sql .= ' ) ';
		        $sql .= $objJson->{'groupOp'}.' ';
			    unset($objJson->{'rules'}[$i]);
			}
			

			if($rules->{'field'}=='de.name')
			{
				
			$sql .= ' ( ';
		
			
				$sql  .= "de.name like '%".filter_values($rules->{'data'})."%'";
				
				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
				unset($objJson->{'rules'}[$i]);
			} 


			if($rules->{'field'}=='range.empDOJ')
			{
			$start="";
			$end="";
			
			$sql .= ' ( ';
				$expKey = explode('-',filter_values($rules->{'data'}));
				$start=(date('Y-m-d',strtotime($expKey[0])));
				$end=(date('Y-m-d',strtotime($expKey[1])));
				 $sql  .= "e.empDOJ >= '".$start."'" ;
				$sql  .= "and  e.empDOJ <= '".$end."'"; $sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
				unset($objJson->{'rules'}[$i]);
			} 
			if($rules->{'field'}=='tep.empDOBactual')
			{
			$start="";
			$end="";
			
			$sql .= ' ( ';
				$expKey = explode('-',filter_values($rules->{'data'}));
				$start  = explode('-',(date('m-d',strtotime($expKey[0]))));
				$end    = explode('-',(date('m-d',strtotime($expKey[1]))));
			
				$sql  .= "month(tep.empDOBactual) between ".$start[0]." and ".$end[0]." and day(tep.empDOBactual) between ".$start[1]." and  ".$end[1];
				
				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
				unset($objJson->{'rules'}[$i]);
			} 

			if($rules->{'field'}=='leaveyear')
			{
			$start="";
			$end="";
			
			$sql .= ' ( ';
		
			
				$sql  .= "Year(tr.fromDate)=".filter_values($rules->{'data'})." and year(tr.regularizationDate) = ".filter_values($rules->{'data'});
				
				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
				unset($objJson->{'rules'}[$i]);
			} 
			
			}
			
			//new  test 18-Aug-18
			//$aFilters[$rules->field] = $rules->data;
			foreach ($objJson->{'rules'} as $rules)
		       {
			   
				$sql .= $rules->{'field'}.' '; // field name
				$sql .= $this->decodeGridOP($rules->{'op'},filter_values($rules->{'data'})).' '; // op, val
				$sql .= $objJson->{'groupOp'}.' '; // and, or 
				
				}
			}
			
		

		$sql = rtrim($sql, $objJson->{'groupOp'}.' ');
		return $sql.') ';
		}
		}
	}

	function get_employee_details($empId=0, $resultType='G',$status=false)
	{
		$addsql = '';
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			// $addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		if($empId > 0)
		{
			$addsql .= " and e.empId=".$empId;
		}
		if($this->input->post('status')!='')
		{
			$addsql .= " and e.status='".$this->input->post('status')."' ";
		}
		else
		{
				//$addsql .= " and e.status='1' ";
		}
		if($status)
		{
			$addsql .= " and e.status!='".$status."' ";
		}

		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select sb.ctc,e.empMobile,can.offerLettersent,can.termEnddate,can.empType,e.joiningFor, e.empDesination, e.regionPermission, e.isActive, e.appointmentLetterSend,DATE_FORMAT(e.appointmentLetterSenddate,'%d-%b-%Y') appointmentLetterSenddate, e.empId,e.empEmailOffice,e.status,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,DATE_FORMAT(e.lastWorkingday,'%d-%b-%Y') lastWorkingday ,  DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,e.candidateId, e.empEmailPersonal, e.empEmailOffice , de.name as desination, pr.name as projects, cl.name as clients, em.empId as managerId, concat(em.empTitle,' ',em.empFname,' ',em.empLname) as manager,em.empEmailOffice as manageremail, emt.name as employeeType,ser.empType as serviceEmpType, ser.ticcard,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName  from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id		
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_EMP." em on e.reportingTo=em.empId
		Left Join ".TABLE_SERVICE." ser on e.empId=ser.empId
		Left Join ".TABLE_EMPTYPE." emt on ser.empType=emt.id
		Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		
		LEFT JOIN ".TABLE_SALARYBREAKUP." sb  on sb.empId=e.empId
		where 1=1 ".$addsql." and e.empId!=10000 ";
		//echo $sql; die;
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		//pre($result);
		return $result;
	}
	function get_client_employee_details($empId=0, $resultType='G',$status=false)
	{
		$addsql = '';
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		if($empId > 0)
		{
			$addsql .= " and e.empId=".$empId;
		}
		if($this->input->post('status')!='')
		{
			$addsql .= " and e.status='".$this->input->post('status')."' ";
		}
		else
		{
				//$addsql .= " and e.status='1' ";
		}
		if($status)
		{
			$addsql .= " and e.status!='".$status."' ";
		}

		if($this->input->post('filters')!='') // search filters
		{
		   /*  pre(self::decodeFilters($this->input->post('filters')));
		   die; */ 
		   if(self::decodeFilters($this->input->post('filters')))
			{  
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
			
		}

		$stateaccess =   $this->modules_model->state_wise_access($this->session->userdata('admin_id')); 
	
		if(isset($stateaccess['stateIds']) && $stateaccess['stateIds'] != ''){
		   $commaSepState   =    $stateaccess['stateIds']; 
		   $stateCond       =   " and e.jobLocation in (".$commaSepState.")";      
		} else {
		   $stateCond = '';
		}

		$viewPermission = isset($stateaccess['viewPermission'])?$stateaccess['viewPermission']:0;
		$sql = "select sb.ctc,e.empMobile,can.offerLettersent,can.termEnddate,ser.empType,e.joiningFor, e.empDesination, e.regionPermission, e.isActive, e.appointmentLetterSend,DATE_FORMAT(e.appointmentLetterSenddate,'%d-%b-%Y') appointmentLetterSenddate,DATE_FORMAT(e.resignationReceiveday,'%d-%b-%Y') resignationReceiveday,e.empId,e.empEmailOffice,e.status,d.name, concat(e.empFname,' ',e.empLname) as empName,DATE_FORMAT(e.lastWorkingday,'%d-%b-%Y') lastWorkingday ,  DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,e.candidateId, e.empEmailPersonal, e.empEmailOffice , de.name as desination, pr.name as projects, cl.name as clients, em.empId as managerId, concat(em.empFname,' ',em.empLname) as manager,em.empEmailOffice as manageremail, emt.name as employeeType,ser.empType as serviceEmpType, ser.ticcard,concat( upper(substring(cityjob.cityName,1,1)),lower(substring(cityjob.cityName,2)) ) as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName,$viewPermission as viewPermission  from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id		
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_EMP." em on e.reportingTo=em.empId
		Left Join ".TABLE_SERVICE." ser on e.empId=ser.empId
		Left Join ".TABLE_EMPTYPE." emt on ser.empType=emt.id
		Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_CITY." cityjob on e.jobLocation=cityjob.cityId
		LEFT JOIN ".TABLE_STATE." s on cityjob.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN ".TABLE_PERSONAL_DETAILS." tpd on tpd.empId=e.empId
		LEFT JOIN ".TABLE_SALARYBREAKUP." sb  on sb.empId=e.empId
		/* LEFT JOIN ".TABLE_DIVISION." td  on e.devision_id=td.id
		 LEFT JOIN ".TABLE_BRAND." tb on e.brand_id=tb.id	*/	
		where 1=1 $stateCond ".$addsql."  and e.empId NOT LIKE '%100%' and e.empId!=10000 and e.empId <= 20000225";
		
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		//pre($result);
		return $result;
	}
	
	function get_team_details($empId=0, $resultType='G')
	{
		
		$addsql = '';
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		if($empId > 0)
		{
			$addsql .= " and e.empId=".$empId;
		}
		if($this->input->post('status')!='')
		{
			$addsql .= " and e.status='".$this->input->post('status')."' ";
		}
		else
		{
			//$addsql .= " and e.status='1' ";
		}

		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select e.appointmentLetterSend,DATE_FORMAT(e.appointmentLetterSenddate,'%d-%b-%Y') as appointmentLetterSenddate, e.empId,e.empEmailOffice,e.status,d.name, concat(e.empFname,' ',e.empLname) as empName,DATE_FORMAT(e.lastWorkingday,'%d-%b-%Y') lastWorkingday ,  DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,e.candidateId,de.name as desination,r.name as region,s.State_Name as state,c.cityName as jobLocation, pr.name as projects, cl.name as clients from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		where 1=1 AND e.status=1
		".$addsql." AND reportingTo=".$this->session->userdata('admin_id')." ";

		//echo $sql; 

		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		//pre($result);
		return $result;
	}
	function get_salary_details($resultType='G', $param1,$param2)
	{

		$addsql = "";
		if($empId > 0)
		{
			$addsql .= " and s.empId=".$empId;
		}
		/// update on 18 nov
		/*if($this->session->userdata('empDesination')=='17' or $this->session->userdata('empDesination')=='9')
		{
			$region = explode(',',$this->session->userdata('admin_region')); 

			for($i=0; $i < count($region); $i++)
			{
				$addsql .=" and tms.region='".$region[$i]."' ";
			}
		}*/
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}

		/////////////////////
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilterssalary($this->input->post('filters'));
		}
		if($param1){
			$addsql .="  and s.month='".$param1."' and s.year='".$param2."'";
		}
		//
		$sql ="select s.mailalertsent, DATE_FORMAT(s.sendMailalertdate,'%d-%b-%Y %h:%i: %p') as sendMailalertdate, s.empId, s.id, s.month, s.year, s.name, concat(h.empTitle,' ',h.empFname,' ',h.empLname) as  uploadedby ,DATE_FORMAT('0000-12-01' + INTERVAL month MONTH,'%b') as monthname, s.smsResponse, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as  mp_name,  concat(e.empTitle,' ',e.empFname,' ',e.empLname) as  empName,  DATE_FORMAT(s.uploaddate,'%d-%b-%Y') uploadedon, DATE_FORMAT(s.smsResponsedate,'%d-%b-%Y %h:%i %p') responseDate, tmg.name as empdg,tmc.cityName as jobCityName,tms.State_Name as jobStateName,r.name as jobRegionName from tbl_emp_salary_slip s
		left join  ".TABLE_EMP." em on s.empId=em.empId
		left join  ".TABLE_EMP." e on s.empId=e.empId
		left join  ".TABLE_EMP." h on h.empId=s.uploadedBy
		left join  tbl_candidate can on can.id=em.candidateId
		left join tbl_mst_city tmc on can.jobCity=tmc.cityId
		left join tbl_mst_state tms on tmc.state=tms.State_Id
		Left Join ".TABLE_REGION." r on tms.region=r.id
		left join tbl_mst_designation tmg on e.empDesination=tmg.id
		WHERE 1=1  ".$addsql."";
		
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

	function getDetails($eid)
	{
		$sql = "select doc.adharNo,ep.empDOBactual,e.empId,ep.emergencyContactNumber,ep.empEmailOffice as official,es.pfNumber,es.uanNumber, es.esicNumber, e.empImage,e.empEmailPersonal,e.empEmailOffice,e.empMobile as contactNo,concat(e.empTitle, ' ',e.empFname,' ',e.empLname) as empName, DATE_FORMAT(e.empDOJ,'%d %M, %Y') empDOJ,e.clients,e.projects,d.name as department,de.name as designation,pr.name as projects,cl.name as clients,r.name as region,s.State_Name as state,c.cityName as jobocation, ma.empMobile,concat(ma.empTitle, ' ',ma.empFname,' ',ma.empLname) as reportingManager, concat(ap.empTitle, ' ',ap.empFname,' ',ap.empLname) as aprovalManager,ep.empGender, ep.empFathersName, ep.empMotherName,ep.empMaritalStatus,ep.spouseName,ep.empNationality,DATE_FORMAT(ep.empDOM,'%d %M, %Y')as mdate,DATE_FORMAT(ep.empDOB,'%d %M, %Y')as dateofbirth, ep.empReligion, b.name as bloodgroup, es.empType, doc.passportNo, doc.passport, doc.panNo, doc.pan, doc.addressProof, doc.photoIdproof, doc.certificates10th, doc.marksheet10th, doc.heighestqualificationsMarksheet, doc.heighestqualificationsCertificates, doc.experienceCertificates, doc.payslip, doc.	relievingLetter, doc.cancelCheck, doc.cVSubmited, doc.esifamilyPhotograph, doc.appointmentLetter, mst_ban.bankName, ban.branch, ban.accountNo, ban.accountType, ban.ifscCode, es.shift as shift_service,d.shift as shift_dept
		from ".TABLE_EMP." e
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.Id
		Left Join ".TABLE_DEPT." d on e.empDept=d.Id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_EMP." ma on e.reportingTo=ma.empId
		Left Join ".TABLE_SERVICE." es on e.empId=es.empId
		Left Join ".TABLE_EMP." ap on ap.empId=e.reportingTo
		Left Join ".TABLE_PERSONAL_DETAILS." ep on e.empId=ep.empId
		Left Join ".TABLE_BLOODGROUP." b on ep.empBloodGroup=b.id	
		Left Join ".TABLE_DOCUMENT." doc on e.empId=doc.empId	
		Left Join ".TABLE_BANK." ban on e.empId=ban.empId	
		Left Join tbl_bank_mst mst_ban on mst_ban.id=ban.bankName	
		where 1=1 AND e.empId =".$eid." LIMIT 0,1";
		
		$result = $this->db->query($sql)->result_array();
		return $result = $this->db->query($sql)->result_array();
	}
	
	function getPrimaryDetails($eid)
	{
		$sql = "select e.empDept,e.empDesination,e.jobLocation, e.empTitle,e.empFname,e.empLname, e.empEmailPersonal,e.empEmailOffice,e.empMobile, DATE_FORMAT(e.empDOJ,'%d %M, %Y') empDOJ,e.clients,e.projects,s.State_Id as state, ma.empMobile,e.empRole
		from ".TABLE_EMP." e
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.Id
		Left Join ".TABLE_DEPT." d on e.empDept=d.Id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_EMP." ma on e.reportingTo=ma.empId
		Left Join ".TABLE_SERVICE." es on e.empId=es.empId
		Left Join ".TABLE_EMP." ap on ap.empId=e.reportingTo
		Left Join ".TABLE_PERSONAL_DETAILS." ep on e.empId=ep.empId
		Left Join ".TABLE_BLOODGROUP." b on ep.empBloodGroup=b.id	
		Left Join ".TABLE_DOCUMENT." doc on e.empId=doc.empId	
		Left Join ".TABLE_BANK." ban on e.empId=ban.empId	
		where 1=1 AND e.empId =".$eid." LIMIT 0,1";
		$result = $this->db->query($sql)->result_array();
	 //pre($result);
		return $result = $this->db->query($sql)->result_array();
	}
	function employeeList()
	{

		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0'])
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		
		$sql = "select e.empId,e.empEmailOffice,e.status,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,
		DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,e.candidateId,de.name as desination,r.name as region,s.State_Name as state,c.cityName as jobLocation, pr.name as projects, cl.name as clients from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		where 1=1 ".$addsql." AND empId!=10000 AND empId>10016 Order By e.empId";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	
	function teamList($id=0,$resultType='G')
	{
		if($this->input->post('status')!='')
		{
			$addSql .= " and e.status='".$this->input->post('status')."' ";
		}
		else
		{
			$addSql .= " and e.status='1' ";
		}

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".self::UserDecodeFilters($this->input->post('filters'));
		}
		if(($this->session->userdata('role')!=1) and $this->session->userdata('role')!=5){
			$addsql = " AND  reportingTo=".$this->session->userdata('admin_id')."";
		}
		$sql = "select e.empId,e.empEmailOffice,e.status,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,
		DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,de.name as desination from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		where 1=1 ".$addsql." AND empId!=10000";
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
	
	function employeeListing($colVal='')
	{
		$uid=$colVal;
		$sql = "select e.empId, e.joiningFor,e.empRole, e.regionPermission, e.empTitle, e.empFname, e.empLname, e.empImage, e.empEmailPersonal, e.empEmailOffice, e.empMobile, e.empDOJ, e.empDept, e.empDesination, e.reportingTo, e.jobLocation, e.clients, e.projects, e.status, e.intimatedLastWorkingday,e.lastWorkingday,e.resignationReceiveday, e.createdBy, e.candidateId, e.lastLogin, e.appointmentLetterSend, e.isCreated, e.appointmentLetterSenddate, e.appoinmentLettersendby, e.appoinmentLetterdownload, e.appoinmentLetterdownloadon, e.	appointmentLetterdownloadby, e.appointmentLetter, e.isActive, e.reasonOfresignation, e.reasonTypeOfresignation, e.noticePeriodserved, e.totalExperience, e.relevantExperience, d.panNo, em.empEmailOffice as managerEmail, r.id as region from ".TABLE_EMP." e 
		LEFT JOIN tbl_candidate can on e.candidateId=can.id 
		LEFT JOIN ".TABLE_DOCUMENT." d on e.empId=d.empId 
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN ".TABLE_EMP." em on e.reportingTo=em.empId 
		LEFT JOIN ".TABLE_EMP." ehr on e.reportingTo=ehr.empId 
		LEFT JOIN tbl_emp_exit_interview eei on e.empId=eei.empId 
		where e.empId='".$uid."'"; 
		return $this->db->query($sql)->result_array();
	}

	
	function check_email_duplicate($id='')
	{
		$this->db->select('user_id');
		$this->db->where('user_email', $this->input->post("user_email"));
		if($id<>""){
			$this->db->where('user_id !=', $id);
		}
		$this->db->from(TABLE_USERS);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$query->free_result();
			return 1;
		} else {
			$query->free_result();
			return 0;
		}
	}
	
	function checkUserPassword($pass)
	{
		$this->db->select('user_id');
		$this->db->where('user_id', $this->session->userdata('user_id'));
		$this->db->where('user_password', md5($pass));
		$this->db->from(TABLE_USERS);
		$query = $this->db->get();
		if($query->num_rows() > 0){return true;}
		else{return false;}
	}
	function getEmpDetails($empid)
	{
		$sql = "select em.empDept as managerDepartment,s.id,e.empId,e.empDept, concat(e.empFname,' ',e.empLname) as empName, e.reportingTo,em.empDesination as managerDesignation, e.empDOJ, s.uanNumber, s.pfNumber, s.esicNumber, s.empType, leaveGroup, s.empRetirementDate, s.previousTakehome, s.previousCTC, s.currentTakehome, s.currentCTC, se.shiftName as deptShift, DATE_FORMAT(se.shiftTimeFrom, '%h:%i %p') as shiftTimeFrom, DATE_FORMAT(se.shiftTimeTo, '%h:%i %p') as shiftTimeTo,s.shift, s.effectiveDate, t.name as ticcard  
		from ".TABLE_EMP." e
		Left Join ".TABLE_EMP." em on em.empId=e.reportingTo
		Left Join ".TABLE_SERVICE." s on e.empId = s.empId
		Left Join ".TABLE_DEPT." d on e.empDept= d.id
		Left Join ".TABLE_SHIFT." se on d.shift= se.id
		Left Join ".TABLE_TIC." t on e.empId= t.empId
		where e.empId='".$empid."' ";
		$result = $this->db->query($sql)->result_array();
		return $result= $result['0'];
	}
	
	function lastLogin($userid)
	{
		$sql ="select login from ".TABLE_USER_LOG." where user_id=".$userid." order by id DESC limit 1,1";
		return $result = $this->db->query($sql)->result_array();
	}
	
	function educationDetails($eid=0,$resultType='G')
	{
		$sql ="select * from ".TABLE_EDUCATION." where empId=".$eid." ";
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
	function empeducation($id)
	{
		$sql ="select * from ".TABLE_EDUCATION." where empId='".$id."' ORDER BY `tbl_emp_education`.`passingYear` DESC";
		return $result = $this->db->query($sql)->result_array();
	}
	function educationDetail($id)
	{
		$sql ="select * from ".TABLE_EDUCATION." where id=".$id;
		return $result = $this->db->query($sql)->result_array();
	}
	function getPersonalDetails($id)
	{
		$sql ="select p.empEmailOffice,p.empDOBactual,p.emergencyContactName, p.emergencyContactNumber, p.spouseName,p.empBloodGroup,p.empDOB,p.empDOM,p.empGender,p.empFathersName,p.empMotherName, p.empMaritalStatus,p.empNationality ,p.empReligion, DATE_FORMAT(p.empDOB,'%d %M, %Y') DOB, DATE_FORMAT(empDOM,'%d %M, %Y') DOM,b.name as BloodGroupName from ".TABLE_PERSONAL_DETAILS." p LEFT JOIN ".TABLE_BLOODGROUP." b on p.empBloodGroup=b.id	where p.empId=".$id;
		return $result = $this->db->query($sql)->result_array();
	}
	function getDocumentDetails($id)
	{
		$sql ="select * from ".TABLE_DOCUMENT." where empId=".$id;
		return $result = $this->db->query($sql)->result_array();
	}
	function getBankDetails($id)
	{
		$sql ="select * from ".TABLE_BANK." where empId=".$id;
		return $result = $this->db->query($sql)->result_array();
	}
	function getAddressDetails($eid=0)
	{

		$sql ="select a.same_address, a.empId, a.empPresentStreet, a.empPresentCity, a.empPresentState, a.empPresentZipcode, a.empPresentCountry, a.empMobile, a.empPermanentStreet, a.empPermanentCity, a.empPermanentState, a.empPermanentCountry, a.empPermanentZipcode, a.empPermanentMobile, a.empPermanentContact, c.countryName as pcountry,s.State_Name as pstate,ci.cityName as pcity, pc.countryName as percountry, ps.State_Name as perstate, pci.cityName as percity from ".TABLE_ADDRESS." a 
		LEFT JOIN ".TABLE_COUNTRY." c on a.empPresentCountry=c.countryId
		LEFT JOIN ".TABLE_STATE." s on a.empPresentState=s.State_Id
		LEFT JOIN ".TABLE_CITY." ci on a.empPresentCity=ci.cityId
		LEFT JOIN ".TABLE_COUNTRY." pc on a.empPermanentCountry=pc.countryId
		LEFT JOIN ".TABLE_STATE." ps on a.empPermanentState=ps.State_Id
		LEFT JOIN ".TABLE_CITY." pci on a.empPermanentCity=pci.cityId
		where a.empId=".$eid;

		return $result = $this->db->query($sql)->result_array();
	}
	
	function experienceDetail($id,$resultType='G')
	{
		if($id > 0)
		{
			$addsql .= " and empId=".$id;
		}
		$sql ="select id, empId, companyName, workFrom, workTo, designation, responsibilities, lastSalaryDrawn, reasonLeave, remarks, location from ".TABLE_EXP." where 1=1 ".$addsql."";
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
	
	function familyDetail($id,$resultType='G')
	{
		if($id > 0)
		{
			$addsql .= " and empId=".$id;
		}
		$sql ="select id, empId, empRelation, personName, isLive, education, occupation, contactNo, dependent, nominee, nomineeOccupation, DATE_FORMAT(nomineeDOB,'%d-%b-%Y') nomineeDOB from ".TABLE_FAMILY."  where 1=1 ".$addsql."";
		
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
	
	function growthDetails($id,$resultType='')
	{
		if($id > 0)
		{
	      
			$addsql .= " and empId=".$id;
		}
		$sql ="select id, empId, DATE_FORMAT(fromDate,'%d-%b-%Y')fromDate, DATE_FORMAT(toDate,'%d-%b-%Y')toDate, designation, grade, ctc from ".TABLE_GROWTH." where 1=1 ".$addsql."";
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

	
	function bloodGroup()
	{
		$sql ="select id, name from ".TABLE_BLOODGROUP." ";
		return $result = $this->db->query($sql)->result_array();
	}
	
	function getLanguagedetails($id,$resultType='')
	{
		if($id > 0)
		{
			$addsql .= " and l.empId=".$id;
		}
		$sql ="select l.id, l.empId, l.language, l.read, l.write, l.speak, l.languageCourse, l.languagelCourseDetails ,lm.name from ".TABLE_LANGUAGE." l Left Join ".TABLE_MASTERST_LANGUAGE." lm on l.Language=lm.Id where 1=1 ".$addsql."";
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

	
	function languageList(){
		$sql ="select id, name, addedBy, updatedBy from ".TABLE_MASTERST_LANGUAGE." ";
		return $result = $this->db->query($sql)->result_array();
	}
	
	function assetDetails($id='0', $resultType)
	{
		if($id > 0)
		{
			$addsql .= " and a.empId=".$id;
		}
		$id = $this->session->userdata('employeeId');
		$sql ="select a.id, a.empId, a.assetName, a.assetSrno, a.assetDescription, a.dateOfIssue, a.dateOfReturn ,lm.name from ".TABLE_EMP_ASSET." a
		Left Join ".TABLE_MASTER_ASSET." lm on a.assetName=lm.Id
		where 1=1 ".$addsql."";
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
	function assetDetail($id)
	{
		if($id > 0)
		{
			$addsql .= " and id=".$id;
		}
		$sql ="select id, name, addedBy, updatedBy from ".TABLE_EMP_ASSET." where 1=1 ".$addsql."";
		return $result = $this->db->query($sql)->result_array();
	}
	
	
	function getServiceDetails($id)
	{
		$sql ="select s.*,e.name,  concat(em.empFname,' ',em.empLname) as reportingManager from ".TABLE_SERVICE." s
		LEFT JOIN ".TABLE_EMPTYPE." e on s.empType=e.Id
		LEFT JOIN ".TABLE_EMP." em on em.reportingTo=em.empId
		where s.empId=".$id;
		return $result = $this->db->query($sql)->result_array();
	}	
	function empTypeList()
	{
		$sql ="select * from ".TABLE_EMPTYPE." Order By name";
		return $result = $this->db->query($sql)->result_array();
	}
	function empdesignationList($clientId=false)
	{
		$wh = " ";
		if($clientId)
		{
			$wh = " and clientId = '".$clientId."' ";
		}
		$sql ="select * from ".TABLE_MASTER_DESIGNATION." where designation_status='1' $wh Order By name";
		return $result = $this->db->query($sql)->result_array();
	}
	function empgradeList($clientId=false)
	{
		$wh = " ";
		if($clientId)
		{
			$wh = " and clientId = '".$clientId."' ";
		}
		$sql ="select * from ".TABLE_GRADE." where status='1' $wh Order By grade";
		return $result = $this->db->query($sql)->result_array();
	}

    function brand_list($clientId=false)
	{
		$wh = " ";
		if($clientId)
		{
			$wh = " and clientId = '".$clientId."' ";
		}
		$sql ="select * from ".TABLE_BRAND." where status='1' $wh Order By name";
		return $result = $this->db->query($sql)->result_array();
	}

	function division_list($clientId=false)
	{
		$wh = " ";
		if($clientId)
		{
			$wh = " and clientId = '".$clientId."' ";
		}
		$sql ="select * from ".TABLE_DIVISION." where status='1' $wh Order By name";
		return $result = $this->db->query($sql)->result_array();
	}

	function empquarterList($clientId=false)
	{
		$wh = " ";
		if($clientId)
		{
			$wh = " and clientId = '".$clientId."' ";
		}
		$sql ="select * from ".TABLE_KRA." where status='1' group by quarter Order By quarter";
		return $result = $this->db->query($sql)->result_array();
	}
	
	
	//department 6-August-2018
	function empdepartmentList($clientId=false)
	{
		$wh = " ";
		if($clientId)
		{
			$wh = " and clientId = '".$clientId."' ";
		}
		$sql ="select * from tbl_mst_dept where department_status='1' $wh Order By name";
		return $result = $this->db->query($sql)->result_array();
	}
	
	function quarter_list($clientId=false)
	{
		$wh = " ";
		if($clientId)
		{
			$wh = " and clientId = '".$clientId."' ";
		}
		$sql ="select * from tbl_kra_mst where status='1' $wh Order By financial_year";
		return $result = $this->db->query($sql)->result_array();
	}
	
	function financce_list($clientId=false)
	{
		$wh = " ";
		if($clientId)
		{
			$wh = " and clientId = '".$clientId."' ";
		}
		$sql ="select financial_year from tbl_kra_mst where status='1' $wh group by financial_year  Order By financial_year ";
		return $result = $this->db->query($sql)->result_array();
	}
	//department 6-August-2018
	function reportingTo()
	{
		$sql ="select e.* from ".TABLE_EMP." e
		LEFT JOIN ".TABLE_SERVICE." s on e.empId=s.empId
		WHERE s.empDesination=1";
		return $result = $this->db->query($sql)->result_array();
	}
	
	function ajaxManager($did)
	{
		$sql ="select * from ".TABLE_EMP." where empDesination=".$did." AND empId!=".$this->session->userdata('employeeId')." ";
		return $result = $this->db->query($sql)->result_array();
	}
	function ajaxManagerdeptwise($did)
	{
		$sql ="select * from ".TABLE_EMP." where empDept=".$did." AND empId!=".$this->session->userdata('employeeId')." ";
		return $result = $this->db->query($sql)->result_array();
	}
	function getManager($did)
	{
		//$sql ="select empId,concat(empFname,' ',empLname) as Manager,empEmailOffice from ".TABLE_EMP." WHERE empDesination NOT IN(3,11,12) order by empFname";
		$sql ="select empId,concat(empFname,' ',empLname) as Manager,empEmailOffice from ".TABLE_EMP."  order by empFname";
		return $result = $this->db->query($sql)->result_array();
	}
	function getApprovalmanager()
	{
		$sql ="select empId,concat(empFname,' ',empLname) as Manager,empEmailOffice from ".TABLE_EMP." where empRole!=1 and  empDesination NOT IN(2,3,11,12)";
		return $result = $this->db->query($sql)->result_array();
	}
	
	function getempName($eid)
	{
		$sql ="select concat(empFname,' ',empLname) as name from ".TABLE_EMP." where empId=".$eid."";
		return $result = $this->db->query($sql)->result_array();
	}
	
	function managerDetails($id)
	{
		$sql ="select * from ".TABLE_EMP." where empId=".$id;
		$result = $this->db->query($sql)->result_array();
		return $result = $result['0'];
	}
	function moduleList()
	{
		$sql ="select * from ".TABLE_MASTER_MODULE." where 1=1 and status=1";
		return $result = $this->db->query($sql)->result_array();
	}
	function userMenuList(){
		$sql ="select * from ".TABLE_LEFTV2." where 1=1 and status=1 Order By name";
		
		return $result = $this->db->query($sql)->result_array();
	}	
	function roleList()
	{
		$sql ="select * from ".TABLE_ROLE." where 1=1 AND roleId !=1 and status='1' Order By roleName";
		return $result = $this->db->query($sql)->result_array();
	}
	function empType($id)
	{
		$sql ="select name from ".TABLE_EMPTYPE." where id=".$id;
		$result = $this->db->query($sql)->result_array();
		return $result['0']['name'];
	}
	function getReporting($id)
	{
		$sql = "select e.empEmailOffice, concat(e.empFname,' ',e.empLname) as empName, s.empReportingTo from ".TABLE_EMP." e
		LEFT JOIN ".TABLE_SERVICE." s  on e.empId=s.empReportingTo  where s.empReportingTo='".$id."'";
		return $result = $this->db->query($sql)->result_array();
	}
	function getEmployeeList(){
		$sql ="select empId from ".TABLE_EMP." where is_Displayed= 1 AND empRole !='1'";
		return $result = $this->db->query($sql)->result_array();
	}
	function getApprovedBy($id)
	{
		$sql = "select s.empId as reportingTo ,concat(s.empFname,' ',s.empLname) as manager,tep.empEmailOffice from ".TABLE_EMP." e
		LEFT JOIN ".TABLE_EMP." s  on e.reportingTo=s.empId
		LEFT JOIN ".TABLE_PERSONAL_DETAILS." tep on s.empId=tep.empId
		where e.empId='".$id."'";
		return $result = $this->db->query($sql)->result_array();
	}
	function getEmpdoj($id){
		$sql ="select e.empDOJ,e.empgender,ep.empGender,eg.grade,ep.empMaritalStatus, s.empType,CONCAT(empFname,' ',empLname) as name,TIMESTAMPDIFF(MONTH, e.empDOJ, now()) noofmonth,TIMESTAMPDIFF(YEAR, e.empDOJ, now()) noofyear  from ".TABLE_EMP." e 
		    LEFT JOIN ".TABLE_PERSONAL_DETAILS." ep on ep.empId=e.empId 
		    LEFT JOIN ".TABLE_SERVICE." s on e.empId=s.empId 
		    LEFT JOIN ".employees_grade." eg on eg.empId= e.empId 
		    WHERE e.empId=".$id." LIMIT 0,1";
           $result = $this->db->query($sql)->result_array();

			 #######For Getting special leave data ################# 
		   if($result[0]['empGender'] == "Female" && $result[0]['empMaritalStatus'] =='Married' ){
		   	 $this->db->select('*');
		   	 $this->db->where('leave_type','ML');
		   	 $this->db->or_where('leave_type','LSL');
		     $query = $this->db->get('special_leave');
		     $special_leave = $query->result_array();
		     $result['specialLeave'] = $special_leave; 

		   }else if($result[0]['empGender'] == "Male" && $result[0]['empMaritalStatus'] =='Married'){
	         $this->db->select('*');
		   	 $this->db->where('leave_type','PL');
		     $query = $this->db->get('special_leave');
		     $special_leave = $query->result_array();
		     $result['specialLeave'] = $special_leave; 
		   }

		   return $result;
	}	

	function getsalarySlip()
	{
		$sql ="select *,DATE_FORMAT('0000-12-01' + INTERVAL month MONTH,'%b') as monthname from ".TABLE_SALARYSLIP." s where s.empId=".$this->session->userdata('admin_id')." order by year, month";
		return $result = $this->db->query($sql)->result_array();
	}
	
	function viewSalarySlip($id)
	{
		$sql ="select name from ".TABLE_SALARYSLIP." where id=".$id." LIMIT 0,1";
		$result = $this->db->query($sql)->result_array();
		return $result =$result['0'];
	}
	function salaryStructure($id=0)
	{
		if($id){
			$eid=$id;
		}else{
			$eid = $this->session->userdata('admin_id');
		}
		$sql ="select s.*, DATE_FORMAT(s.effectiveFrom,'%d-%b-%Y')effectiveFrom,  e.joiningFor as emptype from ".TABLE_SALARYBREAKUP." s left join ".TABLE_EMP." e on s.empId=e.empId where s.empId=".$eid." ";
		$result = $this->db->query($sql)->result_array();
		return $result =$result['0'];
	}
	function exportcsv($empType = false)
	{
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		else{
			$addsql .=" AND r.id in(1,2,3,4)";
		}

			if($empType==1)
			{
				$addsql .=" AND can.empType =  ".$empType."";
			}
			else{
				$addsql .=" AND can.empType != 1 ";

			}
		
		
		$sql = "select DATE_FORMAT(p.empDOBactual,'%d-%b-%Y') as  empDOBactual, e.appointmentLetterSend,e.candidateId,DATE_FORMAT(e.resignmarkOn,'%d-%b-%Y') as resignmarkOn,  DATE_FORMAT(e.lastWorkingday,'%d-%b-%Y') as lastworkingday, e.empEmailPersonal,e.empEmailOffice,e.status,d.name as department,e.empMobile, concat(e.empFname,' ',e.empMname,' ',e.empLname) as empName, DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,concat(h.empFname,' ',h.empLname) as createdBy, cl.name as clients,pr.name as projects, concat(re.empFname,' ',re.empLname) as reportingManager,de.name as desination,a.empPresentStreet,concat( upper(substring(ad.cityName,1,1)),lower(substring(ad.cityName,2)) ) as empPresentCity,concat( upper(substring( pres.State_Name,1,1)),lower(substring( pres.State_Name,2)) ) as empPresentState, a.empPresentZipcode, a.empPermanentStreet,concat( upper(substring(  pc.cityName,1,1)),lower(substring(  pc.cityName,2)) ) as empPermanentCity,concat( upper(substring(pers.State_Name,1,1)),lower(substring(pers.State_Name,2)) ) as empPermanentState ,a.empPermanentZipcode, a.empPermanentMobile, a.empPermanentContact, DATE_FORMAT(p.empDOB,'%d-%b-%Y') DOB, p.empGender, p.empFathersName, p.empMotherName, p.empMaritalStatus, p.spouseName, p.empNationality, p.empBloodGroup, p.empDOM, p.empReligion,p.emergencyContactName,p.emergencyContactNumber,DATE_FORMAT(e.isCreated,'%d-%b-%Y %r') as creationdate, sr.approvedBy, sr.leaveGroup, sr.previousTakehome, sr.previousCTC, sr.currentTakehome, sr.currentCTC, sr.pfNumber,sr.uanNumber, sr.esicNumber, bl.name as bloodGroup, b.bankName, b.branch, b.accountNo, b.nameInAccount, b.ifscCode, sl.*, do.panNo, do.pan, do.photoIdproof, do.addressProof, do.certificates10th, do.marksheet10th, do.heighestqualificationsMarksheet, do.heighestqualificationsCertificates, do.experienceCertificates, do.payslip, do.relievingLetter, do.cancelCheck, do.cVSubmited, do.esifamilyPhotograph, do.appointmentLetter, do. passportNo, do. passport, do.adharNo, can.offerLetterIssue,concat( upper(substring(c.cityName,1,1)),lower(substring(c.cityName,2)) ) as jobCityName,concat( upper(substring(s.State_Name,1,1)),lower(substring(s.State_Name,2)) ) as jobStateName,concat( upper(substring(r.name,1,1)),lower(substring(r.name,2)) ) as jobRegionName , e.empId
		from ".TABLE_EMP." e
		Left Join ".TABLE_EMP." h on e.createdBy=h.empId
		Left Join ".TABLE_ADDRESS." a on e.empId=a.empId
		Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
		Left Join ".TABLE_PERSONAL_DETAILS." p on e.empId=p.empId		
		Left Join ".TABLE_SERVICE." sr on e.empId=sr.empId						
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		Left Join ".TABLE_CITY." pc on a.empPermanentCity=pc.cityId
		Left Join ".TABLE_CITY." ad on a.empPresentCity=ad.cityId
		Left Join ".TABLE_STATE." pres on a.empPresentState=pres.State_Id
		Left Join ".TABLE_STATE." pers on a.empPermanentState=pers.State_Id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLE_BANK." b on e.empId=b.empId
		Left Join ".TABLE_SALARYBREAKUP." sl on e.empId=sl.empId
		Left Join ".TABLE_DOCUMENT." do on e.empId=do.empId
		Left Join ".TABLE_BLOODGROUP." bl on p.empBloodGroup=bl.id
		Left Join ".TABLE_EMP." re on e.reportingTo=re.empId
		where 1=1  AND e.empId!='10000' AND e.empId NOT LIKE '%100%' ".$addsql." group By e.empId DESC";
		
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	
	
	function csvforrag($status)
	{
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		else{
			$addsql .=" AND r.id in(1,2,3,4)";
		}
		if($status)
			$addsql .=" AND e.status='".$status."'";
		
		$sql = "select  e.empId, e.candidateId, e.appointmentLetterSend,DATE_FORMAT(e.lastWorkingday,'%d %M, %Y') as lastworkingday, e.empEmailPersonal,e.empEmailOffice,e.status,d.name as department,e.empMobile, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ , cl.name as clients,pr.name as projects, concat(re.empTitle,' ',re.empFname,' ',re.empLname) as reportingManager,c.cityName as jobLocation,de.name as desination,a.empPermanentMobile,a.empPermanentContact,DATE_FORMAT(p.empDOB,'%d %M, %Y') DOB, p.empGender, p.empFathersName, p.empMotherName, r.name as region, s.State_Name as state  from ".TABLE_EMP." e
		Left Join ".TABLE_EMP." h on e.createdBy=h.empId
		Left Join ".TABLE_ADDRESS." a on e.empId=a.empId
		Left Join ".TABLE_PERSONAL_DETAILS." p on e.empId=p.empId		
		Left Join ".TABLE_SERVICE." sr on e.empId=sr.empId						
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_CITY." pc on a.empPermanentCity=pc.cityId
		Left Join ".TABLE_CITY." ad on a.empPresentCity=ad.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLE_BANK." b on e.empId=b.empId
		Left Join ".TABLE_SALARYBREAKUP." sl on e.empId=sl.empId
		Left Join ".TABLE_DOCUMENT." do on e.empId=do.empId
		Left Join ".TABLE_BLOODGROUP." bl on p.empBloodGroup=bl.id
		Left Join ".TABLE_EMP." re on e.reportingTo=re.empId
		Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
		where 1=1  AND e.empId!='10000' and e.empDesination IN(12,26,27,31,33) and e.status='1' ".$addsql." Order By e.empId DESC";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	
	function exportcsv_payroll()
	{
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		else{
			$addsql .=" AND r.id in(1,2,3,4)";
		}
		
		$sql = "select e.appointmentLetterSend, e.empId,e.candidateId, DATE_FORMAT(e.lastWorkingday,'%d-%b-%Y') as lastworkingday, e.empEmailPersonal,e.empEmailOffice,e.status,d.name as department,e.empMobile, concat(e.empFname,' ',e.empLname) as empName, DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,concat(h.empTitle,' ',h.empFname,' ',h.empLname) as createdBy, cl.name as clients,pr.name as projects, concat(re.empTitle,' ',re.empFname,' ',re.empLname) as reportingManager,c.cityName as jobLocation,de.name as desination,a.empPresentStreet,ad.cityName as empPresentCity, a.empPresentZipcode,a.empPermanentStreet,pc.cityName as empPermanentCity,a.empPermanentZipcode,a.empPermanentMobile,a.empPermanentContact,DATE_FORMAT(p.empDOB,'%d-%b-%Y') DOB, p.empGender, p.empFathersName, p.empMotherName, p.empMaritalStatus, p.spouseName, p.empNationality, p.empBloodGroup, p.empDOM, p.empReligion, DATE_FORMAT(e.isCreated,'%d-%b-%Y') as creationdate, sr.approvedBy, sr.leaveGroup, sr.previousTakehome, sr.previousCTC, sr.currentTakehome, sr.currentCTC, sr.pfNumber,sr.uanNumber, sr.esicNumber, bl.name as bloodGroup, r.name as region, s.State_Name as state, b.bankName, b.branch, b.accountNo, b.nameInAccount, b.ifscCode, sl.*, do.panNo, do.pan, do.photoIdproof, do.addressProof, do.certificates10th, do.marksheet10th, do.heighestqualificationsMarksheet, do.heighestqualificationsCertificates, do.experienceCertificates, do.payslip, do.relievingLetter, do.cancelCheck, do.cVSubmited, do.esifamilyPhotograph, do.appointmentLetter, do. passportNo, do. passport,can.offerLetterIssue
		from ".TABLE_EMP." e
		Left Join ".TABLE_EMP." h on e.createdBy=h.empId
		Left Join ".TABLE_ADDRESS." a on e.empId=a.empId
		Left Join ".TABLE_PERSONAL_DETAILS." p on e.empId=p.empId		
		Left Join ".TABLE_SERVICE." sr on e.empId=sr.empId						
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_CITY." pc on a.empPermanentCity=pc.cityId
		Left Join ".TABLE_CITY." ad on a.empPresentCity=ad.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLE_BANK." b on e.empId=b.empId
		Left Join ".TABLE_SALARYBREAKUP." sl on e.empId=sl.empId
		Left Join ".TABLE_DOCUMENT." do on e.empId=do.empId
		Left Join ".TABLE_BLOODGROUP." bl on p.empBloodGroup=bl.id
		Left Join ".TABLE_EMP." re on e.reportingTo=re.empId
		Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
		where 1=1  AND e.empId!='10000' ".$addsql." Order By e.empId DESC";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	
	
	function get_employeeList($id){
		$sql ="select e.candidateId, e.empId, DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ, LCASE(e.empEmailPersonal) as email,  concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, d.name as designation, dept.name as department, c.cityName as jobLocation,  r.name as region, LCASE(er.empEmailOffice) as tl  from ".TABLE_EMP." e
		Left Join ".TABLE_MASTER_DESIGNATION." d on e.empDesination=d.id
		Left Join ".TABLE_DEPT." dept on e.empDept=dept.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLE_EMP." er on e.reportingTo=er.empId
		where e.status = 1  and e.empId=".$id." " ;
		$result = $this->db->query($sql)->result_array();
		return $result = $result['0'];
	}
	function salaryslip($id)
	{
		$sql ="select * from ".TABLE_SALARYSLIP." WHERE id=".$id."";
		$result = $this->db->query($sql)->result_array();
		return $result = $result['0'];
	}
	
	function decodeFilterssalary($filters)
	{
		$sql = ' (';
		$objJson = json_decode($filters);
              //  pre($objJson);
		foreach($objJson->{'rules'} as $rules)
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
			$sql .= $rules->{'field'}.' '; // field name
			$sql .= $this->decodeGridOP($rules->{'op'},$rules->{'data'}).' '; // op, val
			$sql .= $objJson->{'groupOp'}.' '; // and, or
		}
	}

	$sql = rtrim($sql, $objJson->{'groupOp'}.' ');
	return $sql.') ';
}
function totalcountrecords($id,$fields,$status='0')
{
	if($status=='1')
	{
		$addsql .="and e.status=".$status."";
	}
	$sql = "select r.name,count(e.".$fields.")as total".$fields." from ".TABLE_EMP." e
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	Where e.empDesination=".$id." AND e.".$fields."!='' ".$addsql." group by r.id order by r.name";
	return $this->db->query($sql)->result_array();
}
function totalperosnaldaata($id,$fields,$status='0')
{
	
	if($fields=='empDOB'){
		$addsql .="AND p.".$fields."!='0000-00-00'";
	}else{
		$addsql .="AND p.".$fields."!=''";
	}
	if($status=='1')
	{
		$addsql .="and e.status=".$status."";
	}
	$sql = "select r.name,count(p.".$fields.")as total".$fields." from ".TABLE_EMP." e
	Left Join ".TABLE_PERSONAL_DETAILS." p on e.empId=p.empId
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	Where  e.empDesination IN(12,26,27,31,33) ".$addsql."  group by r.id order by r.name";
	return $this->db->query($sql)->result_array();
}



function totalsalary($id,$fields,$status='0')
{
	if($status=='1')
	{
		$addsql .="and e.status=".$status."";
	}
	$sql = "select r.name,count(p.".$fields.")as total".$fields." from ".TABLE_EMP." e
	Left Join ".TABLE_SALARYBREAKUP." p on e.empId=p.empId
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	Where e.empDesination=".$id." AND p.".$fields."!='' ".$addsql." group by r.id order by r.name";
	return $this->db->query($sql)->result_array();
}

function totalbank($id,$fields,$status='0')
{
	if($status=='1')
	{
		$addsql .="and e.status=".$status."";
	}
	$sql = "select r.name,count(p.".$fields.")as total".$fields." from ".TABLE_EMP." e
	Left Join ".TABLE_BANK." p on e.empId=p.empId
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	Where e.empDesination=".$id." AND p.".$fields."!='' ".$addsql." group by r.id order by r.name";
	return $this->db->query($sql)->result_array();
}

function totalPF($id,$fields,$status='0')
{
	if($status=='1')
	{
		$addsql .="and e.status=".$status."";
	}
	$sql = "select r.name,count(p.".$fields.")as total".$fields." from ".TABLE_EMP." e
	Left Join ".TABLE_SERVICE." p on e.empId=p.empId
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	Where e.empDesination=".$id." AND p.".$fields."!='' ".$addsql." group by r.id order by r.name";
	return $this->db->query($sql)->result_array();
}

function totalDocument($id,$fields,$status='0')
{
	if($status=='1')
	{
		$addsql .="and e.status=".$status."";
	}
	$sql = "select r.name,count(p.".$fields.")as total".$fields." from ".TABLE_EMP." e
	Left Join ".TABLE_DOCUMENT." p on e.empId=p.empId
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	Where e.empDesination=".$id." AND p.".$fields."!='' ".$addsql." group by r.id order by r.name";
	return $this->db->query($sql)->result_array();
}


function getempid($id)
{
	$sql ="select empId from ".TABLE_EMP." WHERE candidateId=".$id."";
	$result = $this->db->query($sql)->result_array();
	return $result = $result['0'];
}

function checkempid($id)
{
	$sql ="select empId from ".TABLE_DOCUMENT." WHERE empId=".$id."";
	$result = $this->db->query($sql)->result_array();
	return $result = $result['0'];
}
function get_emp_shift_details($id)
{
	if($this->session->userdata('admin_id')==10000)
		$id='1';

	$sql ="select DATE_FORMAT(shiftTimeFrom,'%h:%i %p') as shiftTimeFrom, DATE_FORMAT(shiftTimeFrom,'%h:%i:%s') as shiftTimeFromp,  DATE_FORMAT(shiftTimeTo,'%h:%i %p') as  shiftTimeTo, DATE_FORMAT(graceTimeTill,'%H:%i') as graceTimeTill, DATE_FORMAT(halfDayStart, '%H:%i') as halfDayStart, minimumWorkingHours from ".TABLE_SHIFT." 	WHERE id=".$id."";
	return $result = $this->db->query($sql)->result_array();
}

function get_emp_shift_details_cron($id)
{
	$sql ="select DATE_FORMAT(shiftTimeFrom,'%h:%i %p') as shiftTimeFrom, DATE_FORMAT(shiftTimeFrom,'%h:%i:%s') as shiftTimeFromp,  DATE_FORMAT(shiftTimeTo,'%h:%i %p') as  shiftTimeTo,DATE_FORMAT(shiftTimeTo,'%h:%i:%s') as shiftTimeTop,  graceTimeTill, DATE_FORMAT(halfDayStart, '%H:%i') as halfDayStart, minimumWorkingHours from ".TABLE_SHIFT." 	WHERE id=".$id."";
	return $result = $this->db->query($sql)->result_array();
}
function get_emp_shift_details_backup($id)
{
	$sql ="select s.empId, sh.shiftTimeFrom, sh.shiftTimeTo, sh.graceTimeTill, s.shift, DATE_FORMAT(sh.halfDayStart, '%H:%i') as halfDayStart, sh.minimumWorkingHours from ".TABLE_SERVICE." s
	LEFT JOIN ".TABLE_SHIFT." sh on s.shift=sh.id
	WHERE s.empId=".$id."";
	return $result = $this->db->query($sql)->result_array();
}

function getReportingDetails($eid)
{
	$sql ="select e.empId as requestFrom, concat(e.empFname,' ',e.empLname) as empName,e.empEmailOffice as oemail,e.empEmailPersonal as pemail, m.empId,concat(m.empTitle,' ',m.empFname,' ',m.empLname) as reportingTo,m.empEmailOffice from ".TABLE_EMP." e
	Left Join ".TABLE_EMP." m on e.reportingTo= m.empId
	WHERE e.empId=".$eid." ";	
	return $result = $this->db->query($sql)->result_array();
}

function getEmpDetailsData($eid)
{
	$sql ="select concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,e.empEmailOffice as oemail,e.empEmailPersonal as pemail from ".TABLE_EMP."  e
	WHERE e.empId=".$eid." ";
	return $result = $this->db->query($sql)->result_array();
} 


function getempcandidateID($id)
{
	$sql ="select candidateId from ".TABLE_EMP." WHERE empId=".$id."";
	$result = $this->db->query($sql)->result_array();
	return $result = $result['0'];
}

function getResigndata()
{
	$sql ="select candidateId,status from ".TABLE_EMP." WHERE status=0 ";
	return $result = $this->db->query($sql)->result_array();
}

function updateDocument()
{
	
	$sql ="select * from ".TABLE_DOCUMENT." Order By id desc ";
	return $result = $this->db->query($sql)->result_array();
}	

function duplicatePan($panNo,$empId)
{
	$sql="select d.id from ".TABLE_DOCUMENT." d
	LEFT JOIN ".TABLE_EMP." e on d.empId=e.empId
	where panNo='".$panNo."' and d.empId!='".$empId."' and e.status='1'";
	$result=$this->db->query($sql)->result_array();
	return $result['0'];
}

function duplicateEmail($email,$eid)
{
	$sql="select empId from ".TABLE_EMP." where empEmailPersonal='".$email."' and empId!='".$eid."' and status='1'";
	$result=$this->db->query($sql)->result_array();
	return $result['0'];
}
function duplicateMobile($mobile,$eid)
{
	$sql="select empId from ".TABLE_EMP." where empMobile='".$mobile."' and empId!='".$eid."' and status='1'";
	$result=$this->db->query($sql)->result_array();
	return $result['0'];
}
function get_bank_details($resultType='G',$orderby='0' )
{
	if($orderby){
		$addsql  =" order by e.empId DESC";
	}
	if($this->input->post('filters')!='') // search filters
	{
		$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
	}
	$sql = "select e.empId, UPPER(tmb.bankName) as bankName, UPPER(b.branch) as branch, b.accountNo, b.accountType, b.ifscCode, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,  DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,de.name as desination,r.name as region,s.State_Name as state,c.cityName as jobLocation  from ".TABLE_EMP." e
	Left Join ".TABLE_DEPT." d on e.empDept=d.id
	Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	Left Join ".TABLE_BANK." b on e.empId= b.empId
	Left Join tbl_bank_mst tmb on tmb.id=b.bankName
	where 1=1 AND b.accountNo!='' and e.empId NOT LIKE '%100%' AND e.empId!=10000 ".$addsql."";
	
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
function getemailmobile($empId)
{
	$sql="select concat(empFname,' ',empLname) as empName, empMobile, empEmailPersonal, empEmailOffice from ".TABLE_EMP." where empId='".$empId."' and status='1'";
	$result=$this->db->query($sql)->result_array();

	return $result['0'];
}

function checkAttendance($empId, $date)
{
	$sql ="select empId from ".TABLE_ATTENDANCE." WHERE empId='".$empId."' and attendanceDate='".$date."'";
	//$sql ="select empId from ".TABLE_ATTENDANCE." WHERE empId='".$empId."' and attendanceDate='".$date."'";
	//echo $sql;die;
	$result=$this->db->query($sql)->result_array(); 
	return $result['0'];
}

function get_login_history_details(){
	$sql ="select l.id, l.empId, DATE_FORMAT(l.login, '%h:%i %p') as login, if(l.logout ='00:00:00','00:00',DATE_FORMAT(l.logout, '%h:%i %p')) as logout,  DATE_FORMAT(l.activityDate,'%d %b, %Y') as  loginDate, l.ip, if(LENGTH(e.empFname) > 0,(concat(e.empTitle,' ',e.empFname,' ',e.empLname)),(concat(g.empTitle,' ',g.empFname,' ',g.empLname))) as empName  from ".TABLE_EMP_LOG." l Left Join ".TABLE_EMP." e on l.empId=e.empId
	Left Join ".TABLE_GUEST_MASTER." g on l.empId=g.empId
	WHERE 1=1 and l.empId>=10000 or l.empId BETWEEN 100 AND 107";
	
	return $result = parent::result_grid_array($sql);

}

function get_candidate_login_history_details(){
	$sql ="select l.id, l.empId, DATE_FORMAT(l.login, '%h:%i %p') as login, if(l.logout ='00:00:00','00:00',DATE_FORMAT(l.logout, '%h:%i %p')) as logout,  DATE_FORMAT(l.activityDate,'%d %b, %Y') as  loginDate, l.ip, concat(g.empTitle,' ',g.empFname,' ',g.empLname) as empName  from ".TABLE_EMP_LOG." l
	Left Join ".TABLE_CANDIDATE." g on l.empId=g.id
	WHERE 1=1 and l.empId NOT IN(101,102,103,104,105,106,107) and l.empId < 10000";
	
	return $result = parent::result_grid_array($sql);

}


function totalOnline()
{
	$sql="select count(empId) as online from ".TABLE_EMP." where isOnline=1 and status=1";
	$result=$this->db->query($sql)->result_array();
	return $result['0'];
}
//22-Aug-18
function get_balance_leave($resultType='G')
{	
	if($this->input->post('filters')!='') // search filters
	{
	    if(self::decodeFilters($this->input->post('filters')))
		{
		$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
	}	
	
	$stateIds   =  $this->stateRolePermissionData($this->session->userdata('admin_id'));	    
	
	if($stateIds['stateIds']){
		$commaSepState   =   $stateIds['stateIds'];   
		$stateCond       =  " and e.jobLocation in (".$commaSepState.")";      
	} else {
		$stateCond = '';
	}
	
   $leaveYear =  $this->checkLeaveYear1($this->input->post('filters'));
	
	if($leaveYear == date("Y")){
	    $sql ="SELECT $leaveYear as leaveyear ,DATE_FORMAT(e.empDOJ,'%d-%b-%Y') as empDOJ,de.name as designation,c.cityName,d.name as dept,b.empId, concat(e.empFname,' ',e.empLname) as empName,
		SUM_OF_LIST(SL_Opening) as opening_sl,SUM_OF_LIST(CL_Opening) as opening_cl,SUM_OF_LIST(EL_Opening) as opening_el,
		GET_TOTAL_LEAVE_DONATED(b.empId,'EL') as donate_el,
		IFNULL(SUM_OF_LIST(SL_Applied),0)as applied_sl,IFNULL(SUM_OF_LIST(CL_Applied),0) as applied_cl,IFNULL(SUM_OF_LIST(EL_Applied),0) as applied_el,
		IFNULL((SUM_OF_LIST(SL_Opening)-(IFNULL(SUM_OF_LIST(SL_Applied),0))),0) as balance_sl,IFNULL((SUM_OF_LIST(CL_Opening)- (IFNULL(SUM_OF_LIST(CL_Applied),0))),0) as balance_cl,
		IFNULL((SUM_OF_LIST(EL_Opening)- (IFNULL(SUM_OF_LIST(EL_Applied),0) + IFNULL(GET_TOTAL_LEAVE_DONATED(b.empId,'EL'),0) )),0)as balance_el FROM `opening_leave` as b left join leave_applied la on b.empId=la.requestFrom
		Left Join ".TABLE_EMP." e on b.empId=e.empId 
		Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		WHERE 1=1 $stateCond ".$addsql."  group by b.empId";
	} else {    
  
		$sql ="SELECT  $leaveYear as leaveyear,DATE_FORMAT(e.empDOJ,'%d-%b-%Y') as empDOJ,de.name as designation,c.cityName,d.name as dept,b.empId, concat(e.empFname,' ',e.empLname) as empName,GET_TOTAL_LEAVE_BALANCE(b.empId,'CL') as opening_cl,GET_TOTAL_LEAVE_BALANCE(b.empId,'SL') as opening_sl,GET_TOTAL_LEAVE_BALANCE(b.empId,'EL') as opening_el,IFNULL(SUM(CASE WHEN tr.leaveType='SL' THEN noofdays end  ),0) as applied_sl,IFNULL(SUM(CASE WHEN tr.leaveType='CL' THEN noofdays end  ),0) as applied_cl,IFNULL(SUM(CASE WHEN tr.leaveType='EL' THEN noofdays end  ),0) as applied_el,
		IFNULL((GET_TOTAL_LEAVE_BALANCE(b.empId,'SL')-SUM(CASE WHEN tr.leaveType='SL' THEN noofdays Else 0 end)),0) as balance_sl,IFNULL((GET_TOTAL_LEAVE_BALANCE(b.empId,'CL')-SUM(CASE WHEN tr.leaveType='CL' THEN noofdays Else 0 end)),0) as balance_cl,
		IFNULL((GET_TOTAL_LEAVE_BALANCE(b.empId,'EL')-(SUM(CASE WHEN tr.leaveType='EL' THEN noofdays Else 0 end) + MAX(CASE WHEN b.leaveType='EL' THEN  b.el_donated Else 0 end))),0) as balance_el,MAX(CASE WHEN b.leaveType='EL' THEN  b.el_donated Else 0 end) as donate_el
		 from tbl_emp_leave_balance_2019 b left join tbl_regularization  tr on b.empId = tr.requestFrom and  b.leaveType = tr.leaveType
		Left Join ".TABLE_EMP." e on b.empId=e.empId 
		Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		WHERE 1=1 $stateCond and tr.status='A'and tr.cancelled_status=0 ".$addsql." group by b.empId";
	 
	}


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
//22-Aug-18
function get_guest_details($resultType='G')
{
	$sql="select *,concat(empFname,' ',empLname) as empName from ".TABLE_GUEST_MASTER."";
	if($resultType=='G')
	{
		$result = parent::result_grid_array($sql);
	}
	else
	{
		$result = $this->db->query($sql)->result_array();
	}
		//pre($result);
	return $result;
}

function getProbationData($month, $empType)
{
	$sql="select e.empId,  DATE_FORMAT(DATE_ADD(e.empDOJ,INTERVAL 6 MONTH), '%d %b, %Y')as completedDate,  DATE_FORMAT(e.empDOJ,'%d %b, %Y') as empDOJ, concat(e.empFname,' ',e.empLname) as empName, d.name as department, de.name as designation   from ".TABLE_EMP." e
	Left Join ".TABLE_SERVICE." s on e.empId=s.empId
	Left Join ".TABLE_DEPT." d on e.empDept=d.id
	Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
	where e.empDOJ <=date_sub(now(), interval ".$month." month)  and  e.status='1' and s.empType=".$empType." order by e.empDOJ";
	$result=$this->db->query($sql)->result_array();
	return $result;
}

function getContractData($empType)
{
	$sql="select e.empId,  DATE_FORMAT(c.termEnddate, '%d %b, %Y')as contractExpire,  DATE_FORMAT(e.empDOJ,'%d %b, %Y') as empDOJ, concat(e.empFname,' ',e.empLname) as empName, d.name as department, de.name as designation,c.termEnddate    from ".TABLE_EMP." e
	Left Join ".TABLE_SERVICE." s on e.empId=s.empId
	Left Join ".TABLE_DEPT." d on e.empDept=d.id
	Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
	Left Join ".TABLE_CANDIDATE." c on e.candidateId=c.id
	where 1=1 and  e.status=1 and s.empType=".$empType." order by e.empId, e.empDOJ";
	$result=$this->db->query($sql)->result_array();
	return $result;
}
function getTodayemployeedata()
{
	$region = explode(',',$this->session->userdata('admin_region')); 
	if($region['0']){
		$addsql .=" and r.id in(".$this->session->userdata('admin_region').")";
	}
	$sql ="select DATE_FORMAT(e.empDOJ, '%d %b, %y')as empD, count(NULLIF(e.status, '' )) totalresign,  count(e.empId) as todayjoin from ".TABLE_EMP." e
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	where 1=1 ".$addsql." and e.empDOJ <='".date('Y-m-d')."' group by e.empDOJ order by e.empDOJ DESC limit 0,3" ;
	$result=$this->db->query($sql)->result_array();
	return $result;
} 
function getTotalworking()
{
	$sql ="select count(empId) as totalworing from ".TABLE_EMP." where status='1' and empDOJ<='".date('Y-m-d')."' group by status" ;
	$result=$this->db->query($sql)->result_array();
	return $result;
}

function clientwiseTotal()
{
	$region = explode(',',$this->session->userdata('admin_region')); 
	if($region['0']){
		$addsql .=" and r.id in(".$this->session->userdata('admin_region').")";
	}
	$sql ="select cl.name as client, count(e.empId) as headcount from ".TABLE_EMP." e
	left join ".TABLECLIENTS." cl on e.clients=cl.id
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id

	where 1=1 ".$addsql."  and e.status=1 group by e.clients";
	return $result=$this->db->query($sql)->result_array();
}
function headcount()
{
	$sql ="select count(empId) as totalheadcount from ".TABLE_EMP."  where status=1 and isActive=1";
	$result=$this->db->query($sql)->result_array();
	return $result['0'];
}
function totalempjointill()
{
	$sql ="select count(empId) as totalemp from ".TABLE_EMP."  where 1=1 and  isActive=1 and empId>2000000";
	$result=$this->db->query($sql)->result_array();
	return $result['0'];
}
function maximumResign()
{
	$sql ="SELECT count(empId) as mamimuxresign FROM ".TABLE_EMP." WHERE status=0 group by lastWorkingday ORDER BY count(empId) DESC limit 0 ,1 ";
	$result=$this->db->query($sql)->result_array();
	return $result['0'];
}
function empBirthday(){
	$region = explode(',',$this->session->userdata('admin_region')); 
	if($region['0']){
		$addsql .=" and r.id in(".$this->session->userdata('admin_region').")";
	}
	$sql= "select e.empImage, e.empId, concat(e.empFname, ' ',e.empLname) as empName, DATE_FORMAT(p.empDOB,'%d-%b') as empDOB from  ".TABLE_EMP." e
	left join ".TABLE_PERSONAL_DETAILS." p on e.empId=p.empId
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	WHERE DATE_ADD(p.empDOB, INTERVAL YEAR(CURDATE())-YEAR(p.empDOB) YEAR) BETWEEN DATE_SUB(CURDATE(), INTERVAL 0 DAY) AND DATE_ADD(CURDATE(), INTERVAL 2 DAY) and e.status=1 ORDER BY empDOB ASC  limit 0,40";

	$result=$this->db->query($sql)->result_array();
	return $result;
}
function empAnniversary(){
	$region = explode(',',$this->session->userdata('admin_region')); 
	if($region['0']){
		$addsql .=" and r.id in(".$this->session->userdata('admin_region').")";
	}
	$sql= "select e.empImage, e.empId, concat(e.empFname, ' ',e.empLname) as empName, DATE_FORMAT(p.empDOM,'%d-%b') as empDOM from  ".TABLE_EMP." e
	left join ".TABLE_PERSONAL_DETAILS." p on e.empId=p.empId
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	where DATE_FORMAT(p.empDOM,'%m')='".date('m')."' and DATE_FORMAT(p.empDOM,'%d')>='".date('d')."' and e.status=1 ".$addsql." order by DATE_FORMAT(p.empDOM,'%d') ASC  limit 0,40";
	$result=$this->db->query($sql)->result_array();
	return $result;
}
function empPf()
{
	$region = explode(',',$this->session->userdata('admin_region')); 
//pre($region);
	if($region['0']){
		$addsql .=" and r.id in(".$this->session->userdata('admin_region').")";
	}
	$sql ="select count(e.empId) as totalworkingemp, count( NULLIF(se.pfNumber, '' )) as totalpf, count( NULLIF(se.esicNumber, '' )) as totalesi from  ".TABLE_EMP." e 
	Left Join ".TABLE_SERVICE." se on e.empId=se.empId
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	WHERE 1=1 and e.status=1 and e.isActive=1 and e.empId>2000000 ";
	//echo $sql;die;
	$result=$this->db->query($sql)->result_array();
	return $result['0'];

}
function genderWisedata(){
	$region = explode(',',$this->session->userdata('admin_region')); 
//pre($region);
	if($region['0']){
		$addsql .=" and r.id in(".$this->session->userdata('admin_region').")";
	}
	$sql ="select se.empGender, count(se.empGender) as genderwiseemp from  ".TABLE_PERSONAL_DETAILS." se 
	Left Join ".TABLE_EMP." e on se.empId=e.empId
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	WHERE 1=1 and e.isActive=1 and e.empId!=10000 ".$addsql." group by se.empGender order by count(se.empGender) DESC ";
	$result=$this->db->query($sql)->result_array();
	return $result;
}
function todayempBirthday(){
	$sql= "select r.name as region,e.status, e.empEmailOffice, e.empEmailPersonal, e.empId, concat(e.empFname, ' ',e.empLname) as empName, DATE_FORMAT(p.empDOB,'%d-%b') as empDOB, m.empEmailOffice as reportingManager from  ".TABLE_EMP." e
	Left Join ".TABLE_EMP." m on e.reportingTo=m.empId
	Left join ".TABLE_PERSONAL_DETAILS." p on e.empId=p.empId
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	WHERE DATE_FORMAT(p.empDOB,'%d-%m')='".date('d-m')."' and e.status=1 ";
	return $result=$this->db->query($sql)->result_array();
}
function lastWorkingday($date)
{
	$sql ="select empId, concat(e.empFname, ' ',e.empLname) as empName, e.empEmailOffice, e.lastWorkingday from ".TABLE_EMP." e where 1=1 and e.lastWorkingday ='".$date."' and e.status=0 and e.empEmailOffice!=''";
	$result=$this->db->query($sql)->result_array();
	return $result;
}
function getticcard($empId)
{
	$sql ="select empId, name from ".TABLE_TIC." where 1=1 and empId=".$empId."";
	$result=$this->db->query($sql)->result_array();
	return $result['0'];
}
function getHrdetails()
{
	$sql ="select e.empId, r.name as region, e.empEmailOffice from ".TABLE_EMP." e
	Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
	Left Join ".TABLE_STATE." s on c.state=s.State_Id
	Left Join ".TABLE_REGION." r on s.region=r.id
	where 1=1 and e.empDept=10 and e.empId!='10537' and e.status=1 order by r.name";
	$result=$this->db->query($sql)->result_array();
	return $result;
}

function get_employee_resume_details($resultType)
{
	//pre($this->input->post());

	$addsql = '';
	$region = explode(',',$this->session->userdata('admin_region')); 

	if($this->input->post('designation'))
	{
		$addsql .= " and de.id=".$this->input->post('designation');
	}
	if($this->input->post('status')!='')
	{
		$addsql .= " and e.status='".$this->input->post('status')."' ";
	}
	if($this->input->post('state')!='')
	{
		$addsql .= " and s.State_Id='".$this->input->post('state')."' ";
	}
	if($this->input->post('region')!='')
	{
		$addsql .= " and r.id='".$this->input->post('region')."' ";
	}
	if($this->input->post('clients')!='')
	{
		$addsql .= " and cl.id='".$this->input->post('clients')."' ";
	}
	if($this->input->post('from')!='')
	{
		$addsql .= " and  date(e.empDOJ) between date('".$this->input->post('from')."') and date('".$this->input->post('to')."') ";
	}


	//$addsql .= " and e.status='1' ";


	if($this->input->post('filters')!='') // search filters
	{
		$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
	}
	
	$sql = "select  e.empId,e.empEmailOffice,e.status,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ, e.empEmailPersonal, e.empEmailOffice , de.name as desination, cl.name as clients, do.cVSubmited as resume,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName  from ".TABLE_EMP." e
	Left Join ".TABLE_DEPT." d on e.empDept=d.id
	Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
	LEFT JOIN ".TABLE_CANDIDATE." can on e.candidateId=can.id
	LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
	LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
	LEFT JOIN ".TABLE_REGION." r on s.region=r.id
	Left Join ".TABLECLIENTS." cl on e.clients=cl.id
	Left Join ".TABLE_DOCUMENT." do on e.empId=do.empId
	where 1=1 ".$addsql." and do.cVSubmited!=''  and e.empId!=10000";
	//echo $sql; 

	if($resultType=='G')
	{
		$result = parent::result_grid_array($sql);
	}
	else
	{
		$result = $this->db->query($sql)->result_array();
	}
	//pre($result);
	return $result;
}
	
	
	function get_employee_salaryslip_details($resultType)
	{
		
		$addsql = '';
		$region = explode(',',$this->session->userdata('admin_region')); 
		
		if($this->input->post('designation'))
		{
			$addsql .= " and de.id=".$this->input->post('designation');
		}
		if($this->input->post('status')!='')
		{
			$addsql .= " and e.status='".$this->input->post('status')."' ";
		}
		if($this->input->post('state')!='')
		{
			$addsql .= " and s.State_Id='".$this->input->post('state')."' ";
		}
		if($this->input->post('region')!='')
		{
			$addsql .= " and r.id='".$this->input->post('region')."' ";
		}
		if($this->input->post('clients')!='')
		{
			$addsql .= " and cl.id='".$this->input->post('clients')."' ";
		}
		if($this->input->post('month')){
			$addsql .="  and sl.month='".$this->input->post('month')."'";
		}
		if($this->input->post('year')){
			$addsql .=" and sl.year='".$this->input->post('year')."'";
		}
		
			//$addsql .= " and e.status='1' ";
		

		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select  sl.id, e.empId, d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, de.name as desination, cl.name as clients, sl.name as salaryslip, MONTHNAME(STR_TO_DATE(sl.month, '%m')) as monthname, sl.month, sl.month, sl.year,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName  from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		LEFT JOIN ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_SALARYSLIP." sl on e.empId=sl.empId
		where 1=1 and sl.empId>0 ".$addsql."  ";
		//echo $sql; 

		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		//pre($result);
		return $result;
	}
	function checkduplicatesalaryslip($month, $year, $empId)
	{
		$sql ="select empId from ".TABLE_SALARYSLIP." WHERE empId='".$empId."' and month='".$month."' and year='".$year."'";
		$result = $this->db->query($sql)->result_array();
		return $result = $result['0']['empId'];
	}

	//report for MisIndia Gionee_Report

	
	function misIndiaGioneeReport()
	{
		$sql ="select e.empId,e.empEmailOffice,e.status,d.name,concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,DATE_FORMAT(e.lastWorkingday,'%d-%b-%Y') lastWorkingday,DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,e.candidateId, e.empEmailPersonal, e.empEmailOffice,de.name as desination,r.name as region,s.State_Name as state,c.cityName as jobLocation,pr.name as projects,cl.name as clients,concat(em.empTitle,' ',em.empFname,' ',em.empLname) as manager,em.empEmailOffice as manageremail, emt.name as employeeType,ser.empType as empType,ser.ticcard,e.empMobile,p.empGender, DATE_FORMAT(p.empDOB,'%d-%b-%Y') as empDOB,e.reportingTo,sal.ctc, if(can.backout='1','Working','Backout') as 'backout'  from ".TABLE_EMP." e 
		left join ".TABLE_PERSONAL_DETAILS." p on e.empId=p.empId
		left join ".TABLE_SALARYBREAKUP." sal on e.empId=sal.empId
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_EMP." em on e.reportingTo=em.empId
		Left Join ".TABLE_SERVICE." ser on e.empId=ser.empId
		Left Join ".TABLE_EMPTYPE." emt on ser.empType=emt.id
		Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
		where 1=1 and e.empId!=10000 and (de.id='12' or de.id='13' or de.id='15' or de.id='26' or de.id='27' or de.id='31' or de.id='33') and cl.id='3'";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	
	function misCandidateIndiaGioneeReport()
	{
		$sql ="select e.id as candidateId, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, DATE_FORMAT(e.joiningDate,'%d-%b-%Y') empDOJ, de.name as desination,r.name as region,s.State_Name as state,c.cityName as jobLocation, pr.name as projects,cl.name as clients,e.gender as empGender, e.email as empEmailPersonal,e.mobile as empMobile, DATE_FORMAT(e.empDOB,'%d-%b-%Y') as empDOB, e.ctc, if(e.backout='1','Working','Backout') as 'backout'  from ".TABLE_CANDIDATE." e 
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.designation=de.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		where 1=1 and (de.id='12' or de.id='13' or de.id='15' or de.id='26' or de.id='27' or de.id='31' or de.id='33' ) and cl.id='3' order by e.id";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	
	function employeeListstatewise($state,$basic)
	{
		$addsql = "";
		if($state)
		{
			$addsql .= " and s.State_Id=".$state;
		}
		if($basic)
		{
			$addsql .= " and sal.basic < ".$basic;
		}
		
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilterssalary($this->input->post('filters'));
		}
		$sql ="select sal.* from ".TABLE_SALARYBREAKUP."  sal
		left join  ".TABLE_EMP." e on sal.empId=e.empId
		left join  ".TABLE_CITY_MASTER." c on e.jobLocation=c.cityId
		left join  ".TABLE_STATE_MASTER." s on c.state=s.State_Id
		WHERE 1=1 and e.status='1' ".$addsql."";
		$result = parent::result_grid_array($sql);
		return $result;
	}	
	function getcurrentsalary($empId, $newbasic='')
	{
		if($newbasi>0)
			$addsql .= "and basic < ".$newbasic."";
		$sql ="select * from ".TABLE_SALARYBREAKUP." WHERE empId=".$empId." ".$addsql."";
		$result = $this->db->query($sql)->result_array();
		return $result = $result['0'];
	}
	function emp_confirmation_details($empId)
	{
		$sql ="select * from tbl_confirmation_request WHERE empId=".$empId." ";
		$result = $this->db->query($sql)->result_array();
		return $result = $result['0'];
	}
	
	function get_salaryslip_details($id)
	{
		$sql ="select * from tbl_emp_salary_slip WHERE id=".$id." and mailalertsent='0'";
		$result = $this->db->query($sql)->result_array();
		return $result = $result['0'];
	}	
	function get_employee_salary_details($resultType='G')
	{

		$addsql = "";
		/////////////////////
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilterssalary($this->input->post('filters'));
		}
		
		$sql ="select sb.*, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as  empName,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName from ".TABLE_EMP." e
		LEFT JOIN ".TABLE_SALARYBREAKUP." sb on e.empId=sb.empId
		LEFT JOIN ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		WHERE 1=1  ".$addsql." and e.empId!=10000 and can.empType=2";
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

	function resignationReason()
	{
		$sql ="select * from tbl_resignreason WHERE status='1' and parent_id=0 ";
		return $result = $this->db->query($sql)->result_array();
	}
	function getRecord($empId, $table)
	{
		$sql ="select count(id) as recordFound from ".$table." WHERE empId='".$empId."' ";
		$result = $this->db->query($sql)->row();
		return $result;
	}
	
	function getReportingmanager($empId)
	{
		$sql ="select reportingTo from ".TABLE_EMP." WHERE empId='".$empId."' ";
		$result = $this->db->query($sql)->row();
		return $result;
	}
	function getFathermonther($empId)
	{
		$sql ="select empFathersName from ".TABLE_FAMILY." WHERE empId='".$empId."' and empRelation='Father' ";
		$result = $this->db->query($sql)->row();
		return $result;
	}
	function getNominee($empId)
	{
		$sql ="select nominee as recordFound from ".TABLE_FAMILY." WHERE empId='".$empId."' and nominee='1'";
		$result = $this->db->query($sql)->row();
		return $result;
	}
	
	function getHremail($region)
	{
		$sql ="select empEmailOffice from ".TABLE_EMP." WHERE regionPermission in(".$region.") and empDept=10 and status='1'";
		$result = $this->db->query($sql)->row();
		return $result;
	}
	function getTicdetails($id)
	{
		$sql ="select empId from ".TABLE_SERVICE."  where esicNumber='".$id."'";
		$result = $this->db->query($sql)->row();
		return $result->empId; 
	}	
	function getPancarddetails($panNo)
	{
		$sql ="select empId from ".TABLE_DOCUMENT." where panNo='".$panNo."'";
		$result = $this->db->query($sql)->row();
		return $result->empId; 
	}
	function get_emp_investmentDeclaration_details($empId, $resultType='G',$period='')
	{

		$addsql = "";
		if($empId)
		{
			$addsql .= " and r.empId=".$empId."";
		}
		if($period)
		{
			$addsql .= " and r.period='".$period."'";
		}
		/////////////////////
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilterssalary($this->input->post('filters'));
		}
		
		$sql ="SELECT r.id, r.metro, r.empId, r.amount, r.period, i.intrest,i.deducation_80c_parent, i.deduction_80c_health_heckup, i.medical_insurance_premium_80d, i.deduction_80c, i.remarks FROM ".TABLE_RENT_API." r inner join ".TABLE_INVESTMENT_DECLARE." i on r.id=i.id 
		WHERE 1=1  ".$addsql."  group by period";
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
	function ajaxReasonType($sid)
	{
		$sql="select id,name from tbl_resignreason where parent_id=".$sid." and status='1' order by name ASC";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	function checkExitInterviewExist($empId)
	{
		$sql="select exit_interview_id from tbl_emp_exit_interview where empId='".$empId."' ";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}

	// 21 nov
	function get_staff_employee_details($empId=0, $resultType='G')
	{

		$addsql = '';
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		if($empId > 0)
		{
			$addsql .= " and e.empId=".$empId;
		}
		if($this->input->post('status')!='')
		{
			$addsql .= " and e.status='".$this->input->post('status')."' ";
		}
		else
		{
			//$addsql .= " and e.status='1' ";
		}

		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select can.offerLettersent,can.termEnddate,e.joiningFor, e.empDesination, e.regionPermission, e.isActive, e.appointmentLetterSend,DATE_FORMAT(e.appointmentLetterSenddate,'%d-%b-%Y') appointmentLetterSenddate, e.empId,e.empEmailOffice,e.status,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,DATE_FORMAT(e.lastWorkingday,'%d-%b-%Y') lastWorkingday ,  DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,e.candidateId, e.empEmailPersonal, e.empEmailOffice , de.name as desination, pr.name as projects, cl.name as clients, em.empId as managerId, concat(em.empTitle,' ',em.empFname,' ',em.empLname) as manager,em.empEmailOffice as manageremail, emt.name as employeeType,ser.empType as empType, ser.ticcard,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName  from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id		
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_EMP." em on e.reportingTo=em.empId
		Left Join ".TABLE_SERVICE." ser on e.empId=ser.empId
		Left Join ".TABLE_EMPTYPE." emt on ser.empType=emt.id
		LEFT JOIN ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		
		where 1=1 ".$addsql." and e.empId!=10000 and can.empType=1 ";

		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		//pre($result);
		return $result;
	}
	//21-feb-18
	 function goalkpidetails($id,$resultType='')
	{
	   
		$addSql = "";
		if($id > 0)
		{
	 
			$addSql .= "and designationId=".$id;
			$addSql .= " and year=".date('Y');
			
		}
		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}
		$sql = "select tgk.*,concat(em.empFname,' ',em.empLname) as addedBy,DATE_FORMAT(tgk.isCreated,'%d-%b-%Y') as isCreated,tmd.name as designationName from tbl_goal_kpi tgk left join tbl_mst_designation tmd on tgk.designationId=tmd.id left join ".TABLE_EMP." em on tgk.addedBy=em.empId where 1=1 ".$addSql;
	
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
	//21-feb-18
	//13-march-18
	function get_resignation_details($empId=0, $resultType='G',$status=false)
	{

		$addsql = '';
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		if($empId > 0)
		{
			$addsql .= " and e.empId=".$empId;
		}
		if($this->input->post('status')!='')
		{
			$addsql .= " and e.status='".$this->input->post('status')."' ";
		}
		else
		{
				//$addsql .= " and e.status='1' ";
		}
		if($status)
		{
			$addsql .= " and e.status!='".$status."' ";
		}

			if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select ff.isApprovedByHR,ff.isApprovedByPM,ff.isApprovedByIT, ff.fandfId,can.offerLettersent,can.termEnddate,can.empType,e.joiningFor, e.empDesination, e.regionPermission, e.isActive, e.appointmentLetterSend,DATE_FORMAT(e.appointmentLetterSenddate,'%d-%b-%Y') appointmentLetterSenddate, e.empId,e.empEmailOffice,e.status,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,DATE_FORMAT(e.lastWorkingday,'%d-%b-%Y') lastWorkingday ,  DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,e.candidateId, e.empEmailPersonal, e.empEmailOffice , de.name as desination, pr.name as projects, cl.name as clients, em.empId as managerId, concat(em.empTitle,' ',em.empFname,' ',em.empLname) as manager,em.empEmailOffice as manageremail, emt.name as employeeType,ser.empType as serviceEmpType, ser.ticcard,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName  from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id		
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_EMP." em on e.reportingTo=em.empId
		Left Join ".TABLE_SERVICE." ser on e.empId=ser.empId
		Left Join ".TABLE_EMPTYPE." emt on ser.empType=emt.id
		Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN  tbl_fandf ff on ff.empId=e.empId
		where 1=1 ".$addsql." and e.empId!=10000 ";
		//echo $sql; die;
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		//pre($result);
		return $result;
	}
	//13-march-18
	//personal references details
	function personalreferencesDetails($eid=0,$resultType='G')
	{
		$sql ="select * from ".TABLE_EMP_PERSONAL_CNCT." where empId=".$eid." ";
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
	//personal references details
	//professional references details
	function professionalreferencesDetails($eid=0,$resultType='G')
	{
		$sql ="select * from ".TABLE_EMP_PROFESSIONAL_CNCT." where empId=".$eid." ";
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
	//professional references details
	//training details
	function get_trainingdetails($eid=0,$resultType='G')
	{
		$sql ="select * from ".TABLE_EMP_TRAINING." where empId=".$eid." ";
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
	//training details
	//additional details
	function getEmpAdditionalDetails($id)
	{
		$sql ="select * from ".TABLE_EMP_ADDITIONAL." where empId=".$id;
		return $result = $this->db->query($sql)->row_array();
	}
	//additional details
	//employee count kra
	function getemployeecount($doj,$count=false)
	{
	
	$sql ="select * from ".TABLE_EMP." where empDOJ<='".$doj."' AND status=1 AND `empId`>=20000001 and (empEmailOffice!='' and empEmailOffice!='teamhr@arkinfo.in')";

	if($count)
	{
	return $result = $this->db->query($sql)->num_rows();
	}
	else
	{
	 	return $result = $this->db->query($sql)->result_array();
	}
	
	}
	//employee count kra
	//kra status details
	
	function get_kra_details($eid=0,$resultType='G')
	{
		$sql ="SELECT tbl_kra_mst.id,tbl_kra_mst.status,tbl_kra_mst.financial_year,tbl_kra_mst.quarter,tbl_kra_mst.total_employee, 
sum(case when tbl_kra_emp.kra_filled_status = 1 then 1 else 0 end) as published,
   sum(case when tbl_kra_emp.kra_filled_status = 0 then 1 else 0 end) as unpublished,
   sum(case when tbl_kra_emp.email_status = 1 then 1 else 0 end) as email_send,
   sum(case when tbl_kra_emp.email_status = 0 then 1 else 0 end) as email_pending,
    sum(case when tbl_kra_emp.emp_status = 1 then 1 else 0 end) as emp_review_done,
   sum(case when tbl_kra_emp.emp_status = 0 then 1 else 0 end) as emp_review_pending,
    sum(case when tbl_kra_emp.manager_status = 1 then 1 else 0 end) as manager_review_done,
   sum(case when tbl_kra_emp.manager_status = 0 then 1 else 0 end) as manager_review_pending,
    sum(case when tbl_kra_emp.hr_status = 1 then 1 else 0 end) as hr_review_done,
   sum(case when tbl_kra_emp.hr_status = 0 then 1 else 0 end) as hr_review_pending
   
   FROM `tbl_kra_mst` left join tbl_kra_emp on tbl_kra_mst.id=tbl_kra_emp.quarter_id group by tbl_kra_emp.quarter_id ";
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
	function get_kra_data($Id,$check=0)
	{
	    $addSql="";
	    if($check=='1')
		{
		$addSql.="AND email_status=1 AND tbl_kra_emp.status=1 AND tbl_kra_emp.kra_filled_status=0";
		}
		else if($check=='2')
		{
		$addSql.="AND email_status=1 AND tbl_kra_emp.status=1 AND tbl_kra_emp.emp_status=0 AND tbl_kra_emp.kra_filled_status=1";
		}
		else if($check=='3')
		{
		$addSql.="AND email_status=1 AND tbl_kra_emp.status=1 AND tbl_kra_emp.emp_status=1 AND tbl_kra_emp.kra_filled_status=1 AND tbl_kra_emp.manager_status=0";
		}
		else
		{
		$addSql.="AND email_status=0";
		}
		$sql="select tbl_kra_emp.id,tbl_kra_emp.empId,tbl_kra_emp.email,concat(tem.empFname, ' ',tem.empLname) as manager_name,concat(tbl_emp_master.empFname,' ',tbl_emp_master.empLname) as name,tbl_emp_master.reportingTo,tem.empEmailOffice as manager_email  from tbl_kra_emp Left Join  tbl_emp_master on tbl_kra_emp.empId=tbl_emp_master.empID  LEFT join tbl_emp_master tem on tbl_emp_master.reportingTo=tem.empId where tbl_kra_emp.quarter_id='".$Id."' ".$addSql."";
		
		$result=$this->db->query($sql)->result_array(); 
		return $result;
	}
	function get_kra_emp_details($eid=0,$resultType='G',$mId=0)
	{
	   if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
	   if($mId)
	   {
	   $sql="Select *,CONCAT(empFname,' ',empMname,' ',empLname)as name,tbl_kra_emp.id as id,tbl_kra_emp.status as rejectStatus from tbl_kra_emp left join tbl_kra_mst on tbl_kra_mst.id=tbl_kra_emp.quarter_id left join tbl_emp_master on tbl_kra_emp.empId=tbl_emp_master.empId   where email_status=1  and reportingTo='".$mId."' 
	   ".$addsql." order by tbl_kra_emp.id desc";
	   }
	   else
	   {
		$sql ="Select *,tbl_kra_emp.id as id ,tbl_kra_emp.status as rejectStatus from tbl_kra_emp left join tbl_kra_mst on tbl_kra_mst.id=tbl_kra_emp.quarter_id where email_status=1  and empId='".$eid."'  ".$addsql." ";
		}
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

	/*Start modification 7-nov-2019 */
	public function get_kra_emp_data($kra_emp_id)
	{
		$this->db->select('*');
		$this->db->from('tbl_kra_emp');
		$this->db->where('id',$kra_emp_id);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$result = $query->result_array();
			return $result = $result[0];
		}else{
			return array();
		}

	}
	/*End modification 7-nov-2019 */

	function get_kra_attributes_data($kra_emp_id,$quarter_id,$emp_id)
	{
	    $addsql="";
		//echo $emp_id;die;
		if($emp_id)
		{
		$addsql.="AND empId='".$emp_id."'";
		}
	   $sql="select  *  from tbl_kra_attributes where kra_emp_id='".$kra_emp_id."' AND status=1 AND quarter_id='".$quarter_id."' $addsql ";
		//echo $sql;die;
		$result=$this->db->query($sql)->result_array(); 
		return $result;
	
	}
	
 function get_kra_attributes_reject_data($kra_emp_id,$quarter_id,$emp_id)
	{
	    $addsql="";
		//echo $emp_id;die;
		if($emp_id)
		{
		$addsql.="AND reject.empId='".$emp_id."' and  reject.status=1";
		}
	   $sql="select  reject.comment  from  tbl_kra_reject_comment as reject   where reject.emp_kra_id='".$kra_emp_id."'   AND reject.quarter_id='".$quarter_id."' $addsql ";
		//echo $sql;die;
		$result=$this->db->query($sql)->result_array(); 
		return $result;
	
	}
 function get_HRkra_emp_details($eid=0,$resultType='G',$mId=0)
	{
		$addsql="";
		$uId = $this->session->userdata('admin_id');       

		if($this->input->post('filters')!='')
		 // search filters
		{   
		     $filterResultsJSON = json_decode($this->input->post('filters'));
			 $filterArray = get_object_vars($filterResultsJSON);
			// print_r($filterArray['rules']);die; 
			 if(!empty($filterArray['rules']))
			 {
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			 }
		}
		else {
			$addsql="";
		}

		if($uId == 20000495){
			$addsql .= " and tbl_emp_master.empDept != 21";
		}
		//echo $addsql;die;
		$filter="tbl_emp_master.reportingTo=".$mId."";
	   $sql="Select *,CONCAT(empFname,' ',empMname,' ',empLname)as name,empDOJ as doj,isActive,de.name as designaionName,tbl_kra_emp.id as id,tbl_kra_emp.status as rejectStatus,tbl_mst_dept.name as department,reporting_name(reportingTo) as reporting from tbl_kra_emp left join tbl_kra_mst on tbl_kra_mst.id=tbl_kra_emp.quarter_id left join tbl_emp_master on tbl_kra_emp.empId=tbl_emp_master.empId  left join  tbl_mst_dept on tbl_emp_master.empDept=tbl_mst_dept.id Left Join tbl_mst_designation de on tbl_emp_master.empDesination=de.id where email_status=1  and tbl_emp_master.empId>=20000001  ".$addsql." order by tbl_kra_mst.financial_year,tbl_kra_mst.quarter desc";
	
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
	//kra status details
	//no of leaves
	function get_no_leave_data($leavegrp)
	{
		$sql="select  tbl_mst_leavetype.name,(CASE WHEN tbl_mst_leavetype.name='CL' THEN 'CASUAL LEAVES' WHEN tbl_mst_leavetype.name='SL' THEN 'SICK LEAVES' WHEN tbl_mst_leavetype.name='EL' THEN 'EARNED LEAVES' END) as fullname, tbl_mst_leave.*  from tbl_mst_leave Left join tbl_mst_leavetype on tbl_mst_leave.leaveType=tbl_mst_leavetype.id where leaveGroup='".$leavegrp."' ";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	function get_emp_leave_data($empId)
	{
		$sql="select  *  from tbl_emp_leave_balance where empId='".$empId."' ";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	
	//no of leaves
	//service for mobileservice
	function get_emp_service_data($empId)
	{
		$sql="select  tbl_emp_service.*,tbl_emp_master.empDOJ  from tbl_emp_service left join tbl_emp_master on tbl_emp_service.empId=tbl_emp_master.empId where tbl_emp_service.empId='".$empId."' ";
		$result=$this->db->query($sql)->row_array();
		return $result;
	}
	//service for mobileservice
	 
	 // get upcomming birthday details
	 function get_upcomming_birthday($timePeriod){
	       $sql="SELECT concat(tem.empFname,' ',tem.empMname,'',tem.empLname) as empName,tem.empImage,tep.empDOBactual,tmd.name as departmentName,
                  tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 0 YEAR AS currbirthday,
                  tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 1 YEAR AS nextbirthday
                  FROM tbl_emp_personal as
                  tep inner join tbl_emp_master as tem on tem.empId = tep.empId left join tbl_mst_dept as tmd on tem.empDept = tmd.id where tep.empDOBactual != '0000-00-00' and tem.isActive = '1'  ORDER BY CASE
                  WHEN currbirthday >= CURRENT_TIMESTAMP THEN currbirthday
                  ELSE nextbirthday 
                  END 
                  limit $timePeriod";
		     //echo  $sql;die;
		   $result  = $this->db->query($sql)->result_array();		   
		   return $result;
	 }


function checkAttendanceLog($empId, $date)
{
	$sql ="select empId,attendanceStatus from ".TABLE_ATTENDANCE." WHERE empId='".$empId."' and attendanceDate='".$date."'";

    
	$result=$this->db->query($sql)->result_array(); 
	return $result['0'];
}

function getTaTeamDetails(){       
	   $resultData = array(); 
	   $sql      =   "select empId,empEmailOffice as empEmail from ".TABLE_EMP." WHERE empRole='13'";	
	   $result   =   $this->db->query($sql)->result_array(); 
	    if(isset($result) && !empty($result)){
		    foreach($result as  $resData){
                $resultData[] =  $resData['empEmail'];
            }			
		}		
        return $resultData; 		
}


/******** get Quarter month ****************/
function get_quater_months($kraQuarterId){ 
         $months = array("Jan","Feb","Mar","Apr","May","Jun","July","Aug","Sep","Oct","Nov","Dec");
         $monthName = '';		 
		 $resultData = array();
		 $result     = $this->db->query("select financial_year,quarter,initiation_start_date,initiation_end_date,self_eval_initiate_date,self_eval_end_date from tbl_kra_mst  where id ='". $kraQuarterId."'")->result_array();
		 if(isset($result[0]))	{
		     $resultData = $result[0];
			 if($resultData['quarter'] != ''){
			        $quarterList =  explode('-',$resultData['quarter']);
					    if(!empty($quarterList)){
						      for($i=$quarterList[0];$i<=$quarterList[1];$i++){
							         $monthName .=  substr($months[$i-1], 0, 1);
							  }
						}
			 }
			  $qmonth = explode('-',$resultData['financial_year']);
			  if($resultData['quarter'] == '01-03') {
				   $resultData['monthName']= $monthName.'`'.$qmonth[1];
		       } else {
				   $resultData['monthName']= $monthName.'`'.$qmonth[0];
		        } 
         }	
         return $resultData;		 
	}

 function get_current_quater_months(){ 
         $months = array("Jan","Feb","Mar","Apr","May","Jun","July","Aug","Sep","Oct","Nov","Dec");
         $monthName = '';		 
		 $resultData = array();
		$result     = $this->db->query("select id,financial_year,quarter,initiation_start_date,initiation_end_date,self_eval_initiate_date,self_eval_end_date from tbl_kra_mst where initiation_end_date >= CURDATE() order by id  desc limit 1")->result_array();
		 if(isset($result[0]))	{
		     $resultData = $result[0];
			 if($resultData['quarter'] != ''){
			        $quarterList =  explode('-',$resultData['quarter']);
					    if(!empty($quarterList)){
						      for($i=$quarterList[0];$i<=$quarterList[1];$i++){
							         $monthName .=  substr($months[$i-1], 0, 1);
							  }
						}
			 }
			  $qmonth = explode('-',$resultData['financial_year']);
			  if($resultData['quarter'] == '01-03') {
					$resultData['monthName']= $monthName.'`'.$qmonth[1];
			  } else {
			     	$resultData['monthName']= $monthName.'`'.$qmonth[0];
			  }
         }	
         return $resultData;		 
	}	
	
 function get_current_quater_months_self_eval(){ 
         $months = array("Jan","Feb","Mar","Apr","May","Jun","July","Aug","Sep","Oct","Nov","Dec");
         $monthName = '';		 
		 $resultData = array();
		$result     = $this->db->query("select id,financial_year,quarter,initiation_start_date,initiation_end_date,self_eval_initiate_date,self_eval_end_date from tbl_kra_mst where self_eval_end_date >= CURDATE() order by id  desc limit 1")->result_array();
		 if(isset($result[0]))	{
		     $resultData = $result[0];
			 if($resultData['quarter'] != ''){
			        $quarterList =  explode('-',$resultData['quarter']);
					    if(!empty($quarterList)){
						      for($i=$quarterList[0];$i<=$quarterList[1];$i++){
							         $monthName .=  substr($months[$i-1], 0, 1);
							  }
						}
			 }
			  $qmonth = explode('-',$resultData['financial_year']);
			  if($resultData['quarter'] == '01-03') {
					$resultData['monthName']= $monthName.'`'.$qmonth[1];
			  } else {
			     	$resultData['monthName']= $monthName.'`'.$qmonth[0];
			  }
         }	
         return $resultData;		 
	}	
	
function getReportingManagers($clientId=false)
	{
		$wh = " ";
		if($clientId)
		{
			$wh = " and clientId = '".$clientId."' ";
		}
		$sql ="Select CONCAT(a.empFname,' ',a.empMname,' ',a.empLname)as name,a.`empId` FROM `tbl_emp_master` as a left join tbl_emp_master as b on a.`empId`=b.`reportingTo`  WHERE 1=1  and a.empId>=20000001 and a.isActive=1 group by b.`reportingTo` order by a.empFname asc ";
		return $result = $this->db->query($sql)->result_array();
	}
	
	
	function get_kra_annual_report($resultType='G',$filterType)
	{
		$addsql="";		  
		$roleId = $this->session->userdata('role');
		$uId = $this->session->userdata('admin_id');
		if($this->input->post('filters')!='')
		 // search filters
		{   
		     $filterResultsJSON = json_decode($this->input->post('filters'));
			 $filterArray = get_object_vars($filterResultsJSON);
		
			 if(!empty($filterArray['rules']))
			 {
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			$myJson = json_decode($this->input->post('filters'));
             
		foreach($myJson->{'rules'} as $data)
		{
		
		if($data->{'field'}=='tbl_kra_mst.financial_year')
			{
				
			 $yearfilter   =  "and  financial_year = '".$data->{'data'}."'";
				 
			  }
		    }
			 }
		}
		else{
			$addsql="";
		} 
		
		if($roleId != 12 && $uId !='10000' && $uId != 20000495){
			    $addsql  .= "and tbl_emp_master.reportingTo=".$uId; 
		} 
		
	    if($filterType == 'R'){
				$empId                   =   isset($_GET['empId'])? $_GET['empId']:'';
				$empName 				 =   isset($_GET['empName'])?$_GET['empName']:'';
				$financial_year         =   isset($_GET['financial_year'])?$_GET['financial_year']:'';
				$department            =   isset($_GET['department'])?$_GET['department']:'';
				$rep_manager           =   isset($_GET['rep_manager'])?$_GET['rep_manager']:'';
		     
			if($empId !=''){
				$addsql   .=  "and tbl_emp_master.empId like '".$empId."%'";
			} else if($empName !=''){
				$addsql   .=  "and  tbl_emp_master.empFname LIKE '".$empName."%'";
			} else if($financial_year !=''){
				
				$addsql   .=  "and  tbl_kra_mst.financial_year = ".$financial_year;
				$yearfilter   =  "and  financial_year = ".$financial_year;
			} else if($department){
				$addsql   =  "and  tbl_mst_dept.id  =  '".$department."%'";
			}			
     }
		
	  if($uId == 20000495){
		$addsql .= " and tbl_emp_master.empDept != 21";
	  }

      $quesrterQuery="SELECT group_concat(id)as  financialsYear,financial_year as kra_year FROM tbl_kra_mst where 1=1 ".$yearfilter."  group by financial_year order by  financial_year desc limit 0,1  "; 
	
		$quesrtes   =   $this->db->query($quesrterQuery)->result_array();
		//print_r($quesrtes);die;
		$quarterIdz      =   $quesrtes[0]['financialsYear'];
		$quarterIdz      =   explode(',',$quarterIdz);
		$kraYear         =    $quesrtes[0]['kra_year'];
		$latestQuarterId =  end($quarterIdz);
		
		//$filter="tbl_emp_master.reportingTo=".$mId."";
		
	       $sql="Select *,tbl_emp_master.empId as emp_id,de.name as designation_name,group_concat(manager_tot_weightage) as allQuarter,group_concat(tbl_kra_emp.quarter_id) as quarterId,CONCAT(empFname,' ',empMname,' ',empLname)as name,tbl_kra_emp.id as id,tbl_kra_emp.status as rejectStatus,tbl_mst_dept.name as department,reporting_name(reportingTo) as reporting,tbl_kra_mst.financial_year as finaceYear ,GET_JSON_ARRAY_OF_KRA_STATUS('".$quarterIdz[0]."',tbl_emp_master.empId) as q1Performance,GET_JSON_ARRAY_OF_KRA_STATUS('".$quarterIdz[1]."',tbl_emp_master.empId) as q2Performance ,GET_JSON_ARRAY_OF_KRA_STATUS('".$quarterIdz[2]."',tbl_emp_master.empId) as q3Performance ,GET_JSON_ARRAY_OF_KRA_STATUS('".$quarterIdz[3]."',tbl_emp_master.empId) as q4Performance,getQuarter1Exist('".$kraYear."') as Q1Exist,getQuarter2Exist('".$kraYear."') as Q2Exist,getQuarter3Exist('".$kraYear."') as Q3Exist,getQuarter4Exist('".$kraYear."') as Q4Exist,$latestQuarterId as latest_quarter_id from tbl_kra_emp left join tbl_kra_mst on tbl_kra_mst.id=tbl_kra_emp.quarter_id left join tbl_emp_master on tbl_kra_emp.empId=tbl_emp_master.empId  left join  tbl_mst_dept on tbl_emp_master.empDept=tbl_mst_dept.id Left Join tbl_mst_designation de on tbl_emp_master.empDesination=de.id where email_status=1  and tbl_emp_master.empId>=20000001  ".$addsql." and tbl_kra_mst.financial_year='".$kraYear."' and tbl_emp_master.isActive=1 group by tbl_emp_master.empId,tbl_kra_mst.financial_year order by tbl_kra_mst.financial_year,tbl_kra_mst.quarter desc";
	   
	   //Select *,tbl_emp_master.empId as emp_id,group_concat(manager_tot_weightage) as allQuarter,group_concat(tbl_kra_emp.quarter_id) as quarterId,CONCAT(empFname,' ',empMname,' ',empLname)as name,tbl_kra_emp.id as id,tbl_kra_emp.status as rejectStatus,tbl_mst_dept.name as department,reporting_name(reportingTo) as reporting,getQuarterIds(tbl_kra_mst.financial_year) as quarterIdz ,getQuarterPerformance(@quarterIdz,1,@emp_id) as Q1Performance,getQuarterPerformance(@quarterIdz,2,@emp_id) as Q2Performance ,getQuarterPerformance(@quarterIdz,3,@emp_id) as Q3Performance ,getQuarterPerformance(@quarterIdz,4,@emp_id) as Q4Performance from tbl_kra_emp left join tbl_kra_mst on tbl_kra_mst.id=tbl_kra_emp.quarter_id left join tbl_emp_master on tbl_kra_emp.empId=tbl_emp_master.empId left join tbl_mst_dept on tbl_emp_master.empDept=tbl_mst_dept.id where email_status=1 and tbl_emp_master.empId>=20000001 and tbl_kra_emp.`empId`=20000499 group by tbl_emp_master.empId,tbl_kra_mst.financial_year order by tbl_kra_mst.financial_year,tbl_kra_mst.quarter desc
	   //echo $sql;die;
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
				//$result = $this->db->query($sql)->result_array();
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		
		
		return $result;
	}
	
	// birthday and anniversary section
	/***** fetch all employee who have birthday on current date ******/
	 function get_emp_birthday_list(){
	        
		   $sql="SELECT tem.empId,tem.empFname as empName,tem.empLname,tem.empEmailOffice,tem.empImage,tep.empDOBactual FROM `tbl_emp_master` as tem inner join tbl_emp_personal as tep on tem.empId = tep.empId where  tem.isActive='1' and DATE_FORMAT(tep.empDOBactual,'%m-%d') = DATE_FORMAT(CURDATE(),'%m-%d')
				 UNION 
				 SELECT tem.empId,tem.empFname as empName,tem.empLname,tem.empEmailOffice,tem.empImage,tem.empDOBactual FROM `tbl_emp_brand` as tem  where  tem.isActive='1' and DATE_FORMAT(tem.empDOBactual,'%m-%d') = DATE_FORMAT(CURDATE(),'%m-%d')";

		   $result  = $this->db->query($sql)->result_array();		   
		   return $result;	 
	 }
	 
	 /***** fetch all employee who have complete 1,2.... years  anniversary in company ******/
	 function get_emp_aniversary_list(){	 
	        
			$sql="select tem.empId,tem.empFname as empName,tem.empLname,tem.empEmailOffice,tem.empImage,tem.empDOJ,
				      YEAR(CURDATE()) - YEAR(tem.empDOJ) as count_years
				      from tbl_emp_master as tem where tem.isActive='1' and DATE_FORMAT(tem.empDOJ,'%m-%d') =  DATE_FORMAT(CURDATE(),'%m-%d')
				  UNION
				 select tem.empId,tem.empFname as empName,tem.empLname,tem.empEmailOffice,tem.empImage,tem.empDOJ,
				 YEAR(CURDATE()) - YEAR(tem.empDOJ) as count_years
				 from tbl_emp_brand as tem where tem.isActive='1' and DATE_FORMAT(tem.empDOJ,'%m-%d') =  DATE_FORMAT(CURDATE(),'%m-%d')
				 ";

		    $result  = $this->db->query($sql)->result_array();		   
		    return $result;
	 }	
	 
	 // fetch birthday template
	  function get_birth_template(){
			$sql="select email_name,email_content,email_code,email_subject from email_templates where email_type=2 and isActive=1 ";				 
		    $result  = $this->db->query($sql)->result_array();		   
		    return $result;
	  }

	   // fetch birthday template
	function get_non_image_birth_template(){
		$sql="select email_name,email_content,email_code,email_subject from email_templates where email_type=5 and isActive=1 ";				 
		
		$result  = $this->db->query($sql)->result_array();		   
		return $result;
    }

       // fetch anniversary template
	  function get_anniversary_template($countYear){
	        $countYear =  trim($countYear);
			if($countYear > 7){
				$sql = "select email_name,email_content,email_code,email_subject from email_templates where email_code= 'ANNIVERSARY_TEMPLATE_ALL'  and email_type=3 and isActive=1";	
			} else {
				$sql = "select email_name,email_content,email_code,email_subject from email_templates where email_code= 'ANNIVERSARY_TEMPLATE_ALL'  and email_type=3 and isActive=1";	
				// $sql = "select email_name,email_content,email_code,email_subject from email_templates where email_code= 'ANNIVERSARY_TEMPLATE_".$countYear."'  and email_type=3 and isActive=1";	
			 }
			
		    $result  = $this->db->query($sql)->result_array();		   
		    return $result;
	  }	  
	 
function get_emp_images_details($id=0, $resultType='G')
	{	    
		if($id){
		$addsql .= " AND a.empId=".$id."";
		}
	 
		if($this->input->post('filters')!='') // search filters
		{
		      if(self::decodeFilters($this->input->post('filters')))
			{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
		}
		//new search option
		
		//new search option
	
	
		$sql = "select e.empId,concat(e.empFname,' ',e.empLname) as empName,DATE_FORMAT(e.empDOJ,'%d-%b-%Y') as doj,d.name as dept,de.name as desig,c.cityName,a.empImgUrl,a.createdDate,e.empImage,DATE_FORMAT(tep.empDOBactual,'%d-%b') as empDOBactual from  ".TABLE_EMP." e 
		LEFT JOIN  emp_img_tbl  a  on e.empId =a.empId
		LEFT JOIN ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN  tbl_emp_personal  tep on e.empId =tep.empId
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id		
		
 		 where 1=1 and e.isActive = 1 and e.empId !='10000' ".$addsql. " order by month(tep.empDOBactual),day(tep.empDOBactual)";
	
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		//pre($result);
		
		return $result;
		
	}	
function departWiseEmpCount()
	
	{   
	$sql="select emt.name as employeeType, SUM(CASE  WHEN emt.name='probation' THEN 1  ELSE 0  END) AS probation,SUM(CASE  WHEN emt.name='Permanent' THEN 1  ELSE 0  END) AS ConfirmEmployee,count(e.empId) AS countEmplyee, concat( upper(substring(cityjob.cityName,1,1)),lower(substring(cityjob.cityName,2)) ) as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName,d.name  from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id		
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_EMP." em on e.reportingTo=em.empId
		Left Join ".TABLE_SERVICE." ser on e.empId=ser.empId
		Left Join ".TABLE_EMPTYPE." emt on ser.empType=emt.id
		Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_CITY." cityjob on e.jobLocation=cityjob.cityId
		LEFT JOIN ".TABLE_STATE." s on cityjob.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		
		where 1=1 and e.isActive=1 group by e.empDept order by count(e.empId) desc ";
//echo $sql;die;
		   $result  = $this->db->query($sql)->result_array();		   
		   return $result;
		
	}
function pendingDoneKra($lastquarter ='')
{
	$sql="SELECT  SUM(CASE  WHEN `manager_verified_status`=1 THEN 1  ELSE 0  END) AS kraFIll, SUM(CASE  WHEN `manager_verified_status`=0 THEN 1  ELSE 0  END) AS pendingKra, count(empId) as totalEmployee FROM `tbl_kra_emp`  WHERE `quarter_id`=(SELECT max(id) FROM  tbl_kra_mst  WHERE 1=1  ) and empId>=20000001 and`email`!='teamhr@arkinfo.in'  group by  quarter_id";
	
     $result  = $this->db->query($sql)->result_array();		   
     return $result;
	 
	 	
}

function regionWiseData()
{
	$sql="select count(e.empId) AS countEmplyee, concat( upper(substring(cityjob.cityName,1,1)),lower(substring(cityjob.cityName,2)) ) as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName,d.name from tbl_emp_master e Left Join tbl_mst_dept d on e.empDept=d.id Left Join tbl_mst_designation de on e.empDesination=de.id	Left Join tbl_project pr on e.projects=pr.id Left Join tbl_client cl on e.clients=cl.id Left Join tbl_emp_master em on e.reportingTo=em.empId Left Join tbl_emp_service ser on e.empId=ser.empId Left Join tbl_mst_emptype emt on ser.empType=emt.id Left Join tbl_candidate can on e.candidateId=can.id LEFT JOIN tbl_mst_city c on can.jobCity=c.cityId LEFT JOIN tbl_mst_city cityjob on e.jobLocation=cityjob.cityId LEFT JOIN tbl_mst_state s on cityjob.state=s.State_Id LEFT JOIN tbl_region r on s.region=r.id where 1=1 and e.isActive=1 group by s.region order by  count(e.empId) desc ";
	
     $result  = $this->db->query($sql)->result_array();		   
     return $result;
	 
}

// Get employee budget
function get_emp_budget($resultType='G'){
	
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
	
	$sql = "select e.empId, concat(e.empFname,' ',e.empLname) as empName,e.empRole,e.empImage, e.empEmailPersonal, e.empEmailOffice, e.empMobile, e.empDOJ, e.empDept, e.empDesination, e.reportingTo, e.jobLocation, e.clients, e.projects, e.status, e.createdBy, e.candidateId, e.lastLogin, e.appointmentLetterSend, e.isCreated,d.name as departmentName,de.name as designationName,c.cityName,umb.monthly_budget from ".TABLE_EMP." e 
			Left Join user_monthly_budget umb  on  e.empId = umb.emp_id
	        Left Join ".TABLE_DEPT." d on e.empDept=d.id
			Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
			LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
			where 1=1 ". $addSql; 
	
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

// get employee budget detail

function get_emp_budget_detail($empId){
   
		if($empId !='')
		{			
			$sql = "select tem.empId,umb.monthly_budget,umb.id from ".TABLE_EMP." as tem left join user_monthly_budget as umb on tem.empId = umb.emp_id where tem.empId=$empId";
			return $this->db->query($sql)->result_array();
        }
}
 function upcoming_aniversary_list(){	 
	        
			$sql="select tem.empId,concat(tem.empFname,' ',tem.empLname) as empName,tem.empEmailOffice,tem.empImage,tem.empDOJ,
				      YEAR(CURDATE()) - YEAR(tem.empDOJ) as count_years
				      from tbl_emp_master as tem where DATE_FORMAT(tem.empDOJ,'%m') = DATE_FORMAT(CURDATE(),'%m') and  DATE_FORMAT(tem.empDOJ,'%d') > DATE_FORMAT(CURDATE(),'%d')";
		 
		    $result  = $this->db->query($sql)->result_array();		   
		    return $result;
	 }
	 
 function locationWiseEmployee()
	
	{   
	$sql="select emt.name as employeeType, SUM(CASE  WHEN emt.name='probation' THEN 1  ELSE 0  END) AS probation,SUM(CASE  WHEN emt.name='Permanent' THEN 1  ELSE 0  END) AS ConfirmEmployee,count(e.empId) AS countEmplyee, concat( upper(substring(cityjob.cityName,1,1)),lower(substring(cityjob.cityName,2)) ) as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName,d.name  from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id		
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_EMP." em on e.reportingTo=em.empId
		Left Join ".TABLE_SERVICE." ser on e.empId=ser.empId
		Left Join ".TABLE_EMPTYPE." emt on ser.empType=emt.id
		Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_CITY." cityjob on e.jobLocation=cityjob.cityId
		LEFT JOIN ".TABLE_STATE." s on cityjob.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		
		where 1=1 and e.isActive=1 group by e.jobLocation  order by count(e.empId) desc ";
//echo $sql;die;
		   $result  = $this->db->query($sql)->result_array();		   
		   return $result;
		
	}

	  // fetch new joinee template
	  function get_newjoinee_template(){			
		$this->db->select('email_name,email_content,email_code,email_subject');
		$this->db->where('email_type',4);
		$this->db->where('isActive',1);
		$this->db->order_by('rand()');
		$this->db->limit(1);
		$query = $this->db->get('email_templates');
		return  $query->result_array();			
	 }
	 

     // get all employee kra data
	 function get_all_emp_kra_data($resultType='G'){
		
			
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

		if($uId == 20000495){
			$addSql .= " and tbl_emp_master.empDept != 21";
		}
		
		
		$sql="Select tka.id,tbl_kra_emp.empId,tbl_kra_mst.financial_year,tbl_kra_mst.quarter,CONCAT(empFname,' ',empMname,' ',empLname)as name,empDOJ,if(isActive = 1,'Active','In Active') as isActive,de.name as designationName,tmc.cityname,
		tbl_kra_emp.status as rejectStatus,tbl_mst_dept.name as department,reporting_name(reportingTo) as reporting,tka.attributes,tka.defination,tka.quantitative,tka.annual_target,tka.quarter_target,tka.weightage,
		tka.user_weightage,tka.user_performance,tka.user_achievement,tka.user_achievement_manager,tka.user_performance_manager,
		tka.manager_weightage,tka.formula_1,tka.formula_2,CASE
	    	WHEN tbl_kra_mst.quarter = '01-03'  THEN 'Jan-Mar'
			WHEN tbl_kra_mst.quarter = '04-06'  THEN 'Apr-June'
			WHEN tbl_kra_mst.quarter = '07-09'   THEN 'July-Sept'
			WHEN tbl_kra_mst.quarter = '10-12'  THEN 'Oct-Dec'		  
		   END as quater_month
		from tbl_kra_emp left join tbl_kra_mst on tbl_kra_mst.id=tbl_kra_emp.quarter_id left join tbl_emp_master on tbl_kra_emp.empId=tbl_emp_master.empId
		left join tbl_kra_attributes tka on  tbl_kra_emp.quarter_id =tka.quarter_id and tbl_kra_emp.empId = tka.empId left join  tbl_mst_dept
		on tbl_emp_master.empDept=tbl_mst_dept.id Left Join tbl_mst_designation de on tbl_emp_master.empDesination=de.id left join tbl_mst_city tmc on tbl_emp_master.jobLocation = tmc.cityId  where  tbl_emp_master.empId>=20000001 ". $addSql."   order by tka.empId desc";
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
	
	function getEmpdata($empId)
		
		{
			//16-Aug-18
			$sql =" select s.State_Id ,a.empId,e.empDOJ, e.status,e.clients, r.id as region, se.leaveGroup, se.shift as servicesShift, ed.shift as departmentShift from rowdata a
			
			left join ".TABLE_EMP." e on a.empId=e.empId
			
			left join ".TABLE_DEPT." ed on e.empDept=ed.id
			
			left join ".TABLE_SERVICE." se on e.empId=se.empId
			
			left join ".TABLE_CITY_MASTER." c on e.jobLocation=c.cityId
			
			left join ".TABLE_STATE_MASTER." s on c.state=s.State_Id
			
			left join ".TABLE_REGION_MASTER." r on s.region=r.id
			
			WHERE 1=1 and e.isActive=1 and e.empId=".$empId."   group by e.empId";
			
			return $result = $this->db->query($sql)->result_array();
			
		}
function update_KRA_formula($ids,$fid,$updateType)
{
	 if($fid==1){
		 $filedName="formula_1";
	 }else{
		 $filedName="formula_2";
	 }
	 if($updateType=="reject") { 
	  $sql =" update tbl_kra_attributes set ". $filedName."=0 where id IN(".$ids.")";
	
	 }else{ 
	  $sql =" update tbl_kra_attributes set ". $filedName."=1 where id IN(".$ids.")";  
	 }
	
	return $result = $this->db->query($sql);
	echo "Success";die; 
}

function getQuarterId($ids)
{
		
	 $empIdQuarter="Select DISTINCT(empId),quarter_id from  tbl_kra_attributes  where id IN(".$ids.")  group by  empId ";
	 
	 $result1 = $this->db->query($empIdQuarter)->result_array();
	 $user_weightage=0;
	 
	 $manager_weightage=0;
	 foreach( $result1 as $result1)
	 {
	  $kraData="Select empId,`quarter_target` , `weightage` , `user_weightage` , `user_performance` ,`user_achievement` ,  `user_achievement_manager`,`user_performance_manager`,`manager_weightage`,`formula_1`,  `formula_2`  from  tbl_kra_attributes  where empId=".$result1['empId']." and  quarter_id=".$result1['quarter_id']."";
	
	  $KRA = $this->db->query($kraData)->result_array(); 
	  
	
	  
	    foreach( $KRA as $kraROw) {
	                    if($kraROw['formula_1'] ==0 and $kraROw['formula_2']==0 )
										{
											$user_weightage+=$kraROw['user_weightage'];
											$manager_weightage+=$kraROw['manager_weightage'];
											
										  }else{
										    if($kraROw['formula_2']==0){
											if($kraROw['weightage']< $kraROw['manager_weightage'])
											{
											$manager_weightage+=$kraROw['weightage'];
											
											}else{
											$manager_weightage+=$kraROw['manager_weightage'];
											
											}
											if($kraROw['weightage']< $kraROw['user_weightage'])
											{
												$user_weightage+=$kraROw['weightage'];
												
												
											}else{
											   $user_weightage+=$kraROw['user_weightage'];
											}
										 
											}
											}
										
		    if($kraROw['formula_2']==1  and ($kraROw['user_weightage']!=0 || $kraROw['user_weightage']!="")){
				
				if($kraROw['manager_weightage']!="")
				{
  
			  $manager_weightage+=$this->reverformula($kraROw['quarter_target'],$kraROw['user_achievement_manager'],$kraROw['weightage'],$kraROw['formula_1']);
				}
			
				if($kraROw['user_weightage']!="")
				{					 
			  
		          $user_weightage+=$this->reverformula($kraROw['quarter_target'],$kraROw['user_achievement'],$kraROw['weightage'],$kraROw['formula_1']);
				}
		
		                 }
	
	//  print_r($KRA);	die;  
	 }
	 
	 $updateEmpKra="update  `tbl_kra_emp` set `emp_tot_weightage`='".$user_weightage."' ,`manager_tot_weightage`='".$manager_weightage."' WHERE   empId=".$result1['empId']." and  quarter_id=".$result1['quarter_id']."";
	
	 //$manager_weightage=0;
	// $user_weightage=0; echo $updateEmpKra;die; 
	 $KRA = $this->db->query($updateEmpKra); 
	// echo $updateEmpKra;die; 
	 
}

}

   function reverformula($quarterTarget,$achievement,$wieghtage,$formula1)
		  {  
			  
			   
	     $achiveValue=sprintf("%0f",((1-($achievement-$quarterTarget)/$quarterTarget))*100);
		 if($formula1==1  and $achiveValue>$wieghtage)
		 {
		 $achiveValue=100;	 
		 }
		 
		  return sprintf("%.2f",($wieghtage*$achiveValue)/100);
			
			   
		
			   
		  }
		  
		  
		  /*End modification 7-nov-2019 */

	function get_kra_attributes_data_for_annual_report($quarter_id,$emp_id)
	{
	    $addsql="";
		//echo $emp_id;die;
		if($emp_id)
		{
		$addsql.="AND empId='".$emp_id."'";
		}
	   $sql="select  *  from tbl_kra_attributes where status=1 AND quarter_id='".$quarter_id."' $addsql ";
		//echo $sql;die;
		$result=$this->db->query($sql)->result_array(); 
		return $result;
	
	}
	
	public function get_kra_emp_data_for_annual_report($quarter_id,$emp_id)
	{
		$this->db->select('*');
		$this->db->from('tbl_kra_emp');
		$this->db->where('empId',$quarter_id);
		$this->db->where('quarter_id',$emp_id);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$result = $query->result_array();
			return $result = $result[0];
		}else{
			return array();
		}

	}

	// Api policy data
	function fetchAllPolicyData(){
		$this->db->select('name,handbookTitle,handbookPath,logoPath');
		$this->db->from('tbl_handbook');
		$this->db->where('isDisplayed',1);		
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$result = $query->result_array();
			return $result;
		} else {
			return array();
		}

	}
	
	function checkLeaveYear1($filters)
	{
		$tableName  =  'tbl_emp_leave_balance';	
		$year       =   date("Y");  
		$filterYear =   $year;
		$objJson = json_decode($filters);               
		
		if(!empty($objJson)){
		
			foreach($objJson->{'rules'} as $rules)
			{				
			   if($rules->{'field'}!="")
			   {			
				if($rules->{'field'} == 'leaveyear')
				{			
				
					if($rules->{'data'} != $year){
						$filterYear = $rules->{'data'};
						//$tableName = 'tbl_emp_leave_balance_'.$rules->{'data'};
					 }	else {
						$filterYear = $year;
						//$tableName = 'tbl_emp_leave_balance';	
					 }					 
				}
			  }	
		   }
		}
        return	$filterYear;	
	 }


	 function stateRolePermissionData($empId){
		  
		$this->db->select("GROUP_CONCAT(stateId SEPARATOR ',') as stateIds",FALSE); 
		$this->db->from('employee_state_wise_permission');
		$this->db->where('empId', $empId);
		$this->db->where('type', 2);
		$query  =  $this->db->get(); 
		$result =  $query->row_array();
		return $result;
	   }

	 function getSerachEmployeeData(){ 
		 $search  =  filter_values($_GET['term']);
		 $this->db->select("e.empId as id,e.empEmailOffice as value, concat(e.empFname,' ',e.empLname) as label,e.empImage",FALSE); 
		 $this->db->from('tbl_emp_master as e');		
		 $this->db->where("e.empEmailOffice LIKE '%$search%'");
		 $this->db->where("e.isActive",1);
		 $query  =  $this->db->get(); 
		 $result =  $query->result_array();
		 return $result;
		 exit;
	 }
}
?>