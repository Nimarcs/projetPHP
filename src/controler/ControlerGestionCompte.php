<?php
declare(strict_types=1);

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

    /**
     * Fonction 17
     * creer un compte
     * @author Lucas Weiss
     */
    public function creerCompte(Request $rq, Response $rs, array $args) {
        try {
            $vue = new VueGestionCompte($this->container);
            if (sizeof($args) == 2) {
                if ($this->loginValide(filter_var($args['login'], FILTER_SANITIZE_STRING)) == true) {
                    $this->creerCompteInBDD($args);
                    setcookie("lastPSEUDO", "", time() - 60*60, "/"); //On supprime le cookie qui stocke le pseudo utilise en dernier car le compte le fait deja
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

    /**
     * Fonction 17
     * Methode privée qui teste si le login existe ou non
     * retourne un boolean
     * @author Lucas Weiss
     */
    private function loginValide($login) {
            $res =  Compte::where('login', '=', $login)->get();
            $r = $res->count();
            if ($r==0) return true;
            else return false;
    }

    /**
     * Fonction 17
     * Methode privee qui creer un compte dans la base de donnees
     * @author Lucas Weiss
     */
    private function creerCompteInBDD(array $args) {
        $c = new Compte();
        $c->login = filter_var($args['login'], FILTER_SANITIZE_STRING);
        $c->sel = password_hash(filter_var($args['psw'], FILTER_SANITIZE_STRING), PASSWORD_DEFAULT);
        $c->save();
    }

    /**
     * Fonction 18
     * Affiche get formulaire pour se connecter et post pour verifier
     * @author Lucas Weiss
     */
    public function seConnecterCompte(Request $rq, Response $rs, array $args) {
        try {
            $vue = new VueGestionCompte($this->container);
            if (sizeof($args) == 2) {
                $login = filter_var($args['login'], FILTER_SANITIZE_STRING);
                if ($this->loginValide($login) == false) {
                    if ($this->mdpValide($login, filter_var($args['psw'], FILTER_SANITIZE_STRING)) == true) {
                        $this->genereSessionConnexion($login);
                        setcookie("lastPSEUDO", "", time() - 60*60, "/"); //On supprime le cookie qui stocke le pseudo utilise en dernier car le compte le fait deja
                        $rs = $rs->withRedirect($this->container->router->pathFor('accueil'));
                    } else {
                        $vue = new VueRender($this->container);
                        $rs->getBody()->write($vue->render($vue->htmlErreur("Erreur, la connexion n'a pas pu aboutir. Erreur dans le mdp.")));
                    }
                } else {
                    $vue = new VueRender($this->container);
                    $rs->getBody()->write($vue->render($vue->htmlErreur("Erreur, la connexion n'a pas pu aboutir. Erreur dans le login")));
                }
            } else { // Si ce n'est pas le cas, la methode est un get
                $rs->getBody()->write($vue->render(2));
            }
        } catch (\Exception $e) {
            $rs->getBody()->write("Erreur a la connexion d'un compte...<br>".$e->getMessage()."<br>".$e->getTrace());
        }
        return $rs;
    }

    /**
     * Fonction 18
     * Methode pour se deconnecter de son compte
     * @author Lucas Weiss
     */
    public function seDeconnecterCompte(Request $rq, Response $rs, array $args) {
        $this->supprimerSessionConnexion();
        $rs = $rs->withRedirect($this->container->router->pathFor('accueil'));
        return $rs;
    }

    /**
     * Fonction 18
     * Verifie que le mot de passe est valide
     * @author Lucas Weiss
     */
    private function mdpValide($login, $mdp) {
        $sel = Compte::select('sel')->where('login', '=', $login)->first();
        return password_verify($mdp, $sel["sel"]);
    }

    /**
     * Fonction 18
     * Genere une variable session pour la connection
     * @author Lucas Weiss
     */
    private function genereSessionConnexion($login){
        session_start();
        $_SESSION['login'] = $login;
    }

    /**
     * Fonction 18
     * Methode qui supprime la session et donc qui deconnecte l'utilisateur
     * @author Lucas Weiss
     */
    private function supprimerSessionConnexion() {
        session_destroy();
    }

}
