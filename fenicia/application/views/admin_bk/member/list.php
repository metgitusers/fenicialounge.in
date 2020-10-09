<div class="main-content">
    <div class="content-wrapper">
        <div class="container-fluid">
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
            <!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="page-title-wrap">
                                    <h4 class="card-title">Users List</h4>
                                    <a class="add_bttn title_btn t_btn_list" href="<?= base_url(); ?>admin/member/add"><span><i class="fa fa-plus" aria-hidden="true"></i></span> Add Users</a>    
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="px-3">
                                    <form class="form">
                                        <div class="form-body">
                                            <!--<h4 class="form-section">
                                                <i class="icon-user"></i> Personal Details</h4>-->
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="staff_tab_area">
                                                        <ul class="nav nav-tabs" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" data-toggle="tab" href="#active_user">Active Users</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#inactive_user">Inactive Users</a>
                                                            </li>
                                                        <!--    <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#trash_user">Trash Driver</a>
                                                            </li> -->
                                                        </ul>
                                                        <div class="tab-content">
                                                            <div id="active_user" class="tab-pane active"><br>
                                                                <div class="table-responsive custom_table_area">
                                                                    <table class="table table-striped table-bordered dom-jQuery-events c_table_style table-hover">
                                                                        <thead>
                                                                            <tr>
                                                                                <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>    
                                                                                <th>SL No.</th>                                                                                
                                                                                <th>Name</th>
                                                                                <th>Source</th>
                                                                                <th>Mobile</th>
                                                                                <th>Email</th>
                                                                                <th>Gender</th>
                                                                                <th>Registered On</th>
                                                                                <th>Status</th>
                                                                                
                                                                                <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                                                                                
                                                                            </tr>
                                                                        </thead>
                                                                        	<tbody>
                                                                                <?php if (!empty($member_active_list)) { 
                                                                                        //PR($member_active_list);
                                                                                ?>
                                                                                <?php     foreach ($member_active_list as $key => $actv_mem) { ?>
                                                                                <tr>
                                                                                    
                                                                                    <td><?= $key + 1 ?></td>                                                                                        
                                                                                    <td class="name_space" =""><?= ucfirst($actv_mem['full_name']).'<br>'.'Dob: '.date('d/m/Y',strtotime($actv_mem['dob'])) ?></td>
                                                                                    <?php if($actv_mem['added_form'] == "admin"): 
                                                                                            $added_form  = 'Offline';
                                                                                          elseif($actv_mem['added_form'] == "front"): 
                                                                                            $added_form  = 'App';
                                                                                          else: 
                                                                                            $added_form  = 'Web';
                                                                                          endif; 
                                                                                     ?>
                                                                                    <td><?= $added_form; ?></td>                                                                                        
                                                                                    <?php if($actv_mem['registration_type'] == "1"): 
                                                                                    		$registration_type	= 'Mobile';
                                                                            			  elseif($actv_mem['registration_type'] == "2"): 
                                                                            				$registration_type	= 'Email';
                                                                    					  else: 
                                                                    						$registration_type	= 'Facebook';
                                                                    					  endif; 
                                                                					 ?>
                                                                					<?php if($actv_mem['login_status'] == "1"): 
                                                                                        $Verified   = $registration_type;
                                                                                      else:
                                                                                        $Verified   = '';
                                                                                      endif; 
                                                                                    ?>                                                                					
                                                                                    <td><?php if($Verified == "Mobile"){ echo '<i class="fa fa-check" style="color:green" aria-hidden="true"></i> ';} echo $actv_mem['mobile'] ?></td>
                                                                                    <td><?php if($Verified == "Email"){ echo '<i class="fa fa-check" style="color:green" aria-hidden="true"></i> ';} echo $actv_mem['email']?></td>                                                                                
                                                                                    <td><?= ucfirst($actv_mem['gender']); ?></td>
                                                                                    <td><?= date('d/m/Y',strtotime($actv_mem['created_ts'])); ?></td>
                                                                                    <td class="action_td text-center">
                                                                                        <a title="Inactive" class="btn_action edit_icon inactive_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $actv_mem['member_id'];?>" href="<?=base_url('admin/member/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                    </td>                                                                                
                                                                                    <td class="action_td text-center" <?php echo $actrion_visibility; ?>>
                                                                                        <a title="Inactive" class="edit_bttn btn_action btn-warning active_btn make_inactive" data-id="<?php echo $actv_mem['member_id'];?>" href="<?=base_url('admin/member/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                        <a title="Edit" href="<?=base_url('admin/member/edit/'.$actv_mem['member_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                        <!--<a title="Delete" href="<?=base_url('admin/member/DeleteMember/'.$actv_mem['member_id'])?>" class="delete_bttn btn_action btn-danger delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                                                                    </td>
                                                                                    
                                                                                </tr>
                                                                            <?php 
                                                                            } } else { ?>
	                                                                            <tr>
	                                                                                <td colspan="13" style="text-align:center;">No Data Available</td>
	                                                                            </tr>

                                                                        <?php  } ?>

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div id="inactive_user" class="tab-pane fade"><br>
                                                                <div class="table-responsive custom_table_area">
                                                                    <table class="table table-striped table-bordered dom-jQuery-events c_table_style">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>SL No.</th>                                                                                
                                                                                <th>Name</th>                                                                                
                                                                                <th>Source</th>
                                                                                <th>Mobile</th>
                                                                                <th>Email</th>
                                                                                <th>Gender</th>
                                                                                <th>Registered On</th>
                                                                                <th>Status</th>
                                                                                <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
                                                                                <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                                                                                
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php if (!empty($member_inactive_list)) { ?>
                                                                            <?php     foreach ($member_inactive_list as $key => $inactv_mem) { ?>
                                                                            <tr>
                                                                                <td><?= $key + 1 ?></td>                                                                                        
                                                                                <td class="name_space"><?= ucfirst($inactv_mem['full_name']).'<br>'.'Dob: '.date('d/m/Y',strtotime($inactv_mem['dob'])) ?> </td>
                                                                                <?php if($inactv_mem['added_form'] == "admin"): 
                                                                                            $added_form  = 'Offline';
                                                                                          elseif($inactv_mem['added_form'] == "front"): 
                                                                                            $added_form  = 'App';
                                                                                          else: 
                                                                                            $added_form  = 'Web';
                                                                                          endif; 
                                                                                     ?>
                                                                                <td><?= $added_form; ?></td>                                                                                                                                                                    
                                                                                <?php if($inactv_mem['registration_type'] == "1"): 
                                                                                		$registration_type	= 'Mobile';
                                                                        			  elseif($inactv_mem['registration_type'] == "2"): 
                                                                        				$registration_type	= 'Email';
                                                                					  else: 
                                                                						$registration_type	= 'Facebook';
                                                                					  endif; 
                                                            					 ?>
                                                            					<?php if($inactv_mem['login_status'] == "1"): 
                                                                                		$Verified	= $registration_type;
                                                                        			  else:
                                                                        			  	$Verified	= '';
                                                                					  endif; 
                                                            					 ?>                                                                                
                                                                                <td><?php if($Verified == "Mobile"){ echo '<i class="fa fa-check" style="color:green" aria-hidden="true"></i> ';} echo $inactv_mem['mobile'] ?></td>
                                                                                <td><?php if($Verified == "Email"){ echo '<i class="fa fa-check" style="color:green" aria-hidden="true"></i> ';} echo $inactv_mem['email']?></td>   
                                                                                <td><?= ucfirst($inactv_mem['gender']); ?></td>
                                                                                <td><?= date('d/m/Y',strtotime($actv_mem['created_ts'])); ?></td>
                                                                                <td class="action_td text-center">
                                                                                    <a title="Active" class="btn_action btn-warning active_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $inactv_mem['member_id'];?>" href="<?=base_url('admin/member/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                </td>
                                                                                <td class="action_td text-center" >
                                                                                    <a title="Active" class="edit_bttn btn_action edit_icon inactive_btn make_active" data-id="<?php echo $inactv_mem['member_id'];?>" href="<?=base_url('admin/member/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                    <a title="Edit" href="<?=base_url('admin/member/edit/'.$inactv_mem['member_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                    <!--<a title="Delete" href="<?=base_url('admin/member/DeleteMember/'.$inactv_mem['member_id'])?>" class="delete_bttn btn_action btn-danger delete_btn"><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                                                                </td>
                                                                                
                                                                            </tr>
                                                                        <?php 
                                                                        } } else { ?>
	                                                                        <tr>
	                                                                            <td colspan="13" style="text-align:center;">No Data Available</td>
	                                                                        </tr>

                                                                    <?php  } ?>

                                                                        </tbody>
                                                                    </table>
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
                </div>
            </section>
            <!-- // Basic form layout section end -->
        </div>
    </div>
</div>
<script>

</script>