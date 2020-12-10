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
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Digit;
use Phalcon\Mvc\Url;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

class MemberController extends ControllerBase
{
    private $response;
    private $post = NULL;
    private $transaction;

    // Primeira função a ser executada
    public function initialize()
    {
        // Executa o initialie do controller base
        parent::initialize();

        // Constroi a resposta da API
        $this->response = new stdClass();
        $this->response->errorCode = 0;
        $this->response->errorMessage = '';
        $this->response->successMessage = '';
        $this->response->data = NULL;
    }

    // Renderiza a view index do Controller
    public function indexAction()
    {

    }

    // Cria um novo usuário
    public function createAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        if(!isset($this->post->userType)) {

            $this->response->errorCode = 1;
            $this->response->errorMessage = 'Falta o tipo do usuário';

            // Retorno
            return $this->return(); 
        }

        // Verifica o tipo de usuário que vai ser cadastrado
        if($this->post->userType == 1 || $this->post->userType == 2) {

            // Instancia um novo validator que verifica o email
            $validation = new Validation();
            $validation->add(
                [
                    "user",
                    "userName",
                    "password",
                    "userType"
                ],
                new PresenceOf(
                    [
                        "message" => [
                            "user"  => "Informe o nome",
                            "userName" => "Informe o nome de usuario",
                            "password" => "Informe a senha",
                            "userType" => "Informe o tipo de usuário"
                        ],
                    ]
                )
            );

            $validation->add(
                [
                    "user",
                    "userName",
                    "password"
                ],
                new StringLength(
                    [
                        "max" => [ 
                            "user"  => 30,
                            "userName" => 30,
                            "password" => 12                      
                        ],
                        "min" => [
                            "user"  => 4,
                            "userName" => 4,
                            "password" => 6                        
                        ],
                        "messageMaximum" => [
                            "user"  => "Nome com máximo 30 caracteres",
                            "userName" => "Nome de usuário com máximo 30 caracteres",
                            "password" => "Senha com máximo 12 caracteres"                       
                        ],
                        "messageMinimum" => [
                            "user"  => "Nome com mínimo 4 caracteres",
                            "userName" => "Nome de usuário com mínimo de 4 caracteres",
                            "password" => "Senha com mínimo 6 caracteres"                      
                        ]                                        
                    ]
                )
            );

            // Caso o email nao seja valido, retorna para Login e imprime a mensagem para o usuario.
            $messages = $validation->validate($this->post);

            if (count($messages)) {

                foreach ($messages as $message) {

                    $this->response->errorCode = 1;
                    $this->response->errorMessage = $message->getMessage();
                    $this->response->errorField = $message->getField();

                    // Retorno
                    return $this->return();
                }
            }

            try {
                
                // Salva o usuário
                $member = new Member();
                $member->Name = parent::sanitize($this->post->user, 'string');
                $member->UserName = parent::sanitize($this->post->userName, 'string');
                $member->Password = $this->security->hash($this->post->password);
                $member->IDMBT = parent::sanitize($this->post->userType, 'int');

                if(!$member->save()) {
                    foreach ($member->getMessages() as $message) {

                        // Grava o erro
                        parent::saveError(
                            $this->dispatcher->getControllerName(),
                            $this->dispatcher->getActionName(),
                            $message,
                            'Erro ao salvar usuario tipo administrador'
                        );

                        throw new Exception(parent::internalError()); 
                    }
                }

                //return 'salvou'; //$this->post->userType;
                $this->response->successMessage = 'Usuário cadastrado com sucesso!';
                return $this->return();
            
            } catch (Exception $e) {
                
                $this->response->errorCode = 1;
                $this->response->errorMessage = $e->getMessage();

                // Retorno
                return $this->return();
            }
        }

        if($this->post->userType == 3) {
           
            // Instancia um novo validator que verifica o email
            $validation = new Validation();
            $validation->add(
                [
                    "user",
                    "userType",
                    "ddd",
                    "phone",
                    "street",
                    "number",
                    "state",
                    "city",
                ],
                new PresenceOf(
                    [
                        "message" => [
                            "user"  => "Informe o nome",
                            "ddd" => "Informe o DDD do telefone",
                            "phone" => "Informe o telefone",
                            "street" => "Informe a rua",
                            "number" => "Informe o número da casa",
                            "state" => "Informe o estado",
                            "city" => "Informe a cidade",
                            "userType" => "Informe o tipo de usuário"
                        ],
                    ]
                )
            );

            $validation->add(
                [
                    "user",
                    "ddd",
                    "phone",
                    "street",
                    "number"
                ],
                new StringLength(
                    [
                        "max" => [ 
                            "user"  => 30,
                            "ddd" => 2,
                            "phone" => 9,
                            "street" => 100,
                            "number" => 10
                        ],
                        "min" => [
                            "user"  => 4,
                            "ddd" => 2,
                            "phone" => 8,
                            "street" => 4,
                            "number" => 1                        
                        ],
                        "messageMaximum" => [
                            "user"  => "Nome com máximo 30 caracteres",
                            "ddd" => "DDD tem que ter 2 dígitos",
                            "phone" => "Telefone máximo 9 dígitos",
                            "street" => "Rua com máximo 100 caracteres",
                            "number" => "Número com máximo 10 caracteres"      
                        ],
                        "messageMinimum" => [
                            "user"  => "Nome com mínimo 4 caracteres",
                            "ddd" => "DDD tem que ter 2 dígitos",
                            "phone" => "Telefone mínimo 8 dígitos",
                            "street" => "Rua com no mínimo 4 dígitos",
                            "number" => "Número com mínimo 1 dígito"
                        ]                                        
                    ]
                )
            );

            $validation->add(
                [
                    "ddd",
                    "phone",
                ],
                new Digit(
                    [
                        "message" => [
                            "ddd"  => "DDD somente números",
                            "phone"  => "Telefone somente números"
                        ],
                    ]
                )               
            );

            if(isset($this->post->email)) {
                $validation->add(
                    [
                        "email",
                    ],
                    new Email(
                        [
                            "message" => [
                                "email"  => "Email inválido, digite novamente"
                            ],
                        ]
                    )               
                );
            }

            // Caso o email nao seja valido, retorna para Login e imprime a mensagem para o usuario.
            $messages = $validation->validate($this->post);

            if (count($messages)) {

                foreach ($messages as $message) {

                    $this->response->errorCode = 1;
                    $this->response->errorMessage = $message->getMessage();
                    $this->response->errorField = $message->getField();

                    // Retorno
                    return $this->return();
                }
            }

            try {

                // Verifica se o CPF é válido
                if(isset($this->post->cpf)) {

                    if(!parent::validateCPF($this->post->cpf)) {

                        $this->response->errorField = 'cpf';
                        
                        throw new Exception('CPF inválido');
                    }
                }

                // Verifica se o CNPJ é válido
                if(isset($this->post->cnpj)) {

                    if(!parent::validateCNPJ($this->post->cnpj)) {

                        $this->response->errorField = 'cnpj';
                        
                        throw new Exception('CNPJ inválido');
                    }
                }

                // $transactionManager = new TransactionManager();
                // $this->transaction = $transactionManager->get();
                
                $member = new Member();
                //$member->setTransaction($this->transaction);
                $member->IDMBT = parent::sanitize($this->post->userType, 'int');  //  isset($this->post->nameCompany) ? parent::sanitize($this->post->nameCompany, 'string') : NULL;   //parent::sanitize((isset()) ? $this->post->userType  $this->post->userType : null) , 'int');
                $member->Name = parent::sanitize($this->post->user, 'string');
                $member->NameCompany = isset($this->post->nameCompany) ? parent::sanitize($this->post->nameCompany, 'string') : NULL;
                $member->Gender = isset($this->post->gender) ? parent::sanitize($this->post->gender, 'int') : NULL;
                $member->CPF = isset($this->post->cpf) ? parent::sanitize($this->post->cpf, 'int') : NULL;
                $member->CNPJ = isset($this->post->cnpj) ? parent::sanitize($this->post->cnpj, 'int') : NULL;

                // Salva o usuário
                if(!$member->save()) {
                    foreach ($member->getMessages() as $message) {

                        // Grava o erro
                        parent::saveError(
                            $this->dispatcher->getControllerName(),
                            $this->dispatcher->getActionName(),
                            $message,
                            'Erro ao salvar usuario tipo cliente'
                        );

                        throw new Exception(parent::internalError()); 
                    }
                }

                // Salva o contato do usuário
                $memberContact = new MemberContact();
                $memberContact->IDMB = $member->IDMB;
                $memberContact->Email = isset($this->post->email) ? $this->post->email : NULL;
                $memberContact->Phone = isset($this->post->phone) ? parent::sanitize($this->post->phone, 'int') : NULL;
                $memberContact->SecondPhone = isset($this->post->secondPhone) ? parent::sanitize($this->post->secondPhone, 'int') : NULL;

                // Salva o contato do usuário
                if(!$memberContact->save()) {
                    foreach ($memberContact->getMessages() as $message) {

                        // Grava o erro
                        parent::saveError(
                            $this->dispatcher->getControllerName(),
                            $this->dispatcher->getActionName(),
                            $message,
                            'Erro ao salvar o contato do usuário'
                        );

                        throw new Exception(parent::internalError()); 
                    }
                }

                // Salva o endereço do usuário
                $memberAddress = new MemberAddress();
                $memberAddress->IDMB = $member->IDMB;
                $memberAddress->IDGC = isset($this->post->city) ? parent::sanitize($this->post->city, 'int') : NULL;
                $memberAddress->IDGS = isset($this->post->state) ? parent::sanitize($this->post->state, 'int') : NULL;
                $memberAddress->Distric = isset($this->post->district) ? parent::sanitize($this->post->district, 'string') : NULL;
                $memberAddress->Street = isset($this->post->street) ? parent::sanitize($this->post->street, 'string') : NULL;
                $memberAddress->Number = isset($this->post->number) ? parent::sanitize($this->post->number, 'string') : NULL;
                $memberAddress->Complement = isset($this->post->complement) ? parent::sanitize($this->post->complement, 'string') : NULL;
                $memberAddress->ZipCode = isset($this->post->zipCode) ? parent::sanitize($this->post->zipCode, 'string') : NULL;

                // Salva o endereço do usuário
                if(!$memberAddress->save()) {
                    
                    //$this->transaction->rollBack();

                    foreach ($memberAddress->getMessages() as $message) {

                        parent::saveError(
                        // Grava o erro
                            $this->dispatcher->getControllerName(),
                            $this->dispatcher->getActionName(),
                            $message,
                            'Erro ao salvar o endereço do usuário'
                        );
                        
                        throw new Exception(parent::internalError());
                        
                    }
                }

                // Salva a FK do endereço e do contato
                $member->IDMBA = $memberAddress->IDMBA;
                $memberContact->IDMBC = $memberContact->IDMBC;

                print_r($memberAddress);
                print_r('-----------------');
                print_r($memberContact);
                exit;

                if(!$member->save()) {

                    foreach ($member->getMessages() as $message) {

                        // Grava o erro
                        parent::saveError(
                            $this->dispatcher->getControllerName(),
                            $this->dispatcher->getActionName(),
                            $message,
                            'Erro ao salvar o IDMBA e IDMBC no membro'
                        );
                        
                        throw new Exception(parent::internalError());
                        
                    }
                }

                $this->response->successMessage = 'Cadastrado com sucesso!';

                return $this->return();

            } catch (Exception $e) {
                
                $this->response->errorCode = 1;
                $this->response->errorMessage = $e->getMessage();

                return $this->return();
            }
        }

        // Resposta da API
        $this->response->errorCode = 1;
        $this->response->errorMessage = 'Tipo de usuário precisa ser definido';
        return $this->return();
    }

    // Edita um usuário
    public function editAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        // Verifica o tipo de usuário que vai ser cadastrado
        if($this->post->userType == 1 || $this->post->userType == 2) {

            // Instancia um novo validator que verifica o email
            $validation = new Validation();
            $validation->add(
                [
                    "user",
                    "userId",
                    "userName",
                    "password",
                    "userType"
                ],
                new PresenceOf(
                    [
                        "message" => [
                            "user"  => "Informe o nome",
                            "userId"  => "Informe o ID do usuário",
                            "userName" => "Informe o nome de usuario",
                            "password" => "Informe a senha",
                            "userType" => "Informe o tipo de usuário"
                        ],
                    ]
                )
            );

            $validation->add(
                [
                    "user",
                    "userName",
                    "password"
                ],
                new StringLength(
                    [
                        "max" => [ 
                            "user"  => 30,
                            "userName" => 30,
                            "password" => 12                      
                        ],
                        "min" => [
                            "user"  => 4,
                            "userName" => 4,
                            "password" => 6                        
                        ],
                        "messageMaximum" => [
                            "user"  => "Nome com máximo 30 caracteres",
                            "userName" => "Nome de usuário com máximo 30 caracteres",
                            "password" => "Senha com máximo 12 caracteres"                       
                        ],
                        "messageMinimum" => [
                            "user"  => "Nome com mínimo 4 caracteres",
                            "userName" => "Nome de usuário com mínimo de 4 caracteres",
                            "password" => "Senha com mínimo 6 caracteres"                      
                        ]                                        
                    ]
                )
            );

            // Caso o email nao seja valido, retorna para Login e imprime a mensagem para o usuario.
            $messages = $validation->validate($this->post);

            if (count($messages)) {

                foreach ($messages as $message) {

                    $this->response->errorCode = 1;
                    $this->response->errorMessage = $message->getMessage();
                    $this->response->errorField = $message->getField();

                    // Retorno
                    return $this->return();
                }
            }

            try {

                // Faz a busca do usuário no banco    
                $member = Member::findFirst([ 
                    'IDMB = :userId:',
                    'bind' => [
                    'userId' => parent::sanitize($this->post->userId, 'int'),
                    ]
                ]);

                if(!$member) {

                    throw new Exception("Tente novamente, se o erro persistir contate o administrador");
                }
                
                $member->Name = parent::sanitize($this->post->user, 'string');
                $member->UserName = parent::sanitize($this->post->userName, 'string');
                $member->Password = $this->security->hash($this->post->password);
                //$member->IDMBT = parent::sanitize($this->post->userType, 'int');

                if(!$member->save()) {
                    foreach ($member->getMessages() as $message) {

                        // Grava o erro
                        parent::saveError(
                            $this->dispatcher->getControllerName(),
                            $this->dispatcher->getActionName(),
                            $message,
                            'Erro ao salvar usuario tipo administrador'
                        );

                        throw new Exception(parent::internalError()); 
                    }
                }

                $this->response->successMessage = 'Alterado com sucesso!';
                return $this->return();
            
            } catch (Exception $e) {
                
                $this->response->errorCode = 1;
                $this->response->errorMessage = $e->getMessage();

                // Retorno
                return $this->return();
            }
        }

        if($this->post->userType == 3) {
           
            // Instancia um novo validator que verifica o email
            $validation = new Validation();
            $validation->add(
                [
                    "user",
                    "userId",
                    "userType",
                    "ddd",
                    "phone",
                    "street",
                    "number",
                    "state",
                    "city",
                ],
                new PresenceOf(
                    [
                        "message" => [
                            "user"  => "Informe o nome",
                            "userId"  => "Informe o ID do usuário",
                            "ddd" => "Informe o DDD do telefone",
                            "phone" => "Informe o telefone",
                            "street" => "Informe a rua",
                            "number" => "Informe o número da casa",
                            "state" => "Informe o estado",
                            "city" => "Informe a cidade",
                            "userType" => "Informe o tipo de usuário"
                        ],
                    ]
                )
            );

            $validation->add(
                [
                    "user",
                    "ddd",
                    "phone",
                    "street",
                    "number"
                ],
                new StringLength(
                    [
                        "max" => [ 
                            "user"  => 30,
                            "ddd" => 2,
                            "phone" => 9,
                            "street" => 100,
                            "number" => 10
                        ],
                        "min" => [
                            "user"  => 4,
                            "ddd" => 2,
                            "phone" => 8,
                            "street" => 4,
                            "number" => 1                        
                        ],
                        "messageMaximum" => [
                            "user"  => "Nome com máximo 30 caracteres",
                            "ddd" => "DDD tem que ter 2 dígitos",
                            "phone" => "Telefone máximo 9 dígitos",
                            "street" => "Rua com máximo 100 caracteres",
                            "number" => "Número com máximo 10 caracteres"      
                        ],
                        "messageMinimum" => [
                            "user"  => "Nome com mínimo 4 caracteres",
                            "ddd" => "DDD tem que ter 2 dígitos",
                            "phone" => "Telefone mínimo 8 dígitos",
                            "street" => "Rua com no mínimo 4 dígitos",
                            "number" => "Número com mínimo 1 dígito"
                        ]                                        
                    ]
                )
            );

            $validation->add(
                [
                    "ddd",
                    "phone",
                ],
                new Digit(
                    [
                        "message" => [
                            "ddd"  => "DDD somente números",
                            "phone"  => "Telefone somente números"
                        ],
                    ]
                )               
            );

            if(isset($this->post->email)) {
                $validation->add(
                    [
                        "email",
                    ],
                    new Email(
                        [
                            "message" => [
                                "email"  => "Email inválido, digite novamente"
                            ],
                        ]
                    )               
                );
            }

            // Caso o email nao seja valido, retorna para Login e imprime a mensagem para o usuario.
            $messages = $validation->validate($this->post);

            if (count($messages)) {

                foreach ($messages as $message) {

                    $this->response->errorCode = 1;
                    $this->response->errorMessage = $message->getMessage();
                    $this->response->errorField = $message->getField();

                    // Retorno
                    return $this->return();
                }
            }

            try {

                // Verifica se o CPF é válido
                if(isset($this->post->cpf)) {

                    if(!parent::validateCPF($this->post->cpf)) {

                        $this->response->errorField = 'cpf';
                        
                        throw new Exception('CPF inválido');
                    }
                }

                // Verifica se o CNPJ é válido
                if(isset($this->post->cnpj)) {

                    if(!parent::validateCNPJ($this->post->cnpj)) {

                        $this->response->errorField = 'cnpj';
                        
                        throw new Exception('CNPJ inválido');
                    }
                }

                // Faz a busca do usuário no banco    
                $member = Member::findFirst([ 
                    'IDMB = :userId:',
                    'bind' => [
                    'userId' => parent::sanitize($this->post->userId, 'int'),
                    ]
                ]);

                // $transactionManager = new TransactionManager();
                // $this->transaction = $transactionManager->get();
                
                //$member = new Member();
                //$member->setTransaction($this->transaction);
                //$member->IDMBT = parent::sanitize($this->post->userType, 'int');  //  isset($this->post->nameCompany) ? parent::sanitize($this->post->nameCompany, 'string') : NULL;   //parent::sanitize((isset()) ? $this->post->userType  $this->post->userType : null) , 'int');
                $member->Name = parent::sanitize($this->post->user, 'string');
                $member->NameCompany = isset($this->post->nameCompany) ? parent::sanitize($this->post->nameCompany, 'string') : NULL;
                $member->Gender = isset($this->post->gender) ? parent::sanitize($this->post->gender, 'int') : NULL;
                $member->CPF = isset($this->post->cpf) ? parent::sanitize($this->post->cpf, 'int') : NULL;
                $member->CNPJ = isset($this->post->cnpj) ? parent::sanitize($this->post->cnpj, 'int') : NULL;

                // Salva o usuário
                if(!$member->save()) {
                    foreach ($member->getMessages() as $message) {

                        // Grava o erro
                        parent::saveError(
                            $this->dispatcher->getControllerName(),
                            $this->dispatcher->getActionName(),
                            $message,
                            'Erro ao salvar usuario tipo cliente'
                        );

                        throw new Exception(parent::internalError()); 
                    }
                }

                // Salva o contato do usuário
                $memberContact = new MemberContact();
                $memberContact->IDMB = $member->IDMB;
                $memberContact->Email = isset($this->post->email) ? $this->post->email : NULL;
                $memberContact->Phone = isset($this->post->phone) ? parent::sanitize($this->post->phone, 'int') : NULL;
                $memberContact->SecondPhone = isset($this->post->secondPhone) ? parent::sanitize($this->post->secondPhone, 'int') : NULL;

                // Salva o contato do usuário
                if(!$memberContact->save()) {
                    foreach ($memberContact->getMessages() as $message) {

                        // Grava o erro
                        parent::saveError(
                            $this->dispatcher->getControllerName(),
                            $this->dispatcher->getActionName(),
                            $message,
                            'Erro ao salvar o contato do usuário'
                        );

                        throw new Exception(parent::internalError()); 
                    }
                }

                // Salva o endereço do usuário
                $memberAddress = new MemberAddress();
                $memberAddress->IDMB = $member->IDMB;
                $memberAddress->IDGC = isset($this->post->city) ? parent::sanitize($this->post->city, 'int') : NULL;
                $memberAddress->IDGS = isset($this->post->state) ? parent::sanitize($this->post->state, 'int') : NULL;
                $memberAddress->Distric = isset($this->post->district) ? parent::sanitize($this->post->district, 'string') : NULL;
                $memberAddress->Street = isset($this->post->street) ? parent::sanitize($this->post->street, 'string') : NULL;
                $memberAddress->Number = isset($this->post->number) ? parent::sanitize($this->post->number, 'string') : NULL;
                $memberAddress->Complement = isset($this->post->complement) ? parent::sanitize($this->post->complement, 'string') : NULL;
                $memberAddress->ZipCode = isset($this->post->zipCode) ? parent::sanitize($this->post->zipCode, 'string') : NULL;

                // Salva o endereço do usuário
                if(!$memberAddress->save()) {
                    
                    //$this->transaction->rollBack();

                    foreach ($memberAddress->getMessages() as $message) {

                        // Grava o erro
                        parent::saveError(
                            $this->dispatcher->getControllerName(),
                            $this->dispatcher->getActionName(),
                            $message,
                            'Erro ao salvar o endereço do usuário'
                        );
                        
                        throw new Exception(parent::internalError());
                        
                    }
                }

                $this->response->successMessage = 'Alterado com sucesso!';

                return $this->return();

            } catch (Exception $e) {
                
                $this->response->errorCode = 1;
                $this->response->errorMessage = $e->getMessage();

                return $this->return();
            }
        }

        // Resposta da API
        $this->response->errorCode = 1;
        $this->response->errorMessage = 'Tipo de usuário precisa ser definido';
        return $this->return();
    }

    // Deleta um usuário
    public function deleteAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        try {

            if(!isset($this->post->userId)) {

                throw new Exception("Informe o usuário");  
            }

            $memberId = parent::sanitize($this->post->userId, 'int');
            
            // Faz a busca do usuário no banco    
            $member = Member::findFirst([ 
                'IDMB = :userId:',
                'bind' => [
                'userId' => $memberId,
                ]
            ]);

            if (!$member){

                throw new Exception("Usuário não cadastrado");
                
            }
    
            $member->IDMBS = 2;

            if(!$member->save()) {

                foreach ($member->getMessages() as $message) {
                    
                    // Grava o erro
                    parent::saveError(
                        $this->dispatcher->getControllerName(),
                        $this->dispatcher->getActionName(),
                        $message,
                        'Erro ao salvar o status do usuario'
                    );

                    throw new Exception("Houve um erro, tente novamente. Caso o erro persista, comunique o administrador");
                }
            }    
        
            $this->response->successMessage = 'Alterado com sucesso!';
            return $this->return();

        } catch (Exception $e) {
            
            $this->response->errorCode = 1;
            $this->response->errorMessage = $e->getMessage();

            return $this->return();
        }
    }

    /**
     * Retorna os usuarios cadastrados
     * Precisa informar o tipo de usuario que deseja reetornar
     */
    public function getUserAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        try {

            if(!isset($this->post->userType)) {
                throw new Exception("Informe o tipo de usuario que deseja retornar");
            }

            // Faz a busca do usuário no banco    
            $member = Member::find([ 
                'IDMBT = :userType:',
                'bind' => [
                'userType' => parent::sanitize($this->post->userType, 'int'),
                ]
            ]);

            if(!count($member) > 0) {
                throw new Exception("Nenhum registro encontrado");
            }

            $this->response->data = $member;
            return $this->return();

        } catch (Exception $e) {
            
            $this->response->errorCode = 1;
            $this->response->errorMessage = $e->getMessage();

            return $this->return();
        }
    }

    // Retorna a resposta
    public function return() {

        // Tranforma o objeto em uma string JSON
        $this->response = json_encode($this->response);

        // Retorna a string JSON criptografada
        return parent::encryptJS($this->response, KEY_JS);
    }
}