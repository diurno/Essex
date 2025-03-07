<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Esendex_Us_Form_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		ESENDEXUSF
 * @subpackage	Classes/Esendex_Us_Form_Run
 * @author		500designs
 * @since		1.0.0
 */
class Esendex_Us_Form_Run{

	/**
	 * Our Esendex_Us_Form_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
	
		add_action( 'plugins_loaded', array( $this, 'add_wp_webhooks_integrations' ), 9 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );		
		add_shortcode( 'esendex_us_form', array( $this,'esendex_us_form_shortcode'), 70 );
	
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	 * ####################
	 * ### WP Webhooks 
	 * ####################
	 */

	/*
	 * Register dynamically all integrations
	 * The integrations are available within core/includes/integrations.
	 * A new folder is considered a new integration.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function add_wp_webhooks_integrations(){

		// Abort if WP Webhooks is not active
		if( ! function_exists('WPWHPRO') ){
			return;
		}

		$custom_integrations = array();
		$folder = ESENDEXUSF_PLUGIN_DIR . 'core' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'integrations';

		try {
			$custom_integrations = WPWHPRO()->helpers->get_folders( $folder );
		} catch ( Exception $e ) {
			WPWHPRO()->helpers->log_issue( $e->getTraceAsString() );
		}

		if( ! empty( $custom_integrations ) ){
			foreach( $custom_integrations as $integration ){
				$file_path = $folder . DIRECTORY_SEPARATOR . $integration . DIRECTORY_SEPARATOR . $integration . '.php';
				WPWHPRO()->integrations->register_integration( array(
					'slug' => $integration,
					'path' => $file_path,
				) );
			}
		}
	}

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles() {
		wp_enqueue_style( 'esendex-backend-styles', ESENDEXUSF_PLUGIN_URL . 'core/includes/assets/css/frontend-styles.css', array(), ESENDEXUSF_VERSION, 'all' );
		wp_enqueue_script( 'esendex-backend-scripts', ESENDEXUSF_PLUGIN_URL . 'core/includes/assets/js/frontend-scripts.js', array(), ESENDEXUSF_VERSION, false );
		wp_localize_script('esendex-backend-scripts', 'esendex_url', array( 'siteurl' => get_option('siteurl') ));
	
	}

	public function esendex_us_form_shortcode($atts) {
		
		$html = "";
		$html .='<form id="esendex-salesforce-form" >';    
		$html .='<div class="form-row"><input type="text" placeholder="name" name="FirstName" maxlength="50" required /></div>';    
		$html .='<div class="form-row"><input type="text" placeholder="last name" name="LastName" maxlength="50" /></div>';    
		$html .='<div class="form-row"><input type="text" placeholder="company" name="Company" maxlength="100" /></div>';    
		$html .='<div class="form-row"><input type="tel" placeholder="phone" name="PhoneNumber" maxlength="40" /></div>';    
		$html .='<div class="form-row"><input type="email" placeholder="email" name="Email" /></div>';    
		$html .='<div class="form-row">';    
		$html .='<select name="ServiceId">';
		$html .='<option value="34">SMS Notify</option><option value="20">Phone Notify</option><option value="35"> Postal Address Verification</option><option value="12">Phone Verify</option>';    
		$html .='</select>';
		$html .='</div>';
		$html .='<div class="form-row"><input id="send-info" type="submit" value="submit" /></div>';
		$html .='<div class="form-row-error"></div>';    
		$html .='</form>';
		
		
		return $html;
	}

}
