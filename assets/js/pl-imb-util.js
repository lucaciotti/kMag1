/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/


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




var chkPesoBanc = function (obj, ncollo) {
	var cRet = getPeso("banc", document.getElementById("id").value, ncollo);
	if (obj.value < cRet) {
		alert("Attenzione:\nPeso inferiore al peso netto");
	}
	if (obj.value > cRet * 1.1) {
		alert("Attenzione:\nDifferenza con il peso teorico\nsuperiore al 10%");
	}
};
