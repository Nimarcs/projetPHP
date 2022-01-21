<?php

declare(strict_types=1);

// NAMESPACE
namespace mywishlist\view;

// IMPORTS
use mywishlist\modele\Item;
use mywishlist\modele\Liste;
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
    private function htmlCreationItemPourUneListe(array $arg) :string {
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
     * Fonction 2
     * affichage d'un item
     * @param $args tableau avec l'item, le token et l'id
     * @return String html coorespondant
     *
     * @author Mathieu VINOT
     * @author Fabrice Arnout
     * @author Marcus RICHIER (reservation + affichage différent si createur / date)
     */
    public function htmlAfficherUnItem( array $args) : String
    {
        $i = $args['item'];

        //reservation
        if ($i->reserverPar == null) $texteReservation = <<<END
<p>L'item n'est pas encore reservé</p>
<form action="{$this->container->router->pathFor('reserverItem', ['token' => $args['token'], 'id' => $args['id']])}" method="get"> 
    <button type="submit" class="btn submit">Réserver l'item</button>
</form>
END;
        else $texteReservation = "L'item est reserver par : {$i->reserverPar}";


        //si la date d'échéance est pas passé
        if ($i->liste->expiration > date('YYYY-MM-DD')) {
            //la vision du propriétaire change

            //on check s'il est le propriétaire
            $estProprietaire = false;
            if (isset($_COOKIE['listeCree'])) {
                $a = unserialize($_COOKIE['listeCree']);
                print "ICI";
                var_dump($a);
                foreach ($a as $noListe) {
                    if ($noListe == $i->liste_id) $estProprietaire = true;
                }
            }
            var_dump($estProprietaire);

            if ($estProprietaire || $args['edition'] || (isset($_SESSION['login']) && $_SESSION['login'] == $i->liste->login)) {

                //on change l'affichage de la reservation si c'est le propriétaire
                if ($i->reserverPar != null) $texteReservation = "L'item est réservé";
                else $texteReservation = "L'item n'est pas réservé";
            }
        }

        //html
        return <<<END
<div class="boite-item">
        <div class="nomItem">
            <h3>${i['nom']}</h3>
        </div>
        <p>
            ${i['description']}<br>
        </p>
        <img style='width: 100px;' src="{$this->container->router->pathFor('accueil')}img/{$i['img']}"
        <br>
        <div class="reservation">
            $texteReservation
        </div>
        <form action="{$this->container->router->pathFor('afficherListe', ['token' => $args['token']])}" method="get"> 
            <button type="submit" class="btn submit">Retourner à la liste</button>
        </form>
</div>


END;

    }

    /**
     * Fonction qui retourne l'html selon le selecteur choisis
     * @param $selecteur entier: choix de la page a afficher
     * @return string String: texte html, cointenu global de chaque page
     * @author Lucas Weiss
     */
    public function render(int $selecteur, array $arg1 = null):string
    {
        $content = "";
        switch ($selecteur) {
            case 1:
            {
                $content = $this->htmlCreationItemPourUneListe($arg1);
                break;
            }
            case 2:
            {
                $content = $this->htmlAfficherUnItem($arg1);
                break;
            }
            case 3:
            {
                $content = $this->htmlReserverItem($arg1);
                break;
            }
        }
        $vue = new VueRender($this->container);
        return $vue->render($content);
    }

    /**
     * Methode qui genere le contenu de la page de reservation
     * @param array $args parametre necessaire a la creation de cette page
     * @return string html du content de la page ou l'on reserve
     * @author Marcus Richier
     */
    private function htmlReserverItem(array $args):string
    {
        //si quelqu'un est connecté on préremplit son login
        $login = $_SESSION['login'] ?? "";
        //s'il a deja rempli une demande de pseudo, c'est prérempli aussi
        if (isset($_COOKIE["lastPSEUDO"])) $login = $_COOKIE["lastPSEUDO"];

        //on cache le formulaire si c'est deja reserve
        if ($args['reserverPar'] == null)
            $form = <<<END
            <p>l'item n'est pas reservé</p>
            <form action="" method="post">     
                <label for="reservateur">Nom avec lequel vous voulez reserver :</label>
                <input type="text" name="reservateur" maxlength="50" placeholder="nom" size="50" value="$login" required autofocus>
                <input type="hidden" name="token" value="{$args['token']}" required>
                <input type="hidden" name="id" value="{$args['id']}" required>
                <button type="submit" class="btn submit">
                    Réserver l'item
                </button>
            </form>
END;
        else $form = "<p>l'item est reserver par {$args['reserverPar']}</p>";


        //on renvoie la page complete
        return <<<END
        <h2>Reserver l'item "{$args['nom']}" ?</h2>
        <div class="formulaire">
            $form
            <form action="{$this->container->router->pathFor('afficherItem', ['token' => $args['token'], 'id' => $args['id']])}" method="get"> 
                <button type="submit" class="btn submit">Retourner sur la page de l'item</button>
            </form>
        </div>
END;
    }

}
/*
 *
 */