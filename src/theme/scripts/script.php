adresse = "<?php echo $_GET['domain']; ?>";
adresseTorrent = 'action/listeTorrent/';
inProgress = 0;
var listeTorrent = null;
    
function evalscript(str){
    splited=str.split("script>");
    for(i=0;i<splited.length;i++){

    if(!((i-1)%2)){

    lavar=splited[i];
    eval(lavar.substr(0, (lavar.length-2) ));

    }

    }
}

function seeNewUser(idUser){
	
	$.ajax({
	    type: "GET",
	      url: adresse+'action/seeNewUser/'+idUser+'/',
	      success: function(data) {
	      	evalscript(data);
	      }
	     });
}

function startRss(id){

  $.ajax({
	    type: "GET",
	      url: adresse+'action/startRss/'+id+'/',
	      success: function(data) {
	      	evalscript(data);
	      }
	     });
}

function nbNotif(){
  $.ajax({
    type: "GET",
      url: adresse+"action/nbNotif/",
      success: function(data) {
      var REP = eval('(' + data + ')');

      $("#nbMess").html(REP['mess']);

        if(REP['mess'] != 0 && $("#nbMess").attr('aff') == 0)
          $("#nbMess").fadeIn(function(){
            $("#nbMess").attr('aff', 1);
          });

        if(REP['mess'] == 0 && $("#nbMess").attr('aff') == 1)
          $("#nbMess").fadeOut(function(){
            $("#nbMess").attr('aff', 0);
          });

    }
  })
}

function action(action, id){

    var notice = $.pnotify({
        title: "En traitement ...",
        type: 'info',
        hide: false,
        closer: false,
        sticker: false,
        opacity: .75,
        shadow: false,
        width: "150px"
    });
    
    var total = $(".active").length;
    var pourcent = 0;
    var error = false;
    
    $(".active").each(function(){ 
    	idE = $(this).attr(''+id);
    	
    	$.ajax({
	    type: "GET",
	      url: adresse+action+idE+'/',
	      success: function(data) {
	      	evalscript(data);
	      	pourcent += 100/total;
	      	var options = {
	            text: Math.round(pourcent) + "% éffectué."
	        };
	        notice.pnotify(options);
	      }
	     }).fail(function() {
		    error = true;
		  });
    });
    
    var options = new Array();
    
    if(error){
    	options.title = "Terminé avec une/des erreurs";
		options.type = "error";
    }else{
    	options.title = "Terminé";
		options.type = "success";
    }
    
    options.hide = true;
    options.closer = true;
    options.sticker = true;
    options.opacity = 1;
    options.shadow = true;

	notice.pnotify(options);

}

function debut(chaine, nbr){
  if (chaine.length>nbr){ affiche=chaine.substr(0,nbr-3)+" ...";}else{affiche=chaine;}
  return affiche;
}


function affNotice(text){
	$.pnotify({
	    title: 'Liens',
	    text: '<textarea style="width:500px;height:100px;" >'+text+'</textarea>',
	    hide: false,
	    sticker: false,
	    width: '550px',
	    type: 'info'
	});
}

function confirmation(title, desc, eff){
	
	$('#modal .modal-title').html(title);
	$('#modal .modal-body').html(desc);
	$('#modal #OK').attr('onclick', eff+';$(\'#modal\').modal(\'hide\');');
	$('#modal').modal('show');
	
}

function liens(){
	
	hashs = "";
	$(".active").each(function(){ 
		hashs = hashs+adresse+'downloads/'+$(this).attr('hash')+"/"+$(this).attr('name')+"\r";
	});	
	
	$('#liens .modal-body .contenu').val(hashs);
	$('#liens').modal('show');
	
}

function array_intersect (arr1) {
  // http://kevin.vanzonneveld.net
  // +   original by: Brett Zamir (http://brett-zamir.me)
  // %        note 1: These only output associative arrays (would need to be
  // %        note 1: all numeric and counting from zero to be numeric)
  // *     example 1: $array1 = {'a' : 'green', 0:'red', 1: 'blue'};
  // *     example 1: $array2 = {'b' : 'green', 0:'yellow', 1:'red'};
  // *     example 1: $array3 = ['green', 'red'];
  // *     example 1: $result = array_intersect($array1, $array2, $array3);
  // *     returns 1: {0: 'red', a: 'green'}
  var retArr = {},
    argl = arguments.length,
    arglm1 = argl - 1,
    k1 = '',
    arr = {},
    i = 0,
    k = '';

  arr1keys: for (k1 in arr1) {
    arrs: for (i = 1; i < argl; i++) {
      arr = arguments[i];
      for (k in arr) {
        if (arr[k] === arr1[k1]) {
          if (i === arglm1) {
            retArr[k1] = arr1[k1];
          }
          // If the innermost loop always leads at least once to an equal value, continue the loop until done
          continue arrs;
        }
      }
      // If it reaches here, it wasn't found in at least one array, so try next value
      continue arr1keys;
    }
  }

  return retArr;
}

function openContextMenu(a, e){
	
	x = e.pageX -160 ;
	y = e.pageY -5;
	
		
	ancien = new Array();
	first = true;
	
	$(".active").each(function(){ 
		support = $(this).attr('support').split(',');
		if(first){
			ancien = support;
			first = false;
		}else{
			ancien = array_intersect(ancien, support);
		}
	});
	
	console.log(ancien);
	
	html = '<div class="dropdown"><ul class="dropdown-menu" role="menu" style="display:block;" aria-labelledby="dropdownMenu1">';
	
	for(var t in ancien)
		{	
			if(ancien[t] == 'startTorrent'){
				html += '<li role="presentation"><a onclick="action(\'action/start/\', \'idT\');$(\'#context\').hide();return false;" role="menuitem" tabindex="-1" href="#">Lancer</a></li>';
			}
			
			if(ancien[t] == 'stopTorrent'){
				html += '<li role="presentation"><a onclick="action(\'action/stop/\', \'idT\');$(\'#context\').hide();return false;" role="menuitem" tabindex="-1" href="#">Arrêter</a></li>';
			}
			
			if(ancien[t] == 'deleteTorrent'){
				html += '<li role="presentation"><a onclick="confirmation(\'Suppression de Torrent\', \'Voulez vous vraiment supprimer le/les torrent(s) séléctionné(s) ?\', \'action(\\\'action/sup/\\\', \\\'idT\\\')\');$(\'#context\').hide();return false;" role="menuitem" tabindex="-1" href="#">Supprimer</a></li>';
			}
			
			if(ancien[t] == 'directDownload' && $(".active").length == 1){
				id = $(a).attr('hash');
				name = $(a).attr('name');
				html += '<li role="presentation"><a onclick="$(\'#context\').hide();" target="_BLANK" role="menuitem" tabindex="-1" href="'+adresse+'downloads/'+id+'/'+name+'">Télécharger</a></li>';
			}
			if(ancien[t] == 'delete'){
				html += '<li role="presentation"><a onclick="confirmation(\'Suppression de Fichier\', \'Voulez vous vraiment supprimer le/les fichier(s) séléctionné(s) ?\', \'action(\\\'action/delete/\\\', \\\'id\\\')\');$(\'#context\').hide();return false;" role="menuitem" tabindex="-1" href="#">Supprimer</a></li>';
			}
			if(ancien[t] == 'link'){
				html += '<li role="presentation"><a role="menuitem" tabindex="-1" onclick="liens();" href="#">Lien</a></li>';
			}
		}
			
	
	html += ' </ul></div>';
	$('#context').html(html).show();
	$("#context").css('top', y).css('left', x);

	return false;
}

function refreshPanel(){

	$("#panelRight").html('<header><h3 class="main-header">Aucun torrent sélectionné</h3></header>');
	
	if($(".active").length == 1){	
		i = $(".active").attr('i');
		var REPP = listeTorrent[i];
		
		statut = REPP.statut
		sharing = REPP.sharing
		
		if(statut == "Envoi"){
	        color = "#029ec6";
	        REPP.percent_done = sharing;
	    }
	    
			pourcent = REPP.percent_done
	    
        if(statut == "Fini")
          color = "#90d000";
        if(statut == "Arrêté"){
          color = "#af1d00";
          percent_done = 0;
        }
        if(statut == "Téléchargement")
          color = "#54709f";
        if(statut == "Erreur"){
          color = "#af1d00";
          percent_done = 0;
        }

        if(statut == "Vérification")
          color = "#90d000";
		
			$("#panelRight").html('<header><h3 class="main-header">'+debut(listeTorrent[i]['nameTorrent'], 30)+'</h3></header><table class="table" style="margin:0px;background-color:white;"><tbody><tr><td>Avancement</td><td>'+pourcent+'%</td></tr><tr><td>Taille</td><td>'+REPP.size+'</td></tr><tr><td>Téléchargé</td><td>'+REPP.downtotal+'</td></tr><tr><td>Envoyé</td><td>'+REPP.uptotal+'</td></tr><tr><td>Téléchargement</td><td><font color="#54709f" >de <b>'+REPP.seeds+'</b> peers</font></td></tr> <tr><td>Envoi</td><td><font color="#029ec6" >à <b>'+REPP.peers+'</b> peers</font></td></tr></tbody></table><header><h3 class="main-header">Peers connectés ('+REPP['peersList'].length+')</h3></header><table class="table block" style="background-color:white;"><thead><th style="width:30%;" >DL</th><th style="width:30%;">UP</th><th style="width:40%;">IP</th></thead><tbody id="listPeers" ></tbody></table>');

		
		listPeers = REPP['peersList'];
		
		for(var i in listPeers)
		{
			$("#listPeers").append("<tr><td>"+Math.round((listPeers[i]['rateToClient'])/1024)+" kb/s</td><td>"+Math.round((listPeers[i]['rateToPeer'])/1024)+" kb/s</td><td>"+listPeers[i]['address']+"</td></tr>");
		
		}
	}else if($(".active").length > 1){
		$("#panelRight").html('<header><h3 class="main-header">Plusieurs torrents séléctionné !</h3></header>');
	}
}

function refreshTorrent(){
  if(inProgress == 1)
    return false;
  else
    inProgress = 1;

  $.ajax({
    type: "GET",
      url: adresse+adresseTorrent,
      success: function(data) {
      var REP = eval('(' + data + ')');
	  listeTorrent = REP;
	  
      html = '';

      for(var i in REP)
      {
        statut = '';

        REP[i] = REP[i].torrentData
        id = REP[i].id
        name = REP[i].nameTorrent
        running = REP[i].running
        percent_done = REP[i].percent_done
        time_left = REP[i].time_left
        down_speed = REP[i].down_speed
        up_speed = REP[i].up_speed
        sharing = REP[i].sharing
        uptotal = REP[i].uptotal
        downtotal = REP[i].downtotal
        size = REP[i].size
        statut = REP[i].statut
        login = REP[i].login
        seeds = REP[i].seeds
        peers = REP[i].peers
        cons = REP[i].cons
        time = REP[i].time
        
        if(peers == '')
        	peers = 0;
        	
        if(seeds == '')
        	seeds = 0;

        if(statut == "Envoi"){
	        color = "success";
	        percent_done = sharing;
	    }
        if(statut == "Fini")
          color = "success";
        if(statut == "Arrêté"){
          color = "warning";
          percent_done = 0;
        }
        if(statut == "Téléchargement")
          color = "info";
        if(statut == "Erreur"){
          color = "danger";
          percent_done = 0;
        }

        if(statut == "Vérification")
          color = "warning";

          pourcent = percent_done
          
        if(pourcent > 100)
        	pourcent_bar = 100;
        else
        	pourcent_bar = pourcent;
        	
        	check = $('#torrent'+id).attr('class');
        	$('#torrent'+id).remove();
        	
        	if(!check){
        		check = 'selectable';
        	}
        		
        		if(down_speed == '&nbsp;')
        			down_speed = '0 B/s';
        			
        		if(up_speed == '&nbsp;')
        			up_speed = '0 B/s';
        			
        	nameTorrent = name;
		            
		   html += '<div i="'+i+'" class="list-group-item '+check+'" idT="'+id+'" id="torrent'+id+'" ';
		              if(running == 0 || running == -2)
		                html += 'style="opacity:0.8;" ';
		              if(running == 1)
		              	html +=" support='deleteTorrent,stopTorrent' ";
		              else
		              	html +=" support='deleteTorrent,startTorrent' ";
		            html += '>';
				 	html += '	<h5 class="list-group-item-heading">'+nameTorrent+'<span class="label label-'+color+' pull-right">'+statut+'</span></h5>';
				 	html += '	<p class="list-group-item-text">';
				 	html += '<span class="glyphicon glyphicon-arrow-up"></span> '+up_speed+' <span class="glyphicon glyphicon-arrow-down"></span> '+down_speed+' <span class="pull-right" >'+time_left+'</span>';
				 	html += '<div class="progress" style="margin-bottom:0px;" ><div class="progress-bar progress-bar-'+color+'" style="width: '+pourcent_bar+'%;"></div></div><p>';
				 	if(statut == "Envoi"){
						html += size+', '+uptotal+' envoyé (Ratio: '+Math.round(pourcent)/100+')';
					}		
					if(statut == "Fini" || statut == "Arrêté" || statut == "Vérification")
						html += size;
					if(statut == "Téléchargement"){
						html += size+', '+downtotal+' téléchargé';
					}	
				 	html += '</p>';
				 	html += '</div>';
        
      }
      
	  $('#listeTorrent').html('');
	  $('#listeTorrent').append(html);
	  $('#loaderTorrent').hide();
	  
	  refreshPanel();
	  
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
		  refreshPanel();
		  return false;
		});
	  
      inProgress = 0;
    }
  })
}

function refreshRss(){
  if(inProgress == 1)
    return false;
  else
    inProgress = 1;

  $.ajax({
    type: "GET",
      url: adresse+"action/listeRss/",
      success: function(data) {
      var REP = eval('(' + data + ')');

      $('#listeTorrent').html("");

      html = '';

      for(var i in REP)
      {
        id = REP[i].id
        name = REP[i].name[0]
        etat = REP[i].etat
        isDwn = REP[i].isDwn

        if($("#torrent"+id).html() == null){
            html += '<tr id="torrent'+id+'" ';
            html += '>';
            html += '<td><b>'+debut(name, 80)+'</b></td>';
            html += '<td>'+etat+'</td>';
              if(isDwn == 1)
                html += '<td><a href="#" onclick="startRss('+id+'); return false;" class="glyphicon glyphicon-plus-sign"><i></i> </a></td>';
              else
                html += '<td>-</td>';

            html += '</tr>';
        }
      }
      
	      $('#listeTorrent').html(html);
	      $('#loaderTorrent').hide();
	      
      inProgress = 0;
    }
  })
}

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function majDebit(){
      $.ajax({
        url: adresse+'action/stats',
        success: function(data) {

            a = data.split('|');
            
            if(parseInt(a[0]) >= 1)
            	html = a[0]+' Mo/s';
            else
            	html = (a[0]*1024)+' Ko/s';
            
            $("#downInst").html(html);
            
            if(parseInt(a[1]) >= 1)
            	html = a[1]+' Mo/s';
            else
            	html = (a[1]*1024)+' Ko/s';
            	
            $("#upInst").html(html);

        }
      });
    }
    
function seeBulleSpace(){
	$.ajax({
    type: "GET",
      url: adresse+'action/bulleSpace/',
      success: function(data) {}
     });

}

function seeBulleAbo(){
	$.ajax({
    type: "GET",
      url: adresse+'action/bulleAbo/',
      success: function(data) {}
     });

}
