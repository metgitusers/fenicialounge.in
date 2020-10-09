<div class="row">
    <div class="col-sm-12">
        <div class="staff_tab_area">                                                        
            <div class="tab-content">
                <div id="active_user" class="tab-pane active"><br>
                    <div class="table-responsive custom_table_area">
                        <table class="table table-striped table-bordered c_table_style export_btn_dt log_list_table">
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
                                                <td colspan="4" style="text-align:center;">No Data Available</td>
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
<script type="text/javascript">
$(document).ready(function(){
    var now = new Date();
        var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
        $('.log_list_table').DataTable({
          pageLength: 100,
          dom: 'Bfrtip',
          buttons: [{
              extend: 'excel',        
              text: '<i class="fa fa-download" aria-hidden="true"></i>',
              tag:  'span',
              filename: 'log_report_' + date,
              exportOptions: {
                      columns: [0,1,2,3,4]
              }
            }
            //'copy', 'csv', 'excel', 'pdf', 'print'
          ]
        });
})
</script>
