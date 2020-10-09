<?php
/**
 * The template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

if(!empty(get_field('venuerightimagegallery'))){?>
<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 forgap2">
							<div class="page_middle">
								<div class="page_middle_breadcrumb">
									<?php get_breadcrumb(); ?>
								</div>
								<div class="page_middle_info mCustomScrollbar">
									 
									 <?php the_content();?>
									 <div class="row gallery">
										
										<?php 

											$images = get_field('venuetextraimagegallery');

											if( $images ): ?>
											     
											        <?php foreach( $images as $image ): ?>
											            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
											            <div class="gallery_box">    
											                <a href="<?php echo $image['url']; ?>"class="big" >
											                     <img src="<?php bloginfo('template_directory'); ?>/inc/timthumb/timthumb.php?src=<?php echo $image['url']; ?>&w=139&h=156&zc=1&q=100" alt="" />
											                </a>
											            </div>
										                </div>     
											        <?php endforeach; ?>
											     
											<?php endif; ?>
										 
										  
									</div>
								</div>

									
								 
							</div>
						</div>
						
						<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 forgap3">
							<div class="page_right">
 <div id="fadeCarousel" class="carousel slide carousel-fade" data-ride="carousel" data-interval="4500">
 	<?php /**11.06.2019 As er discussion with Aridam Dutta***/ ?>
   <!--  <ol class="carousel-indicators">
     <li data-target="#fadeCarousel" data-slide-to="0" class="active"></li>
     <li data-target="#fadeCarousel" data-slide-to="1"></li>
     <li data-target="#fadeCarousel" data-slide-to="2"></li>
   </ol> -->
    	

    <div class="carousel-inner">
    	<?php 

$images = get_field('venuerightimagegallery');
 
if( $images ): $c = 0;  foreach( $images as $image ): ?>
            <div class="item <?php if($c==0){echo 'active';} else { echo '';}?>">
                
                <img src="<?php echo $image['url'];?>" alt="<?php echo $image['alt']; ?>">
                     
                 </div>
             
        <?php $c++; endforeach; ?>
    
<?php endif; ?>
   
 </div>
<!--  
    <a class="left carousel-control" href="#fadeCarousel" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#fadeCarousel" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right"></span>
      <span class="sr-only">Next</span>
    </a>-->
</div> 

						</div>
								 
                            </div>

                           
						<?php } else{
$image = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), 'full' );
if($image){ 
	$col = "5";
}else{
	$col = "10";
}
?>
							<div class="col-xs-12 col-sm-12 col-md-<?php echo $col;?> col-lg-<?php echo $col;?> forgap2">
							<div class="page_middle">
								<div class="page_middle_breadcrumb">
									<?php get_breadcrumb(); ?>
								</div>
								<div class="page_middle_info mCustomScrollbar">
								<div class="eventboxinner">
									<?php if ( is_single(290) ) {}else{?>
									 <ul class="eventtime">
								<li><i class="fa fa-clock-o" aria-hidden="true"></i><?php the_field('event_time');?></li>
								<li><i class="fa fa-calendar" aria-hidden="true"></i><?php echo get_the_date();?></li>
								<li><i class="fa fa-map-marker" aria-hidden="true"></i><?php the_field('event_location');?></li>
									 

							</ul>
									<?php }?>
							<?php the_content();?>
								</div>
								</div>
							</div>
						</div>
						<?php if($image){?><div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 forgap3">
							<div class="page_right">
                                               	<img src="<?php echo $image[0]; ?>" width="100%" alt="<?php the_title(); ?>" />
                                              	
                                               	
                                               	
                                               	</div>
						</div><?php }?>
						<?php } ?>