<?php
/** Romain ANGIER */
class VueCategorie{
    private $oListe;

    public function __construct($theListe)
    {
        $this->oListe = $theListe;
    }

    /** Renvoie un objet de type SELECT  */
    public function getDomSelect(){
        $DOM =  new Balise('select');
        foreach ($this->oListe as $KC => $OCAT){
            $OPT = new Balise('option');
            $DOM->add($OPT);
            $OPT->add($OCAT->getLibelle());
            $OPT->addAttribut('value', $KC);
        }
        return $DOM;
    }

    public function getDomTbody($cols, $itemsClickables=false){
        $DOM = new Balise('tbody');
        if ($itemsClickables){
            $DOM->addAttribut('data-nature', 'categorie');
            $DOM->addAttribut('onclick', 'gestc_Demande(event);');
        }

        $TBCOLS = explode(';', $cols);
        $cpt = 0;
        foreach ($this->oListe as $KF => $OCAT){
            $cpt++;
            $TR = new Balise('tr'); $DOM->add($TR);
            if ($itemsClickables){
                $TR->addAttribut('data-key', $KF);
            }
            foreach ($TBCOLS as $codeCol){
                $TD = new Balise('td'); $TR->add($TD);
                switch ($codeCol){
                    case 'ID':
                        $TD->add($OCAT->getID());
                        break;
                    case 'L':
                        $TD->add($OCAT->getLibelle());
                        break;
                    default:
                        $TD->add($codeCol);
                }
            }
        }
        return $DOM;
    }

}
