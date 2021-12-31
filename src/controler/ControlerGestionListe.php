<?php

// NAMESPACE
namespace mywishlist\controler;

// IMPORTS
use mywishlist\view\VueGestionListe;
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

    public function creerListe(Request $rq, Response $rs, $args) {
        $vue = new VueGestionListe($this->container);
        $rs->getBody()->write($vue->render(1));
        return $rs;
    }


}