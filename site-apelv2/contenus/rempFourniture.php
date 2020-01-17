<?php
/** Romain ANGIER */

/** Doit gérer le contenu pour un menu donné, soit globalement, soit en mise à jour */
class Remplisseur{
    public function __construct()
    {
        require_once 'poo/fourniture.php';
        require_once 'poo/categorie.php';
    }
    //$codeMenu=null peut servir en PPE
    /** Doit afficher l'intégralité du contenu @$raison : peut être null */
    public function fullDisplay($raison = null){
        require_once 'vues/vFourniture.php';
        $ALLFOUR = Fournitures::makeFromBDD();
        $ALLCATEG = Categories::makeFromBDD();
        $VUEFOUR = new VueFourniture($ALLFOUR);
        $TAB = new Balise('table');
        $TAB->addClass('vuefour', 'alternate');
        $TAB->add('<caption>Liste des fournitures</caption>');
        $TAB->add('<thead><tr><th>Code</th>',
            '<th>Catégorie</th><th>Libellé</th>',
            '<th>Prix Unitaire</th><th>Quantité</th></thead>');
        $TAB->add($VUEFOUR->getDomTbody('ID;CATEG;L;P;Q', true));

        $JOB = new Balise('div');
        $JOB->setID('job1');
        $JOB->add($TAB);
        $JOB->add('<button onclick="gestf_Nouveau()">Ajouter fourniture</button><br/>');
        //$JOB->display(true);

        /*
            Formulaire d'ajout, modif, supression
        */
        $DIV = new Balise('div');
        $DIV->addClass('saisie masked');
        $DIV->setID('saisiefourniture');
        $FORM = new Balise('form');
        $FORM->addAttribut('name', 'frmfourniture');

        $FORM->add('<input name="oldid" type="hidden" value="0">');
        // $INPUT = new Balise('input');
        // $INPUT->addAttribut('name', 'oldid');
        //$INPUT->addAttribut('type', 'hidden');
        //$INPUT->addAttribut('value', '0');
        //$FORM->add($INPUT);


        $TR = '<tr>'; $NTR = '</tr>';
        $TD = '<td>'; $NTD = '</td>';

        $TABLE = new Balise('table');
        $TABLE->add('<caption id="titresaisie">Saisie Fourniture</caption>');

        $TABLE->add($TR);
        $TABLE->add('<td>Code</td>');
        $TABLE->add('<td><input type="text" id="code"></td>');
        $TABLE->add($NTR);

        $TABLE->add($TR);
        $TABLE->add('<td>Categorie</td>');
        $SELECT = new Balise('select');
        $SELECT->addAttribut('name', 'categ');
//      $SELECT->add('<option value="1">Cahiers</option>');
//      $SELECT->add('<option value="2">Classeurs</option>');
//      $SELECT->add('<option value="3">Stylos</option>');
        $i = 1;
        foreach ($ALLCATEG as $value){
            $SELECT->add('<option value="'.$i.'">'. $value->getLibelle() .'</option>');
            $i++;
            //echo $value->getLibelle();
        }
        $TABLE->add('<td>',$SELECT, '</td>');
        $TABLE->add($NTR);

        $TABLE->add($TR);
        $TABLE->add('<td>Libelle</td>');
        $TABLE->add('<td><input type="text" id="libelle"></td>');
        $TABLE->add($NTR);

        $TABLE->add($TR);
        $TABLE->add('<td>Prix Unitaire</td>');
        $TABLE->add('<td><input type="text" id="prixunit"></td>');
        $TABLE->add($NTR);

        $TABLE->add($TR);
        $TABLE->add('<td>Quantite</td>');
        $TABLE->add('<td><input type="text" id="quantite"></td>');
        $TABLE->add($NTR);

        $FORM->add($TABLE);

        //$FORM->add($TR);
        $FORM->add('<span class="bouton" onclick="gestf_Save();">Enregistrer</span>');
        $FORM->add('<span class="bouton" onclick="gestf_Supprimer();">Supprimer</span>');
        $FORM->add('<span class="bouton" onclick="saisiefourniture.classList.add(\'masked\')">Annuler</span>');
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