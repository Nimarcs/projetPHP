<?php
declare(strict_types=1);

// NAMESPACE
namespace mywishlist\controler;

// IMPORTS
use Carbon\Exceptions\Exception;
use Illuminate\Database\Eloquent\Collection;
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
     * Méthode pour afficher une liste de souhaits,
     * Affiche une liste de lecture ou d'edition
     *
     * @author Mathieu Vinot
     * @author Lucas Weiss
     */
    public function affichageListe(Request $rq, Response $rs, array $args): Response {
        try {
            $vue = new VueGestionListe($this->container);

            //on cherche la liste
            $edition = false;
            $liste = $this->recupererListeLecture($args['token']);
            if ($liste == null) {//si elle n'est pas trouvé
                $liste = $this->recupererListeEdition($args['token']);
                $edition = true;
            }

            if ($liste != null) {

                $rs->getBody()->write($vue->render(3, $liste, $edition));

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

    /**
     * Fonction 1.1
     * Permet de récupérer la liste
     *
     * @param $token , token d'identification de la liste
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null
     *
     * @author Mathieu Vinot
     */
    private function recupererListeLecture(string $token): ?Liste{
        try {
            return Liste::query()->where('token_lecture', '=', $token)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Fonction 1.2
     * Permet de récupérer la liste
     *
     * @param $token , token d'edtion  de la liste
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null
     *
     * @author Renard Guillaume
     */
    private function recupererListeEdition(string $token) :?Liste{
        try {
            return Liste::query()->where('token_edition', '=', $token)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * fonction 1.3
     * Permet de récupérer la liste des items de la liste
     *
     * @param $idliste , id de la liste
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|null
     *
     * @author Mathieu Vinot
     */
    private function recupererListeItem(int $idliste): ?Collection{
        try {
            return Item::query()->where('liste_id', '=', $idliste)->get();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Fonction afficher listes personnels
     * @param $rq
     * @param $rs
     * @param $args
     * @return Response
     * @author Mathieu Vinot
     */
    public function AffichageListePerso(Request $rq,Response $rs,array $args): Response{
        try {
            $vue = new VueGestionListe($this->container);
            $listes = $this->selectListePerso();
            $rs->getBody()->write($vue->render(7, $listes));
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("Erreur dans l'affichage des listes personnelles...<br>".$e->getMessage()."<br>".$e->getTrace()));
        }
        return $rs;
    }

    /**
     * Fonction qui permet la récupération de ses listes personneles
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @author Mathieu Vinot
     */
    private function selectListePerso(){
        //on récupère à l'aide du login contenue dans notre variable session, cela permet de sécuriser
        return Liste::query()->where('login', '=', $_SESSION["login"])->get();

    }

    /**
     * Fonction 6
     * Methode pour gérer la creation d'une liste
     *     GET: on obtient la page qui permet de créer une nouvelle liste
     *     POST: s'execute lorsque trois paramètres sont donnees par l'utilisateur, génère la creation de la liste de la BDD, puis dirige vers la page d'affichage de toutes les listes
     * @author Lucas Weiss
     * @author Marcus Richier (cookie listeCree)
     */
    public function creerListe(Request $rq, Response $rs, array $args):Response {
        try {
            $vue = new VueGestionListe($this->container);
            // Dans la creation d'une liste, l'utilisateur doit rentrer 3 parametres, donc un post
            if (sizeof($args) == 3) {
                $no = $this->creerListeInBDD($args); // On insere dans la BDD
                if (isset($_COOKIE['listeCree'])) $a = unserialize( $_COOKIE['listeCree']);
                else $a = [];
                $a[] = $no;
                setcookie("listeCree", serialize($a), 0, "/" );
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
     * @author Mathieu Vinot (ajout du login pour suivre l'évolution de notre bdd)
     * @author Marcus RICHIER (creation de liste sans etre connecte)
     */
    private function creerListeInBDD(array $args):int {
        $l = new Liste();
        $l->titre = filter_var($args['titre'], FILTER_SANITIZE_STRING);

        //si la personne est connecte c'est son login, sinon c'est "anonyme"
        if (isset($_SESSION['login'])) $l->login = $_SESSION['login'];
        else $l->login = "anonyme";

        $l->description = filter_var($args['description'], FILTER_SANITIZE_STRING);
        $l->expiration = $args['expiration'];
        $l->public = 1;
        $l->token_lecture= bin2hex(random_bytes(32));
        $l->token_edition= bin2hex(random_bytes(32));
        $l->save();
        return $l->no;
    }

    /**
     * Fonction 7
     * La méthode est utilisée lorsque l'on veut modifier les données de la liste
     * on a donc un get et un post
     * le get affiche ce que l'utilsateur a mis
     * le post renvoie les valeurs qu'il a changé
     * @author Guillaume Renard
     */
    public function modifierListe(Request $rq, Response $rs, array $args):Response {
        try {
            $vue = new VueGestionListe($this->container);


            if (sizeof($args) == 5) {
                //on est dans un post
                //on prend les informations de la liste de la page avec la méthode vue dans Fonction 1
                $liste = $this->recupererListeEdition($args['token_edition']);

                $this->modifierListeInBDD($liste, $args);
                $rs = $rs->withRedirect($this->container->router->pathFor('affichageListesPublique'));
            } else {
                //on est dans le get

                //on prend les informations de la liste de la page avec la méthode vue dans Fonction 1
                $liste = $this->recupererListeEdition($args['token_edition']);
                $rs->getBody()->write($vue->render(5, $liste));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("Erreur dans la modification de la liste...<br>".$e->getMessage()."<br>".$e->getTrace()));
        }
        return $rs;
    }

    /**
     * Fonction 7
     * Methode privee qui permet de modifier la liste au sein de la BDD grâce à la méthode save()
     * @author Guillaume Renard
     */
    private function modifierListeInBDD(Liste $l, array $args):void {

        $l['titre'] = filter_var($args['titre'], FILTER_SANITIZE_STRING);
        $l['description']= filter_var($args['description'], FILTER_SANITIZE_STRING);
        $l['expiration'] = $args['expiration'];
        $l['public'] = $args['public'];
        $l->save();
    }

    /**
     * Fonction ?
     * Methode privee qui permet de suprimer la liste au sein de la BDD grâce à la méthode save()
     * @author Guillaume Renard
     */
    public function supprimerListe(Request $rq, Response $rs, array $args): Response
    {
        try {
            $vue = new VueGestionListe($this->container);
            if (sizeof($args) == 2) {

                if($args["supr"]==0){

                    Liste::query()->where('token_edition', '=', $args['token_edition'])->delete();
                }

                $rs = $rs->withRedirect($this->container->router->pathFor('affichageListesPublique'));
            } else {
                //on prend les informations de la liste de la page avec la méthode vue dans Fonction 1
                $liste = $this->recupererListeEdition($args['token_edition']);
                $rs->getBody()->write($vue->render(6, $liste));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("Erreur dans la modification de la liste...<br>" . $e->getMessage() . "<br>" . $e->getTrace()));
        }
        return $rs;
    }



        /**
     * Fonction 21
     * Methode qui gere l'affichage de toutes les listes publique
     * @author Lucas Weiss
     */
    public function afficherListesPublique(Request $rq, Response $rs, array $args): Response {
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

    /**
     * Fonction 26
     * Métjo
     */
    public function afficherListesCreateur(Request $rq, Response $rs, array $args): Response{
        try {
            $vue = new VueGestionListe($this->container);
            $liste = $this->recupererListesLogin($args['login']);
            if ($liste != null) {
                $rs->getBody()->write($vue->render(6, $liste));
            } else {
                $vue = new VueRender($this->container);
                $rs->getBody()->write($vue->render($vue->htmlErreur("Erreur dans le login...<br>")));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("Erreur dans l'affichage des listes publiques...<br>".$e->getMessage()."<br>".$e->getTrace()));
        }
        return $rs;
    }

    /**
     * Fonction 26.2
     * Permet de récupérer les listes d'une personne
     *
     * @param $login, login du createur
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|null
     *
     * @author Mathieu Vinot
     */
    private function recupererListesLogin(string $login){
        try {
            return Liste::query()->where('login', '=', $login)->get();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

}