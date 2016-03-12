<?php
	$admin_only_page = 'd';
	include "include/core-top.php";
?>
<?php	
	if(isset($_POST['send'])) {
		$continue				= true;
		$form_username			= isset($_POST['username']) ? strtolower(trim($_POST['username'])) : '????';
		$form_name				= trim($_POST['nama']);
		$form_password			= $_POST['userpass'];
		$form_password2			= $_POST['user2pass'];
		$form_password_admin	= $_POST['password'];
		$form_access			= (isset($_POST['access'])) ? $_POST['access'] : 'a';
		
		if(is_array($form_access)) {
			$form_access = 'a' . implode($form_access);
		}
		
		$form_admin_id	= trim($_POST['adminid']);
		$is_insert		= true;
		
		if($form_admin_id != "") {
			if(ctype_digit($form_admin_id)) {
				$is_insert = false;
			}
			else {
				$continue = false;
			}
		}
		
		if($continue && $is_insert && ($form_password == "" || $form_password2 != $form_password)) {
			$continue = false;
		}
		
		if($continue && !$is_insert && $form_password2 != $form_password) {
			$continue = false;
		}
		
		if($continue && ($form_username == "" || $form_name == "" || $form_password_admin == "")) {
			$continue = false;
		}
		
		if($continue) {
			$admin_pass 	= md5('salt0198451$!@$@205hac' . $form_password_admin . 'rq<>????$(@!*&!(@$');
			$sql_login		= mysqli_query($mysql, "SELECT * FROM ms_admin WHERE admin_id = '". mysqli_real_escape_string($mysql, $admin_id) ."' AND admin_password = '". $admin_pass ."'");
			
			if(mysqli_num_rows($sql_login) != 1) {
				$continue = false;
			}
		}
		
		if($continue) {
			$admin_pass	= md5('salt0198451$!@$@205hac' . $form_password . 'rq<>????$(@!*&!(@$');
			$insert_update_query = "";
			
			if($is_insert) {
				$insert_update_query = "
					INSERT INTO ms_admin
						(admin_username, admin_password, admin_name, admin_access)
					VALUES
						('" . mysqli_real_escape_string($mysql, $form_username) ."',
						'" . mysqli_real_escape_string($mysql, $admin_pass) ."',
						'" . mysqli_real_escape_string($mysql, $form_name) ."',
						'" . mysqli_real_escape_string($mysql, $form_access) ."'
						)
					ON DUPLICATE KEY UPDATE
						admin_name = '" . mysqli_real_escape_string($mysql, $form_name) ."',
						admin_password = '" . mysqli_real_escape_string($mysql, $admin_pass) ."',
						admin_access = '" . mysqli_real_escape_string($mysql, $form_access) ."'
				";
			}
			else {
				$edit_password_too = "";
				if($form_password != "") {
					$edit_password_too = "
						admin_password = '" . mysqli_real_escape_string($mysql, $admin_pass) ."',
					";
				}
				
				$insert_update_query = "
					UPDATE
						ms_admin
					SET
						$edit_password_too
						admin_name = '" . mysqli_real_escape_string($mysql, $form_name) ."',
						admin_access = '" . mysqli_real_escape_string($mysql, $form_access) ."'
					WHERE
						admin_id = $form_admin_id
				";
			}
			
			mysqli_query($mysql, $insert_update_query);
			
			header("Location: ". $config['full_domain'] ."daftar-admin");
			die;
		}
	}
	
	if(isset($_POST['delete'])) {
		$continue			= true;
		$form_admin_id		= trim($_POST['adminid']);
		
		if($form_admin_id == "" || !ctype_digit($form_admin_id)) {
			$continue = false;
		}
		
		if($continue) {
			$insert_update_query = "
				UPDATE
					ms_admin
				SET
					admin_access = '0'
				WHERE
					admin_id = $form_admin_id
			";
				
			mysqli_query($mysql, $insert_update_query);
			
			header("Location: ". $config['full_domain'] ."daftar-admin");
			die;
		}
	}
?>
<?php
	include "include/template-top.php";
?>
<div class="hide form-admin-all" id="tambah-admin">
	<h1>Tambah / Edit Admin <a class="float-right tutup-admin" href="javascript:void(0)">[x] Tutup</a></h1>
    <form name="form_add_admin" id="form_add_admin"
          enctype="application/x-www-form-urlencoded"
          method="post"
          action="<?php echo $config['full_domain']; ?>daftar-admin"
          onsubmit="return checkFormFirst(this);">
    	<div class="err-text hide">Harap isi dengan lengkap</div>
        <div id="table-container">
            <table border="0">
                <tbody>
                    <tr>
                        <td width="30%" valign="top">Username:</td>
                        <td>
                            <input type="text" name="username" maxlength="20" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Nama Lengkap:</td>
                        <td>
                            <input type="text" name="nama" maxlength="60" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Password User:</td>
                        <td>
                            <input type="password" maxlength="50" name="userpass" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Ulangi Password:</td>
                        <td>
                            <input type="password" maxlength="25" name="user2pass" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Password Kamu:</td>
                        <td>
                            <input type="password" maxlength="25" name="password" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Akses:</td>
                        <td>
                            <label><input type="checkbox" name="access[]" value="a" checked="checked" disabled /> Halaman Pesanan</label><br />
                            <label><input type="checkbox" name="access[]" value="b" /> Halaman Menu</label><br />
                            <label><input type="checkbox" name="access[]" value="c" /> Halaman Pengumuman</label><br />
                            <label><input type="checkbox" name="access[]" value="d" /> Halaman Daftar Admin</label><br />
                            <label><input type="checkbox" name="access[]" value="e" /> Halaman Pengaturan Jam</label><br />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" name="send" class="button" value="Tambah / Edit Admin" />
                            <input type="hidden" name="adminid" value="" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>

<div class="hide form-admin-all" id="hapus-admin">
	<h1>Yakin Hapus admin? <a class="float-right tutup-admin" href="javascript:void(0)">[x] Tutup</a></h1>
    <form name="form_hapus_admin" id="form_hapus_admin"
          enctype="application/x-www-form-urlencoded"
          method="post"
          action="<?php echo $config['full_domain']; ?>daftar-admin">
        <div id="table-container">
            <table border="0">
                <tbody>
                    <tr>
                        <td width="30%" valign="top">Username:</td>
                        <td>
                            <input type="text" name="username" maxlength="20" value="" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Nama Lengkap:</td>
                        <td>
                            <input type="text" name="nama" maxlength="60" value="" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" name="delete" class="button" value="Hapus" />
                            <input type="reset" name="reset" class="button tutup-admin" value="Batal" />
                            <input type="hidden" maxlength="15" class="hapus-admin-id" name="adminid" value="" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>

<h1>Daftar Admin</h1>
<div id="table-container">
    <?php
        $admin_list			= mysqli_query($mysql, "SELECT * FROM ms_admin WHERE admin_access != '0'");
        $count_all_admin	= mysqli_num_rows($admin_list);
    ?>
    
    <a class="button-admin float-right" id="tambah-button" href="javascript:void(0);" style="vertical-align: middle;">Tambah</a>
    <div class="saldo-total-admin" style="vertical-align: middle">
        Total Admin :
        <strong><?php echo number_format($count_all_admin, 0, '', '.'); ?> User</strong>
    </div>
    <div class="clear"></div>
    <input type="text" class="find-admin" placeholder="Cari admin..." />
    <table border="0" id="sortabletable" class="tablesorter">
        <thead>
            <th width="30%">ID</th>
            <th>Nama</th>
        </thead>
        <tbody class="admin-finder">
            <?php                    
                while($list_admin = mysqli_fetch_array($admin_list)) {
					$list_admin_id			= $list_admin['admin_id'];
                    $list_admin_nama		= htmlentities($list_admin['admin_name'], ENT_QUOTES);
                    $list_admin_user		= htmlentities($list_admin['admin_username'], ENT_QUOTES);
            ?>
            <tr>
                <td>
                    <span class="admin-cut-finder"><?php echo $list_admin_user; ?></span>
                    
                    <div class="table-sort-data hide"><?php echo $list_admin_user; ?></div>
                </td>
                <td>
                    <?php echo $list_admin_nama; ?>
                    
                    <div class="table-sort-data hide"><?php echo $list_admin_nama; ?></div>
                    
                    <div class="small-text-info float-right">
                    
						<?php
                            if($list_admin_id == $admin_id) {
								echo "Kamu!";
							}
							else {
                        ?>
                        <a href="javascript:void(0);" class="ubah-admin">[ Ubah ]</a>
                        <a href="javascript:void(0);" class="hapus-admin">[ Hapus ]</a>
                        
                        <span class="hide this_admin_id"><?php echo $list_admin_id; ?></span>
                        <span class="hide this_admin_name"><?php echo $list_admin_nama; ?></span>
                        <span class="hide this_admin_user"><?php echo $list_admin_user; ?></span>
                        
						<?php
                            }
                        ?>
                        
                    </div>
                </td>
            </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>
<script src="<?php echo $config['full_domain']; ?>scripts/jquery.tablesorter.min.js" type="text/javascript"></script>
<script type="text/javascript">
	
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
	
	$('.ubah-admin').on('click', function() {
		$('.form-admin-all').hide();
		$('#form_add_admin')[0].reset();
		$('#form_add_admin input[name=adminid]').val($(this).parent().find('.this_admin_id').text());
		$('#form_add_admin input[name=nama]').val($(this).parent().find('.this_admin_name').text());
		$('#form_add_admin input[name=username]').val($(this).parent().find('.this_admin_user').text());
		$('#form_add_admin input[name=username]').attr('disabled','disabled');
		$('#tambah-admin').show();
		$('#form_add_admin input[name=nama]').focus();
	});
	
	$('.hapus-admin').on('click', function() {
		$('.form-admin-all').hide();
		$('#form_hapus_admin input[name=adminid]').val($(this).parent().find('.this_admin_id').text());
		$('#form_hapus_admin input[name=nama]').val($(this).parent().find('.this_admin_name').text());
		$('#form_hapus_admin input[name=username]').val($(this).parent().find('.this_admin_user').text());
		$('#hapus-admin').show();
		$('#form_hapus_admin input[name=delete]').focus();
	});
	
	$('#tambah-button').on('click', function() {
		$('.form-admin-all').hide();
		$('#form_add_admin')[0].reset();
		$('#form_add_admin input[name=username]').removeAttr('disabled');
		$('#tambah-admin').show();
		$('#form_add_admin input[name=username]').focus();
	});
	
	$('.tutup-admin').on('click', function() {
		$('.form-admin-all').hide();
	});

	$('.find-admin').on('keyup', function() {
		var findString = new RegExp($(this).val(), 'i'); 
		
		$('.admin-finder tr').hide();
		$('.admin-finder tr').filter(function(){ return $(this).find('.admin-cut-finder').text().match(findString) }).show();
	});
	
	function checkFormFirst(obj) {
		var admin_username	= obj.username.value;
		var admin_nama		= obj.nama.value;
		var admin_userpass	= obj.userpass.value;
		var admin_userpass2	= obj.user2pass.value;
		var admin_password	= obj.password.value;
		var admin_adminid	= obj.adminid.value;
		
		if(admin_username == "" || admin_nama == "" || admin_password == "") {
			alert("Isi dengan lengkap");
			return false;
		}
		
		if(admin_adminid == "" && (admin_userpass == "" || admin_user2pass == "")) {
			alert("Isi dengan lengkap");
			return false;
		}
		
		if(/[^a-zA-Z0-9._-]/.test(admin_username)) {
			alert('Karakter yang diizinkan untuk username: abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789._-');
			return false;
		}
		
		if(admin_userpass != admin_userpass2) {
			alert("Password tidak sama!");
			return false;
		}
		
	}
	
</script>
<?php
	include "include/template-bottom.php";
?>