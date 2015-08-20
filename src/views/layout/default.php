<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html> <!--<![endif]-->
<head>
	<title><?php echo $title ; ?></title>
	
	<!-- Meta -->
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	
	<link rel="shortcut icon" type="image/jpg" href="<?php echo DOMAIN; ?>favicon.png" />
	
	<!-- Bootstrap -->
	<link href="<?php echo DOMAIN; ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	
	<link href="<?php echo DOMAIN; ?>theme/css/jquery.pnotify.default.css" rel="stylesheet" />
	<link href="<?php echo DOMAIN; ?>theme/css/jquery.pnotify.default.icons.css" rel="stylesheet" />

	<!-- JQuery v1.8.2 -->
	<script src="<?php echo DOMAIN; ?>theme/scripts/jquery-1.8.2.min.js"></script>
	
	<!-- Theme -->
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="<?php echo DOMAIN; ?>theme/css/style.min.css" />
	
	<link rel="stylesheet" href="<?php echo DOMAIN; ?>theme/css/facelist.css" type="text/css" media="screen" title="Facelist" charset="utf-8" />
		
	<script src="<?php echo DOMAIN; ?>theme/scripts/script.php?domain=<?php echo DOMAIN; ?>&v2.1"></script>
	
	<!-- Bootstrap Script -->
	<script src="<?php echo DOMAIN; ?>bootstrap/js/bootstrap.min.js"></script>
	
	<script type="text/javascript" src="<?php echo DOMAIN; ?>theme/scripts/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN; ?>theme/scripts/jquery.facelist.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN; ?>theme/scripts/jquery.cookie.js"></script>
	
	
	<script src="<?php echo DOMAIN; ?>theme/scripts/jquery.pnotify.min.js"></script>	
	
	<script src="<?php echo DOMAIN; ?>theme/scripts/jquery.tablesorter.min.js"></script>
	
	<script type="text/javascript" src="<?php echo DOMAIN; ?>theme/scripts/farbtastic.js"></script>
	<link rel="stylesheet" href="<?php echo DOMAIN; ?>theme/css/farbtastic.css" type="text/css" />	

		<!--  Flot (Charts) JS -->
	<script src="<?php echo DOMAIN; ?>theme/scripts/flot/jquery.flot.js" type="text/javascript"></script>
	<script src="<?php echo DOMAIN; ?>theme/scripts/flot/jquery.flot.pie.js" type="text/javascript"></script>
	<script src="<?php echo DOMAIN; ?>theme/scripts/flot/jquery.flot.tooltip.js" type="text/javascript"></script>
	<script src="<?php echo DOMAIN; ?>theme/scripts/flot/jquery.flot.resize.js" type="text/javascript"></script>
	
	<script src="<?php echo DOMAIN; ?>uploadify/jquery.uploadify.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo DOMAIN; ?>uploadify/uploadify.css">
	
	<script src="<?php echo DOMAIN; ?>theme/scripts/jquery.hammer.min.js"></script>
	
	<!-- Custom Onload Script -->
	<script src="<?php echo DOMAIN; ?>theme/scripts/load.js?v2.1"></script>

</head>
<body>
	
	<!-- Start Content -->
	<div class="container">
		
		<aside id="menu">
		
			<header style="padding-top:4px;">
				<img src="<?php echo DOMAIN; ?>theme/images/friendly-torrent-logo.png" width="40" style="margin-left:3px;" >
			</header>
			
			
			<section id="profil" >
				<ul>
					<a href="<?php echo DOMAIN; ?>boxe/">
						<li>
							<img src="<?php echo Tools::get_gravatar($user['mail']); ?>" class="show">
							<?php echo ucfirst($user['login']); ?>
						</li>
					</a>
				</ul>
			</section>
			
			<section style="border-bottom:none;" >
				<ul>
					<a href="<?php echo DOMAIN; ?>torrents/">
						<li>
							<span class="glyphicon glyphicon-cloud-download"></span>
							<?php echo LANG_DOWNLOADS; ?>
						</li>
					</a>
					<a href="<?php echo DOMAIN; ?>rss/">
						<li>
							<span class="glyphicon glyphicon-bookmark"></span>
							<?php echo LANG_RSS; ?>
						</li>
					</a>
					<a href="<?php echo DOMAIN; ?>messagerie/">
					<li>
						<span class="glyphicon glyphicon-inbox"></span>
						<?php echo LANG_INBOX; ?>
						<div aff="0" style="position:relative;float:right;margin-top:-50px;display:none;" id="nbMess" class="badge">0</div>
					</li>
					</a>

					<li onclick="$('#boxesMenu').toggle();" id="otherBoxes" >
						<span class="glyphicon glyphicon-user"></span>
						<?php echo LANG_BOXES; ?>
					</li>
					<a href="<?php echo DOMAIN; ?>stats/">
					<li>
						<span class="glyphicon glyphicon-stats"></span>
						<?php echo LANG_CHARTS; ?>
					</li>
					</a>
					<?php 
						if($user['admin'] == 1){
							?>
								<a href="<?php echo DOMAIN; ?>admin/">
								<li>
									<span class="glyphicon glyphicon-cog"></span>
									<?php echo LANG_ADMIN; ?>
								</li>
								</a>
							<?php
						}
					?>
				</ul>
			</section>
			
			<section class="bottom" >
				<ul >
					<a href="<?php echo DOMAIN; ?>compte/">
						<li style="border-left:none;">
							<span class="glyphicon glyphicon-cog"></span>
						</li>
					</a>
					<a href="<?php echo DOMAIN; ?>action/disconnect/">
						<li style="border-right:none;" >
							<span class="glyphicon glyphicon-off"></span>
						</li>
					</a>
				</ul>
			</section>
		</aside>
		
		<div id="ajaxContent">
			<?php echo $content_for_layout; ?>
		</div>	
		
		 <!-- Modal -->
  <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo LANG_CLOSE; ?></button>
          <button type="button" class="btn btn-primary" id="OK" ><?php echo LANG_DONE; ?></button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
	</div>
	
	<div class="modal fade" id="liens" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><?php echo LANG_LINKS; ?></h4>
        </div>
        <div class="modal-body">
        	<textarea class="contenu" style="width:100%;height:100px;" ></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo LANG_CLOSE; ?></button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
	</div>
	
	<div class="menuAccount" id="context" style="position:absolute;z-index:10;display:none;" >
	</div>

	<?php
		$i = 0;
		$r = "";
		$user = new User();
		while($rslt = mysql_fetch_assoc($other)){
			$i++;
			
			$sql2 = "SELECT COUNT(*) as nb FROM torrents WHERE idBoxe = '".$rslt['id']."' AND time >= '".$user->timeLastCloud($rslt['id'])."';";
			$rst2 = mysql_query($sql2);
			$rslt2 = mysql_fetch_assoc($rst2);

			$r .='<li role="presentation"><a href="'.DOMAIN.'boxe/users/'.$rslt['login'].'/" >'.$rslt['login'].'<span style="';
			if($rslt2['nb'] == 0){
				$r .= 'display:none;';
			}
			$r .= '" class="badge pull-right">'.$rslt2['nb'].'</span></a></li>';
		}
		
		$nb = 360 - ($i * 26);
		
		if($nb < 20)
			$nb = 20;
			
		if (!$r)
			echo "<script>$('#otherBoxes').hide();</script>";
		?>

	<div id="boxesMenu" style="position:fixed;z-index:10;display:none;top:<?php echo $nb; ?>px;left:90px;" >
		<div class="dropdown">
			<ul class="dropdown-menu" role="menu" style="display:block;" aria-labelledby="dropdownMenu1">
				<?php echo $r; ?>
			</ul>
		</div>
	</div>

	<script>

			nbNotif();
	 		setInterval("nbNotif()", 5000);

	</script>
</body>
</html>