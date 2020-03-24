/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

var noCache = function() {
	var milliseconds = (new Date()).getTime();
	return "&x=" + milliseconds;
};

var checkCodiceArtix = function (cCodice) {
	var url = "getcodiceartix.php?cod=" + encodeURIComponent(cCodice) + noCache();
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;
	var oRet = new Object();
	oRet.codice = xRet.getElementsByTagName("codice")[0].firstChild.nodeValue;
	if ("*error*" == oRet.codice) {
		alert("Codice non riconosciuto");
		oRet.codice = "";
		oRet.lottoob = 0;
		oRet.isImballo = 0;
	} else {
		oRet.lottoob = xRet.getElementsByTagName("lottoob")[0].firstChild.nodeValue;
		oRet.xml = xRet;
		oRet.isImballo = xRet.getElementsByTagName("imballo")[0].firstChild.nodeValue;
	}
	return oRet;
};

var getRigaDocx = function (cCodice) {
	var url = "getrigadocx.php?cod=" + encodeURIComponent(cCodice) + noCache();
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	return httpXml.responseXML;
};

var setMisuraImb = function (oArt, collo) {
	document.getElementById("imb_u_misural"+collo).value = oArt.xml.getElementsByTagName("u_misural")[0].firstChild.nodeValue;
	document.getElementById("imb_u_misurah"+collo).value = oArt.xml.getElementsByTagName("u_misurah")[0].firstChild.nodeValue;
	document.getElementById("imb_u_misuras"+collo).value = oArt.xml.getElementsByTagName("u_misuras")[0].firstChild.nodeValue;
};


var decodeImb = function (obj) {
	if ("" == obj.value) {
		return;
	}
	var oArt = checkCodiceArtix(obj.value);
	obj.value = oArt.codice;
	var collo="";
	if (oArt.codice != "" && oArt.isImballo == 0) {
		alert("L'articolo non &egrave; un imballo.");
		obj.value = "";
		obj.focus();
	} else if ("NOTA IMBALLO" == oArt.codice) {
		var oRiga = getRigaDocx(292000000000 + document.getElementById("id").value * 1);
		setMisuraImb(checkCodiceArtix(oRiga.getElementsByTagName("codicearti")[0].firstChild.nodeValue), collo);
	} else {
		setMisuraImb(oArt, collo);
	}
	// i fasci sono anche "bancali"
	// ROBERTO 16.07.2013 Per il momento tale gestione � sospesa
	// if( "#KZ-SCG(009)" == oArt.codice) {
	// document.getElementById("hasbanc").checked = true;
	// clickBancale();
	// document.getElementById("closebanc").checked = true;
	// showHideText(document.getElementById("closebanc"), 'askcodbanc');
	// var lista = document.getElementById("codbanc");
	// for (var i = 0; i < lista.options.length; i++) {
	// if( lista.options[i].value == oArt.codice) {
	// lista.options[i].selected = true;
	// }
	// }
	// }

	//LUCA PROVO A INSERIRE PESO AUTOMATICO FASCIO
	// if ("#KZ-SCG(009)" == obj.value) {
		var peso = getPeso("collo", document.getElementById("id").value, document.getElementById("collo").value );
		//peso += oArt.xml.getElementsByTagName("u_misuras")[0].firstChild.nodeValue;
		document.getElementById("pesocollo").value = peso;
	// } else {
	// 	document.getElementById("pesocollo").value = "";
	// }
};

var decodeImb2 = function (obj) {
	if ("" == obj.value) {
		return;
	}
	var oArt = checkCodiceArtix(obj.value);
	obj.value = oArt.codice;
	var collo="2";
	if (oArt.codice != "" && oArt.isImballo == 0) {
		alert("L'articolo non &egrave; un imballo.");
		obj.value = "";
		obj.focus();
	} else if ("NOTA IMBALLO" == oArt.codice) {
		setMisuraImb(checkCodiceArtix(document.getElementById("artcollo").value), collo);
	} else {
		setMisuraImb(oArt, collo);
	}
};

var copiaVal = function (orig, dest) {
	if ("#KZ-SCG(009)" == document.getElementById("art").value) {
		dest.value = orig.value;
	}
};

var getPeso = function (mode, id_riga, item) {
	var url = "getpeso.php?mode=" + mode + "&id_riga=" + id_riga + "&item=" + item + noCache();
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	return httpXml.responseText;
}

var decodeBanc = function (obj) {
	if ("" == obj.value || "NONE" == obj.value) {
		alert("L'articolo non e' un bancale.");
		return;
	}
	var oArt = checkCodiceArtix(obj.value);
	document.getElementById("pal_u_misural").value = oArt.xml.getElementsByTagName("u_misural")[0].firstChild.nodeValue;
	document.getElementById("pal_u_misuras").value = oArt.xml.getElementsByTagName("u_misuras")[0].firstChild.nodeValue;

	// Roberto 30.09.2014
	// Solo per i fasci preimpostiamo il peso
	// if ("#KZ-SCG(009)" == obj.value) {
		var peso = getPeso("banc", document.getElementById("id").value,	document.getElementById("bancnum").value );
		peso += getPeso("collo", document.getElementById("id").value, document.getElementById("collo").value );
		peso += oArt.xml.getElementsByTagName("u_misuras")[0].firstChild.nodeValue;
		document.getElementById("pesobanc").value = peso;
	// } else {
		// document.getElementById("pesobanc").value = "";
	// }
};

var checkForm = function () {
	var obj = document.getElementById("art");
	if ("" == obj.value) {
		alert("Specificare l'imballo");
		obj.focus();
		return false;
	}
	if (document.getElementById("hasbanc").checked) {
		if (checkBanc(document.getElementById("id_pl").value,
				document.getElementById("bancnum").value, false, false)) {

			if (document.getElementById("closebanc").checked &&
				document.getElementById("codbanc").value == "NONE") {

				alert("L'articolo non e' un bancale.");

				document.getElementById("codbanc").focus();
				return false;
			}

			if (document.getElementById("closebanc").checked &&
				(document.getElementById("pal_u_misural").value == 0 ||
					document.getElementById("pal_u_misuras").value == 0 ||
					document.getElementById("altezza").value == 0)) {

				alert("Una o piu' misure pallet mancanti");
				document.getElementById("altezza").focus();
				return false;
			}

			if (document.getElementById("closebanc").checked &&
				document.getElementById("pesobanc").value == 0) {

				alert("Peso bancale mancante");
				document.getElementById("pesobanc").focus();
				return false;
			}

		} else {
			return false;
		}
	}
	if (document.getElementById("imb_u_misural").value == 0 ||
		document.getElementById("imb_u_misurah").value == 0 ||
		document.getElementById("imb_u_misuras").value == 0) {

		alert("Una o piu' misure imballo mancanti");
		document.getElementById("imb_u_misural").focus();
		return false;
	}
	if (document.getElementById("pesocollo").value == 0 && document.getElementById("hasbanc").checked == false) {

		alert("Peso collo mancante");
		document.getElementById("pesocollo").focus();
		return false;
	}

	// Roberto 10.10.2014
	// Gestione pesi e misure secondo collo
	if (document.getElementById("ncolli").value > 1) {
		var obj2 = document.getElementById("art2");
		if ("" == obj2.value) {
			alert("Specificare l'imballo del secondo collo");
			obj2.focus();
			return false;
		}
		if (document.getElementById("imb_u_misural2").value == 0 ||
			document.getElementById("imb_u_misurah2").value == 0 ||
			document.getElementById("imb_u_misuras2").value == 0) {

			alert("Una o piu' misure imballo del secondo collo mancanti");
			document.getElementById("imb_u_misural2").focus();
			return false;
		}
		if (document.getElementById("pesocollo2").value == 0 && document.getElementById("hasbanc").checked == false) {

			alert("Peso secondo collo mancante");
			document.getElementById("pesocollo2").focus();
			return false;
		}
	}

	if (document.getElementById("closebanc").checked === true) {
		checkBanc(document.getElementById("id_pl").value,
			document.getElementById("bancnum").value, true, false);
	}
	return true;
};

var checkBanc = function (id, collo, close, quiet) {
	soloNumeri("bancnum");
	if (collo == 0) {
		alert("Numero bancale non specificato!");
		return false;
	}
	var url = "getbancfree.php?id=" + id + "&banc=" + collo + "&rep=" + document.getElementById("rep").value;
	if (close === true) {
		url += "&close";
	}
	var milliseconds = new Date().getTime();
	url += "&x=" + milliseconds;
	//    alert(url);
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var cRet = httpXml.responseText;
	//	alert(cRet);
	cRet = cRet.slice(0, 2);
	if (cRet.slice(0, 2) !== "ok") {
		if (cRet === "0") {
			alert("Bancale gi� chiuso!");
			return true;
		} else {
			if (!quiet) {
				alert("Numero bancale utilizzato da altro reparto\n" +
					"Primo numero disponibile: " + cRet);
			}
			document.getElementById("bancnum").value = cRet;
			return false;
		}
	} else {
		return true;
	}
};

var clickBancale = function () {
	var oHasBanc = document.getElementById("hasbanc");
	showHideText(oHasBanc, 'askbanc');
// Roberto 14.10.2014
// Il peso dei colli non � obbligatorio, ma si pu� comunque inserire
//	var elm = document.getElementById("divpesocollo");
//	elm.style.display = oHasBanc.checked ? "none" : "block";
//	elm = document.getElementById("divpesocollo2");
//	elm.style.display = oHasBanc.checked ? "none" : "block";

	if (oHasBanc.checked) {
		var oBancNum = document.getElementById("bancnum");
		//		if(0 == oBancNum.value) {
		checkBanc(document.getElementById("id_pl").value, oBancNum.value, false, true);
		//		}
	}
};

var chkPesoCollo = function (obj, ncollo) {
	var cRet = getPeso("collo", document.getElementById("id").value, ncollo);
	if(obj.value < cRet) {
		alert("Attenzione:\nPeso inferiore al peso netto");
	}
	if(obj.value > cRet *1.1) {
		alert("Attenzione:\nDifferenza con il peso teorico\nsuperiore al 10%");
	}
};

var chkPesoBanc = function (obj, ncollo) {
	var cRet = getPeso("banc", document.getElementById("id").value, ncollo);
	if(obj.value < cRet) {
		alert("Attenzione:\nPeso inferiore al peso netto");
	}
	if(obj.value > cRet *1.1) {
		alert("Attenzione:\nDifferenza con il peso teorico\nsuperiore al 10%");
	}
};
