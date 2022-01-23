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
        <input type="text" name="login" required maxlength="50" value="$lastPSEUDO"><br>
        
        <label for="Mdp">Mot de passe</label>
        <input type="password" name="psw" required maxlength="20"><br>
        
         <label for="Email">Email</label>
        <input type="email" name="email" required maxlength="254"><br>
        
         <label for="DateNais">Date anniversaire</label>
        <input type="date" name="dateN" required maxlength="10"><br>
        
          <label for="numDep">Numéro de votre département</label>
        <input type="text" name="numDep" required maxlength="6"><br>
        
          <label for="ville">Nom de votre ville</label>
        <input type="text" name="ville" required maxlength="254"><br>
        
          <label for="adresse">Adresse</label>
        <input type="text" name="adresse" required maxlength="254"><br>
        
         
        <input type="radio"  required>
        <label for="condition_gene">J'ai lu et j'accepte les <a href=''> conditions générales d'utilisation </a> et <a href=''>la politique de confidentialité </a></label><br>
        
        <button type="submit" class="btn btn-primary">Créer son compte</button>
    </form>
</div>
END;

    }

    /**
     * Fonctionnalite 18
     * affiche formulaire connection compte
     * @author Lucas Weiss
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
        <div class="mb-3"><label class="form-label" for="NomUser">Login</label><input class="form-control item" type="text" name="login" required maxlength="50" value="$lastPSEUDO"></div>
        <div class="mb-3"><label class="form-label" for="Mdp">Mot de passe</label><input class="form-control" type="password" name="psw" required maxlength="20"></div>
        <div class="mb-3">
            <div class="form-check"><input class="form-check-input" type="checkbox" id="checkbox"><label class="form-check-label" for="checkbox">Remember me</label></div>
        </div><button class="btn btn-primary" type="submit">Connexion</button>
    </form><br>
    <p>Pas de compte ? </p> <a href={$this->container->router->pathFor("creationCompte")}><button class="btn btn-primary">S'inscrire</button></a>
</div>
END;

    }

    /**
     *Fonctionnalité 19
     * affiche les champs pour modifier les informations du compte
     * @author Guillaume Renard
     * @return string
    */
    private function htmlModificationCompte($compte): string{
        $html= <<<END

<div class="block-heading">
    <h2 class="text-info">Modification des informations du compte</h2>
</div>
<p>Modifiez vos informations en remplissant les formulaires</p>
<div class="formulaire">
    
        <form action="{$this->container->router->pathFor('modificationMotDePasse')}" method="get"> 
            <button type="submit" class="btn btn-primary">Modifier mot de passe</button><br>
        </form>
        <form action="" method="post">
         <label for="Email"  class="form-label">Email</label>
        <input type="email" name="email" value="${compte['email']}" required maxlength="254"  ><br>
        
         <label for="DateNais"  class="form-label">Date anniversaire</label>
        <input type="date" name="dateN" value="${compte['date_naissance']}"required maxlength="10" ><br>
        
          <label for="numDep"  class="form-label">Numéro de votre département</label>
        <input type="text" name="numDep" value="${compte['num_dep']}" required maxlength="6" ><br>
        
          <label for="ville"  class="form-label">Nom de votre ville</label>
        <input type="text" name="ville" value="${compte['ville']}" required maxlength="254" ><br>
        
          <label for="adresse"  class="form-label">Adresse</label>
        <input type="text" name="adresse" value="${compte['adresse']}" required maxlength="254" ><br>
        
        
        <button type="submit" class="btn btn-primary" >Sauvegarder les modifications</button>
    </form>
      <form action="{$this->container->router->pathFor('supprimerCompte')}" method="get"> 
            <button type="submit" class="btn btn-primary">Supprimer votre compte</button><br>
        </form>
</div>

END;
        return $html;

    }

    /**
     * Fonction 19 suite
     * affiche les champs pour modifier le mot de passe d'un compte
     *
     * @author Guillaume Renard
     * @return string
     */
    private function htmlModificationMotDePasse(){
        $html = <<<END
 <div class="block-heading">
                        <h2 class="text-info">Changer votre mot de passe</h2>
            </div><div class="formulaire">
            
            <form action="{$this->container->router->pathFor('modificationCompte')}" method="get"> 
            <button type="submit" class="btn btn-primary">Retournez en arrière</button><br>
        </form>
            <form action="" method="post">
                <div class="mb-3"><label class="form-label" for="ancien Mdp">Mettez votre ancien mot de passe</label><input class="form-control" type="password" name="anc_Mdp" required maxlength="20"></div>
                <div class="mb-3"><label class="form-label" for="nouveau Mdp">Mettez votre nouveau mot de passe</label><input class="form-control" type="password" name="new_Mdp" required maxlength="20" minlength="4"></div>
                <div class="mb-3"><label class="form-label" for="confimrer nouveau Mdp">confirmez votre mot de passe</label><input class="form-control" type="password" name="conf_Mdp" required maxlength="20" minlength="4"></div>
            

            <button type="submit" class="btn btn-primary">Confirmez modification</button><br>
        </form>

</div>
END;
        return $html;
    }


    /**
     * Fonction 27
     * Affichage pour supprimer un compte ou non
     * @author Guillaume Renard
     */
    private function afficherSupressionCompte():string{
        $html= <<<END
            <div class="block-heading">
                        <h2 class="text-info">Supression d'un compte</h2>
            </div>
            <div class="formulaire">
            <form action="" method="post">
            
                <label for="suppression compte" class="form-label">Etes-vous vraiment sûre de vouloir suprimer votre compte, </label><br> 
                <label for="oui" class="form-label">Oui</label>
                <input type="radio" value="0" class="form-control" name="supr" placeholder="" required >
                 <label for="non" class="form-label">Non</label>
                <input type="radio" value="1" class="form-control" name="supr" placeholder="" required checked><br> 
                
                <button type="submit" class="btn btn-primary">
                   Valider
                </button>
            </form>
        </div>
END;
        return $html;
    }

    /**
     * @param $selecteur
     * @param null $args1
     * @return string
     */
    public function render($selecteur, $args1 = null) : string{
        $content = "";
        switch ($selecteur) {
            case 1: {
                $content = $this->htmlCreationCompte();
                break;
            }
            case 2:
            {
                $content = $this->htmlConnectionCompte();
                break;
            }
            case 3 :  {
                $content = $this->htmlModificationCompte($args1);
                break;
            }
            case 4 :  {
                $content = $this->htmlModificationMotDePasse();
                break;
            }
            case 5 : {
                $content=$this->afficherSupressionCompte();
                break;
            }
        }
        $vue = new VueRender($this->container);
        return $vue->render($content);
    }

}