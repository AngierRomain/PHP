<!-- Page PHP Unique -->
<h3>Bienvenue sur le site de l'APEL Champagnat !</h3>
<?php
require_once 'poo/si.php';
require_once 'poo/imetier.php';
require_once 'poo/element.php';
require_once 'poo/elements.php';
//require_once 'poo/tools/memo.php';
require_once 'outils/balise.php';
require_once 'outils/controleur.php';

// Objet SI qui prend le controleur et le démarre (si.php)
SI::getSI()->getControleur()->DEMARRER();

?>