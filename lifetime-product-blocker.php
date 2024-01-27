<?php
/*
Plugin Name: Lifetime Product Blocker
Description: Block purchase lifetime product buy
Version:     1.0.0
Author:      Lifetime Product Blocker
Author URI:  #
Text Domain: lifeproblocker
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die;

/**
 * Define required constants
 */
define( 'LIFE_PRO_BLOCKER_VER', '1.0.0' );
define( 'LIFE_PRO_BLOCKER_FILE', __FILE__ );
define( 'LIFE_PRO_BLOCKER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define('QUICKSTART_PATH', __DIR__);
define('LIFE_PRO_BLOCKER_URL', plugins_url('', LIFE_PRO_BLOCKER_FILE));
define('LIFE_PRO_BLOCKER_URL_ASSETS', LIFE_PRO_BLOCKER_URL . '/assets');

define( 'LIFE_PRO_BLOCKER_OPT_GROUP', 'lifeproblocker_admin_settings_group' );
define( 'LIFE_PRO_BLOCKER_OPT_NAME', 'lifeproblocker_admin_settings' );


if ( ! class_exists( 'Lifetime_Pro_Blocker' ) ) {

	require_once( __DIR__ . '/inc/methods.php' );
	
	class Lifetime_Pro_Blocker {

		public $options;
		public $lifeproblocker_active;
		public $limit;

		use Lifetime_Pro_Blocker_Functions;

		public static function get_instance() {
			if ( self::$instance == null ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		private static $instance = null;

		/**
		 * Construct initiates
		 */
		public function __construct() {

			$this->options = null;
			$this->lifeproblocker_active = $this->get_option('lifeproblocker_active');
			$this->limit = $this->get_option('user_can_purchase_lifetime');
			
			// Actions
			add_action( 'wp_loaded', array( $this, 'initialize_features' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_wp_assets' ) );
	
		}

		/**
		 * Initialize features
		 */
		public function initialize_features() {
			load_plugin_textdomain( 'lifeproblocker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
			
		}

		/**
		 * Enqueue admin assets
		 */
		public function enqueue_admin_assets( ) {
			wp_enqueue_style( 'lifeproblocker-admin-style', plugins_url( 'assets/css/admin-style.css', __FILE__ ), array(), LIFE_PRO_BLOCKER_VER, 'all' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'lifeproblocker-admin-script', plugins_url( 'assets/js/admin-script.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), LIFE_PRO_BLOCKER_VER, true );
			
		}

		/**
		 * Enqueue wp assets
		 */
		public function enqueue_wp_assets( ) {
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'lifeproblocker-style', plugins_url( 'assets/css/custom-style.css', __FILE__ ), array(), LIFE_PRO_BLOCKER_VER, 'all' );
			wp_enqueue_script( 'lifeproblocker-script', plugins_url( 'assets/js/custom-script.js', __FILE__ ), array( 'jquery' ), LIFE_PRO_BLOCKER_VER, true );
		}

	}
	

	
	/**
	 * Instantiate
	 */
	$Lifetime_Pro_Blocker = new Lifetime_Pro_Blocker();
	$Lifetime_Pro_Blocker->get_instance();

	/**
	 * Include php files 
	 */
	require_once( __DIR__ . '/inc/admin/admin-settings.php' );
	require_once( __DIR__ . '/inc/order_update_user.php' );
	require_once( __DIR__ . '/inc/shortcode.php' );
	require_once( __DIR__ . '/inc/functions.php' );

}


