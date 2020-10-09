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
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
<?php endif; ?>
<?php wp_head(); ?>
</head>

<body <?php body_class(); global $option_data;?>>
	<?php if(is_front_page()){?>
		<div class="main_header">
			<div class="header">
				<div class="container">
					<div class="col-md-3 col-sm-12 col-xs-12">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
							<img src="<?php echo $option_data['custom_logo']; ?>" alt="">
						</a>

					</div>
					<div class="col-md-9 col-sm-12 col-xs-12">
						<div class="header_right">
							<div id="main-nav" class="stellarnav">
								<?php wp_nav_menu( array( 
									'menu'            => 'header menu', 
									'container'       => 'ul', 
									'container_class' => '', 
									'container_id'    => '',  
									'menu_id'         => ''

								)); ?>
							</div><!-- .stellar-nav -->
							<div class="search_panel">
								<a href="#"><img src="<?php echo bloginfo('template_url');?>/assets/images/search_icon.png" alt="search_icon" /></a><?php get_search_form();?>
							</div>
						</div>
					</div>
				</div>
			</div>


			<div class="container">
				<div class="slider">
					<div class="inner_slider">
						<div id="product-carousel" class="owl-carousel">
							<?php $i = 0; while ( have_rows('banner') ) : the_row();?>
							<div class="item <?php if($i == 0){ echo "active";}else{ echo "";}?>">
								<?php $image = get_sub_field('bannerimage');?>
								<img src="<?php echo $image['url']; ?>" width="100%" alt="<?php the_title(); ?>" />
							</div>
							<?php $i++; endwhile;?>
						</div>
					</div>
				</div>
				<!-- <div class="banner_left">
					<?php //$img = get_field('bannerleftimage'); ?>
					<img src="<?php //echo $img['url'];?>" alt="man_img" />
					<?php $appimg //= get_field('smartphoneappimage'); ?>
					<a href="<?php //the_permalink(53)?>">
						<img src="<?php //echo $appimg['url']; ?>" alt="smart_phone_app" /></a>
						<?php $betterimg //= get_field('betterthanimage'); ?>
						<a href="#"><img src="<?php //echo $betterimg['url']; ?>" alt="better_than" /></a>
						<div class="clr"></div>
						<div class="banner_left_bottom">
							<a href="#"><img src="<?php //echo bloginfo('template_url');?>/assets/images/google_play.png" alt="google_play" /></a>
							<a href="#"><img src="<?php //echo bloginfo('template_url');?>/assets/images/app_store.png" alt="app_store" /></a>
							<a href="#" class="recharge">Buy a Recharge Pin Here</a>
						</div>
					</div> -->
				</div>
				<div class="clr"></div>
			</div>
		<?php } elseif(get_the_ID() == 162){
				?>
				<div class="inner-header" id="free-bee">
					<div class="container">
						<div class="col-md-2 col-sm-12 col-xs-12">

							<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<img src="<?php echo $option_data['productpage_logo']; ?>" alt="logo">
							</a>

						</div>
						<div class="col-md-10 col-sm-12 col-xs-12">
							<?php wp_nav_menu( array( 
								'menu'            => 'header product menu', 
								'container'       => 'ul', 
								'container_class' => '', 
								'container_id'    => '',  
								'menu_id'         => '',
								'menu_class'      => 'inner-header-menu'

							)); ?>

						</div>
					</div>
				</div>
				<div class="main_header">
					<div class="container">
						<div class="slider">
							<div class="inner_slider">
								<div id="product-carousel" class="owl-carousel">
									<?php $i = 0; while ( have_rows('banner') ) : the_row();?>
									<div class="item <?php if($i == 0){ echo "active";}else{ echo "";}?>">
										<?php $image = get_sub_field('bannerimage');?>
										<img src="<?php echo $image['url']; ?>" width="100%" alt="<?php the_title(); ?>" />
									</div>
									<?php $i++; endwhile;?>
								</div>
							</div>
						</div>
						<div class="banner-top">
							<?php the_field('bannertext');?>
						</div>
						<div class="banner_left">
							<?php $img = get_field('bannerleftimage'); ?>
							<img src="<?php echo $img['url'];?>" alt="man_img" />
							<?php $appimg = get_field('smartphoneappimage'); ?>
							<a href="<?php the_permalink(53)?>">
								<img src="<?php echo $appimg['url']; ?>" alt="smart_phone_app" /></a> 
								<?php $betterimg = get_field('betterthanimage'); ?>
								<a href="#"><img src="<?php echo $betterimg['url']; ?>" alt="better_than" /></a>
								<div class="clr"></div>
								
							</div>
						</div>
						<div class="clr"></div>
						</div><?php }else {banner ?>
							<div class="main_header_inner">
								<div class="header">
									<div class="container">
										<div class="col-md-3 col-sm-12 col-xs-12">
											<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
												<img src="<?php echo $option_data['custom_logo']; ?>" alt="">
											</a>

										</div>
										<div class="col-md-9 col-sm-12 col-xs-12">
											<div class="header_right">
												<div id="main-nav" class="stellarnav">
													<?php wp_nav_menu( array( 
														'menu'            => 'header menu', 
														'container'       => 'ul', 
														'container_class' => '', 
														'container_id'    => '',  
														'menu_id'         => ''

													)); ?>
												</div><!-- .stellar-nav -->
												<div class="search_panel">
													<a href="#"><img src="<?php echo bloginfo('template_url');?>/assets/images/search_icon.png" alt="search_icon" /></a>
													<?php get_search_form();?>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="clr"></div>
							</div>

						<?php } ?>


