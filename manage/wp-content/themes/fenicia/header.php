<?php

/**

 * The template for displaying the header

 *

 * Displays all of the head element and everything up until the "site-content" div.

 *

 * @package WordPress

 * @subpackage Twenty_Sixteen

 * @since Twenty Sixteen 1.0

 */



?><!DOCTYPE html>

<html <?php language_attributes(); ?> class="no-js">

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>

	<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">

	<?php endif; ?>

	<?php wp_head(); global $option_data;?>

</head>



<body <?php body_class(); ?>>



<!-- <div id="site-wrapper"> -->

	<!-- <div id="site-canvas"> -->

		<!-- <div id="site-menu">

			

		  </div> -->

		  <?php if(is_front_page()){?>

		   

			<header class="main_header">

				<div class="container-fluid">

					<div class="top_logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">

							<img src="<?php echo $option_data['custom_logo']; ?>" alt="">

						</a></div>

					<div class="header_navigation">

						<div class="header_navigation_inner">

								<div id="main-nav" class="stellarnav">

                            <?php wp_nav_menu( array( 

								'menu'            => 'header menu', 

								'container'       => 'ul', 

								'container_class' => '', 

								'container_id'    => '',  

								'menu_id'         => ''



							)); ?>



							

						</div>

						<div class="top_social">

							<?php if($option_data['facebook_link']):?>

							<a href="<?php echo $option_data['facebook_link'];?>" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a>

							<?php endif;?>

							<?php //if($option_data['twitter_link']):?>

							<!-- <a href="<?php //echo $option_data['twitter_link'];?>" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a> -->

							<?php //endif;?>

							<?php if($option_data['youtube_link']):?>

							<a href="<?php echo $option_data['youtube_link'];?>" target="_blank"><i class="fa fa-youtube" aria-hidden="true"></i></a>

							<?php endif;?>

							<?php if($option_data['instagram_link']):?>

							<a href="<?php echo $option_data['instagram_link'];?>" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a>

							<?php endif;?>
							<a class="gplaystore" href="https://play.google.com/store/apps/details?id=com.fenicia&hl=en" target="_blank"><img src="https://www.fenicialounge.in/manage/wp-content/uploads/2019/04/googleplay.png" alt="" /></a>

						</div>

						<div class="top_reserv"><a href="<?php the_permalink(182);?>">Reservations</a></div>

						

					</div>
						

				</div>

			</header>

			<?php } else { ?>

				<header class="main_header inner_page">

				<div class="container-fluid">

					<div class="header_navigation">

						<div class="header_navigation_inner">

								<div id="main-nav" class="stellarnav">

                            <?php wp_nav_menu( array( 

								'menu'            => 'header menu', 

								'container'       => 'ul', 

								'container_class' => '', 

								'container_id'    => '',  

								'menu_id'         => ''



							)); ?>



							

						</div>

						<div class="top_social">

							<?php if($option_data['facebook_link']):?>

							<a href="<?php echo $option_data['facebook_link'];?>" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a>

							<?php endif;?>

							<?php if($option_data['twitter_link']):?>

							<a href="<?php echo $option_data['twitter_link'];?>" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a>

							<?php endif;?>

							<?php if($option_data['youtube_link']):?>

							<a href="<?php echo $option_data['youtube_link'];?>" target="_blank"><i class="fa fa-youtube" aria-hidden="true"></i></a>

							<?php endif;?>

							<?php if($option_data['instagram_link']):?>

							<a href="<?php echo $option_data['instagram_link'];?>" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a>

							<?php endif;?>
							
							<a class="gplaystore" href="https://play.google.com/store/apps/details?id=com.fenicia&hl=en" target="_blank"><img src="https://www.fenicialounge.in/manage/wp-content/uploads/2019/04/googleplay.png" alt="" /></a>

						</div>

						<div class="top_reserv"><a href="<?php the_permalink(182);?>">Reservations</a></div>

						</div>

					</div>

				</div>

			</header>

			<?php } ?>