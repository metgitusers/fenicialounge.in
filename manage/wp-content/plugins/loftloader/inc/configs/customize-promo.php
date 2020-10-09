<?php
/**
* Load loftloader lite Promotion related functions
*
* @since version 2.0.0
*/
add_action( 'customize_register', 'loftloader_customize_promotion', 100 );
function loftloader_customize_promotion( $wp_customize ) {
	$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'loftloader_promo', array(
		'default'   		=> '',
		'transport'	 		=> 'postMessage',
		'type' 				=> 'option',
		'sanitize_callback' => 'sanitize_text_field'
	) ) );

	$wp_customize->add_section( new WP_Customize_Section( $wp_customize, 'loftloader_promo', array(
		'title'       => esc_html__( 'Upgrade to Pro', 'loftloader' ),
		'description' => '',
		'priority'    => 100
	) ) );

	$wp_customize->add_control( new LoftLoader_Customize_Control( $wp_customize, 'loftloader_promo', array(
		'type' => 'loftloader-ad',
		'label' => '',
		'img' => LOFTLOADER_URI . 'assets/img/pro-ad.jpg',
		'href' => esc_url( 'https://codecanyon.net/item/loftloader-pro-preloader-plugin-for-wordpress/17339671?ref=LoftOcean' ),
		'section' => 'loftloader_promo',
		'settings' => 'loftloader_promo'
	) ) );
}