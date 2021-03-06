<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Routing\Router;

class BatePapoController extends AppController
{
	public $protected_area = true;

	// API - add_message()
	public function api_add_message()
	{
    	$this->autoRender = false;

    	// Objetos de tabela
		$messages_table = TableRegistry::get("Messages");
		$message_recipients_table = TableRegistry::get("MessageRecipients");

		// Sessão de admin
    	$dados = json_decode(file_get_contents('php://input'), true);

      $user_id = $dados['usuarioLogado']['id'];

      if(!empty($dados['usuarioLogado']['user_id'])) {
        $user_id = $dados['usuarioLogado']['user_id'];
      }

      $mensagem = [
        'user_id' => $user_id,
        'model' => $dados['usuarioLogado']['table'],
        'model_id' => $dados['usuarioLogado']['id'],
        'name' => $dados['mensagem']['name'],
        'content' => $dados['mensagem']['content'],
      ];

    	$entity = $messages_table->newEntity($mensagem);

    	$messages_table->save($entity);

    	foreach($dados['mensagem']['to'] as $to)
    	{
        $table = TableRegistry::get($to['model']);
        $user = $table->get($to['id']);

        $extra_data = [
          'url' => Router::url( ['action' => 'index'], true )
        ];

        if($this->dispararEmail(3, $user, $extra_data )) {

      		$tmp = [
      			'model_id' => $to['id'],
      			'model' => $to['model'],
      			'message_id' => $entity->id
      		];

      		$tmp = $message_recipients_table->newEntity($tmp);

      		$message_recipients_table->save($tmp);
        }
    	}
	}

	// API - add_reply()
	public function api_add_reply()
	{
    	$this->autoRender = false;

    	// Objetos de tabela
      $message_recipients_table = TableRegistry::get("MessageRecipients");
		  $messages_table = TableRegistry::get("MessageReplies");

    	$dados = json_decode(file_get_contents('php://input'), true);

      $resposta = [
        'message_id' => $dados['message_id'],
        'model' => $dados['usuarioLogado']['table'],
        'model_id' => $dados['usuarioLogado']['id'],
        'content' => $dados['resposta']['content'],
      ];

    	$entity = $messages_table->newEntity($resposta);

    	$messages_table->save($entity);

      $recipientes = $message_recipients_table->find()->where(['MessageRecipients.message_id' => $dados['message_id'] ])->all();

      foreach($recipientes as $recipiente) {
        $table = TableRegistry::get($recipiente->model);
        $user = $table->get($recipiente->model_id);

        $extra_data = [
        'name' => $dados['usuarioLogado']['full_name'],
        'url' => Router::url( ['action' => 'index'], true )
        ];
        if($this->dispararEmail(4, $user, $extra_data )) {

        }
      }


	}

	// API - fetch_all()
	public function api_fetch_all()
    {
    	$this->autoRender = false;

    	// Objetos de tabela
		  $messages_table = TableRegistry::get("Messages");

		// Sessão de admin
    	$user_id = $this->userLogged['user_id'];

      // Se ele for um aluno
      if($this->userLogged['table'] == 'Users') {

      }

    	// Busca todas as mensagens do usuário logado
    	$where = [
    		'Messages.user_id' => $user_id
    	];
		$messages = $messages_table->find()->where($where)->contain(['MessageRecipients', 'MessageReplies'])->order(['id' => 'DESC'])->all()->toArray();

		$atores_global = $this->getAtores();

		// Itera todas as mensagens
		foreach($messages as $key_message => $message)
		{
			// Data de envio formatada
			$message->date = $message->created->format("d M");

			// Destinatário
			$message->to = [];

			// Respostas
			$message->replies = [];

			// Autor
			$message->from = "";

			// Resumo
			$message->excerpt = Text::truncate(
				$message->content,
				140,
				[
				    'ellipsis' => '...',
				    'exact' => false
				]
			);


			// Itera todos os atores
			foreach($atores_global as $ator_label => $atores)
			{

				foreach($atores as $ator)
				{

					if($ator->id == $message->model_id && $ator_label == $message->model)
					{
						$message->from = $ator->full_name;
					}
				}

			}

			// Itera todos os recipientes e adiciona ao $to
			foreach($message->message_recipients as $message_recipient)
			{
				foreach($atores_global[$message_recipient->model] as $ator)
				{
					if($ator->id == $message_recipient->model_id)
					{
						$message->to[] = $ator->full_name;
					}
				}
			}

			// Itera todas as respostas e adiciona a $replies
			foreach($message->message_replies as $reply)
			{
				foreach($atores_global[$reply->model] as $ator)
				{
					if($ator->id == $reply->model_id)
					{
						$message->replies[] = [
							'content' => $reply->content,
							'author' => $ator->full_name,
						];
					}
				}

			}

		}

		// Retorna em JSON
		echo json_encode($messages);
    }

	public function index()
    {
		// Sessão de admin
    	$admin_logged = $this->getAdminLogged();

    	$atores = $this->getAtores();

    	$this->set(compact("admin_logged", "atores"));

    }

}