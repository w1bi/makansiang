<?php
	include "include/core-top.php";
?>
<?php
	if(isset($_SESSION['is_admin'])) {
		header("Location: ". $config['full_domain'] ."daftar-pesanan");
		die;
	}
	
	$admin_username = "";	
	$login_error = false;
	
	if(isset($_POST['admin_id'])) {
		$admin_username 	= strtolower(trim($_POST['admin_id']));
		$admin_pass			= $_POST['admin_password'];
		
		if($admin_username != "" && $admin_pass != "") {
			$admin_pass = md5('salt0198451$!@$@205hac' . $admin_pass . 'rq<>????$(@!*&!(@$');
			$sql_login = mysqli_query($mysql, "SELECT admin_id, admin_access FROM ms_admin WHERE admin_username = '". mysqli_real_escape_string($mysql, $admin_username) ."' AND admin_password = '". $admin_pass ."'");
			
			if($data = mysqli_fetch_array($sql_login)) {
				if($data[1] != '0') {
					$_SESSION['is_admin'] 			= $data[0];
					$_SESSION['admin_permission'] 	= $data[1];
				
					header("Location: ". $config['full_domain'] ."daftar-pesanan");
					die;
				}
				else {
					$login_error = true;
				}
			}
			else {
				$login_error = true;
			}
		} else {
			$login_error = true;
		}
	}
?>
<?php 
	include "include/template-top.php";
?>
<h1>Admin</h1>
<form enctype="application/x-www-form-urlencoded" method="post" action="<?php echo $config['full_domain']; ?>backend-login">
    <div id="table-container">
        <?php if($login_error) { ?>
			<div class="err-text">Username atau Password salah...</div>
		<?php } ?>
        <table cellpadding="5" cellspacing="5" border="0">
            <tbody>
                <tr>
                    <td width="30%" valign="top">ID:</td>
                    <td><input type="text" name="admin_id" value="<?php echo htmlentities($admin_username, ENT_QUOTES); ?>" autofocus /></td>
                </tr>
                <tr>
                	<td valign="top">Password:</td>
                    <td><input type="password" name="admin_password" value="" /></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><input type="submit" name="send" class="button" value="Login" /></td>
                </tr>
            </tbody>
        </table>
    </div>
</form>
<?php
	include "include/template-bottom.php";
?>