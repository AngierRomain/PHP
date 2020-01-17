<?php
class pgExplorer {
	private	$fichiers;
	private	$rep;
	public 	$o_Dossier;
	
	public static $CurrentFolder; 
	public function __construct($repDeBase = null) {
		$this->fichiers = array();
		$tmp = getcwd();
		static::$CurrentFolder = $tmp;			
		if ($repDeBase==null) {
			//remonter au dossier parent;
			$posit = strrpos($tmp, '\\');
			$this->rep = substr($tmp,0,$posit);
		} else {
			$this->rep = $repDeBase;
		}
		$this->o_Dossier = new pgDossier($this->rep, '.');
	}
	
	/******************************* Temporary TRASH
	public function presenterFichiers() {
		$this->listage($this->rep, '.',0,true);
	}	
	
	function listage($dossier, $shortname, $prof, $inclure=true) {
		if ($handle = opendir($dossier)) {
			$decalage = str_repeat("\t", $prof);
			//echo '<tr class="dossier"><td><pre>', 
			//			$decalage , $shortname,'</pre></td></tr>';
			echo '<ul class="dossier">', $shortname;
						//$decalage , $shortname,'</pre></td></tr>';							
			$prof+=1;
			$decalage = str_repeat("\t", $prof);
			// Ceci est la façon correcte de traverser un dossier. 
			while (false !== ($entry = readdir($handle))) {
				switch ($entry) {
					case '.':
					case '..':break;
					default:
						$tmp = $dossier.'\\'.$entry;
						if (is_dir($tmp)) {
							echo '<li class="dossier">';
							$this->listage ($tmp, $entry, $prof);
							echo '</li>';
						} else {
							if ($inclure && substr(strrev($entry),0,4)=='php.') {
						//		echo '<tr class="fichier"><td><pre>', 
						//$decalage , $entry,'</pre></td></tr>';
								echo '<li class="fichier">',$entry, '</li>';
								//echo "$decalage$entry\n";
								//try {
								//	require_once $tmp;
								//} catch (Exception $e) {
								//   echo $e;
								//   halt();
								//}
							}
						}
				}
			}
			echo '</ul>';
			closedir($handle);
		}		
	}
	
	function balayage($dossier, $prof, $inclure=true) {
		if ($handle = opendir($dossier)) {
			$decalage = str_repeat("\t", $prof);
			$prof+=1;
			$decalage = str_repeat("\t", $prof);
			// Ceci est la façon correcte de traverser un dossier. 
			while (false !== ($entry = readdir($handle))) {
				switch ($entry) {
					case '.':
					case '..':break;
					default:
						$tmp = $dossier.'\\'.$entry;
						if (is_dir($tmp)) {
							$this->balayage ($tmp, $prof);
						} else {
							if ($inclure && substr(strrev($entry),0,4)=='php.') {
								//echo "$decalage$entry\n";
								//try {
								//	require_once $tmp;
								//} catch (Exception $e) {
								//   echo $e;
								//   halt();
								//}
							}
						}
				}
			}
			closedir($handle);
		}		
	}
	*/
}
?>