<?php

namespace mywishlist\view;

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
        }
        $vue = new VueRender($this->container);
        return $vue->render($content);

    }

}