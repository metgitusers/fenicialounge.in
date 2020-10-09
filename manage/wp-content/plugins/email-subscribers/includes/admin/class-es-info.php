<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ES_Info {

	static $instance;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
	}

	public function plugin_menu() {
		$help_title = __('Help & Info', 'email-subscribers');
		add_submenu_page( 'es_dashboard', $help_title, $help_title, 'edit_posts', 'es_general_information', array( $this, 'es_information_callback' ) );

        // 	$pro_title = __('Go Pro', 'email-subscribers');
		$active_plugins = (array) get_option( 'active_plugins', array() );
		$pro_title = __('<span class="es-fire-sale"> 🔥 </span> Go Pro', 'email-subscribers');
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		if ( ! ( in_array( 'email-subscribers-premium/email-subscribers-premium.php', $active_plugins ) || array_key_exists( 'email-subscribers-premium/email-subscribers-premium.php', $active_plugins ) ) ) {
			add_submenu_page( 'es_dashboard', $pro_title, $pro_title, 'edit_posts', 'es_pricing', array( $this, 'es_pricing_callback' ) );
		}
	}

	public function es_information_callback() {
		include_once( EMAIL_SUBSCRIBERS_DIR . '/admin/partials/help.php' );
	}

	public static function es_pricing_callback() {
		require_once( EMAIL_SUBSCRIBERS_DIR . '/admin/partials/pricing.php' );
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}