/*
Romain ANGIER
fonctions dédiées à la gestion des fournitures
*/

function gestc_Demande(ev){
    //console.lo(ev)
    var obj=ev.target;
    var maitre =ev.currentTarget; 			// objet ayant subi le click
    while(obj.dataset.key === undefined) {	// objet ayant la def de onclik
        if (obj === maitre)return;
        obj=obj.parentNode;
    }
    var k = obj.dataset.key;
    console.log('key : ', k,'nature ',maitre.dataset.nature);

    var xxx= new ajax_call_getdata({'type':maitre.dataset.nature,'key':k},gestc_Reception);



}
function gestc_Reception(str){
    console.log(str);
    divtravail.innerHTML=str;
    var truc = ajax_findJson();
    console.log(truc);
    if(truc==null) return;
    // mettre les infos dans le formulaire
    with (document.frmcategorie) {
        oldid.value = truc.id;
        codecat.value = truc.codecat;
        libelle.value = truc.libelle;
    }
    titresaisie.innerHTML = 'Modification categorie'
    saisiecategorie.classList.remove('masked');
}

function gestc_Nouveau () {
    with (document.frmcategorie) {
        oldid.value = '0';
        codecat.value = '';
        libelle.value = '';
    }
    titresaisie.innerHTML = 'Nouvelle catégorie'
    saisiecategorie.classList.remove('masked');
}


function gestc_Save () {
    with (document.frmcategorie) {
        var xxx= new ajax_call_setdata(
            {'type':'categorie',
                'oldkey': oldid.value,
                'codecat': codecat.value,
                'libelle': libelle.value}
            ,gestc_ConfirmSave);
    }
    console.log('save envoyer');
}

function gestc_ConfirmSave(str){
    divtravail.innerHTML = str;
}
/** On mémorise l'ancien identifiant de la categorie dans oldkey, on passe l'identifiant ($codecat) à SUPPR pour que SQLdoMAJ dans categorie.php
 *  comprenne qu'il s'agit d'une suppression (via une condition qui vérifie si $codecat = 'SUPPR') et si c'est le cas on fait un SQLDelete where CATCode = oldkey. */
function gestc_Supprimer() {
    with (document.frmcategorie) {
        var xxx= new ajax_call_setdata(
            {'type':'categorie',
                'oldkey': oldid.value,
                'codecat': 'SUPPR',
                'libelle': libelle.value}
            ,gestc_ConfirmSave);
    }
    console.log('save envoyer');
}

