<?php
	function getAdminAccess($var) {
		if(!isset($_SESSION['admin_permission'])) {
			return false;
		}
		
		$get_char = strpos($_SESSION['admin_permission'], $var);
		
		if($get_char === false) {
			return false;
		}
		
		return true;
	}
?>