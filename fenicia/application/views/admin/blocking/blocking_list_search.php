<style>
  .error{
    color:red;
    font-size: 13px;
  }
</style>
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
                  <h4 class="card-title">Zone Blocking List</h4>                  
                </div>


                <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
              </div>
              <div class="card-body">
                <div class="px-3">
                  <div class="form-body" style="padding:10px 10px 0 10px;margin-bottom:10px">
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
                    <form id="reservation_filter_form" class="form custom_form_style">
                      	<div class="form-body">
                          <div class="user_permission_top">
  	                        <div class="row">	                        	
  		                      	<div class="col-md-2">
                                <div class="form-group">
                                  <label>From Date: <sup>*</sup></label>
                                  <div class="input-group" id="blocking_from_date_div">
                                    <input style="font-size:12px" type="text" placeholder="DD/MM/YY" required name="blocking_from_date" id="blocking_from_date" value="" class="blocking_date form-control pickadate" readonly="true"/>
                                    <div class="input-group-append">
                                      <span class="input-group-text">
                                        <span class="fa fa-calendar-o"></span>
                                      </span>
                                    </div>
                                  </div>
                                  <span class="error"></span>
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group">
                                  <label>To Date: <sup>*</sup></label>
                                  <div class="input-group" id="blocking_to_date_div">
                                    <input style="font-size:12px" type="text" placeholder="DD/MM/YY" required name="blocking_to_date" id="blocking_to_date" value="" class="blocking_date form-control pickadate" readonly="true"/>
                                    <div class="input-group-append">
                                      <span class="input-group-text">
                                        <span class="fa fa-calendar-o"></span>
                                      </span>
                                    </div>
                                  </div>
                                  <span class="error"></span>                                
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group"> 
                                  <label>Blocking Time: </label>                                         
                                  <div class="input-group time_pick" id="blocking_from_time_div">                                                                            
                                    <input class="form-control customize_inputdate timepicker" value="" id="blocking_from_time" name="blocking_from_time" placeholder ="select"required/>
                                    <div class="input-group-append">
                                      <span class="input-group-text">
                                        <span class="fa fa-clock-o"></span>
                                      </span>
                                    </div>
                                  </div>
                                  <span class="error"></span>
                                </div>
                              </div>
                              <div class="col-md-2">
                                <div class="form-group"> 
                                  <label>To Time: </label>                                         
                                  <div class="input-group time_pick" id="blocking_to_time_div">                                                                            
                                    <input class="form-control customize_inputdate timepicker" value="" id="blocking_to_time" name="blocking_to_time" placeholder ="select" required/>
                                    <div class="input-group-append">
                                      <span class="input-group-text">
                                        <span class="fa fa-clock-o"></span>
                                      </span>
                                    </div>
                                  </div>
                                  <span class="error"></span>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label>Zone </label>                                  
                                  <select id="club_zone_name" name="club_zone_name" class="form-control zoneId" required>
                                    <option value="">Select Zone</option>
                                    <?php if(!empty($zone_club_list)): ?>                                            
                                    <?php   foreach($zone_club_list as $list): ?>
                                              <option value="<?php echo $list['club_zone_name']; ?>" <?php if($list['club_zone_name'] == set_value('club_zone_name')): echo "selected";endif; ?>><?php echo $list['club_zone_name']; ?></option>
                                    <?php   endforeach; ?>
                                    <?php endif; ?>
                                  </select>
                                  <span class="error"></span>
                                </div>
                              </div>
                              <div class="" style="margin-top:26px">
                                <div class="form-group">
                                  <button type="button" style="" class="btn btn-success pull-left" id="search_btn">
                                    <i class="fa fa-search" aria-hidden="true"></i> Go
                                  </button>
                                </div>
                              </div>
                            </div>
                          </div>                   
              						<!--<div class="form-actions">
              							<a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/Reservation'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
              							<button type="submit" class="btn btn-success">
              							  <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
              							</button>
  						            </div>-->
                        </div>
                    </form>
                  </div>
                  <div id="list_view"></div>
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
  $('.timepicker').pickatime({
    interval: 90
  })
  
 $('#blocking_from_date').pickadate({
    format: 'dd/mm/yyyy',
    min: new Date()
  });
});
$(document).on('click','#search_btn',function(){
    var blocking_from_date  = $('#blocking_from_date').val();
    var blocking_to_date    = $('#blocking_to_date').val();
    var blocking_from_time  = $('#blocking_from_time').val();
    var blocking_to_time    = $('#blocking_to_time').val();
    var club_zone           = $('#club_zone_name').val();
    var ctn_error =0;
    if(blocking_from_date == ''){
        $("#blocking_from_date_div").next('span').html('Put blocking from date');
        ctn_error++;
    }
    else{
      $("#blocking_from_date_div").next('span').html(' ');
      
    }    
    if(blocking_to_date == ''){
        $("#blocking_to_date_div").next('span').html('Put blocking to date');
        ctn_error++;
    }
    else{
      $("#blocking_to_date_div").next('span').html(' ');
      
    }
    if(ctn_error == 0){
      $.ajax({
          type: "POST",
          url: '<?php echo base_url('admin/Zoneblocking/SearchBlockingList')?>', 
          data:{blocking_from_date : blocking_from_date,blocking_to_date : blocking_to_date,blocking_from_time : blocking_from_time,blocking_to_time : blocking_to_time,club_zone:club_zone},
          datatype:'html',
          success: function(response){ 
          //alert(response);          
            $('#list_view').html(response);
            var now = new Date();
            var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
            $('.reservation_blocking_list_table').DataTable({
              pageLength: 10,              
            });  
          },
          error:function(response){
            $.alert({
               type: 'red',
               title: 'Alert!',
               content: 'Error',
            });
          }
      });
    }
})
$(document).on('click','.do_block',function(){
  var current_obj         = $(this);
  var zone_name           = $(this).data('name');
  var blocking_from_date  = $('#blocking_from_date').val();  
  var blocking_from_time  = $('#blocking_from_time').val();  
  //var club_zone           = $('#club_zone_name').val();
  $.confirm({
    title: 'Confirm!',
    content: 'Are you sure want to change your status?',
    buttons: {            
        Okay: {
            btnClass: 'btn-green',
            action: function(){
              $.ajax({
                      type: "POST",
                      url: '<?php echo base_url('admin/reservation/doBlockZone')?>', 
                      data:{blocking_from_date : blocking_from_date,blocking_from_time : blocking_from_time,zone_name:zone_name},
                      datatype:'text',
                      success: function(response){ 
                          if(response =='1'){
                            $.alert({
                               type: 'green',
                               title: 'Alert!',
                               content: 'Successfully Blocked',
                            });
                            //
                            current_obj.parent("td").prev("td").html('Blocked');
                            current_obj.parent("td").html('');
                          }
                          else{
                            $.alert({
                               type: 'red',
                               title: 'Alert!',
                               content: 'Successfully Blocked',
                            });  
                          }  
                      },
                      error:function(response){
                        $.alert({
                           type: 'red',
                           title: 'Alert!',
                           content: 'Opp! some problem ,please try again',
                        });
                      }
                  });
            }
          },
            Close: {
              btnClass: 'btn-default',
              action: function(){
                  
              }
            }  
        }
    });
});

function nospaces(t){
    if(t.value.match(/\s/g) && t.value.length == 1){
        alert('Sorry, you are not allowed to enter any spaces in the starting.');

        t.value=t.value.replace(/\s/g,'');
    }
}

</script>