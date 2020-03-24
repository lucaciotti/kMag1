function printCollo(id, collo) {
    "use strict";
    var url = "pl-print.php?id=" + id + "&collo=" + collo;
    var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;
    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    var cRet = httpXml.responseText;
    return true;
}

function setPrinter(value) {
    createCookie("plprinter", value, 10);
    return true;
}

var toggleAll = function(status) {
  $("INPUT[type='checkbox'].linked").each(function() {
    $(this).prop("checked", status);
  });
};

var doPrint = function() {
  var idTesta = $("#id_testa").prop("value");
  var prt = $("#prt").prop("value");
  var peso = $("#label_peso").prop("checked") ? 1 : 0;
  var url =
    "../plMake/07_print.php?idTesPb=" +
    idTesta +
    "&prt=" +
    prt +
    "&warnpeso=" +
    peso +
    "&prtbanc=0&collo=";
  $("INPUT[type='checkbox']:checked.linked").each(function() {
    var collo = $(this).prop("value");
    $.get(url + collo);
  });
  alert("Stampe inviate");
};

var doPrintBanc = function() {
  var idTesta = $("#id_testa").prop("value");
  var url =
    "../plMake/07_print.php?idTesPb=" +
    idTesta +
    "&prtbanc=1&collo=0&banc=";
  $("INPUT[type='checkbox']:checked.linked").each(function() {
    var collo = $(this).prop("value");
    $.get(url + collo);
  });
  alert("Stampe inviate");
};

// --------------------------------------

function decodePrint(obj) {
    if(obj.value!=''){
        var x = checkIdPrint(obj.value.trim());
        document.getElementById('num').value = x.numero;
        document.getElementById('anno').value = x.esercizio;
    }
    //  document.getElementById('id_testa').value = x.id_testa;
}

function checkIdPrint(cId) {
    var oRet = new Object();
    var url = window.basePATH + "docRigGet.php?cod=" + encodeURIComponent(cId);
    var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;
    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    var xRet = httpXml.responseXML;
    oRet.codice = xRet.getElementsByTagName("codicearti")[0].firstChild.nodeValue;
    if ("*error*" == oRet.codice) {
        alert("Codice non riconosciuto");
        oRet.codice = "";
        oRet.numero = "";
        oRet.esercizio = "";
        oRet.id_testa = 0;
    } else {
        oRet.numero = xRet.getElementsByTagName("numerodoc")[0].firstChild.nodeValue;
        oRet.esercizio = xRet.getElementsByTagName("esercizio")[0].firstChild.nodeValue;
        oRet.id_testa = xRet.getElementsByTagName("id_testa")[0].firstChild.nodeValue;
    }
    return oRet;
}