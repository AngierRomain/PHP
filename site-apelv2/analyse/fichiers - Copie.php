<?php
	function joliEntier($n) {
		$m = $n;
		$tmp = '';
		$l = strlen($n);
		while ($l>3) {
			$l-=3;
			$tmp = ' ' . substr($m, $l,3) . $tmp;
		}
		$tmp = substr($m, 0, $l) . $tmp;
		return $tmp;
	}

	abstract class pgItem implements JsonSerializable {
		public $nom;
		public $nomcourt;
		public $ID;
		public function __construct($theNom, $theNomCourt) {
			$this->nom = $theNom ;
			$this->nomcourt = $theNomCourt ;
		}	
		
		public static function comparer(pgItem $o1, pgItem $o2) {
			if ($o1==$o2) {return 0;}
			$n1 = $o1->nomcourt;
			$n2 = $o2->nomcourt;
			return strcasecmp ( $n1 , $n2 );
		} 
		public function jsonSerialize() {
			return
				  ['CLASS' => get_class($this),
					 'id' => $this->ID,
					 'nom' => utf8_encode($this->nom),
					 'nomcourt' => utf8_encode($this->nomcourt)];
		}
	}
	
	class pgFichier extends pgItem {
		public $nbl;
		public function __construct($theNom, $theNomCourt) {
			parent::__construct($theNom, $theNomCourt);
			
		$this->nbl = filesize($theNom);
		//$fp=fopen($theNom,"r");
		//$start=microtime(true);
		//$cpt = 0;
      //  //while($line=stream_get_line($fp,65535)) {
      //  //        $cpt++;
      //  //}
		//while($line=fgets($fp,65535)) {
		//$cpt++;
      //}
		//$end=microtime(true);
		//fclose($fp);
		//$this->nbl = $cpt;
		
		// filemtime(filanme) : int date last modif
		}
		function displayLi(&$nb) {
				$nb++;
				$monId = 'F'.$nb;
				$this->ID = $monId;
				echo '<li class="fichier" id="'.$monId.'" onclick="clickOnFichier(this);"><span>', $this->nomcourt, ' (', joliEntier($this->nbl) ,' o)</span><span></span></li>';}
				
		public function jsonSerialize() {
			$rep = parent::jsonSerialize();
			$infof = stat($this->nom);

			$rep ['taille'] = $infof['7'];
			$rep ['dtcreat'] = Date('Y-m-d H:i:s',$infof['8']);
			$rep ['dtmodif'] = Date('Y-m-d H:i:s',$infof['9']);
			$rep ['dtacces'] = Date('Y-m-d H:i:s',$infof['10']);
			$rep ['kname'] = md5($this->nom) ;
			$rep ['kfile'] = md5_file($this->nom) ;
			return $rep ;
		}				
	}
	
	class pgDossier extends pgItem {
		public $fichiers;
		public $dossiers;
		public function __construct($theNom, $theNomCourt, $inclure=true) {
			parent::__construct($theNom, $theNomCourt);
			$this->fichiers = array();
			$this->dossiers = array();	
			if ($handle = opendir($theNom)) {
				while (false !== ($entry = readdir($handle))) {
					switch ($entry) {
						case '.':
						case '..':break;
						default:
							$tmp = $theNom.'\\'.$entry;
							if (is_dir($tmp)) {
								$unDoss = new pgDossier($tmp, $entry);
								if ($unDoss->yaDesFichiers()) {
									$this->dossiers[] = $unDoss;
								}
							} else {
								if ($inclure && substr(strrev($entry),0,4)=='php.') {
									$this->fichiers[] = new pgFichier($tmp, $entry);
								}
							}
					}
				}
				closedir($handle);
			}				
		}
	
		public function yaDesFichiers() {
			//return true;
			if (count($this->fichiers)>0) {return true;}
			foreach ($this->dossiers as $D) {
				if ($D->yaDesFichiers()) {return true;}
			}
			return false;
		} 

		public function nbFichiers () {
			$cpt = count($this->fichiers);
			foreach ($this->dossiers as $D) {
				$cpt += $D->nbFichiers();
			}
			return $cpt;
		}
		public function jsonSerialize() {
			$rep = parent::jsonSerialize();
			$info = [
					'fichiers' => $this->fichiers,
					'dossiers' => $this->dossiers];
			return array_merge($rep, $info) ;
		}
		
		public function displayUl(&$nb) {
			$nb++;
			$key = 'D'.$nb;
			$this->ID = $key;
			$monId = 'LI' . $key;
			if ($this->nomcourt!='.') {
			echo '<li class="dossier"', 
					' id="', $monId,'">', 
					'<span onclick="clickOnDossier(',"'", $key,"'",');">', 
					$this->nomcourt, ' (', $this->nbFichiers(),')</span>' ; 
			}
			
			echo '<ul id="'.$key.'">' ;
			foreach ($this->dossiers as $D) {
				if ($D->yaDesFichiers()) {$D->displayUl($nb);}
				
			}
			foreach ($this->fichiers as $D) {
				$D->displayLi($nb);
			}
			echo '</ul>'; 
			if ($this->nomcourt!='.') {
			echo '</li>';
			}
		}
	}
	class pgAnalyse {
		private $fichiers;
		private $rep;
		public $o_Dossier;
		
		function balayage($dossier, $prof, $inclure=true) {
			if ($handle = opendir($dossier)) {
				$decalage = str_repeat("\t", $prof);
				//echo $decalage,"Dossier : $dossier\n";
				$prof+=1;
				$decalage = str_repeat("\t", $prof);
				/* Ceci est la façon correcte de traverser un dossier. */
				while (false !== ($entry = readdir($handle))) {
					switch ($entry) {
						case '.':
						case '..':break;
						default:
							$tmp = $dossier.'\\'.$entry;
							if (is_dir($tmp)) {
								$this->balayage ($tmp, $prof);
							} else {
								if ($inclure && substr(strrev($entry),0,4)=='php.') {
									//echo "$decalage$entry\n";
									//try {
										require_once $tmp;
									//} catch (Exception $e) {
									//   echo $e;
									//   halt();
									//}
								}
							}
					}
				}
				closedir($handle);
			}		
		}
		
		function listage($dossier, $shortname, $prof, $inclure=true) {
			if ($handle = opendir($dossier)) {
				$decalage = str_repeat("\t", $prof);
				//echo '<tr class="dossier"><td><pre>', 
				//			$decalage , $shortname,'</pre></td></tr>';
				echo '<ul class="dossier">', $shortname;
							//$decalage , $shortname,'</pre></td></tr>';							
				$prof+=1;
				$decalage = str_repeat("\t", $prof);
				/* Ceci est la façon correcte de traverser un dossier. */
				while (false !== ($entry = readdir($handle))) {
					switch ($entry) {
						case '.':
						case '..':break;
						default:
							$tmp = $dossier.'\\'.$entry;
							if (is_dir($tmp)) {
								echo '<li class="dossier">';
								$this->listage ($tmp, $entry, $prof);
								echo '</li>';
							} else {
								if ($inclure && substr(strrev($entry),0,4)=='php.') {
							//		echo '<tr class="fichier"><td><pre>', 
							//$decalage , $entry,'</pre></td></tr>';
									echo '<li class="fichier">',$entry, '</li>';
									//echo "$decalage$entry\n";
									//try {
									//	require_once $tmp;
									//} catch (Exception $e) {
									//   echo $e;
									//   halt();
									//}
								}
							}
					}
				}
				echo '</ul>';
				closedir($handle);
			}		
		}
		
		public function presenterFichiers() {
			//echo '<table>';
			$this->listage($this->rep, '.',0,true);
			
			//echo '</table>';
		}
		public function __construct($repDeBase = null) {
			$this->fichiers = array();
			if ($repDeBase==null) {$this->rep = getcwd();} else {$this->rep = $repDeBase;}
			
			$this->o_Dossier = new pgDossier($this->rep, '.');
		}
	}
	//ob_start();
	//$obj = new pgAnalyse();
	//ob_clean();
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv = "content-type" content = "text/html; charset=utf-8" />
<title>PG - Analyse PHP</title>
<style>
.unselectab {
  -webkit-user-select: none;  
  -moz-user-select: none;    
  -ms-user-select: none;      
  user-select: none;
}
.dossier > span {
	border:1px black solid;
	border-radius:7px;
	margin-left:20px; 
	font-weight:bold; 
	cursor:pointer; 
	background-color:#00FF00;
	padding:0px 6px;

}
.fichier > span {
	margin-left:20px; 
	font-style:italic; 
}
ul.masked {visibility:collapse; height:0px; opacity:0;}
li.masked > span {background-color:orange;}


ul {list-style-type: none;}
li.dossier
{
	background: url('data:image/svg+xml;charset=utf-8,<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 16 16" enable-background="new 0 0 16 16" xml:space="preserve"><g id="Shape_1_copy"><g><path fill="%23B3B4B4" d="M6,1H2C1.4,1,0,1.4,0,2c0,0.6,1.4,1,2,1h4c0.6,0,2-0.4,2-1C8,1.4,6.6,1,6,1z"/></g></g><g id="Shape_2_copy"><g><path fill="%23B3B4B4" d="M14,2H0v11c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2V4C16,2.9,15.1,2,14,2z"/></g></g></svg>') 0px 4px no-repeat;
}
li.fichselect {color:blue; font-weight:bold;}
li.fichier
{
	background: url('data:image/svg+xml;charset=utf-8,<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 16 16" enable-background="new 0 0 16 16" xml:space="preserve"><g id="Shape_25"><g><path fill="%23B3B4B4" d="M10,4V0H2v16h12V4H10z M3,3h6v1H3V3z M13,14H3v-1h10V14z M13,12H3v-1h10V12z M13,10H3V9h10V10z M13,8H3V7h10V8z M13,6H3V5h10V6z"/></g></g><g id="Shape_26"><g><polygon fill="%23B3B4B4" points="11,3 14,3 11,0"/></g></g></svg>') 0px 4px no-repeat;
}
.fichierchoisi {
	draggable:true;
	color:blue;
}
ol {list-style-type: custom-counter-style; counter-reset: choisi;}
ol li {
	display:block; 
	counter-increment: choisi; 
	margin-right:7px;  
	text-indent: -2.2em;

}
ol li:before {
	content: counter(choisi); /* on affiche le compteur */
	padding: 0px 8px 3px;
	margin-right: 6px;
	vertical-align: middle;
	background: #00FF00;
	-moz-border-radius: 60px;
	border-radius: 60px;
	font-weight: bold;
	font-size: 0.8em;
}

#divsel {
	position:fixed;
	border:1px black solid;
	right:15px;
	top:30px;
	background-color:white;
	color:black;
	min-width:160px;
	min-height:100px;
}


</style>
<script>
var myManager = null;

function afficherException(e) {
	alert('ERREUR\n' + e.fileName + '\nLIGNE N° ' + e.lineNumber + '\n' + e.message );
}

class Element {
	constructor(objJSON) {
		this.JSON = objJSON;
	}
	get ID() 		{return this.JSON.id;}
	get Nom()		{return this.JSON.nom;}
	get NomCourt()	{return this.JSON.nomcourt;}
}

class Fichier extends Element {
	constructor(objJSON, leDossier) {
		super(objJSON);
		this.monDossier = leDossier;
		this.numerochoix = -1;
	}
	get SelectId()		{return 'LS-'+ this.ID;}
	get Dossier() 		{return this.monDossier ;}
	get Choix()			{return this.numerochoix;}
	get	Rang()			{return this.numerochoix; }
	get NomRelatif()	{
		var longdebase = myManager.Dossier.Nom.length;
		return this.Nom.substr(longdebase);
	}
	set Rang(value)		{
		if (value != this.numerochoix) {
			var leli = document.getElementById(this.ID);
			if (this.numerochoix<0) {leli.classList.add("fichselect");} 
			var lespan = leli.childNodes[1];
			this.numerochoix = value;
			if (this.numerochoix<0) {
				lespan.innerHTML="";
				leli.classList.remove("fichselect");
			} else {
				lespan.innerHTML=" ("+(value+1)+")";
			}
		}
	}
}

class Dossier extends Element {

	constructor(objJSON, lepapa) {
		super(objJSON);
		this.papa = lepapa;
		
		this.mesFichiers = {};
		var nb = objJSON.fichiers.length ;
		var i =0;
		for (i=0; i<nb; i++) {
			var unFichier = new Fichier(objJSON.fichiers[i], this);
			this.mesFichiers[unFichier.ID] = unFichier;
			//alert(unFichier.NomCourt);
		}
		this.mesSousDossiers = {};
		nb = objJSON.dossiers.length ;
		for (i=0; i<nb; i++) {
			var unDossier = new Dossier(objJSON.dossiers[i], this);
			this.mesSousDossiers[unDossier.ID] = unDossier;
		}
	}
	get Fichiers() 		{return this.mesFichiers;}
	get SousDossiers()	{return this.mesSousDossiers;}
	get Nb()			{return  Object.keys(this.mesFichiers).length;}
	get NbFichiers()	{
		var cpt = this.Nb;
		for (var k in this.mesSousDossiers) {
			cpt += this.mesSousDossiers[k].NbFichiers;
		}
		return cpt;
	}
	Enregistrer(unM) 	{
		var cpt = this.Nb;
		for (var x in this.mesFichiers) {
			unM.Memoriser(this.mesFichiers[x]);
		}
		for (var k in this.mesSousDossiers) {
			cpt += this.mesSousDossiers[k].Enregistrer(unM);
		}
		return cpt;
	}
}

var spanDragged = null;
var liDragged = null;
var fichDragged = null;
var positDragged = 0;
var cptDrag =0 ;
function dragDemarrage(ev) {
	cptDrag = 0;
	spanDragged = ev.currentTarget ;
	spanDragged.style.color = "red";
	liDragged = spanDragged.parentNode ;
	fichDragged = myManager.RecupFichier(liDragged.id.substr(3));
	positDragged = fichDragged.Rang;
	
	//spanDragged.style.opacity = "0";
	console.log("dragstart 1 :" + spanDragged.nodeName + ":" + spanDragged.innerHTML);
 	console.log("dragstart 2 :" + spanDragged.nodeName + " in LI " + liDragged.id);
	console.log("dragstart 3 :" + positDragged + " - " + fichDragged.Nom);
	ev.dataTransfer.setData("text/plain", "boujour tout va bien");
	
	//ev.currentTarget.style.color = "white";
	//console.log("dragstart 1 " + ev.currentTarget.nodeName + ":" + ev.currentTarget.innerHTML);
 	//console.log("dragstart 2 " + ev.currentTarget.nodeName + " in " + ev.currentTarget.parentNode.id);
// Set the drag's format and data. Use the event target's id for the data 
	//ev.dataTransfer.setData("text/plain", liDragged.id);
	//alert(ev.type);
	//console.log(ev);
}

function dragTerminer(e) {
	spanDragged.style.color = "";
}

function dragDepot(event) {
	// prevent default action (open as link for some elements)
	event.preventDefault();
	try {
		if ( spanDragged != event.currentTarget 
			&& event.currentTarget.className == "fichpris" ) {
			console.log("drop 1 ");
			var sonli = event.currentTarget.parentNode ;
			var sonfichier =  myManager.RecupFichier(sonli.id.substr(3));
			var leol = document.getElementById("listsel");
			console.log("drop 2 " + leol);
			if (sonfichier.Rang < fichDragged.Rang) {
				//montée
				console.log("drop 3 monte ");
				leol.removeChild(liDragged);
				console.log("drop 4 monte remove fait ");
				//  mettre liDragged BEFORE sonli
				leol.insertBefore(liDragged, sonli);
				console.log("drop 5 monte insert fait");
			} else {
				//descente
				console.log("drop 3 descend ");
				leol.removeChild(liDragged);
				console.log("drop 4 descend remove fait ");
				//  mettre liDragged BEFORE sonli
				leol.insertBefore(liDragged, sonli);
				console.log("drop 5 descend insert fait");
				leol.removeChild(sonli);
				console.log("drop 6 descend remove fait ");
				//  mettre sonli BEFORE liDragged
				leol.insertBefore(sonli, liDragged);
				console.log("drop 7 descend insert fait ");
			}
			myManager.Echanger(fichDragged, sonfichier.Rang);
		//  var tmp = sonfichier.Rang;
		//  sonfichier.Rang = fichDragged.Rang;
		//  fichDragged.Rang = tmp; */
		}		
	}
	catch (e) {
		afficherException(e);
	}
}

function dragSortie(event) {
	// reset background of potential drop target when the draggable element leaves it
	if ( spanDragged != event.currentTarget 
		&& event.currentTarget.className == "fichpris" ) {
		event.currentTarget.style.color = "";
	}
}

function dragEntree(event) {
  // highlight potential drop target when the draggable element enters it
	if ( spanDragged != event.currentTarget 
		&& event.currentTarget.className == "fichpris" ) {
	  event.currentTarget.style.color = "purple";
	}
}
class Manager {
	constructor(objJSON) {
		this.monDossier = new Dossier(objJSON, null);
		this.tousLesFichiers = {};
		this.monDossier.Enregistrer(this);
		this.selection = [];
	}
	get Dossier() 		{ return this.monDossier; }
	get NbFichiers() 	{ return Object.keys(this.tousLesFichiers).length; }
	get NbSelected()	{ return this.selection.length; }
	Memoriser(fich) 	{ this.tousLesFichiers[fich.ID] = fich; }
	RecupFichier(id) 	{ return this.tousLesFichiers[id]; }
	Echanger(fich, newposition)	{ 
		var n1 = fich.Rang;
		this.selection.splice(n1,1);
		this.selection.splice(newposition,0,fich) ;
		var deb = Math.min(n1, newposition) ;
		var fin = Math.max(n1, newposition) ;
		for (var i=deb; i<= fin; i++) {
			this.selection[i].Rang = i;
		}
	}
	
	Selectionner(fich)	{
		var posit 	= this.selection.indexOf(fich);
		var x 		= document.getElementById("listsel");
		if (posit < 0) {
			fich.Rang = (this.selection.push (fich)-1);
			var newLI 	= document.createElementNS(null,"li");
			newLI.id = fich.SelectId;
			var lespan = document.createElement ("span");
			lespan.classList.add("fichpris")
			lespan.innerHTML = fich.NomRelatif;
			newLI.appendChild(lespan);
			lespan.draggable = true;
			x.appendChild(newLI);
			  /* events fired on the draggable target */
			lespan.addEventListener("drag"		, function (e) {
				cptDrag++;
				//console.log("drag ... " + cptDrag);
			}, false);
			lespan.addEventListener("dragstart"	, dragDemarrage, false);			
			lespan.addEventListener("dragend"	, dragTerminer, false);		
			/* events fired on the drop targets // prevent default to allow drop */
			lespan.addEventListener("dragover", function( event ) { event.preventDefault();}, false);
			lespan.addEventListener("dragenter"	, dragEntree, false);
			lespan.addEventListener("dragleave"	, dragSortie, false);
			lespan.addEventListener("drop"		, dragDepot, false);
			
			newLI.classList.add("fichierchoisi");
			
		} else {
			fich.Rang = -1;
			this.selection.splice (posit,1);
			for (var i=posit; i<this.selection.length; i++) {
				this.selection[i].Rang = i;
			}
			var oldi = x.childNodes[posit+1] ;
			x.removeChild(oldi);
		}
		//alert ("selection " + this.NbSelected);	
	}
}

function clickOnFichier(element) {
	try {
		var sonid = element.id;
		var fich = myManager.RecupFichier(sonid);
		myManager.Selectionner(fich);
		//alert (fich.NomRelatif);
	}
	catch(e) {
		alert(e);
	}
}

function clickOnDossier(idelement) {
	try {
		var obj = document.getElementById(idelement);
		obj.classList.toggle("masked");
		var obj2 = document.getElementById("LI" + idelement);
		obj2.classList.toggle("masked");
		//alert (idelement);
	}
	catch(e) {
		alert(e);
	}
}

window.onload = function() {
	try {
		alert('coucou 1');
		var obj = document.getElementById("jsonpre");
		var om = JSON.parse(obj.innerHTML);
		myManager = new Manager(om);
		alert("Dossier de base : " + myManager.Dossier.Nom);
		alert("Nombre de fichiers : " + myManager.Dossier.NbFichiers);
		alert("Nb Selected : " + myManager.NbSelected);
	}
	catch (e) {
		afficherException(e);
	}
}
</script>
</head>
<body>
<h3>Analyse des classes</h3>
<div>
		<?php
			$obj=new pgAnalyse();
			echo '<span>Fichiers dans ', $obj->o_Dossier->nom,'</span>';
			$cpt = 0;
			$obj->o_Dossier->displayUl($cpt);
		?>
</div>
<div id="divsel">
	<ol id="listsel">
	</ol>
</div>
<div id="jsondiv">
<pre id="jsonpre">
<?php
echo json_encode($obj->o_Dossier, JSON_PRETTY_PRINT);
?>
</pre>
</div>
<div id="result">
</div>
</body>
</html>