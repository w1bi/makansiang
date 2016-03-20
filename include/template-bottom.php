    </div>
    <?php if(!isset($config["template_full"])) { ?>
    <div id="container-right">
        <h1>Quota Hari Ini</h1>
        <div class="menu-container text-center">
			<?php
				
				$quota_is_open			= (date('Gi') >= $config['max_order_time']) ? false : true;
				$count_quota_green		= 0;
				
				if($quota_is_open) {
					if(isset($pesanan_sekarang)) {
						$count_quota_green = $pesanan_sekarang;
					}
					else {
						$now_date = date('Y-m-d');
						$list_query = mysqli_query($mysql, "SELECT * FROM ms_pesanan WHERE pesan_tanggal = '$now_date' ORDER BY pesan_id DESC");
						$pesanan_sekarang = mysqli_num_rows($list_query);
						$count_quota_green = $pesanan_sekarang;
					}
				}
				else {
					$count_quota_green = $config['max_order'];
				}
					
                for($i = 0; $i < $config['max_order']; $i++) {
					$is_avail = '';
                    if($i >= $count_quota_green) {
						$is_avail = ' people-quota-available';
                    }
					
                    echo '<i class="people-quota-round'.$is_avail.'" title="'. ($config['max_order']-$i) .'">'.($config['max_order']-$i).'</i>';
                }
				
				$count_quota_allowed	= $config['max_order'] - $count_quota_green;
            ?>
			<?php if($quota_is_open && $count_quota_allowed > 0) { ?>
                <div class="time-counter"></div>
                <a class="button-quota-pesan" href="<?php echo $config['full_domain']; ?>makan-dong">
                Pesan Sekarang &raquo;</a>
            <?php } else if($quota_is_open && $count_quota_allowed == 0) { ?>
                <div class="time-counter">Quota Habis</div>
            <?php } else { ?>
                <div class="time-counter">Waktu Pesanan Telah Ditutup!</div>
            <?php } ?>
        </div>
        <h1>Menu Hari Ini</h1>
        <div class="menu-container">
            <input type="text" class="find-menu" placeholder="Cari menu..." />
            <div class="clear"></div>
            
            <?php
                $menu_number = 0;
                
                foreach($global_menu as $menu_list) {
					if ($menu_list['menu_status'] != 1) {
						continue;
					}
								
                    $menu_name 	= ucwords(htmlentities($menu_list['menu_nama']));
                    $menu_price	= number_format($menu_list['menu_harga'], 0, '', '.');
					$menu_id	= $menu_list['menu_id'];
					
					$menu_side	= '';
					if(in_array('tambahan', $menu_list['tags'])) {
						$menu_side = ' menu-side';
					}
					
					$menu_price_unformat = $menu_list['menu_harga'];
                    
                    $menu_number++;
					$menu_third_color = "";
					
					if($menu_number%3 == 0) {
						$menu_third_color = " menu-diff";
					}
            ?>
            <div class="menu-detail menu-detail-show<?php echo $menu_side; ?><?php echo $menu_third_color; ?>" title="<?php echo $menu_name; ?>">
                <div class="menu-nama"><?php echo $menu_name; ?></div>
                <div class="menu-harga">Rp <?php echo $menu_price; ?></div>
                <div class="hide menu-id"><?php echo $menu_id; ?></div>
                <div class="hide menu-harga-asli"><?php echo $menu_price_unformat; ?></div>
                <div class="clear"></div>
            </div>
            <?php
                }
            ?>
            <div class="clear"></div>
    	</div>
	</div>
    <?php } ?>
	<div class="clear"></div>
	<div id="footer">
    	&copy; <?php echo date('Y'); ?> MSG v1.6 - <a href="http://www.wibisaja.com/" target="_blank">Wibi</a> | <a href="https://github.com/w1bi/makansiang" target="_blank">MakanSiang GitHub Project</a>
    </div>
</div>
<div class="clear"></div>
<div id="feedback" class="hide">&raquo; Berikan Masukan &laquo;</div>

<script type="text/javascript">
$('.menu-detail').on('click', function() {
	if($('#main-food-autocomplete').length) {
		
		$('html, body').animate({
			scrollTop: $("#table-container").offset().top - 60
		}, 1000);
	
		if($('#main-food-autocomplete').is(":disabled")) {
			return;
		}
		
		if($(this).hasClass('menu-side') && $('#main-food-id').val() != '') {
			$("#side-menu-delete-button").show();
			$("#side-food-autocomplete").addClass("menu-readonly");
			$("#side-food-autocomplete").attr("readonly","readonly");
			$('#side-food-autocomplete').val($(this).find('.menu-nama').html());
			$('#side-food-id').val($(this).find('.menu-id').html());
			$('#side-food-price').val($(this).find('.menu-harga-asli').html());
			count_total();
		}
		else {
			$("#main-menu-delete-button").show();
			$("#main-food-autocomplete").addClass("menu-readonly");
			$("#main-food-autocomplete").attr("readonly","readonly");
			$('#main-food-autocomplete').val($(this).find('.menu-nama').html());
			$('#main-food-id').val($(this).find('.menu-id').html());
			$('#main-food-price').val($(this).find('.menu-harga-asli').html());
			count_total();
		}
	} else {
	
		var newForm = document.createElement("form");
		newForm.setAttribute('enctype', 'application/x-www-form-urlencoded');
		newForm.setAttribute('method', 'post');
		newForm.setAttribute('action', '<?php echo $config['full_domain']; ?>makan-dong');
		newForm.setAttribute('style', 'display: none;');
		
		var newText = document.createElement("input");
		newText.setAttribute('name', 'clickmenu');
		newText.setAttribute('type', 'hidden');
		newText.setAttribute('value', $(this).find('.menu-nama').html());
		
		var newText2 = document.createElement("input");
		newText2.setAttribute('name', 'clickmenuid');
		newText2.setAttribute('type', 'hidden');
		newText2.setAttribute('value', $(this).find('.menu-id').html());
		
		var newText3 = document.createElement("input");
		newText3.setAttribute('name', 'clickmenuprice');
		newText3.setAttribute('type', 'hidden');
		newText3.setAttribute('value', $(this).find('.menu-harga-asli').html());
		
		newForm.appendChild(newText);
		newForm.appendChild(newText2);
		newForm.appendChild(newText3);
		document.body.appendChild(newForm);
		newForm.submit();
		
	}
});

var $num_menu;

$('.find-menu').on('keyup', function() {
	var findString = new RegExp($(this).val(), 'i'); 
	
	$num_menu = 0;
	
	$('.menu-detail').removeClass('menu-diff');
	$('.menu-detail').removeClass('menu-detail-show');
	$('.menu-detail .menu-nama').filter(function(){		
		if($(this).text().match(findString)) {
			$num_menu++;
			return true;
		}
		
		return false;
	}).parent().addClass('menu-detail-show');
	
	$('.menu-detail-show').each(function (i) {
		if (i % 3 == 2) $(this).addClass('menu-diff');
	});
});

$('textarea').on('keydown', function (e) {
	if (e.ctrlKey && e.keyCode == 13) {
		$(this).closest('form').find(':submit').click();
	}
});

function dockShow() {
	$('#a-left-dock').attr('class','');
	$('.menu-line-container').addClass('rotate')
	$('.dock-button').addClass('active');
}

function dockHide() {
	$('#a-left-dock').attr('class','hide-dock');
	$('.menu-line-container').removeClass('rotate')
	$('.dock-button').removeClass('active');
}

function dockHideShow() {
	if($('#a-left-dock').attr('class') == 'hide-dock') {
		dockShow();
	}
	else {
		dockHide();
	}
}

$('.dock-button').on('click', dockHideShow);
if( /Android|iPhone|iPad|iPod|IEMobile/i.test(navigator.userAgent) ) {
	$(window).on('swipeleft', dockHide);
	$(window).on('swiperight', dockShow);
}

/*document.onkeydown = checkKey;
function checkKey(e) {
    e = e || window.event;
    if (e.keyCode == '37') {
       dockHide();
    }
    else if (e.keyCode == '39') {
       dockShow();
    }
}*/

$('.photo').on('click', function() {
	if($(this).find('img').length <= 0) {
		return;
	}
	
	imgsrc = $(this).find('img').attr('src').replace(/\?sz=[0-9]+/i, "?sz=500");
	
	text =  "<img src=\"" + imgsrc + "\" style=\"max-width: 90%;\" /><br /><br />";
	text += "<input type=\"button\" name=\"cancel-cancel\" class=\"button cancel\" value=\"Tutup\" onclick=\"$('.blocker').remove()\" /> ";
	
	var blocker = document.createElement("div");
	blocker.className = 'blocker';
	
	var box_text = document.createElement("div");
	box_text.innerHTML = text;
	top_margin = (screen.height / 2) - 350; 
	if(top_margin < 50) {
		top_margin = 50;
	}
	
	box_text.setAttribute("style", "text-align: center; margin: " + top_margin + "px auto;");	
	blocker.appendChild(box_text);
	
	document.body.appendChild(blocker);
});

function goParseCountdown(t) {
	if($('.button-quota-pesan').length <= 0) {
		return false;
	}
	
	var seconds = Math.floor( t % 60 );
	var minutes = Math.floor( (t/60) % 60 );
	var hours = Math.floor( (t/(60*60)) % 24 );
	if(t <= 0) {
		location.href = location.href;
		return false;
	}
	var text = "Waktu Tersisa: ";
	if(hours > 0) {
		text += "<strong>" + hours + "</strong> jam ";
	}
	if(minutes > 0) {
		text += "<strong>" + minutes + "</strong> menit ";
	}
	text += "<strong>" + seconds + "</strong> detik ";
	$('.time-counter').html(text);
	setTimeout('goParseCountdown(' + (t - 1) + ')', 970);
}


<?php
$date_max_order_create	= strtotime(date('d F Y ') . substr($config['max_order_time'], 0, 2) . ":" . substr($config['max_order_time'], 2, 2) . ":00");
$date_create_difference	= $date_max_order_create - time();
echo "goParseCountdown($date_create_difference);";
?>

</script>
</body>
</html>
<?php
	mysqli_close($mysql);
?>