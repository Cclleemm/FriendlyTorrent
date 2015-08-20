<header class="top" >
	<p><?php echo $title ; ?></p>
</header>

<br />
<div class="panel wrapper">
	<div class="row text-center">
		<div class="col-md-2">
			<p class="h3 font-bold m-t" style="margin-top:5px;" ><?php echo $totalOff; ?></p> <p style="margin-bottom:0px;" class="text-muted"><?php echo LANG_NETWORK; ?></p>
		</div>
		<div class="col-md-2">
			<p class="h3 font-bold m-t" style="margin-top:5px;"><?php echo $me; ?></p> <p style="margin-bottom:0px;" class="text-muted"><?php echo LANG_MY_NETWORK; ?></p>
		</div>
		<div class="col-md-2">
			<p class="h3 font-bold m-t" style="margin-top:5px;"><?php echo Tools::convertBoxe($uploadTotal); ?></p> <p style="margin-bottom:0px;" class="text-muted">Upload <?php echo LANG_THIS_MONTH; ?></p>
		</div>
		<div class="col-md-2">
			<p class="h3 font-bold m-t" style="margin-top:5px;"><?php echo Tools::convertBoxe($downloadTotal); ?></p> <p style="margin-bottom:0px;" class="text-muted">Download  <?php echo LANG_THIS_MONTH; ?></p>
		</div>
		<div class="col-md-2">
			<p class="h3 font-bold m-t" style="margin-top:5px;" id="upInst" >-</p> <p style="margin-bottom:0px;" class="text-muted">Upload live</p>
		</div>
		<div class="col-md-2">
			<p class="h3 font-bold m-t" style="margin-top:5px;" id="downInst" >-</p> <p style="margin-bottom:0px;" class="text-muted">Download live</p>
		</div>
	</div>
</div>
<br />

<div class="row">
	<div class="col-md-12">
		<div class="widget">
			<div class="widget-head">
				Utilisation de l'upload ce mois
			</div>
			<div class="widget-body">
				<div id="chart_ordered_bars" style="height: 280px;"></div>
			</div>
		</div>			
	</div>
	<div class="col-md-12">
		<div class="widget">
			<div class="widget-head">
				Utilisation du download ce mois
			</div>
			<div class="widget-body">
				<div id="chart_ordered_barsDown" style="height: 280px;"></div>
			</div>
		</div>	
	</div>
	<div class="col-md-12">
		
						<?php
			if($space <= 15)
				echo '<div class="alert alert-warning">
	<strong>Attention !</strong> Reste moins de 15% d\'espace disponible : '.$free.' restant !
</div>';
		?>
		
		<div class="widget">
			<div class="widget-head">
				Espace Disque
			</div>
			<div class="widget-body">
				<div class="progress">
					<div style="color:#fff;width:90%;text-align:center;"><?php echo Tools::convertFileSize($usedspace)." / ".Tools::convertFileSize($totalspace); ?></div>
					<div class="progress-bar" style="width: <?php echo $pourcent; ?>%;margin-top:-20px;"></div>
				</div>
				<br>
				<div id="chart_donut" style="height: 350px;"></div>
			</div>
		</div>
	</div>
</div>

<!-- <div class="row">
	<div class="col-md-8">	

		<?php
			if($space <= 15)
				echo '<div class="alert alert-primary">
			<strong>Attention !</strong> Reste moins de 15% d\'espace disponible : '.$free.' restant !
	</div>';
		?>
		
		<div class="row">
			<div class="col-md-6">
				
				<ul class="list-group">
				  <li class="list-group-item"><span class="glyphicon glyphicon-transfer"></span> Transferts <span style="float:right;"><?php echo $totalOff; ?></span></li>
				  <li class="list-group-item"><span class="glyphicon glyphicon-user"></span> Mes transferts <span style="float:right;"><?php echo $me; ?></span></li>
				  <li class="list-group-item"><span class="glyphicon glyphicon-cloud-upload"></span> Upload ce mois <span style="float:right;"><?php echo Tools::convertBoxe($uploadTotal); ?></span></li>
				  <li class="list-group-item"><span class="glyphicon glyphicon-cloud-download"></span> Download ce mois <span style="float:right;"><?php echo Tools::convertBoxe($downloadTotal); ?></span></li>
				</ul>

			
						      <?php
									while($rslt = mysql_fetch_assoc($connect)){
										echo '<a style="margin-top:5px;width:10%;float:left;" href="'.WEBROOT.'boxe/users/'.$rslt['login'].'/" >
      <img data-toggle="tooltip" data-placement="top" data-original-title="'.ucfirst($rslt['login']).' : '.Tools::date_fr_texte($rslt['lastTime']).'" class="img-circle" src="'.Tools::get_gravatar($rslt['mail']).'">
    </a>';
									}
								?>
						    <p style="clear:both;"></p><br />

				<ul class="list-group">
				
					<?php
						$sql = "SELECT * FROM users WHERE id != '".Core::idCo()."';";
						$rst = $this->bdd->query($sql);
						
						while($rslt = mysql_fetch_assoc($rst)){
							$sql2 = "SELECT * FROM torrents WHERE idBoxe = '".$rslt['id']."' AND time >= '".$this->user->timeLastCloud($rslt['id'])."';";
							$rst2 = mysql_query($sql2);
							
							$i = 0;
							$text = "";
							while($rslt2 = mysql_fetch_assoc($rst2)){
								$i++;
								
								$tab = explode('-', $rslt2['name']);
								//print_r($tab);
								$name = '';
								for($t = 0; $t < count($tab)-1; $t++){
									$name .= $tab[$t].'-';
								}
								
								$name = substr($name,0, -1);
			
								$text .= '<li class=\'list-group-item\'>'.Tools::debutchaine($name, 30)."</li>";
							}
							
							if($i > 0){
								echo '<li id="new'.$rslt['id'].'" class="list-group-item"><span class="glyphicon glyphicon-user"></span> <a href="'.WEBROOT.'boxe/users/'.$rslt['login'].'/" >'.$rslt['login'].'</a> <span style="float:right;"><button style="margin-top:-10px;width:40px;border-top-left-radius:0px;border-top-right-radius:0px;" type="button" data-html="true" class="btn btn-info popover-btn" data-container="body" data-placement="left" data-content="<ul style=\'margin-bottom:0px;\' class=\'list-group\'>'.$text.'</ul>" data-original-title="'.$i.' nouveau(x) torrent(s) <button type=\'button\' class=\'btn btn-success pull-right\' style=\'margin-top:-9px;padding:5px;border-top-left-radius:0px;border-top-right-radius:0px;\' onclick=\'seeNewUser('.$rslt['id'].')\'><i class=\'glyphicon glyphicon-eye-open\' ></i></button>" title="">
          '.$i.'
        </button></span></li>';
							}
						}
					?>
				</ul>
			</div>
			<div class="col-md-6">
				<div class="widget">
					<div class="widget-head">
						Utilisation de l'upload ce mois
					</div>
					<div class="widget-body">
						<div id="chart_ordered_bars" style="height: 250px;"></div>
					</div>
				</div>
				
				<div class="widget">
					<div class="widget-head">
						Utilisation du download ce mois
					</div>
					<div class="widget-body">
						<div id="chart_ordered_barsDown" style="height: 250px;"></div>
					</div>
				</div>
			</div>
		</div>
		
	</div>
	<div class="col-md-4">
		<div class="widget">
					<div class="widget-head">
						Débits
					</div>
					<div class="widget-body">
					<br />
							<span class="glyphicon glyphicon-cloud-upload" data-toggle="tooltip" data-placement="top" data-original-title="Upload"></span>
							<div class="progress" style="margin-left:30px;margin-top:-15px;" >
								<div class="progress-bar" id="debitUp" style="width: 0%;" ></div>
								<span style="padding-left:10px;" ><span id="debitAffUp" >-</span></span>
							</div>
							<span class="glyphicon glyphicon-cloud-download" data-toggle="tooltip" data-placement="top" data-original-title="Download" ></span>
							<div class="progress" style="margin-left:30px;margin-top:-15px;" >
								<div class="progress-bar" id="debit" style="width: 0%;"></div>
								<span style="padding-left:10px;" ><span id="debitAff" >-</span></span>
							</div>
					</div>
				</div>
				
		<?php
			if($etape['valeur'] == 'questionnaire'){
				echo '<div class="widget">
			<div class="widget-head">
				Abonnement
			</div>
			<div class="widget-body">
				';
				if($going == 1 && $active)
				   echo '<h5>Renouveler pour le mois suivant ?</h5>';
				else
					echo '<h5>S\’abonner pour le mois suivant ?</h5>';
				
				 echo '  <center><a href="'.WEBROOT.'action/setGoing/2/" ><button type="button" class="btn btn-danger">NON</button></a>
				   <a href="'.WEBROOT.'action/setGoing/1/" ><button type="button" class="btn btn-success">OUI</button></a></center>
				   
				   <br />';

					   echo '	<div class="progress">
							<div class="progress-bar progress-bar-success" data-toggle="tooltip" data-placement="top" data-original-title="'.$statsPayment['oui'].' personne(s)" style="overflow:hidden;width: '.(($statsPayment['oui']/$statsPayment['total'])*100).'%;">OUI</div>
							<div class="progress-bar progress-bar-danger" data-toggle="tooltip" data-placement="top" data-original-title="'.$statsPayment['non'].' personne(s)" style="overflow:hidden;width: '.(($statsPayment['non']/$statsPayment['total'])*100).'%;">NON</div>
							<div class="progress-bar" data-toggle="tooltip" data-placement="top" data-original-title="'.$statsPayment['nouveau'].' personne(s)" style="overflow:hidden;width: '.(($statsPayment['nouveau']/$statsPayment['total'])*100).'%;">Nouvelles</div>
						</div>';
						
						if($going == 1 && $active)
					   	echo '<h6><font color="green" >Vous avez renouvelé pour le mois suivant.</font></h6>';
					   else if($going == 1 && !$active)
					   	echo '<h6><font color="#029ec6" >Vous vous êtes abonné pour le mois prochain !</font></h6>';
					   else if($going == 2)
					   	echo '<h6><font color="red" >Vous n\'avez pas renouvelé pour le mois suivant.</font></h6>';
					   	else {
						  	echo '<h6><font color="red" >Date d\'échéance : '.Tools::date($etape['echeance'], 'fr').'</font></h6>';
					   	}
					   	echo '
				    <p style="clear:both;"></p>
			</div>
		</div>';
			}
			
			if($etape['valeur'] == 'payment' && $going == 1){
				echo '<div class="widget">
			<div class="widget-head">
				Abonnement
			</div>
			<div class="widget-body">
					';
					
					if($going == 1){
				   echo '<h4>Veuillez faire parvenir : <b>'.$prixNextMonth.' euros</b></h4>';
				  
					   	if($activeNextMonth == 1)
					   		echo '<h6><font color="green" >Paiement effectué !</font></h6>';
					   	else
						  	echo '<h6><font color="red" >En attente de paiement !<br />Date d\'échéance : '.Tools::date($etape['echeance'], 'fr').'</font></h6><div class="separator line"></div> <b>Paypal</b> : fbn.impakt@gmail.com<br />
						<b>Motif</b> : <small><i>J\'envoie de l\'argent à ma famille ou à mes amis</i></small>
				    <p style="clear:both;"></p>';
					   	
					}else{
						echo '<h6><font color="red" >Vous n\'avez pas renouvelé pour le mois suivant !</font></h6>';
					}
						echo '
			</div>
		</div>';
			}
		?>
		
		<div class="widget">
					<div class="widget-head">
						Espace Disque
					</div>
					<div class="widget-body">
						<div class="progress">
							<div style="color:#fff;width:90%;text-align:center;"><?php echo Tools::convertFileSize($usedspace)." / ".Tools::convertFileSize($totalspace); ?></div>
							<div class="progress-bar" style="width: <?php echo $pourcent; ?>%;margin-top:-20px;"></div>
						</div>
						<div id="chart_donut" style="height: 250px;"></div>
					</div>
				</div>
	</div>
</div>	
</div>	 -->



<script>
	var charts = 
	{
		// init all charts
		init: function()
		{

		$.ajax({
		    type: "GET",
		      url: adresse+"action/space/",
		      success: function(data) {
		      var REP = eval('(' + data + ')');

		      charts.utility.chartColors = REP['colors'];

		      REP = REP['space'];

		      for(var i in REP)
		      {
		        charts.chart_donut.data.push(REP[i]);
		      }

			  	// init donut chart
				charts.chart_donut.init();
		    }
		  })
		  
		  $.ajax({
				    type: "GET",
				      url: adresse+"action/useBand/",
				      success: function(data) {
				      var REP = eval('(' + data + ')');
		
				      up = REP['up'];
				      down = REP['down'];
					  
					  	
					  	charts.utility.chartColors = REP['colors'];
						charts.chart_ordered_bars.data = up;
						charts.chart_ordered_bars.dataDown = down;
						charts.chart_ordered_bars.init();
		
				    }
				  })
		},

		// utility class
		utility:
		{
			chartColors: [ "#029ec6", "#ed5ae8", "#54709f", "#a51f80", "#484848", "#585858" ],
			chartBackgroundColors: [ "#FFFFFF", "#FFFFFF" ],

			applyStyle: function(that)
			{
				that.options.colors = charts.utility.chartColors;
				that.options.grid.backgroundColor = { colors: charts.utility.chartBackgroundColors };
				that.options.grid.borderColor = charts.utility.chartColors[0];
				that.options.grid.color = "#222";
			},
		},			
		
		// donut chart
		chart_donut:
		{
			// chart data
			data: [],

			// will hold the chart object
			plot: null,

			// chart options
			options: 
			{
				series: {
					pie: { 
						show: true,
						innerRadius: 0.4,
						highlight: {
							opacity: 0.1
						},
						radius: 1,
						stroke: {
							color: '#2c2c2d',
							width: 1
						},
						startAngle: 2,
					    combine: {
		                    color: '#686868',
		                    threshold: 0.05
		                },
		                label: {
		                    show: true,
		                    radius: 1,
		                    formatter: function(label, series){
		                        return '<div class="label label-primary">'+label+'&nbsp;'+Math.round(series.percent)+'%</div>';
		                    }
		                }
					}
				},
				legend:{ show:false },
				grid: {
		            hoverable: true,
		            clickable: true,
		            backgroundColor : { }
		        },
		        colors: []
			},
			
			// initialize
			init: function()
			{	
				// apply styling
				charts.utility.applyStyle(this);
				
				this.plot = $.plot($("#chart_donut"), this.data, this.options);
			}
		},
		// ordered bars chart
		chart_ordered_bars:
		{
			// chart data
			data: null,
			dataDown: null,

			// will hold the chart object
			plot: null,

			// chart options
			options:
			{
				xaxis: {
					mode: "time",
					minTickSize: [1, "month"]
				},
				grid: {
					show: true,
				    aboveData: false,
				    color: "#029ec6" ,
				    labelMargin: 5,
				    axisMargin: 0, 
				    borderWidth: 0,
				    borderColor:null,
				    minBorderMargin: 5 ,
				    clickable: true, 
				    hoverable: true,
				    autoHighlight: false,
				    mouseActiveRadius: 20,
				    backgroundColor : { }
				},
		        series: {
		        	grow: {active: false},
		            lines: {
	            		show: true,
	            		fill: false,
	            		lineWidth: 4,
	            		steps: false
	            	},
		            points: {
		            	show:true,
		            	radius: 5,
		            	symbol: "circle",
		            	fill: true,
		            	borderColor: "#fff"
		            }
		        },
		        legend: { 
					show: false
				},
		        colors: [],
		        tooltip: true,
				tooltipOpts: {
					content: "%s : %y.0 Go",
					shifts: {
						x: -30,
						y: -50
					}
				}
			},

			// initialize
			init: function()
			{
				// apply styling
				charts.utility.applyStyle(this);
				this.plot = $.plot("#chart_ordered_bars", this.data, this.options);
				$.plot("#chart_ordered_barsDown", this.dataDown, this.options);
			}
		}
	};

	$(function()
	{
		// initialize charts
		setTimeout(function(){
			charts.init();
			
		}, 500);
	});

	$("#flecheAccueil").fadeIn();
	
	majDebit();
	clearInterval(seedbox.interval);
	seedbox.interval = setInterval("majDebit()", 2000);
</script>