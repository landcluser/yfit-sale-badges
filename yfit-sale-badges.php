<?php
/**
* Plugin Name: YFIT Sale Badges
* Plugin URI:
* Description: You can even set a different sale badge for each product individually! YouthFireIT Themes and Plugins! Visit https://youthfireit.com
* Author: YouthFire It
* Version: 1.0.0
* Requires at least: 4.4
* Tested up to: 5.3.2
* Author URI: https://youthfireit.com
* Text Domain: yfit-sale-badges
* License: GNU GPLv2
* License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$GLOBALS['svx'] = isset( $GLOBALS['svx'] ) && version_compare( $GLOBALS['svx'], '1.3.7') == 1 ? $GLOBALS['svx'] : '1.3.7';

if ( !class_exists( 'YFIT_Sale_Badges' ) ) :

	final class YFIT_Sale_Badges {

		public static $version = '1.0.0';

		protected static $_instance = null;

		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct() {
			do_action( 'wcmnisb_loading' );

			$this->includes();

			if ( !function_exists( 'YFIT' ) ) {
				$this->single_plugin();
			}

			do_action( 'wcmnisb_loaded' );
		}

		private function single_plugin() {
			if ( is_admin() ) {
				register_activation_hook( __FILE__, array( $this, 'activate' ) );
			}

			add_action( 'init', array( $this, 'load_svx' ), 100 );

			// Legacy will be removed
			add_action( 'plugins_loaded', array( $this, 'fix_svx' ), 100 );

			// Texdomain only used if out of YFIT
			add_action( 'init', array( $this, 'textdomain' ), 0 );
		}

		public function activate() {
			if ( !class_exists( 'WooCommerce' ) ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );

				wp_die( esc_html__( 'This plugin requires WooCommerce. Download it from WooCommerce official website', 'yfit-sale-badges' ) . ' &rarr; https://woocommerce.com' );
				exit;
			}
		}

		public function fix_svx() {
			include_once( 'includes/svx-settings/svx-fixoptions.php' );
		}

		public function load_svx() {
			if ( $this->is_request( 'admin' ) ) {
				include_once( 'includes/svx-settings/svx-settings.php' );
			}
		}

		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		public function includes() {

			if ( $this->is_request( 'admin' ) ) {

				include_once( 'includes/isb-settings.php' );

			}

			//if ( $this->is_request( 'frontend' ) ) {
				$this->frontend_includes();
			//}

		}

		public function frontend_includes() {
			include_once( 'includes/isb-frontend.php' );
			include_once( 'includes/isb-shortcode.php' );
		}

		public function textdomain() {

			$this->load_plugin_textdomain();

		}

		public function load_plugin_textdomain() {

			$domain = 'yfit-sale-badges';
			$dir = untrailingslashit( WP_LANG_DIR );
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			if ( $loaded = load_textdomain( $domain, $dir . '/plugins/' . $domain . '-' . $locale . '.mo' ) ) {
				return $loaded;
			}
			else {
				load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
			}

		}

		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		public function plugin_basename() {
			return untrailingslashit( plugin_basename( __FILE__ ) );
		}

		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		public static function version_check( $version = '3.0.0' ) {
			if ( class_exists( 'WooCommerce' ) ) {
				global $woocommerce;
				if( version_compare( $woocommerce->version, $version, ">=" ) ) {
					return true;
				}
			}
			return false;
		}

		public function version() {
			return self::$version;
		}

	}

	add_filter( 'svx_plugins', 'svx_sale_badges_add_plugin', 30 );
	add_filter( 'svx_plugins_settings_short', 'svx_sale_badges_add_short' );

	function svx_sale_badges_add_plugin( $plugins ) {

		$plugins['sale_badges'] = array(
			'slug' => 'sale_badges',
			'name' => esc_html__( 'YFIT Badges', 'yfit-sale-badges' )
		);

		return $plugins;

	}
	function svx_sale_badges_add_short( $plugins ) {
		$plugins['sale_badges'] = array(
			'slug' => 'sale_badges',
			'settings' => array(


				'wc_settings_isb_style' => array(
					'autoload' => false,
				),
				'wc_settings_isb_color' => array(
					'autoload' => false,
				),
				'wc_settings_isb_position' => array(
					'autoload' => false,
				),
				'wc_settings_isb_special' => array(
					'autoload' => false,
				),
				'wc_settings_isb_special_text' => array(
					'autoload' => false,
				),

				'wcmn_isb_presets' => array(
					'autoload' => false,
				),

				'wcmn_isb_overrides' => array(
					'autoload' => false,
				),

				'wc_settings_isb_template_overrides' => array(
					'autoload' => true,
				),
				'wc_settings_isb_archive_action' => array(
					'autoload' => true,
				),
				'wc_settings_isb_single_action' => array(
					'autoload' => true,
				),
				'wc_settings_isb_force_scripts' => array(
					'autoload' => true,
				),

				'wc_settings_isb_timer' => array(
					'autoload' => false,
				),
				'wc_settings_isb_timer_adjust' => array(
					'autoload' => false,
				),

			)
		);

		return $plugins;
	}

	function YFITSaleBadges() {
		return YFIT_Sale_Badges::instance();
	}

	YFIT_Sale_Badges::instance();

endif;
