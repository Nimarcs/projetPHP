<?php
declare(strict_types=1);

// NAMESPACE
namespace mywishlist\modele;

/**
 * Classe Compte
 * Représente un compte au sein de la base de données
 * Hérite de la classe Modele du module Eloquent
 */
class Compte extends \Illuminate\Database\Eloquent\Model
{

    // ATTRIBUTS

    public $timestamps = false;
    protected $table = 'compte';
    protected $primaryKey = 'login';

    // CONSTRUCTEUR

    public function listes()
    {
        return $this->hasMany('\mywishlist\modele\Liste', 'user_id');
    }

}
