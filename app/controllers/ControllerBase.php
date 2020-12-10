<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    // Inicializada por primeiro quando o controller é chamado
    public function initialize() {

        // Define o timezone padrão
        date_default_timezone_set('America/Sao_Paulo');
    }

    // Gera token
    public function getToken() {

        return bin2hex(random_bytes(32)) . md5(date('Y-m-d h:i:s'));
    }

    // Verifica se o usuario está logado
    public function isLogged(){
        
        // Check if the variable is defined
        if (!$this->session->has(DEFAULT_ADMIN_SESSION)) {
            return $this->response->redirect('login');
        }
    }

    // Erro interno
    public function internalError() {

        return 'Houve um erro interno, tente novamente. Caso o erro persista, comunique o administrador';
    }

    // Verifica o token de requisiçao
    public function checkApiToken($token) {

        return (KEY_API == $token) ? true : false ;
    }

    // 
    public function checkApi() {
        
        // Verifica a requisição foi feita com AJAX
        if (!$this->request->isAjax()) {
            return false;
        }

        // Verifica se existe um metodo Post
        if (!($this->request->isPost())) {
            return false;
        }

        // Dados vindo do POST e descriptografa
        $post = $this->decryptJS($this->request->getPost('config'), KEY_JS);
        
        if(!$post) {
            return false;
        }

        // Verifica a chave de comunicação da API
        if(isset($post->apiKey)) {

            if(!$this->checkApiToken($post->apiKey)) {
                return false;
            }
        }

        return $post;
    }

    // Salva um erro no sistema
    public function saveError($controller, $action, $message, $optionalMessage) {

        $error = new ErrorLog();

        $error->Controller = $controller;
        $error->Action = $action;
        $error->Message = $message;
        $error->OptionalMessage = $optionalMessage;

        $error->save();
    }

    // Limpa os dados
    public function sanitize($value, $type) {

        switch ($type) {
            case 'int':
                return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'phone':
                return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'cpf':
                return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'cep':
                return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'string':
                //return filter_var($value, FILTER_SANITIZE_STRING);
                $value = str_replace('\'', '', $value);
                $value = str_replace('\"', '', $value);
                $value = str_replace('*', '', $value);

                return $value;
                break;
            case 'sql':

                $value = str_replace('>', '', $value);
                $value = str_replace('<', '', $value);
                $value = str_replace('=', '', $value);
                $value = str_replace('\'', '', $value);
                $value = str_replace('\"', '', $value);
                $value = str_replace('*', '', $value);

                return $value;
                break;
            default:
                return $value;
                break;
        }
    }

    // Validação de CPF
    public function validateCPF($cpf) {
 
        // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
         
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }
    
        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
    
        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    // Js e Css Admin
    public function assetsAdmin() {

        // Adiciona o Css do Admin
        $this->assets->addCss(DEFAULT_LANDING_CSS);
        $this->assets->addCss(DEFAULT_BASE_CSS . 'all.min.css');
        $this->assets->addCss('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');
        $this->assets->addCss(DEFAULT_BASE_CSS . 'adminlte.min.css');
        $this->assets->addCss(DEFAULT_BASE_CSS . 'helper.css');
        $this->assets->addCss(DEFAULT_BASE_CSS . 'icheck-bootstrap.min.css');
        $this->assets->addCss('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700');

        // Adiciona o Js do Admin
        $this->assets->addJs(DEFAULT_BASE_JS);
        $this->assets->addJs('https://adminlte.io/themes/v3/dist/js/adminlte.js');
        $this->assets->addJs('https://adminlte.io/themes/v3/dist/js/pages/dashboard.js');
        $this->assets->addJs('https://adminlte.io/themes/v3/dist/js/demo.js');
    }

    // Js e Css Landing Page
    public function assetsLanding() {

        // Adiciona o CSS da Landing Page
        $this->assets->addCss(DEFAULT_LANDING_CSS);

        // Adiciona o JS da Landing Page
        $this->assets->addJs(DEFAULT_LANDING_JS);
    }

    public function validateCNPJ($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
        
        // Valida tamanho
        if (strlen($cnpj) != 14)
            return false;

        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;	

        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;

        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }

    // Criptografia de dados
    public function encrypt($data, $key) 
    {
        $encryption_key = base64_decode($key);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    // Descriptografia de dados
    public function decrypt($data, $key) 
    {
        $encryption_key = base64_decode($key);
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }

    // Descriptografia de dados criptografados vindo do JS
    public function decryptJS($data, $key)
    {
        $number = filter_var('AES-256-CBC', FILTER_SANITIZE_NUMBER_INT);
        $number = intval(abs($number));
        $json = json_decode(base64_decode($data), true);

        try {
            $salt = hex2bin($json["salt"]);
            $iv = hex2bin($json["iv"]);
        } catch (Exception $e) {
            return null;
        }

        $cipherText = base64_decode($json['ciphertext']);

        $iterations = intval(abs($json['iterations']));
        if ($iterations <= 0) {
            $iterations = 49;
        }
        $hashKey = hash_pbkdf2('sha512', $key, $salt, $iterations, ($number / 4));
        unset($iterations, $json, $salt);

        $decrypted= openssl_decrypt($cipherText , 'AES-256-CBC', hex2bin($hashKey), OPENSSL_RAW_DATA, $iv);
        unset($cipherText, $hashKey, $iv);

        return json_decode($decrypted);
    }

    // Criptografia para dados a serem enviados para o front-end
    public function encryptJS($data, $key)
    {
        $number = filter_var('AES-256-CBC', FILTER_SANITIZE_NUMBER_INT);
        $number = intval(abs($number));
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = openssl_random_pseudo_bytes($ivLength);
    
        $salt = openssl_random_pseudo_bytes(32);
        $iterations = 49;
        $hashKey = hash_pbkdf2('sha512', $key, $salt, $iterations, ($number / 4));
        $encryptedString = openssl_encrypt($data, 'AES-256-CBC', hex2bin($hashKey), OPENSSL_RAW_DATA, $iv);

        $encryptedString = base64_encode($encryptedString);
        unset($hashKey);

        $output = ['ciphertext' => $encryptedString, 'iv' => bin2hex($iv), 'salt' => bin2hex($salt), 'iterations' => $iterations];
        unset($encryptedString, $iterations, $iv, $ivLength, $salt);

        return base64_encode(json_encode($output));
    }
}