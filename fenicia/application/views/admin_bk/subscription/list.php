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
                                    <h4 class="card-title">Membership Package List</h4>

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
                                                                <a class="nav-link active" data-toggle="tab" href="#active_user">Active Driver</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#inactive_user">Inactive Driver</a>
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
                                                                                <th>Package Name</th>
                                                                                <th>Unite Price($)</th>
                                                                                <th>Monthly Price($)</th>
                                                                                <th>Year Price($)</th>
                                                                                <th>Status</th>                                                                                
                                                                                <th>Action</th>
                                                                                
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php if (!empty($package_active_list)) { ?>
                                                                                    <?php     foreach ($package_active_list as $key => $actv_pkg) { ?>
                                                                                    <tr>
                                                                                        <td><?= $key + 1 ?></td>  
                                                                                        <td><?= ucfirst($actv_pkg['package_name']); ?></td>
                                                                                        <td><?= $actv_pkg['unit_price'] ?></td>
                                                                                        <td><?= $actv_pkg['monthly_price'] ?></td>
                                                                                        <td><?= $actv_pkg['yearly_price'] ?></td>
                                                                                        <td class="action_td text-center">
                                                                                            <a title="Inactive" class="btn_action btn-warning active_btn" data-id="<?php echo $actv_pkg['package_id'];?>" href="<?=base_url('admin/Subscription/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                        </td>                                                                                        
                                                                                        <td class="action_td text-center">
                                                                                            <a title="Edit" href="<?=base_url('admin/Subscription/edit/'.$actv_pkg['package_id'])?>" class="btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                            <a title="Delete" href="<?=base_url('admin/Subscription/DeletePackage/'.$actv_pkg['package_id'])?>" class="btn_action btn-danger delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>
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
                                                                                <th>Package Name</th>
                                                                                <th>Unite Price($)</th>
                                                                                <th>Monthly Price($)</th>
                                                                                <th>Year Price($)</th>
                                                                                <th>Status</th>                                                                                
                                                                                <th>Action</th>
                                                                                
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php if (!empty($package_inactive_list)) { ?>
                                                                                    <?php     foreach ($package_inactive_list as $key => $inactv_pkg) { ?>
                                                                                    <tr>
                                                                                        <td><?= $key + 1 ?></td>  
                                                                                        <td><?= ucfirst($inactv_pkg['package_name']); ?></td>
                                                                                        <td><?= $inactv_pkg['unit_price'] ?></td>
                                                                                        <td><?= $inactv_pkg['monthly_price'] ?></td>
                                                                                        <td><?= $inactv_pkg['yearly_price'] ?></td>
                                                                                        <td class="action_td text-center">
                                                                                            <a title="Inactive" class="btn_action edit_icon inactive_btn" data-id="<?php echo $inactv_pkg['package_id'];?>" href="<?=base_url('admin/Subscription/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                        </td>                                                                                        
                                                                                        <td class="action_td text-center">
                                                                                            <a title="Edit" href="<?=base_url('admin/Subscription/edit/'.$inactv_pkg['package_id'])?>" class="btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                            <a title="Delete" href="<?=base_url('admin/Subscription/DeletePackage/'.$inactv_pkg['package_id'])?>" class="btn_action btn-danger delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>
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