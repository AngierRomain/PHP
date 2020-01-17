<?php
/** Doit gérer le contenu pour un menu donné, soit globalement, soit en mise à jour */
class Remplisseur{
    public function __construct()
    {

    }
    //$codeMenu=null peut servir en PPE
    /** Doit afficher l'intégralité du contenu @$raison : peut être null */
    public function fullDisplay($raison = null){
		
		$JOB = new Balise('div');
		$JOB->setID('job1');
		$JOB->add('<em><b>Chers Parents, nous vous souhaitons la bienvenue sur le site de l\'Apel (Association des Parents d\'élèves de l\'Enseignement Libre) de l\'Institution Champagnat. <br>
                 Cet espace d\'échange a pour vocation de vous informer de l’actualité de votre Apel d\'Etablissement mais également de vous permettre de commander les fournitures de vos enfants via notre formulaire en ligne.</b></em><br>
                 <br><em>Actuellement nous sommes le </em>' . date("d-m-Y") . ' <br><br><em>Un problème, une question ? Contactez nous par mail à apel@champagnat.org</em>');
		$JOB->add();
		$JOB->display(true);
		
		echo "<img src=\"images/empty.gif\" alt=\"pas d'image\" 
		onload=\"
		console.log ('image chargée');
		var work = document.getElementById('job1');
		work.parentNode.removeChild(work);
		details.innerHTML = work.innerHTML;
		//console.log ('Le contenu de détails a été remplacé');
		this.parentNode.removeChild(this);
		divtravail.innerHTML='';
		\"></img>";
    }

    /** Doit mettre à jour le contenu en fonction de l'opération ($demande) et des données complémentaires (dans $datas) */
    public function actualiser($demande, array $datas){
		//echo '<span class="bouton" onclick="">Déconnexion</span>';
		echo "<img src=\"images/empty.gif\" alt=\"pas d'image\" 
		onload=\"
		console.log ('image chargée');
		var work = document.getElementById('job1');
		work.parentNode.removeChild(work);
		details.innerHTML = work.innerHTML;
		//console.log ('Le contenu de détails a été remplacé');
		this.parentNode.removeChild(this);
		divtravail.innerHTML='';
		\"></img>";
    }
}


?>