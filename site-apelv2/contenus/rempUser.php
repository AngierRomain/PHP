<?php
/** Doit gérer le contenu pour un menu donné, soit globalement, soit en mise à jour */
class Remplisseur{
    public function __construct()
    {
		require_once 'poo/utilisateur.php';
    }
    //$codeMenu=null peut servir en PPE
    /** Doit afficher l'intégralité du contenu @$raison : peut être null */
    public function fullDisplay($raison = null){
		require_once 'vues/vutilisateur.php';
		$ALLUSR = Utilisateurs::makeFromBDD();
		$VUEUSR = new VueUtilisateur($ALLUSR);
		
		$TAB = new Balise('table');
		$TAB->addClass('vueusr', 'alternate');
		$TAB->add('<caption>Liste des utilisateurs</caption>');
		$TAB->add('<thead><tr><th>N°</th>',
			'<th>Code</th><th>Prenom</th>',
			'<th>Nom</th><th>Civilite</th><th>Date de Naissance</th>',
			'<th>Adresse</th><th>Telephone</th><th>Email</th><th>Nature</th></thead>');
		$TAB->add($VUEUSR->getDomTbody('CPT;COD;PNM;NOM;CIV;DTN;ADR;TEL;MAI;NAT', true));

		$JOB = new Balise('div');
		$JOB->setID('job1');
		$JOB->add($TAB);
		$JOB->add('<button onclick="gestu_Nouveau()">Ajouter</button><br/>');
		//$JOB->display(true);
		
		/*
			Formulaire d'ajout, modif, supression
		*/
		$DIV = new Balise('div');
		$DIV->addClass('saisie masked');
		$DIV->setID('saisieuser');
		$FORM = new Balise('form');
		$FORM->addAttribut('name', 'frmuser');
		
		$INPUT = new Balise('input');
		$INPUT->addAttribut('name', 'oldid');
		$INPUT->addAttribut('type', 'hidden');
		$INPUT->addAttribut('value', '0');
		$FORM->add($INPUT);
		
		$DELETE = new Balise('input');
		$DELETE->addAttribut('name', 'suppr');
		$DELETE->addAttribut('type', 'hidden');
		$DELETE->addAttribut('value', '0');
		$FORM->add($DELETE);
		
		
		$TR = '<tr class="truser">'; $NTR = '</tr>';
		$TD = '<td>'; $NTD = '</td>';
		
		$TABLE = new Balise('table');
		$TABLE->add('<caption id="titresaisie">Saisie Utilisateur</caption>');	
		$TABLE->add($TR);	
			$TABLE->add('<td><input type="checkbox" id="valide" name="valide"></td>');
			$TABLE->add('<td><label for="valide">Compte validé</label></td>');
			$TABLE->add('<td></td><td></td><td></td><td>Code');
			$TABLE->add('<input type="text" id="code" maxlength="3" placeholder="3 caractères"required></td>');
		$TABLE->add($NTR);
		$TABLE->add($TR);	
			$TABLE->add('<td><label>Civilite</label></td>');
			$TABLE->add($TD);	
			$SELECT = new Balise('select');
			$SELECT->addAttribut('name', 'civilite');
			$SELECT->add('<option value="1">Monsieur</option>');
			$SELECT->add('<option value="2">Madame</option>');
			$SELECT->add('<option value="3">Autre</option>');	
			$TABLE->add($SELECT);
			$TABLE->add($NTD);
		$TABLE->add($NTR);
		$TABLE->add($TR);
			$TABLE->add('<td>Nom</td>');
			$TABLE->add('<td><input type="text" id="nom" maxlength="64" required></td>');
			$TABLE->add('<td>Prenom</td>');
			$TABLE->add('<td><input type="text" id="prenom" maxlength="64" required></td>');
			$TABLE->add('<td>Date de naissance</td>');
			$TABLE->add('<td><input type="date" id="daten" required></td>');
		$TABLE->add($NTR);
		$TABLE->add($TR);
			$TABLE->add('<td>Adresse</td>');
			$TABLE->add('<td><input type="text" id="adresse" maxlength="255" required></td>');
			$TABLE->add('<td>Numero de teléphone</td>');
			$TABLE->add('<td><input type="text" id="tel" maxlength="10" placeholder="Numéro de 10 chiffres" required></td>');
			$TABLE->add('<td>Email</td>');
			$TABLE->add('<td><input type="email" id="mail" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" maxlength="255" placeholder="exemple@orange.fr" required></td>');
		$TABLE->add($NTR);
		$TABLE->add($TR);
			$TABLE->add('<td>Login</td>');
			$TABLE->add('<td><input type="text" id="login" maxlength="64" required></td>');
			$TABLE->add('<td>Mot de passe</td>');
			$TABLE->add('<td><input type="text" id="pass" maxlength="32" required></td>');
		$TABLE->add($NTR);
		$TABLE->add($TR);
				$TABLE->add('<td colspan="3"><h3 id="statut">Statut de l\'utilisateur</h3></td>');	
		$TABLE->add($NTR);
		$TABLE->add($TR);
			$TABLE->add('<td><input type="checkbox" id="iparent" name="iparent"></td>');
			$TABLE->add('<td><label for="iparent">Parent</label></td>');
		$TABLE->add($NTR);
		$TABLE->add($TR);	
			$TABLE->add('<td><input type="checkbox" id="adherent" name="adherent"></td>');
			$TABLE->add('<td><label for="adherent">Adherent</label></td>');
		$TABLE->add($NTR);
		$TABLE->add($TR);	
			$TABLE->add('<td><input type="checkbox" id="membre" name="membre"></td>');
			$TABLE->add('<td><label for="membre">Membre du bureau</label></td>');
		$TABLE->add($NTR);
		$FORM->add($TABLE);

		$BUTDELETE = new Balise('span');
		$BUTDELETE->setID('deleteuser');
		$BUTDELETE->addClass('bouton masked');
		$BUTDELETE->addAttribut('onclick', 'gestu_Supprimer();');
		$BUTDELETE->add('Supprimer');
		
		$BUTSAVE = new Balise('span');
		$BUTSAVE->setID('adduser');
		$BUTSAVE->addClass('bouton');
		$BUTSAVE->addAttribut('onclick', 'gestu_Save();');
		$BUTSAVE->add('Enregistrer');
		
		$BUTCANCEL = new Balise('span');
		$BUTCANCEL->setID('annul');
		$BUTCANCEL->addClass('bouton');
		$BUTCANCEL->addAttribut('onclick', 'saisieuser.classList.add(\'masked\');');
		$BUTCANCEL->add('Annuler');
		
		//$FORM->add($TR);
		//$FORM->add('<td><span class="bouton" onclick="gestu_Save();">Enregistrer</span></td>');
		//$FORM->add('<td><span class="bouton" onclick="saisieuser.classList.add(\'masked\')">Annuler</span></td>');
		$FORM->add($BUTCANCEL);
		$FORM->add($BUTSAVE);
		$FORM->add($BUTDELETE);
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
		\"></img>";
    }

    /** Doit mettre à jour le contenu en fonction de l'opération ($demande) et des données complémentaires (dans $datas) */
    public function actualiser($demande, array $datas){
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
		\"></img>";
		
    }
}


?>