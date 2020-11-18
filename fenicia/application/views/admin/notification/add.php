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
                <!-- added on 12-11 -->
                <div class="col-md-12">
                  <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group emoji">
                      <label>Title*</label>
                      <input type="text" maxlength="25" name="message_title" id="message_title" class="form-control emoji_text" value="<?php echo set_value('message_title');?>" required>
                    </div>
                  </div>
                  <!-- <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                      <label>Sub Text*</label>
                      <input type="text" name="sub_text" id="sub_text" class="form-control" value="<?php echo set_value('sub_text');?>" 
                      required>
                    </div>
                  </div> -->
                  <!-- <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                      <label>Category</label>
                      <input type="text" name="category" id="category" class="form-control" value="<?php echo set_value('category');?>">
                    </div>
                  </div> -->
                  
                  <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group">
                      <label>Image <small>(Accept format image only)</small></label>
                      <input type="file" name="file" id="file" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="form-group emoji">
                      <label>Message*</label>
                      <!-- <input type="text" name="name" id="name" class="form-control" value="<?php echo set_value('name');?>" 
                      required> -->
                      <textarea required="required" maxlength="30" name="offer_text" id="offer_text" class="form-control emoji_text"></textarea>
                    </div>
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
<style>
.emoji div{
  z-index: 99;
}
  </style>
<script src="<?=base_url('public/js/inputEmoji.js')?>"></script>

      <!-- End of Main Content -->
      <script type="text/javascript">
        $(document).ready(function() {
          $("#file").change(function () {
              var validExtensions = ["jpg","jpeg","png","gif"];
              var file = $(this).val().split('.').pop();
              if (validExtensions.indexOf(file) == -1) {
                  $("#file").val(null);
                  alert("Only formats are allowed : "+validExtensions.join(', '));
              }

              });
          //emoji
          $('.emoji_text').emoji({place: 'after'});
        })
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