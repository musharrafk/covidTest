<!--suraj-->

<script language="javascript">
setRightHeight();
var base_tbl = '<?php //echo TABLE_STATE;?>';
var u_column = 'id';
var site_url = '<?php echo site_url();?>';
$(document).ready(function(){

jQuery("#list").jqGrid({
    url:'<?php echo site_url("helpdesk/get_team_details"); ?>',
    postData:{from:$('#from').val(),to:$('#to').val()},
    datatype: "json",
    mtype : "post",
    //'Projects','Clients'
     colNames:['ID','Brand Name','Department','Customer List','Created Date','Added By','Status','Action','departId'],
    colModel:[
        {name:'id',index:'id',width:10, sorttype:'int', hidden:true,searchtype:"integer", align:'l',searchrules:{"required":true, "number":true, "maxValue":13}},
        {name:'team_name',index:'team_name', width:5,sortable:true,search:true,sorttype:'text'},
		{name:'name',index:'name',sortable:true,search:false,width:5, align:'left'},
        {name:'addCustomer',index:'',sortable:true,search:false,width:2, align:'center'},
        {name:'isCreated',index:'isCreated',sortable:true,search:true,width:5, align:'center',search:false},
		{name:'addedBy',index:'addedBy',sortable:true,search:false,width:5, align:'center'},
		{name:'status',index:'status',sortable:true,search:true,width:5, align:'center',search:false},
        {name:'action',index:'action',sortable:false,search:false,width:3, align:'center'},
		{name:'departId',index:'departId',sortable:true,search:true,width:5, align:'center',hidden:true,search:false},
	
		
    ],

    multiselect: false,
	
    height: $('.content-wrap').height()-75,
    //width: $('.content-wrap').width()-20,
    jsonReader: { repeatitems : false},
    rowNum:20,
    rowList:[20,50,100,500,1000],
    pager: '#pager',
    sortname: '',
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
				var status = $('#list').jqGrid('getCell',cl,'status');
                var id = $('#list').jqGrid('getCell',cl,'id');
                var name = $('#list').jqGrid('getCell',cl,'name');
				var departId = $('#list').jqGrid('getCell',cl,'departId');
               
				$("#list").jqGrid('setCell', cl, 'name','<a href="javascript:void(0);" onclick="add_map_team('+id+','+departId+')">  <img src="images/add_item.png" title="Add" style="margin-right:10px;"></a><a href="javascript:void(0);" onclick="#">'+name+'</a>');
                $("#list").jqGrid('setCell', cl, 'addCustomer','<a href="javascript:void(0);" onclick="add_brand_partner('+id+')">  <img src="images/add_item.png" title="Add Brand partner" style="margin-right:10px;">');

				if(status==1)
				{
				$("#list").jqGrid('setCell', cl, 'status','<img src="images/aprove.png" title="Edit" style="margin-right:10px;">');
				}
				else
				{
					$("#list").jqGrid('setCell', cl, 'status','<img src="images/disaprove.png" title="Edit" style="margin-right:10px;">');
				}
				
				if(status==1)
				{
				$("#list").jqGrid('setCell', cl, 'action','<a href="javascript:void(0);" onClick="add_edit('+id+')"><img src="images/edit-icon.png" title="Edit" style="margin-right:10px;"></a>&nbsp;<a href="javascript:void(0);" onClick="update_status_request('+id+','+status+')"><img src="images/active-ico.png" title="status Update" style="margin-right:10px;"></a>');
				}
				else
				{
					$("#list").jqGrid('setCell', cl, 'action','<a href="javascript:void(0);" onClick="add_edit('+id+')"><img src="images/edit-icon.png" title="Edit" style="margin-right:10px;"></a>&nbsp;<a href="javascript:void(0);" onClick="update_status_request('+id+','+status+')"><img src="images/inactive-ico.png" title="status Update" style="margin-right:10px;"></a>');
				}
				
				//$("#list").jqGrid('setCell', cl, 'Status', set_action_area_master(id, 'update_status(\''+id+'\');'))	
      }
    }

});

jQuery("#list").jqGrid('filterToolbar', { defaultSearch:'bw',stringResult: true, searchOnEnter: false, defaultSearch: "cn", groupOp:'AND' });
jQuery("#list").jqGrid('navGrid','#pager',{del:false,add:false,edit:false,search:true},{},{},{},{multipleSearch:true});
//jQuery("#list").jqGrid('navGrid','#pager',{del:false,add:false,edit:false,search:true},{},{},{},{multipleSearch:true});


});

function add_edit(id)
{
	//alert(id);
    $("#page_loader1").show();
    var dialogOpts = {
        title: (id!=''?'Edit':'')+" Brand Team",
        modal: true,
        width: "700px",
        resize: "auto",
        close: dialogClosed,
		draggable: false,
		resizable: false
    };
    $("#page_loader1").hide();
        editDialog = $('<div></div>')
        .load("<?php echo site_url("helpdesk/add_edit_team"); ?>", {edit:id})
        .dialog(dialogOpts);
    editDialog.dialog('open');
    return false;
}

function add_map_team(id,departId)
{
	//alert(departId);
    $("#page_loader1").show();
    var dialogOpts = {
        title: (id!=''?'Team':'')+" Map with Brand group",
        modal: true,
        width: "700px",
        resize: "auto",
        close: dialogClosed,
		draggable: false,
		resizable: false
    };
    $("#page_loader1").hide();
        editDialog = $('<div></div>')
        .load("<?php echo site_url("helpdesk/add_edit_team1"); ?>", {edit:id,departId:departId})
        .dialog(dialogOpts);
    editDialog.dialog('open');
    return false;
}

function add_brand_partner(id){
   
   //alert(departId);
   $("#page_loader1").show();
    var dialogOpts = {
        title: (id!=''?'Add Partner Team':'Edit Partner Team'),
        modal: true,
        width: "700px",
        resize: "auto",
        close: dialogClosed,
		draggable: false,
		resizable: false
    };
    $("#page_loader1").hide();
        editDialog = $('<div></div>')
        .load("<?php echo site_url("helpdesk/add_brand_partner"); ?>", {edit:id})
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



function view_request_list(id)
{
	// alert(id);
    $("#page_loader1").show();
    var dialogOpts = {
        title: "Manage Training Name",
        modal: true,
        width: "90%",
        resize: "auto",
        close: dialogClosed,
		draggable: false,
		resizable: false
    };
    $("#page_loader1").hide();
        editDialog = $('<div></div>')
        .load("<?php echo site_url("training/get_training_details"); ?>", {tId:id} )
        .dialog(dialogOpts);
    editDialog.dialog('open');
    return false;
}

function update_status(id)
{
    $("#page_loader1").show();
    var dialogOpts = {
        title: (id!=''?'Update':'Add')+" Status",
        modal: true,
        width: "700px",
        resize: "auto",
        close: dialogClosed,
		draggable: false,
		resizable: false
    };
    $("#page_loader1").hide();
        editDialog = $('<div></div>')
        .load("<?php echo site_url("helpdesk/update_request_Status"); ?>", {edit:id})
        .dialog(dialogOpts);
    editDialog.dialog('open');
    return false;

}



function update_status_request(id,status)

{ 

if(status ==1){
var response = confirm("Are you sure you want deactive Brand Team");
} else {
	var response = confirm("Are you sure you want active Brand Team");
}	
if ( response == true )
{

$.ajax({
		   type: 'POST',
		   url: '<?php echo site_url();?>helpdesk/update_team_status',
		   data: {id:id,status:status},
		   success:function (data){ //alert(data);
			 // $("#requestPanel").html(data);
			  // alert(data); 
			  //get_list();
			  location.reload();
			  

		   }
			});	
       
}
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
        <h4 class="text-themecolor"><img src="images/department_big.png"> Manage Brand Team </h4>
      </div>
      <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
         
		  <button type="button" class="btn btn-dark d-none d-lg-block m-l-15" href="javascript:void(0)" onclick="add_edit('');"><i class="fa fa-plus-circle"></i> Add Brand Team</button>
		 <!--  <button type="button" class="btn btn-dark d-none d-lg-block m-l-15" > <a href="javascript:void(0)" onclick="add_request('');"><i class="fa fa-plus-circle text-white"></i></a> Add Request  </button>-->
		  
		  <!-- <button type="button" class="btn btn-dark d-none d-lg-block m-l-15" > <a href="javascript:void(0)" onclick="add_request('');"><i class="fa fa-plus-circle text-white"></i></a> Add Request  </button> -->
		   
		   
		  
		 <!-- <a href="<?php echo base_url()."helpdesk/helpdesk_ticket";?>" class="btn btn-dark d-none d-lg-block m-l-15" >View All Ticket</a>
		  
		   <a href="<?php echo base_url()."helpdesk/helpdesk_priority";?>" class="btn btn-dark d-none d-lg-block m-l-15" >View Priority List</a>
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
});
</script>