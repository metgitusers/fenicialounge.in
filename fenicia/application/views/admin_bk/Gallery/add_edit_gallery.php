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
                                        <h4 class="card-title">Album Management</h4>
                                        <a class="title_btn t_btn_list" href="<?= base_url();?>admin/gallery/"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> Album List</a>
                                    </div>
                                    
                                    
                                    <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
                                </div>
                                <div class="card-body">
                                    <div class="px-3">
                                        <?php
                                            if(empty($gallery_list))
                                            {
                                        ?>
                                        <form class="form custom_form_style" method="post" action="<?= base_url();?>admin/gallery/addGallery" enctype="multipart/form-data">
                                            <div class="form-body">                                                    
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Album Name<sup>*</sup></label>
                                                            <input onkeypress="nospaces(this)" type="text" class="form-control" name="gallery_name" required="required">
                                                        </div>
                                                    </div>
                                                   <!-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Album Link</label>
                                                            <input onkeypress="nospaces(this)" type="url" class="form-control" name="gallery_link">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Album Title<sup>*</sup></label>
                                                            <input onkeypress="nospaces(this)" type="text" class="form-control" name="gallery_text" required="required">
                                                        </div>
                                                    </div>-->
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
                                                <h4 class="form-section">Album Image:</h4>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Album Image<sup>* (accept file extention - .gif,.jpg,.png,.jpeg)</sup></label>
                                                        <div class="input-group mb-3">                                                          
                                                            <div class="custom-file">
                                                                <input type="file" accept=".gif,.jpg,.png,.jpeg" name="gallery_img[]" class="custom-file-input" id="gallery_img" required multiple>
                                                                <label class="custom-file-label" for="inputGroupFile01">Select Image</label>
                                                            </div>
                                                        </div>
                                                  </div>
                                                    <div class="col-md-12" id="gallery_img_div" style="margin-top: 27px;"></div>                                           
                                                </div>
                                            </div>
                                            <div class="form-actions">
                                                <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/Gallery'; ?>">
                                                  <i class="fa fa-times" aria-hidden="true"></i> Cancel
                                                </a>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
                                                </button>
                                            </d>
                                        </form>
                                        <?php } else { ?>                                        
                                        <form class="form custom_form_style" method="post" action="<?= base_url();?>admin/Gallery/UpdateGallery/<?=$gallery_list['gallery_id']?>" enctype="multipart/form-data">
                                            <div class="form-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Album Name<sup>*</sup></label>
                                                            <input onkeypress="nospaces(this)" type="text" class="form-control" name="gallery_name" value="<?php echo $gallery_list['gallery_name']; ?>" required="required">
                                                        </div>
                                                    </div>
                                                    <!--<div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Album Link</label>
                                                            <input onkeypress="nospaces(this)" type="url" class="form-control" name="gallery_link" value="<?php echo $gallery_list['gallery_link']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Album Title<sup>*</sup></label>
                                                            <input onkeypress="nospaces(this)" type="text" class="form-control" name="gallery_text" value="<?php echo $gallery_list['gallery_text']; ?>" required="required">
                                                        </div>
                                                    </div> --> 
                                                    <div class="col-md-12">
                                                        <div class="form-group pb-1" style="display:flex;align-items: center;">
                                                            <label style="margin-right:7px;">Inactive</label>
                                                            <label class="switch" for="checkbox">
                                                                <?php if($gallery_list['status'] =='1'): 
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
                                                <h4 class="form-section">Album Image:</h4>
                                                <div class="row">
                                                    <div class="col-md-12" style="margin-top: 27px;">
                                                        <?php if($gallery_img_list): 
                                                              foreach($gallery_img_list as $img_list): ?>
                                                                <div style="float:left">
                                                                    <img src="<?php echo base_url().'public/upload_image/gallery/'.$img_list['gallery_image'];?>" width="100px" height="100px" style="margin:8px">
                                                                    <button class="btn btn-danger delete_pro_img" id="<?php echo $img_list['gallery_img_id']; ?>"><i class="fa fa-trash-o"></i></button>
                                                                </div>    
                                                        <?php   endforeach; ?>
                                                        <?php endif; ?>        
                                                    </div>                                                
                                                    <div class="col-md-6">
                                                        <label>Album Image<sup>* (accept file extention - .gif,.jpg,.png,.jpeg)</sup></label>
                                                        <div class="input-group mb-3">                                                          
                                                            <div class="custom-file">
                                                                <input type="file" accept=".gif,.jpg,.png,.jpeg" name="gallery_img[]" class="custom-file-input" id="gallery_img" multiple>
                                                                <label class="custom-file-label" for="inputGroupFile01">Select Image</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12" id="gallery_img_div" style="margin-top: 27px;"></div>                                           
                                                </div>
                                            </div>
                                            <div class="form-actions">
                                                <a class="btn btn-danger mr-1" href="<?php echo base_url().'admin/Gallery'; ?>">
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

<script>
function nospaces(t){
    if(t.value.match(/\s/g) && t.value.length == 1){
        alert('Sorry, you are not allowed to enter any spaces in the starting.');

        t.value=t.value.replace(/\s/g,'');
    }

}
function readURL(input) {
    if (input.files) {
        var filesAmount = input.files.length;
        $('#gallery_img_div').html("");
        for (i = 0; i < filesAmount; i++) {
            var reader = new FileReader();
            reader.onload = function(event) {
                
                $('#gallery_img_div').append('<div style="float:left"><img src="'+event.target.result+'" width="100px" height="100px" style="margin:8px"></div>');
            }
            reader.readAsDataURL(input.files[i]);
        }
    }
}
$("#gallery_img").change(function() {
  var ext = $('#gallery_img').val().split('.').pop().toLowerCase();
  if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
      alert('Accept file extention - .gif,.jpg,.png,.jpeg. Please upload vaild file');
  }
  else{
    readURL(this);
  } 
}); 
$(document).on('click','.delete_pro',function(){
    if(confirm("Are you sure you want to delete?")){
       $(this).parent().remove();        
    }
    return false;
}); 
$(document).on('click','.delete_pro_img',function(){
    if(confirm("Are you sure you want to delete?")){
       $(this).parent().parent().remove();
       var gallery_img_id = $(this).attr('id');
       //alert(package_img_id);
       $.ajax({
          type: "POST",
          url: '<?php echo base_url("admin/gallery/DeleteImage")?>',
          data:{gallery_img_id:gallery_img_id},
          dataType:'html',
          success: function(response){
            if(response ==1 ){
             $.alert({
                 type: 'green',
                 title: 'Alert!',
                 content: 'Successfully deleted.',
              });
              location.reload();                 
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
</script>



