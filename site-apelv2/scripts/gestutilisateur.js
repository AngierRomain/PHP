/*
	fonction dédié a la gestion des professeurs



*/

function gestu_Demande(ev) {
	//console.log(ev);
	var obj = ev.target; //obj ayant subbit le ckick
	var maitre = ev.currentTarget; //ovj ayant la def de onclick
	while (obj.dataset.key === undefined) {
		if (obj === maitre) return;
		obj = obj.parentNode;
	}
	var k = obj.dataset.key;
	//console.log('key : ', k, 'nature ', maitre.dataset.nature);
	var xxx = new ajax_call_getdata({'type':maitre.dataset.nature, 
									'key':k}, 
									gestu_Reception);
}

function gestu_Reception(str) {
	iparent.checked = false; 
    valide.checked = false; 
	membre.checked = false; 
	adherent.checked = false;
	//console.log(str);
	divtravail.innerHTML = str;
	
	var truc = ajax_findJson();
	console.log(truc);
	
	if (truc == null) return;
	//mettre les infos dans le formulaire
	with (document.frmuser) {
		oldid.value = truc.id;
		suppr.value = truc.suppr;
		civilite.value = truc.civilite;
		code.value = truc.id;
		nom.value = truc.nom;
		prenom.value = truc.prenom;
		daten.value = truc.daten;
		adresse.value = truc.adresse;
		login.value = truc.login;
		pass.value = truc.pass;
		tel.value = truc.tel;
		mail.value = truc.mail;
		decode_Nature(truc);
	}
	titresaisie.innerHTML = 'Modification utilisateur';
	saisieuser.classList.remove('masked')
	deleteuser.classList.remove('masked');
}

function gestu_Nouveau() {
	deleteuser.classList.add('masked');
	with (document.frmuser) {
		oldid.value = '0';
		suppr.value = '0';
		civilite.value = '1';
		code.value = '';
		nom.value = '';
		prenom.value = '';
		daten.value = '';
		adresse.value ='';
		login.value ='';
		pass.value = '';
		tel.value = '';
		mail.value = '';
	}
	iparent.checked = false; 
    valide.checked = false; 
	membre.checked = false; 
	adherent.checked = false;
	titresaisie.innerHTML = 'Nouveau utilisateur';
	saisieuser.classList.remove('masked');
}

function gestu_Save() {
	//alert(verif_info());
	if(verif_info() == false) { divtravail.innerHTML = "Information non rempli"; return -1; }
	with (document.frmuser) {
		//console.log(code.value);
		var xxx = new ajax_call_setdata(
			{'type':'user', 
			'oldkey':oldid.value,
			'suppr':suppr.value,
			'key':code.value,
			'civ':civilite.value,
			'nom':nom.value,
			'pren':prenom.value,
			'daten':daten.value,
			'adresse':adresse.value,
			'login':login.value,
			'pass':pass.value,
			'tel':tel.value,
			'mail':mail.value,
			'nature':encode_Nature()},		
			gestu_ConfirmSave);
	}	
	console.log('save envoyé');
}

function gestu_ConfirmSave(str) {
	divtravail.innerHTML = str;
	//saisieuser.classList.add('masked');
}

function gestu_Supprimer() {
	if(code.value == ' ') { divtravail.innerHTML = "Code invalide"; return -1; }
	with (document.frmuser) {
		var xxx = new ajax_call_setdata(
			{'type':'user', 
			'oldkey':oldid.value,
			'suppr':'-1',
			'key':code.value,
			'civ':civilite.value,
			'nom':nom.value,
			'pren':prenom.value,
			'daten':daten.value,
			'adresse':adresse.value,
			'login':login.value,
			'pass':pass.value,
			'tel':tel.value,
			'mail':mail.value,
			'nature':encode_Nature()},		
			gestu_ConfirmSave);
	}
	iparent.checked = false; 
    valide.checked = false; 
	membre.checked = false; 
	adherent.checked = false;
	saisieuser.classList.add('masked');
}

function verif_info(){
	var res = true;
	with (document.frmuser) {
		if(code.value == "") res = false;
		if(nom.value == "") res = false;
		if(prenom.value == "") res = false;
		if(daten.value == "") res = false;
		if(adresse.value == "") res = false;
		if(login.value == "") res = false;
		if(pass.value == "") res = false;
		if(tel.value == "") res = false;
		if(mail.value == "") res = false;
	}
	return res;
}


function encode_Nature() {
	var vnature = 0;
	with (document.frmuser) {
		if(valide.checked == true) vnature += 1;
		if(iparent.checked == true) vnature += 2;
		if(adherent.checked == true) vnature += 4;
		if(membre.checked == true) vnature += 8;
	}
	return vnature;
}


function decode_Nature (truc) {
	var nature = truc.nature;
	
	if ((nature-8) >= 0) { membre.checked = true; nature-8;}
	if ((nature-4) >= 0) { adherent.checked = true; nature-4;}
	if ((nature-2) >= 0) { iparent.checked = true; nature-2;}
	if ((nature-1) >= 0) { valide.checked = true; nature-1;}
}


