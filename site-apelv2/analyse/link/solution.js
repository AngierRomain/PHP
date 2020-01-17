var myManager = null;
var myDivFichiers = null;
var myDivAnalyse = null;

function afficherException(e) {
	alert('ERREUR\n' + e.fileName + '\nLIGNE : ' + e.lineNumber + '\nMESS  : ' + e.message );
}

function singulierPluriel(n, mot) {
	var tmp = "" + n + " " + mot;
	if (n<2) {return tmp;} else {return tmp + "s";}
}

function centrer(element) {
    demi = -Math.ceil(element.offsetWidth / 2);
	element.style.marginLeft = demi+"px" ;
	demi = -Math.ceil(element.offsetHeight / 2);
	element.style.marginTop = demi+"px" ;
}

class Element {
	constructor(objJSON) {
		this.JSON = objJSON;
	}
	get ID() 		{return this.JSON.nom;}
	get Nom()		{return this.JSON.nom;}
	get NomCourt()	{return this.JSON.nomcourt;}
	get DateModif()	{return this.JSON.dtmodif;}
}

class Fichier extends Element {
	constructor(objJSON, leDossier) {
		super(objJSON);
		this.monDossier = leDossier;
		this.numerochoix = -1;
		var leLI = document.createElement("li");
			leLI.element = this;
			leLI.classList.add("fichier");
		var leDiv = document.createElement("div");
			leDiv.element = this;
			leDiv.setAttribute("title", this.DateModif);
			leLI.appendChild(leDiv);
			var sp1 = document.createElement("span") ;
				sp1.innerHTML = this.NomCourt + " (" + singulierPluriel(this.JSON.nblignes," ligne") + ")";
				leDiv.appendChild(sp1);		
			sp1 = document.createElement("span") ;
				sp1.innerHTML = "";
				leDiv.appendChild(sp1);
			leDiv.addEventListener("dblclick", function (e) {
										this.element.DoubleClicEvent(e) ;}, false);
		leDossier.UL.appendChild(leLI);
		this.LI = leLI;
		this.DIV = leDiv;
	}
	get Taille()		{return this.JSON.taille;}
	get SelectId()		{return 'LS-'+ this.ID;}
	get Dossier() 		{return this.monDossier ;}
	get Choix()			{return this.numerochoix;}
	get	Rang()			{return this.numerochoix; }
	get NomRelatif()	{
		var longdebase = myManager.Dossier.Nom.length;
		return this.Nom.substr(longdebase);
	}
	set Rang(value)		{
		if (value != this.numerochoix) {
			var leli = this.DIV;
			if (this.numerochoix<0) {leli.classList.add("fichselect");} 
			var lespan = leli.childNodes[1];  //2nd span indquant le n° de sélection
			this.numerochoix = value;
			if (this.numerochoix<0) {
				lespan.innerHTML="";
				leli.classList.remove("fichselect");
			} else {
				var joli="";
				switch (value) {
					case 0:joli = "1<sup>er</sup>"; break;
					case 1:joli = "2<sup>nd</sup>"; break;
					default:joli = (value+1) + "<sup>ème</sup>"; break;
				}
				lespan.innerHTML=" ("+joli+" fichier)";
			}
		}
	}
	DoubleClicEvent(e)	{ 
		myManager.Selectionner(this);
	}
}

class Dossier extends Element {
	constructor(objJSON, lepapa) {
		super(objJSON);
		this.papa = lepapa;
		this.DIVUL = document.createElement("DIV"); // contient UL
		this.DIVUL.classList.add("contenudossier");
		this.UL = document.createElement("UL");
		this.DIVUL.appendChild (this.UL);
		this.UL.element = this;
		var spfich = null;
		if (lepapa!=null) {
			var leLI = document.createElement("li");
			leLI.element = this;
			leLI.classList.add("dossier");
			var undiv = document.createElement("div") ; // premiere partie
				undiv.classList.add("titredossier");
				undiv.element = this;
				undiv.addEventListener("click", function (e) {this.element.DoubleClicEvent(e) ;}, false);
				leLI.appendChild(undiv);
			var sp1 = document.createElement("span") ;
				sp1.innerHTML = this.NomCourt;
				undiv.appendChild(sp1);
			spfich = document.createElement("span") ;
				undiv.appendChild(spfich);
			leLI.appendChild(this.DIVUL);
			lepapa.UL.appendChild(leLI);
			this.LI = leLI;
		} else {
			this.LI = null;
		}
		this.mesFichiers = {};
		this.mesSousDossiers = {};
		var nb = objJSON.dossiers.length ;
		var i = 0;
		for (i=0; i < nb; i++) {
			var unDossier = new Dossier(objJSON.dossiers[i], this);
			this.mesSousDossiers[unDossier.ID] = unDossier;
		}
		nb = objJSON.fichiers.length ;
		for (i=0; i < nb; i++) {
			var unFichier = new Fichier(objJSON.fichiers[i], this);
			this.mesFichiers[unFichier.ID] = unFichier;
		}
		if (spfich != null) {spfich.innerHTML = " (" + this.NbFichiers+ ")";}
	}
	
	DoubleClicEvent(e)	{
		this.DIVUL.classList.toggle("masked");
		this.LI.classList.toggle("masked");
		myManager.FaireCookieReductions();
	}
	
	Reduire(nomdossier) {
		var monNom = this.Nom;
		if (monNom==nomdossier) {
			this.DIVUL.classList.add("masked");
			this.LI.classList.add("masked");			
			return true ;
		} else {
			if (monNom.indexOf(monNom) == 0) {
				for (var k in this.mesSousDossiers) {
					if (this.mesSousDossiers[k].Reduire(nomdossier)) {return true; }
				}
			} 
		}
		return false;
	}
	
	get Fichiers() 		{return this.mesFichiers;}
	get SousDossiers()	{return this.mesSousDossiers;}
	get Nb()			{return  Object.keys(this.mesFichiers).length;}
	get NbFichiers()	{
		var cpt = this.Nb;
		for (var k in this.mesSousDossiers) {
			cpt += this.mesSousDossiers[k].NbFichiers;
		}
		return cpt;
	}
	Enregistrer(unM) 	{
		var cpt = this.Nb;
		for (var x in this.mesFichiers) {
			unM.Memoriser(this.mesFichiers[x]);
		}
		for (var k in this.mesSousDossiers) {
			cpt += this.mesSousDossiers[k].Enregistrer(unM);
		}
		return cpt;
	}
}

var spanDragged = null;
var liDragged = null;
var fichDragged = null;
var positDragged = 0;
var cptDrag =0 ;

function dragDemarrage(ev) {
	cptDrag = 0;
	spanDragged = ev.currentTarget ;
	spanDragged.style.color = "red";
	liDragged = spanDragged.parentNode ;
	fichDragged = liDragged.element;
	positDragged = fichDragged.Rang;
	console.log("dragstart 1 :" + spanDragged.nodeName + ":" + spanDragged.innerHTML);
 	console.log("dragstart 2 :" + spanDragged.nodeName + " in LI " + liDragged.id);
	console.log("dragstart 3 :" + positDragged + " - " + fichDragged.Nom);
	ev.dataTransfer.setData("text/plain", "boujour tout va bien");
}

function dragTerminer(e) {
	e.currentTarget.style.color = "";
}

function dragDepot(event) {
	// prevent default action (open as link for some elements)
	event.preventDefault();
	try {
		//alert("drop 0");
		if ( spanDragged != event.currentTarget 
			&& event.currentTarget.className == "fichpris" ) {
			dragSortie(event);
			console.log("drop 1 ");
			var sonli = event.currentTarget.parentNode ;
			var sonfichier =  sonli.element;
			var leol = document.getElementById("listsel");
			console.log("drop 2 " + leol);
			if (sonfichier.Rang < fichDragged.Rang) {
				//montée
				leol.removeChild(liDragged);
				leol.insertBefore(liDragged, sonli);
			} else {
				//descente
				leol.removeChild(liDragged);
				leol.insertBefore(liDragged, sonli);
				leol.removeChild(sonli);
				leol.insertBefore(sonli, liDragged);
			}
			myManager.Echanger(fichDragged, sonfichier.Rang);
		}
	}
	catch (e) {
		afficherException(e);
	}
}

function dragSortie(event) {
	event.currentTarget.style.color = "";
}

function dragEntree(event) {
	event.currentTarget.style.color = "purple";
}

function dbclickfichier(ev) {
	var lespan = ev.currentTarget ;
	var leli = lespan.parentNode ;
	var lefichier = leli.element;
	myManager.Selectionner(lefichier);
}

class Manager {
	constructor(objJSON) {
		this.monDossier = new Dossier(objJSON, null);
		this.tousLesFichiers = {};
		this.monDossier.Enregistrer(this);
		this.selection = [];
		this.reductions = [];
		this.classesplus = [];
		
		this.chkResume = document.getElementById("voirresume");
		var x = GestionCookie.Recupere("resume", "N");
		this.chkResume.checked = (x == "Y");
		this.chkResume.addEventListener("click", function(e) {
			var resu = this.checked;
			var m = "";
			var a = "";
			if (resu) {m = "contenudetaille"; a="classresume";} 
			else {m = "classresume"; a = "contenudetaille"}
			GestionCookie.Memorise("resume", (resu) ? "Y" : "N");
			var masquer = document.getElementsByClassName(m);
			for (var i=0; i<masquer.length; i++)
				{masquer[i].classList.add("contenumasque");}
			var afficher = document.getElementsByClassName(a);
			for (var i=0; i<afficher.length; i++)
				{afficher[i].classList.remove("contenumasque");}
		}, false);
		
		this.chkStatic = document.getElementById("visustatic");
		x = GestionCookie.Recupere("vstatic", "Y");
		this.chkStatic.checked = (x == "Y");
		this.chkStatic.addEventListener("click"		, function (e) {
				GestionCookie.Memorise("vstatic", (this.checked) ? "Y" : "N");
				myAnalyseur.refaireContenus();
			}, false);
			
		this.chkNonStatic = document.getElementById("visunonstatic");
		x = GestionCookie.Recupere("vnotstatic", "Y");
		this.chkNonStatic.checked = (x == "Y");
		this.chkNonStatic.addEventListener("click"		, function (e) {
				GestionCookie.Memorise("vnotstatic", (this.checked) ? "Y" : "N");
				myAnalyseur.refaireContenus();
			}, false);
			
		this.chkEnCouleurs = document.getElementById("voircouleur");
		x = GestionCookie.Recupere("vcolors", "Y");
		this.chkEnCouleurs.checked = (x == "Y");
		this.chkEnCouleurs.addEventListener("click"		, function (e) {
				GestionCookie.Memorise("vcolors", (this.checked) ? "Y" : "N");
				if (!myManager.chkResume.checked) {
					myAnalyseur.refaireContenus() ;
				}
			}, false);
			
		this.chkHeritage = document.getElementById("visuherit") ;
		x = GestionCookie.Recupere("heritage", "Y") ;
		this.chkHeritage.checked = (x == "Y");
		this.chkHeritage.addEventListener("click", function (e) {
				GestionCookie.Memorise("heritage", (this.checked) ? "Y" : "N");
				myAnalyseur.refaireContenus();
			}, false);
			
		this.chkPrivate = document.getElementById("visuprivate") ;
		x = GestionCookie.Recupere("private", "N") ;
		this.chkPrivate.checked = (x == "Y");
		this.chkPrivate.addEventListener("click", function (e) {
				GestionCookie.Memorise("visuprivate", (this.checked) ? "Y" : "N");
				myAnalyseur.refaireContenus();
			}, false);
			
		this.txtClasse = document.getElementById("txtclasse");
		this.txtClasse.value = "";
		this.txtClasse.addEventListener("keydown", function (e) {
			if (e.keyCode == 13 && this.value != "") {
				try { myManager.ajouterClasse(this.value);
				myManager.FaireCookieExtra();
					this.value="";}
				catch (e) {afficherException(e);}
			}
		}, false);
		
		x = GestionCookie.Recupere("letri", "0");
		this.criteretri = parseInt(x,10);
		document.getElementById("rb"+x).checked = true;
		for (var i=0; i<=4; i++) {
			document.getElementById("rb"+i).addEventListener("click", this.choixDuTri, false);
		}
	}
	choixDuTri() {
		GestionCookie.Memorise("letri", this.value);
		myManager.criteretri = parseInt(this.value,10);
		myAnalyseur.refaireContenus();
	}
	get Dossier() 		{ return this.monDossier; }
	get NbFichiers() 	{ return Object.keys(this.tousLesFichiers).length; }
	get NbSelected()	{ return this.selection.length; }
	Memoriser(fich) 	{ this.tousLesFichiers[fich.ID] = fich; }
	RecupFichier(id) 	{ return this.tousLesFichiers[id]; }
	ajouterClasse(nom) {
		var p = this.classesplus.indexOf(nom);
		var leol = document.getElementById("listcla");
		if (p<0) {
			this.classesplus.push(nom);
			var li = document.createElement("li");
			li.innerHTML = nom;
			li.addEventListener("dblclick", function (e) {
				myManager.ajouterClasse(this.innerHTML);
				myManager.FaireCookieExtra();
			}, false);
			leol.appendChild(li);
		} else {
			this.classesplus.splice(p, 1);
			leol.removeChild(leol.childNodes[p]);
		}
		this.ControlerBoutons();
	}
	Echanger(fich, newposition)	{ 
		var n1 = fich.Rang;
		this.selection.splice(n1,1);
		this.selection.splice(newposition,0,fich) ;
		var deb = Math.min(n1, newposition) ;
		var fin = Math.max(n1, newposition) ;
		for (var i=deb; i<= fin; i++) {
			this.selection[i].Rang = i;
		}
		this.FaireCookieFichiers();
	}
	DumpConsole() {
		for (var k in this.tousLesFichiers) {
			console.log(k + " => " + this.tousLesFichiers[k].Nom);
		}
	}
	viderSelection() {
		this.classesplus = [];
		var olcl = document.getElementById("listcla");
		olcl.innerHTML = "";
		while (this.selection.length>0) {
			var f = this.selection[0];
			this.Selectionner(f);
		}
		this.ControlerBoutons();
		this.FaireCookieFichiers();
		this.FaireCookieExtra();
	}
	FaireCookieExtra() {
		if (this.classesplus.length==0) {GestionCookie.Efface("extra");}
		else {
			var tmp = ""
			for (var i=0; i<this.classesplus.length; i++) {
				if (i>0) {tmp += "$";}
				tmp += this.classesplus[i];
			}
			GestionCookie.Memorise("extra", tmp);
		}
	}
	
	RecupCookieExtra() {
		var cook = GestionCookie.Recupere("extra", "");
		if (cook!="") {
			var lesnoms = cook.split("$");
			for (var i=0; i<lesnoms.length;i++) {
				var n = lesnoms[i];
				this.ajouterClasse(n);
			}
		}
	}

	FaireCookieFichiers() {
		if (this.selection.length==0) {GestionCookie.Efface("fich");}
		else {
			var tmp = ""
			for (var i=0; i<this.selection.length; i++) {
				if (i>0) {tmp += "$";}
				tmp += this.selection[i].Nom;
			}
			GestionCookie.Memorise("fich", tmp);
		}
	}
	
	FaireCookieReductions () {
		try {
			var elems = document.querySelectorAll("li.masked");
			var tmp = "";
			for (var i=0; i<elems.length; i++) {
				if (i>0) {tmp+="$";}
				tmp += elems[i].element.Nom ;
			}
			if (tmp=="") {GestionCookie.Efface("red");}
			else {GestionCookie.Memorise("red", tmp);}
		}
		catch (e) { afficherException(e); }
	}
	
	FaireCookieProgrammeur () {
		try {
			var tmp="";
			for (var k in myAnalyseur.Classes) {
				var cl = myAnalyseur.Classes[k];
				if ((cl.modevisu!=0) && (cl.JSON.fichiersource != null)) {
					if (tmp!="") {tmp += "$";}
					tmp += cl.Name + "$" + cl.JSON.fichiersource + "$" + cl.modevisu;
				}
			}
			if (tmp=="") {GestionCookie.Efface("programmeur");}
			else {GestionCookie.Memorise("programmeur", tmp);}
		}
		catch (e) {afficherException (e);}
	}
	
	RecupCookiePogrammeur () {
		var cook = GestionCookie.Recupere("programmeur", "");
		if (cook!="") {
			var tmp="";
			var lesnoms = cook.split("$");
			for (var i=0; i<lesnoms.length;i=i+3) {
				var n = lesnoms[i];
				var cl = null;
				cl = myAnalyseur.Classes[n];
				if (cl != null) {
					cl.redefProgrammeur( parseInt(lesnoms[i+2],10));
				}
			}
		}
	}
	
	RecupCookieReductions () {
		var cook = GestionCookie.Recupere("red", "");
		if (cook!="") {
			var tmp="";
			var lesnoms = cook.split("$");
			for (var i=0; i<lesnoms.length;i++) {
				var n = lesnoms[i];
				if (this.monDossier.Reduire(n)) {
					if (tmp!="") {tmp+="$";}
					tmp+=n;
				}
			}
			GestionCookie.Memorise ("red", tmp);
		}
	}
	
	RecupCookieFichiers() {
		var cook = GestionCookie.Recupere("fich", "");
		if (cook!="") {
			var lesnoms = cook.split("$");
			for (var i=0; i<lesnoms.length;i++) {
				var n = lesnoms[i];
				var obj = this.tousLesFichiers[n];
				if (obj!=null) {
					this.Selectionner(obj);
				}
			}
		}
	}
	
	Selectionner(fich)	{
		var posit 	= this.selection.indexOf(fich);
		var x 		= document.getElementById("listsel");
		if (posit < 0) {
			fich.Rang = (this.selection.push (fich)-1);    
			var newLI 	= document.createElementNS(null,"li");
				newLI.element = fich;
				var lespan = document.createElement ("span");
				lespan.classList.add("fichpris")
				lespan.innerHTML = fich.NomRelatif;
				newLI.appendChild(lespan);
				lespan.draggable = true;
				x.appendChild(newLI);
			  /* events fired on the draggable target */
			lespan.addEventListener("drag"		, function (e) {
				cptDrag++;
				//console.log("drag ... " + cptDrag);
			}, false);
			//lespan.addEventListener("click"		, clickfichier, false);
			lespan.addEventListener("dblclick"	, dbclickfichier, false);	
			lespan.addEventListener("dragstart"	, dragDemarrage, false);
			lespan.addEventListener("dragend"	, dragTerminer, false);		
			/* events fired on the drop targets // prevent default to allow drop */
			lespan.addEventListener("dragover", function( event ) { event.preventDefault();}, false);
			lespan.addEventListener("dragenter"	, dragEntree, false);
			lespan.addEventListener("dragleave"	, dragSortie, false);
			lespan.addEventListener("drop"		, dragDepot, false);

			newLI.classList.add("fichierchoisi");

		} else {
			fich.Rang = -1;
			this.selection.splice (posit,1);
			for (var i=posit; i<this.selection.length; i++) {
				this.selection[i].Rang = i;
			}
			var oldi = x.childNodes[posit+1] ;
			x.removeChild(oldi);
		}
		this.FaireCookieFichiers();
		this.ControlerBoutons();
	}
	
	ControlerBoutons() {
		var obj1 = document.getElementById("btnvalider");
		var obj2 = document.getElementById("btnvider");
		var disab = (this.selection.length==0 && (this.classesplus.length == 0)) ;
		obj1.disabled = disab;
		obj2.disabled = disab;
		var obj1 = document.getElementById("divbtn");
		if (disab) {obj1.classList.add("interdit");} else {obj1.classList.remove("interdit");}
	}
}

function demander() {
	var tmp = null;
	var traiter = function (texte) {
		try {
			var oDiv = document.getElementById("result") ;
			oDiv.innerHTML = texte;
			var oj = oDiv.getElementsByClassName("enjson");
			var ocl = oDiv.getElementsByClassName("choixclasse");
			var src="";
			if (oj.length>0) {
				src = oj[0].innerHTML;
				try {
					var test = JSON.parse(src);
					myAnalyseur = new Analyseur(test);
					myDivFichiers.classList.add("etatoff");
					var divDesClasses = document.getElementById("lstclasses");
					divDesClasses.innerHTML = "";
					var lechoix = document.getElementById("choixclasse");
					divDesClasses.appendChild(myAnalyseur.makeDiv());
					myDivAnalyse.classList.remove("etatoff");				
					
					try { 
						 var cc = myAnalyseur.makeDivChoix(lechoix);
						 myManager.RecupCookiePogrammeur ();
						 }
					catch (e) {	afficherException (e);}	
					myAnalyseur.voirPremiere();
				}
				catch(e) { 
					afficherException(e);
				}
			} else { 
				var oInfo = document.getElementById("dialtexte");
				oInfo.innerHTML = texte;
				centrer(document.getElementById("pgdialogue"));
				oInfo = document.getElementById("pginformation");
				oInfo.classList.remove("visuno");
			}
		}
		catch (e) {
			afficherException (e);
		}
	}
	try {
		var datas = {};
		datas["operation"] = "analyse";
		var fichiers = [];
		var s = myManager.selection;
		for (var i=0; i< s.length;i++) {
			fichiers.push(s[i].Nom);
		}
		datas["fichiers"] = fichiers;
		datas["extras"] = myManager.classesplus;
		tmp = new pgAjaxPostJson("", traiter);
		tmp.setTimeOut(10000); // 10 secondes
		tmp.appeler(datas);
		return false;
	}
	catch (e) {
		afficherException(e);
	}
}

function fermerAnalyse() {
	myDivFichiers.classList.remove('etatoff');
	myDivAnalyse.classList.add('etatoff');
}

function viderListe() {
	myManager.viderSelection();
}

function pgScroll() {
	var inmove = false;
	var X = 0; 
	var Y = 0;
	var DEB = 0;
	var obj = document.getElementById("choixclasse");
	var btdebut = document.getElementById("prevbtn");
	var btfin = document.getElementById("nextbtn");
	var btavant = document.getElementById("avantbtn");
	var btapres = document.getElementById("apresbtn");

	this.start = function(e) {
		inmove = true;
		console.log(e);
		console.log("coucou");
		X = e.clientX ;
		Y = e.clientY ;
		DEB = obj.scrollLeft;
		masquesdeb = [];
		masquesfin = [];
		nonmasques = [];
	}
	
	this.moving = function (e) {
		if (!inmove) {return false;}
		console.log("moving " + (X-e.clientX));
		obj.scrollLeft = DEB + (X-e.clientX) ;
		myScroll.analyse(0);
	}

	this.stop = function (e) {
		inmove = false;
		console.log("stop moving");
	}
	
	this.analyse = function () {
		//console.log ("scrollleft = " + n);
		//console.log("---------------------");
		var nb = obj.children.length;
		var RB = obj.getBoundingClientRect();
		//console.log(RB);
		//console.log("**************");
		var avant = false;
		var apres = false;
		masquesdeb = [];
		masquesfin = [];
		var from0 = -obj.children[0].getBoundingClientRect().x;
		//console.log("from 0 : " + from0);
		//console.log("**************");
		for (var i=0; i<nb; i++) {
			var item = obj.children[i];
			var R = item.getBoundingClientRect();
			//console.log(R);
			if (R.x + (R.width/2) < RB.x ) {
				avant = true;
				masquesdeb.push(item);
				//console.log(item.textContent + ": avant son x " + R.x);
				//console.log(R);
				item.classList.add("visuno");
			} else {
				if (R.right - (R.width/2) > RB.right) {
					apres = true;
					masquesfin.push(item);
					//console.log(item.textContent + ": apres son right " + R.right);
					//console.log(R);
					item.classList.add("visuno");
				} else {
					item.classList.remove("visuno");
					//console.log(item.textContent + ": visible");
				}
			}
		}
		if (avant) {
			btdebut.classList.remove("visuno");
			btavant.classList.remove("visuno");
		} else {
			btdebut.classList.add("visuno");
			btavant.classList.add("visuno");			
		}
		if (apres) {
			btfin.classList.remove("visuno");
			btapres.classList.remove("visuno");
		} else {
			btfin.classList.add("visuno");
			btapres.classList.add("visuno");			
		}
	}
	this.audebut = function(e) {
		obj.scrollLeft = 0;
		this.element.analyse();

	}
	
	this.alafin = function(e) {
		if (obj.scrollWidth > obj.clientWidth) 
		{ 	obj.scrollLeft = (obj.scrollWidth - obj.clientWidth);
			this.element.analyse();}
	}
	
	this.allerapres = function (e) {
		var cach = masquesfin[0];
		var R = cach.getBoundingClientRect();
		var RB = obj.getBoundingClientRect();
		obj.scrollLeft += (R.right+1 - RB.right);
		myScroll.analyse(0);
	}
	
	this.alleravant = function (e) {
		var cach = masquesdeb[masquesdeb.length-1];
		console.log("faire apparaitre " + cach.textContent);
		var R = cach.getBoundingClientRect();
		var RB = obj.getBoundingClientRect();
		obj.scrollLeft -= (RB.x+1 - R.x);
		myScroll.analyse(0);
	}
	
	btdebut.element = this;
	btdebut.addEventListener("click", this.audebut, false);
	
	btfin.element = this;
	btfin.addEventListener("click", this.alafin, false);
	
	btapres.element = this;
	btapres.addEventListener("click", this.allerapres, false);
	
	btavant.element = this;
	btavant.addEventListener("click", this.alleravant, false);
}

//var myScroll = null;

window.onload = function() {
	try {
		myDivFichiers = document.getElementById("fichiersetdossiers");
		myDivAnalyse = document.getElementById("analyse");
		var obj = document.getElementById("jsonpre");
		var om = JSON.parse(obj.innerHTML);
		obj.innerHTML="";
		myManager = new Manager(om);
		myManager.RecupCookieFichiers();
		myManager.RecupCookieReductions();
		myManager.RecupCookieExtra() ;
		myManager.ControlerBoutons();
		var lediv = document.getElementById("detailsfichiers");
		lediv.appendChild(myManager.monDossier.UL);		
		var lespan = document.getElementById("nbfichiers");
		lespan.innerHTML = myManager.NbFichiers;
		var finDial = document.getElementById("dialbtnok");
		finDial.addEventListener("click", function (e) {
			oInfo = document.getElementById("pginformation");
			oInfo.classList.add("visuno");
		}, false);
	}
	catch (e) {
		afficherException(e);
	}
}
