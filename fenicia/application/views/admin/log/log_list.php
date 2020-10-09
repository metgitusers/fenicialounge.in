<div class="main-content">
    <div class="content-wrapper">
        <div class="container-fluid">            
            <!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="page-title-wrap">
                                    <h4 class="card-title">Log List</h4>                                    
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="px-3">
                                    <div class="form-body" id="log_table">
                                        <!--<h4 class="form-section">
                                            <i class="icon-user"></i> Personal Details</h4>-->
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="staff_tab_area">                                                        
                                                    <div class="tab-content">
                                                        <div id="active_user" class="tab-pane active"><br>
                                                            <div class="table-responsive custom_table_area">
                                                                <table class="table table-striped table-bordered dom-jQuery-events c_table_style">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>SL No</th>                                                                                
                                                                            <th>Action</th>
                                                                            <th>Action By</th>
                                                                            <th>Action Date</th>                                                                            
                                                                            <th>IP</th>                                                           
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php   if (!empty($log_lists)) { ?>
                                                                        <?php       foreach ($log_lists as $key => $list) { $min="570"; ?>
                                                                                        <tr>
                                                                                            <td><?= $key + 1 ?></td>                                                                                        
                                                                                            <td><?= $list['statement'];?></td>
                                                                                            <td><?= $list['full_name'] ?></td>                                                                                   
                                                                                            <td><?= date('d/m/Y h:i A',strtotime("+".$min." minutes",strtotime($list['action_on']))) ?></td>
                                                                                            <td><?= $list['IP'] ?></td>                                                                       
                                                                                        </tr>
                                                                        <?php       }
                                                                                } 
                                                                                else 
                                                                                { 
                                                                        ?>
                                                                                    <tr>
                                                                                        <td colspan="7" style="text-align:center;">No Data Available</td>
                                                                                    </tr>

                                                                        <?php  } ?>
                                                                    </tbody>
                                                                </table>
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
                    </div>
                </div>
            </section>
            <!-- // Basic form layout section end -->
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    populateData();
    var now = new Date();
    var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
    $('.reservation_list_table').DataTable({
      pageLength: 100,
      dom: 'Bfrtip',
      buttons: [{
          extend: 'excel',        
          text: '<i class="fa fa-download" aria-hidden="true"></i>',
          tag:  'span',
          filename: 'reservation_report_' + date,
          exportOptions: {
                  columns: [0,1,2,3,4,5,6,7]
          }
        }
        //'copy', 'csv', 'excel', 'pdf', 'print'
      ]
    });
});
function populateData(){        
      $.ajax({
          type: "POST",
          url: '<?php echo base_url('admin/Log/logList')?>',
          data:{},
          dataType:'JSON',
          success: function(response){  
           //alert(response);
            $("#log_table").html(response['html']);
            
            var now = new Date();
            var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
            $('.reservation_list_table').DataTable({
              pageLength: 10,
              dom: 'Bfrtip',
              buttons: [{
                  extend: 'excel',        
                  text: '<i class="fa fa-download" aria-hidden="true"></i>',
                  tag:  'span',
                  filename: 'reservation_report_' + date,
                  exportOptions: {
                          columns: [0,1,2,3,4,5,6,7]
                  }
                }
                //'copy', 'csv', 'excel', 'pdf', 'print'
              ]
            });  
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
</script>