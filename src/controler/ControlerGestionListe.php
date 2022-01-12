<?php
declare(strict_types=1);

// NAMESPACE
namespace mywishlist\controler;

// IMPORTS
use Carbon\Exceptions\Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use mywishlist\modele\Item;
use mywishlist\modele\Liste;
use mywishlist\view\VueGestionListe;
use mywishlist\view\VueRender;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Container;

/**
 * Classe ControlerGestionListe,
 * Controleur sur la gestion de listes
 */
class ControlerGestionListe
{

    // ATTRIBUTS
    private $container;

    // CONSTRUCTEUR
    public function __construct(Container $container) {
        $this->container = $container;
    }

    // METHODES

    /**
     * Fonction 1
     * Méthode pour afficher une liste de souhaits
     * @author Mathieu Vinot
     */
    public function affichageListe(Request $rq, Response $rs, array $args) {
        try {
            $vue = new VueGestionListe($this->container);
            $liste = $this->recupererListeLecture($args['token']);
            if ($liste != null) {
                $items = $this->recupererListeItem($liste['no']);
                $rs->getBody()->write($vue->render(3, $liste, $items));
            } else {
                $vue = new VueRender($this->container);
                $rs->getBody()->write($vue->render($vue->htmlErreur("Erreur dans le token de la liste...<br>")));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render($vue->htmlErreur("Erreur dans l'affichage de la liste...<br>".$e->getMessage()."<br>".$e->getTrace())));
        }
        return $rs;
    }

    private function recupererListeLecture($token){
        try {
            return Liste::query()->where('token_lecture', '=', $token)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    private function recupererListeItem($idliste){
        try {
            return Item::query()->where('liste_id', '=', $idliste)->get();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }


    /**
     * Fonction 6
     * Methode pour gérer la creation d'une liste
     *     GET: on obtient la page qui permet de créer une nouvelle liste
     *     POST: s'execute lorsque trois paramètres sont donnees par l'utilisateur , génère la creation de la liste de la BDD, puis dirige vers la page d'affichage de toutes les listes
     * @author Lucas Weiss
     */
    public function creerListe(Request $rq, Response $rs, array $args) {
        try {
            $vue = new VueGestionListe($this->container);
            // Dans la creation d'une liste, l'utilisateur doit rentrer 3 parametres, donc un post
            if (sizeof($args) == 3) {
                $this->creerListeInBDD($args); // On insere dans la BDD
                $rs = $rs->withRedirect($this->container->router->pathFor('affichageListesPublique')); // On redirige l'utilisateur vers la pages d'affichages de toutes les listes
            } else { // Si ce n'est pas le cas, la methode est un get
                $rs->getBody()->write($vue->render(1));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("Erreur dans la creation de la liste...<br>".$e->getMessage()."<br>".$e->getTrace()));
        }
        return $rs;
    }

    /**
     * Fonction 6
     * Methode privee qui permet de creer la liste au sein de la BDD
     * @author Lucas Weiss
     */
    private function creerListeInBDD(array $args) {
        $l = new Liste();
        $l->titre = filter_var($args['titre'], FILTER_SANITIZE_STRING);
        $l->description = filter_var($args['description'], FILTER_SANITIZE_STRING);
        $l->expiration = $args['expiration'];
        $l->public = 1;
        $l->token_lecture= bin2hex(random_bytes(32));
        $l->token_edition= bin2hex(random_bytes(32));
        $l->save();
    }

    /**
     * Fonction 21
     * Methode qui gere l'affichage de toutes les listes publique
     * @author Lucas Weiss
     */
    public function afficherListesPublique(Request $rq, Response $rs, array $args) {
        try {
            $vue = new VueGestionListe($this->container);
            $listes = $this->selectListePubliques();
            $rs->getBody()->write($vue->render(2, $listes));
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("Erreur dans l'affichage des listes publiques...<br>".$e->getMessage()."<br>".$e->getTrace()));
        }
        return $rs;
    }

    /**
     * Fonction 21
     * Methode privee qui retourne toutes les listes publique
     */
    private function selectListePubliques() {
        return Liste::query()->where('public', '=', 'true')->get();

    }

}