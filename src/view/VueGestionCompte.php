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
     * @author Lucas Weiss (création) + Guillaume Renard (modification)
     */
    private function htmlCreationCompte() : string {
        //si il a deja utilise un pseudo on precomplete le champ
        if (isset($_COOKIE["lastPSEUDO"])) $lastPSEUDO = $_COOKIE["lastPSEUDO"];
        else $lastPSEUDO = "";

        return <<<END
            <div class="block-heading">
                        <h2 class="text-info">Creation d'un nouveau compte</h2>
            </div><div class="formulaire">
    <p>Afin de créer un nouveau compte, veuillez remplir ce formulaire.</p>
    <form action="" method="post">
        <label for="NomUser">Login</label>
        <input type="text" name="login" required maxlength="30" value="$lastPSEUDO"><br>
        
        <label for="Mdp">Mot de passe</label>
        <input type="password" name="psw" required maxlength="20"><br>
        
         <label for="Email">Email</label>
        <input type="email" name="email" required maxlength="30"><br>
        
         <label for="DateNais">Date anniversaire</label>
        <input type="date" name="dateN" required maxlength="30"><br>
        
          <label for="numDep">Numéro de votre département</label>
        <input type="text" name="numDep" required maxlength="6"><br>
        
          <label for="ville">Nom de votre ville</label>
        <input type="text" name="ville" required maxlength="30"><br>
        
          <label for="adresse">Adresse</label>
        <input type="text" name="adresse" required maxlength="80"><br>
        
         
        <input type="radio"  required>
        <label for="condition_gene">J'ai lu et j'accepte les <a href=''> conditions générales d'utilisation </a> et <a href=''>la politique de confidentialité </a></label><br>
        
        <button type="submit" class="btn submit">Créer son compte</button>
    </form>
</div>
END;

    }

    /**
     * Fonctionnalite 18
     * affiche formulaire connection compte
     */
    private function htmlConnectionCompte() : string {
        //si il a deja utilise un pseudo on precomplete le champ
        if (isset($_COOKIE["lastPSEUDO"])) $lastPSEUDO = $_COOKIE["lastPSEUDO"];
        else $lastPSEUDO = "";

        return <<<END
            <div class="block-heading">
                        <h2 class="text-info">Connection à un compte</h2>
            </div><div class="formulaire">
            <form action="" method="post">
        <div class="mb-3"><label class="form-label" for="NomUser">Login</label><input class="form-control item" type="text" name="login" required maxlength="30" value="$lastPSEUDO"></div>
        <div class="mb-3"><label class="form-label" for="Mdp">Mot de passe</label><input class="form-control" type="password" name="psw" required maxlength="20"></div>
        <div class="mb-3">
            <div class="form-check"><input class="form-check-input" type="checkbox" id="checkbox"><label class="form-check-label" for="checkbox">Remember me</label></div>
        </div><button class="btn btn-primary" type="submit">Connexion</button>
    </form><br>
    <p>Pas de compte ? </p> <a href={$this->container->router->pathFor("creationCompte")}><button class="btn btn-primary">S'inscrire</button></a>
</div>
END;

    }

    public function render($selecteur, $args1 = null) : string{
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