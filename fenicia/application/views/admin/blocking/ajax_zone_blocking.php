<div class="row" style="margin-top:15px">
  <div class="table-responsive custom_table_area export_table_area">
    <table class="table table-striped table-bordered c_table_style export_btn_dt reservation_blocking_list_table">
      <thead>
          <tr>
              <th class="name_space border-top-0">Zone Name</th>
              <th class="border-top-0">Status</th>
              <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
              <th class="action_bttn border-top-0" style="min-width:105px" <?php echo $actrion_visibility; ?>>Action</th>
          </tr>
      </thead>
      <tbody>
        <?php if(!empty($reservation_data)) { ?>
        <?php     foreach ($reservation_data as $key => $list) { ?>
        <tr>                                                                                      
            <td><?php echo ucfirst($key); ?></td>
            <?php 
              if($list ==''){
                  $status ='Available';
                  $block_bttn = '<a title="Do Block" style="width:auto; height:auto;front-size:20px" data-name="'.$key.'" class="edit_bttn btn btn-success do_block"><strong>Do Block</strong></a>';
              } 
              else{
                if($list['status'] =='Blocked'){
                  $status = $list['status'];
                }
                else{
                  $status = 'Reserved';
                }
                  
                $block_bttn = '';
              }
            ?>
            <td><?php echo $status; ?></td>
            <td><?php echo $block_bttn; ?></td>
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
 });
  	</script>