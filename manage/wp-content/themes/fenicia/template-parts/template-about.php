<?php
/*
*Template Name:About-us
*/
get_header();
?>
<section class="body_wrapper" id="main">
				<div class="container-fluid">
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 forgap1">
								<div class="page_left_menu">
									<div class="page_left_menu_logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" >
                <img src="<?php echo $option_data['custom_logo']; ?>" alt="">
              </a></div>
									<div class="page_left_menu_listing">
										<ul>
											<?php
										$args = array(
										  'post_type'   => 'venue',
										  'post_status' => 'publish',
										  'posts_per_page' => 5
										 );
 
									$events = new WP_Query( $args );
									if( $events->have_posts() ) :

							        while( $events->have_posts() ) :
							        $events->the_post();
							        ?>
							        <li><a href="<?php the_permalink();?>"><?php the_title();?></a></li>
							        <?php
								    endwhile; endif;
								    wp_reset_postdata();
								    ?>
										</ul>
									</div>
									<div class="page_left_footer mobiledisplaynone">
										<p>&copy; <?php echo date('Y');?> <a href="<?php echo home_url();?>">Fenicia</a>. All rights reserved | Designed by <a href="https://www.fitser.com/" target="_blank">Fitser</a>.</p>
									</div>
								</div>
							
						</div>
						    <?php   while(have_posts() ) :
							        the_post();
							        ?>
						<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 forgap2">
							<div class="page_middle">
								<div class="page_middle_breadcrumb">
									 <ul>
										<li><a href="<?php echo home_url();?>">Home</a></li>
										<li class="selected"><a href="<?php the_permalink();?>"><?php the_title();?></a></li>
									</ul>
								</div>
								<div class="page_middle_info mCustomScrollbar">
									 
									<?php the_content();?>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 forgap3">
							<div class="page_right"><?php $image = wp_get_attachment_image_src( 
                                               get_post_thumbnail_id( $post->ID ), 'full' );?>
                                               	<img src="<?php echo $image[0]; ?>" width="100%" alt="<?php the_title(); ?>" />
                                              	
                                               	
                                               	
                                               	</div>
						</div>
						<?php endwhile; wp_reset_postdata();?>
					</div>
				</div>
				<div class="page_left_footer mobilefooter">
							<p>&copy; <?php echo date('Y');?> <a href="<?php echo home_url();?>">Fenicia</a>. All rights reserved | Designed by <a href="https://www.fitser.com/" target="_blank">Fitser</a>.</p>
						</div>
						<script>
	(function(jQuery){
		//jQuery(window).on("load",function(){

			jQuery("body").mCustomScrollbar({
				theme:"minimal"
			});

		//});
	})(jQuery);
</script>
<?php get_footer();?>