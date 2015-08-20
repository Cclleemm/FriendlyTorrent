<header class="top" >
	<p><?php echo $title ; ?></p>
</header>

	<?php
		if($error){
			echo '<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert">×</button>
					<strong>Attention !</strong> '.$error.'
				</div>';
		}
	?>
<br />

<form class="form-horizontal" method="POST" action="" role="form" >
	
	<h3><?php echo LANG_BASIC_INFO; ?></h3><br />

		<div class="form-group">
			<label for="inputUsername" class="col-lg-2 control-label" ><?php echo LANG_LOGIN; ?></label>
			<div class="col-lg-10">
				<input type="text" id="inputUsername" class="form-control" value="<?php echo $user['login']; ?>" disabled="disabled" />
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputPasswordOld" class="col-lg-2 control-label" ><?php echo LANG_PASSWORD; ?></label>
			<div class="col-lg-10">
				<input type="text" id="inputPasswordOld" class="form-control" name="oldPass" placeholder="<?php echo LANG_OLD_PASSWORD; ?>" /><br />
				<input type="text" id="inputPasswordOld" class="form-control" name="newPass" placeholder="<?php echo LANG_NEW_PASSWORD; ?>" /><br />
				<input type="text" id="inputPasswordOld" class="form-control" name="newPass2" placeholder="<?php echo LANG_NEW_PASSWORD; ?>" />
			</div>
		</div>
		
	<br /><h3><?php echo LANG_RSS; ?></h3><br />
	
		<div class="form-group">
			<label for="inputUsername" class="col-lg-2 control-label" ><?php echo LANG_LINK_RSS; ?></label>
			<div class="col-lg-10">
				<input type="text" id="inputUsername" class="form-control" name="rss" value="<?php echo $user['rss']; ?>" placeholder="<?php echo LANG_LINK_RSS; ?>" />
			</div>
		</div>

	<br /><h3><?php echo LANG_PROFIL_PICTURE; ?></h3><br />
	
		<div class="form-group">
			<label for="inputUsername" class="col-lg-2 control-label" >Gravatar</label>
			<div class="col-lg-1">
				<img src="<?php echo Tools::get_gravatar($user['mail']); ?>" style="float:left;padding-right:10px;" />
			</div>
			<div class="col-lg-9">
				<input type="text" id="inputUsername" class="form-control" value="<?php echo $user['mail']; ?>" disabled="disabled" /><br />
				<a class="btn btn-info" target="_BLANK" href="http://fr.gravatar.com/" ><?php echo LANG_MANAGE_MY_PROFIL_PICTURE; ?></a>
			</div>
		</div>
	<ol class="breadcrumb" style="margin-top:10px;">
		<button type="submit" class="btn btn-success"><i></i><?php echo LANG_SAVE; ?></button>
	</ol>
</form>
<br/>
		
</div>

<?php
	echo $java;
?>