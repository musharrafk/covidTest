<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class recruitment_uploadmobile_model extends parent_model {

    function get_details($resultType = 'G', $id = '',$isfill=false) {
        $addSql = "  ";
        $addJoin = " ";
        if ($this->session->userdata('user_id') != '10000') {
            // $addSql .= " and m.uploadedBy='" . $this->session->userdata('admin_id') . "'";
            if($this->session->userdata('empDesination')==HR_DESG)
            {

                // $addSql .= " and tr.assigned_to ='" . $this->session->userdata('admin_id') . "'";
                $addSql .= ' and tr.assigned_to  REGEXP "(^|,)'.$this->session->userdata('admin_id').'(,|$)"';
                // SELECT * FROM `recruit_job_allocation` WHERE assigned_to REGEXP "(^|,)13753(,|$)"
            }
            if($this->session->userdata('empDesination')==PM_DESG)
            {

                $addSql .= " and m.sentToPm='1' ";
            }

        }
        if ($id) {
            $addSql .= " and m. id='" . $id . "'";
        }
        if ($this->input->post('filters') != '') {
            $addSql .= " and " . self::decodeFiltersStore($this->input->post('filters'));
        }
        if($isfill)
        {
          $addSql .= " and m.isCandidateFill='" . $isfill . "'";
      }	


      if($this->session->userdata('empDesination')==PM_DESG)
      {
          $addSql .= " and ra.is_selected='Y' and ra.interviewerDest='".HR_DESG."'";
          $addJoin = " LEFT JOIN recruitment_assessment ra on ra.recruitment_id=m.id ";
      }
      $sql = "select m.*,if(m.isCandidateFill=0 , 'No' , 'Yes') as isCandidateFill , tc.name as client_name, m.id, m.mobileNo, m.noofSms, concat(e.empFname,' ',e.empLname) as uploadedBy, date_format(m.uploadedOn,'%d %b, %Y') as uploadedOn,DATE_FORMAT(m.    isCreated,'%d-%b-%Y') as    isCreated, c.cityName , s.State_Name , r.name as region, count(l.mobileNo) as total  , (select group_concat(i.interviewerDest,'#',i.is_selected) from recruitment_assessment i where i.recruitment_id=m.id) as res 

      from recruit_uploadmobile m
      left join tbl_emp_master e on m.uploadedBy=e.empId
      left join recruit_sms_log l on m.mobileNo=l.mobileNo
      LEFT JOIN " . TABLE_CITY . " c on m.city=c.cityId
      LEFT JOIN " . TABLE_RECRUITMENT . " tr on tr.jobCode=m.jobCode
      LEFT JOIN tbl_client tc on tc.id=tr.clientId
      LEFT JOIN " . TABLE_STATE . " s on m.state=s.State_Id
      LEFT JOIN " . TABLE_REGION . " r on s.region=r.id ". $addJoin ."
      where 1=1 " . $addSql . " group by l.mobileNo";
      if ($resultType == 'G') {
        $result = parent::result_grid_array($sql);
    } else {
        if ($id) {
            $result = $this->db->query($sql)->row();
        } else {
            $result = $this->db->query($sql)->result_array();
        }
    }
    return $result;
}

function decodeFiltersCandidate($filters) {
    $sql = ' (';
    $objJson = json_decode($filters);
    foreach ($objJson->{'rules'} as $rules) {
        if ($rules->{'field'} == 'uploadedBy') {
            $sql .= ' ( ';
            $expKey = explode(' ', $rules->{'data'});
            for ($k = 0; $k < count($expKey); $k++) {
                if ($k > 0) {
                    $sql .= " or ";
                }
                $sql .= "  e.empTitle like '%" . $expKey[$k] . "%'";
                $sql .= " or e.empFname like '%" . $expKey[$k] . "%'";
                $sql .= " or e.empLname like '%" . $expKey[$k] . "%'";
            }
            $sql .= ' ) ';
            $sql .= $objJson->{'groupOp'} . ' ';
        } else {
            $sql .= $rules->{'field'} . ' ';
            $sql .= $this->decodeGridOP($rules->{'op'}, $rules->{'data'}) . ' ';
            $sql .= $objJson->{'groupOp'} . ' ';
        }
    }
    $sql = rtrim($sql, $objJson->{'groupOp'} . ' ');
    return $sql . ') ';
}

function getTL($city) {
        //echo $city;die;
    $sql = "select empId,concat(empTitle,' ',empFname,' ',empLname) as empName, empDesination, jobLocation, status from tbl_emp_master Where jobLocation='" . $city . "' and empDesination=" . L1_ID . " and status='1'";
        //echo $sql;
    $result = $this->db->query($sql)->result_array();
    return $result;
}

function getduplicate($mobile) {
    $sql = " select  id  from recruit_uploadmobile where mobileNo='" . $mobile . "' ";
    $result = $this->db->query($sql)->result_array();
    return $result['0']['id'];
}

}
