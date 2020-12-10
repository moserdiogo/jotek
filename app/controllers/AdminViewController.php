<?php

class AdminViewController extends ControllerBase
{

    public function initialize()
    {
        parent::assetsAdmin();
    }

    // Renderiza a página de adicionar cliente
    public function createClientAction() {
        return $this->view->render('admin', 'client/create');
    }

    // Renderiza a página de adicionar orçamento
    public function createBudGetAction() {
        return $this->view->render('admin', 'budget/create');
    }

}