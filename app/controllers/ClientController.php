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
use Phalcon\Mvc\Model\Query;

class ClientController extends ControllerBase
{
    public function initialize()
    {
        // Executa o construtor da classe ControllerBase
        parent::initialize();

        // Constroi a resposta da API
        $this->response = new stdClass();
        $this->response->errorCode = 0;
        $this->response->errorMessage = '';
        $this->response->successMessage = '';
        $this->response->data = NULL;
    }

    public function indexAction()
    {

    }

    // Cadastra um novo cliente
    public function createAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        // 
        if(!isset($this->post->userType) || $this->post->userType != 3) {
            $this->response->errorCode = 1;
            $this->response->errorMessage = 'Tipo de usuário deve ser CLIENTE';
            return $this->return();
        }

        // Instancia um novo validator que verifica o email
        $validation = new Validation();
        $validation->add(
            [
                "user",
                "userType",
                //"ddd",
                "phone",
                "street",
                "number",
                // "state",
                "city",
            ],
            new PresenceOf(
                [
                    "message" => [
                        "user"  => "Informe o nome",
                        //"ddd" => "Informe o DDD do telefone",
                        "phone" => "Informe o telefone",
                        "street" => "Informe a rua",
                        "number" => "Informe o número da casa",
                        // "state" => "Informe o estado",
                        "city" => "Informe a cidade",
                        "userType" => "Informe o tipo de usuário"
                    ],
                ]
            )
        );

        $validation->add(
            [
                "user",
                //"ddd",
                "phone",
                "street",
                "number"
            ],
            new StringLength(
                [
                    "max" => [ 
                        "user"  => 30,
                        //"ddd" => 2,
                        "phone" => 11,
                        "street" => 100,
                        "number" => 10
                    ],
                    "min" => [
                        "user"  => 4,
                        //"ddd" => 2,
                        "phone" => 10,
                        "street" => 1,
                        "number" => 1                        
                    ],
                    "messageMaximum" => [
                        "user"  => "Nome com máximo 30 caracteres",
                        //"ddd" => "DDD tem que ter 2 dígitos",
                        "phone" => "Telefone máximo 11 dígitos",
                        "street" => "Rua com máximo 100 caracteres",
                        "number" => "Número com máximo 10 caracteres"      
                    ],
                    "messageMinimum" => [
                        "user"  => "Nome com mínimo 4 caracteres",
                        //"ddd" => "DDD tem que ter 2 dígitos",
                        "phone" => "Telefone mínimo 8 dígitos",
                        "street" => "Rua com no mínimo 1 dígitos",
                        "number" => "Número com mínimo 1 dígito"
                    ]                                        
                ]
            )
        );

        $validation->add(
            [
                //"ddd",
                "phone",
            ],
            new Digit(
                [
                    "message" => [
                        //"ddd"  => "DDD somente números",
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

            // Validação da data
            if(isset($this->post->birthDate) && !empty($this->post->birthDate)) {

                $date = explode('/', $this->post->birthDate);

                // Invertido a posiçao de comparação do array para dar certo com o formato de data americano
                if(!checkdate($date[1], $date[0], $date[2])) {
                    throw new Exception("Ano de nascimento inválido");
                }
            }

            // Verifica se já existe um CPF cadastrado com o numero
            $clientCPF = Member::find([ 
                'CPF = :cpf:',
                'bind' => [
                'cpf' => $this->post->cpf
                ]
            ]);

            if(count($clientCPF) > 0) {
                throw new Exception("Já existe um CPF cadastrado com esse número");
            }
            
            // Verifica se já existe um CNPJ cadastrado com o numero
            $clientCPF = Member::find([ 
                'CNPJ = :cnpj:',
                'bind' => [
                'cnpj' => $this->post->cnpj
                ]
            ]);

            if(count($clientCPF) > 0) {
                throw new Exception("Já existe um CNPJ cadastrado com esse número");
            }

            // $transactionManager = new TransactionManager();
            // $this->transaction = $transactionManager->get();
            
            $member = new Member();
            //$member->setTransaction($this->transaction);
            $member->IDMBT = parent::sanitize($this->post->userType, 'int');  //  isset($this->post->nameCompany) ? parent::sanitize($this->post->nameCompany, 'string') : NULL;   //parent::sanitize((isset()) ? $this->post->userType  $this->post->userType : null) , 'int');
            $member->Name = parent::sanitize($this->post->user, 'string');
            //$member->NameCompany = isset($this->post->nameCompany) ? parent::sanitize($this->post->nameCompany, 'string') : NULL;
            $member->Gender = isset($this->post->gender) ? parent::sanitize($this->post->gender, 'int') : NULL;
            $member->CPF = isset($this->post->cpf) ? parent::sanitize($this->post->cpf, 'int') : NULL;
            $member->CNPJ = isset($this->post->cnpj) ? parent::sanitize($this->post->cnpj, 'int') : NULL;
            $member->Entity = isset($this->post->entity) ? parent::sanitize($this->post->entity, 'int') : 1;


            // Salva o usuário
            if(!$member->save()) {
                foreach ($member->getMessages() as $message) {

                    // Grava o erro
                    parent::saveError(
                        $this->dispatcher->getControllerName(),
                        $this->dispatcher->getActionName(),
                        $message,
                        'Erro ao salvar usuario novo cliente Member'
                    );

                    if($message->getCode() == 3) {
                        throw new Exception($message->getMessage());
                        
                    } else {
                        throw new Exception(parent::internalError()); 
                    }
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
                        'Erro ao salvar o contato do novo cliente'
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
                        'Erro ao salvar o endereço do novo cliente'
                    );
                    
                    throw new Exception(parent::internalError());
                    
                }
            }

            // $this->useDynamicUpdate(true);

            // Salva a FK do endereço e do contato
            // $member->IDMBA = $memberAddress->IDMBA;
            // $member->IDMBC = $memberContact->IDMBC;

            // $member->update();

            // With bound parameters
            $query = $this->modelsManager->createQuery('UPDATE Member set IDMBA = :idmba:, IDMBC = :idmbc: WHERE IDMB = :idmb:');
            $updateMember  = $query->execute(
                [
                    'idmb' => $member->IDMB,
                    'idmba' => $memberAddress->IDMBA,
                    'idmbc' => $memberContact->IDMBC
                ]
            );

            $this->response->successMessage = 'Cadastrado com sucesso!';

            return $this->return();

        } catch (Exception $e) {
            
            $this->response->errorCode = 1;
            $this->response->errorMessage = $e->getMessage();

            return $this->return();
        }
    }

    // Edita um cliente
    public function editAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        // 
        if(!isset($this->post->userType) || $this->post->userType != 3) {
            $this->response->errorCode = 1;
            $this->response->errorMessage = 'Tipo de usuário deve ser CLIENTE';
            return $this->return();
        }

        // Instancia um novo validator que verifica o email
        $validation = new Validation();
        $validation->add(
            [
                "userId",
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
                        "userId"  => "Informe o ID do usuário",
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

            $member = Member::findFirst([ 
                'IDMB = :idmb:',
                'bind' => [
                'idmb' => parent::sanitize($this->post->userId, 'int'),
                ]
            ]);

            if(!$member) {
                throw new Exception("Nenhum usuário encontrado");
            }

            // $transactionManager = new TransactionManager();
            // $this->transaction = $transactionManager->get();
            
            // $member = new Member();
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
                        'Erro ao salvar usuario novo cliente Member'
                    );

                    throw new Exception(parent::internalError()); 
                }
            }

            // Salva o contato do usuário
            $memberContact = MemberContact::findFirst([ 
                'IDMB = :idmb:',
                'bind' => [
                'idmb' => parent::sanitize($this->post->userId, 'int'),
                ]
            ]);

            if(!$memberContact) {
                throw new Exception("Nenhum usuário encontrado");
            }

            $memberContact->Email = isset($this->post->email) ? $this->post->email : NULL;
            $memberContact->Phone = isset($this->post->phone) ? parent::sanitize($this->post->phone, 'int') : NULL;
            $memberContact->SecondPhone = isset($this->post->secondPhone) ? parent::sanitize($this->post->secondPhone, 'int') : NULL;

            // Salva o contato do usuário
            if(!$memberContact->update()) {
                foreach ($memberContact->getMessages() as $message) {

                    // Grava o erro
                    parent::saveError(
                        $this->dispatcher->getControllerName(),
                        $this->dispatcher->getActionName(),
                        $message,
                        'Erro ao salvar o contato do novo cliente'
                    );

                    throw new Exception(parent::internalError()); 
                }
            }

            // Salva o endereço do usuário
            $memberAddress = MemberAddress::findFirst([ 
                'IDMB = :idmb:',
                'bind' => [
                'idmb' => parent::sanitize($this->post->userId, 'int'),
                ]
            ]);

            if(!$member) {
                throw new Exception("Nenhum usuário encontrado");
            }

            $memberAddress->IDGC = isset($this->post->city) ? parent::sanitize($this->post->city, 'int') : NULL;
            $memberAddress->IDGS = isset($this->post->state) ? parent::sanitize($this->post->state, 'int') : NULL;
            $memberAddress->Distric = isset($this->post->district) ? parent::sanitize($this->post->district, 'string') : NULL;
            $memberAddress->Street = isset($this->post->street) ? parent::sanitize($this->post->street, 'string') : NULL;
            $memberAddress->Number = isset($this->post->number) ? parent::sanitize($this->post->number, 'string') : NULL;
            $memberAddress->Complement = isset($this->post->complement) ? parent::sanitize($this->post->complement, 'string') : NULL;
            $memberAddress->ZipCode = isset($this->post->zipCode) ? parent::sanitize($this->post->zipCode, 'string') : NULL;

            // Salva o endereço do usuário
            if(!$memberAddress->update()) {
                
                //$this->transaction->rollBack();

                foreach ($memberAddress->getMessages() as $message) {

                    parent::saveError(
                    // Grava o erro
                        $this->dispatcher->getControllerName(),
                        $this->dispatcher->getActionName(),
                        $message,
                        'Erro ao salvar o endereço do novo cliente'
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

    // Deleta um cliente
    public function deleteAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        // 
        if(!isset($this->post->userId) || !is_integer($this->post->userId)) {
            $this->response->errorCode = 1;
            $this->response->errorMessage = 'Iforme o ID do usuário';
            return $this->return();
        }

        try {

            // Busca o usuário
            $member = Member::findFirst([ 
                'IDMB = :idmb:',
                'bind' => [
                'idmb' => parent::sanitize($this->post->userId, 'int'),
                ]
            ]);

            if(!$member) {

                throw new Exception("Usuário não encontrado");
            }

            // Altera o status para inativo
            $member->IDMBS = 2;

            // Salva o endereço do usuário
            if(!$member->update()) {

                foreach ($member->getMessages() as $message) {

                    parent::saveError(
                    // Grava o erro
                        $this->dispatcher->getControllerName(),
                        $this->dispatcher->getActionName(),
                        $message,
                        'Erro ao deletar cliente'
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

    // Retorna todos clientes
    public function getClientsAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        // Salva o contato do usuário
        $clients = Member::find([ 
            'IDMBT = :idmb:',
            'bind' => [
            'idmb' => 3,
            ]
        ]);

        if(!$clients) {
            // throw new Exception("Nenhum usuário encontrado");
            $this->response->successMessage = 'Nenhum usuário encontrado';
            return $this->return();
        } else {
            $this->response->data = $clients;
            return $this->return();
        }
    }

    // Retorna um cliente específico por ID
    public function getClientByIdAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        // 
        if(!isset($this->post->userId)) {
            $this->response->errorCode = 1;
            $this->response->errorMessage = 'userId(ID do usuário) precisa ser informado';
            return $this->return();
        }

        // Salva o contato do usuário
        $client = Member::findFirst([ 
            'IDMBT = :idmbt: AND IDMB = :idmb:',
            'bind' => [
            'idmbt' => 3,
            'idmb' => parent::sanitize($this->post->userId, 'int'),
            ]
        ]);

        if(!$client) {

            $this->response->successMessage = 'Nenhum usuário encontrado';
            return $this->return();
        } else {
            $this->response->data = $client;
            return $this->return();
        }
    }

    // Retorna clientes específico por nome utilizando o operador LIKE
    public function getClientsByNameAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        // 
        if(!isset($this->post->client)) {
            $this->response->errorCode = 1;
            $this->response->errorMessage = 'Informe o nome';
            return $this->return();
        }

        // Salva o contato do usuário
        $clients = Member::find([ 
            'conditions' => 'Name LIKE :name:',
            'bind' => [
            'name' => '%' . parent::sanitize($this->post->client, 'string') . '%'
            ]
        ]);

        if(!count($clients) > 0) {

            $this->response->successMessage = 'Nenhum usuário encontrado';
            $this->response->data = $clients;
            return $this->return();
        } else {
            $this->response->data = $clients;
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