<body class="loginsection">
<div class="wrapper">
  <div class="login-form">
    <!-- <div class="head"> <img alt="" src="<?php echo base_url()?>/public/images/user2-160x160.jpg"> </div> -->
    <form class="form-horizontal" action="<?php echo base_url('commission/Login/submit_login_form');?>" method="post">
      <div class="card-header text-center">
        <img src="<?php echo base_url();?>public/images/logo.png" alt="company-logo" class="mb-3" width="80">
        <h4 class="text-uppercase text-bold-400 grey darken-1">Login</h4>
      </div>
      <div class="box-body1">
        <?php if($this->session->flashdata('msg')){?>
           <span style="color:red; font-weight:bold"><?php echo $this->session->flashdata('msg');?></span> 
        <?php }?>
        <div class="form-group">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <input type="email" class="form-control" placeholder="E-mail Id" name="email" id="email">
            <a class=" icon user" href="#"></a> <?php echo form_error('email','<span class="error" style="color:red; font-weight:bold">', '</span>'); ?></div>
        </div>
        <div class="form-group">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <input type="password" class="form-control" id="inputPassword3" placeholder="Password" name="password" id="password">
            <a class=" icon lock" href="#"></a><?php echo form_error('password','<span class="error" style="color:red; font-weight:bold">', '</span>'); ?> </div>
        </div>       
      </div>
      <!-- /.box-body -->
      <div class="text-center">
        <!--<a type="button" href="<?=base_url('admin/forgotpassword')?>" class="btn btn-default">Forgot Password?</a>-->
        <button type="submit" class="btn btn-info">Sign in</button>
      </div>
      <!-- /.box-footer -->
    </form>
  </div>
</div>


<!-- /.login-box -->