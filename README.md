# Projet MyWishList
Ce projet est un site web qui permet de créer et de partager des wishlists, codés en php avec Slim et Eloquent.
Voici les instructions pour pouvoir installer notre projet « MyWishList » sur votre ordinateur personnel.

1°) Installer Xampp sur sa machine personnelle :
Xampp est un logiciel qui nous permettra de simuler un serveur web Apache2 ainsi qu’une base de données sur notre machine. De nombreux tutoriels sont présents en ligne.
Cette étape n’est pas nécessaire si vous utilisez un serveur dédié (exemple: webetu fourni par le dut dans notre cas)

2°)Composer 
Composer est un autoloader, il permet aux fichiers php de se charger sans faire de require_once à chaque fois.
Pour l’installer il faut télécharger composer par ce lien : https://getcomposer.org/ et après cela il faudra paramétrer le fichier composer.json. Puis, il faudra faire la commande composer install.
Notre autoloader est donc prêt, il faut juste le charger une seule fois dans un fichier php, pour que l’autoloader soit appliqué à notre projet.

3°) Fichier dbconfig.ini
Après avoir installé l’autoloader, il faut créer le fichier dbconfig.ini, qui permet de faire le lien entre la base de données et votre serveur web virtuel.
Il faut donc accéder au fichier dbconfig.ini.txt qui est dans ce répertoire “src/config/”, en créer une copie du nom de “dbconfig.ini” et d’y insérer les informations de votre base de données.


4°) Fichier .htaccess
Vérifiez bien que votre fichier .htaccess est bien présent dans votre projet.
Si votre projet n’est pas dans la racine de votre serveur, veuillez décommenter la ligne :
"# RewriteBase /www/username0/mywishlist"
et veuillez remplacer “/www/username0/mywishlist” par le chemin d’accès relatif de votre projet sur votre serveur.

5°) Jeu de données à éxécuter
Pour créer la base de données MySQL, veuillez exécuter le fichier DocumentSQL.sql dans votre base de données. Cela vous permettra de pourvoir le projet avec un jeu de données.

Auteurs :
WEISS Lucas
VINOT Mathieu
RENARD Guillaume
ARNOUT Fabrice
RICHIER Marcus