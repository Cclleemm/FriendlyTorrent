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
		
		if($_GET['paypal']){
			echo '<div class="alert alert-success">
					<button type="button" class="close" data-dismiss="alert">×</button>
					Votre paiement a été envoyé, les jours payés seront ajoutés sous 24h.
				</div>';
		}
	?>
<br />
<form class="form-horizontal" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<h3 id="abo" >Abonnement</h3><br />
		
		<div class="form-group">
			<label class="col-lg-2 control-label" style="padding-top:0px;" >Abonnement en cours</label>
				<div class="col-lg-10">
					Il vous reste actuellement <?php echo $user['nbrJours']; ?> jour(s) sur votre compte.
				</div>
		</div>
		<div class="form-group">
			<label class="col-lg-2 control-label" >Paiement</label>
			<div class="col-lg-10">
				
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="fbn.impakt@gmail.com">
				<input type="hidden" name="lc" value="US">
				<input type="hidden" name="item_name" value="30 jours">
				<input type="hidden" name="amount" value="4.00">
				<input type="hidden" name="currency_code" value="EUR">
				<input type="hidden" name="button_subtype" value="services">
				<input type="hidden" name="no_note" value="0">
				<input type="hidden" name="return" value="http://88.190.38.35/compte/?paypal=1">
				<input type="hidden" name="bn" value="PP-BuyNowBF:btn_paynowCC_LG.gif:NonHostedGuest">
				<input type="image" src="https://www.paypalobjects.com/fr_XC/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
				<img alt="" border="0" src="https://www.paypalobjects.com/fr_XC/i/scr/pixel.gif" width="1" height="1">
			</div>
		</div><br />
</form>

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