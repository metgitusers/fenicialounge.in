<section class="content-header">
  <h3>
    User Settings
  </h3>         
</section>

<!-- Main content -->
<section class="content"> 
	<div class="row">
		<!-- general form elements -->
		<div class="col-md-12">
		  	<div class="card">
				<div class="box-header with-border">
					<h3 class="box-title"></h3>
					<?php if($this->session->flashdata('success_msg')){?>
                      	<div class="alert alert-success">
                        	<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                        	<?php echo $this->session->flashdata('success_msg')?>
                      	</div>
                    <?php } else if($this->session->flashdata('error_msg')){?>
                        <div class="alert alert-error">
	                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
	                        <?php echo $this->session->flashdata('error_msg')?>
                      	</div>
                    <?php } ?>
				</div>
				<div class="card-body">
	                <div class="card-block">
				<!-- /.box-header -->
				<!-- form start -->
						<form role="form" class="mtbresize" method="post" action="<?php echo base_url('admin/user/update');?>" enctype="multipart/form-data" autocomplete="off">
						  <div class="box-body">													
							<div class="form-group">
							  <label for="first_name">First Name/User Name<sup class="superr">*</sup></label>
							  <input type="text" class="form-control" id="first_name" name="first_name"  value="<?php echo $user['first_name'];?>" placeholder="First Name" required="required">
							  <?php echo form_error('first_name','<span class="error">', '</span>'); ?>
							</div>
							<div class="form-group">
							  <label for="last_name">Last Name</label>
							  <input type="text" class="form-control" id="last_name" name="last_name"  value="<?php echo $user['last_name'];?>" placeholder="Last Name">
							  <?php echo form_error('last_name','<span class="error">', '</span>'); ?>
							</div>
							<div class="form-group">
								<label for="email">Email<sup class="superr">*</sup></label>
								<input type="email" class="form-control" id="email" name="email"  value="<?php echo $user['email'];?>" placeholder="Email" readonly>
								<?php echo form_error('email','<span class="error">', '</span>'); ?>
							</div>
						  </div>
						  <!-- /.box-body -->
						  <div class="box-footer">
							<input type="hidden" name="user_id" value="<?php echo $user['user_id'];?>">
							<button type="submit" class="btn btn-sm btn-primary">Submit</button>
							<a href="<?=base_url()?>admin/user" class="btn btn-sm btn-primary" style="margin-left: 50px;">Cancel</a>
						  </div>
						</form>
			  		</div>
				</div>
			</div>
  	<!-- /.box -->
  		</div>
    </div><!-- /.row -->	
</section><!-- /.content -->

