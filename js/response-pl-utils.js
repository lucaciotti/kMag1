// ***********************************************************************
// Project ArcaWeb
// ===========================
//
// Copyright (c) 2003-2013 by Roberto Ceccarelli
//
// **********************************************************************

//Handler cambio unita di misura
var changeHandler = function(event) {
	var newValue = 1;
	var index = this.selectedIndex;
	if (index >= 0 && this.options.length > index) {
		newValue = this.options[index].value;
	}
	// **Your code here**: old value is `oldValue`, new value is `newValue`
	var qtaBox = document.getElementById('qta');
	qtaBox.value = Math.floor(qtaBox.value * oldValue / newValue);
	//document.getElementById("umdesc").value = this.options[index].innerHTML;

	// When done processing the change, remember the old value
	oldValue = newValue;
};

// init Handler
var ubox = document.getElementById('um');
var oldValue = ubox.options[ubox.selectedIndex].value;
//mi serve poi per convertire tutto a um iniziale
if (document.getElementById('oldFatt').value == 0){
    document.getElementById('oldFatt').value = oldValue;
    document.getElementById("newFatt").value = ubox.options[ubox.selectedIndex].value;
}
if (ubox.addEventListener) {
	// DOM2 standard
	ubox.addEventListener("change", changeHandler, false);
} else if (ubox.attachEvent) {
	// IE fallback
	ubox.attachEvent("onchange", changeHandler);
} else {
	// DOM0 fallback
	ubox.onchange = changeHandler;
}

var checkCodiceArtix = function(cCodice, cCF)  {
	var url = 'getcodiceartix.php?cod=' + encodeURIComponent(cCodice);
	if (cCF != "") {
		url = url + "&cf=" + cCF;
	}
	var milliseconds = new Date().getTime();
	url += "&x=" + milliseconds;

	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;

	var oRet = new Object();
	oRet.codice = xRet.getElementsByTagName("codice")[0].firstChild.nodeValue;
	if ("*error*" == oRet.codice)  {
		alert("Codice non riconosciuto");
		oRet.codice = "";
		oRet.lottoob = 0;
	} else {
		oRet.lottoob = xRet.getElementsByTagName("lottoob")[0].firstChild.nodeValue;
		oRet.xml = xRet;
	}
	return oRet;
};

var checkGiacArtix = function(cCodice, cLotto)  {
	var url = "getgiacartix.php?codArt=" + encodeURIComponent(cCodice);
	url = url + "&codLotto=" + encodeURIComponent(cLotto);
	url = url + "&maga=" + global_magGiac;
	var milliseconds = new Date().getTime();
	url += "&x=" + milliseconds;

	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;

	var oRet = new Object();
	oRet.codice = xRet.getElementsByTagName("codice")[0].firstChild.nodeValue;
	if ("*error*" == oRet.codice)  {
		alert("Codice non riconosciuto");
		oRet.codice = "";
		oRet.giacenza = 0;
	} else {
		oRet.giacenza = xRet.getElementsByTagName("giacenza")[0].firstChild.nodeValue;
		oRet.xml = xRet;
	}
	return oRet;
};

function decodeArt(codice, obj) {
    var val = obj.value;
    if(val != "") {
		var oArt = checkCodiceArtix(obj.value);
		obj.value = oArt.codice;
		var newUm = oArt.xml.getElementsByTagName("unmisura")[0].firstChild.nodeValue;
		var oldFatt = ubox.options[ubox.selectedIndex].value;
		document.getElementById('oldFatt').value = oldFatt;
		var tbox = String(document.getElementById('codart').innerHTML);
		var str = String(oArt.codice);
		//alert(tbox +" "+ str+" "+ tbox.length+" "+ str.length);
		if (tbox.trim() == str.trim() && tbox.length == str.length){
			if(newUm != ""){
				for( var j=0; j<ubox.length; j++) {
					if( ubox.options[j].innerHTML == newUm) {
						oldValue = ubox.options[ubox.selectedIndex].value;
						ubox.selectedIndex = j;
						var qtaBox = document.getElementById('qta');
						qtaBox.value = qtaBox.value * oldFatt / ubox.options[ubox.selectedIndex].value;
						document.getElementById("newFatt").value = ubox.options[ubox.selectedIndex].value;
						ubox.disabled = true;
					}
				}
			} else {
				alert("Codice barcode Cliente!\nInserire U.M. manualmente...");
			}
			obj.disabled = true;
			if(oArt.xml.getElementsByTagName("lottoob")[0].firstChild.nodeValue==0){
				document.getElementById('lotto').disabled = true;
				document.getElementById('qta').focus();
			} else {
				document.getElementById('lotto').focus();
			}
			return true;
		} else {
			obj.focus();
			alert("Codice non corrisponde");
			return false;
		}
    }
};

var checkLotto = function(cCodice, obj) {
	var lotto = cleanCode(obj.value.trim());
	/*var lotto = obj.value.trim();*/
	if("" == lotto) {
		return true;
	}
	var lista = checkCodiceArtix(cCodice).xml.getElementsByTagName("lotto");
	for( var j=0; j < lista.length; j++) {
		if(lista[j].firstChild.nodeValue == lotto) {
			obj.value = lotto;
			document.getElementById('qta').focus();
			return true;
		}
	}
	alert("Lotto: "+lotto+" non valido per questo articolo.");
	obj.value = "";
	obj.focus();
	return false;
};

var checkQta = function(cCodice, obj) {
	var qta = obj.value;
	var qtaRes = document.getElementById('qtaRes').value;
	var oldFatt = document.getElementById('oldFatt').value;
	if (oldFatt != 0){
		var newFatt = ubox.options[ubox.selectedIndex].value;
		qtaRes = qtaRes * oldFatt / newFatt;
		//alert(qta+" "+qtaRes+" "+oldFatt+" "+newFatt);
	}
	if(qta != 0){
		var lotto = document.getElementById("lotto").value.trim();
		var giac = checkGiacArtix(cCodice, lotto).giacenza;
		var fatt = ubox.options[ubox.selectedIndex].value;
		var um = ubox.options[ubox.selectedIndex].innerHTML;
		giac = giac / fatt;
		if (qta > qtaRes){
			alert("Qta Superiore a Qta Residua");
            return false;
		} else {
			if (qta > giac){
				if (lotto != ""){
					var message = "Qta Superiore a GIACENZA LOTTO : "+giac+" "+ um;
				} else {
					var message = "Qta Superiore a GIACENZA: "+giac+" "+ um;
				}
				alert(message);
				obj.focus();
				return false;
			} else {
				//alert("ok");
				return true;
			}
		}
	}
};

var conferma = function() {
	var qta = document.getElementById('qta').value;
	var qtaRes = document.getElementById('qtaRes').value;
	var oldFatt = document.getElementById('oldFatt').value;
	var newFatt = ubox.options[ubox.selectedIndex].value;
	var collo = parseInt(document.getElementById('collo').value);
	var codart = new String(document.getElementById('codart').innerHTML);
	var lotto = new String(document.getElementById('lotto').value);
	var umOld = new String(document.getElementById('umdesc').value);
	var umNew = new String(ubox.options[ubox.selectedIndex].innerHTML);
    var close = (document.getElementById('close').checked ? "\n*Collo Chiuso" : "");
    var stampo = (document.getElementById('print').checked ? "\n*Stampo Collo" : "");
	var message = "test";
	if (umOld.trim() == umNew.trim() || oldFatt == 0){
		var message = "Collo n."+collo+"\nArt: "+codart+"\n";
		if (lotto.trim() != ""){
			message = message+"Lotto: "+lotto+"\n";
		}
		message = message+"Qta: "+qta+" "+umNew+""+close+""+stampo;
	} else {
		var qta2 = qta * newFatt / oldFatt;
		var message = "Collo n."+collo+"\nArt: "+codart+"\n";
		if (lotto.trim() != ""){
			message = message+"Lotto: "+lotto+"\n";
		}
		message = message+"Qta: "+qta+" "+umNew+"  ==>  "+qta2+" "+umOld+""+close+""+stampo;
	}
    message = message+"\nPROCEDO?";
	var r=confirm(message);
	if (r==true){
		return true;
	} else {
		document.getElementById("qta").focus();
		return false;
	}
};

var checkForm = function(id, collo, close, ncolli) {

	var check = true;
    if(document.getElementById("lottoobb").innerHTML == "Obbligatorio") {
        if(document.getElementById("lotto").value.trim() == "") {
			alert("Lotto OBBLIGATORIO!");
            return false;
        }
		if(document.getElementById("controllo").value.trim() == "") {
			alert("Controllo Articolo OBBLIGATORIO!");
			document.getElementById("controllo").focus();
            return false;
        }
    }
    check = checkQta(document.getElementById("codart").innerHTML, document.getElementById("qta"));
	if (check == false){
		return false;
    } else {
		check = conferma();
		if(check == false){
			return false;
		} else {
			return checkCollo(id, collo, close, ncolli);
		}
	}
};


var chkColloPann_click = function() {
	if($("#chkColloPann").is(':checked')) {
		$("#colloPann").text( $("#colloPann").text() -1);
		$("#ncolli").val(1);
	} else {
		$("#colloPann").text( $("#colloPann").text()*1 +1);
		$("#ncolli").val(2);
	}
}

/*
var dividiColli = function() {
	if(document.getElementById("lottoobb").innerHTML == "Obbligatorio") {
		alert("Funzione non utilizzabile:\nlotto obbligatorio!");
		return false;
	}
	bootbox.prompt("Pezzi per collo", function(result) {
		if (result === null) {
			return false;
		} else {
			window.location.href = "pl-split.php?"+$("#plrow").serialize()+"&totcolli="+result;
		}
	});
};

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function removeOptionNotSelected(id)
{
  var elSel = document.getElementById(id);
  var i;
  for (i = elSel.length - 1; i>=0; i--) {
    if (!elSel.options[i].selected) {
      elSel.remove(i);
    }
  }
}
    */
