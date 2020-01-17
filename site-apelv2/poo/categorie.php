<?php
/** Romain ANGIER */
class Categorie extends Element {

    public static function SQLInsert(){
        return 'INSERT INTO categorie (CATCode, CATLibelle)
							VALUES (?,?)';
    }
    public static function SQLUpdate() {
        return 'UPDATE categorie
				SET CATCode = ?,
				CATLibelle = ?
				WHERE CATCode = ?';
    }

    public static function SQLDelete(){
        return 'DELETE FROM categorie
                WHERE CATCode = ?';
    }

    public static function SQLdoMAJ($datas) {
        $oldkey = $datas ['oldkey'];
        $keyCat = $datas ['codecat'];
        $L = $datas ['libelle'];
        $LESI = SI::getSI();

        if ($keyCat=='SUPPR'){
            return $LESI-> SGBDexecuteQuery(
                static::SQLDelete(), $oldkey);
        }
        if($oldkey=='0'){
            return $LESI-> SGBDexecuteQuery(
                static::SQLInsert(), $keyCat,$L);
        }else {
            print_r($datas);
            return $LESI-> SGBDexecuteQuery(
                static::SQLUpdate(), $keyCat,$L, $oldkey);
        }

    }
    public static function champPK() {return 'CATCode';}

    public static function SQLSelect(){
        return 'SELECT CATCode, CATLibelle FROM categorie';}


    /** Attributs HORS BDD */
    public $DATA;

    public function __construct($uneLigne){
        parent:: __construct($uneLigne);
    }

    public function getLibelle(){return $this->getFld('CATLibelle');}

    public function jsonSerialize() {
        $rep = parent::jsonSerialize();
        $rep ['codecat'] = $this->getID();
        $rep ['libelle'] = $this->getLibelle();
        return $rep;
    }
}

class Categories extends Elements {
    public function __construct() {
        parent::__construct();
    }


    protected function getClassOfItem() {
        return 'Categorie';
    }

}

?>