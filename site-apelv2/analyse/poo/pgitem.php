<?php
abstract class pgItem implements JsonSerializable {
	public $nom;
	public $nomcourt;
	public $ID;
	
	public function __construct($theNom, $theNomCourt) {
		$this->nom = $theNom ;
		$this->nomcourt = $theNomCourt ;
		$this->ID = null;
	}	
	
	public static function comparer(pgItem $o1, pgItem $o2) {
		if ($o1==$o2) {return 0;}
		$n1 = $o1->nomcourt;
		$n2 = $o2->nomcourt;
		return strcasecmp ( $n1 , $n2 );
	} 
	
	public function jsonSerialize() {
		return
			  ['CLASS' => get_class($this),
				 'id' => $this->ID,
				 'nom' => utf8_encode($this->nom),
				 'nomcourt' => utf8_encode($this->nomcourt)];
	}
}



?>