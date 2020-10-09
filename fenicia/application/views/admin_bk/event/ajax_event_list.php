<div id="active_user" class="tab-pane active"><br>
    <div class="table-responsive custom_table_area export_table_area">
        <table class="table table-striped table-bordered export_btn_dt c_table_style reservation_list_table">
            <thead>
                <tr>
                    <th>SL No.</th>                                                                                
                    <th>Event Details</th>
                    <!--<th>Location</th>-->
                    <th width="12%">Event Date</th>
                    <th width="12%">Event Time</th>
                    <th>Status</th>
                    <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
                    <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($event_active_list)) { ?>
                <?php     foreach ($event_active_list as $key => $actv_ent) { ?>
                <tr>
                    <td><?= $key + 1 ?></td>                                                                                        
                    <td><?= '<p><strong>'.ucfirst($actv_ent['event_name']).'</strong></p>'.$actv_ent['event_location']; ?></td>
                    <td width="12%"><?= date('d-m-Y',strtotime($actv_ent['event_start_date'])) ?></td>
                    <td width="12%"><?= date('h:i A',strtotime($actv_ent['event_start_time'])) ?></td>
                    <td class="action_td text-center">
                        <a title="Inactive" class="btn_action edit_icon inactive_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $actv_ent['event_id'];?>" href="<?=base_url('admin/event/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                    </td>                                                                                      
                    <td class="action_bttn action_td text-center">
                        <a title="Inactive" class="edit_bttn btn_action btn-warning active_btn make_inactive" data-id="<?php echo $actv_ent['event_id'];?>" href="<?=base_url('admin/event/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                        <a title="Edit" href="<?=base_url('admin/event/edit/'.$actv_ent['event_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <!--<a title="Delete" href="<?=base_url('admin/event/DeleteEvent/'.$actv_ent['event_id'])?>" class="delete_bttn btn_action btn-danger delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                    </td>
                    
                </tr>
                <?php 
                } } else { ?>
                <tr>
                    <td colspan="17" style="text-align:center;">No Data Available</td>
                </tr>

                <?php  } ?>
            </tbody>
        </table>
    </div>
</div>
<div id="inactive_user" class="tab-pane fade"><br>
    <div class="table-responsive custom_table_area">
        <table class="table table-striped table-bordered dom-jQuery-events c_table_style reservation_list_table">
            <thead>
                <tr>
                    <th>SL No.</th>                                                                                
                    <th>Event Details</th>
                    <!--<th>Location</th>-->
                    <th width="12%">Event Date</th>
                    <th width="12%">Event Time</th>
                    <th>Status</th>
                    <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
                    <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                </tr>
            </thead>
            <tbody>
                        <?php if (!empty($event_inactive_list)) { ?>
                        <?php     foreach ($event_inactive_list as $key => $inactv_evn) { ?>
                        <tr>
                            <td><?= $key + 1 ?></td>                                                                                        
                            <td><?= '<p><strong>'.ucfirst($inactv_evn['event_name']).'</strong></p>'.$inactv_evn['event_location']; ?></td>
                            <td  width="12%"><?= date('d-m-Y',strtotime($inactv_evn['event_start_date'])) ?></td>
                            <td width="12%"><?= date('h:i A',strtotime($inactv_evn['event_start_time'])) ?></td>
                            <td class="action_td text-center">
                                <a title="Active" class="btn_action btn-warning active_btn" data-id="<?php echo $inactv_evn['event_id'];?>" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" href="<?=base_url('admin/event/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                            </td>                                                                                      
                            <td class="action_bttn action_td text-center">
                                <a title="Active" class="edit_bttn btn_action edit_icon inactive_btn make_active" data-id="<?php echo $inactv_evn['event_id'];?>" href="<?=base_url('admin/event/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                <a title="Edit" href="<?=base_url('admin/event/edit/'.$inactv_evn['event_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                <!--<a title="Delete" href="<?=base_url('admin/event/DeleteEvent/'.$inactv_evn['event_id'])?>" class="delete_bttn btn_action btn-danger delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                            </td>
                            
                        </tr>
                    <?php 
                    } } else { ?>
                    <tr>
                        <td colspan="17" style="text-align:center;">No Data Available</td>
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
 $('a.inactive_btn').confirm({    
    title: "confirm Activation",    
    content: "Are you sure want to active?",  
     buttons: {
        Activate: {
            btnClass: 'btn-green',
            action: function(){
              var id = this.$target.data('id');
              //alert(id );        
              $.ajax({
                type: "POST",
                url: this.$target.attr('href'),
                data: {id:id,change_status:1},
                dataType:'html',
                success: function(response){
                  //alert(response);
                  if(response == 1){
                    $.alert({
                     type: 'green',
                     title: 'Alert!',
                     content: 'Successfully Activated.',
                    });
                    setTimeout(function(){
                        window.location.reload();
                    },1400);                   
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
            }
        },         
        Close: {
            btnClass: 'btn-default',
            action: function(){
              
            }
        },  
       }
});
$('a.active_btn').confirm({    
    title: "confirm Deactive",    
    content: "Are you sure want to deactive?",  
     buttons: {
        Deactive: {
            btnClass: 'btn-red',
            action: function(){
              var id = this.$target.data('id');
             //alert(id );        
              $.ajax({
                type: "POST",
                url: this.$target.attr('href'),
                data: {id:id,change_status:0},
                dataType:'html',
                success: function(response){
                 //alert(response);
                  if(response == 1){
                    $.alert({
                     type: 'green',
                     title: 'Alert!',
                     content: 'Successfully Deactivated.',
                    }); 
                    setTimeout(function(){
                        window.location.reload();
                    },1400);
                    
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
            }
        },         
        Close: {
            btnClass: 'btn-default',
            action: function(){
              
            }
        },  
       }
});
  	</script>