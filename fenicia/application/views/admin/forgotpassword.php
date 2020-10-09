<body class="loginsection">
<div class="wrapper">  
  <div class="login-form">
    <!-- <div class="head"> <img alt="" src="<?php echo base_url()?>/public/images/user2-160x160.jpg"> </div> -->    
    <form class="form-horizontal" action="<?php echo base_url('admin/index/forget_password');?>" method="post">
      <div style="margin-bottom:30px;text-align: center;">
      <img src="<?php echo base_url();?>public/images/logo.png" alt="company-logo" class="mb-3" width="80">
      <h3>Forgot Password</h3></div>
      <div class="box-body1">
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


        <div class="form-group">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <input type="email" class="form-control" placeholder="Email" name="email" id="email">
            <a class=" icon user" href="#"></a> <?php echo form_error('email','<span class="error">', '</span>'); ?></div>
        </div>        
      </div>
      <!-- /.box-body -->
      <div class="text-center">        
        <button type="submit" class="btn btn-info">Submit</button>
      </div>
      <!-- /.box-footer -->
    </form>
  </div>
</div>


<!-- /.login-box -->