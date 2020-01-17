function gestc_Demande(ev) {
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
									gestc_Reception);
}
function gestc_Connex(){
	if (login.value == "") return divtravail.innerHTML = "Champ login vide";
	if (pass.value == "") return divtravail.innerHTML = "Champ mot de passe vide";
	with (document.frmconnex) {
		var xxx = new ajax_call_getdata(
			{'type':'connex', 
			'key':login.value,
			'pass':pass.value},
			gestp_Reception);
	}	
	//console.log(xxx);
	console.log('connex envoyé');
}

function gestc_Reception(str) {
	//console.log(str);
	divtravail.innerHTML = str;
	
	var truc = ajax_findJson();
	//console.log(truc);
	
	if (truc == null) return;
	//mettre les infos dans le formulaire
	with (document.frmconnex) {

	}
}