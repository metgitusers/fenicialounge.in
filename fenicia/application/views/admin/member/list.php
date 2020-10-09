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
                                    
                                        <div class="form-body">
                                            <form class="form custom_form_style" method="post" action="<?php echo base_url();?>admin/member">
                                          <div class="form-body">
                                            <div class="user_permission_top">
                                              <div class="row">                            
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                    <label>From Date</label>
                                                    <div class="input-group">
                                                        <input id="from_dt" name="start_date" type="text" class="form-control customize_inputdate pickadate" value="<?php if(!empty($start_date)): echo $start_date;endif;?>"  placeholder="DD/MM/YYYY" />
                                                        <div class="input-group-append">
                                                        <span class="input-group-text">
                                                          <span class="fa fa-calendar-o"></span>
                                                        </span>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="col-md-3">
                                                  <div class="form-group">
                                                    <label>To Date</label>
                                                    <div class="input-group">
                                                        <input id="to_dt" name="end_date" type="text" class="form-control customize_inputdate pickadate" value="<?php if(!empty($end_date)): echo $end_date;endif;?>" placeholder="DD/MM/YYYY" />
                                                        <div class="input-group-append">
                                                        <span class="input-group-text">
                                                          <span class="fa fa-calendar-o"></span>
                                                        </span>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group" style="margin-bottom: 0;">
                                                        <label>App Registered</label>
                                                        <div class="settlement_inline">
                                                          <select name="user_type" class="form-control">
                                                            
                                                            <option value="">All </option>
                                                            <option value="App" <?php if(!empty($user_type)&&$user_type=="App"){ echo "selected"; } ?>>Yes</option>
                                                            <option value="Web" <?php if(!empty($user_type) && $user_type=="Web"){ echo "selected"; } ?>>No</option>
                                                          </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                                                              
                                                <div class="col-md-1" >
                                                  <div class="form-group">
                                                   <label>&nbsp;&nbsp;</label>
                                                    <button type="submit"  class="btn btn-success pull-right" id="search_btn">
                                                      <i class="fa fa-search" aria-hidden="true"></i> Go
                                                    </button>
                                                  </div>
                                                </div>
                                                <div class="col-md-1" >
                                                  <div class="form-group" style="margin-right:12px;margin-top:-1px">
                                                    <label>&nbsp;</label>
                                                    <a class="btn btn-danger pull-right" style="font-size:13px;padding:9px" href="<?php echo base_url();?>admin/member">
                                                      <i class="fa fa-refresh" aria-hidden="true"></i> Clear
                                                    </a>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </form>
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
                                                        <!--<li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#trash_user">Trash Driver</a>
                                                            </li> -->
                                                        </ul>
                                                        <div class="tab-content">
                                                            <div id="active_user" class="tab-pane active"><br>
                                                                <div class="table-responsive custom_table_area export_table_area">
                                                                    <table class="table table-striped table-bordered c_table_style export_btn_dt member_list_table">
                                                                        <thead>
                                                                            <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
                                                                            <tr>
                                                                                <th>SL No.</th>                                                                                
                                                                                <th>Name</th>
                                                                                <th>Source</th>
                                                                                <th>Mobile</th>
                                                                                <th>Email</th>
                                                                                <th>Gender</th>
                                                                                <th>Registered On</th>
                                                                                <!-- <th>Status</th> -->
                                                                                <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                                                                                <th>Log</th>
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
                                                                                    <td><?php echo $actv_mem['mobile'] ?></td>
                                                                                    <td><?php echo $actv_mem['email']?></td>                                                                                
                                                                                    <td><?= ucfirst($actv_mem['gender']); ?></td>
                                                                                    <td><?= date('d/m/Y',strtotime($actv_mem['created_ts'])); ?></td>
                                                                                    <!-- <td class="action_td text-center">
                                                                                        <a title="Inactive" class="btn_action edit_icon inactive_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $actv_mem['member_id'];?>" href="<?=base_url('admin/member/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                    </td> -->                                                                                
                                                                                    <td class="action_bttn action_td text-center">
                                                                                        <a title="Inactive" class="edit_bttn btn_action btn-warning active_btn make_inactive" data-id="<?php echo $actv_mem['member_id'];?>" href="<?=base_url('admin/member/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                        <a title="Edit" href="<?=base_url('admin/member/edit/'.$actv_mem['member_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                        <!--<a title="Delete" href="<?=base_url('admin/member/DeleteMember/'.$actv_mem['member_id'])?>" class="delete_bttn btn_action btn-danger delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                                                                    </td>
                                                                                    <td class="action_td text-center">
                                                                                        <a title="Log" class="btn_action edit_icon log_view" data-column="member_id" data-title="Member" data-id="<?= $actv_mem['member_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a>
                                                                                    </td>
                                                                                    
                                                                                </tr>
                                                                            <?php 
                                                                            } } else { ?>
	                                                                            <tr>
	                                                                                <td colspan="12" style="text-align:center;">No Data Available</td>
	                                                                            </tr>

                                                                        <?php  } ?>

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div id="inactive_user" class="tab-pane fade"><br>
                                                                <div class="table-responsive custom_table_area export_table_area">
                                                                    <table class="table table-striped table-bordered c_table_style export_btn_dt member_list_table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>SL No.</th>                                                                                
                                                                                <th>Name</th>                                                                                
                                                                                <th>Source</th>
                                                                                <th>Mobile</th>
                                                                                <th>Email</th>
                                                                                <th>Gender</th>
                                                                                <th>Registered On</th>
                                                                                <!-- <th>Status</th> -->
                                                                            <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
                                                                                <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                                                                                <th>Log</th>
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
                                                                                <td><?php echo $inactv_mem['mobile'] ?></td>
                                                                                <td><?php echo $inactv_mem['email']?></td>   
                                                                                <td><?= ucfirst($inactv_mem['gender']); ?></td>
                                                                                <td><?= date('d/m/Y',strtotime($actv_mem['created_ts'])); ?></td>
                                                                                <!-- <td class="action_td text-center">
                                                                                    <a title="Active" class="btn_action btn-warning active_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $inactv_mem['member_id'];?>" href="<?=base_url('admin/member/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                </td> -->
                                                                                <td class="action_td text-center">
                                                                                    <a title="Active" class="edit_bttn btn_action edit_icon inactive_btn make_active" data-id="<?php echo $inactv_mem['member_id'];?>" href="<?=base_url('admin/member/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                    <a title="Edit" href="<?=base_url('admin/member/edit/'.$inactv_mem['member_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                    <!--<a title="Delete" href="<?=base_url('admin/member/DeleteMember/'.$inactv_mem['member_id'])?>" class="delete_bttn btn_action btn-danger delete_btn"><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                                                                </td>
                                                                                <td class="action_td text-center">
                                                                                    <a title="Log" class="btn_action edit_icon log_view" data-column="member_id" data-title="Member" data-id="<?= $inactv_mem['member_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a>
                                                                                </td>
                                                                            </tr>
                                                                        <?php 
                                                                        } } else { ?>
	                                                                        <tr>
	                                                                            <td colspan="12" style="text-align:center;">No Data Available</td>
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
$(document).on('click','.page-link',function(){
    var page = $(this).data('dt-idx');
    //alert(page);
})

///////////////datatable export option///////////////////////////////////////

$(document).ready(function() {
    
    var now = new Date();
    var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
    $('.member_list_table').DataTable({
      pageLength: 10,
      dom: 'Bfrtip',
      buttons: [{
          extend: 'excel',        
          text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
          tag:  'span',
          filename: 'user_report_' + date,
          exportOptions: {
                  columns: [0,1,2,3,4,5,6]
          }
        }
        //'copy', 'csv', 'excel', 'pdf', 'print'
      ]
    });
});
///////////////////////////////////////////////////////////////////////////////
</script>