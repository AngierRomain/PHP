<?php
/** Romain ANGIER */
class VueFourniture{
    private $oListe;

    public function __construct($theListe)
    {
        $this->oListe = $theListe;
    }

    /** Renvoie un objet de type SELECT  */
    public function getDomSelect(){
        $DOM =  new Balise('select');
        foreach ($this->oListe as $KF => $OFOUR){
            $OPT = new Balise('option');
            $DOM->add($OPT);
            $OPT->add($OFOUR->getLibelle());
            $OPT->addAttribut('value', $KF);
        }
        return $DOM;
    }

    public function getDomTbody($cols, $itemsClickables=false){
        $DOM = new Balise('tbody');
        if ($itemsClickables){
            $DOM->addAttribut('data-nature', 'fourniture');
            $DOM->addAttribut('onclick', 'gestf_Demande(event);');
        }

        $TBCOLS = explode(';', $cols);
        $cpt = 0;
        foreach ($this->oListe as $KF => $OFOUR){
            $cpt++;
            $TR = new Balise('tr'); $DOM->add($TR);
            if ($itemsClickables){
                $TR->addAttribut('data-key', $KF);
            }
            foreach ($TBCOLS as $codeCol){
                $TD = new Balise('td'); $TR->add($TD);
                switch ($codeCol){
                    case 'ID':
                        $TD->add($OFOUR->getID());
                        break;
                    case 'CATEG':
                        $TD->add($OFOUR->getCategorie()->getLibelle());
                        break;
                    case 'L':
                        $TD->add($OFOUR->getLibelle());
                        break;
                    case 'P':
                        $TD->add($OFOUR->getPrixU());
                        break;
                    case 'Q':
                        $TD->add($OFOUR->getQuantite());
                        break;
                    default:
                        $TD->add($codeCol);
                }
            }
        }
        return $DOM;
    }

}
