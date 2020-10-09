<div class="main-content">
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Basic form layout section start -->
      <section id="basic-form-layouts">
        <!--<div class="row">
          <div class="col-sm-12">
            <h2 class="content-header">Driver Master</h2>
          </div>
        </div>-->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <div class="page-title-wrap">
                  <h4 class="card-title">Push Notification</h4>
                  
                </div>


                <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
              </div>
              <div class="card-body">
            <?php if ($this->session->flashdata('success_msg')) : ?>
                <div class="alert alert-success" role="alert">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                  <?php echo $this->session->flashdata('success_msg') ?>
                </div>
            <?php endif ?>
            <?php if ($this->session->flashdata('error_msg')) : ?>
                <div class="alert alert-danger" role="alert">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                  <?php echo $this->session->flashdata('error_msg') ?>
                </div>
            <?php endif ?>
                         
              <form method="post" id="NotificationAddform" role="form" action="<?php echo base_url();?>admin/notification/add_content" autocomplete="off"  enctype="multipart/form-data">
                <div class="row">
                  <div class="col-md-4 col-sm-12 col-xs-12">
                  <div class="form-group">
                      <label>Message*</label>
                      <!-- <input type="text" name="name" id="name" class="form-control" value="<?php echo set_value('name');?>" 
                      required> -->
                      <textarea required="required" name="offer_text" id="offer_text" class="form-control"></textarea>
                       
                    </div>
                </div>
             
               <?php if(!empty($user_list)){ ?>
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="form-group">
                      <label>Select All <input type="checkbox" id="selectAll"></label>
                      <div class="row">
                       <?php foreach($user_list as $row1){?>
                       <div class="col-md-3 col-sm-12 col-xs-12">
                        <div class="form-check">
                          <input class="form-check-input move_cafe_checkbox" type="checkbox" value="<?php echo $row1['member_id'];?>" name="user_id[]" >
                          <label class="form-check-label" for="<?php echo $row1['first_name']." ".$row1['last_name'];?>">
                            <?php echo $row1['first_name']." ".$row1['last_name'];?>
                          </label>
                        </div>
                        </div>
                         <?php } ?>
                         </div>
                        
                    </div>
                </div>
              <?php } ?>
              
              
               
                 <div class="col-md-2 col-xs-2 col-xs-2">
                    <div class="form-group">
                       <button type="submit" class="btn btn-primary btn-user btn-block">Send </button>
                          <!--  <input type="submit" name="submit" value="Submit"/> -->
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
      <!-- End of Main Content -->
      <script type="text/javascript">
        $("#selectAll").click(function(){
          if($(this).prop("checked")) {
                //$(".checkBox").prop("checked", true);
                $("input[type=checkbox]").prop('checked', true);
            } else {
                //$(".checkBox").prop("checked", false);
                $("input[type=checkbox]").prop('checked', false);
            }  
        

        });
      </script>