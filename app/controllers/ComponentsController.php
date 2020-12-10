<?php

class ComponentsController extends ControllerBase
{

    public function indexAction(){

    }
    
    // 
    public function testAction() {

        return $this->view->render('components', 'forms/client-create');
    }
}