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
                  <h4 class="card-title">Sub Administrator Details</h4>
                  <a class="title_btn t_btn_list" href="<?= base_url(); ?>admin/users"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> Sub Administrator List</a>
                </div>


                <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
              </div>
              <div class="card-body">
                <div class="px-3">

                  <?php
                  if (empty($users)) { ?>

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
                    <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/users/addusers" enctype="multipart/form-data">
                      	<div class="form-body">
	                        <div class="row">	                        	
		                      	<div class="col-md-4">
			                        <div class="form-group">
			                          <label>First Name <sup>*</sup></label>
			                          <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required=""  VALUE="<?php echo set_value('first_name');?>" name="first_name">
			                        </div>
		                      	</div>
		                        <div class="col-md-4">
		                            <div class="form-group">
		                              <label>Middle Name</label>
		                              <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control"  value="<?php echo set_value('middle_name');?>" name="middle_name">
		                            </div>
		                        </div>
	                          	<div class="col-md-4">
		                            <div class="form-group">
		                              <label>Last Name <sup>*</sup></label>
		                              <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required=""  value="<?php echo set_value('last_name');?>" name="last_name">
		                            </div>
	                          	</div>
                              <div class="col-sm-4">
                                <div class="form-group" style="margin-bottom: 0;">
                                  <label>Role<sup>*</sup></label>
                                  <div class="settlement_inline">
                                    <select id="benefit_id" class="js-select2" name="role_id" data-show-subtext="true" data-live-search="true" required>
                                      <option value="">Select Role</option>
                                      <?php if(!empty($role)): ?>
                                      <?php   foreach($role as $list): ?>
                                                <option value="<?php echo $list['role_id'];?>"><?php echo $list['role_name'];?></option>
                                      <?php   endforeach; ?>
                                      <?php endif; ?>
                                    </select>
                                  </div>
                                </div>
                              </div>                         
	                          	<div class="col-md-4">
	                            	<div class="form-group">
	                              		<label>Email ID</label>
	                          			  <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" name="email" class="form-control" value="<?php echo set_value('email');?>" >                                  
	                            	</div>
                                <?php echo form_error('email', '<div class="error">', '</div>'); ?>
	                          	</div>
	                          	<div class="col-md-4">
		                            <div class="form-group">
		                              <label>Mobile <sup>*</sup></label>
		                              <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" name="mobile" class="form-control mobileNO" value="<?php echo set_value('mobile');?>" required="">
		                              <span></span>                                  
		                            </div>
                                <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>
	                          	</div>                              
  		                        <div class="col-md-4">
                                <div class="form-group">
                                  <label>Password <sup>*</sup></label>
                                  <input type="password" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="password" name="password" value="<?php echo set_value('password');?>" class="form-control" required="">
                                </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label>Confirm Password <sup>*</sup></label>
                                  <input type="password" onkeypress="nospaces(this)" onkeyup="nospaces(this)" name="conf_password" value="<?php echo set_value('conf_password');?>" class="form-control conf_password" required="">
                                  <span></span>
                                </div>
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
	                        </div>
    						          <div class="form-actions">
                							<a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/users'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
                							<button type="submit" class="btn btn-success">
                							  <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
                							</button>
				                  </div>
                    </form>
                  <?php
                  } else {

                    ?>
                    <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/users/UpdateUsers/<?php echo $users['user_id']; ?>" enctype="multipart/form-data">
                        <div class="form-body">
                          <div class="row">                           
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>First Name <sup>*</sup></label>
                                <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required="" name="first_name" value="<?php echo $users['first_name']; ?>">
                              </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                  <label>Middle Name</label>
                                  <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" name="middle_name" value="<?php echo $users['middle_name']; ?>">
                                </div>
                            </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label>Last Name <sup>*</sup></label>
                                  <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" name="last_name" value="<?php echo $users['last_name']; ?>">
                                </div>
                              </div>
                              <div class="col-sm-4">
                                <div class="form-group" style="margin-bottom: 0;">
                                  <label>Role<sup>*</sup></label>
                                  <div class="settlement_inline">
                                    <select id="benefit_id" class="js-select2" name="role_id" data-show-subtext="true" data-live-search="true" required >
                                      <option value="">Select Role</option>
                                      <?php if(!empty($role)): ?>
                                      <?php   foreach($role as $list): ?>
                                                <option value="<?php echo $list['role_id'];?>" <?php if($list['role_id'] == $users['role_id']): echo 'selected';endif; ?>><?php echo $list['role_name'];?></option>
                                      <?php   endforeach; ?>
                                      <?php endif; ?>
                                    </select>
                                  </div>
                                </div>
                              </div>                         
                              <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email ID </label>
                                  <input type="email" onkeypress="nospaces(this)" onkeyup="nospaces(this)" name="email" class="form-control" value="<?php echo $users['email']; ?>">
                                 </div>
                                <?php echo form_error('email', '<div class="error">', '</div>'); ?>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label>Mobile <sup>*</sup></label>
                                  <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" name="mobile" class="form-control mobileNO" required="" value="<?php echo $users['mobile']; ?>">
                                  <span></span>
                                </div>
                                <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>
                              </div>                              
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label>Password <sup>*</sup></label>
                                  <input type="password" onkeypress="nospaces(this)" onkeyup="nospaces(this)" id="edit_password" name="password" class="form-control" required="" value="<?php echo $users['original_password']; ?>">
                                </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label>Confirm Password <sup>*</sup></label>
                                  <input type="password" onkeypress="nospaces(this)" id="edit_conf_password" onkeyup="nospaces(this)" name="conf_password" class="form-control conf_password" required="" value="<?php echo $users['original_password']; ?>">
                                  <span></span>
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="form-group pb-1" style="display:flex;align-items: center;">
                                    <label style="margin-right:7px;">Inactive</label>
                                    <label class="switch" for="checkbox">
                                        <?php if($users['status'] =='1'): 
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
                                <label>Profile Image<sup>* (accept file extention - .gif,.jpg,.png,.jpeg)</sup></label>
                                <div class="input-group mb-3">                              
                                  <div class="custom-file">
                                    <input type="file" accept=".gif,.jpg,.png,.jpeg" name="profile_img" class="custom-file-input" id="profile_img">
                                    <label class="custom-file-label" for="inputGroupFile01">Select Image</label>
                                    <input type="hidden" name="old_profile_img" value="<?php echo $users['profile_photo']?>" >
                                  </div>
                                </div>
                            </div>
                            <?php if(!empty($users['profile_photo'])): ?>
                              <div class="col-md-4" id="profile_img_div" style="margin-top: 27px;"><img id="profile_image" src="<?php echo base_url().'public/upload_image/profile_photo/'.$users['profile_photo']?>" alt="your image" width="100px" height="100px"/></div>   
                            <?php else: ?>
                              <div class="col-md-4" id="profile_img_div" style="margin-top: 27px;"><img id="profile_image" src="<?php echo base_url().'public/upload_image/No_Image_Available.jpg'; ?>" alt="your image" width="100px" height="100px"/></div>
                            <?php endif;?>
                          </div>
                        </div>
                        <div class="form-actions">
                         <input type="hidden" id="user_id" name="user_id" value="<?php echo  $user_id; ?>">
                          <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/users'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
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

function nospaces(t){
    if(t.value.match(/\s/g) && t.value.length == 1){
        alert('Sorry, you are not allowed to enter any spaces in the starting.');

        t.value=t.value.replace(/\s/g,'');
    }
}
$(document).on('keyup','.conf_password',function(){
    var passwd      = $("#password").val();
    var old_passwd  = $(this).val();
    //alert(passwd+"dsfhjsak"+old_passwd);
    if(passwd !='' && old_passwd !=''){
      if(passwd != old_passwd){
        $(this).next('span').html('Opps! confirm password is not matching');
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
$(document).on('keyup','#edit_conf_password',function(){
    var passwd            = $("#edit_password").val();
    var edit_old_passwd   = $(this).val();
    if(passwd !='' && edit_old_passwd !=''){
      if(passwd != edit_old_passwd){      
          $(this).next('span').html('Opps! confirm password is not matching');
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

</script>