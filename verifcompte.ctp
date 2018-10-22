
<?= $this->Form->create() ?>
<?php

 ?>
<div class="users form large-10 medium-9 columns menu2" id="header2">
	<div class="bloc col-xs-12 col-sm-6 ">
		<div id="bloc1">
			<h3 style="color: #2f536a;">Nouveau Client ? <br/> Créez-vous un compte !!!</h3>
		</div>
		<div class="row">
			<div class="col-xs-6 col-sm-16">
				<div>
					<header>
						<?= $this->Html->link('Inscription', array( 'controller' => 'Users',
																	'action' => 'inscriptionpartielle', $choixpack, $choixmodule),['class' => 'btn-savoir-plus']); ?>
					</header>
				</div>
			</div>
		</div>
	</div>
	<div class="bloc col-xs-12 col-sm-6 ">
		<div id="bloc1">
			<h3 style="color: #2f536a;">Déjà client ? <br/> Connectez-vous</h3>
		</div>
		<!-- <?= $this->Form->create() ?> -->
		<?= $this->Form->input('email') ?>
		<?= $this->Form->input('password', array('label' => 'Mot de Passe')) ?>

		<div class="row">
			<div id="liste-niveaux" class="text-center" role="main">
				<div class="col-xs-6 col-sm-16">
					<div>
						<header>
							<?= $this->Form->button('Connexion', ['class' => 'btn-savoir-plus']) ?>
						</header>
					</div>
				</div>
				<div class="col-xs-6 col-sm-16">
					<div>
						<header>
							<?= $this->Html->link('Mot de Passe oublié', array(	'controller' => 'Oublipasswords', 'action' => 'edit'),['class' => 'btn-savoir-plus']);	//forgotpassword ?>
						</header>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?= $this->Form->end() ?>
</div>
