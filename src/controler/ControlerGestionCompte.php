<?php

// NAMESPACE
namespace mywishlist\controler;

// IMPORTS
use Illuminate\Database\Eloquent\ModelNotFoundException;
use mywishlist\modele\Compte;
use mywishlist\view\VueGestionCompte;
use mywishlist\view\VueRender;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Container;

/**
 * Classe ControlerGestionCompte
 * Controleur sur la gestion des comptes
 */
class ControlerGestionCompte
{

    // ATTRIBUTS
    private $container;

    // CONSTRUCTEUR
    public function __construct(Container $container) {
        $this->container = $container;
    }

    // METHODES

    public function creerCompte(Request $rq, Response $rs, array $args) {
        try {
            $vue = new VueGestionCompte($this->container);
            if (sizeof($args) == 2) {
                if ($this->loginValide(filter_var($args['login'], FILTER_SANITIZE_STRING)) == true) {
                    $this->creerCompteInBDD($args);
                    $rs = $rs->withRedirect($this->container->router->pathFor('accueil'));
                } else {
                    $vue = new VueRender($this->container);
                    $rs->getBody()->write($vue->render($vue->htmlErreur("Erreur, login déjà existant!")));
                }
            } else { // Si ce n'est pas le cas, la methode est un get
                $rs->getBody()->write($vue->render(1));
            }
        } catch (\Exception $e) {
            $rs->getBody()->write("Erreur dans la creation d'un compte...<br>".$e->getMessage()."<br>".$e->getTrace());
        }
        return $rs;
    }

    private function loginValide($login) {
            $res =  Compte::where('login', '=', $login)->get();
            $r = $res->count();
            if ($r==0) return true;
            else return false;
    }

    private function creerCompteInBDD(array $args) {
        $c = new Compte();
        $c->login = filter_var($args['login'], FILTER_SANITIZE_STRING);
        $c->sel = password_hash(filter_var($args['psw'], FILTER_SANITIZE_STRING), PASSWORD_DEFAULT);
        $c->mdp = filter_var($args['psw'], FILTER_SANITIZE_STRING);
        $c->save();
    }

}
