<?php
	$login_only_page = true;
	include "include/core-top.php";
?>
<?php	
	$now_date		= date('Y-m-d');
	$is_open		= (date('Gi') >= $config['max_order_time']) ? false : true;
	
	if($is_open) {
		$open_limit = mysqli_query($mysql, "
							SELECT count(pesan_id)
							FROM ms_pesanan
							WHERE pesan_tanggal = '$now_date'
						");
	
		if($open_limit_arr = mysqli_fetch_array($open_limit)) {
			$is_max_open = $open_limit_arr[0];
			
			if($open_limit_arr[0] >= $config['max_order']) {
				$is_open = false;
			}
		}
	}
	
	$form_error 		= "";
	$form_success		= "";
	$form_makanan		= "";
	$form_makanan_id	= "";
	$form_makanan_harga	= 0;
	$form_tambahan		= "";
	$form_tambahan_id	= "";
	$form_tambahan_harga= 0;
	$form_pesanan 		= "";
	$form_pesandonasi	= "";
	$is_already_order	= false;
	
	if($is_open) {
		if(preg_match('/\/A+I\/A+\//i', $login_data['user_photo'])) {
			$form_error	= "PROFILWOI";
		}
		
		// Check Last, is it paid?
		$check_last_paid = mysqli_query($mysql, "
							SELECT pesan_status, pesan_tanggal
							FROM ms_pesanan
							WHERE
								pesan_user_email = '" . mysqli_real_escape_string($mysql, $login_data['user_email']) . "'
								AND pesan_tanggal <> '$now_date'
							ORDER BY pesan_id DESC
							LIMIT 0,1
						");
	
		if($check_last_paid_fetch = mysqli_fetch_array($check_last_paid)) {
			$is_max_open = $check_last_paid_fetch[0];
			
			if($check_last_paid_fetch[0] < 2) {
				$is_open = false;
				$date = explode("-", $check_last_paid_fetch[1]);
				$date_string = $date[2]."/".$date[1]."/".$date[0];
				$form_error = "Pesanan kamu tanggal " . $date_string . " belum dibayar!\nSegera hubungi OB atau ke Lantai 7!";
			}
		}
	}
	
	if(isset($_POST['send'])) {
		if(isset($_POST['ordered'])) {
			$is_already_order = true;
		}
		
		$form_makanan		= trim($_POST['makanan']);
		$form_makanan_id	= trim($_POST['makanan_id']);
		$form_makanan_harga	= trim($_POST['makanan_harga']);
		$form_tambahan		= trim($_POST['tambahan']);
		$form_tambahan_id	= trim($_POST['tambahan_id']);
		$form_tambahan_harga= trim($_POST['tambahan_harga']);
		$form_pesanan 		= trim($_POST['pesan']);
		$form_pesandonasi	= str_replace('.', '', trim($_POST['donasi']));
		$form_lantai		= 7;
		
		if(!ctype_digit($form_pesandonasi)) {
			$form_pesandonasi = 0;
		}
		
		if(!ctype_digit($form_makanan_id)) {
			$form_makanan_id = 0;
		}
		
		if(!$is_open) {
			$form_error = "Waktu pemesanan sudah ditutup";
		}
		
		if($form_error == "" && (!isset($global_menu['menu_' . $form_makanan_id]) || $form_makanan == "")) {
			$form_error = "Harap isi pesanan dengan lengkap";
		}
		
		if($form_error == "" && ($form_tambahan != "" && $form_tambahan_id == "")) {
			$form_error = "Menu Tambahan harus dipilih dari menu!";
		}
		
		if($form_error == "") {
			$price_total = $form_pesandonasi;
			$price_total += $global_menu['menu_' . $form_makanan_id]['menu_harga'];
			if($form_tambahan_id != "" && isset($global_menu['menu_' . $form_tambahan_id])) {
				$price_total += $global_menu['menu_' . $form_tambahan_id]['menu_harga'];
			}
		}
			
		if($form_error == "") {
			$insert_pesanan	= "[[menu:$form_makanan_id:1]]\n";
			if($form_tambahan_id != "" && isset($global_menu['menu_' . $form_tambahan_id])) {
				$insert_pesanan	.= "[[menu:$form_tambahan_id:1]]\n";
			}
			$insert_pesanan	.= "[remark]${form_pesanan}[/remark]";
			
			$form_pesanan	= $insert_pesanan;
			
			$sql_insert = "
			INSERT INTO ms_pesanan
				(pesan_tanggal, pesan_waktu, pesan_user, pesan_user_email, pesan_user_photo, pesan_gedung, pesan_uang_donasi, pesan_text)
			VALUES
				(CURDATE() , 
				 CURTIME(), 
				 '" . mysqli_real_escape_string($mysql, $login_data['user_name']) . "',
				 '" . mysqli_real_escape_string($mysql, $login_data['user_email']) . "',
				 '" . mysqli_real_escape_string($mysql, preg_replace('/\?sz\=[0-9]+/i', '', $login_data['user_photo'])) . "',
				 '" . mysqli_real_escape_string($mysql, $form_lantai) . "',
				 '" . mysqli_real_escape_string($mysql, $form_pesandonasi) . "',
				 '" . mysqli_real_escape_string($mysql, $insert_pesanan) . "'
				)
			ON DUPLICATE KEY UPDATE
				pesan_text = values(pesan_text),
				pesan_uang = values(pesan_uang),
				pesan_uang_donasi = values(pesan_uang_donasi),
				pesan_gedung = values(pesan_gedung)
			";
			
			if(mysqli_query($mysql, $sql_insert)) {
				$is_already_order = true;
				$form_success = "Berhasil Menyimpan Pesanan";
				#header("Location: ". $config['full_domain']);
				#die;
			}
			else {
				$form_error = "Unknown Error [ 0x00IDKM8 ]";
			}
		}
	} elseif(isset($_POST['cancel'])) {
		mysqli_query($mysql, "
			DELETE
			FROM ms_pesanan 
			WHERE pesan_tanggal = '$now_date' 
			AND pesan_user_email = '". mysqli_real_escape_string($mysql, $login_data['user_email']) ."'
		");
		
		$form_success = "Pesanan Dibatalkan";
	} else {
		$pesanan_query = mysqli_query($mysql, "
			SELECT pesan_text, pesan_uang, pesan_uang_donasi, pesan_gedung
			FROM ms_pesanan 
			WHERE pesan_tanggal = '$now_date' 
			AND pesan_user_email = '". mysqli_real_escape_string($mysql, $login_data['user_email']) ."'
		");
		
		if($data = mysqli_fetch_array($pesanan_query)) {
			$form_pesanan = $data[0];
			$form_pesandonasi = $data[2];
			$cookie_lantai = $data[3];
			$is_already_order = true;
		}
	}
	
	if(preg_match("/\[remark\]((.|\n)*)\[\/remark\]/", $form_pesanan, $preg_arr)) {
		// Check menu
		$makanan_count = 0;
		while(preg_match("/\[\[menu\:([0-9]+):([0-9]+)\]\]/", $form_pesanan, $preg_arr_2)) {
			$the_string	= $preg_arr_2[0];
			$menu_id 	= $preg_arr_2[1];
			$menu_jml	= $preg_arr_2[2];
			$menu_obj	= $global_menu['menu_' . $menu_id];
			
			$makanan_count++;
			if($makanan_count == 1) {
				$form_makanan		= $menu_obj["menu_nama"];
				$form_makanan_id 	= $menu_obj["menu_id"];
				$form_makanan_harga	= $menu_obj["menu_harga"];
			} else {
				$form_tambahan		= $menu_obj["menu_nama"];
				$form_tambahan_id 	= $menu_obj["menu_id"];
				$form_tambahan_harga= $menu_obj["menu_harga"];
			}
			
			$form_pesanan		=  str_replace($the_string, "", $form_pesanan);
		}
		
		$form_pesanan = $preg_arr[1];
	}
	
	if($is_open && isset($_POST['clickmenu'])) {
		$form_makanan		= trim($_POST['clickmenu']);
		$form_makanan_id	= trim($_POST['clickmenuid']);
		$form_makanan_harga	= trim($_POST['clickmenuprice']);
	}
?>
<?php 
	include "include/template-top.php";
?>
<form enctype="application/x-www-form-urlencoded" method="post" action="<?php echo $config['full_domain']; ?>makan-dong">
    <div id="table-container">
        <?php if($form_success != "") { ?>
			<div class="success-text-block"><?php echo htmlentities($form_success, ENT_QUOTES); ?></div>
		<?php } ?>
        <?php if($form_error != "") { ?>
        	<?php if($form_error == "PROFILWOI") { ?>
			<div class="err-text-block">
            	Mohon unggah foto kamu pada profil <a href="https://plus.google.com/me" target="_blank">Google+</a>
                dan lakukan <a href="<?php echo $config['full_domain']; ?>?relog">Login Ulang</a>.
             </div>
            <?php } else { ?>
			<div class="err-text-block"><?php echo str_replace("\n", "<br />", htmlentities($form_error, ENT_QUOTES)); ?></div>
            <?php } ?>
		<?php } ?>
        <?php if(!$is_open) { ?>
        	<?php if(date('Gi') > $config['max_order_time']) { ?>
			<div class="err-text-block">Waktu Pemesanan Telah Habis</div>
            <?php } elseif ($form_error == "") { ?>
			<div class="err-text-block">Kuota Penuh: <strong><?php echo $config['max_order']; ?></strong> Pemesan Tercapai</div>
            <?php } ?>
		<?php } ?>
		<h1>Info</h1>
        <table border="0">
            <tbody>
                <tr>
                    <td width="30%" valign="middle">G+ Account:</td>
                    <td>
                    	<div class="photo" style="float: left; height: 75px; width: 75px;vertical-align:middle; cursor: pointer;">
                    		<img src="<?php echo preg_replace('/\?sz\=[0-9]+/i', '', $login_data['user_photo']) ?>?sz=75" style="border-radius: 75px;" />
                        </div>
                        <div style="margin-top: 17px; margin-left: 85px;">
                        	<?php echo htmlentities($login_data['user_name'], ENT_QUOTES); ?>
                            <br />
                            <span class="small-text-info">
                        	<?php echo htmlentities($login_data['user_email'], ENT_QUOTES); ?>
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <br />
        <h1>Pesanan</h1>
        <table border="0">
            <tbody>
                <tr>
                	<td width="30%" valign="top">Menu:</td>
                    <td>
                    	<input type="text"
                               name="makanan"
                               id="main-food-autocomplete"
                               class="text<?php if($form_makanan_id != ""){ ?> menu-readonly<?php } ?>"
                               placeholder="Ketik Nama Menu"
                               value="<?php echo htmlentities($form_makanan); ?>"
                               required
							   <?php if(!$is_open ){ ?> disabled<?php } ?>
							   <?php if($form_makanan_id != ""){ ?> readonly="readonly"<?php } ?>
                               />
                    	<input type="hidden" name="makanan_id" id="main-food-id" value="<?php echo htmlentities($form_makanan_id); ?>" />
                    	<input type="hidden" name="makanan_harga" class="price-add" id="main-food-price" value="<?php echo htmlentities($form_makanan_harga); ?>" />
                        <button class="<?php if(!($is_open && $form_makanan_id != "")){ ?>hide <?php } ?>menu-delete" id="main-menu-delete-button" onclick="return changeMainMenu()">Ubah</button>
                    </td>
                </tr>
                <tr title="Optional">
                	<td valign="top"><span style="color: #FF0000">*</span> Menu Tambahan:</td>
                    <td>
                    	<input type="text"
                               name="tambahan"
                               id="side-food-autocomplete"
                               class="text<?php if($form_tambahan_id != ""){ ?> menu-readonly<?php } ?>"
                               placeholder="Opsional"
                               value="<?php echo htmlentities($form_tambahan); ?>"
                               <?php if(!$is_open){ ?> disabled<?php } ?>
							   <?php if($form_tambahan_id != ""){ ?> readonly="readonly"<?php } ?>
                               />
                    	<input type="hidden" name="tambahan_id" id="side-food-id" value="<?php echo htmlentities($form_tambahan_id); ?>" />
                    	<input type="hidden" name="tambahan_harga" class="price-add" id="side-food-price" value="<?php echo htmlentities($form_tambahan_harga); ?>" />
                        <button class="<?php if(!($is_open && $form_tambahan_id != "")){ ?>hide <?php } ?>menu-delete" id="side-menu-delete-button" onclick="return changeSideMenu()">Ubah</button>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                    	<span style="color: #FF0000">*</span> Info Tambahan:
                    </td>
                    <td>
                    	<textarea name="pesan" id="order_text" placeholder="Contoh: &quot;Tidak pakai sayur! Jus Alpukat!&quot;" <?php if(!$is_open){ ?> disabled<?php } ?>><?php echo htmlentities($form_pesanan); ?></textarea>
                    </td>
                </tr>
                <tr>
                	<td></td>
                	<td>
                    	<span class="small-text-info" style="font-size: 11px;">
                        	<span style="color: #FF0000">*</span>
                            Opsional, boleh diisi boleh tidak.<br />
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
        <br />
        <h1>Pembayaran</h1>
        <table border="0">
            <tbody>
                <tr>
                	<td width="30%" valign="top">Est. Total (Rp):</td>
                    <td>
                    	<input type="text" name="total" id="price-total" class="numberformat menu-readonly" placeholder="500" value="<?php echo htmlentities($form_makanan_harga + $form_tambahan_harga); ?>" readonly="readonly"<?php if(!$is_open){ ?> disabled<?php } ?>/>
                    </td>
                </tr>
                <tr title="Optional">
                	<td valign="top"><span style="color: #FF0000">*</span> Donasi (Rp):</td>
                    <td>
                    	<input type="text" name="donasi" class="numberformat" placeholder="500" value="<?php if($form_pesandonasi >= 0) { echo htmlentities($form_pesandonasi); } ?>"<?php if(!$is_open){ ?> disabled<?php } ?>/>
                    </td>
                </tr>
                <tr>
                	<td></td>
                	<td>
                    	<span class="small-text-info">
                        	<span style="color: #FF0000">*</span>
                            Opsional, Donasi sepenuhnya akan diberikan untuk OB.<br />
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
        <br />
			<?php
                if($is_open) {
            ?>
            	<input type="submit" name="send" class="button full" value="Kirim" />
                <?php
                    if($is_already_order) {
                ?>
                <input type="hidden" name="ordered" value="1" />
                <input type="button" name="cancel" class="button cancel full" style="margin-top: 5px;" value="Batalkan Pesanan" onclick="return confirmBlocker(this, 'Yakin ingin membatalkan pesanan?');" />
                <?php
                    }
                ?>
            <?php
                }
            ?>
    </div>
</form>
<script src="<?php echo $config['full_domain']; ?>scripts/jquery.number.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$('.numberformat').number(true, 0, ',', '.');
	
	function confirmBlocker(obj, text) {
		// Tambahin Input
		form_action = obj.form.getAttribute('action');
		text =  "<span class=\"blocker-box-text\">"+text+"</span><br />";
		text += "<form action=\""+form_action+"\" method=\"POST\" style=\"margin-top: 15px; margin-bottom: 0;\" name=\"cancelform\"> ";
		text += "<input type=\"button\" name=\"cancel-cancel\" class=\"button cancel\" value=\"Tidak\" onclick=\"$('.blocker').remove()\" /> ";
		text += "<input type=\"submit\" name=\"cancel\" class=\"button\" value=\"Ya\" /> ";
		text += "</form>"
		
		var blocker = document.createElement("div");
		blocker.className = 'blocker';
		
		var box_text = document.createElement("div");
		box_text.className = 'blocker-box';
		box_text.innerHTML = text;
		blocker.appendChild(box_text);		
		
		document.body.appendChild(blocker);
		document.cancelform.cancel.focus();
	}
	
	function changeMainMenu() {
		$("#main-food-id").val("");
		$("#main-food-price").val("");
		$("#main-food-autocomplete").val("");
		$("#main-food-autocomplete").removeAttr("readonly");
		$("#main-food-autocomplete").removeClass("menu-readonly");
		$("#main-menu-delete-button").hide();
		return false;
	}
	
	function changeSideMenu() {
		$("#side-food-id").val("");
		$("#side-food-price").val("");
		$("#side-food-autocomplete").val("");
		$("#side-food-autocomplete").removeAttr("readonly");
		$("#side-food-autocomplete").removeClass("menu-readonly");
		$("#side-menu-delete-button").hide();
		return false;
	}
	
	if($('.success-text-block').length >= 0) {
		setTimeout("$('.success-text-block').css('opacity', '1')", 50);		
		setTimeout("$('.success-text-block').css('opacity', '0')", 4000);
		setTimeout("$('.success-text-block').remove()", 4510);
	}
	
	var menu_list_order = [<?php
			$menu_list_order_01 = array();
			
			foreach($global_menu as $menu_list_ord) {
				if($menu_list_ord['menu_status'] != 1) {
					continue;
				}
				
				$menu_list_ord_string = "{";
				$menu_list_ord_string .= "id:\"" . $menu_list_ord['menu_id'] . "\",";
				$menu_list_ord_string .= "label:\"" . ucwords(htmlentities($menu_list_ord['menu_nama'])) . "\",";
				$menu_list_ord_string .= "price:\"Rp. " . number_format($menu_list_ord['menu_harga'], 0, '', '.') . "\",";
				$menu_list_ord_string .= "price_unformatted: \"" . $menu_list_ord['menu_harga'] . "\",";
				$menu_list_ord_string .= "}";
				$menu_list_order_01[] = $menu_list_ord_string;
			}
			
			echo join(', ', $menu_list_order_01);
	?>];
	
	var menu_list_order_opt = [<?php
			$menu_list_order_01 = array();
			
			foreach($global_menu as $menu_list_ord) {
				if($menu_list_ord['menu_status'] != 1) {
					continue;
				}
				
				if(!in_array('tambahan', $menu_list_ord['tags'])) {
					continue;
				}
				
				$menu_list_ord_string = "{";
				$menu_list_ord_string .= "id:\"" . $menu_list_ord['menu_id'] . "\",";
				$menu_list_ord_string .= "label:\"" . ucwords(htmlentities($menu_list_ord['menu_nama'])) . "\",";
				$menu_list_ord_string .= "price:\"Rp. " . number_format($menu_list_ord['menu_harga'], 0, '', '.') . "\",";
				$menu_list_ord_string .= "price_unformatted: \"" . $menu_list_ord['menu_harga'] . "\",";
				$menu_list_ord_string .= "}";
				$menu_list_order_01[] = $menu_list_ord_string;
			}
			
			echo join(', ', $menu_list_order_01);
	?>];
	
	$("#main-food-autocomplete").autocomplete({
		minLength: 1,
		source: function(request, response) {
			var results = $.ui.autocomplete.filter(menu_list_order, request.term);
			
			response(results.slice(0, 3));
		},
		select: function( event, ui ) {
			$("#main-menu-delete-button").show();
			$("#main-food-autocomplete").addClass("menu-readonly");
			$("#main-food-autocomplete").attr("readonly","readonly");
			$("#main-food-autocomplete").val( ui.item.label );
			$("#main-food-id").val( ui.item.id );
			$("#main-food-price").val( ui.item.price_unformatted );
			count_total();
			return false;
		}
	})
	.autocomplete( "instance" )._renderItem = function( ul, item ) {
		return $( "<li>" )
		.append( "<a>" + item.label + "<br><span style=\"font-size: 12px; color: #454545; font-style: italic;\">" + item.price + "</span></a>" )
		.appendTo( ul );
	};
	
	$("#side-food-autocomplete").autocomplete({
		minLength: 1,
		source: function(request, response) {
			var results = $.ui.autocomplete.filter(menu_list_order_opt, request.term);
			
			response(results.slice(0, 3));
		},
		select: function( event, ui ) {
			$("#side-menu-delete-button").show();
			$("#side-food-autocomplete").attr("readonly","readonly");
			$("#side-food-autocomplete").addClass("menu-readonly");
			$("#side-food-autocomplete").val( ui.item.label );
			$("#side-food-id").val( ui.item.id );
			$("#side-food-price").val( ui.item.price_unformatted );
			count_total();
			return false;
		}
	})
	.autocomplete( "instance" )._renderItem = function( ul, item ) {
		return $( "<li>" )
		.append( "<a>" + item.label + "<br><span style=\"font-size: 12px; color: #454545; font-style: italic;\">" + item.price + "</span></a>" )
		.appendTo( ul );
	};
	
	function count_total() {
		total = 0;
		$(".price-add").each(function() {
			total += parseInt($(this).val());
		});
		
		$("#price-total").val(total);
	}
	
	function show_notice() {
		if($('#notice-timestamp').length <= 0) {
			return;
		}
		
		timestamp_cookie = readCookie('noticetimestamp');
		if(isNaN(timestamp_cookie)) {
			timestamp_cookie = 0;
		}
		
		timestamp_notice = $('#notice-timestamp').html();
		if(timestamp_cookie < timestamp_notice) {
			content_html = $("#notice-content").html();
			text = "<div class=\"blocker-box-text\">";
			text += "<div class=\"notice-popup-title\">PENGUMUMAN</div>";
			text += "<div class=\"notice-popup-content\">";
			text += content_html;
			text += "</div>";
			text += "</div><br />";
			text += "<form action=\"javascript:void(0);\" method=\"POST\" style=\"margin: 0;\" name=\"cancelform\"> ";
			text += "<input type=\"submit\" name=\"cancel\" class=\"button\" value=\"Tutup\" onclick=\"$('.blocker').remove()\" /> ";
			text += "</form>"
			
			var blocker = document.createElement("div");
			blocker.className = 'blocker';
			
			var box_text = document.createElement("div");
			box_text.className = 'blocker-box notice';
			box_text.innerHTML = text;
			blocker.appendChild(box_text);		
			
			document.body.appendChild(blocker);
			document.cancelform.cancel.focus();
			
			var d = new Date();
			d.setTime(d.getTime() + (365*24*60*60*1000));
			
			document.cookie="noticetimestamp=" + timestamp_notice + "; expires=" + d.toUTCString() + "; path=/";
		}
	}
	
	/* Thanks, Internet! */
	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
		}
		return null;
	}
	
	$(document).ready(function() {
		show_notice();
	});
</script>
<?php
	include "include/template-bottom.php";
?>