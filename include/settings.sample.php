<?php	
	// MySQL
	$mysql_host				= "localhost";		// Koneksi ke DB, default Localhost
	$mysql_username			= "root";			// User Database, default root
	$mysql_password			= "";				// Password Database, default kosong
	$mysql_database			= "makansiang";		// Nama Database, default makansiang
	
	// GOOGLE +
	$config['gplus_app_name']		= "makan-siang-tokopedia";						// G+ App Name yang telah didaftarkan
	$config['gplus_client_id']		= "gplusclientid.apps.googleusercontent.com";	// G+ Client ID.
	$config['gplus_client_secret']	= "gplusclientsecret";							// G+ Client Secret
	
	// Other
	$config['full_domain']			= "http://makansiang.ga/";	// Full Domain, jangan lupa slash dibelakang. Kalau localhost biasanya http://localhost/makansiang/
	$config['email_domain']			= "tokopedia.com";			// G+ Domain, disarankan harus memiliki domain sendiri
	
	$config['max_order_time']		= "1030";	// Maksimal waktu order, format = JamMenit
	$config['max_order']			= "50";		// Maksimal orang perhari
?>