<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<style>
	a.canvasjs-chart-credit[title="JavaScript Charts"] {
    left: 0;
    background: #fff;
    width: 70px;
    pointer-events: none;
    color: #fff !important;
}
	</style>
<?php 
  /*if($this->session->userdata('user_details') != ''){ 
    $user_details = $this->session->userdata('user_details'); 
?>
    <input type="hidden" name="default_passwd_change_status" id="default_passwd_change_status" value="<?php echo $user_details['default_passwd_change_status']; ?>"
<?php 
  } */
?> 
<div class="main-content">
  <div class="content-wrapper">
    	<div class="container-fluid"><!--Statistics cards Starts-->
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                <div class="card bg-white">
                    <div class="card-body">
                        <div class="card-block pt-2 pb-0">
                            <div class="media justify-content">
                                <a href="<?php echo base_url().'admin/member';?>">
                                    <div class="media-body white text-left">
                                        <h4 class="font-medium-5 card-title mb-0"><u><?php echo $member_active_cnt; ?></u></h4>
                                         <span class="grey darken-1">Registered Users</span>
                                    </div>
                                </a>
                                <div class="media-right text-right">
                                    <i class="fa fa-users font-large-1 primary"></i>
                                </div>
                            </div>
                        </div>
                        <div id="Widget-line-chart" class="lineChartWidget WidgetlineChart mb-2">
                        </div>
                    </div>
                </div>
            </div>                
            <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                <div class="card bg-white">
                    <div class="card-body">
                        <div class="card-block pt-2 pb-0">
                            <div class="media justify-content">
                                <a href="<?php echo base_url().'admin/membership';?>">
                                    <div class="media-body white text-left">
                                        <h4 class="font-medium-5 card-title mb-0"><u><?php echo $packages_purchased_active_cnt; ?></u></h4>
                                        <span class="grey darken-1">Club Members</span>
                                    </div>
                                    </a>
                                <div class="media-right text-right">
                                    <i class="fa fa-users font-large-1 success"></i>
                                </div>
                            </div>
                        </div>
                        <div id="Widget-line-chart2" class="lineChartWidget WidgetlineChart2 mb-2">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                <div class="card bg-white">
                    <div class="card-body">
                        <div class="card-block pt-2 pb-0">
                            <div class="media justify-content">
                                <a href="<?php echo base_url().'admin/Reservation';?>"><div class="media-body white text-left">
                                    <h4 class="font-medium-5 card-title mb-0"><u><?php echo $reservation_booking_member_cnt; ?></u></h4>
                                    <span class="grey darken-1">Users</span>
                                </div></a>
                                <div class="media-right text-right">
                                    <i class="fa fa-users font-large-1 success"></i>
                                </div>
                            </div>
                        </div>
                        <div id="Widget-line-chart2" class="lineChartWidget WidgetlineChart2 mb-2">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                <div class="card bg-white">
                    <div class="card-body">
                        <div class="card-block pt-2 pb-0">
                            <div class="media justify-content">
                                <a href="<?php echo base_url().'admin/event';?>"><div class="media-body white text-left">
                                    <h4 class="font-medium-5 card-title mb-0"><u><?php echo $event_active_cnt; ?></u></h4>
                                    <span class="grey darken-1">Active Events</span>
                                </div></a>
                                <div class="media-right text-right">
                                    <i class="fa fa-calendar font-large-1 success" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                        <div id="Widget-line-chart2" class="lineChartWidget WidgetlineChart2 mb-2">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title-wrap bar-warning">
                            <h4 class="card-title">Today's Request For Reservation</h4>
                            <a class="btn btn-success pull-right" href="<?php echo base_url().'admin/Reservation';?>">View All Reservation List</a>
                        </div>
                    </div>    
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
                    <div class="card-body">
                        <div class="card-block">
                          <div class="px-3">
                            <form id="bond_report_form" action="" method="Post" class="form custom_form_style">
                                <div class="form-body">
                                  <div class="user_permission_top">
                                    <div class="row" style="background-color:#e8e6e2;padding:10px 10px 0 10px;margin-bottom:10px">                            
                                      <!--<div class="col-md-3">
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
                                      </div>-->
                                      <div class="col-md-5">
                                        <div class="form-group dashboar_search">                                          
                                          <div class="input-group time_pick"> 
                                          <label>Reservation Time: </label>                                         
                                            <input class="form-control customize_inputdate timepicker" value="" id="reservation_time" name="reservation_time" placeholder ="Select time"required/>
                                            <div class="input-group-append">
                                              <span class="input-group-text">
                                                <span class="fa fa-clock-o"></span>
                                              </span>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-group dashboar_search">                                            
                                            <div class="settlement_inline">
                                            <label>Zone: </label>
                                              <select id="zone_id" class="js-select2" name="zone_id" data-show-subtext="true" data-live-search="true">
                                                <option value="">Select Zone</option>
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
                                      <div class="col-md-1" style="">
                                        <div class="form-group">
                                          <button type="button" style="padding-left:25px;padding-right:25px" class="btn btn-success pull-left" id="search_btn">
                                            <i class="fa fa-search" aria-hidden="true"></i> Go
                                          </button>
                                        </div>
                                      </div>
                                      <div class="col-md-1" >
                                        <div class="form-group" style="margin-left:90px;margin-top:-30px">
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
                            <div id="reservation_request_list"></div>
                          </div>                          
                        </div>
                    </div>
                </div>
            </div>                
        </div>            
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
  $('.timepicker').pickatime({
    disable: [
    { from: [2,30], to: [11,30] }
    ],
    interval: 15
  })
  populateReservationData();
  var default_passwd_change_status = $("#default_passwd_change_status").val();
  if(default_passwd_change_status == '0'){
      $("#change_password_modal").modal('show');
  }
  else{
      $("#change_password_modal").modal('hide');
  }
});
$(document).on('click','#search_btn',function(event){
    event.preventDefault();
    populateReservationData();    
});
function populateReservationData(){
  var from_date           = $("#from_dt").val();
  var to_date             = $("#to_dt").val();
  var zone_id             = $("#zone_id").val();
  var reservation_time    = $("#reservation_time").val();
  //alert(reservation_time); 
  var cnt   = 0;    
    $.ajax({
        type: "POST",
        url: '<?php echo base_url('admin/Dashboard/reservationRequestList')?>',
        data:{from_date:from_date,to_date:to_date,zone_id:zone_id,reservation_time:reservation_time},
        dataType:'Json',
        success: function(response){  
       //alert(response);            
          $("#reservation_request_list").html(response['html']);
          var now = new Date();
          var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
          $('.reservation_list_table').DataTable({
            pageLength: 10,
            buttons: [{
                extend: 'excel',
                text: '<i class="fa fa-download" aria-hidden="true"></i>',
                tag: 'span',
                filename: 'today_reservation_list_' + date,
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