<?php
abstract class Element implements IMetier, JsonSerializable {

	/*
	Stockage interne de la ligne	
	*/
	private $lig;
	
	/*
	S'instencie toujours a partir d'une ligne de la BDD
	*/
	protected function __CONSTRUCT($lig){
		$this->lig = $lig;
		//echo '<h2><bold>CONSTRUCTEUR ' , get_Class($this) , '</bold></h2>';

		Memo::register($this);
	}
	
	protected function getFld($fldName){
		if(!array_key_exists($fldName, $this->lig)) return null;
		return $this->lig[$fldName];
	}
	
	public function getID(){
		//echo '<p>', get_called_class() ,'</p>';
		//return $this->lig[static::champPK()];
		return static::getIDinRAW($this->lig);
	}
	/*recherche l'id dans la ligne issue de la base*/
	public static function getIDinRAW(array $lig) {
		//return $lig[static::champPK()];
		$CPK = static::champPK();
		if (!is_array($CPK)) return $lig[$CPK];
		$tmp = '';
		foreach ($CPK as $idx => $FLD) {
			if ($idx>0) $tmp .= ';';
			$tmp .= $lig[$FLD];
		}
		return $tmp;
	}

	public function __toString(){
		return 'objet ' . get_Class($this);
	}

	
	/* renvoi l'objet correspondant a la pk donnee en parametre*/
	public static function findFromPK($value) {
		if (func_num_args() > 1){
			// transformation en tableau
			$value = func_get_args();
		}
		$laclass = get_called_class();
		$IDITEM = (is_array($value)) ? implode(';', $value) : $value;
		$OBJ = Memo::find($laclass, $IDITEM);
		if ($OBJ != null) return $OBJ;

		//recherche dans la bdd
		//echo '</br>', $laclass;
		$req = $laclass::SQLselect();
		$defPK = $laclass::champPK();
		//echo '</br>defPK = ', var_export($defPK);
		if (is_array($defPK)){
			foreach ($defPK as $idx => $FPK){
				$req .= ($idx==0) ? ' WHERE ' : ' AND ';
				$req .= '('.$FPK.'=?)';
			}
		}
		else{
			$req .= ' WHERE (' . $defPK . '=?)';
		}
		//echo '</br> Requete pour find from pk : ', $req;
		$tmp = SI::getSI()->SGBDgetOneRow($req, $value);
		//echo '</br> ligne bdd obtenue </br> ', var_dump ($tmp);
		return ($tmp == null) ? null : new $laclass($tmp);

		//return $temp;
	}
	
	public function jsonSerialize() {
		$rep = array();
		$rep ['CLASS'] = get_class($this);
		$rep ['id'] = $this->getID();
		return $rep;		
	}
	
}


?>