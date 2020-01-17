<?php
/** Romain ANGIER */

/** Doit gérer le contenu pour un menu donné, soit globalement, soit en mise à jour */
class Remplisseur{
    public function __construct()
    {
        require_once 'poo/commande.php';
        require_once 'poo/fourniture.php';
    }
    //$codeMenu=null peut servir en PPE
    /** Doit afficher l'intégralité du contenu @$raison : peut être null */
    public function fullDisplay($raison = null){
        require_once 'vues/vCommande.php';
        $ALLFOUR = Fournitures::makeFromBDD();
        $VUECOM = new VueCommande($ALLFOUR);
        $TAB = new Balise('table');
        $TAB->addClass('vuefour', 'alternate');
        $TAB->add('<caption>Effectuer une commande</caption>');
        $TAB->add('<thead><tr>',
            '<th>Libellé</th>',
            '<th>Prix Unitaire</th><th>Quantité</th></thead>');
        $TAB->add($VUECOM->getDomTbody('L;P;Q', true));

        $JOB = new Balise('div');
        $JOB->setID('job1');
        $JOB->add($TAB);
        $JOB->display();

        echo "<img src=\"images/empty.gif\" alt=\"pas d'image\"
		onload=\"
		console.log ('image chargée');
		var work = document.getElementById('job1');
		work.parentNode.removeChild(work);
		details.innerHTML = work.innerHTML;
		//console.log ('Le contenu de détails a été remplacé');
		this.parentNode.removeChild(this);
		//divtravail.innerHTML='';
		\"/>";
    }

    /** Doit mettre à jour le contenu en fonction de l'opération ($demande) et des données complémentaires (dans $datas) */
    public function actualiser($demande, array $datas){
        //echo '<span class="bouton" onclick="">Déconnexion</span>';

        echo '<br/>Dans actualisation';
        echo '<br/> demande : ', $demande;
        var_dump($datas);

        echo "<img src=\"images/empty.gif\" alt=\"pas d'image\"
		onload=\"
		console.log ('image chargée');
		var work = document.getElementById('job1');
		work.parentNode.removeChild(work);
		details.innerHTML = work.innerHTML;
		//console.log ('Le contenu de détails a été remplacé');
		this.parentNode.removeChild(this);
		//divtravail.innerHTML='';
		\"/>";
    }
}


?>