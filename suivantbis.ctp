<?php
//print_r($_POST);
 ?>

<script type="text/javascript">
	choixpack = 0;
</script>

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
					<legend><?= __('Sélection du Choix des Modules') ?></legend>
					<table>
						<thead>
							<tr align="left">
								<th width="200px">Choix</th>
								<th width="400px">Désignation</th>
								<th width="200px" style="text-align:center;">Montant</th>
							</tr>
						</thead>
						<tbody>
							<?php
								echo '<tr style="border-bottom: 1px solid black; border-top: 1px solid black;">';
									//echo '<td>Base de l\'Abonnement</td>';
									//echo '<td>'.$pack[0]['typepack'].'</td>';
									foreach($pack as $key => $val)
									{
										//echo '<tr><td>Option</td>';
										echo '<tr><td>
											<input type="checkbox" id="'.$pack[$key]['id'].'" value="'.$pack[$key]['id'].'"
											name="choixpack[]"  style="width: 15px; margin-left: 2rem; margin-right: 2rem; margin-top: 1rem; margin-bottom: 1rem;"
											onChange="checkNum('.$pack[$key]['id'].');" />
										</td>';
										echo '<td/>'.$pack[$key]['typepack'].'</td>';
										echo '<td  align="right" style="color:blue;">'.$pack[$key]['prixstd'].' € TTC</td></tr>';
										//$totaloption=$totaloption+$pack[$key]['prixstd'];
									}
									//echo '<td align="right" style="color:blue;">'.$pack[0]['prixstd'].' € TTC </td>';
								echo '</tr/>';
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

							?>
						</tbody>
					</table>
					<br>
				</fieldset>

				<br/>
				<fieldset style="text-align: center;">
					<?= $this->Html->link(__('Précédent'), ['action' => 'nospacks'], ['class' => 'btn-savoir-plus']) ?>
					<!-- <?= $this->Html->Link(__('Suivant'), ['action' => 'suivant', $user->id], ['class' => 'btn-savoir-plus']) ?>	 -->

					<?= $this->Form->button('Suivant', array('type' => 'submit', 'class' => 'btn-savoir-plus', ['controller' => 'Users', 'action' => 'suivantbis', $user->id])); ?>
				</fieldset>

				<?= $this->Form->end() ?>

		</div>
	</div>
</div>

<script type="text/javascript">
	function checkNum(id) {
		var num = document.getElementById(id).checked;
		if(num == true)
		{
			choixpack = choixpack + id+'/';
		}
	}
</script>
