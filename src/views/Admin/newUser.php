

	
	<?php
		if($error){
			echo '<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert">×</button>
					<strong>Attention !</strong> '.$error.'
				</div>';
		}
	?>

<form class="form-horizontal" method="POST" action="" role="form" >

	<h3>Création d'un compte</h3><br />
	
	<div class="form-group">
			<label for="inputUsername" class="col-lg-2 control-label" >Login</label>
			<div class="col-lg-10">
				<input type="text" name="login" id="inputUsername" class="form-control"  />
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputPasswordOld" class="col-lg-2 control-label" >Mot de passe</label>
			<div class="col-lg-10">
				<input type="text" id="inputPasswordOld" class="form-control" name="password" placeholder="Nouveau mot de passe" /><br />
				<input type="text" id="inputPasswordOld" class="form-control" name="password2" placeholder="Répétez le mot de passe" />
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputMail" class="col-lg-2 control-label" >Mail</label>
			<div class="col-lg-10">
				<input type="text" id="inputMail" class="form-control" name="mail" placeholder="Adresse mail" />
			</div>
		</div>
		
		<ol class="breadcrumb" style="margin-top:10px;">
			<button type="submit" class="btn btn-primary">Inscrire le membre</button>
		</ol>
</form>
