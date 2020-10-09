<div id="active_user" class="tab-pane active"><br>
    <div class="table-responsive custom_table_area export_table_area">
        <table class="table table-striped table-bordered export_btn_dt c_table_style pckg_purchased_report_table">
            <thead>
                <tr>
                    <th>SL No.</th>
                    <th>Membership Id</th>                                                                               
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Membership Name</th>
                    <th>Registered on</th>
                    <th>Membership type</th>
                    <th>Source</th>
                    <th>Expiry Date</th>                                                                               
                    <!-- <th>Status</th> -->
                </tr>
            </thead>
              <tbody>
                    <?php if (!empty($active_membership_list)) { ?>
                    <?php     foreach ($active_membership_list as $key => $actv_mem) { ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= $actv_mem['membership_id'] ?></td>                                                                                       
                        <td class="name_space"><?= ucfirst($actv_mem['full_name'])."<br>DOB: ".date('d/m/Y', strtotime($actv_mem['dob'])) ?></td>
                        <td><?= $actv_mem['mobile'] ?></td>
                        <td><?= $actv_mem['email'] ?></td>
                        <td><?= $actv_mem['package_name'] ?></td>
                        <td><?= date('d/m/Y', strtotime($actv_mem['buy_on'])) ?></td>
                        <td><?= ucfirst($actv_mem['package_type_name']) ?></td>
                        <?php if($actv_mem['added_form'] == "admin"): 
                                $added_form  = 'Offline';
                              elseif($actv_mem['added_form'] == "front"): 
                                $added_form  = 'App';
                              else: 
                                $added_form  = 'Web';
                              endif; 
                        ?>
                        <td><?= $added_form; ?></td>
                        <?php if($actv_mem['expiry_date'] !='0000-00-00'): $expiry_date =  date('d/m/Y', strtotime($actv_mem['expiry_date']));else: $expiry_date =''; endif;?>
                        <td><?= $expiry_date ?></td>                                                                                    
                        <!-- <td class="action_td text-center">
                            <button class="btn" style="pointer-events: none;background-color: #28D094">Active</button>
                        </td> -->
                    </tr>
                <?php 
                } } else { ?>
                  <tr>
                      <td colspan="10" style="text-align:center;">No Data Available</td>
                  </tr>

            <?php  } ?>
            </tbody>
        </table>
    </div>
</div>
<div id="inactive_user" class="tab-pane fade"><br>
    <div class="table-responsive custom_table_area">
        <table class="table table-striped table-bordered export_btn_dt c_table_style pckg_purchased_report_table">
            <thead>
                <tr>
                    <th>SL No.</th>
                    <th>Membership Id</th>                                                                               
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Membership Name</th>
                    <th>Registered on</th>
                    <th>Membership type</th>
                    <th>Source</th>
                    <th>Expiry Date</th>                                                                               
                    <!-- <th>Status</th> -->
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($inactive_membership_list)) { ?>
                <?php     foreach ($inactive_membership_list as $key => $inactv_mem) { ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= $inactv_mem['membership_id'] ?></td>                                                                                        
                    <td class="name_space"><?= ucfirst($inactv_mem['full_name'])."<br>DOB: ".date('d/m/Y', strtotime($inactv_mem['dob'])) ?></td>
                    <td><?= $inactv_mem['mobile'] ?></td>
                    <td><?= $inactv_mem['email'] ?></td>
                    <td><?= $inactv_mem['package_name'] ?></td>
                    <td><?= date('d/m/Y', strtotime($inactv_mem['buy_on'])) ?></td>
                    <td><?= ucfirst($inactv_mem['package_type_name']) ?></td>
                    <?php if($actv_mem['added_form'] == "admin"): 
                            $added_form  = 'Offline';
                          elseif($actv_mem['added_form'] == "front"): 
                            $added_form  = 'App';
                          else: 
                            $added_form  = 'Web';
                          endif; 
                    ?>
                    <td><?= $added_form; ?></td>
                    <?php if($inactv_mem['expiry_date'] !='0000-00-00'): $expiry_date =  date('d/m/Y', strtotime($inactv_mem['expiry_date']));else: $expiry_date =''; endif;?>
                    <td><?= $expiry_date ?></td>                                                                                    
                    <!-- <td class="action_td text-center">
                        <button class="btn btn-warning" style="pointer-events: none;">Inactive</button>
                    </td> -->
                </tr>
            <?php 
            } } else { ?>
              <tr>
                  <td colspan="10" style="text-align:center;">No Data Available</td>
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