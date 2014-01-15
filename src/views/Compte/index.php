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
	
	<h3>Informations de bases</h3><br />

		<div class="form-group">
			<label for="inputUsername" class="col-lg-2 control-label" >Login</label>
			<div class="col-lg-10">
				<input type="text" id="inputUsername" class="form-control" value="<?php echo $user['login']; ?>" disabled="disabled" />
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputPasswordOld" class="col-lg-2 control-label" >Mot de passe</label>
			<div class="col-lg-10">
				<input type="text" id="inputPasswordOld" class="form-control" name="oldPass" placeholder="Ancien mot de passe" /><br />
				<input type="text" id="inputPasswordOld" class="form-control" name="newPass" placeholder="Nouveau mot de passe" /><br />
				<input type="text" id="inputPasswordOld" class="form-control" name="newPass2" placeholder="Répétez le mot de passe" />
			</div>
		</div>
		
	<br /><h3>Flux RSS</h3><br />
	
		<div class="form-group">
			<label for="inputUsername" class="col-lg-2 control-label" >Lien vers le Flux RSS</label>
			<div class="col-lg-10">
				<input type="text" id="inputUsername" class="form-control" name="rss" value="<?php echo $user['rss']; ?>" placeholder="Lien" />
			</div>
		</div>

	<br /><h3>Avatar</h3><br />
	
		<div class="form-group">
			<label for="inputUsername" class="col-lg-2 control-label" >Gravatar</label>
			<div class="col-lg-1">
				<img src="<?php echo Tools::get_gravatar($user['mail']); ?>" style="float:left;padding-right:10px;" />
			</div>
			<div class="col-lg-9">
				<input type="text" id="inputUsername" class="form-control" value="<?php echo $user['mail']; ?>" disabled="disabled" /><br />
				<a class="btn btn-info" target="_BLANK" href="http://fr.gravatar.com/" >Gérer mon avatar</a>
			</div>
		</div>
	<ol class="breadcrumb" style="margin-top:10px;">
		<button type="submit" class="btn btn-success"><i></i>Enregistrer</button>
	</ol>
</form>
<br/>
		
</div>

<?php
	echo $java;
?>