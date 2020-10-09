

<style>
  .login-box{
    background: #313131;
    padding: 30px 30px 30px 30px;
    -webkit-box-shadow: 0px 6px 20px 0px rgba(0,0,0,0.4);
    -moz-box-shadow: 0px 6px 20px 0px rgba(0,0,0,0.4);
    box-shadow: 0px 6px 20px 0px rgba(0,0,0,0.4);
    width: 474px;
    margin: 0 auto 0 auto;
    max-width: 100%;
    webkit-box-shadow: 0px 0px 5px 0px rgb(255, 255, 67);
    -moz-box-shadow: 0px 0px 20px 0px rgba(207,247,46,1);
    box-shadow: 0px 0px 5px 0px rgb(255, 255, 67);
  }

  .login-box .login-logo {
    display: block;
    margin: 0 auto;
    width: 80px;
}

 .login-box h3 {
    font-size: 24px;
    margin-top: 20px;
    margin-bottom: 10px;
    font-weight: 500;
    color: #efefef;
    text-transform: capitalize;
    font-family: 'Poppins', sans-serif;
}
.login-box .form-group{
  margin-bottom: 15px;
}
.login-box label{

  color: #a9a9a9;
    font-size: 16px;
    display: inline-block;
    font-family: 'Poppins', sans-serif;
    margin-bottom:10px;
}
.login-box .form-control{
	width: 100%;
	height: 48px;
	padding: 6px 12px;
	font-size: 14px;
	line-height: 1.42857143;
	color: #ffff43;
	border: 1px solid #ffff43;
	background: #191818;
	border-radius: 0;
	top: 1px;
	position: relative;
	-webkit-box-shadow: inset 0 1px 1px rgba(255,255,255,.075);
	box-shadow: inset 0 1px 1px rgba(255,255,255,.075);
	-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
	-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
	transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
	transition-duration: 1s;
	-webkit-transition-duration: 1s;
}

.login-box textarea.form-control{
	height: auto;
}

.login-box .form-control:focus{
    border-color: #ec4b4e;
    outline: 0;
    -webkit-box-shadow: inset 0 1px 1px rgba(236,75,78,.075), 0 0 8px rgba(236,75,78,.6);
    box-shadow: inset 0 1px 1px rgba(236,75,78,.075), 0 0 8px rgba(236,75,78,.6);
	transition-duration: 0.5s;
    -webkit-transition-duration: 0.5s;
	
}

.login-box .btn-green{
	 background: #ffff43;
	 color: #232323;
	 -webkit-border-radius: 3px;
	 -moz-border-radius: 3px;
	 border-radius: 3px;
	 font-family: 'Poppins', sans-serif;
	 padding: 14px 45px;
	 font-size: 14px;
	 font-weight: 400;
	 transition-duration: 1s;
	 -webkit-transition-duration: 1s;
	 margin-top: 15px;
   border:#ffff43 1px solid;
}
.login-box .btn-green:hover, .login-box .btn-green:focus {
	background: #28a745;
	color: #fff;
  border:#28a745 1px solid;
	transition-duration: 1s;
	-webkit-transition-duration: 1s;
}
.center {
  display: block;
  margin-left: auto;
  margin-right: auto;
  width: 50%;
}
</style>

<!-- <script src="http://localhost/dad/php//public/js/jquery.min.js"></script>  -->
<script src="<?php echo base_url('public/front_assets/js/jquery-3.2.1.min.js')?>"></script>

<div class="login-box">
  <div class="center">
  <img src="<?=base_url('public/images/logo.png');?>" style="width:200px;">
    <!-- <a href="<?php //echo base_url('admin');?>"><b>Admin</b>LTE</a> -->
	 <!--<a href="../../index2.html"><b>Admin</b>LTE</a>-->
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">
	<?php if($this->session->flashdata('error_msg') && $this->session->flashdata('error_msg')!=''){ ?>
			<span style='color:red'><?php echo $this->session->flashdata('error_msg');?></span>
		<?php }else{?>
			<h3>Reset Password</h3>
		<?php }?>
	</p>
    <form action="#" method="post" id="new_form">
      <div class="form-group has-feedback">
        <label for="title">New password</label>
        <input type="password" class="form-control" placeholder="New password" name="newpassword1" id="newpassword1" required>
      </div>
      <div class="form-group has-feedback">
        <label for="title">Confirm password</label>
        <input type="password" class="form-control" placeholder="New password" name="newpassword2" id="newpassword2" required>
      </div>
      <?php 
        $url=$this->uri->segment_array();
       //print_r($p);
        $record_num = end($url);
      ?>
      <input type="hidden" name="recovery_key" id="recovery_key" value="<?php echo $key;?>">
      <div class="row">
        <div class="col-xs-8">
          <!--<div class="checkbox icheck">
             <label>
              <input type="checkbox"> Remember Me
            </label>
          </div> -->
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat" id="submit">Submit</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
    
    <!--<div class="social-auth-links text-center">
      <p>- OR -</p>
      <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
        Facebook</a>
      <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
        Google+</a>
    </div>-->
    <!-- /.social-auth-links -->

    <!-- <a href="#">I forgot my password</a><br> -->
    <!-- <a href="register.html" class="text-center">Register a new membership</a> -->

  </div>
  <!-- /.login-box-body -->
</div>
    
<!-- /.login-box -->
<script> 
  $(document).prop('title', 'Account recovery');
  $(document).on('click','#submit',function(event){
   event.preventDefault(); 
   var newpassword1=$('#newpassword1').val();
   var newpassword2=$('#newpassword2').val();
   var recovery_key=$('#recovery_key').val();
   if(newpassword1 == '' || newpassword1==0){
    alert('New Password field is required');
    return false;
   }
   if(newpassword2 == '' || newpassword2==0){
    alert('Confirm Password field is required');
    return false;
   }   
   if(newpassword1!=newpassword2){
    alert('Passwords are not matched!');
    return false;
   }
   else{
      $.ajax({
        type: "POST",
        url: "<?php echo base_url('recoverPasswordUser/recoverAccount');?>",
        data: {newpassword1 :newpassword1,newpassword2: newpassword2,recovery_key:recovery_key},
        dataType:'json',
        success: function( response ) {
          //alert(response);
          if(response.status){
            alert(response.message);
            //window.location.href="<?=base_url('admin')?>";
          }
          else{
            alert(response.message);
          }
        }
      });
   }     
  });
</script>