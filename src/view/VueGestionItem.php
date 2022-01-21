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
     * Fonction 2
     * affichage d'un item
     * @param $args tableau avec l'item, le token et l'id
     * @return String html coorespondant
     *
     * @author Mathieu VINOT
     * @author Marcus RICHIER (reservation)
     */
    public function htmlAfficherUnItem($args) : String {
        $i = $args['item'];

        //reservation
        if ($i->reserverPar == null) $texteReservation = <<<END
<p>L'item n'est pas encore reservé</p>
<form action="{$this->container->router->pathFor('reserverItem', ['token' => $args['token'], 'id' => $args['id']])}" method="get"> 
    <button type="submit" class="btn submit">Réserver l'item</button>
</form>
END;
        else $texteReservation = "L'item est reserver par : {$i->reserverPar}";


        //vision du propriétaire
        if ($args['edition']) {
            if ($i->reserverPar != null) $texteReservation = "L'item est réservé";
            else $texteReservation = "L'item n'est pas réservé";
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
        <img src="img/"+${i['image']}>
        <div class="reservation">
            $texteReservation
        </div>
</div>


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

    private function htmlReserverItem($args)
    {
        if ($args['reserverPar'] == null) $form = <<<END
            <p>l'item n'est pas reservé</p>
            <form action="" method="post">     
                <label for="reservateur">Nom avec lequel vous voulez reserver :</label>
                <input type="text" name="reservateur" maxlength="50" required autofocus placeholder="nom">
                <input type="hidden" name="token" value="{$args['token']}" required>
                <input type="hidden" name="id" value="{$args['id']}" required>
                <button type="submit" class="btn submit">
                    Réserver l'item
                </button>
            </form>
END;
        else $form = "<p>l'item est reserver par {$args['reserverPar']}</p>";


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