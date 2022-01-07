<?php

declare(strict_types=1);

// NAMESPACE
namespace mywishlist\controler;

// IMPORTS
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    /***
     * Fonction 8
     * Methode qui ajoute un nouvel item dans une liste precise
     * @author Lucas Weiss
     */
    public function ajouterNouvelItem(Request $rq, Response $rs, array $args) {
        try {
            $liste = $this->recupererListeAvecTokenCreation($args['token']);
            if ($liste!=null) { // On teste si le token creation est correct
                $vue = new VueGestionItem($this->container);
                if (sizeof($args) == 4) {
                    $rs = $rs->withRedirect($this->container->router->pathFor('liste/' . $args['token_lecture']));
                } else {
                    $rs->getBody()->write($vue->render(1, $liste));
                }
            } else {
                $vue = new VueRender($this->container);
                $rs->getBody()->write($vue->render($vue->htmlErreur("Page 404")));
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


}