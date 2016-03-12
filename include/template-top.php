<html>
<head>
	<title>Makan Siang Ga?</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, height=device-height, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
    <link rel="shortcut icon" type="image/ico" href="<?php echo $config['full_domain']; ?>favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:600,600italic,300,400,400italic">
    <link href="<?php echo $config['full_domain']; ?>style/reset.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $config['full_domain']; ?>style/global.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $config['full_domain']; ?>style/table-blue/style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $config['full_domain']; ?>style/shake.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $config['full_domain']; ?>style/jquery-ui.css" rel="stylesheet" type="text/css">
    <script src="<?php echo $config['full_domain']; ?>scripts/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo $config['full_domain']; ?>scripts/jquery.mobile.custom.min.js" type="text/javascript"></script>
    <script src="<?php echo $config['full_domain']; ?>scripts/jquery-ui.min.js" type="text/javascript"></script>
</head>
<body>
    <div id="a-top-container">
        
        <div id="logo-top-container">
            <a href="<?php echo $config['full_domain']; ?>">
                <img src="<?php echo $config['full_domain']; ?>images/top-small.png" class="logo-top" alt="Makan Siang Ga?" />
            </a>
        </div>
        
        <a class="float-left top primary dock-button" href="javascript:void(0);">
        	<div class="menu-line-container">
                <div class="menu-line"></div>
                <div class="menu-line"></div>
                <div class="menu-line last"></div>
            </div>
        </a>
        
        <?php if($is_admin) { ?>
        <a class="float-right top" href="<?php echo $config['full_domain']; ?>?admin-logout">Keluar</a>
        <?php } else { ?>
        <a class="float-right top" href="<?php echo $config['full_domain']; ?>backend-login">Backend</a>
        <?php } ?>
        <div class="clear"></div>
    </div>
    
    <div id="a-left-dock" class="hide-dock">
    	<div id="a-left-dock-user">
        	<?php if($is_login) { ?>
        	<img class="user-photo" src="<?php echo htmlentities(preg_replace('/\?sz\=[0-9]+/i', '', $login_data['user_photo'])); ?>?sz=75"  title="<?php echo htmlentities($login_data['user_name']); ?>" alt="<?php echo htmlentities($login_data['user_name']); ?>"/>
            <div class="user-name"><?php echo htmlentities($login_data['user_name']); ?></div>
            <div class="user-email"><?php echo htmlentities($login_data['user_email']); ?></div>
            <?php } else { ?>
        	<img class="user-photo" src="<?php echo $config['full_domain']; ?>images/photo-default.png" />
            <div class="user-name">Tamu <a class="user-login-button" href="<?php echo $config['full_domain']; ?>login">Masuk</a></div>
            <div class="user-email">Harap masuk terlebih dahulu</div>
            <?php } ?>
        </div>
        <div id="a-left-dock-nav">
            <a href="<?php echo $config['full_domain']; ?>" title="Beranda"><div class="icon-beranda"></div>Beranda</a>
            <a href="<?php echo $config['full_domain']; ?>makan-dong" title="Pesan Makan"><div class="icon-makan"></div>Pesan Makan</a>
            
            <?php
                if($is_login) {
            ?>
            <a href="<?php echo $config['full_domain']; ?>riwayat" title="Riwayat"><div class="icon-riwayat"></div>Riwayat</a>
            <a href="<?php echo $config['full_domain']; ?>?logout" title="Keluar"><div class="icon-keluar"></div>Keluar</a>
            <?php
                }
            ?>
        </div>
    </div>
    
	<div id="all-container">
        
        <?php
			$notice_query = mysqli_query($mysql, "SELECT * FROM ms_pengumuman WHERE peng_status = 1 AND peng_text != ''");
			
			if(mysqli_num_rows($notice_query) > 0) {
		?>
            <div id="notice">
			<?php
                while($data = mysqli_fetch_array($notice_query)) {
					$parser = new JBBCode\Parser();
					$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
					$parser->addBBCode("sup", '<sup>{param}</sup>');
					$parser->addBBCode("sub", '<sub>{param}</sub>');
					$parser->addBBCode("s", '<strike>{param}</strike>');
					
					$parser->parse(htmlentities($data['peng_text'], ENT_QUOTES));
					
					$pengumuman = str_replace("\n", "<br />", $parser->getAsHtml());
					if($pengumuman == "") {
						continue;
					}
			?>
                <div id="notice-title">PENGUMUMAN</div>
                <div id="notice-content"><?php echo $pengumuman; ?></div>
                <div id="notice-timestamp" class="hide"><?php echo strtotime($data['peng_lastedit']); ?></div>
                <div class="clear"></div>
			<?php
				}
			?>
            </div>
		<?php
        	}
		?>
        
        <?php if($is_admin) { ?>
        <div class="clear">
        	<?php if(getAdminAccess('a')) { ?>
        	<a class="button-admin float-left" href="<?php echo $config['full_domain']; ?>daftar-pesanan">&gt; Pesanan</a>
            <?php } ?>
        	<?php if(getAdminAccess('b')) { ?>
        	<a class="button-admin float-left" href="<?php echo $config['full_domain']; ?>daftar-menu">&gt; Menu</a>
            <?php } ?>
        	<?php if(getAdminAccess('c')) { ?>
        	<a class="button-admin float-left" href="<?php echo $config['full_domain']; ?>daftar-pengumuman">&gt; Pengumuman</a>
            <?php } ?>
        	<?php if(getAdminAccess('e')) { ?>
        	<a class="button-admin float-left" href="<?php echo $config['full_domain']; ?>pengaturan-jam">&gt; Pengaturan Jam</a>
            <?php } ?>
        	<?php if(getAdminAccess('d')) { ?>
        	<a class="button-admin float-left" href="<?php echo $config['full_domain']; ?>daftar-admin">&gt; Admin</a>
            <?php } ?>
        	<a class="button-admin float-left" href="<?php echo $config['full_domain']; ?>profil-admin">Profil</a>
        </div>
        <?php  } ?>
        
        <div class="clear"></div>
        
    	<div id="container-left<?php if(isset($config["template_full"])) { ?> container-full<?php } ?>">