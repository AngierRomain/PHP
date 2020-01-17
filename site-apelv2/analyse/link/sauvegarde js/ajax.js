/*************************************************************
	CLASSE pgAjax 
	       ------
	leRepondeur = chaine contenant l'url relative du répondeur
	fctretour 	= fonction qui sera appelée pour exploiter le flux reçu
*/	
var JS_AJAX_TIMEOUT 	= {"CLASS" : "TIMEOUT", "limite" : 20000};
var JS_AJAX_ERREUR	= {"CLASS" : "ERREUR"};
function pgAjax(leRepondeur, fctRetour) {
	var xHttp = null;
	this.repondeur = leRepondeur;
	this.retour = fctRetour;
	this.message = null;
	if (window.XMLHttpRequest) {
	   xHttp = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
	} else {
	   xHttp = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
	}
	xHttp.timeout = 20000;  // 2000 ms = 2 secondes

	xHttp.onreadystatechange=function()  {
		if (xHttp.readyState==4) {  // DONE
			switch (xHttp.status) {
				case 200 :	var m = xHttp.responseText ;
							if (m=="") {m = "Temps d'attente écoulé (" + (JS_AJAX_TIMEOUT.limite) + " ms)";}
							fctRetour(m);    // HTTP OK
							
							break;
				default : 
						alert("donne avec retour : " + xHttp.status);
						fctRetour("blabla");
			}
		}
	}
	this.getStatus = function() {return xHttp.status}
	this.abort = function() {xHttp.abort();};
	this.getHttp = function() {return xHttp;};
	this.getTimeOut = function() {return xHttp.timeout};
	this.setTimeOut = function(nbMilliSec) {
		xHttp.timeout = nbMilliSec; 
		JS_AJAX_TIMEOUT.limite = nbMilliSec;
	};
}


/*************************************************************
	CLASSE pgAjaxGet hérite de pgAjax
	       ---------           ------
	leRepondeur = chaine contenant l'url relative du répondeur
				  exemple : monrepondeur.php?champ1=val1&champ2=val2
	fctretour 	= fonction qui sera appelée pour exploiter le flux reçu
*/	
function pgAjaxGet(leRepondeur, fctRetour) {
	pgAjax.call(this, leRepondeur, fctRetour);
}
pgAjaxGet.prototype.appeler = function() {
		try {
		
			with (this.getHttp()) {
				open("GET", this.repondeur, true);
				send();
				return true;
			}
		}
		catch (e) {
			afficherException(e);
			return false;
		}
	} ;

/*************************************************************
	CLASSE pgAjaxPostJson hérite de pgAjax
	       --------------           ------
	leRepondeur = chaine contenant l'url relative du répondeur
				  exemple : monrepondeur.php
	fctretour 	= fonction qui sera appelée pour exploiter le flux reçu
*/	
function pgAjaxPostJson(leRepondeur, fctRetour) {
	pgAjax.call(this, leRepondeur, fctRetour);
}

pgAjaxPostJson.prototype.appeler = function(tableauDatas) {
		try {
			with (this.getHttp()) {
				open("POST", this.repondeur, true);
				setRequestHeader("Content-type", "application/json; charset=UTF-8");
				send(JSON.stringify(tableauDatas));
				return true;
			}
		}
		catch (e) {
			afficherException(e);
			return false;
		}	
	};

