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
                                    <h4 class="card-title">List</h4>
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
                                                        <div class="tab-content" id="reservation_booking_user_list_div" style="margin-top: -13px;!important">
                                                            
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
    reservationBookingListPopulateData();
});
function reservationBookingListPopulateData(){
    var cnt   = 0;    
      $.ajax({
          type: "POST",
          url: '<?php echo base_url('admin/reservation/getReservationBookingUserList')?>',          
          dataType:'json',
          success: function(response){               
            $("#reservation_booking_user_list_div").html(response['html']);            
            var now = new Date();
            var date = now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();  
            $('.reservation_booking_user_table').DataTable({
              pageLength: 10,
              dom: 'Bfrtip',
              buttons: [{
                  extend: 'excel',
                  text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Export',
                  tag:  'span',
                  filename: 'reservation_booking_user_list_' + date,
                  exportOptions: {
                          columns: [0,1,2,3,4,5]
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