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
  	colNames:['IDs','Ticket ID','Subject',/*'Request Type',*/'Brand Name','Assigned To','Email From',/*'Location',*/'Status','Created Date','Created by','Updated Date','TAT'],
    colModel:[
        {name:'ids',index:'IDs',width:10, sorttype:'int', hidden:true,searchtype:"integer", align:'center',searchrules:{"required":true, "number":true, "maxValue":13}},
	    {name:'id',index:'id', width:2,align:'center',sortable:true,search:false,sorttype:'int'},
       
		{name:'subject',index:'subject', width:8,sortable:true,search:true,sorttype:'text'},
	/*	{name:'request_name',index:'request_name',width:5, align:'center',sortable:true,search:true,sorttype:'text'},*/
		{name:'team_name',index:'team_name', width:4,sortable:true,search:true,sorttype:'text'},
		{name:'assigneeName',index:'tem.empFname', width:4,sortable:true,search:true,sorttype:'text'},
		{name:'isCreatedby',index:'isCreatedby', width:3,sortable:true,search:true,sorttype:'text'},
		/*{name:'location',index:'location',sortable:true,search:true,width:5, align:'center',sorttype:'text'},*/
		{name:'s_type',index:'s_type',sortable:true,search:true,align:'center', width:2,stype:"select",sorttype:'int', 
            searchoptions: { 
            value: ":All;new:New;open:Open;pending:Pending;resolved:Resolved;closed:Closed;Credit Hold:Credit Hold", 
            defaultValue: "" 
        }},
		{name:'isCreated',index:'isCreated',sortable:true,search:false,width:4,align:'center',sorttype:'text'},
		{name:'isCreatedby',index:'isCreatedby',sortable:true,search:false,width:3, align:'center'},
		{name:'isUpdated',index:'isUpdated',sortable:true,search:false,width:4, align:'center'},
		{name:'tat',index:'tat',sortable:true,search:false,width:2, align:'center'},
       // {name:'action',index:'action',sortable:false,search:false,width:2, align:'center'},
		
    ],

    multiselect: false,
    height: $('.content-wrap').height()-83,
    //width: $('.content-wrap').width()-20,
    jsonReader: { repeatitems : false},
    rowNum:20,
    rowList:[20,50,100,500,1000],
    pager: '#pager',
    sortname: 'ticked_id',
    viewrecords: true,
    sortorder: "desc",
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
				var subject       =   $('#list').jqGrid('getCell',cl,'subject');  
				
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
				
				$("#list").jqGrid('setCell', cl, 'subject','<a href="javascript:void(0);" onClick="view_ticket('+id+')"><i class="fa fa-info-circle" aria-hidden="true"></i> '+subject+'</a>');
				
				
				
			//	$("#list").jqGrid('setCell', cl, 'action','<a href=<?php echo site_url("helpdesk/viewticked");?>/'+id+'><img src="images/view.png"></a>');
				
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
       // width: "700px",
	    width: "70%",
        resize: "auto",
		
        close: dialogClosed,
		draggable: false,
		resizable: false
    };
    $("#page_loader1").hide();
        editDialog = $('<div></div>')
        .load("<?php echo site_url("helpdesk/add_edit_ticket"); ?>", {id:id})
        .dialog(dialogOpts);
    editDialog.dialog('open');
    return false;
}


function add_request(id)

{
	//alert(id);
    $("#page_loader1").show();
    var dialogOpts = {
        title: (id!=''?'Edit':'Request')+" Type Creation",
        modal: true,
        width: "90%",
        resize: "auto",
        close: dialogClosed,
		draggable: false,
		resizable: false
    };
    $("#page_loader1").hide();
        editDialog = $('<div></div>')
        .load("<?php echo site_url("helpdesk/add_edit_request"); ?>", {edit:id})
        .dialog(dialogOpts);
    editDialog.dialog('open');
    return false;
}


function view_ticket(id)

{
	//alert(id);
    $("#page_loader1").show();
    var dialogOpts = {
        title: (id!=''?'Ticket':'')+"  Details",
        modal: true,
        width: "95%",
        resize: "auto",
        close: dialogClosed,
		draggable: false,
		resizable: false
    };
    $("#page_loader1").hide();
        editDialog = $('<div></div>')
        .load("<?php echo site_url("helpdesk/viewticked"); ?>", {id:id})
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
        <h4 class="text-themecolor"><img src="images/department_big.png"> Ticket List </h4>
      </div>
      <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
        <a href="<?php echo base_url(); ?>helpdesk/emailRead" id="refreshbtn" >Refresh</a>        
	  <!--	<button type="button" class="btn btn-dark d-none d-lg-block m-l-15" href="javascript:void(0)" onclick="add_edit('')"><i class="fa fa-plus-circle"></i> New Ticket</button> -->
		 
		
		 
		 <!-- <a href="<?php echo base_url()."helpdesk/helpdesk_master";?>" class="btn btn-dark d-none d-lg-block m-l-15" >View Support List</a>
		   <a href="<?php echo base_url()."helpdesk/helpdesk_status";?>" class="btn btn-dark d-none d-lg-block m-l-15" >View Status List</a> -->
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

$('#refreshbtn').click(function(){	
	 $("#list").trigger("reloadGrid");
});

});
</script>