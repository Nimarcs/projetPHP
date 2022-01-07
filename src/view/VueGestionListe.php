<?php
declare(strict_types=1);

// NAMESPACE
namespace mywishlist\view;

// IMPORTS
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
     * Affichage d'une liste
     *
     * @param string $
     * @return string
     *
     * @author Mathieu Vinot
     */
    //L'affichage d'une liste précise se fait grace à son token, qui l'identifie de façon unique
    private function htmlAffichageListeToken($arg1) {
        //$html = "<h3>{$arg1['no']}</h3>";

        $html = <<<END
              <div class="boite-liste"'>
                <div class="titreDeListe">
                    <h2>{$arg1['titre']}</h2>
                </div>
                    <p>
                        {$arg1['description']} <br>
                        --- Expire le {$arg1['expiration']}</li>
                    </p>
                    <p>
                    
</p>
            </div>
            <br><br>
END;

        return $html;
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
     * Fonction 21
     * Methode privee qui generer l'affichage de toutes les listes publique
     * @author Lucas Weiss
     */
    private function htmlAffichageListes($listes) {
        $html = "";
        //erreur -> à mettre dans controleur (idem pour affichage liste)
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
                    <button type="button" class="" onclick="window.location.href='';">
                        VOIR LA LISTE
                    </button>
                    Token: <input type="text" value="" disabled="disabled">
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
     * Fonction qui retourne selon le selecteur choisis
     * @param $selecteur entier: choix de la page a afficher
     * @return string String: texte html, cointenu global de chaque page
     * @author Lucas Weiss
     */
    public function render($selecteur, $arg1 = null) :string
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
                $content = $this->htmlAffichageListeToken($arg1);
                break;
            }
        }
        $vue = new VueRender($this->container);
        return $vue->render($content);
    }

}