<!--suraj-->
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); print_r($result[0][id]);  ?>
<div id="myModal" class="reveal-modal" >
  <div class="row">
    <div class="col-md-12">
      <form action="" method="post" name="trainingform" id="trainingform" class="form-validate form_class" enctype="multipart/form-data" >
        <input type="hidden" name="id" id="id" value="<?php  print_r($result[0][id]); ?>">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label" for="name">Team Name</label>
              <?php $result22=$this->db->query("select id,team_name from helpdesk_support_team where status=1 order by team_name asc")->result_array();?>
              <?php $reg =field_value($result, 'teamId');?>
              <?php // pre($result[0]['teamId']);die;?>
              <select class="form-control validate[required]" name="teamId" id="teamId">
                <option value="">Select Team</option>
                <?php foreach($result22 as $result22){ ?>
                <option value="<?php echo $result22['id'];?>" <?php echo($result22['id']==$reg)?'selected':'';?>><?php echo $result22['team_name']; ?> </option>
                <?php }?>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label" for="name">Priority </label>
              <?php $result22=$this->db->query("select id, priority_type from helpdesk_priority order by priority_type asc")->result_array();?>
              <?php $reg =field_value($result, 'id');?>
              <?php  //pre($result22);die;?>
              <select class="form-control validate[required]" name="priority" id="priority">
                <option value="">Select Priority</option>
                <?php foreach($result22 as $result22){ ?>
                <option value="<?php echo $result22['id'];?>" <?php echo($result22['id']==$reg)?'selected':'';?>><?php echo $result22['priority_type']; ?> </option>
                <?php }?>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label" for="name">Request Name</label>
              <input type="text" placeholder="Request Name" class="form-control validate[required]" id="request_name" name="request_name" value="<?php echo field_value($result, 'request_name ');?>"/>
              <div id="responcemobile"></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label" for="name">TAT</label>
              <input type="number" placeholder="HH" class="form-control validate[required]" id="tat" name="tat" min="1" max="72" value="<?php echo field_value($result, 'tat');?>"/>
              <div id="responcemobile"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
    <!--end .box --> 
  </div>
</div>
<div id="myModal" class="reveal-modal" >
  <div class="row">
    <div class="col-md-12">
      <div class="row kra_table">
        <div class="col-md-12 col-sm-12">
          <form action="" method="post" name="trainingform" id="trainingform" class="form-validate form_class" enctype="multipart/form-data" >
            <table  style="width:100%;" cellspacing="0" class="kra display  table table-hover table-striped table-bordered">
              <thead>
                <tr> </tr>
                <tr>
                  <th style='display:none;'>ID</th>
                  <th>Team Name</th>
                  <th>Request Name</th>
                  <th>Created Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="requestPanel">
              <div class="request-list">
                <?php //print_r($teamData); ?>
              </div>
                </tbody>
              
            </table>
          </form>
        </div>
      </div>
    </div>
    <!--end .box --> 
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	get_list();
	});
	$(document).ready(function(){

		jQuery("#trainingform").validationEngine('attach',{

			onValidationComplete: function(form, status){
				if(status)
				{
					check_request_name();
				}
			}
		});				


	});
	
function get_list()

{ 
$.ajax({
		   type: 'POST',
		   url: '<?php echo site_url();?>helpdesk/get_add_edit_request',
		   data: {},
		   success:function (data){ //alert(data);
			  $("#requestPanel").html(data);
			  // alert(data); 
		   }
			});	
       
}
	
	
	function check_request_name()
	{
		$.post("<?php echo site_url('helpdesk/check_request_name');?>",{request_name:$('#request_name').val(),teamId:$('#teamId').val()},
		
			function (data)
			{
				
				if(data.length>0)
				{
					
					if(jQuery.trim(data)!='0')
					{
						jQuery('#request_name').validationEngine('showPrompt', '* This Request Type already exists', 'fail');
					}
					else
					{
						//alert("tt");
						var formdata = $("#trainingform").serialize();
						$.ajax({
							type: 'POST',
							url: '<?php echo site_url();?>helpdesk/insert_update_request',
							data: formdata,
							success:function (data){
								//alert(data);return;
								if(data==1)
								{
									
									if($("#id").val()>0)
									{
										jQuery.noticeAdd({text:"updated successfully"});
										$("#list").trigger("reloadGrid");
										editDialog.dialog("close");
									}
									else
									{
										jQuery.noticeAdd({text:"added successfully"});
										//add_request(id);
										get_list();
										//$("#list").trigger("reloadGrid");
										//editDialog.dialog("close");
									}
								}
								else
								{
									if($("#id").val()>0)
									{
										jQuery.noticeAdd({text:"Update Failed"});
									}
									else
									{
										jQuery.noticeAdd({text:"Added Failed"});
									}
								}

							}
						});

					}
				}

			}
		);
		
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

function update_status1(id)
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
        .load("<?php echo site_url("helpdesk/edit_request1"); ?>", {edit:id})
        .dialog(dialogOpts);
    editDialog.dialog('open');
    return false;

}


function edit_request111(id)
{
	
	$.ajax({
		   type: 'POST',
		   url: '<?php echo site_url();?>helpdesk/edit_request1',
		   data: {edit:id},
		   success:function (data){ //alert(data);
			 // $("#requestPanel").html(data);
			  // alert(data); 
			//  get_list();
			$("#list").trigger("reloadGrid");
			//editDialog.dialog("close");
			update_status1(id)
		   }
			});	
}




function edit_request(id)

{
	//alert(id);
    $("#page_loader1").show();
    var dialogOpts = {
        title: (id!=''?'Edit':'Add')+" Request Type",
        modal: true,
        width: "700px",
        resize: "auto",
        close: dialogClosed,
		draggable: false,
		resizable: false
    };
    $("#page_loader1").hide();
        editDialog = $('<div></div>')
        .load("<?php echo site_url("helpdesk/edit_request1"); ?>", {edit:id})
        .dialog(dialogOpts);
    editDialog.dialog('open');
    return false;
}




	
function update_status_request(id,status)

{ 
//alert(id);return;
if(status ==1){
var response = confirm("Are you sure you want deactive request type");
} else {
	var response = confirm("Are you sure you want active request type");
}	
if ( response == true )
{

$.ajax({
		   type: 'POST',
		   url: '<?php echo site_url();?>helpdesk/insert_update_request',
		   data: {id:id,status:status},
		   success:function (data){ //alert(data);
			 // $("#requestPanel").html(data);
			  // alert(data); 
			  get_list();

		   }
			});	
       
}
}

//delete_request

function delete_request(id)

{ 
//alert(id);return;

var response = confirm("Are you sure you want delete request type this record");

if ( response == true )
{

$.ajax({
		   type: 'POST',
		   url: '<?php echo site_url();?>helpdesk/delete_request',
		   data: {id:id},
		   success:function (data){ //alert(data);
			 // $("#requestPanel").html(data);
			  // alert(data); 
			  get_list();

		   }
			});	
       
}
}
	
</script> 
<script>

$(document).ready(function() {
  var wfheight = $(window).height();
          
  $('.kra_table').height(wfheight-465);

	
$('.kra_table').perfectScrollbar()
});
</script>