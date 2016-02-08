<?php
	$admin_only_page = 'a';
	include "include/core-top.php";
?>   
<?php
	if(isset($_POST['id']) && isset($_POST['status'])) {
		$id 			= trim($_POST['id']);
		$status			= trim($_POST['status']);
		$uang_terpakai	= "";
		
		$form_uang_terpakai	= (isset($_POST['terpakai'])) ? str_replace('.', '', trim($_POST['terpakai'])) : 'not a number lol';
		
		if(ctype_digit($form_uang_terpakai)) {
			$uang_terpakai	= $form_uang_terpakai;
		}
		
		if(ctype_digit($id) && ctype_digit($status)) {
			$uang_terpakai_query = "";
			if($uang_terpakai != "") {
				$uang_terpakai_query = ", pesan_uang_terpakai = $uang_terpakai";
			}
			
			$query = "
				UPDATE ms_pesanan
				SET
				pesan_status = $status
				$uang_terpakai_query				
				WHERE
				pesan_id = $id
			";
			
			// Exec
			mysqli_query($mysql, $query);
		}
		
		$refback = isset($_POST['refback']) ? trim($_POST['refback']) : $config['full_domain']."daftar-pesanan";
		
		header("Location: " . $refback);
		die;
	}
?>
<?php
	include "include/template-top.php";
?>
<?php
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
	
	$link_prev_date = $config['full_domain'] . 'daftar-pesanan/' . ($get_time_minus + 1);
	$link_next_date = ($get_time_minus > 0) ? $config['full_domain'] . 'daftar-pesanan/' . ($get_time_minus - 1) : '';
	
	// Status Pesanan
	$status 	= -1;
	
	if(isset($_GET['belum'])) {
		$status = 0;
	}
	elseif(isset($_GET['terhitung'])) {
		$status = 1;
	}
	elseif(isset($_GET['terbayar'])) {
		$status = 2;
	}
?>

<div id="listorang">
    <h1>
        Yang Makan!
        <?php if($status < 0) { ?>
        <span class="float-right quota-list">
            <a href="<?php echo $config['full_domain'] . "daftar-pesanan-text/" . $get_time_minus; ?>" class="button-inline-pesan">Mode Hanya Teks &raquo;</a>
        </span>
        <?php } ?>
    </h1>
    <div class="date-picker">
        <a href="<?php echo $link_prev_date; ?>" class="button-admin-next-prev prev">&laquo;</a>
        
        <?php echo $info_date; ?>
        
        <?php if($link_next_date != "") {?>
        <a href="<?php echo $link_next_date; ?>" class="button-admin-next-prev next">&raquo;</a>
        <?php } ?>
    </div>

	<?php
		
        $all_used_saldo 	= 0;
		$all_bought			= 0;
		$all_donate			= 0;
        $all_used_saldo_sql	= mysqli_query($mysql, "SELECT SUM(pesan_uang), SUM(pesan_uang_terpakai), SUM(pesan_uang_donasi) FROM ms_pesanan WHERE pesan_tanggal = '$now_date'");
        
        if($all_used_saldo_data = mysqli_fetch_array($all_used_saldo_sql)) {
            $all_used_saldo 	= $all_used_saldo_data[0];
			$all_bought			= $all_used_saldo_data[1];
            $all_donate 		= $all_used_saldo_data[2];
        }
		
		$all_saldo_gathered		= 0;
        $all_saldo_gathered_sql	= mysqli_query($mysql, "SELECT SUM(pesan_uang) FROM ms_pesanan WHERE pesan_tanggal = '$now_date' AND pesan_status > 0");
		
		if($all_saldo_gathered_data = mysqli_fetch_array($all_saldo_gathered_sql)) {
            $all_saldo_gathered	= $all_saldo_gathered_data[0];
        }
		
		$all_saldo_unused		= $all_saldo_gathered - $all_bought - $all_donate;
    ?>
	<div class="saldo-terpakai-admin">
    	<div class="uang float-left">
            Uang Total<br />
            <strong>Rp <?php echo number_format($all_used_saldo, 0, '', '.'); ?></strong>
        </div>
		<div class="uang float-left">
            Donasi<br />
            <strong>Rp <?php echo number_format($all_donate, 0, '', '.'); ?></strong>
        </div>
        <?php if($status < 0) { ?>
    	<div class="uang float-left">
            Estimasi Terpakai<br />
            <strong id="estimasi-uang">...</strong>
        </div>
        <?php } ?>
    	<div class="uang float-left">
            Terpakai<br />
            <strong>Rp <?php echo number_format($all_bought, 0, '', '.'); ?></strong>
        </div>
    	<div class="uang float-left">
            Tidak Terpakai<br />
            <strong>Rp <?php echo number_format($all_saldo_unused, 0, '', '.'); ?></strong>
        </div>
        <div class="clear"></div>
    </div>
    <?php
		$is_cookie_search	= false;
		$select_lantai = "";
		
		if(isset($_COOKIE['ai_b'])) {
			$is_cookie_search = true;
			
			$select_lantai = $_COOKIE['ai_b'];
		}
	?>
	<div class="filter-container">
		<?php
            $cookie_search_name = "";
            if(isset($_COOKIE['ai_u'])) {
                $is_cookie_search = true;
                $cookie_search_name = htmlentities(urldecode($_COOKIE['ai_u']));
            }
        ?>
        <select class="full float-left" id="change_target_status" onchange="location.href=this.value;" name="change_target" style="width: 49%;">
            <option value="?semua">Semua Status</option>
            <option value="?belum"<?php if(isset($_GET['belum'])) { ?> selected="selected"<?php } ?>>Belum Dihitung</option>
            <option value="?terhitung"<?php if(isset($_GET['terhitung'])) { ?> selected="selected"<?php } ?>>Sudah Dihitung</option>
            <option value="?terbayar"<?php if(isset($_GET['terbayar'])) { ?> selected="selected"<?php } ?>>Sudah Dibayar</option>
        </select>
    	<input type="text" id="find-user-admin" class="float-right" placeholder="Cari Nama..." value="<?php echo $cookie_search_name; ?>" style="width: 49%;" />
    </div>
    <div class="clear"></div>
    <?php 		
		$query_status = "";
		
		if($status >= 0) {
			$query_status = "AND pesan_status = $status";
		}
		
		$list_query = mysqli_query($mysql, "SELECT * FROM ms_pesanan WHERE pesan_tanggal = '$now_date' $query_status ORDER BY pesan_id ASC");
		
		$pesanan_estimasi_terpakai	= 0;
		$pesanan_counter			= 0;
		
		while($list_pesanan = mysqli_fetch_array($list_query)) {
			$pesanan_counter++;
			
			$pesanan_id		= $list_pesanan['pesan_id'];
			$pesanan_status = $list_pesanan['pesan_status'];
			$nama_user		= htmlentities($list_pesanan['pesan_user'], ENT_QUOTES);
			$email_user		= htmlentities($list_pesanan['pesan_user_email'], ENT_QUOTES);
			$photo			= htmlentities($list_pesanan['pesan_user_photo'], ENT_QUOTES);
			$pesanan		= str_replace("\n", "<br />", htmlentities($list_pesanan['pesan_text'], ENT_QUOTES));
			$uang_terpakai	= $list_pesanan['pesan_uang_terpakai'];
			$uang_donasi	= $list_pesanan['pesan_uang_donasi'];
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
	?>
    <div class="perone<?php echo $insert_even;?><?php if($donatur) { ?> donor<?php } ?>">
    	<div class="photo" title="<?php echo $nama_user; ?>">
        	<img src="<?php echo $photo;?>?sz=75" alt="<?php echo $nama_user; ?>">
        	<div class="photo-number"><?php echo $pesanan_counter; ?></div>
        </div>
        <div class="list">
            <div class="user">
				<?php echo $nama_user; ?>
				<?php if($donatur) { ?>
                    <div class="donator-user" title="Dogenatur"></div>
                <?php } ?>
            </div>
            <div class="detail">
                <?php echo $pesanan; ?>
                <?php if($donatur) { ?>
                	<br />
                    <div class="donasi-info">
                    Donasi : Rp <?php echo number_format($uang_donasi, 0, '', '.'); ?>
                    </div>
                <?php  } ?>
            </div>
        </div>
        <div class="harga harga-admin">
            <form action="<?php echo $config['full_domain']; ?>daftar-pesanan" method="post" enctype="application/x-www-form-urlencoded" onSubmit="return clickForm(this);">
                <input type="hidden" name="id" value="<?php echo $pesanan_id; ?>" />
                <input type="hidden" name="status" value="<?php echo $pesanan_status + 1; ?>" />
                <input type="hidden" name="refback" value="<?php echo $refback; ?>" />
                
                <?php if($pesanan_status > 0) { ?>
                <input type="button" name="not-submit" class="button-inline" value="&laquo;" onclick="cancelStatus(this, <?php echo $pesanan_status; ?>)" />&nbsp;
                <?php } ?>
                
                <?php if($pesanan_status == 0) { ?>
                
                
                Rp <input type="text" class="numberformat small-text" name="terpakai" value="<?php echo number_format($uang_makanan, 0, '', '.'); ?>" />
                <input type="submit" class="button-inline" href="<?php echo $config['full_domain']; ?>daftar-pesanan" value="Simpan" />
                
                <?php } elseif($pesanan_status == 1) { ?>
                
                <input type="hidden" name="terpakai" value="<?php echo $uang_terpakai; ?>" />
                <input type="submit" class="button-inline bold-dikit" value="Rp <?php echo number_format($uang_terpakai_tambah_donasi, 0, '', '.'); ?> - Lunas" />
                
                <?php } elseif($pesanan_status >= 2) { ?>
                
                Rp <?php echo number_format($uang_terpakai_tambah_donasi, 0, '', '.'); ?>
                
                <?php } ?>
            </form>
        </div>
        <div class="clear"></div>
    </div>
    <?php
        }
		
		if($pesanan_counter <= 0) {
    ?>
    <div class="perone">
    	Tidak ada pesanan
    </div>
    <?php
		}
	?>
    </div>
<script src="<?php echo $config['full_domain']; ?>scripts/jquery.number.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$('.numberformat').number(true, 0, ',', '.');
	
	function clickForm(obj) {		
		if(!confirm("Yakin?")) {
			return false;
		}
		
		return true;
	}
	
	function cancelStatus(obj, stat) {
		if(!confirm("Yakin mengembalikan status ke sebelumnya?")) {
			return false;
		}
		
		obj.form.status.value = stat - 1;
		if(obj.form.status.value == 0) {
			obj.form.terpakai.value = "0";
		}
		obj.form.removeAttribute('onSubmit');
		obj.form.submit();
	}
	
	$('.tags').on('click', function(){
		curr_val	= $('#find-user-tag').val();
		curr_val_lc	= curr_val.toLowerCase();
		this_tag	= $(this).html();
		this_tag_lc	= this_tag.toLowerCase();
		
		search_tags	= curr_val_lc.split(/,\s*/);
		
		if(search_tags.indexOf(this_tag_lc) >= 0) {
			return;
		}
		
		if(curr_val != "") {
			curr_val += ", ";
		}
		
		curr_val += this_tag;
		$('#find-user-tag').val(curr_val);
		searchComplex();
	});
	
	$('#find-user-building').on('change', searchComplex);
	$('#find-user-admin').on('keyup change', searchComplex);
	$('#find-user-tag').on('keyup change', searchComplex);
	function searchComplex() {	
		var search_name		= $('#find-user-admin').val();
		
		var nameRegex		= new RegExp(search_name, 'i'); 
	
		$('.perone').hide();
		$('.perone').filter(function(){
			return $(this).find('.user').text().match(nameRegex)
		}).show();
		
		/* Creating Cookie */
		var d = new Date();
		d.setTime(d.getTime() + (10*60*1000));
		document.cookie="ai_u=" + encodeURIComponent(search_name) + "; expires=" + d.toUTCString() + "; path=/";
	}
	<?php if($is_cookie_search) { ?>
	searchComplex(true);
	<?php } ?>
	
	$('#estimasi-uang').html("Rp <?php echo number_format($pesanan_estimasi_terpakai, 0, '', '.'); ?>");
</script>
<?php
	include "include/template-bottom.php";
?>