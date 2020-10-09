<?php
// Not allowed by directly accessing.
if(!defined('ABSPATH')){
	die('Access not allowed!');
}

/**
 * Main class for front display
 * 
 * @package   LoftLoader
 * @link	  http://www.loftocean.com/
 * @author	  Suihai Huang from Loft Ocean Team

 * @since version 1.0
 */

if ( ! class_exists( 'LoftLoader_Front' ) ) {
	class LoftLoader_Front { 
		private $defaults; 
		private $type; // Get the loader settings
		public function __construct() {
			$this->get_settings();
			$this->start_cache();
			if ( $this->loader_enabled() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
				add_action( 'wp_head', array( $this, 'loader_custom_styles' ), 100 );
				add_action( 'wp_footer', array( $this, 'load_inline_js' ), 99 );
				add_filter( 'loftloader_modify_html', array( $this, 'show_loader_html' ) );
			}
		}
		/**
		* Start cache for outputing
		*/
		public function start_cache() {
			// Only for front view 
			if ( ! is_admin() ) {
				// Start cache the output with callback function
				ob_start( array( $this, 'modify_html' ) );
			}
		}
		/**
		* Will be called when flush cache
		*
		* @param string cached string
		* @return string modified cached string 
		*/
		public function modify_html( $html ) {
			return apply_filters( 'loftloader_modify_html', $html );
		}
		/**
		* @description get the plugin settings
		*/
		public function get_settings() { 
			global $loftloader_default_settings;
			$this->defaults = $loftloader_default_settings;
			do_action( 'loftloader_settings' );
			$this->type = esc_attr( $this->get_loader_setting( 'loftloader_loader_type' ) );
		}
		/**
		 * @description enqueue the scripts and styles for front end
		 */
		public function enqueue_scripts() {
			$loadJSStyle = $this->get_loader_setting( 'loftloader_inline_js' );
			if ( ! is_customize_preview() && ( 'inline' !== $loadJSStyle ) ) { 
				wp_enqueue_script( 'loftloader-lite-front-main', LOFTLOADER_URI . 'assets/js/loftloader.min.js', array( 'jquery' ), LOFTLOADER_ASSET_VERSION, true );
			}
			wp_enqueue_style('loftloader-lite-animation', LOFTLOADER_URI . 'assets/css/loftloader.min.css', array(), LOFTLOADER_ASSET_VERSION);
		}
		/**
		* Load inline JavaScript code if set
		*/
		public function load_inline_js() {
			$loadJSStyle = $this->get_loader_setting( 'loftloader_inline_js' );
			if ( ( 'inline' === $loadJSStyle ) && ! is_customize_preview() ) { ?>
				<script type="text/javascript">
					( function() {
						function loftloader_finished() {
							document.body.classList.add( 'loaded' );
						}
						var loader = document.getElementById( 'loftloader-wrapper' );
						if ( loader ) {
							window.addEventListener( 'load', function( e ) { 
								loftloader_finished(); 
							} );
							if ( loader.dataset && loader.dataset.showCloseTime ) {
								var showCloseTime = parseInt( loader.dataset.showCloseTime ),
									closeBtn = loader.getElementsByClassName( 'loader-close-button' );
								if ( showCloseTime && closeBtn.length ) {
									setTimeout( function() {
										closeBtn[0].style.display = ''; 
									}, showCloseTime );
									closeBtn[0].addEventListener( 'click', function( e ) {
										loftloader_finished();
									} );
								}
							}
						}
					} ) ();
				</script> <?php
			}
		}
		/**
		 * @description custom css for front end
		 */
		public function loader_custom_styles() {
			$color = esc_attr( $this->get_loader_setting( 'loftloader_loader_color' ) );
			$bgColor = esc_attr( $this->get_loader_setting( 'loftloader_bg_color' ) );
			$bgOpacity = intval( $this->get_loader_setting('loftloader_bg_opacity' ) ) / 100;

			$styles  = $this->generate_style(
				'loftloader-lite-custom-bg-color', 
				'#loftloader-wrapper .loader-section {' . PHP_EOL . "\t" . 'background: ' . $bgColor . ';' . PHP_EOL . '}' . PHP_EOL
			);
			$styles .= $this->generate_style(
				'loftloader-lite-custom-bg-opacity', 
				'#loftloader-wrapper .loader-section {' . PHP_EOL . "\t" . 'opacity: ' . $bgOpacity . ';' . PHP_EOL . '}' . PHP_EOL
			);
			$css = '';
			switch ( $this->type ) {
				case 'sun':
					$css = '#loftloader-wrapper.pl-sun #loader {' . PHP_EOL . "\t" . 'color: ' . $color . ';' . PHP_EOL . '}' . PHP_EOL;
					break;
				case 'circles':
					$css = '#loftloader-wrapper.pl-circles #loader {' . PHP_EOL . "\t" . 'color: ' . $color . ';' . PHP_EOL . '}' . PHP_EOL;
					break;
				case 'wave':
					$css = '#loftloader-wrapper.pl-wave #loader {' . PHP_EOL . "\t" . 'color: ' . $color . ';' . PHP_EOL . '}' . PHP_EOL;
					break;
				case 'square':
					$css = '#loftloader-wrapper.pl-square #loader span {' . PHP_EOL . "\t" . 'border: 4px solid ' . $color . ';' . PHP_EOL . '}' . PHP_EOL;
					break;
				case 'frame':
					$css = '#loftloader-wrapper.pl-frame #loader {' . PHP_EOL . "\t" . 'color: ' . $color . ';' . PHP_EOL . '}' . PHP_EOL;
					break;
				case 'imgloading':
					$width = absint($this->get_loader_setting('loftloader_img_width'));
					$image = esc_url($this->get_loader_setting('loftloader_custom_img'));
					$css  = empty($width) ? '' : '#loftloader-wrapper.pl-imgloading #loader {' . PHP_EOL . "\t" . 'width: ' . $width . 'px;' . PHP_EOL . '}' . PHP_EOL;
					$css .= '#loftloader-wrapper.pl-imgloading #loader span {' . PHP_EOL . "\t" . 'background-size: cover;' . PHP_EOL . "\t" . 'background-image: url(' . $image . ');' . PHP_EOL . '}' . PHP_EOL;
					break;
				case 'beating':
					$css = '#loftloader-wrapper.pl-beating #loader {' . PHP_EOL . "\t" . 'color: ' . $color . ';' . PHP_EOL . '}' . PHP_EOL;
					break;
			}
			$styles .= $this->generate_style( 'loftloader-lite-custom-loader', $css );
			echo wp_kses( $styles, array(
				'style' => array( 'type' => array(), 'id' => array(), 'media' => array() )
			) );
		}
		/**
		 * @description loftloader html
		 */
		public function show_loader_html( $origin ) {
			if ( ! empty( $origin ) ) {
				$regexp ='/(<body[^>]*>)/i';
				$split = preg_split( $regexp, $origin, 3, PREG_SPLIT_DELIM_CAPTURE );
				if ( is_array( $split ) && ( 3 <= count( $split ) ) ) { 
					$image  = esc_url($this->get_loader_setting('loftloader_custom_img'));
					$ending = esc_attr($this->get_loader_setting('loftloader_bg_animation'));

					$html  = '<div id="loftloader-wrapper" class="pl-' . $this->type . '"' . $this->loader_attributes() . '>';
					$html .= '<div class="loader-inner"><div id="loader">';
					$html .= in_array($this->type, array('frame', 'imgloading'))
						? ('<span></span>' . (empty($image) ? '' : ('<img src="' . $image . '" alt="preloder">'))) : '<span></span>';
					$html .= '</div></div>';
					switch($ending){
						case 'fade':
							$html .= '<div class="loader-section section-fade"></div>';
							break;
						case 'up':
							$html .= '<div class="loader-section section-slide-up"></div>';
							break;
						case 'split-v':
							$html .= '<div class="loader-section section-up"></div>';
							$html .= '<div class="loader-section section-down"></div>';
							break;
						default:
							$html .= '<div class="loader-section section-left">';
							$html .= '</div><div class="loader-section section-right"></div>';
					}

					if(!is_customize_preview()){
						$close_description = $this->get_loader_setting('loftloader_show_close_tip');
						$html .= sprintf(
							'<div class="loader-close-button" style="display: none;"><span class="screen-reader-text">%s</span>%s</div>',
							esc_html__('Close', 'loftloader'),
							empty($close_description) ? '' : sprintf('<span class="close-des">%s</span>', $close_description)
						);
					}
					$html .= '</div>';

					return $split[0] . $split[1] . $html . implode( '', array_slice( $split, 2 ) );
				}
			}
			return $origin;
		}
		/**
		* Helper function to add manual loader settings
		*/
		private function loader_attributes() {
			$attrs = '';
			$show_close_time = $this->get_loader_setting( 'loftloader_show_close_timer' );
			$show_close_time = number_format( $show_close_time, 0, '.', '' );
			$attrs .= sprintf( ' data-show-close-time="%s"', esc_js( esc_attr( $show_close_time * 1000 ) ) );
			return apply_filters( 'loftloader_loader_attributes', $attrs );
		}
		/**
		* Helper function to test whether show loftloader
		* @return boolean return true if loftloader enabled and display on current page, otherwise false
		*/
		private function loader_enabled() {
			if ( ( $this->get_loader_setting( 'loftloader_main_switch' ) === 'on' ) ) {
				$range = $this->get_loader_setting( 'loftloader_show_range' );
				if ( ( $range === 'sitewide' ) || ( ( $range === 'homepage' ) && is_front_page() ) ) {
					return true;
				} else {
					return false;
				}
			} else {
				return apply_filters( 'loftloader_loader_enabled', false );
			}
		}
		/**
		* Helper function get setting option
		*/
		private function get_loader_setting( $setting_id ) {
			return apply_filters( 'loftloader_get_loader_setting', get_option( $setting_id, $this->defaults[ $setting_id ] ), $setting_id );
		}
		/**
		* Helper function generate styles
		*/
		private function generate_style( $id, $style ) {
			return '<style id="' . $id . '">' . $style . '</style>';
		}
	}
	new LoftLoader_Front();
}