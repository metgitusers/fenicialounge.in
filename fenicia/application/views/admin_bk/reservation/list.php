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
                                    <h4 class="card-title">Reservation List</h4>
                                    <a class="add_bttn title_btn t_btn_list" href="<?= base_url(); ?>admin/reservation/add"><span><i class="fa fa-plus" aria-hidden="true"></i></span> New Reservation</a>    
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="px-3" >
                                    <div class="form-body" style="padding:10px 10px 0 10px;margin-bottom:10px">
                                        <form id="reservation_filter_form" action="<?php //echo base_url().'admin/reservation/filterSearch';?>" method="Post" class="form custom_form_style">
                                          <div class="form-body">
                                            <div class="user_permission_top">
                                              <div class="row">                            
                                                <div class="col-md-3">
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
                                                <div class="col-sm-3">
                                                    <div class="form-group" style="margin-bottom: 0;">
                                                        <label>Zone</label>
                                                        <div class="settlement_inline">
                                                          <select id="zone_id" class="js-select2" name="zone_id" data-show-subtext="true" data-live-search="true">
                                                            <option value="">Select</option>
                                                            <option value="">All</option>
                                                            <?php if(!empty($zone_list)): ?>
                                                            <?php   foreach($zone_list as $zlist): ?>
                                                                      <option value="<?php echo $zlist['zone_id'];?>" <?php if(!empty($zone_id) && $zone_id == $zlist['zone_id']): echo 'selected';endif;?>><?php echo $zlist['zone_name'];?></option>
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
                                                            <option value="1" <?php if(!empty($status_id) && $status_id == '1'): echo 'selected';endif;?>>Pending</option>
                                                            <option value="2" <?php if(!empty($status_id) && $status_id == '2'): echo 'selected';endif;?>>Confirm</option>
                                                            <option value="0" <?php if(!empty($status_id) && $status_id == '0'): echo 'selected';endif;?>>Cancelled</option>
                                                            <option value="3" <?php if(!empty($status_id) && $status_id == '3'): echo 'selected';endif;?>>Rejected</option>
                                                          </select>
                                                        </div>
                                                    </div>
                                                </div>                                                
                                                <div class="col-md-1" >
                                                  <div class="form-group">
                                                   <label>&nbsp;</label>
                                                    <button type="submit"  class="btn btn-success pull-right" id="search_btn">
                                                      <i class="fa fa-search" aria-hidden="true"></i> Go
                                                    </button>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </form>
                                    </div>
                                    <div id="report_list">
                                        
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
<input type="hidden" id="reservation_id" value="<?php if(!empty($reservation_id)): echo $reservation_id;endif;?>">
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

        /*to_dt_picker.on('set', function(event) {
        
          if ( event.select ) {
            from_dt_picker.set('max', to_dt_picker.get('select'));    
          }
          else if ( 'clear' in event ) {
            from_dt_picker.set('max', false);
          }
        })*/
        //*********************************************************//
        var menu_id = $("#menu_id").val();
	//alert(menu_id);
     	$.ajax({
            type: "POST",
            url: '<?php echo base_url('admin/index/Ck_User_Permission/')?>',
            data:{menu_id:menu_id},
            dataType:'json',
            success: function(response){   
            //alert(response);           
              //$('#modalContent').html(response.message);  
              //$('#myModal').modal('show');
           
    	      	if(response['add_flag'] =='0'){
    			    $(".add_bttn").remove();
    			    $(".rev_status").css('display','none');
    			    $(".action_bttn").css('display','none');
    			}   
    			if(response['edit_flag'] =='0'){
    			    $(".edit_bttn").remove();
    			    $(".action_bttn").css('display','none');
    			}   
    			if(response['view_flag'] =='0'){
    			    $(".delete_bttn").remove();
    			}   
    			if(response['download_flag'] =='0'){
    			    $(".download_bttn").remove();
    			}       
            },
            error:function(response){
              
            }
      	}); 
    //*********************************************************//
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
    var reservation_id   = $("#reservation_id").val();
    var cnt   = 0;    
      $.ajax({
          type: "POST",
          url: '<?php echo base_url('admin/Reservation/filterSearch')?>',
          data:{from_date:from_date,to_date:to_date,zone_id:zone_id,status_id:status_id,reservation_id:reservation_id},
          dataType:'JSON',
          success: function(response){  
           //alert(response);
            $("#reservation_cnt").html(response['reservation_cnt']);
            $("#guest_cnt").html(response['guest_cnt']);
            $("#report_list").html(response['html']);
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
            if(zone_id !=""){
                
                $('#zone_id option[value="'+zone_id+'"]').attr("selected", "selected");
            }
            else{
               $("#zone_id").val(); 
            }
            if(status_id !=""){
                $('#status_id option[value="'+status_id+'"]').attr("selected", "selected");
            }
            else{
               $("#status_id").val(); 
            }
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