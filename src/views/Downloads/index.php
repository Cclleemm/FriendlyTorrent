<div id="wrapper">
	<div id="content" class="">
		<div id="login">
			<div class="loginWrap">
				<h2 class="glyphicons download form-signin-heading"><i></i> Download</h2>
				<div class="form-signin">
					<?php
						if(!$error){
							echo LANG_YOU_WILL_DOWNLOAD; 
							
							if($file['type'] == "FOLDER"){
								echo LANG_FOLDER;
								$size = 0;
							}else{
								echo LANG_FILE;
								$size = filesize($download['linkFile']);
							}
							
							$tab = explode('/', $download['linkFile']);
							$name = $tab[count($tab)-1];
						?>
						 : <br /><br /><center><h6 style="color:black;" ><?php 
						$nameTmp = explode('/', $file['link']);
						$name = $nameTmp[count($nameTmp)-1];
						echo Tools::debutchaine($name, 40); 
						?></h6></center>
						<?php if($size){ ?><?php echo LANG_SIZE; ?> : <br /><br /><center><h4 style="color:black;" ><?php 
							echo Tools::convertFileSize($size);
						?></h4></center><br /><br /><?php } ?>
						
						<a href="<?php echo DOMAIN.'downloads/start/'.$download['clef'].'/'.$name; ?>"><span style="width:247px;" class="btn btn-large color-4"><?php echo LANG_START_DOWNLOAD; ?></span></a>
					<?php
						}else{
							echo '<br /><center><h4 style="color:red;" >'.LANG_ERROR_INEXISTING.'</h4></center>';
						}
					?>
				</div>
			</div>
		</div>		
	</div>
</div>