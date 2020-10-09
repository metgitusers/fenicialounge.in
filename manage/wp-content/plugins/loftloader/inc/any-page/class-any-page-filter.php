<?php
if ( ! class_exists('LoftLoader_Any_Page_Filter' ) ) {
	class LoftLoader_Any_Page_Filter{
		private $defaults = array(); 
		private $page_settings = array();
		private $page_enabled = false;
		private $is_customize = false;
		public function __construct(){
			add_filter('loftloader_get_loader_setting', array($this, 'get_loader_setting'), 10, 2);
			add_filter('loftloader_loader_enabled', array($this, 'loader_enabled'));
			add_action('loftloader_settings', array($this, 'loader_settings'));
		}
		/**
		* @description get the plugin settings
		*/
		public function loader_settings(){ 
			global $wp_customize, $loftloader_default_settings;
			$this->is_customize = isset($wp_customize) ? true : false;
			if(((is_front_page() || is_home()) && (get_option('show_on_front', false) == 'page')) || is_page()){
				$page = get_queried_object();
				if(($atts = $this->get_loader_attributes($page->ID)) !== false){
					if( isset( $atts['loftloader_show_close_tip'] ) ) {
						$atts['loftloader_show_close_tip'] = base64_decode( $atts['loftloader_show_close_tip'] );
					}
					$this->page_settings = array_merge( $loftloader_default_settings, $atts );
					$this->page_enabled = ( $atts['loftloader_main_switch'] === 'on' );
				}
			}
		}
		/**
		* @description helper function to get shortcode attributes
		*/
		private function get_loader_attributes($page_id){
			$loader = get_post_meta($page_id, 'loftloader_page_shortcode', true);
			$loader = trim($loader);
			if(!empty($loader)){
				$loader = substr($loader, 1, -1);
				return shortcode_parse_atts($loader);
			}
			return false;
		}
		/**
		* Helper function to test whether show loftloader
		* @return boolean return true if loftloader enabled and display on current page, otherwise false
		*/
		public function loader_enabled(){
			return $this->page_enabled;
		}
		/**
		* Helper function get setting option
		*/
		public function get_loader_setting($setting_value, $setting_id){
			return ($this->page_enabled && !$this->is_customize && isset($this->page_settings[$setting_id])) ? $this->page_settings[$setting_id] : $setting_value;
		}
	}
}