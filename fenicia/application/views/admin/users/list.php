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
                                    <h4 class="card-title">Sub Administrator List</h4>
                                    <a class="add_bttn title_btn t_btn_list" href="<?= base_url(); ?>admin/users/add"><span><i class="fa fa-plus" aria-hidden="true"></i></span> Add Sub Administrator</a>    
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
                                                                <a class="nav-link active" data-toggle="tab" href="#active_user">Active Sub Administrator</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#inactive_user">Inactive Sub Administrator</a>
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
                                                                                <th>Name</th>
                                                                                <th>Role</th>
                                                                                <th>Mobile</th>
                                                                                <th>Email</th>
                                                                                <!-- <th>Status</th> -->
                                                                                <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
                                                                                <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                                                                                <th>Log</th>
                                                                            </tr>
                                                                        </thead>
                                                                            <tbody>
                                                                                <?php if (!empty($user_active_list)) { ?>
                                                                                <?php     foreach ($user_active_list as $key => $actv_user) { ?>
                                                                                <tr>
                                                                                    <td><?= $key + 1 ?></td>                                                                                        
                                                                                    <td><?= ucfirst($actv_user['full_name']) ?></td>
                                                                                    <td><?= ucfirst($actv_user['role_name']) ?></td>
                                                                                    <td><?= $actv_user['mobile'] ?></td>
                                                                                    <td><?= $actv_user['email'] ?></td>                                                                                    
                                                                                    <!-- <td class="action_td text-center">
                                                                                        <a title="Inactive" class="btn_action edit_icon inactive_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $actv_user['user_id'];?>" href="<?=base_url('admin/users/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                    </td> -->
                                                                                    <td class="action_bttn action_td text-center">
                                                                                        <a title="Inactive" class="edit_bttn btn_action btn-warning active_btn make_inactive" data-id="<?php echo $actv_user['user_id'];?>" href="<?=base_url('admin/users/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                        <a title="Edit" href="<?=base_url('admin/users/edit/'.$actv_user['user_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                        <!--<a title="Delete" href="<?=base_url('admin/users/DeleteUser/'.$actv_user['user_id'])?>" class="delete_bttn btn_action btn-danger" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                                                                    </td>
                                                                                    <td class="action_td text-center"><a title="Log" class="btn_action edit_icon log_view" data-column="package_id" data-title="Package" data-id="<?= $actv_user['user_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a></td>
                                                                                </tr>
                                                                            <?php 
                                                                            } } else { ?>
                                                                                <tr>
                                                                                    <td colspan="7" style="text-align:center;">No Data Available</td>
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
                                                                                <th>Role</th>
                                                                                <th>Mobile</th>
                                                                                <th>Email</th>
                                                                                <!-- <th>Status</th> -->
                                                                                <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
                                                                                <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                                                                                <th>Log</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php if (!empty($user_inactive_list)) { ?>
                                                                            <?php     foreach ($user_inactive_list as $key => $inactv_user) { ?>
                                                                            <tr>
                                                                                <td><?= $key + 1 ?></td>                                                                                        
                                                                                <td><?= ucfirst($inactv_user['full_name']) ?></td>
                                                                                <td><?= ucfirst($inactv_user['role_name']) ?></td>
                                                                                <td><?= $inactv_user['mobile'] ?></td>
                                                                                <td><?= $inactv_user['email'] ?></td>
                                                                                <!-- <td class="action_td text-center">
                                                                                    <a title="Inactive" class="btn_action btn-warning active_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $inactv_user['user_id'];?>" href="<?=base_url('admin/users/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                </td> -->
                                                                                <td class="action_bttn action_td text-center">
                                                                                    <a title="Inactive" class="edit_bttn btn_action edit_icon inactive_btn make_active" data-id="<?php echo $inactv_user['user_id'];?>" href="<?=base_url('admin/users/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                    <a title="Edit" href="<?=base_url('admin/users/edit/'.$inactv_user['user_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                    <!--<a title="Delete" href="<?=base_url('admin/users/DeleteUser/'.$inactv_user['user_id'])?>" class="delete_bttn btn_action btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                                                                </td>
                                                                                <td class="action_td text-center"><a title="Log" class="btn_action edit_icon log_view" data-column="package_id" data-title="Package" data-id="<?= $inactv_user['user_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a></td>
                                                                                
                                                                            </tr>
                                                                        <?php 
                                                                        } } else { ?>
                                                                            <tr>
                                                                                <td colspan="7" style="text-align:center;">No Data Available</td>
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