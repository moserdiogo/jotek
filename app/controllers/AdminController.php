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

class AdminController extends ControllerBase
{

    // Primeira função a iniciar antes de qualquer outra
    public function initialize()
    {
        // Verifica se o usuário está logado
        parent::isLogged();

        // Assets
        parent::assetsAdmin();
    }

    // Renderiza a página inicial do Admin
    public function indexAction()
    {
    }
}