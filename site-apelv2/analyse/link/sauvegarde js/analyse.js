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
	var trait = tableRef.insertRow(tableRef.rows.length) ;
		trait.classList.add("trait");
		trait.innerHTML="<td></td><td></td><td></td>";
	var newRowCom  = tableRef.insertRow(tableRef.rows.length);
		newRowCom.classList.add("comment");
	if (heritage>0) {
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
	unTexte = unTexte.replace(/^(\t|\s)*\n$/mg, "");
	unTexte = "/**" + unTexte + "*/";
	var cap = document.createElement("pre");
	cap.innerHTML = unTexte;
	//cap.appendChild(document.createTextNode(unTexte));
	cell.appendChild(cap);
	return cell;
}
function ajouterColonneSpan(unRow, unTexte) {
	var cap = document.createElement("span");
	//var tmp = texte.replace("\n","<br>");
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
	
	createTableRow(tableRef) {
		var newRow  = tableRef.insertRow(tableRef.rows.length);
		newRow.classList.add("constante");
		if (tableRef.fairecouleur) {newRow.classList.add("couleur");}
		
		newRow.element = this;
		var tmp = this.Name + " = " + this.JSON.valeurtxt;
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
	}
	get Name()		{return this.JSON.name;}
	
	createTableRow(tableRef, heritage) {
		ajouterCommentaire(this, tableRef, heritage);
		var newRow  = tableRef.insertRow(tableRef.rows.length);
		newRow.classList.add("attribut");
		newRow.classList.add(this.JSON.access);
		if (this.JSON.static) {	newRow.classList.add("static");	}
		newRow.element = this;
		if (heritage>0) {
			newRow.classList.add("heritage");
			newRow.classList.add("herit" + heritage);
		} else {
			if (tableRef.fairecouleur) {newRow.classList.add("couleur");}
			tableRef.fairecouleur = !tableRef.fairecouleur;
		}
		ajouterColonne(newRow, "");
		var tmp = this.JSON.access ; 
		tmp += (this.JSON.static) ? " static"  : ""
		ajouterColonne(newRow, tmp);
		tmp="";
		if (heritage>0) {tmp += this.Classe.Name + "::" } 
		tmp += "$" + this.Name;		
		ajouterColonne(newRow, tmp);
		return newRow;
	}
}

class Methode {
	constructor (oJSON, oClasse) {
		this.JSON = oJSON;
		this.Classe = oClasse;
	}
	get Name()		{return this.JSON.name;}
	get IsClasse()	{return !this.JSON.isinterface;}
	
	createTableRow(tableRef, heritage) {
		ajouterCommentaire(this, tableRef, heritage);
		var newRow  = tableRef.insertRow(tableRef.rows.length);
		newRow.classList.add("methode");
		var tmp = (this.JSON.static) ? "static" : "";
		if (heritage>0) {
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
		var tmp = this.JSON.access;
		tmp += (this.JSON.static) ? " static" : "";
		if (this.JSON.abstract) {tmp += " abstract";}
		if (this.JSON.final) {tmp+=" final" ;}
		ajouterColonne(newRow, tmp);
		tmp = "";
		
		if (heritage>0) {tmp +=  this.Classe.Name + "::";}
		tmp = tmp + this.Name + "(";
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

class Classe {
	constructor (oJSON) {
		this.JSON = oJSON;
		this.Interfaces = {};
		this.Constantes = {};
		this.Attributs = {};
		this.Methodes = {};

		var i = 0;
		for (i=0; i< oJSON.constantes.length; i++) {
			var obj = new Constante(oJSON.constantes[i]) ;
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
	
	giorgi() {
		if (this.JSON.herite != null) {
			this.ClasseMere = myAnalyseur.Classes[this.JSON.herite];
		}
	}
	
	getDivHeader() {
		var d1 = document.createElement("DIV");
		d1.classList.add("entete");
		var text = (this.JSON.isinterface) ? "Interface " : " Classe ";
		if (this.JSON.abstract && (!this.JSON.isinterface)) {text += "abstraite ";}
			d1.appendChild(creerTxtNode(text));
		var sp = document.createElement("SPAN");
			sp.classList.add("defclasse");
			if (this.JSON.abstract) {sp.classList.add("abstraite");}
			sp.innerHTML = this.Name;
			d1.appendChild(sp);	
		this.giorgi();
		var papa = this.JSON.herite;
		var h = 0;
		while (papa != null) {
			h++;
			switch (h) {
				case 1: d1.appendChild(document.createTextNode(" ==> ")); break
				default: d1.appendChild(document.createTextNode(" ~~> "));
			}
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
				text = (nb>1) ? "les " + nb + " interfaces" : "l'interface";
				divInterf.appendChild(creerTxtNode(" Implémente " + text));
			}
			divInterf.appendChild(this.makeSpanLiaison(this.JSON.interfaces[x]));		
		}
		if (this.JSON.comment!="") {
			var comment = document.createElement("DIV");
			comment.classList.add("comment");
			comment.innerHTML = "<pre>" + this.JSON.comment+ "</pre>" ;
			//comment.appendChild(creerSpan());
			d1.appendChild(comment);
		}
		if (this.JSON.fichiersource!=null) {
			var plus = document.createElement("DIV");
			plus.appendChild(creerTxtNode("Source : " + this.FichierRelatif + " (" + this.JSON.linedeb + "-" + this.JSON.linefin + ")"));
			d1.appendChild(plus);
		}
		var divDer = null;
		for (var i=0; i<this.JSON.derivees.length; i++) {
			if (i==0) {
				divDer = document.createElement("DIV");
				d1.appendChild(divDer);
				text = (this.JSON.isinterface) ? "Implémenté par " : "Dérivée par ";
				divDer.appendChild(creerTxtNode(text));
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
			GestionCookie.Memorise("classe", this.Name);
		}
		catch (e) {
		}		
	}
	
	remplirConstantes(uneTable) {
		var tbod = null;
		var first=true;
		for (var k in this.Constantes) {
			if (first) {
				tbod = creerTbody("constantes", "Constantes");
				uneTable.appendChild(tbod);
				first = false;
			}
			this.Constantes[k].createTableRow(tbod);
		}	
	}
	
	remplirAttributs(uneTable, heritage, lebody) {
		var tbod = lebody;
		var first=(lebody == null);
		for (var k in this.Attributs) {
			if (first) {
				tbod = creerTbody("attributs", "Attributs");
				uneTable.appendChild(tbod);
				first = false;
			}
			this.Attributs[k].createTableRow(tbod, heritage);
		}
		if ((this.ClasseMere != null) && myManager.chkHeritage.checked) 
			{this.ClasseMere.remplirAttributs(uneTable, heritage+1, tbod);}		
	}
	
	remplirMethodes(uneTable, heritage, lebody) {
		var tbod = lebody;
		var first=(lebody == null);
		for (var k in this.Methodes) {
			if (first) {
				tbod = creerTbody("methodes","Méthodes");
				uneTable.appendChild(tbod);
				first = false;
			}
			this.Methodes[k].createTableRow(tbod, heritage);
		}		
		if (this.ClasseMere != null && myManager.chkHeritage.checked) 
			{this.ClasseMere.remplirMethodes(uneTable, heritage+1, tbod);}
	}
	
	getDivContenu() {
		var d1 = document.createElement("DIV");
		d1.classList.add("contenu");
		var tb = document.createElement("table");
		d1.appendChild(tb);
		this.remplirConstantes(tb);
		this.remplirAttributs(tb, 0, null);
		this.remplirMethodes(tb, 0, null);
		return d1;
	}
	
	get Div()	{
		if (this.myDiv != null) {return this.myDiv}
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
	
	makeDivChoix () {
		var oDiv = document.createElement("DIV");
		var debut = true;
		for (var k in this.Classes) {
			if (!debut) {oDiv.appendChild(document.createTextNode(" "));}
			oDiv.appendChild(this.Classes[k].Choix);
			debut = false;
		}
		return oDiv;
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
}

