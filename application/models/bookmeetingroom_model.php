<?php error_reporting(1); if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class bookmeetingroom_model extends parent_model {


    // get Device id of employee
    function get_emp_device_id($empId){
        $this->db->select("e.empId,tgu.deviceId");
        $this->db->from('tbl_emp_master as e');
        $this->db->join('tbl_gcm_users as tgu', "e.empId = tgu.empId");
        $this->db->where('e.isActive',1);
        $this->db->where('e.empId',$empId);
        $this->db->group_by('e.empId');
        $result = $this->db->get();
        $resultData = $result->result_array();
        return $resultData;  
    }

    public function get_city_list($value='')
    {
        // $sql="select cityId, cityName from ".TABLE_CITY." where status=1  order by cityName ASC";
        $this->db->select('tbl_mst_city.*','tbl_mst_city.cityName as location');
        $this->db->from('tbl_mst_city');
        $this->db->where('status',1);
        $this->db->order_by('cityName','ASC');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $response = $query->result_array();
            return $response;
        }else{
            return $response = array();
        }
    }

    public function get_meetingroom_city_list($value='')
    {
        // $sql="select cityId, cityName from ".TABLE_CITY." where status=1  order by cityName ASC";
        $this->db->select('tbl_mst_city.*','tbl_mst_city.cityName as location');
        $this->db->from('tbl_mst_city');
        $this->db->join('meeting_rooms','tbl_mst_city.cityId = meeting_rooms.location','right');
        $this->db->where('status',1);
        $this->db->group_by('meeting_rooms.location');
        $this->db->order_by('cityName','ASC');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $response = $query->result_array();
            return $response;
        }else{
            return $response = array();
        }
    }


    public function get_location($empId)
    {
        $this->db->select('tbl_mst_city.cityId');
        $this->db->select('tbl_mst_city.cityName as location');
        $this->db->from('tbl_emp_master');
        $this->db->join('tbl_mst_city','tbl_mst_city.cityId=tbl_emp_master.jobLocation');
        $this->db->where('tbl_emp_master.empId',$empId);
        // $this->db->where('tbl_mst_city.status',1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $response = $query->result_array();
            return $response[0];
        }else{
            return $response = array();
        }
    }
	
    function get_room_list($resultType='G') {
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
        /**
         * created By suraj
         * @var [type]
         */
         // space survey list Create 
        $sql = "SELECT meeting_rooms.* , tbl_mst_city.cityName as location FROM meeting_rooms 
                LEFT JOIN tbl_mst_city ON tbl_mst_city.cityId = meeting_rooms.location
                WHERE 1=1".$addSql;
        /*$sql = "SELECT meeting_rooms.* , tbl_mst_state.State_Name FROM meeting_rooms 
                LEFT JOIN tbl_mst_state ON tbl_mst_state.State_Id = meeting_rooms.location
                WHERE 1=1".$addSql;*/
        // echo $sql;
        // die();
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
    
    // fetch the room details
    function book_room_details($id)
    {
        $this->db->select("*",False);
        $this->db->from("meeting_rooms");
        $this->db->where('id',$id);
        $query = $this->db->get();	 
        if($query->num_rows() > 0){       
            $result = $query->row_array(); 
        } else {
            $result = array(); 
        }
        return $result;
    }

    public function meeting_room_store($meetingRoomData)
    {
          unset($meetingRoomData['check_list']);
        $res = $this->db->insert('meeting_rooms',$meetingRoomData);
        $id  =  $this->db->insert_id();
        if ($res) {
            return $id;
        }else{
            return false;
        }
    }

    public function save_meeting_check_list($checkArr,$lastInsertId){
      foreach ($checkArr as $key => $value) {
        $empArr = [
          'checklist_id'       => $value,
          'meetingroom_id'     => $lastInsertId,
          'status'             => 1,
        ];

        $this->db->insert('meetingroom_checklist',$empArr);
      }
      return true;

    }

    public function update_meeting_check_list($checkArr,$meetingroomId){

            if(count($checkArr > 0)){
                $this->db->set('status', '2');
                $this->db->where('meetingroom_id',$meetingroomId);
                $this->db->update('meetingroom_checklist as mc');
             

                foreach($checkArr as $checks){
                   
                    $this->db->select('id,checklist_id');
                    $this->db->where('meetingroom_id',$meetingroomId); 
                    $this->db->where('checklist_id',$checks); 
                    $this->db->from('meetingroom_checklist');
                    $query = $this->db->get();
                    if ($query->num_rows() > 0) {
                        $response = $query->result_array();
                        $this->db->set('status', '1');
                        $this->db->where('id',$response[0]['id']);
                        $this->db->update('meetingroom_checklist');

                    }else{
                         $checksArr = [
                          'meetingroom_id'   => $meetingroomId,
                          'checklist_id'     => $checks,
                          'status'           => 1,
                        ];
                        $this->db->insert('meetingroom_checklist',$checksArr);
                      
                    }
                }
            }
       

    }

    public function meeting_room_update($meetingRoomData,$id)
    {
        // print_r($meetingRoomData);die();
        $this->db->where('id',$id);
        $this->db->update('meeting_rooms',$meetingRoomData);
        return true;
        /*if ($res) {
            return $res;
        }else{
            return false;
        }*/
    }

    public function get_book_room_list($location,$select_date,$capacity)
    {
        $this->db->select('meeting_rooms.*');
        $this->db->select('tbl_mst_city.cityName as location');
        $this->db->select('tbl_mst_city.cityId');
        $this->db->from('meeting_rooms');
        $this->db->join('tbl_mst_city','tbl_mst_city.cityId = meeting_rooms.location','INNER');
        $this->db->where('location',$location);  
        $this->db->order_by('meeting_rooms.id','DESC');  
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $response = $query->result_array();
            // return $response;
        }else{
            return $response = array();
        }

        $newResponse = array();
        if (count($response) > 0) {
            foreach ($response as $key => $value) {
            $newResponse[]  = $value;
            $newResponse[$key]['booked_lists'] = $this->booking_rooms_details($value['id'],$select_date);
            }
            return $newResponse;
        }
    }

    public function booking_rooms_details($roomid,$select_date=null)
    {
        $this->db->select("booking_rooms.*",False);
        $this->db->select("CONCAT((tbl_emp_master.empFname),(' '),(tbl_emp_master.empLname)) as emp_name");
        $this->db->select("tbl_emp_master.empId as emp_id");
        $this->db->from("booking_rooms");
        $this->db->join("tbl_emp_master","tbl_emp_master.empId = booking_rooms.emp_id");
        $this->db->where('booking_rooms.meeting_room_id',$roomid);
        $this->db->like('booking_date', $select_date);
        $this->db->order_by('booking_rooms.id','DESC');
        $this->db->where('booking_rooms.status !=','C');
        $this->db->where('booking_rooms.requester_flag','!=',1);
        $query = $this->db->get();   
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
        } else {
            $result = array(); 
        }
        return $result;
    }
    
    public function exists_book_room($booking_date,$start_time,$end_time,$meeting_room_id,$booking_room_id=null,$admin_approve='')
    {
       
      // return $booking_room_id;
      if (!empty($booking_room_id) && $booking_room_id != 0 && $admin_approve == '') {
        $this->db->select('*');
        $this->db->from('booking_rooms');
        $this->db->where('id',$booking_room_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
           $result = $query->result_array();
          $result = $result[0];
        }else{
          $result = array();
        }
        if (!empty($result)) {
        $newTypeStart_time  = strtotime($start_time);// 424742474
        $newTypeEnd_time    = strtotime($end_time);// 424742474
        $newDbTypeStartTime = strtotime($result['start_time']);
        $newDbTypeEndTime   = strtotime($result['end_time']);
        if ($newTypeStart_time == $newDbTypeStartTime && $newTypeEnd_time == $newDbTypeEndTime) {
          return 4;
        }
        }
      }
      $cancelStatus = 'C';
      $sql = "select * from booking_rooms WHERE booking_date = '".$booking_date."' AND meeting_room_id = '".$meeting_room_id."' AND status != '".$cancelStatus."' AND (start_time <= '".$start_time."' AND end_time >= '".$start_time."' OR  start_time <= '".$end_time."' AND end_time >= '".$end_time."')";
      /*$sql = "select * from booking_rooms WHERE booking_date = '".$booking_date."' AND meeting_room_id = '".$meeting_room_id."' AND (start_time <= '".$start_time."' AND end_time >= '".$start_time."' OR  start_time <= '".$end_time."' AND end_time >= '".$end_time."') ORDER BY id DESC limit 1";*/
   // echo $sql;
        $data = $this->db->query($sql)->result_array();

        return $data;
    }

    public function save_book_room($bookArr)
    {
      $this->db->insert('booking_rooms',$bookArr);
      return $this->db->insert_id();
    }
    public function save_participants_emp($participantsArr,$lastInsertId)
    {
      foreach ($participantsArr as $key => $value) {
        $empArr = [
          'participants_emp_id' => $value,
          'booking_id'          => $lastInsertId,
        ];
        $this->db->insert('room_emp',$empArr);
      }
      return true;
    }

    // Save bookingroom checklist
    public function save_check_list($checkArr,$lastInsertId)
    {
        // print_r($checkArr);
        // die();
      foreach ($checkArr as $key => $value) {
        $empArr = [
          'meetingroom_checklist_id'       => $value,
          'booking_id'                     => $lastInsertId,
          'status'                         => 1,
        ];
        $this->db->insert('room_check_list',$empArr);
         $this->db->insert_id();
      }
      return true;
    }

    public function update_book_room($bookArr,$booking_id)
    {
        $this->db->where('id',$booking_id);
        $this->db->update('booking_rooms',$bookArr);
        if ($this->db->affected_rows() == true) {
            return true;
        }else{
            return false;
        }
    }  
    public function get_book_room_detail($booking_id)
    {
        $this->db->select("booking_rooms.*",False);
        $this->db->select("CONCAT((tbl_emp_master.empFname),(' '),(tbl_emp_master.empLname)) as emp_name");
        $this->db->select("tbl_emp_master.empId as emp_id");
        $this->db->select("tbl_emp_master.empEmailOffice as empEmailOffice");
        $this->db->from("booking_rooms");
        $this->db->join("tbl_emp_master","tbl_emp_master.empId = booking_rooms.emp_id");
        $this->db->where('booking_rooms.id',$booking_id);
        $query = $this->db->get();   
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
            $result = $result[0];
        } else {
            $result = array(); 
        }
        return $result;
    }

    public function update_participants_emp($participantsArr,$lastUpdateId)
    {
      $this->db->select('*');
      $this->db->where('booking_id',$lastUpdateId);
      $query = $this->db->get('room_emp');
      if ($query->num_rows() > 0) {
        $this->db->where('booking_id',$lastUpdateId);
        $this->db->delete('room_emp');
      }
      foreach ($participantsArr as $key => $value) {
        $empArr = [
          'participants_emp_id' => $value,
          'booking_id'          => $lastUpdateId,
        ];
        $this->db->insert('room_emp',$empArr);
      }
      return true;
    }

    public function update_check_list($checkArr,$lastUpdateId)
    {
      $this->db->select('*');
      $this->db->where('booking_id',$lastUpdateId);
      $query = $this->db->get('room_check_list');
      if ($query->num_rows() > 0) {
        $this->db->where('booking_id',$lastUpdateId);
        $this->db->delete('room_check_list');
      }
      foreach ($checkArr as $key => $value) {
        $empArr = [
          'meetingroom_checklist_id'       => $value,
          'booking_id'                     => $lastUpdateId,
        ];
        $this->db->insert('room_check_list',$empArr);
      }
      return true;
    }

    public function get_book_history($resultType='G')
    {
        $addSql = "  ";
        $uId = $this->session->userdata('admin_id');
        $empIdCondtion = " and emp_id = $uId";
        // $parent_id = " and parent_id = 0";
        $requester_flag = " and requester_flag != 1";
        if($this->input->post('filters')!='')   {   
            $filterResultsJSON = json_decode($this->input->post('filters'));
            $filterArray = get_object_vars($filterResultsJSON);                          
            if(!empty($filterArray['rules']))
            {
                $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
            }
        }   
        /**
         * created By suraj
         * @var [type]
         */
         // space survey list Create 
         // total_persons
        $sql = "SELECT booking_rooms.*,DATE_FORMAT(booking_rooms.booking_date,'%d-%m-%Y') as booking_date,TIME_FORMAT(booking_rooms.start_time, ' %h:%i %p') as start_time,TIME_FORMAT(booking_rooms.end_time, ' %h:%i %p') as end_time,booking_rooms.capacity, meeting_rooms.meeting_room as meeting_room,meeting_rooms.id as meeting_room_id,
                       meeting_rooms.office_address as office_address,
                       tbl_mst_city.cityName as location,
                       CONCAT(tbl_emp_master.empFname, ' ', tbl_emp_master.empLname) as approve_by FROM booking_rooms 
                LEFT JOIN meeting_rooms ON meeting_rooms.id = booking_rooms.meeting_room_id
                LEFT JOIN tbl_emp_master ON tbl_emp_master.empId = booking_rooms.approve_by
                LEFT JOIN tbl_mst_city ON tbl_mst_city.cityId = meeting_rooms.location
                WHERE 1=1".$empIdCondtion.$requester_flag.$addSql;
        // echo $sql;
        // die();
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

    public function booking_reschdule($booking_id)
    {
        $this->db->select("booking_rooms.*",False);
        $this->db->select("meeting_rooms.capacity as max_capacity");
        $this->db->from("booking_rooms");
        $this->db->join('meeting_rooms','booking_rooms.meeting_room_id = meeting_rooms.id','INNER');
        $this->db->where('booking_rooms.id',$booking_id);
        // $this->db->like('booking_date', $select_date);
        // $this->db->where('booking_rooms.booking_date',$select_date);
        $query = $this->db->get();   
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
            return $result = $result[0];
        } else {
            $result = array(); 
        }
        return $result;
    }


    public function save_cancel_remarks($bookArr,$booking_room_id)
    {
        $this->db->where('id',$booking_room_id);
        $this->db->update('booking_rooms',$bookArr);
        if ($this->db->affected_rows() == true) {
            return true;
        }else{
            return false;
        }
    }
    

    public function get_booked_room_list($resultType='G')
    {
        $addSql = "  ";
        $requester_flag = " and requester_flag != 1";
        $uId = $this->session->userdata('admin_id');
            
        if($this->input->post('filters')!='')   {   
            $filterResultsJSON = json_decode($this->input->post('filters'));
            $filterArray = get_object_vars($filterResultsJSON);                          
            if(!empty($filterArray['rules']))
            {
                $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
            }
        }   
        /**
         * created By suraj
         * @var [type]
         */
         // space survey list Create 
        $sql = "SELECT booking_rooms.*,DATE_FORMAT(booking_date,'%d-%m-%Y') as booking_date,DATE_FORMAT(approved_date,'%d-%m-%Y') as approved_date,TIME_FORMAT(booking_rooms.start_time, ' %h:%i %p') as start_time,TIME_FORMAT(booking_rooms.end_time, ' %h:%i %p') as end_time,meeting_rooms.meeting_room as meeting_room,
                       meeting_rooms.office_address as office_address,concat(tbl_emp_master.empFname,' ',tbl_emp_master.empLname) as emp_name,
                       tbl_mst_city.cityName as location,
                       CONCAT(tbl_master.empFname, ' ', tbl_master.empLname) as approve_by 
                       FROM booking_rooms 
                LEFT JOIN meeting_rooms ON meeting_rooms.id = booking_rooms.meeting_room_id
                LEFT JOIN tbl_emp_master ON tbl_emp_master.empId = booking_rooms.emp_id
                LEFT JOIN tbl_emp_master as tbl_master ON booking_rooms.approve_by = tbl_master.empId 
                LEFT JOIN tbl_mst_city ON tbl_mst_city.cityId = meeting_rooms.location
                WHERE 1=1".$requester_flag.$addSql;
        // echo $sql;
        // die();
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

    public function save_approve_reject_remarks($bookArr,$booking_room_id)
    {
        $this->db->where('id',$booking_room_id);
        $this->db->update('booking_rooms',$bookArr);
        if ($this->db->affected_rows() == true) {
            return true;
        }else{
            return false;
        }
    }

    public function get_details_booking_room($booking_id)
    {   

        $this->db->select("meeting_rooms.*",False);
        $this->db->select("booking_rooms.*",False);
        $this->db->select("booking_rooms.id as booking_room_id");
        $this->db->select("booking_rooms.capacity as capacity");
        $this->db->select("meeting_rooms.capacity as max_capacity");
        $this->db->select('tbl_mst_city.cityName as location');
        $this->db->select('tbl_mst_city.cityId');
        $this->db->from("booking_rooms");
        $this->db->join('meeting_rooms','booking_rooms.meeting_room_id = meeting_rooms.id','INNER');
        $this->db->join('tbl_mst_city','tbl_mst_city.cityId = meeting_rooms.location','INNER');
        $this->db->where('booking_rooms.id',$booking_id);
        // $this->db->like('booking_date', $select_date);
        // $this->db->where('booking_rooms.booking_date',$select_date);
        $query = $this->db->get();   
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
            $result = $result[0];
        } else {
            $result = array(); 
        }
        return $result;
    }

    public function getCityName($location)
    {
        $sql =  "SELECT cityName FROM ".TABLE_CITY." WHERE cityId = '5383' ";
        
         $result = $this->db->query($sql)->result_array();
    
      return $result;
    }

    public function get_details_booking_room_new_row($booking_id)
    {   

        $this->db->select("meeting_rooms.*",False);
        $this->db->select("booking_rooms.*",False);
        $this->db->select("booking_rooms.id as booking_room_id");
        $this->db->select("booking_rooms.capacity as capacity");
        $this->db->select("meeting_rooms.capacity as max_capacity");
        $this->db->select('tbl_mst_city.cityName as location');
        $this->db->select('tbl_mst_city.cityId');
        $this->db->from("booking_rooms");
        $this->db->join('meeting_rooms','booking_rooms.meeting_room_id = meeting_rooms.id','INNER');
        $this->db->join('tbl_mst_city','tbl_mst_city.cityId = meeting_rooms.location','INNER');
        $this->db->where('booking_rooms.parent_id',$booking_id);
        // $this->db->like('booking_date', $select_date);
        // $this->db->where('booking_rooms.booking_date',$select_date);
        $query = $this->db->get();   
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
            $result = $result[0];
        } else {
            $result = array(); 
        }
        return $result;
    }
    

    public function participantEmpDetails($booking_id)
    {
      $this->db->select('room_emp.participants_emp_id');
      $this->db->from('room_emp');
      $this->db->where('booking_id',$booking_id);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        $result = $query->result_array();
        $result =  array_column($result, 'participants_emp_id');
      }else{
        $result = array();
      }
      return $result;

    }

    public function MeetingroomChecklist($meetingroom_id)
    {
      $this->db->select('meetingroom_checklist.*');
      $this->db->from('meetingroom_checklist');
      $this->db->where('meetingroom_id',$meetingroom_id);
      $this->db->where('status',1);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        $result = $query->result_array();
        $result =  array_column($result, 'checklist_id');
      }else{
        $result = array();
      }
      return $result;

    }


    // public function checkListDeatils($booking_id)
    // {
    //   $this->db->select('room_check_list.*');
    //   // $this->db->select('check_list.*');
    //   $this->db->from('room_check_list');
    //   // $this->db->join('check_list','check_list.id = room_check_list.checklist_id');
    //   $this->db->where('booking_id',$booking_id);
    //   $query = $this->db->get();
    //   if ($query->num_rows() > 0) {
    //     $result = $query->result_array();
    //     $result =  array_column($result, 'meetingroom_checklist_id');
    //   }else{
    //     $result = array();
    //   }
    //   return $result;

    // }

    public function checkListDeatils($booking_id)
    {
      $this->db->select('room_check_list.*');
      // $this->db->select('check_list.*');
      $this->db->from('room_check_list');
      // $this->db->join('check_list','check_list.id = room_check_list.checklist_id');
      $this->db->where('booking_id',$booking_id);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        $result = $query->result_array();
        $result =  array_column($result, 'meetingroom_checklist_id');
      }else{
        $result = array();
      }
      return $result;

    }

    public function getSwapNotificationData($meetingroom_id='',$booking_id)
    {

        $meeting_booking = [];
           // $this->db->select('id,meeting_room,office_address,capacity,image_url');
           // $this->db->from('meeting_rooms');
           // $this->db->where('location',$location);

           //  $this->db->order_by('id','desc');
           // $query = $this->db->get();
           //  foreach ($query->result() as $row)
           //  {
             
                // DATE_FORMAT(a.workingHours, '%H:%i')) as workingHours
               $this->db->select("br.*, mr.id as meeting_id,mr.meeting_room,mr.office_address,mr.capacity,mr.image_url,IFNULL(br.requester_emp_id,'') as requester_emp_id,IFNULL(br.requester_reason,'') as requester_reason,IFNULL(br.cancel_remarks,'') as cancel_remarks,IFNULL(br.others_participants,'') as others_participants,DATE_FORMAT(br.start_time,'%h:%i %p') as StartTime,DATE_FORMAT(br.end_time,'%h:%i %p') as EndTime,emp.empFname,emp.empLname,IFNULL(concat(ep.empFname,' ',ep.empLname),'') as requesterName,CONCAT(DATE_FORMAT(br.start_time,'%h:%i %p'),' ','to',' ',DATE_FORMAT(br.end_time,'%h:%i %p')) as total_time",false);
               $this->db->from('booking_rooms br');
               $this->db->order_by("id", "desc");
               $this->db->join('tbl_emp_master emp','br.emp_id = emp.empId');
               $this->db->join('meeting_rooms mr','br.meeting_room_id = mr.id');
               $this->db->join('tbl_emp_master ep','br.requester_emp_id = ep.empId ','left');
               $this->db->where('meeting_room_id',$meetingroom_id);
               // $this->db->where('br.status !=','C');
               // $this->db->where('requester_flag !=', 1);
               // $this->db->where('booking_date',$date);
               $this->db->where('br.id',$booking_id);
                   $query = $this->db->get();
                   $booking_room  = $query->result_array();
                   // print_r($booking_room);
                     $i = 0;
                     // $booking_room[$i]['checklist'] ="";
                     // $booking_room[$i]['empIds'] ="";


                    foreach($booking_room as $bk){
                           $this->db->select('rcl.id,cl.accessories'); 
                           $this->db->from('booking_rooms br');
                           $this->db->join('room_check_list rcl','rcl.booking_id = br.id');
                           $this->db->join('meetingroom_checklist mc','mc.id = rcl.meetingroom_checklist_id');
                           $this->db->join('check_list cl','cl.id = mc.checklist_id');
                           $this->db->where('br.id',$bk['id']);
                            $query = $this->db->get();
                            $booking_checklist  = $query->result_array();
                            $booking_room[$i]['checklist'] = $booking_checklist;
                            //print_r($booking_checklist);
                            // die();
                          
                           $this->db->select("IFNULL(concat(emp.empFname,' ',emp.empLname),'') as empName,emp.empFname,emp.empLname,emp.empEmailOffice,emp.empId",false); 
                           $this->db->from('booking_rooms br');
                           $this->db->join('room_emp rm','rm.booking_id = br.id');
                           $this->db->join('tbl_emp_master emp','emp.empId = rm.participants_emp_id');
                           $this->db->where('br.id',$bk['id']);
                           // $this->db->where('rm.status',1);
                           // $this->db->where('rcl.status',1);
                            $query = $this->db->get();
                            $booking_employees  = $query->result_array();
                            $booking_room[$i]['empIds']  = $booking_employees;
                            // print_r($booking_employees);
                           //  // die();
                           if($bk['image_url']){

                                $booking_room[$i]['image'] = base_url('ark_assets/images/meeting_rooms/'.$bk['image_url'].'');
                           }

                            if($bk['status'] == "P"){

                                $booking_room[$i]['status_msg'] = "Booking is in Process";

                            }else if($bk['status'] == "A"){
                                $booking_room[$i]['status_msg'] = "Booking is Approved";

                            }else if($bk['status'] == "R"){

                                $booking_room[$i]['status_msg'] = "Booking is Rejected";
                            }else if($bk['status'] == "C"){

                                $booking_room[$i]['status_msg'] = "Booking is Cancelled";
                            }
                       
                            $i++;    
                    }
                    
                   // $meeting_booking[] = ['meeting_id'=>$row->id,'room_name'=>$row->meeting_room,'office_address'=>$row->office_address,'image'=>base_url('ark_assets/images/meeting_rooms/'.$row->image_url.''),'date'=>$date,'capacity'=>$row->capacity,'booking_rooms'=>$booking_room];

                    // $meeting_booking[] = ['booking_rooms'=>$booking_room];

                  
            // }
            // print_r($meeting_booking);
            // die();
           return $booking_room;
    }
    



  #################### Booking Room Api Start########################################  

    function getMeetingRooms($location,$date)
    {

        $meeting_booking = [];
           $this->db->select('id,meeting_room,office_address,capacity,image_url');
           $this->db->from('meeting_rooms');
           $this->db->where('location',$location);
            $this->db->order_by('id','desc');
           $query = $this->db->get();
            foreach ($query->result() as $row)
            {
             
                // DATE_FORMAT(a.workingHours, '%H:%i')) as workingHours
               $this->db->select("br.*, IFNULL(br.requester_emp_id,'') as requester_emp_id,IFNULL(br.requester_reason,'') as requester_reason,IFNULL(br.cancel_remarks,'') as cancel_remarks,IFNULL(br.others_participants,'') as others_participants,DATE_FORMAT(br.start_time,'%h:%i %p') as StartTime,DATE_FORMAT(br.end_time,'%h:%i %p') as EndTime,emp.empFname,emp.empLname,IFNULL(concat(ep.empFname,' ',ep.empLname),'') as requesterName,CONCAT(DATE_FORMAT(br.start_time,'%h:%i %p'),' ','to',' ',DATE_FORMAT(br.end_time,'%h:%i %p')) as total_time",false);
               $this->db->from('booking_rooms br');
               $this->db->order_by("id", "desc");
               $this->db->join('tbl_emp_master emp','br.emp_id = emp.empId');
               $this->db->join('tbl_emp_master ep','br.requester_emp_id = ep.empId ','left');
               $this->db->where('meeting_room_id',$row->id);
               $this->db->where('br.status !=','C');
               $this->db->where('br.status !=','R');
               $this->db->where('requester_flag !=', 1);
               $this->db->where('booking_date',$date);
                   $query = $this->db->get();
                   $booking_room  = $query->result_array();
                   // print_r($booking_room);
                     $i = 0;
                     // $booking_room[$i]['checklist'] ="";
                     // $booking_room[$i]['empIds'] ="";


                    foreach($booking_room as $bk){
                           $this->db->select('rcl.id,cl.accessories'); 
                           $this->db->from('booking_rooms br');
                           $this->db->join('room_check_list rcl','rcl.booking_id = br.id');
                           $this->db->join('meetingroom_checklist mc','mc.id = rcl.meetingroom_checklist_id');
                           $this->db->join('check_list cl','cl.id = mc.checklist_id');
                           $this->db->where('br.id',$bk['id']);
                            $query = $this->db->get();
                            $booking_checklist  = $query->result_array();
                            $booking_room[$i]['checklist'] = $booking_checklist;
                            //print_r($booking_checklist);
                            // die();
                          
                           $this->db->select("IFNULL(concat(emp.empFname,' ',emp.empLname),'') as empName,emp.empFname,emp.empLname,emp.empEmailOffice,emp.empId",false); 
                           $this->db->from('booking_rooms br');
                           $this->db->join('room_emp rm','rm.booking_id = br.id');
                           $this->db->join('tbl_emp_master emp','emp.empId = rm.participants_emp_id');
                           $this->db->where('br.id',$bk['id']);
                           // $this->db->where('rm.status',1);
                           // $this->db->where('rcl.status',1);
                            $query = $this->db->get();
                            $booking_employees  = $query->result_array();
                            $booking_room[$i]['empIds']  = $booking_employees;
                            // print_r($booking_employees);
                            // die();
                           
                            if($bk['status'] == "P"){

                                $booking_room[$i]['status_msg'] = "Booking is in Process";

                            }else if($bk['status'] == "A"){
                                $booking_room[$i]['status_msg'] = "Booking is Approved";

                            }else if($bk['status'] == "R"){

                                $booking_room[$i]['status_msg'] = "Booking is Rejected";
                            }else if($bk['status'] == "C"){

                                $booking_room[$i]['status_msg'] = "Booking is Cancelled";
                            }
                       
                            $i++;    
                    }
                    
                    $meeting_booking[] = ['meeting_id'=>$row->id,'room_name'=>$row->meeting_room,'office_address'=>$row->office_address,'image'=>base_url('ark_assets/images/meeting_rooms/'.$row->image_url.''),'date'=>$date,'capacity'=>$row->capacity,'booking_rooms'=>$booking_room];

                  
            }
            // print_r($meeting_booking);
            // die();
            return $meeting_booking;
    }


    // function getCity()
    // {

    //    $this->db->select('cityId,cityName');
    //    $this->db->from('tbl_mst_city');
    //    $this->db->where('status',1);
    //    $query = $this->db->get();
    //    return $query->result_array();
    // }

    function getCity()
    {

        $this->db->select('tbl_mst_city.*','tbl_mst_city.cityName as location');
        $this->db->from('tbl_mst_city');
        $this->db->join('meeting_rooms','tbl_mst_city.cityId = meeting_rooms.location','right');
        $this->db->where('status',1);
        $this->db->group_by('meeting_rooms.location');
        $this->db->order_by('cityName','ASC');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $response = $query->result_array();
            return $response;
        }else{
            return $response = array();
        }

    }

    function insertMeetingRoom($data,$checklist,$empids,$id=NULL)
    {
        if($id!='')
        {
          $this->db->where('id', $id);
          $msg     = $this->db->update('booking_rooms', $data);
          $success = $this->db->affected_rows();
            if(isset($checklist)){
               $bookingChecklist =  explode(",",$checklist);
               $checkListArrinfo = $this->bookmeetingroom_model->update_check_list($bookingChecklist,$id);
            }
            if(isset($empids)){
               $participants =  explode(",",$empids);
               $participantsArrinfo = $this->bookmeetingroom_model->update_participants_emp($participants,$id);
            }

          // echo $success;
        // if($success == 1)
        //   {
            $query = $this->db->get_where('booking_rooms', array('id' => $id));
            $data = $query->result_array();
            $data[0]['start_time'] =  $date = date("g:i A", strtotime($data[0]['start_time']));
            $data[0]['end_time']   =   $date = date("g:i A", strtotime($data[0]['end_time']));
            if($data[0]['status'] == "P"){
                 $data[0]['status_msg'] = "Booking is in process";
            }elseif($data[0]['status'] == "A"){
                 $data[0]['status_msg'] = "Booking is Approved";
            }elseif($data[0]['status'] == "R")
            {
                $data[0]['status_msg'] = "Booking is Rejected";
            }elseif($data[0]['status'] == "C"){

                $data[0]['status_msg'] = "Booking is Cancelled";
            }
            if($empids){
            $data[0]['empIds']     = $empids;
            }
            if($checklist){
            $data[0]['checklists'] = $checklist;
            }

               $msg = array('data'=>$data,'booking_id'=>$id);
        // }else{
        //        $msg = "0";
        // }

                return $msg;
        }else{  
            // print_r($data);
            // die();
           $this->db->insert('booking_rooms', $data); 
           $insert_id = $this->db->insert_id();
           if($insert_id){
             $query = $this->db->get_where('booking_rooms', array('id' => $insert_id));
             $data = $query->result_array();
             $data[0]['start_time'] =  $date = date("g:i A", strtotime($data[0]['start_time']));
             $data[0]['end_time']   =  $date = date("g:i A", strtotime($data[0]['end_time']));
             if($data[0]['status'] == "P"){
                
                $data[0]['status_msg'] = "Booking is in process";
             }elseif($data[0]['status'] == "A"){

                $data[0]['status_msg'] = "Booking is Approved";
             }elseif($data[0]['status'] == "R")
             {
                $data[0]['status_msg'] = "Booking is Rejected";
             }elseif($data[0]['status'] == "C"){

                $data[0]['status_msg'] = "Booking is Cancelled";
            }
            if($empids){
            $data[0]['empIds']     = $empids;
            }
            if($checklist){
            $data[0]['checklists'] = $checklist;
            }

            if(isset($checklist)){
               $checklist =  explode(",",$checklist);
               $participantsArrinfo = $this->save_check_list($checklist,$insert_id);
           
               
            }
            if(isset($empids)){
               $participants =  explode(",",$empids);
               $participantsArrinfo = $this->save_participants_emp($participants,$insert_id);
              
            }
      
            $msg = array('data'=>$data,'booking_id'=>$insert_id);
            return  $msg;  
            }
        }

    }

    function delteBooking($id)
    {
        $this->db->set('br.status', 'C');
        $this->db->set('rcl.status', '2');
        $this->db->set('remp.status', '2');
        $this->db->where('br.id', $id);
        $this->db->where('rcl.booking_id', $id);
        $this->db->where('remp.booking_id', $id);
        $this->db->update('booking_rooms as br,room_check_list as rcl,room_emp as remp');
        // $msg = $this->db->affected_rows();
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {

            // generate an error... or use the log_message() function to log your error
        }else{
            $msg =  $this->db->trans_status();
            return $msg;
        }
        // print_r($this->db->trans_status());
        // die();
        
    }

    function checkBookingTime($mr,$d,$st,$et,$empId,$bi=NULL)
    {   

       $this->db->select('start_time,end_time,status');
       $this->db->from('booking_rooms');
       $this->db->where('emp_id',$empId);
       $this->db->where('booking_date',$d);
       $this->db->where('start_time',$st);
       $this->db->where('end_time',$et);
       $this->db->where('status',"P");
       $this->db->where('id !=',$bi);
       $query = $this->db->get();

        $result = $query->result_array();
        if(sizeof($result)>0){
              $result['msg'] =  "You already sended request" ;
              return $result;
        }else{
     
        $sql = "select status,emp_id,id,meeting_room_id,booking_date from booking_rooms WHERE booking_date = '".$d."' AND meeting_room_id = '".$mr."' AND id != '".$bi."' AND (status = 'A'  OR status = 'P')
         AND (start_time <= '".$st."' AND end_time >= '".$st."' OR  start_time <= '".$et."' AND end_time >= '".$et."'OR start_time BETWEEN '".$st."' AND '".$et."' OR end_time BETWEEN '".$st."' AND '".$et."')";
         $data = $this->db->query($sql)->result_array();
       
            return $data;
       }
  
     
      
    }

    // function checkEmpBooking($empId,$date,$startTime,$endTime)
    // {
    //    $this->db->select('start_time,end_time');
    //    $this->db->from('booking_rooms');
    //    $this->db->where('emp_id',$empId);
    //    $this->db->where('emp_id',$empId);
    //    $this->db->where('start_time',$startTime);
    //    $this->db->where('end_time',$endTime);
    //    $query = $this->db->get();
    //    return $query->result_array();
    // }

    function bookingRequester($id,$data,$empids,$checklist)
    {

        $this->db->where('id', $id);
        $query   = $this->db->get('booking_rooms');
        $data1 = $query->row_array();
        // print_r($data1);
        // print_r($data1['requester_emp_id']);
        // die();
       $bookingDate =  $data1['booking_date'];
       $startTime   =  $data1['start_time'];
       $currentdate  = date("Y-m-d");
       $currentTime = date("H:i:s"); 
  
    
    if($currentdate >= $bookingDate &&  $currentTime >= $startTime){
        
     
                    $response =  [
                    'response' => 'error',
                    'msg'      => 'Booking time is closed.',
                    ];
                    echo json_encode($response);
                    die();
    
    }else{            


        if($data1['status'] == "A" && $data1['requester_status'] == "0"  &&  $data1['requester_emp_id'] == "0"){

            $this->db->set('requester_reason', $data['requester_reason']);
            $this->db->set('requester_status', $data['requester_status']);
            $this->db->set('requester_emp_id', $data['requester_emp_id']);
            $this->db->where('id', $id);
            $this->db->update('booking_rooms');
            
            // $msg     = $this->db->update('booking_rooms', $data);
            $success = $this->db->affected_rows();
          
            if($success == "1"){
                    $data['start_time']          = $data1['start_time']; 
                    $data['end_time']            = $data1['end_time']; 
                    $data['booking_date']        = $data1['booking_date']; 
                    $data['meeting_room_id']     = $data1['meeting_room_id'];
                    $data['capacity']            = $data['capacity'];
                    $data['emp_id']              = $data['requester_emp_id'];
                    $data['others_participants'] = $data['others_participants'];
                    $data['reason']              = $data['reason'];   
                    $data['status']              = "P"; 
                    $data['requester_flag']      = "1";
                    $data['parent_id']           = $id;
                    $data['requester_reason']    = 0;
                    $data['requester_status']    = 0;
                    $data['requester_emp_id']    = 0;
                    $data['meals_for_person']    = $data['meals_for_person'];
                  

                    $this->db->insert('booking_rooms', $data); 
                     $insert_id = $this->db->insert_id();
                    if($insert_id){
                     // $query = $this->db->get_where('booking_rooms', array('id' => $insert_id));
                     // $data = $query->result_array();
                  
                        if(isset($checklist)){
                           $checklist =  explode(",",$checklist);
                           $participantsArrinfo = $this->save_check_list($checklist,$insert_id);

                        }
                        if(isset($empids)){
                           $participants =  explode(",",$empids);
                           $participantsArrinfo = $this->save_participants_emp($participants,$insert_id);
                        }

                        // $msg = 1;
                        $msg = array('msg'=>1,'lastinsert_id'=>$insert_id);
                     
                    }
            }
        }else if($data1['status'] == "P"){
            
            $msg = array('msg'=>2);
        }else{
            $msg = array('msg' => 0);
        }
    }

            return  $msg; 

    
    }

    function bookingRequesterStatus($bookingid,$status,$empid)
    {
        $this->db->where('id', $bookingid);
        $query   = $this->db->get('booking_rooms');
        $data1 = $query->row_array();

       $bookingDate =  $data1['booking_date'];
       $startTime   =  $data1['start_time'];
       $currentdate  = date("Y-m-d");
       $currentTime = date("H:i:s"); 
    
    if($currentdate >= $bookingDate &&  $currentTime >= $startTime){
    
                    $response =  [
                    'response' => 'error',
                    'msg'      => 'Booking time is closed.',
                    ];
                    echo json_encode($response);
                    die();
    }else{
      

        if($status == 2){
            $this->db->set('status',"C");
            $this->db->set('requester_status',"2");
            $this->db->where('id', $bookingid);
            $this->db->update('booking_rooms');
             $success = $this->db->affected_rows();
             // echo $success;
             // die();
            if($success == 1)
            {
                 $this->db->set('status',"A");
                 $this->db->set('requester_flag',"0");
                 // $this->db->set('requester_status',"2");
                 $this->db->set('approve_by',$empid);
                 $this->db->set('approved_date',$currentdate);
                 $this->db->where('parent_id', $bookingid);
                 $this->db->update('booking_rooms');
                 $success = $this->db->affected_rows();
                 if($success == "1"){
                    $msg = 1;
                 }
            }
        }else if($status == 3){

                 $this->db->set('requester_status',"3");
                 $this->db->where('id', $bookingid);
                 $this->db->update('booking_rooms');
                 $success = $this->db->affected_rows();
            if($success == 1)
            {
                 $this->db->set('status',"C");
                 $this->db->set('requester_flag',"1");
                 // $this->db->set('requester_status',"3");
                 $this->db->set('approve_by',$empid);
                 $this->db->where('parent_id', $bookingid);
                 $this->db->update('booking_rooms');
                 $success = $this->db->affected_rows();
                 if($success == "1"){
                    $msg = 1;
                 }
            }
           
        }
    }


        if($msg == 1){

                $requesterDetails = $this->fetch_all_employee_list($data1['requester_emp_id']);
                $requestAccepterDetails = $this->fetch_all_employee_list($empid);
                $data['result']  = $this->get_details_booking_room($bookingid);

                  /***************** Start Email sent to Employee *******************/
                  $maildata = array(
                      'SITE_LOGO_URL'  =>   base_url().SITE_IMAGEURL.'logo.png',
                      'empToName'      =>   $requesterDetails[0]['empName'],
                      'empFromName'    =>   $requestAccepterDetails[0]['empName'],
                      'empToEmail'     =>   $requesterDetails[0]['empEmailOffice'],
                      'empFromEmail'   =>   $requestAccepterDetails[0]['empEmailOffice'],
                      'meeting_room'   =>   $data['result']['meeting_room'],
                      'date'           =>   date('d F, Y',strtotime($data['result']['booking_date'])),
                      'start_time'     =>   $data['result']['start_time'],
                      'end_time'       =>   $data['result']['end_time'],
                      'locationName'   =>   $data['result']['location'],
                      'SITE_NAME'      =>   SITE_NAME,
                      'cc'             =>   ''
                  );
                  if ($status == 2) {
                    $maildata['msg']    = 'Your request for '.$data['result']['meeting_room'].' room Swap for date '.date('d F, Y',strtotime($data['result']['booking_date'])).' has been Accepted by '.$maildata['empFromName'];
                  }else{
                    $maildata['msg']    = 'Your request  for '.$data['result']['meeting_room'].' room Swap for date '.date('d F, Y',strtotime($data['result']['booking_date'])).' has been Rejected by '. $maildata['empFromName'];
                  }
                  $templatePath        =  'emails/meeting_room/book_room_email.php'; 
                  $subject             =  $status == 2 ? "Meeting Room Swap Confirmed" : 'Meeting Room Swap Rejected';        
                  $this->send_booking_room_email($maildata,$subject,$templatePath);
                  /***************** End Email sent to HR *******************/

                    /********* Push Notification App ***************************/
                        $locationName = $data['result']['location']; 
                        $locationId = $data['result']['cityId']; 
                        $deviceData  =  $this->get_emp_device_id($data1['requester_emp_id']); 
                        $deviceValue = $deviceData[0]['deviceId']; 
                        $title       =  $status == 2 ? "Meeting Room Swap Confirmed" : 'Meeting Room Swap Rejected';  
                        $appSubject  = [
                          'msg' => $status == 2 ? 'Your request for room swap  has been accepted by  '.$maildata['empFromName'] : 'Your meeting room booking request has been declined by '.$maildata['empFromName'],
                          'locationName' => $locationName,
                          'locatonId'    => $locationId,
                          'date'         => $data['result']['booking_date'],
                          'type'         => $title,

                        ]; 


                        // $appSubject  = $status == 2 ? 'Your request for room swap  has been accepted by  '.$maildata['empFromName']: 'Your meeting room booking request has been declined by '.$maildata['empFromName'];
                        if(!empty($deviceValue)){
                              send_multiple_user_push_notification($title, $appSubject, $deviceValue);  
                        }
                    /*********************** End Push Notification Ap ***************/
            }
            if ($status == 2) {

              
            /***************** Start Email sent to HR For Room Swapping Information *******************/
                $maildata = array(
                    'SITE_LOGO_URL'  =>   base_url().SITE_IMAGEURL.'logo.png',
                    'empToName'      =>   'TeamAD',
                    'empFromName'    =>   $requestAccepterDetails[0]['empName'],
                    'empToEmail'     =>   "teamad@arkinfo.in",
                    'empFromEmail'   =>    $requestAccepterDetails[0]['empEmailOffice'],
                    'date'           =>   date('d F, Y',strtotime($data['result']['booking_date'])),
                    'Swapper'        =>   $requesterDetails[0]['empName'],
                    'meeting_room'   =>   $data['result']['meeting_room'],
                    'start_time'     =>   date("g:i A",strtotime($data['result']['start_time'])),
                    'end_time'       =>   date("g:i A",strtotime($data['result']['end_time'])),
                    'SITE_NAME'      =>   SITE_NAME,
                    'cc'             =>   ''
                );
                $maildata['msg']  =  ''.$maildata['Swapper'].' has swapped the '.$maildata['meeting_room'].' meeting room with '. $requestAccepterDetails[0]['empName'].' for '.$maildata['start_time'].' to '.$maildata['end_time'].' for '.$maildata['date'].'. This is for your information and appropriate action';
                $templatePath     =  'emails/meeting_room/book_room_email.php'; 
                $subject          =  "Booked Meeting Room Swapping";  
                // print_r($maildata);       
                $this->send_booking_room_email($maildata,$subject,$templatePath);

                /************* Start Email sent to HR For Room Swapping Information *****************/
            }

        return $msg;
      
    }


    #################### Booking Room Api Ends######################################## 
    

    // fetch all employee list
    public function fetch_all_employee_list($value='')
    {
        $this->db->select("empId,concat(tem.empFname,' ',tem.empLname) as empName,empImage,empEmailOffice",false);
        $this->db->where('isActive',1);
        $this->db->order_by("tem.empFname", "asc");
        if (!empty($value)) {
        $this->db->where('empId',$value);
        }
        $query = $this->db->get("tbl_emp_master as tem");      
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
        } else {
            $result = array(); 
        }
        return $result;
    }

    // fetch all meeting room  check list
    public function fetch_all_check_list($meetingroom_id = "")
    {

        if($meetingroom_id == ""){
            $this->db->select("id,accessories",false);
            $this->db->where('status',1);
            $query = $this->db->get("check_list");      
            if($query->num_rows() > 0){       
                $result = $query->result_array(); 
            } else {
                $result = array(); 
            }

        }else{
            $this->db->select("mc.id as roomcheckid,check_list.accessories");
            $this->db->from("meetingroom_checklist mc"); 
            $this->db->join('check_list','mc.checklist_id = check_list.id','INNER'); 
            $this->db->where('mc.meetingroom_id',$meetingroom_id);
            $this->db->where('mc.status',1);  
             $query = $this->db->get();    
            if($query->num_rows() > 0){       
                $result = $query->result_array(); 
            } else {

                $result = array(); 
            }
        }
        return $result;
    }


    // // fetch all meeting room  check list
    // public function fetch_all_check_list($meetingroom_id)
    // {
    //     $this->db->select("mc.id as roomcheckid,check_list.accessories");
    //     $this->db->from("meetingroom_checklist mc"); 
    //     $this->db->join('check_list','mc.checklist_id = check_list.id','INNER'); 
    //     $this->db->where('mc.meetingroom_id',$meetingroom_id);
    //     $this->db->where('mc.status',1);  
    //      $query = $this->db->get();    
    //     if($query->num_rows() > 0){       
    //         $result = $query->result_array(); 
    //     } else {
    //         $result = array(); 
    //     }
    //     return $result;
    // }
    // reminder email
    public function get_reminderBookDetail()
    { 
        $current_date = date('Y-m-d');
        $this->db->select("booking_rooms.*",False);
        $this->db->select("meeting_rooms.capacity as max_capacity");
        $this->db->select("meeting_rooms.*");
        $this->db->select('tbl_mst_city.cityName as location');
        $this->db->select('tbl_mst_city.cityId');
        $this->db->from("booking_rooms");
        $this->db->join('meeting_rooms','booking_rooms.meeting_room_id = meeting_rooms.id','INNER');
        $this->db->join('tbl_mst_city','tbl_mst_city.cityId = meeting_rooms.location','INNER');
        $this->db->where('booking_rooms.status','A');
        $this->db->like('booking_date', $current_date);
        // $this->db->where('booking_rooms.booking_date',$select_date);
        $query = $this->db->get();   
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
            // $result = $result[0];
        } else {
            $result = array(); 
        }
        return $result;
    }

    public function exists_book_room_pending($booking_date,$start_time,$end_time,$meeting_room_id,$booking_room_id=null,$admin_approve='')
    {
       
      $cancelStatus  = 'C';
      $sql = "select * from booking_rooms WHERE booking_date = '".$booking_date."' AND meeting_room_id = '".$meeting_room_id."' AND status != '".$cancelStatus."' AND (start_time <= '".$start_time."' AND end_time >= '".$start_time."' OR  start_time <= '".$end_time."' AND end_time >= '".$end_time."' OR start_time BETWEEN '".$start_time."' AND '".$end_time."' OR end_time BETWEEN '".$start_time."' AND '".$end_time."')";
        $data = $this->db->query($sql)->result_array();

        return $data;
    }

    public function check_self_exists_book_room($booking_date,$start_time,$end_time,$meeting_room_id,$booking_room_id=null,$admin_approve='')
    {
       
      $cancelStatus  = 'C';
      $sql = "select * from booking_rooms WHERE booking_date = '".$booking_date."' AND meeting_room_id = '".$meeting_room_id."' AND (start_time <= '".$start_time."' AND end_time >= '".$start_time."' OR  start_time <= '".$end_time."' AND end_time >= '".$end_time."' OR start_time BETWEEN '".$start_time."' AND '".$end_time."' OR end_time BETWEEN '".$start_time."' AND '".$end_time."')";
        $data = $this->db->query($sql)->result_array();

       
        return $data;
    }

    public function exists_check_book_room($booking_date,$start_time,$end_time,$meeting_room_id,$booking_room_id=null,$admin_approve='')
    {
        // return $booking_room_id;
      if (!empty($booking_room_id) && $booking_room_id != 0 && $admin_approve == '') {
        $this->db->select('*');
        $this->db->from('booking_rooms');
        $this->db->where('id',$booking_room_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
           $result = $query->result_array();
          $result = $result[0];
        }else{
          $result = array();
        }
        // return $result;
        if (!empty($result)) {
        $newTypeStart_time  = strtotime($start_time);// 424742474
        $newTypeEnd_time    = strtotime($end_time);// 424742474
        $newDbTypeStartTime = strtotime($result['start_time']);
        $newDbTypeEndTime   = strtotime($result['end_time']);
        if (($newTypeStart_time == $newDbTypeStartTime) && ($newTypeEnd_time == $newDbTypeEndTime) && ($result['status'] == 'A' || $result['status'] == 'R')) {
            return 3;
        }else if ($newTypeStart_time == $newDbTypeStartTime && $newTypeEnd_time == $newDbTypeEndTime) {
          return 4;
        }
        }
    }
    $cancelStatus = 'C';
    $approveStatus = 'A';
    $sql = "select * from booking_rooms WHERE booking_date = '".$booking_date."' AND meeting_room_id = '".$meeting_room_id."' AND status != '".$cancelStatus."' AND (status = '".$approveStatus."' OR status = 'P') AND (start_time <= '".$start_time."' AND end_time >= '".$start_time."' OR  start_time <= '".$end_time."' AND end_time >= '".$end_time."' OR start_time BETWEEN '".$start_time."' AND '".$end_time."' OR end_time BETWEEN '".$start_time."' AND '".$end_time."')";
    // echo $sql;
    $data = $this->db->query($sql)->result_array();

        return $data;
    }

    
    public function update_book_room_new_row($bookArr,$booking_id)
    {
        $this->db->where('parent_id',$booking_id);
        $this->db->update('booking_rooms',$bookArr);
        if ($this->db->affected_rows() == true) {
            return true;
        }else{
            return false;
        }
    }

    public function send_booking_room_email($data,$subject,$templatePath) {
              
        if(MODE =='live'){
              $to  =  $data['empToEmail'];
              $cc  =  $data['cc'] ? $data['cc'] : '';
        }else{
              $to  =  EMAILTO;
              $cc  =  $data['cc'] ? $data['cc'] : '';
        }
          
      $fromEmail     =  isset($data['empFromEmail'])?$data['empFromEmail']:'';
      $fromName      =  isset($data['empFromName'])?$data['empFromName']:'';                
      $htmlMessage   =  $this->parser->parse($templatePath,$data, true);                
      $mailStatus    =  $this->myemail->sendEmail($to,$subject, $htmlMessage, $fromEmail, $fromName,$cc);   
      return $mailStatus;               
    }

    public function MeetingroomNameExists($location,$name,$id='')
    {
     
        $this->db->select('*');
        $this->db->from('meeting_rooms');
        $this->db->where('location',$location);
        $this->db->where('id !=',$id);
        $this->db->like('meeting_room', $name);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
           $result = $query->result_array();
          
        }else{
          $result = array();
        }
     
        return $result;
    }

}

?>