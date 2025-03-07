<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Esendex_Us_Form_Settings
 *
 * This class contains all of the plugin settings.
 * Here you can configure the whole plugin data.
 *
 * @package		ESENDEXUSF
 * @subpackage	Classes/Esendex_Us_Form_Settings
 * @author		500designs
 * @since		1.0.0
 */
class Esendex_Us_Form_Settings{

	/**
	 * The plugin name
	 *
	 * @var		string
	 * @since   1.0.0
	 */
	private $plugin_name;
	private $sift_client;

	/**
	 * Our Esendex_Us_Form_Settings constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){

		$this->plugin_name = ESENDEXUSF_NAME;
		$this->include_sift_classes();
		$this->add_settings_hooks();
	}


	public function include_sift_classes() {

		require_once ESENDEXUSF_PLUGIN_DIR . 'sift-php/lib/SiftRequest.php';
		require_once ESENDEXUSF_PLUGIN_DIR . 'sift-php/lib/SiftResponse.php';
		require_once ESENDEXUSF_PLUGIN_DIR . 'sift-php/lib/SiftClient.php';
		require_once ESENDEXUSF_PLUGIN_DIR . 'sift-php/lib/Sift.php';

		$this->sift_client = new SiftClient([
			'api_key' => '768bf5d8a613b007'
		]);

		//$this->esendex_us_form_send_transaction();
	}



	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * Return the plugin name
	 *
	 * @access	public
	 * @since	1.0.0
	 * @return	string The plugin name
	 */
	public function get_plugin_name(){
		return apply_filters( 'ESENDEXUSF/settings/get_plugin_name', $this->plugin_name );
	}



	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_settings_hooks(){	
		add_action( 'rest_api_init', array( &$this, 'rest_api_register_routes' ) );
	}

	
	public function rest_api_register_routes() {
		
		register_rest_route(
			'esendex-us-form-submissions-api/v1',
			'/form-submission/',
			array(
				'methods'  => 'POST',
				'callback' => [$this, 'esendex_us_form_send_transaction'],
				'permission_callback' => '__return_true',
			)
		);

	}

	public function validate_fields($form_fields) {
		$required_fields = ['FirstName','LastName','Email','PhoneNumber','Company'];
		$errors_array = [];
		foreach ($required_fields as $field) {
			
			if (empty($form_fields[$field])) {
            	http_response_code(400); // set HTTP response status code to 401 Unauthorized
				array_push($errors_array, "{$field} is required.");
            } elseif ($field === 'Email') {           	

            	if (!filter_var($form_fields[$field], FILTER_VALIDATE_EMAIL)) {
            		http_response_code(400); // set HTTP response status code to 401 Unauthorized
		            array_push($errors_array, "Invalid email format.");
		        }
            }
        }

        return $errors_array;
	}

	public function esendex_us_form_send_transaction($request) {

		// TODO:
			//sanitize params = DOne
			// validate parameters =  DOne

		$request_object = $request->get_json_params();

		$email_sanitized = sanitize_email($request_object['Email']);
		$fname_sanitized = sanitize_text_field($request_object['FirstName']);
		$lname_sanitized = sanitize_text_field($request_object['LastName']);
		$phone_sanitized = sanitize_text_field($request_object['PhoneNumber']);
		$company_sanitized = sanitize_text_field($request_object['Company']);
		//$service_id_sanitized = sanitize_text_field($request_object['ServiceId']); ??
		
		
	
		$err_object = $this->validate_fields($request->get_json_params());	
		
		if(is_array($err_object) && count($err_object) > 0) {
			echo json_encode(['success' => false, 'message' => $err_object]);
    		die();	
		} else {
			if(strlen($fname_sanitized) > 50 ) {
				http_response_code(400); // set HTTP response status code to 400 Bad Request
				echo json_encode(['success' => false, 'message' => 'First Name Field Too Long']);
			} elseif(strlen($lname_sanitized) > 50 ) {
				http_response_code(400); // set HTTP response status code to 400 Bad Request
				echo json_encode(['success' => false, 'message' => 'Last Name Field Too Long']);
			} elseif(strlen($email_sanitized) > 100 ) {
				http_response_code(400); // set HTTP response status code to 400 Bad Request
				echo json_encode(['success' => false, 'message' => 'Email Field Too Long']);

			} elseif(strlen($company_sanitized) > 100 ) {
				http_response_code(400); // set HTTP response status code to 400 Bad Request
				echo json_encode(['success' => false, 'message' => 'Company Field Too Long']);

			} elseif(!is_int((int)$request_object['ServiceId']) || (int)$request_object['ServiceId'] == 0 ) {
				http_response_code(400); // set HTTP response status code to 400 Bad Request
				echo json_encode(['success' => false, 'message' => 'ServiceId Must Be Integer']);
			}
		}
		
	
		if( empty($email_sanitized) ) {
			http_response_code(400); // set HTTP response status code to 400 Bad Request
			echo json_encode(['success' => false, 'message' => 'Invalid Email']);
    		die();
		} else {
			$properties = array(
			  // Required Fields
			  '$type'				=> '$create_account',
			  '$api_key'			=> '768bf5d8a613b007',
			  '$user_id'    		=> 'esendexus_'.$email_sanitized,
			  '$session_id' 		=> 'gigtleqddo84l8sa15qe4il',
			  '$user_email'       	=> $email_sanitized,
			  '$phone'            	=> $request_object['PhoneNumber'],
			  '$name'             	=> sanitize_text_field($request_object['FirstName']).' '.sanitize_text_field($request_object['LastName']),
			  '$browser'    => array(
			    '$user_agent'       => $_SERVER["HTTP_USER_AGENT"],
			    '$accept_language'  => 'en-US',
			    '$content_language' => 'en-GB'
			  ),
			  '$brand_name'         => 'Esendex US',
			  '$site_country' 		=> 'us',
			  '$site_domain' 		=>  $_SERVER['SERVER_NAME'],
			  '$ip'         		=>  $_SERVER['REMOTE_ADDR'],
			  'company_name' 		=> sanitize_text_field($request_object['Company']),
			  'within_working_hours' => false
			);


			//print_r($properties);die('prepare');die;	


			$sift_client = new SiftClient(array('api_key' => '768bf5d8a613b007'));			

			$response = $sift_client->track('$create_account', 
											$properties, 
											array('return_workflow_status' => true, 'abuse_types' => array('account_abuse')));


			error_log(PHP_EOL.ESENDEXUSF_LOG_SEPARATOR.PHP_EOL.date("F j, Y, g:i a e O").'User blocked by Signup API:'. print_r($response, true), 3, ESENDEXUSF_PLUGIN_DIR.'/error_fede.log');


			//print_r($response);die('prepare');	

			$sift_response = $response->body['score_response']['workflow_statuses'][0]['history'][0]['name'];

			error_log(PHP_EOL.ESENDEXUSF_LOG_SEPARATOR.PHP_EOL.date("F j, Y, g:i a e O").'sift_response:'.$sift_response, 3, ESENDEXUSF_PLUGIN_DIR.'/error_fede.log');

			if($sift_response == "Looks Bad") {
				http_response_code(400);
				echo json_encode(['success' => false, 'message' => 'We’re sorry – there was a problem. Please call 757-544-9510 for more information.']);
    			die();
			} else {

				$post_data_for_essex = [
					"FirstName" 	=>  sanitize_text_field($request_object['FirstName']),
					"LastName" 		=>  sanitize_text_field($request_object['LastName']),
					"Company" 		=>  sanitize_text_field($request_object['Company']),
					"Email" 		=>  $email_sanitized,
					"PhoneNumber" 	=>  $request_object['PhoneNumber'],
					"ServiceId" 	=>  $request_object['ServiceId'],
					"LeadSource" 	=>  "Direct",
					"SourceCode" 	=>  "esendex.us",
					"SourceDetail" 	=>  "https://esendex.us/",
					"IpAddress" 	=>  $_SERVER['REMOTE_ADDR']
				];


				error_log(PHP_EOL.ESENDEXUSF_LOG_SEPARATOR.PHP_EOL.date("F j, Y, g:i a e O").'Array for Signup API:'. print_r($post_data_for_essex, true), 3, ESENDEXUSF_PLUGIN_DIR.'/error_fede.log');


				$essex_response = $this->send_data_to_esendex_api(json_encode($post_data_for_essex));

				if($essex_response['status'] == "Error") {
					http_response_code(400);
					echo json_encode(['success' => false, 'message' => 'We’re sorry – there was a problem. Please call 757-544-9510 for more information.']);
					//log this error
				} else {
					return true;
				}
			}

		}	
		  

	}

	private function send_data_to_esendex_api($params) {

		$ch = curl_init('https://admin-api.esendex.us/api/signup');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","X-API-KEY: ".ESENDEXUSF_API_KEY));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $data = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        $res = [];		

		if ($curl_error) {
			error_log(PHP_EOL.ESENDEXUSF_LOG_SEPARATOR.PHP_EOL.date("F j, Y, g:i a e O").'Error Signup API:'. $curl_error, 3, ESENDEXUSF_PLUGIN_DIR.'/error_fede.log');
			return $res = ["status" => "Error","error_message" => "Error #:" . $curl_error];
		} else {
			error_log(PHP_EOL.ESENDEXUSF_LOG_SEPARATOR.PHP_EOL.date("F j, Y, g:i a e O").'Success Signup API:'. $data, 3, ESENDEXUSF_PLUGIN_DIR.'/error_fede.log');
			return $res = ["status" => "Ok", "response" => $data];
		}

	}
}
