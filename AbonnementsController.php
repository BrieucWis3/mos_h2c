<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Error\Debugger;
use Cake\Event\Event;


/**
 * Abonnements Controller
 *
 * @property \App\Model\Table\AbonnementsTable $Abonnements
 */

class AbonnementsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Typepacks', 'Typepromos']
        ];
        $this->set('abonnements', $this->paginate($this->Abonnements));
        $this->set('_serialize', ['abonnements']);
    }

    /**
     * View method
     *
     * @param string|null $id Abonnement id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    /*public function view($id = null)
    {
        $abonnement = $this->Abonnements->get($id, [
            'contain' => ['Users', 'Typepacks', 'Typepromos']
        ]);
        $this->set('abonnement', $abonnement);
        $this->set('_serialize', ['abonnement']);
    }*/

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $abonnement = $this->Abonnements->newEntity();
        if ($this->request->is('post')) {
			if(!is_null($_POST['listeclient']) && isset($_POST['listeclient'])){
				foreach($_POST['choix'] as $k => $val){
					$test=explode('/', $val);
					$idpack = $test[0];
					$listoption = $listoption.$test[1].',';
				}
				$iduser=explode('-', $_POST['listeclient']);
				$newiduser=trim($iduser[1]);
				$abonnements = TableRegistry::get('Abonnements');
				$dateencours=date("Y-m-d H:i:s", time());
				$query = $abonnements->query();
				$query->insert(['user_id', 'pack_id', 'promo_id', 'listoption', 'payeur_nom', 'payeur_prenom', 'payeur_datenaiss', 'montant', 'blocage_mos1', 'blocage_mos2', 'blocage_mos3', 'etat_paiement', 'date_paiement', 'created'])
						->values([
							'user_id' => $newiduser,
							'pack_id' => $idpack,
							'promo_id' => $_POST['idpromo'],
							'listoption' => $listoption,
							'payeur_nom' => $_POST['payeur_nom'],
							'payeur_prenom' => $_POST['payeur_prenom'],
							'payeur_datenaiss' => $_POST['payeur_datenaiss'],
							'montant' => $_POST['montant'],
							'blocage_mos1' => $_POST['blocage_mos1'],
							'blocage_mos2' => $_POST['blocage_mos2'],
							'blocage_mos3' => $_POST['blocage_mos3'],
							'etat_paiement' => $_POST['etat_paiement'],
							'date_paiement' => $_POST['date_paiement'].' '.$_POST['heure_paiement'],
							'created' => $dateencours
						])
						->execute();
				$this->Flash->success(__('Le Nouvel Abonnement a bien été sauvegardé.'));
			}elseif(is_null($_POST['listeclient']) || !isset($_POST['listeclient']))
				{
					$this->Flash->error(__('Merci de choisir un client. Veuillez réessayer.'));
				}
        }
        $users = TableRegistry::get('Users');
		$users = $users->find('All')->where(['profil_id = ' => '1'])->order(['nom' => 'DESC']);
		$packs = TableRegistry::get('Typepacks');
		$packs = $packs->find('All');
		/*$listoptions = TableRegistry::get('Typeoptions');
		$listoptions = $listoptions->find('All');*/
		$promos = TableRegistry::get('Typepromos');
		$promos = $promos->find('All');
        //$this->set(compact('abonnement', 'users', 'packs', 'listoptions', 'promos'));
		$this->set(compact('abonnement', 'users', 'packs', 'promos'));
        $this->set('_serialize', ['abonnement']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Abonnement id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $abonnement = $this->Abonnements->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
			$abonnement = $this->Abonnements->patchEntity($abonnement, $this->request->data);
            if ($this->Abonnements->save($abonnement)) {
                $this->Flash->success(__('Les modifications sur l\'Abonnement ont bien été enregistrées.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Les modifications sur l\'Abonnement n\'a pas pu être enregistrées. Veuillez réessayer.'));
            }
        }
        $users = $this->Abonnements->Users->find('list', ['limit' => 200]);
        $packs = $this->Abonnements->Typepacks->find('list', ['limit' => 200]);
        $promos = $this->Abonnements->Typepromos->find('list', ['limit' => 200]);
        $this->set(compact('abonnement', 'users', 'packs', 'promos'));
        $this->set('_serialize', ['abonnement']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Abonnement id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $abonnement = $this->Abonnements->get($id);
        if ($this->Abonnements->delete($abonnement)) {
            $this->Flash->success(__('L\'Abonnement a bien été supprimé.'));
        } else {
            $this->Flash->error(__('L\'Abonnement n\'a pas pu être supprimé. Veuillez réessayer.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
