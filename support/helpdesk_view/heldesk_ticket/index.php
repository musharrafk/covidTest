<!--suraj-->
<?php //print_r("test".$this->session->userdata('admin_id'));die; ?>
<script language="javascript">
setRightHeight();
var base_tbl = '<?php echo TABLE_STATE;?>';
var u_column = 'State_Id';
var site_url = '<?php echo site_url();?>';
var role = '<?php echo $this->session->userdata('role');?>';
var admin_id = '<?php echo $this->session->userdata('admin_id');?>';

$(document).ready(function(){
jQuery("#list").jqGrid({
    url:'<?php echo site_url("helpdesk/get_ticket_details"); ?>',
    postData:{from:$('#from').val(),to:$('#to').val()},
    datatype: "json",
    mtype : "post",
    //'Projects','Clients'
  	colNames:['IDs','Ticked ID','Contact Name','Subject','Request','Group/Agent','Priority','Status','Last replier','Created Date','Updated Date','Action'],
    colModel:[
       {name:'ids',index:'IDs',width:10, sorttype:'int', hidden:true,searchtype:"integer", align:'center',searchrules:{"required":true, "number":true, "maxValue":13}},
	   {name:'id',index:'id', width:5,align:'center',sortable:true,search:false,sorttype:'int'},
        {name:'empFname',index:'empFname', width:5,align:'center',sortable:true,search:true,sorttype:'text'},
		{name:'subject',index:'subject', width:10,align:'center',sortable:true,search:true,sorttype:'text'},
		{name:'req_type',index:'req_type',width:5, align:'center',sortable:true,search:true,sorttype:'text'},
		{name:'roleName',index:'roleName',sortable:true,search:false,width:5,hidden:true, align:'center',sorttype:'text'},
		{name:'priority_type',index:'priority_type',sortable:true,search:true,width:5, align:'center',sorttype:'text'},
		{name:'s_type',index:'s_type',sortable:true,search:true,width:5, align:'center',sorttype:'text'},
		{name:'roleName',index:'roleName',sortable:true,search:false,width:5,align:'center',sorttype:'text'},
		{name:'isCreated',index:'isCreated',sortable:true,search:false,width:5, align:'center'},
		{name:'isUpdated',index:'isCreated',sortable:true,search:false,width:5, align:'center'},
        {name:'action',index:'action',sortable:false,search:false,width:3, align:'center', hidden:true},
		
    ],

    multiselect: false,
    height: $('.content-wrap').height()-50,
    //width: $('.content-wrap').width()-20,
    jsonReader: { repeatitems : false},
    rowNum:20,
    rowList:[20,50,100,500,1000],
    pager: '#pager',
    sortname: 'id',
    viewrecords: true,
    sortorder: "asc",
	autowidth:true,
    //shrinkToFit : false,
    caption:"",
    loadComplete: function()
    {
        //totuser();
        var ids = jQuery("#list").jqGrid('getDataIDs');
            for(var i=0;i < ids.length;i++)
            {
			    var cl = ids[i];
				var empFname = $('#list').jqGrid('getCell',cl,'empFname');
				var empAddedby = $('#list').jqGrid('getCell',cl,'empAddedby');
				var statusVal = $('#list').jqGrid('getCell',cl,'empStatus');
				if(empAddedby == admin_id && statusVal=="Approved")
				{					
					$("#list").jqGrid('setCell', cl, 'statusby','Approved');
					//var statusVal = $('#list').jqGrid('getCell',cl,'status');
				}
				else if(empAddedby == admin_id && statusVal=="Pending")
				{
					$("#list").jqGrid('setCell', cl, 'statusby','Pending');
				}
                var id = $('#list').jqGrid('getCell',cl,'id');
                var group = $('#list').jqGrid('getCell',cl,'name');
				
				if(statusVal=="Approved")
				{
					$("#list").jqGrid('setCell', cl, 'action','<img src="images/aprove.png" title="Apply" style="margin-right:10px;">');
				
                } else if(statusVal=="Pending")
				{
					$("#list").jqGrid('setCell', cl, 'action','<a href="javascript:void(0);" onClick="cancel_apply('+id+')"><img src="images/cancel_icon.png" title="Cancel" style="margin-right:10px;"></a>'); 
                }else if(statusVal=="Rejected")
				{
					$("#list").jqGrid('setCell', cl, 'action','Rejected');
                }
				else
				{
					$("#list").jqGrid('setCell', cl, 'action','<a href="javascript:void(0);" onClick="add_edit('+id+')"><img src="images/add_icon.png" title="Apply" style="margin-right:10px;"></a>');
				}
				// $("#list").jqGrid('setCell', cl, 'action', set_action_area_master(id, 'add_edit(\''+id+'\');'))
				//$("#list").jqGrid('setCell', cl, 'action',' <a href=<?php echo site_url("employee/empDetails");?>/'+uid+' rel="tab"></a>');
            }
    }

});

jQuery("#list").jqGrid('filterToolbar', { defaultSearch:'bw',stringResult: true, searchOnEnter: false, defaultSearch: "cn", groupOp:'AND' });
jQuery("#list").jqGrid('navGrid','#pager',{del:false,add:false,edit:false,search:true},{},{},{},{multipleSearch:true});

});

function add_edit(id)
{
    //alert(id);
	$("#page_loader1").show();
    var dialogOpts = {
        title: (id!=''?'Add':'New')+" Ticket",
        modal: true,
        width: "700px",
        resize: "auto",
        close: dialogClosed,
		draggable: false,
		resizable: false
    };
    $("#page_loader1").hide();
        editDialog = $('<div></div>')
        .load("<?php echo site_url("helpdesk/add_edit_ticket"); ?>", {edit:id})
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
        <h4 class="text-themecolor"><img src="images/department_big.png"> Manage All Ticket </h4>
      </div>
      <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
         <button type="button" class="btn btn-dark d-none d-lg-block m-l-15" href="javascript:void(0)" onclick="#"><i class="fa fa-plus-circle"></i> New Ticket</button>
		  <a href="<?php echo base_url()."helpdesk/helpdesk_master";?>" class="btn btn-dark d-none d-lg-block m-l-15" >View Support List</a>
		   <a href="<?php echo base_url()."helpdesk/helpdesk_status";?>" class="btn btn-dark d-none d-lg-block m-l-15" >View Status List</a>
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
<script>
$(document).ready(function() {
$('.ui-jqgrid .ui-jqgrid-bdiv').perfectScrollbar()
});
</script>