<div id="panelRight" class="hidden-xs" style="display:block;overflow:scroll ">
	
</div>

<header id="hTorrent" class="top" >
	<input style="display:none;" id="file_upload" name="file_upload" type="file" multiple="true">
	<p><?php echo $title ; ?></p>
</header>

<div id="lTorrent" >

	<div id="alert" ></div>
	<div id="queue"></div>
	<br />
		
		<div class="list-group" id="listeTorrent" >
           
		</div>

		<div id="loaderTorrent">
			<br /><center><h2> <?php echo LANG_LOADING; ?> ...</h2></center><br />
		</div>


<script>
		adresseTorrent = 'action/listeTorrent/';
	 	seedbox.interval = setInterval("refreshTorrent()", 3000);
	 	refreshTorrent();
	 	setTimeout('loadUpload()', 500);
	 	
</script>
</div>
