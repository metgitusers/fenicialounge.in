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
                                    <h4 class="card-title">Event List</h4>
                                    <a class="add_bttn title_btn t_btn_list" href="<?= base_url(); ?>admin/event/add"><span><i class="fa fa-plus" aria-hidden="true"></i></span> Add Event</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="px-3">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="staff_tab_area">
                                                    <ul class="nav nav-tabs" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link tab_acvt_inacvt active" data-toggle="tab" href="#active_user">Active Event</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link tab_acvt_inacvt" data-toggle="tab" href="#inactive_user">Inactive Event</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link btn-info" data-toggle="tab" href="#calendar_div"><i class="fa fa-calendar" aria-hidden="true"></i> Calendar View</a>
                                                        </li>
                                                    </ul>
                                                    <div class="form-body" style="padding:10px 10px 0 10px;margin-bottom:10px;margin-top:10px;">
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
                                                                <div class="col-md-3" style="margin-top:10px;">
                                                                    <div><strong>Total Active Event(s) :</strong> <span class="cnt" id="active_event_cnt" ><?php echo $active_event_cnt; ?></span></div>
                                                                    <div><strong>Total Inctive Event(s) :</strong> <span class="cnt" id="inactive_event_cnt" ><?php echo $inactive_event_cnt; ?></span></div>
                                                                </div>
                                                              </div>
                                                            </div>
                                                          </div>
                                                        </form>
                                                    </div>
                                                    <div class="tab-content" id="events_list">
                                                        <div id="active_user" class="tab-pane active"><br>
                                                            <div class="table-responsive custom_table_area">
                                                                <table class="table table-striped table-bordered dom-jQuery-events c_table_style reservation_list_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>SL No.</th>                                                                                
                                                                            <th>Event Details</th>
                                                                            <!--<th>Location</th>-->
                                                                            <th width="12%">Event Date</th>
                                                                            <th width="12%">Event Time</th>
                                                                            <!-- <th>Status</th> -->
                                                                            <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
                                                                            <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                                                                            <th>Log</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php if (!empty($event_active_list)) { ?>
                                                                        <?php     foreach ($event_active_list as $key => $actv_ent) { ?>
                                                                        <tr>
                                                                            <td><?= $key + 1 ?></td>                                                                                        
                                                                            <td><?= '<p><strong>'.ucfirst($actv_ent['event_name']).'</strong></p>'.$actv_ent['event_location']; ?></td>
                                                                            <td width="12%"><?= date('d/m/Y',strtotime($actv_ent['event_start_date'])) ?></td>
                                                                            <td width="12%"><?= date('h:i A',strtotime($actv_ent['event_start_time'])) ?></td>
                                                                            <!-- <td class="action_td text-center">
                                                                                <a title="Inactive" class="btn_action edit_icon inactive_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $actv_ent['event_id'];?>" href="<?=base_url('admin/event/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                            </td> -->                                                                                      
                                                                            <td class="action_bttn action_td text-center">
                                                                                <a title="Inactive" class="edit_bttn btn_action btn-warning active_btn make_inactive" data-id="<?php echo $actv_ent['event_id'];?>" href="<?=base_url('admin/event/changeStatus')?>" ><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                                <a title="Edit" href="<?=base_url('admin/event/edit/'.$actv_ent['event_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                <!--<a title="Delete" href="<?=base_url('admin/event/DeleteEvent/'.$actv_ent['event_id'])?>" class="delete_bttn btn_action btn-danger delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                                                            </td>
                                                                            <td class="action_td text-center">
                                                                                <a title="Log" class="btn_action edit_icon log_view" data-column="event_id" data-title="Event" data-id="<?= $actv_ent['event_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a>
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
                                                                            <!-- <th>Status</th> -->
                                                                            <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
                                                                            <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                                                                            <th>Log</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                                <?php if (!empty($event_inactive_list)) { ?>
                                                                                <?php     foreach ($event_inactive_list as $key => $inactv_evn) { ?>
                                                                                <tr>
                                                                                    <td><?= $key + 1 ?></td>                                                                                        
                                                                                    <td><?= '<p><strong>'.ucfirst($inactv_evn['event_name']).'</strong></p>'.$inactv_evn['event_location']; ?></td>
                                                                                    <td  width="12%"><?= date('d/m/Y',strtotime($inactv_evn['event_start_date'])) ?></td>
                                                                                    <td width="12%"><?= date('h:i A',strtotime($inactv_evn['event_start_time'])) ?></td>
                                                                                    <!-- <td class="action_td text-center">
                                                                                        <a title="Active" class="btn_action btn-warning active_btn" data-id="<?php echo $inactv_evn['event_id'];?>" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" href="<?=base_url('admin/event/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                    </td> -->                                                                                      
                                                                                    <td class="action_bttn action_td text-center">
                                                                                        <a title="Active" class="edit_bttn btn_action edit_icon inactive_btn make_active" data-id="<?php echo $inactv_evn['event_id'];?>" href="<?=base_url('admin/event/changeStatus')?>" ><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                                        <a title="Edit" href="<?=base_url('admin/event/edit/'.$inactv_evn['event_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                                        <!--<a title="Delete" href="<?=base_url('admin/event/DeleteEvent/'.$inactv_evn['event_id'])?>" class="delete_bttn btn_action btn-danger delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->
                                                                                        </td>
                                                                                    <td class="action_td text-center">
                                                                                        <a title="Log" class="btn_action edit_icon log_view" data-column="event_id" data-title="EVENT" data-id="<?= $inactv_evn['event_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a>
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
                                                        <div id="calendar_div" class="tab-pane fade">
                                                            <!--<button class="btn btn-secondary" style="cursor: context-menu;">Inactive Event</button>
                                                            <button class="btn btn-success" style="cursor: context-menu;">Active Event</button>-->
                                                            <div id="calendar"></div>
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
    var from_date   = $("#from_dt").val();
    var to_date     = $("#to_dt").val();
    var cnt   = 0;    
    $.ajax({
      type: "POST",
      url: '<?php echo base_url('admin/Event/filterSearch')?>',
      data:{from_date:from_date,to_date:to_date,event_type:'new'},
      dataType:'JSON',
      success: function(response){  
       //alert(response);
        var active_tab   = $(".tab_acvt_inacvt.active").attr('href');
        //alert(active_tab);
        $("#active_event_cnt").html(response['active_event_cnt']);
        $("#inactive_event_cnt").html(response['inactive_event_cnt']);            
        $("#events_list").html(response['html']);
        if(active_tab == '#active_user'){
            $("#active_user").addClass("active");
            $("#active_user").removeClass("fade");
            $("#inactive_user").addClass("fade");
            $("#inactive_user").removeClass("active");                
        }
        else{
            $("#active_user").removeClass("active");
            $("#active_user").addClass("fade");
            $("#inactive_user").addClass("active");
            $("#inactive_user").removeClass("fade");
            //$("#events_list").html(response['html']);
        }
        var now = new Date();
        var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
        $('.reservation_list_table').DataTable({
          pageLength: 10,
          dom: 'Bfrtip',
          buttons: [{
              extend: 'excel',        
              text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
              tag:  'span',
              filename: 'event_report_' + date,
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