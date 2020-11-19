<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class form16 extends front_main
	{
			function index($param1=0)
			{
			 $data = array();
             $data['param1'] = $param1;
			$this->load->helper('download');
			parent::pageLoad('form16/index',$data);
		}
		function get_form16_details($param1=0)
		{
		$result = $this->form16_model->get_details('G', $param1);
			echo  json_encode($result);
		}
		function add_edit()
		{
			
			$this->load->view('form16/add_edit');
		
		}
		
	function uploadfile($y=0)
	{
		$new_name = trim($_FILES['file']['name']);
		$expName = explode('_',$new_name);
		$empId =$this->employee_model->getPancarddetails($expName['0']);
		if($empId){
			if($_FILES['file']['name']!='')
			{
			
				$year = $y;
				
				$config['allowed_types'] = 'pdf|PDF';
				$config['max_size'] = '300000';
				$config['overwrite'] = TRUE;
				
				//$new_name = md5(time());
				$new_name = $empId.'_'.md5(time());			
				//////////////////
			
				if (!file_exists(FORM16.$year)) {
					mkdir(FORM16.$year, 0777, true);
					$config['upload_path'] = FORM16.$year;
				}else{
					$config['upload_path'] = FORM16.$year;
				}
				$config['upload_path'];
				$config['file_name'] = $new_name;
				$this->load->library('upload', $config);
				$this->load->library('image_lib', $config);
				$this->upload->initialize($config); 
				if ( ! $this->upload->do_upload('file'))
				{
				$error = array('error' => $this->upload->display_errors());
				print_r($error);
				exit;
				}
				else
				{
				$data_file = $this->upload->data();
				$data['name'] =  $new_name.'.pdf';			
				}

				$data['empId'] = $empId;
				$data['period'] = $year;
				$data['addedBy'] = $this->session->userdata('admin_id');
				$data['isCreated'] = date('Y-m-d H:i:s',time());						
				
				//$this->sendSMS($empId, $month, $year);
				if($data['name']){
					if($this->parent_model->query_insert(TABLE_FORM16, $data));
					{
						//$this->sendAlert($empId, $month, $year);
					}
				}else{
				echo 'upload fail';
				}
			}
			
		}else{
			echo 'Pan Number not Found';}
		}
		
		function sendAlert($empId, $month, $year)
		{
		$monthNames = array(1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June', 7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December');
			$userDetails =  $this->employee_model->getemailmobile($empId);
					
						if($userDetails['empEmailOffice'])
						$email = $userDetails['empEmailOffice'];
						else
						$email = $userDetails['empEmailPersonal'];
					   $datamailData = $this->master_model->getMailcontent();
						$data['empName'] = $userDetails['empName'];
						$data['month'] = $monthNames[$month];
						$data['year'] = $year;
		$subject ='Transedge '.$monthNames[$month].'\' '.$year.' Salary Slip'; 
				$body_txt = $this->master_model->decode_scode($data,html_entity_decode($datamailData['content']));
				
				$full_name = $userDetails['empName'];
				$email_data = array(
				  'SITE_LOGO_URL' => base_url().IMAGE_DIR.'logo.png',
				  'USER' => $full_name,
				  'SITE_NAME' => SITE_NAME,
				  'MAIL_DATA'=>$body_txt
				);
				if(MODE =='live'){
				$to = $email;
				
				}else{
					$to = EMAILTO;
					
				}
				
				$htmlMessage =  $this->parser->parse('emails/alert', $email_data, true);
				if($this->myemail->sendEmail($to,$subject,  $body_txt, ADMIN_EMAIL, ADMIN_NAME)){
				$where=" ".empId." ='".$empId."' and month='".$month."' and year='".$year."'";
				$alertdata['sendMailalertdate']= date('Y-m-d H:i:s',time());
				$alertdata['mailalertsent']=1;
				$alertdata['mail']=$to;
				$data =array();
				$data['mail'] = $to;
				$data['isCreated'] = date('Y-m-d H:i:s',time());
				$data['empId'] = $empId;
				if($this->parent_model->query_update('tbl_emp_salary_slip', $alertdata, $where)){
				$this->parent_model->query_insert('tbl_salary_sliplog', $data);
				echo 'success';
				}else{
				echo 'error';
				}
				}else{
				echo "Try agian ";
				}
				
		}
		
		
		
		function sendAlertmanuallysingle()
		{
		
		$salary_data = array();
		$salary_data = $this->employee_model->get_salaryslip_details($this->input->post('id'));
		$monthNames = array(1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June', 7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December');
			$userDetails =  $this->employee_model->getemailmobile($salary_data['empId']);
					
						if($userDetails['empEmailOffice'])
						$email = $userDetails['empEmailOffice'];
						else
						$email = $userDetails['empEmailPersonal'];
					    $datamailData = $this->master_model->getMailcontent();
						$data['empName'] = $userDetails['empName'];
						$data['month'] = $monthNames[$salary_data['month']];
						$data['year'] = $salary_data['year'];
		$subject ='Transedge '.$monthNames[$salary_data['month']].'\' '.$salary_data['year'].' Salary Slip'; 
				$body_txt = $this->master_model->decode_scode($data,html_entity_decode($datamailData['content']));
				
				$full_name = $userDetails['empName'];
				$email_data = array(
				  'SITE_LOGO_URL' => base_url().IMAGE_DIR.'logo.png',
				  'USER' => $full_name,
				  'SITE_NAME' => SITE_NAME,
				  'MAIL_DATA'=>$body_txt
				);
				if(MODE =='live'){
				$to = $email;
				}else{
					$to = EMAILTO;
				}
				$htmlMessage =  $this->parser->parse('emails/alert', $email_data, true);
				if($this->myemail->sendEmail($to,$subject,  $body_txt, ADMIN_EMAIL, ADMIN_NAME)){
				$where=" ".id." ='".$this->input->post('id')."'";
				$alertdata['sendMailalertdate']= date('Y-m-d H:i:s',time());
				$alertdata['mailalertsent']=1;
				$alertdata['mail']=$to;
				$data =array();
				$data['mail'] = $to;
				$data['isCreated'] = date('Y-m-d H:i:s',time());
				$data['empId'] = $empId;
				if($this->parent_model->query_update('tbl_emp_salary_slip', $alertdata, $where)){
				$this->parent_model->query_insert('tbl_salary_sliplog', $data);
				echo 'success';
				}else{
				echo 'error';
				}
				}else{
				echo "Try agian ";
				}
				
		}
		
		function sendAlertmanually()
		{
		$i=0;
		$data_id = $this->input->post('id');
		foreach($data_id as $data_id){
		
		$salary_data = array();
		$salary_data = $this->employee_model->get_salaryslip_details($data_id);
		//pre($salary_data);die;
		$monthNames = array(1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June', 7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December');
			//$userDetails= array();
			$userDetails =  $this->employee_model->getemailmobile($salary_data['empId']);
					//pre($userDetails);
						if($userDetails['empEmailOffice'])
						$email = $userDetails['empEmailOffice'];
						else
						$email = $userDetails['empEmailPersonal'];
					    $datamailData = $this->master_model->getMailcontent();
						$data['empName'] = $userDetails['empName'];
						$data['month'] = $monthNames[$salary_data['month']];
						$data['year'] = $salary_data['year'];
		$subject ='Transedge '.$monthNames[$salary_data['month']].'\' '.$salary_data['year'].' Salary Slip'; 
				$body_txt = $this->master_model->decode_scode($data,html_entity_decode($datamailData['content']));
				
				$full_name = $userDetails['empName'];
				$email_data = array(
				  'SITE_LOGO_URL' => base_url().IMAGE_DIR.'logo.png',
				  'USER' => $full_name,
				  'SITE_NAME' => SITE_NAME,
				  'MAIL_DATA'=>$body_txt
				);
				if(MODE =='live'){
				$to = $email;
				}else{
					$to = EMAILTO;
				}
				//pre($email_data);
				if($email){
				$htmlMessage =  $this->parser->parse('emails/alert', $email_data, true);
				if($this->myemail->sendEmail($to,$subject,  $body_txt, ADMIN_EMAIL, ADMIN_NAME)){
				$where=" ".id." ='".$data_id."'";
				$alertdata['sendMailalertdate']= date('Y-m-d H:i:s',time());
				$alertdata['mailalertsent']=1;
				$alertdata['mail']=$to;
				$data =array();
				$data['mail'] = $to;
				$data['isCreated'] = date('Y-m-d H:i:s',time());
				$data['empId'] = $userDetails['empId'];
				//pre($alertdata);die;
				if($this->parent_model->query_update('tbl_emp_salary_slip', $alertdata, $where)){
				$this->parent_model->query_insert('tbl_salary_sliplog', $data);
				$i++;
				}else{
				$i=0;
				}
				}else{echo "Try agian ";}
				}
				
				}
				echo $i;
		}
		

	function delete($id)
	{
	$data = $this->form16_model->getdetails($this->input->post('id'));
	if($id){
	$this->parent_model->delete_rec(TABLE_FORM16,'id',$id);
	unlink(FORM16.$data['period'].'/'.$data['name']);
	echo 'success';
	}else{
	echo 'Error Occured';
	}
	}	
	

	function sendSMS($empId, $month, $year){
	$monthNames = array(1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June', 7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December');
	$months = $monthNames[$month];
	$userDetails =  $this->employee_model->getemailmobile($empId);
	$mobile =$userDetails['empMobile'];
	//$mobile ='9711212997';
	
	// Authorisation details.
	$username = "ankit.aggarwal@transedgemarketing.com";
	$hash = "12f2cc68b60ddf9d645480d84e318cd5e2c49201";

	// Config variables. Consult http://api.textlocal.in/docs for more info.
	$test = "0";

	// Data for text message. This is the text message data.
	$sender = "TXTLCL"; // This is who the message appears to be from.
	//$numbers = "919015273301"; // A single number or a comma-seperated list of numbers
	$m = strtoupper(substr($months,0,3));
	$message = "Dear ".$userDetails['empName'].", we have uploaded ".$months."'".$year." salary slip, If you have received please reply WJBCX ".$m."".$year." YES, else reply WJBCX ".$m."".$year." NO to 9220592205, Thanks Transedge";
	// 612 chars or less
	// A single number or a comma-seperated list of numbers
	$message = urlencode($message);
	$data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$mobile."&test=".$test;
	$ch = curl_init('http://api.textlocal.in/send/?');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	echo $result = curl_exec($ch); // This is the result from the API
	curl_close($ch);
	}
}
?>
