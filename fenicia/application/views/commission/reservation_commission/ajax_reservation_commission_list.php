<div class="row">
  <div class="col-sm-10 offset-1">
    <div class="staff_tab_area">                                                        
      <div class="tab-content">
        <div id="" class="tab-pane active"><br>
          <div class="table-responsive custom_table_area">
            <table class="table table-striped table-bordered c_table_style export_btn_dt reservation_commission_list_table">
              <thead>
                  <tr>
                      <th>Sl No.</th>
                      <!--<th>Date</th>--> 
                      <th>Zone</th>                                                                              
                      <th>No. of bookings</th>                                                                                                     
                      <th>Commission</th>
                      <!--<th>Details</th>-->                                                                                
                  </tr>
              </thead>
              <tbody>                                                                                
                  <?php if (!empty($reservation_commission_list)) { ?>
                  <?php foreach ($reservation_commission_list as $key => $list) { ?>
                          <tr>
                              <td><?= $key + 1 ?></td>                                                                                        
                              <!--<td><?= date('d/m/Y', strtotime($list['reservation_date']));?></td>--> 
                              <td><?= $list['zone_name']; ?></td>                                                                                 
                              <td><a title="View" style="width:auto; height:auto;text-decoration: underline;" href="<?=base_url('commission/ReservationCommission/viewReservationDetails/'.$list['zone_id'])?>" class="btn_action edit_icon"><strong><?= $list['no_of_reservation']; ?></strong></a></td> 
                              <td><?= ($list['no_of_reservation']*100); ?></td>            
                              <!--<td class="action_td text-center" style="min-width:105px"><a title="View Details" style="width:auto; height:auto;" href="<?=base_url('commission/ReservationCommission/viewReservationDetails/'.$list['zone_id'])?>" class="btn_action edit_icon"><i class="fa fa-eye" aria-hidden="true"></i>View Details</a></td>-->
                          </tr>
                  <?php 
                  } } else { ?>
                      <tr>
                          <td colspan="4" style="text-align:center;">No Data Available</td>
                      </tr>

                <?php  } ?>
              </tbody>
              <tfoot style="background:#dde1e5;color:white">
                <tr>
                  <td></td>
                  <td style="text-align: center;"><strong>Total</strong></td>
                  <td><strong><?php echo $total_reservation_cnt; ?></strong>
                  </td>
                  <td><strong><?php echo $total_reservation_commission; ?></strong>
                  </td>
                </tr>
              <tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
