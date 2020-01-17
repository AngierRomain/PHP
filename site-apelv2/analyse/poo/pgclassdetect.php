<?php
/** classe bidon dans le but d'implémenter JSONSERIALIZABLE */
abstract class pgPipo implements JsonSerializable {
	const TRUC1 = 1;
	const TRUC2 = false;
	const TRUC3 = true;
	const TRUC4 = 22.7;
	const TRUC5 = 'bonjour';
	
	public function __construct() { 
	}
	
	/** renvoie en clair true ou false */
	public static function booEnClair($b, $valeurs = ['true', 'false']) {
		return ($b) ? $valeurs[0] : $valeurs[1];
	}
	public function jsonSerialize() {
		return 
			[	'CLASS' 			=> get_class($this)
			];
	}
}

/** classe de base de tout élément issu de la REFLECTION */
abstract class pgReflection extends pgPipo {
	/** 
	indique si ce membre est STATIC ou pas */
	protected $isStatic ;
	protected $isPublic ;
	protected $isPrivate ;
	protected $isProtected ;
		
	public $lineDeb ;
	public $lineFin ;
	public $name;
	protected $comment ;
	public function __construct() { 
		parent::__construct();
		$this->comment = null;
	}
	
	/** mémorise le commentaire du membre */
	public final function recupCommentaire($c) {
		$c = str_replace(array('/**', '*/'), '', $c);
		//$c=trim($c);
		//$this->comment = htmlspecialchars_decode($c); 
		$this->comment = htmlspecialchars($c, ENT_HTML5, 'UTF-8'); 
	}
	
	/** sérailisation JSON de base */
	public function jsonSerialize() {
		$acces = 'public';
		if ($this->isPrivate) {$acces = 'private';}
		elseif ($this->isProtected) {$acces = 'protected';}
		return 
			[	'CLASS' 			=> get_class($this),
				'name' 			=> $this->name,
				'static' 		=> $this->isStatic,
				'access'			=> $acces,
				'comment' 		=> $this->comment
			];
	}
}

/** classe présentant un attribut défini dans une classe */
class pgAttribut extends pgReflection {
	/** valeur par défaut de l'attribut */
	public $defval;
	public function __construct($rp, ReflectionClass $classe) { 
		parent::__construct();
		$this->isPublic 		= $rp->isPublic();
		$this->isProtected	= $rp->isProtected();
		$this->isPrivate		= $rp->isPrivate();
		$this->isStatic		= $rp->isStatic();
		$this->name				= $rp->getName();
		if ($classe->isUserDefined()) {
			$this->recupCommentaire($rp->getDocComment());
		}
	}
	
	public function jsonSerialize() {
		$rep = parent::jsonSerialize();
		$rep['defvalue'] = $this->defval;
		return $rep;
	}
}

/** classe présentant un paramètre déclaré dans une méthode */
class pgParam extends pgPipo {
	public $name;
	public $optionnel ;
	public $isreference ;
	public $defval ;
	
	
	public function __construct($P, $classe) { 
		$this->name = $P->getName();
		$this->isreference = $P->isPassedByReference();
		if ($classe->isUserDefined()) {
			$this->optionnel = $P->isOptional();
			if ($this->optionnel && $P->isDefaultValueAvailable()) {
				$defval = $P->getDefaultValue();
			} 
		}
	}
	/** fonction bidon */
	public final function azerty(&$a, $b=22) {}
	
	public function jsonSerialize() {
		$rep = [
			'CLASS' => get_class($this),
			'name' => $this->name,
			'ref' => $this->isreference,
			'optionnel' => $this->optionnel,
			];
		if ($this->optionnel) {$rep['defval'] = $this->defval;}
		return $rep;
	}
}
/** objet décrivant un méthode d'une classe */
class pgMethode extends pgReflection {
	public $isFinal;
	public $parametres ;
	public function __construct($M, $classe) { 
		parent::__construct();
		$this->parametres = array();
		$this->isPublic 		= $M->isPublic();
		$this->isProtected	= $M->isProtected();
		$this->isPrivate		= $M->isPrivate();
		$this->isStatic		= $M->isStatic();
		$this->isFinal			= $M->isFinal();
		$this->isAbstract	= $M->isAbstract();
		$this->name				= $M->getName();
		$this->recupCommentaire($M->getDocComment());
		if ($classe->isUserDefined()) {
			$this->lineDeb = $M->getStartLine();
			$this->lineFin = $M->getEndLine();
		} else {
			$this->lineDeb = 0;
			$this->lineFin = 0;		
		}
		$par = $M->getParameters();
		foreach ($par as $kp => $p1) {
			$this->parametres[] = new pgParam($p1, $classe);
		}
	}                
	
	public function jsonSerialize() {
		$rep = parent::jsonSerialize();
		$rep['abstract'] = $this->isAbstract;
		$rep['final'] = $this->isFinal;
		$rep['linedeb'] = $this->lineDeb;
		$rep['linefin'] = $this->lineFin;
		$rep['parametres'] = $this->parametres;
		return $rep;
	}
}

class pgConstante extends pgPipo {
	public $name;
	public $valeur;
	public function __construct($K, $V) { 
		$this->name = $K;
		$this->valeur = $V; 
	}
	
	public function jsonSerialize() {
		return [
			'CLASS' => get_class($this),
			'name' => $this->name,
			'valeur' => $this->valeur,
			'valeurtxt' => pgClassDetect::enclair($this->valeur)
		];
		//echo "\n<tr><td>const $K</td><td>";
		//	echo enclair($V);
		//	echo '</td></tr>';

	}
}
class pgClass extends pgReflection {
	protected $nomClasseParente ;
	protected $isInterface ;
	protected $isAbstract ;
	protected $isFinal ;
	public $isUser ;
	protected $interfaces ;
	protected $constantes ;
	protected $attributs ;
	protected $methodes ;
	protected $fichiersource ;
	protected $classesderivees ;
	
	public function jsonSerialize() {
		$rep = parent::jsonSerialize();
		$rep['isinterface'] = $this->isInterface;	
		$rep['isfinal'] = $this->isFinal; 
		$rep['fichiersource'] = $this->fichiersource;	
		if ($this->fichiersource!=null) {
			$rep['linedeb'] = $this->lineDeb;
			$rep['linefin'] = $this->lineFin;
		}
		
		$rep['herite'] = $this->nomClasseParente;        
		$rep['abstract'] = $this->isAbstract;
		$rep['interfaces'] = $this->interfaces;	
		$rep['constantes'] = $this->constantes;	
		$rep['attributs'] = $this->attributs;	
		$rep['methodes'] = $this->methodes;	
		$rep['derivees'] = $this->classesderivees;	
		return $rep;
	}
	
	private function rechercheConstantes(ReflectionClass $classe) {
		$this->constantes = array();
		foreach ($classe->getConstants() as $K => $V){
			$this->constantes[] = new pgConstante($K, $V);
		}  
	}
	
	private function rechercheAttributs(ReflectionClass $classe) {
		$this->attributs = array ();
		$Prop_Stat = $classe->getStaticProperties();
		$Prop_DP = $classe->getDefaultProperties();
		$Prop = $classe->getProperties();

		foreach ($Prop_Stat as $K => $V) {
			$P = $classe->getProperty($K);
			if ($P->getDeclaringClass()==$classe) {
				$this->attributs[] = new pgAttribut($P, $classe);
			}
			
		}
		$lesProps = $classe->getProperties();
		foreach ($lesProps as $K => $P) {
			if ($P->getDeclaringClass()==$classe) {
			if (!array_key_exists($P->getName(), $Prop_Stat)) {
				$tmpattr = new pgAttribut($P, $classe);
				$tmpattr->defval = $Prop_DP[$P->getName()];
				$this->attributs[] = $tmpattr;
			}
			}
		}
	}
	
	private function rechercheMethodes(ReflectionClass $classe) {
		$this->methodes = array();
		$tmp = $classe->getMethods();
		foreach ($tmp as $M) {
			if ($classe==$M->getDeclaringClass()) {
				$this->methodes[] = new pgMethode ($M, $classe);
			}
		}
	}
	
	private function rechercheClassesDerivees (ReflectionClass $classe) {
		// recherche des classe dérivées
		$this->classesderivees = array();
		$ilya=false;
		$estInterf = $classe->isInterface();
		foreach (pgClassDetect::getSubclassesOf($classe) as $CD) {
			$this->classesderivees[] = $CD;
		}
	}
	
	public function __construct (ReflectionClass $classe) { 
		parent::__construct();
		$this->isPublic 		= true;
		$this->isProtected	= false;
		$this->isPrivate		= false;
		$this->isStatic		= false;
		$this->isFinal = $classe->isFinal();
		$this->name				= $classe->getName();
		$this->isInterface 	= $classe->isInterface();
		$this->isUser			= $classe->isUserDefined();
		$this->isAbstract 	= $classe->isAbstract();
		$this->recupCommentaire($classe->getDocComment());
		if ($this->isUser) {
			$this->lineDeb = $classe->getStartLine();
			$this->lineFin = $classe->getEndLine();
			$this->fichiersource = $classe->getFileName();
		} else {
			$this->lineDeb = 0;
			$this->lineFin = 0;		
		}
		$herite = $classe->getParentClass(); 
		if ($herite!==false) {
			$this->nomClasseParente = $herite->getName(); }
		$this->interfaces = array ();
		$Interf = $classe->getInterfaces();
		foreach ($Interf as $K => $I) {
			$this->interfaces[] = $K;
		}
		$this->recupCommentaire($classe->getDocComment());
		$this->rechercheConstantes($classe);
		$this->rechercheAttributs($classe);
		$this->rechercheMethodes($classe);
		$this->rechercheClassesDerivees($classe);
	}
}

/** classe de travail permettant de détecter tous les classes */
class pgClassDetect extends pgPipo {
	private static $_os;
	private $tbclasses ;
	/** stockage des classes trouvées */
	private $mesClasses ;
	public $repertoire;
	public $fichiers ;
	public $extras;
	public $contenus;
	public static $ordrealpha = false;
	
	/** sérialisation JSON de la liste des classes */
	public function jsonSerialize() {
		$rep = parent::jsonSerialize();
		$rep['lstclasses'] = array_values($this->mesClasses);
		return $rep;
	}
	public function memoExtras (array $extr) {
		$this->extras = $extr;
	}
	public function memoFiles (array $fich) {
		$this->fichiers = $fich;
		$this->contenus = array();
		foreach ($fich as $f) {
			$lignes = array();
			$fp = fopen($f,"r");
			while($line = fgets($fp)) {
					$lignes[] = $line;
			}
			fclose($fp);
			$this->contenus[] = $lignes;
		}
	}
	public function __construct() {
		$this->tbclasses = array();
		$this->mesClasses = array();
		$this->repertoire = getcwd();
		static::$_os = $this;
	}	
	static function enclair($truc) {
		if (is_bool($truc)) {
			return ($truc) ? 'true' : 'false';
		}
		if (is_string($truc)) {return '"'.$truc.'" (string)'; }
		if ($truc==null) {return 'NULL'; }
		if (is_int($truc)) {return $truc.' (int)';}
		if (is_numeric($truc)) {return $truc;}
		if (is_array($truc)) {return 'tableau';}
		return $truc;
	}
	
	static function getSubclassesOf($parent) {
		$result = array();
		$np = $parent->getName();
		foreach (static::$_os->tbclasses as $K => $class) {
			if (is_subclass_of($K, $np))
				$result[] = $K;
		}
		return $result;
	}	
	public static function trierN($obj1, $obj2) {
		if ($obj1==$obj2) {return 0;}
		$n1 = $obj1->name;
		$n2 = $obj2->name;
		return strcasecmp ( $n1 , $n2 );
	}
	static function trierSN($obj1, $obj2) {
		if ($obj1==$obj2) {return 0;}
		if ($obj1->isStatic()) {
			if (!$obj2->isStatic()) {return -1;}
		} elseif ($obj2->isStatic()) {return 1;}
		return static::trierN($obj1, $obj2);
	}	
	
	function plop($P) {
		$tmp='';
		if ($P->isPrivate()) {$tmp .= 'private ';}
		elseif ($P->isProtected()) {$tmp .= 'protected ';}
		elseif ($P->isPublic()) {$tmp .= 'public ';}
		if ($P->isStatic()) {$tmp .= 'static ';}
		return $tmp;
	}
	function commentaire($P) {
		$tmp = '';
		$c = $P->getDocComment();
		//$c = str_replace(array('/**', '*/'), '', $c);
		$c=trim($c);
		return htmlspecialchars($c); 
	}	
	static function makeLiaison($txt) {
		$tmp = '<span class="liaison" onclick="voir('."'".$txt."');".'">'. $txt . '</span>';
		return $tmp;
	}
	function rechercheDesClasses() {
		$dossier = $this->repertoire;
		// recherche de toutes les classes utilisateur
		foreach (get_declared_classes() as $cl) {
			$obj = new ReflectionClass($cl);
			if ($obj->isUserDefined()) {
				if (strpos($obj->getFileName(), $dossier)===false) {
					$this->tbclasses[$cl] = $obj;
					$this->mesClasses[$cl] = new pgClass($obj);
				}
			} elseif (in_array($cl, $this->extras)) {
					$this->tbclasses[$cl] = $obj;
					$this->mesClasses[$cl] = new pgClass($obj);
			}
		}
		
		// recherche de tous les interfaces utilisateur
		foreach (get_declared_interfaces() as $cl) {
			$obj = new ReflectionClass($cl);
			if ($obj->isUserDefined()) {
				if (strpos($obj->getFileName(), $dossier)===false) {
					$this->tbclasses[$cl] = $obj;
					$this->mesClasses[$cl] = new pgClass($obj);
				}
			} elseif (in_array($cl, $this->extras)) {
					$this->tbclasses[$cl] = $obj;
					$this->mesClasses[$cl] = new pgClass($obj);
			}
		}
		
		// recherche des classes parentes ou Interfaces
		foreach ($this->tbclasses as $cl) {
			$herite = $cl->getParentClass(); 
			if ($herite!==false) {
				$this->tbclasses[$herite->getName()] = $herite; 
				$this->mesClasses[$herite->getName()] = new pgClass($herite);}
			foreach ($cl->getInterfaces() as $I) {
				$this->tbclasses[$I->getName()] = $I;
				$this->mesClasses[$I->getName()] = new pgClass($I);
			}
		}
		
		//uasort($this->tbclasses, array('pgClassDetect','trierN'));		
	}
	
	function afficherUnSelect() {
		echo '<div class="choixclasse">';
		//echo '<select id="selclass" class="selclass">';
		//foreach ($this->tbclasses as $nom => $cl) {
		//		echo '<option value="',$nom,'">';
		//		echo $nom;
		//		echo '</option>';
		//}
		//echo '</select>';	
		echo '</div>';
	}
	
	function afficherEnteteClasse ($classe) {
		$n = $classe->getName();
		//echo "\n", '<div id="', $n, '" class="classe visuno">';
		echo '<div class="defclasse">';
		$estinterface = $classe->isInterface();
		if ($estinterface) {echo 'Interface : ';}
		else {echo 'Classe : ';}
		echo '<span>', $n, '</span> ';
		$estecrit = $classe->isUserDefined();
		echo ($estecrit ? ' (définie dans la solution)' : '(Interne)' );
		echo '<br/>';
		
		if ($estecrit) {
			$deb = $classe->getStartLine();
			$fin = $classe->getEndLine();
			echo 'Source : ',$classe->getFileName(), 
				'<br/>',($fin-$deb+1), ' lignes (',$deb,', ',$fin,')<br/>';
		}

		$herite = $classe->getParentClass(); 
		if ($herite!==false) {
			echo ' --- Hérite de ', $herite->getName(),'<br/>';}
		if ((!$estinterface) && $classe->isAbstract()) {
			echo 'classe Abstraite', "<br/>";}
		$Interf = $classe->getInterfaces();
		$nbi=count($Interf);
		if ($nbi==1) {$nbi=" --- Implémente l'interface ";} 
		else {$nbi=" --- Implémente les $nbi interfaces ";}
		$ilya=false;
		
		foreach ($Interf as $K => $I) {
			if (!$ilya) {echo "\n$nbi";}
			else {echo ', ';}
			echo $K;
			//echo $K;
			$ilya=true;
		}
		
		// recherche des classe dérivées
		$ilya=false;
		foreach ($this->getSubclassesOf($classe) as $CD) {
			if (!$ilya) {
				echo '<br/> --- ';
				echo  (($estinterface) ? 'Est implémenté par ' : 'Est dérivée par ');
				$ilya = true;
			}
			else {echo ', ';}
			echo static::makeLiaison($CD);
		}
		echo '</div>';	
	}
	
	function afficherProprietesClasse($classe) {
		$Prop_Stat = $classe->getStaticProperties();
		$Prop_DP = $classe->getDefaultProperties();
		$Prop = $classe->getProperties();
		//uasort($Prop_Stat, array('pgClassDetect','trierN')) ;
		
		//echo "\n", '<tr><td colspan="4">Membres STATIC</td></tr>';
		foreach ($Prop_Stat as $K => $V) {
			$P = $classe->getProperty($K);
			echo '<!--', get_class($P), '-->';
			$c = $this->commentaire($P);
			echo "\n static ";


			echo $this->plop($P);
			echo $K, ' ';
			var_dump($V);
			echo "</br>"; 

		}
		$i=0;
		//echo "\n", '<tr><td colspan="4">Membres</td></tr>';
		$lesProps = $classe->getProperties();
		//uasort($lesProps, 'trierN') ;
		foreach ($lesProps as $K => $P) {
			if (!array_key_exists($P->getName(), $Prop_Stat)) {
				$i++;
				$c = $this->commentaire($P);
				if ($c!='') {	echo "<i><pre>$c</pre></i></br>"; }
				echo $this->plop($P);
				echo ' $';
				echo $P->getName(), ' = ';
				var_dump($Prop_DP[$P->name]);
				echo '<br/>';
			}
		}	
	}
	
	function afficherMethodesClasse($classe) {
		$tmp = $classe->getMethods();
		//uasort($tmp, 'trierSN') ;
		foreach ($tmp as $M) {
			if ($classe==$M->getDeclaringClass()) {
				// récup des arguments
				$comm = $this->commentaire($M);
				if ($comm != '') {echo "<i><pre>$comm</pre></i></br>"; }
				$par = $M->getParameters();
				$signat = '(';
				$plus='';
				foreach ($par as $kp => $p1) {
					if ($kp>0) {$signat .= ', ';}
					$signat .= $p1->getName();
					if ($classe->isUserDefined()) {
						if ($p1->isOptional()) {
							$signat .= '='.static::enclair($p1->getDefaultValue());
						} else {
												
						}
					}
				}
				$signat .= ')';
				$cla = '';
				$declarer = $this->plop($M);
				if ($M->isFinal()) {$declarer .= ' final ';}

				if ($M->isStatic()) {$cla .= ' class="static"';}
				echo $declarer,' function ', $M->getName(), $signat;
				if (!$classe->isInterface() && $classe->isUserDefined()) {
					$deb = $M->getStartLine();
					$fin = $M->getEndLine();
					if ($deb==$fin) {echo " (ligne $deb)";}
					else {echo " (lignes $deb à $fin)";}
				}
				echo '<br/>';
				
				//if ($M->hasReturnType()) { PHP 7
				//	echo $M->getReturnType()->__toString();
				//}
				//echo get_class($M);

			}
		}	
	}
	
	function afficherConstantesClasse($classe) {
		foreach ($classe->getConstants() as $K => $V){
			echo "const $K = ";
			echo static::enclair($V);
			echo '</br>';
		} 	
	}
	function afficherUneClasse($classe) {
		echo "\n", '<div id="', $classe->getName(), '" class="classe visuno">';
		$this->afficherEnteteClasse ($classe);
		echo "\n<p>";
		$this->afficherConstantesClasse ($classe);
		$this->afficherProprietesClasse ($classe);
		$this->afficherMethodesClasse ($classe);
		//echo "\n</table>";	
		echo "\n</div>";
	}
	
	function afficherToutesLesClasses() {
		echo '<div id="allclasses">';
		foreach ($this->tbclasses as $classe) {
			$this->afficherUneClasse($classe);
		}
		echo '</div>';
		echo '<p>fin des classes</p>';
	}
	
	
	
}

/************************* Temporary TRASH ***********************


*/
?>