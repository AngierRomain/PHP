<?php
/** Classe servant de base à des listes 
par tableau associatif */
class Elements implements Iterator {
	
/** stockage interne dans tableau associatif */
	private $arr;
	
/** nom de la classe des objets dans la liste */
	private $classItem;
	
/** constructeur initialisant le tableau assoc et définissant
la classe des objets qu'il contiendra */
	protected function __construct() {
		$this->arr = array(); 
		$this->classItem = $this->getClassOfItem();
	}
	
/** renvoie le nom de la classe des éléments contenus.
Par défaut le Nom de la classe plurielle sans le 's' final.
Peut être REECRITE */
	protected function getClassOfItem() {
		$clapp = get_called_class();
		$taille = strlen($clapp);
		return substr($clapp, 0, $taille-1);
	}
	
	
	
/** instancie une liste en la remplissant
depuis la BDD */
	public static function makeFromBDD($whereOrder = null, $params=null) {
		$classList = get_Called_Class(); // recup nom class plurielle
		$TMP = new $classList(); // instanciation d'un objet de classe plurielle
		$clobj = $TMP->classItem; // récup du nom de la classe des futurs objets contenus
		$req = $clobj::SQLSelect(); // recup requete de base
		if ($whereOrder != null) { // compléter s'il y a lieu
			$req .= ' '.$whereOrder;
		}
		// etude des arguments
		if ($params != null) {
			if (!is_array($params) ) {// les arguments ne sont pas dans un tableau
				$nbargs = func_num_args(); // Nbr d'arguments
				if ($nbargs>2) { // il y en a au moins 3
					$params = func_get_args(); // recup en array de ts les arguments
					array_shift($params); // suppression 1er argument (whereorder)
				}
			}
		}
		//echo '<br/>', $req;
		$curs = SI::getSI()->SGBDgetCursorReady($req, $params);
		foreach ($curs as $ligne) {
			// récupération de l'id dans le ROW
			$IDROW = $clobj::getIDinRow($ligne);
			// recherche objet avec cet ID
			$obj = Memo::find($clobj, $IDROW);
			//if ($obj==null) $obj = new $clobj($ligne);
			if ($obj==null) $obj = $clobj::makeObjectFromRow($ligne);
			$TMP->arr[$IDROW] = $obj;
		}
		return $TMP;
	}
	/** renvoie l'element s'il existe
     *@$id: id de l'element recherché
     */
	public function getObject($id){
	    if (!array_key_exists($id, $this->arr)) return null;
	    return $this->arr[$id];
    }
	// ----------------------------------------------------------
	// foreach exploitable avec un Elements
	//-----------------------------------------------------------
	
	/** stockage interne d'un itérateur pour autoriser le foreach */
	private $myIterator;
/** ----- Interface Iterator::rewind */
	public final function rewind() {
		$this->myIterator = new ArrayIterator($this->arr); 
		$this->myIterator->rewind(); }
/** ----- Interface Iterator::current */
	public final function current()	{ return $this->myIterator->current(); }
/** ----- Interface Iterator::key */
	public final function key() 	{ return $this->myIterator->key(); }
/** ----- Interface Iterator::next */
	public final function next() 	{ $this->myIterator->next();}
/** ----- Interface Iterator::valid */
	public final function valid() 	{ return $this->myIterator->valid(); }
	
}

?>