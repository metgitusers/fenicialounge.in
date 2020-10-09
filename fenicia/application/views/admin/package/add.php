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
                  <h4 class="card-title">Club Membership Details</h4>
                  <a class="title_btn t_btn_list" href="<?= base_url(); ?>admin/package"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span>Club Membership List</a>
                </div>
              </div>
              <div class="card-body">
                <div class="px-3">
                  <?php
                  if (empty($package)) { ?>

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
                    <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/Package/addMember" enctype="multipart/form-data">
                      	<div class="form-body">
	                        <div class="row">	                        	
		                      	<div class="col-md-4">
			                        <div class="form-group">
			                          <label>Membership Name <sup>*</sup></label>
			                          <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required="" name="package_name" value="<?php echo set_value('package_name');?>">
			                        </div>
		                      	</div>
                            <div class="col-sm-4">
                                <div class="form-group" style="margin-bottom: 0;">
                                  <label>Membership Benefits<sup>*</sup></label>
                                   <div class="settlement_inline">
                                  <select id="benefit_id" class="js-select2" name="benefit_id[]" data-show-subtext="true" data-live-search="true" required multiple>
                                    <option value="">Select Benefits</option>
                                    <?php if(!empty($package_benefits)): ?>
                                    <?php   foreach($package_benefits as $blist): ?>
                                              <option value="<?php echo $blist['package_benefit_id'];?>"><?php echo $blist['benefit_name'];?></option>
                                    <?php   endforeach; ?>
                                    <?php endif; ?>
                                  </select>
                                  </div>
                                   <?php echo form_error('benefit_id', '<div class="error">', '</div>'); ?> 
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group" style="margin-bottom: 0;">
                                  <label>Membership Vouchers<sup>*</sup></label>
                                   <div class="settlement_inline">
                                  <select id="voucher_id" class="js-select2" name="voucher_id[]" data-show-subtext="true" required data-live-search="true" required multiple>
                                    <option value="">Select Vouchers</option>
                                    <?php if(!empty($package_vouchers)): ?>
                                    <?php   foreach($package_vouchers as $vlist): ?>
                                              <option value="<?php echo $vlist['package_voucher_id'];?>"><?php echo $vlist['voucher_name'];?></option>
                                    <?php   endforeach; ?>
                                    <?php endif; ?>
                                  </select>
                                  </div>
                                  <?php echo form_error('voucher_id', '<div class="error">', '</div>'); ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                              <div class="form-group">
                                  <label>Membership Title<sup>*</sup></label>
                                  <textarea  id="cms_description2" name="package_title" required="required" cols="80"></textarea>                                  
                              </div>
                              <?php echo form_error('package_title', '<div class="error">', '</div>'); ?>
                            </div>
                            <div class="col-md-12">
                              <div class="form-group">
                                  <label>Membership Description<sup>*</sup></label>
                                  <textarea  id="cms_description" name="package_description" required="required" rows="10" cols="80"></textarea>                                  
                              </div>
                              <?php echo form_error('package_description', '<div class="error">', '</div>'); ?>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group pb-1" style="display:flex;align-items: center;">
                                    <label style="margin-right:7px;">Inactive</label>
                                    <label class="switch" for="checkbox">
                                        <input value="1" name="status" type="checkbox" id="checkbox" checked/>
                                        <div class="slider round"></div>
                                    </label>
                                    <label style="margin-left:7px;">Active</label>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12" style="margin-bottom:15px">
                              <label>Membership Image<sup> (accept file extention - .gif,.jpg,.png,.jpeg)</sup></label>
                              <div class="panel panel-default">
                                  <div class="panel-body" align="center">
                                      <input type="file" accept=".gif,.jpg,.png,.jpeg" name="pkg_image[]" id="upload_image" multiple/>
                                      <br/>                                         
                                  </div>
                                  <div class="pull-right" id="profile_img_div"></div>
                                  <input type="hidden" name="pkg_img_name" id="pakg_img_name">
                              </div>
                            </div>
                            <div class="col-md-12" id="pkg_div">   
                              <div class="col-md-4">
                                <div class="form-group pkg_select_div">                                
                                  <label>Membership Type<sup>*</sup><a href="Javascript:void(0);" id="add_more_package_type" style="background: #239c91;margin-left:10px;padding: 0 10px;display: inline-block;color: #fff;float:right; border-radius: 3px; font-size: 12px;text-decoration: none;">Add More</a></label>
                                    <select name="package_type[]" class="form-control pkg_type" required>
                                      <option value="">Select membership type</option>
                                      <?php if(!empty($package_type)): ?>
                                      <?php   foreach($package_type as $pkg): ?>                                                                                   
                                                <option value="<?php echo $pkg['package_type_id']; ?>"><?php echo ucfirst($pkg['package_type_name']); ?></option>
                                      <?php   endforeach; ?>
                                      <?php endif; ?>
                                    </select>
                                    <div class="input-group pkg_type_price_div" style="display:none">
                                      <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">₹</span>
                                      </div> 
                                      <input class="pkg_type_price" type="number" min="1" name="package_type_price[]" placeholder="price" value="">  
                                    </div>
                                    <!--- added number input box on 15/07/20 by soma --->
                                    <div class="input-group number" style="display:none;margin-top: 10px;"> Duration:  
                                    
                                      <input class="number" name="package_type_number[]" type="text" placeholder=" No. of days" >
                                  </div>
                                  <!--- added number input box on 15/07/20 by soma --->
                                </div>                                
                              </div>                              
                            </div>
                          </div>  
	  						<div class="form-actions">
  								<a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/Package'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
  								<button type="submit" class="btn btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
				            </div>
                    </form>
                  <?php
                  } else {

                    ?>
                        <form class="form custom_form_style" method="Post" action="<?= base_url(); ?>admin/package/UpdatePackge/<?php echo $package['package_id']; ?>" enctype="multipart/form-data">
                          <div class="form-body">
                            <div class="row">         
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label>Membership Name <sup>*</sup></label>
                                  <input type="text" onkeypress="nospaces(this)" onkeyup="nospaces(this)" class="form-control" required="" name="package_name" value="<?php echo $package['package_name']?>">
                                </div>
                              </div>
                              <div class="col-sm-4">
                                  <div class="form-group" style="margin-bottom: 0;">
                                    <label>Membership Benefits<sup>*</sup></label>
                                     <div class="settlement_inline">
                                        <select id="benefit_id" class="js-select2" name="benefit_id[]" data-show-subtext="true" data-live-search="true" required multiple>
                                          <option value="">Select Benefits</option>
                                          <?php if(!empty($package_benefits)): ?>
                                          <?php   foreach($package_benefits as $blist): ?>
                                          
                                              <option value="<?php echo $blist['package_benefit_id'];?>" <?php if(in_array($blist['package_benefit_id'],$benifit_list)): echo 'selected';endif;?> ><?php echo $blist['benefit_name'];?></option>
                                          
                                          <?php   endforeach; ?>
                                          <?php endif; ?>
                                        </select>
                                    </div>
                                    <?php echo form_error('benefit_id', '<div class="error">', '</div>'); ?>
                                  </div>
                              </div>
                              <div class="col-sm-4">
                                  <div class="form-group" style="margin-bottom: 0;">
                                    <label>Membership Vouchers<sup>*</sup></label>
                                      <div class="settlement_inline">
                                        <select id="voucher_id" class="js-select2" data-show-subtext="true" data-live-search="true" name="voucher_id[]" required multiple>
                                          <option value="">Select Vouchers</option>
                                          <?php if(!empty($package_vouchers)): ?>
                                          <?php   foreach($package_vouchers as $vlist): ?>
                                                    <option value="<?php echo $vlist['package_voucher_id'];?>" <?php if(in_array($vlist['package_voucher_id'],$voucher_list)): echo 'selected';endif;?>><?php echo $vlist['voucher_name'];?></option>
                                          <?php   endforeach; ?>
                                          <?php endif; ?>
                                        </select>
                                      </div>
                                      <?php echo form_error('voucher_id', '<div class="error">', '</div>'); ?>
                                  </div>
                              </div>
                              	<div class="col-md-12">
	                              <div class="form-group">
	                                  <label>Membership Title<sup>*</sup></label>
	                                  <textarea  id="cms_description2" name="package_title" required="required" cols="80"><?php echo $package['package_title']?></textarea>                                  
	                              </div>
	                              <?php echo form_error('package_title', '<div class="error">', '</div>'); ?>
	                            </div>
                              <div class="col-md-12">
                                <div class="form-group">
                                    <label>Membership Description<sup>*</sup></label>
                                    <textarea  id="cms_description" name="package_description" required="required" rows="10" cols="80"><?php echo $package['package_description']?></textarea>
                                </div>
                                <?php echo form_error('package_description', '<div class="error">', '</div>'); ?>
                              </div>
                              <div class="col-md-12">
                                  <div class="form-group pb-1" style="display:flex;align-items: center;">
                                      <label style="margin-right:7px;">Inactive</label>
                                      <label class="switch" for="checkbox">
                                          <?php if($package['status'] =='1'): 
                                                  $checked  = 'checked';
                                                else:
                                                  $checked  = '';
                                                endif;
                                          ?>
                                          <input value="1" name="status" type="checkbox" id="checkbox" <?php echo $checked; ?>/>
                                          <div class="slider round"></div>
                                      </label>
                                      <label style="margin-left:7px;">Active</label>
                                  </div>
                              </div>
                              <div class="col-md-12">
                                <?php if(!empty($package_type)): ?>
                                <?php   foreach($image_list[$package['package_id']] as $pkg_img): ?>
                                  <div class="img_class" style="float:left">
                                      <img src="<?php echo base_url().'public/upload_image/package_image/'.$pkg_img['images']; ?>" width="100px" height="100px">
                                      <div><button class="btn pull-right btn-danger delete_pro_img" id="<?php echo $pkg_img['package_img_id']; ?>"><i class="fa fa-trash-o"></i></button></div>
                                  </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                              </div>
                              <div class="col-lg-6 col-md-12" style="margin-bottom:15px">
                                <label>Membership Image<sup> (accept file extention - .gif,.jpg,.png,.jpeg)</sup></label>
                                <div class="panel panel-default">
                                    <div class="panel-body" align="center">
                                        <input type="file" accept=".gif,.jpg,.png,.jpeg" name="pkg_image[]" id="upload_image" multiple/>
                                        <br/>                                         
                                    </div>
                                    <div class="pull-right" id="profile_img_div"></div>
                                    <input type="hidden" name="pkg_img_name" id="pakg_img_name">
                                </div>
                              </div>                              
                              <div class="col-md-12" id="pkg_div">                                
                                <?php     if(!empty($price_list[$package['package_id']])): ?> 
                                <?php       foreach($price_list[$package['package_id']] as $pr_list): ?> 
                                  <div class="col-md-4">                                  
                                    <div class="form-group pkg_select_div">                                
                                      <label>Membership Type<sup>*</sup></label>
                                      <a href="Javascript:void(0);" id="add_more_package_type" style="background: #f9b92d;margin-left:10px;padding: 0 10px;display: inline-block;color: #fff;border-radius: 3px; font-size: 12px;text-decoration: none;">Add More</a> 
                                        <select  name="package_type[]" class="form-control pkg_type" required>
                                          <option value="">Select membership type</option>
                                          <?php if(!empty($package_type)): ?>
                                          <?php   foreach($package_type as $pkg): ?> 
                                                        <option value="<?php echo $pkg['package_type_id']; ?>" <?php if($pkg['package_type_id'] == $pr_list['package_type_id']): echo 'selected';endif;?>><?php echo ucfirst($pkg['package_type_name']); ?></option>
                                          <?php   endforeach; ?>
                                          <?php endif; ?>                                        
                                        </select>
                                        <div class="input-group pkg_type_price_div" style="">
                                          <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">₹</span>
                                          </div> 
                                          <input class="pkg_type_price" type="number" name="package_type_price[]" placeholder="price" value="<?php echo $pr_list['price']; ?>">
                                         </div>
                                         <?php //echo '<pre>';print_r($pr_list);?>
                                        <!--- added number input box on 15/07/20 by soma --->
                                         <div class="input-group number" style="<?php if($pr_list['package_type_name']!='Custom'){ echo "display:none"; }?>;margin-top: 10px;"> Duration:
                                         <input class="number" name="package_type_number[]" type="text" placeholder="No. of days"  value="<?php echo $pr_list['number']; ?>" >
                                        </div>
                                          <!--- added number input box on 15/07/20 by soma --->
                                    </div>                                
                                  </div>
                                <?php   endforeach; ?>
                                <?php endif; ?>
                              </div>
                            </div> 
                          </div>                                                    
                          <div class="form-actions">
                            <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/Package'; ?>"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
                            <button type="submit" class="btn btn-success">
                              <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
                            </button>
                          </div>
                        </form>
                  <?php
                  }
                  ?>
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
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
<script type="text/javascript">
CKEDITOR.replace('cms_description');
CKEDITOR.replace('cms_description2');
CKEDITOR.config.basicEntities = false;
$("form").submit( function(e) {   
    var total_length    = CKEDITOR.instances['cms_description'].getData().replace(/<[^>]*>/gi, '').length;
    var total_length2    = CKEDITOR.instances['cms_description2'].getData().replace(/<[^>]*>/gi, '').length;
    if(!total_length2) {
      //alert(data_val);
        //$(".error").html('Please enter a description' );
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Please enter a membership tile',
        });
        e.preventDefault();
    }
    else if(!total_length) {
      //alert(data_val);
        //$(".error").html('Please enter a description' );
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Please enter a membership description',
        });
        e.preventDefault();
    }     
    else{
              
    }
});
$(document).ready(function() {
    $(".js-select2").select2({
      multiple: true,
    }); 
    
})
function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#pakg_img_name').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]);    
    $("#profile_img_div").show();
  }
}

$("#upload_image").change(function() {
  var ext = $('#upload_image').val().split('.').pop().toLowerCase();
  if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
      alert('Accept file extention - .gif,.jpg,.png,.jpeg. Please upload vaild file');
  }
  else{
    readURL(this);
  }   
});

$(document).on('click','#add_more_package_type',function(){
    $.ajax({
      type: "POST",
      url: '<?php echo base_url('admin/Package/ajaxAddmorePackageType')?>',
      dataType:'JSON',
      success: function(response){
        if(response['html']){
            $('#pkg_div').append(response['html']);
        }
        else{
          //nothing to do 
        }
      },
      error:function(response){
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Error',
        });
      }
    });
});
$(document).on('click','.delete_pro',function(){
    if(confirm("Are you sure you want to delete this?")){
       $(this).parent().parent().remove();
        
    }
    return false;
});
$(document).on('click','.delete_pro_img',function(){
    if(confirm("Are you sure you want to delete this ?")){
       $(this).parent().parent().remove();
       var package_img_id = $(this).attr('id');
       //alert(package_img_id);
       $.ajax({
          type: "POST",
          url: '<?php echo base_url('admin/Package/DeleteImage')?>',
          data:{package_img_id:package_img_id},
          dataType:'html',
          success: function(response){
            //alert(response);
            if(response ==1 ){
                 $.alert({
                     type: 'green',
                     title: 'Alert!',
                     content: 'Successfully deleted.',
                  });
            }
            else{
              //nothing to do 
            }
          },
          error:function(response){
            $.alert({
               type: 'red',
               title: 'Alert!',
               content: 'Error',
            });
          }
      }); 
    }
    return false;
});
//$(document).on('change','.pkg_type',function(){
  // $(this).closest("div.pkg_select_div").find(".pkg_type_price_div").show();
    
//});
/* modified on 15/07/20 by soma */
$(document).on('change','.pkg_type',function(){
      $(this).closest("div.pkg_select_div").find(".pkg_type_price_div").show();
      $(this).closest("div.pkg_select_div").find(".number").css('display', (this.value == '3') ? 'block' : 'none');
});

var pkg_type_id=$('.pkg_type').find('option:selected').val();

//alert(pkg_type_id);
if(pkg_type_id==3){

 $(this).closest("div.pkg_select_div").find(".number").css('display', (this.value == '3') ? 'block' : 'none');

}else{
   $(this).closest("div.pkg_select_div").find(".pkg_type_price_div").show();
}

</script>
