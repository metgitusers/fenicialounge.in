<div class="main-content">
    <div class="content-wrapper">
        <div class="container-fluid">
            <section id="basic-form-layouts">
              <div class="row">
                  <div class="col-sm-12">
                  <?php if(!empty($event_list)): ?>
                      <h2 class="content-header"><?php echo $event_list['event_name'];?></h2>
                      <a style="margin-bottom:10px" class="title_btn album_btn_list" href="<?= base_url();?>admin/past-events"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> Past Event List</a>
                  <?php endif;?>                   
                  </div>
              </div>
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
              <section id="hover-effects" class="card">
                <div class="card-body">
                  <div class="past_event_upload_bttn" id="upload_event_img" style="margin:20px 10px 0 10px">
                    <h4 class="form-section">Upload Event Images:<sup style="font-size:12px">* (accept file extention - .gif,.jpg,.png,.jpeg)</sup></h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">                                                          
                                    <div class="custom-file">
                                        <form action ="<?php echo base_url().'admin/event/addPastEventImages'; ?>" method="post" enctype="multipart/form-data">
                                          <input type="hidden" name="event_id" value="<?php echo $this->uri->segment(4);?>">
                                          <input type="file" accept=".gif,.jpg,.png,.jpeg" name="past_event_img[]" class="custom-file-input" id="past_event_img" required="required" multiple>
                                          <label class="custom-file-label" for="inputGroupFile01">Select Images</label><br><br><br>
                                          <input type="submit" value="Upload">
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="past_event_img_div" style="margin-top: 27px;"></div>                                           
                        </div>
                  </div>
                  <div class="card-block my-gallery" itemscope itemtype="http://schema.org/ImageGallery">
                    <div class="grid-hover">
                      <div class="row">
                      <?php if(!empty($event_img_list)): ?> 
                      <?php   foreach($event_img_list as $img_list):?>                      
                                <div class="col-md-4">
                                  <figure class="effect-julia">
                                    <a href="<?php echo base_url().'public/upload_image/past_event_images/'.$img_list['images'];?>" data-fancybox data-caption="<?php if(!empty($event_list)): echo $event_list['event_name']; endif;?>">
                                      <img style="width: 100%; object-fit: cover;" src="<?php echo base_url().'public/upload_image/past_event_images/'.$img_list['images']; ?>" alt="<?php echo $img_list['images']; ?>" />  
                                      <a class="edit_bttn delete_btn" href="<?php echo base_url().'admin/event/DeletePastEventImage/'.$img_list['past_event_image_id'].'/'.$img_list['event_id']; ?>"><button class="img_over_delete"><i class="fa fa-trash" aria-hidden="true"></i></button></a>    
                                    </a>                              
                                  </figure>
                                </div>
                      <?php endforeach; ?>  
                      <?php else: ?>
                        <div class="col-md-12">
                          <h4>No image Available</h4>
                        </div>
                      <?php endif; ?>                        
                      </div>
                    </div>
                  </div>
                </div>
              </section>
<!--Gallery Hover Effect Starts-->
            </section>
        </div>
    </div>
</div>
<style type="text/css">
  figure.my-thumb {
      min-width: 255px;
      max-width: 255px;
      overflow: hidden;
  }
</style>
<script>
function readURL(input) {
    if (input.files) {      
        var filesAmount = input.files.length;
        $('#past_event_img_div').html("");
        for (i = 0; i < filesAmount; i++) {
            var reader = new FileReader();
            reader.onload = function(event) {
                
                $('#past_event_img_div').append('<div style="float:left"><img src="'+event.target.result+'" width="100px" height="100px" style="margin:8px"><a class="btn pull-right btn-danger delete_pro" id="delete"><i class="fa fa-trash-o"></i></a></div>');
            }
            reader.readAsDataURL(input.files[i]);
        }
    }
}
$("#past_event_img").change(function() {
  var ext = $('#past_event_img').val().split('.').pop().toLowerCase();
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
/*$('a.delete_pro').confirm({
    title: "confirm Delete",    
    content: "Are you sure you want to delete?",  
      buttons: {        
        confirm: function () {
            //this.$target.parent().remove();
            alert("cvbd");
        },
        cancel: function () {
            $.alert('Canceled!');
        }
      }
});*/
</script> 