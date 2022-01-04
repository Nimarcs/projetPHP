<?php

declare(strict_types=1);

// NAMESPACE
namespace mywishlist\view;

// IMPORTS
use mywishlist\modele\Item;
use Slim\Container;

/**
 * Classe VueGestionItem
 * Gere les vues concernant les vues sur les fonctionnalités concernant les items
 */
class VueGestionItem
{
    // ATTRIBUTS

    private $container;

    public function __construct(Container $c){
        $this->container = c;
    }


}