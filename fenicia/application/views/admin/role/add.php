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
                  <h4 class="card-title">Role Details</h4>
                  <a class="title_btn t_btn_list" href="<?= base_url(); ?>admin/role"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> Role List</a>
                </div>


                <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
              </div>
              <div class="card-body">
                <div class="px-3">

                  <?php
                  if (empty($role)) { ?>

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
                    <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/role/addRole">
                      	<div class="form-body">
	                        <div class="row">	                        	
		                      	<div class="col-md-3">
			                        <div class="form-group">
			                          <label>Role Name <sup>*</sup></label>
			                          <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required=""  name="role_name">
			                        </div>
                              <?php echo form_error('role_name', '<div class="error">', '</div>'); ?>   
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

            						<div class="form-actions">
            							<a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/role'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
            							<button type="submit" class="btn btn-success">
            							  <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
            							</button>
						            </div>
                    </form>
                  <?php
                  } else {

                    ?>
                      <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/role/updateRole/<?php echo $role['role_id'];?>">
                        <div class="form-body">
                          <div class="row">                           
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>Role Name <sup>*</sup></label>
                                <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required="" value="<?php echo $role['role_name'];?>" name="role_name">
                              </div>
                              <?php echo form_error('role_name', '<div class="error">', '</div>'); ?>   
                            </div>                            
                            <div class="col-md-12">
                                <div class="form-group pb-1" style="display:flex;align-items: center;">
                                    <label style="margin-right:7px;">Inactive</label>
                                    <label class="switch" for="checkbox">
                                        <?php if($role['status'] =='1'): 
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
                          <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/role'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
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
$(document).on('change','#mrg_status',function(){
    if($(this).val() == 'married'){
      $("#doc_dt").show();
    }
    else if($(this).val() == 'single'){
      $("#doc_dt").hide();
    }
});
$(document).on('change','#mrg_status_edit',function(){
    //alert($(this).val());
    if($(this).val() == 'married'){
      $("#doc_dt_edit").show();
    }
    else if($(this).val() == 'single'){
      $("#doc_dt_edit").hide();
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
</script>