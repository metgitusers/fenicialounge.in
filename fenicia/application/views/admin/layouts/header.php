<!DOCTYPE html>
<html lang="en" class="loading">
<?php 	$active_class 		='';
		$has_submenu_class	='';
?>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
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

/*/////////////////////////////for loader//////////////////////////////////////////////////////*/

.loader {
  border: 16px solid #000; /* Light grey */
  border-top: 16px solid #f9b92d; /* Blue */
  border-radius: 50%;
  width: 120px;
  height: 120px;
  animation: spin 2s linear infinite;
  margin: 20% auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

</style>
</head>
<?php 
//pr($this->notification_data);
$leftmenu  		= get_menu_tree(0); 
//PR($leftmenu);
//exit;
$url_link 					= current_url();
$tmp_url_link				= explode('/', $url_link);
if(count($tmp_url_link) >6){
	$previous_string_url_link	= 'admin/'.$tmp_url_link[count($tmp_url_link)-2];
	$last_string_url_link		= 'admin/'.$tmp_url_link[count($tmp_url_link)-2].'/'.end($tmp_url_link);
}
elseif(count($tmp_url_link) == 6){
	$previous_string_url_link	= '';
	$last_string_url_link		= 'admin/'.end($tmp_url_link);
}
//$last_string_url_link	= prev($tmp_url_link);
//echo $last_string_url_link;exit;

if($last_string_url_link=="admin/index/App")
{
	$last_string_url_link="admin/member";
}
?>


<body data-col="2-columns" class=" 2-columns ">
  <div class="loader"></div> 
  <div class="loader2"></div> 
	<div class="wrapper" style="display:none;">	
		<div data-active-color="white" data-background-color="crystal-clear" data-image="" class="app-sidebar">
			<div class="sidebar-header">
				<div class="logo clearfix"><a href="<?= base_url().'admin/dashboard' ?>" class="logo-text float-left">
						<div class="logo-img"><img src="<?= base_url('public/images/logo.png') ?>" alt="Convex Logo" /></div><span class="align-middle" style="font-size:16px">Club Fenicia</span>
					</a><a id="sidebarToggle" href="javascript:;" class="nav-toggle d-none d-sm-none d-md-none d-lg-block"><i data-toggle="expanded" class="ft-disc toggle-icon"></i></a><a id="sidebarClose" href="javascript:;" class="nav-close d-block d-md-block d-lg-none d-xl-none"><i class="ft-circle"></i></a></div>
			</div>			               
	 	<!--***************************************** Leftsidebar Portion ****************************************************-->
			<div class="sidebar-content">
				<div class="nav-container">
				<?php if(!empty($leftmenu)): ?>
					<ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
						<?php foreach($leftmenu as $val): ?>
						        <?php if($val['is_active'] !='0'): ?>
						        <input type="hidden" name="menu_id" id="menu_id" value="<?php echo $val['menu_id']; ?>" >
						<?php $menu_link				= $val['menu_link'];
								if(!empty($menu_link)){
									$tmp_menu_link			= explode('/', $menu_link);
									//pr($tmp_menu_link,0);
									if(count($tmp_menu_link) >2){
										$previous_string_menu_link	= 'admin/'.$tmp_menu_link[count($tmp_menu_link)-2];
										$last_string_menu_link		= 'admin/'.$tmp_menu_link[count($tmp_menu_link)-2].'/'.end($tmp_menu_link);
									}
									elseif(count($tmp_menu_link) == 2){
										$previous_string_menu_link	= '';
										$last_string_menu_link		= 'admin/'.end($tmp_menu_link);
									}
								}
								else{
									//echo count($tmp_menu_link);
									$last_string_menu_link	= ' ';
								}													
								//$last_string_menu_link	= end($tmp_menu_link);
								//echo $previous_string_url_link."$$###".$previous_string_menu_link;
								if($previous_string_url_link == $val['action']):
									$open_class = 'open';	
								else:
									$open_class = '';
								endif;							
								if($last_string_url_link == $last_string_menu_link):
									$active_class = 'active';
						?>
													
						<?php	else: 
									$active_class = '';
								endif;
								if(!empty($val['sub_menu'])):
									$has_submenu_class	= 'has-sub';
								else:
									$has_submenu_class	= '';
								endif;
						?>
						<?php if(!empty($val['sub_menu']) || $val['menu_link'] !=''): ?>
							<li class="<?php echo $has_submenu_class;?> nav-item <?php echo $active_class.$open_class;?>"><a data-id="<?php echo $val['menu_id']; ?>" href="<?php if(!empty($val['menu_link'])): echo base_url($val['menu_link']); else: echo 'javascript:void(0)'; endif; ?>" class="menu-item"><?php echo $val['menu_icon']; ?><span data-i18n="" class="menu-title"><?php echo $val['menu_name']; ?></span></a>
								<ul class="menu-content">
									<?php if(!empty($val['sub_menu'])): 
											foreach($val['sub_menu'] as $sub_list): ?>
											<?php if($sub_list['is_active'] !='0'): ?>
						                             <input type="hidden" name="menu_id" id="menu_id" value="<?php echo $sub_list['menu_id']; ?>" >
												<?php	$sub_menu_link				= $sub_list['menu_link'];
														$tmp_submenu_link			= explode('/', $sub_menu_link);
														if(count($tmp_submenu_link) >2){
															
															$last_string_submenu_link		= 'admin/'.$tmp_submenu_link[count($tmp_submenu_link)-2].'/'.end($tmp_submenu_link);
														}
														elseif(count($tmp_submenu_link) == 2){
															
															$last_string_submenu_link		= 'admin/'.end($tmp_submenu_link);
														}
														
														if($previous_string_url_link == $sub_list['action']):
															$open_sub_class = 'open';	
														else:
															$open_sub_class = '';
														endif;
													 	if($last_string_url_link == $last_string_submenu_link):
															$sub_active_class = 'active';
												?>
													<input type="hidden" name="menu_id" id="menu_id" value="<?php echo $sub_list['menu_id']; ?>" >
										<?php	else:
												$sub_active_class = '';
												endif;	
										?>									
												<li class="<?php echo $sub_active_class; ?>"><a data-id="<?php echo $sub_list['menu_id']; ?>" href="<?php if(!empty($sub_list['menu_link'])): echo base_url($sub_list['menu_link']); else: 'javascript:void(0)'; endif; ?>" class="menu-item"><?php echo $sub_list['menu_name']; ?></a></li>
									<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
								</ul>								
							</li>
						<?php endif; ?>	
						<?php endif; ?>							
						<?php endforeach; ?>
						<?php if($this->session->userdata('role_id') == '1'): ?>
							<li class="<?php echo ($this->uri->segment(3)=='user-permission' && $this->uri->segment(4)=='')?'active' : '' ?>"><a href="<?= base_url();?>admin/user-permission"><i class="fa fa-check-circle-o" aria-hidden="true"></i><span data-i18n="" class="menu-title">User Permission</span></a>
							</li>
						<?php endif; ?>					
					</ul>
				<?php endif; ?>	
				</div>
			</div>
			<div class="sidebar-background"></div>			
		<!--***************************************** End Left Sidebar Portion *****************************************-->
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
									<?php if(empty($this->notification_data) && !array_key_exists('count', $this->notification_data)): ?>
									<?php	$count_notification  = 0;?>
									<?php else: ?>
									<?php 	$count_notification		= $this->notification_data['count']; ?>
									<?php endif; ?>
									
									<li><a class="add_bttn btn btn-success" href="<?php echo base_url().'admin/reservation/add'?>">New Reservation</a></li>
									<li><a class="edit_bttn btn btn-success" href="<?php echo base_url().'admin/member/add'?>">New Club Membership</a></li>
									<li class="dropdown nav-item mt-1"><a id="dropdownBasic2" href="javascript:void(0);" data-toggle="dropdown" class="nav-link position-relative dropdown-toggle"><i class="fa fa-bell-o" aria-hidden="true" style="color:white"></i><span class="notification badge badge-pill badge-danger"><?php echo $count_notification; ?></span>
											<p class="d-none">Notifications</p>
										</a>
										<?php if(!empty($this->notification_data) && !empty($this->notification_data['details']) && $this->notification_data['count'] != 0): ?>
										<div class="notification-dropdown dropdown-menu dropdown-menu-right">
											<div class="arrow_box_right">
												<div class="noti-list">											
													<ul class="list_notification_head">
													<?php foreach($this->notification_data['details'] as $list): ?>
														<div class="row">
															<div class="col-sm-9"><li><a style="font-size:14px"  reL ="" href="<?php echo base_url().'admin/Reservation/getReservationDetails/'.$list['reservation_id']; ?>" class="notification_url"> <?php echo $list['admin_notification_details']; ?></a></li></div>
															<div class="col-sm-3"><a style="font-size:10px;margin-top:2px" reL=""  href="<?php echo base_url().'admin/Reservation/getReservationDetails/'.$list['reservation_id']; ?>" class="btn btn-danger notification_url">Get Details</a></div>
														</div>
													<?php endforeach; ?>	
													</ul>
												</div>
												<!--<a href="<?php echo base_url().'admin/notification'; ?>" class="noti-footer primary text-center d-block border-top border-top-blue-grey border-top-lighten-4 text-bold-400 py-1">Read All Notifications</a>-->
											</div>
										</div>
									<?php endif; ?>
									</li>
									
									<?php if(empty($this->profile_img)): ?>
									<?php	$profile_img	= base_url('public/admin_assets/img/portrait/small/avatar-s-3.jpg'); ?>
									<?php else: ?>
									<?php	$profile_img	= base_url().'public/upload_image/profile_photo/'.$this->profile_img; ?>
									<?php endif;?>
									<?php 	if($this->session->userdata('user_details') !=''): 
												$user_details 	= $this->session->userdata('user_details');
												$logger_name	= $user_details['first_name'];
											else: 
												$logger_name	= '';
											endif;
										if(!empty($logger_name)): 
									?>

									<li class="dropdown nav-item mr-0"><a id="dropdownBasic3" href="javascript:void(0);" data-toggle="dropdown" class="nav-link position-relative dropdown-user-link dropdown-toggle"><span style="color:white"><?php echo $logger_name; ?></span>
											<p class="d-none">User Settings</p>
										</a>
										<div aria-labelledby="dropdownBasic3" class="dropdown-menu dropdown-menu-right">
											<div class="arrow_box_right"> 
												<!-- <a href="<?= base_url('admin/changeprofile') ?>" class="dropdown-item py-1"><i class="ft-edit mr-2"></i><span>My Profile</span></a> -->
												<div class="arrow_box_right"> <a href="<?= base_url('admin/changepassword') ?>" class="dropdown-item py-1"><i class="fa fa-unlock-alt mr-2"></i><span>Change Password</span></a>
												<?php if($this->session->userdata('user_details') !=''){$user_details =  $this->session->userdata('user_details');} ?> 
												<?php if($user_details['code'] !=''){ ?>
													<div class="arrow_box_right">
													<a href="" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" class="dropdown-item py-1"><i class="fa fa-lock" aria-hidden="true"></i> <span>PIN: <?php echo $user_details['code']; ?></span></a>
												<?php } ?>
												<a href="<?= base_url('admin/logout') ?>" class="dropdown-item"><i class="ft-power mr-2"></i><span>Logout</span></a> </div>
										</div>
									</li>
								<?php endif; ?>
								</ul>
							</div>
						</div>
			</div>
		</nav>

<div class="main-panel">
<script src="<?php echo base_url();?>public/admin_assets/js/Datepair.js"></script>
<script src="<?php echo base_url();?>public/admin_assets/js/jquery.datepair.js"></script>
<script src='<?php echo base_url()?>public/admin_assets/js/fullcalendar.min.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script>
 $(document).ready(function() {
 	
 	$(".js-select2").select2();
	/*var rel_val       = $('#dashboard_tab3').attr('rel');
	alert(rel_val);*/   
	
	//$("body").prepend('<div id="preloader">Loading...</div>');
var menu_id = $("#menu_id").val();
//alert(menu_id);
$.ajax({
	type: "POST",
	url: '<?php echo base_url('admin/index/Ck_User_Permission/')?>',
	data:{menu_id:menu_id},
	dataType:'json',
	success: function(response){   
	//alert(response['add_flag']);           
	  //$('#modalContent').html(response.message);  
	  //$('#myModal').modal('show');

	  	if(response['add_flag'] =='0'){
		    $(".add_bttn").remove();
		    $(".rev_status").css('display','none');
		    $(".past_event_upload_bttn").css('display','none');
		}   
		if(response['edit_flag'] =='0'){
		    $(".edit_bttn").remove();
		    $(".past_event_upload_bttn").css('display','none');
		   	//$(".action_bttn").attr('data-visible',false);
		   //	$(".action_bttn").css('display','none');
		}   
		if(response['view_flag'] =='0'){
		    $(".view_bttn").remove();
		}   
		if(response['download_flag'] =='0'){
		    $(".download_bttn").remove();
		} 
		
		/////////////////////loading the page//////////////////////////////////////
		$(".loader").remove();
		$(".wrapper").show();
	},
	error:function(response){
	  
	}
});
/*$(document).on('click','.menu-item',function(){
	var menu_id = $(this).data('id');
	alert(menu_id);
 	$.ajax({
        type: "POST",
        url: '<?php echo base_url('admin/index/Ck_User_Permission/')?>',
        data:{menu_id:menu_id},
        dataType:'json',
        success: function(response){   
        //alert(response['add_flag']);           
          //$('#modalContent').html(response.message);  
          //$('#myModal').modal('show');
       
	      	if(response['add_flag'] =='0'){
			    $(".add_bttn").remove();
			    $(".rev_status").css('display','none');
			    $(".past_event_upload_bttn").css('display','none');
			}   
			if(response['edit_flag'] =='0'){
			    $(".edit_bttn").remove();
			    $(".past_event_upload_bttn").css('display','none');
			   	//$(".action_bttn").attr('data-visible',false);
			   //	$(".action_bttn").css('display','none');
			}   
			if(response['view_flag'] =='0'){
			    $(".view_bttn").remove();
			}   
			if(response['download_flag'] =='0'){
			    $(".download_bttn").remove();
			} 
			
			/////////////////////loading the page//////////////////////////////////////
			$(".loader").remove();
			$(".wrapper").show();
        },
        error:function(response){
          
        }
  	});
});*/
	$('#calendar').fullCalendar({                   
	    header: {
			left: 'prev,next',
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
	$('#pasteventcalendar').fullCalendar({                   
	    header: {
			left: 'prev,next',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listMonth'
		},          
		weekNumbers: true,		
		height: 'auto',	
		editable: true,
		eventLimit: 5, 
		events: '<?php echo base_url() ?>admin/event/jsonPastEventFeed/',				
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