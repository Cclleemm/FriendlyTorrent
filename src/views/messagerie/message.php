<header class="top" >
	<a class="ajax" href="<?php echo DOMAIN; ?>messagerie/"><button style="margin-top:-5px;" class="btn btn-success pull-right"><i></i><?php echo LANG_INBOX; ?></button></a>
	<div style="margin-top:-5px;margin-right:5px;" class="btn-group pull-right"> 
			<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button> 
			<ul class="dropdown-menu pull-right"> 
				<li><a href="<?php echo DOMAIN; ?>messagerie/nouveau/<?php echo $message['id']; ?>/"><i class="glyphicon glyphicon-share-alt"></i> <?php echo LANG_REPLY; ?></a></li> 
				<li class="divider"></li> 
				<li><a href="<?php echo DOMAIN; ?>messagerie/delete/<?php echo $message['id']; ?>/"><?php echo LANG_DELETE_MESSAGE; ?></a></li> 
			</ul>
		</div>
	<p><?php echo $message['object']; ?></p>
</header>

<header class="top" style="height:auto;background-color:#f9fafc;" >
	<img style="width:20px;" src="<?php echo Tools::get_gravatar($message['mail']); ?>" class="img-circle"> <?php echo $message['login']; ?> &lt;<?php echo $message['mail']; ?>&gt;
		<div class="pull-right inline"><?php echo Tools::date_fr_texte($message['time']); ?> </div>
</header>

 
	<div class="padder"> 
		<div class="panel text-sm bg-light"> 
			<div class="panel-body"> <?php echo nl2br($message['text']); ?> </div> 
		</div> 
	</div>