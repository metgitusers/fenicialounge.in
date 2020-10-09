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
      						<h4 class="card-title">Membership Packages Purchased Report</h4>      						
      					</div>
      				</div>
      				<div class="card-body">
      					<div class="px-3">
      						<form id="bond_report_form" action="" method="Post" class="form custom_form_style">
                      <div class="form-body">
                        <div class="user_permission_top">
                          <div class="row">                            
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>From Date</label>
                                <div class="input-group">
                                  <input id="from_dt" name="from_dt" type="text" class="form-control pickadate" value=""  placeholder="DD/MM/YYYY" />
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
                                  <input id="to_dt" name="to_dt" type="text" class="form-control pickadate" value="" placeholder="DD/MM/YYYY" />
                                  <div class="input-group-append">
                                    <span class="input-group-text">
                                      <span class="fa fa-calendar-o"></span>
                                    </span>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-3" style="margin-top:30px;">
                              <div class="form-group">
                                <button type="button" class="btn btn-success" id="search_btn">
                                  <i class="fa fa-search" aria-hidden="true"></i> Go
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                  </form>
                  <div id="report_list" style="margin-top:7px"> </div>
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
  $('.pckg_purchased_report_table').DataTable({
    pageLength: 100,
    dom: 'Bfrtip',
    buttons: [{
        extend: 'excel',        
        text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
        tag:  'span',
        filename: 'membership_package_transaction_report_' + date,
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
    var cnt   = 0;
    
      $.ajax({
          type: "POST",
          url: '<?php echo base_url('admin/reports/membershipPackagesPurchasedGenerate')?>',
          data:{from_date:from_date,to_date:to_date},
          dataType:'json',
          success: function(response){  
            //alert(response);
            $("#report_list").html(response['html']);  

            var now = new Date();
            var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
            $('.pckg_purchased_report_table').DataTable({
              pageLength: 10,
              dom: 'Bfrtip',
              buttons: [{
                  extend: 'excel',        
                  text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                  tag:  'span',
                  filename: 'membership_package_purchased_report_' + date,
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
