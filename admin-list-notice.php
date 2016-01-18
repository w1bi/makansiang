<?php
	$admin_only_page = 'c';
	include "include/core-top.php";
?>
<?php	
	$form_pengumuman = "";
	
	if(isset($_POST['send'])) {
		$form_pengumuman 	= trim($_POST['text']);
		mysqli_query($mysql, "
			UPDATE
				ms_pengumuman
			SET
				peng_admin_id = $admin_id, 
				peng_text = '" . mysqli_real_escape_string($mysql, $form_pengumuman) . "'
			WHERE
				peng_status = 1
		");
	} elseif(isset($_POST['cancel'])) {
		mysqli_query($mysql, "
			UPDATE
				ms_pengumuman
			SET
				peng_admin_id = $admin_id, 
				peng_text = '" . mysqli_real_escape_string($mysql, $form_pengumuman) . "'
			WHERE
				peng_status = 1
		");
	} else {
		$peng_query = mysqli_query($mysql, "
			SELECT peng_text
			FROM ms_pengumuman 
			WHERE peng_status = 1
		");
		
		if($data = mysqli_fetch_array($peng_query)) {
			$form_pengumuman = $data[0];
		}
	}
?>
<?php 
	include "include/template-top.php";
?>

<h1>Pengumuman</h1>
<div id="table-container">
<form enctype="application/x-www-form-urlencoded" method="post" action="<?php echo $config['full_domain']; ?>daftar-pengumuman">
	<textarea name="text"><?php echo htmlentities($form_pengumuman, ENT_QUOTES); ?></textarea>
    <br /><br />
	<div class="text-center">
    	<input type="button" name="cancel" class="button cancel" value="Hapus" onclick="document.form_notice_cancel.submit()" />
    	<input type="submit" name="send" class="button" value="Simpan" />
    </div>
</form>
<form enctype="application/x-www-form-urlencoded" method="post" action="<?php echo $config['full_domain']; ?>daftar-pengumuman" name="form_notice_cancel">
	<input type="hidden" value="1" name="cancel" />
</form>
</div>

<link rel="stylesheet" href="<?php echo $config['full_domain']; ?>scripts/sceditor/themes/square.min.css" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo $config['full_domain']; ?>scripts/sceditor/jquery.sceditor.bbcode.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("textarea").sceditor({
			plugins: "bbcode",
			style: "<?php echo $config['full_domain']; ?>scripts/sceditor/jquery.sceditor.default.min.css",
			toolbar: "bold,italic,underline,strike|subscript,superscript",
			resizeEnabled: false,
			height: "300px",
			autofocus: true,
		});
	});
</script>
<?php
	include "include/template-bottom.php";
?>