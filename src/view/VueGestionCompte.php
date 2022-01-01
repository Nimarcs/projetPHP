<?php

// NAMESPACE
namespace mywishlist\view;

// IMPORTS
use Slim\Container;

/**
 * Classe VueGestionCompte
 * Gere les vues concernant les comptes
 */
class VueGestionCompte
{

    // ATTRIBUTS
    private $container;

    // CONSTRUCTEUR
    public function __construct(Container $c)
    {
        $this->container = $c;
    }

    // METHODES

    private function htmlCreationCompte() {
        return <<<END
<h2>Creation d'un nouveau compte</h2>
<div class="formulaire">
    <p>Afin de créer un nouveau compte, veuillez remplir ce formulaire.</p>
    <form action="" method="post">
        <label for="NomUser">Login</label>
        <input type="text" name="login" required maxlength="30"><br>
        <label for="Mdp">Mot de passe</label>
        <input type="password" name="psw" required maxlength="20"><br>
        <button type="submit" class="btn submit">Créer son compte</button>
    </form>
</div>
END;

    }

    public function render($selecteur) {
        $content = "";
        switch ($selecteur) {
            case 1: {
                $content = $this->htmlCreationCompte();
                break;
            }
        }
        $vue = new VueRender($this->container);
        return $vue->render($content);
    }

}