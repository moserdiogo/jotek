<?php

class HelperController extends ControllerBase
{
    // Primeira função a ser executada
    public function initialize()
    {
        // Executa o construtor da classe ControllerBase
        parent::initialize();   
    }

    public function indexAction()
    {

    }

    // Retorna um estado
    public function getState($id) {

        // Busca o estado    
        $state = Member::findFirst([ 
            'IDGS = :stateId:',
            'bind' => [
            'stateId' => parent::sanitize($id, 'int')
            ]
        ]);

        if(count($state) > 0) {

            return $state;
        } else {
            return false;
        }
    }

    // Retorna uma cidade
    public function getCity($id) {

        TODO:
    }
    // Retorna todos estados
    public function getStates() {

        TODO:
    }

    // Retorna todas cidades
    public function getCities() {

        TODO:
    }

    // Retorna todos estados e cidades
    public function getStatesCities() {

        TODO:
    }
}