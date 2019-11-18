<h2>Coucou depuis rempAccueil</h2>
<?php

class Remplisseur{
    public function __construct()
    {
    }

    public function fullDisplay($raison = null){
        require_once 'views/vAccueil.php';
    }

}