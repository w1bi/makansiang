<?php
	$admin_only_page = 'a';
	include "include/core-top.php";
?>
<?php
	if(isset($_POST['send'])) {
		$continue				= true;
		$form_name				= trim($_POST['nama']);
		$form_password			= $_POST['userpass'];
		$form_password2			= $_POST['user2pass'];
		$form_password_admin	= $_POST['password'];
		
		if($form_name == "" || $form_password_admin == "") {
			$_SESSION['error_form'] = "Harap isi dengan lengkap";
			$continue = false;
		}
		
		if($continue && $form_password2 != $form_password) {
			$_SESSION['error_form'] = "Ulangi Password tidak sama!";
			$continue = false;
		}
		
		if($continue) {
			$admin_pass 	= md5('salt0198451$!@$@205hac' . $form_password_admin . 'rq<>????$(@!*&!(@$');
			$sql_login		= mysqli_query($mysql, "SELECT * FROM ms_admin WHERE admin_id = '". mysqli_real_escape_string($mysql, $admin_id) ."' AND admin_password = '". $admin_pass ."'");
			
			if(mysqli_num_rows($sql_login) != 1) {
				$_SESSION['error_form'] = "Password kamu salah!";
				$continue = false;
			}
		}
		
		if($continue) {
			$insert_update_query = "";
			
			$edit_password_too = "";
			if($form_password != "") {
				$admin_pass	= md5('salt0198451$!@$@205hac' . $form_password . 'rq<>????$(@!*&!(@$');
				
				$edit_password_too = "
					admin_password = '" . mysqli_real_escape_string($mysql, $admin_pass) ."',
				";
			}
			
			$insert_update_query = "
				UPDATE
					ms_admin
				SET
					$edit_password_too
					admin_name = '" . mysqli_real_escape_string($mysql, $form_name) ."'
				WHERE
					admin_id = $admin_id
			";
			
			mysqli_query($mysql, $insert_update_query);
			
			$_SESSION['success_form'] = "Berhasil Mengubah Profil!";
			header("Location: ". $config['full_domain'] ."profil-admin");
			die;
		}
	}
?>
<?php
	include "include/template-top.php";
?>
<div class="form-admin-all" id="tambah-admin">
	<h1>Profil</h1>
    <br />
    <form name="form_add_admin" id="form_add_admin"
          enctype="application/x-www-form-urlencoded"
          method="post"
          action="<?php echo $config['full_domain']; ?>profil-admin">
          
        <?php
			if(isset($_SESSION['error_form'])) {
		?>
    	<div class="err-text"><?php echo htmlentities($_SESSION['error_form'], ENT_QUOTES); ?></div><br />
        <?php
				unset($_SESSION['error_form']);
			}
		?>
        
        <?php
			if(isset($_SESSION['success_form'])) {
		?>
    	<div class="success-text"><?php echo htmlentities($_SESSION['success_form'], ENT_QUOTES); ?></div><br />
        <?php
				unset($_SESSION['success_form']);
			}
		?>
        
        <?php
			$admin_list			= mysqli_query($mysql, "SELECT * FROM ms_admin WHERE admin_id = $admin_id");
			if($list_admin = mysqli_fetch_array($admin_list)) {
                    $list_admin_nama		= htmlentities($list_admin['admin_name'], ENT_QUOTES);
                    $list_admin_user		= htmlentities($list_admin['admin_username'], ENT_QUOTES);
		?>
        <div id="table-container">
            <table border="0">
                <tbody>
                    <tr>
                        <td width="30%" valign="top">Username:</td>
                        <td>
                            <input type="text" name="username" maxlength="20" value="<?php echo $list_admin_user; ?>" disabled="disabled" />
                            <br />
                            <span class="small-text-info">&nbsp;</span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Nama Lengkap:</td>
                        <td>
                            <input type="text" name="nama" maxlength="60" value="<?php echo $list_admin_nama; ?>" />
                            <br />
                            <span class="small-text-info">&nbsp;</span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Password User:</td>
                        <td>
                            <input type="password" maxlength="50" name="userpass" value="" />
                            <br />
                            <span class="small-text-info">Kosongkan jika tidak ingin mengubah password</span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Ulangi Password:</td>
                        <td>
                            <input type="password" maxlength="25" name="user2pass" value="" />
                            <span class="small-text-info">Kosongkan jika tidak ingin mengubah password</span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">Password Kamu:</td>
                        <td>
                            <input type="password" maxlength="25" name="password" value="" />
                            <br />
                            <span class="small-text-info">Masukan password sekarang</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="submit" name="send" class="button" value="Ubah Profil" />
                            <br />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
			}
		?>
    </form>
</div>
<?php
	include "include/template-bottom.php";
?>