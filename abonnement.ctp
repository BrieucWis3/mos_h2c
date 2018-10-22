<div id="bloc1">
	<h3 style="color: #2f536a;"><?= 'Mon Abonnement' ?> </h3>
</div>
<br>
<div class="users view large-12 medium-9 columns-5">
	<div id="bloc1">
		<div class="col-xs-12 col-sm-2 text-center">
			<?= $this->Html->link(__('Mon Compte'), ['action' => 'editcompte', $_SESSION['Auth']['User']['id']], ['class' => 'btn-orange',
																												  'style' => 'width: 170px;']) ?>
			<!-- <?= $this->Html->link(__('Rétractation'), '/cgv/retractation.pdf',['class' => 'btn-orange',
																				'target' => 'blank',
																				'style' => 'width: 170px;']) ?>
			
			<?= $this->Html->link(__('Renonciation Rétractation'), '/cgv/renonretract.pdf',['class' => 'btn-orange',
																							 'target' => 'blank',
																							 'style' => 'width: 170px;']) ?> -->
		</div>
		<div class="col-xs-12 col-sm-10 text-left">
			<?= $this->Form->create($user) ?>
			<fieldset>
				<legend><?= __('Packs Proposés') ?></legend>
				<p> Veuillez sélectionner le pack auquel vous souhaitez souscrire : </p>
				<br/>
				<table>
					<thead>
						<tr align="left">
							<th width="450px">Formule</th>
							<th width="150px">Prix en € TTC</th>
							<th width="60px" style="text-align: center;" class="actions">Choisir</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							foreach($pack as $key => $val)
							{ 
								echo '<tr>'; 
									echo '<td>'.$val['typepack'].'</td>';
									echo '<td>'.$val['prixstd'].'</td>'; ?>
									<td><input type="radio" name="choix" id="choix" value="<?php echo $val['id']; ?>" style="width: 15px; margin-left: 2rem; margin-right: 2rem; margin-top: 1rem; margin-bottom: 1rem;" onclick="TypePack(this.value)" /></td>
									<?php
								echo '</tr>'; 
							}
						?>
					</tbody>
				</table>
				<br/>
				<div id='mesoptions' style="display:none;">
					<legend><?= __('Options Proposées') ?></legend>
					<p> Veuillez sélectionner les options auxquelles vous souhaitez souscrire : </p>
					<br/>
					<table>
						<thead>
							<tr align="left">
								<th width="450px">Option</th>
								<th width="450px">Description</th>
								<th width="150px">Prix en € TTC</th>
								<th width="60px" style="text-align: center;" class="actions">Choisir</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								foreach($option as $key => $val)
								{ 
									echo '<tr>'; 
										echo '<td>'.$val['typeoption'].'</td>';
										echo '<td>'.$val['description'].'</td>';
										echo '<td>'.$val['prixstd'].'</td>'; ?>
										<td><input type="checkbox" name="option[]" id="option[]" value="<?php echo $val['id']; ?>" style="width: 15px; margin-left: 2rem; margin-right: 2rem; margin-top: 1rem; margin-bottom: 1rem;" /></td>
										<?php 
									echo '</tr>'; 
								}
							?>
						</tbody>
					</table>
				</div>
				<br/>
				<legend><?= __('Codes Promotionnels') ?></legend>
				<p> Si vous bénéficiez d'un code promotionnel, veuillez le saisir ici : </p>
				<input type="text" name="promo" id="promo" />
				<!-- <br/>
				<table>
					<thead>
						<tr align="left">
							<th width="450px">Code Promotion</th>
							<th width="450px">Propriété</th>
							<th width="150px">Prix en € TTC</th>
							<th width="60px" style="text-align: center;" class="actions">Choisir</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							/*foreach($promo as $key => $val)
							{ 
								echo '<tr>'; 
									echo '<td>'.$val['typepromo'].'</td>';
									echo '<td>'.$val['description'].'</td>';
									echo '<td>'.$val['prixstd'].'</td>'; ?>
									<td><input type="radio" name="promo" id="promo" value="<?php echo $val['id']; ?>" style="width: 15px; margin-left: 2rem; margin-right: 2rem; margin-top: 1rem; margin-bottom: 1rem;" /></td>
									<?php
								echo '</tr>'; 
							}*/
						?>
					</tbody>
				</table> -->
			</fieldset>
			<br/>
			<fieldset style="text-align: center;">
				<?= $this->Form->button(__('Suivant'), ['class' => 'btn-savoir-plus']) ?>
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
		if(valeur==1){
			document.getElementById('mesoptions').style.display = "none";
			
		}else{
			if(valeur==2){
				document.getElementById('mesoptions').style.display = "";
			}else{
				document.getElementById('mesoptions').style.display = "none";
			}
		}
	}
</script>
