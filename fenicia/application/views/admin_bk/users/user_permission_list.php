<div class="main-content">
    <div class="content-wrapper">
        <div class="container-fluid">
        <!-- Basic form layout section start -->
          <?php if ($this->session->flashdata('success_msg')) : ?>
            <div class="alert alert-success">
              <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
              <?php echo $this->session->flashdata('success_msg') ?>
            </div>
          <?php endif ?>
          <?php if ($this->session->flashdata('error_msg')) : ?>
            <div class="alert alert-danger">
              <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
              <?php echo $this->session->flashdata('error_msg') ?>
            </div>
          <?php endif ?>
          <section id="basic-form-layouts">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="page-title-wrap">
                      <h4 class="card-title">User Permission Management</h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="px-3">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="card">
                            <div class="card-body">
                              <div class="px-3">
                                <form id="user_permission_form" class="form custom_form_style" action="<?php echo base_url().'admin/userPermission/mutilyUserPermission';?>" method="POST">
                                  <div class="form-body">
                                      <div class="user_permission_top">
                                        <div class="row">                
                                          <div class="col-md-8">
                                            <div class="form-group">
                                              <label>Select Role </label>
                                              <select id="role_select" class="form-control">
                                                <option value="">Select</option>
                                                <?php if(!empty($role_data)): ?>
                                                <?php   foreach($role_data as $val): ?>
                                                <?php if($val['role_id'] !='1'): ?>
                                                            <option value="<?php echo $val['role_id']; ?>"><?php echo $val['role_name']; ?></option>
                                                <?php   endif; ?>
                                                <?php   endforeach; ?>
                                                <?php endif; ?>
                                              </select>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="col-sm-12">
                                          <div class="table-responsive custom_table_area" id="User_permission_data_div">
                                            
                                          </div>
                                        </div>
                                      </div>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </section>
<!-- // Basic form layout section end -->
    </div>
  </div>
</div>
<script>
$(document).on('change','#role_select',function(){  
    if($(this).val() ==''){
        $('.action_sec').hide();
    }
    else{
       $('.action_sec').show();
       var role_id  = $("#role_select").val();       
       $.ajax({
            type: "POST",
            url: '<?php echo base_url('admin/userPermission/ajaxGetUserPermissionDataOnRole/')?>'+role_id,
            dataType:'HTML',
            success: function(response){  
            //alert(response);            
              //$('#modalContent').html(response.message);  
              //$('#myModal').modal('show');
              $("#User_permission_data_div").html(response);
                           
            },
            error:function(response){
              $('#modalContent').html(response.message);  
              $('#myModal').modal('show');
              setTimeout(function(){
                  $('#myModal').modal('hide')
              },400);
            }
      });
    }
})
$(document).on('click','.save_actn',function(e){  
  e.preventDefault();   
   var add            = '';
   var edit           = '';
   var delete_acn     = '';
   var download       = '';
   var menu_id        = $(this).attr('id');
   var menu_name      = $('#menu_name'+menu_id).html();
   var role_id        = $("#role_select").val();
   var parent_id      = $("#parent_id_"+menu_id).val();
   //alert(parent_id);
   var permission_id  = $(this).prop('title');
   if($("#add_actn_"+menu_id).is(':checked'))
   {
      add = '1';
   }
   else{
      add = '';
   }
   if($("#edit_actn_"+menu_id).is(':checked'))
   {
      edit = '1';
   }
   else{
      edit = '';
   }
   if($("#delete_actn_"+menu_id).is(':checked'))
   {
      delete_acn = '1';
   }
   else{
      delete_acn = '';
   }
   if($("#download_actn_"+menu_id).is(':checked'))
   {
      download = '1';
   }
   else{
      download = '';
   }

   $.ajax({
            type: "POST",
            url: '<?php echo base_url('admin/userPermission/saveUserPermitionSingle')?>',
            data: {parent_id:parent_id,menu_id: menu_id,menu_name: menu_name,role_id:role_id,add:add,edit:edit,delete_acn:delete_acn,download:download,permission_id:permission_id},
            dataType:'JSON',
            success: function(response){                 
              if(response['process'] =='success'){                  
                  $.alert({
                     type: 'green',
                     title: 'Alert!',
                     content: 'Successfully saved!',
                  });
              }
              else{
                  swal("Error!", "You clicked the button!", "error").done();
                  $.alert({
                     type: 'red',
                     title: 'Alert!',
                     content: 'Opps!Sorry some error occurred.Please try again.',
                  });
              }           
            },
            error:function(response){
              $('#modalContent').html(response.message);  
              $('#myModal').modal('show');
              setTimeout(function(){
                  $('#myModal').modal('hide')
              },400);
            }
      });
   $(document).on('click','.btn-save-all',function(){
      $("#user_permission_form").submit();
   });
    
})
</script>