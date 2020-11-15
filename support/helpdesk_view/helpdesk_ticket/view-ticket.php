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
<style>.fr-toolbar .fr-command.fr-btn.fr-hidden, .fr-popup .fr-command.fr-btn.fr-hidden{display:block;}.fr-box.fr-basic .fr-element{ min-height:250px;}</style>
<div id="myModal" class="reveal-modal" > 
  <div class="row">
    <div class="col-md-12">
      <input type="hidden" name="id" id="id" value="<?php echo field_value($result, 'id')?>">
      <div class="row">
        <div class="col-md-12">
          <div class="ticket-details-1">
            <ul>
              <li>
                <p>Brand Name</p>
                <span><?php echo $result['team_name'];?></span> </li>
              <li>
                <p>Created On</p>
                <span><?php echo $result['isCreated'];?></span> </li>
              <li>
                <p>Status</p>
                <span class="active"><?php echo $result['s_type'];?></span> </li>
              <li>
                <p>Created By</p>
                <span><?php echo $result['isCreatedby'];?></span> </li>
            </ul>
          </div>
          <!--ticket-details-1--> 
        </div>
        <!--col-lg-12--> 
      </div>
      <!--row-->
      
      <div class="row">
        <div class="col-lg-6">
          <div class="ticket-details-2">
            <div class="form-group">
              <input type="hidden" name="id" id="id" value="<?php echo field_value($result, 'id')?>">
              <label class="control-label" for="name"><b>Change Status :</b></label>
              <?php $result22=$this->db->query("select id, s_type  from brand_status where status=1 order by id asc")->result_array();?>
              <?php $reg =field_value($result, 'status');?>
              &nbsp;&nbsp;
              <select class="form-control validate[required]" name="status" id="status" onchange="ChangeTicketStatus(this.value,'<?php echo field_value($result, 'id')?>')">
                <?php foreach($result22 as $result22){ ?>
                <option value="<?php echo $result22['id'];?>" <?php echo($result22['id']==$reg)?'selected':'';?>><?php echo $result22['s_type']; ?> </option>
                <?php }?>
              </select>
            </div>
          </div>
          <!--ticket-details-2--> 
        </div>
        <!--col-lg-4-->
        <div class="col-lg-6">
          <div class="ticket-details-3">
            <?php if($result['status'] != "5") { ?>
            <!--  <button class="btn btn-xs btn-primary text-white"><i class="fa fa-exchange" aria-hidden="true"></i> Change Status</button> -->
            <button  type='button' href="#collapse1" class="btn btn-xs btn-info text-white m-l-15 nav-toggle" id="reply_message"> <i class="fa fa-reply" ></i> Reply</button>
            <button type='button' href="#collapse2" class="btn btn-xs btn-danger text-white m-l-15 nav-toggle1"> <i class="fa fa-share" ></i> Forward</button>
            <?php } ?>
          </div>
          <!--ticket-details-3--> 
        </div>
        <!--col-lg-4--> 
        
      </div>
      <!--row-->
      
      <div class="helpdesk-reply">
        <form action="<?php echo site_url();?>helpdesk/insert_reply_ticket"  method="post" name="mrfcandidateform" id="mrfcandidateform"  class="form-validate form_class" enctype="multipart/form-data" >
          <input type="hidden" name="id" id="id" value="<?php echo field_value($result, 'id')?>">
          <div class="m-t-20" id="collapse1" style="display:none" >
            <div class="col-md-12">
              <div class="form-group helpdesk-mailto">
                <label class="control-label" for="to">To</label>
                <input type="text" class="form-control" id="to" name="to" value="<?php print_r($result['fromEmailId']); ?>" readonly />
                <span class="toError alert-Error"> </span>
                <input type="hidden" name="subject" value="<?php echo $result['subject']; ?>" >
                <input type="hidden" name="teamId" value="<?php echo $result['teamId']; ?>" >
              </div>

              <div class="form-group helpdesk-mailcc">
                <label class="control-label" for="cc">cc</label>
                <input type="text" class="form-control" id="cc" name="cc" value="<?php echo $result['t_cc']; ?>"  />
                <span class="ccError alert-Error" > </span>
              </div>

              <div class="form-group helpdesk-mailcc">
                <label class="control-label" for="subject">Subject</label>
                <input type="text" class="form-control" id="subject"  value="<?php echo $result['subject']; ?>" readonly/>
              </div>
             <!-- <div class="form-group helpdesk-mailbcc">
                <label class="control-label" for="to">Bcc</label>
                <input type="text" class="form-control" id="bcc" name="bcc" value="<?php echo $result['t_bcc']; ?>"  />
              </div> -->

            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="name">Message</label>
				 <?php
                $arr1 = "<br><br><hr><p><b>From:</b> ".$lastmessage[0]['t_fromEmail']."
                </p><p><b>Sent:</b> ".$lastmessage[0]['t_isUpdated']."
                </p><p><b>To:</b> ".$lastmessage[0]['t_toEmail']."                
                </p><p><b>Subject:</b> ".$result['subject']."
                </p><p>".stripslashes($lastmessage[0]['t_message'])."</p>";			
			          ?>
                <textarea cols="50" rows="6"  class="form-control validate[required]" id="message" name="message" ><?php print_r($arr1); ?> </textarea>
                <span class="messageError alert-Error"> </span>
               
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-10">
                    <input type="file" id="attachUpload" name="attach[]" multiple>
					          <div class="attachItems">
                    </div>
                   <!-- <img id="imageBox" src="<?php echo base_url().'ark_assets/images/no-image.jpg';?>" style="height:50px;width:auto;"> --></div>
                  
                  <div class="col-lg-2"> <span class="pull-right">
                    <button type="submit" class="btn btn-primary" id="addCandMrf1">Submit</button>
                    </span> </div>
                </div>
                <!--row--> 
              </div>
            </div>
          </div>
        </form>
      </div>
      <!--helpdesk-reply-->
      
      <div class="helpdesk-forword">
        <form action="<?php echo site_url();?>helpdesk/insert_reply_ticket"  method="post" name="mrfcandidateform1" id="mrfcandidateform1"  class="form-validate form_class" enctype="multipart/form-data" >
          <input type="hidden" name="id" id="id" value="<?php echo field_value($result, 'id')?>">
          <input type="hidden" name="fwd" id="fwd" value="fwd">
     
          <input type="hidden" name="hiddenAttachImage" id="hiddenAttachImage" value="<?php echo $lastmessage[0]['t_attach']; ?>">
          <div class="m-t-20" id="collapse2" style="display:none" >
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="to">To</label>
                <input type="text" class="form-control" name="to" id="to" placeholder="" value="" />
                <span class="toFwdError alert-Error"> </span>
              </div>
            <!--  <div class="form-group ">
                <label class="control-label" for="to">cc</label>
                <input type="text" class="form-control" id="cc" name="cc" value=""/>
              </div> -->
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label" for="name">Message</label>
                <?php
			          $arr  = "From: ".$result['email']."<br>"."Sent: ".$lastmessage[0]['t_isUpdated']."<br>"."To: ".$lastmessage[0]['empFname']."<br>"."Subject: ".$result['subject'];			
                $arr1 = "<br><br><hr><p><b>From:</b> ".$lastmessage[0]['t_fromEmail']."
                </p><p><b>Sent:</b> ".$lastmessage[0]['t_isUpdated']."
                </p><p><b>To:</b> ".$lastmessage[0]['t_toEmail']."                 
                </p><p><b>Subject:</b> ".$result['subject']."
                </p><p>".stripslashes($lastmessage[0]['t_message'])."</p>";			
			          ?>

                <textarea class="form-control validate[required]" id="message" name="message" ROWS=10 COLS=50><?php print_r($arr1);  ?></textarea>
                <span class="toFwdMessage alert-Error"> </span>
                <br />
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-10">
                    <input type="file" id="fwdattachUpload" name="attach[]" multiple>
                    <div class="fwdAttachItems">
                           <?php 
                           if($lastmessage[0]['t_attach'] != ''){
                              $listAttach = explode(',',$lastmessage[0]['t_attach']);
                              foreach($listAttach as $attacData){
                                $checkExt = substr($attacData, strrpos($attacData, '.') + 1);
                                 
                                if($checkExt == 'jpg' || $checkExt == 'png' || $checkExt == 'jpeg' || $checkExt == 'gif' ){ ?>                        
                                     <span class="pip">
                                       <img class="imageThumb" src="<?php echo base_url().'uploads/helpdesk/'.$attacData;?>" title=''>                                 
                                     </span>                                   
                                <?php } else { ?>                       
                                    <span class="pip">
                                      <i title="" class="fa fa-file-text-o" aria-hidden="true"></i>                                 
                                    </span>
                               <?php }                            
                              }
                            }                           
                           ?>

                    </div>
                  </div>
                  <!--<label class="control-label col-md-2" for="name">Attach</label>
                 <input type="file" name="attach" class="form-control col-md-6" id="attach" />-->
                  <div class="col-lg-2"> <span class="pull-right">
                         <button type="submit" class="btn btn-primary" id="addCandMrf1">Submit</button>
                    </span> </div>
                </div>
                <!--row--> 
              </div>
            </div>
          </div>
        </form>
      </div>
      <!--helpdesk-forword-->
      
      <div class="mailSummary">
        <?php if($tickedSummary){ foreach($tickedSummary as $row){	
		?>
        <div class="card <?php echo ($row['t_fromEmail'] == $result['fromEmailId']) ? 'ticket_summary':'ticket_summary-box-right'; ?>">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <p><strong>Date :</strong> <?php echo date('Y-m-d h:i A' ,strtotime($row['t_isUpdated'])); ?></p>
              </div>
              <div class="col-md-6">
                <p><strong> Name :</strong> <?php echo $row['t_addedby']; ?></p>
              </div> 
              <div class="col-md-12">
                <p><strong> From :</strong> <?php echo $row['t_fromEmail']; ?></p>
              </div> 
              <div class="col-md-12">
                <p><strong> To :</strong> <?php echo $row['t_toEmail']; ?></p>
              </div>             
              <?php if(isset($row['t_cc']) && $row['t_cc'] !=''){ ?>
              <div class="col-md-12">
                <p><strong> Cc :</strong> <?php echo $row['t_cc']; ?></p>
              </div>
              <?php } ?>
              <?php if($row['t_attach'] !=''){ ?>
              <div class="col-md-6"> 
              <div class="row">
              <div class="col-lg-3">
              <p> <strong>Attachment :</strong></p>
              </div><!--col-lg-6-->
              <div class="col-lg-9">
              <?php 
                   $attachmentList = explode(',',$row['t_attach']);
                   foreach($attachmentList as $attachData) {
                    $checkExt = substr($attachData, strrpos($attachData, '.') + 1);                                     
                   ?>  
                   <div class="doc-dwonload">
                       <?php if($checkExt == 'jpg' || $checkExt == 'png' || $checkExt == 'jpeg' || $checkExt == 'gif' ){ ?>                        
                            <a href="javascript:void(0);" title="<?php echo $attachData; ?>" onclick="viewAttachment('<?php echo base_url().'uploads/helpdesk/'.$attachData; ?>')"  href="<?php echo base_url().'uploads/helpdesk/'.$attachData; ?>"><img src="<?php echo base_url().'uploads/helpdesk/'.$attachData; ?>" /></a>
                       <?php } else { ?>                       
                            <a href="javascript:void(0);" title="<?php echo $attachData; ?>" onclick="viewAttachment('<?php echo base_url().'uploads/helpdesk/'.$attachData; ?>')"  href="<?php echo base_url().'uploads/helpdesk/'.$attachData; ?>"><img src="<?php echo base_url().'ark_assets/images/doc-dwnld.png'?>"/></a>
                       <?php } ?>
                   </div>                 
                  <?php } ?>
               </p>             
              </div>
			    </div><!--col-lg-6-->
              
              </div><!--row-->            
                
              <?php } ?>
            
                
              <div class="col-md-12">
                <p> <strong>Message</strong></p>
                <p> <?php echo stripslashes($row['t_message']); ?> </p>
              </div>
            </div>
          </div>
        </div>
        <?php } } ?>
      </div>
    </div>
  </div>
  <!--end .box --> 
</div>
</div>

<div class="modal" id="attachModal" style="display: none;">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header popup-v-heading">        
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeAttachModal()">
                      <span aria-hidden="true">×</span>
                    </button>
                  </div>
                  <div class="modal-body">
                   <iframe width="100%" height="515" id="attachFrame" src="" frameborder="0" allow="accelerometer;  encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
                  </div>
                </div>
              </div>
              </div>

<script type="text/javascript">

	$(document).ready(function(){
       new FroalaEditor('textarea#message', { 	toolbarButtons: ['fullscreen', 'bold', 'italic', 'underline', 'strikeThrough', '|','fontSize', 'color', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'outdent', 'indent', 'quote', '-', 'insertLink','insertTable']
   })
 
	$('#mrfcandidateform').submit(function(evt) {
      evt.preventDefault();  
      var valid = true; 
      var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      var to        =  $("[name='to']", mrfcandidateform).val();  
      var message   =  $("[name='message']", mrfcandidateform).val();  
      var emailList = $("[name='cc']", mrfcandidateform).val();
      var emails    = emailList.replace(/\s/g,'').split(",");      
      if(to == '' || to == null){
		 $("[name='to']", mrfcandidateform).focus(); 
         $('.toError').text('Please enter valid mail id');
         return false;
      } else if(message == '' || message == null){
         $('.messageError').text('Please enter message');
         return false;
      }
       // email list validation
      for (var i = 0; i < emails.length; i++) {
        if(emails != ''){
          if( emails[i] == "" || ! regex.test(emails[i])){
              valid = false;           
          }  
        }   
      } 
    
      var formData = new FormData(this);
      if(valid){
          $('.toError').text('');
          $('.messageError').text('');
          $('.ccError').text('');
		    	$('#page_loader1').show();
              $.ajax({
                type: 'POST',
                url: '<?php echo site_url();?>helpdesk/insert_reply_ticket',
                data: formData,
                dataType: "json",
                cache:false,
                contentType: false,
                processData: false,							
                success:function (data){
                  if(data.respCode == 200)
                  {
                    if($("#id").val()>0)
                    {									
                      $('.mailSummary').prepend(data.htmlData);
                      $('#message').val('');
                      $('.attachItems').html('');
                      document.getElementById("attachUpload").value = "";
					            $('#mrfcandidateform .fr-view').empty();
                      jQuery.noticeAdd({text:"Email Send Successfully"}); 
                    }
                    else
                    {										
                      $('.mailSummary').prepend(data.htmlData);
                      $('#message').val('');
                      $('.attachItems').html('');
                      document.getElementById("attachUpload").value = "";
                      $('#mrfcandidateform .fr-view').empty();
                      jQuery.noticeAdd({text:"Email Send Successfully"}); 
                          
                    }
                  } else {
                      jQuery.noticeAdd({text:"failed successfully"});
                  }
                  $('#page_loader1').hide();
                }
              });
      } else {
        $("[name='cc']", mrfcandidateform).focus();
        $('.ccError').text('Please enter valid email id');
      }
	});

  $('#mrfcandidateform1').submit(function(evt) {
          evt.preventDefault();           
          var to       =  $("[name='to']", mrfcandidateform1).val();  
          var message  =  $("[name='message']", mrfcandidateform1).val();        
          if(to == '' || to == null){
              $("[name='to']", mrfcandidateform1).focus();
			       $('.toFwdError').text('Please enter mail id');
             return false;
          }else if(message == '' || message == null){
             $('.messageFwdError').text('Please enter message');
             return false;
          }
            
          var formData = new FormData(this);
		    	$('#page_loader1').show();
						$.ajax({
							type: 'POST',
							url: '<?php echo site_url();?>helpdesk/insert_reply_ticket',
							data: formData,
							dataType: "json",
							cache:false,
              contentType: false,
              processData: false,							
							success:function (data){
								if(data.respCode == 200)
								{
									if($("#id").val()>0)
									{									
									   $('.mailSummary').prepend(data.htmlData);
									   $('#message').val('');
                     $('.fwdAttachItems').html('');
                     document.getElementById("fwdattachUpload").value = ""; 
                      $('#mrfcandidateform1 .fr-view').empty();
                      $('.toFwdError').text('');
                      $('.messageFwdError').text('');
                      jQuery.noticeAdd({text:"Email Send Successfully"}); 					 
									}
									else
									{										
										$('.mailSummary').prepend(data.htmlData);
										$('#message').val('');
                    $('.fwdAttachItems').html('');
                    document.getElementById("fwdattachUpload").value = "";
                     $('#mrfcandidateform1 .fr-view').empty();
                     $('.toFwdError').text('');
                     $('.messageFwdError').text('');
                     jQuery.noticeAdd({text:"Email Send Successfully"}); 					
									}
								} else {
									  jQuery.noticeAdd({text:"failed successfully"});
								}
								$('#page_loader1').hide();
							}
						});
			
	});

	
	});
	
  function viewAttachment(url){
   $('#attachFrame').attr('src',url)
   $('#attachModal').modal('show');      
}



  function ChangeTicketStatus(status,id){

   if(id!='' && status !=''){
   
    var txt;
    var checkConfirm = confirm("Are you sure you want to change the status!");
    if (checkConfirm == true) {
      $.ajax({
							type: 'POST',
							url: '<?php echo site_url();?>helpdesk/change_status',
							data: {id:id,status:status},						
							success:function (data){
								if(data==1)
								{
									if($("#Id").val()>0)
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
                    
									}
								}
								else
								{
									if($("#Id").val()>0)
									{
										//jQuery.noticeAdd({text:"Candidate Update Failed"});
									}
									else
									{
										// jQuery.noticeAdd({text:"Candidate Added Failed"});
									}
								}
							
							}
            });
         }
      }    
  }

	$(document).ready(function(){

	$('#formstatus').submit(function(evt) {
             evt.preventDefault();
            var Id = $('#id').val();
			var formData = new FormData(this);
		
						$.ajax({
							type: 'POST',
							url: '<?php echo site_url();?>helpdesk/change_status',
							data: formData,
							cache:false,
              contentType: false,
              processData: false,
							success:function (data){
								if(data==1)
								{
									if($("#Id").val()>0)
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
                    
									}
								}
								else
								{
									if($("#Id").val()>0)
									{
										//jQuery.noticeAdd({text:"Candidate Update Failed"});
									}
									else
									{
										// jQuery.noticeAdd({text:"Candidate Added Failed"});
									}
								}
							
							}
						});
			
	});
	
	
  // image uplaod preview
  var fileListArray = [];
  if (window.File && window.FileList && window.FileReader) {
    $("#attachUpload").on("change", function(e) {
        var files     = e.target.files;
        console.log(files);
    
        filesLength   = files.length;
        $('.attachItems').html('');
      for (var i = 0; i < filesLength; i++) {
        var f = files[i]; 
      
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {          
          var file     =  e.target; 
          var checkExt  =  e.target.result.split(';');
          var extension =  checkExt[0];
         
          if( extension.includes("pdf") ||extension.includes('csv') || extension.includes('pptx') || extension.includes('xlsx') || extension.includes('xls') || extension.includes('doc') || extension.includes('docx')) {
  
              $(".attachItems").append("<span class=\"pip\">" +
              "<i title=\"" + '' + "\" class=\"fa fa-file-text-o\" aria-hidden=\"true\"></i>"+
              "<br/><span class=\"remove\"><!--<i class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>--></span>" +
              "</span>");

          } else if( extension.includes('jpg') || extension.includes('png') || extension.includes('gif') || extension.includes('jpeg')){ 
              
              $(".attachItems").append("<span class=\"pip\">" +
             "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + '' + "\"/>" +
              "<br/><span class=\"remove\"><!--<i class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>--></span>" +
              "</span>");                     
           }
          
          $(".remove").click(function(){
            $(this).parent(".pip").remove();
          });
        });
        fileReader.readAsDataURL(f);
       }
       //console.log(fileListArray);
     });
    } else {
        alert("Your browser doesn't support to File API")
    }
    
    // image upload for forward attachment
    if (window.File && window.FileList && window.FileReader) {
    $("#fwdattachUpload").on("change", function(e) {
        var files     = e.target.files;
        
        filesLength   = files.length;
        $('.fwdAttachItems').html('');
      for (var i = 0; i < filesLength; i++) {
        var f = files[i]; 
      
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {          
          var file     =  e.target; 
          var checkExt  =  e.target.result.split(';');
          var extension =  checkExt[0];
         
          if( extension.includes("pdf") ||extension.includes('csv') || extension.includes('pptx') || extension.includes('xlsx') || extension.includes('xls') || extension.includes('doc') || extension.includes('docx')) {
  
              $(".fwdAttachItems").append("<span class=\"pip\">" +
              "<i title=\"" + '' + "\" class=\"fa fa-file-text-o\" aria-hidden=\"true\"></i>"+
              "<br/><span class=\"remove\"><!--<i class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>--></span>" +
              "</span>");

          } else if( extension.includes('jpg') || extension.includes('png') || extension.includes('gif') || extension.includes('jpeg')){ 
              
              $(".fwdAttachItems").append("<span class=\"pip\">" +
             "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + '' + "\"/>" +
              "<br/><span class=\"remove\"><!--<i class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>--></span>" +
              "</span>");                     
           }
          
          $(".remove").click(function(){
            $(this).parent(".pip").remove();
          });
        });
        fileReader.readAsDataURL(f);
       }
       //console.log(fileListArray);
     });
    } else {
        alert("Your browser doesn't support to File API")
    }

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
		  $('.nav-toggle').click(function(){
			//get collapse content selector
			var collapse_content_selector = $(this).attr('href');
			//make the collapse content to be shown or hide
			var toggle_switch = $(this);
			$(collapse_content_selector).toggle(function(){
			  if($(this).css('display')=='none'){
				  $("#collapse2").hide();
                                //change the button label to be 'Show'
				//toggle_switch.html('Reply');
			  }else{
                  $("#collapse2").hide();            
							  //change the button label to be 'Hide'
				//toggle_switch.html('Reply');
			  }
			});
		  });
				
		});	
		
		$(document).ready(function() {
		  $('.nav-toggle1').click(function(){
			 
			//get collapse content selector
			var collapse_content_selector = $(this).attr('href');
			
					
			//make the collapse content to be shown or hide
			var toggle_switch = $(this);
			$(collapse_content_selector).toggle(function(){
			  if($(this).css('display')=='none'){
				  // $("#hide").click(function(){
						   $("#collapse1").hide();
					 // });
				  
        //change the button label to be 'Show'
				//toggle_switch.html('Reply');
			  }else{
            $("#collapse1").hide();  
						//change the button label to be 'Hide'
				   //toggle_switch.html('Reply');
			  }
			});
		  });
				
		});	
		


$(document).ready(function () {
	$('.ticket_message').hide();
    $("#reply_message").click(function () {
        $(".ticket_message").show();
    });
});
$(document).ready(function() {
	 
	 var wfheight = $(window).height();
 $('#myModal').height(wfheight-110);	
 
$('#myModal').perfectScrollbar()
});
</script>
<style>
.ui-dialog{top:1.5% !important}

</style>
