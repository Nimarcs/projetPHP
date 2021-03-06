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
 * Fonction 1, afficher une liste avec un token de lecture ou token d'édition + Fonction 14, partager une liste
 * @author Mathieu Vinot (lecture)
 * @author Guillaume Renard (edition)
 * @author Marcus Richier (fusion des deux)
 */
$app->get('/liste/{token}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->AffichageListe($rq, $rs, $args, false);
})->setName('afficherListe');

/**
 * Fonction afficher ses listes personnelles
 * @author Mathieu Vinot
 */
//on affiche les listes où le login est le même que celui de $_session
$app->get('/listePerso[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->AffichageListePerso($rq, $rs, $args);
})->setName('afficherListePerso');

/**
 * Fonction 2
 * Afficher item
 * @author Mathieu Vinot
 */
$app->get('/item/{token}/{id}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionItem($container);
    return $controleur->affichageItem($rq, $rs, $args);
})->setName('afficherItem');

/**
 * Fonction 3, reserver un item
 * @author Marcus Richier
 */
$app->get('/reserverItem/{token}/{id}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionItem($container);
    return $controleur->reserverItem($rq, $rs, $args);
})->setName('reserverItem');
$app->post('/reserverItem/{token}/{id}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionItem($container);
    return $controleur->reserverItem($rq, $rs, $_POST);
})->setName('reserverItem');

/**
 * Fonctionnalité 5, ajout d'un message sur une liste
 * @author Mathieu Vinot
 */

$app->get('/posterUnMessage/{token}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->posterUnMessage($rq, $rs, $args);
})->setName('posterUnMessage');
$app->post('/posterUnMessage/{token}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->posterUnMessage($rq, $rs, $_POST);
})->setName('posterUnMessage');


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
 * Fonction 7, modifier information sur la liste
 * @author Guillaume Renard
 */
$app->get('/liste/modification/{token_edition}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->modifierListe($rq, $rs, $args);
})->setName('modifierListe');
$app->post('/liste/modification/{token_edition}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->modifierListe($rq, $rs, $_POST);
})->setName('modifierListe');

/**
 * Fonction ?, supprimer une liste
 * @author Guillaume Renard
 */
$app->get('/liste/edition/{token_edition}/supression[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->supprimerListe($rq, $rs, $args);
})->setName('suprimerListe');
$app->post('/liste/edition/{token_edition}/supression[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->supprimerListe($rq, $rs, $_POST);
})->setName('suprimerListe');


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
    return $controleur->ajouterNouvelItem($rq, $rs, $args);
})->setName('newItem');

/**
 * Fonction 9, Modifier un item
 * @Author Fabrice Arnout
 */

$app->get('/modifierItem/{token}/{id}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionItem($container);
    return $controleur->modifierItem($rq, $rs, $args);
})->setName('modifierItem');
$app->post('/modifierItem/{token}/{id}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionItem($container);
    return $controleur->modifierItem($rq, $rs, $_POST);
})->setName('modifierItem');

/**
 * Fonction 10, supprimer un item
 * @Author Marcus Richier
 */

$app->get('/supprimerItem/{token}/{id}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionItem($container);
    return $controleur->supprimerItem($rq, $rs, $args);
})->setName('supprimerItem');
$app->post('/supprimerItem/{token}/{id}[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionItem($container);
    return $controleur->supprimerItem($rq, $rs, $_POST);
})->setName('supprimerItem');


/**
 * Fonction 13, Supprimer une image d'un item
 * @author Mathieu Vinot
 */
/*
$app->post('/item/{id}/supprimerImage[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionItem($container);
    return $controleur->supprimerImageItem($rq, $rs, $_POST);
})->setName('supprimerImageItem');
*/




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
 * Fonction 19, modification des information du compte
 * @author Guillaume Renard
 */
$app->get('/modificationCompte[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionCompte($container);
    return $controleur->modifierCompte($rq, $rs, $args);
})->setName('modificationCompte');
$app->post('/modificationCompte[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionCompte($container);
    return $controleur->modifierCompte($rq, $rs, $_POST);
})->setName('modificationCompte');
$app->get('/modificationCompte/mdp[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionCompte($container);
    return $controleur->modifierMdp($rq, $rs, $args);
})->setName('modificationMotDePasse');
$app->post('/modificationCompte/mdp[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionCompte($container);
    return $controleur->modifierMdp($rq, $rs, $_POST);
})->setName('modificationMotDePasse');




/**
 * Fonction 21, afficher les listes publiques
 * @author Lucas Weiss
 */
$app->get('/listes', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->afficherListesPublique($rq, $rs, $args);
})->setName('affichageListesPublique');




/**
 * Fonction 27, supression d'un compte
 * @author Guillaume Renard
 */
$app->get('/modificationCompte/supression[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionCompte($container);
    return $controleur->supprimerCompte($rq, $rs, $args);
})->setName('supprimerCompte');
$app->post('/modificationCompte/supression[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionCompte($container);
    return $controleur->supprimerCompte($rq, $rs, $_POST);
})->setName('supprimerCompte');


/**
 * Fonction 28, Joindre des listes à son compte
 * @author Marcus Richier
 */
$app->post('/ajouterProprietaire[/]', function (Request $rq, Response $rs, array $args) use ($container): Response {
    $controleur = new ControlerGestionListe($container);
    return $controleur->ajouterProprietaire($rq, $rs, $_POST);
})->setName('ajouterProprietaire');

# On lance l'app
session_start();
$app->run();
