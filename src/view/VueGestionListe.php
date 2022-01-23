<?php
declare(strict_types=1);

// NAMESPACE
namespace mywishlist\view;

// IMPORTS
use Illuminate\Database\Eloquent\Collection;
use mywishlist\modele\Liste;
use Slim\Container;

/**
 * Classe VueGestionListe
 * Gere les vues concernant les vues sur les fonctionnalités concernant les listes
 */
class VueGestionListe
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
     * Fonction 1
     * Affichage d'une liste et de ses items
     * L'affichage d'une liste précise se fait grace à son token, qui l'identifie de façon unique
     * genere l'affichage d'une liste avec ces items et des boutons pour modifier la liste et affiche le lien à partager si et seulement si c'est un token d'edition qui est utlise
     *
     * @param $liste Liste liste a afficher
     * @param $isTokenEdition bool est ce que le token d'edition a été utilisé
     * @return string html a afficher
     *
     * @author Mathieu Vinot (lecture et les messages)
     * @author Marcus (fusion des deux fonctionnalités (1 et 1bis))
     * @author Lucas Weiss (modification)
     * @author Guillaume Renard (modifcation)
     */
    private function htmlAffichageListe(Liste $liste, bool $isTokenEdition) {

        //on recupere le token
        if ($isTokenEdition)
            $token = $liste['token_edition'];
        else
            $token = $liste['token_lecture'];

        //on recupere l'affichage des items
        $items = self::htmlAfficherTousItem($token, $liste->items);

        //on change l'affichage en fonction de sis c'est public
        if (!$liste['public']) $public = 'La liste est actuellement publique.';
        else $public = 'La liste est actuellement privée.';

        if ($isTokenEdition){
            $lesMessages = "";
            $boutonsEdition = <<<END
<p> Vous êtes dans la partie edition de votre liste, pour aller sur la page que vous devez partager <a href="{$this->container->router->pathFor('afficherListe', ['token' => $liste['token_lecture']])}">cliquez ici</a> ou copier le lien ci-dessous</p>
<div id="boutonModificationListe"
    <label for="partage">PARTAGER LA LISTE :</label> <input type="text" name="partage" value={$_SERVER['HTTP_HOST']}{$this->container->router->pathFor('accueil')}liste/${liste['token_lecture']} disabled="disabled" size="100">
    <form action="{$this->container->router->pathFor('modifierListe', ['token_edition' => $liste['token_edition']])}" method="get"> 
        <button class="btn btn-primary" type="submit"> MODIFIER LISTE</button>
    </form>
    <form action="{$this->container->router->pathFor('suprimerListe', ['token_edition' => $liste['token_edition']])}" method="get"> 
        <button class="btn btn-primary" type="submit"> SUPPRIMER LISTE</button>
    </form>
</div>
END;
            $boutonAjouterItem = <<<END
<form action="{$this->container->router->pathFor('newItem', ['token' => $liste['token_edition']])}" method="get"> 
    <button type="submit" class="btn btn-primary"> AJOUTER ITEM</button>
</form>
END;

        } else{
            $boutonsEdition ="";
            $boutonAjouterItem ="";
            $log = $_SESSION['login'];
            if ($log == null){
                $log = 'anonyme';
            }
            $lesMessages = <<<END
<h3>Messages des autres participants : </h3><br>
{$liste['messages']}
<form action="{$this->container->router->pathFor('posterUnMessage', ['token' => $liste['token_lecture']])}" method="post">
                <input type="hidden" name="token" value="{$liste['token_lecture']}">
                <input type="hidden" name="login" value="$log">
                <label for="nom" class="form-label">Ajouter un message : </label>
                <input type="text" value=  "" class="form-control" name="message" autocomplete="off" required maxlength="100"><br>      
                <button type="submit" class="btn btn-primary" >
                   Envoyer
                </button>
            </form>
END;

        }

        if ($liste->login == "anonyme") $createur = "anonyme";
        else $createur = str_replace( ",", ", ", substr($liste->login, 1, -1));

        $html = <<<END
            <div class="block-heading">
                    <h2 class="text-info">Liste - {$liste['titre']}</h2>
                    <p>
                    {$liste['description']} <br><br>
                    Créer par {$createur}.<br>Expire le {$liste['expiration']}.<br>$public
                    </p>
            </div>
              <div class="boite-liste"'>
                $boutonsEdition
                $items
                
                $boutonAjouterItem
                $lesMessages
            </div>
            <br><br>
END;
        return $html;
    }


    /**
     *
     * fonction 1.1
     * permet l'affichage de tout les items de la liste
     *
     * @param $arg2
     * @return string
     *
     * @author Mathieu Vinot
     * @author Lucas Weiss
     */
    private function htmlAfficherTousItem(string $tokenL, $i)
    {
        $res = "";
        $num = 1;
        foreach ($i as $itemCurr) {

            //reservation
            if ($itemCurr->reserverPar == null) $reserver = 'état : disponible';
            else $reserver = 'état : reservée';

            //img
            if (filter_var($itemCurr['img'], FILTER_VALIDATE_URL)){
                $urlImg = $itemCurr['img'];
            } else{
                $urlImg ="{$this->container->router->pathFor('accueil')}img/{$itemCurr['img']}";
            }

            $res .= <<<END
                <div class="row align-items-center">
            <div class="col-md-6Img"><img class="img-thumbnail imgItem" src=$urlImg /></div>
            <div class="col-md-6Texte">
                <h3>$num. {$itemCurr->nom}</h3>
                <form action="{$this->container->router->pathFor('afficherItem', ['token' => $tokenL, 'id' => $itemCurr->id])}" method="get">
                    <button class="btn btn-primary" type="submit">AFFICHER ITEM</button>
                </form><br>
                <div class="getting-started-info">
                    <p>{$itemCurr->descr}</p>
                </div>
                <p>{$reserver}</p>
            </div>
        </div>
END;
            $num++;
        }
        return $res;
    }


    /**
     * Fonction 6
     * Affichage du formulaire de la création de liste
     * @author Lucas Weiss
     */
    private function htmlCreationListe() :string{
        $html = <<<END
            <div class="block-heading">
                        <h2 class="text-info">Creation d'une liste</h2>
            </div>
        <div class="formulaire">
            <form action="" method="post">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" name="titre" placeholder="" required maxlength="22"><br>
                
                <label for="desc" class="form-label">Description</label>
                <input type="text" class="form-control" name="description" placeholder="" required><br>

                <label for="exp" class="form-label">Date limite</label>
                <input type="date" class="form-control" name="expiration" placeholder="" required><br>
                <button class="btn btn-primary" type="submit">
                    Créer la liste
                </button>
            </form>
        </div>
END;
        return $html;
    }

    /**
     * Fonction 7
     * Affichage formulaire pour modifier la liste
     * @author Guillaume Renard
     */
    private function afficherModifierListe(Liste $l):string{

        if( $l["public"]=="0"){
            $p_oui="checked";
            $p_non="";
        } else {
            $p_oui="";
            $p_non="checked";
        }

        $html = <<<END
            <div class="block-heading">
                        <h2 class="text-info">Modification des informations d'une liste</h2>
            </div>
        <div class="formulaire">
            <form action="" method="post">
                <label for="titre" class="form-label">Modifier le titre</label>
                <input type="text" value=  "${l['titre']}" class="form-control" name="titre" placeholder="" required maxlength="22"><br>
                
                <label for="desc" class="form-label">Modifier la description</label>
                <input type="text" value="{$l['description']}" class="form-control" name="description" placeholder="" required><br>

                <label for="exp" class="form-label">Modifier la date limite</label>
                <input type="date" value="{$l["expiration"]}" class="form-control" name="expiration" placeholder="" required><br> 
                
                <label for="listePublic" class="form-label">Mettre en public</label><br> 
                <label for="public" class="form-label">Oui</label>
                <input type="radio" value="0" class="form-control" name="public" placeholder="" required $p_oui>
                 <label for="public" class="form-label">Non</label>
                <input type="radio" value="1" class="form-control" name="public" placeholder="" required $p_non><br> 
                
                <button type="submit" class="btn btn-primary" value="{$l["token_edition"]}" name="token_edition">
                   Enregistrer les modifications
                </button>
            </form>
        </div>
END;
        return $html;
    }


    /**
     * Fonction ?
     * Affichage pour supprimer une liste ou non
     * @author Guillaume Renard
     */
    private function afficherSupressionListe(Liste $l):string{
        $html= <<<END
            <div class="block-heading">
                        <h2 class="text-info">Supression d'une liste</h2>
            </div>
            <div class="formulaire">
            <form action="" method="post">
            
                <label for="listeSupression" class="form-label">Êtes-vous vraiment sûr.e de vouloir suprimer cette liste</label><br> 
                <label for="supression" class="form-label">Oui</label>
                <input type="radio" value="0" class="form-control" name="supr" placeholder="" required >
                 <label for="supression" class="form-label">Non</label>
                <input type="radio" value="1" class="form-control" name="supr" placeholder="" required checked><br> 
                
                <button type="submit" class="btn btn-primary" value="{$l["token_edition"]}" name="token_edition">
                   Valider
                </button>
            </form>
        </div>
END;
    return $html;
    }


    /**
     * Fonction 21
     * Methode privee qui generer l'affichage de toutes les listes publique
     * @author Lucas Weiss
     */
    private function htmlAffichageListes($listes):string {
        $html = <<<END
            <div class="block-heading">
                        <h2 class="text-info">Listes publique</h2>
            </div>
END;
        $pasVide = false;
        foreach ($listes as $l) {
            $pasVide = true;
            $html = $html . $this->afficherEnLigneUneListe($l);
        }
        if (!$pasVide){
            $html = $html . "<p>Il semble qu'il n'y a pas de liste publique actuellement !</p>";
        }
        return $html;
    }

    /**
     * Fonction 21
     * Methode privee qui genere l'affichage d'une liste sur une ligne avec les boutons adequats
     * @author Lucas Weiss
     */
    private function afficherEnLigneUneListe($l) : String {
        return <<<END
<div class="boite-liste"'>
                <div class="titreDeListe">
                    <h2>${l['titre']}</h2>
                </div>
                    <p>
                        ${l['description']} <br>
                        --- Expire le ${l['expiration']}</li>
 
                    </p>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='liste/${l['token_lecture']}';">
                        AFFICHER LA LISTE
                    </button>
                    Token: <input type="text" size="80" value="{$l['token_lecture']}" disabled="disabled">
            </div>
            <br><br>
END;

    }

    /**
     * Fonction qui permet d'afficher ses listes personnelles
     *
     * @param $listes, listees
     * @return string
     *
     * @author Mathieu Vinot
     */
    private function afficherListePerso($listes) {
        $html = <<<END
            <div class="block-heading">
                        <h2 class="text-info">Listes personnelle</h2>
            </div>
END;
        foreach ($listes as $l) {
            $html = $html .$this->afficherEnLigneUneListePerso($l);
        }
        return $html;
    }

    /**
     * Fonction qui permet l'affichage de ses listes personnelles
     *
     * @param $l
     * @return String
     *
     * @author Mathieu Vinot
     */
    private function afficherEnLigneUneListePerso($l) : String {
        return <<<END
<div class="boite-liste"'>
                <div class="titreDeListe">
                    <h2>${l['titre']}</h2>
                </div>
                    <p>
                        ${l['description']} <br>
                        --- Expire le ${l['expiration']}</li>
 
                    </p>
                    <form action="{$this->container->router->pathFor('afficherListe', ['token' => $l['token_edition']])}" method="get">
                         <button class="btn btn-primary" type="submit">AFFICHER LA LISTE</button>
                    </form><br>
                    Partager la liste: <input type="text" value="{$_SERVER['HTTP_HOST']}{$this->container->router->pathFor('accueil')}liste/${l['token_lecture']}" disabled="disabled" style="width: 700px;">
                    <form action="{$this->container->router->pathFor('modifierListe', ['token_edition' => $l['token_edition']])}" method="get">
                         <button class="btn btn-primary" type="submit">MODIFIER LISTE</button>
                    </form><br>
                    <form action="{$this->container->router->pathFor('suprimerListe', ['token_edition' => $l['token_edition']])}" method="get">
                         <button class="btn btn-primary" type="submit">SUPPRIMER LISTE</button>
                    </form><br>
            </div>
            <br><br>
END;

    }

    /**
     * Fonction 26
     * @param $arg1
     * @return string
     */
    /*
    private function afficherListeCreateur($arg1) {

        $content=self::recupererListeCreateur($arg1);

        $html = <<<END
              <div class="boite-liste"'>
                $content
            </div>
            <br><br>
END;
        return $html;
    }
*/
    /**
     * Fonction 26
     * @param $arg1
     * @return string
     */
    /*
    private function recupererListeCreateur($arg1)
    {
        $res = "";

        foreach ($arg1 as $listeCurr) {
            $res .= "<div class='titreDeListe'>";
            $res .=   "    <h2>{$listeCurr['titre']}</h2>";
            $res .=  "  </div>";
            $res .=  "<p>";
            $res .= "           {$listeCurr['description']} <br>";
            $res .= "--- Expire le {$listeCurr['expiration']}</li>";
            $res .=   "     </p>";
        }
        return $res;
    }
    */

    /**
     * Fonction qui retourne selon le selecteur choisis
     * @param $selecteur int: choix de la page a afficher
     * @return string String: texte html, cointenu global de chaque page
     * @author Lucas Weiss
     */
    public function render(int $selecteur, $arg1 = null, $arg2 = null) :string
    {
        $content = "";
        switch ($selecteur) {
            case 1: {
                $content = $this->htmlCreationListe();
                break;
            }
            case 2: {
                $content = $this->htmlAffichageListes($arg1);
                break;
            }
            case 3: {
                $content = $this->htmlAffichageListe($arg1, $arg2);
                break;
            }
            case 5 : {
                $content = $this->afficherModifierListe($arg1);
                break;
            }
            case 6 : {
                $content = $this->afficherSupressionListe($arg1);
                break;
            }
            case 7 :
                $content = $this->afficherListePerso($arg1);
                break;
        }
        $vue = new VueRender($this->container);
        return $vue->render($content);
    }

}