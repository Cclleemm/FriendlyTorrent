
<div id="content" class="color-13">
		
	<?php include(ROOT.'views/layout/header.php'); ?>
	
<div class="separator"></div>

	<div id="alert" >
		<?php
			if($_GET['alert'] == 'editUser'){
				echo '<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>'.LANG_USER_EDITED.'</strong>
</div>';
			}
			
			if($_GET['alert'] == 'adminUserFail'){
				echo '<div class="alert alert-warning">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>'.LANG_YOU_ARE_THE_LAST_ADMIN.'</strong>
</div>';
			}
			
			if($_GET['alert'] == 'newUser'){
				echo '<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>'.LANG_USER_CREATED.'</strong>
</div>';
			}
		?>
	</div>
	
		<h4><?php echo LANG_USERS; ?> <a href="<?php echo DOMAIN; ?>admin/newUser/"><button style="float:right;" class="btn btn-success btn-icon glyphicons message_new"><i></i><?php echo LANG_NEW_USER; ?></button></a> </h4><br />
		<table class="table table-vertical-center table-primary table-thead-simple">
			<thead>
                <tr>
                  <th><?php echo LANG_LOGIN; ?></th>
                  <th><?php echo LANG_EMAIL; ?></th>
                  <th><?php echo LANG_TRANSMISSION_PORT; ?></th>
                  <th><?php echo LANG_ADMIN; ?></th>
                  <th><?php echo LANG_ACTION; ?></th>
                </tr>
              </thead>
              <tbody>
              	<?php	
              		foreach($users as $value){
              		
              		$user= new User($value['id']);
			  		
			  		/*$paiements = "";
			  		
			  		foreach($user->listPaiement() as $key => $rst){
				  		$paiements .= Tools::date_fr_texte($rst['time'])." : ".$rst['nbrJours']." jours [".$rst['price']." euros]";
			  		}*/
			  		
					try {
					   	$rpc = new Transmission($user->configRPC());
					} catch (Exception $e) {
					    $rpc = false;
					}

              		
              			($value['admin'] == 1)? $admin = LANG_YES : $admin = LANG_NO;
              			($value['admin'] == 1)? $adminBtn = '<a data-toggle="tooltip" data-placement="top" data-original-title="'.LANG_REMOVE_ADMIN_PRIVILEGES.'" href="'.DOMAIN.'admin/editUser/'.$value['id'].'/?admin=true&value=0" class="btn btn-action glyphicon glyphicon-thumbs-down btn-warning"><i></i></a>' : $adminBtn = '<a data-toggle="tooltip" data-placement="top" data-original-title="Mettre admin" href="'.DOMAIN.'admin/editUser/'.$value['id'].'/?admin=true&value=1" class="btn btn-action glyphicon glyphicon-thumbs-up btn-warning"><i></i></a>';
              		
	              		echo '<tr>
	              				<td><b>'.$value['login'].'</b></td>
	              				<td>'.$value['mail'].'</td>
	              				<td>'.$value['port'].'</td>
	              				<td>'.$admin.'</td>
	              				<td class="center" style="width: 100px;">
									<a href="'.DOMAIN.'admin/editUser/'.$value['id'].'/" data-toggle="tooltip" data-placement="top" data-original-title="'.LANG_EDIT_ACCOUNT.'" class="btn-action glyphicon glyphicon-pencil btn btn-success"><i></i></a>
									'.$adminBtn;
										if (!$rpc)
											echo ' <a data-toggle="tooltip" data-placement="left" data-original-title="'.LANG_START_TRANSMISSION.'" href="'.DOMAIN.'admin/startTrans/'.$value['id'].'/" class="btn btn-action glyphicon glyphicon-play btn-success"><i></i></a>';
										else if(!$rpc->isRunning())
											echo ' <a data-toggle="tooltip" data-placement="left" data-original-title="'.LANG_STOP_TRANSMISSION.' | RPC DOWN" href="'.DOMAIN.'admin/stopTrans/'.$value['id'].'/" class="btn btn-action glyphicon glyphicon-stop btn-warning"><i></i></a>';
										else
											echo ' <a data-toggle="tooltip" data-placement="left" data-original-title="'.LANG_START_TRANSMISSION.'" href="'.DOMAIN.'admin/stopTrans/'.$value['id'].'/" class="btn btn-action glyphicon glyphicon-stop btn-danger"><i></i></a>';
									echo '
								</td>
	              			</tr>';
              		}
              	?>
              </tbody>
		</table>
</div>
