<?php
class pgFichier extends pgItem {
	public static $JSON_CONTENU = false;
	public $nbl;
	private $exten;
	public function __construct($theNom, $theNomCourt, $theExtension) {
		parent::__construct($theNom, $theNomCourt);
		$this->exten = $theExtension;
		$fp=fopen($theNom,"r");
		$cpt = 0;
		while($line = fgets($fp)) {
				$cpt++;
		}
		fclose($fp);
		$this->nbl = $cpt;
	}
			
	public function jsonSerialize() {
		$rep = parent::jsonSerialize();
		$infof = stat($this->nom);
		$rep ['extension'] = $this->exten;
		$rep ['nblignes'] = $this->nbl;
		$rep ['taille'] = $infof['7'];
		$rep ['dtcreat'] = Date('Y-m-d H:i:s',$infof['8']);
		$rep ['dtmodif'] = Date('Y-m-d H:i:s',$infof['9']);
		$rep ['dtacces'] = Date('Y-m-d H:i:s',$infof['10']);
		$rep ['kname'] = md5($this->nom) ;
		$rep ['kfile'] = md5_file($this->nom) ;
		if (static::$JSON_CONTENU) {
			//$lignes = array();
			//$fp = fopen($this->nom,"r");
			//while($line = fgets($fp)) {
			//		$lignes[] = $line;
			//}
			//fclose($fp);
			//$rep ['contenu'] = $lignes;
		}
		return $rep ;
	}		
	
	/********************** Temporary Trash
	function displayLi(&$nb) {
			$nb++;
			$monId = 'F'.$nb;
			$this->ID = $monId;
			echo '<li class="fichier" id="'.$monId.'" onclick="clickOnFichier(this);"><span>', $this->nomcourt, ' (', joliEntier($this->nbl) ,' o)</span><span></span></li>';}
	*/
}
?>