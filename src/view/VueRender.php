<?php
declare(strict_types=1);

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
     * @author Lucas Weiss
     */
    public function render(String $content) :string{
        return <<<END
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
                <title>Home - Brand</title>
                <link rel="stylesheet" href="{$this->container->router->pathFor("accueil")}assets/bootstrap/css/bootstrap.min.css">
                <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i,600,600i">
                <link rel="stylesheet" href="{$this->container->router->pathFor("accueil")}assets/fonts/simple-line-icons.min.css">
                <link rel="stylesheet" href="{$this->container->router->pathFor("accueil")}assets/css/vanilla-zoom.min.css">
                <link rel="stylesheet" href="{$this->container->router->pathFor("accueil")}css/style.css">

            </head>
            <body>
                <header>
                    <nav class="navbar navbar-light navbar-expand-lg fixed-top bg-white clean-navbar">
                        <div class="container"><button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navcol-1"><span class="visually-hidden">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
                            <div class="collapse navbar-collapse" id="navcol-1"><a class="nav-link" href={$this->container->router->pathFor("accueil")}><img class="logoPrincipal" src="{$this->container->router->pathFor("accueil")}assets/img/MyWishList_logo.png"></a>
                                <ul class="navbar-nav ms-auto">
                                    <li class="nav-item"><a class="nav-link" href={$this->container->router->pathFor("accueil")}><p class="textNav">Accueil</p></a></li>
                                    <li class="nav-item"><a class="nav-link" href={$this->container->router->pathFor("affichageListesPublique")}><p class="textNav">Listes publique</p></a></li>
                                    <li class="nav-item"><a class="nav-link" href={$this->container->router->pathFor("connectionCompte")}><p class="textNav">Se connecter</p></a></li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </header>
                    <main class="page landing-page">
                        <section class="clean-block clean-info dark">
                            <div class="container">
                                $content
                            </div>
                        </section>
                    </main>
                <script src="assets/bootstrap/js/bootstrap.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.10.0/baguetteBox.min.js"></script>
                <script src="assets/js/vanilla-zoom.js"></script>
                <script src="assets/js/theme.js"></script>
                <footer>
                    <div class="clean-block add-on call-to-action blue">
                        <p class="auteurs">Auteurs: Fabrice ARNOUT, Guillaume RENARD, Marcus RICHIER, Mathieu VINOT, Lucas WEISS<br></p>
                    </div>          
                </footer>
            </body>
        </html> 
END;
    }

    /**
     * Méthode pour afficher la page d'accueil
     * Utilisé par la fonction 0, route racine de l'accueil
     * @return string String: contenu html
     * @author Lucas Weiss
     */
    public function affichageAccueil() : string {

        $val = isset($_SESSION['login']);
        if ($val==0) {
            $txt = '<p><a href='.$this->container->router->pathFor("creationCompte").'>Se creer un compte</a></p>
                    <p><a href='.$this->container->router->pathFor("connectionCompte").'>Se connecter</a></p>
                    <p><a href='.$this->container->router->pathFor("creationListe").'>Creer une nouvelle liste</a></p>';
        } else {
            $txt = <<<END
<p> Bonjour a toi, {$_SESSION["login"]}</p>
<p><a href={$this->container->router->pathFor("afficherListePerso")}>Afficher ses listes personnelles</a></p>
<p><a href={$this->container->router->pathFor("creationListe")}>Creer une nouvelle liste</a></p>
<p>Ajouter une liste dans les listes perso : <form action="{$this->container->router->pathFor('ajouterProprietaire')}" method="post"><input type="text" size="50" name="token" placeholder="notez votre token d'édition ici" autocomplete="off" required> <button type="submit" class="btn btn-primary">Ajouter</button></form></p>
<p><a href={$this->container->router->pathFor("modificationCompte")}>Modifier les informations du compte</a></p>
<p><a href={$this->container->router->pathFor("deconectionCompte")}>Se deconnecter</a></p>
END;
        }

        $html = <<<END
                    <div class="block-heading">
                        <h2 class="text-info">Bienvenue</h2>
                        <p>Bienvenue sur notre projet PHP de 3ème semestre.<br>Crée votre liste et accès à vos listes et réserver les items ! Où encore modifier vos items de vos listes pour créer des superbes listes.<br><br></p>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-6ImgAccueil"><img class="img-thumbnail" src="assets/img/gift1.jpg"></div>
                            <div class="col-md-6">
                                <h3>Envie d'avoir des cadeaux qui vous plaisent ?<br>A vous de jouer !</h3>
                                <div class="getting-started-info">
                                    <p><a href={$this->container->router->pathFor("affichageListesPublique")}>Afficher toutes les listes publiques</a></p>
                                    {$txt}
                                </div>
                        </div>
                    </div>
END;


        return $html;
    }

    /**
     * Methode qui genere le html pour afficher un message d'erreur
     * @author Lucas Weiss
     */
    public function htmlErreur(string $message): string {
        return <<<END
                    <div class="block-heading">
                        <h2 class="text-info">Erreur</h2>
                        <p>$message</p>
                    </div>
END;
    }


}