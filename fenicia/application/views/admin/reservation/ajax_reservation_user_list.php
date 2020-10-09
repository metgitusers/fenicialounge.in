<style type="text/css">
  .reservation_list_table thead th {
    padding-right: 19px !important;
}
</style>
<div class="row">
  <div class="table-responsive custom_table_area export_table_area">
    <table class="table table-striped table-bordered c_table_style export_btn_dt reservation_booking_user_table">
      <thead>
          <tr>
            <th class="border-top-0">SL No.</th>                            
            <th class="name_space border-top-0">Name</th>
            <th class="border-top-0">Email</th>
            <th class="border-top-0">Mobile</th>
            <th class="border-top-0">Date</th>
            <th class="border-top-0">Source</th>
          </tr>
      </thead>
      <tbody>
        <?php if (!empty($reservation_user_list)) { ?>
        <?php     foreach ($reservation_user_list as $key => $list) { ?>
        <tr>
            <td><?= $key + 1 ?></td>                                                                                                             
            <td class="name_space"><?= ucfirst($list['full_name']) ?></td>            
            <td><?php if(!empty($list['email'])){ echo '<i class="fa fa-envelope" aria-hidden="true"></i>'.$list['email']; } ?></td>
            <td><?php if(!empty($list['member_mobile'])){ echo '<i class="fa fa-phone-square" aria-hidden="true"></i> '.$list['country_code'].$list['member_mobile']; } ?></td>
            <td><?= date('l d M Y', strtotime($list['reservation_date'])); ?></td>
            <td><?= $list['reservation_type']; ?></td>            
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
 
  	</script>