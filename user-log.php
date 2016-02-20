<?php
	$login_only_page = true;
	include "include/core-top.php";
?>
<?php 
	include "include/template-top.php";
?>
    <h1>Riwayat 2 Minggu Terakhir</h1>
    <div class="clear"></div>
    <div id="listorang">
    <?php 
		$time_now = strtotime('-14 day', time());		
		$n14_date = date('Y-m-d', $time_now);
		
        $list_query = mysqli_query($mysql, "
			SELECT 
				*
			FROM
				ms_pesanan
			WHERE
				pesan_user_email = '". mysqli_real_escape_string($mysql, $login_data['user_email']) ."'
				AND pesan_tanggal >= '" . $n14_date . "'
			ORDER BY pesan_id DESC
		");
        
        $pesanan_counter = 0;
        
        while($list_pesanan = mysqli_fetch_array($list_query)) {
            $pesanan_counter++;
            
            $nama_user		= htmlentities($list_pesanan['pesan_user'], ENT_QUOTES);
			$email_user		= htmlentities($list_pesanan['pesan_user_email'], ENT_QUOTES);
			$photo			= htmlentities($list_pesanan['pesan_user_photo'], ENT_QUOTES);
			$pesanan		= str_replace("\n", "<br />", htmlentities($list_pesanan['pesan_text'], ENT_QUOTES));
			$status			= $list_pesanan['pesan_status'];
			$uang			= $list_pesanan['pesan_uang'];
			$uang_terpakai	= $list_pesanan['pesan_uang_terpakai'];
			$uang_donasi	= $list_pesanan['pesan_uang_donasi'];
			$uang_kembali	= $uang - $uang_terpakai - $uang_donasi;
			
			$donatur		= ($uang_donasi > 0) ? true : false;
			
			$insert_even	= ($pesanan_counter%2 == 0) ? ' even' : '';
			
			$status_text = '<strong>[ Belum Lunas ]</strong> Rp ' . number_format($uang, 0, '', '.');
			
			if($status >= 2) {
					$status_text = '<strong>[ Lunas ]</strong> Terpakai Rp ' . number_format($uang_terpakai, 0, '', '.');
			}
			
			// Check menu
			while(preg_match("/\[\[menu\:([0-9]+):([0-9]+)\]\]/", $pesanan, $preg_arr)) {
				$the_string	= $preg_arr[0];
				$menu_id 	= $preg_arr[1];
				$menu_jml	= $preg_arr[2];
				
				if(isset($global_menu['menu_' . $menu_id])) {
					$menu_obj	= $global_menu['menu_' . $menu_id];
					
					$the_subtitute	= htmlentities($menu_obj['menu_nama']);
					
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
			
			$tanggal		= strtotime($list_pesanan['pesan_tanggal'], time());
			
			$insert_even	= ($pesanan_counter%2 == 0) ? ' even' : '';
	?>
    <div class="perone<?php echo $insert_even;?><?php echo $insert_even;?><?php if($donatur) { ?> donor<?php } ?>">
    	<div class="photo">
        <img src="<?php echo $photo;?>?sz=75">
        </div>
        <div class="list">
            <div class="user">
				<?php echo $nama_user; ?>
            </div>
            <div class="detail">
                <?php echo $pesanan; ?>
                <?php if($donatur) { ?>
                	<br  />
                    <span class="donasi-info">
                    Donasi : Rp <?php echo number_format($uang_donasi, 0, '', '.'); ?>
                    </span>
                <?php  } ?>
            </div>
        </div>
		<div class="harga">
        	<?php echo $status_text; ?>
        </div>
		<div class="building b2">
            <?php
            	echo date('d M Y', $tanggal);
			?>
		</div>
        <div class="clear"></div>
    </div>
    <?php
        }
        
        if($pesanan_counter <= 0) {
    ?>
    <div class="perone">
        Belum ada riwayat...
    </div>
    <?php
        }
    ?>
    </div>
<?php
	include "include/template-bottom.php";
?>