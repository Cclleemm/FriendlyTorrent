<header class="top" >
	<p><?php echo $title ; ?></p>
</header>

<?php

$user = new User();

if($_GET['dir'] != NULL)
	$lien = explode('/', $_GET['dir']);
	
	
$_GET['dir'] = str_replace('../', '', $_GET['dir']);

  if($_GET['dir'] == NULL)
    $dir = $boxe['boxe'].'/';
  else
    $dir = $boxe['boxe']."/".$_GET['dir'];

  $d = opendir($dir);

  if($boxe['id'] != Core::idCo())
    $boxeLink = "users/".$boxe['login'].'/';
?>

<div class="row" style="margin-top:-15px;height:100%;" >
	<div id="profilCard" class="col-md-2" style="text-align:center;padding:10px;padding-top:50px;height:100%;" >
	    <img class="img-circle" src="<?php echo Tools::get_gravatar($boxe['mail']); ?>">
	    <h4><?php echo ucfirst($boxe['login']);?></h4>
	    <br /><br />
	    <div class="panel wrapper"> <div class="row">  <div class="col-xs-6"> <a href="#"> <span class="m-b-xs h4 block"><?php echo Tools::convertBoxe($uploadTotal); ?></span> <br /><small class="text-muted">Upload</small> </a> </div> <div class="col-xs-6"> <a href="#"> <span class="m-b-xs h4 block"><?php echo Tools::convertBoxe($downloadTotal); ?></span><br /> <small class="text-muted">Download</small> </a> </div> </div> </div>
	    <div class="panel wrapper"> <div class="row">  <div class="col-xs-6"> <a href="#"> <span class="m-b-xs h4 block"><?php echo Tools::convertBoxe($space); ?></span> <br /><small class="text-muted"><?php echo LANG_USE_ON_SERVER; ?></small> </a> </div> <div class="col-xs-6"> <a href="#"> <span class="m-b-xs h4 block"><?php if($pourcent > 7){ echo '<string style="color:#b94a48;font-weight:bold;" data-toggle="tooltip" data-placement="top" data-original-title="'.LANG_TO_MUCH_SPACE.'" >'; }echo $pourcent.'%'; if($pourcent > 10){ echo '</string>'; } ?></span><br /> <small class="text-muted"><?php echo LANG_OF_SERVER; ?></small> </a> </div> </div> </div>
	    
	    <?php
	    	if($boxe['id'] != Core::idCo()){
		    	echo '<a class="ajax" href="'.DOMAIN.'messagerie/chat/'.$boxe['id'].'"><button style="margin-top:-5px;" class="btn btn-success"><i></i>Chat</button></a>';
	    	}
	    ?>
	</div>
	<div class="col-md-10" style="border-left: 1px solid #dddddd;padding-top:20px;" >
		
<div class="sortable table-responsive">
		<table id="boxe" class="table table-vertical-center table-primary table-thead-simple table-responsive block">
			<thead>
                <tr>
                  <th axis="string" width="70%" ><?php echo LANG_NAME; ?></th>
                  <th width="10%" ><?php echo LANG_INFO; ?></th>
                  <th width="10%" ><?php echo LANG_TYPE; ?></th>
                  <th width="10%" ><?php echo LANG_SIZE; ?></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
               <?php
                $i = 0;
                $tab = array();
                
                  while($entry = readdir($d)) {
                    if(!in_array($entry, array(".","..", ".config"))){
                    
                      /*if($dir == $boxe['boxe']."/")
                    	$cat = Tools::defineCat(Tools::detectHD($entry) . Tools::detectV($entry) . Tools::detectType($entry));*/
                    
                      $entry = str_replace('.part', '', $entry);

                      $sql = "SELECT * FROM files WHERE link = '".addslashes($dir).addslashes($entry)."' AND idBoxe = '".$boxe['id']."';";
                      
                      $rst = mysql_query($sql);
                      $rslt = mysql_fetch_assoc($rst);

                      $sql45 = "SELECT id, name FROM torrents WHERE datapath = '".addslashes($entry)."' AND idBoxe = '".$boxe['id']."';";
                      $rst45 = mysql_query($sql45);
                      $rslt45 = mysql_fetch_assoc($rst45);
                      if($rslt45['id']){
	                      $stat = new StatFile(ROOT_DOWNLOADS.'.transferts/'.$rslt45['name']);
	                      if($stat->percent_done < 100 && $stat->percent_done >= 0 && $stat->time_left != 'verification'){
	                      	$dwn = true;
	                      	$stat->mtime = 9999999999;
	                      }else
	                      	$dwn = false;
                      }else{
	                      $dwn = false;
                      }
                      
                      $download = new Download(null, $rslt['id']);
                      
                      $tabName = explode('/', $download->downloadData['linkFile']);
					  $name = $tabName[count($tabName)-1];
                      
                      $html = '';
                      
                      if(is_dir($dir."/".$entry)) {
                      
                        if($_GET['id'])
                          $dossier = '?id='.$_GET['id'].'&dir='.$_GET['dir'].$entry;
                        else
                          $dossier = '?dir='.$_GET['dir'].$entry;

                        if($dwn){
                           $html .= '<tr type="DOSSIER" id="'.$rslt['id'].'" style="opacity:0.8;" ';
                           
                           if($rslt['time'] > $user->timeLastCloud($boxe['id']))
                                  $html .= ' class="success" ';
                           $html .= ' >
                                <td data-title="'.LANG_NAME.'" >';
                                    
                                    $html .= '<i class="icon-folder-open"></i> '.$entry.'';
                                    $html .= '</td><td data-title="'.LANG_INFO.'" > <span class="label label-info" data-toggle="tooltip" data-placement="top" data-original-title="'.LANG_DOWNLOADING_DESC.'">'.LANG_DOWNLOADING.'</label>';
                                $html .= '</td>
                                <td data-title="'.LANG_TYPE.'" >'.LANG_FOLDER.'</td>
                                <td data-title="'.LANG_SIZE.'" >'.Tools::convertFileSize($stat->size).'</td>
                                <td></td>
                              </tr>';
                        }else{
                           $html .= '<tr support="directDownload,link,';
                           if($boxe['id'] == Core::idCo())
                           	$html .= 'delete';
                           	
                           	$html .= '" type="DOSSIER" id="'.$rslt['id'].'" name="'.addslashes($name).'.zip" hash="'.$download->downloadData['clef'].'" ';
                           if($rslt['time'] > $user->timeLastCloud($boxe['id']))
                                  $html .= ' class="success selectable" ';
                           else
                           		$html .= ' class="selectable" ';
                           $html .= ' >
                                <td data-title="'.LANG_NAME.'" >';
                                    /*if($dir == $boxe['boxe']."/")
                                    	$html .= "<div class='categorie ".$cat."' ></div>";*/
                                $html .= '<a href="'.$dossier.'/" style="color:#000000;" ><i class="icon-folder-open"></i> <string>'.$entry.'</string></a></td><td data-title="'.LANG_INFO.'" >';
                                  if(!$download->ifDownloaded())
                                    $html .= ' <div class="label label-warning" data-toggle="tooltip" data-placement="top" data-original-title="'.LANG_NO_DOWNLOAD_DESC.'" >'.LANG_NO_DOWNLOADING.'</div>';
                                $html .= '</td>
                                <td data-title="'.LANG_TYPE.'" >Dossier</td>
                                <td data-title="'.LANG_SIZE.'" >'.Tools::convertFileSize(Tools::dirsize($dir."/".$entry)).'</td>
                                <td><icon class="glyphicon glyphicon-chevron-down" ></icon></td></tr>';
                        }

                       
                      }else{
                          $ext = explode('.', $entry);

                        if($dwn){
                          $html .= '<tr type="FICHIER" id="'.$rslt['id'].'" style="opacity:0.8;" ';
                           if($rslt['time'] > $user->timeLastCloud($boxe['id']))
                                  $html .= ' class="success" ';
                           $html .= ' > <td data-title="'.LANG_NAME.'" >';
                            $html .= '<i class="icon-file"></i> <string>'.$entry.'</string>';
                                    $html .= ' </td><td data-title="'.LANG_INFO.'" ><span class="label label-info" data-toggle="tooltip" data-placement="top" data-original-title="'.LANG_DOWNLOADING_DESC.'" >'.LANG_DOWNLOADING.'</label>';
                                $html .= '</td>
                                <td data-title="'.LANG_TYPE.'" >'.LANG_FILE.' <span class="label label-important">'.$ext[count($ext)-1].'</label></td>
                                <td data-title="'.LANG_SIZE.'" >'.Tools::convertFileSize($stat->size).'</td>
                                <td></td>
                          </tr>';
                        }else{
                         $html .= '<tr support="directDownload,link,';
                           if($boxe['id'] == Core::idCo())
                           	$html .= 'delete';
                           	
                           	$html .= '" type="FICHIER" id="'.$rslt['id'].'" name="'.addslashes($name).'" hash="'.$download->downloadData['clef'].'" ';
                           if($rslt['time'] > $user->timeLastCloud($boxe['id']))
                                  $html .= ' class="success selectable" ';
                           else
                           		$html .= ' class="selectable" ';
                           $html .= ' > <td data-title="'.LANG_NAME.'" >';
                                  /*if($dir == $boxe['boxe']."/")
                                    	$html .= "<div class='categorie ".$cat."' ></div>";*/
                            $html .= '<i class="icon-file"></i> <string>'.$entry.'</string></td><td data-title="'.LANG_INFO.'" >';
                                	if(!$download->ifDownloaded())
                                		$html .= ' <div class="label label-warning" data-toggle="tooltip" data-placement="top" data-original-title="'.LANG_NO_DOWNLOAD_DESC.'" >'.LANG_NO_DOWNLOAD.'</div>';
                                $html .= '</td>
                                <td data-title="'.LANG_TYPE.'" >'.LANG_FILE.' <span class="label label-info">'.$ext[count($ext)-1].'</label></td>
                                <td data-title="'.LANG_SIZE.'" >'.Tools::convertFileSize(filesize($dir."/".$entry)).'</td>
                                <td><icon class="glyphicon glyphicon-chevron-down" ></icon></td></tr>';
                        }
                      }
                      $stat = stat($dir."/".$entry);
                      $tab[] = array($stat['mtime'], $html);
                      $i++;
                    }
                  }
                  
                  function cmp($a, $b) {
					    if ($a[0] == $b[0]) {
					        return 0;
					    }
					    return ($a[0] < $b[0]) ? 1 : -1;
					}
                  
                  uasort($tab, 'cmp');
                  
                  foreach($tab as $key => $value){
	                  echo $value[1];
                  }
               ?>
              </tbody>
		</table>
	</div>
</div>


<script>
	/*jQuery.tablesorter.addParser({
            id: 'filesize', 
            is: function(s) { 
             return s.match(new RegExp( /[0-9]+(\.[0-9]+)?\ (Ko|O|Go|Mo|To)/ ));
            }, 
            format: function(s) {
            	
            	if(s.match(new RegExp( /(Ko|O|Go|Mo|To)$/ )) == null){
            		console.log(s);
					return 0;
            	}
            	
              var suf = s.match(new RegExp( /(Ko|O|Go|Mo|To)$/ ))[1];
              var num = parseFloat(s.match( new RegExp( /^[0-9]+(\.[0-9]+)?/ ))[0]);
              switch(suf) {
                case 'O':
                  return num;
                case 'Ko':
                  return num * 1024;
                case 'Mo':
                  return num * 1024 * 1024;
                case 'Go':
                  return num * 1024 * 1024 * 1024;
                case 'To':
                  return num * 1024 * 1024 * 1024 * 1024;
                }
            }, 
            type: 'numeric' 
          }); */
          
	$("#boxe").tablesorter();
	/*{
            headers: {
              3: { sorter: 'filesize' }
            }                                
          }*/

	 $(".selectable").bind("contextmenu",function(e){ return false; });
		 $(".selectable").mousedown(function(e) {
		  if((e.ctrlKey || e.metaKey) && (e.button == 0 || e.button == 1)) {
		  	$(this).addClass('active');
		  }else if(e.button == 2) {
		  	if($('#'+$(this).attr('id')+'').attr('class').indexOf("active") == -1){
		  	 	$('#'+$(this).attr('id')+'').addClass('active');
		  	 	$('.selectable:not(#'+$(this).attr('id')+')').removeClass('active');
		  	 	
		  	 	openContextMenu(this, e);
		  	}else{
		  		openContextMenu(this, e);
		  	}
		  }else{
		  	$('.selectable:not(#'+$(this).attr('id')+')').removeClass('active');
		  	$(this).addClass('active');
		  }
		  return false;
		});
		$(".glyphicon-chevron-down").click(function(e){
			openContextMenu($(this).parent().parent(), e);
			return false;
		})
</script>

<?php
  $user->setTimeLastCloud($boxe['id']);
?>