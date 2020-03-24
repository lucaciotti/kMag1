var sendpick = function () {
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

var sendpickCheck = function () {
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
        } else {
            alert("Non ci sono altre righe.");
        }
    } else {
        alert("Codice non corretto!");
    }
};