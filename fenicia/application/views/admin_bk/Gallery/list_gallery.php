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
                                    <h4 class="card-title">Album</h4>
                                    <a class="add_bttn title_btn t_btn_list" href="<?= base_url(); ?>admin/gallery/add"><span><i class="fa fa-plus" aria-hidden="true"></i></span> Add Album</a>
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
                                                                <a class="nav-link active" data-toggle="tab" href="#active_user">Active Albums</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#inactive_user">Inactive Albums</a>
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
                                                                                <th>Album Name</th>
                                                                               <!--<th>Album Title</th>-->
                                                                                <th>Creation Date</th>
                                                                                <th>Status</th>
                                                                                <th>Action</th>
                                                                                
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php if (!empty($gallery_active_list)) { ?>
                                                                                    <?php     foreach ($gallery_active_list as $key => $actv_glly) { ?>
                                                                                    <tr>
                                                                                        <td><?= $key + 1 ?></td>                                                                                        
                                                                                        <td><?= ucfirst($actv_glly['gallery_name']); ?></td>
                                                                                        <!--<td><?= $actv_glly['gallery_text'] ?></td>-->
                                                                                        <td><?= date('d-m-Y',strtotime($actv_glly['created_on'])) ?></td>
                                                                                        <td class="action_td text-center">
                                                                                            <a title="Inactive" class="btn_action edit_icon inactive_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $actv_glly['gallery_id'];?>" href="<?=base_url('admin/gallery/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                        </td>                                                                                      
                                                                                        <td class="action_td text-center">
                                                                                            <a title="Inactive" class="edit_bttn btn_action btn-warning active_btn make_inactive" data-id="<?php echo $actv_glly['gallery_id'];?>" href="<?=base_url('admin/gallery/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                        	<a title="View Gallery Image" href="<?=base_url('admin/gallery/ViewGalleryImgs/'.$actv_glly['gallery_id'])?>" class="view_bttn btn_action btn-info edit_icon"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                                                            <a title="edit_bttn Edit" href="<?=base_url('admin/gallery/edit/'.$actv_glly['gallery_id'])?>" class="edit_bttn  btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                            <!--<a title="delete_bttn Delete" href="<?=base_url('admin/gallery/DeleteGallery/'.$actv_glly['gallery_id'])?>" class="delete_bttn btn_action btn-danger album_delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->
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
                                                                                <th>Album Name</th>
                                                                                <!--<th>Album Title</th>-->
                                                                                <th>Creation Date</th>
                                                                                <th>Status</th>
                                                                                <th>Action</th>
                                                                                
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php if (!empty($gallery_inactive_list)) { ?>
                                                                                    <?php     foreach ($gallery_inactive_list as $key => $inactv_glly) { ?>
                                                                                    <tr>
                                                                                        <td><?= $key + 1 ?></td>                                                                                        
                                                                                        <td><?= ucfirst($inactv_glly['gallery_name']); ?></td>
                                                                                        <!--<td><?= $inactv_glly['gallery_text'] ?></td>-->
                                                                                        <td><?= date('d-m-Y',strtotime($inactv_glly['created_on'])) ?></td>
                                                                                        <td class="action_td text-center">
                                                                                            <a title="Active" class="btn_action btn-warning active_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $inactv_glly['gallery_id'];?>" href="<?=base_url('admin/gallery/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                        </td>                                                                                      
                                                                                        <td class="action_td text-center">
                                                                                            <a title="Active" class="edit_bttn btn_action edit_icon inactive_btn make_active" data-id="<?php echo $inactv_glly['gallery_id'];?>" href="<?=base_url('admin/gallery/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                        	<a title="View Gallery Image" href="<?=base_url('admin/gallery/ViewGalleryImgs/'.$inactv_glly['gallery_id'])?>" class="view_bttn btn_action btn-info edit_icon"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                                                            <a title="Edit" href="<?=base_url('admin/gallery/edit/'.$inactv_glly['gallery_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                            <!--<a title="Delete" href="<?=base_url('admin/gallery/DeleteGallery/'.$inactv_glly['gallery_id'])?>" class="delete_bttn btn_action btn-danger album_delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->
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