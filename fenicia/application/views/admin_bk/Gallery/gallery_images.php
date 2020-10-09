<div class="main-content">
    <div class="content-wrapper">
        <div class="container-fluid">
            <section id="basic-form-layouts">
              <div class="row">
                  <div class="col-sm-12">
                  <?php if(!empty($gallery_list)): ?>
                      <h2 class="content-header"><?php echo $gallery_list['gallery_name'];?></h2>
                      <a class="title_btn album_btn_list" href="<?= base_url();?>admin/gallery/"><span><i class="fa fa-list-ul" aria-hidden="true"></i></span> Album List</a>
                  <?php endif;?>                   
                  </div>
              </div>
              <section id="hover-effects" class="card">
                <div class="card-body">
                  <div class="card-block my-gallery" itemscope itemtype="http://schema.org/ImageGallery">
                    <div class="grid-hover">
                      <div class="row">
                      <?php if(!empty($gallery_img_list)): ?> 
                      <?php   foreach($gallery_img_list as $img_list):?>                      
                                <div class="col-md-4">
                                  <figure class="effect-julia">
                                    <a href="<?php echo base_url().'public/upload_image/gallery/'.$img_list['gallery_image'];?>" data-fancybox data-caption="<?php if(!empty($gallery_list)): echo $gallery_list['gallery_name']; endif;?>">
                                      <img style="width: 100%; object-fit: cover;" src="<?php echo base_url().'public/upload_image/gallery/'.$img_list['gallery_image']; ?>" alt="img12" />      
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
/*$(document).ready(function() {
    var menu_id = $("#menu_id").val();
	//alert(menu_id);
 	$.ajax({
        type: "POST",
        url: '<?php echo base_url('admin/index/Ck_User_Permission/')?>',
        data:{menu_id:menu_id},
        dataType:'json',
        success: function(response){   
        alert(response);           
          //$('#modalContent').html(response.message);  
          //$('#myModal').modal('show');
       // alert(response['add_flag']);
	      	if(response['add_flag'] =='0'){
			    $(".add_bttn").remove();
			    $(".rev_status").css('display','none');
			    
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
 });*/
 </script>