
<div class="main-content">
          <div class="content-wrapper">
            <div class="container-fluid"><!-- Basic form layout section start -->
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
                                        <h4 class="card-title">Membership Voucher Management</h4>
                                        <a class="title_btn t_btn_list" href="<?= base_url();?>admin/PackageVoucher"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> Voucher List</a>
                                    </div>
                                    
                                    
                                    <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
                                </div>
                                <div class="card-body">
                                    <div class="px-3">
                                        <?php
                                            if(empty($pck_voucher_data))
                                            {
                                        ?>
                                        <form class="form custom_form_style" method="post" action="<?= base_url();?>admin/PackageVoucher/save">
                                            <div class="form-body">
                                                    
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Voucher Name<sup>*</sup></label>
                                                            <input onkeypress="nospaces(this)" type="text" class="form-control" name="voucher_name" required="required">
                                                            <?php echo form_error('voucher_name', '<div class="error">', '</div>'); ?>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Voucher Description<sup>*</sup></label>
                                                            <textarea  id="cms_description" name="voucher_description" required="required" rows="10" cols="80"></textarea>
                                                            <?php echo form_error('voucher_description', '<div class="error">', '</div>'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-actions">
                                                <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/PackageVoucher'; ?>">
                                                  <i class="fa fa-times" aria-hidden="true"></i> Cancel
                                                </a>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
                                                </button>
                                            </div>
                                        </form>

                                        <?php } else { ?>
                                        
                                        <form class="form custom_form_style" method="post" action="<?= base_url();?>admin/PackageVoucher/UpdateVoucher/<?=$pck_voucher_data['package_voucher_id']?>">
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Voucher Name<sup>*</sup></label>
                                                            <input onkeypress="nospaces(this)" type="text" class="form-control" name="voucher_name" required="required" value="<?=$pck_voucher_data['voucher_name']?>">
                                                        </div>
                                                    </div>                                                    
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Voucher Description<sup>*</sup></label>
                                                            <textarea  id="cms_description" name="voucher_description" required="required" rows="10" cols="80"><?=$pck_voucher_data['voucher_description']?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions">
                                                <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/PackageVoucher'; ?>">
                                                  <i class="fa fa-times" aria-hidden="true"></i> Cancel
                                                </a>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa fa-floppy-o" aria-hidden="true"></i> Update
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
<script>
function nospaces(t){
    if(t.value.match(/\s/g) && t.value.length == 1){
        alert('Sorry, you are not allowed to enter any spaces in the starting.');

        t.value=t.value.replace(/\s/g,'');
    }

}
 CKEDITOR.replace('cms_description');
 CKEDITOR.config.basicEntities = false;
  $("form").submit( function(e) {   
    var total_length    = CKEDITOR.instances['cms_description'].getData().replace(/<[^>]*>/gi, '').length;
    if(!total_length) {
      //alert(total_length);
        //$(".error").html('Please enter a description' );
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Please enter a description',
        });
        e.preventDefault();
    }
    else{
              
    }
});
</script>


