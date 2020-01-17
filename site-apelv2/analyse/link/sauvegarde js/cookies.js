/*========================================================================

 GESTION LOCALE DE COOKIES, en pseudo STATIC
*/

class GestionCookie {
	//
	static Afficher(titre) {
		alert(titre + " COOKIE:"+document.cookie);
	}
	//
	static Recupere (nom, defaultvalue) {
		//GestionCookie.Afficher("RECUP " + nom);
		var oRegex = new RegExp("(?:; )?" + nom + "=([^;]*);?");
		if (oRegex.test(document.cookie)) {
			return decodeURIComponent(RegExp["$1"]);
		} else {
			return defaultvalue;
		}
	}
	static Memorise (nom, valeur) {
		var today = new Date(), expires = new Date();
		expires.setTime(today.getTime() + (365*24*60*60*1000));
		document.cookie = nom + "=" + encodeURIComponent(valeur) + ";expires=" + expires.toGMTString();
		//GestionCookie.Afficher("MEMORISE " + nom);
	}
	//
	static Efface (nom) {
		document.cookie = nom +  "=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
		//GestionCookie.Afficher("EFFACE "+ nom);
	}
}

