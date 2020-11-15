<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class helpdesk extends front_main {
		var $u_column = 'id';
		public function __construct(){
			parent::__construct();
			$this->isLoggedIn();
			$this->load->library('parser');
				   
			
		$this->load->model('helpdesk/helpdesk_master');
		}
		//-----------training-----------//
		
		function helpdesk_master()
		{
			parent::pageLoad('helpdesk/helpdesk_master/index',$data);
		}
		
		function get_team_details()
		{
			//echo "test";die;
			
			$result = $this->helpdesk_master->get_team_details();
			
			echo  json_encode($result);
		}
		
		function add_edit_team()
		{
			$data['u_column'] = 'id';
			$data['edit'] = $this->input->post('edit');
			if($data['edit']!='')
			{
				$result = $this->parent_model->listing(TABLE_BRAND_SUPPORT_TEAM, 'id', $data['edit']);
				$data['result'] = $result[0];
				
			}
			else
			{
				$data['result'] = array();
				
			}
			//pre($data);die;
			$this->load->view('/helpdesk/helpdesk_master/add_edit', $data);
		}
		
		function check_team_name()
		{
		
			$result = $this->helpdesk_master->duplicateTeamName($this->input->post('team_name'),$this->input->post('departId'));			
			if(!empty($result))
			echo 1;
			else
			echo 0;
		}
		
		
		function insert_update_team()
		{
			$data=array();
			$u_column='id';
			$data =$this->input->post();
			$tbl = "tbl_training_master";
			//pre($data);die;
			if($this->input->post('id')!=0)
			{
				$data1['activityType'] ='Update';
				$where="id='".$this->input->post('id')."' ";
				//$data['updatedBy']=$this->session->userdata('admin_id');
				//$data['isUpdated']=date('Y-m-d H:i:s');
				$data['addedBy']=$this->session->userdata('admin_id');
				//pre($data);die;
				if($this->parent_model->query_update(TABLE_BRAND_SUPPORT_TEAM, $data, $where))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			else
			{
				$data1['activityType'] ='Insert';
				$data['addedBy']=$this->session->userdata('admin_id');
				$data['isCreated']=date('Y-m-d H:i:s');
				//pre($data);die;
				if($this->parent_model->query_insert(TABLE_BRAND_SUPPORT_TEAM, $data))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			//insert activity	
			//$data1['activityCategory'] = "Master State";
			//$this->newActivity(TABLE_ACTIVITY,$data1);
			exit;
			//end activity
			
		}
				
		function update_team_status()
		{
			$data=array();
			$u_column='id';
			$data =$this->input->post();
			$data2['status'] = $this->input->post('status');
			
			if($data2['status'] ==1)
			{
				$data2['status'] = 0;
			}
			else 
			{
				$data2['status'] = 1;
			}
			//pre($data2);die;
			if($this->input->post('id')!=0)
			{
				$data1['activityType'] ='Update';
				$where="id='".$this->input->post('id')."' ";
				$data['addedBy']=$this->session->userdata('admin_id');
				
				
				if($this->parent_model->query_update(TABLE_BRAND_SUPPORT_TEAM, $data2, $where))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			
			exit;
			//end activity
		}
		
		// Request Type
		
		function add_edit_request()
		{
			$data['u_column'] = 'id';
			$data['edit'] = $this->input->post('edit');
			//pre($data);die;
			if($data['edit']!='')
			{
				$result = $this->parent_model->listing(TABLE_BRAND_REQUEST_TYPE, 'id', $data['edit']);
				$data['result'] = $result[0];
			}
			else
			{
				$data['result'] = array();
			}
			//pre($data);die;
			$result = $this->helpdesk_master->get_request_details();
			$data['result'] = $result;
            $htmlData="";
			//pre($data);die;			
			$this->load->view('helpdesk/helpdesk_master/add_request',$htmlData);
		}
		
		function edit_request1()
		{
			$data['u_column'] = 'id';
			$data['edit'] = $this->input->post('edit');
			if($data['edit']!='')
			{
				$result = $this->parent_model->listing(TABLE_BRAND_REQUEST_TYPE, 'id', $data['edit']);
				$data['result'] = $result[0];
				
			}
			else
			{
				$data['result'] = array();
				
			}
			//pre($data);die;
			$this->load->view('helpdesk/helpdesk_master/add_request1', $data);
		}

		function get_add_edit_request()
		{
			$result = $this->helpdesk_master->get_request_details();
			$data['result'] = $result;
            $htmlData="";			
				//print_r($result1);die;
				$teamData = array();
					
									//print_r($result);die;			
							if($result) { 
							   foreach($result as $row) {	
								 $htmlData.='<tr>
								    <td>'.$row['team_name'].'</td> 
									<td>'.$row['request_name'].'</td>  
									<td>'.$row['isCreated'].'</td><td>';
								 if($row['status'] == 1 ) {
									$htmlData.='<img src="images/aprove.png" height="18" title="" style="margin-right:10px;">';
								 } else  {
									$htmlData.='<img src="images/disaprove.png" height="15" title="" style="margin-right:10px;">';
									
									 } 
									if($row['status'] == 1 ) { 
									$htmlData.='</td> 
									<td><a href="javascript:void(0);" onClick="update_status_request('.$row[id].','.$row[status].')"><img src="images/active-ico.png" height="15" title="Update Status" style="margin-right:10px;"></a>'; 
									 } else  {
									$htmlData.='</td> 
									<td><a href="javascript:void(0);" onClick="update_status_request('.$row[id].','.$row[status].')"><img src="images/inactive-ico.png" height="15" title="Update Status" style="margin-right:10px;"></a>'; 
									}	 
								$htmlData.='<a href="javascript:void(0);" onClick="edit_request('.$row[id].')"><img src="images/edit-icon.png" height="15" title="Edit" style="margin-right:10px;"></a></td></tr>';
								}
								}
						echo  $htmlData;exit();
							
				//	print_r($htmlData);die;					
								
			//$this->load->view('helpdesk/helpdesk_master/add_request',$htmlData);
		}		
		function update_request_Status()
		{
			$data['u_column'] = 'id';
			$data['id'] = $this->input->post('id');
			$data['status'] = $this->input->post('status');
			//print_r("tt".$data['status']);die();
			if($data['status'] ==1)
			{
				$data['status'] = 0;
			}
			else 
			{
				$data['status'] = 1;
			}
			
			if($data['id']!='')
			{
				$result = $this->parent_model->listing(TABLE_BRAND_REQUEST_TYPE, 'id', $data['edit']);
				$data['result'] = $result[0];
			}
			else
			{
				$data['result'] = array();
				
			}
			//pre($data);die;
			$this->load->view('/helpdesk/helpdesk_master/updateStatus', $data);
		}
		
		function insert_update_request()
		{
			$data=array();
			$u_column='id';
			$data =$this->input->post();
			$data2['status'] = $this->input->post('status');
			
			if($data2['status'] ==1)
			{
				$data2['status'] = 0;
			}
			else 
			{
				$data2['status'] = 1;
			}
			
			if($this->input->post('id')!=0)
			{
				$data1['activityType'] ='Update';
				$where="id='".$this->input->post('id')."' ";
				$data['addedBy']=$this->session->userdata('admin_id');
				//$data2['status']=$this->input->post('Status');
				//$data['status']=$this->input->post('Status');
				
				if($this->parent_model->query_update(TABLE_BRAND_REQUEST_TYPE, $data2, $where))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			else
			{
				$data1['activityType'] ='Insert';
				$data['addedBy']=$this->session->userdata('admin_id');
				$data['isCreated']=date('Y-m-d H:i:s');
				//pre($data);die;
				if($this->parent_model->query_insert(TABLE_BRAND_REQUEST_TYPE, $data))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			exit;
			//end activity
			
		}
		
		
		function update_request()
		{
			$data=array();
			$u_column='id';
			$data =$this->input->post();
			//pre($data);die;
			
			
			if($this->input->post('id')!=0)
			{
				$data1['activityType'] ='Update';
				$where="id='".$this->input->post('id')."' ";
				$data['addedBy']=$this->session->userdata('admin_id');
				//$data2['status']=$this->input->post('Status');
				//$data['status']=$this->input->post('Status');
				
				if($this->parent_model->query_update(TABLE_BRAND_REQUEST_TYPE, $data, $where))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			else
			{
				$data1['activityType'] ='Insert';
				$data['addedBy']=$this->session->userdata('admin_id');
				$data['isCreated']=date('Y-m-d H:i:s');
				//pre($data);die;
				if($this->parent_model->query_insert(TABLE_BRAND_REQUEST_TYPE, $data))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			exit;
			//end activity
			
		}
		
		function delete_request()
		{
			//$where="id='".$this->input->post('id')."' ";
			$where = $this->input->post('id');
			$field = "id";
			$result = $this->parent_model->delete_rec(TABLE_BRAND_REQUEST_TYPE, $field, $where);			
			if(!empty($result))
			echo 1;
			else
			echo 0;
		}
		
		function check_request_name()
		{
		//print_r($this->input->post('request_name'));die;
			$result = $this->helpdesk_master->duplicateRequestName($this->input->post('request_name'),$this->input->post('teamId'));			
			if(!empty($result))
			echo 1;
			else
			echo 0;
		}
		
		function userStatus() 
		{
			//print_r("test");die;
			$data['status'] = $this->input->post('value');
			$this->um->updateStatus($this->input->post('id'),$data);
			echo TRUE;
		}
		
		
		
		
		// Priority
		function helpdesk_priority()
		{
			parent::pageLoad('helpdesk/helpdesk_priority/index',$data);
		}
				
		function get_priority_details()
		{
			$result = $this->helpdesk_master->get_priority_details();
			echo  json_encode($result);
		}
		function add_edit_priority()
		{
			$data['u_column'] = 'id';
			$data['edit'] = $this->input->post('edit');
			if($data['edit']!='')
			{
				$result = $this->parent_model->listing(TABLE_MST_BRAND_PRIORITY, 'id', $data['edit']);
				$data['result'] = $result[0];
				//pre($data);
			}
			else
			{
				$data['result'] = array();
				
			}
			//pre($data);die;
			$this->load->view('/helpdesk/helpdesk_priority/add_edit', $data);
		}
				
		function insert_update_priority()
		{
			$data=array();
			$u_column='id';
			$data =$this->input->post();
			//pre($data);die;
			if($this->input->post('id')!=0)
			{
				$data1['activityType'] ='Update';
				$where="id='".$this->input->post('id')."' ";
				$data['addedBy']=$this->session->userdata('admin_id');
				//pre($data);die;
				if($this->parent_model->query_update(TABLE_MST_BRAND_PRIORITY, $data, $where))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			else
			{
				$data1['activityType'] ='Insert';
				$data['addedBy']=$this->session->userdata('admin_id');
				$data['isCreated']=date('Y-m-d H:i:s');
				//pre($data);die;
				if($this->parent_model->query_insert(TABLE_MST_BRAND_PRIORITY, $data))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			exit;
		}
		
		function check_helpdesk_priority()
		{
			$result = $this->helpdesk_master->duplicatePriorityName($this->input->post('type'));			
			if(!empty($result))
			echo 1;
			else
			echo 0;
		}
		
		function update_priority_Status()
		{
			$data['u_column'] = 'id';
			$data['edit'] = $this->input->post('edit');
			if($data['edit']!='')
			{
				$result = $this->parent_model->listing(TABLE_MST_BRAND_PRIORITY, 'id', $data['edit']);
				$data['result'] = $result[0];
			}
			else
			{
				$data['result'] = array();
				
			}
			//pre($data);die;
			$this->load->view('/helpdesk/helpdesk_priority/updateStatus', $data);
		}
		
		// helpdesk_status
		
		function helpdesk_status()
		{
			parent::pageLoad('helpdesk/helpdesk_status/index',$data);
		}
				
		function get_status_details()
		{
			$result = $this->helpdesk_master->get_status_details();
			echo  json_encode($result);
		}
		
		function add_edit_status()
		{
			$data['u_column'] = 'id';
			$data['edit'] = $this->input->post('edit');
			if($data['edit']!='')
			{
				$result = $this->parent_model->listing(TABLE_MST_BRAND_STATUS, 'id', $data['edit']);
				$data['result'] = $result[0];
				//pre($data);
			}
			else
			{
				$data['result'] = array();
				
			}
			//pre($data);die;
			$this->load->view('/helpdesk/helpdesk_status/add_edit', $data);
		}
				
		function insert_update_status()
		{
			$data=array();
			$u_column='id';
			$data =$this->input->post();
			//pre($data);die;
			if($this->input->post('id')!=0)
			{
				$data1['activityType'] ='Update';
				$where="id='".$this->input->post('id')."' ";
				$data['addedBy']=$this->session->userdata('admin_id');
				//pre($data);die;
				if($this->parent_model->query_update(TABLE_MST_BRAND_STATUS, $data, $where))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			else
			{
				$data1['activityType'] ='Insert';
				$data['addedBy']=$this->session->userdata('admin_id');
				$data['isCreated']=date('Y-m-d H:i:s');
				//pre($data);die;
				if($this->parent_model->query_insert(TABLE_MST_BRAND_STATUS, $data))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			exit;
		}
		
		function check_helpdesk_status()
		{
			$result = $this->helpdesk_master->duplicateStatusName($this->input->post('type'));			
			if(!empty($result))
			echo 1;
			else
			echo 0;
		}
		
		function update_Status()
		{
			$data['u_column'] = 'id';
			$data['edit'] = $this->input->post('edit');
			if($data['edit']!='')
			{
				$result = $this->parent_model->listing(TABLE_MST_BRAND_STATUS, 'id', $data['edit']);
				$data['result'] = $result[0];
			}
			else
			{
				$data['result'] = array();
				
			}
			//pre($data);die;
			$this->load->view('/helpdesk/helpdesk_status/updateStatus', $data);
		}
		
		// Ticket
		function helpdesk_ticket()
		{
			parent::pageLoad('helpdesk/helpdesk_ticket/index',$data);
		}
				
		function get_ticket_details()
		{			
			//$this->mailsupportTeam();
			//die;
			$result = $this->helpdesk_master->get_ticket_details();
			echo  json_encode($result);			
		}
		function add_edit_ticket()
		{
			$data['u_column'] = 'id';
			$data['edit'] = $this->input->post('id');
			//pre($data);die;
			if($data['edit']!='')
			{
				$result = $this->parent_model->listing(TABLE_MST_BRAND_TICKET, 'id', $data['edit']);
				$data['result'] = $result[0];
				//pre($data);
			}
			else
			{
				$data['result'] = array();
			}
			//pre($data);die;
			$this->load->view('/helpdesk/helpdesk_ticket/add_edit', $data);
		}
		
		function insert_update_ticket()
		{
			$data=array();
			$u_column='id';
			$data =$this->input->post();
		
			// attachment upload
			if(isset($_FILES['attach']['name'][0]) && $_FILES['attach']['name'][0] !=''){			
			    $data['attach'] =   $this->uploadMultipleDocument($_FILES, HELPDESK, 'brand', 'attach');
			}


			if($this->input->post('id')!=0)
			{
				$data1['activityType'] ='Update';
				$where="id='".$this->input->post('id')."' ";
				$data['addedBy']=$this->session->userdata('admin_id');
				//pre($data);die;
				if($this->parent_model->query_update(TABLE_MST_BRAND_TICKET, $data, $where))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			else
			{
				$user_name = $this->session->userdata('admin_name');
				$empEmailOffice = $this->session->userdata('admin_email');
				$data1['activityType'] ='Insert';
				$data['addedBy']=$this->session->userdata('admin_id');
				//$data['departId']=$this->session->userdata('role');
				$dataTicket['isCreated']=date('Y-m-d H:i:s');
				$data['isUpdated']=date('Y-m-d H:i:s');
				$dataTicket['isUpdated']=date('Y-m-d H:i:s');
				//$data['ticked_id']=date('Y-m-d');
				//pre($data);die;
				
				$dataTicket['teamId'] = $this->input->post('teamId');
				$dataTicket['requestId'] =$this->input->post('requestId');
				$dataTicket['subject'] =$this->input->post('subject');
				$dataTicket['isCreatedby']=$this->session->userdata('admin_id');
				
				//print_r(TABLE_MST_HELPDESK_TICKET_TRANS);die;
				$result = $this->parent_model->query_insert(TABLE_MST_BRAND_TICKET, $dataTicket);
				if($result)
				{
					$dataTrans['t_ticked_id'] = $result;
					$dataTrans['t_subject'] = $this->input->post('subject');
					//$dataTrans['t_type'] = $this->input->post('requestId');
					//$dataTrans['t_status'] = $this->input->post('status');
					//$dataTrans['t_priority'] = $this->input->post('priority');
					$dataTrans['t_message'] = $this->input->post('message');
					$dataTrans['t_attach'] = $data['attach'];
					$dataTrans['t_isCreated']=date('Y-m-d H:i:s');
					$dataTrans['t_isUpdated']=date('Y-m-d H:i:s');
					$dataTrans['t_addedby']=$this->session->userdata('admin_name');
					//pre($data);die;
					
					$result_trans = $this->parent_model->query_insert(TABLE_MST_BRAND_TICKET_TRANS, $dataTrans);
					$TeamName = $this->helpdesk_master->getTeamName($this->input->post('teamId'));
					$tat_time = $this->helpdesk_master->getTAT($this->input->post('requestId'));
					
					$email_data = array(					
						'message' => $this->input->post('message'),
						'subject' => $this->input->post('subject'),
						'empFromName' => $this->session->userdata('admin_id'),
						'teamname' => $TeamName[0]['team_name'],
						'tickedId' => $result,
						'tat' => $tat_time[0]['tat'],
						'username' => $user_name,
						'SITE_NAME'  =>SITE_NAME,
						'MAIL_DATA'  =>$content
						);
					if(MODE =='live'){
				 	    $to   = $data1['empEmailOffice'];
						//$to   ='krishnakant.singh@arkinfo.in';
					$cc  =   '';
					}else{
						//$to  =  EMAILTO;
						$to  =  '';
						$cc  =  '';
					}		
						
						// $htmlMessage =  $this->parser->parse('emails/helpdesk/ticketEmail', $email_data, true);
						// $htmlMessageUser =  $this->parser->parse('emails/helpdesk/ticketEmailuser', $email_data, true);
						 		
				 
					// $sendmail    =   $this->myemail->sendEmail($to,'Training ARK Infoslutions Private Ltd.', $htmlMessage, '' , 'HR-ARK', $cc);
					
					// $sendmailuser    =   $this->myemail->sendEmail($empEmailOffice,'Training ARK Infoslutions Private Ltd.', $htmlMessageUser, '' , '', '');
					
				echo 1;	
				}
				else
				{
					echo 0;
				}
			}
			exit;
		}
		
		
		
		function insert_reply_ticket()
		{
			
			$data=array();
			$u_column = 'id';
			$attachImage = '';
			$data     =  $this->input->post();
			$message  =  str_replace('<p data-f-id="pbf" style="text-align: center; font-size: 14px; margin-top: 30px; opacity: 0.65; font-family: sans-serif;">Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a></p>','',$this->input->post('message'));

			$data1['activityType'] ='Update';
			$where = "ticked_id='".$this->input->post('id')."' ";
			$data['addedBy']     =  $this->session->userdata('admin_id');
			$data2['isUpdated']  =  date('Y-m-d H:i:s');
			$data2['isupstatus'] =  1;
			//pre($data);die; 
			$upstatusReply = $this->parent_model->query_update(TABLE_MST_BRAND_TICKET, $data2, $where);
			
			$attachDoc = [];
			if(isset($_FILES['attach']['name'][0]) && $_FILES['attach']['name'][0] !=''){			
				$data['attach'] =   $this->uploadMultipleDocument($_FILES, HELPDESK, 'helpdeskId', 'attach');
				$attachDoc   = 	explode(',',$data['attach']);			
			}
		 
				$user_name = $this->session->userdata('admin_name');
			
				$empEmailOffice = $this->session->userdata('admin_email');
				$data1['activityType'] ='Insert';
				$data['addedBy']=$this->session->userdata('admin_id');
			
					$dataTrans['t_ticked_id'] = $this->input->post('id');
					$dataTrans['t_subject']   = $this->input->post('subject');
					$dataTrans['t_message']   = $message;
					$dataTrans['t_attach']    = $data['attach'];
					$dataTrans['t_cc']        = $data['cc'];
					//$dataTrans['t_bcc']       = $data['bcc'];
					$dataTrans['t_isCreated'] = date('Y-m-d H:i:s');
					$dataTrans['t_isUpdated'] = date('Y-m-d H:i:s');
					$dataTrans['t_addedby']   = $this->session->userdata('admin_name');
					$dataTrans['t_fromEmpId'] = $this->session->userdata('admin_id');
					$dataTrans['t_fromEmail'] = $this->session->userdata('admin_email');
					$dataTrans['t_toEmail']   = $data['to'];
					
					if(isset($data['fwd']) && $data['fwd'] =='fwd'){
					  $dataTrans['t_mailType']  =  1;
					  $dataTrans['t_fwdTo']     = $data['to'];				  			
					} else {
					  $dataTrans['t_mailType']       =  2;
					}
					// count transaction records.
			        $tickedSummary = $this->helpdesk_master->gettickedSummary($data['id']);
					if(count($tickedSummary) == 1){
						     $where = "ticked_id='".$data['id']."' "; 
							 $dataRec = array('handleBy'=>$this->session->userdata('admin_id'));
						     $this->parent_model->query_update(TABLE_MST_BRAND_TICKET, $dataRec, $where); 
					}
					
					$result_trans      =  $this->parent_model->query_insert(TABLE_MST_BRAND_TICKET_TRANS, $dataTrans);
					
					$hidAttachArray = [];				
					if(isset($data['fwd']) && $data['fwd'] =='fwd'){
						$hidAttachList = $data['hiddenAttachImage'];
						if($hidAttachList != ''){
							$hidAttachArray  =  explode(',',$hidAttachList);							
						}                
						$toName = ''; 
						$TeamName = '';
					} else {
					   $brandPartnerData  =  $this->helpdesk_master->checkBrandPartnerEmailCheck($data['to']);
					   $toName =  isset($brandPartnerData['partner_name'])? $brandPartnerData['partner_name']:'';
					   $TeamName = $this->helpdesk_master->getTeamName($this->input->post('teamId'));
					}
					
					
					// merge post attachment and already save attachment.
					if( count($hidAttachArray) > 0 && count($attachDoc) > 0){					
						$docList    =  array_merge($hidAttachArray,$attachDoc);
					} else if( count($hidAttachArray) > 0 && count($attachDoc) == 0 ){
											
						$docList    = $hidAttachArray;
					} else if(count($hidAttachArray) == 0 && count($attachDoc) > 0 ){
					    $docList    = $attachDoc;
					} else {
						$docList    =  '';
					}
					
					// concat path to all value of array
					array_walk($docList, function(&$value, $key) { $value = './uploads/helpdesk/'.$value; } );  
					
					if($result_trans)
					{
				
					 $email_data = array(					
						'message' => $message,
						'subject' => $this->input->post('subject'),
						'fromName'  => $this->session->userdata('admin_id'),
						'fromEmail' => $this->session->userdata('admin_email'),
						'teamname' => $TeamName[0]['team_name'],					
						'attachement'=> $docList,
						'toName' => $toName,
						'SITE_NAME'  =>SITE_NAME,
						//'MAIL_DATA'  =>$content
						);
				 
					if(MODE =='live'){
				 	    $to   =  $data['to'];						
					    $cc   =  $dataTrans['t_cc'] ;
					}else {
						//$to  =  EMAILTO;
						$to  =  '';
						$cc  =  '';
					}	
				
					$htmlMessageUser =   $this->parser->parse('emails/helpdesk/reply_email', $email_data, true);
			
				    $sendmailuser    =   $this->myemail->sendCommonEmail($to,$email_data['subject'],$htmlMessageUser, $email_data['fromEmail'], $email_data['fromName'],$cc, $email_data['attachement']);
					
					  $html = '<div class="card ticket_summary-box-right">
							    <div class="card-body">
									<div class="row">
									<div class="col-md-6">
										<p><strong> Date : </strong>'.date('Y-m-d h:i A',strtotime($dataTrans['t_isCreated'])).'</p>
									</div>
									<div class="col-md-6">
										<p><strong> Name : </strong>'.$user_name.'</p>
								   </div>
								   <div class="col-md-12">
               							  <p><strong> From :</strong> '.$empEmailOffice.'</p>
              					   </div>
								   <div class="col-md-12">
										<p><strong> To : </strong>'.$data['to'] .'</p>
					  		       </div>';
					 if($data['cc'] != ''){
						$html  .= '<div class="col-md-12">
										<p><strong> Cc : </strong>'.$data['cc'].'</p>
								   </div>';
					  }				
					 if(isset($docList) && $docList!=''){
					
						$html .= '<div class="col-md-6">
									<p><strong> Attachment : </strong>';
						
						foreach($docList as $attachData){ 	
							$checkExt = substr($attachData, strrpos($attachData, '.') + 1); 				
							$html .=  '<div class="doc-dwonload">';
							if($checkExt == 'jpg' || $checkExt == 'png' || $checkExt == 'jpeg' || $checkExt == 'gif' ){ 
								$html .=  '<a href="javascript:void(0);" onclick="viewAttachment(\''.$attachData.'\')" ><img src="'.$attachData.'" /></a>';
							} else { 
								$html .= '<a href="javascript:void(0);" onclick="viewAttachment(\''.$attachData.'\')"><img src="'.base_url().'ark_assets/images/doc-dwnld.png"></a>';
							}
							
							$html .= '</div>';					
						}
						
						$html .= '</div>';	
				   }			
					 $html .= '<div class="col-md-12">
										<label> <strong>Message</strong></label>
										<p> '.$message.'</p>
									   </div>
									 </div>
								  </div>
								</div>';
					
                      echo json_encode(array('respCode'=>200,'htmlData'=>$html));				  
				      exit;
				}
				else
				{
					 echo json_encode(array('respCode'=>201,'htmlData'=>$html));				  
				     exit;
				}
			
			exit;
		}
		
		
		function mailsupportTeam()
		{
			$teamId = 1;
			//print_r("sdfsd");die;
			$result = $this->helpdesk_master->geEmailSuppTeam($teamId);
			
		}
		
		function uploadDocument($folder, $cid, $fieldname)
		{
			$config['allowed_types'] = 'pdf|jpg|gif|png|PDF|doc|docx|DOCX|DOC|xlsx|xls';
			$config['upload_path'] = $folder;
			$config['max_size'] = '3000000';
			$config['overwrite'] = TRUE;
			$new_name = $fieldname."_".$cid."_".md5(time());
			$config['file_name'] = $new_name;
			$this->load->library('upload', $config);
			$this->load->library('image_lib', $config);
			$this->upload->initialize($config); 
			if ( ! $this->upload->do_upload($fieldname))
			{
				$error = array('error' => $this->upload->display_errors());
				print_r($error);
				exit;
			}
			else
			{
				$data_file = $this->upload->data();
				$data[$fieldname] = $data_file['file_name'];				
			}
			
			  return $data[$fieldname];
		}
		
        function uploadMultipleDocument($fileArr , $folder, $cid, $fieldname)
		{
			$fileArray = [];
			$filesCount = count($fileArr[$fieldname]['name']);
			
            for($i = 0; $i < $filesCount; $i++){
					$_FILES['file']['name']     = $fileArr[$fieldname]['name'][$i];
					$_FILES['file']['type']     = $fileArr[$fieldname]['type'][$i];
					$_FILES['file']['tmp_name'] = $fileArr[$fieldname]['tmp_name'][$i];
					$_FILES['file']['error']    = $fileArr[$fieldname]['error'][$i];
					$_FILES['file']['size']     = $fileArr[$fieldname]['size'][$i];
					
					$fileName = $_FILES['file']['name'];				   				
					$fileName = substr($fileName, 0, strpos($fileName, '.'));
					$fileName = preg_replace('/[^a-zA-Z0-9_.]/', '', $fileName);

					$config['allowed_types'] = 'pdf|jpg|gif|png|jpeg|PNG|JPEG|PDF|PPT|doc|docx|DOCX|DOC|xlsx|xls';
					$config['upload_path'] = $folder;
					$config['max_size'] = '3000000';
					$config['overwrite'] = TRUE;
					$new_name = $fileName.$i.time();
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
						$data_file      = $this->upload->data();
						$fileArray[$i]  = $data_file['file_name'];										
					}
			   } 
			 
			if(!empty($fileArray)){
				$attachList  = implode(',',$fileArray);
				return $attachList;
			} else {
                return '';
			}
			
		}


		function helpdesk_query()
		{
			parent::pageLoad('helpdesk/helpdesk_query/index',$data);
		}
		
		function get_query_details()
		{
			//echo "test";die;
			$result = $this->helpdesk_master->get_query_details();
			echo  json_encode($result);
		}
		
		function add_edit_query()
		{
			$data['u_column'] = 'id';
			$data['edit'] = $this->input->post('edit');
			if($data['edit']!='')
			{
				$result = $this->parent_model->listing(TABLE_BRAND_SUPPORT_TEAM, 'id', $data['edit']);
				$data['result'] = $result[0];
				//pre($data);
			}
			else
			{
				$data['result'] = array();
				
			}
			//pre($data);die;
			$this->load->view('/helpdesk/helpdesk_master/add_edit', $data);
		}
		
		
		function add_edit_team1()
		{
			$id =$this->input->post('edit');
			$departId =$this->input->post('departId');
			//print_r($departId);die;
			$result = $this->helpdesk_master->getEmplistuser($departId);
		
			$data['result'] = $result;
			$data['id']=$id;
			$data['departId']=$departId;
			$this->load->view('helpdesk/helpdesk_master/add_map_team', $data);
		}
		
		function get_emplist($id)
		{
				
				$final = '';
				
				$result = $this->helpdesk_master->getEmplistuser($id);
				$data['result'] = $result;
				
				
				//$final .= '<option value="">------------ Select User ------------ </option>';
				$final .= '<label class="control-label" for="name">Employee List</label><br>';
				if($id >0)
				{	
			
				foreach ($result as $userData) {
					$empId = $userData['empId'];
					$empFname = $userData['empFname'];
					$empMname = $userData['empMname'];
					$empLname = $userData['empLname'];
					
				$final .= '<input type="checkbox" name="chk[]" id="chk" value="'.$empId.'" class="checkbox" >&nbsp;&nbsp;&nbsp;'.$empFname.'';
					$final .='&nbsp;&nbsp;&nbsp;<br>';
				}
					echo $final;	
				}
				else
				{
					
					$final ='';	
					echo $final;	
				}
		}
		
		
		function check_team_map()
		{
		
			$result = $this->helpdesk_master->duplicateTeamMap($this->input->post('teamId'));			
			if(!empty($result))
			echo 1;
			else
			echo 0;
		}
		
		
		
		function insert_update_map()
		{
			$data=array();
			$u_column='id';
			$data =$this->input->post();
			$tbl = "helpdesk_map_team";
			//pre($data);die;
			
			if($this->input->post('id')!=0)
			{
				$data1['activityType'] ='Update';
				$where="id='".$this->input->post('id')."' ";
				$data['addedBy']=$this->session->userdata('admin_id');
				
				
				if($this->parent_model->query_update($tbl, $data, $where))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			else
			{
				$data1['activityType'] ='Insert';
				$data['addedBy']=$this->session->userdata('admin_id');
				$data['isCreated']=date('Y-m-d H:i:s');
				$arr['empId'] =$this->input->post('chk');
				$data2['teamId'] = $this->input->post('teamId');
				$data2['isCreated']=date('Y-m-d H:i:s');
				$data2['empId'] = implode(",",$chk);
				$data2['addedBy']=$this->session->userdata('admin_id');
				
				for($i = 0;$i < count($arr['empId']); $i++)
				$batch[] = array("empId" =>$arr["empId"][$i],
									"teamId" => $data2['teamId'],
									"addedBy" => $data2['addedBy'],
								"isCreated" =>$data2['isCreated']);	
				
				
				
				//pre($arr);die;
				
				$sql = array(); 
				for($i = 0;$i < count($arr['empId']); $i++)
				{
					$sql[] = '("'.$arr["empId"][$i].'", '.$data2['teamId'].', '.$data2['addedBy'].', "'.$data2['isCreated'].'")';
				}
				$this->db->query('INSERT INTO brand_map_team (empId, teamId ,addedBy ,isCreated) VALUES '.implode(',', $sql));
				$result = $this->db->insert_id();
				if($result)
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			exit;
			//end activity
			
		}
		
		
		function update_teammap_status()
		{
			$data=array();
			$u_column='id';
			$data =$this->input->post();
			$data2['status'] = $this->input->post('status');
			
			if($data2['status'] ==1)
			{
				$data2['status'] = 0;
			}
			else 
			{
				$data2['status'] = 1;
			}
			//pre($data);die;
			if($this->input->post('id')!=0)
			{
				$data1['activityType'] ='Update';
				$where="id='".$this->input->post('id')."' ";
				$data['addedBy']=$this->session->userdata('admin_id');
				//$data2['status']=$this->input->post('Status');
				//$data['status']=$this->input->post('Status');
				//pre($data);die;
				if($this->parent_model->query_update(brand_map_team, $data2, $where))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			
			exit;
			//end activity
			
		}
		
		function get_requestList($id)
		{
				
				$final = '';
				$result = $this->helpdesk_master->getRequestlist($id);
				$data['result'] = $result;
				$final = '';
				$final .= '<option value="">------------ Select Request ------------</option>';
				foreach ($result as $userData) {
					$requestId = $userData['id'];
					$requestName = $userData['request_name'];
					$final .= '<option value="'.$requestId.'">'.$requestName.'</option>';
				}
				echo $final;
				
				
		}
		
		function viewticked()
		{
			//error_reporting(E_ALL);
			$id =$this->input->post('id');
			//print_r($id);die;
			$empEmailOffice = $this->session->userdata('admin_email');
			//$data['email'] = $empEmailOffice;
			$result = $this->helpdesk_master->getviewticket($id);
			$data['result'] = $result[0];
			
			//	print_r($data);die;
			$tickedSummary = $this->helpdesk_master->gettickedSummary($id);
			$data['tickedSummary'] = $tickedSummary;
			$lastmessage = $this->helpdesk_master->gettickedLastSummary($id);
			$data['lastmessage'] = $lastmessage;
  			
			//print_r($data);die;
			$this->load->view('helpdesk/helpdesk_ticket/view-ticket',$data);
			//parent::pageLoad('helpdesk/helpdesk_ticket/view-ticket',$data);	
		}
		
		function reply($id)
		{
				//echo "test";die;
				$result = $this->helpdesk_master->getviewticket($id);
				$data['result'] = $result[0];
				$tickedSummary = $this->helpdesk_master->gettickedSummary($id);
				$data['tickedSummary'] = $tickedSummary;
				parent::pageLoad('helpdesk/helpdesk_ticket/reply_ticket',$data);	
		}
		
		function change_status()
		{
			$data=array();
			$u_column='id';
			$data =$this->input->post();
			//pre($data);die;
			if($this->input->post('id')!=0)
			{
				$data1['activityType'] ='Update';
				$where="ticked_id='".$this->input->post('id')."' ";
				$data['addedBy']=$this->session->userdata('admin_id');
				$data2['status'] = $this->input->post('status');
				
				if($this->parent_model->query_update('brand_ticked_records', $data2, $where))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			
			exit;
			
		}
		
	  public function search()
		{
			
			if (isset($_GET['term'])) {
				$keyword = strtolower($_GET['term']);
				$products = $this->helpdesk_master->search($keyword, 'both');
				if (count($products) > 0) {
					$names = array();
					foreach ($products as $product) {
						array_push($names, $product->empEmailOffice);
					}
					echo json_encode($names);
				}
			}
		}
		
    function add_brand_partner(){
		$data['u_column'] = 'id';
		$data['edit']     = $this->input->post('edit');
		$data['brand_id'] = $this->input->post('edit');
		if($data['edit']!='')
		{
			$result = $this->helpdesk_master->fetch_brand_list($data['edit']);
			$data['result'] = $result;			
		}
		else
		{
			$data['result'] = array();
			
		}
		$this->load->view('/helpdesk/helpdesk_master/add_brand_partner', $data);
		  
	}
	
	
	function add_edit_brand_partner()
		{
			$data=array();
			$u_column='id';
			$data =$this->input->post();
	     	
			if($this->input->post('id')!=0)
			{
				$data1['activityType'] ='Update';
				$where="id='".$this->input->post('id')."' ";
				$data['addedBy']=$this->session->userdata('admin_id');
			
				if($this->parent_model->query_update(TABLE_MST_BRAND_STATUS, $data, $where))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			else
			{
				$data1['activityType'] ='Insert';
				$data['addedBy']=$this->session->userdata('admin_id');
				$data['isCreated']=date('Y-m-d H:i:s');
				//pre($data);die;
				if($this->parent_model->query_insert(TABLE_MST_BRAND_STATUS, $data))
				{
					echo 1;
				}
				else
				{
					echo 0;
				}
			}
			exit;
		}


        public function save_update_partner_details(){
			  
			$data =$this->input->post();
			
		    $bdata['partner_email']  = $data['partnerEmail']; 
			$bdata['partner_name']   = $data['partnerName']; 			 
			$bdata['status']         = 1;
			if(isset($data['bpid']) && $data['bpid']!=''){				  
				 $this->db->where('id', $data['bpid']);
				 $this->db->update('brand_partner',$bdata);				 
			} else {
				 $bdata['brand_id']   = $data['brandId']; 			   
				 $this->parent_model->query_insert('brand_partner', $bdata);
				 $insert_id = $this->db->insert_id();                  
			}
			
			if(isset($data['bpid']) && $data['bpid']!=''){				
				echo json_encode(array('respCode'=>200,'status'=>2,'msg'=>'Update successfully','id'=>''));
				exit;
			} else {
				echo json_encode(array('respCode'=>200,'status'=>1,'msg'=>'Insert successfully','id'=>$insert_id));
				exit;
			}
		}

	  
		public function delete_brand_partner_details(){
			$data = $this->input->post();
			  
			$bdata['status']  = 0; 
			$this->db->where('id', $data['bid']);
			$this->db->update('brand_partner',$bdata);	

			echo json_encode(array('respCode'=>200,'msg'=>'deleted successfully'));
			exit;
		}


	   public function email_reader(){
		        error_reporting(E_ALL);	
                $mailRecData   = [];
                $mailTransData = [];				
				$this->load->library('some_class');	
				$result = 	$this->some_class->inbox();
				echo '<pre>';
				print_r($result);
				exit;
				
				if(!empty($result)){
					$mailRecData['subject'] = '';
					$mailRecData['isCreated'] = '';
					$mailRecData['isCreatedby'] = '';
					
			       foreach($result as $mailData){
					
	
					      $mailRecData['subject']   = isset($mailData['header']->subject)?$mailData['header']->subject:'';				          
						  $mailRecData['isCreated'] = isset($mailData['header']->MailDate)? date("Y-m-d h:i:s", strtotime($mailData['header']->MailDate)):'';
						 
						  $this->db->insert('helpdesk_ticked_records', $mailRecData);
                          
						  $insert_id = $this->db->insert_id();
						  
						  $mailTransData['t_ticked_id'] =   $insert_id;
						  $mailTransData['t_status']    =  1;
						  $mailTransData['t_message']   =  isset($mailData['body'])?$mailData['body']:'';						 
						  $mailTransData['t_isCreated'] =  isset($mailData['header']->MailDate)? date("Y-m-d h:i:s", strtotime($mailData['header']->MailDate)):'';
						  $mailTransData['t_addedby']   =  0;
						  $mailTransData['t_subject']   =  isset($mailData['header']->subject)?$mailData['header']->subject:'';	
						  
						  $this->db->insert('helpdesk_transaction_tkrecords', $mailTransData);
                          echo $this->db->last_query();						  
                         $mailRecData  = [];
                         $mailTransData = [];         						 
				   }			   
		        }			
		}
		
		
	public function emailRead(){
	
			//error_reporting(e_all);
			//set_time_limit(800);
		
			//ini_set('max_execution_time', 0);
			$mailbox   = '{outlook.office365.com:143/tls}';
			$username  = 'teamops@arkinfo.in';
			$password  = 'Noida@123';
			$imapResource = imap_open($mailbox, $username, $password);
			if($imapResource === false){
				throw new Exception(imap_last_error());
			}
			
			//$search = 'TO "mbxsupport@arkinfo.in"';
			$date   = date( "d F Y", strtotime("-1 day"));
			$search = 'ALL SINCE "'.$date.'"';
			$emails = imap_search($imapResource, $search);
		   

			if(!empty($emails)){
					//rsort($emails);
					$attachmentFile = '';
				//Loop through the emails.
				foreach($emails as $email){
				 $maxMailNo  =  $this->helpdesk_master->getMaxMsgNo();
					//Fetch an overview of the email.
					$uid = imap_uid($imapResource,$email);
				if($uid >  $maxMailNo) {
					$overview = imap_fetch_overview($imapResource, $email);
					$header = imap_headerinfo($imapResource, $email);
					$overview = $overview[0];
				  
				  // set all to email ids
				   $toaddr   = '';
				   $fromaddr = '';
				   $ccaddr   = '';
				   if(!empty($header->to)){
                     foreach($header->to as $toData)
					    $toaddr .= $toData->mailbox . "@" . $toData->host.',';
				    } 
				  
				    if(!empty($header->from)){
					 foreach($header->from as $fromData)
					   $fromaddr .= $fromData->mailbox . "@" . $fromData->host.',';
				    } 

				    if(!empty($header->cc)){
					  foreach($header->cc as $ccData)
					     $ccaddr .= $ccData->mailbox . "@" . $ccData->host.',';
					} 
				
				    //$toaddr = $header->to[0]->mailbox . "@" . $header->to[0]->host;
					//$fromaddr = $header->from[0]->mailbox . "@" . $header->from[0]->host;// print_r($header);
					
					$toaddr   = rtrim($toaddr, ',');
					$fromaddr = rtrim($fromaddr, ',');
					$ccaddr   = rtrim($ccaddr, ',');
					
					// fetch employee data
					//$empData = $this->helpdesk_master->getEmployeeId($fromaddr);
					//$empId = isset($empData['empId'])? $empData['empId'] : '';
					
					 //fetch team data
                    // $teamData = $this->helpdesk_master->getTeamData($toaddr);
					// $teamId   = isset($teamData['id'])? $teamData['id'] : 0;
				    
					// check mail exist or not
			    
					$mailCheck = $this->helpdesk_master->checkBrandPartnerEmailCheck($fromaddr);
				
					$teamId    = isset($mailCheck['brand_id'])? $mailCheck['brand_id'] : '';
		
					if($teamId !=''){
						// fetch brand assign employee
						$brandTeamList  = $this->helpdesk_master->fetch_brand_assign_employee($teamId);
						
						$structure = imap_fetchstructure($imapResource, $email);
						
						/*
						$bodyText = imap_fetchbody($imapResource,$email,1.2);
						if(!strlen($bodyText)>0){
							$bodyText = imap_fetchbody($imapResource,$email,1);
						} 
						$message = $bodyText;
						*/

	                    $message = $this->getmsg($imapResource,$email);
					    $message = $message;
					   
						$date = date('Y-m-d H:i:s',strtotime($overview->date));
						
						$mailSubject      = trim(str_ireplace("RE: ","",$overview->subject));
						$mailSubject      = trim(str_ireplace("RECALL: ","",$mailSubject));
						$mailSubject      = trim(str_ireplace("FW: ","",$mailSubject));
						$mailSubject      = trim(str_ireplace("FWD: ","",$mailSubject));
						
						$checkEmailData  = $this->helpdesk_master->checkTicketSubjectExist($mailSubject);
							
						if(!empty($checkEmailData)){
							$checkMasterSave = 0; 
							$checkTransSave  = 1;
						} else {
							$checkMasterSave = 1; 
							$checkTransSave  = 1;
						} 
						
						if($checkMasterSave == 1){
							
							if(!empty($mailCheck)){
								$ticketMaster['fromEmailId']        =  $fromaddr;
								$ticketMaster['isCreatedby']        =  $mailCheck['partner_name'];
								$ticketMaster['from_email_status']  =  1; 							 
							}	else {
								$ticketMaster['fromEmailId']  =  $fromaddr;
								$ticketMaster['isCreatedby']  =  $fromaddr;
								$ticketMaster['from_email_status'] =  0; 							  
							}							  
							 
							$ticketMaster['subject']      =  $mailSubject;
							$ticketMaster['requestId']    =  0;                    				  
							$ticketMaster['udate']        =  $header->udate;
							$ticketMaster['mailUniqueNo'] =  $uid;
							$ticketMaster['teamId']       =  $teamId;
							$ticketMaster['priorityId']   =  '';
							$ticketMaster['isCreated']    =  $date;							
							
							
							$this->db->insert('brand_ticked_records',$ticketMaster);
							
							// fetch last insert id of helpdesk ticket records
							$ticket_id  = $this->db->insert_id();
							$udata      = array('subject' => $mailSubject.' #'.$ticket_id);
							$this->db->where('ticked_id', $ticket_id);
							$this->db->update('brand_ticked_records',$udata);
						}	

						
						$attachments = array('',$ticketMaster);
						
						if(isset($structure->parts) && count($structure->parts))
						{
						for($i = 0; $i < count($structure->parts); $i++)
						{
						$attachments[$i] = array(
						'is_attachment' => false,
						'filename' => '',
						'name' => '',
						'attachment' => ''
						);
						
						if($structure->parts[$i]->ifdparameters)
						{
						foreach($structure->parts[$i]->dparameters as $object)
						{
						if(strtolower($object->attribute) == 'filename')
						{
						$attachments[$i]['is_attachment'] = true;
						$attachments[$i]['filename'] = $object->value;
						}
						}
						}
						
						if($structure->parts[$i]->ifparameters)
						{
						foreach($structure->parts[$i]->parameters as $object)
						{
						if(strtolower($object->attribute) == 'name')
						{
						$attachments[$i]['is_attachment'] = true;
						$attachments[$i]['name'] = $object->value;
						}
						}
						}
						
						if($attachments[$i]['is_attachment'])
						{
						$attachments[$i]['attachment'] = imap_fetchbody($imapResource, $email, $i+1);
						
						// 3 = BASE64 encoding 
						if($structure->parts[$i]->encoding == 3)
						{
						$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
						}
						// 4 = QUOTED-PRINTABLE encoding 
						elseif($structure->parts[$i]->encoding == 4)
						{
						$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
						}
						}
						}
						}
						
						$attachmentFile = '';
						foreach($attachments as $attachment)
						{
							
						if($attachment['is_attachment'] == 1)
						{
						$filename = time().$attachment['name'];
						if(empty($filename)) $filename = $attachment['filename'];
						$file_path = 'uploads/helpdesk/'; //  Upload folder
						$fp = fopen($file_path.$filename, "w+");
						fwrite($fp, $attachment['attachment']);
						$attachmentFile .= $filename.',';									
	
						fclose($fp);
						}
						}
							
						$checkEmailDateData = array();	
						if($checkTransSave == 1){
							
						if(!empty($checkEmailData)){						  
							//$checkEmailDateData  = $this->helpdesk_master->checkEmailDateExist($mailSubject,$fromaddr,$date);
							$checkEmailDateData  = $this->helpdesk_master->checkTicketSubjectExist($mailSubject);
						}						 
						
						if(!empty($checkEmailDateData)){
							if(!empty($mailCheck)){
								$ticketTransData['t_addedby']  =  $mailCheck['partner_name'];			 
								} else {
								$ticketTransData['t_addedby']  =  $fromaddr;
								}								
								
								$ticketMid   =    isset($checkEmailData['ticked_id'])? $checkEmailData['ticked_id'] : $ticket_id;
								$ticketTransData['t_ticked_id']  =  isset($checkEmailData['ticked_id'])? $checkEmailData['ticked_id'] : $ticket_id;
								$ticketTransData['t_status']     =  1;
								$ticketTransData['t_message']    =  addslashes($message);
								$ticketTransData['t_attach']     =  rtrim($attachmentFile, ',');
								$ticketTransData['t_toEmail']    =  $toaddr;
								$ticketTransData['t_fromEmail']  =  $fromaddr;
								$ticketTransData['t_cc']         =  $ccaddr;
								$ticketTransData['t_isCreated']  =  $date;
								$ticketTransData['t_isUpdated']  = 	$date;					
								$ticketTransData['t_assign']     =  0;
								$ticketTransData['t_subject']    =  $mailSubject.$ticketMid;							
								
								$this->db->insert('brand_transaction_tkrecords',$ticketTransData);

								$this->db->where('ticked_id', $ticketMid);
								$this->db->update('brand_ticked_records', array('status' => 2));

						} else {
							
								if(!empty($mailCheck)){
								$ticketTransData['t_addedby']  =  $mailCheck['partner_name'];			 
								} else {
								$ticketTransData['t_addedby']  =  $fromaddr;
								}								
								
								$ticketMid   =    isset($checkEmailData['ticked_id'])? $checkEmailData['ticked_id'] : $ticket_id;
								$ticketTransData['t_ticked_id']  =  isset($checkEmailData['ticked_id'])? $checkEmailData['ticked_id'] : $ticket_id;
								$ticketTransData['t_status']     =  1;
								$ticketTransData['t_message']    =  addslashes($message);
								$ticketTransData['t_attach']     =  rtrim($attachmentFile, ',');
								$ticketTransData['t_toEmail']    =  $toaddr;
								$ticketTransData['t_fromEmail']  =  $fromaddr;
								$ticketTransData['t_cc']         =  $ccaddr;
								$ticketTransData['t_isCreated']  =  $date;
								$ticketTransData['t_isUpdated']  = 	$date;					
								$ticketTransData['t_assign']     =  0;
								$ticketTransData['t_subject']    =  $mailSubject.$ticketMid;							
								
								$this->db->insert('brand_transaction_tkrecords',$ticketTransData);
								
							// Send mail to assign employees of brand
						     if(!empty($brandTeamList)){
								
								    foreach($brandTeamList as $teamData){
										$maildata = array(
											'SITE_LOGO_URL'  =>    base_url().SITE_IMAGEURL.'logo.png',
											'empToName'      =>    $teamData['empFname'],
											'empFromName'    =>    $mailCheck['partner_name'],
											'empToEmail'     =>    $teamData['empEmailOffice'],
											'empFromEmail'   =>    $fromaddr,
											'mailContent'	 =>    addslashes($message),						
											'SITE_NAME'      =>    SITE_NAME							    
										);							  
									
										 $templatePath  = 'emails/helpdesk/emailToBrandEmp';
										 $subject	    =  str_replace("RE: ","",$overview->subject);

										if(MODE =='live'){
											$to  = $maildata['empToEmail'];
											$cc  = '';
									    } else {
											$to  =  EMAILTO;
											$cc  =  '';
									    }
										
										$attachment         = '';
										$attachmentList     = [];
										$attachmentArray    = [];
                                        if($attachmentFile !=''){
										   $value = '';
										   $attachment       =   rtrim($attachmentFile, ',');
										   $attachmentArray  =   explode(',',$attachment);										  
										   $attachmentList   =   array_map(function($value) { return UPLOADS.'helpdesk/'.$value; }, $attachmentArray);
										}										
										
									    $fromEmail    =   isset($maildata['empFromEmail'])?$maildata['empFromEmail']:'';
									    $fromName     =   isset($maildata['empFromName'])?$maildata['empFromName']:'';				
									    $htmlMessage  =   $this->parser->parse($templatePath,$maildata, true);				
										
									    $mailStatus   =   $this->myemail->sendCommonEmail($to,$subject, $htmlMessage, $fromEmail, $fromName, $cc, $attachmentList);	
								            $this->email->clear(TRUE);   
									    $attachmentList = [];

									}

							   } 
						   
						  }						
						}
						
						$attachmentFile = '';
				   }
				}   
			}	
		}		
		header('Location: '.base_url().'helpdesk/helpdesk_ticket');
	} 



function getmsg($mbox,$mid) {

	// input $mbox = IMAP stream, $mid = message id
    // output all the following:
    global $charset,$htmlmsg,$plainmsg,$attachments;
    $htmlmsg = $plainmsg = $charset = '';
    $attachments = array();

    // HEADER
    $h = imap_header($mbox,$mid);
    // add code here to get date, from, to, cc, subject...

    // BODY
    $s = imap_fetchstructure($mbox,$mid);
    if (!$s->parts)  // simple
	$this->getpart($mbox,$mid,$s,0);  // pass 0 as part-number
    else {  // multipart: cycle through each part
        foreach ($s->parts as $partno0=>$p)
		$this->getpart($mbox,$mid,$p,$partno0+1);
	}

	return $htmlmsg;
	
}

function getpart($mbox,$mid,$p,$partno) {
    // $partno = '1', '2', '2.1', '2.1.3', etc for multipart, 0 if simple
    global $htmlmsg,$plainmsg,$charset,$attachments;

    // DECODE DATA
    $data = ($partno)?
        imap_fetchbody($mbox,$mid,$partno):  // multipart
        imap_body($mbox,$mid);  // simple
    // Any part may be encoded, even plain text messages, so check everything.
    if ($p->encoding==4)
        $data = quoted_printable_decode($data);
    elseif ($p->encoding==3)
        $data = base64_decode($data);

    // PARAMETERS
    // get all parameters, like charset, filenames of attachments, etc.
    $params = array();
    if ($p->parameters)
        foreach ($p->parameters as $x)
            $params[strtolower($x->attribute)] = $x->value;
    if ($p->dparameters)
        foreach ($p->dparameters as $x)
            $params[strtolower($x->attribute)] = $x->value;

   /* // ATTACHMENT
    // Any part with a filename is an attachment,
    // so an attached text file (type 0) is not mistaken as the message.
    if ($params['filename'] || $params['name']) {
        // filename may be given as 'Filename' or 'Name' or both
        $filename = ($params['filename'])? $params['filename'] : $params['name'];
        // filename may be encoded, so see imap_mime_header_decode()
        $attachments[$filename] = $data;  // this is a problem if two files have same name
    } */

    // TEXT
    if ($p->type==0 && $data) {
        // Messages may be split in different parts because of inline attachments,
        // so append parts together with blank row.
        if (strtolower($p->subtype)=='plain')
            $plainmsg .= trim($data)."\n\n";
        else
            $htmlmsg .= $data ."<br><br>";
        $charset = $params['charset'];  // assume all parts are same charset
    }

    // EMBEDDED MESSAGE
    // Many bounce notifications embed the original message as type 2,
    // but AOL uses type 1 (multipart), which is not handled here.
    // There are no PHP functions to parse embedded messages,
    // so this just appends the raw source to the main message.
    elseif ($p->type==2 && $data) {
        $plainmsg .= $data."\n\n";
    }

    // SUBPART RECURSION
    if ($p->parts) {
        foreach ($p->parts as $partno0=>$p2)
		$this->getpart($mbox,$mid,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
    }
}




}

?>