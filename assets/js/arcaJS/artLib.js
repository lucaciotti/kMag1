var decode = function(obj, elm) {
    if (obj.value == "") return;
    obj.value = checkCodiceArti(obj.value);
    var tbox = document.getElementById(elm);
    tbox.value = obj.value;
};

function cleanCode (code, elm) {
    if (code.indexOf("*") > -1) {
        var n = code.trim().length;
        code = code.substring(1, n - 1);
    }
    if(elm){
        document.getElementById(elm).value = code;
    } else {
        return code
    }
}

var cleanObj = function (Obj) {
    Obj.value = cleanCode(Obj.value);
};

// --------------------------------------------

 function checkCodiceArti(cCodice, cCF) {
    "use strict";
    if (cCodice.substring(0, 3) !== "292" && cCodice.substring(0, 3) !== "293") {
        var url = window.basePATH + "codeArtCheck.php?cod=" + encodeURIComponent(cCodice);
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
}

function checkCodiceArtix (cCodice, cCF) {
    var url = window.basePATH + 'codeArtGet.php?cod=' + encodeURIComponent(cCodice);
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
    if ("*error*" == oRet.codice) {
        alert("Codice non riconosciuto");
        oRet.codice = "";
        oRet.lottoob = 0;
    } else {
        oRet.lottoob = xRet.getElementsByTagName("lottoob")[0].firstChild.nodeValue;
        oRet.xml = xRet;
    }
    return oRet;
}

function getLottiArti(cCodice, cLotto, cCF) {
    var url = window.basePATH + 'LottiGet.php?cod=' + encodeURIComponent(cCodice);
    if (cCF != "") {
        url = url + "&cf=" + cCF;
    }
    if (cLotto != "") {
        url = url + "&lotto=" + cLotto;
    }
    var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;

    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    return httpXml.responseText;

    // var oRet = new Object();
    // oRet.codice = xRet.getElementsByTagName("codice")[0].firstChild.nodeValue;
    // if ("*error*" == oRet.codice) {
    //     alert("Codice non riconosciuto");
    //     oRet.codice = "";
    //     oRet.lottoob = 0;
    // } else {
    //     oRet.lottoob = xRet.getElementsByTagName("lottoob")[0].firstChild.nodeValue;
    //     oRet.xml = xRet;
    // }
    // return oRet;
}

function checkGiacArtix (cCodice, cLotto, nEsercizio) {
    var url = window.basePATH + 'giacArtGet.php?cod=' + encodeURIComponent(cCodice);
    url = url + '&lotto=' + encodeURIComponent(cLotto);
    url = url + '&esercizio=' + nEsercizio;
    var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;

    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    return httpXml.responseText;
};

function checkUbicaz(idUbicaz) {
    "use strict";
    var codice = document.getElementById("reqcodice").value;
    var ubicazione = document.getElementById(idUbicaz).value.toUpperCase();
    var url = "getubicazione.php?cod=" + encodeURIComponent(ubicazione);
    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    var cRet = httpXml.responseText;
    if ("*error*" === cRet) {
        if (!confirm("Codice ubicazione non riconosciuto, confermi ugualmente?")) {
            return false;
        }
    }
    return true;
}

var sendUbicazione = function () {
    "use strict";
    if (checkUbicaz("ubicazione")) {
        var url = "writeubicazione.php?id=M&codice=" + encodeURIComponent(document.getElementById("reqcodice").value) + "&ubicazione=" + encodeURIComponent(document.getElementById("ubicazione").value.toUpperCase());
        sendUrl(url);
        //window.open(url,"_self");
        alert("Ubicazione sostituita");
    }
};

var writeUbicaz = function (n) {
    "use strict";
    if (checkUbicaz("ub" + n)) {
        var url = "writeubicazione.php?id=" + n + "&codice=" + encodeURIComponent(document.getElementById("reqcodice").value) + "&ubicazione=" + encodeURIComponent(document.getElementById("ub" + n).value.toUpperCase());
        sendUrl(url);
    }
};