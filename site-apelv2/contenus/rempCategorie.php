<?php
/** Romain ANGIER */

/** Doit gérer le contenu pour un menu donné, soit globalement, soit en mise à jour */
class Remplisseur{
    public function __construct()
    {
        require_once 'poo/categorie.php';
    }

    /** Doit afficher l'intégralité du contenu @$raison : peut être null */
    public function fullDisplay($raison = null){
        require_once 'vues/vCategorie.php';
        $ALLCAT = Categories::makeFromBDD();
        $VUECAT = new VueCategorie($ALLCAT);

        $TAB = new Balise('table');
        $TAB->addClass('vuecat', 'alternate');
        $TAB->add('<caption>Liste des catégories</caption>');
        $TAB->add('<thead><tr><th>Code</th>',
            '<th>Libellé</th>');
        $TAB->add($VUECAT->getDomTbody('ID;L', true));

        $JOB = new Balise('div');
        $JOB->setID('job1');
        $JOB->add($TAB);
        $JOB->add('<button onclick="gestc_Nouveau()">Ajouter catégorie</button><br/>');
        //$JOB->display(true);

        /*
            Formulaire d'ajout, modif, supression
        */
        $DIV = new Balise('div');
        $DIV->addClass('saisie masked');
        $DIV->setID('saisiecategorie');
        $FORM = new Balise('form');
        $FORM->addAttribut('name', 'frmcategorie');

        $FORM->add('<input name="oldid" type="hidden" value="0">');
        // $INPUT = new Balise('input');
        // $INPUT->addAttribut('name', 'oldid');
        //$INPUT->addAttribut('type', 'hidden');
        //$INPUT->addAttribut('value', '0');
        //$FORM->add($INPUT);


        $TR = '<tr>'; $NTR = '</tr>';
        $TD = '<td>'; $NTD = '</td>';

        $TABLE = new Balise('table');
        $TABLE->add('<caption id="titresaisie">Saisie Catégorie</caption>');

        $TABLE->add($TR);
        $TABLE->add('<td>Code</td>');
        $TABLE->add('<td><input type="text" id="codecat"></td>');
        $TABLE->add($NTR);

        $TABLE->add($TR);
        $TABLE->add('<td>Libelle</td>');
        $TABLE->add('<td><input type="text" id="libelle"></td>');
        $TABLE->add($NTR);

        $FORM->add($TABLE);

        //$FORM->add($TR);
        $FORM->add('<span class="bouton" onclick="gestc_Save();">Enregistrer</span>');
        $FORM->add('<span class="bouton" onclick="gestc_Supprimer();">Supprimer</span>');
        $FORM->add('<span class="bouton" onclick="saisiecategorie.classList.add(\'masked\')">Annuler</span>');
        //$FORM->add($NTR);

        $DIV->add($FORM);
        //$DIV->display();
        $JOB->add($DIV);
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