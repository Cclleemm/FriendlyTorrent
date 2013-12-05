<header class="top" >
	<a class="ajax" href="<?php echo DOMAIN; ?>messagerie/"><button style="margin-top:-5px;" class="btn btn-success pull-right"><i></i>Messagerie</button></a>
	<!--<div style="margin-top:-5px;margin-right:5px;" class="btn-group pull-right"> 
			<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button> 
			<ul class="dropdown-menu pull-right"> 
				<li><a href="<?php echo DOMAIN; ?>messagerie/nouveau/<?php echo $message['id']; ?>/"><i class="glyphicon glyphicon-share-alt"></i> Reply</a></li> 
				<li class="divider"></li> 
				<li><a href="<?php echo DOMAIN; ?>messagerie/delete/<?php echo $message['id']; ?>/">Supprimer ce message</a></li> 
			</ul>
		</div>-->
	<p><?php echo $title; ?></p>
</header>

<section style="border: 1px solid #ddd;width:100%;" class="panel panel-info col-lg-6 no-padder"> <form action="<?php echo DOMAIN; ?>messagerie/nouveau" method="POST" > <textarea class="form-control no-border" rows="2" name="message" placeholder="Message a envoyer"></textarea> <footer style="padding-bottom:0px;" class="panel-footer"> <input class="btn btn-info pull-right btn-sm font-bold" value="Envoyer" type="submit" /> <input name="to_user" type="hidden" value="<?php echo $idUser; ?>" /><p style="clear:both;" /></footer> </form> </section>

<p style="clear:both;"></p>
<?php
	$i = 0;
	while($rslt = mysql_fetch_assoc($messages)){
	              		$i++;
	              		?>
	              			<article <?php
	              				if($rslt['seen'] == 0)
	              				echo ' style="border:1px solid #269abc;" ';
	              			?>class="comment-item"> <a class="pull-left thumb-sm"> <img width="35px" src="<?php echo Tools::get_gravatar($rslt['mail']); ?>" class="img-circle"> </a> <section style="margin-left:50px;" class="comment-body m-b-lg"> <header> <a href="#"><strong><?php echo $rslt['login'] ; ?></strong></a> <span class="text-muted text-xs"> <?php echo Tools::date_fr_texte($rslt['time']) ; ?> </span> </header> <div> <?php echo nl2br($rslt['text']) ; ?></div> </section> </article>
	              		<?php
              		}
    if($i == 0){
	    echo '<div class="alert alert-info">
	<strong>Il n\'y a encore aucun message !</strong>';
    }
?>