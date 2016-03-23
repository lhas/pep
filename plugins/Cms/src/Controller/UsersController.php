<?php
namespace Cms\Controller;

use Cms\Controller\AppController;
use Cake\ORM\TableRegistry;

class UsersController extends AppController
{

    public function config_actors()
    {

    // generate labels
    $labels = [
       'Protectors' => 'Pai / Mãe'
      ,'Schools' => 'Mediador / Coordenador'
      ,'Therapists' => 'Terapeuta'
      ,'Tutors' => 'Tutor'
    ];

    // get all actors from this student
    $actors = $this->getAtores();

    $instituitions = TableRegistry::get("instituitions");
    $instituitions = $instituitions->find('list')->all();

    // send to view
    $this->set(compact("labels", "actors", "instituitions"));

    // if POST request
    if($this->request->is(["post", "put"])) {

      // shortcut
      $data = $this->request->data;

      // Se não tiver sido preenchido nenhuma senha, remove o campo dela
      // para nao bugar e atualizar a senha para vazio
      if(strlen($data['password']) <= 0) {
        unset($data['password']);
      }

      // generates model object
      $table = TableRegistry::get($data['model']);

      // generates entity object
      $entity = $table->newEntity();

      // if has any ID, update it
      if(!empty($data['id']))
      {
        $entity = $table->get($data['id']);
      }

      $entity = $table->patchEntity($entity, $data);

      if(!empty($data['instituition_id']))
      {

        $table2 = TableRegistry::get("Instituitions");

        // start - get instituition name input and converts to ID
        $tmp = $table2->find()->where(['name LIKE' => '%' . $data['instituition_id'] . '%' ])->first();

        if($tmp)
        {
            $entity->instituition_id = $tmp->id;
        } else {
            $tmp = $table2->newEntity(['name' => $data['instituition_id'] ]);
            $table2->save($tmp);

            $entity->instituition_id = $tmp->id;
        }
        // end - get instituition name

      }

      // save entity
      $table->save($entity);

      // alert
      $this->Flash->success($labels[$data['model']] . ' foi atualizado!');

      // redirect
      return $this->redirect(['action' => 'config_actors', '#' => 'c_' . $data['model'] ]);
    } // end POST request

  }

    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $this->request->data['profile_attachment'] = $this->Upload->uploadIt("profile_attachment", $user);

            $user = $this->Users->patchEntity($user, $this->request->data);

            // start - get instituition name input and converts to ID
            $tmp = $this->Users->Instituitions->find()->where(['name LIKE' => '%' . $this->request->data['instituition_id'] . '%' ])->first();

            if($tmp)
            {
                $user->instituition_id = $tmp->id;
            } else {
                $entity = $this->Users->Instituitions->newEntity(['name' => $this->request->data['instituition_id']]);
                $this->Users->Instituitions->save($entity);

                $user->instituition_id = $entity->id;
            }
            // end - get instituition name

            if ($this->Users->save($user)) {

                $this->Cookie->write('current_user_selected', $user);

                $this->Flash->success(__('O estudante foi cadastrado. Agora você irá configurar os atores.'));
                return $this->redirect(['controller' => 'dashboard', 'action' => 'config_actors']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $instituitions = $this->Users->Instituitions->find('list', ['limit' => 200]);
        $this->set(compact('user', 'instituitions'));
        $this->set('_serialize', ['user']);
    }

    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Instituitions']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {

            $this->request->data['profile_attachment'] = $this->Upload->uploadIt("profile_attachment", $user);

            $user = $this->Users->patchEntity($user, $this->request->data);

            // start - get instituition name input and converts to ID
            $tmp = $this->Users->Instituitions->find()->where(['name LIKE' => '%' . $this->request->data['instituition_id'] . '%' ])->first();

            if($tmp)
            {
                $user->instituition_id = $tmp->id;
            } else {
                $entity = $this->Users->Instituitions->newEntity(['name' => $this->request->data['instituition_id']]);
                $this->Users->Instituitions->save($entity);

                $user->instituition_id = $entity->id;
            }
            // end - get instituition name

            if ($this->Users->save($user)) {
                $this->Flash->success(__('As informações do estudante foram atualizadas.'));
                return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
            } else {
                $this->Flash->error(__('Não foi possível salvar o estudante. Verifique os dados.'));
            }
        }
        $instituitions = $this->Users->Instituitions->find('list', ['limit' => 200]);

        $user->instituition_id = $user->instituition->name;

        $this->set(compact('user', 'instituitions'));
        $this->set('_serialize', ['user']);
    }

    public function delete($id = null)
    {
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

/**
 * Action utilizada para remover atores.
 * Primeiro parametro é o nome do model.
 * Segundo é o ID a ser removido.
 * Esta action é usada na página de configuração de ator.
 */
    public function delete_actor($model = null, $id = null)
    {
      $table = TableRegistry::get($model);

      $entry = $table->get($id);

        if ($table->delete($entry)) {
            $this->Flash->success(__('O ator foi removido.'));
        } else {
            $this->Flash->error(__('Não foi possível remover o ator.'));
        }
        return $this->redirect(['action' => 'config_actors']);
    }
}
