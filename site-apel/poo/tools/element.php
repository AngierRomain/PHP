<?php
/** classe de base de toutes les classes Métier issues de la BDD */
abstract class Element implements IMetier, JsonSerializable {
	
/** stockage interne de la ligne : tableau assoc */
	private $lig;
	
/** s'instancie toujours à partir d'une ligne de la BDD */
	protected function __construct($ligne) {
		$this->lig = $ligne;
		//echo '<h1><b>CONSTRUCTEUR ',get_Class($this),'</b></h1>';
		
		Memo::register($this);
	}
/** renvoie le contenu champ spécifié en paramètre. NULL si le champ n'existe pas */
	protected function getField($fldName) {
		if (!array_key_exists($fldName, $this->lig)) return null;
		return $this->lig[$fldName];
	}
	
/** renvoie l'ID de l'instance
Si PK est simple, ID = PK
Si PK est composée, ID = champ1PK;Champ2PK; .....*/
	public function getID() {
		return static::getIDinRow($this->lig);
	}
	
/** recherche l'id dans la ligne issue de la base, en fonction de la
définition de la PK */
	public static function getIDinRow(array $lig) {
		$CPK = static::champPK();
		if (!is_array($CPK)) return $lig[$CPK];
		$tmp = '';
		foreach ($CPK as $idx => $FLD) {
			if ($idx>0) $tmp .=';';
			$tmp .= $lig[$FLD];
		}
		return $tmp;
	}
	
/** renvoie par défaut un string décrivant l'objet.
Peut (doit) être REECRITE de façon spécifique */
	public function __toString() {
		return 'objet ' . get_class($this);
	}
	
/** renvoie (string) la classe métier qui devra être employée pour instancier 
Cette méthode peut être surchargée pour pouvoir gérer des instanciations spécifiques
avec des classes dérivées.*/
	public static function getClassFor(array $ligneFromDB) {
		return get_called_class();
	}
	
/** cette méthode instancie un objet métier et le renvoie */
	public static function makeObjectFromRow($row) {
		$classObj = static::getClassFor($row);
		return new $classObj($row);
	}
	
/** renvoie l'objet correspondant à la PK donnée en paramètre (s'il existe)
SI besoin est, recherche dans la BDD. */
	public static function findFromPK($value) {
		if (func_num_args()>1) { //il y a au moins 2 arguments, donc clé composée
			// transformation en tableau de la liste des arguments
			$value = func_get_args();
		}
		$laclass = get_called_class();
		$IDITEM = (is_array($value)) ? implode(';', $value): $value ;
		$OBJ = Memo::find($laclass, $IDITEM);
		if ($OBJ!=null) return $OBJ;
		Memo::tracer("sqlsql Recherche $laclass ID=$IDITEM dans la BDD");
		//Recherche dans la base de donnée;
		$req = $laclass::SQLSelect(); // récup du SELECT de base
		$defPK = $laclass::champPK(); // récupération de la definition de la PK
		//echo '<br/>defPK = ', var_export($defPK);
		if (is_array($defPK)) { // clé composée : plusieurs conditions
			foreach ($defPK as $idx => $FPK) {
				$req .= ($idx==0) ? ' WHERE ': ' AND ';
				$req .= '('.$FPK.'=?)';
			}
		} else {
			$req .= ' WHERE ('.$defPK. '=?)';
		}
		//echo '<br/>Requête pour findFromPK : ', $req;
		$row = SI::getSI()->SGBDgetOneRow($req, $value);
		Memo::tracer("dbdbdb result :". var_export($row, true));
		//echo '<br/>ligne BDD obtenue<br/>'; var_dump ($row);
		//return ($row==null) ? null : new $laclass($row);
		return (!$row) ? null : static::makeObjectFromRow($row);
	}

	/** contrôle de la sérialisation JSON */
	public function jsonSerialize()
    {
        $rep = array();
        $rep ['CLASS'] = get_class($this);
        $rep ['id'] = $this->getID();
        return $rep;
    }
}



?>