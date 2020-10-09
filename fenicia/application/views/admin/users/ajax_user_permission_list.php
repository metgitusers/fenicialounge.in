<table class="table table-striped table-bordered c_table_style">                                                
      <thead>
          <tr>
              <th>Menu Name</th>
              <th>Add</th>
              <th>Edit</th>
              <th>View</th>
              <!--<th>Download</th>-->
              <th class="action_sec">Action</th>
          </tr>
      </thead>
      <tbody>
          <?php if(!empty($menu_data)): ?>            
            <?php if(count($menu_data) >1):?>
              <?php  $cnt_add_chk     = 0; ?>
              <?php  $cnt_edit_chk    = 0; ?>
              <?php  $cnt_view_chk  = 0; ?>              
              <?php foreach($menu_data as $val): ?>              
              <?php   if(!empty($user_permission_data) && array_key_exists($val['menu_id'], $user_permission_data) && !empty($user_permission_data[$val['menu_id']])): ?>  
              <?php     if(empty($user_permission_data[$val['menu_id']]) || $user_permission_data[$val['menu_id']]['add_flag'] != '1'): ?> 
              <?php       $cnt_add_chk++;    ?>
              <?php     endif; ?>
              <?php     if($user_permission_data[$val['menu_id']]['edit_flag'] != '1'): ?>
              <?php       $cnt_edit_chk++;    ?>
              <?php     endif; ?>
              <?php     if($user_permission_data[$val['menu_id']]['view_flag'] != '1'): ?>
              <?php       $cnt_view_chk++; ?>
              <?php     endif; ?>
              <?php   else: ?>
               <?php    $cnt_add_chk     = 1; ?>
              <?php     $cnt_edit_chk    = 1; ?>
              <?php     $cnt_view_chk  = 1; ?>    
              <?php   endif; ?>
              <?php endforeach; ?>
                              
              <tr>
                  <td><input type="hidden" name="role_id" id="role_id" value="<?php if(!empty($role_id)): echo $role_id;endif; ?>"></td>
                  <td class=""><input type="checkbox" id="add_all" <?php if($cnt_add_chk == 0): echo 'checked'; endif; ?>/>Select All</td>
                  <td class=""><input type="checkbox" id="edit_all" <?php if($cnt_edit_chk == 0): echo 'checked'; endif;?> />Select All</td>
                  <td class=""><input type="checkbox" id="view_all" <?php if($cnt_view_chk == 0): echo 'checked'; endif;?>/>Select All</td>
                  <!--<td class=""><input type="checkbox" id="download_all"/>Select All</td>-->
                  <td class="action_sec"><button class="btn btn-save-all"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save all</button></td>
              </tr>
            <?php endif; ?>
              <?php foreach($menu_data as $list): ?>
                      <?php  $add_chk[]       = ''; ?>
                      <?php  $edit_chk[]      = ''; ?>
                      <?php  $view_chk[]    = ''; ?>
                      <?php  $download_chk[]  = ''; ?>
                      <?php  $permission_id[]  = ''; ?>
                      <?php if(!empty($user_permission_data) && array_key_exists($list['menu_id'], $user_permission_data) && !empty($user_permission_data[$list['menu_id']])): ?>  
                            <?php if($user_permission_data[$list['menu_id']]['add_flag'] == '1'): ?> 
                                    <?php $add_chk[$list['menu_id']] = 'checked'; ?>
                            <?php else: ?>
                                    <?php $add_chk[$list['menu_id']] = '';  ?>
                            <?php endif; ?>
                            <?php if($user_permission_data[$list['menu_id']]['edit_flag'] == '1'): ?>
                                    <?php $edit_chk[$list['menu_id']] = 'checked'; ?>
                            <?php else: ?>
                                    <?php $edit_chk[$list['menu_id']] = ''; ?>
                            <?php endif; ?>
                            <?php if($user_permission_data[$list['menu_id']]['view_flag'] == '1'): ?>
                                    <?php $view_chk[$list['menu_id']] = 'checked'; ?>
                            <?php else: ?>
                                    <?php $view_chk[$list['menu_id']] = ''; ?>
                            <?php endif; ?>        
                            <?php if($user_permission_data[$list['menu_id']]['download_flag'] == '1'): ?>
                                    <?php $download_chk[$list['menu_id']] = 'checked'; ?> 
                            <?php else: ?>
                                    <?php $download_chk[$list['menu_id']] = ''; ?>
                            <?php endif; ?>
                      <?php else: ?>
                            <?php  $add_chk[$list['menu_id']]       = ''; ?>
                            <?php  $edit_chk[$list['menu_id']]      = ''; ?>
                            <?php  $view_chk[$list['menu_id']]    = ''; ?>
                            <?php  $download_chk[$list['menu_id']]  = ''; ?>
                            <?php endif; ?>

                      <?php if(!empty($user_permission_data) && array_key_exists($list['menu_id'], $user_permission_data) && !empty($user_permission_data[$list['menu_id']])): ?>
                              <?php $permission_id[$list['menu_id']] = $user_permission_data[$list['menu_id']]['permission_id']; ?> 
                      <?php else: ?>
                              <?php $permission_id[$list['menu_id']] = ''; ?>
                      <?php endif; ?>
                  <tr>
                      <input type="hidden" name="parent_id[<?php echo $list['menu_id'];?>]" id="parent_id_<?php echo $list['menu_id'];?>" value="<?php echo $list['parent_id']; ?>">
                      <td id="menu_name<?php echo $list['menu_id'];?>"><?php echo $list['menu_name']; ?></td>
                      <td><input type="checkbox" class="add_bttn" name="add[<?php echo $list['menu_id'];?>]"  value="1" id="add_actn_<?php echo $list['menu_id'];?>" <?php echo $add_chk[$list['menu_id']]; ?> />Yes</td>
                      <td><input type="checkbox" class="edit_bttn" name="edit[<?php echo $list['menu_id'];?>]" value="1" id="edit_actn_<?php echo $list['menu_id'];?>" <?php echo $edit_chk[$list['menu_id']]; ?>/>Yes</td>
                      <td><input type="checkbox" class="view_bttn" name="view[<?php echo $list['menu_id'];?>]" value="1" id="view_actn_<?php echo $list['menu_id'];?>" <?php echo $view_chk[$list['menu_id']]; ?>/>Yes</td>
                      <!--<td><input type="checkbox" class="download_bttn" name="download[<?php echo $list['menu_id'];?>]" value="1" id="download_actn_<?php echo $list['menu_id'];?>" <?php echo $download_chk[$list['menu_id']];?>/>Yes</td>-->
                      <td class="action_sec"><button class="btn btn-primary save_actn" title='<?php echo $permission_id[$list['menu_id']]; ?>' id="<?php echo $list['menu_id'];?>"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button></td>
                  </tr>
              <?php endforeach; ?>
          <?php else: ?>        
              <tr>
                  <td colspan="6">No menu found</td>
              </tr>
          <?php endif; ?>
      </tbody>
  </table>
<script>
 $(document).on('click','#add_all',function(){  
    if(this.checked){
        $('.add_bttn').each(function(){
            this.checked = true;
        });
    }else{
         $('.add_bttn').each(function(){
            this.checked = false;
        });
    }
   //$('.add_bttn').prop('checked', true);
  });
  $(document).on('click','#edit_all',function(){
   $('.edit_bttn').not(this).prop('checked', this.checked);
  });
  $(document).on('click','#view_all',function(){
   $('.view_bttn').not(this).prop('checked', this.checked);
  });
  $(document).on('click','#download_all',function(){
   $('.download_bttn').not(this).prop('checked', this.checked);
  });
</script>