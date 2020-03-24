<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Ricerca fornitore documento");
?>
<script>
    function checkCodFor(cCodice)  {
        if (cCodice.substr(0,3) == "111") {
            var url = "getCodForx.php?cod=" + encodeURIComponent(cCodice.substring(7,12));
            makeHttpXml();
            httpXml.open("GET", url, false);
            httpXml.send(null);
            var xRet = httpXml.responseXML;

            var oRet = new Object();
            oRet.codice = xRet.getElementsByTagName("codice")[0].firstChild.nodeValue;

            if ("*error*" == oRet.codice)  {
                alert("Fornitore non trovato");
                oRet.codice = "";
            }
            else{
                //aggiungo i campi nell'oggetto
                oRet.ragsoc = xRet.getElementsByTagName("ragsoc")[0].firstChild.nodeValue;
                oRet.indirizzo = xRet.getElementsByTagName("indirizzo")[0].firstChild.nodeValue;
                oRet.cap = xRet.getElementsByTagName("cap")[0].firstChild.nodeValue;
                oRet.localita = xRet.getElementsByTagName("localita")[0].firstChild.nodeValue;
                oRet.prov = xRet.getElementsByTagName("prov")[0].firstChild.nodeValue;
            }
        } else {
            alert("Codice non valido");
            oRet.codice = "";
        }
        return oRet;
    }

    function decode(obj) {
        var oForn = checkCodFor(obj.value);
        document.getElementById('codfor').value = oForn.codice;
        document.getElementById('ragsoc').innerHTML = oForn.ragsoc;
        document.getElementById('indirizzo').innerHTML = oForn.indirizzo;
        document.getElementById('cap').innerHTML = oForn.cap;
        document.getElementById('localita').innerHTML = oForn.localita;
        document.getElementById('prov').innerHTML = oForn.prov;
    }

</script>
Barcode Fornitore:
<input type="text" onblur="decode(this);"/>
<table border='1' width="400px">
    <tr>
        <td width="150px">Ragione Sociale</td>
        <td>
            <label name="ragsoc" id="ragsoc"></label>
        </td>
    </tr>
    <tr>
        <td>Indirizzo</td>
        <td>
            <label name="indirizzo" id="indirizzo"></label>
        </td>
    </tr>
    <tr>
        <td>Cap</td>
        <td>
            <label name="cap" id="cap"></label>
        </td>
    </tr>
    <tr>
        <td>Localit&agrave</td>
        <td>
            <label name="localita" id="localita"></label>
        </td>
    </tr>
    <tr>
        <td>Prov.</td>
        <td>
            <label name="prov" id="prov"></label>
        </td>
    </tr>
</table>
<form name="input" action="testadocfor-lista.php" method="get" >
    <input type="hidden" name="codfor" id="codfor" />
    <input type="hidden" name="user" id="user" />
    <input type="hidden" name="mode" id="mode" value="b" />
    <input type="submit" id="btnok" value="Cerca" />
</form>
<?php
print ("<br/><a href=\"index.php\"><img noborder src=\"b_home.gif\"/>Menu principale</a>\n");
footer();
?>