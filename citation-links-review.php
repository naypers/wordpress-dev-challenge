<?php

/**
 *
 * The plugin bootstrap file
 *
 * This file is responsible for starting the plugin using the main plugin class file.
 *
 * @since 0.0.1
 * @package Plugin_Name
 *
 * @wordpress-plugin
 * Plugin Name:     OPP - Citaciones & Revisión de Enlaces
 * Description:		El mejor plugin para agregar citaciones a tus posts con un campo WYSIWYG (HTML) y un shortcode. Además te ayuda a revisar si tienes enlaces rotos dentro de tus posts.
 * Version:         1.0
 * Author:          Oscar Estrella
 * Author URI:      https://www.linkedin.com/in/oscarestrella
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     citaciones-y-revision-de-enlaces
 * Domain Path:     /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not permitted.' );
}

if ( ! class_exists( 'plugin_name' ) ) {

	/*
	 * main plugin_name class
	 *
	 * @class plugin_name
	 * @since 0.0.1
	 */
	class plugin_name {

		/*
		 * plugin_name plugin version
		 *
		 * @var string
		 */
		public $version = '1.0';

		/**
		 * The single instance of the class.
		 *
		 * @var plugin_name
		 * @since 0.0.1
		 */
		protected static $instance = null;

		/**
		 * Main plugin_name instance.
		 *
		 * @since 0.0.1
		 * @static
		 * @return plugin_name - main instance.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * plugin_name class constructor.
		 */
		public function __construct() {
			$this->load_plugin_textdomain();
			$this->define_constants();
			$this->includes();
			$this->define_actions();
			$this->define_menus();
		}

		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'plugin-name', false, basename( dirname( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Include required core files
		 */
		public function includes() {
            // Metabox for "citation"
			require_once __DIR__ . '/includes/metabox-citation.php';
			// Shortcode for "citation"
			require_once __DIR__ . '/includes/shortcode-citation.php';
			// Cronjob for "urls review"
			require_once __DIR__ . '/includes/cronjob-url-review.php';
			// Load custom functions and hooks
			require_once __DIR__ . '/includes/includes.php';
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}


		/**
		 * Define plugin_name constants
		 */
		private function define_constants() {
			define( 'PLUGIN_NAME_PLUGIN_FILE', __FILE__ );
			define( 'PLUGIN_NAME_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'PLUGIN_NAME_VERSION', $this->version );
			define( 'PLUGIN_NAME_PATH', $this->plugin_path() );
		}

		/**
		 * Define plugin_name actions
		 */
		public function define_actions() {
			register_activation_hook( __FILE__, 'ctl_table_creation' );
			register_activation_hook( __FILE__, 'ctl_cronjob_activation' );
			register_deactivation_hook( __FILE__, 'ctl_cronjob_desativation' );
		}

		/**
		 * Define plugin_name menus
		 */
		public function define_menus() {
            require_once plugin_dir_path( __FILE__ ) . "/includes/menupage-url-review.php";
		}
	}

	$plugin_name = new plugin_name();
}
