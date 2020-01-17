/*
Romain ANGIER
fonctions dédiées à la gestion des fournitures
*/

function gestf_Demande(ev){
    //console.lo(ev)
    var obj=ev.target;
    var maitre =ev.currentTarget; 			// objet ayant subi le click
    while(obj.dataset.key === undefined) {	// objet ayant la def de onclik
        if (obj === maitre)return;
        obj=obj.parentNode;
    }
    var k = obj.dataset.key;
    console.log('key : ', k,'nature ',maitre.dataset.nature);

    var xxx= new ajax_call_getdata({'type':maitre.dataset.nature,'key':k},gestf_Reception);



}
function gestf_Reception(str){
    console.log(str);
    divtravail.innerHTML=str;
    var truc = ajax_findJson();
    console.log(truc);
    if(truc==null) return;
    // mettre les infos dans le formulaire
    with (document.frmfourniture) {
        //champ.value = truc.valeur json
        oldid.value = truc.id;
        code.value = truc.id;
        categ.value = truc.codecat;
        libelle.value = truc.libelle;
        prixunit.value = truc.prixunit;
        quantite.value = truc.quantite;
    }
    titresaisie.innerHTML = 'Modification fourniture'
    saisiefourniture.classList.remove('masked');
}

function gestf_Nouveau () {
    with (document.frmfourniture) {
        oldid.value = '0';
        code.value = '';
        categ.value = '';
        libelle.value = '';
        prixunit.value = '';
        quantite.value = '';
    }
    titresaisie.innerHTML = 'Nouvelle fourniture'
    saisiefourniture.classList.remove('masked');
}


function gestf_Save () {
    with (document.frmfourniture) {
        //alert(categ.selectedIndex);
        var xxx= new ajax_call_setdata(
            {'type':'fourniture',
                'oldkey': oldid.value,
                'code': code.value,
                'codecat': categ.selectedIndex,
                'libelle': libelle.value,
                'prixunit' : prixunit.value,
                'quantite': quantite.value}
            ,gestf_ConfirmSave);
    }
    console.log('save envoyer');
}

function gestf_ConfirmSave(str){
    divtravail.innerHTML = str;
}
/** On mémorise l'ancien identifiant de la fourniture dans oldkey, on passe l'identifiant ($code) à SUPPR pour que SQLdoMAJ dans fourniture.php
 *  comprenne qu'il s'agit d'une suppression (via une condition qui vérifie si $code = 'SUPPR') et si c'est le cas on fait un SQLDelete where FOUCode = oldkey. */

function gestf_Supprimer() {
    with (document.frmfourniture) {
        var xxx= new ajax_call_setdata(
            {'type':'fourniture',
                'oldkey': oldid.value,
                'code': 'SUPPR',
                'codecat': categ.value,
                'libelle': libelle.value,
                'prixunit' : prixunit.value,
                'quantite': quantite.value}
            ,gestf_ConfirmSave);
    }
    console.log('save envoyer');
}

