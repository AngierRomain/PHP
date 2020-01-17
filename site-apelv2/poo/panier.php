<?php
/** Romain ANGIER */
class Panier extends Element {

    public static function SQLInsert(){
        return 'INSERT INTO panier (PANCode, PANCodeUTI, PANDate, PANMontant)
							VALUES (?,?,?,?)';
    }
    public static function SQLUpdate() {
        return 'UPDATE panier
				SET PANCode = ?,
				PANCodeUTI = ?,
				PANDate = ?,
				PANMontant = ?
				WHERE PANCode = ?';
    }

    public static function SQLDelete(){
        return 'DELETE FROM panier
                WHERE PANCode = ?';
    }

    public static function SQLdoMAJ($datas) {
        $oldkey = $datas ['oldkey'];
        $keyPanier = $datas ['codepan'];
        $D = $datas ['date'];
        $M = $datas['montant'];
        $LESI = SI::getSI();

        if ($keyPanier=='SUPPR'){
            return $LESI-> SGBDexecuteQuery(
                static::SQLDelete(), $oldkey);
        }
        if($oldkey=='0'){
            return $LESI-> SGBDexecuteQuery(
                static::SQLInsert(), $keyPanier,$D, $M);
        }else {
            print_r($datas);
            return $LESI-> SGBDexecuteQuery(
                static::SQLUpdate(), $keyPanier,$D, $M, $oldkey);
        }

    }
    public static function champPK() {return 'PANCode';}

    public static function SQLSelect(){
        return 'SELECT PANCode, PANCodeUTI, PANDate, PANMontant FROM panier';}


    /** Attributs HORS BDD */
    public $DATA;

    public function __construct($uneLigne){
        parent:: __construct($uneLigne);
    }

    public function getDate(){return $this->getFld('PANDate');}

    public function jsonSerialize() {
        $rep = parent::jsonSerialize();
        $rep ['codepan'] = $this->getID();
        $rep ['date'] = $this->getDate();
        return $rep;
    }
}

class Categories extends Elements {
    public function __construct() {
        parent::__construct();
    }


    protected function getClassOfItem() {
        return 'Panier';
    }

}

?>