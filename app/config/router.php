<?php

$router = $di->getRouter();

// Define your routes here

$router->handle();

/**
 * API
 */
$router->add('/api/v1/user/create', ['controller' => 'member', 'action' => 'create']);
$router->add('/api/v1/user/delete', ['controller' => 'member', 'action' => 'delete']);
$router->add('/api/v1/user', ['controller' => 'member', 'action' => 'getUser']); // Precisa informar o tipo de usuario deseja retornar
$router->add('/api/v1/user/edit', ['controller' => 'member', 'action' => 'edit']); // Edita um usuário, precisa passar o ID do usuário

$router->add('/api/v1/client/create', ['controller' => 'client', 'action' => 'create']); // 
$router->add('/api/v1/client/edit', ['controller' => 'client', 'action' => 'edit']); //
$router->add('/api/v1/client/delete', ['controller' => 'client', 'action' => 'delete']); // Deleta um usuário, precisa ser informado o userId(ID do usuário)
$router->add('/api/v1/client/getClients', ['controller' => 'client', 'action' => 'getClients']); // 
$router->add('/api/v1/client/getClientById', ['controller' => 'client', 'action' => 'getClientById']); // 
$router->add('/api/v1/client/getClientsByName', ['controller' => 'client', 'action' => 'getClientsByName']); // 

// Retorna um estado, precisa passar o ID do estado {stateId}
$router->add('/api/v1/geo/getState', ['controller' => 'geo', 'action' => 'getState']);

// Retorna uma cidade, precisa passar o ID da cidade {cityId}
$router->add('/api/v1/geo/getCity', ['controller' => 'geo', 'action' => 'getCity']);

// Retorna todos estados
$router->add('/api/v1/geo/getStates', ['controller' => 'geo', 'action' => 'getStates']);

// Retorna todas as cidades
$router->add('/api/v1/geo/getCities', ['controller' => 'geo', 'action' => 'getCities']);

// Retorna todas as cidades
$router->add('/api/v1/geo/getCitiesByState', ['controller' => 'geo', 'action' => 'getCitiesByState']);


 /**
  * Admin
  */
  $router->add('/admin', ['controller' => 'admin', 'action' => 'index']);
  $router->add('/login', ['controller' => 'login', 'action' => 'index']);
  $router->add('/login/entrar', ['controller' => 'login', 'action' => 'submit']);
  $router->add('/sair', ['controller' => 'login', 'action' => 'logout']);
  $router->add('/cliente-adicionar', ['controller' => 'adminview', 'action' => 'createClient']);
  $router->add('/orcamento-adicionar', ['controller' => 'adminview', 'action' => 'createBudGet']);

  // Testes
  $router->add('/test', ['controller' => 'index', 'action' => 'test']);

  /**
   * Site
   */