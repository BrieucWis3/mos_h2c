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
			<?= $this->Html->link(__('Mon Compte'), ['action' => 'editcompte', $_SESSION['Auth']['User']['id']], ['class' => 'btn-orange',
																												  'style' => 'width: 170px;']) ?>
			<!-- <?= $this->Html->link(__('Rétractation'), './cgv/retractation.pdf',['class' => 'btn-orange',
																				'target' => 'blank',
																				'style' => 'width: 170px;']) ?>

			<?= $this->Html->link(__('Renonciation Rétractation'), './cgv/renonretract.pdf',['class' => 'btn-orange',
																							 'target' => 'blank',
																							 'style' => 'width: 170px;']) ?> -->
		</div>
		<div class="col-xs-12 col-sm-10 text-left">
			<?= $this->Form->create($user) ?>
			<fieldset>
				<legend><?= __('Confirmation du Choix du Pack') ?></legend>
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
							/*echo '<tr style="border-bottom: 1px solid black; border-top: 1px solid black;">';
								echo '<td>Base de l\'Abonnement</td>';
								echo '<td>'.$pack[0]['typepack'].'</td>';
								echo '<td align="right" style="color:blue;">'.$pack[0]['prixstd'].' € TTC </td>';
							echo '</tr/>';*/
							$totaloption=0;
              $totalpack=0;
              $totalmodule=0;
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

									echo '<tr style="border-bottom: 1px solid black; border-top: 1px solid black;"><td>'. $category .'</td>';
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
              //print_r($totalpack);
              //print_r($totalmodule);
							//$totaloption=0;
							//echo '<tr>';
								/*foreach($option as $key => $val)
								{
									echo '<tr><td>Option</td>';
									echo '<td/>'.$option[$key]['typeoption'].'</td>';
									echo '<td  align="right" style="color:blue;">'.$option[$key]['prixstd'].' € TTC</td></tr>';
									$totaloption=$totaloption+$option[$key]['prixstd'];
								}*/
							//echo '</tr/>';
							//if(!empty($promo)){
							if($idpromo==0){
								echo '<tr style="border-top: 1px solid black;">';
									echo '<td> Réduction </td>';
									echo '<td> Code réduction : </td>';
									//echo '<td  align="right" style="color:red;"> -'.$promo[0]['prixstd'].' € TTC </td>';
									echo '<td  align="right" style="color:red;"><input type="text" name="promo" id="promo" style="margin-top: 10px;" onchange="TypePack(this.value)" /></td>';
								echo '</tr/>';
							}else{
								echo '<tr style="border-top: 1px solid black;">';
									echo '<td> Réduction </td>';
									echo '<td> Code réduction : '.$promo[0]['typepromo'].'</td>';
									echo '<td  align="right" style="color:red;"> -'.$promo[0]['prixstd'].' € TTC </td>';
									//echo '<td  align="right" style="color:red;"><input type="text" name="promo" id="promo" onchange="TypePack(this.value)" /></td>';
								echo '</tr/>';
							}

              if($estPartenaire){
                echo '<tr style="border-top: 1px solid black;">';
									echo '<td> Remise partenaire </td>';
									echo '<td> -50€ sur le pack 1, 2 ou 3 / -10% sur tous les modules</td>';
									echo '<td  align="right" style="color:red;"> -'.$remisepartenaire.' € TTC </td>';
									//echo '<td  align="right" style="color:red;"><input type="text" name="promo" id="promo" onchange="TypePack(this.value)" /></td>';
								echo '</tr/>';
              }

							if($idpromo==0){
								echo '<tr style="border-top: 1px solid black;">';
									echo '<td colspan=2 align=center><b>Total :</b></td>';
									if($idpromo==0){
										echo '<td  align="right" style="color:blue;"> <b>'.number_format((($totaloption)-$idpromo),2,',',' ').' € TTC </b></td>';
									}else{
										echo '<td  align="right" style="color:blue;"> <b>'.number_format(($totaloption),2,',',' ').' € TTC </b></td>';
									}
								echo '</tr>';
							}else{
								echo '<tr style="border-top: 1px solid black;">';
									echo '<td colspan=2 align=center><b>Total :</b></td>';
									if(!empty($promo)){
										echo '<td  align="right" style="color:blue;"> <b>'.number_format((($totaloption)-$promo[0]['prixstd']),2,',',' ').' € TTC </b></td>';
									}else{
										echo '<td  align="right" style="color:blue;"> <b>'.number_format((+$totaloption),2,',',' ').' € TTC </b></td>';
									}
								echo '</tr>';
							}

						?>
					</tbody>
				</table>
				<br>
				<div style='text-align: justify; text-justify: inter-word;'>
					<div style="width:1000px; height:200px; overflow:auto; border:1px solid #000; background-color: #f2f2f2; margin-bottom: 10px; padding: 5px;
								font-style: italic;  max-width: 100%; background-color: #f2f2f2; border: none; border-radius: 0px;">
						<p style="font-size: 13px;">
							<strong><center>CONDITIONS GENERALES DE VENTE ET D’UTILISATION</center></strong>
							<br/>
							<strong>1 - Dispositions générales</strong><br/>
							Les présentes conditions générales ont pour objet de définir les conditions dans lesquelles la société MOS (« MOS ») rend au Client qui l’accepte, un service de conseil à l’orientation scolaire sur son site internet www.mon-orientation-scolaire.com (« le Site »). Ledit service peut être souscrit par le Client pour lui-même ou pour un autre utilisateur (« l’Utilisateur ») qu’il désigne lors de la commande. Les présentes conditions générales de vente et d’utilisation régissent donc les relations entre MOS, le Client et l’Utilisateur s’il s’agit d’une autre personne. Ces conditions seront seules applicables même en cas d’indication contraire mentionnée par ailleurs. Aucune tolérance ne pourra être interprétée comme valant renonciation à un droit.
							MOS se réserve le droit de modifier lesdites conditions à tout moment. Les conditions générales sont celles en vigueur sur le Site à la date de passation de la commande.
							La passation de la commande sur le Site implique l’adhésion pleine et entière du Client et de l’Utilisateur s’il s’agit d’une autre personne, aux présentes conditions générales, dont il déclare avoir pris connaissance avant de passer commande.
							<br/><br/>
							<strong>2 - Services offerts par MOS</strong><br/>
							MOS propose un service de conseil à l’orientation scolaire. Il s’adresse notamment aux élèves ou futurs élèves des collèges, lycées, à leurs parents ainsi qu’aux étudiants et futurs étudiants.
							<br/><strong>2.1. Services gratuits</strong><br/>
							Le Site offre des informations en libre disposition ainsi que l’accès gratuit à des tests permettant une première « pré-orientation » scolaire.
							<br/><strong>2.2. Service payant</strong><br/>
							MOS propose un service d’accompagnement personnalisé à l’orientation scolaire réalisé sur la base d’informations fournies par le Client ou l’Utilisateur s’il s’agit d’une autre personne (telles que les bulletins scolaires) et obtenues suite à la réalisation de tests de personnalité et bilans de compétence auxquels l’Utilisateur se sera prêté. L’ensemble des échanges entre l’Utilisateur et MOS s’effectue par l’intermédiaire d’une plateforme internet personnalisée active du lundi au vendredi de 9h à 20h, heure de Paris et le samedi de 9h à 13h, heure de Paris. Dans ces créneaux, l’Utilisateur est libre de se connecter à la plateforme selon ses disponibilités et ses objectifs et ainsi d’adopter le rythme qui lui convient. L’Utilisateur échangera par visioconférence si nécessaire par échanges téléphoniques avec un conseiller MOS. L’attention du Client et de l’Utilisateur est attirée sur le fait que les conditions de réussite du processus reposent sur un travail d’une durée d’au-moins 1 à 2 mois et qu’à défaut pour le Client ou l’Utilisateur de fournir les informations demandées par MOS ou de se prêter aux tests et bilans de compétence proposés, MOS ne serait en mesure de rendre la prestation de service demandée. A l’issue des échanges et tests, MOS adresse à l’Utilisateur une première synthèse technique « métiers » ciblant une famille de métiers. Des échanges complémentaires permettront de cibler 5 métiers. MOS fournira alors à l’Utilisateur les fiches détaillées des métiers ciblés ainsi que les études permettant d’accéder à ces métiers. La remise par MOS de ces fiches entraînera la fin de la prestation de service d’accompagnement personnalisé à l’orientation scolaire. Ainsi, le service de conseil personnalisé à l’orientation scolaire n’inclut pas la préparation, le soutien scolaire ou le coaching qui pourraient s’avérer nécessaire pour l’accession à l’un des métiers ciblés par MOS. MOS est toutefois en mesure de mettre le Client et l’Utilisateur en relation avec des partenaires proposant de tels services.
							<br/><br/>
							<strong>3 - Inscription du Client et de l’Utilisateur</strong><br/>
							Afin de réaliser son inscription sur le Site, le Client et l’Utilisateur s’il s’agit d’une autre personne, devront obligatoirement suivre les étapes suivantes :<br/>
							&nbsp&nbsp&nbsp-	Ouvrir un compte client,<br/>
							&nbsp&nbsp&nbsp-	Remplir et vérifier le formulaire d’inscription,<br/>
							&nbsp&nbsp&nbsp-	Valider la commande (dont les présentes CGV),<br/>
							&nbsp&nbsp&nbsp-	Régler la commande.<br/>
							<br/>
							Le Client reçoit ensuite par courriel le cas échéant :<br/>
							&nbsp&nbsp&nbsp-	un accusé réception valant confirmation de la commande,<br/>
							&nbsp&nbsp&nbsp-	une confirmation de paiement,<br/>
							&nbsp&nbsp&nbsp-	et le code et le mot de passe lui permettant d’accéder à la plateforme internet dans les conditions précisées ci-après.<br/>
							<br/>
							<strong>4 - Conclusion du contrat</strong><br/>
							Bien que les services fournis par MOS soient susceptibles de bénéficier à des mineurs, le contrat ne peut être conclu qu’avec un Client majeur qui devra s’identifier lors de la commande et s’engager personnellement dans le cadre des présentes.
							La conclusion du contrat n’est parfaite qu’après inscription du Client et de l’Utilisateur s’il s’agit d’une autre personne, sur le Site et réception du paiement complet par MOS, sauf exercice du droit de rétractation du Client dans les conditions indiquées ci-après.
							Les données enregistrées par MOS constituent la preuve de la transaction effectuée (article 1316-1 du Code civil). Toutefois MOS se réserve le droit de refuser la commande si elle est anormale, passée de mauvaise foi ou pour tout autre motif légitime, et en particulier, lorsqu’il existe un litige avec le Client concernant le paiement d’une commande antérieure.
							<br/><br/>
							<strong>5 - Droit de rétractation</strong><br/>
							Le Client dispose d’un droit de rétractation sans motif à fournir dans un délai de quatorze (14) jours à compter de la conclusion du contrat. Toutefois, le Client peut expressément renoncer à son droit de rétractation lors de la passation de la commande en suivant les instructions présentes sur le Site. La renonciation à son droit de rétractation lui permettra de bénéficier, à sa demande, de l’accès à la plateforme internet dès la conclusion du contrat.
							Dans l’hypothèse où le Client entend exercer son droit de rétractation, il lui appartient d’adresser à MOS par lettre recommandée avec demande d’accusé de réception à l’adresse suivante : Société MOS, 69 Boulevard des Canuts, 69004 LYON, un courrier libre exprimant son intention non équivoque de résilier le contrat totalement ou partiellement ou le formulaire de <a href='https://www.mon-orientation-scolaire.com/backend/cgv/retractation.pdf' target=_blank>rétractation</a> dûment complété et signé.
							MOS adresse alors un accusé de réception par courriel (y compris en cas de renonciation à l’exercice de son droit de rétractation) et rembourse au Client, selon les mêmes moyens que ceux utilisés par celui-ci pour le paiement initial, la totalité des sommes versées dans les quatorze (14) jours suivant la date de réception de la demande de rétractation.
							<br/> <br/>
							<strong>6 - Accès à la plateforme internet</strong><br/>
							Le Client reçoit un code d’accès et un mot de passe lui donnant accès à la plateforme internet à l’expiration du délai de rétractation prévu ci-dessus. Dans l’hypothèse où il a expressément renoncé à son droit de rétractation, il reçoit dès la validation de son paiement son identifiant et son mot de passe.
							<br/>
							La plateforme internet personnalisée est active du lundi au vendredi de 9h à 20h, heure de Paris et le samedi de 9h à 13h.
							Il appartient donc à l’Utilisateur de s’y connecter pendant ces créneaux, selon ses disponibilités. Pour ce faire, il devra disposer d’un ordinateur, d’une webcam ainsi que d’une connexion internet de bonne qualité. L’utilisation du navigateur GOOGLE CHROME est par ailleurs, requise pour une utilisation performante.
							<br/><br/>
							<strong>7 - Prix, Conditions de Paiement & Sécurisation</strong><br/>
							Le service payant donne lieu au règlement d’une somme forfaitaire dont le montant est indiqué sur le Site et rappelé lors de la passation de la commande. Aucun autre paiement ne sera demandé au Client. Toutefois, dans l’hypothèse où des communications téléphoniques seraient nécessaires, le Client s’engage à en prendre l’initiative et frais de télécommunication qui en résulteraient demeureraient à sa charge. L’appel de la société MOS entraîne le coût d’un appel local depuis un poste fixe, hors coût d’opérateur.
							Le prix est payable lors de la passation de la commande selon les instructions présentes sur le Site :<br/>
							- soit par carte bancaire,<br/>
							- soit par chèque bancaire à l’ordre de MOS adressé par voie postale à l’adresse suivante : Société MOS, 69 Boulevard des Canuts, 69004 LYON.<br/>
							<strong>7.1 Carte Bancaire</strong><br/>
							Les cartes bancaires acceptées sont celles des réseaux Carte Bleue, Visa, Eurocard / MasterCard.
							Conformément à l’article L. 132-2 du Code monétaire et financier, l’engagement de payer donné au moyen d’une carte de paiement est irrévocable. En communiquant les informations relatives à sa carte bancaire, le Client autorise MOS à débiter sa carte bancaire du montant correspondant au prix.
							Le Client communique les seize chiffres et la date d’expiration de sa carte bleue ainsi que le cas échéant, les numéros du cryptogramme visuel.
							<br/><strong>7.2 Chèque</strong><br/>
							En cas de paiement par chèque bancaire, celui-ci doit être émis par une banque domiciliée en France métropolitaine ou à Monaco.
							En cas de paiement par chèque, les codes d’accès et mots de passe permettant l’accès à la plateforme internet ne seront fournis qu’après encaissement du chèque.
							<br/><strong>7.3 Sécurisation</strong><br/>
							Le Site fait l’objet d’un système de sécurisation des paiements, afin de protéger le plus efficacement possible toutes les données sensibles liées au moyen de paiement.
							MOS met en œuvre tous les moyens pour assurer la confidentialité et la sécurité des données transmises sur le Site.
							<br/><strong>7.4. Remboursement</strong><br/>
							Le prix est ferme et définitif de sorte qu’à l’exception de l’exercice du droit de rétractation visé à l’article 5, aucun remboursement même partiel du prix ne pourra être accordé au Client quelle qu’en soit la cause.
							<br/><br/>
							<strong>8 - Obligations - Responsabilités</strong><br/>
							<strong>8.1. Obligations et responsabilité de MOS</strong><br/>
							MOS s’engage à mettre en œuvre les moyens nécessaires pour fournir au Client ou à l’Utilisateur s’il s’agit d’une autre personne, un conseil personnalisé au vu des informations échangées sur la plateforme internet. MOS contracte dans ce cadre une obligation de moyens.
							La responsabilité de MOS ne peut être engagée qu’en cas de faute ou de négligence prouvée et est limitée aux préjudices directs à l’exclusion de tout préjudice indirect, de quelque nature que ce soit. La responsabilité de MOS ne pourra notamment pas être engagée dans l’hypothèse où le Client ou l’Utilisateur s’il s’agit d’une autre personne, ne fournit pas les informations demandées par MOS, ne se prête pas aux tests et bilans de compétence proposés par MOS, ne respecte pas la durée préconisée d’utilisation des services d’1 à 2 mois minimum ou n’est pas satisfait des métiers qui lui sont proposés à l’issus de la prestation d’accompagnement personnalisé. La responsabilité de MOS ne pourra être engagée sur les conséquences des conseils ou recommandations fournis au Client et/ou à l’Utilisateur, ces derniers assumant l’entière responsabilité du choix de suivre ou non les conseils et/ou les recommandations de MOS.
							<br/>
							<strong>8.2. Obligations et responsabilité du Client et de l’Utilisateur</strong><br/>
							L’Utilisateur s’engage à fournir les informations nécessaires à la réalisation de la prestation de conseil attendue et à se soumettre aux différents tests et bilans de compétence proposés par MOS.
							Le Client et l’Utilisateur s’il s’agit d’une autre personne, s’engagent à ne pas communiquer le code d’accès et le mot de passe fournis pour l’accès à la plateforme internet, ces derniers étant strictement personnels et confidentiels.
							Le Client et l’Utilisateur s’il s’agit d’une autre personne, sont responsables du respect du présent accord, de l’exactitude, de la qualité et de la légalité des données personnelles fournies, ainsi que de la manière par laquelle ces dernières ont été obtenues et mises à disposition sur le Site. Ils ne doivent en aucun cas utiliser ces services afin de stocker ou transmettre des éléments contrefaits, diffamatoires, illégaux, délictueux, s’inscrivant en violation de la vie privée d’un tiers ou tout autre élément que MOS pourrait juger inapproprié.
							<br/> <br/>
							<strong>9 - Propriété intellectuelle</strong>
							L’ensemble des textes, ouvrages et illustrations auxquels MOS est susceptible de donner accès, directement ou sous licence d’un tiers, sont protégés par le droit d’auteur. Toute reproduction ou représentation des supports sera soumise à accord.
							<br/> <br/>
							<strong>10 - Service Client</strong>
							Pour toute information ou question, le Service Client est ouvert du lundi au vendredi de 9h à 20h, heure de Paris et le samedi de 9h à 13h.
							Pour obtenir des informations : directement sur le Site ou par téléphone au 0970590000, ou courriel à l’adresse courriel suivante : contact@mon-orientation-scolaire.com
							Pour une éventuelle réclamation : par voie postale ou électronique en rappelant la référence et la date de l’inscription à l’adresse suivante : Société MOS, 69, Boulevard des Canuts, 69004 LYON. (Coût d’un appel local depuis un poste fixe, hors coût opérateur).
							Ou à l’adresse courriel suivante : comptabilité@mon-orientation-scolaire.com
							<br/> <br/>
							<strong>11 - Confidentialité des Données</strong>
							Toute information collectée relative à une personne identifiable devra être utilisée conformément aux intentions et buts de la personne ici concernée et avec son autorisation expresse, conformément à la loi.
							MOS n’accèdera aux données qu’afin de fournir le service commandé, éviter ou régler d’éventuels problèmes techniques ou pour des questions relevant de l’assistance technique, par exemple.
							Dans l’hypothèse où le Client ou l’Utilisateur s’il s’agit d’une autre personne, consent à communiquer des données individuelles à caractère personnel, il dispose d’un droit individuel d’accès, de retrait et de rectification de ces données dans les conditions prévues par la loi n° 78-17 du 6 janvier 1978, modifiée par la loi du 23 janvier 2006, relative à l’informatique et aux libertés. Le Client ou l’Utilisateur s’il s’agit d’une autre personne, doit adresser toute demande écrite à l’adresse suivante : Société MOS, 69, Boulevard des Canuts, 69004 LYON.
							Ou à l’adresse courriel suivante : comptabilité@mon-orientation-scolaire.com
							À l’occasion de la création de son compte client sur le Site, le Client ou l’Utilisateur s’il s’agit d’une autre personne, aura la possibilité de choisir s’il souhaite recevoir des offres de MOS et de ses partenaires.
							<br/> <br/>
							<strong>12 - Droit applicable – Attribution de compétence</strong>
							Le présent contrat est soumis à la loi française. En cas de litige, les tribunaux français seront seuls compétents.
						</p>
						<br />
						<p>
							<strong><center>INFORMATIONS CONCERNANT L'EXERCICE DU DROIT DE RÉTRACTATION</center></strong>
							<br/>
							<strong>Droit de rétractation</strong><br/>
							Vous avez le droit de vous rétracter du présent contrat sans donner de motif dans un délai de quatorze jours.<br/>
							Le délai de rétractation expire quatorze jours après le jour de la conclusion du contrat.<br/><br/>
							Pour exercer le droit de rétractation, vous devez nous notifier votre décision de rétractation du présent contrat au moyen d'une déclaration dénuée d'ambiguïté (par exemple, lettre envoyée par la poste, télécopie ou courrier électronique). Vous pouvez utiliser le modèle de formulaire de rétractation mais ce n'est pas obligatoire.
							<br/><br/>
							Pour que le délai de rétractation soit respecté, il suffit que vous transmettiez votre communication relative à l'exercice du droit de rétractation avant l'expiration du délai de rétractation.
							<br/><br/>
							<strong>Effets de rétractation</strong><br/>
							En cas de rétractation de votre part du présent contrat, nous vous rembourserons tous les paiements reçus de vous, au plus tard quatorze jours à compter du jour où nous sommes informés de votre décision de rétractation du présent contrat. Nous procéderons au remboursement en utilisant le même moyen de paiement que celui que vous aurez utilisé pour la transaction initiale, sauf si vous convenez expressément d'un moyen différent ; en tout état de cause, ce remboursement n'occasionnera pas de frais pour vous.
							<br/><br/>
							Vous pouvez également remplir et transmettre le modèle de formulaire de rétractation ou toute autre déclaration dénuée d'ambiguïté sur notre site internet comptabilite@mon-orientation-scolaire.com.<br/>
							Si vous utilisez cette option, nous vous enverrons sans délai un accusé de réception de la rétractation sur un support durable (par exemple, par courriel).
						</p>
						<br />
						<!-- <div align="center" style="font-size:15px; ">
							<table>
								<tr>
									<td align="center">
										<input type="checkbox" id="cgvok" name="cgvok" style="width: 15px; margin-left: 2rem; margin-right: 2rem; margin-top: 1rem; margin-bottom: 1rem;" title="Vous devez avoir pris connaissance des Conditions Générales de Vente et du Droit de Rétractation." required />
									</td>
									<td align="left">
										J'ai lu et j’accepte les conditions générales de ventes ci-dessus.
									</td>
								</tr>
								<tr>
									<td align="center">
										<input type='checkbox' name="majorite" id="majorite" style="width: 15px; margin-left: 2rem; margin-right: 2rem; margin-top: 1rem; margin-bottom: 1rem;" title="Il est nécessaire d'être majeur pour payer en ligne." required />
									</td>
									<td align="left">
										Je certifie être majeur.
									</td>
								</tr>

							</table>
						</div> -->
						<br/>
					</div>
				</div>
				<div align="center" style="font-size:15px; ">
					<table>
						<tr>
							<td align="center">
								<input type="checkbox" id="cgvok" name="cgvok" style="width: 15px; margin-left: 2rem; margin-right: 2rem; margin-top: 1rem; margin-bottom: 1rem;" title="Vous devez avoir pris connaissance des Conditions Générales de Vente et du Droit de Rétractation." required />
							</td>
							<td align="left">
								J'ai lu et j’accepte les conditions générales de ventes ci-dessus.
							</td>
						</tr>
						<tr>
							<td align="center">
								<input type='checkbox' name="refusretract" id="refusretract" style="width: 15px; margin-left: 2rem; margin-right: 2rem; margin-top: 1rem; margin-bottom: 1rem;" />
							</td>
							<td align="left">
								Je désire rennoncer au droit de rétractation.
							</td>
						</tr>
						<tr>
							<td align="center">
								<input type='checkbox' name="majorite" id="majorite" style="width: 15px; margin-left: 2rem; margin-right: 2rem; margin-top: 1rem; margin-bottom: 1rem;" title="Il est nécessaire d'être majeur pour payer en ligne." required />
							</td>
							<td align="left">
								Je certifie être majeur.
							</td>
						</tr>
					</table>
				</div>
			</fieldset>
			<br/>
			<fieldset style="text-align: center;">
				<?php //echo '<a href="https://mon-orientation-scolaire.com/backend/payment/call_request.php?amount='.$solde.'" target="_blank" class="btn-savoir-plus">Payer</a>'; ?>
				<!-- <?= $this->Html->link(__('Précédent'), ['action' => 'abonnement'], ['class' => 'btn-savoir-plus']) ?> -->
				<?= $this->Html->link(__('Précédent'), ['action' => 'nospacks'], ['class' => 'btn-savoir-plus']) ?>
				<?= $this->Form->button('Suivant', ['class' => 'btn-savoir-plus'],['action' => 'suivant2', $user->id, $pack[0]['id']]) ?>
				<!-- <?= $this->Html->link(__('Suivant2'), ['action' => 'suivant', $user->id, $pack[0]['id']], ['class' => 'btn-savoir-plus']) ?> -->

				<!-- <?= $this->Html->link(__('Rétractation'), '/cgv/retractation.pdf',['class' => 'btn-savoir-plus',
																				   'target' => 'blank']) ?>

				<?= $this->Html->link(__('Renonciation Rétractation'), '/cgv/renonretract.pdf',['class' => 'btn-savoir-plus',
																								'target' => 'blank']) ?> -->
			</fieldset>
			<?= $this->Form->end() ?>
		</div>
	</div>
</div>


<script>
	function TypePack(valeur){
		/*
		if(valeur==1){
			document.getElementById('mesoptions').style.display = "none";

		}else{
			if(valeur==2){
				document.getElementById('mesoptions').style.display = "";
			}else{
				document.getElementById('mesoptions').style.display = "none";
			}
		}*/
	}
</script>
