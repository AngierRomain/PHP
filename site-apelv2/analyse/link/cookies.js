/*========================================================================
 GESTION LOCALE DE COOKIES, en pseudo STATIC
*/

function GestionCookie() {
	//
}
//-------------------- méthodes statiques ECMA Script 5
GestionCookie.Afficher = function(titre) {
	alert(titre + " COOKIE:"+document.cookie);
}
//
GestionCookie.Recupere  = function(nom, defaultvalue) {
	//GestionCookie.Afficher("RECUP " + nom);
	var oRegex = new RegExp("(?:; )?" + nom + "=([^;]*);?");
	if (oRegex.test(document.cookie)) {
		return decodeURIComponent(RegExp["$1"]);
	} else {
		return defaultvalue;
	}
}
GestionCookie.Memorise  = function(nom, valeur) {
	var today = new Date(), expires = new Date();
	expires.setTime(today.getTime() + (365*24*60*60*1000));
	document.cookie = nom + "=" + encodeURIComponent(valeur) + ";expires=" + expires.toGMTString();
	//GestionCookie.Afficher("MEMORISE " + nom);
}
//
GestionCookie.Efface  = function(nom) {
	document.cookie = nom +  "=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
	//GestionCookie.Afficher("EFFACE "+ nom);
}

