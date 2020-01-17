var myAnalyseur = null;
var classevue = null;

function ajouterColonne(unRow, unTexte) {
	var x = document.createTextNode(unTexte);
	var cell = unRow.insertCell(-1) ;
	cell.appendChild(x);
	return cell;
}

function ajouterCommentaire(unObjet, tableRef, heritage) {
	var unTexte = unObjet.JSON.comment;
	if (unTexte=="") {return null;}
	if (unTexte==null) {return null;}
	var trait = tableRef.insertRow(tableRef.rows.length) ;
		trait.classList.add("trait");
		trait.innerHTML="<td></td><td></td><td></td>";
	var newRowCom  = tableRef.insertRow(tableRef.rows.length);
		newRowCom.classList.add("comment");
	if (heritage>0 && myManager.chkEnCouleurs.checked) {
		newRowCom.classList.add("heritage");
		newRowCom.classList.add("herit" + heritage);
	} else {
		if (tableRef.fairecouleur) {newRowCom.classList.add("couleur");}
	}
	newRowCom.insertCell(-1) ; // colonne blanche
	var cell = newRowCom.insertCell(-1) ;
	cell.setAttribute("colspan","2");
	//var JD = new JavaDoc;
	//var regex = /^(.*)\@param(.*)$/mgi;
	//var matches = unTexte.match(regex);
	//if (matches!=null) {
	//	for (var i=0; i<matches.length; i++) {
	//		var tr = matches[i];
	//		var posit = tr.indexOf("param");
	//		JD.params.push(tr);
	//		//alert(matches[i] + " in " + unRow.element.Name);
	//	}
	//	unTexte = unTexte.replace(regex, "");
	//	unTexte = unTexte.replace(/^(\t|\s)*\n$/mg, "");
	//	for (var xxx = 0; xxx < JD.params.length; xxx++) {
	//		var phrase = JD.params[xxx];
	//		var posit = phrase.indexOf("@param") ;
	//		var myRegexp = /(\@param)\b(^(.)*$)/gm;
	//		var match = myRegexp.exec(phrase);
	//		//if (match) (alert("xxxxx ++"+match[1])); 
	//		var debut = phrase.substr(posit+7);
	//		alert (phrase + "--->" + phrase.match(/(\w+)\b(.*)$/));
	//		//alert (.exec(phrase));
	//		//alert("" + xxx + " +++" + debut + "+++");
	//	}
	//}
	var cap = document.createElement("pre");
	unTexte = unTexte.replace(/^(\t|\s)*\n$/mg, "");
	unTexte = "/**" + unTexte + "*/";	
	cap.innerHTML = unTexte;
	cell.appendChild(cap);
	return cell;
}
function ajouterColonneSpan(unRow, unTexte) {
	var cap = document.createElement("span");
	cap.innerHTML = unTexte ;
	unRow.insertCell(-1).appendChild(cap);
	return cap;
}
// exemple "attributs" , "Liste des attributs"
function creerTbody(classe, titre) {
	var tbod = document.createElement("TBODY");
	tbod.classList.add(classe); 
	var cap = document.createElement("TR");
	var untd = document.createElement("TD");
		untd.classList.add("titre");
		untd.setAttribute("colspan",3);
		untd.innerHTML = titre;
	cap.appendChild(untd);
	tbod.appendChild(cap); 
	tbod.fairecouleur = true;
	return tbod;
}

function creerSpan(texte) {
	var cap = document.createElement("span");
	var tmp = texte.replace("\n","<br>");
	cap.innerHTML = tmp;
	return cap;
}

function creerTxtNode(texte) {
	return document.createTextNode(texte);
}

function creerHeaderBody(texte) {
	var cap = document.createElement("TR");
	var untd = document.createElement("TD");
		untd.classList.add("titre");
		untd.appendChild(creerTxtNode(texte));		
		untd.setAttribute("colspan",2);
	cap.appendChild(untd);
	return cap;
}

class Constante {
	constructor (oJSON, oClasse) {
		this.JSON = oJSON;
		this.Classe = oClasse;
	}
	get Name()		{return this.JSON.name;}
	get MaValeur()	{
		var x = this.JSON.valeur ;
		var tof = (typeof x) ;
		if (tof == "string") {x = '"' + x + '"';}
		var tmp = x + " (" +  tof + ")";
		return tmp;
	}
	
	createTableRow(tableRef, heritage) {
		var newRow  = tableRef.insertRow(tableRef.rows.length);
		newRow.classList.add("constante");
		if (tableRef.fairecouleur) {newRow.classList.add("couleur");}
		newRow.element = this;
		var tmp = (heritage==0) ? "" : "(" + this.Classe.Name + ") ";
		tmp += this.Name + " = " + this.JSON.valeurtxt;
		ajouterColonne(newRow, "const");
		ajouterColonne(newRow, tmp).setAttribute("colspan","2");
		tableRef.fairecouleur = !tableRef.fairecouleur;
		return newRow;
	}
}
class Attribut {
	constructor (oJSON, oClasse) {
		this.JSON = oJSON;
		this.Classe = oClasse;
		this.niveau = 0;
	}
	get Name()		{return this.JSON.name;}
	
	createTableRow(tableRef, heritage) {
		ajouterCommentaire(this, tableRef, heritage);
		var newRow  = tableRef.insertRow(tableRef.rows.length);
		newRow.classList.add("attribut");
		newRow.classList.add(this.JSON.access);
		if (this.JSON.static) {	newRow.classList.add("static");	}
		newRow.element = this;
		var prefixherit = "";
		if (heritage>0) {prefixherit = "(" + this.Classe.Name + ") ";}
		if ((heritage > 0) && myManager.chkEnCouleurs.checked) {
			newRow.classList.add("heritage");
			newRow.classList.add("herit" + heritage);
		} else {
			if (tableRef.fairecouleur) {newRow.classList.add("couleur");}
			tableRef.fairecouleur = !tableRef.fairecouleur;
		}
		ajouterColonne(newRow, "");
		var tmp = prefixherit + this.JSON.access ; 
		tmp += (this.JSON.static) ? " static"  : ""
		ajouterColonne(newRow, tmp);
		tmp="";
		tmp = "$" + this.Name;		
		ajouterColonne(newRow, tmp);
		return newRow;
	}
	
	createSummaryLine() {
		var oDiv = document.createElement("div");
		oDiv.classList.add("aresume");
		var lepre = document.createElement("span");
		var tmp = this.JSON.access;
		tmp += (this.JSON.static) ? " static"  : ""
		lepre.innerHTML = tmp + " <b>$" + this.Name + "</b>";
		oDiv.appendChild(lepre);
		if (this.niveau > 0) {
			lepre = document.createElement("span");
			lepre.classList.add("fromclasse");
			lepre.innerHTML = "(" + this.Classe.Name + ")";
			oDiv.appendChild(lepre);
		}
		oDiv.element = this;
		return oDiv;
	}
}

class Methode {
	constructor (oJSON, oClasse) {
		this.JSON = oJSON;
		this.Classe = oClasse;
		this.niveau = 0;
	}
	get Name()		{return this.JSON.name;}
	get IsClasse()	{return !this.JSON.isinterface;}
	
	createSummaryLine() {
		var oDiv = document.createElement("div");
		oDiv.classList.add("mresume");
		var suffixherit = "";
		if (this.niveau > 0) {suffixherit = " (<i>" + this.Classe.Name + "</i>)";}
		if (this.Classe.JSON.fichiersource==null) {
			oDiv.innerHTML = "function " + this.Name + "()" + suffixherit;
		} else {
			var tmp = this.Classe.JSON.fichiersource;
			var lediv = myAnalyseur.Sources[tmp];
			var lefrom = this.JSON.linedeb;
			var php = lediv.children[lefrom-1].textContent;
			php = php.replace(/\t/gi, "");
			php = php.replace(/(\{.*)/gi, "");
			var plus = this.Name;
			var plus2 = "<b>" + plus + "</b>";
			php = php.replace(plus, plus2);
			var lepre = document.createElement("span");
			lepre.innerHTML = php ;

			oDiv.appendChild(lepre);
			if (this.niveau > 0) {
				lepre = document.createElement("span");
				lepre.classList.add("fromclasse");
				lepre.innerHTML = "(" + this.Classe.Name + ")";
				oDiv.appendChild(lepre);
			}
		}
		oDiv.element = this;
		if (this.Classe.JSON.fichiersource!=null) {
			oDiv.addEventListener("click", function (e) {
				myAnalyseur.displayMethode(this.element) ;
				}, false);
		}
		return oDiv;
	}
	createTableRow(tableRef, heritage) {
		ajouterCommentaire(this, tableRef, heritage);
		var newRow  = tableRef.insertRow(tableRef.rows.length);
		newRow.classList.add("methode");
		var tmp = (this.JSON.static) ? "static" : "";
		var prefixherit = "";
		if (heritage>0) {prefixherit = "(" + this.Classe.Name + ") ";}
		if ((heritage > 0) && myManager.chkEnCouleurs.checked) {
			newRow.classList.add("heritage");
			newRow.classList.add("herit" + heritage);
		} else {
			if (tableRef.fairecouleur) {newRow.classList.add("couleur");}
			tableRef.fairecouleur = !tableRef.fairecouleur;
		}

		if (this.JSON.static) {	newRow.classList.add("static");	}
		if (this.JSON.final) {newRow.classList.add("final"); tmp+=" final";}
		newRow.classList.add(this.JSON.access);
		newRow.element = this;
		if (this.Classe.JSON.fichiersource==null) {
			ajouterColonne(newRow, "");
		} else {
			var tmplines = "" + this.JSON.linedeb + "-" + this.JSON.linefin;
			var sp1 = ajouterColonneSpan(newRow, tmplines);
			sp1.classList.add("liensource");
			sp1.element = this;
			sp1.addEventListener("click", function (e) {
				myAnalyseur.displayMethode(this.element) ;
				}, false);
		}
		var tmp = prefixherit + this.JSON.access;
		tmp += (this.JSON.static) ? " static" : "";
		if (this.JSON.abstract) {tmp += " abstract";}
		if (this.JSON.final) {tmp+=" final" ;}
		ajouterColonne(newRow, tmp);
		tmp = "";
		
		tmp = this.Name + "(";
		for (var i=0; i <this.JSON.parametres.length; i++) {
			if (i>0) {tmp += ", ";}
			var par = this.JSON.parametres[i];
			if (par.ref) {tmp += "&";}
			tmp += "$" + par.name;
			if (par.optionnel) {
				tmp += "=" ;
			}
		}
		tmp +=")";
		ajouterColonne(newRow, tmp);
		return newRow;
	}
}

function comparerNiveau (a, b) {
		if (a.niveau < b.niveau) {return -1;}
		if (a.niveau > b.niveau) {return 1;}
		return 0;
}
	
function comparerNom (a, b) {
		var na = a.Name.toUpperCase();
		var nb = b.Name.toUpperCase() ;
		if (na < nb) {return -1;}
		if (na > nb) {return 1;}
		return 0;
	}
	
	function comparerStatic (a, b) {
		var sa = a.JSON.static;
		var sb = b.JSON.static;
		if (sa==sb) {return 0;}
		return (sa) ? -1 : 1;
	}
	
	function comparerPPP (a, b) {
		var sa = a.JSON.access;
		var sb = b.JSON.access;
		if (sa < sb) {return -1;}
		if (sa > sb) {return 1;}
		return 0;
	}
	
class Classe {
	constructor (oJSON) {
		this.JSON = oJSON;
		this.Interfaces = {};
		this.Constantes = {};
		this.Attributs = {};
		this.Methodes = {};
		this.myPopWindow = null;
		this.divhtml = null;
		this.modevisu = 0; // 0 : developpeur, 1 : extendeur, 2 : utilisateur
		if (oJSON.fichiersource==null) {this.modevisu = 2; }

		var i = 0;
		for (i=0; i< oJSON.constantes.length; i++) {
			var obj = new Constante(oJSON.constantes[i], this) ;
			this.Constantes[obj.Name] = obj;
		}
		for (i=0; i< oJSON.attributs.length; i++) {
			var obj = new Attribut(oJSON.attributs[i], this) ;
			this.Attributs[obj.Name] = obj;
		}
		for (i=0; i< oJSON.methodes.length; i++) {
			var obj = new Methode(oJSON.methodes[i], this) ;
			this.Methodes[obj.Name] = obj;
		}
		
		this.myDiv = null;
		this.mySpanChoix = null;
		this.ClasseMere = null;
	}
	get Name()		{return this.JSON.name;}
	get IsClasse()	{return !this.JSON.isinterface;}
	get FichierRelatif() {
		var tmp = myManager.monDossier.JSON.nom;
		return this.JSON.fichiersource.substr(tmp.length);
	}
	
	makeSpanLiaison (nom) {
		var sp = document.createElement("SPAN");
		sp.classList.add("liaison");
		sp.classList.add("unselectable");
		sp.element = myAnalyseur.Classes[nom];
		sp.addEventListener("click", function (e) {
					this.element.visualiser(e) ;
				}, false);
		sp.innerHTML = nom;
		return sp;
	}
	
	recupParent() {
		if (this.JSON.herite != null) {
			this.ClasseMere = myAnalyseur.Classes[this.JSON.herite];
		}
	}
	programmeur() {
		var text = "";
		switch (this.modevisu) {
			case 0 : text = "Auteur"; break;
			case 1 : text = "Extendeur"; break;
			case 2 : text = "Utilisateur"; break;
		}
		return text ;
	}
	
	redoContenu(recurs) {
		var lediv = this.myDiv;
		var ledivcontenu = lediv.children[1];
		lediv.removeChild(ledivcontenu);
		lediv.appendChild(this.getDivContenu());
		
		for (var i=0; recurs && (i<this.JSON.derivees.length); i++) {
			var nomderiv = this.JSON.derivees[i];
			var obj = myAnalyseur.Classes[nomderiv];
			obj.redoContenu(true);
		}
	}
	
	redefProgrammeur (v) {
		try {
			this.mySpanChoix.classList.remove(this.programmeur().toLowerCase());
			this.modevisu = v;
			this.mySpanChoix.classList.add(this.programmeur().toLowerCase());
			var spdev = this.myDiv.getElementsByClassName("developpeur")[0];
			spdev.innerHTML = this.programmeur();
			this.redoContenu(true);
		}
		catch (e) {afficherException (e);}
	}
	
	getDivHeader() {
		var d1 = document.createElement("DIV");
		d1.classList.add("entete");
		var spprog = document.createElement("SPAN");
			spprog.element= this;
			spprog.innerHTML = this.programmeur();
			if (this.JSON.fichiersource!=null) {
				spprog.classList.add("developpeur");
				spprog.addEventListener("click", function(e) {
					var el = this.element;
					el.redefProgrammeur((el.modevisu + 1) % 3);
					myManager.FaireCookieProgrammeur();
					}, false);
			}
		d1.appendChild(spprog);
		var text = (this.JSON.isinterface) ? " de l'interface " : " de la classe ";
		if (this.JSON.abstract && (!this.JSON.isinterface)) {text += "abstraite ";}
		if (this.JSON.isfinal && (!this.JSON.isinterface)) {text += "non dérivable ";}
			d1.appendChild(creerTxtNode(text));
		var sp = document.createElement("SPAN");
			sp.classList.add("defclasse");
			if (this.JSON.abstract) {sp.classList.add("abstraite");}
			sp.innerHTML = this.Name;
			sp.element = this;
			sp.addEventListener("click", function (e) {
				try {
					var el = this.element;
					if (el.myPopWindow==null) {
						var myWindow = window.open("page.php", "", "toolbar=no,scrollbars=yes,resizable=yes,width=800,height=500,location:no,menubar=yes");
						myWindow.addEventListener("load", function () {
							var d1 = this.document.createElement("DIV");
							d1.innerHTML = classevue.myDiv.innerHTML;
							//------------------- enlever les traits ;
							var lst = d1.getElementsByClassName("trait");
							var nbu = lst.length ;
							for (var i = nbu-1; i>=0; i--) {
								var objTrait = lst[i];
								var sonpapa = objTrait.parentNode;
								sonpapa.removeChild(objTrait);
							}
							//------------------- retailler les pre
							lst = d1.getElementsByTagName("pre");
							nbu = lst.length ;
							for (var i = nbu-1; i>=0; i--) {
								var objTrait = lst[i];
								objTrait.style.margin="0px";
								objTrait.style.fontStyle="italic";
							}
							this.document.body.style.backgroundColor = "#eeeeee";
							this.document.body.appendChild(d1);
							this.document.body.element = classevue;
						}, false);
						//myWindow.addEventListener("unload", function () {
						//	this.document.body.element.myPopWindow = null;
						//}, false);
						//el.myPopWindow= myWindow;
					} else {el.myPopWindow.focus();}
					return true;
				}
				catch (e) {afficherException (e);}
			} ,false);
			d1.appendChild(sp);	
		this.recupParent();
		var papa = this.JSON.herite;
		var h = 0;
		while (papa != null) {
			h++;
			var spanh = document.createElement("span");
			switch (h) {
				case 1: spanh.innerHTML="&#x21D2;"; break
				default: spanh.innerHTML="&#x21D2;";
			}
			d1.appendChild(spanh);
			var lelien = this.makeSpanLiaison(papa);
			lelien.classList.add("herit" + h);
			d1.appendChild(lelien);			
			papa = lelien.element.JSON.herite;
		}
		var divInterf = null;
		var nb = this.JSON.interfaces.length;
		for (var x=0; x<nb; x++) {
			if (x==0) {
				divInterf = document.createElement("DIV");
				d1.appendChild(divInterf);
				text = (nb>1) ? "les " + nb + " interfaces " : "l'interface ";
				divInterf.appendChild(creerTxtNode(" Implémente " + text));
			} else {
				divInterf.appendChild(creerTxtNode(" "));
			}
			divInterf.appendChild(this.makeSpanLiaison(this.JSON.interfaces[x]));		
		}
		if (this.JSON.comment!="") {
			var comment = document.createElement("DIV");
			comment.classList.add("comment");
			comment.innerHTML = "<pre>" + this.JSON.comment+ "</pre>" ;
			d1.appendChild(comment);
		}
		if (this.JSON.fichiersource!=null) {
			var plus = document.createElement("DIV");
			plus.appendChild(creerTxtNode("Source : " + this.FichierRelatif + " (" + this.JSON.linedeb + "-" + this.JSON.linefin + ")"));
			d1.appendChild(plus);
		} else  {
			var plus = document.createElement("DIV");
			plus.classList.add("lienphp");
			var txturl = "http://php.net/manual/fr/class." + this.Name.toLowerCase() + ".php";
			var txta = '<a href="'+ txturl + '" target="_blank">' + txturl + "</a>";
			plus.innerHTML = "Plus d'infos&nbsp;:&nbsp;" + txta;
			d1.appendChild(plus);
		}
		var divDer = null;
		for (var i=0; i<this.JSON.derivees.length; i++) {
			if (i==0) {
				divDer = document.createElement("DIV");
				d1.appendChild(divDer);
				text = (this.JSON.isinterface) ? "Implémenté par " : "Dérivée par ";
				divDer.appendChild(creerTxtNode(text));
			} else {
				divDer.appendChild(creerTxtNode(" "));
			}
			divDer.appendChild(this.makeSpanLiaison(this.JSON.derivees[i]));
		}
		return d1;
	}
	
	visualiser(e) {
		try {
			if (classevue!=null) {
				classevue.Div.classList.remove("visuyes");
				classevue.Div.classList.add("visuno");
				classevue.mySpanChoix.classList.remove ("selected");
			}
			classevue = this;
			this.Div.classList.add("visuyes");
			this.Div.classList.remove("visuno");
			classevue.mySpanChoix.classList.add ("selected");
			classevue.mySpanChoix.scrollIntoView({behavior: "smooth", inline: "center"});
			//myScroll.analyse(0);
			GestionCookie.Memorise("classe", this.Name);
		}
		catch (e) {
		}		
	}
	
	remplirConstantes(uneTable, heritage, prog, lebody) {
		if (prog < this.modevisu) {prog = this.modevisu;}
		var tbod  = lebody;
		var first = (lebody == null);
		for (var k in this.Constantes) {
			if (first) {
				tbod = creerTbody("constantes", "Constantes");
				uneTable.appendChild(tbod);
				first = false;
			}
			this.Constantes[k].createTableRow(tbod, heritage);
		}
//		if ((this.ClasseMere != null) && myManager.chkHeritage.checked) 
//			{this.ClasseMere.remplirConstantes(uneTable, heritage+1, prog, tbod);}	
	}
	
	comparerNiveauElements (a, b) {
		var tmp = comparerNiveau(a, b);
		if (tmp != 0) {return tmp;}
		return comparerNom (a, b);
	}
	
	comparerAccesAlpha (a, b) {
		var tmp = comparerPPP(a, b);
		if (tmp != 0) {return tmp;}
		return comparerNom (a, b);
	}

	comparerElements (a, b) {
		var tmp = comparerNom (a, b);
		if (tmp != 0) {return tmp;}
		return comparerNiveau(a,b);
	}
	
	comparerStaticAlpha (a, b) {
		var tmp = comparerStatic (a, b);
		if (tmp != 0) {return tmp;}
		return comparerNom(a,b);
	}
	getDivContenu() {
		var d1 = document.createElement("div");
		d1.classList.add("contenu");
		var dtab = document.createElement("div") ;
		dtab.classList.add("contenudetaille");
		d1.appendChild(dtab);
		var tb = document.createElement("table");
		dtab.appendChild(tb);
		
		
		this.remplirConstantes(tb, 0, this.modevisu, null);
		
		var divresume = document.createElement("div");
		divresume.classList.add("classresume");
		d1.appendChild(divresume);
		
		if (myManager.chkResume.checked) {
			dtab.classList.add("contenumasque");
		} else {
			divresume.classList.add("contenumasque");
		}
		
		var TBATT = this.recupererTousLesAttributs ();
		var TBMETH = this.recupererToutesLesMethodes();
		switch (myManager.criteretri) {
			case 0: break;
			case 1: TBATT.sort(this.comparerNiveauElements);
					TBMETH.sort(this.comparerNiveauElements);
					break;
			case 2: TBATT.sort(this.comparerElements);
					TBMETH.sort(this.comparerElements);
					break ;
			case 3: TBATT.sort(this.comparerAccesAlpha);
					TBMETH.sort(this.comparerAccesAlpha);
					break ;
			case 4: TBATT.sort(this.comparerStaticAlpha);
					TBMETH.sort(this.comparerStaticAlpha);
					break ;
		}

		var tbod = null;
		for (var i=0; i<TBATT.length; i++) {
			if (i==0) {
				tbod = creerTbody("attributs","Attributs");
				tb.appendChild(tbod);
				var oTitre = document.createElement("h3");
				oTitre.innerHTML = "Attributs";
				divresume.appendChild(oTitre);
			}
			var item = TBATT[i];
			item.createTableRow(tbod, item.niveau);
			divresume.appendChild(item.createSummaryLine());
		}
		for (i=0; i<TBMETH.length; i++) {
			if (i==0) {
				tbod = creerTbody("methodes","Méthodes");
				tb.appendChild(tbod);
				var oTitre = document.createElement("h3");
				oTitre.innerHTML = "Méthodes";
				divresume.appendChild(oTitre);
			}
			var item = TBMETH[i];
			item.createTableRow(tbod, item.niveau);
			divresume.appendChild(item.createSummaryLine());
		}
		return d1;
	}
	// renvoie un tableau contenant tous les attributs
	recupererTousLesAttributs () {
		var obj = this;
		var h = 0;
		var tbm = [];
		var prog = obj.modevisu ;
		
		while (obj != null) {
			if (obj.modevisu > prog) {prog = obj.modevisu;}
			for (var k in obj.Attributs) {
				var objet = obj.Attributs[k];
				var doIt = true;
				switch (prog) {
					case 0: doIt = (objet.JSON.access != "private") || (h==0) || (myManager.chkPrivate.checked); break;
					case 1: doIt = objet.JSON.access != "private" || (myManager.chkPrivate.checked); break;
					case 2: doIt = objet.JSON.access == "public"; break;
				}
				if (objet.JSON.static && !myManager.chkStatic.checked) {doIt=false;}
				if (!objet.JSON.static && !myManager.chkNonStatic.checked) {doIt=false;}
				if (doIt) {objet.niveau = h; tbm.push(objet); }
			}
			h++;
			if (obj.JSON.herite != null && myManager.chkHeritage.checked) {obj = myAnalyseur.Classes[obj.JSON.herite];}
			else {obj = null;}
		}	
		return tbm;
	}
	// renvoie un tableau contenant toutes les méthodes
	recupererToutesLesMethodes () {
		var obj = this;
		var h = 0;
		var tbm = [];
		var prog = obj.modevisu ;
		
		while (obj != null) {
			if (obj.modevisu > prog) {prog = obj.modevisu;}
			for (var k in obj.Methodes) {
				var objet = obj.Methodes[k];
				var doIt = true;
				switch (prog) {
					case 0: doIt = (objet.JSON.access != "private") || (h==0) || (myManager.chkPrivate.checked); break;
					case 1: doIt = objet.JSON.access != "private" || (myManager.chkPrivate.checked); break;
					case 2: doIt = objet.JSON.access == "public"; break;
				}
				if (objet.JSON.static && !myManager.chkStatic.checked) {doIt=false;}
				if (!objet.JSON.static && !myManager.chkNonStatic.checked) {doIt=false;}

				if (doIt) {objet.niveau = h; tbm.push(objet); }
			}
			h++;
			if (obj.JSON.herite != null && myManager.chkHeritage.checked) {obj = myAnalyseur.Classes[obj.JSON.herite];}
			else {obj = null;}
		}
		return tbm;
	}
	
	get Div()	{
		if (this.myDiv != null) {return this.myDiv;}
		var oDiv = document.createElement("DIV")
		oDiv.element = this;
		oDiv.classList.add("classe");
		oDiv.classList.add("visuno");
		oDiv.appendChild(this.getDivHeader());
		oDiv.appendChild(this.getDivContenu());	
		this.myDiv = oDiv;
		return oDiv;
	}
	
	get Choix() {
		if (this.mySpanChoix!=null) {return this.mySpanChoix;}
		var obj = document.createElement("SPAN")
		obj.element = this;
		obj.classList.add("spchoix");
		obj.classList.add("unselectable");
		obj.classList.add(this.programmeur().toLowerCase());
		obj.innerHTML = this.Name;
		obj.addEventListener("click", function (e) {
					this.element.visualiser(e) ;
				}, false);
		this.mySpanChoix = obj;
		return obj;
	}
}

class Analyseur {
	constructor (oJSON) {
		this.JSON = oJSON ;
		this.Classes = {} ;
		this.Sources = {} ;
		this.chercherSources();
		var i = 0;
		for (i=0; i< oJSON.lstclasses.length; i++) {
			var obj = new Classe(oJSON.lstclasses[i]) ;
			this.Classes[obj.Name] = obj;
		}
	}
	
	visualiserNomClasse(nom) {
		var obj = this.Classes[nom];
		obj.visualiser(null);
	}
	
	makeDivChoix (oDiv) {
		oDiv.innerHTML = "";
		var tbc = this.ListeDesClasses() ;
		for (var i=0; i<tbc.length;i++) {
			//if (i>0) {oDiv.appendChild(document.createTextNode(" "));}
			oDiv.appendChild(tbc[i].Choix);
		}
		return null;
	}
	
	// renvoie un tableau contenant les noms des classes
	ListeDesClasses() {
		var tb = [];
		for (var k in this.Classes) {
			tb.push(this.Classes[k]);
		}
		tb.sort(function (a, b) {
			if (a.Name.toUpperCase() < b.Name.toUpperCase()) {return -1;}
			if (a.Name.toUpperCase() > b.Name.toUpperCase()) {return 1;}
			return 0;
		});
		return tb;
	}
	makeDiv () {
		var oDiv = document.createElement("DIV");
		for (var k in this.Classes) {
			oDiv.appendChild(this.Classes[k].Div);
		}
		return oDiv;
	}
	
	voirPremiere() {
		try {
			var test = GestionCookie.Recupere("classe","") ;
			if (test!="") {
				var cl = this.Classes[test];
				if (cl!=null) {
					cl.visualiser(null);
					return true
				}
			}
			for (var k in this.Classes) {
				this.Classes[k].visualiser(null) ;
				return true;
				break;
			}
		}
		catch (e) {}
		return false;
	}           

	displayMethode (uneMethode) {
		var tmp = uneMethode.Classe.JSON.fichiersource;
		var lediv = this.Sources[tmp];
		var lefrom = uneMethode.JSON.linedeb;
		var leto = uneMethode.JSON.linefin;
		var php = "";
		for (var i=lefrom-1; i<leto; i++) {
			var src = lediv.children[i].textContent;
			src = src.replace(/\t/gi, "   ");
			php += src;
		}
		var predisplay = document.getElementById("methodelines");
		predisplay.textContent = php;
		var methdisplay = document.getElementById("methodename");
		methdisplay.innerHTML = uneMethode.Classe.Name + "::" + uneMethode.Name;
	}
	
	chercherSources() {
		var elements = document.getElementsByClassName("filesource");
		for (var i=0;i<elements.length;i++) {
			var unDiv = elements[i] ;
			var sp = unDiv.children[0];
			this.Sources[sp.innerHTML] = unDiv.children[1];
			unDiv.innerHTML="";
		} 
	}
	
	refaireContenus() {
		for (var k in this.Classes) {
			this.Classes[k].redoContenu(false) ;
		}
	}
}

