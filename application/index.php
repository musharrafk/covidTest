<!--suraj-->
<?php
$cnt=$this->db->query("select * from tbl_mst_holiday where year='".date('Y')."' order by holidayDate asc")->result_array();
?>
<script language="javascript">
	setRightHeight();
	var base_tbl = '<?php echo TABLE_HOLIDAYS;?>';
	var u_column = 'id';
	var site_url = '<?php echo site_url();?>';
	$(document).ready(function(){
		jQuery("#list").jqGrid({
			url:'<?php echo site_url("master/get_holiday_details_state_wise"); ?>',
			postData:{from:$('#from').val(),to:$('#to').val()},
			datatype: "json",
			mtype : "post",
	//'Projects','Clients'
	colNames:['ID','State' <?php 
	foreach($cnt as $cntCol)
	{
		echo ',"'.$cntCol['holiday'].'"';
	}
	?> ,'Action'],
	colModel:[
	{name:'State_Id',index:'State_Id',width:5, sorttype:'int', hidden:true,searchtype:"integer", align:'center',searchrules:{"required":true, "number":true, "maxValue":13}},
	{name:'State_Name',index:'State_Name', sortable:true,align:'center',width:100,  search:true},
	<?php
	foreach($cnt as $cntData)
	{
		echo '{name:"holi_'.$cntData['id'].'",index:"cnt_'.$cntData['id'].'", width:50,align:"center",search:false,sortable:false},';

	}
	?>
	{name:'action',index:'action',sortable:false,search:false,width:5, align:'center' , hidden:true}
	],

	multiselect: false,
	height: $('.content-wrap').height()-96,
	width: $('.content-wrap').width()-20,
	jsonReader: { repeatitems : false},
	rowNum:20,
	rowList:[20,50,100,500,1000],
	pager: '#pager',
   	//sortname: 'year',
   	viewrecords: true,
    //sortorder: "desc",
	//shrinkToFit : false,
	caption:"",
	loadComplete: function()

	{
		//totuser();
		var ids = jQuery("#list").jqGrid('getDataIDs');
// alert(ids);
for(var i=0;i < ids.length;i++)
{
	var cl = ids[i];
	var statusVal = $('#list').jqGrid('getCell',cl,'status');
	var id = $('#list').jqGrid('getCell',cl,'State_Id');
				// alert(id);
				var nn = $('#list').jqGrid('getCell',cl,'holiday');
				//alert(id);
				var rejectIcon = '<img src="<?php echo base_url('images/disaprove.gif'); ?>">';
				var approveIcon = '<img src="<?php echo base_url('images/aprove1.jpg'); ?>">';
				
				<?php
				foreach($cnt as $cntData1)
				{
					echo 'var holi_'.$cntData1['id'].' = $("#list").jqGrid("getCell",cl,"holi_'.$cntData1['id'].'");';
					echo 'if(holi_'.$cntData1['id'].'=="NO")'
					. '{
						$("#list").jqGrid("setCell",cl,"holi_'.$cntData1['id'].'","<a href='.'javascript:void(0)'.' onclick=edit_state("+id+",1,'.$cntData1['id'].')>"+rejectIcon+"</a>");'
						. '}
						else{
							$("#list").jqGrid("setCell",cl,"holi_'.$cntData1['id'].'","<a href='.'javascript:void(0)'.' onclick=edit_state("+id+",0,'.$cntData1['id'].')>"+approveIcon+"</a>");'
							.'}';



						}
						?>


						$("#list").jqGrid('setCell', cl, 'action', set_action_area_master(id, 'add_edit(\''+id+'\');'))
					}
				}

			});


		jQuery("#list").jqGrid('navGrid','#pager',{del:false,add:false,edit:false,search:true},{},{},{},{multipleSearch:true});


	});



	function edit_state(stateId , status , holidayId)
	{
		if(status==0)
		{
			var conf = confirm("Disable Holiday for this state?");
		}
		else{
			var conf = confirm("Enable Holiday for this state?");
		}

		if(conf==true)
		{
			$.post("master/add_edit_holiday_status" , {stateId:stateId , status:status , holiday:holidayId} , function (data){
					if(data=='success')
				{
					jQuery.noticeAdd({text:"Holiday updated successfully"});
					$("#list").trigger("reloadGrid");
				}
				else{
					jQuery.noticeAdd({text:"Failed to update Holiday"});


				}
			});
		}
	}
	function add_edit_client_region(id)
	{
		$("#page_loader1").show();
		var dialogOpts = {
			title: "Add Client/Region",
			modal: true,
			width: "700px",
			resize: "auto",
			close: dialogClosed,
			draggable: false,
		    resizable: false
		};
		$("#page_loader1").hide();
		editDialog = $('<div></div>')
		.load("<?php echo site_url("master/add_edit_client_region"); ?>", {edit:id})
		.dialog(dialogOpts);
		editDialog.dialog('open');
		return false;
	}

	function add_edit(id)

	{
		$("#page_loader1").show();
		var dialogOpts = {
			title: (id!=''?'Edit':'Add')+" Holiday",
			modal: true,
			width: "700px",
			resize: "auto",
			close: dialogClosed,
			draggable: false,
		    resizable: false
		};
		$("#page_loader1").hide();
		editDialog = $('<div></div>')
		.load("<?php echo site_url("master/add_edit_holiday"); ?>", {edit:id})
		.dialog(dialogOpts);
		editDialog.dialog('open');
		return false;
	}

	function edit_clientRegion(holiId,clientId)
	{
		$("#page_loader1").show();
		var dialogOpts = {
			title: "Edit Client's Region",
			modal: true,
			width: "700px",
			resize: "auto",
			close: dialogClosed,
			draggable: false,
		    resizable: false
		};
		$("#page_loader1").hide();
		editDialog = $('<div></div>')
		.load("<?php echo site_url("master/edit_clientRegion"); ?>", {holiId:holiId,clientId:clientId})
		.dialog(dialogOpts);
		editDialog.dialog('open');
		return false;

	}




	function totuser()
	{
		var str = $('.ui-paging-info').html();
		var spt = str.split('of');
		$('#totuser').html(spt[1]);
	}
	$('.empload').live('click',function(){
		//alert('dd');
		$(this).html('Loading...');
	});
</script>

<div class="page-wrapper"> 
  <!-- ============================================================== --> 
  <!-- Container fluid  --> 
  <!-- ============================================================== -->
  <div class="container-fluid"> 
    <!-- ============================================================== --> 
    <!-- Bread crumb and right sidebar toggle --> 
    <!-- ============================================================== -->
    <div class="row page-titles">
      <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor"><img src="images/holidays_big.png"></img> Manage Holiday Master</h4>
      </div>
      <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Master</a></li>
            <li class="breadcrumb-item active">Manage Holiday Master</li>
			</ol>
		  <button type="button" class="btn btn-dark d-none d-lg-block m-l-15" href="javascript:void(0)" onclick="add_edit('');"><i class="fa fa-plus-circle"></i> Add Holiday</button>
        </div>
      </div>
    </div>
    <!-- ============================================================== --> 
    <!-- End Bread crumb and right sidebar toggle --> 
    <!-- ============================================================== --> 

<!-- Start Page Content --> 
    <!-- ============================================================== --> 
    <!-- Row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body content-wrap">
		  
										
										
		 <!--Grid Start-->

                   <div class="grid_main">
                    <table id="list"></table>

					<div id="pager"></div>
</div>

<!--Grid End-->
		  </div>
		</div>
	  </div>
    </div>
		   		  	    

 
 
</div>
</div>
 </div>  

</div>
