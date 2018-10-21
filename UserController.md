# mos_h2c
BO from the Website : https://mon-orientation-scolaire.com
<?php
namespace App\Controller;

use Cake\View\View;
use App\Controller\AppController;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Error\Debugger;
use Cake\Event\Event;
use Cake\Validation\Validator;
use Cake\Mailer\Email;
use Cake\Mailer\MailerAwareTrait;
use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\Time;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{


	use MailerAwareTrait;

	public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
		$this->Auth->allow('verifcompte');
		$this->Auth->allow('inscriptionpartielle');
		$this->Auth->allow('inscriptionok');
        $this->Auth->allow('inscription', 'inscriptionok', 'logout');
		$this->Auth->allow('inscriptionko');
		//$this->Auth->allow('forgotpassword', 'logout');
    }

	public function initialize()
	{
		parent::initialize();

		if ($this->request->action === 'index') {
			$this->loadComponent('Search.Prg');
		}
	}

	/**
     * Index method
     *
     * @return void
     */
    public function index()
    {
		$lapage=str_replace("webroot/", "", $_SERVER['REDIRECT_URL']);
		if(isset($_GET) && !empty($_GET)){
			$query2 = TableRegistry::get('Users');
			if((strlen($_GET['Titre'])===1) && (isset($_GET['Titre']))){
				$meslistes = $query2->find('All')->where(function ($exp, $q) {
															return $exp->like('email', $_GET['Titre'].'%');
														 });
			}else{
				if((!empty($_GET['Filtre'])) && (isset($_GET['Filtre']))){
					$meslistes = $query2->find('All')->where(function ($exp, $q) {
																return $exp->like('email', '%'.$_GET['Filtre'].'%');
															 });
				}
			}
			$query = $this->Users
						// Use the plugins 'search' custom finder and pass in the
						// processed query params
						->find('search', $this->Users->filterParams($this->request->query));
						// You can add extra things to the query if you need to
						//->contain(['Comments'])
						//->where(['title IS NOT' => null]);

			$this->paginate = [
				'contain' => ['Profils', 'Niveauetudes', 'Filiereetudes']
			];
			$this->set('users', $this->paginate($query));
			$this->set(compact('lapage'));
			$this->set(compact('meslistes'));
			$this->set('_serialize', ['bookmarks']);
		}else{
			/*$bookmarks = TableRegistry::get('bookmarks');
			$bookmarks = $bookmarks->find('All')->order(['title' => 'ASC']);
			$this->set('bookmarks', $this->paginate($bookmarks));
			$this->set('_serialize', ['bookmarks']);*/
			$query = $this->Users
						// Use the plugins 'search' custom finder and pass in the
						// processed query params
						->find('search', $this->Users->filterParams($this->request->query))
						->where(['profil_id ' => '1'])
						->order(['Users.created' => 'DESC']);
						// You can add extra things to the query if you need to
						//->contain(['Comments'])
						//->where(['title IS NOT' => null]);
			$this->paginate = [
				'contain' => ['Profils', 'Niveauetudes', 'Filiereetudes']
			];
			$this->set('users', $this->paginate($query));
			$this->set(compact('lapage'));
			$this->set('_serialize', ['users']);
		}


		/*$query = $this->Users
					// Use the plugins 'search' custom finder and pass in the
					// processed query params
					->find('search', $this->Users->filterParams($this->request->query))
					->order(['Users.created' => 'DESC'])
					// You can add extra things to the query if you need to
					//->contain(['Comments'])
					->where(['profil_id ' => '1']);

		$this->paginate = [
            'contain' => ['Profils', 'Niveauetudes', 'Filiereetudes']
        ];
		$this->set('users', $this->paginate($query));
		$this->set('_serialize', ['users']);*/

		/*$this->paginate = [
            'contain' => ['Profils', 'Niveauetudes', 'Filiereetudes']
        ];
		$users = TableRegistry::get('users');
		$users = $users->find('All')->where(['users.profil_id' => '1'])->order(['users.created' => 'DESC']);
		$this->set('users', $this->paginate($users));
		$this->set('_serialize', ['users']);*/
    }

	public function internes()
    {
        $this->paginate = [
            'contain' => ['Profils']
        ];
		$internes = TableRegistry::get('users');
		$internes = $internes->find('All')->where(['users.profil_id !=' => '1'])->order(['users.nom' => 'ASC']);
		$this->set('internes', $this->paginate($internes));
		$this->set('_serialize', ['internes']);
    }

	public function editinterne($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$user = $this->Users->patchEntity($user, $this->request->data);
			$res='';
			$res1='';
			$res2='';
			$res3='';
			foreach($this->request->data['profil_id'] as $prof => $valeur) {
				$res.=$valeur;
			}
			$this->Users->profil_id=$res;
			$this->Users->niveauetude_id=$res1;
			$this->Users->filiereetude_id=$res2;
			$this->Users->coach_id=$res3;
			/*if ($this->Users->save($user)) {
				debugger::dump("Dans le save.");
				exit;*/
				$this->Users->UpdateAll(array('profil_id' => $res),array('id' => $id));
				$this->Users->UpdateAll(array('niveauetude_id' => $res1),array('id' => $id));
				$this->Users->UpdateAll(array('filiereetude_id' => $res2),array('id' => $id));
				$this->Users->UpdateAll(array('coach_id' => $res3),array('id' => $id));
				$this->Flash->success(__('L\'utilisateur a été enregistré.'));
                return $this->redirect(['action' => 'index']);
            /*} else {
                $this->Flash->error(__('Le nouvel utilisateur n\'a pas pu être enregistré. Veuillez réessayer.'));
            }*/
        }
		$profils = $this->Users->Profils->find()->combine('id', 'nom');
		$this->set(compact('user', 'profils'));
		$this->set('_serialize', ['user']);
    }

	public function welcome()
	{

	}

	/**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        /*$user = $this->Users->get($id, [
            'contain' => ['Profils', 'Bookmarks']
        ]);*/

		$user = $this->Users->get($id, [
            'contain' => ['Profils', 'Bookmarks','Filieres', 'Niveauetudes']
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$id=$this->Auth->User('id');
		$user = $this->Users->get($id, [
			'contain' => []
		]);
		$newuser = $this->Users->newEntity();
		if ($this->request->is(['patch', 'post', 'put'])) {

			$validator = new Validator();
			$validator
				->requirePresence('adresse')->allowEmpty('adresse',false)
				->requirePresence('codepostal')->allowEmpty('codepostal',false)
				->requirePresence('ville')->allowEmpty('ville',false)
				->requirePresence('telfixe')->allowEmpty('telfixe',false)
				->requirePresence('telportable')->allowEmpty('telportable',false);
			$errors = $validator->errors($this->request->data());
			if (!empty($errors)) {
				$this->Flash->error(__('Veuillez répondre à l\'ensemble des champs.'));
			}else{

				$newuser = TableRegistry::get('Users');
				$dateencours=date("Y-m-d H:i:s", time());
				$query = $newuser->query();
				$query->insert(['nom', 'prenom', 'adresse', 'codepostal', 'ville', 'telfixe', 'telportable', 'genre', 'email', 'password', 'profil_id', 'created', 'modified'])
						->values([
							'nom' => $_POST['nom'],
							'prenom' => $_POST['prenom'],
							'adresse' => $_POST['adresse'],
							'codepostal' => $_POST['codepostal'],
							'ville' => $_POST['ville'],
							'telfixe' => $_POST['telfixe'],
							'telportable' => $_POST['telportable'],
							'genre' => $_POST['genre'],
							'email' => $_POST['email'],
							'password' =>  (new DefaultPasswordHasher)->hash($_POST['password']),
							'profil_id' => $_POST['profil_id'][0],
							'created' => $dateencours,
							'modified' => $dateencours
						])
						->execute();
				$this->Flash->success('Le compte du nouvel utilisateur interne a été enregistré.');
				return $this->redirect(['action' => 'index']);
			}
		}
		$profils = $this->Users->Profils->find()->combine('id', 'nom');
		$this->set(compact('newuser', 'user', 'profils'));
		$this->set('_serialize', ['newuser']);

    }

	/*public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('L\'user a été sauvegardé'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('L\'user n\'a pas été sauvegardé. Merci de réessayer.'));
            }
        }
    }*/


    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    /*public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('L\'utilisateur a été enregistré.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Le nouvel utilisateur n\'a pas pu être enregistré. Veuillez réessayer.'));
            }
        }
        //$profils = $this->Users->Profils->find('list', ['limit' => 200]);
        //$this->set(compact('user', 'profils'));
		$profils = $this->Users->Profils->find()->combine('id', 'nom');
		$this->set(compact('user', 'profils'));
        $this->set('_serialize', ['user']);
    }*/
	public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);

		if ($this->request->is(['patch', 'post', 'put'])) {
			$user = $this->Users->patchEntity($user, $this->request->data);
			$res='';
			$res1='';
			$res2='';
			/*foreach($this->request->data['profil_id'] as $prof => $valeur) {
				$res.=$valeur;
			}*/
			$res=$this->request->data['profil_id'];
			/*foreach($this->request->data['niveauetude_id'] as $niv => $valeur1) {
				$res1.=$valeur1;
			}*/
			$res1=$this->request->data['niveauetude_id'];
			/*foreach($this->request->data['filiereetude_id'] as $fil => $valeur2) {
				$res2.=$valeur2;
			}*/
			$res2=$this->request->data['filiereetude_id'];
			$this->Users->profil_id=$res;
			$this->Users->niveauetude_id=$res1;
			$this->Users->filiereetude_id=$res2;
			if ($this->Users->save($user)) {
				$this->Users->UpdateAll(array('profil_id' => $res),array('id' => $id));
				$this->Users->UpdateAll(array('niveauetude_id' => $res1),array('id' => $id));
				$this->Users->UpdateAll(array('filiereetude_id' => $res2),array('id' => $id));
				$this->Flash->success(__('L\'utilisateur a été enregistré.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Le nouvel utilisateur n\'a pas pu être enregistré. Veuillez réessayer.'));
            }
        }
		$coachs = TableRegistry::get('Users');
		$coachs = $coachs->find('All')->where(['profil_id' => [2,3,9,10,11]], ['profil_id' => 'integer[]']);
		$profils = $this->Users->Profils->find()->combine('id', 'nom');
		$niveauetudes = $this->Users->Niveauetudes->find()->combine('id', 'niveau');
		$filiereetudes = $this->Users->Filiereetudes->find()->combine('id', 'filiere');
		$this->set(compact('user', 'profils', 'niveauetudes', 'filiereetudes', 'coachs'));
        $this->set('_serialize', ['user']);
    }

	public function editcompte($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []

        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
			$user = $this->Users->patchEntity($user, $this->request->data);
			$dateencours=date("Y-m-d H:i:s", time());
			$user->lastlogin=$dateencours;
			$user->niveauetude_id=$_POST['niveauetude_id'][0];
			$user->filiereetude_id=$_POST['filiereetude_id'][0];
			if ($this->Users->save($user)) {
				// Notification aux Coachs d'une Modification dans le Compte Client
				$this->getMailer('User')->send('modifcompte',[$_POST]);
				$this->Flash->success(__('Les modifications ont bien été enregistrées.'));
                //return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Les modifications n\'ont pas pu être enregistrées. Veuillez réessayer.'));
            }
        }
		$coachs = TableRegistry::get('Users');
		$coachs = $coachs->find('All')->where(['profil_id' => [2,3,9,10,11]], ['profil_id' => 'integer[]']);
		$profils = $this->Users->Profils->find()->combine('id', 'nom');
		$niveauetudes = $this->Users->Niveauetudes->find()->combine('id', 'niveau');
		$filiereetudes = $this->Users->Filiereetudes->find()->combine('id', 'filiere');
		$this->set(compact('user', 'profils', 'niveauetudes', 'filiereetudes','coachs'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('L\'utilisateur a bien été effacé.'));
        } else {
            $this->Flash->error(__('L\'utilisateur n\'a pas pu être supprimé. Veuillez réessayer.'));
        }
        return $this->redirect(['action' => 'index']);
    }

	public function login()
	{
		if(isset($_GET['inscripartiel'])){
			if ($this->request->is('post')) {
				$user = $this->Auth->identify();
				$profiluser=$user['profil_id'];
				$lastloginuser=$user['lastlogin'];
				if ($user) {
					$this->Auth->setUser($user);
					if($profiluser==1 || $profiluser==12){
						//var_dump($_GET);
						/*if($_GET['choixpack']==2){
							return $this->redirect($this->Auth->redirectUrl(array('controller' => 'users', 'action' => 'abonnement2', $_GET['idnewuser'], $_GET['choixpack'], 0, 0)));

						}else{
							return $this->redirect($this->Auth->redirectUrl(array('controller' => 'users', 'action' => 'suivant', $_GET['idnewuser'], $_GET['choixpack'], 0, 0)));
						}*/
						return $this->redirect($this->Auth->redirectUrl(array('controller' => 'users', 'action' => 'suivant', $_GET['idnewuser'], $_GET['choixpack'], $_GET['choixmodule'])));
					}
				}
				$this->Flash->error('Votre nom d\'utilisateur ou mot de passe est incorrect.');
			}
		}else{
			//exit;
			if ($this->request->is('post')) {
				$user = $this->Auth->identify();
				$profiluser=$user['profil_id'];
				$lastloginuser=$user['lastlogin'];
				if ($user) {
					$this->Auth->setUser($user);
					//$this->getMailer('User')->send('welcome',[$user]);
					//return $this->redirect($this->Auth->redirectUrl());
					if($profiluser==1 || $profiluser==12){
						if(is_null($lastloginuser)){
							return $this->redirect(array('controller' => 'users', 'action' => 'welcome'));
							//return $this->redirect(array('controller' => 'Users', 'action' => 'inscriptionko'));
						}else{
							if($profiluser==1 || $profiluser==12){
								//return $this->redirect($this->Auth->redirectUrl(array('controller' => 'Docclientpartages', 'action' => 'index')));
								return $this->redirect(array('controller' => 'users', 'action' => 'welcome'));
							}else{
								return $this->redirect($this->Auth->redirectUrl(array('controller' => 'documents', 'action' => 'index')));
							}
						}
					}
					elseif($profiluser!=1){
						if($profiluser==8){
							return $this->redirect($this->Auth->redirectUrl(array('controller' => 'filieres', 'action' => 'index')));
						}else{
							if($profiluser==7){
								return $this->redirect($this->Auth->redirectUrl(array('controller' => 'bookmarks', 'action' => 'index')));
							}else{
								if(($profiluser==2) || ($profiluser==3) || ($profiluser==9) || ($profiluser==10) || ($profiluser==11)){
									return $this->redirect($this->Auth->redirectUrl(array('controller' => 'repqcmpersonnalites', 'action' => 'index')));
								}else{
									return $this->redirect($this->Auth->redirectUrl(array('controller' => 'bookmarks', 'action' => 'index')));
								}
							}
						}
						/*if(($profiluser==8) || ($profiluser==10)){
							return $this->redirect($this->Auth->redirectUrl(array('controller' => 'filieres', 'action' => 'index')));
						}else{
							return $this->redirect($this->Auth->redirectUrl(array('controller' => 'bookmarks', 'action' => 'index')));
						}*/
					}
				}
				$this->Flash->error('Votre nom d\'utilisateur ou mot de passe est incorrect.');
			}
		}
	}

	public function logout()
	{
		$this->Flash->success('Vous êtes maintenant déconnecté.');
		return $this->redirect($this->Auth->logout());
	}

	/*public function isAuthorized($user)
	{

		// Tous les utilisateurs "Administrateur" peuvent ajouter des utilisateurs
		//if ($this->request->action === 'add') {
		//	return true;
		//}

		// L'utilisateur "Client" et les autres profils peuvent éditer leur profil et le supprimer
		if (in_array($this->request->action, ['edit', 'delete'])) {
			$articleId = (int)$this->request->params['pass'][0];
			if ($this->Users->isOwnedBy($userId, $user['id'])) {
				return true;
			}
		}

		return parent::isAuthorized($user);
	}*/

	public function inscription(){

		$numquestion='';
		$query = TableRegistry::get('qcmpersonnalites')->find(); // enregistrement de la requête SELECT * FROM qcmpersonnalites
		$contextes = TableRegistry::get('Typecontextes');
		$contextes = $contextes->find('All')->toArray(); // résultat de la requête sur Typecontextes dans un tableau $contextes
		$this->set(compact('query', 'contextes')); // on place chacune des deux variables dans un tableau
		if ($this->request->is('post')) {  // on vérifie la présence des éléments transmis par le formulaire et son type de données : post
			if(!empty($_POST['justificatif'])){
				$justificatif=$_POST['justificatif'];
			}
			/* création d'un objet Validator pour paramétrer les règles de validation des données reçues*/
			$validator = new Validator();
			$validator
				//->requirePresence('qcm_q1', ['mode' => 'true', 'message' => 'Champs obligatoire'])
				->requirePresence('qcm_q1')->allowEmpty('qcm_q1', false)
				//->add('qcm_q1', ['rule'=> 'notBlank', 'message' => 'invalid syntaxe'])
				->requirePresence('qcm_q2')->allowEmpty('qcm_q2', false)
				->requirePresence('qcm_q3')->allowEmpty('qcm_q3', false)
				->requirePresence('qcm_q4')->allowEmpty('qcm_q4', false)
				->requirePresence('qcm_q5')->allowEmpty('qcm_q5', false)
				->requirePresence('qcm_q6')->allowEmpty('qcm_q6', false)
				->requirePresence('qcm_q7')->allowEmpty('qcm_q7', false)
				->requirePresence('qcm_q8')->allowEmpty('qcm_q8', false)
				->requirePresence('qcm_q9')->allowEmpty('qcm_q9', false)
				->requirePresence('qcm_q10')->allowEmpty('qcm_q10', false)
				->requirePresence('qcm_q11')->allowEmpty('qcm_q11', false)
				->requirePresence('qcm_q12')->allowEmpty('qcm_q12', false)
				->requirePresence('email')
				->requirePresence('prenom')
				->requirePresence('password')
				->add('telfixe', 'valid', 	['rule' => 'numeric',
											 'message' => 'Merci de saisir votre téléphone fixe dans un format valide (exemple 0404040404).'
											])->allowEmpty('telfixe', true)
				->add('telportable', 'valid', 	['rule' => 'numeric',
												 'message' => 'Merci de saisir votre téléphone portable dans un format valide (exemple 0606060606).'
											])->allowEmpty('telportable', false)
				->add('email', 'validFormat', 	[
													'rule' => 'email',
													'message' => 'Votre adresse mail doit être une adresse mail valide.'
												]);
			$errors = $validator->errors($this->request->data()); // test du renvoi d'erreurs dans l'objet Validator
			if (!empty($errors)) { // si il existe des erreurs dans les données reçues :

				foreach($errors as $keyerror => $valerror){ // on boucle sur les éléments du tableau d'erreur renvoyé
					switch($keyerror){      // pour chaque élément, on fait une analyse par cas sur les éléments
						case 'qcm_q1':				// et on enregistre dans numquestion le numéro de l'élément invalide (si question)
							$numquestion='1';   // ou 'email' s'il s'agit de l'élément email
							break;
						case 'qcm_q2':
							$numquestion='2';
							break;
						case 'qcm_q3':
							$numquestion='3';
							break;
						case 'qcm_q4':
							$numquestion='4';
							break;
						case 'qcm_q5':
							$numquestion='5';
							break;
						case 'qcm_q6':
							$numquestion='6';
							break;
						case 'qcm_q7':
							$numquestion='7';
							break;
						case 'qcm_q8':
							$numquestion='8';
							break;
						case 'qcm_q9':
							$numquestion='9';
							break;
						case 'qcm_q10':
							$numquestion='10';
							break;
						case 'qcm_q11':
							$numquestion='11';
							break;
						case 'qcm_q12':
							$numquestion='12';
							break;
						case 'email':
							$numquestion='email';
							break;
					}
					if($numquestion==='email'){  // on teste si l'erreur concerne le champs 'email' : rule => email
						$this->Flash->error(__('Veuillez renseigner une adresse mail valide ou cette adresse mail est déjà utilisée.'));
					}
					else {
						die($numquestion);
						$this->Flash->error(__('Veuillez répondre à la question n° '.$numquestion));
					}
				}
			}
			else {

				$email_exist = TableRegistry::get('Users');
				$email_exist = $email_exist->find('All')->where(['email' => $_POST['email']])->toArray();

					if(!empty($email_exist)){

					return $this->redirect(array('controller' => 'Users', 'action' => 'inscriptionko'));
				}else{
					$reponseqcms = TableRegistry::get('repqcmpersonnalites');
					$dateencours=date("Y-m-d H:i:s", time());
					$query = $reponseqcms->query();

					$query->insert(['prenom', 'user_email', 'password', 'telfixe', 'telportable', 'contexte', 'rep_q1', 'rep_q2', 'rep_q3', 'rep_q4', 'rep_q5', 'rep_q6', 'rep_q7', 'rep_q8', 'rep_q9', 'rep_q10', 'rep_q11', 'rep_q12', 'created'])
							->values([
								'prenom' => $_POST['prenom'],
								'user_email' => $_POST['email'],
								'password' => $_POST['password'],
								'telfixe' => $_POST['telfixe'],
								'telportable' => $_POST['telportable'],
								'contexte' => $_POST['contexte'],
								'rep_q1' => $_POST['qcm_q1'],
								'rep_q2' => $_POST['qcm_q2'],
								'rep_q3' => $_POST['qcm_q3'],
								'rep_q4' => $_POST['qcm_q4'],
								'rep_q5' => $_POST['qcm_q5'],
								'rep_q6' => $_POST['qcm_q6'],
								'rep_q7' => $_POST['qcm_q7'],
								'rep_q8' => $_POST['qcm_q8'],
								'rep_q9' => $_POST['qcm_q9'],
								'rep_q10' => $_POST['qcm_q10'],
								'rep_q11' => $_POST['qcm_q11'],
								'rep_q12' => $_POST['qcm_q12'],
								'created' => $dateencours
							])
							->execute();
              //$reponseqcms=$reponseqcms->find('All')->where(['user_email' => $_POST['email']])->toArray();
					$reponseqcms = TableRegistry::get('Users');
					//$reponseqcms=$reponseqcms->find('All')->where(['prenom' => $_POST['prenom']])->toArray();
					$dateencours=date("Y-m-d H:i:s", time());
					//print_r($reponseqcms[0]);
					$query = $reponseqcms->query();
					$query->insert(['prenom', 'email', 'password', 'telportable', 'telfixe', 'profil_id', 'created'])
							->values([
								'prenom' => $_POST['prenom'],
								'email' => $_POST['email'],
								'password' =>  (new DefaultPasswordHasher)->hash($_POST['password']),
								'telfixe' => $_POST['telfixe'],
								'telportable' => $_POST['telportable'],
								'profil_id' => 1,
								'created' => $dateencours
							])

							->execute();
      //die($reponseqcms->toArray());
/* Attention= fonction de mailing à réactiver en production !!! */

					/*$this->Flash->success('Votre inscription a bien été enregistrée. Vous recevrez prochainement un mail de confirmation.
											Vous pouvez dès à présent vous connecter avec le mail et mot de passe renseignés.');*/
					//$this->getMailer('User')->send('inscription',[$_POST]);
					//$this->getMailer('User')->send('qcmmos1',[$_POST]);
					//$this->getMailer('User')->send('nouveauclient',[$_POST]);

					return $this->redirect(array('controller' => 'Users', 'action' => 'inscriptionok'));
					//return $this->redirect(array('controller' => 'Users', 'action' => 'login'));
				}
			}
		}
		$this->Flash->error('Votre nom d\'utilisateur ou mot de passe est incorrect.');
	}

	public function inscriptionok()
    {
		/*$msg='Vous avez bien rempli votre bilan 360° MOS 1.
		Vous allez recevoir un email de confirmation dans les prochaines minutes.';
		$this->cell('MessageInscription', array($msg));
		$this->getMailer('User')->send('inscription',[$_POST]);*/
    }

	public function inscriptionko()
    {
		/*$msg='Vous avez bien rempli votre bilan 360° MOS 1.
		Vous allez recevoir un email de confirmation dans les prochaines minutes.';
		$this->cell('MessageInscription', array($msg));
		$this->getMailer('User')->send('inscription',[$_POST]);*/
    }

	/* Inscription Partielle pour Paiement Direct à partir du Site Vitrine */
	public function inscriptionpartielle($choixpack=null, $choixmodule=null)
	{
		/*debugger::dump($_GET);
		debugger::dump($choixpack);

		var_dump($_GET);*/
		//var_dump($choixpack);
		//var_dump($choixmodule);
		/*exit;*/
		//die($_POST['file']);
		$numquestion='';
		$query = TableRegistry::get('qcmpersonnalites')->find();
		$contextes = TableRegistry::get('Typecontextes');
		$contextes = $contextes->find('All')->toArray();
		$this->set(compact('query', 'contextes'));
		if ($this->request->is('post')) {
			$validator = new Validator();
			$validator
				->add('qcm_q1', 'valid', 	['rule' => 'numeric',
											])->allowEmpty('qcm_q1', true)
				->add('qcm_q2', 'valid', 	['rule' => 'numeric',
											])->allowEmpty('qcm_q2', true)
				->add('qcm_q3', 'valid', 	['rule' => 'numeric',
											])->allowEmpty('qcm_q3', true)
				->add('qcm_q4', 'valid', 	['rule' => 'numeric',
											])->allowEmpty('qcm_q4', true)
				->add('qcm_q5', 'valid', 	['rule' => 'numeric',
											])->allowEmpty('qcm_q5', true)
				->add('qcm_q6', 'valid', 	['rule' => 'numeric',
											])->allowEmpty('qcm_q6', true)
				->add('qcm_q7', 'valid', 	['rule' => 'numeric',
											])->allowEmpty('qcm_q7', true)
				->add('qcm_q8', 'valid', 	['rule' => 'numeric',
											])->allowEmpty('qcm_q8', true)
				->add('qcm_q9', 'valid', 	['rule' => 'numeric',
											])->allowEmpty('qcm_q9', true)
				->add('qcm_q10', 'valid', 	['rule' => 'numeric',
											])->allowEmpty('qcm_q10', true)
				->add('qcm_q11', 'valid', 	['rule' => 'numeric',
											])->allowEmpty('qcm_q11', true)
				->add('qcm_q12', 'valid', 	['rule' => 'numeric',
											])->allowEmpty('qcm_q12', true)
				->requirePresence('email')
				->requirePresence('prenom')
				->requirePresence('password')
				->add('telfixe', 'valid', 	['rule' => 'numeric',
											 'message' => 'Merci de saisir votre téléphone fixe dans un format valide (exemple 0404040404).'
											])->allowEmpty('telfixe', true)
				->add('telportable', 'valid', 	['rule' => 'numeric',
												 'message' => 'Merci de saisir votre téléphone portable dans un format valide (exemple 0606060606).'
											])->allowEmpty('telportable', false)
				->add('email', 'validFormat', 	[
													'rule' => 'email',
													'message' => 'Votre adresse mail doit être une adresse mail valide.'
												]);
			$errors = $validator->errors($this->request->data());
			if (!empty($errors)) {
				foreach($errors as $keyerror => $valerror){
					switch($keyerror){
						case 'email':
							$numquestion='email';
							break;
					}
					if($numquestion==='email'){
						$this->Flash->error(__('Veuillez renseigner une adresse mail valide ou cette adresse mail est déjà utilisée.'));
					}
				}
			}
			else
			{
				$email_exist = TableRegistry::get('Users');
				$email_exist = $email_exist->find('All')->where(['email' => $_POST['email']])->toArray();
				if(!empty($email_exist)){
					return $this->redirect(array('controller' => 'Users', 'action' => 'inscriptionko'));
				}else{
					$reponseqcms = TableRegistry::get('Repqcmpersonnalites');
					$dateencours=date("Y-m-d H:i:s", time());
					$query = $reponseqcms->query();
					$query->insert(['prenom', 'user_email', 'password', 'telfixe', 'telportable', 'contexte', 'rep_q1', 'rep_q2', 'rep_q3', 'rep_q4', 'rep_q5', 'rep_q6', 'rep_q7', 'rep_q8', 'rep_q9', 'rep_q10', 'rep_q11', 'rep_q12', 'statutreponse', 'created'])
							->values([
								'prenom' => $_POST['prenom'],
								'user_email' => $_POST['email'],
								'password' => $_POST['password'],
								'telfixe' => $_POST['telfixe'],
								'telportable' => $_POST['telportable'],
								'contexte' => 0,
								'rep_q1' => 0,
								'rep_q2' => 0,
								'rep_q3' => 0,
								'rep_q4' => 0,
								'rep_q5' => 0,
								'rep_q6' => 0,
								'rep_q7' => 0,
								'rep_q8' => 0,
								'rep_q9' => 0,
								'rep_q10' => 0,
								'rep_q11' => 0,
								'rep_q12' => 0,
								'statutreponse' => 0,
								'created' => $dateencours
							])
							->execute();
					$reponseqcms = TableRegistry::get('Users');
					$dateencours=date("Y-m-d H:i:s", time());
					$query = $reponseqcms->query();
					$query->insert(['prenom', 'email', 'password', 'telportable', 'telfixe', 'profil_id', 'created'])
							->values([
								'prenom' => $_POST['prenom'],
								'email' => $_POST['email'],
								'password' =>  (new DefaultPasswordHasher)->hash($_POST['password']),
								'telfixe' => $_POST['telfixe'],
								'telportable' => $_POST['telportable'],
								'profil_id' => 1,
								'created' => $dateencours
							])
							->execute();
					$reponseusers = TableRegistry::get('Users');
					$reponseusers = $reponseusers->find('All')->where(['email' => $_POST['email'], 'prenom' => $_POST['prenom']])->toArray();
					$idnewuser = $reponseusers[0]['id'];
					//$this->getMailer('User')->send('inscription',[$_POST]); // à réactiver en production !!!
					//$this->getMailer('User')->send('qcmmos1',[$_POST]);
					//$this->getMailer('User')->send('nouveauclient',[$_POST]); à réactiver en production !!!

					return $this->redirect(array('controller' => 'Users', 'action' => 'login', 'inscripartiel' => 1, 'idnewuser' => $idnewuser, 'choixpack' => $choixpack, 'choixmodule' => $choixmodule ));
				}
			}
		}
	}

	public function verifcompte(){
		if(isset($_GET) && !empty($_GET['choixpack'])){
			
			/*1 er cas : si c'est un pack */
			if($_GET['choixpack']=="3/13" || $_GET['choixpack']=="1/14" || $_GET['choixpack']=="4/15"){
				$choixpack=$_GET['choixpack']; // ce n'est pas un tableau ici
				$fmr = explode("/", $choixpack);
				$choixpack=$fmr[0];
				$choixmodule=$fmr[1];
			}
			/*2ème cas : si il n'y a q'un module */
			elseif(is_array($_GET['choixpack']) && count($_GET['choixpack'])==1){
				$choixpack=$_GET['choixpack'][0];
				$fmr = explode("/", $choixpack);
				$choixpack=$fmr[0];
				$choixmodule=$fmr[1];
			}
			/* si il y a plusieurs modules*/
			else {
			for($i=0; $i<count($_GET['choixpack']); $i++){

			  $tab_pack[$i]=explode("/", $_GET['choixpack'][$i]);
			  $choixpack[$i]=$tab_pack[$i][0];
			  $choixmodule[$i]=$tab_pack[$i][1];

			}
			$choixpack=$choixpack[0];
			$choixmodule=implode(",", $choixmodule);
			}
}


		if ($this->request->is('post')) {
			$user = $this->Auth->identify();
			$profiluser=$user['profil_id'];
			$lastloginuser=$user['lastlogin'];
			if($user) {
				$this->Auth->setUser($user);
				if($profiluser==1 || $profiluser==12){    // à modifier pour partenaire ?
					//if($choixpack==2){
						//return $this->redirect($this->Auth->redirectUrl(array('controller' => 'users', 'action' => 'abonnement2', $user['id'], $choixpack, 0, 0)));

					//}else{
						//return $this->redirect($this->Auth->redirectUrl(array('controller' => 'users', 'action' => 'suivant', $user['id'], $choixpack, 0, 0)));
						return $this->redirect($this->Auth->redirectUrl(array('controller' => 'users', 'action' => 'suivant', $user['id'], $choixpack, $choixmodule)));
					//}
				}
			}
			$this->Flash->error('Votre nom d\'utilisateur ou mot de passe est incorrect.');
		}
		$this->set(compact('choixpack', 'choixmodule'));
	}

	public function forgotpassword(){
		if ($this->request->is('post')) {
			$this->redirect(['controller' => 'Oublipasswords', 'action' => 'edit']);
			//$this->Flash->success('Inscription demandée.');
		}
	}

	public function nospacks(){
		/*if ($this->request->is('post')) {
			$this->redirect(['controller' => 'Oublipasswords', 'action' => 'edit']);
			//$this->Flash->success('Inscription demandée.');
		}*/
	}

	public function abonnement(){
		$id=$this->Auth->User('id');
		$user = $this->Users->get($this->Auth->User('id'), [
            'contain' => []
        ]);
		/* Recherche dans la Table SOGENACTIFS la Présence d'un Abonnement */
		//$abonnement = TableRegistry::get('abonnements')->find('All')->where(['user_id' => $id])->order(['id' =>'DESC'])->toArray();
		$abonnement = TableRegistry::get('abonnements')->find()->where(['user_id' => $id])->order(['id' =>'DESC'])->first();
		if(isset($abonnement) && !is_null($abonnement)){
			$sogenactif = TableRegistry::get('sogenactifs')->find()->where(['order_id' => $abonnement['order_id']])->order(['id' =>'DESC'])->first();
			if(isset($sogenactif) && !is_null($sogenactif)){
				//debugger::dump('Abonnement déjà pris');
				$this->redirect(['action' => 'abonnementpris', $id]);
			}
		}
		/*if(($id==11)||($id==111)||($id==325)||($id==1070)){*/
			if ($this->request->is(['patch', 'post', 'put'])) {
				if(isset($_POST['promo']) && !is_null($_POST['promo']) && !empty($_POST['promo'])){
					$promo_exist = TableRegistry::get('Typepromos');
					$promo_exist = $promo_exist->find('All')->where(['typepromo' => $_POST['promo']])->toArray();
					if(empty($promo_exist)){
						$this->Flash->error(__('Le code promotionnel saisi est invalide. Veuillez le corriger ou le supprimer.'));
					} else {
						if(isset($_POST['choix']) && !is_null($_POST['choix'])){
							if(isset($_POST['option']) && !is_null($_POST['option']) && ($_POST['choix']==2)){
								$option=implode("','", $_POST['option']);
							}else{
								$option=0;
							}
							$promo=$promo_exist[0]['id'];
							$this->redirect(['action' => 'suivant', $id, $_POST['choix'], $option, $promo]);
						}else{
							$this->Flash->error(__('Merci de bien vouloir choisir votre formule d\'abonnement avant de poursuivre.'));
						}
					}
				} else {
					if(isset($_POST['choix']) && !is_null($_POST['choix'])){
						if(isset($_POST['option']) && !is_null($_POST['option']) && ($_POST['choix']==2)){
							$option=implode("','", $_POST['option']);
						}else{
							$option=0;
						}
						$promo=0;
						$this->redirect(['action' => 'suivant', $id, $_POST['choix'], $option, $promo]);
					}else{
						$this->Flash->error(__('Merci de bien vouloir choisir votre formule d\'abonnement avant de poursuivre.'));
					}
				}
			}
			$pack = TableRegistry::get('typepacks')->find();
			$option = TableRegistry::get('typeoptions')->find();
			$promo = TableRegistry::get('typepromos')->find();
			$this->set(compact('user', 'pack', 'option', 'promo'));
			$this->set('_serialize', ['user']);
		/*}else{
			$this->redirect(['action' => 'editcompte', $id]);
		}*/
    }

	public function abonnementpris($iduser=null){
		$user = $this->Users->get($iduser, ['contain' => []]);
		$abonnement = TableRegistry::get('abonnements')->find()->where(['user_id' => $iduser])->order(['id' =>'DESC'])->first();
		if(isset($abonnement) && !is_null($abonnement)){
			$idoption2=str_replace("'","",$abonnement['listoption']);
			$lesoptions=explode(',', $idoption2);
			$pack = TableRegistry::get('typepacks')->find()->where(['id' => $abonnement['pack_id']])->toArray();
			$option = TableRegistry::get('typeoptions')->find()->where(['id IN' => $lesoptions])->toArray();
			$promo = TableRegistry::get('typepromos')->find()->where(['id' => $abonnement['promo_id']])->toArray();
			$this->set(compact('user', 'pack', 'option', 'promo','abonnement'));
			$this->set('_serialize', ['user']);
		}
	}

	public function suivantbis($iduser=null, $idpack=null){
		$lechoixpack = '';
		if ($this->request->is(['patch', 'post', 'put'])) {
			if(isset($_POST['choixpack']) && !empty($_POST['choixpack'])){
				//die($_POST['choixpack']);
				// Redirection sur une page de saisie des informations du payeurs
				foreach($_POST['choixpack'] as $key => $val)
				{
					$lechoixpack = $lechoixpack.$val.",";
				}
				$this->redirect(['action' => 'suivant', $iduser, $idpack, $lechoixpack]);
			}

		}
		$user = $this->Users->get($iduser, ['contain' => []]);
		//$pack = TableRegistry::get('typepacks')->find()->where(['id' => $idpack])->toArray();
		$pack = TableRegistry::get('typepacks')->find()->where(['classe' => $idpack])->toArray();
		$this->set(compact('user', 'pack'));
		$this->set('_serialize', ['user']);
	}

	public function suivant($iduser=null, $idpack=null, $idoption=null, $idpromo=null){
		//$idoption2=str_replace("'","",$idoption);
		/*echo 'idpack : '.$idpack.'<br/>';
		echo 'idoption : '.$idoption.'<br/>';
		echo 'iduser : '.$iduser.'<br/>';*/
		$lesoptions=explode(',', $idoption);
		if ($this->request->is(['patch', 'post', 'put'])) {

			if((isset($_POST['promo']) && !empty($_POST['promo'])) || ($idpromo!=0)){
				if((isset($_POST['promo']) && !empty($_POST['promo']))){
					$promo = TableRegistry::get('typepromos')->find()->where(['typepromo' => $_POST['promo']])->toArray();
				}else{
					$promo = TableRegistry::get('typepromos')->find()->where(['id' => $idpromo])->toArray();
				}
				if(!isset($promo) || is_null($promo) || empty($promo)){
					$this->Flash->error(__('Le code promotionnel saisi est invalide. Veuillez le corriger ou le supprimer.'));
				}else{
					// Redirection sur une page de saisie des informations du payeurs
					//$this->redirect(['action' => 'payeur', $iduser, $idpack, $idoption, $promo[0]['id']]);
					$this->redirect(['action' => 'payeur', $iduser, $idpack, $idoption, $promo[0]['id']]);
				}
			}else{
				// Redirection sur une page de saisie des informations du payeurs
				//$this->redirect(['action' => 'payeur', $iduser, $idpack, $idoption, $idpromo]);
				$this->redirect(['action' => 'payeur', $iduser, $idpack, $idoption, $promo[0]['id']]);
			}
			// Redirection sur une page de saisie des informations du payeurs
			//$this->redirect(['action' => 'payeur', $iduser, $idpack, $idoption, $idpromo]);
		}

		$user = $this->Users->get($iduser, ['contain' => []]);
		if($user['profil_id']==12){
			$user['estPartenaire']=true;
		}
		else{
			$user['estPartenaire']=false;
		}
		//$pack = TableRegistry::get('typepacks')->find()->where(['id' => $idpack])->toArray();
		$pack = TableRegistry::get('typepacks')->find()->where(['classe' => $idpack, 'id IN' => $lesoptions])->toArray();
		//$category=TableRegistry::get('typepacks')->find('All')->where(['category' => 'Pack'])->toArray();
		//$option = TableRegistry::get('typeoptions')->find()->where(['id IN' => $lesoptions])->toArray();
		//$promo = TableRegistry::get('typepromos')->find()->where(['id' => $idpromo])->toArray();
		$promo = TableRegistry::get('typepromos')->find('All')->toArray();
		//$this->set(compact('user', 'pack', 'option', 'promo', 'idpromo'));
		$this->set(compact('user', 'pack', 'promo', 'idpromo'));
		$this->set('_serialize', ['user']);

		foreach ($pack as $key => $val) {
			if($pack[$key]['id']==13 || $pack[$key]['id']==14 || $pack[$key]['id']==15) {
			$pack[$key]['category']='Pack';   // on insère un élément catagory dans le tableau $pack
			}
			else{
				$pack[$key]['category']='Module';
			}
		}
	}

	public function abonnement2($iduser=null, $idpack=null, $idoption=null, $idpromo=null){
		$idoption2=str_replace("'","",$idoption);
		$lesoptions=explode(',', $idoption2);
		if ($this->request->is(['patch', 'post', 'put'])) {
			if(isset($_POST['promo']) && !is_null($_POST['promo']) && !empty($_POST['promo'])){
				$promo_exist = TableRegistry::get('Typepromos');
				$promo_exist = $promo_exist->find('All')->where(['typepromo' => $_POST['promo']])->toArray();
				if(empty($promo_exist)){
					$this->Flash->error(__('Le code promotionnel saisi est invalide. Veuillez le corriger ou le supprimer.'));
				} else {
					if(isset($_POST['option']) && !is_null($_POST['option']) && ($idpack==2)){
						$option=implode("','", $_POST['option']);
					}else{
						$option=0;
					}
					$promo=$promo_exist[0]['id'];
					$this->redirect(['action' => 'suivant', $iduser, $idpack, $option, $promo]);
				}
			} else {
				if(isset($_POST['option']) && !is_null($_POST['option']) && ($idpack==2)){
					$option=implode("','", $_POST['option']);
				}else{
					$option=0;
				}
				$promo=0;
				$this->redirect(['action' => 'suivant', $iduser, $idpack, $option, $promo]);
			}
		}
		$user = $this->Users->get($iduser, ['contain' => []]);
		$pack = TableRegistry::get('typepacks')->find()->where(['id' => $idpack])->toArray();
		$options = TableRegistry::get('typeoptions')->find();
		$option = TableRegistry::get('typeoptions')->find()->where(['id IN' => $lesoptions])->toArray();
		$promo = TableRegistry::get('typepromos')->find()->where(['id' => $idpromo])->toArray();
		$this->set(compact('user', 'pack', 'option', 'options', 'promo'));
		$this->set('_serialize', ['user']);
	}

	private function _validateDate($date, $format = 'd/m/Y'){
		if(strptime($date, "%d/%m/%Y")){
			$d = Time::createFromFormat($format, $date);
			return $d && $d->format($format) == $date;
		}else{
			return false;
		}
	}

	public function payeur($iduser=null, $idpack=null, $idoption=null, $idpromo=null){
		$lesoptions=explode(',', $idoption);
		//$idoption2=str_replace("'","",$idoption);
		$lesoptions=explode(',', $idoption);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$solde=number_format($_POST['montantglobal'],2,',',' ');
			if(!is_null($solde)) {
				$user = $this->Users->get($iduser, ['contain' => []]);
				$abonnements = TableRegistry::get('Abonnements');
				$dateencours=date("Y-m-d H:i:s", time());
				//$datenaissance=str_replace(' ','',$_POST['datenaissance']);

				$datenaissance=$_POST['datenaiss'];

				/*if(strtotime($datenaissance)===false){
					//$this->Flash->error(__('Message 1 : La date saisie est incorrecte. Veuillez respecter le format jj/mm/aaaa.'));
					echo '<script type="text/javascript">history.back();</script>';
					$this->Flash->error(__('Message 1 : La date saisie est incorrecte. Veuillez respecter le format jj/mm/aaaa.'));
					exit;
				}else{*/
					/*if($this->_validateDate($datenaissance)===false){
						//$this->Flash->error(__('Message 3 : La date saisie est incorrecte. Veuillez respecter le format jj/mm/aaaa.'));
						echo '<script type="text/javascript">history.back();</script>';
						$this->Flash->error(__('Message 1 : La date saisie est incorrecte. Veuillez respecter le format jj/mm/aaaa.'));
						exit;
					}else{
						//if(!strptime($_POST['datenaissance'], "%Y-%m-%d")){
						if(!strptime($datenaissance, "%d/%m/%Y")){
							//$this->Flash->error(__('Message 2 : La date saisie est incorrecte. Veuillez respecter le format jj/mm/aaaa.'));
							echo '<script type="text/javascript">history.back();</script>';
							$this->Flash->error(__('Message 1 : La date saisie est incorrecte. Veuillez respecter le format jj/mm/aaaa.'));
							exit;
						}else{*/

							//$datenaissance=$_POST['datenaissance'];
							/*if(strpos($_POST['datenaissance'], "-")!==false){
								$datenaissance=$_POST['datenaissance'];
							}else{
								list($jour,$mois,$annee) = sscanf($_POST['datenaissance'], "%d/%d/%d");
								$datenaissance=$annee."-".$mois."-".$jour;
							}*/

							/*if(strpos($_POST['datenaissance'], "-")!==false){
								$datenaissance=$_POST['datenaissance'];
							}else{
								list($jour,$mois,$annee) = sscanf($_POST['datenaissance'], "%d/%d/%d");
								$datenaissance=$annee."-".$mois."-".$jour;
							}*/
							//debugger::dump($datenaissance);

							//$datenaissance=date("Y-m-d", strtotime($datenaissance));
							//debugger::dump($datenaissance);
							$query = $abonnements->query();
							$query->insert(['user_id', 'pack_id', 'promo_id', 'listoption', 'payeur_nom', 'payeur_prenom', 'payeur_datenaiss', 'montant', 'blocage_mos1', 'blocage_mos2', 'blocage_mos3', 'etat_paiement', 'date_paiement', 'created'])
									->values([
										'user_id' => $iduser,
										'pack_id' => $idpack,
										'promo_id' => $idpromo,
										'listoption' => $idoption2,
										'payeur_nom' => $_POST['nom'],
										'payeur_prenom' => $_POST['prenom'],
										'payeur_datenaiss' => $datenaissance,
										//'payeur_datenaiss' => $_POST['datenaissance'],
										'montant' => $_POST['montantglobal'],
										'blocage_mos1' => 1,
										'blocage_mos2' => 0,
										'blocage_mos3' => 0,
										'etat_paiement' =>'Paiement en ligne',
										'date_paiement' => $dateencours,
										'created' => $dateencours
									])
									->execute();
							if(stripos($_SERVER['SERVER_NAME'], 'www.')!==false){
								$www='www';
							}else{
								$www='';
							}
							//$this->redirect("http://mon-orientation-scolaire.com/payment/call_request.php?amount=".$solde."&user_id=".$iduser."&user_email=".$user['email']."&user_nom=".$user['nom']."&user_prenom=".$user['prenom']."&www=".$www);
							$this->redirect(['action' => 'paiement',$iduser,$idpack,$idoption,$idpromo]);
						/*}
					}
				}*/
			}
		}
		$user = $this->Users->get($iduser, ['contain' => []]);
		if($user['profil_id']==12){
			$user['estPartenaire']=true;
		}
		else{
			$user['estPartenaire']=false;
		}

		//$pack = TableRegistry::get('typepacks')->find()->where(['id' => $idpack])->toArray();
		$pack = TableRegistry::get('typepacks')->find()->where(['classe' => $idpack, 'id IN' => $lesoptions])->toArray();
		foreach ($pack as $key => $val) {
			if($pack[$key]['id']==13 || $pack[$key]['id']==14 || $pack[$key]['id']==15) {
			$pack[$key]['category']='Pack';   // on insère un élément catagory dans le tableau $pack
			}
			else{
				$pack[$key]['category']='Module';
			}
		}
		//$option = TableRegistry::get('typeoptions')->find()->where(['id IN' => $lesoptions])->toArray();
		$promo = TableRegistry::get('typepromos')->find()->where(['id' => $idpromo])->toArray();
		//$this->set(compact('user', 'pack', 'option', 'promo'));
		$this->set(compact('user', 'pack', 'promo'));
		$this->set('_serialize', ['user']);
	}

	//public function paiement($solde=null,$iduser=null,$useremail,$usernom=null,$userprenom=null,$www=null,$idpack=null, $idoption=null, $idpromo=null){
	public function paiement($iduser=null, $idpack=null, $idoption=null, $idpromo=null){
		//$idoption2=str_replace("'","",$idoption);
		$lesoptions=explode(',', $idoption);
		$user = $this->Users->get($iduser, ['contain' => []]);
		if($user['profil_id']==12){
			$user['estPartenaire']=true;
		}
		else{
			$user['estPartenaire']=false;
		}
		//$pack = TableRegistry::get('typepacks')->find()->where(['id' => $idpack])->toArray();
		$pack = TableRegistry::get('typepacks')->find()->where(['classe' => $idpack, 'id IN' => $lesoptions])->toArray();
		foreach ($pack as $key => $val) {
			if($pack[$key]['id']==13 || $pack[$key]['id']==14 || $pack[$key]['id']==15) {
			$pack[$key]['category']='Pack';   // on insère un élément catagory dans le tableau $pack
			}
			else{
				$pack[$key]['category']='Module';
			}
		}
		//$option = TableRegistry::get('typeoptions')->find()->where(['id IN' => $lesoptions])->toArray();
		$promo = TableRegistry::get('typepromos')->find()->where(['id' => $idpromo])->toArray();
		//$this->set(compact('user', 'pack', 'option', 'promo'));
		$this->set(compact('user', 'pack', 'promo'));
		$this->set('_serialize', ['user']);
	}

	public function remerciement()
    {
		$id=$this->Auth->User('id');
		$user = $this->Users->get($this->Auth->User('id'), [
            'contain' => []
        ]);
		$_POST['prenom']=$user['prenom'];
		$_POST['email']=$user['email'];
		$_POST['emailparent']=$user['emailparent'];
		/* Récupération des Informations pour le Ticket de Paiement */
		$datejour=Time::now();
		$datejour=date('Y-m-d',strtotime($datejour));
		$abonnement = TableRegistry::get('Abonnements')->find()->where(['user_id' => $user['id'],
																		'created like ' => $datejour.'%'])->last();
		$sogenactif = TableRegistry::get('Sogenactifs')->find()->where(['order_id' => $abonnement['order_id']])->last();
		$_POST['order_id']=$abonnement['order_id'];
		$_POST['datepaiement']=$sogenactif['created'];
		$_POST['transaction']=$sogenactif['transaction_id'];
		$_POST['autorisation']=$sogenactif['authorisation_id'];
		$_POST['montant']=$abonnement['montant'];
		$this->getMailer('User')->send('paiement',[$_POST]);
		$this->getMailer('User')->send('paiement2',[$_POST]);
		$this->getMailer('User')->send('retractation',[$_POST]);
    }

	public function erreurpaiement()
    {
		$id=$this->Auth->User('id');
		$user = $this->Users->get($this->Auth->User('id'), [
            'contain' => []
        ]);
		$_POST['prenom']=$user['prenom'];
		$_POST['email']=$user['email'];
		$_POST['emailparent']=$user['emailparent'];
		$this->getMailer('User')->send('refuspaiement',[$_POST]);
    }

	public function annulationpaiement()
    {
		$id=$this->Auth->User('id');
		$user = $this->Users->get($this->Auth->User('id'), [
            'contain' => []
        ]);
		$_POST['prenom']=$user['prenom'];
		$_POST['email']=$user['email'];
		$_POST['emailparent']=$user['emailparent'];
		$this->getMailer('User')->send('annulpaiement',[$_POST]);
    }

	public function trimaildecroissant($id=null)
    {
        $query2 = TableRegistry::get('Users');
		$users = $query2->find('All')->order(['email' => 'DESC']);
		$this->set('users', $this->paginate($users));
		$this->set('_serialize', ['users']);
    }

	public function trimailcroissant($id=null)
    {
		$query2 = TableRegistry::get('Users');
		$users = $query2->find('All')->order(['email' => 'ASC']);
		$this->set('users', $this->paginate($users));
		$this->set('_serialize', ['users']);
    }

	public function trinomdecroissant($id=null)
    {
        $query2 = TableRegistry::get('Users');
		$users = $query2->find('All')->order(['nom' => 'DESC']);
		$this->set('users', $this->paginate($users));
		$this->set('_serialize', ['users']);
    }

	public function trinomcroissant($id=null)
    {
		$query2 = TableRegistry::get('Users');
		$users = $query2->find('All')->order(['nom' => 'ASC']);
		$this->set('users', $this->paginate($users));
		$this->set('_serialize', ['users']);
    }

	public function trimobildecroissant($id=null)
    {
        $query2 = TableRegistry::get('Users');
		$users = $query2->find('All')->order(['telportable' => 'DESC']);
		$this->set('users', $this->paginate($users));
		$this->set('_serialize', ['users']);
    }

	public function trimobilcroissant($id=null)
    {
		$query2 = TableRegistry::get('Users');
		$users = $query2->find('All')->order(['telportable' => 'ASC']);
		$this->set('users', $this->paginate($users));
		$this->set('_serialize', ['users']);
    }

	public function triinscriptdecroissant($id=null)
    {
        $query2 = TableRegistry::get('Users');
		$users = $query2->find('All')->order(['created' => 'DESC']);
		$this->set('users', $this->paginate($users));
		$this->set('_serialize', ['users']);
    }

	public function triinscriptcroissant($id=null)
    {
		$query2 = TableRegistry::get('Users');
		$users = $query2->find('All')->order(['created' => 'ASC']);
		$this->set('users', $this->paginate($users));
		$this->set('_serialize', ['users']);
    }

	public function parametrage()
    {
		/*$msg='Vous avez bien rempli votre bilan 360° MOS 1.
		Vous allez recevoir un email de confirmation dans les prochaines minutes.';
		$this->cell('MessageInscription', array($msg));
		$this->getMailer('User')->send('inscription',[$_POST]);*/
    }

	public function viewpdf($id)
	{
		if (!is_null($id)) {
			$mensuel = $this->Users->get($id);
			$nom=$mensuel->nom;
			$prenom=$mensuel->prenom;
			$adresse=$mensuel->adresse;
			$codepostal=$mensuel->codepostal;
			$ville=$mensuel->ville;
			$telfixe=$mensuel->telfixe;
			$telportable=$mensuel->telportable;
			$email=$mensuel->email;
			$emailparent=$mensuel->emailparent;
			$niveauetude_id=$mensuel->niveauetude_id;
			$filiereetude_id=$mensuel->filiereetude_id;
			$coach_id=$mensuel->coach_id;
			$genre=$mensuel->genre;
			$datenaissance=$mensuel->datenaissance;
			// Coach Référent
			if($coach_id!=0){
				$coachs = $this->Users->get($coach_id);
				$lecoach=$coachs->nom.' '.$coachs->prenom;
			}else{
				$lecoach='non défini';
			}
			// Filière
			if($filiereetude_id!=0){
				$filiereetudes = TableRegistry::get('Filiereetudes');
				$filiereetudes = $filiereetudes->find('All')->where(['id' => $filiereetude_id])->toArray();
				$lafiliere=$filiereetudes[0]['filiere'];
			}else{
				$lafiliere='Non renseignée';
			}
			// Niveau Etude
			if($niveauetude_id!=0){
				$niveauetudes = TableRegistry::get('Niveauetudes');
				$niveauetudes = $niveauetudes->find('All')->where(['id' => $niveauetude_id])->toArray();
				$leniveau=$niveauetudes[0]['niveau'];
			}else{
				$leniveau='Non renseigné';
			}
			// Abonnement
			$abonnements = TableRegistry::get('Abonnements');
			$abonnements = $abonnements->find('All')->where(['user_id' => $mensuel->id])->toArray();
			// SOGENACTIF
			$query2 = TableRegistry::get('Sogenactifs');
			$sogenactifs=$query2->find('All')->toArray();
			// Plannings
			$plannings = TableRegistry::get('Plannings');
			$plannings = $plannings->find('All')->where(['user_id2' => $mensuel->id])->toArray();
			// Nom du Fichier
			$filename = utf8_decode($nom).'_'.utf8_decode($prenom);
			$filename = preg_replace("#[^a-zA-Z]#", "", $filename);
			// Génération du PDF
			$view = new View();
			$view->set(compact('id', 'nom', 'prenom', 'adresse', 'codepostal', 'ville', 'telfixe', 'telportable',
								'email', 'emailparent', 'genre', 'lafiliere', 'leniveau', 'lecoach', 'abonnements',
								'sogenactifs', 'plannings', 'filename'));
			$viewdata = $view->render('Users/pdf/view', 'pdf/apercupdf');
		} else throw new Exception\ForbiddenException(__('Référence obligatoire'));
	}

	public function qcmpersonpartielle($id=null)
	{
		$dejarempli = TableRegistry::get('Repqcmpersonnalites');
		$dejarempli = $dejarempli->find('All')->where(['user_email' => $this->Auth->User('email')])->toArray();
		$numquestion='';
		$query = TableRegistry::get('qcmpersonnalites')->find();
		$contextes = TableRegistry::get('Typecontextes');
		$contextes = $contextes->find('All')->toArray();
		$this->set(compact('query', 'contextes', 'dejarempli'));
		if ($this->request->is('post')) {
			$validator = new Validator();
			$validator
				->requirePresence('qcm_q1')->allowEmpty('qcm_q1', false)
				->requirePresence('qcm_q2')->allowEmpty('qcm_q2', false)
				->requirePresence('qcm_q3')->allowEmpty('qcm_q3', false)
				->requirePresence('qcm_q4')->allowEmpty('qcm_q4', false)
				->requirePresence('qcm_q5')->allowEmpty('qcm_q5', false)
				->requirePresence('qcm_q6')->allowEmpty('qcm_q6', false)
				->requirePresence('qcm_q7')->allowEmpty('qcm_q7', false)
				->requirePresence('qcm_q8')->allowEmpty('qcm_q8', false)
				->requirePresence('qcm_q9')->allowEmpty('qcm_q9', false)
				->requirePresence('qcm_q10')->allowEmpty('qcm_q10', false)
				->requirePresence('qcm_q11')->allowEmpty('qcm_q11', false)
				->requirePresence('qcm_q12')->allowEmpty('qcm_q12', false)

				->add('email', 'valid', ['rule' => 'email',
										])->allowEmpty('email', true)
				->add('prenom', 'valid', ['rule' => 'text',
										 ])->allowEmpty('prenom', true)
				->add('password', 'valid',['rule' => 'text',
										  ])->allowEmpty('password', true)
				->add('telfixe', 'valid', 	['rule' => 'numeric',
											 'message' => 'Merci de saisir votre téléphone fixe dans un format valide (exemple 0404040404).'
											])->allowEmpty('telfixe', true)
				->add('telportable', 'valid', 	['rule' => 'numeric',
												 'message' => 'Merci de saisir votre téléphone portable dans un format valide (exemple 0606060606).'
											])->allowEmpty('telportable', false)
				->add('email', 'validFormat', 	['rule' => 'email',
												 'message' => 'Votre adresse mail doit être une adresse mail valide.'
												]);
			$errors = $validator->errors($this->request->data());
			if (!empty($errors)) {

				foreach($errors as $keyerror => $valerror){
					switch($keyerror){
						case 'qcm_q1':
							$numquestion='1';
							$this->Flash->error(__('Veuillez répondre à la question n°1'));
							break;
						case 'qcm_q2':
							$numquestion='2';
							break;
						case 'qcm_q3':
							$numquestion='3';
							break;
						case 'qcm_q4':
							$numquestion='4';
							break;
						case 'qcm_q5':
							$numquestion='5';
							break;
						case 'qcm_q6':
							$numquestion='6';
							break;
						case 'qcm_q7':
							$numquestion='7';
							break;
						case 'qcm_q8':
							$numquestion='8';
							break;
						case 'qcm_q9':
							$numquestion='9';
							break;
						case 'qcm_q10':
							$numquestion='10';
							break;
						case 'qcm_q11':
							$numquestion='11';
							break;
						case 'qcm_q12':
							$numquestion='12';
							break;
						case 'email':
							$numquestion='email';
							break;
					}
					if($numquestion==='email'){
						$this->Flash->error(__('Veuillez renseigner une adresse mail valide ou cette adresse mail est déjà utilisée.'));
					}
					else {
						$this->Flash->error(__('Veuillez répondre à la question n° '.$numquestion));
					}
				}
			}
			else {
				$email_exist = TableRegistry::get('Repqcmpersonnalites');
				$email_exist = $email_exist->find('All')->where(['user_email' => $this->Auth->User('email')])->toArray();
				if(empty($email_exist)){
					return $this->redirect(array('controller' => 'Users', 'action' => 'inscriptionpartielleko'));
				}else{
					$idrepperso=$email_exist[0]['id'];
					$reponseqcms = TableRegistry::get('Repqcmpersonnalites');
					$dateencours=date("Y-m-d H:i:s", time());
					$query = $reponseqcms->query();
					$query->update()->set(['rep_q1' => $_POST['qcm_q1']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['rep_q2' => $_POST['qcm_q2']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['rep_q3' => $_POST['qcm_q3']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['rep_q4' => $_POST['qcm_q4']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['rep_q5' => $_POST['qcm_q5']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['rep_q6' => $_POST['qcm_q6']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['rep_q7' => $_POST['qcm_q7']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['rep_q8' => $_POST['qcm_q8']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['rep_q9' => $_POST['qcm_q9']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['rep_q10' => $_POST['qcm_q10']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['rep_q11' => $_POST['qcm_q11']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['rep_q12' => $_POST['qcm_q12']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['contexte' => $_POST['contexte']])->where(['id' => $idrepperso])->execute();
					$query->update()->set(['statutreponse' => 1])->where(['id' => $idrepperso])->execute();
					$_POST['email']=$this->Auth->User('email');
					$_POST['prenom']=$this->Auth->User('prenom');
					$_POST['emailparent']=$this->Auth->User('emailparent');
					$this->getMailer('User')->send('qcmmos1',[$_POST]);
					$this->Flash->success(__('Votre bilan 360° MOS 1 a été enregistré ! Vous allez recevoir un email de confirmation dans les prochaines minutes.'));
					return $this->redirect(array('controller' => 'Users', 'action' => 'welcome'));
				}
			}
		}
	}

	public function mesachats($id=null)
	{
		if (!is_null($id)) {
			// Utilisateur
			$user = $this->Users->get($this->Auth->User('id'));
			// Abonnement
			$query = TableRegistry::get('Abonnements');
			$abonnements = $query->find('All')->where(['user_id' => $this->Auth->User('id')])->toArray();
			// SOGENACTIF
			$query2 = TableRegistry::get('Sogenactifs');
			$sogenactifs=$query2->find('All')->toArray();
			// Type de Packs
			$typepacks = TableRegistry::get('Typepacks');
			$typepacks=$typepacks->find('All')->toArray();
			// Type d'Options
			$typeoptions = TableRegistry::get('Typeoptions');
			$typeoptions=$typeoptions->find('All')->toArray();
			// Type Promotions
			$typepromos = TableRegistry::get('Typepromos');
			$typepromos=$typepromos->find('All')->toArray();

			$this->set('abonnement', $this->paginate('abonnements'));

			$this->set(compact('user', 'abonnements', 'sogenactifs', 'typepacks', 'typeoptions', 'typepromos'));
			$this->set('_serialize', ['user']);

		}
	}
}
