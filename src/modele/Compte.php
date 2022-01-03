<?php

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
    protected $primaryKey = 'user_id';

    // CONSTRUCTEUR

    public function listes()
    {
        return $this->hasMany('\mywishlist\modele\Liste', 'user_id');
    }

}