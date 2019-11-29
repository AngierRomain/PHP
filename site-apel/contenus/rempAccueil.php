<h2>Coucou depuis rempAccueil</h2>
<?php

class Remplisseur{
    public function __construct()
    {
    }

    public function fullDisplay($raison = null){
        require_once 'views/vAccueil.php';
        echo '<em><b>Chers Parents, nous vous souhaitons la bienvenue sur le site de l\'Apel (Association de Parents d\'élèves de l\'Enseignement Libre) de l\'Institution Champagnat. <br>
                 Cet espace d\'échange a pour vocation de vous informer de l’actualité de votre Apel d\'Etablissement mais également de vous permettre de commander les fournitures de vos enfants via notre formulaire en ligne.</b></em><br>';

        echo '<br><em>Actuellement nous sommes le </em>' . date("d-m-Y");
    }

    /** Doit mettre à jour le contenu en fonction de l'opération ($demande) et des données complémentaires (dans $datas) */
    public function actualiser($demande, array $datas){

    }
}