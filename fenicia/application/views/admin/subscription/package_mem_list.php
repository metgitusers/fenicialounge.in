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
                                                                <a class="nav-link active" data-toggle="tab" href="#premium_user">Premium</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#normal_user">Normal</a>
                                                            </li>
                                                        <!--    <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#trash_user">Trash Driver</a>
                                                            </li> -->
                                                        </ul>
                                                        <div class="tab-content">
                                                            <div id="premium_user" class="tab-pane active"><br>
                                                                <div class="table-responsive custom_table_area">
                                                                    <table class="table table-striped table-bordered dom-jQuery-events c_table_style">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>SL No.</th>                                                                                
                                                                                <th>Name</th>
                                                                                <th>Member Type</th>
                                                                                <th>Mobile</th>
                                                                                <th>Email</th>
                                                                                <th>Gender</th>
                                                                                <th>Marriage Status</th>
                                                                                <th width="25%">DOB</th>
                                                                                <th width="25%">DOA</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php if (!empty($premium_mem_list)) { ?>
                                                                                    <?php     foreach ($premium_mem_list as $key => $actv_mem) { ?>
                                                                                    <tr>
                                                                                        <td><?= $key + 1 ?></td>                                                                                        
                                                                                        <td><?= $actv_mem['full_name'] ?></td>                                                                                        
                                                                                        <td><?= ucfirst($actv_mem['package_name']); ?></td>
                                                                                        <td><?= $actv_mem['mobile'] ?></td>
                                                                                        <td><?= $actv_mem['email'] ?></td>
                                                                                        <td><?= ucfirst($actv_mem['gender']); ?></td>
                                                                                        <td><?= $actv_mem['marriage_status'] ?></td>
                                                                                        <td width="25%"><?= date('d-m-Y', strtotime($actv_mem['dob'])); ?></td>
                                                                                        <td width="25%"><?= date('d-m-Y', strtotime($actv_mem['doa'])); ?></td>
                                                                                        
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
                                                            <div id="normal_user" class="tab-pane fade"><br>
                                                                <div class="table-responsive custom_table_area">
                                                                    <table class="table table-striped table-bordered dom-jQuery-events c_table_style">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>SL No.</th>                                                                                
                                                                                <th>Name</th>
                                                                                <th>Member Type</th>
                                                                                <th>Mobile</th>
                                                                                <th>Email</th>
                                                                                <th>Gender</th>
                                                                                <th>Marriage Status</th>
                                                                                <th width="25%">DOB</th>
                                                                                <th width="25%">DOA</th>
                                                                               
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                    <?php if (!empty($normal_mem_list)) { ?>
                                                                                    <?php     foreach ($normal_mem_list as $key => $inactv_mem) { ?>
                                                                                    <tr>
                                                                                        <td><?= $key + 1 ?></td>                                                                                        
                                                                                        <td><?= $inactv_mem['full_name'] ?></td>
                                                                                        <td><?= ucfirst($inactv_mem['package_name']); ?></td>
                                                                                        <td><?= $inactv_mem['mobile'] ?></td>
                                                                                        <td><?= $inactv_mem['email'] ?></td>
                                                                                        <td><?= ucfirst($inactv_mem['gender']); ?></td>
                                                                                        <td><?= $inactv_mem['marriage_status'] ?></td>
                                                                                        <td width="20%"><?= date('d-m-Y', strtotime($inactv_mem['dob'])); ?></td>
                                                                                        <td width="20%"><?= date('d-m-Y', strtotime($inactv_mem['doa'])); ?></td>
                                                                                        
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