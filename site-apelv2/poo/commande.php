<?php
/** Romain ANGIER */
class Commande extends Element {

    public static function SQLInsert(){
        return 'INSERT INTO ()
							VALUES ()';
    }
    public static function SQLUpdate() {
        return 'UPDATE 
				SET 
				WHERE ';
    }

    public static function SQLDelete(){
        return 'DELETE FROM 
                WHERE ';
    }


    public static function SQLdoMAJ($datas) {
        $oldkey = $datas ['oldkey'];
        $key = $datas ['code'];
        $keyCat = $datas ['codecat'];
        $L = $datas ['libelle'];
        $PU = $datas ['prixunit'];
        $Q = $datas ['quantite'];
        $LESI = SI::getSI();

        if ($key=='SUPPR'){
            return $LESI-> SGBDexecuteQuery(
                static::SQLDelete(), $oldkey);
        }
        if($oldkey=='0'){
            return $LESI-> SGBDexecuteQuery(
                static::SQLInsert(), $key,$keyCat+1,$L, $PU, $Q);
        }else {
            return $LESI-> SGBDexecuteQuery(
                static::SQLUpdate(), $key,$keyCat+1,$L, $PU, $Q);
        }

    }
    public static function champPK() {return 'FOUCode';}

    public static function SQLSelect(){
        return 'SELECT FOUCode, FOUCodeCAT, FOULibelle, FOUPrixU, FOUQuantite FROM fourniture';}


    /** Attributs HORS BDD */
    public $DATA;

    /** Instanciation d'un objet Categorie */
    private $O_CATEG;

    public function getCategorie(){
        if ($this->O_CATEG == null){
            $this->O_CATEG = Categorie::findFromPK($this->getCC());
        }
        return $this->O_CATEG;
    }

    public function __construct($uneLigne){
        parent:: __construct($uneLigne);
    }
    public function getLibelle(){return $this->getFld('FOULibelle');}

    //public function getID(){return $this->getField('FOUCode');}

    public function getPrixU(){return $this->getFld('FOUPrixU');}

    public function getQuantite(){return $this->getFld('FOUQuantite');}


    /** renvoie le FOUCodeCAT */
    public function getCC(){return $this->getFld('FOUCodeCAT');}



    public function getLPQ(){return $this->getLibelle().' '. $this->getPrixU().' '.$this->getQuantite();}

    public function jsonSerialize() {
        $rep = parent::jsonSerialize();
        $rep ['code'] = $this->getID();
        $rep ['codecat'] = $this->getCC();
        $rep ['libelle'] = $this->getLibelle();
        $rep ['prixunit'] = $this->getPrixU();
        $rep ['quantite'] = $this->getQuantite();
        return $rep;
    }
}

class Commandes extends Elements {
    public function __construct() {
        parent::__construct();
    }


    protected function getClassOfItem() {
        return 'Commandes';
    }

}

?>