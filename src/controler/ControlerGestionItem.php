<?php

declare(strict_types=1);

// NAMESPACE
namespace mywishlist\controler;

// IMPORTS
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use mywishlist\modele\Item;
use mywishlist\modele\Liste;

use mywishlist\view\VueGestionItem;
use mywishlist\view\VueGestionListe;
use mywishlist\view\VueRender;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Container;

/**
 * Classe ControlerGestionListe,
 * Controleur sur la gestion des items
 */

class ControlerGestionItem{

    // ATTRIBUTS
    private $container;

    // CONSTRUCTEUR
    public function __construct(Container $container) {
        $this->container = $container;
    }

    //METHODES

    /**
     * Fonction 2
     * affichage d'un item
     * @author Mathieu Vinot
     */
    public function affichageItem(Request $rq, Response $rs, array $args): Response
    {
        try {
            //création de la vue
            $vue = new VueGestionItem($this->container);

            //récupération de l'item
            $edition = false;
            $token = $args['token'];
            $id = intval($args['id']);
            $item = $this->recupererItem($token,$id, false);

            //si l'item n'est pas trouvé, possiblement un token édition
            if ($item == null) {
                $item = $this->recupererItem($token,$id, true);
                $edition = true;
            }

            //l'item où l'item existe
            if ($item != null) {
                $rs->getBody()->write($vue->render(2, ['item' => $item, 'token' => $token, 'id' => $id, 'edition' => $edition]));

            //cas où l'item n'existe pas
            } else {
                $vue = new VueRender($this->container);
                $rs->getBody()->write($vue->render($vue->htmlErreur("Erreur dans l'id de l'item...<br>")));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render($vue->htmlErreur("Erreur dans l'affichage de l'item'...<br>" . $e->getMessage() . "<br>" . $e->getTrace())));
        }
        return $rs;
    }

    /**
     * Fonction 3
     * Methode pour gérer la reservation d'un item
     *     GET: on obtient la page qui permet de reserver un item
     *     POST: on reserve l'item et on affiche si on a reussi a faire la reservation
     * @author Marcus Richier
     */
    public function reserverItem(Request $rq, Response $rs, array $args) : Response {
        try {

            $vue = new VueGestionItem($this->container);

            //on recupere les parametre
            if ($rq->isPost()){
                $token = $rq->getParsedBody()['token'];
                $id = intval( $rq->getParsedBody()['id']);
            } else {
                $token= $args['token'];
                $id = intval( $args['id']);
            }

            // On recupere l'item
            $item = $this->recupererItem($token,$id, false);
            if ($item == null){
                $vue = new VueRender($this->container);
                $rs->getBody()->write($vue->render("Erreur: item non trouvé avec :<br> token = {$args['token']},<br> id = {$args['id']}"));
                return $rs;
            }

            //on fait l'action correspondante
            if ($rq->isPost()) {

                //on récupère le message
                $message = filter_var( $rq->getParsedBody()['message'] , FILTER_SANITIZE_STRING);

                //on nettoie le nom
                $reservateur = filter_var( $rq->getParsedBody()['reservateur'] , FILTER_SANITIZE_STRING);

                //si on a cocher le fait de memoriser, on fait le cookie
                if ($rq->getParsedBody()['memoriser']!=null) {
                    setcookie("lastPSEUDO", $reservateur, time() + 60 * 60, "/");
                }
                // On insere dans la BDD
                $this->reserverItemDansBDD($item, $reservateur, $message);

                //on redirige
                $rs = $rs->withRedirect($this->container->router->pathFor('reserverItem', ['id'=>$id, 'token' => $token])); // On redirige l'utilisateur vers la pages d'affichages de toutes les listes

            } else { // Si ce n'est pas le cas, la methode est un get
                $rs->getBody()->write($vue->render(3, ['token' => $token, 'id' => $id, 'reserverPar' => $item->reserverPar, 'nom' => $item->nom]));
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("Erreur dans la reservation de l'item...<br>"));

        }
        return $rs;
    }

    /**
     * Fonction 3
     * Methode privee qui permet de reserver l'item au sein de la BDD
     * @param Item $item item a reserver
     * @param string $nom nom de la personne qui reserve
     * @author Marcus RICHIER
     * @author Mathieu Vinot (Ajout du message)
     */
    private function reserverItemDansBDD(Item $item, string $nom, string $message) : bool {
        if (!isset($item)) return false;
        if ($item->reserverPar != null) return false;
        $item->message = $message;
        $item->reserverPar = $nom;
        $item->save();
        return true;
    }

    /**
     * Methode qui permet de recuperer un item a partir du token et de son numero
     * @param $token token de la liste
     * @param $id numero de l'item dans la liste
     * @param bool $edition permet de preciser si c'est le token d'edition qui est fourni, si precise a faux, le token de lecture est attendu
     * @return item correspondant ou null
     * @author Marcus Richier
     */
    private function recupererItem(string $token, int $id, bool $edition = true) : ?Item{
        try {
            //Choix du type de token
            $type_token = 'token_edition';
            if (!$edition){
                $type_token = 'token_lecture';
            }

            //requete
            $item = Item::query()
                ->join("liste", "liste_id", "=", "no")
                ->where('id', '=', $id)
                ->where($type_token, '=', $token)
                ->firstOrFail();

            //on revoi l'item trouve
            return $item;
        } catch (\Exception $e) {
            //s'il y a une erreur on renvoi null
            return null;
        }
    }

    /**
     * Fonction 8
     * Methode qui ajoute un nouvel item dans une liste precise
     * @author Lucas Weiss
     * @author Mathieu Vinot
     * @author Marcus Richier
     */
    public function ajouterNouvelItem(Request $rq, Response $rs, array $args): Response
    {
        try {
            $vue = new VueGestionItem($this->container);

            if ($rq->isPost()) {
                $token = filter_var( $rq->getParsedBody()['token'], FILTER_SANITIZE_STRING);
                $nom = filter_var( $rq->getParsedBody()['nom'], FILTER_SANITIZE_STRING);
                $description = filter_var( $rq->getParsedBody()['description'], FILTER_SANITIZE_STRING);
                $prix = filter_var( $rq->getParsedBody()['prix'], FILTER_SANITIZE_NUMBER_FLOAT);
                $typeEntree = $rq->getParsedBody()['typeEntree'] ;
                $imageChoisi = $rq->getParsedBody()['image'];
                if (isset($rq->getParsedBody()['url']))
                    $url = filter_var( $rq->getParsedBody()['url'], FILTER_SANITIZE_URL);
                else
                    $url = "";
            } else {
                $token = $args['token'];
            }

            if ($token == null) {
                $vue = new VueRender($this->container);
                $rs->getBody()->write($vue->render("Erreur : aucun token fourni"));
                return $rs;
            }

            //on recupere la liste
            $liste = $this->recupererListeAvecTokenCreation($token);
            if ($liste == null) {
                $vue = new VueRender($this->container);
                $rs->getBody()->write($vue->render("Erreur la liste n'existe pas"));
                return $rs;
            }

            if ($rq->isPost()) {

                //on ajoute la liste au arguments
                $args['liste'] = $liste;
                $args['nom'] = $nom;
                $args['description'] = $description;
                $args['prix'] = $prix;
                $args['url'] = $url;


                switch ($typeEntree ){
                    case'predef' :
                    {
                        //pas de fichier

                        //on cree l'item
                        $this->ajouterNouvelItemInBDD($args, $imageChoisi);
                        break;
                    }
                    case "url":
                    {
                        //on fourni une url
                        $urlImg = filter_var($rq->getParsedBody()['urlImg'], FILTER_SANITIZE_URL);
                        $this->ajouterNouvelItemInBDD($args, $urlImg);

                        break;
                    }
                    case "file" :
                    {
                        //on fourni un fichier

                        //on recupere le fichier uploads
                        $fichiers = $rq->getUploadedFiles();
                        $fichier = $fichiers['fichier'];
                        //on verifie s'il y a un fichier

                        if ($fichier == null || $fichier->getError() !== UPLOAD_ERR_OK) {
                            //pas d'image
                            $this->ajouterNouvelItemInBDD($args, 'no-image.png');
                        } else {

                            //s'il y a un fichier
                            $nomImg = 'temp';

                            //on cree l'item
                            $item = $this->ajouterNouvelItemInBDD($args, $nomImg);

                            //on recupere l'extension du fichier
                            $nomFichier = (explode(".", $fichier->getClientFilename()));
                            $extension = $nomFichier[array_key_last($nomFichier)];

                            //on met l'image au bon endroit
                            $nomImg = "custom" . DIRECTORY_SEPARATOR . str_replace(" ", "_" , $nom) . $item->id . "." . $extension;
                            $route = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $nomImg;
                            $fichier->moveTo($route);

                            //on redefini le bon nom de l'image
                            $item->img = $nomImg;
                            $item->save();
                        }
                        break;
                    }
                    case "vider":
                    {
                        //on creer un item sans image
                        $this->ajouterNouvelItemInBDD($args,  'no-image.png');
                        break;
                    }
                    default: {
                        throw new \Exception("Etat de programme interdit");
                    }
                }


                //on redirige
                $rs = $rs->withRedirect($this->container->router->pathFor('afficherListe', ['token' => $liste->token_edition]));
            } else {

                if ($liste != null) { // On teste si le token creation est correct
                    $rs->getBody()->write($vue->render(1, ['liste'=>$liste]));
                } else {
                    $vue = new VueRender($this->container);
                    $rs->getBody()->write($vue->render($vue->htmlErreur("Page 404")));
                }
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render($vue->htmlErreur("Erreur dans la creation de l'item...<br>".$e->getMessage()."<br>".$e->getTrace())));
        }
        return $rs;
    }

    /**
     * Fonction 8
     * On recupere la liste avec ce token d'edition
     * @author Lucas Weiss
     */
    private function recupererListeAvecTokenCreation(string $token) {
        try {
            return Liste::query()->where('token_edition', '=', $token)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Fonction 8
     * Methode pour creer un nouvel item
     * /!\ les variables doivent être nettoyé en amont !
     * @param array $args attributs de l'item
     * @param string $nameImage nom de l'image a ajouter
     * @author Lucas Weiss
     * @author Marcus Richier
     */
    private function ajouterNouvelItemInBDD(array $args, string $nameImage) : Item{
        $i = new Item();
        $i->nom = $args['nom'];
        $i->descr = $args['description'];
        $i->tarif = $args['prix'];
        $i->liste_id = $args['liste']->no;
        if ($args['url'] != "")
            $i->url = $args['url'];
        $i->img = $nameImage;
        $i->message = null;
        $i->reserverPar = null;
        $res = $i->save();
        if (!$res){
            throw new \Exception("Sauvegarde de l'item a échoué");
        }
        return $i;
    }

    /**
     * Fonction 9
     * La methode est utilisee lorsque que le createur de la liste veut modifier un item
     *
     * @author Fabrice Arnout
     */
    public function modifierItem(Request $rq, Response $rs, array $args):Response {
        try {
            $vue = new VueGestionItem($this->container);

            if ($rq->isPost()) {

                //on recupère l'item

                $item = $this->recupererItem($args['token'], intval($args['id']));

                if ($item->reserverPar == null) { // pas possible si l'item est reservé

                    //on filtre
                    $args['titre'] = filter_var($args['titre'], FILTER_SANITIZE_STRING);
                    $args['description'] = filter_var($args['description'], FILTER_SANITIZE_STRING);
                    $args['prix'] = filter_var($args['prix'], FILTER_SANITIZE_STRING);

                    //on recupere l'image

                    $typeEntree = $rq->getParsedBody()['typeEntree'];
                    $imageChoisi = $rq->getParsedBody()['image'];


                    switch ($typeEntree) {
                        case'predef' :
                        {
                            //on a choisi depuis la liste
                            $newImg = $imageChoisi;

                            $this->supprimerImage($item);

                            break;
                        }
                        case "url":
                        {
                            //on fourni une url
                            $newImg = filter_var($rq->getParsedBody()['urlImg'], FILTER_SANITIZE_URL);

                            $this->supprimerImage($item);

                            break;
                        }
                        case "rien":
                        {
                            // on demande de ne pas changer
                            $newImg = $item->img;
                            break;
                        }
                        case "vider":
                        {
                            $newImg = 'no-image.png';
                            $this->supprimerImage($item);
                            break;
                        }
                        default:
                        {
                            throw new \Exception("Etat de programme interdit");
                        }
                    }

                    $args['image'] = $newImg;

                    //plus l'image


                    $this->modifierItemInBDD($item, $args);
                }
                $rs = $rs->withRedirect($this->container->router->pathFor('afficherItem', ['token'=>$item->liste->token_edition , 'id'=>$item->id] ));
            } else {

                $item = $this->recupererItem($args['token'], intval($args['id']));
                $tab = array("item"=>$item);
                $rs->getBody()->write($vue->render(4, $tab));
            }


        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("Erreur dans la modification de l'item...<br>".$e->getMessage()."<br>".$e->getTrace()));

        }
        return $rs;
    }



    /**
     * Fonction 9
     * Methode prive qui permet de modifier un item dans la bdd
     * /!\ doit etre filtrée en amont
     * @author Fabrice Arnout
     */

    private function modifierItemInBDD(Item $i, array $args) :void{
        $i['nom'] = $args['titre'];
        $i['descr'] = $args['description'];
        $i['img'] = $args['image'];
        $i['tarif'] = $args['prix'];
        $i->save();
    }


    /**
     * Fonctionnalité 13
     * Methode qui permet de supprimer l'image d'un item
     * la supprime en local et de la bdd
     *
     * /!\ On ne supprime que les images customisé qui ont été upload
     * les image prédéfinie ne peuvent être supprimée
     *
     * @param Item $item item dont on veut supprimer l'image
     * @return bool revoie vrai si et seulement si la suppression a été effectué avec succès
     */
    private function supprimerImage(Item $item): bool {
        $route = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $item->img ;
        if (file_exists($route)){
            $res = unlink($route);
            if ($res) {
                $item->img = 'no-image.png';
                $res = $item->save();
            }
            return $res;
        } else {
            $item->img = 'no-image.png';
            return $item->save();
        }
    }


}