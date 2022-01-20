<?php
declare(strict_types=1);

// NAMESPACE
namespace mywishlist\view;

// IMPORTS
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
     *
     * Fonction 1
     * Affichage d'une liste et de ses items
     * L'affichage d'une liste précise se fait grace à son token, qui l'identifie de façon unique
     *
     * @param $arg1, liste
     * @param $arg2, items
     * @return string
     *
     * @author Mathieu Vinot
     */
    private function htmlAffichageListeToken($arg1, $arg2) {
        $items = self::htmlAfficherTousItem($arg1, $arg2);
        $html = <<<END
              <div class="boite-liste"'>
                <div class="titreDeListe">
                    <h2>{$arg1['titre']}</h2>
                </div>
                    <p>
                        {$arg1['description']} <br>
                        --- Expire le {$arg1['expiration']}</li>
                    </p>
                    $items
            </div>
            <br><br>
END;
        return $html;
    }

    /**
     * Fonction 1 bis
     * Methode privee qui genere l'affichage d'une liste avec ces items et des boutons pour modifier la liste et affiche le lien à partager
     * @author Lucas Weiss
     * @author Guillaume Renard
     */
    private function afficherListeEdition($l, $i) : String {
        $items = self::htmlAfficherTousItem($l, $i);
        return <<<END
<div class="boite-liste"'>
                <div class="titreDeListe">
                    <h2>${l['titre']}</h2>
                </div>
                    <p>
                        ${l['description']} <br>
                        --- Expire le ${l['expiration']}</li>
                        
                        $items
                    </p>
                    
                     PARTAGER LA LISTE : <input type="text" value={$_SERVER['HTTP_HOST']}{$this->container->router->pathFor('accueil')}liste/${l['token_lecture']} disabled="disabled" style="width: 700px;">
                    <button type="button" class="" onclick="window.location.href='modification';">
                        MODIFIER LISTE
                    </button>
                    <button type="button" class="" onclick="window.location.href='supression';">
                        SUPPRIMER LISTE
                    </button>
            </div>
            <br><br>
END;

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
    private function htmlAfficherTousItem($l, $i)
    {
        $res = "";
        $num = 1;
        foreach ($i as $itemCurr) {
            $res .= "<p>" . $num . ". <img style='width: 100px;' src=".$this->container->router->pathFor('accueil')."img/" . $itemCurr->img . ">" . $itemCurr->nom;
            $res.= <<<END
<form action="{$this->container->router->pathFor('afficherItem', ['token' => $l['token_lecture'], 'id' => $itemCurr->id])}" method="get">
    <button type="submit" class="btn submit">AFFICHER ITEM</button>
</form><br>
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
    private function htmlCreationListe() {
        $html = <<<END
              <h2>Creation d'une liste</h2>
        <div class="formulaire">
            <form action="" method="post">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" name="titre" placeholder="" required maxlength="22"><br>
                
                <label for="desc" class="form-label">Description</label>
                <input type="text" class="form-control" name="description" placeholder="" required><br>

                <label for="exp" class="form-label">Date limite</label>
                <input type="date" class="form-control" name="expiration" placeholder="" required><br>
                <button type="submit" class="btn submit">
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
    private function afficherModifierListe($l){

        if( $l["public"]=="0"){
            $p_oui="checked";
            $p_non="";
        } else {
            $p_oui="";
            $p_non="checked";
        }

        $html = <<<END
              <h2>Modification des informations d'une liste</h2>
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
                
                <button type="submit" class="btn submit" value="{$l["token_edition"]}" name="token_edition">
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
    private function afficherSupressionListe($l){
        $html= <<<END
  <h2>Supression d'une liste</h2>
        <div class="formulaire">
            <form action="" method="post">
            
                <label for="listeSupression" class="form-label">Etes-vous vraiment sûre de vouloir suprimer cette liste</label><br> 
                <label for="supression" class="form-label">Oui</label>
                <input type="radio" value="0" class="form-control" name="supr" placeholder="" required >
                 <label for="supression" class="form-label">Non</label>
                <input type="radio" value="1" class="form-control" name="supr" placeholder="" required checked><br> 
                
                <button type="submit" class="btn submit" value="{$l["token_edition"]}" name="token_edition">
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
    private function htmlAffichageListes($listes) {
        $html = "";
        foreach ($listes as $l) {
            $html = $html . $this->afficherEnLigneUneListe($l);
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
                    <button type="button" class="" onclick="window.location.href='liste/${l['token_lecture']}';">
                        AFFICHER LA LISTE
                    </button>
                    Token: <input type="text" value="oui" disabled="disabled">
                    <button type="button" class="" onclick="window.location.href='';">
                        MODIFIER LISTE
                    </button>
                    <button type="button" class="" onclick="window.location.href='';">
                        SUPPRIMER LISTE
                    </button>
            </div>
            <br><br>
END;

    }

    /**
     * Fonction 26
     * @param $arg1
     * @return string
     */
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

    /**
     * Fonction 26
     * @param $arg1
     * @return string
     */
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

    /**
     * Fonction qui retourne selon le selecteur choisis
     * @param $selecteur entier: choix de la page a afficher
     * @return string String: texte html, cointenu global de chaque page
     * @author Lucas Weiss
     */
    public function render($selecteur, $arg1 = null, $arg2 = null) :string
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
                $content = $this->htmlAffichageListeToken($arg1, $arg2);
                break;
            }
            case 4: {
                $content = $this->afficherListeEdition($arg1, $arg2);
                break;
            }
            case 5 : {
                $content = $this->afficherModifierListe($arg1);
                break;
            }
            case 6 : {
                $content = $this->afficherListeCreateur($arg1);
                break;
            }
            case 6 : {
                $content = $this->afficherSupressionListe($arg1);
                break;
            }
        }
        $vue = new VueRender($this->container);
        return $vue->render($content);
    }

}