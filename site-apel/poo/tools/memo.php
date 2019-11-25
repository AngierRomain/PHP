<?php
/** Référencer toutes les instances d'une classe */
class Memo {
	/** Mémorisation interne de tous les Memos.
	Tableau associatif NomClasse => objet Memo */
	private static $o_AllMemos = [];
	
	/** renvoie un tableau contenant les noms des classes instanciées */
	public static function getClassesInstanciees() {
		$tmp=array();
		foreach (static::$o_AllMemos as $K => $M) {
			if (count($M->RefObj)>0) {
				$tmp[] = $K;
			}
		}
		return $tmp;
	}
	/** Boolean indiquant la Mise Au Point */
	private static $b_MAP = false;
	
	/** Renvoie MAP (true ou false) */
	public static function getMAP() {return self::$b_MAP;}

	/** Setter MAP (true ou false) */
	public static function setMAP($value) {self::$b_MAP = $value;}
	

	/** affiche l'intégralité des objets mémorisés */
	public static function display() {
		echo '<br/><h2>Liste des Mémos, leur hiérarchie, et les Objets référencés</h2>';
		foreach (self::$o_AllMemos as $KC => $unMemo) {
			$unMemo->displayContent();
		}
	}
/** Array contenant les références des objets 
de la classe gérée par le mémo ID => Obj*/
	private $RefObj ;
	
/** Nom de la classe des objets référencés dans le Mémo */
	private $Classe ;
	
/** Array des Mémos des classes dérivées */
	private $Derivees ;
	
/** Si en MAP, affichage du message (traçage du fonctionnement Memo */
	public static function tracer($mess) {
		if (self::getMAP()) {
			foreach (func_get_args() as $a) {
				echo '<!-- ',$a, '-->'.PHP_EOL;
			}
		}
	}
/** Constructeur Memo pour la classe donnée en paramètre.
Peut déclencher la construction de Mémos(s) des classes parents */
	private function __construct ($className) { 
		$this->Classe = $className ;
		$this->RefObj = array() ;
		$this->Derivees = array();
		$obj = new ReflectionClass($className);
		self::$o_AllMemos[$className] = $this; 
		self::tracer ("+++++ Création d'un memo des objets de type <b>$className</b>");
		$herite = $obj->getParentClass(); 
		if ($herite!==false) {
			$heriteName = $herite->getName();
			if (!array_key_exists($heriteName, self::$o_AllMemos)) {
				$papa = new Memo($heriteName);
			} else {
				$papa = self::$o_AllMemos[$heriteName];
			}
			self::tracer ("ddddd Rattachement classe <b>$className</b> fille de classe <b>$heriteName</b>");
			$papa->Derivees[$className] = $this;
		}
	}
	
	/** renvoie le nombre d'objets du Memo, en comptant les classes dérivées */
	public function count() {
		$tmp = count($this->RefObj);
		foreach ($this->Derivees as $d) {$tmp += $d->count();}
		return $tmp;
	}
	
	/** affiche le contenu du mémo */
	public function displayContent() {
		echo '<p><b><u>Memo de ', $this->Classe, ', reférençant ', $this->count(),' objet(s)</u></b></p>';
		echo '<div style="margin-left:20px; border:1px black solid;">',
		'<b>',$this->Classe,'</b>, ', count($this->RefObj), ' objet(s) instancié(s) de ce type<br/>';
		foreach ($this->RefObj as $k => $v) {
			echo "$k=&gt;$v</br>";
		}
		$nb = count($this->Derivees);
		if ($nb>0) {
			echo count($this->Derivees), ' dérivée(s) : <br/>';
			foreach ($this->Derivees as $k => $v) {
				$v->displayContent();
			}
		}
		echo '</div>';
	}
	
	/** renvoie l'objet ayant l'id donné en paramètre s'il est mémorisé */
	public function isKnown($id) {
		if (array_key_exists($id, $this->RefObj)) {return $this->RefObj[$id];}
		foreach ($this->Derivees as $m) {
			$tmp = $m->isKnown($id);
			if ($tmp != null) {return $tmp;}
		}
		return null;
	}
	
	/** Revoie le Memo permettant de stocker les références de la classe donnée en paramètre */
	public static function getMemoFor($className) {
		if (array_key_exists($className, self::$o_AllMemos)) {
			return self::$o_AllMemos[$className];
		}
		$tmp = null;
		try {$tmp = new Memo($className); }
		catch (Exception $e) {
			self::tracer ( "erreur Memo : la classe <b>$className</b> n'existe pas");
		}
		return $tmp;
	}
	
	/** Enregistre l'élément donné en paramètre dans le Mémo de sa classe */
	public static function register(Element $obj) {
		$cl = get_class($obj);
		$lst = self::getMemoFor($cl);
		$lst->RefObj[$obj->getID()] = $obj;
		self::tracer ( "rrrrr register $cl : ". $obj. 
				', le Memo référence <b>'. count($lst->RefObj). "</b> objet(s) de type <b>$cl</b>");
	}
	
	/** Recherche Instance de classe avec un ID */
	public static function find($className, $ID) {
		self::tracer ( "<br/>????? recherche ID <b>$ID</b> dans Memo des objets de type <b>$className</b>");		
		return self::getMemoFor($className)->isKnown($ID);
	}
}


?>