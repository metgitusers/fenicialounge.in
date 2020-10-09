<body class="loginsection">
<div class="wrapper">  
  <div class="login-form">   
    <form action="<?php echo base_url('admin/index/reset_newpswd');?>" method="post">
    <div style="margin-bottom:30px;text-align: center;"><h3>Reset Password</h3></div>
      <div class="form-group has-feedback">
         <input type="password" class="form-control" name="password" id="first" placeholder="New Password*" required> 
         <?php echo form_error('password', '<div class="error">', '</div>'); ?>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="confirm_password"  id="second" placeholder="Confirm Password*" required> 
        <span id="error1" class="hidden">Please Enter Same Value</span>
          <?php echo form_error('confirm_password', '<div class="error">', '</div>'); ?>
    
      </div>
      <div class="row">
        <div class="col-xs-4">
          <input type="hidden" name="code" value="<?php echo end($this->uri->segments); ?>">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
  </div>
</div>


<!-- /.login-box -->