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
     * @author Marcus (fusion avec affichage modification)
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
                $rs->getBody()->write($vue->render($vue->htmlErreur("<br><br><div class='block-heading'><h2>Erreur dans le token de la liste...</h2></div><br>")));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render($vue->htmlErreur("<br><br><div class='block-heading'><h2>Erreur dans l'affichage de la liste...</h2></div><br>".$e->getMessage()."<br>".$e->getTrace())));
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
            if (isset($_SESSION['login'])) {
                $listes = $this->recupererListesLogin($_SESSION['login']);
                $rs->getBody()->write($vue->render(7, $listes));
            } else throw new \Exception("<div class='block-heading'>Vous devez être connecté !</div>");
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("<br><br><div class='block-heading'><h2>Erreur dans l'affichage des listes personnelles...</h2></div><br>".$e->getMessage()."<br>".$e->getTrace()));
        }
        return $rs;
    }

    /**
     * Fonction 5
     * @author Mathieu Vinot
     */

    public function posterUnMessage($rq, $rs, array $args):Response{
        try {
            $vue = new VueGestionListe($this->container);

            if ($rq->isPost()) {
                //on est dans un post
                //on prend les informations de la liste de la page avec la méthode vue dans Fonction 1
                $liste = $this->recupererListeLecture($args['token']);

                $this->messageListeInBDD($liste, $args);
                $rs = $rs->withRedirect($this->container->router->pathFor('afficherListe', ['token'=>$args['token']]));
            } else {
                //on est dans le get

                //on prend les informations de la liste de la page avec la méthode vue dans Fonction 1
                $liste = $this->recupererListeLecture($args['token']);
                $rs->getBody()->write($vue->render(2, $liste));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("<br><br><div class='block-heading'><h2>Erreur dans l'envoie de message dans la liste...</h2></div><br>".$e->getMessage()."<br>".$e->getTrace()));
        }
        return $rs;
    }


    /**
     * Fonction 5
     * Methode privee qui permet d'ajouter un message dans la liste au sein de la BDD grâce à la méthode save()
     * @author Mathieu Vinot
     */

    private function messageListeInBDD(Liste $l, array $args):void {
        $newMessage=$args['login'].' : '.filter_var($args['message'], FILTER_SANITIZE_STRING);
        $l['messages'] .= "<p>$newMessage</p><br>";
        $l->save();
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
            // Dans la creation d'une liste, l'utilisateur doit rentrer 3 parametres
            if ($rq->isPost() && sizeof($args) == 3) {

                // On insere dans la BDD
                $liste = $this->creerListeInBDD($args);
                $no = $liste->no;

                //on regarde si on avait deja un cookie de sauvegrade le liste créé
                if (isset($_COOKIE['listeCree'])) $a = unserialize( $_COOKIE['listeCree']);
                else $a = ['exp'=>(strtotime($liste->expiration) + 60*60*2)];

                //on stocke dans le cookie un tableau avec les no des liste et la date d'expiration la plus vielle
                //on ajoute le no
                $a[] = $no;
                //on change la valeur d'expiration si necessaire
                if ($a['exp'] <(strtotime($liste->expiration) + 60*60*2) )
                    $a['exp'] = (strtotime($liste->expiration) + 60*60*2);

                //on defini le cookie
                setcookie("listeCree", serialize($a), $a['exp'] , "/" );

                //on redirige
                $rs = $rs->withRedirect($this->container->router->pathFor('afficherListe', ['token'=>$liste['token_edition']])); // On redirige l'utilisateur vers la pages d'affichages de toutes les listes
            } else { // Si ce n'est pas le cas, la methode est un get
                $rs->getBody()->write($vue->render(1));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("<br><br><div class='block-heading'><h2>Erreur dans la creation de la liste...</h2></div><br>".$e->getMessage()."<br>".$e->getTrace()));
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
    private function creerListeInBDD(array $args):?Liste {
        $l = new Liste();
        $l->titre = filter_var($args['titre'], FILTER_SANITIZE_STRING);

        //si la personne est connecte c'est son login, sinon c'est "anonyme"
        if (isset($_SESSION['login'])) $l->login = ",".$_SESSION['login'].",";
        else $l->login = "anonyme";

        $l->description = filter_var($args['description'], FILTER_SANITIZE_STRING);
        $l->expiration = $args['expiration'];
        $l->public = 1;
        $l->token_lecture= bin2hex(random_bytes(32));
        $l->token_edition= bin2hex(random_bytes(32));
        $l->save();
        return $l;
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


            if ($rq->isPost() && sizeof($args) == 5) {
                //on est dans un post
                //on prend les informations de la liste de la page avec la méthode vue dans Fonction 1
                $liste = $this->recupererListeEdition($args['token_edition']);

                $this->modifierListeInBDD($liste, $args);
                $rs = $rs->withRedirect($this->container->router->pathFor('afficherListe', ['token'=>$liste['token_edition']]));
            } else {
                //on est dans le get

                //on prend les informations de la liste de la page avec la méthode vue dans Fonction 1
                $liste = $this->recupererListeEdition($args['token_edition']);
                $rs->getBody()->write($vue->render(5, $liste));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("<br><br><div class='block-heading'><h2>Erreur dans la modification de la liste...</h2></div><br>".$e->getMessage()."<br>".$e->getTrace()));
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
                    $liste = $this->recupererListeEdition($args['token_edition']);
                    Item::query()->where('liste_id', '=', $liste['no'])->delete();
                    Liste::query()->where('token_edition', '=', $args['token_edition'])->delete();
                }

                $rs = $rs->withRedirect($this->container->router->pathFor('accueil'));
            } else {
                //on prend les informations de la liste de la page avec la méthode vue dans Fonction 1
                $liste = $this->recupererListeEdition($args['token_edition']);
                $rs->getBody()->write($vue->render(6, $liste));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("<br><br><div class='block-heading'><h2>Erreur dans la modification de la liste...</h2></div><br>" . $e->getMessage() . "<br>" . $e->getTrace()));
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
            $rs->getBody()->write($vue->render("<br><br><div class='block-heading'><h2>Erreur dans l'affichage des listes publiques...</h2></div><br>".$e->getMessage()."<br>".$e->getTrace()));
        }
        return $rs;
    }

    /**
     * Fonction 21
     * Methode privee qui retourne toutes les listes publique qui ne sont pas expirée
     */
    private function selectListePubliques() {
        return Liste::query()
            ->where('public', '=', 'true')
            ->whereDate('expiration', '>' , date("Y-m-d"))
            ->orderBy('expiration')
            ->get();
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
            return Liste::query()->where('login', 'like', "%,".$login.",%")->get();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Fonction 28
     * permet d'ajouter un proprietaire a une liste
     */
    public function ajouterProprietaire(Request $rq, Response $rs, array $args): Response{
        try {

            // la requette doit etre un post
            if (!$rq->isPost()) throw new \Exception("La requête n'est pas du bon type");
            if (!isset($_SESSION['login'])) throw new \Exception("L'utilisateur doit être connecté");

            //on recupere la liste et le proprietaire
            $token = $rq->getParsedBody()['token'];
            $liste = $this->recupererListeEdition($token);
            if ($liste == null) throw new \Exception("La liste n'existe pas");

            //on modifie la liste
            $this->ajouterProprioDansBDD($liste, $_SESSION['login']);

            //on redirige
            $rs = $rs->withRedirect($this->container->router->pathFor('accueil')); // On redirige l'utilisateur vers la pages d'affichages de toutes les listes

        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("<br><br><div class='block-heading'><h2>Erreur dans la creation de la liste...</h2></div><br>" . $e->getMessage()));
        }
        return $rs;
    }

    private function ajouterProprioDansBDD(Liste $liste, string $identifiant): void{
        if($liste->login == "anonyme")
            $liste->login = ",".$identifiant.",";

        if (!str_contains($liste->login, ",".$identifiant.",")){
            $liste->login .= $identifiant.",";
        }
        $liste->save();
    }

}