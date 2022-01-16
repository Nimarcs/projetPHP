<?php
declare(strict_types=1);

/**
 * Fichier principal: index.php
 * Appartient au projet wishlist
 * @author Fabrice ARNOUT, Guillaume RENARD, Marcus RICHIER, Mathieu VINOT, Lucas WEISS
 */

# Chargement de l'autoload
require_once __DIR__ . '/vendor/autoload.php';

# Importations des fichiers necessaires
use mywishlist\controler\ControlerGestionCompte;
use mywishlist\controler\ControlerGestionItem;
use mywishlist\controler\ControlerGestionListe;
use mywishlist\controler\ControlerRacine;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as DB;
use Slim\App;
use Slim\Container;

# Installation de la configuration erreur de Slim
$config = ['settings' => ['displayErrorDetails' => true, 'dbconf' => __DIR__.'/src/config/dbconfig.ini']];

# Connection a la base de donnees MYSQL
# Chargement du module Eloquent
$db = new DB();
$db->addConnection(parse_ini_file(__DIR__.'/src/config/dbconfig.ini'));
$db->setAsGlobal();
$db->bootEloquent();

$container = new Container($config);
$app = new App($container);

##### LES ROUTES #####

/**
 * Fonction 0, route racine de l'accueil
 * @author Lucas Weiss
 */
$app->get('/', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerRacine($container);
    return $controleur->racine($rq, $rs, $args);
})->setName('accueil');

/**
 * Fonction 1, afficher une liste avec un token de lecture
 * @author Mathieu Vinot
 */
$app->get('/liste/{token}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->AffichageListe($rq, $rs, $args, false);
})->setName('afficherListe');

/**
 * Fonction 1 bis, afficher une liste avec un token d'édition
 * @author Guillaume Renard
 */
$app->get('/liste/edition/{token_edition}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->AffichageListe($rq, $rs, $args, true);
})->setName('afficherListeEdition');

/**
 * Fonction 6, créer une liste
 * @author Lucas Weiss
 */
$app->get('/newListe[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->creerListe($rq, $rs, $args);
})->setName('creationListe');
$app->post('/newListe[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->creerListe($rq, $rs, $_POST);
})->setName('creationListe');

/**
 * Fonction 8, ajouter un item a une liste
 * @author Lucas Weiss
 */
$app->get('/liste/{token}/newItem[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionItem($container);
    return $controleur->ajouterNouvelItem($rq, $rs, $args);
})->setName('newItem');
$app->post('/liste/{token}/newItem[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionItem($container);
    return $controleur->ajouterNouvelItem($rq, $rs, $_POST);
})->setName('new Item');





/**
 * Fonction 17, creer un compte
 * @author Lucas Weiss
 */
$app->get('/newCompte', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionCompte($container);
    return $controleur->creerCompte($rq, $rs, $args);
})->setName('creationCompte');
$app->post('/newCompte', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionCompte($container);
    return $controleur->creerCompte($rq, $rs, $_POST);
})->setName('creationCompte');

/**
 * Fonction 18, se connecter un compte
 * @author Lucas Weiss
 */
$app->get('/connectionCompte', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionCompte($container);
    return $controleur->seConnecterCompte($rq, $rs, $args);
})->setName('connectionCompte');
$app->post('/connectionCompte', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionCompte($container);
    return $controleur->seConnecterCompte($rq, $rs, $_POST);
})->setName('connectionCompte');
$app->get('/deconectionCompte', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionCompte($container);
    return $controleur->seDeconnecterCompte($rq, $rs, $_POST);
})->setName('deconectionCompte');

/**
 * Fonction 21, afficher les listes publiques
 * @author Lucas Weiss
 */
$app->get('/listes', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->afficherListesPublique($rq, $rs, $args);
})->setName('affichageListesPublique');




# On lance l'app
session_start();
$app->run();
