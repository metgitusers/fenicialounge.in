
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
                                        <h4 class="card-title">CMS Management</h4>
                                        <a class="title_btn t_btn_list" href="<?= base_url();?>admin/cms/"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> CMS List</a>
                                    </div>
                                    
                                    
                                    <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
                                </div>
                                <div class="card-body">
                                    <div class="px-3">
                                        <?php
                                            if(empty($cms_data))
                                            {
                                        ?>
                                        <form id="cms_form" class="form custom_form_style" method="post" action="<?= base_url();?>admin/cms/add">
                                            <div class="form-body">
                                                    
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Page Name<sup>*</sup></label>
                                                            <input onkeypress="nospaces(this)" type="text" class="form-control" id="page_name" name="page_name" >
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Short Description<sup>*</sup></label>
                                                            <input onkeypress="nospaces(this)" type="text" class="form-control" id="short_description" name="short_description" >
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Description<sup>*</sup></label>
                                                            <textarea  id="cms_description" name="cms_description" required="required" rows="10" cols="80"></textarea>
                                                        </div>
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
                                                </div>
                                            </div>

                                            <div class="form-actions">
                                                <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/cms'; ?>">
                                                  <i class="fa fa-times" aria-hidden="true"></i> Cancel
                                                </a>
                                                <button type="submit" id="add_page" class="btn btn-success">
                                                    <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
                                                </button>
                                            </div>
                                        </form>

                                        <?php } else { ?>
                                        
                                        <form id="edit_cms_form" class="form custom_form_style" method="post" action="<?= base_url();?>admin/cms/edit_cms/<?=$cms_data['page_id']?>">
                                            <div class="form-body">
                                            <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Page Name<sup>*</sup></label>
                                                            <input onkeypress="nospaces(this)" type="text" class="form-control" id="edit_page_name" name="page_name" required="required" value="<?=$cms_data['page_name']?>">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Short Description<sup>*</sup></label>
                                                            <input onkeypress="nospaces(this)" type="text" class="form-control" id="edit_short_description" name="short_description" required="required" value="<?=$cms_data['short_desc']?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Description<sup>*</sup></label>
                                                            <textarea  id="edit_cms_description" name="cms_description" required="required" rows="10" cols="80"><?=$cms_data['description']?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                        <div class="form-group pb-1" style="display:flex;align-items: center;">
                                                            <label style="margin-right:7px;">Inactive</label>
                                                            <label class="switch" for="checkbox">
                                                                <?php if($cms_data['status'] =='1'): 
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
                                            </div>

                                            <div class="form-actions">
                                                <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/cms'; ?>">
                                                  <i class="fa fa-times" aria-hidden="true"></i> Cancel
                                                </a>
                                                <button type="submit" id="edit_page" class="btn btn-success">
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
$(document).on("click","#add_page",function(e) {
    //alert("dgjdk");
    e.preventDefault();   
    var page_name       = $("#page_name").val();
    var short_description       = $("#short_description").val();
    var total_length    = CKEDITOR.instances['cms_description'].getData().replace(/<[^>]*>/gi, '').length;
    //alert(total_length);
    var cnt = 0;
    if(page_name ==""){
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Please enter page name.',
        });
        cnt++;
    }
    else if(short_description ==""){
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Please enter short description.',
        });
        cnt++;
    }
    else if(!total_length) {
      //alert(total_length);
        //$(".error").html('Please enter a description' );
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Please enter a description',
        });
       cnt++;
    }    
    if(cnt != 0){
        return false;
    }
    else{
         $("#cms_form").submit();     
    }
}); 
 
$(document).on("click","#edit_page",function(e) {
    e.preventDefault(); 
    var edit_page_name           = $("#edit_page_name").val();
    var edit_short_description   = $("#edit_short_description").val();
    var edit_total_length        = CKEDITOR.instances['edit_cms_description'].getData().replace(/<[^>]*>/gi, '').length;
    //alert(edit_total_length);
    var cnt = 0;
    if(edit_page_name ==""){
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Please enter page name.',
        });
        cnt++;
    }
    else if(edit_short_description ==""){
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Please enter short description.',
        });
        cnt++;
    }
    else if(!edit_total_length) {
      //alert(total_length);
        //$(".error").html('Please enter a description' );
        $.alert({
           type: 'red',
           title: 'Alert!',
           content: 'Please enter a description',
        });
       cnt++;
    }    
    if(cnt != 0){
        return false;
    }
    else{
         $("#edit_cms_form").submit();     
    }   
});   
</script>

