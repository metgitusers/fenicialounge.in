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
                                    <h4 class="card-title">Club Membership List</h4>
                                    <a class="add_bttn title_btn t_btn_list" href="<?= base_url(); ?>admin/package/add"><span><i class="fa fa-plus" aria-hidden="true"></i></span> Add Club Membership</a>
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
                                                                <a class="nav-link active" data-toggle="tab" href="#active_user">Active Club Membership</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#inactive_user">Inactive Club Membership</a>
                                                            </li>
                                                        <!--    <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#trash_user">Trash Driver</a>
                                                            </li> -->
                                                        </ul>
                                                        <div class="tab-content">
                                                            <div id="active_user" class="tab-pane active"><br>
                                                                <div class="table-responsive custom_table_area">
                                                                    <table class="table table-striped table-bordered dom-jQuery-events c_table_style">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>SL No.</th>                                                                                
                                                                                <th>Membership Name</th>
                                                                                <th width='50%'>Membership Images</th>
                                                                                <th width="25%">Membership Benefit</th>
                                                                                <th width="25%">Membership Voucher</th>
                                                                                <th width="20%">Membership Type</th>
                                                                                <!-- <th>Status</th> -->
                                                                                <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>                                                                                
                                                                                <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                                                                                <th>Log</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php if (!empty($package_active_list['pkg_actv_data'])) { ?>
                                                                                    <?php     foreach ($package_active_list['pkg_actv_data'] as $key => $actv_pkg) { ?>
                                                                                    <tr>
                                                                                        <td><?= $key + 1 ?></td>  
                                                                                        <td><?= ucfirst($actv_pkg['package_name']); ?></td>
                                                                                        <td width='50%'>
                                                                                            <?php if(!empty($package_active_list['image_list'][$actv_pkg['package_id']])):
                                                                                                    foreach($package_active_list['image_list'][$actv_pkg['package_id']] as $ilist): ?>
                                                                                                        <div class="img_class" style="float:left"><img src="<?php echo  base_url().'public/upload_image/package_image/'.$ilist['images']; ?>" width="50px" height="50px"></div>
                                                                                            <?php      endforeach; ?>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td width="25%">
                                                                                            <?php if(!empty($package_active_list['benifit_list'][$actv_pkg['package_id']])):?>
                                                                                            <?php      foreach($package_active_list['benifit_list'][$actv_pkg['package_id']] as $blist): ?>
                                                                                                        <?php echo  $blist['benefit_name']; ?></br>
                                                                                            <?php      endforeach; ?>
                                                                                                    </ul>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td width="25%">
                                                                                            <?php if(!empty($package_active_list['voucher_list'][$actv_pkg['package_id']])):?>
                                                                                            <?php      foreach($package_active_list['voucher_list'][$actv_pkg['package_id']] as $vlist): ?>
                                                                                                        <?php echo  $vlist['voucher_name']; ?></br>
                                                                                            <?php      endforeach; ?>
                                                                                                    </ul>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td width="20%">
                                                                                            <?php if(!empty($package_active_list['price_list'][$actv_pkg['package_id']])):?>
                                                                                            <?php      foreach($package_active_list['price_list'][$actv_pkg['package_id']] as $plist): ?>
                                                                                                        <?php echo  $plist['package_type_name'].':'.$plist['price']; ?></br>
                                                                                            <?php      endforeach; ?>
                                                                                                    </ul>
                                                                                            <?php endif; ?>
                                                                                        </td> 
                                                                                        <!-- <td class="action_td text-center">
                                                                                            <a title="Inactive" class="btn_action edit_icon inactive_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $actv_pkg['package_id'];?>" href="<?=base_url('admin/package/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                        </td> -->                                                                                      
                                                                                        <td class="action_bttn action_td text-center">
                                                                                            <a title="Inactive" class="edit_bttn btn_action btn-warning active_btn make_inactive" data-id="<?php echo $actv_pkg['package_id'];?>" href="<?=base_url('admin/package/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                            <a title="Edit" href="<?=base_url('admin/package/edit/'.$actv_pkg['package_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                            <!--<a title="Delete" href="<?=base_url('admin/package/DeletePackage/'.$actv_pkg['package_id'])?>" class="delete_bttn btn_action btn-danger delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                                                                        </td>
                                                                                        <td class="action_td text-center">
                                                                                            <a title="Log" class="btn_action edit_icon log_view" data-column="package_id" data-title="Package" data-id="<?= $actv_pkg['package_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php 
                                                                                } } else { ?>
                                                                                <tr>
                                                                                    <td colspan="17" style="text-align:center;">No Data Available</td>
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
                                                                                <th>Membership Name</th>
                                                                                <th width='50%'>Membership Images</th>
                                                                                <th width="20%">Membership Benefit</th>
                                                                                <th width="20%">Membership Voucher</th>
                                                                                <th width="20%">Membership Type</th>
                                                                                <!-- <th>Status</th> --> 
                                                                                <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>                                                                               
                                                                                <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                                                                                <th>Log</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php if (!empty($package_inactive_list['pkg_inactv_data'])) { ?>
                                                                                    <?php     foreach ($package_inactive_list['pkg_inactv_data'] as $key => $inactv_pkg) { ?>
                                                                                    <tr>
                                                                                        <td><?= $key + 1 ?></td>  
                                                                                        <td><?= ucfirst($inactv_pkg['package_name']); ?></td>
                                                                                        <td width='50%'>
                                                                                            <?php if(!empty($package_inactive_list['image_list'][$inactv_pkg['package_id']])):
                                                                                                    foreach($package_inactive_list['image_list'][$inactv_pkg['package_id']] as $ilist): ?>
                                                                                                        <div style="float:left"><img src="<?php echo  base_url().'public/upload_image/package_image/'.$ilist['images']; ?>" width="50px" height="50px"></div>
                                                                                            <?php      endforeach; ?>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td width="20%">
                                                                                            <?php if(!empty($package_inactive_list['benifit_list'][$inactv_pkg['package_id']])):?>
                                                                                            <?php      foreach($package_inactive_list['benifit_list'][$inactv_pkg['package_id']] as $blist): ?>
                                                                                                        <?php echo  $blist['benefit_name']; ?></br>
                                                                                            <?php      endforeach; ?>
                                                                                                    </ul>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td width="20%">
                                                                                            <?php if(!empty($package_inactive_list['voucher_list'][$inactv_pkg['package_id']])):?>
                                                                                            <?php      foreach($package_inactive_list['voucher_list'][$inactv_pkg['package_id']] as $vlist): ?>
                                                                                                        <?php echo  $vlist['voucher_name']; ?></br>
                                                                                            <?php      endforeach; ?>
                                                                                            <?php endif; ?>
                                                                                        </td>
                                                                                        <td width="20%">
                                                                                            <?php if(!empty($package_inactive_list['price_list'][$inactv_pkg['package_id']])):?>
                                                                                            <?php      foreach($package_inactive_list['price_list'][$inactv_pkg['package_id']] as $plist): ?>
                                                                                                        <?php echo  $plist['package_type_name'].':'.$plist['price']; ?></br>
                                                                                            <?php      endforeach; ?>
                                                                                                    </ul>
                                                                                            <?php endif; ?>
                                                                                        </td>                                                                                        
                                                                                        <!-- <td class="action_td text-center">
                                                                                            <a title="Active" class="btn_action btn-warning active_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $inactv_pkg['package_id'];?>" href="<?=base_url('admin/package/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                        </td> -->
                                                                                        <td class="action_bttn action_td text-center">
                                                                                            <a title="Active" class="edit_bttn btn_action edit_icon inactive_btn make_active" data-id="<?php echo $inactv_pkg['package_id'];?>" href="<?=base_url('admin/package/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                            <a title="Edit" href="<?=base_url('admin/package/edit/'.$inactv_pkg['package_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                            <!--<a title="Delete" href="<?=base_url('admin/package/DeletePackage/'.$inactv_pkg['package_id'])?>" class="delete_bttn btn_action btn-danger delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                                                                        </td>
                                                                                        <td class="action_td text-center"><a title="Log" class="btn_action edit_icon log_view" data-column="package_id" data-title="Package" data-id="<?= $inactv_pkg['package_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a></td>
                                                                                    </tr>
                                                                                <?php 
                                                                                } } else { ?>
                                                                                <tr>
                                                                                    <td colspan="17" style="text-align:center;">No Data Available</td>
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