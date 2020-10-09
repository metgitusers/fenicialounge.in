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
                  <h4 class="card-title">Reservation Details View</h4>
                  <a class="title_btn t_btn_list" href="<?= base_url(); ?>admin/reservation"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> Reservation List</a>
                </div>


                <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
              </div>
              <div class="card-body">
                <div class="px-3">

                  <?php
                  if (!empty($reservation_list)) { ?>
                    <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/reservation/editReservation">
                      <input type="hidden" id="reservation_id" name="reservation_id" value="<?php echo $reservation_list['reservation_id']; ?>">
                        <div class="form-body">
                          <div class="row col-md-12">                           
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Preferred date of reservation <sup>*</sup></label>
                                <div class="input-group">
                                  <input type="text" placeholder="DD/MM/YYYY" required name="reservation_date" id="reservation_date" class="form-control" readonly value="<?php echo date('d/m/Y',strtotime($reservation_list['reservation_date'])); ?>"/>
                                 </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Preferred time of reservation</label>
                                <div class="input-group time_pick">
                                  <input class="form-control" id="reservation_time" name="reservation_time" required value="<?php echo date('h:i A',strtotime($reservation_list['reservation_time'])); ?>" readonly/>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                  <label>Reservation Type</label> 
                                  <input type="text" class="form-control" value="<?php echo $reservation_list['reservation_type']; ?>" readonly>                              
                                </div>
                            </div>
                          </div>
                          <div class="row col-md-12">
                            <div class="col-md-5">
                                <div class="form-group">
                                  <label>Zone </label>                                  
                                  <select id="zone_id" name="zone_id" class="form-control zoneId" disabled>
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
                                <label>Number of guests </label>
                                <input type="number" min="1" max="300" class="form-control max_min_capacity" name="no_of_guests" value="<?php echo $reservation_list['no_of_guests'];?>" readonly>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                  <label>Reservation for</label> <br>                                 
                                  <input type="radio" class="edit_reservation_for" name="reservation_for" value="My self" <?php if($reservation_list['reservation_for'] == 'My self'){echo "checked";} ?> disabled>Member
                                  <input type="radio" class="edit_reservation_for" name="reservation_for" value="Someone else" <?php if($reservation_list['reservation_for'] == 'Someone else'){echo "checked";} ?> disabled>Someone else
                              </div>
                            </div>
                          </div>
                          <h5 class="card-title">Guests Details</h5>
                          <div class="row col-md-12"> 
                            <div class="col-md-5">
                                <div class="form-group" id="old_member_select">
                                  <label>Member</label>                                  
                                  <select name="member_id" id="member_id" class="form-control" disabled>
                                    <option value="">Select Member</option>
                                    <?php if(!empty($member_list)): ?>
                                    <?php   foreach($member_list as $mlist): ?>
                                              <option value="<?php echo $mlist['member_id']; ?>" <?php if($mlist['member_id'] == $reservation_list['member_id']): echo "selected";endif; ?>><?php echo $mlist['full_name']; ?></option>
                                    <?php   endforeach; ?>
                                    <?php endif; ?>
                                  </select>
                                </div>
                            </div>
                            <div class="row col-md-12" id="member_div" style="color:#fff;padding:0 0 0 12px;line-height: 35px;margin: 0 0 20px;background: #6269691f;">
                                <div id="old_member" class="row col-md-12">
                                    <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Email ID</label>
                                        <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="old_email" name="email" class="form-control" value="<?php echo $reservation_list['email'];?>" readonly>
                                        
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>Mobile</label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="old_mobile" name="mobile" value="<?php echo $reservation_list['member_mobile'];?>" class="form-control mobileNO" readonly>
                                        <span></span>                                        
                                      </div>
                                    </div>
                                </div>                                
                                <div id="new_member" class="row col-md-12" style="display:none">
                                  <div class="col-md-12 row" >
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>Mobile</label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_mobile" name="new_mobile" class="form-control mobileNO" value="<?php echo $reservation_list['member_mobile'];?>" readonly>
                                        <span></span>
                                        <?php echo form_error('Mobile', '<div class="error">', '</div>'); ?>
                                      </div>
                                    </div>                                                   
                                    <div class="col-md-6">
                                      <div class="form-group">
                                          <label>Email ID</label>
                                        <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_email" name="new_email" class="form-control" value="<?php echo $reservation_list['email'];?>" readonly>
                                        <?php echo form_error('Email', '<div class="error">', '</div>'); ?>
                                      </div>
                                    </div>                                                                        
                                  </div>
                                  <div class="col-md-12 row" >                                     
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>First Name </label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_first_name" class="form-control" name="new_first_name" value="<?php echo $reservation_list['first_name'];?>" readonly>
                                      </div>
                                    </div>                                   
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_last_name" class="form-control" name="new_last_name" value="<?php echo $reservation_list['last_name'];?>" readonly>
                                      </div>
                                    </div>
                                  </div>                                                  
                                </div>
                            </div>
                          </div>
                          <div class="row col-md-12">
                              <label>Message</label>
                              <textarea name="message" class="form-control" readonly><?php echo $reservation_list['message'];?></textarea>
                          </div>
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
  })
  
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
        //$("#new_first_name").attr('required',false);
        //$("#new_last_name").attr('required',false);
        //$("#new_email").attr('required',false);
        //$("#new_mobile").attr('required',false);       
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
        //$("#new_first_name").attr('required',true);
        //$("#new_last_name").attr('required',true);
        //$("#new_email").attr('required',true);
        //$("#new_mobile").attr('required',true);
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
        $("#new_mobile").val(response['mobile']);
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