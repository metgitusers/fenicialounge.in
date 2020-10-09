<div class="row">
  <div class="table-responsive custom_table_area export_table_area">
    <table class="table table-striped table-bordered export_btn_dt c_table_style reservation_report_table">
      <thead>
          <tr>
              <th class="border-top-0">SL No.</th>
              <th class="border-top-0">Member Details</th>
              <th class="border-top-0">Reservation Date</th>
              <th class="border-top-0">Reservation Time</th>
              <th class="border-top-0">Zone</th>
              <th class="border-top-0">Cover Charge</th>
              <th class="border-top-0">Number of Guests</th>
              <th class="border-top-0">Status</th>
          </tr>
      </thead>
      <tbody>
          <?php if (!empty($reservation_list)) { ?>
          <?php     foreach ($reservation_list as $key => $revlist) { ?>
          <tr>
              <td><?= $key + 1 ?></td>                                                                                        
              <td><a href="<?php echo base_url().'admin/member/details/'.$revlist['member_id']; ?>"><?= ucfirst($revlist['full_name']) ?></a><br>
                  <?= '<i class="fa fa-phone-square" aria-hidden="true"></i> '.$revlist['country_code'].$revlist['member_mobile'] ?><br>
                  <?= '<i class="fa fa-envelope" aria-hidden="true"></i>'.$revlist['email'];?>   
              </td>                                                     
              <td width="25%"><?= date('l d M Y', strtotime($revlist['reservation_date'])); ?></td>  
              <td><?= date('h:i A',strtotime($revlist['reservation_time'])); ?></td>
              <td class="name_space"><?= ucfirst($revlist['zone_name']); ?></td>
              <td><?= $revlist['zone_price']; ?></td>
              <td><?= $revlist['no_of_guests']; ?></td>
              <?php   if($revlist['resv_status'] !=''):
                          if($revlist['resv_status'] == 0):
                              $resv_status  = 'Cancelled';
                              $class        ="red";
                          elseif($revlist['resv_status'] == 1):
                              $resv_status  = 'Pending';
                              $class ="orange";
                          elseif($revlist['resv_status'] == 2):
                              $resv_status  = 'Reserved';
                              $class ="green";
                          else:
                              $resv_status  = 'Rejected';
                              $class ="#b30000";
                          endif;
                      endif;                                                                                           
              ?> 
              <td><button class="btn" style="background-color:<?php echo $class; ?>;color:#fff!important;pointer-events: none;" ><?php echo $resv_status; ?></button></td>        
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