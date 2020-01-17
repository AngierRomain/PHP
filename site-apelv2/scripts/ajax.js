function ajax_make() {
    if (window.XMLHttpRequest) {
        xHttp = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
    } else {
        xHttp = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
    }
    return xHttp;
}

/** appel ajax avec les données (argument). Le traitement du flot reçu
 sera rangé dans DIVTRAVAIL
 exemple : ajax_call('rep1', donnees) met en oeuvre repond/rep1.php */
function ajax_call(datas) {
    //console.log ('ajax_call', datas);
    var appeleur = ajax_make();
    appeleur.onreadystatechange=function()  {
        if (appeleur.readyState==4) {  // DONE
            if (xHttp.status == 200) {
                var flux = appeleur.responseText ;
                divtravail.innerHTML = flux;
            } else {
                console.log('status HTTP pour le repondeur', repondeur ,':', xHttp.status);
            }
        }
    }
    appeleur.open("POST", '', true);
    appeleur.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    appeleur.send(JSON.stringify(datas));
}

/** Appel ajax de la page courante
 datas : objet javascript,
 fnct  : fonction de traitement du flot qui sera reçu */
function ajax_call_getdata(datas, fnct) {
    datas['demand'] = 'getdata';
    //console.log ('ajax_call_getdata', datas);
    var appeleur = ajax_make();
    appeleur.onreadystatechange=function()  {
        if (appeleur.readyState==4) {  // DONE
            switch (xHttp.status) {
                case 200 :	var m = appeleur.responseText ;
                    fnct(m);
                    break;
                default :
            }
        }
    }
    appeleur.open('POST', 'repond/getdata.php', true);
    appeleur.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    appeleur.send(JSON.stringify(datas));


}


function ajax_call_setdata(datas, fnct) {
    datas['demand'] = 'setdata';
    //console.log ('ajax_call_getdata', datas);
    var appeleur = ajax_make();
    appeleur.onreadystatechange=function()  {
        if (appeleur.readyState==4) {  // DONE
            switch (xHttp.status) {
                case 200 :	var m = appeleur.responseText ;
                    fnct(m);
                    break;
                default :
            }
        }
    }
    appeleur.open('POST', 'repond/setdata.php', true);
    appeleur.setRequestHeader("Content-type", "application/json; charset=UTF-8");
    appeleur.send(JSON.stringify(datas));
    ///** Actualisation auto de la page au bout de 3 secondes */
    //window.setTimeout(function(){window.location.reload()}, 3000);
}

/** recherche un objet json dans divtravail */
function ajax_findJson() {
    var tb = divtravail.getElementsByClassName('json');
    if (tb.length==0) return null;
    var item = tb[0];
    try {
        var unJson = JSON.parse(item.innerHTML);
        console.log('json trouvé:', unJson);
        return unJson;
    }catch (ex) {
    }
    console.log('cherche json: ', item);
    return null;
}

function mnu_select(btn) {
    ajax_call({'demand':'choixmnu', 'key':btn.dataset.key});
}

