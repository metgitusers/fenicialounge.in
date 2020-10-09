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
      						<h4 class="card-title">Reservation Report</h4>      						
      					</div>
      				</div>
      				<div class="card-body">
      					<div class="px-3">
      						<form id="bond_report_form" action="" method="Post" class="form custom_form_style">
                      <div class="form-body">
                        <div class="user_permission_top">
                          <div class="row" style="background-color:#e8e6e2;padding:10px 10px 0 10px;margin-bottom:10px">                            
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>From Date</label>
                                <div class="input-group">
                                  <input id="from_dt" name="from_dt" type="text" class="form-control customize_inputdate pickadate" placeholder="DD/MM/YYYY" />
                                  <div class="input-group-append">
                                    <span class="input-group-text">
                                      <span class="fa fa-calendar-o"></span>
                                    </span>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>To Date</label>
                                <div class="input-group">
                                  <input id="to_dt" name="to_dt" type="text" class="form-control customize_inputdate pickadate" placeholder="DD/MM/YYYY" />
                                  <div class="input-group-append">
                                    <span class="input-group-text">
                                      <span class="fa fa-calendar-o"></span>
                                    </span>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-3">
                              <div class="form-group" style="margin-bottom: 0;">
                                  <label>Zone</label>
                                  <div class="settlement_inline">
                                    <select id="zone_id" class="js-select2" name="zone_id" data-show-subtext="true" data-live-search="true">
                                      <option value="">Select</option>
                                      <option value="">All</option>
                                      <?php if(!empty($zone_list)): ?>
                                      <?php   foreach($zone_list as $zlist): ?>
                                                <option value="<?php echo $zlist['zone_id'];?>"><?php echo $zlist['zone_name'];?></option>
                                      <?php   endforeach; ?>
                                      <?php endif; ?>
                                    </select>
                                  </div>
                              </div>
                            </div>
                            <div class="col-sm-2">
                              <div class="form-group" style="margin-bottom: 0;">
                                  <label>Status</label>
                                  <div class="settlement_inline">
                                    <select id="status_id" class="js-select2" name="status_id" data-show-subtext="true" data-live-search="true">
                                      <option value="">Select</option>
                                      <option value="">All</option>
                                      <option value="1">Pending</option>
                                      <option value="2">Reserved</option>
                                      <option value="0">Cancelled</option>
                                      <option value="3">Rejected</option>
                                    </select>
                                  </div>
                              </div>
                            </div>
                            
                            <div class="col-md-1">
                              <div class="form-group">
                               <label>&nbsp;</label>
                                <button type="button" class="btn btn-success pull-right" id="search_btn">
                                  <i class="fa fa-search" aria-hidden="true"></i> Go
                                </button>
                              </div>
                            </div>                            
                          </div>
                        </div>
                      </div>                      
                  </form>
                  <div id="report_list"> </div>
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
$(document).ready(function() {
  populateData();
  var now = new Date();
  var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
  $('.reservation_report_table').DataTable({
    pageLength: 10,
    dom: 'Bfrtip',
    buttons: [{
        extend: 'excel',        
        text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
        tag:  'span',
        filename: 'reservation_report_table_report_' + date,
        exportOptions: {
                columns: [0,1,2,3,4,5,6,7]
        }
      }
      //'copy', 'csv', 'excel', 'pdf', 'print'
    ]
  });
  
  
  $(".js-select2").select2();
    var from_dt       = $('#from_dt').pickadate({format:'dd/mm/yyyy',autoclose:true}),
    from_dt_picker    = from_dt.pickadate('picker');

    var to_dt         = $('#to_dt').pickadate({format:'dd/mm/yyyy',autoclose:true}),
    to_dt_picker      = to_dt.pickadate('picker');
      
    // Check if there’s a “from” or “to” date to start with.
    // if ( from_dt_picker.get('value') ) {
    //     to_dt_picker.set('min', from_dt_picker.get('select'))
    // }
    // if ( to_dt_picker.get('value') ) {
    //     from_dt_picker.set('max', to_dt_picker.get('select'))
    // }

    // When something is selected, update the “from” and “to” limits.
    from_dt_picker.on('set', function(event) {
    
      if ( event.select ) {
        to_dt_picker.set('min', from_dt_picker.get('select'));    
      }
      else if ( 'clear' in event ) {
        to_dt_picker.set('min', false);
      }
    })

    /*to_dt_picker.on('set', function(event) {
    
      if ( event.select ) {
        from_dt_picker.set('max', to_dt_picker.get('select'));    
      }
      else if ( 'clear' in event ) {
        from_dt_picker.set('max', false);
      }
    })*/
});
$(document).on('change','#from_dt',function(event){
  $('#to_dt').val('');
});
$(document).on('click','#search_btn',function(event){
    event.preventDefault();
    populateData();
    
});

  function populateData(){
    var from_date   = $("#from_dt").val();
    var to_date     = $("#to_dt").val();
    var zone_id     = $("#zone_id").val();
    var status_id   = $("#status_id").val();
    var cnt   = 0;    
      $.ajax({
          type: "POST",
          url: '<?php echo base_url('admin/reports/reservationReportGenerate')?>',
          data:{from_date:from_date,to_date:to_date,zone_id:zone_id,status_id:status_id},
          dataType:'json',
          success: function(response){  
           //alert(response);
            $("#reservation_cnt").html(response['reservation_cnt']);
            $("#guest_cnt").html(response['guest_cnt']);
            $("#report_list").html(response['html']);  

            var now = new Date();
            var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
            $('.reservation_report_table').DataTable({
              pageLength: 10,
              dom: 'Bfrtip',
              buttons: [{
                  extend: 'excel',        
                  text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                  tag:  'span',
                  filename: 'reservation_report_table_report_' + date,
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
