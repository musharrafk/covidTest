<?php

class posm_model extends parent_model {
	


	function get_details($resultType='G')
	{
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

		$sql = "select *, date_format(updatedTime,'%d-%m-%y %H:%i') as posmTime from tbl_posm
		where 1=1 ".$addSql;
		
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

	function add_posm()
	{
		$stock = array();
		
		$stock['empId'] = trim($this->input->post('empId'));
		$stock['qty1'] = trim($this->input->post('qty1'));
		$stock['qty2'] = trim($this->input->post('qty2'));
		$stock['qty3'] = trim($this->input->post('qty3'));
		$stock['qty4'] = trim($this->input->post('qty4'));
		$stock['qty5'] = trim($this->input->post('qty5'));

		
		 if($this->input->post('shelfPic')!='')
		 {
			   //urldecode
			$imgName = rand().'_'.time().'.jpg';
			$decoded=base64_decode(($this->input->post('shelfPic')));
			file_put_contents(FCPATH.'uploads/'.$imgName,$decoded);
		
			$stock['shelfPic'] = $imgName;
		}

		 if($this->input->post('totalPosmPic')!='')
		 {
			   //urldecode
			$imgName = rand().'_'.time().'.jpg';
			$decoded=base64_decode(($this->input->post('totalPosmPic')));
			file_put_contents(FCPATH.'uploads/'.$imgName,$decoded);
		
			$stock['totalPosmPic'] = $imgName;
		}

		 if($this->input->post('handsetPic')!='')
		 {
			   //urldecode
			$imgName = rand().'_'.time().'.jpg';
			$decoded=base64_decode(($this->input->post('handsetPic')));
			file_put_contents(FCPATH.'uploads/'.$imgName,$decoded);
		
			$stock['handsetPic'] = $imgName;
		}

		$this->db->insert('tbl_posm', $stock);
			$insertID = $this->db->insert_id();

			if(is_numeric($insertID))
			{
				echo json_encode(array('response'=>'success', 'msg'=>'Inserted successfully.'));
			}
			else
			{
				echo json_encode(array('response'=>'fail', 'msg'=>'Inserted failed'));
			}
	}
}
