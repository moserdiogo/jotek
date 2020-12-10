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

class GeoController extends ControllerBase
{
    private $response;
    private $post = NULL;
    private $transaction;

    // Primeira função a ser executada
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

    // Retorna um estado
    public function getStateAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        // 
        if(!isset($this->post->stateId)) {
            $this->response->errorCode = 1;
            $this->response->errorMessage = 'Falta o ID do estado';
            return $this->return();
        }

        try {
            
            // Busca o estado    
            $state = GeoState::findFirst([ 
                'IDGS = :stateId:',
                'bind' => [
                'stateId' => parent::sanitize($this->post->stateId, 'int')
                ]
            ]);

            if(!$state) {

                throw new Exception("Nenhum registro encontrado");
            } else {

                $this->response->data = $state;
                return $this->return();
            }

        } catch (Exception $e) {
            
            // Resposta da API
            $this->response->errorCode = 1;
            $this->response->errorMessage = $e->getMessage();
            return $this->return();
        }
    }

    // Retorna uma cidade
    public function getCityAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        // 
        if(!isset($this->post->cityId)) {
            $this->response->errorCode = 1;
            $this->response->errorMessage = 'Falta o ID da cidade';
            return $this->return();
        }

        try {
            
            // Busca o estado    
            $city = GeoCity::findFirst([ 
                'IDGC = :cityId:',
                'bind' => [
                'cityId' => parent::sanitize($this->post->cityId, 'int')
                ]
            ]);

            if(!$city) {

                throw new Exception("Nenhum registro encontrado");
            } else {

                $this->response->data = $city;
                return $this->return();
            }

        } catch (Exception $e) {
            
            // Resposta da API
            $this->response->errorCode = 1;
            $this->response->errorMessage = $e->getMessage();
            return $this->return();
        }
    }

    // Retorna todos estados
    public function getStatesAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        try {
            
            // Busca o estado    
            $states = GeoState::find();

            if(!$states) {

                throw new Exception("Nenhum registro encontrado");
            } else {

                $this->response->data = $states;
                return $this->return();
            }

        } catch (Exception $e) {
            
            // Resposta da API
            $this->response->errorCode = 1;
            $this->response->errorMessage = $e->getMessage();
            return $this->return();
        }
    }

    // Retorna todas cidades
    public function getCitiesAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        try {
            
            // Busca o estado    
            $cities = GeoCity::find();

            if(!$cities) {

                throw new Exception("Nenhum registro encontrado");
            } else {

                $this->response->data = $cities;
                return $this->return();
            }

        } catch (Exception $e) {
            
            // Resposta da API
            $this->response->errorCode = 1;
            $this->response->errorMessage = $e->getMessage();
            return $this->return();
        }
    }

    // Retorna todas cidade de acordo com o estado
    public function getCitiesByStateAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        // 
        if(!isset($this->post->stateId)) {
            $this->response->errorCode = 1;
            $this->response->errorMessage = 'Falta o ID do estado';
            return $this->return();
        }

        try {
            
            // Busca o estado    
            $cities = GeoCity::find([ 
                'CityUF = :stateId:',
                'bind' => [
                'stateId' => parent::sanitize($this->post->stateId, 'int')
                ]
            ]);

            if(!count($cities) > 0) {

                throw new Exception("Nenhum registro encontrado");
            } else {

                $this->response->data = $cities;
                return $this->return();
            }

        } catch (Exception $e) {
            
            // Resposta da API
            $this->response->errorCode = 1;
            $this->response->errorMessage = $e->getMessage();
            return $this->return();
        }
    }

    // Retorna todas cidades com operador LIKE
    public function getCitiesByNameAction() {

        // Verifica a chave de comunicacao da API
        if(!parent::checkApi()) {
            // Retorna erro 400 na requisição se a chave não estiver correta
            return http_response_code(400);
        } else {
            // Descriptografa os dados vindos por POST
            $this->post = parent::checkApi();
        }

        // 
        if(!isset($this->post->city)) {
            $this->response->errorCode = 1;
            $this->response->errorMessage = 'Falta o nome da cidade';
            return $this->return();
        }

        try {

            $cities = GeoCity::Find([
                'conditions' => 'City LIKE :city:',
                'bind' => [
                'city' => '%' . parent::sanitize($this->post->city, 'string') . '%',
                ],
                'limit' => 10
            ]);

            if(!count($cities) > 0) {

                throw new Exception("Nenhum registro encontrado");
            } else {

                $this->response->data = $cities;
                return $this->return();
            }

        } catch (Exception $e) {
            
            // Resposta da API
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