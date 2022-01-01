<?php

namespace mywishlist\view;

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
     * Fonction 6
     * Affichage du formulaire de la création de liste
     */
    private function htmlCreationListe() {
        $html = <<<END
              <h2>Creation d'une liste</h2>
        <div>
            <form action="" method="post">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" name="titre" placeholder="" required maxlength="22"><br>
                
                <label for="desc" class="form-label">Description</label>
                <input type="text" class="form-control" name="description" placeholder="" required><br>

                <label for="exp" class="form-label">Date limite</label>
                <input type="date" class="form-control" name="expiration" placeholder="" required><br>
                <button type="submit" class="btn btn-danger btn-lg">
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
     */
    private function htmlAffichageListe() {
        $html = "";
        $list = Liste::query()->where('public', '=', 'true')->get();
        foreach ($list as $l) {
            $html = $html . $this->afficherEnLigneUneListe($l);
        }
        return $html;
    }

    /**
     * Fonction 21
     * Methode privee qui genere l'affichage d'une liste sur une ligne avec les boutons adequats
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
                    <button type="button" class="" onclick="window.location.href='{$this->container->router->pathFor('liste', ['token'=>$l['no']])}';">
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
     */
    public function render($selecteur)
    {
        $content = "";
        switch ($selecteur) {
            case 1: {
                $content = $this->htmlCreationListe();
                break;
            }
            case 2: {
                $content = $this->htmlAffichageListe();
            }
        }
        $vue = new VueRender($this->container);
        return $vue->render($content);

    }

}