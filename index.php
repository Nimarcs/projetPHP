<?php

/**
 * Fichier principal: index.php
 * Appartient au projet wishlist
 * @author Fabrice ARNOUT, Guillaume RENARD, Marcus RICHIER, Mathieu VINOT, Lucas WEISS
 */

# Chargement de l'autoload
require_once __DIR__ . '/vendor/autoload.php';

# Importations des fichiers necessaires
use mywishlist\controler\ControlerRacine;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as DB;
use Slim\App;
use Slim\Container;

# Installation de la configuration erreur de Slim
$config = ['settings' => ['displayErrorDetails' => true, 'dbconf' => './src/config/dbconfig.ini']];

# Connection a la base de donnees MYSQL
# Chargement du module Eloquent
$db = new DB();
$db->addConnection(parse_ini_file('src\config\dbconfig.ini'));
$db->setAsGlobal();
$db->bootEloquent();

$container = new Container($config);
$app = new App($container);

##### LES ROUTES #####

/**
 * Fonction 0, route racine de l'accueil
 */
$app->get('/', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerRacine($container);
    return $controleur->racine($rq, $rs, $args);
})->setName('accueil');

$app->run();
