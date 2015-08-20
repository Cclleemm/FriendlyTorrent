<header class="top" >
	<p><?php echo $title ; ?></p>
</header>

<div id="content" class="color-8">

	<div id="alert" >
		<?php
			if($_GET['alert'] == 'send'){
				echo '<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>'.LANG_MESSAGE_SENDED.'</strong>
</div>';
			}
		?>
	</div>

		<table class="table table-vertical-center table-primary table-thead-simple">
              <tbody>
              	<?php
              		foreach($messages as $key => $rslt){
	              		echo '<tr';
	              			if($rslt['seen'] == 0)
	              				echo ' class="success" ';
	              		echo '><td>';
	              		echo '<a class="ajax" style="color:black;" href="chat/'.$rslt['id'].'/" >'.LANG_CONVERSATION_WITH.' '.$rslt['login'].' ('.$rslt['nb'].')</a></td>
	              		<td><a class="ajax" style="color:black;" href="chat/'.$rslt['id'].'/" >'.Tools::debutchaine($rslt['text'], 50).' </a></td><td>'.Tools::date_fr_texte($rslt['time']).' </td></tr>';
              		}
              	?>
              </tbody>
		</table>

<script>
	$("#flecheMessagerie").fadeIn();
</script>