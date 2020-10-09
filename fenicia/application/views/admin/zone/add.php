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
                  <h4 class="card-title">Zone Details</h4>
                  <a class="title_btn t_btn_list" href="<?= base_url(); ?>admin/zone"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> Zone List</a>
                </div>


                <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
              </div>
              <div class="card-body">
                <div class="px-3">

                  <?php
                  if (empty($zone)) { ?>

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
                    <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/zone/addZone" enctype="multipart/form-data">
                      	<div class="form-body">
	                        <div class="row">	                        	
		                      	<div class="col-md-3">
			                        <div class="form-group">
			                          <label>Zone Name <sup>*</sup></label>
			                          <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required=""  name="zone_name">
			                        </div>
                              <?php echo form_error('zone_name', '<div class="error">', '</div>'); ?>   
		                      	</div>
                            <!--<div class="col-md-3">
                              <div class="form-group">
                                <label>Club Zone Name <sup>*</sup></label>
                                <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required=""  name="club_zone_name">
                              </div>
                              <?php echo form_error('club_zone_name', '<div class="error">', '</div>'); ?>   
                            </div>-->
                            <div class="col-md-11">
                              <div class="form-group">
                                  <label>Zone Description<sup>*</sup></label>
                                  <textarea  id="cms_description" name="zone_description" required="required" rows="10" cols="80"><?php echo set_value('zone_description');?></textarea>
                              </div>
                              <?php echo form_error('zone_description', '<div class="error">', '</div>'); ?>
                            </div>
                            <!--<div class="col-md-4">
                              <div class="form-group">
                              <label>Advanced Price</label>
                                <div class="input-group">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text">&#8377</span>
                                  </div>                                  
                                  <input type="number" min='0' class="form-control" name="minimum_price">
                                </div>
                              </div>
                              <?php echo form_error('minimum_price', '<div class="error">', '</div>'); ?>
                            </div>-->
                            <div class="col-md-5">
                              <div class="form-group">
                              <label>Cover Price</label>
                                <div class="input-group">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text">&#8377</span>
                                  </div>                                  
                                  <input type="number" min='0' class="form-control" name="cover_price">
                                </div>
                              </div>
                              <?php echo form_error('cover_price', '<div class="error">', '</div>'); ?>
                            </div>
                            <!--<div class="col-md-5">
                              <div class="form-group">
                              <label>Additional Price</label>
                                <div class="input-group">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text">&#8377</span>
                                  </div>                                  
                                  <input type="number" min='0' class="form-control" name="additional_price">
                                </div>
                              </div>
                              <?php echo form_error('additional_price', '<div class="error">', '</div>'); ?>
                            </div>-->
                            <div class="col-md-4">
                              <div class="form-group">
                                  <label>Minimum Capacity</label>
                                  <input type="number" min='1' onblur="capacity_cking(this)" class="min_capacity form-control" name="minimum_capacity">
                              </div>
                              <?php echo form_error('minimum_capacity', '<div class="error">', '</div>'); ?>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                  <label>Maximum Capacity</label>
                                  <input type="number" min='1' onblur="capacity_cking(this)" class="max_capacity form-control" name="maximum_capacity">
                              </div>
                              <?php echo form_error('maximum_capacity', '<div class="error">', '</div>'); ?>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                  <label>Zone Type</label><br>
                                  <input style="width: 30px;height: 30px;display: inline-block;" type="checkbox" class="form-control" name="zone_type" value="party"><span style="padding-left:50px;font-size:25px"> Party</span>
                              </div>
                              <?php echo form_error('zone_type', '<div class="error">', '</div>'); ?>
                            </div>                                             
                        </div>
                        <h4 class="form-section">Zone Image:</h4>
                          <div class="row">
                            <div class="col-md-6">
                                <label>Zone Image<sup> (accept file extention - .gif,.jpg,.png,.jpeg)</sup></label>
                                <div class="input-group mb-3">                              
                                  <div class="custom-file">
                                    <input type="file" accept=".gif,.jpg,.png,.jpeg" name="zone_img" class="custom-file-input" id="zone_img">
                                    <label class="custom-file-label" for="inputGroupFile01">Select Image</label>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-4" id="zone_img_div" style="margin-top: 27px; display:none"><img id="zone_image" src="" alt="your image" width="100px" height="100px"/></div>
                            <div class="col-md-12">
                                <div class="form-group pb-1" style="display:flex;align-items: center;">
                                    <label style="margin-right:7px;">Inactive</label>
                                    <label class="switch" for="checkbox">
                                        <input value="1" name="status" type="checkbox" id="checkbox" checked/>
                                        <div class="slider round"></div>
                                    </label>
                                    <label style="margin-left:7px;">Active</label>
                                </div>
                            </div>   
                          </div>
            						<div class="form-actions">
            							<a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/zone'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
            							<button type="submit" class="btn btn-success">
            							  <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
            							</button>
						            </div>
                    </form>
                  <?php
                  } else {

                    ?>
                      <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/zone/updateZone/<?php echo $zone['zone_id'];?>" enctype="multipart/form-data">
                        <div class="form-body">
                          <div class="row">                           
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>Zone Name <sup>*</sup></label>
                                <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required=""  value="<?php echo $zone['zone_name'];?>" name="zone_name">
                              </div>
                              <?php echo form_error('zone_name', '<div class="error">', '</div>'); ?>   
                            </div>
                            <!--<div class="col-md-3">
                              <div class="form-group">
                                <label>Club Zone Name <sup>*</sup></label>
                                <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required=""  value="<?php echo $zone['club_zone_name'];?>" name="club_zone_name">
                              </div>
                              <?php echo form_error('club_zone_name', '<div class="error">', '</div>'); ?>   
                            </div>-->
                            <div class="col-md-11">
                              <div class="form-group">
                                  <label>Zone Description<sup>*</sup></label>
                                  <textarea  id="cms_description" name="zone_description" required="required" rows="10" cols="80"><?php echo $zone['zone_description'];?></textarea>
                              </div>
                              <?php echo form_error('zone_description', '<div class="error">', '</div>'); ?>
                            </div>
                            <!--<div class="col-md-4">
                              <div class="form-group">
                                <label>Advanced Price<sup>*</sup></label>
                                <div class="input-group">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text">&#8377</span>
                                  </div>
                                  <input type="number" min='0' class="form-control" value="<?php echo $zone['advance_charges'];?>" name="minimum_price">
                                </div>
                              </div>
                              <?php echo form_error('minimum_price', '<div class="error">', '</div>'); ?>
                            </div>-->
                            <div class="col-md-5">
                              <div class="form-group">
                                <label>Cover Price</label>
                                <div class="input-group">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text">&#8377</span>
                                  </div>  
                                  <input type="number" min='0' class="form-control" value="<?php echo $zone['cover_charges'];?>" name="cover_price">
                                </div>
                              </div>
                              <?php echo form_error('cover_price', '<div class="error">', '</div>'); ?>
                            </div> 
                            <?php if($zone['zone_id']==5||$zone['zone_id']==6||$zone['zone_id']==8 ||$zone['zone_id']==9){     ?>                      
                            <div class="col-md-5">
                              <div class="form-group">
                              <label>Additional Price</label>
                                <div class="input-group">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text">&#8377</span>
                                  </div>                                  
                                  <input type="number" min='0' class="form-control" value="<?php echo $zone['additional_charges'];?>" name="additional_price">
                                </div>
                              </div>
                              <?php echo form_error('additional_price', '<div class="error">', '</div>'); ?>
                            </div>
                            <?php } ?>
                            <div class="col-md-4">
                              <div class="form-group">
                                  <label>Minimum Capacity</label>
                                  <input type="number" min='0' onblur="capacity_cking(this)" class="min_capacity form-control" value="<?php echo $zone['minimum_capacity'];?>" name="minimum_capacity">
                              </div>
                              <?php echo form_error('minimum_capacity', '<div class="error">', '</div>'); ?>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                  <label>Maximum Capacity</label>
                                  <input type="number" min='0' onblur="capacity_cking(this)" class="max_capacity form-control" value="<?php echo $zone['maximum_capacity'];?>" name="maximum_capacity">
                              </div>
                              <?php echo form_error('maximum_capacity', '<div class="error">', '</div>'); ?>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                  <label>Zone Type</label><br>
                                  <input style="width: 30px;height: 30px;display: inline-block;" type="checkbox" class="form-control" name="zone_type" value="party" <?php if($zone['zone_type'] =='party'){ echo 'checked';}?>><span style="padding-left:5px;font-size:25px"> Party</span>
                              </div>
                              <?php echo form_error('zone_type', '<div class="error">', '</div>'); ?>
                            </div>                                                        
                        </div>
                        <h4 class="form-section">Zone Image:</h4>
                          <div class="row">
                              <div class="col-md-6">
                                  <label>Zone Image<sup>(accept file extention - .gif,.jpg,.png,.jpeg)</sup></label>
                                  <div class="input-group mb-3">                              
                                    <div class="custom-file">
                                      <input type="file" accept=".gif,.jpg,.png,.jpeg" name="zone_img" class="custom-file-input" id="zone_img">
                                      <label class="custom-file-label" for="inputGroupFile01">Select Image</label>
                                      <input type="hidden" name="old_zone_img" value="<?php echo $zone['zone_image']?>" >
                                    </div>
                                  </div>
                              </div>                               
                              <?php if(!empty($zone['zone_image'])): ?>
                                <div class="col-md-4" id="zone_img_div" style="margin-top: 27px;"><img id="zone_image" src="<?php echo base_url().'public/upload_image/zone_image/'.$zone['zone_image']?>" alt="your image" width="100px" height="100px"/></div>   
                              <?php else: ?>
                                <div class="col-md-4" id="zone_img_div" style="margin-top: 27px;"><img id="zone_image" src="<?php echo base_url().'public/upload_image/No_Image_Available.jpg'; ?>" alt="your image" width="100px" height="100px"/></div>
                              <?php endif;?>
                              <div class="col-md-12">
                                <div class="form-group pb-1" style="display:flex;align-items: center;">
                                    <label style="margin-right:7px;">Inactive</label>
                                    <label class="switch" for="checkbox">
                                        <?php if($zone['status'] =='1'): 
                                                $checked  = 'checked';
                                              else:
                                                $checked  = '';
                                              endif;
                                        ?>
                                        <input value="1" name="status" type="checkbox" id="checkbox" <?php echo $checked; ?>/>
                                        <div class="slider round"></div>
                                    </label>
                                    <label style="margin-left:7px;">Active</label>
                                </div>
                            </div>
                          </div>
                        <div class="form-actions">
                          <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/Zone'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
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
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
<script type="text/javascript">
CKEDITOR.replace('cms_description');
CKEDITOR.config.basicEntities = false;
$("form").submit( function(e) {   
    var total_length    = CKEDITOR.instances['cms_description'].getData().replace(/<[^>]*>/gi, '').length;
    if(!total_length) {
      //alert(data_val);
        //$(".error").html('Please enter a description' );
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Please enter a zone description',
        });
        e.preventDefault();
    }
    else{
              
    }
});
$(document).ready(function() {
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
});
function capacity_cking(){
  var min_capacity = $('.min_capacity').val();
  var max_capacity = $('.max_capacity').val();
  //alert(min_capacity+"%%%"+max_capacity);
  if(min_capacity !='' && max_capacity !=''){
    if(parseInt(min_capacity) > parseInt(max_capacity)){
      //alert(min_capacity+"%%%"+max_capacity);
      $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Max capacity should not be less than min capacity.',
      });
    }
  }
}

function readURL(input) {
  if (input.files && input.files[0]) {    
    var reader = new FileReader();    
    reader.onload = function(e) {
      $('#zone_image').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]);    
    $("#zone_img_div").show();
  }
}

$("#zone_img").change(function() {
  var ext = $('#zone_img').val().split('.').pop().toLowerCase();
  if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
      alert('Accept file extention - .gif,.jpg,.png,.jpeg. Please upload vaild file');
  }
  else{
    readURL(this);
  }  
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
</script>