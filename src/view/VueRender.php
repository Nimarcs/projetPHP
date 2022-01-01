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
                <title>MyWishList</title>
            <link rel="stylesheet" href="{$this->container->router->pathFor('accueil')}css/style.css" type="text/css"/>
            </head>
            <header>
	            <p>WishList</p>
            </header>
            <body>
                <!-- page web -->
                <div class="container">
                    $content
                </div>
            </body>
            <footer>
                <p><a href={$this->container->router->pathFor('accueil')}>Retour a l'accueil</a></p>
                <p>Auteurs: Fabrice ARNOUT, Guillaume RENARD, Marcus RICHIER, Mathieu VINOT, Lucas WEISS</p>                    
            </footer>
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
            <div class="text-accueil">
                <p>
                    Bonjour, bienvenue sur notre projet PHP de 3eme semestre. <br>
                    Crée votre liste et accès à vos listes et réserver les items !<br>
                    Où encore modifier vos items de vos listes pour créer des superbes listes.<br>
                    <br>
                    <p><a href={$this->container->router->pathFor('creationCompte')}>Se creer un compte</a></p>
                    <p><a href={$this->container->router->pathFor('affichageListesPublique')}>Afficher toutes les listes publiques</a></p>
                    <p><a href={$this->container->router->pathFor('creationListe')}>Creer nouvelles listes</a></p>

                </p>
            </div>
END;
    }

    public function htmlErreur($message) {
        return <<<END
            <div class="text-erreur">
                <p>$message</p>
                <p><a href={$this->container->router->pathFor('accueil')}>Retour a l'accueil</a></p>
            </div>
END;
    }


}