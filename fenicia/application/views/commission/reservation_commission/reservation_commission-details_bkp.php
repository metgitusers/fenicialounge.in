<div>
    <div>
        <div class="container-fluid">            
            <!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="page-title-wrap">
                                    <h4 class="card-title">Detail List</h4>
                                    <a class="title_btn t_btn_list" href="<?= base_url(); ?>commission/ReservationCommission"><span><i class="fa fa-arrow-left" aria-hidden="true"></i></span> Back</a>    
                                </div>
                            </div>
                            <div class="card-body">
                              <div class="px-3">
                                <div class="form-body">
                                    <form id="reservation_filter_form" action="<?php //echo base_url().'admin/reservation/filterSearch';?>" method="Post" class="form custom_form_style">
                                        <div class="form-body">
                                            <div class="user_permission_top">
                                                <div class="row">                            
                                                    <div class="col-md-3 offset-1">
                                                      <div class="form-group">
                                                        <label>From Date</label>
                                                        <div class="input-group">
                                                            <input id="from_dt" name="from_dt" type="text" class="form-control customize_inputdate pickadate" value="<?php if(!empty($resv_from_date)): echo $resv_from_date;endif;?>"  placeholder="DD/MM/YYYY" />
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
                                                            <input id="to_dt" name="to_dt" type="text" class="form-control customize_inputdate pickadate" value="<?php if(!empty($resv_to_date)): echo $resv_to_date;endif;?>" placeholder="DD/MM/YYYY" />
                                                            <div class="input-group-append">
                                                            <span class="input-group-text">
                                                              <span class="fa fa-calendar-o"></span>
                                                            </span>
                                                          </div>
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
                                                                <option value="1" <?php if(!empty($status_id) && $status_id == '1'): echo 'selected';endif;?>>Pending</option>
                                                                <option value="2" <?php if(!empty($status_id) && $status_id == '2'): echo 'selected';endif;?>>Confirm</option>
                                                                <option value="0" <?php if(!empty($status_id) && $status_id == '0'): echo 'selected';endif;?>>Cancelled</option>
                                                                <option value="3" <?php if(!empty($status_id) && $status_id == '3'): echo 'selected';endif;?>>No-show</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>                                                                                                                                                
                                                    <div class="col-md-1" >
                                                      <div class="form-group" style="margin-top:-3px">
                                                       <label>&nbsp;</label>
                                                       <input type="hidden" name="zone_id" id="zone_id" value="<?php echo $zone_id; ?>">
                                                       <!--<input type="hidden" name="reservation_date" id="reservation_date" value="<?php echo $reservation_date; ?>">-->
                                                        <button type="submit" style="padding-left:25px;padding-right:25px" class="btn btn-success pull-right" id="search_btn">
                                                          <i class="fa fa-search" aria-hidden="true"></i> Go
                                                        </button>
                                                      </div>
                                                    </div>
                                                    <div class="col-md-1" >
                                                      <div class="form-group" style="margin-right:15px;margin-top:-3px">
                                                        <label>&nbsp;</label>
                                                        <button class="btn btn-danger pull-right" id="clear_btn">
                                                          <i class="fa fa-refresh" aria-hidden="true"></i> Clear
                                                        </button>
                                                      </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div id="reservation_details" class="row">
                                        
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
    
    var from_dt     = $('#from_dt').pickadate({format:'dd/mm/yyyy',autoclose:true}),
    from_dt_picker  = from_dt.pickadate('picker');

    var to_dt     = $('#to_dt').pickadate({format:'dd/mm/yyyy',autoclose:true}),
    to_dt_picker  = to_dt.pickadate('picker');
    from_dt_picker.on('set', function(event) {

        if ( event.select ) {
            to_dt_picker.set('min', from_dt_picker.get('select'));    
        }
        else if ( 'clear' in event ) {
            to_dt_picker.set('min', false);
        }
    })

    });
    $(document).on('change','#from_dt',function(event){
      $('#to_dt').val('');
    });

$(document).on('click','#search_btn',function(event){
    event.preventDefault();
    populateData();
    
});

  function populateData(){
    var from_date           = $("#from_dt").val();
    var to_date             = $("#to_dt").val();
    var status_id           = $("#status_id").val();
    var zone_id             = $("#zone_id").val();
    //var reservation_date    = $("#reservation_date").val();
    var cnt   = 0;    
    $.ajax({
        type: "POST",
        url: "<?php echo base_url('commission/ReservationCommission/filterSearchResvDetails')?>",
        data:{from_date:from_date,to_date:to_date,status_id:status_id,zone_id:zone_id},
        dataType:'json',
        success: function(response){  
         //alert(response);          
          $("#reservation_details").html(response['html']);
          if(from_date !="") {
              $("#from_dt").val(from_date);
          }
          else{
              $("#from_dt").val();
          }
          if(to_date !=""){
              $("#to_dt").val(to_date);
          }
          else{
              $("#to_dt").val();
          }          
          var now = new Date();
          var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
          $('.reservation_details_table').DataTable({
            pageLength: 10,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',        
                text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
                tag:  'span',
                filename: 'reservation_details_report_' + date,
                exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9]
                },
                footer: true                 
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