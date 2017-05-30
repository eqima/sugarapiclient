<?php

namespace SugarAPI\Client;

/**
* 
*/
class AbstractClient
{
	    private $url, $user, $password, $session_data,
	    		$methods = array(
					'get_available_modules',
					'get_document_revision',
					'get_entries',
					'get_entries_count',
					'get_entry',
					'get_entry_list',
					'get_language_definition',
					'get_last_viewed',
					'get_modified_relationships',
					'get_module_fields',
					'get_module_fields_md5',
					'get_module_layout',
					'get_module_layout_md5',
					'get_note_attachment',
					'get_quotes_pdf',
					'get_relationships',
					'get_report_entries',
					'get_report_pdf',
					'get_server_info',
					'get_upcoming_activities',
					'get_user_id',
					'get_user_team_id',
					'job_queue_cycle',
					'job_queue_next',
					'job_queue_run',
					'login',
					'logout',
					'oauth_access',
					'seamless_login',
					'search_by_module',
					'set_campaign_merge',
					'set_document_revision',
					'set_entries',
					'set_entry',
					'set_note_attachment',
					'set_relationship',
					'set_relationships',
					'snip_import_emails',
					'snip_update_contacts'
				);
	 
	    public function __construct($user, $password, $url, $app_name  = 'SimpleRest')
	    {
	        $login_parameters = array(
	             "user_auth" => array(
	                  "user_name" => $user,
	                  "password" => md5($password),
	                  "version" => "1"
	             ),
	             "application_name" => $app_name,
	             "name_value_list" => array(),
	        );
	 
	        $this->session_data = self::call("login", $login_parameters, $url);
	        $this->user = $user;
	        $this->password = $password;
	        $this->url = $url;
	        $this->app_name = $app_name;
	    }
	 
	    public function getSessionData()
	    {
	        return $this->session_data;
	    }
	 
	    public static function call($method, $parameters, $url)
	    {
			
	        ob_start();
	        $curl_request = curl_init();
	 
	        curl_setopt($curl_request, CURLOPT_URL, $url);
	        curl_setopt($curl_request, CURLOPT_POST, 1);
	        curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	        curl_setopt($curl_request, CURLOPT_HEADER, 1);
	        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
	        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
	 
	        $jsonEncodedData = json_encode($parameters);

	 
	        $post = array(
	             "method" => $method,
	             "input_type" => "JSON",
	             "response_type" => "JSON",
	             "rest_data" => $jsonEncodedData
	        );
	 
	        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
	        $result = curl_exec($curl_request);
	        curl_close($curl_request);
	 
	        $result = explode("\r\n\r\n", $result, 2);
	        $response = json_decode($result[1]);
	        ob_end_flush();
	 
	        return $response;
	    }
	 
	    public function __call($method, $parameters)
	    {
	        $parameters = array_shift($parameters);

	        if (! in_array($method, $this->methods)) {
	        	return;
	        }

	        if (!array_key_exists('session', $parameters)) {        
	            $session_params = array('session' => $this->session_data->id);
	            $parameters = array_merge($session_params,$parameters);
	        }

	        return self::call($method, $parameters, $this->url);
	    }

}