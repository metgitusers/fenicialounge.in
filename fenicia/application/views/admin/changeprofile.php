        <div class="main-content">
          <div class="content-wrapper">
            <div class="container-fluid">
              <!-- Basic form layout section start -->
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
        <section id="basic-form-layouts">
          <!--<div class="row">
            <div class="col-sm-12">
              <h2 class="content-header"> Master</h2>
            </div>
          </div>-->
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                  <div class="page-title-wrap">
                    <h4 class="card-title">Profile Settings</h4>
                   <!--  <a class="title_btn t_btn_list" href="<?= base_url();?>admin/changeprofile/"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> List</a> -->
                  </div>
                  <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
                </div>
                <div class="card-body">
                  <div class="px-3">
                    <?php
                      if(!empty($user))
                      {
                    ?>
                  <form  id="myprofileForm" class="form custom_form_style" enctype="multipart/form-data" method="Post" action="<?= base_url();?>admin/changeprofile/update_profile">
                      <div class="form-body">
                        <!--<h4 class="form-section">
                          <i class="icon-user"></i> Personal Details</h4>-->

                        <div id="myRadioGroup">
                                <!--2 Cars<input type="radio" name="cars" checked="checked" value="2"  />
                                3 Cars<input type="radio" name="cars" value="3" />-->
                             <div id="Cars2" class="desc">
                                  <div class="row">
                         <div class="col-md-4">
                            <div class="form-group">
                              <label>First name<sup>*</sup></label>
                              <input type="text" class="form-control" onkeyup="nospaces(this)" onkeypress="nospaces(this)" pattern="[A-Za-z]+" value="<?= $user['first_name'];?>" name="first_name" required="required" >
                                <?php echo form_error('first_name','<span class="error">', '</span>'); ?>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Middle name</label>
                              <input type="text" class="form-control" onkeyup="nospaces(this)" onkeypress="nospaces(this)" pattern="[A-Za-z]+" value="<?= $user['middle_name'];?>"  name="middle_name">
                            </div>
                          </div>
                          
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Last name<sup>*</sup></label>
                              <input type="text" class="form-control" onkeyup="nospaces(this)" onkeypress="nospaces(this)" pattern="[A-Za-z]+" value="<?= $user['last_name'];?>"  name="last_name" required="required">
                                <?php echo form_error('last_name','<span class="error">', '</span>'); ?>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>DOB<sup>*</sup></label>
                              <div class="input-group">
                              
                                          <input type="text" class="form-control pickadate" id="dob" name="dob" value="<?= $user['dob'];?>"  placeholder="" required="required" />
                                            <?php echo form_error('dob','<span class="error">', '</span>'); ?>
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
                              <label>ABN<sup>*</sup></label>
                              <input type="number" min="1" onKeyPress="if(this.value.length==11) return false;" class="form-control" name="abn" value="<?= $user['abn'];?>"  required="required">
                                <?php echo form_error('abn','<span class="error">', '</span>'); ?>
                            </div>
                          </div>
                          
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Landline No.</label>
                              <input type="text" name="landline_no" onkeyup="nospaces(this)" onkeypress="nospaces(this)" class="form-control landlineNO" value="<?= $user['landline_no'];?>" >
                              <span class="error"></span>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>Mobile Number<sup>*</sup></label>
                              <input type="text" name="mobile" onkeyup="nospaces(this)" onkeypress="nospaces(this)" class="form-control mobileNO" value="<?= $user['mobile'];?>"  required="required">
                                <?php echo form_error('mobile','<span class="error">', '</span>'); ?>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>Email<sup>*</sup></label>
                              <input type="email" name="email" onkeyup="nospaces(this)" onkeypress="nospaces(this)" class="form-control" value="<?= $user['email'];?>"   readonly>
                            </div>
                          </div>
                          	<div class="col-md-3">
	                          <div class="form-group">
	                            <label>Profile Photo<sup>*</sup></label>
	                            <input type="file" class="" accept=".gif,.jpg,.png,.jpeg" name="profile_photo" id="profile_photo" required>
	                            <input type="hidden" name="old_profile_photo" value="<?= $user['profile_photo'];?>" ?>
	                          </div>	                          
	                        </div>
	                        <?php if(!empty($user['profile_photo'])): ?>
	                        	<div class="col-md-3" id="profile_photo_div"><img id="profile_img" src="<?php echo base_url()?>./public/upload_image/profile_photo/<?php echo $user['profile_photo'];?>" alt="your image" width="100px" height="100px"/></div>
                        	<?php endif; ?>
	                        <div class="col-md-3" id="profile_photo_div" style="display:none"><img id="profile_img" src="" alt="your image" width="100px" height="100px"/></div>                        
                         </div>
                        
                        <h4 class="form-section">Address Details:</h4>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>UNIT NO. / FLAT NO.</label>
                              <input type="text" class="form-control" onkeyup="nospaces(this)" onkeypress="nospaces(this)" name="flat_no" value="<?= $user['flat_no'];?>" required="required">
                                <?php echo form_error('flat_no','<span class="error">', '</span>'); ?>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Street No.<sup>*</sup></label>
                              <input type="text" class="form-control" onkeyup="nospaces(this)" onkeypress="nospaces(this)" name="street_no" value="<?= $user['street_no'];?>" required="required">
                                <?php echo form_error('street_no','<span class="error">', '</span>'); ?>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Street Name<sup>*</sup></label>
                              <input type="text" class="form-control" onkeyup="nospaces(this)" onkeypress="nospaces(this)" name="street_name" value="<?= $user['street_name'];?>" required="required">
                                <?php echo form_error('street_name','<span class="error">', '</span>'); ?>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Suburb<sup>*</sup></label>
                              <input type="text" class="form-control" onkeyup="nospaces(this)" onkeypress="nospaces(this)" name="suburb" value="<?= $user['suburb'];?>" required="required">
                                <?php echo form_error('subrub','<span class="error">', '</span>'); ?>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>State<sup>*</sup></label>
                              <input type="text" class="form-control" onkeyup="nospaces(this)" onkeypress="nospaces(this)" name="state"  value="<?= $user['state'];?>" required="required">
                                <?php echo form_error('state','<span class="error">', '</span>'); ?>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Post Code<sup>*</sup></label>
                              <input type="text"  pattern="[0-9]+" maxlength="4" onkeyup="nospaces(this)" onkeypress="nospaces(this)" class="form-control"  value="<?= $user['pin'];?>" name="pin" required="required">
                                <?php echo form_error('pin','<span class="error">', '</span>'); ?>
                            </div>
                          </div>
                        </div>
                        <input type="hidden" name="user_id" value="<?= $user['user_id'];?>">
                        <div class="form-actions">
                        <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/dashboard'; ?>">
                          <i class="fa fa-times" aria-hidden="true"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                          <i class="fa fa-floppy-o" aria-hidden="true"></i> Update
                        </button>
                      </div>
                      </div>
                    </div>
                  </div>
                </form>
                <?php
                  
                  }else{

                    echo "No data found";
                  }

                ?>

                  </div>
                </div>
              </div>
            </div>
         </div>
        </section>
     </div>
  </div>
</div>

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

<script>
$( "#myprofileForm" ).validate({
          rules: {
            first_name: "required",
            last_name: "required",
            dob: "required",
            abn: "required",
            mobile: {
              required: true,
              digits: true,
              minlength: 10
              },
            flat_no: "required",
            street_no: "required",
            street_name: "required",
            suburb: "required",
            state: "required",
            pin: "required",
           
                                       
          },
          messages: {
            first_name: "First Name is required",
            last_name: "Last Name is required",
            dob: "DOB is required",
            abn: "ABN is required",
            // email: {
            //     required: "We need your email address to contact you",
            //     email: "Your email address must be in the format of name@domain.com"
            //       },
            mobile: {
            required: "mobile no required",
            digits: "Enter valid mobile no",
            minlength: "valid mobile no required!"
                },  
           
            flat_no: "ABN is required",
            street_no: "street no is required",
            street_name: "street name is required",
            suburb: "suburb is required",
            state: "state is required",
            pin: "pin is required",
          },
          errorElement: "em",
         
          highlight: function ( element, errorClass, validClass ) {
            $( element ).parents( ".form-control" ).addClass( "has-error" ).removeClass( "has-success" );
          },
          unhighlight: function (element, errorClass, validClass) {
            $( element ).parents( ".form-control" ).addClass( "has-success" ).removeClass( "has-error" );
          }
        });
    $(document).ready(function(){
    
	    var  licence_exp_min_date = new Date();
	    licence_exp_min_date.setDate(licence_exp_min_date.getDate() + 1);
	    
	    var dob_max_date = new Date();
	    dob_max_date.setFullYear(dob_max_date.getFullYear() - 18);
	    $('#dob').pickadate({
	        format: 'dd/mm/yyyy',
				  max: dob_max_date,
				  selectYears: true,
				  selectMonths: true,
	        selectYears: 80
	    });
  		function validateNumber(mobnumber) {
		    var filter = /^(\d{3})(\d{3})(\d{4})$/;
		    if (filter.test(mobnumber)) {
		      return true;
		    } else {
		      return false;
		    }
		}

		function validatelandlineNumber(number) {
		    var filter = /^(\d{5})(\d{4})(\d{4})$/;
		    if (filter.test(number)) {
		      return true;
		    } else {
		      return false;
		    }
		}
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
  	})
  	function readURL(input) {
	  if (input.files && input.files[0]) {
	    var reader = new FileReader();
	    
	    reader.onload = function(e) {
	      $('#profile_img').attr('src', e.target.result);
	    }
	    
	    reader.readAsDataURL(input.files[0]);    
	    $("#profile_photo_div").show();
	  }
	}

	$("#profile_photo").change(function() {
	  readURL(this);
	});    
</script>