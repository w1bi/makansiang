<?php
	$admin_only_page = 'c';
	include "include/core-top.php";
?>
<?php
	// Tarik user dulu dari get
	if(!isset($_GET['notdirect'])) {
		header("Location: ". $config['full_domain']);
		die;
	}
	
	$get_user_email_id	= "";
	$get_user_email 	= "";
	
	if(isset($_GET['email']) && $_GET['email'] != "" && preg_match('/[a-zA-Z0-9._-]+/', $_GET['email'])) {
		$get_user_email_id	= $_GET['email'];
		$get_user_email 	= $_GET['email'] . '@' . $config['email_domain'];
	}
	
	if($get_user_email == "") {
		header("Location: ". $config['full_domain']);
		die();
	}
	
	// Ambil saldo
	$user_saldo	= 0;
	$saldo_sql 	= mysqli_query($mysql, "SELECT user_saldo FROM ms_saldo WHERE user_email = '". mysqli_real_escape_string($mysql, $get_user_email) ."'");
	
	if($saldo_arr = mysqli_fetch_array($saldo_sql)) {
		$user_saldo = $saldo_arr[0];
	}
?>
<?php 
	include "include/template-top.php";
?>
    <h1>Riwayat <?php echo htmlentities($get_user_email_id, ENT_QUOTES); ?></h1>
    <div class="saldo-total">
        Saldo :
        <strong>Rp <?php echo number_format($user_saldo, 0, '', '.'); ?></strong>
    </div>
    <div class="clear"></div>
    <div id="listorang">
    <?php 
        $list_query = mysqli_query($mysql, "
			SELECT 
				log_time,
				log_info,
				log_saldo,
				log_pesanan,
				pesan_text,
				admin_name
			FROM
				ms_log LEFT JOIN ms_pesanan ON log_pesanan = pesan_id
				INNER JOIN ms_admin ON admin_id = by_admin 
			WHERE
				user_email = '". mysqli_real_escape_string($mysql, $get_user_email) ."'
				AND log_time > DATE_SUB(NOW(), INTERVAL 14 DAY)
			ORDER BY log_time DESC
		");
        
        $log_counter = 0;
        
        while($list_log = mysqli_fetch_array($list_query)) {
            $log_counter++;
            
            $admin_name		= htmlentities($list_log['admin_name'], ENT_QUOTES);
            $tanggal		= date('d M Y [ H:m ]', strtotime($list_log['log_time']));
            $log_info		= htmlentities($list_log['log_info'], ENT_QUOTES);
            $saldo			= 'Rp ' . number_format($list_log['log_saldo'],0,'','.');
			
			if(isset($list_log['log_pesanan'])) {
				 $log_info	= "<strong> " . $log_info . "</strong><br />";
				 $log_info	.= str_replace("\n", "<br />", htmlentities($list_log['pesan_text'], ENT_QUOTES));
			}
			
			$insert_even	= ($log_counter%2 == 0) ? ' even' : '';
    ?>
    <div class="perone<?php echo $insert_even;?>">
    	<div class="photo">
            <img src="<?php echo $config['full_domain']; ?>images/photo-default.png">
        </div>
        <div class="list">
            <div class="user">
                <?php echo $admin_name; ?>
            </div>
            <div class="detail">
                <?php echo $log_info; ?>
            </div>
        </div>
        <div class="harga">	
            <?php echo $tanggal; ?>
            <br />
            <?php echo $saldo; ?>
        </div>
        <div class="clear"></div>
    </div>
    <?php
        }
        
        if($log_counter <= 0) {
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