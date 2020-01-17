<?php
class Classe extends Element {
	public static function SQLInsert() {
		return 'INSERT INTO pack 
		(PACCodeNIV, PACCodeMAT, PACCodeFOU, PACQuantite VALUES(?,?,?,?)';
	}
	
	public static function SQLUpdate() {
		return 'UPDATE pack
		SET PACCodeNIV=?,
		PACCodeMAT=?,
		PACCodeFOU=?,
		PACQuantite=?,
		WHERE PACCodeNIV=?
		AND PACCodeMAT=?
		AND PACCodeFOU=?';
	}
	
	public static function SQLdoMAJ($datas) {
		$oldkey = $datas['oldkey'];
		$key = $datas['key'];
		$Q = $datas['quantite'];
		$LESI = SI::getSI();
		if ($oldkey == '0') {
			return $LESI->SGBDexecuteQuery(
				static::SQLInsert(), $key, $Q);
		} else {
			return $LESI->SGBDexecuteQuery(
				static::SQLUpdate(), $key, $Q, $oldkey);
		}
	}
	public static function champPK(){return 'PACCodeNIV, PACCodeMAT, PACQuantite';}
	public static function SQLselect(){return 'SELECT PACCodeNIV, PACCodeMAT, PACCodeFOU, PACQuantite, FROM pack';}
	
	public function jsonSerialize() {
		$rep = parent::jsonSerialize();
		$rep['lib'] = $this->getLibelle();
		return $rep;
	}
}

class Classes extends Elements {
	public function __CONSTRUCT(){
		parent::__CONSTRUCT();
	}

}

?>