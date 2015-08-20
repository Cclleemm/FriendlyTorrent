<header class="top" >
	<p><?php echo $title ; ?></p>
</header>

<div id="content" class="color-1" >
		
	<?php include(ROOT.'views/layout/header.php'); ?>
	
	<div class="table-responsive">
		<table class="table table-vertical-center table-primary table-thead-simple">
			<thead>
                <tr>
                  <th><?php echo LANG_NAME; ?></th>
                  <th>Statut</th>
                  <th><?php echo LANG_ACTION; ?></th>
                </tr>
              </thead>
              <tbody id="listeTorrent" >
              </tbody>
		</table>
	</div>
		<?php
            if(!$_GET['ajax']){
        ?>
		<div id="loaderTorrent">
			<br /><center><h2><?php echo LANG_LOADING; ?> ...</h2></center><br />
		</div>
		<?php
			}
		?>

<script>
	<?php
			if(!$_GET['ajax'])
				echo 'refreshRss();';
		?>
</script>