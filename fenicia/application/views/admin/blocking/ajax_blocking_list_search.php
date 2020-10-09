<style>
tr.with_dt td {
    background: #dee2e6;
    color: #2aa0f3;
}
</style>
<div class="row" style="margin-top:15px">
  <div class="table-responsive custom_table_area export_table_area">
    <table style="border-bottom: 1px solid rgba(0, 0, 0, 0.125);" class="table table-striped table-bordered c_table_style export_btn_dt reservation_blocking_list_table">
      <thead>
          <tr>
              <th class="name_space border-top-0"></th>
              <th class="name_space border-top-0">Time</th>
              <th class="border-top-0">Zone</th> 
              <th class="border-top-0">Status</th>             
          </tr>
      </thead>
      <tbody>
        <?php if(count($blocking_list) != 0) { ?>
        <?php   foreach ($blocking_list as $key => $dt_list) { 
                  foreach ($dt_list as $dt => $val_list) {   ?>
                    <tr class="with_dt">                                                                                      
                        <td><strong><?php echo date('d/m/Y',strtotime($dt)); ?></strong></td> 
                        <td colspan="3"></td>                       
                    </tr>
        <?php       foreach($val_list as $val){ ?>
                      <tr> 
                        <td></td>                                                                                     
                        <td><?php echo date('h:i A',strtotime($val['blocking_time'])); ?></td>
                        <td><?php echo $val['zone_name']; ?></td>
                        <td><a class="btn btn-danger mr-1" style="pointer-events: none;cursor: default;text-decoration: none;color: black;">Blocked</a></td>                         
                      </tr>
        <?php       } 
                  } 
                }
              } 
              else { ?>
                  <tr>
                      <td colspan="4" style="text-align:center;">No Data Available</td>
                  </tr>

    <?php     } ?>

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
 });
  	</script>