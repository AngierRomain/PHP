<?php
class Controleur {
	// tableau associatif des menus
	private $TB_MENUS;
	public function __construct() {
		$this->TB_MENUS = array(
			'ACC' => 'rempAccueil.php',
			'FOU' => 'rempFourniture.php',
			'USR' => 'rempUser.php',
		    'CAT' => 'rempCategorie.php',
		    'COM' => 'rempCommande.php');
		session_start();
	}
	
	/*
	Nom de menu jolie
	*/
	public function getNomJoli($code){
		switch($code) {
			case 'ACC':
				return 'Accueil';
				break;
			case 'FOU':
				return 'Fournitures';
				break;
			case 'USR':
				return 'Utilisateurs';
				break;
            case 'CAT':
                return 'Catégories';
                break;
            case 'COM':
                return 'Commande';
			default:
				break;
		}
	}
	
	/*
	renvoi la valeur d'une variable de session
	*/
	private function getVarSess($name, $defaultvalue=null) {
		if (array_key_exists($name, $_SESSION))
			return $_SESSION[$name];
		return $defaultvalue;
	}
	
	private function setCodeMenu($value) {
		$_SESSION['CODEMENU'] = $value;
	}
	
	public function getRemplisseur() {
		$fichier = $this->TB_MENUS [$this->getCodeMenu()];
		require_once 'contenus/'.$fichier;
		return new Remplisseur();
	}
	
	private function getCodeMenu() {
		return $this->getVarSess('CODEMENU', 'ACC');
	}
	
	public function displayMenu() {
		echo '<div id="topNav" class="nav">';
		foreach ($this->TB_MENUS as $k => $fich) {
			echo '<button data-key = "'.$k.'"
			onclick="mnu_select(this);">', $this->getNomJoli($k), '</button>';
		}
		echo '</div>';
	}
	
	public function DEMARRER() {
		$postdata = file_get_contents('php://input');
		if($postdata==''){
			$pageEntiere = true;
			include 'hf/debutpage.php';
			//include 'hf/rightmenu.php';
			$REMP = $this->getRemplisseur();
			$REMP->fullDisplay();
			echo "<br/>".date('H:i:s');
			include 'hf/finpage.php';		
		} else {
			$pageEntiere = false;
			echo '<br/>reçu dans controleur; $postdata = ';
			echo($postdata);	
			try{
				/*Converti la chaine json en tableau associatif*/
				$lesdatas = json_decode($postdata, true);
				if (!is_array($lesdatas)) {
					echo '<br/>Les datas pas array';
					exit(0);
				}
			}
			catch (Exception $e){
				echo '<br/>erreur json decode <br/>';
				var_dump($e);
				exit(0);
			}
			$dem = $lesdatas['demand'];
			switch ($dem) {
				case 'choixmnu':
					$this->setCodeMenu($lesdatas['key']);
					$REMP = $this->getRemplisseur();
					$REMP->fullDisplay($dem);
					break;
				default:
					break;
			}
			$REMP = $this->getRemplisseur();
			$REMP->actualiser($dem, $lesdatas);
		}
	}
}

?>