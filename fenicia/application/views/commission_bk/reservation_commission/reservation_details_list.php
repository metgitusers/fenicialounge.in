<div>
    <div>
        <div class="container-fluid">            
            <!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="page-title-wrap">
                                    <h4 class="card-title">Reservation List</h4>
                                    <a class="title_btn t_btn_list" href="<?= base_url(); ?>commission/ReservationCommission"><span><i class="fa fa-plus" aria-hidden="true"></i></span> Back</a>    
                                </div>
                            </div>
                            <div class="card-body">
                              <div class="px-3">
                                <div class="form-body">
                                  <div class="row">
                                    <div class="col-sm-12">
                                      <div class="staff_tab_area">                                                        
                                        <div class="tab-content">
                                          <div id="" class="tab-pane active"><br>
                                            <div class="table-responsive custom_table_area">
                                              <table class="table table-striped table-bordered dom-jQuery-events c_table_style">
                                                <thead>
                                                    <tr>
                                                        <th class="border-top-0">SL No.</th>
                                                        <th class="border-top-0">Member Details</th>
                                                        <th class="border-top-0">Reservation Date</th>
                                                        <th class="border-top-0">Reservation Time</th>
                                                        <th class="border-top-0">Zone</th>
                                                        <th class="border-top-0">Cover Price</th>
                                                        <th class="border-top-0">No. of Guests</th>
                                                        <th class="border-top-0">Status</th>                                                                               
                                                    </tr>
                                                </thead>
                                                <tbody>                                                                                
                                                    <?php if (!empty($reservation_data)){ ?>
                                                    <?php     foreach ($reservation_data as $key => $list) { ?>
                                                    <tr>
                                                        <td><?= $key + 1 ?></td>                                                                                        
                                                        <td class="name_space"><?= ucfirst($list['full_name']) ?><br>
                                                            <?= '<i class="fa fa-phone-square" aria-hidden="true"></i> '.$list['country_code'].$list['member_mobile'] ?><br>
                                                            <?= '<i class="fa fa-envelope" aria-hidden="true"></i>'.$list['email'];?>   
                                                        </td>                                                                                  
                                                        <td><?= date('d/m/Y', strtotime($list['reservation_date'])); ?></td>  
                                                        <td><?= date('h:i A',strtotime($list['reservation_time'])); ?></td>
                                                        <td><?= ucfirst($list['zone_name']); ?></td>
                                                        <td><?= $list['zone_price']; ?></td> 
                                                        <td><?= $list['no_of_guests']; ?></td> 
                                                        <td>
                                                            <?php   if($list['resv_status']!=''):
                                                                        if($list['resv_status'] == 0):
                                                                            $class ="red";
                                                                            $resv_status   = "Cancelled";
                                                                        elseif($list['resv_status'] == 1):
                                                                            $class ="orange";
                                                                            $resv_status   = "Pending";
                                                                        elseif($list['resv_status'] == 2):
                                                                            $class ="green";
                                                                            $resv_status   = "Reserved";
                                                                        else:
                                                                            $class ="#b30000";
                                                                            $resv_status   = "Rejected";
                                                                        endif;
                                                                    endif;                                                                                           
                                                            ?>         
                                                                <a class="btn" style="background-color:<?php echo $class;?>;color:#fff;pointer-events: none;cursor: default;text-decoration: none;margin:6px;" href=""><?php echo $resv_status; ?></a>
                                                                
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