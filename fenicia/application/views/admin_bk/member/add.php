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
                  <h4 class="card-title">Users Details</h4>
                  <a class="title_btn t_btn_list" href="<?= base_url(); ?>admin/member"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> Users List</a>
                </div>
                <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
              </div>
              <div class="card-body">
                <div class="px-3">
                <?php
                  if (empty($member)) { ?>

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
                    <form id="add_form" class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/member/addMember" enctype="multipart/form-data">
                      	<div class="form-body">
	                        <div class="row">
	                        <div class="col-md-12">
	                        <div class="row">
	                        	<div class="col-md-2">
		                            <div class="form-group">
		                              <label>Title <sup>*</sup></label>
		                              <select name="title" class="form-control" required>
		                              	<option value="MR.">MR</option>
		                              	<option value="Mrs.">Mrs</option>
		                              	<option value="Miss">Miss</option>
		                              </select>
		                            </div>
		                            </div>
		                            </div>
	                     		  </div>
		                      	<div class="col-md-4">
			                        <div class="form-group">
			                          <label>First Name <sup>*</sup></label>
			                          <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required="" value="<?php echo set_value('first_name');?>" name="first_name">
			                        </div>
		                      	</div>
                          	<div class="col-md-4">
	                            <div class="form-group">
	                              <label>Last Name <sup>*</sup></label>
	                              <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required="" value="<?php echo set_value('last_name');?>" name="last_name">
	                            </div>
                          	</div> 
                            <div class="col-md-12" > 
                                <div class="row">                                                      
    	                          	<div class="col-md-6">
    	                            	<div class="form-group">
    	                              		<label>Email ID <sup>*</sup></label>
                	                      <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" name="email" class="form-control" value="<?php echo set_value('email');?>" required="">
                                    </div>
                                    <?php echo form_error('email', '<div class="error">', '</div>'); ?>
  	                            	</div>
    	                          	<div class="col-md-6">
    		                            <div class="form-group">
    		                              <label>Mobile <sup>*</sup></label>
    		                              <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" name="mobile" class="form-control mobileNO" value="<?php echo set_value('mobile');?>" required="">
    		                              <span></span>
                                    </div>
                                    <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>
  	                          	  </div>
                                </div>
								              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label>Club Mebership Id </label>
                                  <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="new_membership_id" placeholder="eg: 1club03022020"name="membership_id" class="form-control" value="<?php echo set_value('membership_id');?>">
                                  <span style="color:red"></span>                                      
                                </div>
                              </div>
                              <div class="form-group col-sm-4" style="margin-bottom: 0;">
                                <label>Membership Package</label>
                                <div class="settlement_inline">
                                  <select id="new_package_id" class="js-select2" name="package_id" data-show-subtext="true" data-live-search="true">
                                    <option value="">Select Package</option>
                                    <?php if(!empty($package)): ?>
                                    <?php   foreach($package as $plist): ?>
                                              <option value="<?php echo $plist['package_id'];?>"><?php echo $plist['package_name'];?></option>
                                    <?php   endforeach; ?>
                                    <?php endif; ?>
                                  </select>
                                </div>
                              </div>
                              <?php //echo $member_package['package_price_id'];exit; ?>
                              <?php if(!empty($package_type)): ?>
                                  <div id="new_package_type" class="col-sm-3">
                                    <label>Package Type</label><br>
                                    <div id="">
                                    <?php foreach($package_type as $ptypelist): ?>
                                        <div class="name"><input type="radio" class="" name="package_type_id" value="<?php echo $ptypelist['package_price_mapping_id']; ?>"><?php echo $ptypelist['package_type_name'].':'.$ptypelist['price']; ?></div>
                                    <?php endforeach; ?>
                                    </div>
                                    <span></span> 
                                  </div>
                              <?php endif; ?>                                  
                              <div id="new_package_type_div" class="col-sm-4" style="display:none;">
                                <label>Package Type</label><br>
                                <div id="new_packg_type_div"></div>
                                <span></span> 
                              </div>    
                            </div>
                              <div class="row">     
                                  <div class="col-md-6">                            
    		                            <div class="form-group gnder">
    								                  <label>Gender<sup>*</sup></label>
    			                            <input type="radio" name="gender" id="mem_gender" value="male" <?php if(set_value('gender') =='male'){ echo 'checked'; } ?> required/>
                  										<label class="" for="gender">Male</label>
                  										<input type="radio" name="gender" id="mem_gender" value="female" <?php if(set_value('gender') =='female'){ echo 'checked'; } ?> required/>
                  										<label class="" for="gender">Female</label>
    			                       	  </div>
    			                       	  <?php echo form_error('gender', '<div class="error">', '</div>'); ?>
    	                          	</div>
      			                      <div class="col-md-6">                            
      			                        <div class="form-group marriagestatus">
      								                  <label>Marital status</label>
      			                            		<input type="radio" name="marriage_status" id="mrg_status" value="married" <?php if(set_value('marriage_status') =='married'){ echo 'checked'; } ?> />
                    										<label class="" for="marriage_status">Married</label>
                    										<input type="radio" name="marriage_status" id="mrg_status" value="single" <?php if(set_value('marriage_status') =='single'){ echo 'checked'; } ?>/>
                    										<label class="" for="marriage_status">Single</label>
    			                          </div>
    			                        </div>
  								              </div>
                              <div class="row">                               
      		                        <div class="col-md-6">
      		                            <div class="form-group">
      		                              <label>Date of birth <sup>*</sup></label>
      		                              <div class="input-group">
      		                                <input type="text" id="dob" name="dob" class="form-control pickadate" value="<?php echo set_value('dob');?>" placeholder="" required readonly/>
      		                                <div class="input-group-append">
      		                                  <span class="input-group-text">
      		                                    <span class="fa fa-calendar-o"></span>
      		                                  </span>
      		                                </div>
      		                              </div>
      		                            </div>
                                      <?php echo form_error('dob', '<div class="error">', '</div>'); ?>
      	                         	</div>
                                  <div class="col-md-6" id="doc_dt">
                                    <div class="form-group">
                                      <label>Date of anniversary</label>
                                      <div class="input-group">
                                        <input type="text" id="doc" name="doa" class="form-control pickadate" value="<?php echo set_value('doa');?>" placeholder="" required/>
                                        <div class="input-group-append">
                                          <span class="input-group-text">
                                            <span class="fa fa-calendar-o"></span>
                                          </span>
                                        </div>
                                      </div>
                                    </div>
                                    <?php echo form_error('doa', '<div class="error">', '</div>'); ?>
                                  </div>
						                  </div>
	                        </div>
                        	<h4 class="form-section">Profile Image:</h4>
                          <div class="row">
                          	<div class="col-md-6">
                								<label>Profile Image<sup> (accept file extention - .gif,.jpg,.png,.jpeg)</sup></label>
                								<div class="input-group mb-3">															
                									<div class="custom-file">
                										<input type="file" accept=".gif,.jpg,.png,.jpeg" name="profile_img" class="custom-file-input" id="profile_img">
                                    <label class="custom-file-label" for="inputGroupFile01">Select Image</label>
                									</div>
                								</div>
            							  </div>
            							  <div class="col-md-4" id="profile_img_div" style="margin-top: 27px; display:none"><img id="profile_image" src="" alt="your image" width="100px" height="100px"/></div>   
                          </div>
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
            						<div class="col-md-12 form-actions">
            							<div class="col-md-12">
            								<a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/member'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
            							<button type="submit" id="add_btn" class="btn btn-success">
            							  <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
            							</button>
            							</div>
						            </div>
                    </form>
                  <?php
                  } else {

                    ?>
                      <form id="edit_form" class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/member/UpdateMember/<?php echo $member['member_id']; ?>" enctype="multipart/form-data">
                          <div class="form-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                      <label>Title <sup>*</sup></label>                                  
                                      <select name="title" class="form-control" required>
                                        <option value="MR."  <?php  if($member['title'] == 'MR.') :?>selected<?php endif;?>>MR</option>
                                        <option value="Mrs." <?php  if($member['title'] == 'Mrs.') :?>selected<?php endif;?>>Mrs</option>
                                        <option value="Miss." <?php  if($member['title'] == 'Miss.') :?>selected<?php endif;?>>Miss</option>
                                      </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                  <div class="form-group">
                                    <label>First Name <sup>*</sup></label>
                                    <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required name="first_name" value="<?php echo $member['first_name']?>" >
                                  </div>
                                  <?php echo form_error('first_name', '<div class="error">', '</div>'); ?>                               
                                </div>                              
                                <div class="col-md-5">
                                  <div class="form-group">
                                    <label>Last Name <sup>*</sup></label>
                                    <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required name="last_name" value="<?php echo $member['last_name'];?>">
                                 </div>
                                  <?php echo form_error('last_name', '<div class="error">', '</div>'); ?>                      
                                </div>
                                <div class="row col-sm-12">
                                  <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email ID <sup>*</sup></label>
                                      <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" name="email" class="form-control" required value="<?php echo $member['email'];?>" readonly>
                                    </div>
                                    <?php echo form_error('email', '<div class="error">', '</div>'); ?>
                                  </div>                                  
                                  <div class="col-md-5">
                                    <div class="form-group">
                                      <label>Mobile <sup>*</sup></label>
                                      <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" name="mobile" class="form-control mobileNO" required value="<?php echo $member['mobile'];?>" readonly>
                                      <span></span>
                                    </div>
                                    <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="form-group">
                                      <label>Club Mebership Id </label>
                                      <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="membership_id" placeholder="eg: 1club03022020"name="membership_id" class="form-control" value="<?php echo $member_package['membership_id']; ?>">
                                      <span style="color:red"></span>                                      
                                    </div>
                                  </div>
                                  <div class="form-group col-sm-4" style="margin-bottom: 0;">
                                    <label>Membership Package</label>
                                    <div class="settlement_inline">
                                      <select id="package_id" class="js-select2" name="package_id" data-show-subtext="true" data-live-search="true">
                                        <option value="">Select Package</option>
                                        <?php if(!empty($package)): ?>
                                        <?php   foreach($package as $plist): ?>
                                                  <option value="<?php echo $plist['package_id'];?>" <?php if(!empty($member_package['package_id']) && $plist['status'] !='0' && $member_package['package_id'] == $plist['package_id']): echo "selected";endif;?>><?php echo $plist['package_name'];?></option>
                                        <?php   endforeach; ?>
                                        <?php endif; ?>
                                      </select>
                                    </div>
                                    <input type="hidden" id="old_membership_package_id" value="<?php echo $member_package['package_id']; ?>">
                                  </div>
                                  <?php //echo $member_package['package_price_id'];exit; ?>
                                  <?php if(!empty($package_type)): ?>
                                      <div id="package_type" class="col-sm-3">
                                        <label>Package Type</label><br>
                                        <div id="">
                                        <?php foreach($package_type as $ptypelist): ?>
                                            <div class="name"><input type="radio" class="" name="package_type_id" value="<?php echo $ptypelist['package_price_mapping_id']; ?>" <?php if(!empty($member_package['package_price_id']) && $member_package['package_price_id'] == $ptypelist['package_price_mapping_id']): echo "checked";endif; ?> ><?php echo $ptypelist['package_type_name'].':'.$ptypelist['price']; ?></div>
                                        <?php endforeach; ?>
                                        </div>
                                        <span></span> 
                                      </div>
                                  <?php endif; ?>                                  
                                  <div id="package_type_div" class="col-sm-4" style="display:none;">
                                    <label>Package Type</label><br>
                                    <div id="packg_type_div"></div>
                                    <span></span> 
                                  </div>                               
                                </div>
                                <div class="col-md-6">                            
                                  <div class="form-group">
                                    <label>Gender<sup>*</sup></label>
                                    <input type="radio" name="gender" id="mem_gender" value="male" <?php  if($member['gender']=='male') :?>checked<?php endif?> required/>
                                    <label class="" for="gender">Male</label>
                                    <input type="radio" name="gender" id="mem_gender" value="female" <?php  if($member['gender']=='female') :?>checked<?php endif?> required/>
                                    <label class="" for="gender">Female</label>
                                  </div>
                                </div>
                                <div class="col-md-6">                            
                                  <div class="form-group">
                                      <label>Marital status</label>
                                      <input type="radio" name="marriage_status" id="mrg_status_edit" value="married" <?php  if($member['marriage_status']=='married') :?>checked<?php endif?>/>
                                      <label class="" for="marriage_status">Married</label>
                                      <input type="radio" name="marriage_status" id="mrg_status_edit" value="single" <?php  if($member['marriage_status']=='single') :?>checked<?php endif?> />
                                      <label class="" for="marriage_status">Single</label>
                                  </div>
                                </div> 
                                <div class="col-md-12 row" >                               
                                    <div class="col-md-6">
                                        <div class="form-group">
                                          <label>DOB <sup>*</sup></label>
                                          <div class="input-group">
                                            <input type="text" id="dob" name="dob" class="form-control pickadate" placeholder="" required="" value="<?php echo date('d/m/Y',strtotime($member['dob']));?>" readonly/>
                                            <div class="input-group-append">
                                              <span class="input-group-text">
                                                <span class="fa fa-calendar-o"></span>
                                              </span>
                                            </div>
                                          </div>                                          
                                        </div>
                                        <?php echo form_error('dob', '<div class="error">', '</div>'); ?>
                                    </div>
                                    <div class="col-md-6" id="doc_dt_edit">
                                      <div class="form-group">
                                        <label>DOA </label>
                                        <div class="input-group">
                                          <input type="text" id="doc_edit" name="doa" class="form-control pickadate" placeholder="dd/mm/yyyy" required="" value="<?php if($member['doa']!='0000-00-00'): echo date('d/m/Y',strtotime($member['doa']));endif;?>" />
                                          <div class="input-group-append">
                                            <span class="input-group-text">
                                              <span class="fa fa-calendar-o"></span>
                                            </span>
                                          </div>
                                        </div>
                                      </div>
                                      <?php echo form_error('doa', '<div class="error">', '</div>'); ?>
                                      <span id="doa_edit_error"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                  <div class="form-group pb-1" style="display:flex;align-items: center;">
                                      <label style="margin-right:7px;">Inactive</label>
                                      <label class="switch" for="checkbox">
                                          <?php if($member['status'] =='1'): 
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
                            <h4 class="form-section">Profile Image:</h4>
                            <div class="row">
                              <div class="col-md-6">
                                  <label>Profile Image<sup>(accept file extention - .gif,.jpg,.png,.jpeg)</sup></label>
                                  <div class="input-group mb-3">                              
                                    <div class="custom-file">
                                      <input type="file" accept=".gif,.jpg,.png,.jpeg" name="profile_img" class="custom-file-input" id="profile_img">
                                      <label class="custom-file-label" for="inputGroupFile01">Select Image</label>
                                      <input type="hidden" name="old_profile_img" value="<?php echo $member['profile_img']?>" >
                                    </div>
                                  </div>
                              </div>                               
                              <?php if(!empty($member['profile_img'])): ?>
                                <div class="col-md-4" id="profile_img_div" style="margin-top: 27px;"><img id="profile_image" src="<?php echo base_url().'public/upload_image/profile_photo/'.$member['profile_img']?>" alt="your image" width="100px" height="100px"/></div>   
                              <?php else: ?>
                                <div class="col-md-4" id="profile_img_div" style="margin-top: 27px;"><img id="profile_image" src="<?php echo base_url().'public/upload_image/No_Image_Available.jpg'; ?>" alt="your image" width="100px" height="100px"/></div>
                              <?php endif;?>
                            </div>
                          </div>
                          <div class="form-actions">
                            <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/member'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
                            <button id="update_btn" type="submit" class="btn btn-success">
                              <i class="fa fa-floppy-o"  aria-hidden="true"></i> Save
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
  var old_membership_package_id = $("#old_membership_package_id").val();
  if(old_membership_package_id !=""){
      $('#package_id option[value=""]').attr("disabled", "disabled");
  }
  $(".js-select2").select2();
  var dob_max_date = new Date();
  var doc_max_date = new Date();
  dob_max_date.setFullYear(dob_max_date.getFullYear() - 18);

  $('#dob').pickadate({
    format: 'dd/mm/yyyy',
    max: dob_max_date,
    min: "01/01/1899",
    selectYears: true,
    selectMonths: true,
    selectYears: 180
  });
  $('#doc').pickadate({
    format: 'dd/mm/yyyy',
    max: doc_max_date,
    min: "01/01/1899",
    selectYears: true,
    selectMonths: true,
    selectYears: 180
  });
  $('#doc_edit').pickadate({
    format: 'dd/mm/yyyy',
    max: doc_max_date,
    min: "01/01/1899",
    selectYears: true,
    selectMonths: true,
    selectYears: 180
  });
});

/*$(document).on('change','#mrg_status_edit',function(){
    //alert($(this).val());
    if($(this).val() == 'married'){
      $("#doc_dt_edit").show();
      $("#doc_edit").attr('required',true);
    }
    else if($(this).val() == 'single'){
      $("#doc_dt_edit").hide();
      $("#doc_edit").attr('required',false);
    }
});*/
function nospaces(t){
    if(t.value.match(/\s/g) && t.value.length == 1){
        alert('Sorry, you are not allowed to enter any spaces in the starting.');

        t.value=t.value.replace(/\s/g,'');
    }
}

$(document).on('keyup','.mobileNO',function(){
    var mobile_no = $(this).val();    
    if(mobile_no !=""){
      if(!validateNumber(mobile_no)){
        $(this).next('span').html('Please enter a valid mobile no.');
        $(this).next('span').css({'color':'red','font-size':'12px'});
      }
      else{
        $(this).next('span').html('');
      }
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
  var ext = $('#profile_img').val().split('.').pop().toLowerCase();
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
$(document).on('change','#new_package_id',function(){
  var package_id  = $(this).val();
  $.ajax({
      type: "POST",
      url: '<?php echo base_url('admin/member/ajaxGetPackageType')?>', 
      data:{package_id : package_id},
      datatype:'json',
      success: function(response){
       //alert(response);       
       $("#new_package_type").hide();
          $("#new_package_type_div").show();
         // $("#package_type").hide();
          var json = $.parseJSON(response); 
          $('#new_packg_type_div').html(" ");     
          for (var i=0;i<json.length;++i)
          {
            //alert(json[i].package_type_name+"%%"+json[i].price);
            $('#new_packg_type_div').append('<div class="name"><input class="package_type_id" type="radio" name="package_type_id" value="'+json[i].package_price_mapping_id+'">'+json[i].package_type_name+':'+json[i].price+'</div>');
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

$(document).on('change','#package_id',function(){
  var package_id  = $(this).val();
  $.ajax({
      type: "POST",
      url: '<?php echo base_url('admin/member/ajaxGetPackageType')?>', 
      data:{package_id : package_id},
      datatype:'json',
      success: function(response){
       //alert(response);       
       $("#package_type").hide();
          $("#package_type_div").show();
         // $("#package_type").hide();
          var json = $.parseJSON(response); 
          $('#packg_type_div').html(" ");     
          for (var i=0;i<json.length;++i)
          {
            //alert(json[i].package_type_name+"%%"+json[i].price);
            $('#packg_type_div').append('<div class="name"><input class="package_type_id" type="radio" name="package_type_id" value="'+json[i].package_price_mapping_id+'">'+json[i].package_type_name+':'+json[i].price+'</div>');
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
$(document).on('click','#add_btn',function(event){
    event.preventDefault();
    var  membership_id_string       = /^[0-9a-zA-Z]+$/;
    var  package_id                 = $("#new_package_id").val();
    var  mrg_status                 = $("input[name='marriage_status']:checked"). val();
    var  doc_edit                   = $("#doc_edit").val();
    var  membership_id              = $("#new_membership_id").val();
   //alert(membership_id);
    var  old_membership_package_id  = $("#old_membership_package_id").val();
    var  package_type_id            = $(".package_type_id").is(":checked"); 
    
    var  cnt  = 0;
    if(package_id !=""){
      //alert("345");
      if(membership_id !=""){
        //alert("%^%^345");
        if(membership_id.match(membership_id_string))
        {
          //alert("xcfdsf");
          $('#membership_id').next('span').html(' ');
          $.ajax({
              type: "POST",
              url: '<?php echo base_url('admin/member/uniqueMembershipId')?>', 
              data:{membership_id : membership_id},
              async:false,
              datatype:'html',
              success: function(response){                
                if(response !='0'){    
                  //alert(response);
                  $('#new_membership_id').next('span').html('Sorry! This membership id is already exist.Give a unique membership id.');
                  $('#new_membership_id').focus();
                  
                }
                else{
                  $('#new_membership_id').next('span').html(' ');
                  
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
        else{
          $('#new_membership_id').next('span').html('Please put unique alphanumeric club membership id');
          $('#new_membership_id').focus();
          cnt++; 
        }       
      }
      else{
        $('#new_membership_id').next('span').html('Please put unique alphanumeric club membership id');
        $('#new_membership_id').focus();
        cnt++; 
      }   
      if(package_type_id == false){
       
          $('#package_type_div').children('span').html('Please select a package type');
          $('#package_type_div').children('span').css({'color':'red','font-size':'12px'});
          cnt++;  
      }
      else{
          $('#package_type_div').children('span').html('');
          
      }
    }
    else if(membership_id !=""){
      if(package_id ==""){
        //alert("d65656kj");
        $('#new_memb_package').next('span').html('Please select a package type');
        $('#new_memb_package').next('span').css({'color':'red','font-size':'12px'});
        cnt++; 
      }
      else{
        $('#new_memb_package').next('span').html('');
      }   
    }   
    else if(mrg_status =='married' && doc_edit ==''){

      $("#doa_edit_error").show();
    }   
    if(cnt == '0'){
      $("#add_form").submit();
    }
    else{
      return false;
    }
}); 
$(document).on('click','#update_btn',function(event){
    event.preventDefault();
    var  membership_id_string       = /^[0-9a-zA-Z]+$/;
    var  package_id                 = $("#package_id").val();
    var  mrg_status                 = $("input[name='marriage_status']:checked"). val();
    var  doc_edit                   = $("#doc_edit").val();
    var  membership_id              = $("#membership_id").val();
    //alert(membership_id);
    var  old_membership_package_id  = $("#old_membership_package_id").val();
    var  package_type_id            = $(".package_type_id").is(":checked"); 
    var  cnt  = 0;
    if(package_id !="" && package_id != old_membership_package_id){
      if(membership_id !=""){
        if(membership_id.match(membership_id_string))
        {
          $('#membership_id').next('span').html(' ');
          $.ajax({
              type: "POST",
              url: '<?php echo base_url('admin/member/editUniqueMembershipId')?>', 
              data:{membership_id : membership_id,member_id:'<?php echo $member_id; ?>'},
              async:false,
              datatype:'html',
              success: function(response){ 
                //alert(response);
                if(response !='0'){    
                  
                  $('#membership_id').next('span').html('Sorry! This membership id is already exist.Give a unique membership id.');
                  $('#membership_id').focus();
                  cnt++;
                }
                else{
                  $('#membership_id').next('span').html(' ');
                  
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
        else{
          $('#membership_id').next('span').html('Please put unique alphanumeric club membership id');
          $('#membership_id').focus();
          cnt++; 
        }       
      }
      else{
        $('#membership_id').next('span').html('Please put unique alphanumeric club membership id');
        $('#membership_id').focus();
        cnt++; 
      }   
      if(package_type_id == false){
       
          $('#package_type_div').children('span').html('Please select a package type');
          $('#package_type_div').children('span').css({'color':'red','font-size':'12px'});
          cnt++;  
      }
      else{
          $('#package_type_div').children('span').html('');
          
      }
    }
    else if(package_id !="" && package_id == old_membership_package_id){
      if(membership_id !=""){
        if(membership_id.match(membership_id_string))
        {
          $('#membership_id').next('span').html(' ');
          $.ajax({
              type: "POST",
              url: '<?php echo base_url('admin/member/editUniqueMembershipId')?>', 
              data:{membership_id : membership_id,member_id:'<?php echo $member_id; ?>'},
              datatype:'html',
              async:false,
              success: function(response){
                //alert(response);
                if(response !='0'){
                  //alert(response);                   
                  cnt++;                 
                  $('#membership_id').next('span').html('Sorry! This membership id is already exist.Give a unique membership id.');
                  $('#membership_id').focus();
                  
                }
                else{
                  $('#membership_id').next('span').html(' ');                  
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
        else{
          $('#membership_id').next('span').html('Please put unique alphanumeric club membership id');
          $('#membership_id').focus();
          cnt++; 
        }       
      }
      else{
        $('#membership_id').next('span').html('Please put unique alphanumeric club membership id');
        $('#membership_id').focus();
        cnt++; 
      } 
    }
    else if(membership_id !="" && package_id ==""){
        if(package_id ==""){
          $('#memb_package').next('span').html('Please select a package type');
          $('#memb_package').next('span').css({'color':'red','font-size':'12px'});
          cnt++; 
        }
        else{
          $('#memb_package').next('span').html('');
        }
    }
    else if(mrg_status =='married' && doc_edit ==''){

      $("#doa_edit_error").show();
    } 
    //alert(cnt);
    if(cnt == '0'){

      $("#edit_form").submit();
    }
    else{
      return false;
    }
});   
</script>