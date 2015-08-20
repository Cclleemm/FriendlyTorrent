

	
	<?php
		if($error){
			echo '<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert">×</button>
					<strong>Attention !</strong> '.$error.'
				</div>';
		}
	?>

<form class="form-horizontal" method="POST" action="" role="form" >

	<h3><?php echo LANG_CREATE_ACCOUNT; ?></h3><br />
	
	<div class="form-group">
			<label for="inputUsername" class="col-lg-2 control-label" ><?php echo LANG_LOGIN; ?></label>
			<div class="col-lg-10">
				<input type="text" name="login" id="inputUsername" class="form-control"  />
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputPasswordOld" class="col-lg-2 control-label" ><?php echo LANG_PASSWORD; ?></label>
			<div class="col-lg-10">
				<input type="text" id="inputPasswordOld" class="form-control" name="password" placeholder="Nouveau mot de passe" /><br />
				<input type="text" id="inputPasswordOld" class="form-control" name="password2" placeholder="Répétez le mot de passe" />
			</div>
		</div>
		
		<div class="form-group">
			<label for="inputMail" class="col-lg-2 control-label" ><?php echo LANG_EMAIL; ?></label>
			<div class="col-lg-10">
				<input type="text" id="inputMail" class="form-control" name="mail" placeholder="Adresse mail" />
			</div>
		</div>
		
		<ol class="breadcrumb" style="margin-top:10px;">
			<button type="submit" class="btn btn-primary"><?php echo LANG_ADD_USER; ?></button>
		</ol>
</form>
