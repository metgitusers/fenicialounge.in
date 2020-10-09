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
                    <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/reservation/bookReservation">
                      	<div class="form-body">
	                        <div class="row col-md-12">	                        	
		                      	<div class="col-md-4">
                              <div class="form-group">
                                <label>Preferred date of reservation <sup>*</sup></label>
                                <div class="input-group">
                                  <input type="text" placeholder="DD/MM/YYYY" required name="reservation_date" id="reservation_date" class="form-control pickadate" readonly="true"/>
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
                                </div>
                              </div>
                            </div>
                            <!--<div class="col-md-4">
                                <div class="form-group">
                                  <label>Preferred time of reservation<sup>*</sup></label>                                  
                                  <select id="time_slot_id" name="time_slot_id" class="form-control" required>
                                    <option value="">Select time slot</option>                                    
                                  </select>
                                </div>
                            </div>-->
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
                                  <input type="radio" class="reservation_for" name="reservation_for" value="Someone Else" required >Someone Else
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
                                              <option value="<?php echo $mlist['member_id']; ?>" <?php if($mlist['member_id'] == set_value('member_id')): echo "selected";endif; ?>><?php echo $mlist['full_name']; ?></option>
                                    <?php   endforeach; ?>
                                    <?php endif; ?>
                                  </select>
                                </div>
                            </div>
                            <div class="row col-md-12" id="member_div" style="color:#fff;padding:0 0 0 12px;line-height: 35px;margin: 0 0 20px;background: #6269691f;">
                                <div id="old_member" class="row col-md-12">
                                    <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Email ID <sup>*</sup></label>
                                        <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="old_email" name="email" class="form-control" value="<?php echo set_value('email');?>">
                                        
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>Mobile <sup>*</sup></label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="old_mobile" name="mobile" value="<?php echo set_value('mobile');?>" class="form-control mobileNO">
                                        <span></span>                                        
                                      </div>
                                    </div>
                                </div>
                                <div id="new_member" class="row col-md-12" style="display:none">
                                  <div class="col-md-12 row" >                                     
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>First Name <sup>*</sup></label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_first_name" class="form-control" name="new_first_name" value="<?php echo set_value('new_first_name');?>">
                                      </div>
                                    </div>                                   
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>Last Name <sup>*</sup></label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_last_name" class="form-control" name="new_last_name" value="<?php echo set_value('new_last_name');?>">
                                      </div>
                                    </div>
                                  </div> 
                                  <div class="col-md-12 row" >                                                   
                                    <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Email ID <sup>*</sup></label>
                                        <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_email" name="new_email" class="form-control" value="<?php echo set_value('new_email');?>">
                                        <?php echo form_error('Email', '<div class="error">', '</div>'); ?>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>Mobile <sup>*</sup></label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_mobile" name="new_mobile" class="form-control mobileNO" value="<?php echo set_value('new_mobile');?>">
                                        <span></span>
                                        <?php echo form_error('Mobile', '<div class="error">', '</div>'); ?>
                                      </div>
                                    </div>                                    
                                  </div>              	
                                </div>
                            </div>
                          </div>
                          <div class="row col-md-12">
                              <label>Message<sup>*</sup></label>
                              <textarea name="message" class="form-control" required><?php echo set_value('message');?></textarea>
                          </div>
                        </div>                          
            						<div class="form-actions">
            							<a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/Reservation'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
            							<button type="submit" class="btn btn-success">
            							  <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
            							</button>
						            </div>
                    </form>
                  <?php
                  } else { ?>
                      <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/reservation/editReservation/<?php echo $reservation_list[0]["reservation_id"];?>/<?php echo $reservation_list[0]["reservation_id"];?>">
                        <div class="form-body">
                          <div class="row col-md-12">                           
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Preferred date of reservation <sup>*</sup></label>
                                <div class="input-group">                                
                                  <input type="text" name="reservation_date" id="reservation_date" class="form-control pickadate" value="<?php echo DATE('d/m/Y',strtotime($reservation_list[0]["reservation_date"])); ?>" placeholder="" required="" />
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
                                  <input class="form-control timepicker" id="reservation_time" name="reservation_time" value="<?php echo DATE('h:i A',strtotime($reservation_list[0]["reservation_time"]));?>" required/>
                                </div>
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
                                            <option value="<?php echo $list['zone_id']; ?>" <?php if($list['zone_id'] == $reservation_list[0]["zone_id"]): echo "selected";endif;?>><?php echo $list['zone_name']; ?></option>
                                    <?php   endforeach; ?>
                                    <?php endif; ?>
                                  </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Number of guests <sup>*</sup></label>
                                <input type="number" min="1" max="30" class="form-control max_min_capacity" name="no_of_guests" value="<?php echo $reservation_list[0]["no_of_guests"];?>" required>
                              </div>
                            </div>
                            <div class="col-md-4">                              
                              <div class="form-group">
                                  <label>Reservation for <sup>*</sup></label> <br>                                 
                                  <input type="radio" class="reservation_for" name="reservation_for" value="My self" <?php if($reservation_list[0]["reservation_for"] == 'My self') : echo 'checked';endif; ?> required >Member
                                  <input type="radio" class="reservation_for" name="reservation_for" value="Someone Else" <?php if($reservation_list[0]["reservation_for"] == 'Someone Else') : echo 'checked';endif; ?> required >Someone Else
                              </div>                            
                            </div>
                          </div>
                          <h5 class="card-title">Member Details</h5>
                          <div class="row col-md-12"> 
                            <div class="col-md-5">
                                <div class="form-group">
                                  <label>Member<sup>*</sup></label>                                  
                                  <select name="member_id" id="member_id" class="form-control" style="pointer-events: none;cursor:no-drop;">
                                    <option value="">Select Member</option>
                                    <?php if(!empty($member_list)): ?>
                                    <?php   foreach($member_list as $mlist): ?>
                                              <option value="<?php echo $mlist['member_id']; ?>" <?php if( $mlist['member_id'] == $reservation_list[0]["member_id"]): echo "selected";endif;?>><?php echo $mlist['full_name']; ?></option>
                                    <?php   endforeach; ?>
                                    <?php endif; ?>                                      
                                  </select>
                                </div>                                
                            </div>
                            <div class="row col-md-12" id="member_div" style="color:#fff;padding:0 0 0 12px;line-height: 35px;margin: 0 0 20px;background: #6269691f;">
                                <div id="old_member" class="row col-md-12" >
                                    <div class="col-md-5">
                                      <div class="form-group">
                                          <label>Email ID <sup>*</sup></label>
                                        <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="old_email" name="email" class="form-control" required="" readonly value="<?php echo $reservation_list[0]["email"]; ?>">
                                        
                                      </div>
                                    </div>
                                    <div class="col-md-3">
                                      <div class="form-group">
                                        <label>Mobile <sup>*</sup></label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="old_mobile" name="mobile" class="form-control mobileNO" readonly required="" value="<?php echo $reservation_list[0]["member_mobile"]; ?>">
                                        <span></span>                                        
                                      </div>
                                    </div>                                    
                                    <!--<div class="col-md-3">
                                      <div class="form-group">
                                        <label>Another Mobile No.<sup>(optional)</sup></label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="old_second_mobile" name="secondary_mobile" class="form-control mobileNO" value="<?php echo $reservation_list[0]["member_second_mobile"]; ?>">
                                        <span></span>
                                        <?php echo form_error('secondary_mobile', '<div class="error">', '</div>'); ?>
                                      </div>
                                    </div>-->
                                </div>                                
                            </div>                      
                          </div>
                          <div class="row col-md-12">
                              <label>Message<sup>*</sup></label>
                              <textarea name="message" class="form-control" required><?php echo $reservation_list[0]["message"]; ?></textarea>
                          </div>                        
                        </div>                          
                        <div class="form-actions">
                          <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/Reservation'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
                          <button type="submit" class="btn btn-success">
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
  $('.timepicker').pickatime()
  var dob_max_date = new Date();
  var doc_max_date = new Date();
   
  dob_max_date.setFullYear(dob_max_date.getFullYear() - 18);

  $('#dob').pickadate({
    format: 'dd-mm-yyyy',
    max: dob_max_date,
    selectYears: true,
    selectMonths: true,
    selectYears: 80
  });
  $('#doc').pickadate({
    format: 'dd-mm-yyyy',
    max: doc_max_date,
    selectYears: true,
    selectMonths: true,
    selectYears: 80
  });
  $('#reservation_date').pickadate({
    format: 'dd/mm/yyyy',
    min: new Date(),
    max: "01/01/2050",
    selectYears: true,
    selectMonths: true,
    selectYears: 80
  });
});
$(document).on('change','.reservation_for',function(){
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
        $("#new_first_name").attr('required',false);
        $("#new_last_name").attr('required',false);
        $("#new_email").attr('required',false);
        $("#new_mobile").attr('required',false);       
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
        $("#new_first_name").attr('required',true);
        $("#new_last_name").attr('required',true);
        $("#new_email").attr('required',true);
        $("#new_mobile").attr('required',true);
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
      data:{mobile:mobile},
      dataType:'json',
      success: function(response){
      //alert(response);           
        $("#new_email").val(response['email']);
        $("#new_first_name").val(response['first_name']);
        $("#new_last_name").val(response['last_name']);        
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
</script>