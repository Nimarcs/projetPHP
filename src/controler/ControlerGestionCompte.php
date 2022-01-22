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
    public function creerCompte(Request $rq, Response $rs, array $args): Response {
        try {

            $vue = new VueGestionCompte($this->container);
            if (sizeof($args) == 7) {

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
    private function loginValide( string $login) : bool{
        if ($login == "anonyme") return false; //valeur prise
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
    private function creerCompteInBDD(array $args) : void{
        $c = new Compte();
        $c->login = filter_var($args['login'], FILTER_SANITIZE_STRING);
        $c->sel = password_hash(filter_var($args['psw'], FILTER_SANITIZE_STRING), PASSWORD_DEFAULT);
        $c->email = filter_var($args['email'], FILTER_SANITIZE_EMAIL);
        $c->date_naissance = $args['dateN'];
        $c->num_dep = filter_var($args['numDep'], FILTER_SANITIZE_NUMBER_INT);
        $c->ville = filter_var($args['ville'], FILTER_SANITIZE_STRING);
        $c->adresse = filter_var($args['adresse'], FILTER_SANITIZE_STRING);
        $c->save();
    }

    /**
     * Fonction 18
     * Affiche get formulaire pour se connecter et post pour verifier
     * @author Lucas Weiss
     */
    public function seConnecterCompte(Request $rq, Response $rs, array $args): Response {
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
    public function seDeconnecterCompte(Request $rq, Response $rs, array $args): Response {
        $this->supprimerSessionConnexion();
        $rs = $rs->withRedirect($this->container->router->pathFor('accueil'));
        return $rs;
    }

    /**
     * Fonction 18
     * Verifie que le mot de passe est valide
     * @author Lucas Weiss
     */
    private function mdpValide($login, $mdp): bool {
        $sel = Compte::select('sel')->where('login', '=', $login)->first();
        return password_verify($mdp, $sel["sel"]);
    }

    /**
     * Fonction 18
     * Genere une variable session pour la connection
     * @author Lucas Weiss
     */
    private function genereSessionConnexion($login): void{
        session_start();
        $_SESSION['login'] = $login;
    }

    /**
     * Fonction 18
     * Methode qui supprime la session et donc qui deconnecte l'utilisateur
     * @author Lucas Weiss
     */
    private function supprimerSessionConnexion() :void {
        session_destroy();
    }



    /**
     * Fonction 19
     * Methode qui modifie les informations du compte
     * @author Guillaume Renard
     */
    public function modifierCompte(Request $rq, Response $rs, array $args) : Response {
        try {
            $vue = new VueGestionCompte($this->container);

            var_dump($args);
            if ($rq->isPost() && sizeof($args) == 5) {
                $c = $this->recupererCompte($_SESSION['login']);

                $this->modifierCompteInBDD($c, $args);
                $rs = $rs->withRedirect($this->container->router->pathFor('accueil'));
            } else {
                $com = $this->recupererCompte($_SESSION['login']);

                $rs->getBody()->write($vue->render(3, $com));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("Erreur dans la modification de la liste...<br>".$e->getMessage()."<br>".$e->getTrace()));
        }
        return $rs;
    }


    private function recupererCompte(string $log) : ?Compte{
        try {
            return Compte::query()->where('login', '=', $log)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    private function modifierCompteInBDD($compte, $chgemnt): void{

        $compte->email = filter_var($chgemnt['email'], FILTER_SANITIZE_EMAIL);
        $compte->date_naissance = $chgemnt['dateN'];
        $compte->num_dep = filter_var($chgemnt['numDep'], FILTER_SANITIZE_NUMBER_INT);
        $compte->ville = filter_var($chgemnt['ville'], FILTER_SANITIZE_STRING);
        $compte->adresse = filter_var($chgemnt['adresse'], FILTER_SANITIZE_STRING);
        $compte->save();
    }

}
