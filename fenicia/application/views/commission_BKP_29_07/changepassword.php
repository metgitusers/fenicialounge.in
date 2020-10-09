<style type="">
.change_pasword_area
{
  max-width: 700px;
  width: 100%;
  margin: 0 auto;
}
</style>
        
<div class="container-fluid">
<!-- Basic form layout section start -->              
  <section id="basic-form-layouts">       
    <div class="row">
      <div class="col-md-6 offset-3">
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
        <div class="card">
          <div class="card-header">
            <div class="page-title-wrap" style=" text-align: center;">
              <h4 class="card-title text-center">Change Password</h4>
             <!--  <a class="title_btn t_btn_list" href="<?= base_url();?>commission/changepswd/"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> List</a> -->
            </div>
          </div>
          <div class="card-body">
            <div class="px-3">
              <form  id="myForm" class="form custom_form_style" method="Post" action="<?= base_url();?>commission/changepassword/changeuserpasswd">
                <div class="form-body">
                  <div id="myRadioGroup">
                     <div class="change_pasword_area">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="oldpassw">Old Password</label>
							                <input type="password" class="form-control" id="oldpassw" name="oldpassw" placeholder="old password" value="" required="required">
							                <?php echo form_error('oldpassw'); ?>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="newpassw">New Password</label>
			                        <input type="password" class="form-control" id="newpassw" name="newpassw" value="" placeholder="new password" required="required">
			                        <?php echo form_error('newpassw'); ?>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="confpassw">Confirm Password</label>
			                        <input type="password" class="form-control" id="confpassw" name="confpassw" placeholder="confirm password" value="" required="required">
			                        <?php echo form_error('confpassw'); ?>
                            </div>
                          </div>
                          <?php //$admin=$this->session->userdata('admin');?>
                          <?php $user_id=$this->session->userdata('user_data');?>	
            						  <div class="box-footer">
            						  	<div class="row">
                              <div class="col-sm-6">
                                <input type="hidden" name="user_id" value="<?php echo $user_id;?>">
                     <!--    <button type="submit" class="btn btn-sm btn-primary">Submit</button> -->
                                <button type="submit" class="btn btn-success" style="margin-left: 15px;">
                                  <i class="fa fa-floppy-o" aria-hidden="true"></i> Submit
                                </button>
                              </div>
                            </div>
            						  </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
      </div>
    </div><!-- /.row -->	
  </section><!-- /.content -->
</div>

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>
$("#myForm" ).validate({
          rules: {
             oldpassw: {
                required: true
              } ,
            newpassw: {
                required: true,
                minlength: 6
              } ,
            confpassw: {
              required: true,
              equalTo: "#newpassw"
              }                      
          },
          messages: {
            oldpassw:"Please enter your old password",
            newpassw: {
              required: "Password is required",
              minlength: "At least 6 characters required!"
                },
            confpassw: "Enter same password as confirm password"
          },
          errorElement: "em",
        
          highlight: function ( element, errorClass, validClass ) {
            $( element ).parents( ".form-control" ).addClass( "has-error" ).removeClass( "has-success" );
          },
          unhighlight: function (element, errorClass, validClass) {
            $( element ).parents( ".form-control" ).addClass( "has-success" ).removeClass( "has-error" );
          }
        });


</script>







