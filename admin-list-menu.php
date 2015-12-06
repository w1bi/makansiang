<?php
	$admin_only_page = 'b';
	include "include/core-top.php";
	
	// Template
	$config["template_full"] = true;
?>
<?php	
	if(isset($_POST['send'])) {
		$continue			= true;
		$form_menu			= trim($_POST['menu']);
		$form_tags			= trim($_POST['tags']);
		$form_harga			= preg_replace("/\.|\,/i", "", trim($_POST['harga']));
		$form_menu_id		= (isset($_POST['menuid'])) ? trim($_POST['menuid']) : '0';
		
		if($form_menu == "" || $form_tags == "" || $form_harga == "" || !ctype_digit($form_harga) || $form_menu_id == "" || !ctype_digit($form_menu_id)) {
			$continue = false;
		}
		
		if($continue) {
			$insert_update_query = "";
			
			if($form_menu_id > 0) {
				$insert_update_query = "
					UPDATE
						ms_menu
					SET
						menu_nama = '" . mysqli_real_escape_string($mysql, $form_menu) ."',
						menu_tag = '" . mysqli_real_escape_string($mysql, $form_tags) ."',
						menu_harga = $form_harga
					WHERE
						menu_id = $form_menu_id
				";
			}
			else {
				$insert_update_query = "
					INSERT INTO ms_menu
						(menu_nama, menu_tag, menu_harga)
					VALUES
						('" . mysqli_real_escape_string($mysql, $form_menu) ."', '" . mysqli_real_escape_string($mysql, $form_tags) ."', $form_harga)
				";
			}
				
			mysqli_query($mysql, $insert_update_query);
			
			header("Location: " . $config['full_domain'] . "daftar-menu");
			die();
		}
	}
	
	if(isset($_POST['delete'])) {
		$continue			= true;
		$form_menu_id		= trim($_POST['menuid']);
		
		if($form_menu_id == "" || !ctype_digit($form_menu_id)) {
			$continue = false;
		}
		
		if($continue) {
			$insert_update_query = "
				UPDATE
					ms_menu
				SET
					menu_status = 0
				WHERE
					menu_id = $form_menu_id
			";
				
			mysqli_query($mysql, $insert_update_query);
			
			header("Location: " . $config['full_domain'] . "daftar-menu");
			die();
		}
	}
	
	if(isset($_POST['activate'])) {
		$continue			= true;
		$form_menu_id		= trim($_POST['menuid']);
		
		if($form_menu_id == "" || !ctype_digit($form_menu_id)) {
			$continue = false;
		}
		
		if($continue) {
			$insert_update_query = "
				UPDATE
					ms_menu
				SET
					menu_status = 1
				WHERE
					menu_id = $form_menu_id
			";
				
			mysqli_query($mysql, $insert_update_query);
			
			header("Location: " . $config['full_domain'] . "daftar-menu");
			die();
		}
	}
	
	if(isset($_POST['deactivate'])) {
		$continue			= true;
		$form_menu_id		= trim($_POST['menuid']);
		
		if($form_menu_id == "" || !ctype_digit($form_menu_id)) {
			$continue = false;
		}
		
		if($continue) {
			$insert_update_query = "
				UPDATE
					ms_menu
				SET
					menu_status = 2
				WHERE
					menu_id = $form_menu_id
			";
				
			mysqli_query($mysql, $insert_update_query);
			
			header("Location: " . $config['full_domain'] . "daftar-menu");
			die();
		}
	}
?>
<?php
	include "include/template-top.php";
?>
<div class="hide form-menu-all" id="tambah-menu">
	<h1>Tambah Menu <a class="float-right tutup-menu" href="javascript:void(0)">[x] Tutup</a></h1>
    <form name="form_add_menu" id="form_add_menu"
          enctype="application/x-www-form-urlencoded"
          method="post"
          action="<?php echo $config['full_domain']; ?>daftar-menu"
          onsubmit="return checkMenuFirst(this);">
    	<div class="err-text hide">Harap isi dengan lengkap</div>
        <div id="table-container">
            <table border="0">
                <tbody>
                    <tr>
                        <td width="30%" valign="top">Nama Menu:</td>
                        <td>
                            <input type="text" name="menu" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Rp:</td>
                        <td>
                            <input type="text" class="numberformat" maxlength="15" name="harga" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Tag:</td>
                        <td>
                            <input type="text" name="tags" value="" />
                            <br />
                            <span class="small-text-info">
                            	* Dipisahkan dengan koma<br />
                                ** Contoh: <em>Tambahan, Kantin Belakang</em><br />
                                *** Untuk menu tambahan, seperti minuman, tambahkan tag: <em>Tambahan</em><br />
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" name="send" class="button" value="Tambah Menu" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>
<div class="hide form-menu-all" id="ubah-menu">
	<h1>Ubah Menu <a class="float-right tutup-menu" href="javascript:void(0)">[x] Tutup</a></h1>
    <form name="form_edit_menu" id="form_edit_menu"
          enctype="application/x-www-form-urlencoded"
          method="post"
          action="<?php echo $config['full_domain']; ?>daftar-menu"
          onsubmit="return checkMenuFirst(this);">
    	<div class="err-text hide">Harap isi dengan lengkap</div>
        <div id="table-container">
            <table border="0">
                <tbody>
                    <tr>
                        <td width="30%" valign="top">Nama Menu:</td>
                        <td>
                            <input type="text" name="menu" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Rp:</td>
                        <td>
                            <input type="text" class="numberformat" maxlength="15" name="harga" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Tag:</td>
                        <td>
                            <input type="text" name="tags" value="" />
                            <br />
                            <span class="small-text-info">
                            	* Dipisahkan dengan koma<br />
                                ** Contoh: <em>Tambahan, Kantin Belakang</em><br />
                                *** Untuk menu tambahan, seperti minuman, tambahkan tag: <em>Tambahan</em><br />
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" name="send" class="button" value="Ubah Menu" />
                            <input type="reset" name="reset" class="button tutup-menu" value="Batal" />
                            <input type="hidden" maxlength="15" class="edit-menu-id" name="menuid" value="" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>
<div class="hide form-menu-all" id="hapus-menu">
	<h1>Yakin Hapus Menu? <a class="float-right tutup-menu" href="javascript:void(0)">[x] Tutup</a></h1>
    <form name="form_hapus_menu" id="form_hapus_menu"
          enctype="application/x-www-form-urlencoded"
          method="post"
          action="<?php echo $config['full_domain']; ?>daftar-menu">
        <div id="table-container">
            <table border="0">
                <tbody>
                    <tr>
                        <td width="30%" valign="top">Nama Menu:</td>
                        <td>
                            <input type="text" name="menu" value="" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Rp:</td>
                        <td>
                            <input type="text" class="numberformat" maxlength="15" name="harga" value="" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Tag:</td>
                        <td>
                            <input type="text" name="tags" value="" />
                            <br />
                            <span class="small-text-info" style="font-size: 11px;">
                            	(Dipisahkan dengan koma)
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" name="delete" class="button" value="Hapus" />
                            <input type="reset" name="reset" class="button tutup-menu" value="Batal" />
                            <input type="hidden" maxlength="15" class="hapus-menu-id" name="menuid" value="" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>
<div class="hide form-menu-all" id="aktifkan-menu">
	<h1>Yakin Aktifkan Menu? <a class="float-right tutup-menu" href="javascript:void(0)">[x] Tutup</a></h1>
    <form name="form_aktifkan_menu" id="form_aktifkan_menu"
          enctype="application/x-www-form-urlencoded"
          method="post"
          action="<?php echo $config['full_domain']; ?>daftar-menu">
        <div id="table-container">
            <table border="0">
                <tbody>
                    <tr>
                        <td width="30%" valign="top">Nama Menu:</td>
                        <td>
                            <input type="text" name="menu" value="" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Rp:</td>
                        <td>
                            <input type="text" class="numberformat" maxlength="15" name="harga" value="" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Tag:</td>
                        <td>
                            <input type="text" name="tags" value="" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" name="activate" class="button" value="Aktifkan" />
                            <input type="reset" name="reset" class="button tutup-menu" value="Batal" />
                            <input type="hidden" maxlength="15" class="hapus-menu-id" name="menuid" value="" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>
<div class="hide form-menu-all" id="deaktifkan-menu">
	<h1>Yakin De-Aktifkan Menu? <a class="float-right tutup-menu" href="javascript:void(0)">[x] Tutup</a></h1>
    <form name="form_deaktifkan_menu" id="form_deaktifkan_menu"
          enctype="application/x-www-form-urlencoded"
          method="post"
          action="<?php echo $config['full_domain']; ?>daftar-menu">
        <div id="table-container">
            <table border="0">
                <tbody>
                    <tr>
                        <td width="30%" valign="top">Nama Menu:</td>
                        <td>
                            <input type="text" name="menu" value="" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Rp:</td>
                        <td>
                            <input type="text" class="numberformat" maxlength="15" name="harga" value="" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Tag:</td>
                        <td>
                            <input type="text" name="tags" value="" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" name="deactivate" class="button" value="De-Aktifkan" />
                            <input type="reset" name="reset" class="button tutup-menu" value="Batal" />
                            <input type="hidden" maxlength="15" class="hapus-menu-id" name="menuid" value="" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>
<h1>Daftar Menu</h1>
<div id="table-container">
    <?php
        $all_menu_sql	= mysqli_query($mysql, "SELECT * FROM ms_menu WHERE menu_status IN (1,2)");
        $count_all_menu	= mysqli_num_rows($all_menu_sql);
    ?>
    
    <a class="button-admin float-right" id="tambah-menu-button" href="javascript:void(0);" style="vertical-align: middle;">Tambah</a>
    <div class="saldo-total-admin" style="vertical-align: middle">
        Total :
        <strong><?php echo number_format($count_all_menu, 0, '', '.'); ?> Menu</strong>
    </div>
    <div class="clear"></div>
    <input type="text" class="find-menu-admin" placeholder="Cari menu..." />
    <table border="0" id="sortabletable" class="tablesorter">
        <thead>
            <th width="70%">Menu</th>
            <th>Rp</th>
        </thead>
        <tbody class="menu-finder">
            <?php                    
                foreach($global_menu as $list_menu) {
					if($list_menu['menu_status'] == 0) {
						continue;
					}
					$menu_id		= $list_menu['menu_id'];
                    $menu_nama		= htmlentities($list_menu['menu_nama'], ENT_QUOTES);
                    $menu_harga		= $list_menu['menu_harga'];
					$menu_status	= $list_menu['menu_status'];
					$menu_tag		= $list_menu['menu_tag'];
            ?>
            <tr>
                <td>
                    <span class="menu-cut-finder"><?php echo $menu_nama; ?></span>
                    <div class="small-text-info float-right">
                        <a href="javascript:void(0);" class="hapus-menu">[ Hapus ]</a>
                        <?php if($menu_status == 1) { ?>
                        <a href="javascript:void(0);" class="deaktifkan-menu">[ De-Aktifkan ]</a>
                        <?php } else { ?>
                        <a href="javascript:void(0);" class="aktifkan-menu">[ Aktifkan ]</a>
                        <?php } ?>
                        <a href="javascript:void(0);" class="ubah-menu">[ Ubah ]</a>

                        
                        <span class="hide this_menu_id"><?php echo $menu_id; ?></span>
                        <span class="hide this_menu_name"><?php echo $menu_nama; ?></span>
                        <span class="hide this_menu_price"><?php echo $menu_harga; ?></span>
                        <span class="hide this_menu_tags"><?php echo $menu_tag; ?></span>
                        
                    </div>
                    
                    <div class="table-sort-data hide"><?php echo $menu_nama; ?></div>
                </td>
                <td align="right">
                    <?php echo number_format($menu_harga, 0, '', '.'); ?>
                    
                    <div class="table-sort-data hide"><?php echo $menu_harga; ?></div>
                </td>
            </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>
<script src="<?php echo $config['full_domain']; ?>scripts/jquery.number.min.js" type="text/javascript"></script>
<script src="<?php echo $config['full_domain']; ?>scripts/jquery.tablesorter.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$('.numberformat').number(true, 0, ',', '.');
	
	if($("#sortabletable").find("tbody").find("tr").size() > 0) {
		$("#sortabletable").tablesorter({
			sortList: [
						[0,0]
					  ],
			textExtraction: function(node) { 
				return node.getElementsByClassName("table-sort-data")[0].innerHTML; 
			} 
		}); 
	}
	else {
		$("#sortabletable").tablesorter()
	}
	
	$('.ubah-menu').on('click', function() {
		$('.form-menu-all').hide();
		$('#form_edit_menu input[name=menuid]').val($(this).parent().find('.this_menu_id').text());
		$('#form_edit_menu input[name=menu]').val($(this).parent().find('.this_menu_name').text());
		$('#form_edit_menu input[name=harga]').val($(this).parent().find('.this_menu_price').text());
		$('#form_edit_menu input[name=tags]').val($(this).parent().find('.this_menu_tags').text());
		$('#ubah-menu').show();
		$('#form_edit_menu input[name=menu]').focus();
	});
	
	$('.hapus-menu').on('click', function() {
		$('.form-menu-all').hide();
		$('#form_hapus_menu input[name=menuid]').val($(this).parent().find('.this_menu_id').text());
		$('#form_hapus_menu input[name=menu]').val($(this).parent().find('.this_menu_name').text());
		$('#form_hapus_menu input[name=harga]').val($(this).parent().find('.this_menu_price').text());
		$('#form_hapus_menu input[name=tags]').val($(this).parent().find('.this_menu_tags').text());
		$('#hapus-menu').show();
		$('#form_hapus_menu input[name=delete]').focus();
	});
	
	$('.aktifkan-menu').on('click', function() {
		$('.form-menu-all').hide();
		$('#form_aktifkan_menu input[name=menuid]').val($(this).parent().find('.this_menu_id').text());
		$('#form_aktifkan_menu input[name=menu]').val($(this).parent().find('.this_menu_name').text());
		$('#form_aktifkan_menu input[name=harga]').val($(this).parent().find('.this_menu_price').text());
		$('#form_aktifkan_menu input[name=tags]').val($(this).parent().find('.this_menu_tags').text());
		$('#aktifkan-menu').show();
		$('#form_aktifkan_menu input[name=delete]').focus();
	});
	
	$('.deaktifkan-menu').on('click', function() {
		$('.form-menu-all').hide();
		$('#form_deaktifkan_menu input[name=menuid]').val($(this).parent().find('.this_menu_id').text());
		$('#form_deaktifkan_menu input[name=menu]').val($(this).parent().find('.this_menu_name').text());
		$('#form_deaktifkan_menu input[name=harga]').val($(this).parent().find('.this_menu_price').text());
		$('#form_deaktifkan_menu input[name=tags]').val($(this).parent().find('.this_menu_tags').text());
		$('#deaktifkan-menu').show();
		$('#form_deaktifkan_menu input[name=delete]').focus();
	});
	
	$('#tambah-menu-button').on('click', function() {
		$('.form-menu-all').hide();
		$('#tambah-menu').show();
		$('#form_add_menu input[name=menu]').focus();
	});
	
	$('.tutup-menu').on('click', function() {
		$('.form-menu-all').hide();
	});

	$('.find-menu-admin').on('keyup', function() {
		var findString = new RegExp($(this).val(), 'i'); 
		
		$('.menu-finder tr').hide();
		$('.menu-finder tr').filter(function(){ return $(this).find('.menu-cut-finder').text().match(findString) }).show();
	});
	
	function checkMenuFirst(obj) {
		var menu_name 	= obj.menu.value;
		var menu_price 	= obj.harga.value;
		
		if(menu_name == "" || menu_price == "") {
			$('.err-text').show();
			setTimeout("$('.err-text').hide()", 5000);
			return false;
		}
	}
	
</script>
<?php
	include "include/template-bottom.php";
?>