<?php

declare(strict_types=1);

// NAMESPACE
namespace mywishlist\controler;

// IMPORTS
use Illuminate\Database\Eloquent\ModelNotFoundException;
use mywishlist\modele\Item;
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

    /***
     * Fonction 8
     * Methode qui ajoute un nouvel item dans une liste precise
     * @author Lucas Weiss
     */
    public function ajouterNouvelItem(Request $rq, Response $rs, array $args)
    {
        try {
            $vue = new VueGestionItem($this->container);
            if (sizeof($args) == 4) {
                if ($args['fichier'] != null) {
                    $nameImg = null;
                    if ($_FILES['fichier']['error'] > 0) {
                        $erreur = "Erreur lors de l'envoi de l'image.";
                        echo $erreur;
                    } else {
                        move_uploaded_file($_FILES['fichier']['name'], __DIR__ . '/img');
                        $nameImg = $_FILES['fichier']['name'];
                    }
                    $this->ajouterNouvelItemInBDD($args, $nameImg);
                }
                $valListe = Liste::query()->where('token_ecriture', '=', $args['token']);
                $rs = $rs->withRedirect($this->container->router->pathFor('liste/' . $valListe['token_lecture']));
            } else {
                $liste = $this->recupererListeAvecTokenCreation($args['token']);
                if ($liste != null) { // On teste si le token creation est correct
                    $rs->getBody()->write($vue->render(1, $liste));
                } else {
                    $vue = new VueRender($this->container);
                    $rs->getBody()->write($vue->render($vue->htmlErreur("Page 404")));
                }
            }
        } catch (\Exception $e) {
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("Erreur dans la creation de l'item...<br>".$e->getMessage()."<br>".$e->getTrace()));
        }
        return $rs;
    }

    /**
     * Fonction 8
     * On recupere la liste avec ce token d'edition
     * @author Lucas Weiss
     */
    private function recupererListeAvecTokenCreation($token) {
        try {
            return Liste::query()->where('token_edition', '=', $token)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Fonction 8
     * Methode pour creer un nouvel item
     * @author Lucas Weiss
     */
    private function ajouterNouvelItemInBDD(array $args, string $nameImage) {
        $i = new Item();
        $i->nom = filter_var($args['nom'], FILTER_SANITIZE_STRING);
        $i->descr = filter_var($args['description'], FILTER_SANITIZE_STRING);
        $i->tarif = filter_var($args['prix'], FILTER_SANITIZE_NUMBER_FLOAT);
        if ($nameImage!=null) {
            $i->img = $nameImage;
        }
        $i->save();
    }

    /**
     * Fonction 9
     * Modifier un item, juste changer ses attributs sans changer la liste
     */

    private function modifierItemBDD(array $args){
        $i = Item::query()->where('id','=',$args['id']);
        $i->nom = filter_var($args['nom'], FILTER_SANITIZE_STRING);
        $i->description = filter_var($args['description'], FILTER_SANITIZE_STRING);
        $i->tarif = filter_var($args['prix'], FILTER_SANITIZE_NUMBER_FLOAT);
        $i->image = $args['image'];
        $i->save();
    }

    /**
     * Fonction 13
     *
     */
    /*
    private function supprimerImageItem(Request $rq, Response $rs, array $args){
        try{

        }catch (\Exception $e){
            $vue = new VueRender($this->container);
            $rs->getBody()->write($vue->render("Erreur dans la suppression de l'image de l'item...<br>".$e->getMessage()."<br>".$e->getTrace()));
        }
    }
    */



}