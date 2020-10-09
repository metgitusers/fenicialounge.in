<style type="text/css">
  .reservation_list_table thead th {
    padding-right: 19px !important;
}
</style>
<div class="row">
  <div class="table-responsive custom_table_area export_table_area">
    <table class="table table-striped table-bordered c_table_style export_btn_dt reservation_list_table">
      <thead>
          <tr>
              <th class="border-top-0">SL No.</th>
              <th class="border-top-0">Rev. No.</th>              
              <th class="name_space border-top-0">Guest Details</th>
              <th class="border-top-0">Date</th>
              <th class="border-top-0">Time</th>
              <th class="border-top-0">Zone</th>
              <th class="border-top-0">No. Of Guests</th>
              <th class="border-top-0">Message</th>
              <th class="border-top-0">Source</th>              
              <th class="border-top-0">Cover Charge</th>             
              <th class="border-top-0">Status</th>              
              <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
              <!--<th class="action_bttn border-top-0" style="min-width:105px" <?php echo $actrion_visibility; ?>>Action</th>-->
              <th class="action_bttn border-top-0" style="min-width:105px" <?php echo $actrion_visibility; ?>>Action</th>
              <!--<th class="action_bttn border-top-0" style="min-width:105px" <?php echo $actrion_visibility; ?>>Check In / Out Status</th>-->
              <th class="border-top-0">Remark</th>
              <th class="action_bttn border-top-0" style="min-width:105px">Log</th>
          </tr>
      </thead>
      <tbody>
        <?php if (!empty($reservation_list)) { ?>
        <?php     foreach ($reservation_list as $key => $list) { ?>
        <tr>
            <td><?= $key + 1 ?></td>
            <?php if(($list['resv_status'] == 1 || $list['resv_status'] == 2) && ($list['check_in_out_status'] == 2 || $list['check_in_out_status'] == 1) ){ ?>
                    <td><a style="text-decoration: underline;" href="<?=base_url('admin/reservation/edit/'.$list['reservation_id'])?>" ><?= $list['reservation_id']; ?></a></td> 
            <?php }else{ ?>
                    <td><a style="text-decoration: underline;" href="<?=base_url('admin/reservation/ViewDetails/'.$list['reservation_id'])?>" ><?= $list['reservation_id']; ?></a></td>
            <?php  } ?>                                                                                                  
            <td class="name_space"><?= ucfirst($list['full_name']) ?><br>
                <?php if(!empty($list['member_mobile'])){ echo '<i class="fa fa-phone-square" aria-hidden="true"></i> '.$list['country_code'].$list['member_mobile']; } ?><br>
                <?php if(!empty($list['email'])){ echo '<i class="fa fa-envelope" aria-hidden="true"></i>'.$list['email']; } ?>   
            </td>                                                                                  
            <td width="55%"><?= date('l d M Y', strtotime($list['reservation_date'])); ?></td>  
            <td><?= date('h:i A',strtotime($list['reservation_time'])); ?></td>
            <?php if (strpos($list['zone_name'], '|') >= 0) { 
  
                    $tmp_zone_name  = explode('|', $list['zone_name']);
                    $zone_name      = $tmp_zone_name[0];
                  }
                  else{
                    
                    $zone_name = $list['zone_name'];
                  }
            ?> 
            <td class="name_space"><?= ucfirst($zone_name); ?></td>
            <td><?= $list['no_of_guests']; ?></td>
            <td><?= $list['message']; ?></td>  
            <td><?= $list['reservation_type']; ?></td>            
            <td><?= $list['zone_price']; ?></td>             
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
                                $resv_status   = "No-show";
                            endif;
                        endif;                                                                                           
                ?>         
                    <a class="btn" style="font-size:10px;background-color:<?php echo $class;?>;color:#fff;pointer-events: none;cursor: default;text-decoration: none;margin:6px;" href=""><?php echo $resv_status; ?></a>
                    
            </td>            
           <!-- <td class="action_bttn action_td text-center" style="min-width:105px">
                <div class="form-group">                                                                                                                                                                                             
                    <select id="rev_status" style="color:white;background-color:<?php echo $class; ?>" data-url="<?php echo base_url().'admin/reservation/changeStatus'; ?>" data-id="<?php echo $list['reservation_id']; ?>" class="form-control " required>
                        <option value="">Select</option>
                        <option style="background-color: red;color:white" value="0" <?php if($list['resv_status'] =='0'): echo "selected";endif; ?>>Cancelled</option>
                        <option style="background-color: green;color:white" value="2" <?php if($list['resv_status'] =='2'): echo "selected";endif; ?>>Reserved</option>                                                                                            
                        <option style="background-color: #b30000;color:white" value="3" <?php if($list['resv_status'] =='3'): echo "selected";endif; ?>>No-show</option>
                    </select>
                </div>                                                                               
            </td> -->           
            <td class="action_bttn action_td text-center">
                <?php if(($list['resv_status'] == 1 || $list['resv_status'] == 2) && ($list['check_in_out_status'] == 2 || $list['check_in_out_status'] == 1) ){ ?>
                        <a title="Edit" href="<?=base_url('admin/reservation/edit/'.$list['reservation_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                <?php } ?>                
            </td>                
            <td><?php if($list['resv_status'] == '0' || $list['resv_status'] == '3'){ echo $list['cancellation_reason']; }  ?></td>
            <td class="action_td text-center"><a title="Log" class="btn_action edit_icon log_view" data-column="reservation_id" data-title="Reservation" data-id="<?= $list['reservation_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a>
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
<script>
$(document).ready(function() {
    var menu_id = $("#menu_id").val();
	//alert(menu_id);
 	$.ajax({
        type: "POST",
        url: '<?php echo base_url('admin/index/Ck_User_Permission/')?>',
        data:{menu_id:menu_id},
        dataType:'json',
        success: function(response){   
        //alert(response);           
          //$('#modalContent').html(response.message);  
          //$('#myModal').modal('show');
       
	      	if(response['add_flag'] =='0'){
			    $(".add_bttn").remove();
			    $(".rev_status").css('display','none');
			    $(".action_bttn").css('display','none');
			}   
			if(response['edit_flag'] =='0'){
			    $(".edit_bttn").remove();
			    $(".action_bttn").css('display','none');
			}   
			if(response['view_flag'] =='0'){
			    $(".delete_bttn").remove();
			}   
			if(response['download_flag'] =='0'){
			    $(".download_bttn").remove();
			}       
        },
        error:function(response){
          
        }
  	});
    //var check_in_out_status = $(".check_in_out_status:checked").val();
    //alert(check_in_out_status);
 });
  $(document).on('change','.check_in_out_status',function(){     
      var current_obj         = $(this);
      var check_in_out_status = $(this).val();
      var reservation_id      = $(this).data('id');
      var url                 = $(this).data('href');
      $.ajax({
      type: "POST",
      url:  url,
      data: {status:check_in_out_status,reservation_id:reservation_id},
      dataType:'html',
      success: function(response){
        //alert(response);
        if(response == 1){
          $.alert({
           type: 'green',
           title: 'Alert!',
           content: 'Successfully check in/out status changed.',
          });
          setTimeout(function(){
              window.location.reload();
          },1800);
        }
        else{
          $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Opp! some problem ,please try again',
          }); 
        } 
      },
      error:function(response){          
         $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'error',
          });                    
      }
  });
  })
  	</script>