<?php


class Matiere extends Element
{
    public function __construct($uneLigne){
        parent:: __construct($uneLigne);
    }
    static function champPK(){return 'MATCode';}

    static function SQLSelect(){return 'SELECT MATCode, MATLibelle from matiere';}

    public function getLibelle(){return $this->getField('MATLibelle');}
}

class Matieres extends Elements{
    public function __construct(){
        parent:: __construct();
    }
}

?>