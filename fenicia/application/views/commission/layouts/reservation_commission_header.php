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
?><body data-col="2-columns" class=" 2-columns ">
	<div class="wrapper">	
		
		<nav class="navbar navbar-expand-lg navbar-light bg-faded">
			<div class="container-fluid">
				<div class="logo-img pull-left"><img width="50px" height="50" src="<?= base_url('public/images/logo.png') ?>" alt="Convex Logo" /></div>
				<div class="navbar-header">					
					<button type="button" data-toggle="collapse" class="navbar-toggle d-lg-none float-left"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
					<span class="d-lg-none navbar-right navbar-collapse-toggle"><a class="open-navbar-container"><i class="ft-more-vertical"></i></a></span> </div>
						<div class="navbar-container">							
							<div id="navbarSupportedContent" class="collapse navbar-collapse">
								<ul class="navbar-nav">										
									<?php if($this->session->userdata('user_data') !='' ): 
												//$user_details 	= $this->session->userdata('user_details');
												//$logger_name	= $user_details['first_name'];
												$logger_name	= $this->session->userdata('user_data');
											else: 
												$logger_name	= '';
											endif;
										if(!empty($logger_name)): 
									?>

									<li class="dropdown nav-item mr-0">
										<a id="dropdownBasic3" href="javascript:void(0);" data-toggle="dropdown" class="nav-link position-relative dropdown-user-link dropdown-toggle"><span style="color:white"><?php echo $logger_name; ?></span>
											<p class="d-none">User Settings</p>
										</a>
										<div aria-labelledby="dropdownBasic3" class="dropdown-menu dropdown-menu-right">
											<div class="arrow_box_right"> 
												<a href="<?= base_url('commission/changepassword') ?>" class="dropdown-item py-1"><i class="fa fa-unlock-alt mr-2"></i><span>Change Password</span></a>
											</div>
											<div class="arrow_box_right">
												<a href="<?= base_url('commission/reservationCommission') ?>" class="dropdown-item"><i class="icon-home mr-2" ></i><span>Dashboard</span></a> 
											</div>	
											<div class="arrow_box_right">
												<a href="<?= base_url('commission/logout') ?>" class="dropdown-item"><i class="ft-power mr-2"></i><span>Logout</span></a> 
											</div>
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
<script>
 $(document).ready(function() {
	/*var rel_val       = $('#dashboard_tab3').attr('rel');
	alert(rel_val);*/   

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

	/*var rel_val       = $('#dashboard_tab3').attr('rel');
	alert(rel_val);*/   

	
	var menu_id = $("#menu_id").val();
	//alert(menu_id);
 	$.ajax({
        type: "POST",
        url: '<?php echo base_url('admin/index/Ck_User_Permission/')?>',
        data:{menu_id:menu_id},
        dataType:'json',
        success: function(response){   
        //alert(response);           
          //$('#modalContent').html(response.message);  
          //$('#myModal').modal('show');
       
	      	if(response['add_flag'] !='1'){
			    $(".add_bttn").remove();
			}   
			if(response['edit_flag'] !='1'){
			    $(".edit_bttn").remove();
			}   
			if(response['delete_flag'] !='1'){
			    $(".delete_bttn").remove();
			}   
			if(response['download_flag'] !='1'){
			    $(".download_bttn").remove();
			}         
        },
        error:function(response){
          
        }
  	});
	$('#calendar').fullCalendar('render');
});

</script>