<?php
declare(strict_types=1);

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

    /**
     * Fonctionnalite 17
     * Vue pour avoir le formulaire de creation de compte
     */
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

    /**
     * Fonctionnalite 18
     * affiche formulaire connection compte
     */
    private function htmlConnectionCompte() {
        return <<<END
<h2>Connection a un compte</h2>
<div class="formulaire">
    <form action="" method="post">
        <label for="NomUser">Login</label>
        <input type="text" name="login" required maxlength="30"><br>
        <label for="Mdp">Mot de passe</label>
        <input type="password" name="psw" required maxlength="20"><br>
        <button type="submit" class="btn submit">Connexion</button>
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
            case 2:
                $content = $this->htmlConnectionCompte();
                break;
        }
        $vue = new VueRender($this->container);
        return $vue->render($content);
    }

}