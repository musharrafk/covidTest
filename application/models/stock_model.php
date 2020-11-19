<?php

class stock_model extends parent_model {
	var $base_tbl = TABLE_STOCK;
	var $u_column = 'stockId';


	function get_stock_details($resultType='G')
	{
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

		if($this->input->post('from')!='')
		{
			$addSql .= " and stk.dateRecived>='".$this->input->post('from')."' ";
		}
		if($this->input->post('to')!='')
		{
			$addSql .= " and stk.dateRecived<='".$this->input->post('to')."' ";
		}

		$sql = "select concat(ep.empFname,' ',ep.empLname) as tlName, r.regionName, s.State_Name, c.cityName, u.empId, concat(u.empFname,' ',u.empLname) as emp_name, ed.name, o.id, o.name as outletName, stk.dateRecived, date_format(stk.dateCaptured, '".SQL_DATETIME."') as dateCaptured, stk.skuNo, stk.IMIE, stk.status from ".$this->base_tbl." stk
		inner join ".TABLE_EMP." u on u.empId=stk.empId
		left join ".TABLE_EMP." ep on ep.empId=stk.tlId
		left join ".TABLE_CITY_MASTER." c on c.cityId=stk.cityId
		left join ".TABLE_STATE_MASTER." s on c.state=s.State_Id
		left join tbl_region_state rs on rs.State_Id=s.State_Id
		left join ".TABLE_REGION_MASTER." r on r.regionId=rs.regionId
		left join ".TABLE_DESIGNATION_MASTER." ed on ed.id=stk.designationId
		left join ".TABLE_OUTLET." o on o.id=stk.outletId
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

	function get_competition_details($resultType='G')
	{
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}
		if($this->input->post('from')!='')
		{
			$addSql .= " and insertDate>='".$this->input->post('from')."' ";
		}
		if($this->input->post('to')!='')
		{
			$addSql .= " and insertDate<='".$this->input->post('to')."' ";
		}
		$sql = "select * from tbl_competition where 1=1 ".$addSql;
		
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
	
	function get_sku_details($resultType='G')
	{
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

		if($this->input->post('from')!='')
		{
			$addSql .= " and s.insertedTime>='".$this->input->post('from')." 00:00:00' ";
		}
		if($this->input->post('to')!='')
		{
			$addSql .= " and s.insertedTime<='".$this->input->post('to')." 23:59:59' ";
		}

		$sql = "select s.*,date_format(s.insertedTime,'%d-%m-%y %H:%i') as skudate,c.StockCategoryName,e.empId,concat(e.empFname,' ',empLname) as empName from ".TABLE_SKU_MASTER." s inner join ".TABLE_STOCK_CATEGORY." c on s.stockCategoryId=c.StockCategoryId inner join tbl_emp_master e on e.empId=s.empId
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

	function get_sale_details($resultType='G')
	{
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

		if($this->input->post('from')!='')
		{
			$addSql .= " and stk.saleDate>='".$this->input->post('from')."' ";
		}
		if($this->input->post('to')!='')
		{
			$addSql .= " and stk.saleDate<='".$this->input->post('to')."' ";
		}

		$sql = "select stk.*, concat(ep.empFname,' ',ep.empLname) as tlName, r.regionName, s.State_Name, c.cityName, u.empId, concat(u.empFname,' ',u.empLname) as emp_name, ed.name, o.id, o.name as outletName from ".TABLE_SALE." stk
		inner join ".TABLE_EMP." u on u.empId=stk.empId
		left join ".TABLE_EMP." ep on ep.empId=stk.tlId
		left join ".TABLE_CITY_MASTER." c on c.cityId=stk.cityId
		left join ".TABLE_STATE_MASTER." s on c.state=s.State_Id
		left join tbl_region_state rs on rs.State_Id=s.State_Id
		left join ".TABLE_REGION_MASTER." r on r.regionId=rs.regionId
		left join ".TABLE_DESIGNATION_MASTER." ed on ed.id=stk.designationId
		left join ".TABLE_OUTLET." o on o.id=stk.outletId
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

	function getStockCategory()
	{
		return $this->db->query("select * from ".TABLE_STOCK_CATEGORY)->result_array();	
	}
	
	function add_sale()
	{
		$stock = array();

			
		
		$stock['skuNo'] = trim($this->input->post('suk'));
		$stock['empId'] = trim($this->input->post('empId'));
		$stock['IMIE'] = trim($this->input->post('IMIE'));
		$stock['StockCategoryId'] = trim($this->input->post('StockCategoryId'));
		$stock['invoiceNo'] = trim($this->input->post('invoiceNo'));
		$stock['price'] = trim($this->input->post('price'));
		$stock['custName'] = trim($this->input->post('custName'));
		$stock['custContact'] = trim($this->input->post('custContact'));
		$stock['saleDate'] = date('Y-m-d');

		$stockStatus = $this->db->query("select * from ".TABLE_STOCK." where IMIE='".$stock['IMIE']."' ")->num_rows();
//
		if($stockStatus>0)
		{
			$getStockStatus = $this->db->query("select status from ".TABLE_STOCK." where IMIE='".$stock['IMIE']."' and status='Available'")->num_rows();

			if($getStockStatus>0)
			{
				$user = $this->user_model->getUserInfo($stock['empId']);

				$stock['designationId'] = $user->empDesination;
				$stock['tlId'] = $user->reportingTo;
				$stock['outletId'] = $user->outletId;
				$stock['cityId'] = $user->jobLocation;

				$this->db->insert(TABLE_SALE, $stock);
				$insertID = $this->db->insert_id();


				$iemi = $this->db->query("select outletId, status from tbl_stock where IMIE='".$stock['IMIE']."'")->row();

				$this->db->where('IMIE',$stock['IMIE']);
				$this->db->where('outletId',$iemi->outletId);
				$this->db->update('tbl_stock',array('status'=>'Sold'));

				if(is_numeric($insertID))
				{
					echo json_encode(array('response'=>'success', 'msg'=>'Inserted successfully.'));
				}
				else
				{
					echo json_encode(array('response'=>'Inserted failed', 'msg'=>'Inserted failed'));
				}
			}
			else
			{
				echo json_encode(array('response'=>'IMEI already sold', 'error'=>'IMEI already sold'));
			}
		}
		else
		{
			echo json_encode(array('response'=>'IMEI number not found.', 'error'=>'IMEI number not found.'));
		}
	}

	function perform_stock()
	{
		
		$stock = array();

			
		
		$stock['skuNo'] = trim($this->input->post('suk'));
		$stock['empId'] = trim($this->input->post('empId'));
		//
		$stock['dateRecived'] = trim($this->input->post('dateReceived'));
		$stock['dateCaptured'] = date('Y-m-d H:i:s');;
		//$stock['status'] = trim($this->input->post('status'));
		$stock['status'] = 'Available';
		$stock['IMIE'] = trim($this->input->post('IMIE'));
		
		
		$iemi = $this->db->query("select outletId, status from tbl_stock where IMIE='".$stock['IMIE']."'")->row();
		
		$user = $this->user_model->getUserInfo($stock['empId']);

		if(@$iemi->status=='Sold')
		{
			echo json_encode(array('response'=>'This IMEI has been sold.', 'error'=>'This IMEI has been sold.'));
		}
		elseif(@$iemi->status=='Available')
		{
			echo json_encode(array('response'=>'This IMEI already exists.', 'error'=>'This IMEI already exists.'));
		}
		/*elseif(@$user->outletId==@$iemi->outletId)
		{
			echo json_encode(array('response'=>'IMEI already entered in same outlet.', 'error'=>'This IMEI is in same outlet.'));
		}*/
		else
		{
			if($stock['empId']=='' and $stock['skuNo']=='' and $stock['dateRecived']=='' and $stock['IMIE']=='')
		{
			if($this->input->post('auth'))
			{
				
				echo json_encode(array('response'=>'Enter all required data', 'error'=>'Enter all required data'));
			}
			else
			{
				echo 'Enter all required data';
			}
		}
		else
		{
		
			

				$stock['designationId'] = $user->empDesination;
				$stock['tlId'] = $user->reportingTo;
				$stock['outletId'] = $user->outletId;
				$stock['cityId'] = $user->jobLocation;

				
				
				$this->db->insert(TABLE_STOCK, $stock);
				$insertID = $this->db->insert_id();

				if($this->input->post('auth'))
				{
					if(is_numeric($insertID))
					{
						echo json_encode(array('response'=>'success', 'msg'=>'Inserted successfully.'));
					}
					else
					{
						echo json_encode(array('response'=>'fail', 'msg'=>'Inserted failed'));
					}
				}
				else
				{
					if(is_numeric($insertID))
					{
						return 'success';
					}
					else
					{
							return 'fail';
					}
				}
			}
		}
		
	}

	function get_available_sku()
	{
		$res = $this->db->query("select skuNo from tbl_sku_master where status='1'")->result_array();
		$arr = array();
		foreach($res as $res)
		{
			$arr[]=$res['skuNo'];
		}
		return $arr;
	}

	function get_available_imei()
	{
		return $this->db->query("select IMIE from tbl_stock where status='Available'")->result_array();
	}

	function get_available_sku_imei()
	{
		$res = $this->db->query("select skuNo from tbl_stock where status='Available' group by skuNo")->result_array();

		$arr = array();
		foreach($res as $res)
		{
			$resImie=$this->db->query("select IMIE from tbl_stock where skuNo='".$res['skuNo']."' and status='Available'")->result_array();
			foreach($resImie as $resImie)
			{
				$arr[$res['skuNo']][]=$resImie['IMIE'];
			}
			//$arr[$res['skuNo']][] = $resImie['IMIE'];
		}

		return $arr;
	}

	function get_escalation_type()
	{
		$this->db->query("select * from tbl_escalation_type")->result_array();
	}

	function get_grievance_type()
	{
		$this->db->query("select * from tbl_grievance_type")->result_array();
	}
	
	function chart_color_arr()
	{
		return array('#021c49','#053585','#0c4ebd','#0d64f8','#357df6','#6385bf','#24457d','#143267','#63edff','#0a6498','#0c7ec0','#0b93e1','#25abf7','#64c3f8','#97d9fe','#336a8a','#4593bf','#75aece','#08cee5','#07e2fc');

	}
}
