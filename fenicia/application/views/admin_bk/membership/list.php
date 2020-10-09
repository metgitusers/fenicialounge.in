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
                                    <h4 class="card-title">Club Member List</h4>
                                    <!--<a class="title_btn t_btn_list" href="<?= base_url(); ?>admin/membership/add"><span><i class="fa fa-plus" aria-hidden="true"></i></span> Add Member</a>-->    
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
                                                        <ul class="nav nav-tabs" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link tab_acvt_inacvt active" data-toggle="tab" href="#active_user">Active Membership Owner</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link tab_acvt_inacvt" data-toggle="tab" href="#inactive_user">Inactive Membership Owner</a>
                                                            </li>
                                                        <!--    <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#trash_user">Trash Driver</a>
                                                            </li> -->
                                                        </ul>
                                                        <div class="px-3" style="margin-top:10px">
                                                            <form id="bond_report_form" action="" method="Post" class="form custom_form_style">
                                                              <div class="form-body">
                                                                <div class="user_permission_top">
                                                                  <div class="row" style="background-color:#e8e6e2;padding:10px 10px 0 10px;">                            
                                                                    <div class="col-sm-3">
                                                                      <div class="form-group" style="margin-bottom: 0;">
                                                                          <label>Registration date</label>
                                                                          <div class="settlement_inline">
                                                                            <select id="registration_filter" class="js-select2" name="registration_filter" data-show-subtext="true" data-live-search="true">
                                                                              <option value="">Select</option>
                                                                              <option value="1">All</option>
                                                                              <option value="2">Weekly</option>
                                                                              <option value="3">Monthly</option>
                                                                              <option value="4">Quarterly</option>
                                                                              <option value="5">Yearly</option>
                                                                            </select>
                                                                          </div>
                                                                      </div>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                      <div class="form-group" style="margin-bottom: 0;">
                                                                          <label>Expiry date</label>
                                                                          <div class="settlement_inline">
                                                                            <select id="expiry_filter" class="js-select2" name="expiry_filter" data-show-subtext="true" data-live-search="true">
                                                                              <option value="">Select</option>
                                                                              <option value="1">All</option>
                                                                              <option value="2">Weekly</option>
                                                                              <option value="3">Monthly</option>
                                                                              <option value="4">Quarterly</option>
                                                                              <option value="5">Yearly</option>
                                                                            </select>
                                                                          </div>
                                                                      </div>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                      <div class="form-group" style="margin-bottom: 0;">
                                                                          <label>Membership name</label>
                                                                          <div class="settlement_inline">
                                                                            <select id="membership_name" class="js-select2" name="membership_name" data-show-subtext="true" data-live-search="true">
                                                                              <option value="">Select</option>
                                                                              <option value="">All</option>
                                                                              <?php if(!empty($membership_list)): ?>
                                                                              <?php   foreach($membership_list as $mlist): ?>
                                                                                        <option value="<?php echo $mlist['package_id'];?>"><?php echo $mlist['package_name'];?></option>
                                                                              <?php   endforeach; ?>
                                                                              <?php endif; ?>
                                                                            </select>
                                                                          </div>
                                                                      </div>
                                                                    </div>                                                                   
                                                                    <div class="col-md-3" style="margin-top:30px;">
                                                                      <div class="form-group">
                                                                        <button type="button" class="btn btn-success pull-right" id="search_btn">
                                                                          <i class="fa fa-search" aria-hidden="true"></i> GO
                                                                        </button>
                                                                      </div>
                                                                    </div>                            
                                                                  </div>
                                                                </div>
                                                              </div>                      
                                                            </form>                                              
                                                        </div>
                                                        <div class="tab-content" id="pckg_purchased_list" style="margin-top: -13px;!important">
                                                            
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
<script>
$(document).ready(function() {
    membershipPopulateData();
    var now = new Date();
    var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
    $('.pckg_purchased_report_table').DataTable({
      pageLength: 10,
      dom: 'Bfrtip',
      buttons: [{
          extend: 'excel',        
          text: '<i class="fa fa-download" aria-hidden="true"></i>',
          tag:  'span',
          filename: 'membership_package_purchased_report_' + date,
          exportOptions: {
                  columns: [0,1,2,3,4,5,6,7,8,9]
          }
        }
        //'copy', 'csv', 'excel', 'pdf', 'print'
      ]
    });
});
$(document).on('click','#search_btn',function(event){
    event.preventDefault();
    membershipPopulateData();
    
});

  function membershipPopulateData(){
    var registration_filter   = $("#registration_filter").val();
    var expiry_filter         = $("#expiry_filter").val();
    var membership_name       = $("#membership_name").val();
    var cnt   = 0;    
      $.ajax({
          type: "POST",
          url: '<?php echo base_url('admin/membership/filterSearch')?>',
          data:{registration_filter,expiry_filter,membership_name},
          dataType:'json',
          success: function(response){  
          //alert(response);
            var active_tab   = $(".tab_acvt_inacvt.active").attr('href');
           //alert(active_tab);        
            $("#pckg_purchased_list").html(response['html']);
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
            $('.pckg_purchased_report_table').DataTable({
              pageLength: 10,
              dom: 'Bfrtip',
              buttons: [{
                  extend: 'excel',
                  text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                  tag:  'span',
                  filename: 'membership_package_purchased_report_' + date,
                  exportOptions: {
                          columns: [0,1,2,3,4,5,6,7,8,9]
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