<?php
	$admin_only_page = 'e';
	include "include/core-top.php";
?>
<?php
	if(isset($_POST['send'])) {
		for($i = 0; $i < 7; $i++) {
			$get_hour	= isset($_POST['hour_'.$i]) ? $_POST['hour_'.$i] : 0;
			$get_minute	= isset($_POST['minute_'.$i]) ? $_POST['minute_'.$i] : 0;
			
			if(!ctype_digit($get_hour) || $get_hour >= 24) {
				$get_hour = 0;
				$get_minute = 0;
			}
			
			if(!ctype_digit($get_minute) || $get_minute >= 60) {
				$get_hour = 0;
				$get_minute = 0;
			}
			
			if($get_hour < 10) {
				$get_hour = "0" . intval($get_hour);
			}
			
			if($get_minute < 10) {
				$get_minute = "0" . intval($get_minute);
			}
			
			mysqli_query($mysql, "UPDATE ms_batas_jam SET hari_jam = '$get_hour$get_minute' WHERE hari_id = $i");
		}
	}
?>
<?php
	include "include/template-top.php";
?>
<?php
	$day_date = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
	$get_time_date_query = mysqli_query($mysql, "SELECT hari_id, hari_jam FROM ms_batas_jam");
	$get_time_date = array();
	while($list_time_date = mysqli_fetch_array($get_time_date_query)) {
		$get_time_date[$list_time_date["hari_id"]]["enabled"] = ($list_time_date["hari_jam"] == "0000") ? false : true;
		$get_time_date[$list_time_date["hari_id"]]["hour"] = substr($list_time_date["hari_jam"], 0, 2);
		$get_time_date[$list_time_date["hari_id"]]["minute"] = substr($list_time_date["hari_jam"], 2, 2);
	}
?>
<div class="form-admin-all" id="tambah-admin">
	<h1>Pengaturan Jam Pesanan</h1>
	<form name="form_setting_time" id="form_setting_time"
		  enctype="application/x-www-form-urlencoded"
		  method="post"
		  action="<?php echo $config['full_domain']; ?>pengaturan-jam">
		  
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
					<?php for($i = 0; $i < 7; $i++) { ?>
					<tr>
						<td width="150" valign="top">
							<input type="checkbox"
								name="enable_<?php echo $i; ?>"
								id="enable_<?php echo $i; ?>"
								class="enable_button_day"
								value="enable"
								<?php if($get_time_date[$i]["enabled"]) { ?>checked="checked" <?php } ?>/>	
								&nbsp;
							<?php echo $day_date[$i]; ?> :
						</td>
						<td>
							<input type="number" max="23" min="0"
								name="hour_<?php echo $i; ?>"
								id="hour_<?php echo $i; ?>"
								value="<?php echo $get_time_date[$i]["hour"]; ?>"
								<?php if(!$get_time_date[$i]["enabled"]) { ?>disabled="disabled" <?php } ?>/>
							:
							<input type="number" max="59" min="0"
								name="minute_<?php echo $i; ?>"
								id="minute_<?php echo $i; ?>"
								value="<?php echo $get_time_date[$i]["minute"]; ?>"
								<?php if(!$get_time_date[$i]["enabled"]) { ?>disabled="disabled" <?php } ?>/>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<td></td>
						<td>
							<input type="submit" value="Simpan" class="button" name="send" />
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
<script type="text/javascript">
	$(document).ready(function(){
		$(".enable_button_day").change(function() {
			if(this.checked) {
				$(this).parent().next().find("input[type='number']").removeAttr("disabled");
			}
			else {
				$(this).parent().next().find("input[type='number']").attr("disabled", "disabled");
			}
		});
	});
</script>
<?php
	include "include/template-bottom.php";
?>