<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class csv_model extends parent_model {
 
    function __construct() {
        parent::__construct();
 
    }
 
    function get_attendance() {     
        $query = $this->db->get('tbl_attendance_log');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }
 
    function insert_csv($data) {
		
        $this->db->insert('tbl_attendance_log', $data);
    }
	function emptytable($table)
	{
	$this->db->truncate($table);
	}
	
	 function insert_update_rent()
    {

        $count=0;
        $fp = fopen($_FILES['userfile']['tmp_name'],'r') or die("can't open file");
        while($csv_line = fgetcsv($fp,1024))
        {
            $count++;
            if($count == 1)
            {
                continue;
            }
            for($i = 0, $j = count($csv_line); $i < $j; $i++)
            {
                $insert_csv = array();
                $insert_csv['empId'] = $csv_line[1];
                $insert_csv['period'] = $csv_line[3];
                $insert_csv['periodFrom'] = date('Y-m-d',strtotime($csv_line[4]));
                $insert_csv['periodTo'] = date('Y-m-d',strtotime($csv_line[5]));
                $insert_csv['amount'] = $csv_line[6];
                $insert_csv['metro'] = $csv_line[7];
            }
            $i++;
			$original_date = $csv_line[5];
$pieces = explode(" ", $original_date);

 $new_date = $pieces['2']."-";
$new_date .= date("m",strtotime($csv_line[5]));
$new_date.= "-".date("d",strtotime($csv_line[5]));


            $data = array(
                'empId' => $insert_csv['empId'] ,
                'period' => $insert_csv['period'],
                'periodFrom' => $insert_csv['periodFrom'],
                'periodTo' => $new_date,
                'amount' => $insert_csv['amount'],
                'metro' => $insert_csv['metro']
                );


            $found = $this->payroll_model->rentdataFound($csv_line[1], $csv_line[3]);
			
            if($found->empId)
            {
			 $data['updatedBy'] = $this->session->userdata('admin_id');
				$data['isModified'] = date('Y-m-d H:i:s');
                $where = "empId ='".$csv_line[1]."'";
                $this->db->update('tbl_rent_api', $data, $where);
            }
            else
            {
                $data['addedBy'] = $this->session->userdata('admin_id');
				$data['isCreated'] = date('Y-m-d H:i:s');
				$this->db->insert('tbl_rent_api', $data);
            }
        }
        fclose($fp) or die("can't close file");
        $data['success']="success";
        $this->session->set_flashdata('notice_msg', ' Record uploaded successfully');
        return $data;
    }
}

?>