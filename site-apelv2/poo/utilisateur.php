<?php
class Utilisateur extends Element {
	public static function champPK(){return 'UTICode';}
	public static function SQLselect(){return 'SELECT UTICode, UTILogin, UTIPass, UTINom, UTIPrenom, UTIDateN, UTICivilite, UTIAdresse, UTITelephone, UTIEmail, UTINature FROM utilisateur';}
	
	public static function SQLInsert() {
		return 'INSERT INTO utilisateur
		(UTICode, UTILogin, UTIPass, UTINom,
		UTIPrenom, UTIDateN, UTICivilite, 
		UTIAdresse, UTITelephone, UTIEmail,
		UTINature) VALUES(?,?,?,?,?,?,?,?,?,?,?)';
	}
	
	public static function SQLUpdate() {
		return 'UPDATE utilisateur
		SET UTICode=?,
		UTILogin=?, 
		UTIPass=?, 
		UTINom=?, 
		UTIPrenom=?, 
		UTIDateN=?, 
		UTICivilite=?, 
		UTIAdresse=?, 
		UTITelephone=?, 
		UTIEmail=?, 
		UTINature=?
		WHERE UTICode=?';
	}
	
	public static function SQLDelete() {
		return 'DELETE FROM utilisateur 
		WHERE UTICode=?';
	}
	
	public static function SQLdoMAJ($datas) {
		$oldkey = $datas['oldkey'];
		$suppr = $datas['suppr'];
		$C = $datas['civ'];
		$key = $datas['key'];
		$N = $datas['nom'];
		$PN = $datas['pren'];
		$DN = $datas['daten'];
		$L = $datas['login'];
		$P = $datas['pass'];	
		$A = $datas['adresse'];
		$T = $datas['tel'];
		$M = $datas['mail'];
		$NA = $datas['nature'];
		$LESI = SI::getSI();
		if ($oldkey == '0') {
			return $LESI->SGBDexecuteQuery(
				static::SQLInsert(), $key, $L, $P, $N, $PN, $DN, $C, $A, $T, $M, $NA);
		} 
		if ($suppr == '-1') {
			return $LESI->SGBDexecuteQuery(
				static::SQLDelete(), $oldkey);
		} else {
			return $LESI->SGBDexecuteQuery(
				static::SQLUpdate(), $key, $L, $P, $N, $PN, $DN, $C, $A, $T, $M, $NA, $oldkey);
		}
	}
	

	public static function SQLConnex($datas) {
		$key = $datas['key'];
		//var_dump($key);
		$P = $datas['pass'];
		//var_dump($P);
		//$LESI = SI::getSI();
		
		$oUser = parent::findFromPK($key);
		if ($oUser == null) return static::connex_Redirect(-1);
		
		if ($P == $oUser->getPswd()) {
			echo '<h2>'.$oUser.'</h2>';
			echo '<p> Login : '.$oUser->getLogin().'</p>';
			echo '<p> Password : '.$oUser->getPswd().'</p>';
			echo '<p> Nature : '.$oUser->getNatureLibelle().'</p>';
		
			$_SESSION['id'] = $key;
			//var_dump($_SESSION['id']);
			return static::connex_Redirect(0);
		} else {
			return static::connex_Redirect(-2);
		}
	}
	
	public static function connex_Redirect ($uneRep) {
		switch ($uneRep) {
			case -1 :
				echo '<h3>Incorrect Login(error -1)</h3>';
				break;
			case 0 :
				echo '<h3>Connexion OK</h3>';
				//verifNature();
				break;
			case -2 :
				echo '<h3>Incorrect password(error -2)</h3>';
				break;
			default :
				echo '<h3>error unknow(error 666)</h3>';
				break;
		}
	}
	


	public function __CONSTRUCT($uneLigne){
		parent::__CONSTRUCT($uneLigne);
	}
	
	public function getIdUtilisateur() {return $this->getFld('UTICode');}
	public function getLogin() {return $this->getFld('UTILogin');}
	public function getPswd() {return $this->getFld('UTIPass');} 
	public function getNom() {return $this->getFld('UTINom');} 
	public function getPrenom() {return $this->getFld('UTIPrenom');} 
	public function getDateN() {return $this->getFld('UTIDateN');} 
	public function getCivilite() {return $this->getFld('UTICivilite');} 
	public function getAdresse() {return $this->getFld('UTIAdresse');} 
	public function getTel() {return $this->getFld('UTITelephone');}
	public function getMail() {return $this->getFld('UTIEmail');} 	
	public function getNature() {return $this->getFld('UTINature');} 
	
	public function getPN() {
		return $this->getPrenom(). ' ' . $this->getNom();
	} 
	
	public function getNP() {
		return  $this->getNom(). ' ' .$this->getPrenom();
	}
	
	public function getstrCivilite() {
		switch ($this->getCivilite()) {
			case 1:
				return 'Homme';
				break;
			case 2:
				return 'Femme';
				break;
			case 3:
				return 'Autre';
				break;
		}
	}

	public function getNatureLibelle() {
		switch ($this->getFld('UTINature')) {
			case 0 :
				return 'Nouveau membre'; 
				break;
			case 2 :
				return 'Parent - non validé'; 
				break;
			case 3 :
				return 'Parent'; 
				break;
			case 7 :
				return 'Parent - Adhérent'; 
				break;
			case 13 :
				return 'Adhérent - Membre du bureau'; 
				break;
			case 15 :
				return 'Parent - Adhérent - Membre du bureau';
				break;
			
			default :
				return 'Situation inconnue';
				break;
		}
	} 

	public function jsonSerialize() {
		$rep = parent::jsonSerialize();
		$rep['login'] = $this->getLogin();
		$rep['pass'] = $this->getPswd();
		$rep['nom'] = $this->getNom();
		$rep['prenom'] = $this->getPrenom();
		$rep['daten'] = $this->getDateN();
		$rep['civilite'] = $this->getCivilite();
		$rep['adresse'] = $this->getAdresse();
		$rep['tel'] = $this->getTel();
		$rep['mail'] = $this->getMail();
		$rep['nature'] = $this->getNature();
		return $rep;
	}
	
}

class Utilisateurs extends Elements {
	public function __CONSTRUCT(){
		parent::__CONSTRUCT();
	}
}

?>