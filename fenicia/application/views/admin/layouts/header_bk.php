<!DOCTYPE html>
<html lang="en" class="loading">
<?php 	$active_class 		='';
		$has_submenu_class	='';
?>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<title>Club Fenicia</title>
	<link rel="shortcut icon" type="image/png" href="<?= base_url('public/images/logo.png') ?>">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900%7CMontserrat:300,400,500,600,700,800,900" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?= base_url('public/admin_assets/fonts/feather/style.min.css') ?>">
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&display=swap" rel="stylesheet"> 
	<link rel="stylesheet" type="text/css" href="<?= base_url('public/admin_assets/fonts/simple-line-icons/style.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url('public/admin_assets/fonts/font-awesome/css/font-awesome.min.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url('public/admin_assets/vendors/css/perfect-scrollbar.min.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url('public/admin_assets/vendors/css/prism.min.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url('public/admin_assets/vendors/css/pickadate/pickadate.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url('public/admin_assets/vendors/css/tables/datatable/datatables.min.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url('public/admin_assets/css/app.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url('public/admin_assets/css/custom.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url('public/admin_assets/css/bootstrap-select.min.css') ?>">
	<link href='<?php echo base_url()?>public/admin_assets/css/fullcalendar.min.css' rel='stylesheet' />
	<link href='<?php echo base_url()?>public/admin_assets/css/fullcalendar.print.min.css' rel='stylesheet' media='print' />		
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" /> 
</head>
<script src='<?php echo base_url()?>public/admin_assets/js/moment.min.js'></script>
<script src="<?=base_url('public/admin_assets/vendors/js/core/jquery-3.2.1.min.js')?>"></script>
<script src="<?=base_url('public/admin_assets/vendors/js/core/bootstrap.min.js')?>"></script>
<link rel="stylesheet" type="text/css" href="<?= base_url('public/admin_assets/vendors/css/switchery.min.css') ?>">
<script src="<?php echo base_url().'public/admin_assets/vendors/js/switchery.min.js';?>"></script>
<script src="<?php echo base_url().'public/admin_assets/js/switch.min.js';?>"></script>
<style type="text/css">
    .switch {
  display: inline-block;
  height: 34px;
  position: relative;
  width: 60px;
}

.switch input {
  display:none;
}

.slider {
  background-color: #ccc;
  bottom: 0;
  cursor: pointer;
  left: 0;
  position: absolute;
  right: 0;
  top: 0;
  transition: .4s;
}

.slider:before {
  background-color: #fff;
  bottom: 4px;
  content: "";
  height: 26px;
  left: 4px;
  position: absolute;
  transition: .4s;
  width: 26px;
}

input:checked + .slider {
  background-color: #66bb6a;
}

input:checked + .slider:before {
  transform: translateX(26px);
}

.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

</style>
<?php 
//pr($this->notification_data);
//$leftmenu  		= get_menu_tree(0); 
//PR($leftmenu);
//exit;
$url_link 				= current_url();
$tmp_url_link			= explode('/', $url_link);
$last_string_url_link	= end($tmp_url_link);
?><body data-col="2-columns" class=" 2-columns ">
	<div class="wrapper">	
		<div data-active-color="white" data-background-color="crystal-clear" data-image="" class="app-sidebar">
			<div class="sidebar-header">
				<div class="logo clearfix"><a href="<?= base_url() ?>" class="logo-text float-left">
						<div class="logo-img"><img src="<?= base_url('public/images/logo.png') ?>" alt="Convex Logo" /></div><span class="align-middle" style="font-size:16px">Club Fenicia</span>
					</a><a id="sidebarToggle" href="javascript:;" class="nav-toggle d-none d-sm-none d-md-none d-lg-block"><i data-toggle="expanded" class="ft-disc toggle-icon"></i></a><a id="sidebarClose" href="javascript:;" class="nav-close d-block d-md-block d-lg-none d-xl-none"><i class="ft-circle"></i></a></div>
			</div>			               
	 	<!--***************************************** Leftsidebar Portion ****************************************************-->
			<div class="sidebar-content">
				<div class="nav-container">				
					<ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
						<li class="menu-content nav-item <?php echo ($this->uri->segment(2) == 'dashboard') ? 'active' :''; ?>"><a href="<?php echo base_url().'admin/dashboard'; ?>"><i class="icon-screen-desktop"></i><span data-i18n="" class="menu-title">Dashboard</span></a></li>
						<li class="has-sub nav-item <?php echo ($this->uri->segment(2) == 'member') ? 'open' :''; ?>"><a href="javascript:void(0);"><i class="fa fa-user" aria-hidden="true"></i><span data-i18n="" class="menu-title">Manage Members</span></a>
							<ul class="menu-content">
			                	<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/member';?>" class="menu-item">Members List</a></li>
			                  	<li <?php echo ($this->uri->segment(3) == 'add') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/member/add';?>" class="menu-item">Add New Member</a></li>
			                </ul>
		              	</li>
		              	<li class="has-sub nav-item <?php echo ($this->uri->segment(2) == 'users') ? 'open' :''; ?>"><a href="javascript:void(0);"><i class="fa fa-users" aria-hidden="true"></i><span data-i18n="" class="menu-title">Manage Sub-Administrator</span></a>
			                <ul class="menu-content">
			                	<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/users';?>" class="menu-item">Sub-Administrator List</a></li>
			                </ul>
		              	</li>
		              	<li class="has-sub nav-item <?php echo ($this->uri->segment(2) == 'package' || $this->uri->segment(2) == 'PackageBenefit' || $this->uri->segment(2) == 'PackageVoucher') ? 'open' :''; ?>"><a href="javascript:void(0);"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><span data-i18n="" class="menu-title">Manage Membership</span></a>
			                <ul class="menu-content">
			                	<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/package';?>" class="menu-item">Membership List</a></li>
			                	<li <?php echo ($this->uri->segment(3) == 'add') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/package/add';?>" class="menu-item">Add New Membership</a></li>			                  	
			                  	<!--<li <?php echo ($this->uri->segment(3) == 'PackageMemberList') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/package/PackageMemberList';?>" class="menu-item">Packages Members List</a></li>-->
			                	<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/PackageBenefit';?>" class="menu-item">Membership Benefit List</a></li>
			                	<li <?php echo ($this->uri->segment(3) == 'add') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/PackageBenefit/add';?>" class="menu-item">Add New Membership Benefit</a></li>
			                	<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/PackageVoucher';?>" class="menu-item">Membership Voucher List</a></li>
			                	<li <?php echo ($this->uri->segment(3) == 'add') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/PackageVoucher/add';?>" class="menu-item">Add New Membership Voucher</a></li>
			                </ul>
		              	</li>
		              	<li class="has-sub nav-item <?php echo ($this->uri->segment(2) == 'Membership') ? 'open' :''; ?>">
		              		<a><i class="fa fa-user" aria-hidden="true"></i><span data-i18n="" class="menu-title">Manage Membership Owners</span></a>
			                <ul class="menu-content">
			                	<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/Membership';?>" class="menu-item">Membership Owners</a></li>
			                </ul>
		              	</li>
		              	<li class="has-sub nav-item <?php echo ($this->uri->segment(2) == 'Reservation') ? 'open' :''; ?>">
		              		<a><i class="fa fa-ticket" aria-hidden="true"></i><span data-i18n="" class="menu-title">Manage Reservation</span></a>
			                <ul class="menu-content">
			                	<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/Reservation';?>" class="menu-item">Reservation List</a></li>
			                </ul>
		              	</li>
		              	<li class="has-sub nav-item <?php echo ($this->uri->segment(2) == 'gallery') ? 'open' :''; ?>"><a><i class="fa fa-file-image-o" aria-hidden="true"></i><span data-i18n="" class="menu-title">Manage Gallery</span></a>
			                <ul class="menu-content">
			                	<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/gallery';?>" class="menu-item">Album List</a></li>
			                	<li <?php echo ($this->uri->segment(3) == 'add') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/gallery/add';?>" class="menu-item">Add New Album</a></li>
			                </ul>
		              	</li>
		              	<li class="has-sub nav-item <?php echo ($this->uri->segment(2) == 'event') ? 'open' :''; ?>"><a href="javascript:void(0);"><i class="fa fa-calendar" aria-hidden="true"></i><span data-i18n="" class="menu-title">Manage Event</span></a>
			                <ul class="menu-content">
			                	<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/event';?>" class="menu-item">Event List</a></li>
			                	<li <?php echo ($this->uri->segment(3) == 'add') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/event/add';?>" class="menu-item">Add New Event</a></li>
			                </ul>
		              	</li>
		              	<li class="has-sub nav-item <?php echo ($this->uri->segment(2) == 'cms') ? 'open' :''; ?>"><a><i class="fa fa-file-text" aria-hidden="true"></i><span data-i18n="" class="menu-title">Manage CMS</span></a>
			                <ul class="menu-content">
			                	<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/cms';?>" class="menu-item">Page List</a></li>
			                	<li <?php echo ($this->uri->segment(3) == 'add') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/cms/add';?>" class="menu-item">Add New Page</a></li>
			                </ul>
		              	</li>
		              	<li class="has-sub nav-item <?php echo ($this->uri->segment(2) == 'role') ? 'open' :''; ?>"><a><i class="fa fa-cogs" aria-hidden="true"></i><span data-i18n="" class="menu-title">Manage Role</span></a>
			                <ul class="menu-content">
			                	<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/role';?>" class="menu-item">Role List</a></li>
			                	<li <?php echo ($this->uri->segment(3) == 'add') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/role/add';?>" class="menu-item">Add New Role</a></li>
			                </ul>
		              	</li>
		              	<li class="has-sub nav-item <?php echo ($this->uri->segment(2) == 'report') ? 'open' :''; ?>"><a><i class="fa fa-bar-chart" aria-hidden="true"></i><span data-i18n="" class="menu-title">Manage Report</span></a>
			                <ul class="menu-content">
			                	<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/reports/membershipPackagesPurchased';?>" class="menu-item">Membership Purchased Report</a></li>
			                	<!--<li <?php echo ($this->uri->segment(3) == '') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/reports/membershipPackagesTransaction';?>" class="menu-item">Membership Packages Transaction Report</a></li>-->
			                	<li <?php echo ($this->uri->segment(3) == 'add') ? 'active' :''; ?>><a href="<?php echo base_url().'admin/reports/reservationReport';?>" class="menu-item">Reservation Report</a></li>
			                </ul>
		              	</li>		              	
					</ul>				
				</div>
			</div>
			<div class="sidebar-background"></div>			
		<!--***************************************** End Left Sidebar Portion *****************************************--->
		<!--***************************************** Start Header Portion *****************************************-->
		</div>
		<nav class="navbar navbar-expand-lg navbar-light bg-faded">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" data-toggle="collapse" class="navbar-toggle d-lg-none float-left"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
					<span class="d-lg-none navbar-right navbar-collapse-toggle"><a class="open-navbar-container"><i class="ft-more-vertical"></i></a></span> </div>
				<div class="navbar-container">
					<div id="navbarSupportedContent" class="collapse navbar-collapse">
						<ul class="navbar-nav">	
							<?php if(empty($this->profile_img)): ?>
							<?php	$profile_img	= base_url('public/admin_assets/img/portrait/small/avatar-s-3.jpg'); ?>
							<?php else: ?>
							<?php	$profile_img	= base_url().'public/upload_image/profile_photo/'.$this->profile_img; ?>
							<?php endif;?>
							<li class="dropdown nav-item mr-0"><a id="dropdownBasic3" href="javascript:void(0);" data-toggle="dropdown" class="nav-link position-relative dropdown-user-link dropdown-toggle"><span style="color:white">Admin</span>
									<p class="d-none">User Settings</p>
								</a>
								<div aria-labelledby="dropdownBasic3" class="dropdown-menu dropdown-menu-right">
									<div class="arrow_box_right"> 
										<!--<a href="<?= base_url('admin/changeprofile') ?>" class="dropdown-item py-1"><i class="ft-edit mr-2"></i><span>My Profile</span></a>-->
										<div class="arrow_box_right"> <a href="<?= base_url('admin/changepassword') ?>" class="dropdown-item py-1"><i class="fa fa-unlock-alt mr-2"></i><span>Change Password</span></a> 
										<a href="<?= base_url('admin/logout') ?>" class="dropdown-item"><i class="ft-power mr-2"></i><span>Logout</span></a> </div>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</nav>
<div class="main-panel">
<script src="<?php echo base_url();?>public/admin_assets/js/Datepair.js"></script>
<script src="<?php echo base_url();?>public/admin_assets/js/jquery.datepair.js"></script>
<script src='<?php echo base_url()?>public/admin_assets/js/fullcalendar.min.js'></script>
<script>
 $(document).ready(function() {          
	/*var rel_val       = $('#dashboard_tab3').attr('rel');
	alert(rel_val);*/   

	$('#calendar').fullCalendar({                   
	    header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listMonth'
		},          
		weekNumbers: true,		
		height: 'auto',	
		editable: true,
		eventLimit: 5, 
		events: '<?php echo base_url() ?>admin/event/jsonEventFeed/',				
		eventRender: function (event, element) {
			if(event.url != ''){
				element.popover({
					title: event.title,
					content: event.description,
					trigger: 'hover',
					placement: 'top',
					container: 'body',
					html:true
				});					
			}				
		},
	    displayEventTime: false,               
	});
	$('#calendar').fullCalendar('render');
	});
</script>