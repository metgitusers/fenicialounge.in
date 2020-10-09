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
                                    <h4 class="card-title">Request For Photograph List</h4>                                    
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
                                                        <div class="tab-content">
                                                            <div id="active_user" class="tab-pane active"><br>
                                                                <div class="table-responsive custom_table_area">
                                                                    <table class="table table-striped table-bordered dom-jQuery-events c_table_style">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>SL No.</th>                                                                                
                                                                                <th>Name</th>
                                                                                <th>Image</th>
                                                                                <th>Phone No.</th>
                                                                                <th>Whatsapp No.</th>
                                                                                <th>Date Of Visit</th>
                                                                                <th>Request Received</th>
                                                                                <th>Send On</th>
                                                                                <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>                                                                               
                                                                                <th class="action_bttn" <?php echo $actrion_visibility; ?>>Status</th>
                                                                                <th>Log</th>                                                                               
                                                                            </tr>
                                                                        </thead>
                                                                        	<tbody>
                                                                                <?php if (!empty($request_for_photograph_list)) { ?>
                                                                                <?php     foreach ($request_for_photograph_list as $key => $list) { ?>
                                                                                <tr>
                                                                                    <td><?= $key + 1 ?></td>                                                                                        
                                                                                    <td><?= ucfirst($list['name']) ?></td>
                                                                                    <td><?php if(!empty($list['profile_img'])){ ?> <a href="<?php echo base_url().'public/upload_image/profile_photo/'.$list['profile_img']; ?>" data-fancybox data-caption="Profile Picture"><img src="<?php echo base_url().'public/upload_image/profile_photo/'.$list['profile_img']; ?>" width="50px" height="50px"></a><?php } ?></td>                                                                                   
                                                                                    <td><?= $list['country_code'].$list['phoneno'] ?></td>
                                                                                    <td><?= $list['country_code_whatsappno'].$list['whatsappno'] ?></td>
                                                                                    <td width="12%"><?= date('d/m/Y',strtotime($list['date_of_visit'])) ?></td>
                                                                                    <td width="12%"><?= date('d/m/Y',strtotime($list['created_on'])) ?></td>
                                                                                    <td width="12%"><?php if($list['send_on']){ echo date('d/m/Y',strtotime($list['send_on'])); }?></td>                                                                                                                                                                   
                                                                                    <td class="action_bttn action_td text-center">
                                                                                        <?php if($list['status'] == '0'): ?>
                                                                                            <a title="Send" style="width:auto; height:auto;front-size:20px" class="btn btn-success send_request" data-status="<?php echo $list['status']; ?>" data-id="<?php echo $list['request_for_photograph_id'];?>" href="<?=base_url('admin/Requestforphotograph/changeStatus/')?>" ><strong>SEND</strong></a>
                                                                                        <?php else: ?>
                                                                                            <button class="btn btn-success" style="pointer-events: none;cursor: default;">Sent</button>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                    <td class="action_td text-center">
                                                                                        <a title="Log" class="btn_action edit_icon log_view" data-column="request_for_photograph_id" data-title="Request for photograph" data-id="<?= $list['request_for_photograph_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a>
                                                                                    </td>
                                                                                    
                                                                                </tr>
                                                                            <?php 
                                                                            } } else { ?>
	                                                                            <tr>
	                                                                                <td colspan="8" style="text-align:center;">No Data Available</td>
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