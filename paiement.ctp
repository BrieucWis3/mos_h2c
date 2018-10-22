    <?php //debug($query); ?>
    <script type="text/javascript">
	/*Example message arrays for the two demo scrollers*/
	var pausecontent=new Array()
	pausecontent[0]='<a style="color: red;font-size: 21px; font-weight: bold;">ATTENTION :</a><br /><a style="color: red;font-size: 19px;">Après votre paiement, et pour obtenir une copie de votre ticket de paiement, pensez à revenir dans votre espace en utilisant le bouton "RETOUR A LA BOUTIQUE"</a>'
	pausecontent[1]='<a style="color: red;font-size: 21px; font-weight: bold;">ATTENTION :</a><br /><a style="color: red;font-size: 19px;">Après votre paiement, et pour obtenir une copie de votre ticket de paiement, pensez à revenir dans votre espace en utilisant le bouton "RETOUR A LA BOUTIQUE"</a>'
	pausecontent[2]='<a style="color: red;font-size: 21px; font-weight: bold;">ATTENTION :</a><br /><a style="color: red;font-size: 19px;">Après votre paiement, et pour obtenir une copie de votre ticket de paiement, pensez à revenir dans votre espace en utilisant le bouton "RETOUR A LA BOUTIQUE"</a>'
	/*pausecontent[1]='<a href="http://www.codingforums.com">Coding Forums</a><br />Web coding and development forums.'
	pausecontent[2]='<a href="http://www.cssdrive.com" target="_new">CSS Drive</a><br />Categorized CSS gallery and examples.'*/

	/*var pausecontent2=new Array()
	pausecontent2[0]='<a href="http://www.news.com">News.com: Technology and business reports</a>'
	pausecontent2[1]='<a href="http://www.cnn.com">CNN: Headline and breaking news 24/7</a>'
	pausecontent2[2]='<a href="http://news.bbc.co.uk">BBC News: UK and international news</a>'*/
</script>

<script type="text/javascript">
	/***********************************************
	* Pausing up-down scroller- (c) Dynamic Drive (www.dynamicdrive.com)
	* Please keep this notice intact
	* Visit http://www.dynamicdrive.com/ for this script and 100s more.
	***********************************************/

	function pausescroller(content, divId, divClass, delay){
		this.content=content //message array content
		this.tickerid=divId //ID of ticker div to display information
		this.delay=delay //Delay between msg change, in miliseconds.
		this.mouseoverBol=0 //Boolean to indicate whether mouse is currently over scroller (and pause it if it is)
		this.hiddendivpointer=1 //index of message array for hidden div
		document.write('<div id="'+divId+'" class="'+divClass+'" style="position: relative; overflow: hidden"><div class="innerDiv" style="width: 100%" id="'+divId+'1">'+content[0]+'</div><div class="innerDiv" style="position: absolute; width: 100%; visibility: hidden" id="'+divId+'2">'+content[1]+'</div></div>')
		var scrollerinstance=this
		if (window.addEventListener) //run onload in DOM2 browsers
		window.addEventListener("load", function(){scrollerinstance.initialize()}, false)
		else if (window.attachEvent) //run onload in IE5.5+
		window.attachEvent("onload", function(){scrollerinstance.initialize()})
		else if (document.getElementById) //if legacy DOM browsers, just start scroller after 0.5 sec
		setTimeout(function(){scrollerinstance.initialize()}, 500)
	}

	// -------------------------------------------------------------------
	// initialize()- Initialize scroller method.
	// -Get div objects, set initial positions, start up down animation
	// -------------------------------------------------------------------

	pausescroller.prototype.initialize=function(){
		this.tickerdiv=document.getElementById(this.tickerid)
		this.visiblediv=document.getElementById(this.tickerid+"1")
		this.hiddendiv=document.getElementById(this.tickerid+"2")
		this.visibledivtop=parseInt(pausescroller.getCSSpadding(this.tickerdiv))
		//set width of inner DIVs to outer DIV's width minus padding (padding assumed to be top padding x 2)
		this.visiblediv.style.width=this.hiddendiv.style.width=this.tickerdiv.offsetWidth-(this.visibledivtop*2)+"px"
		this.getinline(this.visiblediv, this.hiddendiv)
		this.hiddendiv.style.visibility="visible"
		var scrollerinstance=this
		document.getElementById(this.tickerid).onmouseover=function(){scrollerinstance.mouseoverBol=1}
		document.getElementById(this.tickerid).onmouseout=function(){scrollerinstance.mouseoverBol=0}
		if (window.attachEvent) //Clean up loose references in IE
		window.attachEvent("onunload", function(){scrollerinstance.tickerdiv.onmouseover=scrollerinstance.tickerdiv.onmouseout=null})
		setTimeout(function(){scrollerinstance.animateup()}, this.delay)
	}

	// -------------------------------------------------------------------
	// animateup()- Move the two inner divs of the scroller up and in sync
	// -------------------------------------------------------------------

	pausescroller.prototype.animateup=function(){
		var scrollerinstance=this
		if (parseInt(this.hiddendiv.style.top)>(this.visibledivtop+5)){
			this.visiblediv.style.top=parseInt(this.visiblediv.style.top)-5+"px"
			this.hiddendiv.style.top=parseInt(this.hiddendiv.style.top)-5+"px"
			setTimeout(function(){scrollerinstance.animateup()}, 50)
		}
		else{
			this.getinline(this.hiddendiv, this.visiblediv)
			this.swapdivs()
			setTimeout(function(){scrollerinstance.setmessage()}, this.delay)
		}
	}

	// -------------------------------------------------------------------
	// swapdivs()- Swap between which is the visible and which is the hidden div
	// -------------------------------------------------------------------

	pausescroller.prototype.swapdivs=function(){
		var tempcontainer=this.visiblediv
		this.visiblediv=this.hiddendiv
		this.hiddendiv=tempcontainer
	}

	pausescroller.prototype.getinline=function(div1, div2){
		div1.style.top=this.visibledivtop+"px"
		div2.style.top=Math.max(div1.parentNode.offsetHeight, div1.offsetHeight)+"px"
	}

	// -------------------------------------------------------------------
	// setmessage()- Populate the hidden div with the next message before it's visible
	// -------------------------------------------------------------------

	pausescroller.prototype.setmessage=function(){
		var scrollerinstance=this
		if (this.mouseoverBol==1) //if mouse is currently over scoller, do nothing (pause it)
			setTimeout(function(){scrollerinstance.setmessage()}, 100)
		else{
			var i=this.hiddendivpointer
			var ceiling=this.content.length
			this.hiddendivpointer=(i+1>ceiling-1)? 0 : i+1
			this.hiddendiv.innerHTML=this.content[this.hiddendivpointer]
			this.animateup()
		}
	}

	pausescroller.getCSSpadding=function(tickerobj){ //get CSS padding value, if any
		if (tickerobj.currentStyle)
		return tickerobj.currentStyle["paddingTop"]
		else if (window.getComputedStyle) //if DOM2
		return window.getComputedStyle(tickerobj, "").getPropertyValue("padding-top")
		else
		return 0
	}
</script>

<?php
 if($user['estPartenaire']==1){
   $estPartenaire=true;
 }
 else {
   $estPartenaire=false;
 }
 ?>

<div id="bloc1">
	<h3 style="color: #2f536a;"><?= 'Mon Abonnement' ?> </h3>
</div>
<br>
<div class="users view large-12 medium-9 columns-5">
    <div id="bloc1">
        <div class="col-xs-12 col-sm-2 text-center">
	<?= $this->Html->link(__('Mon Compte'), ['action' => 'editcompte', $_SESSION['Auth']['User']['id']], ['class' => 'btn-orange', 'style' => 'width: 170px;']) ?>

	<!-- <?= $this->Html->link(__('Rétractation'), './cgv/retractation.pdf',['class' => 'btn-orange', 'target' => 'blank', 'style' => 'width: 170px;']) ?>

	<?= $this->Html->link(__('Renonciation Rétractation'), './cgv/renonretract.pdf',['class' => 'btn-orange', 'target' => 'blank', 'style' => 'width: 170px;']) ?> -->
        </div>
	<div class="col-xs-12 col-sm-10 text-left">
    <!-- Remplacer .. par http://mon-orientation-scolaire.com/ -->
	<?= $this->Form->create(null, ['url'=>'https://mon-orientation-scolaire.com/payment/call_request.php?', 'method' => 'post']);
  //$this->Form->create($user, ['url'=>['action'=>'../payment/call_request.php?'], 'method' => 'post']) ?>
	<fieldset>
	<legend><?= __('Confirmation du Choix de l\'Abonnement') ?></legend>
	<table>
    <thead>
    <tr align="left">
    		<th width="200px">Type</th>
    		<th width="400px">Désignation</th>
    		<th width="200px" style="text-align:center;">Montant</th>
		</tr>
    </thead>

    <tbody>
		<?php
    //die($user);
    $totalpack=0;
    $totalmodule=0;
		$totaloption=0;
		echo '<tr>';

		foreach($pack as $key => $val)
		{
      $category=$pack[$key]['category'];
      if($category=="Pack"){
        $totalpack=$totalpack+$pack[$key]['prixstd'];
      }
      elseif ($category=='Module') {
        $totalmodule=$totalmodule+$pack[$key]['prixstd'];
      }

                    echo '<tr style="border-bottom: 1px solid black; border-top: 1px solid black;"><td>' . $pack[$key]['category'] . '</td>';
                    echo '<td/>'.$pack[$key]['typepack'].'</td>';
                    echo '<td  align="right" style="color:blue;">'.$pack[$key]['prixstd'].' € TTC</td></tr>';
                    $totaloption=$totaloption+$pack[$key]['prixstd'];
		}
                    echo '</tr/>';
                    if($estPartenaire){
                      if($totalpack!=0){
                      $totalpack=$totalpack-50;
                      }
                      $totalmodule=$totalmodule-($totalmodule*0.1);
                      $prixpartenaire=$totalpack+$totalmodule;
                      $remisepartenaire=$totaloption-$prixpartenaire;
                    }
                    $totaloption=$totalpack+$totalmodule;

							/*echo '<tr style="border-bottom: 1px solid black; border-top: 1px solid black;">';
								echo '<td>Base de l\'Abonnement</td>';
								echo '<td>'.$pack[0]['typepack'].'</td>';
								echo '<td align="right" style="color:blue;">'.$pack[0]['prixstd'].' € TTC </td>';
							echo '</tr/>';
							$totaloption=0;
							foreach($option as $key => $val)
							{
								echo '<tr><td>Option</td>';
								echo '<td/>'.$option[$key]['typeoption'].'</td>';
								echo '<td  align="right" style="color:blue;">'.$option[$key]['prixstd'].' € TTC</td></tr>';
								$totaloption=$totaloption+$option[$key]['prixstd'];
							}*/
		if(!empty($promo)){
                    echo '<tr style="border-top: 1px solid black;">';
                    echo '<td> Réduction </td>';
                    echo '<td> Code réduction : '.$promo[0]['typepromo'].'</td>';
                    echo '<td  align="right" style="color:red;"> -'.$promo[0]['prixstd'].' € TTC </td>';
                    echo '</tr>';
		}

    if($estPartenaire) {
      echo '<tr style="border-top: 1px solid black;">';
        echo '<td> Remise partenaire </td>';
        echo '<td> -50€ sur le pack 1, 2 ou 3 / -10% sur tous les modules</td>';
        echo '<td  align="right" style="color:red;"> -'.$remisepartenaire.' € TTC </td>';
        //echo '<td  align="right" style="color:red;"><input type="text" name="promo" id="promo" onchange="TypePack(this.value)" /></td>';
      echo '</tr/>';
    }

		echo '<tr style="border-top: 1px solid black;">';
		echo '<td colspan=2 align=center><b>Total :</b></td>';
		if(!empty($promo)){
		//echo '<td  align="right" style="color:blue;"> <b>'.number_format((($pack[0]['prixstd']+$totaloption)-$promo[0]['prixstd']),2,',',' ').' € TTC </b></td>';
		//$montantglobal=(($pack[0]['prixstd']+$totaloption)-$promo[0]['prixstd']);
		echo '<td  align="right" style="color:blue;"> <b>'.number_format((($totaloption)-$promo[0]['prixstd']),2,',',' ').' € TTC </b></td>';
		$montantglobal=(($totaloption)-$promo[0]['prixstd']);
		}
    else{
				//echo '<td  align="right" style="color:blue;"> <b>'.number_format(($pack[0]['prixstd']+$totaloption),2,',',' ').' € TTC </b></td>';
				//$montantglobal=($pack[0]['prixstd']+$totaloption);
				echo '<td  align="right" style="color:blue;"> <b>'.number_format((($totaloption)-$promo[0]['prixstd']),2,',',' ').' € TTC </b></td>';
				$montantglobal=(($totaloption)-$promo[0]['prixstd']);
								}
							echo '</tr>';
						?>
					</tbody>
				</table>

        <!-- insertion d'un élément dans le formulaire : celle du montant de la transaction-->

        <br>
			</fieldset>
			<br/>

			<!-- <fieldset>
				<div id="avertissement" style="diplay:block;">
					<font color=#FF0000 bold>--- VOTRE TEXTE ---</font>
				</div>
			</fieldset> -->

				<script type="text/javascript">
					new pausescroller(pausecontent, "pscroller1", "someclass", 3000)
					document.write("<br />")
				</script>


			<br/>
			<fieldset>
      <table>
				<tbody>
          <tr>
					<td style="width: 30%;" align="center">
            <img src="/backend/webroot/img/societegenerale.png" alt="société générale" style="width:80%; height:50%; border:none;" />
					</td>
                <td style="width: 70%; text-align:justify;">
								<?php echo 'Afin d\'assurer la sécurité et la qualité de votre paiement, nous avons confié à la <b>Société Générale</b> et sa solution <b>"SOGENACTIF" </b>
											le soin de traiter votre processus de paiement en ligne. <br/>Nous ne recueillons donc aucune information liée à vos compte et numéro de
											carte bancaire. <br/>En cliquant ci-dessous, vous serez redirigé vers la plateforme de paiement en ligne de la <b>Société Générale</b>.'; ?>
                </td>
          </tr>
				</tbody>
        </table>
			</fieldset>
			<br/>
			<fieldset>
				<?php
					/*if(stripos($_SERVER['SERVER_NAME'], 'www.')!==false){
						$www='www.mon-orientation-scolaire.com';
					}
					else{
						$www='mon-orientation-scolaire.com';
					}*/
          /* insertion des paramètres dans l'URL */
					if(stripos($_SERVER['SERVER_NAME'], 'www.')!==false){
						$www='www.mon-orientation-scolaire.com';
					}
					else{
						//$www='localhost/mos_h2c/backend/webroot';
            $www="mon-orientation-scolaire.com";
					}
					$nom=str_replace(' ','',$user['nom']);
					$prenom=str_replace(' ','',$user['prenom']);
					$lien='https://'.$www.'/payment/call_request.php?amount='.$montantglobal.'&user_id='.$user['id'].'&user_email='.$user['email'].'&user_nom='.$nom.'&user_prenom='.$prenom.'&www='.$www;
          // string utilisée pour compléter le liens vers lequel pointe le boutton Paiement
          $user_link='amount='.$montantglobal.'&user_id='.$user['id'].'&user_email='.$user['email'].'&user_nom='.$nom.'&user_prenom='.$prenom.'&www='.$www;
        ?>
        <?= $this->Form->input('montantglobal', array('type' => 'hidden', 'value' => $montantglobal))?>
        <?= $this->Form->input('user_id', array('type' => 'hidden', 'value' => $user['id']))?>
        <?= $this->Form->input('user_email', array('type' => 'hidden', 'value' => $user['email']))?>
        <?= $this->Form->input('user_nom', array('type' => 'hidden', 'value' => $nom))?>
        <?= $this->Form->input('user_prenom', array('type' => 'hidden', 'value' => $prenom))?>
        <?= $this->Form->input('www', array('type' => 'hidden', 'value' => $www))?>

				<iframe src="<?php echo $lien ?>"
					frameborder="0" width ="100%" scrolling="yes" height="216" name="I1" target="_blank" align="center">
				</iframe>
			</fieldset>
			<fieldset style="text-align: center;">
        <!--a href="<?php //echo $lien;?>" class="btn-savoir-plus">Confirmer</a , ['action' => '../webroot/payment/call_request.php?']-->
				<?= $this->Html->link(__('Précédent'), ['action' => 'nospacks'], ['class' => 'btn-savoir-plus']) ?>
				<?= $this->Html->link(__('Rétractation'), '/cgv/retractation.pdf',['class' => 'btn-savoir-plus', 'target' => 'blank']) ?>
				<?= $this->Html->link(__('Renonciation Rétractation'), '/cgv/renonretract.pdf',['class' => 'btn-savoir-plus', 'target' => 'blank']) ?>
			</fieldset>
			<?= $this->Form->end() ?>
		</div>
	</div>
</div>
