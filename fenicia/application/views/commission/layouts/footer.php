    <footer class="commission_footer footer footer-static footer-light">
    		<p class="clearfix text-muted text-center px-2"><span>Copyright  &copy; <?php echo date('Y'); ?>  <a href="https://www.fenicialounge.in/"  target="_blank" class="text-bold-800 primary darken-2">Club Fenicia </a>, All rights reserved. </span></p>
    </footer>
  </div>
</div>
<!--<aside id="notification-sidebar" class="notification-sidebar d-none d-sm-none d-md-block"><a class="notification-sidebar-close"><i class="ft-x font-medium-3"></i></a>
	<div class="side-nav notification-sidebar-content">
		<div class="row">
			<div class="col-12 mt-1">
				<ul class="nav nav-tabs">
					<li class="nav-item"><a id="base-tab1" data-toggle="tab" aria-controls="base-tab1" href="#activity-tab" aria-expanded="true" class="nav-link active"><strong>Activity</strong></a></li>
					<!--<li class="nav-item"><a id="base-tab2" data-toggle="tab" aria-controls="base-tab2" href="#settings-tab" aria-expanded="false" class="nav-link"><strong>Settings</strong></a></li>
				</ul>
				<div class="tab-content">
					<div id="activity-tab" role="tabpanel" aria-expanded="true" aria-labelledby="base-tab1" class="tab-pane active">
						<div id="activity-timeline" class="col-12 timeline-left">
							<h6 class="mt-1 mb-3 text-bold-400">RECENT ACTIVITY</h6>
							<div class="timeline">
								<ul class="list-unstyled base-timeline activity-timeline ml-0">
                  <?php if(!empty($this->Log_data)): ?>
                  <?php     foreach($this->Log_data as $list): ?>
              									<li>
              										<div class="timeline-icon bg-danger"><?php echo $list['icon']; ?></div>
              										<div class="base-timeline-info"><a href="<?php echo base_url().$list['log_link']; ?>" class="text-uppercase text-danger"><?php echo $list['log_title'];?></a><span class="d-block"><?php echo $list['log_description'];?></span></div>
              										<small class="text-muted"><?php echo date('l jS  F Y h:i:s A' ,strtotime($list['log_dt'])); ?></small>
              									</li>
                  <?php     endforeach; ?>
                  <?php     endif; ?>							
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</aside>-->
<input type="hidden" id="add_flag" value="<?php if(!empty($ck_action_falg) && $ck_action_falg['add_flag'] !=''): echo $ck_action_falg['add_flag'];endif;?>" >
<input type="hidden" id="edit_flag" value="<?php if(!empty($ck_action_falg) && $ck_action_falg['edit_flag'] !=''): echo $ck_action_falg['edit_flag'];endif;?>" >
<input type="hidden" id="delete_flag" value="<?php if(!empty($ck_action_falg) && $ck_action_falg['delete_flag'] !=''): echo $ck_action_falg['delete_flag'];endif;?>" >
<input type="hidden" id="download_flag" value="<?php if(!empty($ck_action_falg) && $ck_action_falg['download_flag'] !=''): echo $ck_action_falg['download_flag'];endif;?>" >
<!-- BEGIN VENDOR JS-->
<script src="<?=base_url('public/admin_assets/vendors/js/core/popper.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/perfect-scrollbar.jquery.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/prism.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/jquery.matchHeight-min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/screenfull.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/pace/pace.min.js')?>"></script>
<!-- BEGIN VENDOR JS-->
<script src="<?=base_url('public/admin_assets/vendors/js/jquery.steps.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/pickadate/picker.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/pickadate/picker.date.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/pickadate/picker.time.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/pickadate/legacy.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/jquery.validate.min.js')?>"></script>
<!-- BEGIN PAGE VENDOR JS-->

<!-- DATA TABLE -->
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/datatables.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/dataTables.buttons.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/buttons.flash.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/jszip.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/pdfmake.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/vfs_fonts.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/buttons.html5.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/datatable/buttons.print.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/js/data-tables/datatable-advanced.js')?>"></script>
<script src="<?=base_url('public/admin_assets/js/sweet-alerts.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/sweetalert2.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/js/select2.full.js')?>"></script>
<!-- END PAGE VENDOR JS-->
<!-- BEGIN CONVEX JS-->
<script src="<?=base_url('public/admin_assets/js/app-sidebar.js')?>"></script>
<script src="<?=base_url('public/admin_assets/js/notification-sidebar.js')?>"></script>
<!-- END CONVEX JS-->
<!-- BEGIN PAGE LEVEL JS-->
<script src="<?=base_url('public/admin_assets/js/wizard-steps.js')?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>



<script type="text/javascript">
$(document).ready(function() {
  $(".js-select2").select2();
});


$(document).on('click','#clear_btn',function(){
    location.reload();
});

</script>

