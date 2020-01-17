<?php
	abstract class pgCookieManager {
		/** constructeur inopérant */
		private function __construct() {
		}
/** Récupère la valeur du cookie dont le nom est donné en paramètre.
S'il n'existe pas, c'est la valeur par défaut qui sera renvoyée */
		public static function recupere($nomCookie, $valeurDefaut=null) {
			if (isset($_COOKIE[$nomCookie])) {return $_COOKIE[$nomCookie]; }
			return $valeurDefaut;
		}
/** Récupère la valeur du cookie dont le nom est donné en paramètre.
S'il n'existe pas, il sera créé avec la valeur du second paramètre */
		public static function recupereForce($nomCookie, $valeurforcee="") {
			if (isset($_COOKIE[$nomCookie])) {return $_COOKIE[$nomCookie];}
			setCookie($nomCookie, $valeurforcee);
			return $valeurforcee;
		}
/** Définit la valeur d'un cookie */
		public static function memorise($nomCookie, $valeur) {
			setCookie($nomCookie, $valeur);
		}
	}
?>