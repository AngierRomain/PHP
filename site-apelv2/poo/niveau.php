<?php
class Classe extends Element {
	public static function SQLInsert() {
		return 'INSERT INTO niveau 
		(NIVCode, NIVLibelle VALUES(?,?)';
	}
	
	public static function SQLUpdate() {
		return 'UPDATE niveau
		SET NIVCode=?,
		NIVLibelle=?,
		WHERE NIVCode=?';
	}
	
	public static function SQLdoMAJ($datas) {
		$oldkey = $datas['oldkey'];
		$key = $datas['key'];
		$L = $datas['libelle'];
		$LESI = SI::getSI();
		if ($oldkey == '0') {
			return $LESI->SGBDexecuteQuery(
				static::SQLInsert(), $key, $L);
		} else {
			return $LESI->SGBDexecuteQuery(
				static::SQLUpdate(), $key, $L, $oldkey);
		}
	}
	public static function champPK(){return 'NIVCode';}
	public static function SQLselect(){return 'SELECT NIVCode, NIVLibelle FROM niveau';}
	
}

class Classes extends Elements {
	public function __CONSTRUCT(){
		parent::__CONSTRUCT();
	}

}

?>