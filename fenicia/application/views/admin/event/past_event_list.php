<div class="main-content">
    <div class="content-wrapper">
        <div class="container-fluid">
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
            <!-- Basic form layout section start -->
            <section id="basic-form-layouts">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="page-title-wrap">
                                    <h4 class="card-title">Past Event List</h4>
                                    <!--<a class="add_bttn title_btn t_btn_list" href="<?= base_url(); ?>admin/event/add"><span><i class="fa fa-plus" aria-hidden="true"></i></span> Add Event</a>-->
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="px-3">
                                    <form class="form">
                                        <div class="form-body">
                                            <!--<h4 class="form-section">
                                                <i class="icon-user"></i> Personal Details</h4>-->
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="staff_tab_area">
                                                        <!--<ul class="nav nav-tabs" role="tablist">                                                            
                                                            <li class="nav-item">
                                                                <a class="nav-link btn-info" data-toggle="tab" href="#past_event_calendar_div"><i class="fa fa-calendar" aria-hidden="true"></i> Calendar View</a>
                                                            </li>
                                                        </ul>-->
                                                        <div class="form-body" style="padding:10px 10px 0 10px;margin-top:10px;">
                                                            <form id="reservation_filter_form" action="<?php //echo base_url().'admin/reservation/filterSearch';?>" method="Post" class="form custom_form_style">
                                                              <div class="form-body">
                                                                <div class="user_permission_top">
                                                                  <div class="row">                            
                                                                    <div class="col-md-3">
                                                                      <div class="form-group">
                                                                        <label>From Date</label>
                                                                        <div class="input-group">
                                                                          <input id="from_dt" name="from_dt" type="text" class="form-control customize_inputdate pickadate" value=""  placeholder="DD/MM/YYYY" />
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
                                                                          <input id="to_dt" name="to_dt" type="text" class="form-control customize_inputdate pickadate" value="" placeholder="DD/MM/YYYY" />
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
                                                                        <button type="submit" style="width:50%" class="btn btn-success" id="search_btn">
                                                                          <i class="fa fa-search" aria-hidden="true"></i> Go
                                                                        </button>
                                                                      </div>
                                                                    </div>                                                                    
                                                                  </div>
                                                                </div>
                                                              </div>
                                                            </form>
                                                        </div>
                                                        <div class="tab-content" id="past_events_list" style="margin-top:-15px">                                                                                                                        
                                                            <div id="past_event_calendar_div" class="tab-pane fade">
                                                                <!--<button class="btn btn-secondary" style="cursor: context-menu;">Inactive Event</button>
                                                                <button class="btn btn-success" style="cursor: context-menu;">Active Event</button>-->
                                                                <div id="pasteventcalendar"></div>
                                                            </div>
                                                        </div>
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
      pageLength: 10,
      dom: 'Bfrtip',
      buttons: [{
          extend: 'excel',        
          text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
          tag:  'span',
          filename: 'past_event_report_' + date,
          exportOptions: {
                  columns: [0,1,2,3]
          }
        }
        //'copy', 'csv', 'excel', 'pdf', 'print'
      ]
    }); 
    var from_dt     = $('#from_dt').pickadate({format:'dd/mm/yyyy',autoclose:true,}),
    from_dt_picker  = from_dt.pickadate('picker');

    var to_dt     = $('#to_dt').pickadate({format:'dd/mm/yyyy',autoclose:true}),
    to_dt_picker  = to_dt.pickadate('picker');

    from_dt_picker.set('max', true);

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
    var from_date   = $("#from_dt").val();
    var to_date     = $("#to_dt").val();
    var cnt   = 0;    
      $.ajax({
          type: "POST",
          url: '<?php echo base_url('admin/Event/filterSearch')?>',
          data:{from_date:from_date,to_date:to_date,event_type:'past'},
          dataType:'json',
          success: function(response){  
          //alert(response);
            var active_tab   = $(".tab_acvt_inacvt.active").attr('href');
            //alert(active_tab);
            $("#active_past_event_cnt").html(response['active_past_event_cnt']);
            $("#inactive_past_event_cnt").html(response['inactive_past_event_cnt']);            
            $("#past_events_list").html(response['html']);
            
            var now = new Date();
            var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
            $('.reservation_list_table').DataTable({
              pageLength: 10,
              dom: 'Bfrtip',
              buttons: [{
                  extend: 'excel',        
                  text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
                  tag:  'span',
                  filename: 'past_event_report_' + date,
                  exportOptions: {
                          columns: [0,1,2,3]
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