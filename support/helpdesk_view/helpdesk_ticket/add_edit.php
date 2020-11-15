<!--suraj-->
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script>
function getAlluser()
	{
		
		var id = document.getElementById('teamId').value;
		
		$.ajax({
			type: "POST",
			url: '<?php echo site_url('helpdesk/get_requestList').'/';?>'+id,
			data: id='cat_id',
			success: function(data){
			   // alert(data);
				$('#reporting').html(data);
		},
				});
	}

</script>

<div id="myModal" class="reveal-modal" >
  <div class="row">
    <div class="col-md-12">
      <form action="<?php echo site_url();?>helpdesk/insert_update_ticket"  method="post" name="mrfcandidateform" id="mrfcandidateform"  class="form-validate form_class" enctype="multipart/form-data" >
        <input type="hidden" name="id" id="id" value="<?php echo field_value($result, 'id')?>">
        <div class="row">
          
		    <div class="col-md-4">
            <div class="form-group">
              <label class="control-label" for="name">Team Name </label>
               <?php $result2=$this->db->query("select id,team_name from brand_support_team where status=1 order by team_name asc")->result_array();?>
              <?php $reg =field_value($result, 'teamId');?>
              <?php // pre($result[0]['teamId']);die;?>
              <select class="form-control validate[required]" name="teamId" id="teamId" onChange='getAlluser(this.values)'>
                <option value="">Select Team</option>
                <?php foreach($result2 as $result2){ ?>
                <option value="<?php echo $result2['id'];?>" <?php echo($result2['id']==$reg)?'selected':'';?>><?php echo $result2['team_name']; ?> </option>
                <?php }?>
            </select>
            </div>
          </div>
		 
		  
		  <div class="col-md-4">
            <div class="form-group">
              <label class="control-label" for="name">Request  </label>
			   
                <select class="form-control validate[required]" name="requestId" id="reporting">
                <option value="">Select Request </option>
				</select>
              <div id="reporting1"></div>
            </div>
          </div>
		  
		 <div class="col-md-4">
            <div class="form-group">
              <label class="control-label" for="name">Subject</label>
             <input type="text" placeholder="Subject" class="form-control validate[required]" id="subject" name="subject" value="<?php echo field_value($result, 'subject');?>"/>
            </div>
          </div>
		
		
		
		  
		 
		   <div class="col-md-12">
            <div class="form-group">
              <label class="control-label" for="name">Message</label>
			  
             <textarea cols="50" rows="6"  class="form-control validate[required]" id="message" name="message" value=""/><?php echo field_value($result, 'message');?></textarea>
			<!--  <?php $data= array(
                    'name'        => 'message',
                    'id'          => 'message',
                    'toolbarset'  => 'Default',
                    'basepath'    => base_url().'application/plugins/fckeditor/',
                    //'width'       => '560',
                    'height'      => '320'
                    );
                    echo form_fckeditor( $data, $result['message']);
                    ?> -->
	         <!--<textarea cols="50" rows="3"  class="form-control validate[required]" id="message" name="message" value=""/><?php echo field_value($result, 'message');?></textarea> -->
            </div>
          </div>
		  <div class="col-md-12">
            <div class="form-group helpdesk-reply">            
            <div class="row">
            <div class="col-lg-6">
            <input type="file">
            </div><!--col-lg-6-->
              <!--<label class="control-label col-md-2" for="name">Attach</label>
              <input type="file" name="attach" class="form-control col-md-6" id="attach" />-->
              <div class="col-lg-6">
              <span class="pull-right">
              <button type="submit" class="btn btn-primary" id="addCandMrf1">Submit</button>
              </span>
              </div><!--col-lg-6-->
              </div><!--row-->
          </div>
		</div>
      </div>
      </form>
    </div>
    <!--end .box --> 
  </div>
</div>

<script type="text/javascript">

	$(document).ready(function(){
	$('#mrfcandidateform').submit(function(evt) {
             evt.preventDefault();
           //   $("#addCandMrf").prop('disabled', true);
            var mrfId = $('#mrfId').val();
			var formData = new FormData(this);
			//$("#page_loader1").show();
					//var formdata = $("#mrfcandidateform").serialize();
						$.ajax({
							type: 'POST',
							url: '<?php echo site_url();?>helpdesk/insert_update_ticket',
							data: formData,
							cache:false,
                            contentType: false,
                            processData: false,
							success:function (data){
								if(data==1)
								{
									if($("#job_Id").val()>0)
									{
										jQuery.noticeAdd({text:"updated successfully"});
										$("#list").trigger("reloadGrid");
										editDialog.dialog("close");
									}
									else
									{
										jQuery.noticeAdd({text:"added successfully"});
										$("#list").trigger("reloadGrid");
										editDialog.dialog("close");                    
                    $('#counterCandidate'+mrfId).html(parseInt($('#counterCandidate'+mrfId).html()) + 1);
									}
								}
								else
								{
									if($("#job_Id").val()>0)
									{
										jQuery.noticeAdd({text:"Candidate Update Failed"});
									}
									else
									{
										jQuery.noticeAdd({text:"Candidate Added Failed"});
									}
								}
							//	$("#page_loader1").hide();
							 //  $("#addCandMrf").prop('disabled', false);
							}
						});
			
	});
	
	//Call city With Ajax
    $("#currentState").change(function(){
        var selectedState = $("#currentState").val();		
	  if(selectedState!=''){		
		 $.post("<?php echo site_url("mrf/ajaxCity")?>",{sid:selectedState},
				function(data){
				//alert(data);
					if (data != ""){
					$("#currentLocation").html(data);
					
					}
				});
			
		} else {
			$("#currentLocation").html('<option>Select Job Location</option>');
		 }
       });
	});
	
	$('#empDOB').datepicker({
	    changeMonth : true,
		changeYear  : true ,
		yearRange   : '1960:2019',
		maxDate: new Date()
		});
		
function isNumberValue(event) {
		var key = window.event ? event.keyCode : event.which;
		if (event.keyCode === 8 || event.keyCode === 46) {
			return true;
		} else if ( key < 48 || key > 57 ) {
			return false;
		} else {
			return true;
		}
};
 	
</script>
<script>

$(document).ready(function() {
	 var wfheight = $(window).height();
 //$('#myModal').height(wfheight-170);	
$('#myModal').perfectScrollbar()
});
</script>

