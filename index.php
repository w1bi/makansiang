<?php
	include "include/core-top.php";
?>   
<?php
	include "include/template-top.php";
?>
<?php
	$now_date	= date('Y-m-d');	
	$list_query = mysqli_query($mysql, "SELECT * FROM ms_pesanan WHERE pesan_tanggal = '$now_date' ORDER BY pesan_id DESC");
	$pesanan_sekarang = mysqli_num_rows($list_query);
?>
<h1>
	Yang Makan!
    <span class="float-right quota-list">
    </span>
</h1>
<div id="listorang">
	<div class="wrapper-find-user">
    	<input type="text" class="find-user" placeholder="Cari Nama..." />
    </div>
    <?php 
		$pesanan_counter = 0;
		$max_user = $config['max_order'];
		
		while($list_pesanan = mysqli_fetch_array($list_query)) {
			$pesanan_counter++;
			
			$nama_user		= htmlentities($list_pesanan['pesan_user'], ENT_QUOTES);
			$email_user		= htmlentities($list_pesanan['pesan_user_email'], ENT_QUOTES);
			$photo			= htmlentities($list_pesanan['pesan_user_photo'], ENT_QUOTES);
			$pesanan		= preg_replace("/(\r?\n)+/", "<br />", htmlentities($list_pesanan['pesan_text'], ENT_QUOTES));
			$waktu_pesanan	= explode(":", $list_pesanan['pesan_waktu']);
			$uang_donasi	= $list_pesanan['pesan_uang_donasi'];
			
			$donatur		= ($uang_donasi > 0) ? true : false;
			
			$insert_even	= ($pesanan_counter%2 == 0) ? ' even' : '';
			
			if($pesanan_counter > $max_user) {
				$insert_even .= ' hide';
			}
			
			// Check menu
			while(preg_match("/\[\[menu\:([0-9]+):([0-9]+)\]\]/", $pesanan, $preg_arr)) {
				$the_string	= $preg_arr[0];
				$menu_id 	= $preg_arr[1];
				$menu_jml	= $preg_arr[2];
				
				if(isset($global_menu['menu_' . $menu_id])) {
					$menu_obj				= $global_menu['menu_' . $menu_id];
					
					$the_subtitute			= htmlentities($menu_obj['menu_nama']);
					
					if($preg_arr[2] > 1) {
						$the_subtitute .= " <span class=\"pesanan-jumlah\">[ x " . $menu_jml . " ]</span>";
					}
				
					$the_subtitute .= " <span class=\"pesanan-jumlah\"> - Rp. " . number_format(($menu_jml * $menu_obj['menu_harga']), 0, ',', '.') . "</span>";
				
					$pesanan = str_replace($the_string, $the_subtitute, $pesanan);
				} else {
					$pesanan = str_replace($the_string, "[Menu Dihapus]", $pesanan);
				}
			}
			
			// check remark
			if(preg_match("/\[remark\]((.|\n)*)\[\/remark\]/", $pesanan, $preg_arr)) {
				$the_string		= $preg_arr[0];
				$the_subtitute	= "";
				
				if($preg_arr[1] != "") {
					$the_subtitute	=  "<div class=\"info-header\">Info Tambahan</div>";
					$the_subtitute	.= "<div class=\"pesanan-info\">" . $preg_arr[1] . "</div>";
				}
				
				$pesanan = str_replace($the_string, $the_subtitute, $pesanan);
			}
			
	?>
    <div class="perone<?php echo $insert_even;?><?php if($donatur) { ?> donor<?php } ?>">
    	<div class="photo" title="<?php echo $nama_user; ?>">
        	<img src="<?php echo $photo;?>?sz=75" alt="<?php echo $nama_user; ?>">
            <div class="photo-number"><?php echo $pesanan_sekarang - $pesanan_counter + 1; ?></div>
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
            </div>
        </div>
		<div class="harga">
			<?php
            	if($is_login && $email_user == $login_data['user_email'] && $is_open) {
			?>
            <a class="button-inline" href="<?php echo $config['full_domain']; ?>makan-dong">Ubah</a>
            <?php
            	}
				elseif($waktu_pesanan[0] != '00' || $waktu_pesanan[1] != '00') {
					echo 'Jam: ' . $waktu_pesanan[0] . '.' . $waktu_pesanan[1];
				}
			?>
        </div>
        <div class="clear"></div>
    </div>
    <?php
        }
		
		if($pesanan_counter <= 0) {
    ?>
    <div class="perone">
    	Belum ada pesanan.
        <a class="button-inline-pesan" href="<?php echo $config['full_domain']; ?>makan-dong">Pesan Sekarang &raquo;</a>
    </div>
    <?php
		}
	?>
</div>
<script type="text/javascript">	
	var $num_user = 0;
	
	$('.find-user').on('keyup', function() {
		var findString = new RegExp($(this).val(), 'i');
		
		$num_user = 0;
		
		$('.perone').hide();
		$('.perone').filter(function(){
			if($num_user >= <?php echo $max_user; ?>) {
				return false;
			}
			
			if($(this).find('.user').text().match(findString)) {
				$num_menu++;
				return true;
			}
			
			return false;
		}).show();
	});
</script>
<?php
	include "include/template-bottom.php";
?>