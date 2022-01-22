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
     * @author Lucas Weiss
     * @author Marcus Richier
     */
    private function htmlCreationItemPourUneListe(array $arg) :string {
        $imgs = $this->getImageEnregistree();
        $options ="";
        foreach ($imgs as $img){
            $options.="<option value='$img'>$img</option>";
        }

        return <<<END
            <div class="block-heading">
                <h2 class="text-info">Ajouter un item</h2>
            </div> 
            <h3>Pour la liste n°{$arg['liste']->no}.</h3>
        <form action="" method="post" enctype="multipart/form-data">
                <label for="titre" class="form-label">Nom</label>
                <input type="text" id="titre" class="form-control" name="nom" placeholder="" required maxlength="30"><br>
                
                <label for="desc" class="form-label">Description</label>
                <input type="text" class="form-control" id="desc" name="description" placeholder="" required><br>

                <label for="prix" class="form-label">Prix (en €)</label>
                <input type="number" step="0.01" min="0" id="prix" class="form-control" name="prix" placeholder="" required><br>
                
                
                <h4>Image</h4>
                
                <input type="radio" name="typeEntree" value="file" id="file" >
                <label for="file">Choisir un fichier</label>
                <br>

                <input type="file" name="fichier" accept="image/png, image/gif, image/jpeg" /><br>
                
                <br>
                <p><strong>OU</strong></p>
                
                <input type="radio" name="typeEntree" value="predef" id="predef" checked>
                <label for="predef">Choisir une image predéfinie</label>
                <br>
                <label for="image">Choisissez une image:</label>
                <select id="images" name="image">
                    $options
                </select>
                
                <input type="hidden" name="token" value="{$arg['liste']->token_edition}">
                <br><br>
                
                <button type="submit" class="btn btn-primary">
                    Créer l'item
                </button>
            </form>

END;

    }

    /**
     * Methode qui recupere le nom des images
     * @return array liste des images predefini
     * @author Marcus Richier
     */
    private function getImageEnregistree():array{
        $imgs = Item::query()->select('img')->where('img', 'not like', 'custom%')->groupBy("img")->get();
        $res = [];
        foreach ($imgs as $img){
            if ($img->img != 'no-image.png') //valeur reserve pour quand aucune image n'est fournie
                $res[] = $img->img;
        }
        return $res;
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


        //si la date d'échéance est pas passé
        if ($i->liste->expiration < date('YYYY-MM-DD')) {
            //la vision du propriétaire change

            //on check s'il est le propriétaire
            $estProprietaire = false;
            if (isset($_COOKIE['listeCree'])) {
                $a = unserialize($_COOKIE['listeCree']);

                foreach ($a as $k => $noListe) {
                    if ($k != 'exp')//on ne prend pas la date d'expiration
                        if ($noListe == $i->liste_id) $estProprietaire = true;
                }
            }

            //si il est propriétaire
            if ($estProprietaire || $args['edition'] || (isset($_SESSION['login']) && $_SESSION['login'] == $i->liste->login)) {

                //on change l'affichage de la reservation si c'est le propriétaire
                if ($i->reserverPar != null) $texteReservation = "L'item est réservé";
                else $texteReservation = "L'item n'est pas réservé";

                //sinon
            } else {

                if ($i->reserverPar == null) $texteReservation = <<<END
<p>L'item n'est pas encore reservé</p>
<form action="{$this->container->router->pathFor('reserverItem', ['token' => $args['token'], 'id' => $args['id']])}" method="get"> 
    <button type="submit" class="btn submit">Réserver l'item</button>
</form>
END;
                else $texteReservation = "L'item est reserver par : {$i->reserverPar}";
            }

            //si la date est passé
        } else {//l'affichage est le meme pour tous le monde
            if ($i->reserverPar != null) $texteReservation = "L'item étais reservé par {$i->reserverPar}";
            else $texteReservation = "L'item n'étais pas reservé";
        }

        //html
        return <<<END
            <div class="block-heading">
                <h2 class="text-info">Item: ${i['nom']}</h2>
            </div>
<div class="boite-item">
        <p>
            ${i['descr']}<br>
            prix : <strong>{$i['tarif']}</strong>
        </p>
        <img class="imgItem img-thumbnail" src="{$this->container->router->pathFor('accueil')}img/{$i['img']}"
        <br>
        <div class="reservation">
            $texteReservation
        </div>
        <form action="{$this->container->router->pathFor('afficherListe', ['token' => $args['token']])}" method="get"> 
            <button type="submit" class="btn btn-primary">Retourner à la liste</button>
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
            case 4:
            {
                $content = $this->htmlModifierUnItem($arg1);
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
                <input type="hidden" name="id" value="{$args['id']}" required><br>
                <label for="memoriser">Enregister mon nom pour les prochaines fois : </label>
                <input type="checkbox" name="memoriser"><br>
                <label for="message">Un message à ajouter ? </label>
                <input type="text" name="message" maxlength="50" size="50"><br>
                <button type="submit" class="btn btn-primary">
                    Réserver l'item
                </button>
            </form>
END;
        else $form = "<p>l'item est reserver par {$args['reserverPar']}</p>";


        //on renvoie la page complete
        return <<<END
            <div class="block-heading">
                        <h2 class="text-info">Reserver l'item "{$args['nom']}" ?</h2>
            </div>
        <div class="formulaire">
            $form
            <form action="{$this->container->router->pathFor('afficherItem', ['token' => $args['token'], 'id' => $args['id']])}" method="get"> 
                <button type="submit" class="btn btn-primary">Retourner sur la page de l'item</button>
            </form>
        </div>
END;
    }
    /**
     * Methode qui genere le contenu de la page de modification
     *
     * @author Fabrice Arnout
     */
    private function htmlModifierUnItem(array $args):string{
        $i = $args['item'];

        $html = <<<END
            <div class="block-heading">
                        <h2 class="text-info">Modifier l'item "{$i['nom']}" ?</h2>
            </div>        <div class="formulaire">
           <div class="formulaire">
            <form action="" method="post">
                <label for="nom" class="form-label">Modifier le nom</label>
                <input type="text" value=  "${i['nom']}" class="form-control" name="titre" placeholder="" required maxlength="22"><br>

                <label for="desc" class="form-label">Modifier la description</label>
                <input type="text" value="{$i['description']}" class="form-control" name="description" placeholder="" required><br>
                
                <label for="img" class="form-label">Modifier l'image</label>
                <input type="text" value="{$i['img']}" class="form-control" name="image" placeholder="" required><br> 
              
                <label for="prix" class="form-label">Modifier le prix</label>
                <input type="number" value="{$i['tarif']}" class="form-control" name="prix" placeholder="" required><br> 
                <input type="hidden" value="{$i['id']}" name="id">
                <input type="hidden" value="{$i['liste']->token_edition}" name="token">
                
                <button type="submit" class="btn btn-primary" >
                   Enregistrer les modifications
                </button>
            </form>
        </div>    
END;


        return $html;
    }

}
