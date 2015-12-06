<?php
	session_start();
	
	if(isset($_SESSION['is_login'])) {
		header("Location: ". $config['full_domain'] . "makan-dong");
		die;
	}
	
	include			'include/settings.php';
	require_once 	'include/autoload.php';
	
	$api = new Google_Client();
	$api->setApplicationName($config['gplus_app_name']); // Set Application name
	$api->setClientId($config['gplus_client_id']); // Set Client ID
	$api->setClientSecret($config['gplus_client_secret']); //Set client Secret
	
	$api->setAccessType('online'); // Access method
	$api->setScopes(array('https://www.googleapis.com/auth/plus.login', 'https://www.googleapis.com/auth/plus.me', 'https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'));
	
	$api->setRedirectUri($config['full_domain'] . "login"); // Enter your file path (Redirect Uri) that you have set to get client ID in API console
	
	$service 	= new Google_Service_Plus($api);
	$oauth2 	= new Google_Service_Oauth2($api);
	
	if(isset($_GET['code'])) {
		$api->authenticate($_GET['code']);
		
		$data = $service->people->get('me');
		
		$gmail_email 	= strtolower($data->emails[0]->value);
		$gmail_domain 	= substr($gmail_email, strpos($gmail_email, '@') + 1);
		
		if($gmail_domain != $config['email_domain']) {
			$api_token = json_decode($api->getAccessToken());
			
			$options = array (
							CURLOPT_RETURNTRANSFER => false,
							CURLOPT_HEADER => false,
							CURLOPT_FOLLOWLOCATION => false,
							CURLOPT_ENCODING => "", 
							CURLOPT_USERAGENT => "Makan Siang Ga - CURL REVOKE OAUTH2",
							CURLOPT_AUTOREFERER => false,
							CURLOPT_CONNECTTIMEOUT => 10,
							CURLOPT_TIMEOUT => 10,
							CURLOPT_SSL_VERIFYHOST => 0,
							CURLOPT_SSL_VERIFYPEER => 0
							);
			
			$curl = curl_init("https://accounts.google.com/o/oauth2/revoke?token=" . $api_token->access_token);
			curl_setopt_array ( $curl, $options );
			curl_exec ($curl);
			curl_close ($curl);
			
			$api->reset;
			header("Location: ". $config['full_domain']);
			die;
		}
		else {
			$_SESSION['is_login'] 		= true;
			$_SESSION['user_name'] 		= $data->displayName;
			$_SESSION['user_email'] 	= $gmail_email;
			
			// Get Photo
			$get_photo	= $data->getImage()->getUrl();
			
			if(isset($get_photo) && $get_photo != "") {
				$_SESSION['user_photo']	= $get_photo;
			}
			else {
				$_SESSION['user_photo']	= $config['full_domain'] . 'images/photo-default.png';
			}
			if(isset($_SESSION['login_referer']) && $_SESSION['login_referer'] != "") {
				$login_referer = $_SESSION['login_referer'];
				unset($_SESSION['login_referer']);
				
				header("Location: ". $login_referer);
			}
			else {
				header("Location: ". $config['full_domain'] . "makan-dong");
			}
			die;
		}
	}
	else {		
		header("Location: ". $api->createAuthUrl());
	}
?>