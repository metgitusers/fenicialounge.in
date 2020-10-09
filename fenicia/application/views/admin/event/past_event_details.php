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
                  <h4 class="card-title">Event Details</h4>
                  <a class="title_btn t_btn_list" href="<?= base_url(); ?>admin/past-events"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> Past Event List</a>
                </div>


                <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
              </div>
              <div class="card-body">
                <div class="px-3">

                  <?php
                  if (!empty($event_list)) { ?>
                        <div class="form-body">
                          <div class="row">                           
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>Event Name</label>
                                <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required="" name="event_name" value="<?php echo $event_list['event_name']; ?>" readonly>
                              </div>                                             
                            </div>
                            <div class="col-md-8">
                              <div class="form-group">
                                <label>Location</label>
                                <textarea type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required="" name="location" readonly><?php echo $event_list['event_location']; ?></textarea>
                              </div>
                            </div>
                            <div class="col-md-11">
                              <div class="form-group">
                                  <label>Event Description</label>
                                  <textarea type="text" onkeypress="nospaces(this)" rows="6px" onkeyup="nospaces(this)" class="form-control" required="" name="location" readonly><?php echo strip_tags($event_list['event_description']); ?></textarea>
                              </div>                            
                            </div>                   
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Event Start Date</label>
                                <div class="input-group">
                                  <input type="text" class="form-control" placeholder=""  value="<?php echo date('d/m/Y',strtotime($event_list['event_start_date'])); ?>" id="event_str_date" name="event_str_date" readonly required/>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label>Event Start Time</label>
                                <div class="input-group time_pick">
                                  <input class="form-control" value="<?php echo date('h:i A',strtotime($event_list['event_start_time'])); ?>" id="event_str_time" name="event_str_time" readonly required/>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Event End Date</label>
                                <div class="input-group">
                                  <input type="text" class="form-control" placeholder="" value="<?php echo date('d/m/Y',strtotime($event_list['event_end_date'])); ?>" id="event_end_date" name="event_end_date" readonly required/>
                                </div>
                              </div>
                            </div>
                            <div id="event_time_div" class="col-md-2">
                              <div class="form-group">
                                <label>Event End Time</label>
                                <div class="input-group time_pick">
                                  <input class="form-control" value="<?php echo date('h:i A',strtotime($event_list['event_end_time'])); ?>" id="event_end_time" name="event_end_time" readonly required/>
                                </div>
                              </div>
                            </div>                            
                          </div>
                          <div class="row col-md-4">                                
                              <div class="form-group">  
                                  <input type="checkbox" name="event_flag" class="form-control" id="event_flag" value="popular" <?php if($event_list['event_flag'] =='popular'): echo 'checked';endif; ?> disabled>Popular Event
                              </div>
                          </div>
                          <h4 class="form-section">Event Image:</h4>
                          <div class="row">
                            <div class="col-md-12" style="margin-top: 27px;">
                              <?php if($event_img_list): 
                                      foreach($event_img_list as $img_list): ?>
                                        <div style="float:left">
                                            <img src="<?php echo base_url().'public/upload_image/event_image/'.$img_list['event_img'];?>" width="100px" height="100px" style="margin:8px">
                                        </div>    
                              <?php   endforeach; ?>
                              <?php endif; ?>        
                            </div>                                                       
                          </div>                          
                          <div class="col-md-12">
                            <div class="form-group pb-1" style="display:flex;align-items: center;">
                                <label style="margin-right:7px;">Inactive</label>
                                <label class="switch" for="checkbox">
                                    <?php if($event_list['status'] =='1'): 
                                            $checked  = 'checked';
                                          else:
                                            $checked  = '';
                                          endif;
                                    ?>
                                    <input value="1" name="status" type="checkbox" id="checkbox" <?php echo $checked; ?> disabled/>
                                    <div class="slider round"></div>
                                </label>
                                <label style="margin-left:7px;">Active</label>
                            </div>
                          </div>
                        </div>
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
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
<script type="text/javascript">
CKEDITOR.replace('cms_description');
CKEDITOR.config.basicEntities = false;
 $("form").submit( function(e) {   
    var total_length    = CKEDITOR.instances['cms_description'].getData().replace(/<[^>]*>/gi, '').length;    
    if(!total_length) {
      //alert(total_length);        
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Please enter a description',
        });
        e.preventDefault();
    }
    else{
              
    }
});
$(document).ready(function() {  
  var end_time = $('#event_end_time').pickatime({
        format: 'h:i A',          // Displayed and application format
        formatSubmit: 'HH:i:00',
        //formatSubmit: 'h:i A',
        
        hiddenName: true        
    });
    var start_time = $('#event_str_time').pickatime({
       format: 'h:i A',          // Displayed and application format
       formatSubmit: 'HH:i:00', 
        //formatSubmit: 'h:i A',     
        onSet: function(context) {
            var finish_time_min = context.select + 30;
            var hours = Math.floor(finish_time_min / 60);
            var minutes = (finish_time_min - (hours * 60));
            //end_time.pickatime('picker').set('min', [hours, minutes]);
        }                   
    });  
  var event_start_date       = $('#event_str_date').pickadate({format:'dd/mm/yyyy',autoclose:true,min: new Date()}),
  event_start_date_picker    = event_start_date.pickadate('picker')

  var event_end_date       = $('#event_end_date').pickadate({format:'dd/mm/yyyy',autoclose:true}),
  event_end_date_picker    = event_end_date.pickadate('picker')

  // Check if there’s a “from” or “to” date to start with.
  if ( event_start_date_picker.get('value') ) {
    event_end_date_picker.set('min',event_start_date_picker.get('select'))
  }
  if ( event_end_date_picker.get('value') ) {
    //event_start_date_picker.set('max', event_end_date_picker.get('select'))
  }
  // When something is selected, update the “from” and “to” limits.
  event_start_date_picker.on('set', function(event) {

  /*if ( event.select ) {
    event_end_date_picker.set('min',event_start_date_picker.get('select'));    
  }
  else if ( 'clear' in event ) {
    event_end_date_picker.set('min', false);
  }*/
  })
});
$(document).on('change','.event_str_dt',function(){
    var event_str_dt  = $(this).val();
    if(event_str_dt !=""){
      $('.event_end_dt').val('');
    }
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

function readURL(input) {
  if (input.files) {
      var filesAmount = input.files.length;
      for (i = 0; i < filesAmount; i++) {
          var reader = new FileReader();

          reader.onload = function(event) {
              $($.parseHTML('<img width="100px" height="100px" style="margin:8px">')).attr('src', event.target.result).appendTo('#event_img_div');
          }

          reader.readAsDataURL(input.files[i]);
      }
  }
}
$("#event_img").change(function() {
  readURL(this);
});
$(document).on('click','.delete_pro_img',function(){
    if(confirm("Are you sure do you want to delete ?")){
       $(this).parent().parent().remove();
       var event_img_id = $(this).attr('id');
       //alert(package_img_id);
       $.ajax({
          type: "POST",
          url: '<?php echo base_url("admin/event/DeleteImage")?>',
          data:{event_img_id:event_img_id},
          dataType:'html',
          success: function(response){
            if(response ==1 ){
             $.alert({
                 type: 'green',
                 title: 'Alert!',
                 content: 'Successfully deleted.',
              });
              location.reload();                 
            }
            else{
              //nothing to do 
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
    }
    return false;
});
</script>