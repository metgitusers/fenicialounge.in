<div class="main-content">
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Basic form layout section start -->
      <section id="basic-form-layouts">
        <!--<div class="row">
          <div class="col-sm-12">
            <h2 class="content-header">Driver Master</h2>
          </div>
        </div>-->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <div class="page-title-wrap">
                  <h4 class="card-title">Reservation Details</h4>
                  <a class="title_btn t_btn_list" href="<?= base_url(); ?>admin/reservation"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> Reservation List</a>
                </div>


                <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
              </div>
              <div class="card-body">
                <div class="px-3">

                  <?php
                  if (empty($reservation_list)) { ?>

                    <?php if ($this->session->flashdata('success_msg')) : ?>
                        <div class="alert alert-success">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                          <?php echo $this->session->flashdata('success_msg') ?>
                        </div>
                    <?php endif ?>
                    <?php if ($this->session->flashdata('error_msg')) : ?>
                        <div class="alert alert-danger">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                          <?php echo $this->session->flashdata('error_msg') ?>
                        </div>
                    <?php endif ?>
                    <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/reservation/bookReservation" id="addForm">
                      	<div class="form-body">
	                        <div class="row col-md-12">	                        	
		                      	<div class="col-md-4">
                              <div class="form-group">
                                <label for="reservation_date">Preferred date of reservation <sup>*</sup></label>
                                <div class="input-group">
                                  <input type="text" placeholder="DD/MM/YYYY" required name="reservation_date" id="reservation_date" class="form-control pickadate"/>
                                   <?php echo form_error('Booking Date', '<div class="error">', '</div>'); ?>
                                  <div class="input-group-append">
                                    <span class="input-group-text">
                                      <span class="fa fa-calendar-o"></span>
                                    </span>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Preferred time of reservation <sup>*</sup></label>
                                <div class="input-group time_pick">
                                  <input class="form-control timepicker" value="" id="reservation_time" name="reservation_time" required/>
                                  <?php echo form_error('Booking Time', '<div class="error">', '</div>'); ?>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                  <label>Reservation Type<sup>*</sup></label>                                  
                                  <select id="reservation_type" name="reservation_type" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="Online">Online</option>
                                    <option value="Walk-in">Walk-in</option>
                                    <option value="Phone">Phone</option>                                    
                                  </select>
                                </div>
                            </div>
                          </div>
                          <div class="row col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                  <label>Zone <sup>*</sup></label>                                  
                                  <select id="zone_id" name="zone_id" class="form-control zoneId" required>
                                    <option value="">Select Zone</option>
                                    <?php if(!empty($zone_list)): ?>                                            
                                    <?php   foreach($zone_list as $list): ?>
                                            <option value="<?php echo $list['zone_id']; ?>" <?php if($list['zone_id'] == set_value('zone_id')): echo "selected";endif; ?>><?php echo $list['zone_name']; ?></option>
                                    <?php   endforeach; ?>
                                    <?php endif; ?>
                                  </select>
                                </div>
                            </div>
                          	<div class="col-md-4">
	                            <div class="form-group">
	                              <label>Number of guests <sup>*</sup></label>
	                              <input type="number" min="1" max="300" class="form-control max_min_capacity" name="no_of_guests" value="<?php echo set_value('no_of_guests');?>" required>
	                            </div>
                          	</div>
                            <div class="col-md-4">
                              <div class="form-group">
                                  <label>Reservation for <sup>*</sup></label> <br>                                 
                                  <input type="radio" class="reservation_for" name="reservation_for" value="My self" required checked>Member
                                  <input type="radio" class="reservation_for" name="reservation_for" value="Someone else" required >Guest
                              </div>
                            </div>
                          </div>
                          <h5 class="card-title">Guests Details</h5>
                          <div class="row col-md-12"> 
                            <div class="col-md-5">
                                <div class="form-group" id="old_member_select">
                                  <label>Member<sup>*</sup></label>                                  
                                  <select name="member_id" id="member_id" class="js-select2 form-control" >
                                    <option value="">Select Member</option>
                                    <?php if(!empty($member_list)): ?>
                                    <?php   foreach($member_list as $mlist): ?>
                                              <option value="<?php echo $mlist['member_id']; ?>" <?php if($mlist['member_id'] == set_value('member_id')): echo "selected";endif; ?>><?php echo ucwords($mlist['full_name']); ?></option>
                                    <?php   endforeach; ?>
                                    <?php endif; ?>
                                  </select>
                                </div>
                            </div>
                            <div class="row col-md-12" id="member_div" style="color:#fff;line-height: 35px;margin: 0 0 20px;background: #6269691f;">
                                <div id="old_member" style="width: 100%;">
                                    <div class="row">
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label>Mobile</label>
                                          <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="old_mobile" name="mobile" value="<?php echo set_value('mobile');?>" class="form-control mobileNO" >
                                          <span></span>                                        
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email ID</label>
                                          <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="old_email" name="email" class="form-control" value="<?php echo set_value('email');?>" >
                                          
                                        </div>
                                      </div>                                      
                                    </div>
                                </div>
                                <div id="new_member" style="display:none;width: 100%;">
                                  <div class="row">                                   
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label>Mobile</label>
                                          <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_mobile" name="new_mobile" class="form-control mobileNO" value="<?php echo set_value('new_mobile');?>" >
                                          <span></span>
                                          <?php echo form_error('Mobile', '<div class="error">', '</div>'); ?>
                                        </div>
                                      </div>                                                   
                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email ID</label>
                                          <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_email" name="new_email" class="form-control" value="<?php echo set_value('new_email');?>" >
                                          <?php echo form_error('Email', '<div class="error">', '</div>'); ?>
                                        </div>
                                      </div>                                                                      
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label>First Name </label>
                                          <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_first_name" class="form-control" name="new_first_name" value="<?php echo set_value('new_first_name');?>" >
                                        </div>
                                      </div>                                   
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label>Last Name</label>
                                          <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_last_name" class="form-control" name="new_last_name" value="<?php echo set_value('new_last_name');?>" >
                                        </div>
                                      </div>                                                                                 	
                                  </div>
                                </div>
                            </div>
                          <div class="row col-md-12">
                              <label>Message</label>
                              <textarea name="message" class="form-control"><?php echo set_value('message');?></textarea>
                          </div>
                        </div>                          
            						<div class="form-actions">
            							<a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/Reservation'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
            							<button type="button" class="btn btn-success" id="addReservationBtn">
            							  <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
            							</button>
            							<button type="submit" class="btn btn-success" id="addReservationBtnSubmit" style="display:none;">
            							  <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
            							</button>
						            </div>
                    </form>
                  <?php
                  } 

                  else { ?>
                      <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/reservation/editReservation">
                      <input type="hidden" id="reservation_id" name="reservation_id" value="<?php echo $reservation_list['reservation_id']; ?>">
                        <div class="form-body">
                          <div class="row col-md-12">                           
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Preferred date of reservation <sup>*</sup></label>
                                <div class="input-group">
                                  <input type="text" placeholder="DD/MM/YYYY" required name="reservation_date" id="reservation_date" class="form-control pickadate" value="<?php echo date('d/m/Y',strtotime($reservation_list['reservation_date'])); ?>"/>
                                  <div class="input-group-append">
                                    <span class="input-group-text">
                                      <span class="fa fa-calendar-o"></span>
                                    </span>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Preferred time of reservation <sup>*</sup></label>
                                <div class="input-group time_pick">
                                  <input class="form-control timepicker" id="reservation_time" name="reservation_time" required value="<?php echo date('h:i A',strtotime($reservation_list['reservation_time'])); ?>"/>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                  <label>Reservation Type<sup>*</sup></label>                                  
                                  <select id="reservation_type" name="reservation_type" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="App" <?php if($reservation_list['reservation_type'] == 'App'){echo "selected";} ?>>App</option>
                                    <option value="Online" <?php if($reservation_list['reservation_type'] == 'Online'){echo "selected";} ?>>Online</option>
                                    <option value="Walk-in" <?php if($reservation_list['reservation_type'] == 'Walk-in'){echo "selected";} ?>>Walk-in</option>
                                    <option value="Phone" <?php if($reservation_list['reservation_type'] == 'Phone'){echo "selected";} ?>>Phone</option>                                    
                                  </select>
                                </div>
                            </div>
                          </div>
                          <div class="row col-md-12">
                            <div class="col-md-5">
                                <div class="form-group">
                                  <label>Zone <sup>*</sup></label>                                  
                                  <select id="zone_id" name="zone_id" class="form-control zoneId" required>
                                    <option value="">Select Zone</option>
                                    <?php if(!empty($zone_list)): ?>                                            
                                    <?php   foreach($zone_list as $list): ?>
                                            <option value="<?php echo $list['zone_id']; ?>" <?php if($list['zone_id'] == $reservation_list['zone_id']): echo "selected";endif; ?>><?php echo $list['zone_name']; ?></option>
                                    <?php   endforeach; ?>
                                    <?php endif; ?>
                                  </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>Number of guests <sup>*</sup></label>
                                <input type="number" min="1" max="300" class="form-control max_min_capacity" name="no_of_guests" value="<?php echo $reservation_list['no_of_guests'];?>" required>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                  <label>Reservation for <sup>*</sup></label> <br>                                 
                                  <input type="radio" class="edit_reservation_for" name="reservation_for" value="My self" <?php if($reservation_list['reservation_for'] == 'My self'){echo "checked";} ?>>Member
                                  <input type="radio" class="edit_reservation_for" name="reservation_for" value="Someone else" <?php if($reservation_list['reservation_for'] == 'Someone else'){echo "checked";} ?>>Someone else
                              </div>
                            </div>
                          </div>
                          <h5 class="card-title">Guests Details</h5>
                          <div class="row col-md-12"> 
                            <div class="col-md-5">
                                <div class="form-group" id="old_member_select">
                                  <label>Member<sup>*</sup></label>                                  
                                  <select name="member_id" id="member_id" class="form-control">
                                    <option value="">Select Member</option>
                                    <?php if(!empty($member_list)): ?>
                                    <?php   foreach($member_list as $mlist): ?>
                                              <option value="<?php echo $mlist['member_id']; ?>" <?php if($mlist['member_id'] == $reservation_list['member_id']): echo "selected";endif; ?>><?php echo $mlist['full_name']; ?></option>
                                    <?php   endforeach; ?>
                                    <?php endif; ?>
                                  </select>
                                </div>
                            </div>
                            <div class="row col-md-12" id="member_div" style="color:#fff;line-height: 35px;margin: 0 0 20px;background: #6269691f;">
                                <div id="old_member" style="width: 100%;">
                                    <div class="row">
                                      <div class="col-md-6">
                                        <div class="form-group">
                                          <label>Mobile</label>
                                          <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="old_mobile" name="mobile" value="<?php echo $reservation_list['member_mobile'];?>" class="form-control mobileNO" >
                                          <span></span>                                        
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email ID</label>
                                          <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="old_email" name="email" class="form-control" value="<?php echo $reservation_list['email'];?>" >
                                          
                                        </div>
                                      </div>
                                    </div>                                    
                                </div>                                
                                <div id="new_member" style="display:none;width: 100%;">
                                  <div class="row">
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>Mobile</label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_mobile" name="new_mobile" class="form-control mobileNO"  value="<?php echo $reservation_list['member_mobile'];?>"  >
                                        <span></span>
                                        <?php echo form_error('Mobile', '<div class="error">', '</div>'); ?>
                                      </div>
                                    </div>                                                   
                                    <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Email ID</label>
                                        <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_email" name="new_email" class="form-control" value="<?php echo $reservation_list['email'];?>" >
                                        <?php echo form_error('Email', '<div class="error">', '</div>'); ?>
                                      </div>
                                    </div>                                                                        
                                  
                                  <div class="col-md-6">
                                      <div class="form-group">
                                        <label>First Name </label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_first_name" class="form-control" name="new_first_name" value="<?php echo $reservation_list['first_name'];?>" >
                                      </div>
                                    </div>                                   
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_last_name" class="form-control" name="new_last_name" value="<?php echo $reservation_list['last_name'];?>" >
                                      </div>
                                    </div> 
                                    </div>                                                
                                </div>
                            </div>
                          </div>
                          <div class="row ">
                          <div class="col-md-8">
                              <label>Message</label>
                              <textarea name="message" class="form-control"><?php echo $reservation_list['message'];?></textarea>
                          </div>
                          <?php   if($reservation_list['resv_status']!=''):
                                      if($reservation_list['resv_status'] == 0):
                                          $class ="red";
                                          $resv_status   = "Cancelled";
                                      elseif($reservation_list['resv_status'] == 1):
                                          $class ="orange";
                                          $resv_status   = "Pending";
                                      elseif($reservation_list['resv_status'] == 2):
                                          $class ="green";
                                          $resv_status   = "Reserved";
                                      else:
                                          $class ="#b30000";
                                          $resv_status   = "No-show";
                                      endif;
                                  endif;                                                                                           
                          ?>
                          <div class="col-md-3"> 
                              <label>Status</label>                                                                                                                                                                                            
                              <select name="rev_status" id="rev_status" style="color:white;background-color:<?php echo $class; ?>" data-url="<?php echo base_url().'admin/reservation/changeStatus'; ?>" data-status="<?php echo $reservation_list['resv_status']; ?>" data-id="<?php echo $reservation_list['reservation_id']; ?>" class="form-control " required>
                                  <option value="">Select</option>
                                  <!--<option style="background-color: orange;color:white" value="1" <?php if($reservation_list['resv_status'] =='1'): echo "selected";endif; ?>>Pending</option>-->
                                  <option style="background-color: red;color:white" value="0" <?php if($reservation_list['resv_status'] =='0'): echo "selected";endif; ?>>Cancelled</option>
                                  <option style="background-color: green;color:white" value="2" <?php if($reservation_list['resv_status'] =='2'): echo "selected";endif; ?>>Reserved</option>                                                                                            
                                  <option style="background-color: #b30000;color:white" value="3" <?php if($reservation_list['resv_status'] =='3'): echo "selected";endif; ?>>No-show</option>
                              </select>
                          </div>
                          </div>
                        </div>                          
                        <div class="form-actions">
                          <input type="hidden" name="page" name="redirect_page" value="<?php echo $page; ?>">
                          <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/Reservation'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
                          <button type="submit" id="subm_resv" class="btn btn-success">
                            <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
                          </button>
                        </div>
                    </form>
                  <?php
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>


        </div>
      </section>
      <!-- // Basic form layout section end -->
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
  var reservation_for = $(".edit_reservation_for:checked").val();
  if(reservation_for =='Someone else'){
    $("#new_member").css('display','block');
    $("#old_member").css('display','none');
    $("#old_member_select").css('display','none');    
  }
  else{
    $("#new_member").css('display','none');
    $("#old_member").css('display','block');
  }
  $('.timepicker').pickatime({
    min: [12,0],
    max: [23,59],
    interval: 15
  })
  $('#reservation_date').pickadate({
    format: 'dd/mm/yyyy',
    min: new Date(),
    max: "01/01/2050"
  });
});
$(document).on('change','.reservation_for,.edit_reservation_for',function(){
    var option  = $(this).val();
    //alert(option);
    if(option == ''){
        $("#old_member").hide();
        $("#new_member").hide();
        $("#member_div").hide();
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Please select reservation for.',
        });
    }
    else{      
      $("#member_div").show();
      if(option == 'My self'){ 
        $("#member_id").attr('required',true);
        $("#member_id").val('');
        $("#old_email").val('');
        $("#old_mobile").val('');
        // $("#new_first_name").attr('required',false);
        // $("#new_last_name").attr('required',false);
        // $("#new_email").attr('required',false);
        // $("#new_mobile").attr('required',false); 
        // $("#old_email").attr('required',true);
        // $("#old_mobile").attr('required',true);
        $("#old_member").show();
        $("#old_member_select").show();
        $("#new_member").hide();

      }
      else{
        $("#new_first_name").val('');
        $("#new_last_name").val('');
        $("#new_email").val('');
        $("#new_mobile").val('');
        $("#member_id").attr('required',false);
        $('#member_id').val('');
        $('#old_member_select').hide();
        // $("#new_first_name").attr('required',true);
        // $("#new_last_name").attr('required',true);
        // $("#new_email").attr('required',true);
        // $("#new_mobile").attr('required',true);
        $("#old_email").attr('required',false);
        $("#old_mobile").attr('required',false);
        $("#new_member").show();
        $("#old_member").hide();
      }
    }
    
});
$(document).on('change','#member_id',function(){
    var member_id  = $('#member_id').val(); 
    //alert(member_id);       
    $.ajax({
      type: "POST",
      url: '<?php echo base_url("admin/reservation/getMemberDetails")?>',
      data:{member_id:member_id},
      dataType:'json',
      success: function(response){           
        if(response['email'] !=''){
          $("#old_email").val(response['email']);
          $("#old_mobile").val(response['mobile']);
                 
        }
        else{
          $.alert({
             type: 'red',
             title: 'Alert!',
             content: 'Error',
          });
        }
      },
      error:function(response){
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Error',
        });
      }
  });
});
$(document).on('change','.zoneId',function(){
    var zone_id  = $(this).val();
    $.ajax({
      type: "POST",
      url: '<?php echo base_url("admin/Reservation/getMaxMinCapacity")?>',
      data:{zone_id:zone_id},
      dataType:'json',
      success: function(response){  
       //alert(response);
      //alert(response['max']."$$".response['min']);             
        if(response['max'] !=''){
          $(".max_min_capacity").attr('min',response['min']);
          $(".max_min_capacity").attr('max',response['max']);
                 
        }
        else{
          $.alert({
             type: 'red',
             title: 'Alert!',
             content: 'Error',
          });
        }
      },
      error:function(response){
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Error',
        });
      }
  });
});
function nospaces(t){
    if(t.value.match(/\s/g) && t.value.length == 1){
        alert('Sorry, you are not allowed to enter any spaces in the starting.');

        t.value=t.value.replace(/\s/g,'');
    }
}

  var password = document.getElementById("password")

    ,
    confirm_password = document.getElementById("confirm_password");

  $(document).on('keyup','.landlineNO',function(){
    var landline_no = $(this).val();  
    if(!validatelandlineNumber(landline_no)) {  
      $(this).next('span').html('Please enter valid landline no.');
      $(this).next('span').css({'color':'red','font-size':'12px'});
    } 
    else{
      $(this).next('span').html('');
    }
  });
  $(document).on('keyup','.mobileNO',function(){
    var mobile_no = $(this).val();
    if(!validateNumber(mobile_no)){
      $(this).next('span').html('Please enter a valid mobile no.');
      $(this).next('span').css({'color':'red','font-size':'12px'});
    }
    else{
      $(this).next('span').html('');
    }
  });
function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#profile_image').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]);    
    $("#profile_img_div").show();
  }
}

$("#profile_img").change(function() {
  readURL(this);
});
function validateNumber(mobnumber) {
    var filter = /^(\d{3})(\d{3})(\d{4})$/;
    if (filter.test(mobnumber)) {
      return true;
    } else {
      return false;
    }
}
$(document).on('keyup','.mobileNO',function(){
  var mobile_no = $(this).val();
  if(!validateNumber(mobile_no)){
    $(this).next('span').html('Please enter a valid mobile no.');
    $(this).next('span').css({'color':'red','font-size':'12px'});
  }
  else{
    $(this).next('span').html('');
  }
});
$(document).on('change','#reservation_date',function(){
  var reservation_date = $(this).val();
  $.ajax({
          type: "POST",
          url: '<?php echo base_url("admin/reservation/getReservationTimeSlot")?>',
          data:{reservation_date:reservation_date},
          dataType:'json',
          success: function(response){
          //alert(response);           
            if(response != 'Blank'){
              $('#time_slot_id').find('option').remove().end();
              $("<option></option>", {value: '', text: 'Select time slot'}).appendTo('#time_slot_id');
              $.each( response, function( key, value ) {
                var name = response[key].time_slot;
                var time_slotid = response[key].time_slot_id;         
                $("<option></option>", {value: time_slotid, text: name}).appendTo('#time_slot_id');
              });
            }
            else{
              $('#time_slot_id').find('option').remove().end();
              $("<option></option>", {value: '', text: 'Select time slot'}).appendTo('#time_slot_id');
            } 
          },
          error:function(response){
            $.alert({
               type: 'red',
               title: 'Alert!',
               content: 'Error',
            });
          }
      });
});

/********************/

$(document).on('keyup','#new_mobile',function(){
  var mobile  = $(this).val();  
  //alert(mobile);
  $.ajax({
      type: "POST",
      url: '<?php echo base_url("admin/reservation/getPastGuestInfo")?>',
      data:{field_value:mobile,field_name:'member_mobile'},
      dataType:'json',
      success: function(response){
      //alert(response);
        if(response['email'] !=''){
          $("#new_email").val(response['email']);
        }           
        if(response['first_name'] !=''){
          $("#new_first_name").val(response['first_name']);
        }         
        if(response['last_name'] !=''){
          $("#new_last_name").val(response['last_name']); 
        }                 
      },
      error:function(response){
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Error',
        });
      }
  });

});

$(document).on('keyup','#new_email',function(){
  var email  = $(this).val();  
  //alert(email);
  $.ajax({
      type: "POST",
      url: '<?php echo base_url("admin/reservation/getPastGuestInfo")?>',
      data:{field_value:email,field_name:'email'},
      dataType:'json',
      success: function(response){
      //alert(response); 
        if(response['mobile']!=""){
          $("#new_mobile").val(response['mobile']);
        }          
        if(response['first_name']!=""){
          $("#new_first_name").val(response['first_name']);
        }
        if(response['last_name']!=""){
          $("#new_last_name").val(response['first_name']);
        }               
      },
      error:function(response){
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Error',
        });
      }
  });
});

$('#addReservationBtn').click(function(){
    if($("#reservation_date").val()=="")
    {
        alert('Please select booking date');
        return false;
    }
    else if($("#reservation_time").val()=="")
    {
        alert('Please select booking time');
        return false;
    }
    else
    {
        $('#addReservationBtn').hide();
         $('#addReservationBtnSubmit').show();
         $('#addReservationBtnSubmit').click();
    }
    
});
/*******************/
</script>