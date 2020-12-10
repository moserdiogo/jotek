<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

/**
 * IMPORTANTE: Constantes
 */
define('DEFAULT_BASE_PATH', 'http://localhost/phalcon/production/jotek/');
define('DEFAULT_IMAGE_PATH', DEFAULT_BASE_PATH . 'public/img/admin/');
define('DEFAULT_ADMIN_SESSION', 'AuthAdmin'); // Nome padrão da sessão do administrador
define('DEFAULT_LANDING_CSS', DEFAULT_BASE_PATH . 'public/css/style.css'); // CSS da landing page
define('DEFAULT_LANDING_JS', BASE_PATH . 'public/js/script.js'); // JS da landing page



// Componentes
define('DEFAULT_BASE_COMPONENTS', 'C:/Users/Moser/Dropbox/www/phalcon/production/jotek/app/views/components');

/**
 * IMPORTANTE: CSS usado no desenvolvimento pois pode usar o grunt watch para não precisar buildar o projeto a cada alteração
 */
//define('DEFAULT_BASE_CSS', DEFAULT_BASE_PATH . 'public/grunt/css/style.css');
define('DEFAULT_BASE_CSS', DEFAULT_BASE_PATH . 'public/css/admin-lte/');

 /**
 * IMPORTANTE: CSS de produção minificado, usar somente ao fazer deploy
 */
//define('DEFAULT_BASE_CSS', DEFAULT_BASE_PATH . 'public/css/style.css');

/**
 * IMPORTANTE
 * JS usado no desenvolvimentopois pode usar o grunt watch para não precisar buildar o projeto a cada alteração
 */
define('DEFAULT_BASE_JS', DEFAULT_BASE_PATH . 'public/grunt/js/script.js');

/**
 * IMPORTANTE
 * JS minificado, usar para produção
 */
//define('DEFAULT_BASE_JS', DEFAULT_BASE_PATH . 'public/js/script.js');

// Chave de criptografia e descriptografia de dados
define('KEY_TOKEN', 'AbIjURuD55Yw5wd0mdHP7ytRuDySLlM6wt2vteuiniQBqE70nWeB7U=');

// Chave para descriptografia de dados vindos do cliente
define('KEY_JS', '$2y$12$at83icveIsd1KgYNTbtbE.XJTI.Iw35R2yh8LlheZqN1XlreqBTjmCoFrY');

// Chave de verificacao da API
define('KEY_API', 'sbHL7Y16Q9pTQ4FxU516enZxDWt9hihlHNM5NZxFtk!y08bQ');

/**
 * IMPORTANTE: Variaveis de acordo com a base de dados
 */
define('DB_CLIENT', 'Cliente');

return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => 'mysql741.umbler.com:41890',
        'username'    => 'joteckcwb79g',
        'password'    => 'BYvyX1Q97uGHuZuA4M9vBS',
        'dbname'      => 'joteck',
        'charset'     => 'utf8',
    ],
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => BASE_PATH . '/cache/',

        // This allows the baseUri to be understand project paths that are not in the root directory
        // of the webpspace.  This will break if the public/index.php entry point is moved or
        // possibly if the web server rewrite rules are changed. This can also be set to a static path.
        'baseUri'        => preg_replace('/public([\/\\\\])index.php$/', '', $_SERVER["PHP_SELF"]),
    ]
]);
