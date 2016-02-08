<?php
	$admin_only_page = 'a';
	include "include/core-top.php";
	
	// Template
	$config["template_full"] = true;
	
	$get_time_minus = 0;
	
	if(isset($_GET['rwd']) && $_GET['rwd'] != "" && ctype_digit($_GET['rwd'])) {
		$get_time_minus = $_GET['rwd'];
	}
	
	$time_now	= time();
	
	if($get_time_minus > 0) {
		$time_now = strtotime('-'.$_GET['rwd'].' day', $time_now);
	}
	
	$now_date	= date('Y-m-d', $time_now);
	$info_date	= date('d F Y', $time_now);	
	
	// Status Pesanan
	$status 	= -1;
?>
<html>
<head>
	<title>Print MSG - <?php echo $info_date; ?></title>
	<meta name="viewport" content="width=device-width, height=device-height, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
    <link rel="shortcut icon" type="image/ico" href="<?php echo $config['full_domain']; ?>favicon.ico"/>
    <link href="<?php echo $config['full_domain']; ?>style/admin-print.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php	
	$query_status = "";
	
	if($status >= 0) {
		$query_status = "AND pesan_status = $status";
	}
	
	$list_query = mysqli_query($mysql, "SELECT * FROM ms_pesanan WHERE pesan_tanggal = '$now_date' $query_status ORDER BY pesan_id ASC");
	
	$pesanan_estimasi_terpakai	= 0;
	$pesanan_counter			= 0;
	$simplified_array_menu		= array();
	$simplified_array_menu_user	= array();
	$simplified_array_user_info	= array();
	
	while($list_pesanan = mysqli_fetch_array($list_query)) {
		$pesanan_counter++;
		
		$pesanan_id		= $list_pesanan['pesan_id'];
		$pesanan_status = $list_pesanan['pesan_status'];
		$nama_user		= htmlentities($list_pesanan['pesan_user'], ENT_QUOTES);
		$email_user		= htmlentities($list_pesanan['pesan_user_email'], ENT_QUOTES);
		$photo			= htmlentities($list_pesanan['pesan_user_photo'], ENT_QUOTES);
		$pesanan		= str_replace("\n", "<br />", htmlentities($list_pesanan['pesan_text'], ENT_QUOTES));
		$uang			= $list_pesanan['pesan_uang'];
		$uang_terpakai	= $list_pesanan['pesan_uang_terpakai'];
		$uang_donasi	= $list_pesanan['pesan_uang_donasi'];
		$uang_kembali	= $uang - $uang_terpakai - $uang_donasi;
		$uang_terpakai_tambah_donasi	= $uang_terpakai + $uang_donasi;
		
		$donatur		= ($uang_donasi > 0) ? true : false;
		
		$insert_even	= ($pesanan_counter%2 == 0) ? ' even' : '';
		
		$pesanan_tags	= array();
		$uang_makanan	= 0;
		while(preg_match("/\[\[menu\:([0-9]+):([0-9]+)\]\]/", $pesanan, $preg_arr)) {
			$the_string	= $preg_arr[0];
			$menu_id 	= $preg_arr[1];
			$menu_jml	= $preg_arr[2];
			$menu_obj	= $global_menu['menu_' . $menu_id];
			
			$pesanan_tags = array_merge($pesanan_tags, $menu_obj['tags']);
			$uang_makanan += $menu_obj['menu_harga'];
			$the_subtitute = htmlentities($menu_obj['menu_nama']);
			
			if($preg_arr[2] > 1) {
				$the_subtitute .= " <span class=\"pesanan-jumlah\">[ x " . $menu_jml . " ]</span>";
			}
			
			$the_subtitute .= " <span class=\"pesanan-jumlah\"> - Rp. " . number_format(($menu_jml * $menu_obj['menu_harga']), 0, ',', '.') . "</span>";
			
			$pesanan = implode($the_subtitute, explode($the_string, $pesanan, 2));
			
			// For Simplified
			$simplified_array_menu[] = $menu_obj['menu_nama'] . "\n-\n" . $menu_id . "\n-\n" . $menu_obj['menu_tag'];
			
			if(!isset($simplified_array_menu_user['menu_' . $menu_id])) {
				$simplified_array_menu_user['menu_' . $menu_id] = array();
			}
			$simplified_array_menu_user['menu_' . $menu_id][] = $nama_user . "\n-\n" . $pesanan_id;
		}
		
		$pesanan_estimasi_terpakai += $uang_makanan;
		
		sort($pesanan_tags);
		$pesanan_tags = array_unique($pesanan_tags);
		
		if(preg_match("/\[remark\]((.|\n)*)\[\/remark\]/", $pesanan, $preg_arr)) {
			$the_string		= $preg_arr[0];
			$the_subtitute	= "";
			
			if($preg_arr[1] != "") {
				$the_subtitute	=  "<div class=\"info-header\">Info Tambahan</div>";
				$the_subtitute	.= "<div class=\"pesanan-info\">" . $preg_arr[1] . "</div>";
				
				$simplified_array_user_info['order_' . $pesanan_id] = $preg_arr[1];
			}
			
			$pesanan = str_replace($the_string, $the_subtitute, $pesanan);
		}
		
		$refback	= htmlentities(preg_replace('/^(https?:\/\/[^\/]+).*$/', '$1', $config['full_domain']) . $_SERVER['REQUEST_URI'], ENT_QUOTES);
	}
	
	if(count($simplified_array_menu) > 0) {
		sort($simplified_array_menu);
		$simplified_array_menu_unique = array_values(array_unique($simplified_array_menu));
		
		echo "<div id=\"area-select\">\n";
		echo "<h3>Pilih Menu:</h3>\n";
		echo "<div id=\"check-all-button\" class=\"small-button\">Centang Semua!</div>\n";
		echo "<div id=\"uncheck-all-button\" class=\"small-button\">Hilangkan Semua Centang!</div>\n";
		echo "<div style=\"float: none; clear: both;\"></div>\n";
		for($i = 0; $i < count($simplified_array_menu_unique); $i++) {
			$menu_sort	= $simplified_array_menu_unique[$i];
			$split_it	= explode("\n-\n", $menu_sort, 3);
			$p_name		= $split_it[0];
			$p_m_id		= $split_it[1];
			$p_tag		= $split_it[2];
			
			echo "<label class=\"area-name\">";
			echo "<input type=\"checkbox\" id=\"menu_" . $p_m_id . "\" class=\"menu_list_show\">";
			echo htmlentities($p_name, ENT_QUOTES);
			echo "</label>";
		}
		echo "<div style=\"float: none; clear: both;\"></div>\n";
		echo "<a href=\"javascript:printThis();\" id=\"print-button\">Cetak Halaman Ini!</a>\n";
		echo "</div>\n";
		echo "<div id=\"listminified\">\n";
		for($i = 0; $i < count($simplified_array_menu_unique); $i++) {
			$menu_sort	= $simplified_array_menu_unique[$i];
			$split_it	= explode("\n-\n", $menu_sort, 3);
			$p_name		= $split_it[0];
			$p_m_id		= $split_it[1];
			$p_tag		= $split_it[2];
			
			$array_menu_list	= $simplified_array_menu_user['menu_' . $p_m_id];
			$total_list			= count($array_menu_list);
			
			echo "\n\n<div class=\"minify-per-one\" id=\"box_menu_" . $p_m_id . "\">";
			echo "<h3>" . ucwords(htmlentities($p_name)) . "</h3>";				
			echo "<div class=\"total-pesanan\">Total: <strong>$total_list Pesanan</strong>";
			if($p_tag != "") {
				$p_tags		= explode(",", $p_tag);
				foreach($p_tags as $tag) {
					echo "<div class=\"listminified-smallinfo-tag\">";
					echo ucwords(htmlentities(trim($tag)));
					echo "</div>";
				}
			}
			
			echo "</div>\n";
			
			sort($array_menu_list);
			
			for($i2 = 0; $i2 < count($array_menu_list); $i2++) {
				
				$menu_user_sort	= $array_menu_list[$i2];
				$split_it_list	= explode("\n-\n", $menu_user_sort, 2);
				$p_user			= $split_it_list[0];
				$p_id			= $split_it_list[1];
			
				$p_id_next		= -999;
				
				if($i2 < count($array_menu_list)-1) {
					$menu_sort_next	= $array_menu_list[$i2+1];
					$split_it_next	= explode("\n-\n", $menu_sort_next, 2);
					$p_id_next		= $split_it_next[1];
				}
			
				if($p_id_next == $p_id) {
					$i2++;
					
					echo "<strong><em>";
					echo $i2 . ". ";
					echo "[ x2 ] ";
					echo ucwords(htmlentities($p_user));
					echo "</em></strong><br />";
					
					echo "<strong><em>";
					echo ($i2+1) . ". ";
					echo "[ x2 ] ";
					echo ucwords(htmlentities($p_user));
					echo "</em></strong>";
				}
				else {
					echo "<strong><em>";
					echo ($i2+1) . ". ";
					echo ucwords(htmlentities($p_user));
					echo "</em></strong>";
				}
			
				echo "<div class=\"listminified-detail\">";
				
				if(isset($simplified_array_user_info['order_' . $p_id])) {
					$temp_text = preg_replace('((\<br\ \/\>\n*\s*)+)', '<br />', $simplified_array_user_info['order_' . $p_id]);
					echo "<div class=\"listminified-smallinfo\">";
					echo $temp_text;
					echo "</div>";
				}
				
				echo "</div>\n";
			}
			echo "</div>\n";
		}
		echo '</div>';
	} else {
		echo ' - ';
	}
?>

<script src="<?php echo $config['full_domain']; ?>scripts/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
$('.menu_list_show').on('change', function() {
	this_id = $(this).attr('id');
	has_class_checked = $(this).parent().hasClass('checked');
	
	if(has_class_checked) {
		$(this).parent().removeClass('checked');
		$('#box_' + this_id).hide();
	}
	else {
		$(this).parent().addClass('checked');
		$('#box_' + this_id).show();
	}
});

$('#check-all-button').on('click', function() {
	$('.menu_list_show').prop('checked', true);
	$('.area-name').addClass('checked');
	$('.minify-per-one').show();
});

$('#uncheck-all-button').on('click', function() {
	$('.menu_list_show').prop('checked', false);
	$('.area-name').removeClass('checked');
	$('.minify-per-one').hide();
});

function printThis() {
    $('#area-select').find('.checked').addClass('printed').attr('title', 'Telah di-print');
	window.print();
}
</script>
</body>
</html>