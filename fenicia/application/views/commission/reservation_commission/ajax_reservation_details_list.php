<div class="col-sm-12">
  <div class="staff_tab_area">                                                        
    <div class="tab-content">
      <div id="" class="tab-pane active"><br>
        <div class="table-responsive custom_table_area">
          <table class="table table-striped table-bordered c_table_style export_btn_dt reservation_details_table">
            <thead>
                <tr>
                    <th class="border-top-0">SL No.</th>
                    <th class="border-top-0">Rev. No.</th>
                    <th class="border-top-0">Guest Details</th>
                    <th class="border-top-0">Date</th>
                    <th class="border-top-0">Time</th>
                    <th class="border-top-0">Zone</th>
                    <th class="border-top-0">Cover Price</th>
                    <th class="border-top-0">No. of Guests</th>
                    <th class="border-top-0">Source</th>
                    <th class="border-top-0">Status</th>                                                                               
                </tr>
            </thead>
            <tbody>                                                                                
                <?php if (!empty($reservation_data)){ ?>
                <?php     foreach ($reservation_data as $key => $list) { ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= $list['reservation_id'] ?></td>                                                                                        
                    <td class="name_space"><?= ucfirst($list['full_name']) ?><br>
                        <?php if(!empty($list['member_mobile'])){ echo '<i class="fa fa-phone-square" aria-hidden="true"></i> '.$list['country_code'].$list['member_mobile']; } ?><br>
                        <?php if(!empty($list['email'])){ echo '<i class="fa fa-envelope" aria-hidden="true"></i>'.$list['email']; }?>   
                    </td>                                                                                  
                    <td><?= date('d/m/Y', strtotime($list['reservation_date'])); ?></td>  
                    <td><?= date('h:i A',strtotime($list['reservation_time'])); ?></td>
                    <td><?= ucfirst($list['zone_name']); ?></td>
                    <td><?= $list['zone_price']; ?></td> 
                    <td><?= $list['no_of_guests']; ?></td>
                    <td><?= $list['reservation_type']; ?></td> 
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
                                        $resv_status   = "Confirm";
                                    else:
                                        $class ="#b30000";
                                        $resv_status   = "No-show";
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
