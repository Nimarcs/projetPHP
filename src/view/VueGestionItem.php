<?php

declare(strict_types=1);

// NAMESPACE
namespace mywishlist\view;

// IMPORTS
use mywishlist\modele\Item;
use Slim\Container;

/**
 * Classe VueGestionItem
 * Gere les vues concernant les vues sur les fonctionnalités concernant les items
 */
class VueGestionItem
{
    // ATTRIBUTS

    private $container;

    public function __construct(Container $c){
        $this->container = $c;
    }

    /**
     * Fonction 8
     * Methode vue qui retourne l'html de la page afin d'ajouter un item sur une liste bien précise
     */
    private function htmlCreationItemPourUneListe($arg) {
        return <<<END
        <h2>Ajouter un item</h2>
        <h3>Pour la liste n°{$arg['no']}.</h3>
        <form action="" method="post">
                <label for="titre" class="form-label">Nom</label>
                <input type="text" class="form-control" name="nom" placeholder="" required maxlength="30"><br>
                
                <label for="desc" class="form-label">Description</label>
                <input type="text" class="form-control" name="description" placeholder="" required><br>

                <label for="exp" class="form-label">Prix</label>
                <input type="number" class="form-control" name="prix" placeholder="" required>€<br>
                
                <label for="exp" class="form-label">Image</label>
                <input type="file" name="fichier" accept="image/png, image/gif, image/jpeg" /><br>
                
                <button type="submit" class="btn submit">
                    Créer l'item
                </button>
            </form>

END;

    }

    /**
     * Fonction qui retourne l'html selon le selecteur choisis
     * @param $selecteur entier: choix de la page a afficher
     * @return string String: texte html, cointenu global de chaque page
     * @author Lucas Weiss
     */
    public function render(int $selecteur, $arg1 = null)
    {
        $content = "";
        switch ($selecteur) {
            case 1: {
                $content = $this->htmlCreationItemPourUneListe($arg1);
                break;
            }
        }
        $vue = new VueRender($this->container);
        return $vue->render($content);
    }

}