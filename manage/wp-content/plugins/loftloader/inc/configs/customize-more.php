<?php
/**
* Load loftloader lite more section
*
* @since version 2.1.3
*/
add_action( 'customize_register', 'loftloader_customize_more', 45 );
function loftloader_customize_more( $wp_customize ) {	
	global $loftloader_default_settings;

	$wp_customize->add_section( new LoftLoader_Customize_Section( $wp_customize, 'loftloader_section_more', array(
		'title'       => esc_html__( 'More', 'loftloader' ),
		'description' => '',
		'priority'    => 50
	) ) );

	$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'loftloader_show_close_timer', array(
		'default'   		=> $loftloader_default_settings['loftloader_show_close_timer'],
		'transport' 		=> 'postMessage',
		'type' 				=> 'option',
		'sanitize_callback' => 'absint'
	) ) );
	$wp_customize->add_setting( new WP_Customize_Setting( $wp_customize, 'loftloader_show_close_tip', array(
		'default'   		=> $loftloader_default_settings['loftloader_show_close_tip'],
		'transport' 		=> 'postMessage',
		'type' 				=> 'option',
		'sanitize_callback' => 'sanitize_text_field'
	) ) );

	$wp_customize->add_control( new LoftLoader_Customize_Slider_Control( $wp_customize, 'loftloader_show_close_timer', array(
		'type'    		=> 'slider',
		'label'    		=> esc_html__( 'Show Close Button after', 'loftloader' ),
		'after_text' 	=> 'second(s)',
		'input_attrs' 	=> array(
			'data-default' 	=> '15',
			'data-min' 		=> '5',
			'data-max' 		=> '20',
			'data-step' 	=> '1'
		),
		'input_class' 	=> 'loftloader-show-close-timer',
		'section'  		=> 'loftloader_section_more',
		'settings' 		=> 'loftloader_show_close_timer'
	) ) );
	$wp_customize->add_control( new LoftLoader_Customize_Control( $wp_customize, 'loftloader_show_close_tip', array(
		'type' 			=> 'text',
		'label'			=> esc_html__( 'Description for Close Button', 'loftloader' ),
		'section' 		=> 'loftloader_section_more',
		'settings' 		=> 'loftloader_show_close_tip'
	) ) );
}