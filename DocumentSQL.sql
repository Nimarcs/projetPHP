SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liste_id` int(11) NOT NULL,
  `nom` text NOT NULL,
  `descr` text,
  `img` text,
  `url` text,
  `tarif` decimal(5,2) DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `reserverPar` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `item` (`id`, `liste_id`, `nom`, `descr`, `img`, `url`, `tarif`) VALUES
(1,	2,	'Champagne',	'Bouteille de champagne + flutes + jeux à gratter',	'champagne.jpg',	'',	20.00),
(2,	2,	'Musique',	'Partitions de piano à 4 mains',	'musique.jpg',	'',	25.00),
(3,	2,	'Exposition',	'Visite guidée de l’exposition ‘REGARDER’ à la galerie Poirel',	'poirelregarder.jpg',	'',	14.00),
(4,	3,	'Goûter',	'Goûter au FIFNL',	'gouter.jpg',	'',	20.00),
(5,	3,	'Projection',	'Projection courts-métrages au FIFNL',	'film.jpg',	'',	10.00),
(6,	2,	'Bouquet',	'Bouquet de roses et Mots de Marion Renaud',	'rose.jpg',	'',	16.00),
(7,	2,	'Diner Stanislas',	'Diner à La Table du Bon Roi Stanislas (Apéritif /Entrée / Plat / Vin / Dessert / Café / Digestif)',	'bonroi.jpg',	'',	60.00),
(8,	3,	'Origami',	'Baguettes magiques en Origami en buvant un thé',	'origami.jpg',	'',	12.00),
(9,	3,	'Livres',	'Livre bricolage avec petits-enfants + Roman',	'bricolage.jpg',	'',	24.00),
(10,	2,	'Diner  Grand Rue ',	'Diner au Grand’Ru(e) (Apéritif / Entrée / Plat / Vin / Dessert / Café)',	'grandrue.jpg',	'',	59.00),
(11,	0,	'Visite guidée',	'Visite guidée personnalisée de Saint-Epvre jusqu’à Stanislas',	'place.jpg',	'',	11.00),
(12,	2,	'Bijoux',	'Bijoux de manteau + Sous-verre pochette de disque + Lait après-soleil',	'bijoux.jpg',	'',	29.00),
(19,	0,	'Jeu contacts',	'Jeu pour échange de contacts',	'contact.png',	'',	5.00),
(22,	0,	'Concert',	'Un concert à Nancy',	'concert.jpg',	'',	17.00),
(23,	1,	'Appart Hotel',	'Appart’hôtel Coeur de Ville, en plein centre-ville',	'apparthotel.jpg',	'',	56.00),
(24,	2,	'Hôtel d\'Haussonville',	'Hôtel d\'Haussonville, au coeur de la Vieille ville à deux pas de la place Stanislas',	'hotel_haussonville_logo.jpg',	'',	169.00),
(25,	1,	'Boite de nuit',	'Discothèque, Boîte tendance avec des soirées à thème & DJ invités',	'boitedenuit.jpg',	'',	32.00),
(26,	1,	'Planètes Laser',	'Laser game : Gilet électronique et pistolet laser comme matériel, vous voilà équipé.',	'laser.jpg',	'',	15.00),
(27,	1,	'Fort Aventure',	'Découvrez Fort Aventure à Bainville-sur-Madon, un site Accropierre unique en Lorraine ! Des Parcours Acrobatiques pour petits et grands, Jeu Mission Aventure, Crypte de Crapahute, Tyrolienne, Saut à l\'élastique inversé, Toboggan géant... et bien plus encore.',	'fort.jpg',	'',	25.00);

DROP TABLE IF EXISTS `liste`;
CREATE TABLE `liste` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `titre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `expiration` date DEFAULT NULL,
  `public` int(1) DEFAULT 0,
  `token_lecture` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token_edition` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `liste` (`no`, `login`, `titre`, `description`, `expiration`, `token_lecture`, `token_edition`) VALUES
(1,	'guenuego',	'Pour fêter le bac !',	'Pour un week-end à Nancy qui nous fera oublier les épreuves. ',	'2018-06-27',	'a0b9c57b13aa1a45f2746e8fc8542096b8bad7f0395484f05150a4fce6af3257', '79c018f107985991e70f2a0e078771484265bd5e1aa73fd1fc60091abfedac88'),
(2,	'guenuego',	'Liste de mariage d\'Alice et Bob',	'Nous souhaitons passer un week-end royal à Nancy pour notre lune de miel :)',	'2018-06-30',	'421fa716b36044f4e7fe772b93b8089bb5450149e047bf6f343e5f127afa941f','adf894da18bf1935c588c096ada5e06385dcc0ea7e8015a38bfdaf111a560fcc'),
(3,	'guenuego',	'C\'est l\'anniversaire de Charlie',	'Pour lui préparer une fête dont il se souviendra :)',	'2017-12-12',	'e789e789acce2dd749c82b7e004ec40734959e5813f1de8043d4ea67844c342c', '17aec91139923117948b66e2ee0218f02f419d809fafb92ec3bde0ff76a4724e');

DROP TABLE IF EXISTS `compte`;
CREATE TABLE `compte` (
                          `login` varchar(50),
                          `sel` varchar(256),
                          PRIMARY KEY (`login`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
