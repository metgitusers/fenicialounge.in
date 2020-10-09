<?php
/**
 * The template used for displaying page content
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */


if(has_post_thumbnail()){?>
<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 forgap2">
							<div class="page_middle">
								<div class="page_middle_breadcrumb">
									<?php get_breadcrumb(); ?>
								</div>
								<div class="page_middle_info mCustomScrollbar">
									 
									 <?php the_content();?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 forgap3">
							<div class="page_right">

								<?php $image = wp_get_attachment_image_src( 
                                               get_post_thumbnail_id( $post->ID ), 'full' );?>
                                               	<img src="<?php echo $image[0]; ?>" width="100%" alt="<?php the_title(); ?>" />
                            </div>
						</div>
						<?php } else{?>
							<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 forgap2">
							<div class="page_middle">
								<div class="page_middle_breadcrumb">
									<?php get_breadcrumb(); ?>
								</div>
								<div class="page_middle_info mCustomScrollbar">
									 
									 <?php the_content();?>
								</div>
							</div>
						</div>
						<?php } ?>