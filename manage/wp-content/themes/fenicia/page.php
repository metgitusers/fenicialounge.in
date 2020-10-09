<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

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
						 
						<?php get_template_part( 'template-parts/content', 'page' );?>
						 
					</div>
				</div>
<?php get_footer(); ?>
