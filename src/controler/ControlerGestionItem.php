<?php

declare(strict_types=1);

// NAMESPACE
namespace mywishlist\controler;

// IMPORTS
use mywishlist\modele\Liste;
use mywishlist\view\VueGestionItem;
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



}