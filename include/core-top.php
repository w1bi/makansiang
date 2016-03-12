<?php
	session_start();
	
	include 'include/settings.php';
	include 'include/mysql.php';
	include 'include/global-function.php';
	include 'include/JBBCode/Parser.php';
	
	// check logout
	if(isset($_GET['logout'])) {
		session_destroy();
		header("Location: ". $config['full_domain']);
		die;
	}
	
	if(isset($_GET['relog'])) {
		session_destroy();
		header("Location: ". $config['full_domain'] . "login");
		die;
	}
	
	if(isset($_GET['admin-logout']) && isset($_SESSION['is_admin'])) {
		unset($_SESSION['is_admin']);
		header("Location: ". $config['full_domain']);
		die;
	}
	
	$is_login 	= 0;
	$login_data;
	
	if(isset($_SESSION['is_login'])) {
		$is_login					= true;
		$login_data['user_name'] 	= $_SESSION['user_name'];
		$login_data['user_email']	= $_SESSION['user_email'];
		$login_data['user_photo']	= $_SESSION['user_photo'];
	}
	
	$is_admin	= (isset($_SESSION['is_admin'])) ? true : false;
	$admin_id	= ($is_admin) ? $_SESSION['is_admin'] : NULL;
	
	// Kalo ga login tapi masuk ke login only page
	if(!$is_login && isset($login_only_page)) {
		$_SESSION['login_referer'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header("Location: ". $config['full_domain'] . "login");
		die;
	}
	
	if(isset($admin_only_page) ) {
		if(!isset($_SESSION['is_admin'])) {
			header("Location: ". $config['full_domain'] . "backend-login");
			die;
		}
		elseif(!getAdminAccess($admin_only_page)) {
			header("Location: ". $config['full_domain'] . "daftar-pesanan");
			die;
		}
	}
	
	// Get Menu Nich
	$global_menu = array();
	$global_get_menu = mysqli_query($mysql, "
							SELECT
								*
							FROM
								ms_menu
							ORDER BY
								lower(menu_nama) ASC
						");
	
	while($global_get_menu_arr = mysqli_fetch_array($global_get_menu)) {
		$menu_id = $global_get_menu_arr['menu_id'];
		$global_menu['menu_' . $menu_id] = $global_get_menu_arr;
		$global_menu['menu_' . $menu_id]["tags"] = explode(",", strtolower($global_get_menu_arr["menu_tag"]));
		$global_menu['menu_' . $menu_id]["tags"] = array_map('trim', $global_menu['menu_' . $menu_id]["tags"]);
	}
	
	// Get Limit Date
	$config['max_order_time']		= "0000";
	
	$max_order_time_query   = mysqli_query($mysql, "
								SELECT hari_jam
								FROM ms_batas_jam
								WHERE hari_id = " . date("w") ."
							");
	
	if($max_order_time_result = mysqli_fetch_array($max_order_time_query)) {
		if(strlen($max_order_time_result[0]) == 4 && ctype_digit($max_order_time_result[0])){
			$config['max_order_time'] = $max_order_time_result[0];
		}
	}
?>