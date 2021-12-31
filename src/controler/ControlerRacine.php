<?php

// NAMESPACE
namespace mywishlist\controler;

// IMPORTS
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Container;
use \mywishlist\view\VueRender;

/**
 * Classe ControlerRacine,
 * Controlleur de la page d'accueil a la racine du projet
 */
class ControlerRacine
{

    // ATTRIBUTS
    private $container;

    // CONSTRUCTEUR
    public function __construct(Container $container) {
        $this->container = $container;
    }

    // FONCTIONS

    /**
     * Fonction 0, controleur de la route racine de l'accueil
     */
    public function racine(Request $rq, Response $rs, array $args) {
        $vue = new VueRender($this->container);
        $rs->getBody()->write($vue->render($vue->affichageAccueil()));
        return $rs;
    }

}
