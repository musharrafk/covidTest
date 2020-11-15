<?php
class mobileApiHrmsLive extends CI_Controller{
               public function __construct(){
                              parent::__construct();
               header('Content-Type: application/json');
                              $auth=$_POST['auth'];
                              $key='mobileApiSpace';
                              $arr= array();
                              if($auth != $key){
                                             $arr['response']='fail';
                                             $arr['errorMsg']='Authentication Faild Plz Check';
                                             echo json_encode($arr);
                                             die;
                              }
               }
             function login(){
                              $array = array();
                              $empId = filter_empid($_POST['empId']);
                            
                              $userPass=md5($_POST['userPass']);
                              
                              $sql=$this->db->query("select tbl_emp_master.empId,tbl_emp_master.empTitle,tbl_emp_master.empFname,tbl_emp_master.empMname,tbl_emp_master.empLname,tbl_emp_master.empRole,tbl_mst_designation.name,tbl_emp_master.empEmailOffice,tbl_emp_master.first_login,tbl_emp_master.empDOJ from tbl_emp_master left join tbl_mst_designation on tbl_mst_designation.id=tbl_emp_master.empDesination  where tbl_emp_master.empId='".$empId."' and tbl_emp_master.empPassword='".$userPass."' ")->result_array();
                              //echo json_encode($sql); die;
                           
$sqlEmpIdCount=$this->db->query("SELECT COUNT(empId) as reportingCount  FROM `tbl_emp_master` WHERE `reportingTo` = '".$empId."' AND status ='1' ")->result_array();
                              if($sql){
                                             //echo json_encode($sql); die;
                                             foreach($sql as $row){
                                                            $array['response'] ='success';
                                                            $array['empId']=$row['empId'];
                                                            $array['empTitle']=$row['empTitle'];
                                                            $array['empFname']=$row['empFname'];
                                                            $array['empMname']=$row['empMname'];
                                                            $array['empLname']=$row['empLname'];
                                                            if($array['empMname']){
                                                                           $empMname=$array['empMname']." ";
                                                            }else{
                                                                           $empMname=$array['empMname'];
                                                            }
															$array['empRole']= $row['empRole'];
                                                            $array['empFullName']=$array['empTitle']." ".$array['empFname']." ".$empMname.$array['empLname'];
                                                            $array['empDesination']=$row['name'];
                                                            $array['empEmailOffice']=$row['empEmailOffice'];
                                                            $array['reportingCount'] =$sqlEmpIdCount['0']['reportingCount'];
                                                            $array['auth']='mobileApiSpace';
                                                   
                                                     if($row['first_login'] == 0){
                                                         $this->newJoineeAttendance($row['empDOJ'],$row['empId']);
                                                         $this->db->query("UPDATE tbl_emp_master SET first_login ='1' WHERE empId ='".$empId."' ");  
                                                      }            
                                             }
                              }else{
                                             $array['response'] ='fail';
                                            $array['errorMsg']='User Id And Password Not Correct Please Check Again';

                                            //Please Login First Time From Web Then You Can Access App!
                              }
                              echo json_encode($array);
               }
/*           function checkAttendance(){
                              $array=array();
                              $empId=$_POST['empId'];
                              $attendanceDatetime=date('Y-m-d');
                              $checkjoinDate = $this->db->query("SELECT empId,empDOJ  FROM `tbl_emp_master` WHERE `empId` = '".$empId."' AND empDOJ <=date('".$attendanceDatetime."') ")->num_rows();
                              if(!$checkjoinDate){
                                             $array['response']='fail';
                                             $array['msg']='You can not marked attendance before your joining date';
                                             echo json_encode($array);die;
                              }
                              $res = $this->db->query("SELECT type FROM mobileattendance WHERE empId = '".$empId."' and  `attendanceDatetime` LIKE '%".$attendanceDatetime."%' ")->num_rows();
                              if(!$res){
                                             $array['response']='success';
                                             $array['typeId']='1';
                                             $array['msg']='plz mark morning Attendance';
                              }else if($res=='1'){
                                             $array['response']='success';
                                             $array['typeId']='2';
                                             $array['msg']='plz mark evenning Attendance';
                              }else{
                                             $array['response']='success';
                                             $array['typeId']='0';
                                             $array['msg']='You Have Marked Both Attendance';
                              }
                              echo json_encode($array);
               } */
               function checkAttendance(){
                              $array=array();
                              $empId=$_POST['empId'];
                              $attendanceDatetime=date('Y-m-d');
                              $checkjoinDate = $this->db->query("SELECT empId,empDOJ  FROM `tbl_emp_master` WHERE `empId` = '".$empId."' AND empDOJ <=date('".$attendanceDatetime."') ")->num_rows();
                              if(!$checkjoinDate){
                                             $array['response']='fail';
                                             $array['msg']='You can not marked attendance before your joining date';
                                             echo json_encode($array);die;
                              }
                              $res = $this->db->query("SELECT type FROM mobileattendance WHERE empId = '".$empId."' and  `attendanceDatetime` LIKE '%".$attendanceDatetime."%' ")->num_rows();
                              if(!$res){
                                             $array['response']='success';
                                             $array['typeId']='1';
                                             $array['attLocTyp']='0';
                                             $array['msg']='plz mark morning Attendance';
                              }else if($res=='1'){
                                             $sqlLocType = $this->db->query("SELECT attLocTyp FROM mobileattendance WHERE empId = '".$empId."' and  `attendanceDatetime` LIKE '%".$attendanceDatetime."%' ")->result_array();
                                             $array['response']='success';
                                             $array['typeId']='2';
                                             $array['attLocTyp']=$sqlLocType[0]['attLocTyp'];
                                             $array['msg']='plz mark evenning Attendance';
                              }else{
                                             $array['response']='success';
                                             $array['typeId']='0';
                                             $array['msg']='You Have Marked Both Attendance';
                              }
                              echo json_encode($array);
               }
function attendanceMark(){
                              $array=array();
                              $empId=$_POST['empId'];
                              //$userPic=$_POST['userPic'];
                              $lat=$_POST['lat'];
                              $log=$_POST['log'];
                              $type=$_POST['type'];
                              $attLocTyp=$_POST['attLocTyp'];
                                                                                                         $deviceId=$_POST['deviceId'];
                              //echo json_encode($attLocTyp);die;
                                                                                                         $attendanceDatetime=date('Y-m-d');
                                                                                   $res = $this->db->query("SELECT type FROM mobileattendance WHERE empId = '".$empId."' and type='".$type."' and `attendanceDatetime` LIKE '%".$attendanceDatetime."%' ")->num_rows();
                          
                             /* if(!$userPic){
                                             $array['response']='fail';
                                             $array['errorMsg']='Please Pic User Image';
                                             echo json_encode($array);
                                             die;
                              }else*/
                                                                                                          if(!$lat){
                                             $array['response']='fail';
                                             $array['errorMsg']='Your location empty ';
                                             echo json_encode($array);
                                             die;
                              }else if(!$log){
                                             $array['response']='fail';
                                             $array['errorMsg']='Your location empty ';
                                             echo json_encode($array);
                                             die;
                              }else if(!$type){
                                             $array['response']='fail';
                                             $array['errorMsg']='Attendance Type empty';
                                             echo json_encode($array);
                                             die;
                              }
                                                                                                         else if($res){
                                                                                                                          $array['response']='fail';
                                  $array['errorMsg']='You have already mark attendance';
                                  echo json_encode($array);
                                  die;
                                                                                                                                                        }
                              $attendanceDatetime=date('Y-m-d H:i:s');
                              $arr=array();
                              $arr['empId']=$empId;
                              $arr['attendanceDatetime']=$attendanceDatetime;
                              $arr['createdOn']=$attendanceDatetime;
                           /*   $imgName = rand() . '_' . time() .'_'.$arr['empId'].'.jpg';
                                                            $decoded = base64_decode($userPic);
                                                            file_put_contents(FCPATH . 'uploads/' . $imgName, $decoded);
                                                            //$arr['img']=$imgName;*/
                                                            $imgName="";
                                                            $arr['image'] = $imgName;
                                                            $arr['lat']=$lat;
                                                            $arr['log']=$log;
                                                            $arr['attLocTyp']=$attLocTyp;
                                                                                                                                                                                                                                 $arr['deviceId']=$deviceId;
                              $insertSql= $this->db->insert('rowdata',$arr);
                              if($insertSql){
                              //$this->insert_daily_attendance();
                                             $lastId=$this->db->insert_id();
                                             $arrMobile=array();
                                                            $arrMobile['rowId']=$lastId;
                                                            $arrMobile['empId']=$empId;
                                                            //$arrMobile['img']=$userPic;
                                                            /*$imgName = rand() . '_' . time() .'_'.$arr['empId'].'.jpg';
                                                            $decoded = base64_decode($userPic);
                                                            file_put_contents(FCPATH . 'uploads/' . $imgName, $decoded);*/
                                                            $arrMobile['img']=$imgName;
                                                          
                                                            $arrLog['image'] = $imgName;
                                                            $arrMobile['lat']=$lat;
                                                            $arrMobile['log']=$log;
                                                            $arrMobile['attendanceDatetime']=$attendanceDatetime;
                                                            $arrMobile['type']=$type;
                                                            $arrMobile['attLocTyp']=$attLocTyp;
                                                                                                                                                                                                                                 $arrMobile['deviceId']=$deviceId;
                                                            $Sql= $this->db->insert('mobileattendance',$arrMobile);
                                                            $array['response']='success';
                                                            //redirect(site_url('general/insert_daily_attendance'));
                                                            //base_url('general/insert_daily_attendance');
                              }else{
                                             $array['response']='fail';
                              }
                              echo json_encode($array);
               }
/*           function attendanceMark(){
                              $array=array();
                              $empId=$_POST['empId'];
                              $userPic=$_POST['userPic'];
                              $lat=$_POST['lat'];
                              $log=$_POST['log'];
                              $type=$_POST['type'];
                              if(!$userPic){
                                             $array['response']='fail';
                                             $array['errorMsg']='Please Pic User Image';
                                             echo json_encode($array);
                                             die;
                              }else if(!$lat){
                                             $array['response']='fail';
                                             $array['errorMsg']='Your location empty ';
                                             echo json_encode($array);
                                             die;
                              }else if(!$log){
                                             $array['response']='fail';
                                             $array['errorMsg']='Your location empty ';
                                             echo json_encode($array);
                                             die;
                              }else if(!$type){
                                             $array['response']='fail';
                                             $array['errorMsg']='Attendance Type empty';
                                             echo json_encode($array);
                                             die;
                              }
                              $attendanceDatetime=date('Y-m-d H:i:s');
                              $arr=array();
                              $arr['empId']=$empId;
                              $arr['attendanceDatetime']=$attendanceDatetime;
                              $arr['createdOn']=$attendanceDatetime;
                              $imgName = rand() . '_' . time() .'_'.$arr['empId'].'.jpg';
                                                            $decoded = base64_decode($userPic);
                                                            file_put_contents(FCPATH . 'uploads/' . $imgName, $decoded);
                                                            //$arr['img']=$imgName;
                                                          
                                                            $arr['image'] = $imgName;
                                                            $arr['lat']=$lat;
                                                            $arr['log']=$log;
                              $insertSql= $this->db->insert('rowdata',$arr);
                              if($insertSql){
                              //$this->insert_daily_attendance();
                                             $lastId=$this->db->insert_id();
                                             $arrMobile=array();
                                                            $arrMobile['rowId']=$lastId;
                                                            $arrMobile['empId']=$empId;
                                                            //$arrMobile['img']=$userPic;
                                                            /*$imgName = rand() . '_' . time() .'_'.$arr['empId'].'.jpg';
                                                            $decoded = base64_decode($userPic);
                                                            file_put_contents(FCPATH . 'uploads/' . $imgName, $decoded);
                                                            $arrMobile['img']=$imgName;
                                                          
                                                            $arrLog['image'] = $imgName;
                                                            $arrMobile['lat']=$lat;
                                                            $arrMobile['log']=$log;
                                                            $arrMobile['attendanceDatetime']=$attendanceDatetime;
                                                            $arrMobile['type']=$type;
                                                            $Sql= $this->db->insert('mobileattendance',$arrMobile);
                                                            $array['response']='success';
                                                            //redirect(site_url('general/insert_daily_attendance'));
                                                            //base_url('general/insert_daily_attendance');
                              }else{
                                             $array['response']='fail';
                              }
                              echo json_encode($array);
               } */
               /* function profile(){
                              $array=array();
                              $empId=$_POST['empId'];
                              //$res = $this->db->query("SELECT empMaster.empId,empMaster.empTitle,empMaster.empFname,empMaster.empMname,empMaster.empLname,empMaster.empMobile,empMaster.empDOJ,tblDesignation.name,manager.empTitle as managerEmpTitle ,manager.empFname as managerEmpFname,manager.empMname as managerEmpMname,manager.empLname as managerEmpLname,tblDept.name AS dipartment,empMaster.empEmailPersonal,empMaster.empEmailOffice,personal.empDOB,personal.empGender,personal.empFathersName,personal.empMotherName,personal.empMaritalStatus,personal.emergencyContactNumber, tblBloodGroup.name AS bloodGroup,personal.empNationality,personal.empReligion FROM tbl_emp_master as empMaster Left join tbl_mst_designation as tblDesignation on tblDesignation.id=empMaster.empDesination Left JOIN tbl_emp_master as manager on manager.empId=empMaster.reportingTo LEFT JOIN tbl_mst_dept as tblDept on tblDept.id=empMaster.empDept LEFT JOIN tbl_emp_personal as personal on personal.empId=empMaster.empId LEFT JOIN tbl_mst_blood_group as tblBloodGroup on tblBloodGroup.id=personal.empBloodGroup WHERE empMaster.empId='".$empId."' ")->result_array();
                              $res = $this->db->query("SELECT shift.shiftTimeFrom,shift.shiftTimeTo,service.empType, additionalR.nationality as Nationality, empMaster.empId,empMaster.empTitle,empMaster.empFname,empMaster.empMname,empMaster.empLname,empMaster.empMobile,empMaster.empDOJ,tblDesignation.name,manager.empTitle as managerEmpTitle ,manager.empFname as managerEmpFname,manager.empMname as managerEmpMname,manager.empLname as managerEmpLname,tblDept.name AS dipartment,empMaster.empEmailPersonal,empMaster.empEmailOffice,personal.empDOB,personal.empGender,personal.empFathersName,personal.empMotherName,personal.empMaritalStatus,personal.emergencyContactNumber, tblBloodGroup.name AS bloodGroup,personal.empNationality,personal.empReligion,address.empPresentStreet,stateTbl.State_Name AS statePresent,citymst.cityName as cityPresent,address.empPresentZipcode,address.empPermanentStreet,cityP.cityName AS permanentCity,stateP.State_Name AS statePermanent,address.empPermanentZipcode,address.empMobile as addressMobile FROM tbl_emp_master as empMaster Left join tbl_candidate_additional_det as additionalR on additionalR.empId=empMaster.candidateId Left join tbl_emp_service as service on service.empId=empMaster.empId Left join tbl_mst_shift shift on shift.id=service.shift Left join tbl_mst_designation as tblDesignation on tblDesignation.id=empMaster.empDesination Left JOIN tbl_emp_master as manager on manager.empId=empMaster.reportingTo LEFT JOIN tbl_mst_dept as tblDept on tblDept.id=empMaster.empDept LEFT JOIN tbl_emp_personal as personal on personal.empId=empMaster.empId LEFT JOIN tbl_mst_blood_group as tblBloodGroup on tblBloodGroup.id=personal.empBloodGroup LEFT JOIN tbl_emp_address as address ON empMaster.empId=address.empId LEFT JOIN tbl_mst_city as citymst ON citymst.state=address.empPresentState AND citymst.cityId=address.empPresentCity LEFT JOIN tbl_mst_state as stateTbl ON stateTbl.State_Id=citymst.state LEFT JOIN tbl_mst_city as cityP ON cityP.state=address.empPermanentState AND cityP.cityId=address.empPermanentCity LEFT JOIN tbl_mst_state as stateP ON stateP.State_Id=cityP.state WHERE empMaster.empId='".$empId."' ")->result_array();
                              //echo json_encode($res);die;
                              if($res){
                                             foreach($res as $row){
                                                            $array['response']='success';
                                                            $array['empId']=$row['empId'];
                                                            if($row['empMname']){
                                                                           $empMname=$row['empMname']." ";
                                                            }else{
                                                                           $empMname=$row['empMname'];
                                                            }
                                                            $array['empFullName']=$row['empTitle']." ".$row['empFname']." ".$empMname.$row['empLname'];
                                                            $array['empMobile']=$row['empMobile'];
                                                            $array['empDOJ']=$row['empDOJ'];
                                                            $array['empDesignation']=$row['name'];
                                                            if($row['managerEmpMname']){
                                                                           $managerMname=$row['managerEmpMname']." ";
                                                            }else{
                                                                           $managerMname=$array['empMname'];
                                                            }
                                                            $array['managerFullName']=$row['managerEmpTitle']." ".$row['managerEmpFname']." ".$managerMname.$row['managerEmpLname'];
                                                            $array['dipartment']=(string)$row['dipartment'];
                                                            $array['empEmailPersonal']=$row['empEmailPersonal'];
                                                            $array['empEmailOffice']=$row['empEmailOffice'];
                                                            $array['empDOB']=$row['empDOB'];
                                                            $array['empGender']=$row['empGender'];
                                                            $array['empFathersName']=$row['empFathersName'];
                                                            $array['empMotherName']=$row['empMotherName'];
                                                            $array['empMaritalStatus']=$row['empMaritalStatus'];
                                                            $array['emergencyContactNumber']=$row['emergencyContactNumber'];
                                                            $array['bloodGroup']=(string)$row['bloodGroup'];
                                                            $array['empNationality']=$row['Nationality'];
                                                            $array['empReligion']=$row['empReligion'];
                                                            $array['empType']=($row['empType']=='2'?"Probation":"Permanent");
                                                            $array['officeTimming']=$row['shiftTimeFrom']."-".$row['shiftTimeTo'];
                                                            $array['dateOfMarrige']='';
                                                            $array['addressPresent']=$row['empPresentStreet'].' '.$row['cityPresent'].' '.$row['statePresent'].' '.$row['empPresentZipcode'];
                                                            //$array['pincodePresent']=$row['empPresentZipcode'];
                                                            $array['addressPermanent']=$row['empPermanentStreet'].' '.$row['permanentCity'].' '.$row['statePermanent'].' '.$row['empPermanentZipcode'];
                                                            //$array['pincodePermanent']=$row['empPermanentZipcode'];
                                                            $array['addressMobile']=$row['addressMobile'];
                                             }          
                              }else{
                                             $array['response']='fail';
                                             $array['data']=$res;
                                             }
                              echo json_encode($array);
               } */
               function profile(){
                              $array=array();
                              $empId=$_POST['empId'];
                              //$res = $this->db->query("SELECT empMaster.empId,empMaster.empTitle,empMaster.empFname,empMaster.empMname,empMaster.empLname,empMaster.empMobile,empMaster.empDOJ,tblDesignation.name,manager.empTitle as managerEmpTitle ,manager.empFname as managerEmpFname,manager.empMname as managerEmpMname,manager.empLname as managerEmpLname,tblDept.name AS dipartment,empMaster.empEmailPersonal,empMaster.empEmailOffice,personal.empDOB,personal.empGender,personal.empFathersName,personal.empMotherName,personal.empMaritalStatus,personal.emergencyContactNumber, tblBloodGroup.name AS bloodGroup,personal.empNationality,personal.empReligion FROM tbl_emp_master as empMaster Left join tbl_mst_designation as tblDesignation on tblDesignation.id=empMaster.empDesination Left JOIN tbl_emp_master as manager on manager.empId=empMaster.reportingTo LEFT JOIN tbl_mst_dept as tblDept on tblDept.id=empMaster.empDept LEFT JOIN tbl_emp_personal as personal on personal.empId=empMaster.empId LEFT JOIN tbl_mst_blood_group as tblBloodGroup on tblBloodGroup.id=personal.empBloodGroup WHERE empMaster.empId='".$empId."' ")->result_array();
                              $res = $this->db->query("SELECT shift.shiftTimeFrom,shift.shiftTimeTo,service.empType, additionalR.nationality as Nationality, empMaster.empId,empMaster.empTitle,empMaster.empFname,empMaster.empMname,empMaster.empLname,empMaster.empMobile,empMaster.empDOJ,tblDesignation.name,manager.empTitle as managerEmpTitle ,manager.empFname as managerEmpFname,manager.empMname as managerEmpMname,manager.empLname as managerEmpLname,tblDept.name AS dipartment,empMaster.empEmailPersonal,empMaster.empEmailOffice,personal.empDOB,personal.empGender,personal.empFathersName,personal.empMotherName,personal.empMaritalStatus,personal.emergencyContactNumber, tblBloodGroup.name AS bloodGroup,personal.empNationality,personal.empReligion,address.empPresentStreet,stateTbl.State_Name AS statePresent,citymst.cityName as cityPresent,address.empPresentZipcode,address.empPermanentStreet,cityP.cityName AS permanentCity,stateP.State_Name AS statePermanent,address.empPermanentZipcode,address.empMobile as addressMobile FROM tbl_emp_master as empMaster Left join tbl_candidate_additional_det as additionalR on additionalR.empId=empMaster.candidateId Left join tbl_emp_service as service on service.empId=empMaster.empId Left join tbl_mst_shift shift on shift.id=service.shift Left join tbl_mst_designation as tblDesignation on tblDesignation.id=empMaster.empDesination Left JOIN tbl_emp_master as manager on manager.empId=empMaster.reportingTo LEFT JOIN tbl_mst_dept as tblDept on tblDept.id=empMaster.empDept LEFT JOIN tbl_emp_personal as personal on personal.empId=empMaster.empId LEFT JOIN tbl_mst_blood_group as tblBloodGroup on tblBloodGroup.id=personal.empBloodGroup LEFT JOIN tbl_emp_address as address ON empMaster.empId=address.empId LEFT JOIN tbl_mst_city as citymst ON citymst.state=address.empPresentState AND citymst.cityId=address.empPresentCity LEFT JOIN tbl_mst_state as stateTbl ON stateTbl.State_Id=citymst.state LEFT JOIN tbl_mst_city as cityP ON cityP.state=address.empPermanentState AND cityP.cityId=address.empPermanentCity LEFT JOIN tbl_mst_state as stateP ON stateP.State_Id=cityP.state WHERE empMaster.empId='".$empId."' ")->result_array();
                              //echo json_encode($res);die;
                              if($res){
                                             foreach($res as $row){
                                                            $array['response']='success';
                                                            $array['empId']=$row['empId'];
                                                            if($row['empMname']){
                                                                           $empMname=$row['empMname']." ";
                                                            }else{
                                                                           $empMname=$row['empMname'];
                                                            }
                                                            $array['empFullName']=$row['empTitle']." ".$row['empFname']." ".$empMname.$row['empLname'];
                                                            $array['empMobile']=$row['empMobile'];
                                                            $array['empDOJ']=$row['empDOJ'];
                                                            $array['empDesignation']=$row['name'];
                                                            if($row['managerEmpMname']){
                                                                           $managerMname=$row['managerEmpMname']." ";
                                                            }else{
                                                                           $managerMname=$array['empMname'];
                                                            }
                                                            $array['managerFullName']=$row['managerEmpTitle']." ".$row['managerEmpFname']." ".$managerMname.$row['managerEmpLname'];
                                                            $array['dipartment']=(string)$row['dipartment'];
                                                            $array['empEmailPersonal']=$row['empEmailPersonal'];
                                                            $array['empEmailOffice']=$row['empEmailOffice'];
                                                            $array['empDOB']=$row['empDOB'];
                                                            $array['empGender']=$row['empGender'];
                                                            $array['empFathersName']=$row['empFathersName'];
                                                            $array['empMotherName']=$row['empMotherName'];
                                                            $array['empMaritalStatus']=$row['empMaritalStatus'];
                                                            $array['emergencyContactNumber']=$row['emergencyContactNumber'];
                                                            $array['bloodGroup']=(string)$row['bloodGroup'];
                                                            $array['empNationality']=(string)$row['Nationality'];
                                                            $array['empReligion']=(string)$row['empReligion'];
                                                            $array['empType']=($row['empType']=='2'?"Probation":"Permanent");
                                                            $array['officeTimming']=date('h:i A', strtotime($row['shiftTimeFrom']));//."-".$row['shiftTimeTo'];
                                                            $array['dateOfMarrige']='';
                                                            //$array['addressPresent']=$row['empPresentStreet'].' '.$row['cityPresent'].' '.$row['statePresent'].' '.$row['empPresentZipcode'];
                                                            $array['empPresentStreet']=(string)$row['empPresentStreet'];
                                                            $array['cityPresent']=(string)$row['cityPresent'];
                                                            $array['statePresent']=(string)$row['statePresent'];
                                                            $array['empPresentZipcode']=(string)$row['empPresentZipcode'];
                                                            //$array['addressPermanent']=$row['empPermanentStreet'].' '.$row['permanentCity'].' '.$row['statePermanent'].' '.$row['empPermanentZipcode'];
                                                          
                                                            $array['empPermanentStreet']=(string)$row['empPermanentStreet'];
                                                            $array['permanentCity']=(string)$row['permanentCity'];
                                                            $array['statePermanent']=(string)$row['statePermanent'];
                                                            $array['empPermanentZipcode']=$row['empPermanentZipcode'];
                                                           
                                                            //$array['pincodePermanent']=$row['empPermanentZipcode'];
                                                            $array['addressMobile']=$row['addressMobile'];
                                             }          
                              }else{
                                             $array['response']='fail';
                                             $array['data']=$res;
                                             }
                              echo json_encode($array);
               }
                                             
               function sendLeaveRequest(){
                              //echo MODE;die;
                  $leave_data="";
                              $array=array();
                              $data = $this->input->post();
                              $_POST['fromDate']=date('Y-m-d',strtotime($_POST['fromDate']));
                              $_POST['toDate']=date('Y-m-d',strtotime($_POST['toDate']));
                                             //validation 13-August-18
                              //$dates = array();
                              //print_r($_POST);die;
                              $dates=$this->getnoofdaysleave($_POST['fromDate'],$_POST['toDate'],$_POST['empId']);
                              //print_r($dates);die;
                              $available_leaves=$this->getleaveavailibility($_POST['empId'],$_POST['leaveType']);
                            
                              if($data['regularizationType']=='FHD' or $data['regularizationType']=='SHD')
                              {
                              $cl_days=0.5;
                              }
                              else
                              {
                              $cl_days=count($dates);
                              }
                              $start = $current = strtotime($this->input->post('fromDate'));
                              $end = strtotime($this->input->post('toDate'));
                              //  changed 17-sep-2018 while ($current <= $end) {
                                             // $dates[] = date('Y-m-d', $current);
                                             // $current = strtotime('+1 days', $current);
                              // } 17-sep-2018
$getprevdata=$this->db->query('Select * from tbl_regularization where requestFrom="'.$_POST['empId'].'" AND status!="R"  ')->result_array();
foreach($getprevdata as $row)
{
for($i=0;$i<count($dates);$i++)
{// print_r($dates);
if(($dates[$i]>=$row['fromDate'] AND $dates[$i]<=$row['toDate']))
{
$leave_data=2;
break;
}
else
{
$leave_data="okk";
continue;
}
}
if($leave_data==2)
{
break;
}
}
                              //validation 13-August-18
                           
                              $doj = $this->employee_model->getEmpdoj($_POST['empId']);
                              $data1['doj'] = $doj['0'];
                              unset($data['empId']);
                              unset($data['doj']);
                              unset($data['auth']);
                              if($_POST['leaveType']=='SL' && ($_POST['fromDate']>=date('Y-m-d')))
                              {
                              $array['response']="fail";
                              $array['errorMsg']="Sick Leaves can not be planned leaves";
                              echo json_encode($array);
                              die;
                              }
                              else if($_POST['leaveType']=='CL' && (($cl_days>3)))
                              {
                              $array['response']="fail";
                              $array['errorMsg']="You can not  apply CL consecutively for more than 3 days";
                              echo json_encode($array);
                              die;
                              }
                              else if($cl_days==0)
                              {
                            
                              $array['response']="fail";
                              $array['errorMsg']="You are applying leaves either on holidays or week off";
                              echo json_encode($array);
                              die;
                              }
                              else if($_POST['fromDate']< $data['doj']['empDOJ'])
                              {
                                             $array['response']="fail";
                                             $array['errorMsg']="You can apply leave grater than joining date";
                              echo json_encode($array);
                              die;
                              }
                              else if((($available_leaves))<(($cl_days)))
                              {
                           
                              $array['response']="fail";
                              $array['errorMsg']="Please Check you have no balance for the applied leave type";
                              echo json_encode($array);
                              die;
                              }
                              //leave authentication
                              else if($leave_data=='2')
                              {
                                $array['response']="fail";
                                             $array['errorMsg']="Please check you have already applied leaves for the mentioned dates";
                              echo json_encode($array);
                              die;
                              }
                              //leave authentication
                              else
                              {
                                             $result = $this->employee_model->getApprovedBy($_POST['empId']);
                                             //print_r($result[0]['empEmailOffice']);die;
                                             $data['requestFrom'] = $_POST['empId'];
                                             $data['requestTo'] = $result['0']['reportingTo'];
                                             $data['regularizationDate'] =  date('Y-m-d H:i:s');
                                             $data['status'] = 'P';
                              if($data['regularizationType']=='FHD' or $data['regularizationType']=='SHD'){
                                             $data['noofdays'] =0.5;
                              }else{
                                             $data['noofdays'] = $cl_days;
                              }
                                                            //pre($data);die;
                              $id_insert=$this->parent_model->query_insert(TABLE_REGULARIZATION, $data);
                              $id=$id_insert;
               if($id)
               {
               foreach($dates as $row)
               {
               $data_reg_det['regularizationId']=$id;
               $data_reg_det['requestFrom']=$_POST['empId'];;
               $data_reg_det['requestTo']=$result['0']['reportingTo'];
               $data_reg_det['regularizationDate'] =  date('Y-m-d H:i:s');
               $data_reg_det['status'] = 'P';
               $data_reg_det['fromDate'] = $row;
               $data_reg_det['toDate'] = $row;
               if($data['regularizationType']=='FHD' OR $data['regularizationType']=='SHD'){
                              $data_reg_det['noofdays'] =0.5;
               }else{
                              //$data['noofdays'] = (strtotime($this->input->post('toDate')) - strtotime($this->input->post('fromDate'))) / (60 * 60 * 24)+1;
                              $data_reg_det['noofdays'] = 1;
               }
               $data_reg_det['leaveType'] =$data['leaveType'];
               $data_reg_det['regularizationType'] =$data['regularizationType'];
               $this->parent_model->query_insert(TABLE_REGULARIZATION_DETAILS, $data_reg_det);
               }
            
               }
                                             //send Notification
                              $subject     = "Leave Request :".$data1['doj']['name'];
                              $manager  =$result['0']['manager'];
                              if(MODE =='live'){
                                             $to = $result[0]['empEmailOffice'];
                                                                           //$to = EMAILTO;
                              }
                              else
                              {
                                             $to = EMAILTO;
                              }
                                             $body_txt   = $data1['doj']['name']." has sent Leave Request with the below mentioned dates.
                                             <table>
                                                            <tr><td><b>Leave From </b></td><td>: ".date('d-F-Y',strtotime($data['fromDate']))."</td></tr>
                                                            <tr><td><b>Leave To</b></td><td>: ".date('d-F-Y',strtotime($data['toDate']))."</td></tr>
                                                            <tr><td><b>Leave Type</b></td><td>: ".$this->input->post('leaveType')."</td></tr>
                                                            <tr><td><b>Remarks</b></td><td>: ".$this->input->post('remarks')."</td></tr>
                                                            <tr valign='middle'><td><b>Request Response</b>  </td><td colspan='2' valign='middle'><table><tr><td style='padding:5px;'> <a href='".site_url('login')."'><img src='".base_url()."/images/approve.png'></a></td><td style='padding:5px;'><a href='".site_url('login')."'><img src='".base_url()."/images/decline.png'></a></td></tr></table></td></tr>
                                             </table>";
                                             $data = array('SITE_LOGO_URL' => base_url().SITE_IMAGEURL.'logo.png',
                                                            'USER' => $manager,
                                                            'SITE_NAME' => SITE_NAME,
                                                            'MAIL_DATA'=>$body_txt);
                                                           
                                             $htmlMessage =  $this->parser->parse('emails/alert', $data, true);
                                             $this->myemail->sendEmail($to,$subject, $htmlMessage, ADMIN_EMAIL, ADMIN_NAME);
                                             $array['response']= 'Rsuccess';
                              }
                              //echo $this->db->affected_rows();
                              //die;
                              if($this->db->affected_rows())
                              {
                                   $empNameSql=$this->db->query("SELECT tbl_emp_master.empId,tbl_emp_master.empFname,empLname  FROM tbl_emp_master WHERE tbl_emp_master.empId ='".$_POST['empId']."' ")->result_array();
                              $empName=$empNameSql['0']['empFname'].' '.$empNameSql['0']['empLname'];
                $title=$empName.' Leave Request Apply ';
               $message='From:'.$_POST['fromDate'].' '.'To:'.$_POST['toDate'];
               $empTokenId=$result['0']['reportingTo'];
                                             $tokenSql=$this->db->query("SELECT tbl_gcm_users.deviceId as deviceId FROM tbl_gcm_users WHERE tbl_gcm_users.empId = '".$empTokenId."' ")->result_array();
                                             $token=$tokenSql['0']['deviceId'];
                                             if($token){         
                                                            $API_SERVER_KEY = 'AAAAucVTDK4:APA91bE6T2wVEW1vL17C1Dm5Cizf_5TzkeM8Wb16TG41IE9uXAjvRk6Va9S4ntgdwKX9m6rqAHZH7rLAobyYslJFPTAHN4kcYU8OZrPRf7pPDvD-3PM3xQDvOIuPaFE6tJ1dY98CTc5M';
                                             $is_background = "TRUE";
                                                            $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
                                                               $fields = array(
                                                                 'to' => $token,
                                                                 'notification' =>array('title' => $title, 'body' =>  $message ),
                                                                 'priority' =>'high',
                                                               );
                                      $headers = array(
                                         'Authorization:key=' .$API_SERVER_KEY,
                                          'Content-Type:application/json'
                                      );
                                      $ch = curl_init();
                                      // Set the url, number of POST vars, POST data
                                      curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
                                      curl_setopt($ch, CURLOPT_POST, true);
                                      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                      curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
                                      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                                      $result = curl_exec($ch);
                                      curl_close($ch);
                                      //echo json_encode($result);
                                                           
                                             }
 
 
 
 
                              $array['response']= 'success';
                              }
                              else
                              {
                              $array['response']= 'fail';
                              }
                              echo json_encode($array);die;
               }
               /* changed on 17sep-18 function sendLeaveRequest(){
                    $leave_data="";
                              $array=array();
                              $data = $this->input->post();
                                             //validation 13-August-18
                              $dates = array();
                              $start = $current = strtotime($this->input->post('fromDate'));
                              $end = strtotime($this->input->post('toDate'));
                              while ($current <= $end) {
                                             $dates[] = date('Y-m-d', $current);
                                             $current = strtotime('+1 days', $current);
                              }
                              $getprevdata=$this->db->query('Select * from tbl_regularization where requestFrom="'.$_POST['empId'].'" AND status!="R"  ')->result_array();
foreach($getprevdata as $row)
{
for($i=0;$i<count($dates);$i++)
{
if(($dates[$i]>=$row['fromDate'] AND $dates[$i]<=$row['toDate']))
{
$leave_data=2;
break;
}
else
{
$leave_data="okk";
continue;
}
}
if($leave_data==2)
{
break;
}
}
                              //validation 13-August-18
                           
                              $doj = $this->employee_model->getEmpdoj($_POST['empId']);
                              $data1['doj'] = $doj['0'];
                              unset($data['empId']);
                              unset($data['doj']);
                              unset($data['auth']);
                              if($_POST['leaveType']=='CL' && ((strtotime($this->input->post('toDate')) - strtotime($this->input->post('fromDate'))) / (60 * 60 * 24)+1)>3)
                              {
                                             $array['response']="fail";
                                             $array['errorMsg']="You can not  apply CL consecutively for more than 3 days";
                              echo json_encode($array);
                              die;
                              }
                              else if($_POST['fromDate']< $data['doj']['empDOJ'])
                              {
                                             $array['response']="fail";
                                             $array['errorMsg']="You can apply leave grater than joining date";
                              echo json_encode($array);
                              die;
                              }
                              //leave authentication
                              else if($leave_data=='2')
                              {
                                $array['response']="fail";
                                             $array['errorMsg']="Please check you have already applied leaves for the mentioned dates";
                              echo json_encode($array);
                              die;
                              }
                              //leave authentication
                              else
                              {
                                             $result = $this->employee_model->getApprovedBy($_POST['empId']);
                                             $data['requestFrom'] = $_POST['empId'];
                                             $data['requestTo'] = $result['0']['reportingTo'];
                                             $data['regularizationDate'] =  date('Y-m-d H:i:s');
                                             $data['status'] = 'P';
                              if($data['regularizationType']=='FHD' or $data['regularizationType']=='SHD'){
                                             $data['noofdays'] =0.5;
                              }else{
                                             $data['noofdays'] = (strtotime($this->input->post('toDate')) - strtotime($this->input->post('fromDate'))) / (60 * 60 * 24)+1;
                              }
                                                            //pre($data);die;
                              $this->parent_model->query_insert(TABLE_REGULARIZATION, $data);
                                             //send Notification
                              $subject     = "Leave Request :".$data1['doj']['name'];
                              $manager  =$result['0']['manager'];
                              if(MODE =='live'){
                                             $to = $reporting['0']['empEmailOffice'];
                                                                           //$to = EMAILTO;
                              }
                              else
                              {
                                             $to = EMAILTO;
                              }
                                             $body_txt   = $data1['doj']['name']." has sent Leave Request with the below mentioned dates.
                                             <table>
                                                            <tr><td><b>Leave From </b></td><td>: ".date('d-F-Y',strtotime($data['fromDate']))."</td></tr>
                                                            <tr><td><b>Leave To</b></td><td>: ".date('d-F-Y',strtotime($data['toDate']))."</td></tr>
                                                            <tr><td><b>Leave Type</b></td><td>: ".$this->input->post('leaveType')."</td></tr>
                                                            <tr><td><b>Remarks</b></td><td>: ".$this->input->post('remarks')."</td></tr>
                                                            <tr valign='middle'><td><b>Request Response</b>  </td><td colspan='2' valign='middle'><table><tr><td style='padding:5px;'> <a href='".site_url('login')."'><img src='http://13.126.52.249/space/images/approve.png'></a></td><td style='padding:5px;'><a href='".site_url('login')."'><img src='http://13.126.52.249/space/images/decline.png'></a></td></tr></table></td></tr>
                                             </table>";
                                             $data = array('SITE_LOGO_URL' => base_url().SITE_IMAGEURL.'logo.png',
                                                            'USER' => $manager,
                                                            'SITE_NAME' => SITE_NAME,
                                                            'MAIL_DATA'=>$body_txt);
                                             $htmlMessage =  $this->parser->parse('emails/alert', $data, true);
                                             $this->myemail->sendEmail($to,$subject, $htmlMessage, ADMIN_EMAIL, ADMIN_NAME);
                                             //$array['response']= 'success';
                              }
                              if($this->db->affected_rows())
                              {
                              $array['response']= 'success';
                              }
                              else
                              {
                              $array['response']= 'fail';
                              }
                              echo json_encode($array);
               } changed on 17-sep-18*/
            
               function getMonthlyAttendance()
               {
                  $data=array();
                              $param1=$_POST['month'];
                  $param2=$_POST['year'];
                  $param3=$_POST['empId'];
                  $param4=$_POST['type'];
                              $param1=date("m",strtotime( $param1));
                           
                              /* $param1="06";
                              $param2="2018";
                              $param3="10005";
                              $param4="P"; */
        $monthName = date("F", mktime(0, 0, 0, $param1, 10));
                              $result=$this->db->query("SELECT DAY(LAST_DAY(DATE_FORMAT(attendanceDate,'%Y%m%d'))) as workingday, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus !='A' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalpresent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='R' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as resign,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='NJ' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as newjoin,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HD' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfday,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HL' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfdayleave,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='LC' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as latecoming , (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='A'  and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId ) as totalabsent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='L' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalleave,    concat(e.empTitle,' ',e.empFname,' ',e.empLname) as emp_name, a.empId, group_concat(date_format(attendanceDate,'%d')) as d, max(date_format(attendanceDate,'%d')) as maxday,  GROUP_CONCAT(a.attendanceStatus) as t, GROUP_CONCAT(DATE_FORMAT(a.inTime,'%H:%i:%s')) as ti, GROUP_CONCAT(DATE_FORMAT(a.outTime,'%H:%i:%s')) as outTime, GROUP_CONCAT(DATE_FORMAT(a.workingHours,'%H:%i')) as workingHours,GROUP_CONCAT(a.id) as attId,GROUP_CONCAT(a.status) as attStatus, GROUP_CONCAT(a.attendanceDate) as attDate FROM `tbl_emp_attendance` a inner join tbl_emp_master e on a.empId=e.empId where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' AND a.empId='".$param3."'   group by e.empId ")->result_array();
                           
                              $dates=explode(",",$result[0]['d']);
                              $type=explode(",",$result[0]['t']);
                              $intime=explode(",",$result[0]['ti']);
                              $outtime=explode(",",$result[0]['outTime']);
                              $workinghours=explode(",",$result[0]['workingHours']);
                              $attId=explode(",",$result[0]['attId']);
                              $attStatus=explode(",",$result[0]['attStatus']);
                                                            $attDate=explode(",",$result[0]['attDate']);
                              $array=array();
                              for($i=0;$i<count($dates);$i++)
                              {
                              $data[$i]['day']=date('l',strtotime(($param2."-".$param1."-".$dates[$i])));
                              $data[$i]['date']=$dates[$i];
                              $data[$i]['type']=$type[$i];
                              $data[$i]['intime']=$intime[$i];
                              $data[$i]['outtime']=$outtime[$i];
                              $data[$i]['workinghours']=$workinghours[$i];
                              $data[$i]['month']=date('M',strtotime($monthName));
                              $data[$i]['attId']=$attId[$i];
                              $data[$i]['attStatus']=$attStatus[$i];
                                                            $data[$i]['attDate']=$attDate[$i];
$data[$i]['attDMY']=date("d M Y", strtotime($attDate[$i]));
                              }
                 usort($data, function ($item1, $item2) {
       if ($item1['date'] == $item2['date']) return 0;
       return $item1['date'] > $item2['date'] ? -1 : 1;
       });
                              $array['response']='success';
                              if($result[0]['workingday']!=null)
                              {
                              $array['day']=$result[0]['workingday'];
                              }
                              else
                              {
                              $array['day']="NA";
                              }
                              if($result[0]['totalpresent']!=null)
                              {
                              $array['present_days']=$result[0]['totalpresent'];
                              }
                              else
                              {
                              $array['present_days']="NA";
                              }
                              if($result[0]['totalhalfday']!=null)
                              {
                              $array['halfday']=$result[0]['totalhalfday'];
                              }
                              else
                              {
                              $array['halfday']="NA";
                              }
                              if($result[0]['workingday'])
                              {
                              $array['header']="1st"."-".$result[0]['workingday'].($result[0]['workingday']=="30"?"th":"st")." ".$monthName;
                              }
                              else
                              {
                              $d=cal_days_in_month(CAL_GREGORIAN,$param1,$param2);
                              $array['header']="1st"."-".$d.($d=="30"?"th":"st")." ".$monthName;
                              }
                              if($result[0]['totalleave']!=null)
                              {
                              $array['leave']=$result[0]['totalleave'];
                              }
                              else
                              {
                              $array['leave']="NA";
                              }
                              if($result[0]['totalabsent']!=null)
                              {
                              $array['absent']=$result[0]['totalabsent'];
                              }
                              else
                              {
                              $array['absent']="NA";
                              }
                              if(count($result[0])>1)
                              {
                              if($param4=="P")
                              {
                           
                              function filter($val) {
        return $val['type'] === 'P' OR $val['type'] === 'WO' OR $val['type'] === 'DO' OR $val['type'] === 'HD' OR $val['type'] === 'WH';
        }
         $remaining = array_filter($data, function ($val) {
        return filter($val);
         });
                              $array['data']=array_values($remaining);
                              }
                              else if($param4)
                              {
                              $filter_array = array(
       'type' => $param4,
    
        );
     
                              // filter the array
                              $filtered_array = array_filter($data, function ($val_array) use ($filter_array) {
                              $intersection = array_intersect_assoc($val_array, $filter_array);
                              //return count($filter_array);
                              return (count($intersection)) === count($filter_array);
                              });
   
        $array['data']=array_values($filtered_array);
                              }
                              else
                              {
                              $array['data']=$data;
                              }
                              }
                           
                  echo  json_encode($array);
    }
               function getHolidayList(){
                              $empId=$_POST['empId'];
                              $adminDetails = $this->login_model->get_employee_login_admin_info($empId,TABLE_EMP);
                              $result =$this->db->query("SELECT h.holiday as holiday,DATE_FORMAT(h.holidayDate,'%W') as day,DATE_FORMAT(h.holidayDate,'%d')as date,DATE_FORMAT(h.holidayDate,'%b')as month from tbl_mst_holiday h left join tbl_mst_holiday_region_client hr on h.id=hr.holiday left join tbl_region r on r.id=hr.region where DATE_FORMAT(h.holidayDate,'%Y')='".date('Y')."' and  hr.stateId = '".$adminDetails['State_Id']."'  group by h.id order by h.holidayDate Asc")->result_array();
                              $array=array();
                              $array['response']="success";
                              $array['data']=$result;
               echo json_encode($array);
               }
 
function dashBoard(){
                              $array=array();
                              $empId=$_POST['empId'];
                              $empPassword=md5($_POST['empPassword']);
                              $statusSql =$this->db->query("SELECT tbl_emp_master.isActive,tbl_emp_master.empPassword  FROM tbl_emp_master WHERE empId = '".$empId."' ")->result_array();
                              //$imgSql=$this->db->query("SELECT mobileattendance.img FROM mobileattendance WHERE empId = '".$empId."' ORDER BY mobileattendance.attendanceDatetime DESC LIMIT 1 ")->result_array();
                              $imgSql=$this->db->query("SELECT tbl_emp_master.empImage as img FROM tbl_emp_master WHERE tbl_emp_master.empId = '".$empId."' ")->result_array();
                              $mobileimgSql=$this->db->query("SELECT emp_img_tbl.empImgUrl as img FROM emp_img_tbl WHERE emp_img_tbl.empId = '".$empId."' ")->result_array();
                              $selectUserPic=$this->db->query("SELECT COUNT(emp_img_tbl.id) as rowId  FROM emp_img_tbl WHERE emp_img_tbl.empId = '".$empId."' ")->result_array();
         $checkUserPic= $selectUserPic['0']['rowId'];
         $attCountSql=$this->db->query("SELECT COUNT(mobileattendance.id) totalAttendance FROM mobileattendance WHERE mobileattendance.attendanceDatetime LIKE '%".date('Y-m-d')."%' AND mobileattendance.empId='".$empId."' ")->result_array();
         $todayAttCount= $attCountSql['0']['totalAttendance'];
                              if($statusSql){
                                             $array['response']="success";
                                             foreach($statusSql as $row){
                                                            $array['status']=$row['isActive'];
                                                            $array['password']=($row['empPassword']==$empPassword)? 'true' : 'false';
                                                            $array['todayAttCount']=$todayAttCount;
                                             }
                                             if($imgSql){
                                                            foreach($imgSql as $rows){
                                                                           if($checkUserPic){
                                                                                          $imgMobile=$mobileimgSql['0']['img'];
                                                                                          $array['img']=base_url().'uploads/'.$imgMobile;
                                                                           }else{
                                                                                          if($rows['img']){
                                                                                          //$array['img']=base_url().'uploads/'.$rows['img'];
                                                                                          $array['img']=base_url().'uploads/candidateDocument/empImage/'.$rows['img'];
                                                                                          }else{
                                                                                                         $array['img']='';
                                                                                          }
                                                                           }
                                                                       
                                                            }
                                             }else{
                                                            $array['img']='';
                                             }
                              }else{
                                             $array['response']="fail";
                              }
                              echo json_encode($array);
                              die;  
               }
  /*  function getleavedata()
               {
            
               $array=array();
               $result=array();
               $empId=$_POST['empId'];
               //$empId="10005";
               $data['result'] = $this->attendance_model->empavailableLeave($empId);
               $data['service'] = $this->employee_model->get_emp_service_data($empId);
            
                                             $doj=$data['service']['empDOJ'];
                                             //unset($user_service['doj']);
                                             $leaveGroup=$data['service']['leaveGroup'];
                                             $get_no_leave_data=$this->employee_model->get_no_leave_data($leaveGroup);
                                             //echo $this->db->last_query();
                                             //die;
                                             $get_emp_leave_data=$this->employee_model->get_emp_leave_data($empId);
                                             //pre($get_no_leave_data);
                                             //die;
               $year=date('Y');
               $last_year=date('Y-12-31',strtotime($year));
    $start    = (new DateTime($doj));
$end      = (new DateTime($last_year));
$interval = DateInterval::createFromDateString('1 month');
$period   = new DatePeriod($start, $interval, $end);
foreach ($period as $dt) {
    $month_count[]=$dt->format("Y-m") . "<br>\n";
};
if(date('Y')==date('Y',strtotime($doj)))
{
//$no_month=count($month_count);
if(date('d',strtotime($data['doj']))<=15){
               $no_month=count($month_count);
}else if($data['doj']<=date("Y-01-01")){
               $no_month=12;
}else{
$no_month=(count($month_count)-1);
}
}
else
{
$no_month=12;
}
foreach($get_emp_leave_data as $row )
{          
$array[$row['leaveType']]=$row['balanceLeave'];
//$array[$row['name']]=round((($row['noOfleave']/12)*$no_month),0);
//$this->parent_model->query_insert("tbl_emp_leave_balance", $data_leave_balance);
}
foreach($array as $row=>$value)
{
$result[$row]=$value;
}
$result['response']='success';
foreach($get_no_leave_data as $row=>$value)
{
$applied=$this->db->query("SELECT sum(noofdays) as applied FROM `tbl_regularization` WHERE requestFrom='".$empId."' AND leaveType='".$value['name']."' AND status='P'")->row_array();
//$approved=$this->db->query("SELECT sum(noofdays) as approved FROM `tbl_regularization` WHERE requestFrom='10006' AND leaveType='".$value['name']."' AND status='A'")->row_array();
//$rejected=$this->db->query("SELECT sum(noofdays) as rejected FROM `tbl_regularization` WHERE requestFrom='10006' AND leaveType='".$value['name']."' AND status='R'")->row_array();
$result['data'][$row]=$value;
$result['data'][$row]['Opening']=(string)round((($value['noOfleave']/12)*$no_month),0);
$result['data'][$row]['applied']=($applied['applied']!=""?(string)$applied['applied']:"0");
//$result['data'][$row]['approved']=$applied['approved'];
//$result['data'][$row]['rejected']=$applied['rejected'];
//$result['data']['openingleave']=$value;
//$result['data']['more']='balance';
//$result['data'][$row]=$result[row];
}
$i=0;
foreach($get_emp_leave_data as $row )
{
//$new[]=$this->array_push_assoc($result['data'][$i],"TotalBalance",$row['balanceLeave']);
//$i++;
for($k=0;$k<count($result['data']);$k++)
{
if($row['leaveType']==$result['data'][$k]['name'])
{
$new[]=$this->array_push_assoc($result['data'][$k],"TotalBalance",$row['balanceLeave']);
}
}
}
for($j=0;$j<count($new);$j++)
{
$new_more[]=$this->array_push_assoc($new[$j],'Closed',(string)($new[$j]['Opening']-$new[$j]['TotalBalance']));
}
if($new_more)
{
$result['data']=$new_more;
}
else
{
$result['data']=array();
}
echo json_encode($result);
               //leave calculation
             
               } */
               //new calculation 6-Aug-18
               /*  function getleavedata()
               {
            
               $array=array();
               $result=array();
               $empId=$_POST['empId'];
               //$empId="10005";
               //$data['result'] = $this->attendance_model->empavailableLeave($empId);
            
               $data['service'] = $this->employee_model->get_emp_service_data($empId);
            
                                             $doj=$data['service']['empDOJ'];
                                             //unset($user_service['doj']);
                                             $leaveGroup=$data['service']['leaveGroup'];
                                             $get_no_leave_data=$this->employee_model->get_no_leave_data($leaveGroup);
                                          
                                             $get_emp_leave_data=$this->employee_model->get_emp_leave_data($empId);
                           
               $year=date('Y');
               $last_year=date('Y-12-31',strtotime($year));
    $start    = (new DateTime($doj));
               $start->modify('first day of this month');
    $end      = (new DateTime($last_year));
$interval = DateInterval::createFromDateString('1 month');
$period   = new DatePeriod($start, $interval, $end);
foreach ($period as $dt) {
    $month_count[]=$dt->format("Y-m") . "<br>\n";
};
if(date('Y')==date('Y',strtotime($doj)))
{
if(date('d',strtotime($doj))<=15){
               $no_month=count($month_count);
}else if(date('d',strtotime($doj))>15){
$no_month=(count($month_count)-0.5);
}
else if($doj<=date("Y-01-01")){
               $no_month=12;
}
}
else
{
$no_month=12;
}
foreach($get_emp_leave_data as $row )
{          
$array[$row['leaveType']]=$row['balanceLeave'];
//$array[$row['name']]=round((($row['noOfleave']/12)*$no_month),0);
//$this->parent_model->query_insert("tbl_emp_leave_balance", $data_leave_balance);
}
foreach($array as $row=>$value)
{
$result[$row]=$value;
}
$result['response']='success';
foreach($get_no_leave_data as $row=>$value)
{
$applied=$this->db->query("SELECT sum(noofdays) as applied FROM `tbl_regularization` WHERE requestFrom='".$empId."' AND leaveType='".$value['name']."' AND status='P'")->row_array();
//$approved=$this->db->query("SELECT sum(noofdays) as approved FROM `tbl_regularization` WHERE requestFrom='10006' AND leaveType='".$value['name']."' AND status='A'")->row_array();
//$rejected=$this->db->query("SELECT sum(noofdays) as rejected FROM `tbl_regularization` WHERE requestFrom='10006' AND leaveType='".$value['name']."' AND status='R'")->row_array();
//$result['data'][$row]=$value;
//$result['data'][$row]['Opening']=(string)round((($value['noOfleave']/12)*$no_month),0);
$result['data'][$row]=$value;
$val8=((($value['noOfleave']/12)*$no_month));
$whole = floor($val8);      // 1
$fraction = (($val8 - $whole)); // .25
$fraction =bcdiv($fraction, 1,1);; // .25
if($fraction==0.5)
{
$result['data'][$row]['Opening']=(string)bcdiv((($value['noOfleave']/12)*$no_month),1,1);
}
else
{
$result['data'][$row]['Opening']=(string)round((($value['noOfleave']/12)*$no_month));
}
$result['data'][$row]['applied']=($applied['applied']!=""?(string)$applied['applied']:"0");
//$result['data'][$row]['approved']=$applied['approved'];
//$result['data'][$row]['rejected']=$applied['rejected'];
//$result['data']['openingleave']=$value;
//$result['data']['more']='balance';
//$result['data'][$row]=$result[row];
}
//sahi hai yaha tak 3-Aug-18
// pre($result);
//pre($get_emp_leave_data);
// die;
$i=0;
foreach($get_emp_leave_data as $row )
{
for($k=0;$k<count($result['data']);$k++)
{
if($row['leaveType']==$result['data'][$k]['name'])
{
$new[]=$this->array_push_assoc($result['data'][$k],"TotalBalance",$row['balanceLeave']);
}
}
//$i++;
}
for($j=0;$j<count($new);$j++)
{
$new_more[]=$this->array_push_assoc($new[$j],'Closed',(string)($new[$j]['Opening']-$new[$j]['TotalBalance']));
}
if($new_more)
{
$result['data']=$new_more;
}
else
{
$result['data']=array();
}
echo json_encode($result);
               //leave calculation
             
               } */
               //new calculation 6-Aug-18
               //push array with key
               //new 28-Aug-18
               function getleavedata()
               {
            
               $array=array();
               $result=array();
               $empId=$_POST['empId'];
               //$empId="10005";
               $data['result'] = $this->attendance_model->empavailableLeave($empId);

               /* pre($data['result']);
               echo $this->db->last_query();
               die;  */

               $data['service'] = $this->employee_model->get_emp_service_data($empId);
            
           
                                             $doj=$data['service']['empDOJ'];
                                             //unset($user_service['doj']);
                                             $leaveGroup=$data['service']['leaveGroup'];
                                             $get_no_leave_data=$this->employee_model->get_no_leave_data($leaveGroup);
                                             /* echo $this->db->last_query();
                                             die;  */
                                             /*$get_emp_leave_data=$this->employee_model->get_emp_leave_data($empId);
                                              */
                                             /* echo $this->db->last_query();
                                             die; */
                                             /* pre($get_no_leave_data);
                                             die; */

                                             /*echo json_encode($data);
               die;*/
               $year=date('Y');
               $last_year=date('Y-12-31',strtotime($year));
    $start    = (new DateTime($doj));
               $start->modify('first day of this month');
    $end      = (new DateTime($last_year));
$interval = DateInterval::createFromDateString('1 month');
$period   = new DatePeriod($start, $interval, $end);
foreach ($period as $dt) {
    $month_count[]=$dt->format("Y-m") . "<br>\n";
};
if(date('Y')==date('Y',strtotime($doj)))
{
if(date('d',strtotime($doj))<=15){
               $no_month=count($month_count);
}else if(date('d',strtotime($doj))>15){
$no_month=(count($month_count)-0.5);
}
else if($doj<=date("Y-01-01")){
               $no_month=12;
}
}
else
{
$no_month=12;
}
/* echo $no_month;
die; */
foreach($data['result'] as $row )
{          
$array[$row['leaveType']]=$row['balanceLeave'];
//$array[$row['name']]=round((($row['noOfleave']/12)*$no_month),0);
//$this->parent_model->query_insert("tbl_emp_leave_balance", $data_leave_balance);
}
foreach($array as $row=>$value)
{
$result[$row]=(String)$value;
}
$result['response']='success';
/* pre($result);
die; */
foreach($data['result'] as $row=>$value)
{
$applied=$this->db->query("SELECT sum(noofdays) as applied FROM `tbl_regularization` WHERE requestFrom='".$empId."' AND leaveType='".$value['leaveType']."' AND status='P' and YEAR(fromDate)='".date('Y')."' ")->row_array();
//$data['result']=$applied;
//$approved=$this->db->query("SELECT sum(noofdays) as approved FROM `tbl_regularization` WHERE requestFrom='10006' AND leaveType='".$value['name']."' AND status='A'")->row_array();
//$rejected=$this->db->query("SELECT sum(noofdays) as rejected FROM `tbl_regularization` WHERE requestFrom='10006' AND leaveType='".$value['name']."' AND status='R'")->row_array();
//$result['data'][$row]=$value;
//$result['data'][$row]['Opening']=(string)round((($value['noOfleave']/12)*$no_month),0);
if($value['leaveType']=='CL'){
     $value['fullname']='Casual Leave';
}else if($value['leaveType']=='SL'){
     $value['fullname']='Sick Leave';
}else{
     $value['fullname']='Earned Leave';
}

$value['name']=(String)$value['leaveType'];
$value['applied']=$applied;
$result['data'][$row]=$value;
$val8=((($value['noOfleave']/12)*$no_month));
$whole = floor($val8);      // 1
$fraction = (($val8 - $whole)); // .25
$fraction =bcdiv($fraction, 1,1);; // .25
if($fraction==0.5)
{
$result['data'][$row]['Opening']=(string)bcdiv((($value['noOfleave']/12)*$no_month),1,1);
}
else
{
$result['data'][$row]['Opening']=(string)round((($value['noOfleave']/12)*$no_month));
}
$result['data'][$row]['applied']=($applied['applied']!=""?(string)$applied['applied']:"0");
//$result['data'][$row]['approved']=$applied['approved'];
//$result['data'][$row]['rejected']=$applied['rejected'];
//$result['data']['openingleave']=$value;
//$result['data']['more']='balance';
//$result['data'][$row]=$result[row];
}
//sahi hai yaha tak 3-Aug-18
/* pre($result);
pre($get_emp_leave_data); */
/* die; */
$i=0;
foreach($data['result'] as $row )
{
for($k=0;$k<count($result['data']);$k++)
{
if(str_replace(' ','',$row['leaveType'])==str_replace(' ','',$result['data'][$k]['leaveType']))
{
$new[]=$this->array_push_assoc($result['data'][$k],"TotalBalance",(String)$row['balanceLeave']);
}
}
//$i++;
}
for($j=0;$j<count($new);$j++)
{
if($new[$j]['Opening']=='0'){
          $new_more[]=$this->array_push_assoc($new[$j],'Closed','0'); 
     }else{
         $new_more[]=$this->array_push_assoc($new[$j],'Closed',(string)($new[$j]['Opening']-$new[$j]['TotalBalance'])); 
     }
}
if($new_more)
{
$result['data']=$new_more;
}
else
{
$result['data']=array();
}
/* pre($result);
die; */
echo json_encode($result);
               //leave calculation
             
               }
               //new 28-Aug-18
function array_push_assoc($array, $key, $value){
$array[$key] = $value;
return $array;
}
               //push array with key   
               public function availableLeave(){
                              $emp_id=$_POST['empId'];
                              $data['response']='success';
                              $data['data'] = $this->attendance_model->leaveTypeListforemp($emp_id);
                              //if $data['data']
                              $array=array();
                              $array['data']=$data;
                              echo json_encode($data);
               }
            
               public function historyData(){
                              $array=array();
                              $empId=$_POST['empId'];
                              $statusSql =$this->db->query("SELECT tbl_reg.id,tbl_reg.leaveType,tbl_reg.regularizationType,tbl_reg.fromDate,tbl_reg.toDate,tbl_reg.noofdays,tbl_reg.remarks as comments,tbl_reg.regularizationDate,tbl_reg.status,tbl_reg.cancelled_status FROM tbl_regularization As tbl_reg WHERE tbl_reg.requestFrom ='".$empId."' AND tbl_reg.regularizationType in ('L','FHD','SHD') ORDER BY tbl_reg.regularizationDate DESC ")->result_array();
                              if($statusSql){
                                             $i=0;
                                             foreach($statusSql as $row){
                                                             $arr[$i]['id']=$row['id'];
                                                            $arr[$i]['leaveType']=$row['leaveType'];
                                                            $arr[$i]['fromDate']=$row['fromDate'];
                                                            $arr[$i]['toDate']=$row['toDate'];
                                                            $arr[$i]['regularizationType']=$row['regularizationType'];
$arr[$i]['cancelled_status']=$row['cancelled_status'];
                                                                                                                                                                                                                                 $arr[$i]['noofdays']=(String)($row['noofdays']);
                                                            //$arr[$i]['noofdays']=(String)($row['noofdays']);
                                                                                                                                                                                                                                 if($row['regularizationType']=='FHD'){
                                                                $arr[$i]['leaveDayType']='First Half';
                                                            }else if($row['regularizationType']=='SHD'){
                                                                 $arr[$i]['leaveDayType']='Second Half';
                                                            }else{
                                                            $arr[$i]['leaveDayType']='Full Day';
                                                            }
                                                          
                                                            $arr[$i]['comments']=$row['comments'];
                                                            $arr[$i]['regularizationDate']=$row['regularizationDate'];
                                                            $time=strtotime($row['regularizationDate']);
                                                            $month=date("M",$time);
                                                            $date=date("d",$time);
                                                            $arr[$i]['Date']=$date;
                                                            $arr[$i]['Month']=$month;
                                                                                                                                                                                                                                 //$arr[$i]['status']=$row['status'];
                                                             if($row['status']=='P'){
                                                                           $arr[$i]['status']='Pending';
                                                            }else if($row['status']=='A'){
                                                                           $arr[$i]['status']='Approved';
                                                            }else if($row['status']=='R'){
                                                                           $arr[$i]['status']='Declined';
                                                            }
                                                            $i++;
                                             }
                                             $array['response']="success";
                                             $array['data']=$arr;
                              }
                              else{
                                             $array['response']="fail";
                              }
                           
                              echo json_encode($array);die;
               }
              function attendanceLog(){
                              $array=array();
                              $month=$_POST['month'];
                   $year=$_POST['year'];
                   $empId=$_POST['empId'];
                                                               
                              //echo json_encode($empId.' '.$year.' '.$month);die();
                                                                                                        
                                                                                                           $param1=$_POST['month'];
                   $param2=$_POST['year'];
                   $param3=$_POST['empId'];
                   $param4=$_POST['type'];
                              $param1=date("m",strtotime( $param1));
                              /* $param1="06";
                              $param2="2018";
                              $param3="10005";
                              $param4="P"; */
        $monthName = date("F", mktime(0, 0, 0, $param1, 10));
                              $result=$this->db->query("SELECT DAY(LAST_DAY(DATE_FORMAT(attendanceDate,'%Y%m%d'))) as workingday, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus !='A' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalpresent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='R' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as resign,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='NJ' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as newjoin,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HD' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfday,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HL' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfdayleave,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='LC' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as latecoming , (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='A'  and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId ) as totalabsent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='L' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalleave,    concat(e.empTitle,' ',e.empFname,' ',e.empLname) as emp_name, a.empId, group_concat(date_format(attendanceDate,'%d')) as d, max(date_format(attendanceDate,'%d')) as maxday,  GROUP_CONCAT(a.attendanceStatus) as t, GROUP_CONCAT(DATE_FORMAT(a.inTime,'%H:%i:%s')) as ti, GROUP_CONCAT(DATE_FORMAT(a.outTime,'%H:%i:%s')) as outTime, GROUP_CONCAT(DATE_FORMAT(a.workingHours,'%H:%i')) as workingHours FROM `tbl_emp_attendance` a inner join tbl_emp_master e on a.empId=e.empId where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' AND a.empId='".$param3."'   group by e.empId ")->result_array();
                              //$querySql =$this->db->query("SELECT morAtt.lat as morLat,morAtt.log as morLog,morAtt.img as morImg,TIME(morAtt.createdOn) as morTime,eveAtt.lat as eveLat,eveAtt.log as eveLog,eveAtt.img as eveImg,TIME(eveAtt.createdOn) as eveTime,YEAR(mobAtt.createdOn) as year,MONTHNAME(mobAtt.createdOn) as month,DAYOFMONTH(mobAtt.createdOn) as dayOfMonth,DAYNAME(mobAtt.createdOn) as dayName FROM mobileattendance AS mobAtt LEFT JOIN mobileattendance as morAtt on Date(morAtt.createdOn)=DATE(mobAtt.createdOn) and morAtt.type=1 AND morAtt.empId='".$empId."'  LEFT JOIN mobileattendance as eveAtt on DATE(eveAtt.createdOn)=DATE(mobAtt.createdOn) and eveAtt.type=2  AND eveAtt.empId='".$empId."'  WHERE morAtt.empId='".$empId."' and YEAR(mobAtt.createdOn)='".$year."' and MONTHNAME(mobAtt.createdOn)='".$month."'  group by morAtt.id")->result_array();
                                $imgSql=$this->db->query("SELECT tbl_emp_master.empImage as img FROM tbl_emp_master WHERE tbl_emp_master.empId = '".$empId."' ")->result_array();                                                                         
                                                                                                                        $querySql =$this->db->query("SELECT morAtt.lat as morLat,morAtt.log as morLog,morAtt.img as morImg,TIME(morAtt.createdOn) as morTime,eveAtt.lat as eveLat,eveAtt.log as eveLog,eveAtt.img as eveImg,TIME(eveAtt.createdOn) as eveTime,YEAR(mobAtt.createdOn) as year,DATE_FORMAT(mobAtt.createdOn, '%b') as month,DAYOFMONTH(mobAtt.createdOn) as dayOfMonth,DAYNAME(mobAtt.createdOn) as dayName,morAtt.attLocTyp as morLocTyp ,eveAtt.attLocTyp as eveLocTyp FROM mobileattendance AS mobAtt LEFT JOIN mobileattendance as morAtt on Date(morAtt.createdOn)=DATE(mobAtt.createdOn) and morAtt.type=1 AND morAtt.empId='".$empId."'  LEFT JOIN mobileattendance as eveAtt on DATE(eveAtt.createdOn)=DATE(mobAtt.createdOn) and eveAtt.type=2  AND eveAtt.empId='".$empId."'  WHERE morAtt.empId='".$empId."' and YEAR(mobAtt.createdOn)='".$year."' and MONTHNAME(mobAtt.createdOn)='".$month."'  group by morAtt.id ORDER BY mobAtt.createdOn desc")->result_array();
                                                                                                                      
                              if($querySql){
                                             $i=0;
                                             foreach($querySql as $row){
                                                            $arr[$i]['empId']=(string)$row['empId'];
                                                            $arr[$i]['morLat']=(string)$row['morLat'];
                                                            $arr[$i]['morLog']=(string)$row['morLog'];
                                                                                                                                                                                                                                 /*  $arr[$i]['morImg']="";
                                                                                                                                                                                                                                 $arr[$i]['eveImg']=""; */
                                                           if($row['morImg']){
                                                                           //$arr[$i]['morImg']=base_url().'uploads/'.(string)$row['morImg'];
                                                            if($imgSql){
               foreach($imgSql as $rows){                                                                                                                                                                                                                                                                                        if($rows['img']){                                                                                                                                                                                                                                                                                              $arr[$i]['morImg']=base_url().'uploads/candidateDocument/empImage/'.$rows['img'];
}else{                                                                                                                                                                                                                                                                                   
$arr[$i]['morImg']='';
               }
  }
}
                                                                                                                                                                                                                                 }else{
                                                                           $arr[$i]['morImg']=(string)$row['morImg'];
                                                            }
                                                            $arr[$i]['morTime']=(string)$row['morTime'];
                                                            $arr[$i]['eveLat']=(string)$row['eveLat'];
                                                            $arr[$i]['eveLog']=(string)$row['eveLog'];
                                                            if($row['eveImg']){
                                                                           //$arr[$i]['eveImg']=base_url().'uploads/'.(string)$row['eveImg'];
                                                                                                                                                                                                                                                                                $arr[$i]['eveImg']=$arr[$i]['morImg'];
                                                            }else{
                                                                           $arr[$i]['eveImg']=(string)$row['eveImg'];
                                                            }
                                                            $arr[$i]['eveTime']=(string)$row['eveTime'];
                                                            if((string)$row['eveTime'] && (string)$row['morTime']){
                                                                           $arr[$i]['totalTime']=date_create((string)$row['morTime'])->diff(date_create((string)$row['eveTime']))->format('%H:%I:%s');;
                                                            }else{
                                                                           $arr[$i]['totalTime']='00:00:00';
                                                            }
                                                            $arr[$i]['year']=(string)$row['year'];
                                                            $arr[$i]['month']=(string)$row['month'];
                                                            $arr[$i]['dayOfMonth']=(string)$row['dayOfMonth'];
                                                            $arr[$i]['dayName']=(string)$row['dayName'];
                                                                                                                                                                                                                                 if((string)$row['morLocTyp']=='1'){
                                                                                                                                                                                                                                                $arr[$i]['morLocTyp']='Home';
                                                                                                                                                                                                                                 }else if((string)$row['morLocTyp']=='2'){
                                                                                                                                                                                                                                                $arr[$i]['morLocTyp']='On Duty';
                                                                                                                                                                                                                                 }else if((string)$row['morLocTyp']=='3'){
                                                                                                                                                                                                                                                $arr[$i]['morLocTyp']='Office';
                                                                                                                                                                                                                                 }else{
                                                                                                                                                                                                                                                $arr[$i]['morLocTyp']='';
                                                                                                                                                                                                                                 }
                                                                                                                                                                                                                                 if((string)$row['eveLocTyp']=='1'){
                                                                                                                                                                                                                                                $arr[$i]['eveLocTyp']='Home';
                                                                                                                                                                                                                                 }else if((string)$row['eveLocTyp']=='2'){
                                                                                                                                                                                                                                                $arr[$i]['eveLocTyp']='On Duty';
                                                                                                                                                                                                                                 }else if((string)$row['eveLocTyp']=='3'){
                                                                                                                                                                                                                                                $arr[$i]['eveLocTyp']='Office';
                                                                                                                                                                                                                                 }else{
                                                                                                                                                                                                                                                $arr[$i]['eveLocTyp']='';
                                                                                                                                                                                                                                 }
                                                            $i++;
                                             }
                                             $array['response']="success";
                                                                                                                                                                     if($result[0]['workingday']!=null)
                              {
                              $array['day']=$result[0]['workingday'];
                              }
                              else
                              {
                              $array['day']="NA";
                              }
                              if($result[0]['totalpresent']!=null)
                              {
                              $array['present_days']=$result[0]['totalpresent'];
                              }
                              else
                              {
                              $array['present_days']="NA";
                              }
                              if($result[0]['totalhalfday']!=null)
                              {
                              $array['halfday']=$result[0]['totalhalfday'];
                              }
                              else
                              {
                              $array['halfday']="NA";
                              }
                              if($result[0]['workingday'])
                              {
                              $array['header']="1st"."-".$result[0]['workingday'].($result[0]['workingday']=="30"?"th":"st")." ".$monthName;
                              }
                              else
                              {
                              $d=cal_days_in_month(CAL_GREGORIAN,$param1,$param2);
                              $array['header']="1st"."-".$d.($d=="30"?"th":"st")." ".$monthName;
                              }
                              if($result[0]['totalleave']!=null)
                              {
                              $array['leave']=$result[0]['totalleave'];
                              }
                              else
                              {
                              $array['leave']="NA";
                              }
                              if($result[0]['totalabsent']!=null)
                              {
                              $array['absent']=$result[0]['totalabsent'];
                              }
                              else
                              {
                              $array['absent']="NA";
                              }
                                             $array['data']=$arr;
                              }else{
                                             $array['response']="fail";
                                                                                                                                                                     if($result[0]['workingday']!=null)
                              {
                              $array['day']=$result[0]['workingday'];
                              }
                              else
                              {
                              $array['day']="NA";
                              }
                              if($result[0]['totalpresent']!=null)
                              {
                              $array['present_days']=$result[0]['totalpresent'];
                              }
                             else
                              {
                              $array['present_days']="NA";
                              }
                              if($result[0]['totalhalfday']!=null)
                              {
                              $array['halfday']=$result[0]['totalhalfday'];
                              }
                              else
                              {
                              $array['halfday']="NA";
                              }
                              if($result[0]['workingday'])
                              {
                              $array['header']="1st"."-".$result[0]['workingday'].($result[0]['workingday']=="30"?"th":"st")." ".$monthName;
                              }
                              else
                              {
                              $d=cal_days_in_month(CAL_GREGORIAN,$param1,$param2);
                              $array['header']="1st"."-".$d.($d=="30"?"th":"st")." ".$monthName;
                             }
                              if($result[0]['totalleave']!=null)
                              {
                              $array['leave']=$result[0]['totalleave'];
                              }
                              else
                              {
                              $array['leave']="NA";
                              }
                             if($result[0]['totalabsent']!=null)
                              {
                              $array['absent']=$result[0]['totalabsent'];
                              }
                              else
                              {
                              $array['absent']="NA";
                              }
                                           
                              }
                              echo json_encode($array);
               }
//new calulation for api aditya 17-sep-2018 Leave Request
                              function getnoofdaysleave($startdate="",$enddate="",$empId="")
                              {
                              if($startdate AND $enddate)
                              {
                              $start = $current = strtotime($startdate);
                              $end = strtotime($enddate);
                              $empId = $empId;
                              }
                              else
                              {
                            
                              $data=$this->input->post();
                              $start = $current = strtotime($data['date1']);
                              $end = strtotime($data['date2']);
                              }
                              //print_r($current);die;
                              while ($current <= $end) {
                                             $dates[] = date('Y-m-d', $current);
                                             $current = strtotime('+1 days', $current);
                              }
               //print_r($dates);
                              $dayNames = array(0=>'sunday', 1=>'monday', 2=>'tuesday', 3=>'wednesday', 4=>'thursday', 5=>'friday', 6=>'saturday', 7=>'Any One in Week');
                                             $week = array( 1=>'first', 2=>'second', 3=>'third', 4=>'fourth', 5=>'fifth');
                                             $doff=array();
                                             $doff1=array();
                                             $sql1  ="select effectiveDate,leaveGroup from ".TABLE_SERVICE." where empId='".$empId."'";
                                             $result1 = $this->db->query($sql1)->result_array();
                                             if($result1['0']['effectiveDate']> date('Y-m-d')){
                                             $sql2  ="select leaveGroup from tbl_leavegrouplog where empId='".$empId."' order by id DESC ";
                                             $result2 = $this->db->query($sql2)->result_array();
                                             $leavG = $result2['0']['leaveGroup'];
                                             }else{
                                             $leavG = $result1['0']['leaveGroup'];
                                             }          
                              $sql2="select e.empId, r.id as region,State_Id from tbl_emp_master e left join tbl_mst_city c on e.jobLocation=c.cityId left join tbl_mst_state s on c.state=s.State_Id left join tbl_region r on s.region=r.id WHERE 1=1 and e.isActive=1 and e.empId='".$empId."'";   
                              $result2 = $this->db->query($sql2)->result_array();
                              $holidays = $this->attendance_model->todayempHolidayList($result2[0]['State_Id'],0, "");
                           
                              $weekoff = $this->master_model->getempweekoff($leavG);
            
                              $dayoff = $this->master_model->getempdayoff($leavG);
                           
                              $weekNumber=array();
                              //print_r($dates);die;
                              for($i=0;$i<count($dates); $i++)
                              {
                              $weekDay= date('w', strtotime($dates[$i]));
                              if($weekDay=='6')
                              {
                              $weekofMonth=$this->weekOfMonth($dates[$i]);
                              if($weekofMonth==1 or $weekofMonth==2 or $weekofMonth==3)
                              {
                              $weekNumber[]= $dates[$i];
                              }
                              }
                              else if($weekDay=='0')
                              {
                              $weekNumber[]= $dates[$i];
                              }
                              }
                              foreach($holidays as $row)
                              {
                              $holidays_dates[]=$row['holidayDate'];
                              }
                              //print_r($holidays_dates);die;
                              if($startdate AND $enddate)
                              {
                            
                              return array_values((array_diff(array_diff($dates,$weekNumber),$holidays_dates)));
                              }
                              else
                              {
                           
                              if(count(array_diff(array_diff($dates,$weekNumber),$holidays_dates))!=0 AND $this->input->post('regularizationType1')=='SHD' OR $this->input->post('regularizationType1')=='FHD')
                              {
                              echo (float)0.5;
                              }
                              else
                              {
                               echo count(array_diff(array_diff($dates,$weekNumber),$holidays_dates));
                              }
                              }
                              }
                              //4-Sep-18
               function weekOfMonth($date) {
    list($y, $m, $d) = explode('-', date('Y-m-d', strtotime($date)));
    $w = 1;
    for ($i = 1; $i <= $d; ++$i) {
        if ($i > 1 && date('w', strtotime("$y-$m-$i")) == 0) {
            ++$w;
        }
    }
    return $w;
}
function getleaveavailibility($empId="",$leaveType="")
                              {
                              $getprevdata=$this->db->query('Select sum(noofdays) as noofdays  from tbl_regularization where requestFrom="'.$empId.'" AND leaveType="'.$leaveType.'" AND status!="R" and YEAR(fromDate)="'.date('Y').'" ')->row_array();
                              $getopeningleave=$this->db->query('Select opening   from tbl_emp_leave_balance where empId="'.$empId.'" AND leaveType="'.$leaveType.'"  ')->row_array();
                              return ($getopeningleave['opening']-$getprevdata['noofdays']);
                              }
//new calulation for api aditya 17-sep-2018 Leave Request        
  
function empImageChange(){
                              $attendanceDatetime=date('Y-m-d H:i:s');
                              $empId=$_POST['empId'];
                              $empImgUrl=$_POST['empImgUrl'];
                              //$createdDate;
        $arr=array();
        $arr['empId']=$empId;
       $arr['createdDate']=$attendanceDatetime;
        $imgName = rand() . '_' . time() .'_'.$arr['empId'].'.jpg';
        $decoded = base64_decode($empImgUrl);
        file_put_contents(FCPATH . 'uploads/' . $imgName, $decoded);                                
        $arr['empImgUrl'] = $imgName;
        $selectUserPic=$this->db->query("SELECT COUNT(emp_img_tbl.id) as rowId  FROM emp_img_tbl WHERE emp_img_tbl.empId = '".$empId."' ")->result_array();
        $checkUserPic= $selectUserPic['0']['rowId'];
        if($checkUserPic){
                $updateUserPic=$this->db->query("UPDATE emp_img_tbl SET emp_img_tbl.empImgUrl ='".$imgName."',emp_img_tbl.createdDate='".$attendanceDatetime."' WHERE emp_img_tbl.empId ='".$empId."' ");
                if($this->db->affected_rows){
                              $arr['response']='success';
                                                            echo json_encode($arr);
                                                            die;
                }else{
                              $arr['response']='fail';
                                                           echo json_encode($arr);
                                                            die;
                }
        }else{
               $insertSql= $this->db->insert('emp_img_tbl',$arr);
                       if($insertSql){
                              $lastId=$this->db->insert_id();
                              $arr['response']='success';
                                                            echo json_encode($arr);
                                                            die;
                       }else{
                              $arr['response']='fail';
                                                            echo json_encode($arr);
                                                            die;
                       }
        }
    
       }
       function sendRegularizationRequest(){
                              $data= array();
                              $checkRegSql=$this->db->query("SELECT tbl_emp_attendance.status  FROM tbl_emp_attendance WHERE tbl_emp_attendance.id='".$this->input->post('id')."' AND tbl_emp_attendance.status='P' ")->num_rows();
                              if($checkRegSql){
                                   $data['response']='fail';
                                   $data['errorMsg']='You have already apply regularization';
                                   echo json_encode($data);
                                   die;
                              }
                              $result = $this->attendance_model->attendanceDetail($this->input->post('id'));
                          
                              $data['requestTo'] = $result['0']['reportingTo'];
                              //echo json_encode($data['requestTo']);die;
                              $data['parentId'] = $this->input->post('id');
                             $data['regularizationApplyfor'] = $this->input->post('regularizationApplyfor');
                              $data['requestFrom'] = $result['0']['empId'];
                              $data['status'] =  'P';
                              if($this->input->post('regularizationType')=='T'){
                                    $data['regularizationType']='M';
                              }else{
                                   $data['regularizationType'] =  $this->input->post('regularizationType');
                              }
                              //$data['regularizationType'] =  $this->input->post('regularizationType');
                              /*echo $data['regularizationType']. '  '. $data['regularizationApplyfor'];
                              die;*/

                              if($data['regularizationType']!='T')
                              {
                                                                                          $attDateSql=$this->db->query("SELECT tbl_emp_attendance.attendanceDate FROM tbl_emp_attendance WHERE tbl_emp_attendance.id='".$data['parentId']."'")->result_array();
                                             /*$data['fromDate'] = $this->input->post('attendanceDate');
                                             $data['toDate'] =  $this->input->post('attendanceDate');*/
                                             $data['fromDate'] = $attDateSql[0]['attendanceDate'];
                                             $data['toDate'] =  $attDateSql[0]['attendanceDate'];
 
                                             /* $data['fromDate'] =  $this->input->post('attendanceDate');
                                             $data['toDate'] =  $this->input->post('attendanceDate');*/
                              }
                              $data['regularizationDate'] = date('Y-m-d H:i:s');
                              $data['remarks'] =  $this->input->post('remarks');
                              $this->parent_model->query_insert(TABLE_REGULARIZATION, $data);
                              if($this->input->post('remarks'))
                              {
                                             $thread['regularizationId'] = $this->input->post('id');
                                             $thread['requestFrom'] = $this->input->post('empId');
                                             $thread['requestTo'] =  $result['0']['reportingTo'];
                                             $thread['remarks'] = $this->input->post('remarks');
                                             $thread['threadTime'] = date('Y-m-d H:i:s',time());
                                             $this->parent_model->query_insert('tbl_regularization_thread', $thread);
                              }
                              $data1['status'] = 'P';
                              $where=" ".id." ='".$this->input->post('id')."' ";
                              $this->parent_model->query_update(TABLE_ATTENDANCE, $data1, $where);
                                                            //send Notification
                              $subject     = 'Attendance Regularization Request: '.$this->input->post('empId').' for '.$result['0']['attendanceDate'];
                              $manager  = $result['0']['empFname'];

                              $empNameSql=$this->db->query("SELECT tbl_emp_master.empId,tbl_emp_master.empFname,empLname  FROM tbl_emp_master WHERE tbl_emp_master.empId ='".$this->input->post('empId')."' ")->result_array();
                              $empName=$empNameSql['0']['empFname'].' '.$empNameSql['0']['empLname'];

                              $title=$empName.' Regularization Request';//$this->input->post('empId');
                              $message='Attendance Date: '.$data['fromDate'];
                              $empTokenId=$data['requestTo'];
                         $tokenSql=$this->db->query("SELECT tbl_gcm_users.deviceId as deviceId FROM tbl_gcm_users WHERE tbl_gcm_users.empId = '".$empTokenId."' ")->result_array();
                          $token=$tokenSql['0']['deviceId'];
 
                          if($token){         
                            $API_SERVER_KEY = 'AAAAucVTDK4:APA91bE6T2wVEW1vL17C1Dm5Cizf_5TzkeM8Wb16TG41IE9uXAjvRk6Va9S4ntgdwKX9m6rqAHZH7rLAobyYslJFPTAHN4kcYU8OZrPRf7pPDvD-3PM3xQDvOIuPaFE6tJ1dY98CTc5M';
                            $is_background = "TRUE";
                            $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
                           $fields = array(
                                     'to' => $token,
                                     'notification' =>array('title' => $title, 'body' =>  $message ),
                                     'priority' =>'high',
                                      );
                                    $headers = array(
                                      'Authorization:key=' .$API_SERVER_KEY,
                                       'Content-Type:application/json'
                                      );
                                    $ch = curl_init();
                                                               // Set the url, number of POST vars, POST data
                                   curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
                                   curl_setopt($ch, CURLOPT_POST, true);
                                   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                   curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
                                   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                                   $result_curl_exec= curl_exec($ch);
                                   curl_close($ch);
                                   //echo json_encode($result);
                                                                                    
                         }
 
                              //echo json_encode($result);die;
                              if(MODE =='live'){
                                             $to = $result['0']['empEmailOffice'];
                                         
                              }else{
                                             $to = EMAILTO;
                                             $cc ="";
                              }
                              $rltype = array('T'=>'Meeting','FL'=>'Forgot Login','FLO'=>'Forgot Logout','FP'=>'Forgot Punch', 'M'=>'Meeting');
                              $reason = $rltype[$this->input->post('regularizationType')];
                              $body_txt    = " ".$empName." has sent Attendance Regularization Request with the below mentioned dates.
                              <table>
                                             <tr><td><b>Date</b></td><td>: ".date('d-F-Y',strtotime($result['0']['attendanceDate']))."</td></tr>
                                             <tr><td><b>In Time</b></td><td>: ".$result['0']['inTime']."</td></tr>
                                             <tr><td><b>Out Time</b></td><td>: ".$result['0']['outTime']."</td></tr>
                                             <tr><td><b>Regularization Apply For</b></td><td>: ".$this->input->post('regularizationApplyfor')."</td></tr>
                                             <tr><td><b>Reason</b>  </td><td>: ".$reason."</td></tr>
                                             <tr><td><b>Remarks</b></td><td>: ".$this->input->post('remarks')."</td></tr>
                                             <tr><td valign='middle'><b>Request Response</b>  </td><td colspan='2' valign='middle'><table cellspacing='0' cellpadding='0'><td><a href='".site_url('login')."' ><img src='".base_url()."/images/approve.png'></a></td><td><a href='".site_url('login')."'><img src='".base_url()."/images/decline.png'></a></td></table></td></tr>
                              </table>";
                              $data = array(
                                             'SITE_LOGO_URL' => base_url().SITE_IMAGEURL.'logo.png',
                                             'USER' => $manager,
                                             'SITE_NAME' => SITE_NAME,
                                             'MAIL_DATA'=>$body_txt
                                             );
                              $htmlMessage =  $this->parser->parse('emails/alert', $data, true);
                              if($this->myemail->sendEmail($to,$subject, $htmlMessage, ADMIN_EMAIL, ADMIN_NAME)){
                                             $arr=array();
                                                            $arr['response']='success';
                                                            echo json_encode($arr);
                                                            die;
                             }else{
                                             $arr=array();
                                                            $arr['response']='fail';
                                                            echo json_encode($arr);
                                                            die;
                              }
               }
               function mobileTokenIdInsert(){
                              $empId=$_POST['empId'];
                              $token=$_POST['token'];
                              $createdDate=date('Y-m-d H:i:s');
                              $arr=array();
                              $arr['empId']=$empId;
                              $arr['deviceId']=$token;
                              $arr['createdDate']=$createdDate;
                              $checkUser=$this->db->query("SELECT COUNT(tbl_gcm_users.id) as rowId  FROM tbl_gcm_users WHERE tbl_gcm_users.empId = '".$empId."' ")->result_array();
        $checkUserCount= $checkUser['0']['rowId'];
        if(!$checkUserCount){
               $insertSql= $this->db->insert('tbl_gcm_users',$arr);
               $arr['response']='success';
               echo json_encode($arr);
            die;
        }else{
                              $updateUserPic=$this->db->query("UPDATE tbl_gcm_users SET tbl_gcm_users.deviceId ='".$token."',tbl_gcm_users.createdDate='".$createdDate."' WHERE tbl_gcm_users.empId ='".$empId."' and tbl_gcm_users.deviceId != '".$token."' ");
                if($this->db->affected_rows){
                             $arr['response']='success';
                    echo json_encode($arr);
                    die;
                           
                }else{
                    $arr['response']='fail';
                    echo json_encode($arr);
                    die;
                }
               }
                              }
                              function myTeamView(){
                                             $array=array();
                                             $empId=$_POST['empId'];
                                             $queryMyTeam= $this->db->query("SELECT emp_master.empId,emp_master.empTitle,emp_master.empFname,emp_master.empMname,emp_master.empLname,emp_master.empDOJ,emp_master.empMobile,emp_master.empEmailOffice,dept_mst.name as deptName,mst_designation.name AS designationName,emp_master.status as status,city_mst.cityName AS jobLocation,emp_master.empImage as empImg FROM tbl_emp_master as emp_master LEFT JOIN tbl_mst_dept as dept_mst ON dept_mst.id=emp_master.empDept LEFT JOIN tbl_mst_designation AS mst_designation ON mst_designation.id=emp_master.empDesination LEFT JOIN tbl_mst_city as city_mst ON city_mst.cityId=emp_master.jobLocation WHERE emp_master.reportingTo ='".$empId."' and emp_master.status ='1' ")->result_array();
                                             $arr=array();
                              if($queryMyTeam){
                                             $i=0;
                                             foreach($queryMyTeam as $row){
                                                            $array[$i]['empId']=$row['empId'];
                                             if($row['empMname']){
                                                            $empMname=$row['empMname']." ";
                            }else{
                                                                           $empMname=$row['empMname'];
                            }
                              $array[$i]['empFullName']=$row['empTitle']." ".$row['empFname']." ".$empMname.$row['empLname'];
                              $array[$i]['empDOJ']=$row['empDOJ'];
                              $array[$i]['empMobile']=$row['empMobile'];
                              $array[$i]['empEmailOffice']=$row['empEmailOffice'];
                              $array[$i]['deptName']=$row['deptName'];
                              $array[$i]['designationName']=$row['designationName'];
                              $array[$i]['status']=$row['status'];
                              $array[$i]['jobLocation']=$row['jobLocation'];
                              $mobileimgSql=$this->db->query("SELECT emp_img_tbl.empImgUrl as img FROM emp_img_tbl WHERE emp_img_tbl.empId = '".$array[$i]['empId']."' ")->result_array();
                                                            $selectUserPic=$this->db->query("SELECT COUNT(emp_img_tbl.id) as rowId  FROM emp_img_tbl WHERE emp_img_tbl.empId = '".$array[$i]['empId']."' ")->result_array();
                                       $checkUserPic= $selectUserPic['0']['rowId'];
                                       if($checkUserPic){
                                                                                          $imgMobile=$mobileimgSql['0']['img'];
                                                                                          $array[$i]['empImg']=base_url().'uploads/'.$imgMobile;
                                                                           }
                                                            else{
                                                                                          if($row['empImg']){
                                                                                          $array[$i]['empImg'] =base_url().'uploads/candidateDocument/empImage/'.$row['empImg'];
                                                                                          }else{
                                                                                                         $array[$i]['empImg'] = "";
                                                                                          }
                                                                           }
                              $i++;
                              }
                                  $arr['response']='success';
                                  $arr['data']=$array;   
                     }else{
                           $arr['response']='fail';
                     }
                     echo json_encode($arr);
               }
function myTeamEmpWiseAtt(){
                              $array=array();
                              $empId=$_POST['empId'];
                              $dateYM=$_POST['dateYM'];
                              $attendanceHistroy=$this->db->query("SELECT empId,YEAR(attendanceDate) AS year,DAYOFMONTH(attendanceDate) as date,MONTHNAME(attendanceDate) as month,DAYNAME(attendanceDate) as dayName,inTime,outTime,workingHours,attendanceStatus,inlat,outlat FROM tbl_emp_attendance WHERE empId = '".$empId."' AND attendanceDate LIKE '%".$dateYM."%' ORDER BY tbl_emp_attendance.attendanceDate DESC ")->result_array();
                              $arr=array();
                              if($attendanceHistroy){
                                             $i=0;
                                             foreach($attendanceHistroy as $row){
                                                            $arr[$i]['empId']=$row['empId'];
                                                            $arr[$i]['year']=$row['year'];
                                                            $arr[$i]['date']=$row['date'];
                                                            $arr[$i]['month']=date('M',strtotime($row['month']));
                                                            $arr[$i]['dayName']=$row['dayName'];
                                                            $arr[$i]['inTime']=$row['inTime'];
                                                            $arr[$i]['outTime']=$row['outTime'];
                                                            $arr[$i]['workingHours']=$row['workingHours'];
                                                            if($row['attendanceStatus']=='A'){
                                                                           $arr[$i]['attendanceStatus']='Absent';
                                                            }else if ($row['attendanceStatus']=='P'){
                                                                           $arr[$i]['attendanceStatus']='Present';
                                                            }else if ($row['attendanceStatus']=='DO'){
                                                                           $arr[$i]['attendanceStatus']='Day Off';
                                                            }else if ($row['attendanceStatus']=='WO'){
                                                                           $arr[$i]['attendanceStatus']='Week Off';
                                                            }else if ($row['attendanceStatus']=='HO'){
                                                                           $arr[$i]['attendanceStatus']='Holiday';
                                                            }else if ($row['attendanceStatus']=='HD'){
                                                                           $arr[$i]['attendanceStatus']='Half Day';
                                                            }else if ($row['attendanceStatus']=='L'){
                                                                           $arr[$i]['attendanceStatus']='Leave';
                                                            }else{
                                                                           $arr[$i]['attendanceStatus']=$row['attendanceStatus'];
                                                            }
                                                          
                                                            $arr[$i]['inlat']=$row['inlat'];
                                                            $arr[$i]['outlat']=$row['outlat'];
                                                            $i++;
                                             }
                                             $array['response']='success';
                                             $array['data']=$arr;
                              }else{
                                             $array['response']='fail';            
                                             }
                                             echo json_encode($array);       
                              
               }
               function teamLeaveList(){
                              $array=array();
                              $empId=$_POST['empId'];
                              $statusType=$_POST['statusType'];
                              $queryReg= $this->db->query("SELECT regu.id,regu.parentId,regu.requestFrom,regu.cancelled_status,regu.cancel_remarks,emp_mst.empTitle,emp_mst.empFname,emp_mst.empMname,emp_mst.empLname,regu.regularizationType,regu.regularizationApplyfor,regu.remarks,regu.status,DATE_FORMAT(regu.regularizationDate, '%d-%m-%Y') as regularizationDate,regu.fromDate,regu.toDate,regu.noofdays,regu.leaveType,emp_mst.empImage as empImg FROM tbl_regularization AS regu JOIN tbl_emp_master AS emp_mst on emp_mst.empId=regu.requestFrom WHERE regu.requestTo ='".$empId."' and regu.regularizationType IN ('L','FHD','SHD') and regu.status='".$statusType."' order by regu.toDate DESC")->result_array();
                                             $arr=array();
                              if($queryReg){
                                            $i=0;
                                             foreach($queryReg as $row){
                                                            $array[$i]['id']=$row['id'];
                                                            $array[$i]['parentId']=$row['parentId'];
                                                            $array[$i]['cancelled_status'] = $row['cancelled_status'];
 
                                                            $array[$i]['cancel_remarks']   = $row['cancel_remarks'];
                                                            $array[$i]['empId']=$row['requestFrom'];
                                             if($row['empMname']){
                                                            $empMname=$row['empMname']." ";
                            }else{
                                                                           $empMname=$row['empMname'];
                            }
                              $array[$i]['empFullName']=$row['empTitle']." ".$row['empFname']." ".$empMname.$row['empLname'];
                              $mobileimgSql=$this->db->query("SELECT emp_img_tbl.empImgUrl as img FROM emp_img_tbl WHERE emp_img_tbl.empId = '".$array[$i]['empId']."' ")->result_array();
                                                            $selectUserPic=$this->db->query("SELECT COUNT(emp_img_tbl.id) as rowId  FROM emp_img_tbl WHERE emp_img_tbl.empId = '".$array[$i]['empId']."' ")->result_array();
                                      $checkUserPic= $selectUserPic['0']['rowId'];
                                       if($checkUserPic){
                                                                                          $imgMobile=$mobileimgSql['0']['img'];
                                                                                          $array[$i]['empImg']=base_url().'uploads/'.$imgMobile;
                                                                           }
                                                            else{
                                                                                          if($row['empImg']){
                                                                                          $array[$i]['empImg'] =base_url().'uploads/candidateDocument/empImage/'.$row['empImg'];
                                                                                          }else{
                                                                                                         $array[$i]['empImg'] = "";
                                                                                          }
                                                                           }
                                                            /*if($rows['empImg']){
                                                                           $array[$i]['empImg'] =base_url().'uploads/candidateDocument/empImage/'.$rows['empImg'];
                                                                           }else{
                                                                                          $array[$i]['empImg'] = "";
                                                                                          }*/
                              $array[$i]['regularizationType']=$row['regularizationType'];
                              $array[$i]['regularizationApplyfor']=$row['regularizationApplyfor'];
                              $array[$i]['remarks']=$row['remarks'];
                              $array[$i]['status']=$row['status'];
                                                            $array[$i]['fromDate']=$row['fromDate'];
                              $array[$i]['toDate']=$row['toDate'];
                              $array[$i]['noofdays']=$row['noofdays'];
                              $array[$i]['regularizationDate']=$row['regularizationDate'];
                              $array[$i]['leaveType']=$row['leaveType'];
                              $i++;
                              }
                                  $arr['response']='success';
                                  $arr['data']=$array;    
                     }else{
                           $arr['response']='fail';
                     }
                     echo json_encode($arr);
               }
               function teamRegularizationList(){
                              $array=array();
                              $empId=$_POST['empId'];
                              $statusType=$_POST['statusType'];
                              $queryReg= $this->db->query("SELECT regu.id,regu.parentId,regu.requestFrom,emp_mst.empTitle,emp_mst.empFname,emp_mst.empMname,emp_mst.empLname,regu.regularizationType,regu.regularizationApplyfor,regu.remarks,regu.status,DATE_FORMAT(regu.regularizationDate, '%d-%m-%Y') as regularizationDate,regu.fromDate,regu.toDate, empAtt.inTime,empAtt.outTime,empAtt.workingHours,emp_mst.empImage as empImg FROM tbl_regularization AS regu JOIN tbl_emp_master AS emp_mst on emp_mst.empId=regu.requestFrom Left join tbl_emp_attendance as empAtt On empAtt.id=regu.parentId  WHERE regu.requestTo ='".$empId."' and regu.regularizationType NOT IN ('L','FHD','SHD') and regu.status='".$statusType."' Order By regu.fromdate DESC")->result_array();
                                             $arr=array();
                              if($queryReg){
                                             $i=0;
                                             foreach($queryReg as $row){
                                                            $array[$i]['id']=$row['id'];
                                                           $array[$i]['parentId']=$row['parentId'];
                                                            $array[$i]['empId']=$row['requestFrom'];
                                             if($row['empMname']){
                                                            $empMname=$row['empMname']." ";
                            }else{
                                                                           $empMname=$row['empMname'];
                            }
                              $array[$i]['empFullName']=$row['empTitle']." ".$row['empFname']." ".$empMname.$row['empLname'];
                              $array[$i]['regularizationType']=$row['regularizationType'];
                              $array[$i]['regularizationApplyfor']=$row['regularizationApplyfor'];
                              $array[$i]['remarks']=$row['remarks'];
                              $array[$i]['status']=$row['status'];
                                                            $array[$i]['fromDate']=$row['fromDate'];
                              $array[$i]['toDate']=$row['toDate'];
                                                            $array[$i]['inTime']=$row['inTime'];
                              $array[$i]['outTime']=$row['outTime'];
                              $array[$i]['workingHours']=$row['workingHours'];
                              $array[$i]['regularizationDate']=$row['regularizationDate'];
                              $mobileimgSql=$this->db->query("SELECT emp_img_tbl.empImgUrl as img FROM emp_img_tbl WHERE emp_img_tbl.empId = '".$array[$i]['empId']."' ")->result_array();
                                                            $selectUserPic=$this->db->query("SELECT COUNT(emp_img_tbl.id) as rowId  FROM emp_img_tbl WHERE emp_img_tbl.empId = '".$array[$i]['empId']."' ")->result_array();
                                       $checkUserPic= $selectUserPic['0']['rowId'];
                                       if($checkUserPic){
                                                                                          $imgMobile=$mobileimgSql['0']['img'];
                                                                                          $array[$i]['empImg']=base_url().'uploads/'.$imgMobile;
                                                                           }
                                                            else{
                                                                                          if($row['empImg']){
                                                                                          $array[$i]['empImg'] =base_url().'uploads/candidateDocument/empImage/'.$row['empImg'];
                                                                                          }else{
                                                                                                         $array[$i]['empImg'] = "";
                                                                                          }
                                                                           }
                              $i++;
                              }
                                  $arr['response']='success';
                                  $arr['data']=$array;    
                     }else{
                           $arr['response']='fail';
                     }
                     echo json_encode($arr);
               }
               function teamLeaveCount(){
                              $array=array();
                              $empId=$_POST['empId'];
                              $querypendReg= $this->db->query("SELECT count(regu.id) as pendingLeave FROM tbl_regularization AS regu JOIN tbl_emp_master AS emp_mst on emp_mst.empId=regu.requestFrom WHERE regu.requestTo ='".$empId."' and regu.regularizationType IN ('L','FHD','SHD') and regu.status='P' order by regu.toDate DESC")->result_array();
                              $queryAppReg= $this->db->query("SELECT count(regu.id) as approvedLeave FROM tbl_regularization AS regu JOIN tbl_emp_master AS emp_mst on emp_mst.empId=regu.requestFrom WHERE regu.requestTo ='".$empId."' and regu.regularizationType IN ('L','FHD','SHD') and regu.status='A' order by regu.toDate DESC")->result_array();
                              $queryDeclineReg= $this->db->query("SELECT count(regu.id) as declineLeave FROM tbl_regularization AS regu JOIN tbl_emp_master AS emp_mst on emp_mst.empId=regu.requestFrom WHERE regu.requestTo ='".$empId."' and regu.regularizationType IN ('L','FHD','SHD') and regu.status='R' order by regu.toDate DESC")->result_array();
                              $pendingLeave=$querypendReg['0']['pendingLeave'];
                              $approvedLeave=$queryAppReg['0']['approvedLeave'];
                              $declineLeave=$queryDeclineReg['0']['declineLeave'];
                            
                              $array['response']='success';
                              $array['pendingLeave']=$pendingLeave;
                              $array['approvedLeave']=$approvedLeave;
                              $array['declineLeave']=$declineLeave;
                              echo json_encode($array);
               }
               function teamRegularizationCount(){
                              $array=array();
                              $empId=$_POST['empId'];
                              $queryRegPending= $this->db->query("SELECT count(regu.id) as pendingReg  FROM tbl_regularization AS regu JOIN tbl_emp_master AS emp_mst on emp_mst.empId=regu.requestFrom Left join tbl_emp_attendance as empAtt On empAtt.id=regu.parentId  WHERE regu.requestTo ='".$empId."' and regu.regularizationType NOT IN ('L','FHD','SHD') and regu.status='P' ")->result_array();
                              $queryRegApproved= $this->db->query("SELECT count(regu.id) as approvedReg FROM tbl_regularization AS regu JOIN tbl_emp_master AS emp_mst on emp_mst.empId=regu.requestFrom Left join tbl_emp_attendance as empAtt On empAtt.id=regu.parentId  WHERE regu.requestTo ='".$empId."' and regu.regularizationType NOT IN ('L','FHD','SHD') and regu.status='A' ")->result_array();
                              $queryRegDecline= $this->db->query("SELECT count(regu.id) as declineReg FROM tbl_regularization AS regu JOIN tbl_emp_master AS emp_mst on emp_mst.empId=regu.requestFrom Left join tbl_emp_attendance as empAtt On empAtt.id=regu.parentId  WHERE regu.requestTo ='".$empId."' and regu.regularizationType NOT IN ('L','FHD','SHD') and regu.status='R' ")->result_array();
                              $queryRegPending=$queryRegPending['0']['pendingReg'];
                              $queryRegApproved=$queryRegApproved['0']['approvedReg'];
                              $queryRegDecline=$queryRegDecline['0']['declineReg'];
                              $array['response']='success';
                              $array['pendingReg']=$queryRegPending;
                              $array['approvedReg']=$queryRegApproved;
                              $array['declineReg']=$queryRegDecline;
                              echo json_encode($array);
               }
function myTeamAttendance(){
                              $array=array();
                              $empId=$_POST['empId'];
                              //$dateYM=$_POST['dateYM'];
                              $year=$_POST['year'];
                              $month=$_POST['month'];
                              $nmonth = date('m',strtotime($month));
                              $dateYM=$year.'-'.$nmonth;
                              //echo json_encode($dateYM);die;
                              $sqlMyteamEmpId=$this->db->query("SELECT empId as teamEmpId,empTitle,empFname,empMname,empLname,empImage FROM tbl_emp_master WHERE reportingTo ='".$empId."' and STATUS =1 ORDER BY empFname ASC ")->result_array();
                              $arr=array();
                              if($sqlMyteamEmpId){
                                             $i=0;
                                             foreach($sqlMyteamEmpId as $row){
                                                            $leaveCountSql=$this->db->query("SELECT COUNT(tbl_emp_attendance.id) as leaveCount FROM `tbl_emp_attendance` WHERE `empId` = '".$row['teamEmpId']."' AND `attendanceStatus` IN ('L','HL') AND attendanceDate LIKE '%".$dateYM."%' ")->result_array();
                                                            $absentCountSql=$this->db->query("SELECT COUNT(tbl_emp_attendance.id) as absentCount FROM `tbl_emp_attendance` WHERE `empId` = '".$row['teamEmpId']."' AND `attendanceStatus` IN ('A') AND attendanceDate LIKE '%".$dateYM."%' ")->result_array();
                                                            $holidayCountSql=$this->db->query("SELECT COUNT(tbl_emp_attendance.id) as holidayCount FROM `tbl_emp_attendance` WHERE `empId` = '".$row['teamEmpId']."' AND `attendanceStatus` IN ('HO') AND attendanceDate LIKE '%".$dateYM."%' ")->result_array();
                                                            $halfCountSql=$this->db->query("SELECT COUNT(tbl_emp_attendance.id) as halfCount FROM `tbl_emp_attendance` WHERE `empId` = '".$row['teamEmpId']."' AND `attendanceStatus` IN ('HD') AND attendanceDate LIKE '%".$dateYM."%' ")->result_array();
                                                            $presentCountSql=$this->db->query("SELECT COUNT(tbl_emp_attendance.id) as presentCount FROM `tbl_emp_attendance` WHERE `empId` = '".$row['teamEmpId']."' AND `attendanceStatus` IN ('P','WO','DO','WH') AND attendanceDate LIKE '%".$dateYM."%' ")->result_array();
                                                            $arr[$i]['teamEmpId'] = (String)$row['teamEmpId'];
                                                            $mobileimgSql=$this->db->query("SELECT emp_img_tbl.empImgUrl as img FROM emp_img_tbl WHERE emp_img_tbl.empId = '".$row['teamEmpId']."' ")->result_array();
                                                            $selectUserPic=$this->db->query("SELECT COUNT(emp_img_tbl.id) as rowId  FROM emp_img_tbl WHERE emp_img_tbl.empId = '".$row['teamEmpId']."' ")->result_array();
                                       $checkUserPic= $selectUserPic['0']['rowId'];
                                       if($checkUserPic){
                                                                                          $imgMobile=$mobileimgSql['0']['img'];
                                                                                          $arr[$i]['empImg']=base_url().'uploads/'.$imgMobile;
                                                                           }
                                                            else{
                                                                                          if($rows['empImg']){
                                                                                          $arr[$i]['empImg'] =base_url().'uploads/candidateDocument/empImage/'.$rows['empImg'];
                                                                                          }else{
                                                                                                         $arr[$i]['empImg'] = "";
                                                                                          }
                                                                           }
                                                            /*if($rows['empImg']){
                                                                           $arr[$i]['empImg'] =base_url().'uploads/candidateDocument/empImage/'.$rows['empImg'];;
                                                            }else{
                                                                           $arr[$i]['empImg'] = "";
                                                            }*/
                                                         
                                                            if($row['empMname']){
                                                            $empMname=$row['empMname']." ";
                            }else{
                                                                           $empMname=$row['empMname'];
                            }
                              $arr[$i]['empFullName']=$row['empTitle']." ".$row['empFname']." ".$empMname.$row['empLname'];
                                                            $arr[$i]['leaveCount']= (String)$leaveCountSql[0]['leaveCount'];
                                                            $arr[$i]['absentCount']=(String)$absentCountSql[0]['absentCount'];
                                                            $arr[$i]['holidayCount']=(String)$holidayCountSql[0]['holidayCount'];
                                                            $arr[$i]['halfCount']=(String)$halfCountSql[0]['halfCount'];
                                                            $arr[$i]['presentCount']=(String)$presentCountSql[0]['presentCount'];
                                             $arr[$i]['maxCount']=(String)($arr[$i]['leaveCount']+$arr[$i]['absentCount']+$arr[$i]['holidayCount']+$arr[$i]['halfCount']+$arr[$i]['presentCount']);
                                             $arr[$i]['persentCount']=substr((String)(($arr[$i]['presentCount']*100)/$arr[$i]['maxCount']),0,2);
                                                            if(!$arr[$i]['persentCount']){
                                                                           $arr[$i]['persentCount']='0';
                                                            }
                                                            $arr[$i]['yearMonth']=$dateYM;
                                                            $i++;
                                            }
                                             $array['response']='success';
                                             $array['data']=$arr;
                              }else{
                                             $array['response']='fail';
                              }
                              echo json_encode($array);
               }
 
  
  function leaveAppDis(){      
                // print_r($this->input->post());
                // die();               
               if($this->input->post('responcetype')=='A')
               {
                              $data['status'] ="A";
               }else if($this->input->post('responcetype')=='R')
               {
                              $data['status'] ="R";
               }
                                                                           if(date('Y', strtotime(date('Y-m-d')))!=date('Y', strtotime($this->input->post('fromDate'))))
                                                                           {
                                                                           $leave = $this->attendance_model->availableLeavePrevious($this->input->post('requestFrom'),$this->input->post('leaveType'));
                                                                           }else{
                                                                           $leave = $this->attendance_model->availableLeave($this->input->post('requestFrom'),$this->input->post('leaveType'));
                                                                           }
               $reporting = $this->employee_model->getReportingDetails($this->input->post('requestFrom'));
               //if($leave['0']['balanceLeave']<= $this->input->post('noofdays'))
               $thread['regularizationId'] = $this->input->post('id');
               $thread['requestTo'] = $reporting['0']['empId'];
               $thread['requestFrom'] = $this->input->post('requestFrom');
               $thread['remarks'] =   $this->input->post('remarks');
               $thread['threadTime'] =   date('Y-m-d H:i:s',time());
               $this->parent_model->query_insert('tbl_regularization_thread', $thread);
               if($this->input->post('id'))
               {
               $data_update['status'] =  $this->input->post('responcetype');
               // $data_update['approved_status'] = 1;
               $where=" id='".$this->input->post('id')."' ";
               $success =$this->parent_model->query_update(TABLE_REGULARIZATION, $data_update,$where);
               $success=1;
               if($success){
               if($data_update['status']=="A"){
               $responce ="Approved";
               }else
              {
               $responce ="Declined";
               }           
                              $start =$this->input->post('fromDate'); //start date
                              $end = $this->input->post('toDate'); //end date
                              $dates = array();
                              $start = $current = strtotime($start);
                              $end = strtotime($end);
                              while ($current <= $end) {
                                             $dates[] = date('Y-m-d', $current);
                                             $current = strtotime('+1 days', $current);
                              }
                            
                              if(date('Y-m-d') > $this->input->post('fromDate')){

                                                            //pre date
                                             foreach($dates as $dates){
                    $todayAttendanceStatus = $this->attendance_model->gettodayAttendance($dates, $this->input->post('requestFrom'));
                    if($todayAttendanceStatus['attendanceStatus']=='A' or $todayAttendanceStatus['attendanceStatus']=='HD'){
       
          if(date('Y', strtotime(date('Y-m-d')))!=date('Y', strtotime($this->input->post('fromDate'))))
          {
             $balanceleave = $this->attendance_model->availableLeavePrevious($this->input->post('requestFrom'),$this->input->post('leaveType'));

$getprevdata=$this->attendance_model->countPreApprovLeaves($this->input->post('requestFrom'),$this->input->post('leaveType'));
          /*$this->db->query('Select sum(noofdays) as noofdays  from tbl_regularization where requestFrom="'.$this->input->post('requestFrom').'" AND leaveType="'.$this->input->post('leaveType').'" AND status="A" and YEAR(fromDate)="'.date("Y",strtotime("-1 year")).'"')->row_array();*/
          // $leave['0']['balanceLeave']=($leave[0]['opening']-$getprevdata['noofdays']);
          $balanceleave[0]['balanceLeave']=($balanceleave[0]['opening']-$getprevdata['noofdays']);
                                                                               
}else{
$balanceleave = $this->attendance_model->availableLeave($this->input->post('requestFrom'),$this->input->post('leaveType'));

$getprevdata=$this->attendance_model->countApprovLeaves($this->input->post('requestFrom'),$this->input->post('leaveType'),$this->input->post('id'));
// $leave[0]['balanceLeave']=($leave[0]['opening']-$getprevdata['noofdays']);
$balanceleave[0]['balanceLeave']=($balanceleave[0]['opening']-$getprevdata['noofdays']);

                           
                          
                                                                           }
                                                                    // echo json_encode($balanceleave);die;      
                                                                           if($balanceleave['0']['balanceLeave']>=0.5)
                                                                           { 
                                                                                          if($todayAttendanceStatus['attendanceStatus']=='A' and $this->input->post('noofday')>0.5 and $balanceleave['0']['balanceLeave']>=1){ 
                                                                                                         $update_attendance['attendanceStatus'] ='L';
                                                                                                         $updateleave['balanceLeave'] = $balanceleave['0']['balanceLeave']-1;
                                                                                          }else{
                                                                                                         $update_attendance['attendanceStatus'] ='HL';
                                                                                                         $updateleave['balanceLeave'] =$balanceleave['0']['balanceLeave']-0.5;
                                                                                          }
                                                                           }else{
                                                                              // echo json_encode($balanceleave);die;
                                                                                          $update_attendance['attendanceStatus'] ='LWP';
                                                                                          $updateleave['balanceLeave']='0';
                                                                           }
                                                                           
                                                            $where=" empId='".$reporting['0']['requestFrom']."' and attendanceDate='".$dates."'";
                                                  $success =$this->parent_model->query_update(TABLE_ATTENDANCE, $update_attendance,$where);
                                                            $where1 =" empId='".$reporting['0']['requestFrom']."' and leaveType='".$this->input->post('leaveType')."'";
                              
                                                            if(date('Y', strtotime(date('Y-m-d')))!=date('Y', strtotime($this->input->post('fromDate'))))
                                                                           {
                                                                             $this->parent_model->query_update("tbl_emp_leave_balance_".date("Y",strtotime("-1 year"))."", $updateleave,$where1);
                                                                           }else{
                                                                             $this->parent_model->query_update(TABLE_LEAVE_BALANCE, $updateleave,$where1);
                                                                           }
                                                            
                                                            }
                            }
                              }
                              else
                              {  
                              $balanceleave = $this->attendance_model->availableLeave($this->input->post('requestFrom'),$this->input->post('leaveType'));
                    if($this->input->post('balanceLeave')>=1){
                                $update_attendance['approved_status'] = 1;     
                                $updateleave['balanceLeave'] = $balanceleave['0']['balanceLeave']-$this->input->post('noofday');
                              }else{
                                             $updateleave['balanceLeave']=0; 
                                             $update_attendance['approved_status'] =0;
                              }
                              
                              $condition=" empId='".$this->input->post('requestFrom')."' and attendanceDate='".$this->input->post('toDate')."'";
                              
                    $success =$this->parent_model->query_update(TABLE_REGULARIZATION, $update_attendance,$where);
                              
                              $where1 =" empId='".$reporting['0']['requestFrom']."' and leaveType='".$this->input->post('leaveType')."'";
                              $this->parent_model->query_update(TABLE_LEAVE_BALANCE, $updateleave,$where1);
                              }

             
  
                              $subject     = 'Leave Request is '.$responce.'';
                              $name  = $reporting['0']['empName'];
                              if($reporting['0']['oemail']){
                                             $email =$reporting['0']['oemail'];
                              }else if($reporting['0']['pemail']){
                                             $email =$reporting['0']['pemail'];
                              }
                              if(MODE =='live'){
                                             $to =$email;
                              }else{
                                             $to = EMAILTO;
                              }
                              $body_txt = "Your Leave Request has ".$responce." for the Below mentioned dates
                              <table>
                                             <tr><td><b>Leave From </b></td><td>: ".date('d-F-Y',strtotime($this->input->post('fromDate')))."</td></tr>
                                             <tr><td><b>Leave To</b></td><td>: ".date('d-F-Y',strtotime($this->input->post('toDate')))."</td></tr>
                                             <tr><td><b>Leave Type</b></td><td>: ".$this->input->post('leaveType')."</td></tr>";
                                             if($this->input->post('remarks')){
                                                           $body_txt .="<tr><td><b>Remarks</b></td><td>: ".$this->input->post('remarks')."</td></tr>";
                                             }
                                             $body_txt .="</table>";
                                             $maildata = array(
                                                            'SITE_LOGO_URL' => base_url().SITE_IMAGEURL.'logo.png',
                                                            'USER' => $name,
                                                            'SITE_NAME' => SITE_NAME,
                                                            'MAIL_DATA'=>$body_txt
                                                            );
                                             $htmlMessage =  $this->parser->parse('emails/alert', $maildata, true);
                                             $this->myemail->sendEmail($to,$subject, $htmlMessage, ADMIN_EMAIL, ADMIN_NAME);
                                             $arr=array();
$title='Your Leave Request has '.$responce ;
               $message='From:'.$this->input->post('fromDate').' '.'To:'.$this->input->post('toDate');
               $empTokenId=$this->input->post('requestFrom');
                                             $tokenSql=$this->db->query("SELECT tbl_gcm_users.deviceId as deviceId FROM tbl_gcm_users WHERE tbl_gcm_users.empId = '".$empTokenId."' ")->result_array();
                                             $token=$tokenSql['0']['deviceId'];
                                             if($token){         
                                                            $API_SERVER_KEY = 'AAAAucVTDK4:APA91bE6T2wVEW1vL17C1Dm5Cizf_5TzkeM8Wb16TG41IE9uXAjvRk6Va9S4ntgdwKX9m6rqAHZH7rLAobyYslJFPTAHN4kcYU8OZrPRf7pPDvD-3PM3xQDvOIuPaFE6tJ1dY98CTc5M';
                                             $is_background = "TRUE";
                                                            $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
                                                               $fields = array(
                                                                 'to' => $token,
                                                                 'notification' =>array('title' => $title, 'body' =>  $message ),
                                                                 'priority' =>'high',
                                                               );
                                      $headers = array(
                                         'Authorization:key=' .$API_SERVER_KEY,
                                          'Content-Type:application/json'
                                      );
                                      $ch = curl_init();
                                      // Set the url, number of POST vars, POST data
                                      curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
                                      curl_setopt($ch, CURLOPT_POST, true);
                                      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                      curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
                                      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                                      $result = curl_exec($ch);
                                      curl_close($ch);
                                      //echo json_encode($result);
                                                           
                                             }
                                                            $arr['response']='success';
                                                            echo json_encode($arr);
                                                            die;
                                             }else{
                                                            $arr=array();
                                                            $arr['response']='fail';
                                                            echo json_encode($arr);
                                                            die;
                                             }
                              }
               }


               function resAppDis(){
                              $data['status'] = $this->input->post('responcetype');
                              $where=" id='".$this->input->post('id')."' ";
                              $data1['status'] =$this->input->post('responcetype');
                              if($this->input->post('responcetype')=='A'){
                                             $data1['attendanceStatus'] ='P';
                              }
                              $where1=" id='".$this->input->post('parentId')."' ";
                              if($this->input->post('remarks')){
                                             $thread['regularizationId'] = $this->input->post('parentId');
                                             //$thread['requestFrom'] = $this->session->userdata('admin_id');
                                             $thread['requestFrom'] = $this->input->post('requestFrom');
                                             //$this->input->post('requestTo');
                                             $thread['requestTo'] =  $this->input->post('requestTo');
                                             $thread['remarks'] = $this->input->post('remarks');
                                             $thread['threadTime'] = date('Y-m-d H:i:s',time());
                                             $this->parent_model->query_insert('tbl_regularization_thread', $thread);
                              }
                              if($this->parent_model->query_update(TABLE_REGULARIZATION, $data, $where)){
                                             $this->parent_model->query_update(TABLE_ATTENDANCE, $data1, $where1);
               //send Notification
                                             if($this->input->post('responcetype')=='A'){
                                                            $responce ="Approved";
                                             }else if($this->input->post('responcetype')=='R'){
                                                            $responce ="Declined";
                                             }
                                             $reporting = $this->employee_model->getReportingDetails($this->input->post('requestTo'));
               // pre($reporting);die;
 
               $title='Your Regularization '.$responce;
                     //$this->input->post('empId')
                              $message='Attendance Date: '.$this->input->post('attendanceDate');
                              $empTokenId=$this->input->post('requestTo');
                         $tokenSql=$this->db->query("SELECT tbl_gcm_users.deviceId as deviceId FROM tbl_gcm_users WHERE tbl_gcm_users.empId = '".$empTokenId."' ")->result_array();
                          $token=$tokenSql['0']['deviceId'];
 
                          if($token){         
                            $API_SERVER_KEY = 'AAAAucVTDK4:APA91bE6T2wVEW1vL17C1Dm5Cizf_5TzkeM8Wb16TG41IE9uXAjvRk6Va9S4ntgdwKX9m6rqAHZH7rLAobyYslJFPTAHN4kcYU8OZrPRf7pPDvD-3PM3xQDvOIuPaFE6tJ1dY98CTc5M';
                            $is_background = "TRUE";
                            $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
                           $fields = array(
                                     'to' => $token,
                                     'notification' =>array('title' => $title, 'body' =>  $message ),
                                     'priority' =>'high',
                                      );
                                    $headers = array(
                                     'Authorization:key=' .$API_SERVER_KEY,
                                       'Content-Type:application/json'
                                      );
                                    $ch = curl_init();
                                                               // Set the url, number of POST vars, POST data
                                   curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
                                   curl_setopt($ch, CURLOPT_POST, true);
                                   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                   curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
                                   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                                   $result_curl_exec = curl_exec($ch);
                                   curl_close($ch);
                                   //echo json_encode($result);
                                                                                    
                         }
 
                                             $subject     = 'Regularization Request is '.$responce.' ';
                                             $name  = $reporting['0']['empName'];
                                             if($reporting['0']['oemail']){
                                                            $email =$reporting['0']['oemail'];
                                             }else if($reporting['0']['pemail']){
                                                            $email =$reporting['0']['pemail'];
                                             }
                                             if(MODE =='live'){
                                                            $to = $email;
                                             }else{
                                                            $to = EMAILTO;
                                             }
                                             $rltype = array('T'=>'Travel','FL'=>'Forgot Login','FLO'=>'Forgot Logout','FP'=>'Forgot Punch', 'M'=>'Meeting');
                                             //echo json_encode($reason);die;
                                             $reason = $rltype[$this->input->post('regularizationType')];
 
                                             $body_txt    =" Your attendance regularization Request is ".$responce." with the below mentioned dates.
                                             <table>
                                                            <tr><td><b>Date</b></td><td>: ".date('d-M-Y',strtotime($this->input->post('attendanceDate')))."</td></tr>
                                                            <tr><td><b>In Time</b></td><td>: ".$this->input->post('inTime')."</td></tr>
                                                            <tr><td><b>Out Time</b></td><td>: ".$this->input->post('outTime')."</td></tr>
                                                            <tr><td><b>Regularization Apply For</b></td><td>: ".$this->input->post('regularizationApplyfor')."</td></tr>
                                                            
                                             </table>";
                                             $data = array(
                                                            'SITE_LOGO_URL' => base_url().SITE_IMAGEURL.'logo_Vestige',
                                                            'USER' => $name,
                                                            'SITE_NAME' => SITE_NAME,
                                                            'MAIL_DATA'=>$body_txt
                                                            );
                                             $htmlMessage =  $this->parser->parse('emails/alert', $data, true);
                                             $this->myemail->sendEmail($to,$subject, $htmlMessage, ADMIN_EMAIL, SITE_NAME);
                                             $arrs=array();
                                                            $arrs['response']='success';
                                                            echo json_encode($arrs);
                                                            die;
                                             }else{
                                                            $arrs=array();
                                                            $arr['response']='fail';
                                                            echo json_encode($arrs);
                                                            die;
                                             }
               }
 
function changePassword(){
               $array=array();
               $empId=$_POST['empId'];
               $userPass=md5($_POST['userPass']);
              /* $updatePass=$this->db->query("UPDATE tbl_emp_master SET tbl_emp_master.empPassword='".$userPass."'WHERE tbl_emp_master.empId='".$empId."' ");*/
               $data=array('empPassword'=>$userPass);
               $this->db->set('empPassword',false);
               $this->db->where('empId',$empId);
               $this->db->update('tbl_emp_master',$data);
 
          if($this->db->affected_rows()){
               $array['response']='success';
               $array['msg']='you have changed password successfully';
          }else{
               $array['response']='fail';
          }
          echo json_encode($array);
          die;
     }
     function versionCheck(){
          $array=array();
          $empId=$_POST['empId'];
          $array['response']='success';
          $array['iOS']='5.1';
          $array['Android']='2.8';
          echo json_encode($array);
          die;
     }
     
     function holidayList(){
          $empId=$_POST['empId'];
          $stateId=$_POST['stateId'];
          $adminDetails = $this->login_model->get_employee_login_admin_info($empId,TABLE_EMP);
          $result =$this->db->query("SELECT h.holiday as holiday,DATE_FORMAT(h.holidayDate,'%W') as day,DATE_FORMAT(h.holidayDate,'%d')as date,DATE_FORMAT(h.holidayDate,'%b')as month from tbl_mst_holiday h left join tbl_mst_holiday_region_client hr on h.id=hr.holiday left join tbl_region r on r.id=hr.region where DATE_FORMAT(h.holidayDate,'%Y')='".date('Y')."' and  hr.stateId ='".$stateId."' group by h.id order by h.holidayDate Asc")->result_array();
          $array=array();
          $array['response']="success";
          $array['data']=$result;
          //'".$adminDetails['State_Id']."'
     echo json_encode($array);
     }
     function stateActiveList(){
          $array=array();
          $stateSql=$this->db->query("SELECT state_Id as id,State_Name AS stateName FROM tbl_mst_state WHERE status='0' Order by State_Name ASC")->result_array();
          if($stateSql){
               $array['response']="success";
               $array['data']=$stateSql;
          }else{
               $array['response']="fail";
               //$array['data']=$result;
          }
          echo json_encode($array);
     }
     function myState(){
          $array=array();
          $empId=$_POST['empId'];
          $stateSql=$this->db->query("SELECT cityTbl.state As stateId,stateTbl.State_Name AS stateName FROM tbl_emp_master as empMastTbl LEFT JOIN tbl_mst_city AS cityTbl ON cityTbl.cityId=empMastTbl.jobLocation LEFT JOIN tbl_mst_state AS stateTbl ON stateTbl.State_Id=cityTbl.state WHERE empMastTbl.empId ='".$empId."' ")->result_array();
          if($stateSql){
               $array['response']="success";
               $array['data']=$stateSql;
          }else{
               $array['response']="fail";
               //$array['data']=$result;
          }
          echo json_encode($array);
}

function newJoineeAttendance($DOJ,$empId)
    {		
      $doj   =  $DOJ;
       
       // different between DOJ and Current date
       $diff = strtotime(date('Y-m-d',strtotime(date('Y-m-d')))) - strtotime( $doj);  
     
       // next day  from the joining date	
       
       $dayNames = array(0=>'sunday', 1=>'monday', 2=>'tuesday', 3=>'wednesday', 4=>'thursday', 5=>'friday', 6=>'saturday', 7=>'Any One in Week');
       $week = array( 1=>'first', 2=>'second', 3=>'third', 4=>'fourth', 5=>'fifth');
       $result = $this->employee_model->getEmpdata($empId);
       //print_r($result);die;
     
         
      for($i=1;$i<=abs(round($diff/86400));$i++) 
       {
         if($i==1) {
         $date=$DOJ;
         
         }else{
         $date=date('Y-m-d', strtotime('+1 day',strtotime($DOJ))); 
         }
         
         $DOJ=$date;
         $found = $this->employee_model->checkAttendance($result[0]['empId'],$date);
         
         
         $doff=array();
         $doff1=array();
         if(!$found[0]['empId']){ 
         if($result[0]['status']==1){
         
         //fetch leaveGroup
         $sql1  ="select effectiveDate,leaveGroup from ".TABLE_SERVICE." where empId='".$result[0]['empId']."'";
         $result1 = $this->db->query($sql1)->result_array();
         if($result1['0']['effectiveDate']> date('Y-m-d')){
         $sql2  ="select leaveGroup from tbl_leavegrouplog where empId='".$result[0]['empId']."' order by id DESC ";
         $result2 = $this->db->query($sql2)->result_array();
         $leavG = $result2['0']['leaveGroup']; 
         }else{
         $leavG = $result[0]['leaveGroup']; 
         }
         //echo $result['empId'];	
        $holidays = $this->attendance_model->todayempHolidayList($result[0]['State_Id'],$result[0]['clients'], $date);
      
       
       
       $weekoff = $this->master_model->getempweekoff($leavG);
       $dayoff = $this->master_model->getempdayoff($leavG);
       
       $month =date('F', strtotime($date));
       $month1 =date('m', strtotime($date));
       $last = date('t', strtotime($date)); 
       $year =date('Y', strtotime($date));
       $wk=array();
       foreach($weekoff as $weekoff)
       {
       $w =$weekoff['weekno']-1;
       $wk[] = date('Y-m-d',strtotime('+'.$w.' week '.$dayNames[$weekoff['weekoff']].' '.$month.' '.$year.''));
       }
       $startdate = "$year-$month1-01";
       $enddate = "$year-$month1-$last";
       
       
           $doff =$this->getDateForSpecificDayBetweenDates($startdate, $enddate, (int)$dayoff['0']['dayoff']);
           $doff1 =$this->getDateForSpecificDayBetweenDates($startdate, $enddate, (int)$dayoff['1']['dayoff']);
           $doff2 =$this->getDateForSpecificDayBetweenDates($startdate, $enddate, (int)$dayoff['2']['dayoff']);
           $doff3 =$this->getDateForSpecificDayBetweenDates($startdate, $enddate, (int)$dayoff['3']['dayoff']);
           $doff4 =$this->getDateForSpecificDayBetweenDates($startdate, $enddate, (int)$dayoff['4']['dayoff']);
           $doff5 =$this->getDateForSpecificDayBetweenDates($startdate, $enddate, (int)$dayoff['5']['dayoff']);
           $doff6 =$this->getDateForSpecificDayBetweenDates($startdate, $enddate, (int)$dayoff['6']['dayoff']);
           if(in_array($date,$doff) or in_array($date,$doff1) or in_array($date,$doff2) or in_array($date,$doff3) or in_array($date,$doff4) or in_array($date,$doff5) or in_array($date,$doff6)){
           $data['attendanceStatus']='DO';
           $data['empId'] = $result[0]['empId'];
           $data['attendanceDate'] = $date;
           }
           else if($date == $holidays['holidayDate']){
           $data['attendanceStatus']='HO';
           $data['empId'] = $result[0]['empId'];
           $data['attendanceDate'] = $date;
           }else if(in_array($date,$wk)){
           $data['attendanceStatus']='WO';
           $data['empId'] = $result[0]['empId'];
           $data['attendanceDate'] = $date;
           }else if($result['status']=='0'){
           $data['attendanceStatus']='A';
           $data['empId'] = $result['empId'];
           $data['attendanceDate'] = $date;
           }else{
           $data['attendanceStatus']='A';
           $data['empId'] = $result[0]['empId'];
           $data['attendanceDate'] = $date;
           }
           }
           else{
           $data['attendanceStatus']='A';
           $data['empId'] = $result[0]['empId'];
           $data['attendanceDate'] = $date;
           }
           if($result[0]['empDOJ'] > $date){
           $data['attendanceStatus']='NJ';
           }
           
         $data['isCreated'] =  date('Y-m-d H:i:s',time());
         
         if($found['empId']=="")
           {   
             $this->parent_model->query_insert(TABLE_ATTENDANCE, $data);			  
             $insert_cron['attendanceDate'] =$date;
             $insert_cron['isCreated']= date('Y-m-d H:i:s',time());
             $this->parent_model->query_insert('cron_log', $insert_cron);
           }
           
           }	   
      } 
    
    }
     
    function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber)
    {
       $startDate = strtotime($startDate);
       $endDate = strtotime($endDate);
       $dateArr = array();
      do
      {
        if(date("w", $startDate) != $weekdayNumber)
          {
              $startDate += (24 * 3600); // add 1 day
          }
      } while(date("w", $startDate) != $weekdayNumber);
  
  
      while($startDate <= $endDate)
      {
          $dateArr[] = date('Y-m-d', $startDate);
          $startDate += (7 * 24 * 3600); // add 7 days
      }
  
       return($dateArr);
     }
	 
	 /************************ARK TMELINE API *****************************/
    // fetch latest add announcement
   function getLatestAnnouncement(){
         
        $empId = $this->input->post('empId');
        $type  = $this->input->post('type');       
        
          if($empId != ''){
            $announcementList = $this->announcement_model->getLatestAnnouncement($empId,$type);
                           
                if(count($announcementList) > 0){ 
                    $videoArr = ''; 
                    $content   = ''; 
                    if(strpos($announcementList['content'], '<video') !== false) {
                         $videoArr  =  $this->getVideoLink($announcementList['content']);                   
                      }
                         $content   =  $this->removeVideoData($announcementList['content']);    
                          
                             
                      $array['post']['content']        =  str_replace('<p data-f-id="pbf" style="text-align: center; font-size: 14px; margin-top: 30px; opacity: 0.65; font-family: sans-serif;">Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a></p>','',$content);
                      $array['post']['video']          =    $videoArr;                  
                      

                         $array['post']['announcementId'] =  $announcementList['announcementId'];
                         $array['post']['heading']        =  $announcementList['heading'];
                      
                         $array['post']['type']           =  $announcementList['type'];
                         $array['post']['status']         =  $announcementList['status'];
                         $array['post']['isCreated']      =  $this->timePeriodAnnc($announcementList['isCreated']);
                         $array['post']['video_banner']   =  $announcementList['video_banner'] != '' ? base_url().'ark_assets/images/'.$announcementList['video_banner']:'';
                         $array['post']['emp_id']         =  $announcementList['emp_id'];
                         $array['post']['is_like']        =  $announcementList['is_like'];
                         $array['post']['total_like']     =  $announcementList['total_like'];
                         $array['post']['like_emp_list']  =  $announcementList['like_emp_list'];
                         $array['post']['departmentName'] =  $announcementList['departmentName'];
                         $array['post']['dept_image']     =  base_url().$announcementList['dept_image'];
                         $array['post']['total_comment']  =   $announcementList['total_comment'];								 
                       
                         $array['respCode'] =  200;
                         $array['response'] =  "success";                                                 
                } else {
                       $array['response'] =  'noDataFound';
                       $array['respCode'] =  201;
                       $array['msg']      =  'No data found';         
                }                                              
          } else {
            $array['response'] =  'Invalid Parameters';
            $array['respCode'] =  203;
            $array['msg']      =  'Some thing went wrong, Please try again';  
          }              
        echo json_encode($array);
        exit;                               
    }

    // fetch birthday list
    function getBirthdayList(){    
        $empId        = $this->input->post('emp_id');                            
        $birthdayDataCurrent = $this->announcement_model->getCurrentbirthdayList($empId);      
              
        $birthdayData   =  $this->announcement_model->getUpcommingBirthdayList($empId,10);
            
         $array['birthdayList']     =  [];
         $i = 0;
         if(count($birthdayDataCurrent) > 0 && !empty($birthdayDataCurrent)) {
                                              
          foreach($birthdayDataCurrent as $result){                                                                  
              $array['birthdayList'][$i]['empId']          =  $result['empId'];
              $array['birthdayList'][$i]['empFname']       =  $result['empFname'];
              $array['birthdayList'][$i]['empLname']       =  $result['empLname'];
              $array['birthdayList'][$i]['empImage']       =  isset($result['empImage']) && $result['empImage'] !='' ? base_url().'uploads/candidateDocument/empImage/'.$result['empImage']: base_url().'ark_assets/images/default.jpg';
              $array['birthdayList'][$i]['empDOBactual']   =  $result['empDOBactual'];
              $array['birthdayList'][$i]['departmentName'] =  $result['departmentName'];
              $array['birthdayList'][$i]['designationName']=  $result['designationName'];
              $array['birthdayList'][$i]['fullDob']        =  $result['fullDob'];
              $array['birthdayList'][$i]['isLike']         =  $result['is_like'] != ''? $result['is_like']:'';
              $array['birthdayList'][$i]['totalBirthdayLike'] =  $result['total_birthday_like'] != ''? $result['total_birthday_like']:'';
              $array['birthdayList'][$i]['empList']           =  is_null($result['emp_name']) ? '' : $result['emp_name'];
              $array['birthdayList'][$i]['type']              =  "1";                
              $i++;
            }  
         }

         if(count($birthdayData) > 0 && !empty($birthdayData)) {
                                    
            foreach($birthdayData as $result){                                                                  
                $array['birthdayList'][$i]['empId']          =  $result['empId'];
                $array['birthdayList'][$i]['empFname']       =  $result['empFname'];
			 $array['birthdayList'][$i]['empLname']       =  $result['empLname'];
                $array['birthdayList'][$i]['empImage']       =  isset($result['empImage']) && $result['empImage'] !='' ? base_url().'uploads/candidateDocument/empImage/'.$result['empImage']: base_url().'ark_assets/images/default.jpg';
                $array['birthdayList'][$i]['empDOBactual']   =  $result['empDOBactual'];
                $array['birthdayList'][$i]['departmentName'] =  $result['departmentName'];
                $array['birthdayList'][$i]['designationName']=  $result['designationName'];
                $array['birthdayList'][$i]['fullDob']        =  $result['fullDob'];
                $array['birthdayList'][$i]['isLike']         =  $result['is_like'] != ''? $result['is_like']:'';
                $array['birthdayList'][$i]['totalBirthdayLike'] =  $result['total_birthday_like'] != ''? $result['total_birthday_like']:'';
                $array['birthdayList'][$i]['empList']           =  is_null($result['emp_name']) ? '' : $result['emp_name'];
                $array['birthdayList'][$i]['type']              =  "2";                
                $i++;
             }
          }

          if(!empty($array['birthdayList'])){              
               $array['respCode'] =  "200";                                    
               $array['response'] =  "success";
          } else {
              $array['response'] =  'noDataFound';
              $array['respCode'] =  "201";           
              $array['msg']      =  'No data found'; 
          }
         echo json_encode($array);
         exit;
    }
                
    // fetch anniversary list
    function getAnniversaryList(){                    
         $array['anniversaryList']     = [];
         $type  = "1";
         $anniversaryData   =  $this->announcement_model->getCurrentAnniversaryList();           
         if(count($anniversaryData) > 0 && !empty($anniversaryData)) {
             $i = 0;                                 
             foreach($anniversaryData as $result){                                                                  
                  $array['anniversaryList'][$i]['empId']           =  $result['empId'];
                  $array['anniversaryList'][$i]['empFname']        =  $result['empFname'];
			   $array['anniversaryList'][$i]['empLname']        =  $result['empLname'];
                  $array['anniversaryList'][$i]['empImage']        =   isset($result['empImage']) && $result['empImage'] !='' ? base_url().'uploads/candidateDocument/empImage/'.$result['empImage']: 'http://13.126.52.249/space/ark_assets/images/default.jpg';;
                  $array['anniversaryList'][$i]['empDOJ']          =  $result['empDOJ'];
                  $array['anniversaryList'][$i]['departmentName']  =  $result['departmentName'];
                  $array['anniversaryList'][$i]['designationName'] =  $result['designationName'];
                  $array['anniversaryList'][$i]['designationName'] =  $type;
                  $i++;
             }                  
               $array['respCode'] =  "200";
               $array['response'] =  "success";
         } else {
               $array['response'] =  'noDataFound';
               $array['respCode'] =  "201";
               $array['msg']      =  'No data found';    
         }                              
          echo json_encode($array);
          exit;
    }
                
    function getAllAnnouncement(){    
           
        $empId   = $this->input->post('empId');
        $type    = $this->input->post('type');
        $offset  = $this->input->post('offset');
        $announcementList       =  $this->announcement_model->getAllAnnouncementList($empId,$type,$offset);
      
        $array['total_records'] =  $announcementList['totalrows'];
       if(count($announcementList['resultData']) > 0){
           $i =0;
           $videoArr = '';
           $content  =  '';
		foreach($announcementList['resultData'] as $result){
               $videoArr = '';
               $content  =  '';
               if(strpos($result['content'], '<video') !== false) {
                    $videoArr  =  $this->getVideoLink($result['content']);                   
                 }
                    $content   =  $this->removeVideoData($result['content']);    
                     
                        
                 $array['announcementList'][$i]['content']        =  str_replace('<p data-f-id="pbf" style="text-align: center; font-size: 14px; margin-top: 30px; opacity: 0.65; font-family: sans-serif;">Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a></p>','',$content);
                 $array['announcementList'][$i]['video']          =  $videoArr;            
                 
               $array['announcementList'][$i]['announcementId'] =  $result['id'];                                     
               $array['announcementList'][$i]['heading']        =  $result['heading'];             
               $array['announcementList'][$i]['type']           =  $result['type'];
               $array['announcementList'][$i]['status']         =  $result['status'];
               $array['announcementList'][$i]['isCreated']      =  $this->timePeriodAnnc($result['isCreated']);
			$array['announcementList'][$i]['video_banner']   =   $result['video_banner'] != '' ? base_url().'ark_assets/images/'.$result['video_banner']:'';
               $array['announcementList'][$i]['emp_id']         =  is_null($result['emp_id']) ? '' : $result['emp_id'];
               $array['announcementList'][$i]['is_like']        =  is_null($result['is_like']) ? '' : $result['is_like'];
               $array['announcementList'][$i]['total_like']     =  $result['total_like'];
               $array['announcementList'][$i]['like_emp_list']  =  is_null($result['like_emp_list']) ? '' : $result['like_emp_list'];
                                    
			$array['announcementList'][$i]['departmentName'] =  $result['departmentName'];
			$array['announcementList'][$i]['dept_image']     =  base_url().$result['dept_image'];
               $array['announcementList'][$i]['total_comment']     = $result['total_comment'];
							 
													
             $i++;
           }                      
               $array['respCode'] =  "200";                                    
               $array['response'] =  "success";
        } else {
		     $array['respCode'] =  "201";
               $array['response'] =  'noDataFound';
               $array['msg']      =  'No data found';         
        }
          echo json_encode($array,JSON_UNESCAPED_SLASHES);
          exit;
    }
	
	function getAllCoVideos(){         
      $empId = $this->input->post('empId');
      $type  = $this->input->post('type');
      $offset  = $this->input->post('offset');
      $announcementList = $this->announcement_model->getAllCoVideoList($empId,$type,$offset);
      $array['total_records'] =  $announcementList['totalrows'];
      if(count($announcementList['resultData']) > 0){
          $i =0;				  
		    foreach($announcementList['resultData'] as $result){
                 $videoArr  = '';
                 $content   = '';
			   if(isset($result['content']) && $result['type'] == '3'){
                    if(strpos($result['content'], '<video') !== false) {
                         $videoArr  =  $this->getVideoLink($result['content']);                   
                      }
                         $content   =  $this->removeVideoData($result['content']);     
                        
                      $array['announcementList'][$i]['content']        =  str_replace('<p data-f-id="pbf" style="text-align: center; font-size: 14px; margin-top: 30px; opacity: 0.65; font-family: sans-serif;">Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a></p>','',$content);
                      $array['announcementList'][$i]['video']          =  $videoArr;                
                      
                    $array['announcementList'][$i]['announcementId'] =  $result['id'];                                     
                    $array['announcementList'][$i]['heading']        =  $result['heading'];                  
                    $array['announcementList'][$i]['type']           =  $result['type'];
                    $array['announcementList'][$i]['status']         =  $result['status'];
                    $array['announcementList'][$i]['isCreated']      =  $this->timePeriodAnnc($result['isCreated']);
                    $array['announcementList'][$i]['video_banner']   =  $result['video_banner'] != '' ? base_url().'ark_assets/images/'.$result['video_banner']:'';
                    $array['announcementList'][$i]['emp_id']         =  is_null($result['emp_id']) ? '' : $result['emp_id'];
                    $array['announcementList'][$i]['is_like']        =  is_null($result['is_like']) ? '' : $result['is_like'];
                    $array['announcementList'][$i]['total_like']     =  $result['total_like'];
                    $array['announcementList'][$i]['like_emp_list']  =  is_null($result['like_emp_list']) ? '' : $result['like_emp_list'];
                    $array['announcementList'][$i]['departmentName'] =  $result['departmentName'];
                    $array['announcementList'][$i]['dept_image']     =  base_url().$result['dept_image'];
                    $array['announcementList'][$i]['total_comment']  =  $result['total_comment'];
                    $i++;	
                					 
              }         
           }                      
          $array['respCode'] =  "200";                                    
          $array['response'] =  "success";
      } else {
			     $array['respCode'] =  "201"; 
                    $array['response'] =  'noDataFound';					
                    $array['msg']      =  'No data found';         
      }
      echo json_encode($array);
      exit;
  }
	
	function getAllTeamHrAnnouncement(){         
      $empId = $this->input->post('empId');
      $type  = $this->input->post('type');
      $offset  = $this->input->post('offset');
      $announcementList = $this->announcement_model->getAllTeamHrAnnouncementList($empId,$type,$offset);
      $array['total_records'] =  $announcementList['totalrows'];
      if(count($announcementList['resultData']) > 0){
          $i =0;
          $videoArr ='';
          $content  = '';				  
		foreach($announcementList['resultData'] as $result){	
               $videoArr ='';
               $content  = '';	
               if(strpos($result['content'], '<video') !== false) {
                    $videoArr  =  $this->getVideoLink($result['content']);                   
                 }
                    $content   =  $this->removeVideoData($result['content']);        
                                         
                   
                      
               $array['announcementList'][$i]['content']        =  str_replace('<p data-f-id="pbf" style="text-align: center; font-size: 14px; margin-top: 30px; opacity: 0.65; font-family: sans-serif;">Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a></p>','',$content);
               $array['announcementList'][$i]['video']          =   $videoArr;
               
               $array['announcementList'][$i]['announcementId'] =  $result['id'];
			$array['announcementList'][$i]['heading']        =  $result['heading'];			
			$array['announcementList'][$i]['type']           =  $result['type'];
			$array['announcementList'][$i]['status']         =  $result['status'];
			$array['announcementList'][$i]['isCreated']      =  $this->timePeriodAnnc($result['isCreated']);
			$array['announcementList'][$i]['video_banner']   =   $result['video_banner'] != '' ? base_url().'ark_assets/images/'.$result['video_banner']:'';
			$array['announcementList'][$i]['emp_id']         =  is_null($result['emp_id']) ? '' : $result['emp_id'];
			$array['announcementList'][$i]['is_like']        =  is_null($result['is_like']) ? '' : $result['is_like'];
			$array['announcementList'][$i]['total_like']     =  $result['total_like'];
			$array['announcementList'][$i]['like_emp_list']  =  is_null($result['like_emp_list']) ? '' : $result['like_emp_list'];
			$array['announcementList'][$i]['departmentName'] =  $result['departmentName'];
			$array['announcementList'][$i]['dept_image']     =  base_url().$result['dept_image'];
                        $array['announcementList'][$i]['total_comment']  =  $result['total_comment'];  								 
               $i++;
          }                      
               $array['respCode'] =  "200";                                    
               $array['response'] =  "success";
      } else {
	    $array['respCode'] =  "201"; 
         $array['response'] =  'noDataFound';					
         $array['msg']      =  'No data found';         
      }
      echo json_encode($array);
      exit;
  }
	
	function getAllTeamItAnnouncement(){         
      $empId = $this->input->post('empId');
      $type  = $this->input->post('type');
      $offset  = $this->input->post('offset');
      $announcementList = $this->announcement_model->getAllTeamItAnnouncementList($empId,$type,$offset);
      $array['total_records'] =  $announcementList['totalrows'];
      if(count($announcementList['resultData']) > 0){
          $i =0;
          $videoArr  = '';
          $content   = '';				  
		foreach($announcementList['resultData'] as $result){						
               $videoArr  = '';
               $content   = '';	                      
               if(strpos($result['content'], '<video') !== false) {
                    $videoArr  =  $this->getVideoLink($result['content']);                   
                 }
                    $content   =  $this->removeVideoData($result['content']);   
                     
                        
                 $array['announcementList'][$i]['content']        =  str_replace('<p data-f-id="pbf" style="text-align: center; font-size: 14px; margin-top: 30px; opacity: 0.65; font-family: sans-serif;">Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a></p>','',$content);
                 $array['announcementList'][$i]['video']          =  $videoArr;
                            
               
               $array['announcementList'][$i]['announcementId'] =  $result['id'];                                     
			$array['announcementList'][$i]['heading']        =  $result['heading'];			
			$array['announcementList'][$i]['type']           =  $result['type'];
			$array['announcementList'][$i]['status']         =  $result['status'];
			$array['announcementList'][$i]['isCreated']      =  $this->timePeriodAnnc($result['isCreated']);
			$array['announcementList'][$i]['video_banner']   =  $result['video_banner'] != '' ? base_url().'ark_assets/images/'.$result['video_banner']:'';
               $array['announcementList'][$i]['emp_id']         =  is_null($result['emp_id']) ? '' : $result['emp_id'];
			$array['announcementList'][$i]['is_like']        =  is_null($result['is_like']) ? '' : $result['is_like'];
			$array['announcementList'][$i]['total_like']     =  $result['total_like'];
			$array['announcementList'][$i]['like_emp_list']  =  is_null($result['like_emp_list']) ? '' : $result['like_emp_list'];								 
			$array['announcementList'][$i]['departmentName'] =  $result['departmentName'];
			$array['announcementList'][$i]['dept_image']     =  base_url().$result['dept_image'];
                        $array['announcementList'][$i]['total_comment']  =  $result['total_comment'];								 
              $i++;
          }                      
                 $array['respCode'] =  "200";                                    
                 $array['response'] =  "success";
        } else {
			  $array['respCode'] =  "201"; 
                 $array['response'] =  'noDataFound';					
                 $array['msg']      =  'No data found';         
        }
        echo json_encode($array);
        exit;
    }
    
	// calculate time for announcement
    function timePeriodAnnc($time_ago)
	{            
          $time_ago = strtotime($time_ago);
          $currYear = date("Y",strtotime("now")); 
          $annYear = date("Y",$time_ago);
		$cur_time   = time();
		$time_elapsed   = $cur_time - $time_ago;
		$seconds    = $time_elapsed ;
		$minutes    = round($time_elapsed / 60 );
		$hours      = round($time_elapsed / 3600);
		$days       = round($time_elapsed / 86400 );
		$weeks      = round($time_elapsed / 604800);
		$months     = round($time_elapsed / 2600640 );
		$years      = round($time_elapsed / 31207680 );
		// Seconds
		if($seconds < 60){
			return "$seconds ago";
		}
		//Minutes
		else if($minutes < 60){			
		     return "$minutes mins ";			
		}
		//Hours
		else if($hours < 24){			
				return "$hours hrs";		
		}
		//Days
		else if($days == 1){               
               $annDate = 'Yesterday ';  
               $annTime = date("g:i A",$time_ago); 
			return $annDate."at ".$annTime;		
		}
		else{
               if($annYear < $currYear){
                    $annDate = date("j M Y ",$time_ago);  
                    $annTime = date("g:i A",$time_ago);                    
               } else {
                    $annDate = date("j M ",$time_ago);  
                    $annTime = date("g:i A",$time_ago);
               }
			return $annDate."at ".$annTime;
		}
    }
  
  // Announcement Like Api
  function likeAnnouncement(){
     
	  if($this->input->post('is_like') == 1){
      
	      $data['emoji_content']   =  'Like';		
		 $data['announcement_id'] =  $this->input->post('announcement_id');		
           $data['emp_id']          =  $this->input->post('emp_id');	
           $data['is_like']         =  $this->input->post('is_like');					  
			   
		 $resultLike = $this->announcement_model->check_like_exist($data['emp_id'],$data['announcement_id']);
			   
		 if(!empty($resultLike)){
			 $where="emp_id='".$data['emp_id']."'  and announcement_id = '".$data['announcement_id']."'";
		      $this->parent_model->query_update('announcement_like', $data, $where);
		 } else {
			 $this->parent_model->query_insert('announcement_like', $data);
           }			
        
          $postData   =  $this->announcement_model->get_announcement_like_data($data['announcement_id'],$this->input->post('emp_id'));
        	
	     echo json_encode(array('respCode'=>"200",'response' => "success",'msg'=>"like successfuly",'data'=>$postData ));
	     exit;
		 
    } else {		          	  
	       $this->db->where('emp_id', $this->input->post('emp_id'));
		  $this->db->where('announcement_id', $this->input->post('announcement_id'));
            $this->db->delete('announcement_like');
            $postData  =  $this->announcement_model->get_announcement_like_data($this->input->post('announcement_id'),$this->input->post('emp_id'));		
		  echo json_encode(array('respCode'=>"200",'response' => "success",'msg'=>"unlike successfuly",'data'=>$postData));
		  exit;
		}
    }
	
    // Birthday Like Api
  function birthdayLike(){
     
     if($this->input->post('is_like') == 1){
    
         $data['emoji_content']   =  '';		
         $data['birthday_emp_id'] =  $this->input->post('birthday_emp_id');		
         $data['emp_id']          =  $this->input->post('emp_id');	
         $data['is_like']         =  $this->input->post('is_like');
         $data['birth_date']      =  $this->input->post('birth_date');					  
                
         $resultLike = $this->announcement_model->check_birthday_like_exist($data['birthday_emp_id'],$data['emp_id'],$this->input->post('birth_date'));
                
         if(!empty($resultLike)){
              $where="emp_id='".$data['emp_id']."'  and birthday_emp_id = '".$data['birthday_emp_id']."'";
              $this->parent_model->query_update('birthday_like', $data, $where);
         } else {
              $this->parent_model->query_insert('birthday_like', $data);
         }			
      
        $postData   =  $this->announcement_model->get_birthday_like_data($this->input->post('birthday_emp_id'),$this->input->post('emp_id'),$this->input->post('birth_date'));
      
        echo json_encode(array('respCode'=>"200",'response' => "success",'msg'=>"like successfuly",'data'=>$postData ));
        exit;
         
  } else {		          	  
          $this->db->where('birthday_emp_id', $this->input->post('birthday_emp_id'));
          $this->db->where('emp_id', $this->input->post('emp_id'));
          $this->db->where('birth_date',$this->input->post('birth_date'));
          $this->db->delete('birthday_like');
          $postData  =  $this->announcement_model->get_birthday_like_data($this->input->post('birthday_emp_id'),$this->input->post('emp_id'),$this->input->post('birth_date'));		
         
          echo json_encode(array('respCode'=>"200",'response' => "success",'msg'=>"unlike successfuly",'data'=>$postData));
          exit;
        }
  }
/************************END ARK TIMELINE API ******************/

//Local Convenc Api AMAN---------------------

function sendTripDetail(){
      //echo json_encode(round($this->input->post('distance'),2));die;
     $arr=array();
     $arr['empId']=$this->input->post('empId');
     $arr['startLat']=$this->input->post('startLat');
     $arr['startLang']=$this->input->post('startLang');
     $arr['endLat']=$this->input->post('endLat');
     $arr['endLang']=$this->input->post('endLang');
     $arr['type']=$this->input->post('type');
     $arr['distance']=round($this->input->post('distance'),2);
     $arr['status']=$this->input->post('status');
     if($this->input->post('type')=='Car'){
          $arr['amount']=round($this->input->post('distance')*8);
     }else{
          $arr['amount']=round($this->input->post('distance')*4);
     }
     $insertSql= $this->db->insert('local_convence_tbl',$arr);

     if($this->db->affected_rows()){
          $array['response']='success';
          $array['amount']=$arr['amount'];
          $array['distance']=$arr['distance'];

     }else{
          $array['response']='fail';
     }
     echo json_encode($array);die;
}
function viewTripDetail(){
          $array=array();
          $empId=$_POST['empId'];
          $tripDetails=$this->db->query("SELECT * FROM local_convence_tbl WHERE local_convence_tbl.empId='".$empId."' ")->result_array();
          if($tripDetails){
               $array['response']="success";
               $array['data']=$tripDetails;
          }else{
               $array['response']="fail";
          }
     echo json_encode($array);
}
 function removeVideoData($htmlstring){       
     
     $dom = new DOMDocument;                
     $html  = $htmlstring;
     
     $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
               
     // Find any element which is a link
    $imgNode = $dom->getElementsByTagName('img'); 
     $nodes = $dom->getElementsByTagName('video'); 
     $videotag = []; 
       //print_r($nodes);        
     // Loop the elements
     foreach ($imgNode as $img)               
     { 
          $img->setAttribute('style', 'width:100%;'); 
     }    
           
     
     foreach ($nodes as $node)               
     { 
         $videotag[] = $node;  
     }
     
     foreach($videotag as $vtag) {
         $vtag->parentNode->removeChild($vtag);
     }

     $html = $dom->saveHTML();               
     return $html;
 }

 function getVideoLink($htmlstring = ''){       
     
     $dom = new DOMDocument;                
     $html  = $htmlstring;
     $videoUrl = [];
     $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
               
     // Find any element which is a link
     $nodes = $dom->getElementsByTagName('video'); 
     $videotag = []; 
       //print_r($nodes);        
     // Loop the elements
     $count = 0;
     foreach ($nodes as $node)               
     {      
         $link    = $node->getAttribute('src');
         //$poster  = $node->getAttribute('poster');         
         //$videoArray[$count]['banner'] = $poster;
         $videoUrl  = $link ;
         $count++;
     }
     
    /* foreach($videotag as $vtag) {
         $vtag->parentNode->removeChild($vtag);
     } */
     return $videoUrl;
 }
 function mobileLocationInsert(){
  
    //echo json_encode(round($this->input->post('distance'),2));die;
  
   $arr=array();
     $arr['empId']=$this->input->post('empId');
  
   $arr['latitude']=$this->input->post('latitude');
   
  $arr['longitude']=$this->input->post('longitude');
    
  $arr['mobileTimeStamp']=$this->input->post('mobileTimeStamp');
    
  $insertSql= $this->db->insert('location_track_tbl',$arr);
  
   if($this->db->affected_rows()){
     
     $array['response']='success';

     }else{
   
       $array['response']='fail';
     }
  
   echo json_encode($array);die;

}
   

// cancel leave Api

function CancelLeaveRequest()
     {
               $data = $this->input->post();
               if($data){
                    $empId = $data['empId'];
                    $id    = $data['id'];
                    $employeedata = $this->attendance_model->empLeaveData($empId,$id);
                    
                    if($employeedata[0]['status'] == "A"){
                    
                         $data_update['status']   = "P" ;
                         $data_update['approved_status']   = 1 ;
                         $data_update['cancel_remarks']   =  $data['cancel_remarks'];
                         $data_update['cancelled_status'] =  2;
                         $reportingto = $employeedata[0]['requestTo'];
                         $success = $this->attendance_model->cancelLeaveUpdate($data_update,$id);

                         $result = $this->attendance_model->getManagerDetail($reportingto);
                         $empDetails = $this->attendance_model->getManagerDetail($this->input->post('empId'));
                              //send Notification Email to Manager

                         $subject  = "Approved Leave Cancel Request :"." ".$empDetails['0']['empFname'].' '.$empDetails['0']['empLname'];
                         $manager  = $result['0']['empFname'].' '.$result['0']['empLname'];
                         $to       = $result['0']['empEmailOffice'];
                         
                         $fromDate = date('d-F-Y',strtotime($data['fromDate']));
                         $toDate   = date('d-F-Y',strtotime($data['toDate']));

                         $data = array(
                              'SITE_LOGO_URL'   => base_url().SITE_IMAGEURL.'logo.png',
                              'USER'            => $empDetails['0']['empFname'].' '.$empDetails['0']['empLname'],
                              'Manager'         => $manager,
                              'To'              => $to,
                              'leaveType'       => $employeedata[0]['leaveType'],
                              'fromDate'        => date('d-F-Y',strtotime($employeedata[0]['fromDate'])),
                              'toDate'          => date('d-F-Y',strtotime($employeedata[0]['toDate'])),
                              'cancelRemarks'   => $this->input->post('cancel_remarks') ? $this->input->post('cancel_remarks') : "N/A",
                              );
                         $htmlMessage =  $this->parser->parse('emails/approve_leave_cancel', $data, true);
                         

                         $this->myemail->sendEmail($to,$subject, $htmlMessage, '', '');
                         echo json_encode(array('respCode'=>"200",'response' => "success",'msg'=>"Request Send Successfully"));
                    }else{

                         $data_update['status']   = "R" ;
                         $data_update['approved_status']   = 0 ;
                         // $data_update['cancel_remarks']   =  $data['cancelremarks'];
                         $data_update['cancelled_status'] =  1;
                        
                         $success = $this->attendance_model->cancelLeaveUpdate($data_update,$id);
                         echo json_encode(array('respCode'=>"200",'response' => "success",'msg'=>"Request Send Successfully"));


                       // echo 'success';
                    }
             }else{
               echo json_encode("unsuccess");
             }
     }

     //Modification...
     function CancelLeaveResponse()
     {
          $data = $this->input->post();
          if($data){
          // $resonsetype =  $this->input->post('responcetype') ;
               if($this->input->post('responseType')=="declined"){  
                    $data_update['status'] = 'A';
                    $data_update['cancelled_status'] = "0";
                    $data_update['approved_status'] = 1;
               }else {
                    $data_update['status'] = 'R';
                    $data_update['cancelled_status'] = "2";
                    $data_update['approved_status'] = 0;
               }
               $regid =   $this->input->post('id');
                /*  $data_update['status'] = 'R';
                    $data_update['cancelled_status'] = "1";
                    $data_update['approved_status'] = 0;*/
                    $id= $regid;
                    $update_attendance = $this->attendance_model->updateAttendence($data);
                    // echo "<pre>";
                    // print_r($update_attendance);
                    // die();
               
                    $success =$this->attendance_model->cancelLeaveUpdate($data_update,$id);
                    $getinfoleave = $this->attendance_model->getinfoleave($id);
                    // print_r($getinfoleave);die();
                    $empInfo = $this->attendance_model->getManagerDetail($getinfoleave['requestFrom']);
                    $managerInfo = $this->attendance_model->getManagerDetail($getinfoleave['requestTo']);
                    // $update_attendance = $this->attendance_model->updateAttendence($data);

                    //send Response Email to User for Approval of approved leave cancellation
                    $empName  = $empInfo['0']['empFname'].' '.$empInfo['0']['empLname'];
                    $managerName = $managerInfo['0']['empFname'].' '.$managerInfo['0']['empLname'];
                    $to       = $empInfo['0']['empEmailOffice'];
                    $subject  = "Response  From :"." ".$managerName;
               
                    $fromDate = date('d-F-Y',strtotime($data['fromDate']));
                    $toDate   = date('d-F-Y',strtotime($data['toDate']));

                    $data = array(
                         'SITE_LOGO_URL'   => base_url().SITE_IMAGEURL.'logo.png',
                         'USER'            => $managerName,
                         'empName'         => $empName,
                         'To'              => $to,
                         'leaveType'       => $getinfoleave['leaveType'],
                         'fromDate'        => $fromDate,
                         'toDate'          => $toDate,
                         'Remarks'         => $getinfoleave['cancel_remarks'],
                         'responseType'    => $this->input->post('responseType'),
                         );
                    // print_r($data);die();
                    $htmlMessage =  $this->parser->parse('emails/approve_leave_cancel_response', $data, true);
                    $this->myemail->sendEmail($to,$subject, $htmlMessage, '', '');
                   echo json_encode(array('respCode'=>"200",'response' => "success",'msg'=>"Request Accepted Successfully"));
              }else{
                    json_encode("unsuccess");
              }   
     }

     /**
      * [forgotpassword get new password]
      * @return [type] [number]
      */
     public function forgotpassword()
     {
         /* print_r($this->input->post());
          die();*/
          $data = array();
          if($this->input->post('task')=='getPass')
          {
               $result = $this->login_model->check_sadmin_username();
               // pre($result);
               // die; 
               if($result['0']['empId']){
                    //send password here
                    $subject     = 'Your new password ';
                    $name  = $result['0']['empFname'].' '.$result['0']['empLname'];
                    $username    = $result['0']['empId'];
                    //$email2 = strtolower(trim($result['0']['empEmailPersonal']));
                    if($result['0']['empEmailOffice']){
                         $email = strtolower(trim($result['0']['empEmailOffice']));
                    }else{
                        $response = [
                         'respCode' => '401',
                         'response' => 'error',
                         'msg'      => 'Employee Official email is not found.',
                         ];
                         echo json_encode($response);
                    }
                    if(MODE =='live'){
                         $to = $email;
                    }else{
                         $to = EMAILTO;
                    }
                    $pass  = createUserPassword(8,$username);
                    $body_txt    = " Please find your new login Password.<br/><br/> PassWord : ".$pass." <br/><br/> <a href='".site_url('login')."'>Click here</a> to login.";
                    $data = array(
                         'SITE_LOGO_URL' => base_url().IMAGE_DIR.'logo.png',
                         'USER' => $result['0']['empFname'],
                         'SITE_NAME' => SITE_NAME,
                         'MAIL_DATA'=>$body_txt
                         );
                    $htmlMessage =  $this->parser->parse('emails/forgotpassword', $data, true);
                    if($this->myemail->sendEmail($to,$subject,$htmlMessage, ADMIN_EMAIL, ADMIN_NAME)){
                         $where=" empId='".$username."' ";
                         $status = $this->parent_model->query_update(TABLE_EMP, array("empPassword"=> $pass), $where);
                         $response = [
                         'respCode' => '200',
                         'response' => 'success',
                         'msg'      => 'Password has sent to your mail. Kindly Check your Official and Personal Email.',
                         ];
                         echo json_encode($response);
                    } else {
                         $response = [
                         'respCode' => '500',
                         'response' => 'error',
                         'msg'      => 'Email is not sent Please try again later.',
                         ];
                         echo json_encode($response);
                    }
               } else {
                    $response = [
                    'respCode' => '401',
                    'response' => 'error',
                    'msg'      => 'Employee Id is not found.',
                    ];
                    echo json_encode($response);
               }
          } 
     }



/******************** flash news ******************/
     
  function department_news(){
     $empId    =  $this->input->post('emp_id'); 
     $array['data'] =  [];                           
     $newsData      =  $this->announcement_model->dept_news($empId);      
      $i = 0;
      if(count($newsData) > 0 && !empty($newsData)) {
                                           
       foreach($newsData as $result){                                                                  
           $array['data'][$i]['id']                =  $result['id'];
           $array['data'][$i]['heading']           =  $result['heading'];
           $array['data'][$i]['short_content']     =  strip_tags($result['content']);
           $array['data'][$i]['content']           =  $result['content'];
           $array['data'][$i]['dept_name']         =  $result['name'];
           $array['data'][$i]['isCreated']         =  date("d-M-Y", strtotime($result['isCreated']));                                  
           $i++;
         }  
      }
       if(!empty($array['data'])){              
            $array['respCode'] =  "200";                                    
            $array['response'] =  "success";
       } else {
           $array['response'] =  'noDataFound';
           $array['respCode'] =  "201";           
           $array['msg']      =  'No data found'; 
       }
      echo json_encode($array);
      exit;

  }

    /********************* flash news end **************/
 
  function likeAnnouncementData(){
		
		$announcementId   =  $this->input->post('announcementId');
		$empId             =  $this->input->post('empId');
		$response = [];
		
		if($announcementId != '' && $empId !=''){		
			$likeData         =  $this->announcement_model->get_announcement_like_list($announcementId,$empId);
		    $countRecords     =  $likeData['totalrows']; 
			
		  if(count($likeData['resultData']) > 0){
			   $i =0;			   
		    foreach($likeData['resultData'] as $result){
			     $response['data'][$i]["id"]           =  $result['id'];
                 $response['data'][$i]["announcementId"]  =  $result['announcementId'];
				 $response['data'][$i]["emp_id"]	   =  $result['emp_id'];
               //$response['data'][$i]["totalLike"]       =  $result['totalLike'];
                 $response['data'][$i]["is_like"]         =  $result['is_like'];
                 $response['data'][$i]["empName"]         =  $result['empName']; 
                 $response['data'][$i]["empImage"]        =  $result['empImage']; 
				$i++;
			 } 
		   } 
			if(!empty($response)){
				  
                      $response['response']      = 'success';
                      $response['msg']           = 'Data fetch successfully';
                      $response['totalLikes']    =  $countRecords;          
                    
			} else {  
                    $response['response']   =  'fail';
                    $response['msg']        =  'No data found'; 
                
	        }
		}  else {
			   $response['response']   =  'error';
               $response['msg']        =  'Parameter are missing';
		} 
		 echo json_encode($response); 
	}
	
	function announcementCommentList(){
		
		$announcementId    =  $this->input->post('announcementId');
		$empId             =  $this->input->post('empId');
		$response = [];
		
		if($announcementId != ''){			
			$commentDataList  =  $this->announcement_model->get_announcement_comment_list($announcementId,$empId);
		    $countRecords     =  $commentDataList['totalrows']; 
			
		  if(count($commentDataList['resultData']) > 0){
			   $i =0;			   
		    foreach($commentDataList['resultData'] as $result){
			  $response['data'][$i]["id"]              = $result['id'];
                 $response['data'][$i]["announcementId"]  = $result['announcementId'];
                 $response['data'][$i]["comments"]	   = $result['comments'];
                 $response['data'][$i]["createdDate"]     = $this->timePeriod($result['createdDate']);
                 $response['data'][$i]["empName"]         = $result['empName']; 
                 $response['data'][$i]["empImage"]        = $result['empImage']; 
				 $response['data'][$i]["total_like"]  = $result['total_like']; 
				 $response['data'][$i]["is_like"]     = $result['is_like'];
				$i++;
			 } 
		   } 
			if(!empty($response)){
				  
                      $response['response']      = 'success';
                      $response['msg']           = 'Announcement comment fetch successfully';
                      $response['totalComment'] =  $countRecords;          
                    
			} else {  
                    $response['response']   =  'fail';
                    $response['msg']        =  'No data found'; 
                
	        }
		}  else {
			   $response['response']   =  'error';
               $response['msg']        =  'Parameter are missing';
		} 
		 echo json_encode($response); 
	}
	 
	 
	 function saveAnnouncementComment(){
		
		$announcementId    =  $this->input->post('announcementId');
		$empId             =  $this->input->post('empId');
		$comment           =  $this->input->post('comment');
		
		if($announcementId !='' && $empId !=''){
		
			$data['comments']     =  trim($comment);
			$data['emp_id']       =  trim($empId);
			$data['ann_id']      =  trim($announcementId);
			$data['createdDate'] =  date('Y-m-d H:i:s');
			$data['status']      =  1;
			$insertId=$this->parent_model->query_insert('announcement_comments', $data);
        
			if($insertId){
			
					   $response =  [
						   'response'   =>  'success',
						   'msg'        =>  'Announcement comment added successfully',                       
						   'data'       =>  $data, 
						]; 
			
					 
			} else {				
				$response = [
						   'response'   =>  'fail',
						   'msg'        =>  'Failed to add comment',                       
						   'data'       =>  [],
						];				 
			
			}
		} else {
           	 $response = [
							 'response' => 'error',
							 'msg'      => 'Parameter are missing',
						];
        }			
		 echo json_encode($response); 
	}
	
	// Announcement Like Api
    function likeCommentApi(){     
		  if($this->input->post('isLike') == 1){      
			   $data['emoji_content']   =  'Like';		
			   //$data['ann_id']          =  $this->input->post('announcementId');
               $data['comment_id']          =  $this->input->post('commentId');			   
			   $data['emp_id']          =  $this->input->post('empId');	
			   $data['is_like']         =  $this->input->post('isLike');					  
			   $data['createdDate']     =  date('Y-m-d H:i:s');
			   
			 $resultLike = $this->announcement_model->check_comment_like_exist($data['emp_id'],$data['comment_id']);
				   
			 if(!empty($resultLike)){
				  $where = "emp_id='".$data['emp_id']."'  and comment_id = '".$data['comment_id']."'";
				  $this->parent_model->query_update('comment_like', $data, $where);
			 } else {
				  $this->parent_model->query_insert('comment_like', $data);
			   }			
			
			 $postData   =  $this->announcement_model->get_announcement_comment_like_data($data['comment_id'],$this->input->post('empId'));
				
			 echo json_encode(array('respCode'=>"200",'response' => "success",'msg'=>"liked successfuly",'data'=>$postData ));
			 exit;
			 
		} else if($this->input->post('isLike') == 0) {		          	  
				$this->db->where('emp_id', $this->input->post('empId'));
				$this->db->where('comment_id', $this->input->post('commentId'));
				$this->db->delete('comment_like');
				$postData  =  $this->announcement_model->get_announcement_comment_like_data($this->input->post('commentId'),$this->input->post('empId'));		
				echo json_encode(array('respCode'=>"200",'response' => "success",'msg'=>"unliked successfuly",'data'=> $postData));
				exit;
		} else {
			echo json_encode(array('respCode'=>"201",'response' => "error",'msg'=>"Parameter missing",'data'=>'' ));
			exit;			
		}
    }
	
	
	
    function timePeriod($time_ago)
	{  
	    $time_ago = strtotime($time_ago);
		$cur_time   = time();
		$time_elapsed   = $cur_time - $time_ago;
		$seconds    = $time_elapsed ;
		$minutes    = round($time_elapsed / 60 );
		$hours      = round($time_elapsed / 3600);
		$days       = round($time_elapsed / 86400 );
		$weeks      = round($time_elapsed / 604800);
		$months     = round($time_elapsed / 2600640 );
		$years      = round($time_elapsed / 31207680 );
		// Seconds
		if($seconds <= 60){
			return "just now";
		}
		//Minutes
		else if($minutes <=60){
			if($minutes==1){
				return "1 min ago";
			}
			else{
				return "$minutes min ago";
			}
		}
		//Hours
		else if($hours <=24){
			if($hours==1){
				return "an hour ago";
			}else{
				return "$hours hrs ago";
			}
		}
		//Days
		else if($days <= 7){
			if($days==1){
				return "yesterday";
			}else{
				return "$days days ago";
			}
		}
		//Weeks
		else if($weeks <= 4.3){
			if($weeks==1){
				return "a week ago";
			}else{
				return "$weeks weeks ago";
			}
		}
		//Months
		else if($months <=12){
			if($months==1){
				return "a month ago";
			}else{
				return "$months months ago";
			}
		}
		//Years
		else{
			if($years==1){
				return "1 year ago";
			}else{
				return "$years years ago";
			}
		}
    } 
    
	 /******************************* Announcement like and comment Api *******************/
	 
	// Announcement save api
    function saveAnnouncement(){
  
		$data     =  $this->input->post();
	 
		if($data['empId'] != ''){
			$videoLink   = '';
			$videoBanner = '';
			if($data['videoBanner'] !=''){
			   $videoBanner = substr($data['videoBanner'], strrpos($data['videoBanner'], '/') + 1);
			}
			if($data['videoLink'] !=''){
				$videoLink   = '<video class="fr-draggable" controls="" src="'.$data['videoLink'].'" style="width: 600px;">Your browser does not support HTML5 video.</video>';			   
			}
			$dataAnn['image_path']   =  $videoBanner;
			$dataAnn['content']      =  trim($data['content']).$videoLink;
			$dataAnn['heading']      =  trim($data['heading']);
			$dataAnn['addedBy']      =  $data['empId'];
			$dataAnn['isCreated']    =  date('Y-m-d H:i:s');            				
			$dataAnn['emp_id']       =  $data['empId'];
			$dataAnn['status']       =  1;
			$deptData =  $this->announcement_model->get_employee_department_details($data['empId']);
			$dataAnn['dept_id']      =  isset($deptData['id'])? $deptData['id']:'';	
			if($data['empId'] == '20000131') {
				$dataAnn['type']  = 3;
			}  else if(isset($deptData['id']) && $deptData['id'] == '21'){
				$dataAnn['type']  = 1;
			} else if ($deptData['id'] == '23'){
				$dataAnn['type']  = 2;
			} else {
				$dataAnn['type']  = 4;
			}
			
			$insertId = $this->parent_model->query_insert(TABLE_ANNOUNCEMENT, $dataAnn, $where);
               
               // mail start
               $maildata = array(
                    'SITE_LOGO_URL'  =>   base_url().SITE_IMAGEURL.'logo.png',
                    'empToName'      =>   'All',
                    'empFromName'    =>   '',
                    'empToEmail'     => 'everyone@arkinfo.in,everyone@corelindia.co.in,everyone@trendsettersindia.co.in',
                    'empFromEmail'   =>   '',									
                    'SITE_NAME'      =>   SITE_NAME,
                    'departMent'     =>   $deptData['name'],
               );			
                    
                    
               if($data['empId'] == '20000131') {
                    $subject		   =   ucfirst($data['heading']).': message from CEO';
                    $appSubject       =   "Go to ARK Timeline to know more.";
               } else {
                    $subject		   =   ucfirst($data['heading']);
                    $appSubject       =   "Go to ARK Timeline to know more.";                
               }
     
               $templatePath     =   'emails/announcements/announcement'; 
               $res = $this->send_mail($maildata,$subject,$templatePath);
               
               /********* Push Notification App ***************************/

               $deviceData  =  $this->announcement_model->get_all_emp_device_id();         
               $deviceList  =  array_column($deviceData, 'deviceId');           
               $title       =  ucfirst($data['heading']).': new announcement from the '.$deptData['name'];
               
               if(!empty($deviceList)){
               foreach($deviceList as $deviceValue){
                   send_multiple_user_push_notification($title, $appSubject, $deviceValue);  
               }
               }
               /*********************** End Push Notification Ap ***************/

			if($insertId){
					 $response =  [
							   'response'   =>  'success',
							   'msg'        =>  'Announcement added successfully',                       
							   'data'       =>  $data, 
							]; 
			} else {
					 $response =  [
							   'response'   =>  'fail',
							   'msg'        =>  'Failed to add announcement',                       
							   'data'       =>  [],
							]; 				
			}
        } else {
			    $response = [
							 'response' => 'error',
							 'msg'      => 'Parameter are missing',
						];		
		}		
		 echo json_encode($response);
         exit;		
    }
	
    function saveAnnouncementImage(){		
		$imgUrl  = $_POST['annImageUrl'];		
        $imgName = rand() . '_' . time() .'_'.'annImage'.'.jpg';
        $decoded = base64_decode($imgUrl);
        file_put_contents(FCPATH . 'ark_assets/froala/image/' . $imgName, $decoded); 
		$response = [
					'response' => 'success',
					'msg'      => 'Image save successfully',
					'data'     =>  base_url().'ark_assets/froala/image/'.$imgName
				];	  
	    echo json_encode($response);
	 }
     
	function saveAnnouncementVideo(){

	 if($_FILES['file']["name"] != ''){
		    $filename  = explode(".", $_FILES['file']["name"]);  
			$extension = end($filename);
			$configVideo['upload_path'] = 'ark_assets/froala/video'; # check path is correct
			$configVideo['max_size'] = '502400';
			$configVideo['allowed_types'] = 'mp4|mpg|mpeg|MOV|mov'; # add video extenstion on here
			$configVideo['overwrite'] = FALSE;
			$configVideo['remove_spaces'] = TRUE;
			if($extension != 'mp4' || $extension != 'MP4')
			  $video_name = sha1(microtime()) . ".".'mp4';
		    else
			  $video_name = sha1(microtime()) . "." . $extension;
		  
			$configVideo['file_name'] = $video_name;		   
			$this->load->library('upload', $configVideo);
			$this->upload->initialize($configVideo);
            
			// Check server protocol and load resources accordingly.
			if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off") {
				  $protocol = "https://";
			} else {
				  $protocol = "http://";
			}
			
			if (!$this->upload->do_upload('file')) # form input field attribute
			{
				# Upload Failed
				$errors =  $this->upload->display_errors();
				$response = [
					'response' => 'error',
					'msg'      => $errors,
					'data'     => []
				];	
				
			}
			else
			{			  
				$data_file = $this->upload->data();
				$videoLink = $protocol.$_SERVER["HTTP_HOST"].dirname($_SERVER["PHP_SELF"]).'/ark_assets/froala/video/'.$video_name;
				$videoBanner = base_url().$this->getAnnVideoThumbnail($videoLink);
				$response = [
					'response' => 'success',
					'msg'      => 'Video save successfully',
					'data'     =>  array('videoLink'=>$videoLink,'videoBanner'=>$videoBanner)
				];	
			}        		
		   } else {
			      $response = [
							 'response' => 'error',
							 'msg'      => 'Parameter are missing',
						];	
		   }
	   
	   echo json_encode($response);
	   exit;
	}
	
	function getAnnouncementOfEmployee(){ 
        $empId     = $this->input->post('empId');
        //$type    = $this->input->post('type');
        //$offset  = $this->input->post('offset');
		
	  if($empId != ''){
		$deptData         =  $this->announcement_model->get_employee_department_details($empId);
        $announcementList =  $this->announcement_model->getEmpAnnouncementList($deptData['id']);
      
        $array['total_records'] =  $announcementList['totalrows'];
        if(count($announcementList['resultData']) > 0){
           $i =0;
           $videoArr = '';
           $content  =  '';
		foreach($announcementList['resultData'] as $result){
               $videoArr = '';
               $content  =  '';
               if(strpos($result['content'], '<video') !== false) {
                    $videoArr  =  $this->getVideoLink($result['content']);                   
                 }
                    $content   =  $this->removeVideoData($result['content']);
                        
               $array['announcementList'][$i]['content']        =  str_replace('<p data-f-id="pbf" style="text-align: center; font-size: 14px; margin-top: 30px; opacity: 0.65; font-family: sans-serif;">Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a></p>','',$content);
               $array['announcementList'][$i]['video']          =  $videoArr;
               $array['announcementList'][$i]['announcementId'] =  $result['id'];                                     
               $array['announcementList'][$i]['heading']        =  $result['heading'];             
               $array['announcementList'][$i]['type']           =  $result['type'];
               $array['announcementList'][$i]['status']         =  $result['status'];
               $array['announcementList'][$i]['isCreated']      =  date('d-M-Y',strtotime($result['isCreated']));
			   $array['announcementList'][$i]['video_banner']   =  $result['video_banner'] != '' ? base_url().'ark_assets/images/'.$result['video_banner']:'';
               $array['announcementList'][$i]['emp_id']         =  is_null($result['emp_id']) ? '' : $result['emp_id'];
			   $array['announcementList'][$i]['departmentName'] =  $result['departmentName'];
			   $array['announcementList'][$i]['dept_image']     =  base_url().$result['dept_image'];
             $i++;
           }                      
               $array['respCode'] =  "200";                                    
               $array['response'] =  "success";
        } else {
		       $array['respCode'] =  "201";
               $array['response'] =  'fail';
               $array['msg']      =  'No data found';         
        }
	 } else {
         $array['respCode'] =  "203";
         $array['response'] =  'error';
         $array['msg']      =  'Parameter missing';  
     }		 
       echo json_encode($array,JSON_UNESCAPED_SLASHES);
       exit;
    }
	
	function deleteAnouncementApi(){      
	  $data = $this->input->post();
	  if($data['annId'] != ''){
	      $checkStatus = $this->announcement_model->deleteAnnouncementData($data['annId']);
	     if($checkStatus){
			
			$response =  [
						   'response'   =>  'success',
						   'msg'        =>  'Announcement deleted successfully',                       
						  // 'data'       =>  $data, 
						 ]; 
		 } else {
            $response =  [
						   'response'   =>  'error',
						   'msg'        =>  'Fail to delete announcement',                      
						  // 'data'       =>  [], 
						]; 
         }	
	    } else {
          $response = [
				       'response' => 'error',
					   'msg'      => 'Parameter are missing',
					];
	    } 
	   echo json_encode($response);
       exit;
    }	
     
    
   // save news 
   function saveNews(){

     $data      =  array();       
     $data      =  $this->input->post();
     $id        =  $this->input->post('id');
     $empId     =  $this->input->post('empId');
     $dataNews['emp_id']   = $empId;
     $dataNews['heading']  = $data['heading'];
     $dataNews['content']  = $data['content'];
     
     if($empId!= '' && $data['heading'] !='' && $data['content'] !=''){
        $response  =  array();
        $deptData  =  $this->announcement_model->get_employee_department_details($empId);     
        
        $dataNews['dept_id']    =    isset($deptData['id'])? $deptData['id']:'';			
        $dataNews['status']     =    1;
        $dataNews['emp_id']     =    $empId;			
        $dataNews['addedBy']    =    $empId;
        $dataNews['isCreated']  =    date('Y-m-d H:i:s');
    
        $inserId  =  $this->parent_model->query_insert("dept_news", $dataNews);
        if($inserId){
           $response =  [ 'response'=>'success', 'msg' =>'News added successfully']; 
        } else {			
           $response =  [ 'response'=>'fail', 'msg' =>'Failed to add news successfully']; 
        }
        
            // mail start
                $maildata = array(
                    'SITE_LOGO_URL'  =>   base_url().SITE_IMAGEURL.'logo.png',
                    'empToName'      =>   'All',
                    'empFromName'    =>   '',//$this->session->userdata('admin_name'),
                    'empToEmail'     =>   'everyone@arkinfo.in,everyone@corelindia.co.in,everyone@trendsettersindia.co.in',
                    'empFromEmail'   =>   '',									
                    'SITE_NAME'      =>   SITE_NAME,
                    'departMent'     =>   $deptData['name'],
                );			
                
                $subject		    =    ucfirst($data['heading']);
                $appSubject       =   "Go to ARK Timeline to know more.";           
        
                $templatePath     =   'emails/news/news';
                
                $res = $this->send_mail($maildata,$subject,$templatePath);
                
                /********* Push Notification App ***************************/

                $deviceData  =  $this->announcement_model->get_all_emp_device_id();         
                $deviceList  =  array_column($deviceData, 'deviceId');           
                $title       =  ucfirst($data['heading']).': new news from the '.$deptData['name'];
            
                if(!empty($deviceList)){
                foreach($deviceList as $deviceValue){
                    send_multiple_user_push_notification($title, $appSubject, $deviceValue);  
                }
                }
            /*********************** End Push Notification Ap ***************/
          } else {
               $response = ['response' => 'error','msg' =>'Parameter are missing'];
          }
          echo json_encode($response);
          exit;
    }

    function deleteNewsApi(){      
     $data = $this->input->post();
     if($data['newsId'] != ''){
         $checkStatus = $this->announcement_model->deleteNewsData($data['newsId']);
        if($checkStatus){
             
             $response =  [
                               'response'   =>  'success',
                               'msg'        =>  'News deleted successfully',                       
                              // 'data'       =>  $data, 
                             ]; 
         } else {
          $response =  [
                               'response'   =>  'error',
                               'msg'        =>  'Fail to delete news',                      
                              // 'data'       =>  [], 
                            ]; 
       }	
       } else {
        $response = [
                         'response' => 'error',
                          'msg'      => 'Parameter are missing',
                       ];
       } 
      echo json_encode($response);
      exit;
    }

	// fetch video thumbnail from video upload
    function getAnnVideoThumbnail($videoLink){
          /***************Get thumbnail image from uplaod video *************/
            // where ffmpeg is located
            //$ffmpeg = 'C:\\ffmpeg\\bin\\ffmpeg';
			$ffmpeg = '/usr/bin/ffmpeg';
            //video dir
            $video = $videoLink;
            //where to save the image  
            $imageName = strtotime("now").'.jpg';          
            $image = 'ark_assets/images/'.$imageName;
            //time to take screenshot at
            $interval = 6;
            //screenshot size
            $size = '797x448'; //'640x480';
            //ffmpeg command
            $cmd    = "$ffmpeg -i $video -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $image 2>&1";
            $return = `$cmd`; 
            return $image;
    }
	
    
    function send_mail($data,$subject,$path)
    {        
        if(MODE =='live'){
            $to  =  $data['empToEmail'];
            $cc  =  $data['cc'];
        }else{
            $to  =  EMAILTO;
            $cc  =  $data['cc'];
        }	
        $fromEmail       =  isset($data['empFromEmail'])?$data['empFromEmail']:'';
        $fromName        =  isset($data['empFromName'])?$data['empFromName']:'';				
      
        $htmlMessage     =  $this->parser->parse($path,$data, true);				
      
        $mailStatus      =  $this->myemail->sendEmail($to,$subject, $htmlMessage, $fromEmail, $fromName,$cc);	
        return $mailStatus;
    }

function getAllAnnouncement_test(){    
    
       
        $empId   = $this->input->post('empId');
        $type    = $this->input->post('type');
        $offset  = $this->input->post('offset');
        $announcementList       =  $this->announcement_model->getAllAnnouncementList($empId,$type,$offset);
      
        $array['total_records'] =  $announcementList['totalrows'];
       if(count($announcementList['resultData']) > 0){
           $i =0;
           $videoArr = '';
           $content  =  '';
		foreach($announcementList['resultData'] as $result){
               $videoArr = '';
               $content  =  '';
               if(strpos($result['content'], '<video') !== false) {
                    $videoArr  =  $this->getVideoLink($result['content']);                   
                 }
                    $content   =  $this->removeVideoDatatest($result['content']);    
                     
                        
                 $array['announcementList'][$i]['content']        =  str_replace('<p data-f-id="pbf" style="text-align: center; font-size: 14px; margin-top: 30px; opacity: 0.65; font-family: sans-serif;">Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a></p>','',$content);
                 $array['announcementList'][$i]['video']          =  $videoArr;            
                 
               $array['announcementList'][$i]['announcementId'] =  $result['id'];                                     
               $array['announcementList'][$i]['heading']        =  $result['heading'];             
               $array['announcementList'][$i]['type']           =  $result['type'];
               $array['announcementList'][$i]['status']         =  $result['status'];
               $array['announcementList'][$i]['isCreated']      =  $result['isCreated'];
			   $array['announcementList'][$i]['video_banner']   =   $result['video_banner'] != '' ? base_url().'ark_assets/images/'.$result['video_banner']:'';
               $array['announcementList'][$i]['emp_id']         =  is_null($result['emp_id']) ? '' : $result['emp_id'];
               $array['announcementList'][$i]['is_like']        =  is_null($result['is_like']) ? '' : $result['is_like'];
               $array['announcementList'][$i]['total_like']     =  $result['total_like'];
               $array['announcementList'][$i]['like_emp_list']  =  is_null($result['like_emp_list']) ? '' : $result['like_emp_list'];
                                    
			$array['announcementList'][$i]['departmentName'] =  $result['departmentName'];
			$array['announcementList'][$i]['dept_image']     =  base_url().$result['dept_image'];
			$array['announcementList'][$i]['total_comment']     = $result['total_comment'];
			
							 
													
             $i++;
           }                      
               $array['respCode'] =  "200";                                    
               $array['response'] =  "success";
        } else {
		     $array['respCode'] =  "201";
               $array['response'] =  'noDataFound';
               $array['msg']      =  'No data found';         

        }
          echo json_encode($array,JSON_UNESCAPED_SLASHES);
          exit;
    }

 function removeVideoDatatest($htmlstring){      
 
     
     $dom = new DOMDocument;                
     $html  = $htmlstring;
     
     $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
               
     // Find any element which is a link
    // $imgNode = $dom->getElementsByTagName('img'); 
     $nodes   = $dom->getElementsByTagName('video'); 
	 $imgTag  = $dom->getElementsByTagName('img');
     $videotag = []; 
     //print_r($nodes);        
     // Loop the elements
     /* foreach ($imgNode as $img)               
     { 
          $img->setAttribute('style', 'width:100%;'); 
     } */
           
     
     foreach ($nodes as $node)               
     { 
         $videotag[] = $node;  
     }
     
	 foreach($imgTag as $img) {
		$img->removeAttribute('style');
		$img->setAttribute("style", "width:100%");
	 } 
 
     foreach($videotag as $vtag) {
         $vtag->parentNode->removeChild($vtag);
		 
     }

     $html = $dom->saveHTML();   
	 
     return $html;
 }
 function getPolicyList(){                                
          $list = [];
          $i = 0;
          $colorArray = array('#fb9678','#e46a76','#01c0c8','#00c292','#03a9f3','#ab8ce4','#e46a76','#e46a76','#01c0c8','#00c292','#03a9f3','#ab8ce4','#e46a76','#e46a76','#01c0c8');
          $headerColorArray = array('#F39174','#DD6772','#01BAC2','#00BC8D','#03A4EB','#A688DD','#DD6772','#DD6772','#01BAC2','#00BC8D','#03A4EB','#A688DD','#DD6772','#DD6772','#01BAC2');
          $result = $this->employee_model->fetchAllPolicyData();
          
          if(!empty($result)){
               $count = 0;               
               foreach($result as $pdata){
                    $list[$i]['heading']     =   $pdata['name']; 
                    $list[$i]['subHeading']  =   $pdata['handbookTitle']; 
                    $list[$i]['bottomColor']  =  isset($colorArray[$count])?$colorArray[$count]:'#000000'; 
                    $list[$i]['headerColor']  =  isset($headerColorArray[$count])?$headerColorArray[$count]:'#000000'; 
                    /*$list[$i]['path']        =   base_url().'uploads/policy/'.$pdata['handbookPath'];
                    $list[$i]['logoUrl']     =   base_url().'/uploads/policy-logo/'.$pdata['logoPath'];*/

                    $list[$i]['path']        =     base_url().'uploads/policy/'.$pdata['handbookPath'];
                    $list[$i]['logoUrl']     =     base_url().'uploads/policy-logo/'.$pdata['logoPath'];
                    
                    if($count == count($colorArray)-1) {
                         $count = 0;
                     }                    
                    $i++;
                    $count++;
               }     
          }
          if(!empty($list)){
               $array['response'] =  "success";
               $array['msg']      =  'Policy fetch successfully'; 
               $array['data']     =   $list;
          } else {
               $array['response'] = 'fail';
               $array['msg']      = 'No data found';               
               $array['data']     = [];           
          }     
          echo json_encode($array);
          exit;      
     } 

     function empBirthdayLikeList(){
     $birthdayEmpId = $this->input->post('birthday_emp_id');
     $empId         = $this->input->post('emp_id');
     $birthDate     = $this->input->post('birth_date');
     
     if($birthdayEmpId != '' && $empId !='' && $birthDate !=''){
          $likeData  =  $this->announcement_model->get_employee_birthday_like_list($birthdayEmpId,$empId,$birthDate);       
          if(count($likeData['resultData']) > 0){
               $i =0;    
               $response['total_records'] =  $likeData['totalrows'];          
               foreach($likeData['resultData'] as $result){
                    $response['data'][$i]["birthdayEmpId"]   =  $result['birthday_emp_id'];               
                    $response['data'][$i]["empName"]         =  $result['empName']; 
                    $response['data'][$i]["empImage"]        =  isset($result['empImage']) && $result['empImage'] !='' ? base_url().'uploads/candidateDocument/empImage/'.$result['empImage']: 'http://13.126.52.249/space/ark_assets/images/default.jpg';
                    $i++;
               }           
               if(!empty($response)){                 
                    $response['response']      = 'success';
                    $response['msg']           = 'Data fetch successfully';               
               } else {  
                    $response['response']   =  'fail';
                    $response['msg']        =  'No data found'; 
               }
          } else {
               $response['response']   =  'fail';
               $response['msg']        =  'No data found'; 
          }
     }  else {
          $response['response']   =  'error';
          $response['msg']        =  'Parameter are missing';
     } 
      echo json_encode($response); 
  }

  //*************************** CMIG/DMIG *******************//

    function getCmigGoal(){          
		 $empId        =  $this->input->post('empId');
		 $year         =  date("Y");
		 $quarter      =  $this->getQuarterValue();
		 $this->load->model('goal_model');
		if($empId != ''){
               $isActive     =  $this->goal_model->get_enable_feature();
               $deptData     =  $this->goal_model->get_department_details_of_emp($empId);               
               if($deptData['id'] !='' &&  $deptData['company_id'] != 0){
                    $compType = $deptData['company_id'];
               } else {
                    $compType = 1;
               } 
			$cmigFsList   =  $this->goal_model->getAllCmigFsData($year,$quarter,$compType);
		
			$goalCmigList  =  $this->createGoalFsArray($cmigFsList);
			if(!empty($goalCmigList)){
				$response = [
                              'response'   => 'success',
                              'isActive'   =>  $isActive,
						'msg'        => 'CMIG fetch successfully.',
						'data'       => $goalCmigList,
				   ];
			} else {
				$response = [
						'response' => 'fail',
						'msg'      => 'No data found.',
						'data'     => [],
				   ];
				
			}		
		} else {
			$response = [
						'response' => 'error',
						'msg'      => 'parameter missing'                    
				   ];
		}
		echo json_encode($response);
		exit;
    }
    
        function getfmigGoal(){         
           $empId        =  $this->input->post('empId');
           $year         =  date("Y");
           $quarter      =  $this->getQuarterValue();
           $this->load->model('goal_model');
           $deptData     =  $this->goal_model->get_department_details_of_emp($empId);
           $departmentId   =  $deptData['id'];
           $isActive  =      $this->goal_model->get_enable_feature();
          if( $departmentId !='' && $empId != '' && is_numeric($empId)){
               $cmigFsList    =  $this->goal_model->getAllDmigFsData($departmentId,$year,$quarter);      
               $goalCmigList  =  $this->createGoalFsArray($cmigFsList);
               if(!empty($goalCmigList)){
                    $response = [
                              'response'   => 'success',
                              'isActive'   => $isActive,
                              'msg'        => 'DMIG fetch successfully.',
                              'data'       => $goalCmigList,
                       ];
               } else {
                    $response = [
                              'response' => 'fail',
                              'msg'      => 'No data found.',
                              'data'     => [],
                       ];
                    
               }         
          } else {
               $response = [
                              'response' => 'error',
                              'msg'      => 'parameter missing'                    
                       ];
          }
          echo json_encode($response);
          exit;
    }
     
     /**
     *  function     : createGoalFsArray
     *  description : to multidimensional array of goal
     */
      function createGoalFsArray($goalData){
        $goalArr = [];  
        $color  = ['#F8D9D9','#D5F9F4','#FAEDC9']; 
        if(!empty($goalData)){            
           $counter     = 0;           
           $goalArrList = [];
           $count       = 1;
           $colorCount = 0;           
           $goalId  =  isset($goalData[0]['id'])?$goalData[0]['id']:'';
           foreach($goalData as $goalList){  
            if($goalId !=  $goalList['id'] && $goalId != '' ){  
                 $goalArr[] = $goalArrList; 
                 $count = 1;
                 $goalArrList = [];              
            }             
            if($count == 1){
                 $goalArrList['id']               =   $goalList['id'];
                 $goalArrList['goal']             =   $goalList['goal'];
                 $goalArrList['color']            =   isset($color[$colorCount])?$color[$colorCount]:'';
                 $goalArrList['goal_content']     =   $goalList['goal_content'];
                 $colorCount++;
            } 
            $goalArrList['fs'][]     =   array('fs_id'=> $goalList['fs_id'],'fs'=> $goalList['fs'],'fs_content'=> $goalList['fs_content']);
               
            if(count($goalData)-1 ==  $counter){ 
                $goalArr[] = $goalArrList;    
                $count = 1;
                $goalArrList = [];  
            }
             $goalId    =  $goalList['id'];
             $count++;  
             $counter++;
          }
           
        }   
       return $goalArr;
    }
     
    function getQuarterValue(){
        $quarterArray = array(
                         '1'=>'04,05,06',
                              '2'=>'07,08,09',
                              '3'=>'10,11,12',
                              '4'=>'01,02,03'
                        );
          $indexValue = '';
        $month      = date("m");        
         foreach($quarterArray as $key => $qvalues){             
                $monthValue = explode(',',$qvalues);             
                if(in_array($month,$monthValue)){
                    $indexValue =  $key;
                }
        }
          return  $indexValue;
    }

}
?>
