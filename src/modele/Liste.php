<?php
declare(strict_types=1);

// NAMESPACE
namespace mywishlist\modele;

use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Classe Liste
 * Représente une liste au sein de la base de données
 * Hérite de la classe Modele du module Eloquent
 */
class Liste extends \Illuminate\Database\Eloquent\Model
{

    // ATTRIBUTS

    public $timestamps = false;
    protected $table = 'liste';
    protected $primaryKey = 'no';
    

    // CONSTRUCTEUR

    public function items()
    {
        return $this->hasMany('\mywishlist\modele\Item', 'liste_id');
    }

}
