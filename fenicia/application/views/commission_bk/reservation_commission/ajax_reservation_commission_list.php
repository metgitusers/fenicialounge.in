<div class="row">
  <div class="col-sm-12">
    <div class="staff_tab_area">                                                        
      <div class="tab-content">
        <div id="" class="tab-pane active"><br>
          <div class="table-responsive custom_table_area">
            <table class="table table-striped table-bordered dom-jQuery-events c_table_style">
              <thead>
                  <tr>
                      <th>Sl No.</th>
                      <th>Date</th>                                                                                
                      <th>No. of Reservations</th>
                      <th>zone</th>                                                                                
                      <th>Commission(100 x No of Reservations)</th>
                      <th>Action</th>                                                                                
                  </tr>
              </thead>
              <tbody>                                                                                
                  <?php if (!empty($reservation_commission_list)) { ?>
        <?php     foreach ($reservation_commission_list as $key => $list) { ?>
        <tr>
            <td><?= $key + 1 ?></td>                                                                                        
            <td><?= date('d/m/Y', strtotime($list['reservation_date']));?></td>                                                                                  
            <td><?= $list['no_of_reservation']; ?></td> 
            <td><?= $list['zone_name']; ?></td> 
            <td><?= ($list['no_of_reservation']*100); ?></td>            
            <td class="action_td text-center" style="min-width:105px"><a title="Edit" style="width:auto; height:auto;" href="<?=base_url('commission/ReservationCommission/viewReservationDetails/'.$list['reservation_date'].'/'.$list['zone_id'])?>" class="btn_action edit_icon"><i class="fa fa-eye" aria-hidden="true"></i>View Details</a></td>
        </tr>
            <?php 
            } } else { ?>
                <tr>
                    <td colspan="13" style="text-align:center;">No Data Available</td>
                </tr>

          <?php  } ?>
              </tbody>
              <tfoot style="background:#43839f"><tr><td colspan="4"><strong>Total Commission(₹)</strong></td><td colspan="2"><strong><?php echo $total_reservation_commission; ?></strong></td></tr><tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
