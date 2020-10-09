<div class="main-content">
    <div class="content-wrapper">
        <div class="container-fluid">            
            <!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="page-title-wrap">
                                    <h4 class="card-title">Inquiry List</h4>
                                    <!--<a class="add_bttn title_btn t_btn_list" href="<?= base_url(); ?>admin/Zone/add"><span><i class="fa fa-plus" aria-hidden="true"></i></span> Add Zone</a>-->   
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="px-3">
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
                                                                            <th>Event Name</th>
                                                                            <th>Name</th>
                                                                            <th>Email</th>
                                                                            <th>Contact No.</th>
                                                                            <th>Inquiry Received</th>                                                                            
                                                                            <th>Message</th>
                                                                            <th>Answered On</th>
                                                                            <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
                                                                            <th class="name_space"<?php echo $actrion_visibility; ?>>Action</th>                                                                            
                                                                        </tr>
                                                                    </thead>
                                                                    	<tbody>
                                                                            <?php if (!empty($inquiry_list)) { ?>
                                                                            <?php   foreach ($inquiry_list as $key => $list) { ?>
                                                                                        <tr>
                                                                                            <td><?= $key + 1 ?></td>                                                                                        
                                                                                            <td><?= ucfirst($list['event_name']) ?></td>                                                                                   
                                                                                            <td><?= $list['name'] ?></td>
                                                                                            <td><?= $list['email'] ?></td>
                                                                                            <td><?= $list['country_code'].$list['phone_no']?></td>
                                                                                            <td width="12%"><?= date('d/m/Y',strtotime($list['created_on'])) ?></td>
                                                                                            <td><?= $list['message'] ?></td>
                                                                                            <td width="12%"><?php if(!empty($list['answered_date'])){ echo date('d/m/Y',strtotime($list['answered_date']));} ?></td>
                                                                                            <td class="action_td text-center name_space">
                                                                                                    <?php if($list['status'] =='0'){
                                                                                                           $status ='Pending';
                                                                                                           $status_class ='btn-danger';
                                                                                                        }
                                                                                                        else{
                                                                                                            $status ='Answered';
                                                                                                            $status_class ='btn-success';
                                                                                                        }
                                                                                                    ?>  
                                                                                                <a class="btn <?php echo $status_class;?> send_request" data-status="<?php echo $list['status']; ?>" data-id="<?php echo $list['inquiry_id'];?>" href="<?=base_url('admin/Inquiry/changeStatus')?>" ><?php echo $status; ?></a>
                                                                                                <a title="Log" class="btn_action edit_icon log_view" data-column="inquiry_id" data-title="Inquiry" data-id="<?= $list['inquiry_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a>
                                                                                            </td>                                                                                            
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