<?php

// NAMESPACE
namespace mywishlist\view;

// IMPORTS
use Slim\Container;

/**
 * Classe VueRender
 *
 */
class VueRender
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
     * Fonction qui retourne l'affichage général du site web
     * @param $content Container
     * @return string String: texte html, cointenu global de chaque page
     */
    public function render($content) {
        return <<<END
        <!DOCTYPE html>
        <html>
            <head>
                <title>My Wish List</title>
            </head>
            <body>
                <!-- page web -->
                <div class="container">
                    $content
                </div>
                
                <!-- js boostrap -->
                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
            </body>
        </html> 
END;
    }

    /**
     * Méthode pour afficher la page d'accueil
     * Utilisé par la fonction 0, route racine de l'accueil
     * @return string String: contenu html
     */
    public function affichageAccueil() {
        return <<<END
            <div class="text-page-accueil">
                <p>
                    Bonjour, bienvenue sur notre projet PHP de 3eme semestre. <br>
                    Crée votre liste et accès à vos listes et réserver les items !<br>
                    Où encore modifier vos items de vos listes pour créer des superbes listes.<br>
                    <br>
                    Auteurs: Fabrice ARNOUT, Guillaume RENARD, Marcus RICHIER, Mathieu VINOT, Lucas WEISS                    
                </p>
            </div>
END;
    }



}