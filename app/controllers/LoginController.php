<?php
use Phalcon\Http\Request;
use App\Forms\RegisterForm;
use App\Forms\LoginForm;
use Phalcon\Filter;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Mvc\View;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\Digit as DigitValidator;
use Phalcon\Mvc\Model\Query;

class LoginController extends ControllerBase
{
    // Primeira função a ser executada
    public function initialize()
    {
        // Configura o titulo da página
        $this->tag->setTitle('Login');

        // Assets
        parent::assetsAdmin();

        // Inicializa o initialize do controller base
        parent::initialize();
    }

    public function indexAction()
    {
        
    }

    // Login
    public function submitAction() {

        // Validate CSRF token
        if (!$this->security->checkToken()) {
            return $this->response->redirect(DEFAULT_BASE_PATH);
        }

        // Verifica se existe um metodo Post
        if (!($this->request->isPost())) {
            return $this->response->redirect(DEFAULT_BASE_PATH);
        }

        // Instancia um novo validator que verifica o email
        $validation = new Validation();
        $validation->add(
            [
                "userName",
                "password",
            ],
            new PresenceOf(
                [
                    "message" => [
                        "userName"  => "Digite o usuário",
                        "password" => "Você precisa informar a senha",
                    ],
                ]
            )
        );

        $validation->add(
            [
                "password",
            ],
            new StringLength(
                [
                    "max" => [ 
                        "password"  => 30,                      
                    ],
                    "min" => [
                        "password"  => 6,                       
                    ],
                    "messageMaximum" => [
                        "password"  => "Senha com máximo 30 dígitos",                        
                    ],
                    "messageMinimum" => [
                        "password"  => "Senha com mínimo 6 dígitos",                        
                    ]                                        
                ]
            )
        );

        $this->userName = $this->request->getPost('userName');
        $this->password = $this->request->getPost('password');

        // Caso o email nao seja valido, retorna para Login e imprime a mensagem para o usuario.
        $messages = $validation->validate($_POST);
        if (count($messages)) {
            foreach ($messages as $message) {
                
                $this->view->userName = $this->userName;
                $this->flashSession->error($message);
                return $this->dispatcher->forward([
                    'controller' => 'login', 
                    'action' => 'index',]);
            }
        }

        
        try {

            // Faz a busca do usuário no banco    
            $this->user = Member::findFirst([ 
                'UserName = :userName:',
                'bind' => [
                'userName' => $this->userName,
                ]
            ]);

            if (!$this->user){

                // Lança nova exessão e retorna
                throw new Exception("Usuário ou senha incorreto");                
            }

            // Verifica se a senha está correta
            if(!($this->security->checkHash($this->password, $this->user->Password))){

                // Salva a tentativa de login
                $loginAttempt = new LoginAttempt();
                $loginAttempt->UserName = $this->userName;
                $loginAttempt->Password = $this->password;
                $loginAttempt->IP = $this->request->getClientAddress();
                $loginAttempt->Browser = $this->request->getUserAgent();

                if (!$loginAttempt->save()) {

                    // Salva o erro
                    parent::saveError('Login', 'Submit', 'Erro ao salvar tentativa de login', '');
                }
    
                // Lança nova exessão e retorna
                throw new Exception("Usuário ou senha incorreto");
            }

            // Verifica o status do usuario
            if($this->user->IDMBS != 1) {

                switch ($this->user->IDMBS) {
                    case '2':
                        // Usuario inativo
                        throw new Exception("Usuário inativo, entre em contato com o administrador");
                        break;
                    case '3':
                        // Confirmacao pendente
                        throw new Exception("Usuário bloqueado");
                        break;
                    default:
                        break;
                }
            }

            // Salva o login do usuário
            $log = new Log();
            $log->IDMB = $this->user->IDMB;
            $log->IP = $this->request->getClientAddress();
            $log->Browser = $this->request->getUserAgent();
            
            // Salva o log
            if(!$log->save()) {

                // salva o erro
                foreach ($log->getMessages() as $message) {

                    // Registra o erro se houver
                    parent::saveError('Login', 'Submit', $message, 'Erro ao salvar o log do usuario');
                }

                throw new Exception("Tente novamente, caso o erro persista contate o administrador");
            }

            // Flag que indica que o usuário está online
            // $this->user->Online = 1;

            // With bound parameters
            $query = $this->modelsManager->createQuery('UPDATE Member SET Online = 1 WHERE IDMB = :idmb:');
            $cars  = $query->execute(
                [
                    'idmb' => $this->user->IDMB,
                ]
            );
            
            // if(!$this->user->update()) {

            //     // salva o erro
            //     foreach ($this->user->getMessages() as $message) {

            //         // Registra o erro se houver
            //         parent::saveError('Login', 'Submit', $message, 'Erro ao salvar flag do usuário online');
            //     }

            //     throw new Exception("Tente novamente, caso o erro persista contate o administrador");
            // }

            // Abre sessão com os dados do usuário
            $this->session->set(DEFAULT_ADMIN_SESSION, [
                'userId' => $this->user->IDMB,
                //'name' => $this->user->name,
                // 'username' => $this->user->username,
                //'email' => $this->user->email,
                //'type' => $this->user->type,
                //'token' => $this->sessionToken
                'log' => $log->IDL
            ]);
            
            $this->response->redirect('admin');
            return;

        } catch (Exception $e) {
            
            $this->flashSession->error($e->getMessage());
            $this->view->userName = $this->userName;
            return $this->dispatcher->forward(['controller' => 'login', 'action' => 'index',]);
        }
    }

    // Logout
    public function logoutAction() {

        $session = $this->session->get(DEFAULT_ADMIN_SESSION);

        if ($session) {
         
            try {
                
                $userLog = Log::findFirst([ 
                    'IDL = :log:',
                    'bind' => [
                    'log' => isset($session['log']) ? $session['log'] : NULL,
                    ]
                ]);

                if($userLog) {

                    $userLog->LastActive = date('Y-m-d h:i:s'); 

                    if(!$userLog->save()) {

                        // salva o erro
                        foreach ($userLog->getMessages() as $message) {

                            // Registra o erro se houver
                            parent::saveError('Login', 'Logout', $message, 'Erro ao salvar a ultima atividade do usuário');
                        }
                    }

                    $user = Member::findFirst([
                        'IDMB = :idmb:',
                        'bind' => [
                        'idmb' => isset($session['userId']) ? $session['userId'] : NULL,
                        ]
                    ]);
    
                    if($user) {
    
                        $user->Online = 0;

                        if(!$user->save()) {

                            // salva o erro
                            foreach ($user->getMessages() as $message) {

                                // Registra o erro se houver
                                parent::saveError('Login', 'Logout', $message, 'Erro ao salvar quando o usuário se desconectou');
                            }

                            $this->session->destroy();
                            return $this->response->redirect(DEFAULT_BASE_PATH);
                        }

                        $this->session->destroy();
                        return $this->response->redirect(DEFAULT_BASE_PATH);
                    } else {

                        $this->session->destroy();
                        return $this->response->redirect(DEFAULT_BASE_PATH);
                    }

                } else {

                    $this->session->destroy();
                    return $this->response->redirect(DEFAULT_BASE_PATH);
                }

            } catch (Exception $e) {

                // Registra o erro se houver
                parent::saveError('Login', 'Logout', 'Erro ao deslogar', 'Verificar o erro, pois caiu em uma exessão');

                $this->session->destroy();
                return $this->response->redirect(DEFAULT_BASE_PATH);
            }
        } else {

            return $this->response->redirect(DEFAULT_BASE_PATH);
        }

        session_destroy();

        $this->response->redirect(DEFAULT_BASE_PATH);
    }
}