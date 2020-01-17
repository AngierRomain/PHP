<?php
class Balise {
	private $TYPE;
	/*mémo des styles*/
	/*mémo des classes CSS*/
	/*mémo des autres attributs*/
	/*mémo du contenu*/
	private $T_STYLES;//tableau associatif
	private $T_CLASSES;//tableau non associatif
	private $T_ATTRIBS;// tableau associatif
	private $T_CONTENU;// tableau non associatif
	
	public function __construct($type){
		$this->TYPE = $type;
		$this->T_STYLES = array();
		$this->T_CLASSES = array();
		$this->T_ATTRIBS = array();
		$this->T_CONTENU = array(); 
	}
	
	/*renvoie la balise open*/
	public function getOpen() {
		$tmp = '<' . $this->TYPE;
		/*Exploitement des attributs et des styles*/
		foreach($this->T_ATTRIBS as $KA => $VA){
			$tmp .= ' '.$KA.'="'.$VA.'"';
		}
		$tmp .= $this->getClass();
		$tmp .= $this->getStyle();
		$tmp .= '>';
		return $tmp;
	}
	/*Construit l'attribut style*/
	public function getStyle(){
		if (count($this->T_STYLES) == 0) return '';
		$tmp = ' style="';
		foreach ($this->T_STYLES as $KS => $ST) {
			$tmp .= $KS.':'.$ST.'; ';
		}
		return $tmp.'"';
	}
	
	/*realise l'attribut particulier class*/
	public function getClass(){
		if (count($this->T_CLASSES) == 0) return '';
		return ' class="'.implode(' ', $this->T_CLASSES). '"';
	}
	
	/*renvoi la balise close*/
	public function getClose() {
		$tmp = '</'. $this->TYPE;
		$tmp .= '>';
		
		return $tmp;
	}
	
	/*Afficage par ECHO*/
	public function display ($joli = false, $dec = '') {
		if ($joli) echo PHP_EOL, $dec;
		echo $this->getOpen();
		
		foreach($this->T_CONTENU as $unItem) {
			/*test si item est un objet balise*/
			if (is_a($unItem, 'Balise')) {
				$unItem->display($joli, $dec."\t");
			}else {
				echo $unItem;
			}
		}
		if ($joli) echo PHP_EOL, $dec;
		echo $this->getClose();
	}
	
	/*Ajouter du contenu*/
	public function add($value) {
		if ($value == null) return;
		$params = func_get_args();
		foreach($params as $unParam) {
			$this->T_CONTENU[] = $unParam;
		}
	}
	
	/*Ajoute une attribut
	@$name : nom de l'attribut
	@$value : valeur de l'attribut*/
	public function addAttribut($name, $value) {
		$this->T_ATTRIBS[$name] = $value;
	}
	/*défini l'attribut id
	@$value : valuer de l'attribut*/
	public function setID($value){
		$this->addAttribut('id', $value);
	}
	/*Ajoute une élément de styles*
	@$name : nom de l'élément de styles*
	@$value : valeur de l'attribut*/
	public function addStyle($name, $value) {
		$this->T_STYLES[$name] = $value;
	}
	
	/*Ajoute une classe CSS ou plusieurs*
	@$name : nom de la classe css*
	@$value : valeur de l'attribut*/
	public function addClass($name) {
		$params = func_get_args();
		foreach($params as $unParam) {
			$this->T_CLASSES[] = $unParam;
		}
	}
	
	
	
	
}

?>