// ***********************************************************************
// Project ArcaWeb
// ===========================
//
// Copyright (c) 2003-2013 by Roberto Ceccarelli
//
// **********************************************************************

if (!String.prototype.trim) {
 String.prototype.trim = function() {
  return this.replace(/^\s+|\s+$/g,'');
 }
}

var stopRKey = function(evt) {
    var evt = (evt) ? evt : ((event) ? event : null);
    var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
    if ((evt.keyCode == 13) && (node.type=="text")) {
		return false;
	}
};

function disableEnterKey(e) {
    var key;
    if(window.event) {
		key = window.event.keyCode;     //IE
	} else {
		key = e.which;     //firefox
	}
	if (key == 13) {
		return false;
    } else {
        return true;
	}
};


var httpXml = false;
var makeHttpXml = function() {
    "use strict";
    httpXml = false;
    if (window.XMLHttpRequest) { // Mozilla, Safari,...
		httpXml = new XMLHttpRequest();
    } else if (window.ActiveXObject) { // IE
        try {
            httpXml = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e1) {
            try {
				httpXml = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e2) {}
        }
    }
    if (!httpXml) {
        alert('Cannot create XMLHTTP instance');
        return false;
	}
};

var sendUrl = function(url) {
	"use strict";
	makeHttpXml();
    httpXml.open("GET", url, true);
    httpXml.send(null);
};

var soloNumeri = function(id) {
    var valore=document.getElementById(id).value
    valore=valore.replace (/[^\d]/g,'')
    document.getElementById(id).value=valore
};

var sendpick = function() {
	"use strict";
	var idriga = document.getElementById("idriga").value;
	var quantita = document.getElementById("quantita").value;
	var url = "writepick.php?idriga=" + idriga + "&quantita=" + quantita;
//	document.getElementById("check").checked = true;
	// alert('Dato inviato');
//	sendUrl(url);
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var cRet = httpXml.responseText;
	document.getElementById("check").checked = true;
};

var sendpickCheck = function() {
	"use strict";
	var chkcodice = document.getElementById("chkcodice").value;
	var reqcodice = document.getElementById("reqcodice").value;
	if (chkcodice === reqcodice) {
		var idriga = document.getElementById("idriga").value;
		var quantita = document.getElementById("quantita").value;
		var url = "writepick.php?idriga=" + idriga + "&quantita=" + quantita;
		document.getElementById("check").checked = true;
		sendUrl(url);
		// se  see ci sono altre righe ordine passo automaticamente alla successiva
		var idnextriga = document.getElementById("idnextriga").value;
		if (idnextriga != "0") {
			window.location = idnextriga;
		}
		else {
			alert("Non ci sono altre righe.");
		} 	
	} else {
		alert("Codice non corretto!");
	}
};

var senduser = function(id_testa) {
	"use strict";
	var user = document.getElementById("user" + id_testa).value;
	var url = "writeuser.php?id=" + id_testa + "&user=" + user;
	sendUrl(url);
};

var checkCodiceArti = function(cCodice, cCF) {
	"use strict";
	if (cCodice.substring(0, 3) !== "292" && cCodice.substring(0, 3) !== "293") {
		var url = "getcodicearti.php?cod=" + encodeURIComponent(cCodice);
		if (cCF !== "") {
			url = url + "&cf=" + cCF;
		}
		makeHttpXml();
		httpXml.open("GET", url, false);
		httpXml.send(null);
		var cRet = httpXml.responseText;
		if ("*error*" === cRet) {
			alert("Codice non riconosciuto");
			cRet = "";
		}
		return cRet;
	} else {
		return cCodice;
	}
};

var checkCollo = function(id, collo, close, ncolli) {
    "use strict";

//    alert("ID:" + id + " COLLO:" + collo + " CLOSE:" + close + " NCOLLI:" + ncolli);
    soloNumeri("collo");
    if (collo == 0) {
        alert("Numero collo non specificato!");
        return false;
    }
    var url = "getcollofree.php?id=" + id + "&collo="+collo;
    if (close === true) {
        url += "&close";
    }
    if(ncolli== 2){
        url += "&extracollo";
    }
    var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;
    //alert(url);
    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    var cRet = httpXml.responseText;
//	alert(cRet);
    if(document.getElementById("collo").readOnly) {
        return true;
    };
    //cRet = cRet.slice(0,2);
    if (cRet.slice(0,2) !== "ok") {
        if (cRet.slice(0,2).trim() === "0") {
            alert("Collo gi� chiuso!");
            return true;
        } else {
            alert("Numero collo utilizzato da altro operatore\n" +
                "Primo numero disponibile: "+cRet.slice(0,3).trim());
            document.getElementById("collo").value = cRet.slice(0,3).trim();
            return false;
        }
    } else {
        return true;
    }
};

var printCollo = function(id, collo) {
	"use strict";
	var url = "pl-print.php?id=" + id + "&collo="+collo;
	var milliseconds = new Date().getTime(); 
	url += "&x=" + milliseconds;
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var cRet = httpXml.responseText;
	return true;
};

var checkUbicaz = function(idUbicaz) {
	"use strict";
	var codice = document.getElementById("reqcodice").value;
	var ubicazione = document.getElementById(idUbicaz).value.toUpperCase();
	var url = "getubicazione.php?cod=" + encodeURIComponent(ubicazione);
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var cRet = httpXml.responseText;
	if ("*error*" === cRet) {
		if (!confirm("Codice ubicazione non riconosciuto, confermi?") ) {
			return false;
		}
	}
	return true;
};

var sendUbicazione = function() {
	"use strict";
	if (checkUbicaz("ubicazione")) {
		var url = "writeubicazione.php?id=M&codice=" + encodeURIComponent(document.getElementById("reqcodice").value) + "&ubicazione=" + encodeURIComponent(document.getElementById("ubicazione").value.toUpperCase());
		sendUrl(url);
		alert("Ubicazione sostituita");
	}
};

var writeUbicaz = function(n) {
	"use strict";
	if (checkUbicaz("ub" + n)) {
		var url = "writeubicazione.php?id="+n+"&codice=" + encodeURIComponent(document.getElementById("reqcodice").value) + "&ubicazione=" + encodeURIComponent(document.getElementById("ub"+n).value.toUpperCase());
		sendUrl(url);
	}
};

var changeIframeSrc = function(id, url) {
	"use strict";
    var el = document.getElementById(id);
    if (el && el.src) {
        el.src = url;
        return false;
    }
    return true;
};

var del = function(table, id) {
	"use strict";
    var url="delete.php?id=" + id + "&table=" + table;
	sendUrl(url);
	window.location.reload();
};

var showHideText = function(box,id) {
	var elm = document.getElementById(id);
	elm.style.display = box.checked ? "block" : "none";
};
