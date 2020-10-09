    <footer class="footer footer-static footer-light">
    		<p class="clearfix text-muted text-center px-2"><span>Copyright  &copy; <?php echo date('Y'); ?>  <a href="https://www.fenicialounge.in/"  target="_blank" class="text-bold-800 primary darken-2">Club Fenicia </a>, All rights reserved. </span></p>
    </footer>
  </div>
</div>

<!--<aside id="notification-sidebar" class="notification-sidebar d-none d-sm-none d-md-block"><a class="notification-sidebar-close"><i class="ft-x font-medium-3"></i></a>
	<div class="side-nav notification-sidebar-content">
		<div class="row">
			<div class="col-12 mt-1">
				<ul class="nav nav-tabs">
					<li class="nav-item"><a id="base-tab1" data-toggle="tab" aria-controls="base-tab1" href="#activity-tab" aria-expanded="true" class="nav-link active"><strong>Activity</strong></a></li>
					<!--<li class="nav-item"><a id="base-tab2" data-toggle="tab" aria-controls="base-tab2" href="#settings-tab" aria-expanded="false" class="nav-link"><strong>Settings</strong></a></li>
				</ul>
				<div class="tab-content">
					<div id="activity-tab" role="tabpanel" aria-expanded="true" aria-labelledby="base-tab1" class="tab-pane active">
						<div id="activity-timeline" class="col-12 timeline-left">
							<h6 class="mt-1 mb-3 text-bold-400">RECENT ACTIVITY</h6>
							<div class="timeline">
								<ul class="list-unstyled base-timeline activity-timeline ml-0">
                  <?php if(!empty($this->Log_data)): ?>
                  <?php     foreach($this->Log_data as $list): ?>
              									<li>
              										<div class="timeline-icon bg-danger"><?php echo $list['icon']; ?></div>
              										<div class="base-timeline-info"><a href="<?php echo base_url().$list['log_link']; ?>" class="text-uppercase text-danger"><?php echo $list['log_title'];?></a><span class="d-block"><?php echo $list['log_description'];?></span></div>
              										<small class="text-muted"><?php echo date('l jS  F Y h:i:s A' ,strtotime($list['log_dt'])); ?></small>
              									</li>
                  <?php     endforeach; ?>
                  <?php     endif; ?>							
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</aside>-->
<input type="hidden" id="add_flag" value="<?php if(!empty($ck_action_falg) && $ck_action_falg['add_flag'] !=''): echo $ck_action_falg['add_flag'];endif;?>" >
<input type="hidden" id="edit_flag" value="<?php if(!empty($ck_action_falg) && $ck_action_falg['edit_flag'] !=''): echo $ck_action_falg['edit_flag'];endif;?>" >
<input type="hidden" id="delete_flag" value="<?php if(!empty($ck_action_falg) && $ck_action_falg['delete_flag'] !=''): echo $ck_action_falg['delete_flag'];endif;?>" >
<input type="hidden" id="download_flag" value="<?php if(!empty($ck_action_falg) && $ck_action_falg['download_flag'] !=''): echo $ck_action_falg['download_flag'];endif;?>" >
<!-- BEGIN VENDOR JS-->
<script src="<?=base_url('public/admin_assets/vendors/js/core/popper.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/perfect-scrollbar.jquery.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/prism.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/jquery.matchHeight-min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/screenfull.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/pace/pace.min.js')?>"></script>
<!-- BEGIN VENDOR JS-->
<script src="<?=base_url('public/admin_assets/vendors/js/jquery.steps.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/pickadate/picker.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/pickadate/picker.date.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/pickadate/picker.time.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/pickadate/legacy.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/jquery.validate.min.js')?>"></script>
<!-- BEGIN PAGE VENDOR JS-->

<!-- DATA TABLE -->
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/datatables.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/dataTables.buttons.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/buttons.flash.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/jszip.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/pdfmake.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/vfs_fonts.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/buttons.html5.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/buttons.print.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/js/data-tables/datatable-advanced.js')?>"></script>
<script src="<?=base_url('public/admin_assets/js/sweet-alerts.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/sweetalert2.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/js/select2.full.js')?>"></script>
<!-- END PAGE VENDOR JS-->
<!-- BEGIN CONVEX JS-->
<script src="<?=base_url('public/admin_assets/js/app-sidebar.js')?>"></script>
<script src="<?=base_url('public/admin_assets/js/notification-sidebar.js')?>"></script>
<!-- END CONVEX JS-->
<!-- BEGIN PAGE LEVEL JS-->
<script src="<?=base_url('public/admin_assets/js/wizard-steps.js')?>"></script>

<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

<script type="text/javascript">
	$(function(){

	    var url = window.location.pathname, 
	        urlRegExp = new RegExp(url.replace(/\/$/,'') + "$"); // create regexp to match current url pathname and remove trailing slash if present as it could collide with the link in navigation in case trailing slash wasn't present there
	        // now grab every link from the navigation
	        $('.nav-container a').each(function(){
	            // and test its normalized href against the url pathname regexp
	            if(urlRegExp.test(this.href.replace(/\/$/,''))){
	                $(this).parent('li').addClass('active');
	            }
	        });

	});
</script>


 <script>
		$(document).ready(function() {
  $(".js-select2").select2();
  $(".js-select2-multi").select2();

  $(".large").select2({
    dropdownCssClass: "big-drop",
  });

});
</script>


<script type="text/javascript">
	$('a.delete_btn').confirm({
    title: "confirm Delete",    
    content: "Are you sure you want to delete this?",  
     buttons: {
        Delete: {
            btnClass: 'btn-red',
            action: function(){
            	 location.href = this.$target.attr('href');
            }
        },         
        Close: {
            btnClass: 'btn-default',
            action: function(){
            	
            }
        },  
       }
});
$('a.album_delete_btn').confirm({
    title: "confirm Delete",    
    content: "Are you sure want to delete?If you delete the album all the images user this album will get delete.",  
     buttons: {
        Delete: {
            btnClass: 'btn-red',
            action: function(){
              location.href = this.$target.attr('href');
            }
        },         
        Close: {
            btnClass: 'btn-default',
            action: function(){
              
            }
        },  
       }
});
</script>


<script type="text/javascript">
	$('a.deactv_btn').confirm({
    title: "confirm Deactive",    
    content: "Are you sure want to deactive?",  
     buttons: {
        Deactive: {
            btnClass: 'btn-red',
            action: function(){
            	 location.href = this.$target.attr('href');
            }
        },         
        Close: {
            btnClass: 'btn-default',
            action: function(){
            	
            }
        },  
       }
});

</script>

<script type="text/javascript">
$(document).on('click','.log_view',function(){
    var id        = $(this).data('id');
    var title     = $(this).data('title');
    /*var columns   = $(this).data('column');*/
    $.ajax({
      type: "POST",
      url:  '<?php echo base_url() ?>admin/Log/getLogData',
      data: {id:id,title:title},
      dataType:'JSON',
      success: function(response){
        //alert(response);
        $("#log_activity_modal").modal('show');
        $("#log_activity").html(response['html']);
      },
      error:function(response){          
         $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'error',
          });                    
      }
  });
    
});
$(document).on('change','#rev_status',function(){
    $("#subm_resv").attr('disabled','true');
    var id              = $(this).data('id');
    var url             = $(this).data('url');
    var change_status   = $(this).val();
    var old_status      = $(this).data('status');
    $.confirm({        
        title: 'Confirm!',
        content: 'Are you sure want to change your status?',
        buttons: {            
            Okay: {
                btnClass: 'btn-green',
                action: function(){                 
                  if(change_status == '0' || change_status == '3'){                    
                    $("#cancel_reject_reason").modal('show');
                    if(change_status == '0'){
                      $("#tile").html('cancellation');
                    }
                    else{
                      $("#tile").html('rejection');
                    }
                    $("#reservation_id").val(id);
                    $("#action_url").val(url);
                    $("#change_status").val(change_status);
                    
                  }
                  else{
                    $(".loader2").css('display','block');
                    $(".wrapper").hide();
                    $.ajax({
                        type: "POST",
                        url:  url,
                        data: {id:id,change_status:change_status},
                        dataType:'html',
                        success: function(response){
                          //alert(response);
                          if(response == 1){  
                            $(".loader2").css('display','none');
                            $(".wrapper").show();                          
                            $.alert({
                             type: 'green',
                             title: 'Alert!',
                             content: 'Successfully status changed.',
                            });
                            setTimeout(function(){
                                window.location.reload();
                            },1400);
                          }
                          else{
                            $.alert({
                             type: 'red',
                             title: 'Alert!',
                             content: 'Opp! some problem ,please try again',
                            }); 
                          } 
                          $("#subm_resv").attr('disabled','false');
                        },
                        error:function(response){          
                           $.alert({
                             type: 'red',
                             title: 'Alert!',
                             content: 'error',
                            });                    
                        }
                    });
                  }                    
                }
            },
            Close: {
              btnClass: 'btn-default',
              action: function(){                
                  $("#change_status").val(old_status);
                  $("#subm_resv").attr('disabled','false');
                  window.location.reload();
              }
            }  
        }
    });
});
$(document).on('click','#close_btn',function(){
    location.reload();
});
$(document).on('click','#log_close_btn',function(){
    $("#log_activity_modal").modal('hide');
});
$(document).on('click','#save_status_change',function(){
   
  var id              = $("#reservation_id").val();
  var url             = $("#action_url").val();
  var change_status   = $("#change_status").val();
  var reason          = $("#reason").val();
  if(reason ==""){    
    $("#reason").next('span').html("please put your remark");
    $("#reason").next('span').css('color','red');
  }
  else{    
    $("#reason").next('span').html(" ");
    $("#cancel_reject_reason").modal('hide');
    $(".loader2").css('display','block');
    $(".wrapper").hide();
    $.ajax({        
        type: "POST",
        url:  url,
        data: {id:id,change_status:change_status,reason:reason},
        dataType:'html',
        success: function(response){
          //alert(response);
          if(response == 1){
            $(".loader2").css('display','none');
            $(".wrapper").show(); 
            $.alert({
             type: 'green',
             title: 'Alert!',
             content: 'Successfully status changed.',
            });
            setTimeout(function(){
                window.location.reload();
            },1400);
          }
          else{
            $.alert({
             type: 'red',
             title: 'Alert!',
             content: 'Opp! some problem ,please try again',
            }); 
          } 
        },
        error:function(response){          
           $.alert({
             type: 'red',
             title: 'Alert!',
             content: 'error',
            });                    
        }
    });
  }
});
    $('a.send_request').confirm({    
    title: "confirm Status change",    
    content: "Are you sure want to send request?",  
     buttons: {
        Send: {
            btnClass: 'btn-green',
            action: function(){
              var id = this.$target.data('id');
              var status = this.$target.data('status');
              if(status == '0'){
                change_status = '1';
              }
              else{
                change_status = '0';
              }       
              $.ajax({
                type: "POST",
                url: this.$target.attr('href'),
                data: {id:id,change_status:change_status},
                dataType:'html',
                success: function(response){
                  //alert(response);
                  if(response == 1){
                    $.alert({
                     type: 'green',
                     title: 'Alert!',
                     content: 'Successfully Status change.',
                    });
                    setTimeout(function(){
                        window.location.reload();
                    },1400);                   
                  }
                  else{
                    $.alert({
                     type: 'red',
                     title: 'Alert!',
                     content: 'Opp! some problem ,please try again',
                    }); 
                  } 
                },
                error:function(response){          
                   $.alert({
                     type: 'red',
                     title: 'Alert!',
                     content: 'error',
                    });                    
                }
              });
            }
        },         
        Close: {
            btnClass: 'btn-default',
            action: function(){
              
            }
        },  
       }
});
$('a.inactive_btn').confirm({    
    title: "confirm Activation",    
    content: "Are you sure want to active?",  
     buttons: {
        Activate: {
            btnClass: 'btn-green',
            action: function(){
              var id = this.$target.data('id');
              //alert(id );        
              $.ajax({
                type: "POST",
                url: this.$target.attr('href'),
                data: {id:id,change_status:1},
                dataType:'html',
                success: function(response){
                  //alert(response);
                  if(response == 1){
                    $.alert({
                     type: 'green',
                     title: 'Alert!',
                     content: 'Successfully Activated.',
                    });
                    setTimeout(function(){
                        window.location.reload();
                    },1400);                   
                  }
                  else{
                    $.alert({
                     type: 'red',
                     title: 'Alert!',
                     content: 'Opp! some problem ,please try again',
                    }); 
                  } 
                },
                error:function(response){          
                   $.alert({
                     type: 'red',
                     title: 'Alert!',
                     content: 'error',
                    });                    
                }
              });
            }
        },         
        Close: {
            btnClass: 'btn-default',
            action: function(){
              
            }
        },  
       }
});
$('a.active_btn').confirm({    
    title: "confirm Deactive",    
    content: "Are you sure want to deactive?",  
     buttons: {
        Deactive: {
            btnClass: 'btn-red',
            action: function(){
              var id = this.$target.data('id');
             //alert(id );        
              $.ajax({
                type: "POST",
                url: this.$target.attr('href'),
                data: {id:id,change_status:0},
                dataType:'html',
                success: function(response){
                 //alert(response);
                  if(response == 1){
                    $.alert({
                     type: 'green',
                     title: 'Alert!',
                     content: 'Successfully Deactivated.',
                    }); 
                    setTimeout(function(){
                        window.location.reload();
                    },1400);
                    
                  }
                  else{
                    $.alert({
                     type: 'red',
                     title: 'Alert!',
                     content: 'Opp! some problem ,please try again',
                    }); 
                  } 
                },
                error:function(response){          
                   $.alert({
                     type: 'red',
                     title: 'Alert!',
                     content: 'error',
                    });                    
                }
              });
            }
        },         
        Close: {
            btnClass: 'btn-default',
            action: function(){
              
            }
        },  
       }
});
</script>


<script type="text/javascript">
	$(document).ready(function() {
    /*$('#example').DataTable( {
        initComplete: function () {
            var orderInit = this.api().order();
            this.api().columns(1).every( function () {
                var column = this;
                var select = $('<select  class="js-select2" data-show-subtext="true" data-live-search="true"><option value="">Accident Reference No:</option></select>')
                    .appendTo('#selectapnd')
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                        
                    } );
 
                column.order('DESC').draw(false).data().unique().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
            this.api().order(orderInit).draw(false);
        }

    } );*/
} );    
</script>
<!-- END PAGE LEVEL JS-->
<!-- Modal -->
<div id="calendarModal" class="modal fade">
  <div class="modal-dialog" style="width:300px">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom: 0px solid #fff;">
        <button type="button" class="close" data-dismiss="modal" style="margin-right:-10px;margin-top:-10px"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
      </div>
      <div id="modalBody" class="modal-body">
        
      </div>              
    </div>
  </div>
</div>
<!-- Cancel Reject Reason Modal -->
<div id="cancel_reject_reason" class="modal fade text-left" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-info white">
          <h4 class="modal-title" id="myModalLabel11">Cancel/No-show Reservation Reason</h4>
          <button type="button" class="close" id="close_btn" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>
          <div class="modal-body">            
            <div class="form-group">
              <label>Put reason for <span id="tile"></span><sup>*</sup></label>
              <textarea class="form-control" id="reason" name="reason" value="" required></textarea>
              <span></span>
              <input type="hidden" id="reservation_id" value="">
              <input type="hidden" id="action_url" value="">
              <input type="hidden" id="change_status" value="">
            </div>
          </div>
          <div class="modal-footer">
          <button type="button" class="btn grey btn-outline-secondary" id="close_btn" data-dismiss="modal">Close</button>
          <button id="save_status_change" type="button" class="btn btn-outline-info">Save</button>
          </div>
        </div>
    </div>
</div>
<!-- User unique code varified modal -->
<div id="unique_code" class="modal fade text-left" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-info white">
          <h4 class="modal-title" id="myModalLabel11">Pin</h4>
          <button type="button" class="close" id="close_btn" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div>
          <div class="modal-body" style="text-align: center;">            
            <div class="form-group">
              <label>Enter your 4 digit pin <span id="tile"></span><sup>*</sup></label><br>
              
              <input type="text" required id="code" value="">
              <input type="hidden" id="user_id" value="<?php if($this->session->userdata('user_data') != ''): echo $this->session->userdata('user_data'); endif; ?>"><br>
              <span id="error_msg" style="display:none;color:red">Incorrect pin</span>              
            </div>
          </div>
          <div class="modal-footer">
          <button type="button" class="btn grey btn-outline-secondary" id="close_btn" data-dismiss="modal">Close</button>
          <button id="save_code" type="button" class="btn btn-outline-info">Save</button>
          </div>
        </div>
    </div>
</div>
<!--******* Change password **********-->
<div id="change_password_modal" class="modal fade text-left" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-info white">
            <h4 class="modal-title" id="myModalLabel11">Change Password</h4>
            <button type="button" class="close" id="close_btn" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="text-align: center;">            
              <div class="px-3">
                <form  id="myForm" class="form custom_form_style" method="Post" action="<?= base_url();?>commission/changepassword/changeuserpasswd">
                  <div class="form-body">
                    <div id="myRadioGroup">
                      <div class="change_pasword_area">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="oldpassw">Old Password</label>
                              <input type="password" class="form-control" id="oldpassw" name="oldpassw" placeholder="old password" value="" required="required">
                              <?php echo form_error('oldpassw'); ?>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="newpassw">New Password</label>
                              <input type="password" class="form-control" id="newpassw" name="newpassw" value="" placeholder="new password" required="required">
                              <?php echo form_error('newpassw'); ?>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="confpassw">Confirm Password</label>
                              <input type="password" class="form-control" id="confpassw" name="confpassw" placeholder="confirm password" value="" required="required">
                              <?php echo form_error('confpassw'); ?>
                            </div>
                          </div>
                          <?php //$admin=$this->session->userdata('admin');?>
                          <?php $user_id=$this->session->userdata('user_data');?> 
                          <div class="box-footer">
                            <div class="row">
                              <div class="col-sm-6">
                                <input type="hidden" name="user_id" value="<?php echo $user_id;?>">
                     <!--    <button type="submit" class="btn btn-sm btn-primary">Submit</button> -->
                                <button type="submit" class="btn btn-success" style="margin-left: 15px;">
                                  <i class="fa fa-floppy-o" aria-hidden="true"></i> Submit
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn grey btn-outline-secondary" id="close_btn" data-dismiss="modal">Close</button>
            <button id="save_code" type="button" class="btn btn-outline-info">Save</button>
          </div>
        </div>
    </div>
</div>
<!--******* Log details list **********-->

<div id="log_activity_modal" class="modal fade text-left" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel11" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="log_activity">
                        
        </div>
    </div>
</div>

</body>
</html>
<script>
$(document).on('change','#refund_type',function(){
    var refund_type = $(this).val();
    if(refund_type =='full'){
      var bond_accumulated_amt  = $("#bond_accumulated_amt").val();
      $('#refund_amnt').val(bond_accumulated_amt);
      $('#refund_amnt').attr('readonly',true);
    }
    else{
      $('#refund_amnt').val('');
      $('#refund_amnt').attr('readonly',false);      
    }
});
$(document).on('focusout','#refund_amnt',function(){
    var refund_amt            = $(this).val();
    var bond_accumulated_amt  = $("#bond_accumulated_amt").val();
    if(parseFloat(bond_accumulated_amt) < parseFloat(refund_amt)){
      alert("Refund amount should not be grater than remaining bond refund.");
      $(this).css("background-color", "yellow"); 
    }
    else{
      $(this).css("background-color", "white");
    }    
});
$(document).on('click','#sub_refund',function(event){
  event.preventDefault();
  var refund_amt            = $("#refund_amnt").val();
  var bond_accumulated_amt  = $("#bond_accumulated_amt").val();
  if(parseFloat(bond_accumulated_amt) < parseFloat(refund_amt)){
    alert("Refund amount should not be grater than remaining bond refund.");
    $(this).css("background-color", "yellow");
  }  
  else{
    $('#refund_form').submit();
  }
});

// Restricts input for each element in the set of matched elements to the given inputFilter.
(function($) {
  $.fn.inputFilter = function(inputFilter) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
      if (inputFilter(this.value)) {
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      }
    });
  };
}(jQuery));

$(".num_validation_cls").inputFilter(function(value) {
  return /^\d*$/.test(value); 
});

</script>

