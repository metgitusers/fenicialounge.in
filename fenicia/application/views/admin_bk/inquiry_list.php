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
                                                                            <th>Message</th>
                                                                            <th>Status</th>                                                                            
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
                                                                                            <td><?= $list['message'] ?></td>
                                                                                            <td class="action_td text-center">
                                                                                                    <?php if($list['status'] =='0'){
                                                                                                           $status ='Pending';
                                                                                                           $status_class ='btn-danger';
                                                                                                        }
                                                                                                        else{
                                                                                                            $status ='Answered';
                                                                                                            $status_class ='btn-success';
                                                                                                        }
                                                                                                    ?>  
                                                                                                <a class="btn <?php echo $status_class;?> send_request" data-id="<?php echo $list['inquiry_id'];?>" href="<?=base_url('admin/Inquiry/changeStatus')?>" ><?php echo $status; ?></a>
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