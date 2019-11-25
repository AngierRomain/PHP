<?php

class Balise{
    private $TYPE;

    //Memo des styles
    //Memo des classes CSS
    //Memo des autres attributs
    //Memo du contenu

    private $T_STYLES; //table des styles
    private $T_CLASSES;
    private $T_ATTRIBS; //tableau associatif
    private $T_CONTENU; //tableau non associtatif
    public function __construct($typ)
    {
        $this->TYPE = $typ;
        $this->T_STYLES = array();
        $this->T_CLASSES = array();
        $this->T_ATTRIBS = array();
        $this->T_CONTENU = array();
    }

    /** renvoie la balise open */
    public function getOpen(){
        $tmp = '<' . $this->TYPE;
        //exploiter les attributs et les styles
        foreach ($this->T_ATTRIBS as $KA => $VA){ //$VA = Valeur de l'Attribut
            $tmp.= ' '.$KA.'="'.$VA.'"';
        }
        $tmp .= $this->getClass();
        $tmp .= $this->getStyle();
        $tmp .= '>';
        return $tmp;
    }
/** realise l'attribut particulier CLASS */
    public function getClass(){
        if (count($this->T_CLASSES) == 0) return '';
        return ' class="'.implode(' ', $this->T_CLASSES).'"';
    }
/** realise l'attribut particulier STYLE */
    public function getStyle(){
        if (count($this->T_STYLES) == 0) return '';
        $tmp = ' style="';
        foreach ($this->T_STYLES as $KS => $ST){
            $tmp .= $KS.':'.$ST.'; ';
        }
        return $tmp.'"';
    }

    /** renvoie la balise close */
    public function getClose(){
        $tmp = '</' . $this->TYPE;
        $tmp .= '>';
        return $tmp;
    }

    //affichage par echo
    public function display($joli = false, $dec=''){
        if ($joli)echo PHP_EOL,$dec;
        echo $this->getOpen();
        foreach ($this->T_CONTENU as $unItem){
            //test si item est un objet balise
            if (is_a($unItem, 'Balise')){
                $unItem->display($joli,$dec."\t");
            }else{
                echo $unItem;
            }
        }
        if ($joli)echo PHP_EOL,$dec;
        echo $this->getClose();
    }

    /** Ajouter du contenu */
    public function add($value){
        if ($value== null) return;
        $params = func_get_args(); //recup en array de tous les arguments
        foreach ($params as $unParam){
            $this->T_CONTENU[] = $unParam;
        }
    }

    /** ajoute un attribut
     *@$name : nom de l'attribut
     *@$value : valeur de l'attribut
     */
    public function addAttribut($name, $value){
        $this->T_ATTRIBS[$name] = $value;
    }
    /** définit l'attribut id
     * @value: valeur de l'attribut
     */
    public function setID($value){
        $this->addAttribut('id', $value);
    }

    /** ajoute un element de style
     *@$name : nom de l'élément de style
     *@$value : valeur de l'élément de style
     */
    public function addStyle($name, $value){
        $this->T_STYLES[$name] = $value;
    }

    /** ajoute une classe CSS ou plusieurs
     *@$name : nom de la classe
     */
    public function addClass($name){
            $params = func_get_args(); //recup en array de tous les arguments
            foreach ($params as $unParam){
                $this->T_CLASSES[] = $unParam;
            }
    }
}

?>