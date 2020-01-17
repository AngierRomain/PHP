<?php
class pgDossier extends pgItem {
	public static $extensions = ['php'];
	//public static $extensions = ['php', 'js'];
	public $fichiers;
	public $dossiers;
	
	public function __construct($theNom, $theNomCourt) {
		parent::__construct($theNom, $theNomCourt);
		$this->fichiers = array();
		$this->dossiers = array();	
		if ($handle = opendir($theNom)) {
			while (false !== ($entry = readdir($handle))) {
				switch ($entry) {
					case '.':
					case '..':break;
					default:
						$tmp = $theNom.'\\'.$entry;
						if (is_dir($tmp)) {
							if ($tmp != pgExplorer::$CurrentFolder) {
								$unDoss = new pgDossier($tmp, $entry);
								if ($unDoss->yaDesFichiers()) {
									$this->dossiers[] = $unDoss;
								}
							}
						} else {
							$pos = strrpos($tmp, '.');
							if ($pos === false) {$ext = ''; }
							else {$ext = substr($tmp, $pos+1);}
							if (in_array($ext, static::$extensions)) {
								$this->fichiers[] = new pgFichier($tmp, $entry, $ext);
							}
						}
				}
			}
			closedir($handle);
		}				
	}

	public function yaDesFichiers() {
		if (count($this->fichiers)>0) {return true;}
		foreach ($this->dossiers as $D) {
			if ($D->yaDesFichiers()) {return true;}
		}
		return false;
	} 

	public function jsonSerialize() {
		$rep = parent::jsonSerialize();
		$info = [
				'kname' 		=> md5($this->nom),
				'fichiers' => $this->fichiers,
				'dossiers' => $this->dossiers];
		return array_merge($rep, $info) ;
	}
	
	/********************************* Temporary Trash
	public function displayUl(&$nb) {
		$nb++;
		$key = 'D'.$nb;
		$this->ID = $key;
		$monId = 'LI' . $key;
		if ($this->nomcourt!='.') {
		echo '<li class="dossier"', 
				' id="', $monId,'">', 
				'<span onclick="clickOnDossier(',"'", $key,"'",');">', 
				$this->nomcourt, ' (', $this->nbFichiers(),')</span>' ; 
		}
		
		echo '<ul id="'.$key.'">' ;
		foreach ($this->dossiers as $D) {
			if ($D->yaDesFichiers()) {$D->displayUl($nb);}
			
		}
		foreach ($this->fichiers as $D) {
			$D->displayLi($nb);
		}
		echo '</ul>'; 
		if ($this->nomcourt!='.') {
		echo '</li>';
		}
	}
	
	public function nbFichiers () {
		$cpt = count($this->fichiers);
		foreach ($this->dossiers as $D) {
			$cpt += $D->nbFichiers();
		}
		return $cpt;
	}
	*/
}
?>