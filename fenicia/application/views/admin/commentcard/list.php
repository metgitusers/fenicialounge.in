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
                                    <h4 class="card-title">Feedback List</h4>
                                   <!--  <a class="add_bttn title_btn t_btn_list" href="<?= base_url(); ?>admin/member/add"><span><i class="fa fa-plus" aria-hidden="true"></i></span> Add Users</a>  -->   
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
                                                        <div class="form-body" style="padding:10px 10px 0 10px;margin-bottom:10px;margin-top:10px;">
                                                        <form id="commentcard_form" action="<?php //echo base_url().'admin/reservation/filterSearch';?>" method="Post" class="form custom_form_style">
                                                          <div class="form-body">
                                                            <div class="user_permission_top">
                                                              <div class="row">
                                                              <?php if(!empty($_GET['from_dt'])){ 
                                                                            $from_date_value=$_GET['from_dt'];
                                                                        }else{
                                                                             $from_date_value="";
                                                                        }?>                            
                                                                <div class="col-md-3">
                                                                  <div class="form-group">
                                                                    <label>From Date</label>
                                                                    <div class="input-group">
                                                                        
                                                                      <input id="from_dt" name="from_dt" type="text" class="form-control pickadate" value="<?php echo $from_date_value;?>" placeholder="DD/MM/YYYY"  required="required"/>
                                                                      <div class="input-group-append">
                                                                        <span class="input-group-text">
                                                                          <span class="fa fa-calendar-o"></span>
                                                                        </span>
                                                                      </div>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                                 <?php if(!empty($_GET['to_dt'])){ 
                                                                            $to_date_value=$_GET['to_dt'];
                                                                        }else{
                                                                             $to_date_value="";
                                                                        }?>
                                                                <div class="col-md-3">
                                                                  <div class="form-group">
                                                                    <label>To Date</label>
                                                                    <div class="input-group">
                                                                      <input id="to_dt" name="to_dt" type="text" class="form-control pickadate" value="<?php echo $to_date_value;?>" placeholder="DD/MM/YYYY"  required="required"/>
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
                                                        <div class="tab-content">
                                                            <div id="" class="tab-pane active"><br>
                                                                <div class="table-responsive custom_table_area">
                                                                    <table class="table table-striped table-bordered dom-jQuery-events c_table_style table-hover commentcard_table">
                                                                        <thead>
                                                                            <?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
                                                                            <tr>
                                                                                                                                                              
                                                                                <th>SL No.</th>        
                                                                                <th>Visit Date</th>
                                                                                <th>Food </th>
                                                                                <th>Service</th>
                                                                                
                                                                                <th>Venue</th>
                                                                                <th>Name</th>
                                                                                <th>Phone</th>
                                                                                <th class="action_bttn" <?php echo $actrion_visibility; ?>>Action</th>
                                                                               
                                                                            </tr>
                                                                        </thead>
                                                                        	<tbody>
                                                                                <?php if (!empty($command_card_list)) { 
                                                                                        //PR($member_active_list);
                                                                                ?>
                                                                                <?php     foreach ($command_card_list as $key => $row) { 
                                                                                
                                                                                $food=0;
                                                                                $serving=0;
                                                                                $venue=0;
                                                                                $food=($row['food_varity']+$row['food_quality']+$row['food_serving']+$row['food_presentation'])/4;
                                                                                $serving=($row['service_speed']+$row['service_courtesy']+$row['service_knowledge'])/3;
                                                                                $venue=($row['venue_atmosphere']+$row['venue_cleanliness'])/2;
                                                                                ?>
                                                                                <tr>
                                                                                    
                                                                                    <td><?= $key + 1 ?></td>  
                                                                                    <td><?= date('d/m/Y',strtotime($row['visit_date'])); ?></td>
                                                                                    <td>
                                                                                        <?php 
                                                                                        /*if($row['food_varity']==1){echo "VERY POOR"; }else if($row['food_varity']==2){echo "POOR"; } else if($row['food_varity']==3){echo "AVERAGE";}else if($row['food_varity']==4){echo "GOOD"; }
                                                                                            else if($row['food_varity']==5){echo "EXCELLENT"; } else if($row['food_varity']==0){echo "NO FEEDBACK"; } */
                                                                                            echo round($food,1);
                                                                                            ?>
                                                                                            
                                                                                    </td> 
                                                                                    <td>  
                                                                                        <?php 

                                                                                          echo round($serving,1);   
                                                                                            ?>
                                                                                                
                                                                                    </td> 
                                                                                    <td>  
                                                                                        <?php 
                                                                                         echo round($venue,1);   
                                                                                         ?>
                                                                                                
                                                                                    </td> 
                                                                                    <td>
                                                                                      <?php echo $row['first_name']." ".$row['last_name']; ?>  
                                                                                    </td>
                                                                                     <td>
                                                                                        <?php echo $row['mobile']; ?>   
                                                                                     </td>                                                                                    
                                                                                                                                                                  
                                                                                    <td class="action_bttn action_td text-center">
                                                                                        
                                                                                        <!-- <a title="View" href="<?=base_url('admin/commentcard/details/'.$row['commet_id'])?>" class="edit_bttn btn_action edit_icon"><i class="fa fa-eye" aria-hidden="true"></i></a> -->
                                                                                        <a title="View Details" style="width:auto; height:auto;" href="<?=base_url('admin/commentcard/details/'.$row['commet_id'])?>" class="view_bttn btn_action edit_icon"><i class="fa fa-eye" aria-hidden="true"></i> Details</a>
                                                                                      
                                                                                    </td>
                                                                                    
                                                                                    
                                                                                </tr>
                                                                            <?php 
                                                                            } } else { ?>
	                                                                            <tr>
	                                                                                <td colspan="8" style="text-align:center;">No Data Available</td>
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
$(document).on('click','.page-link',function(){
    var page = $(this).data('dt-idx');
    //alert(page);
})

</script>
<script>

$(document).ready(function() { 

    table = $('#commentcard_table').DataTable({ 

       "processing": true, //Feature control the processing indicator.

        "serverSide": true, //Feature control DataTables' server-side processing mode.

        "order": [], //Initial no order.

        // Load data for the table's content from an Ajax source

        "ajax": {

            "url": "<?php echo site_url('admin/commentcard')?>",

           "type": "POST",

           "data":function(args){

            args.from_dt = $('#from_dt').val(),

            args.to_dt = $('#to_dt').val()
        }

        },

        //Set column definition initialisation properties.

        "columnDefs": [

        { 

           // "targets": [ -1 ], //last column

            "targets": 'no-sort',

            "orderable": false, //set not orderable

        },

        ],

    });

  $('#search_btn').click(function(){
    table.draw();
});
</script>


