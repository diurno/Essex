<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Esendex_Us_Form' ) ) :

	/**
	 * Main Esendex_Us_Form Class.
	 *
	 * @package		ESENDEXUSF
	 * @subpackage	Classes/Esendex_Us_Form
	 * @since		1.0.0
	 * @author		500designs
	 */
	final class Esendex_Us_Form {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Esendex_Us_Form
		 */
		private static $instance;

		/**
		 * ESENDEXUSF helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Esendex_Us_Form_Helpers
		 */
		public $helpers;

		/**
		 * ESENDEXUSF settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Esendex_Us_Form_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'esendex-us-form' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'esendex-us-form' ), '1.0.0' );
		}

		/**
		 * Main Esendex_Us_Form Instance.
		 *
		 * Insures that only one instance of Esendex_Us_Form exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Esendex_Us_Form	The one true Esendex_Us_Form
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Esendex_Us_Form ) ) {
				self::$instance					= new Esendex_Us_Form;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		= new Esendex_Us_Form_Helpers();
				self::$instance->settings		= new Esendex_Us_Form_Settings();

				//Fire the plugin logic
				new Esendex_Us_Form_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'ESENDEXUSF/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once ESENDEXUSF_PLUGIN_DIR . 'core/includes/classes/class-esendex-us-form-helpers.php';
			require_once ESENDEXUSF_PLUGIN_DIR . 'core/includes/classes/class-esendex-us-form-settings.php';

			require_once ESENDEXUSF_PLUGIN_DIR . 'core/includes/classes/class-esendex-us-form-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'esendex-us-form', FALSE, dirname( plugin_basename( ESENDEXUSF_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.