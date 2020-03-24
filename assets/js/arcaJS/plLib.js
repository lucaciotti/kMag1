function decodeArt(obj) {
    var val = cleanCode(obj.value.trim().toUpperCase());
    var codArtBox = htmlEntities(String($("#codArt").text())).trim();
    if (val != "") {
        var oArt = checkCodiceArtix(val);
        var codArtRes = String(oArt.codice).trim();
        if (codArtBox !== codArtRes && codArtBox.length !== codArtRes.length) {
            obj.focus();
            alert("Codice non corrisponde");
            return false;
        }
        obj.value = codArtRes;
        obj.disabled = true;
        var newUm = oArt.xml.getElementsByTagName("unmisura")[0].firstChild
            .nodeValue;
        //Mappo i Box di riferimento
        var umBox = document.getElementById("um");
        var qtaBox = $("#qta");
        //imposto Fattore di conversione precedente
        var oldFatt = umBox.options[umBox.selectedIndex].value;
        $("#oldFatt").val(oldFatt);

        for (var j = 0; j < umBox.length; j++) {
            if (umBox.options[j].innerHTML == newUm) {
                umBox.selectedIndex = j;
                qtaBox.val((qtaBox.val() * oldFatt) / umBox.options[j].value);
                $("#newFatt").val(umBox.options[j].value);
                umBox.disabled = true;
            }
        }

        //Controllo il lotto obbligatorio
        if (oArt.xml.getElementsByTagName("lottoob")[0].firstChild.nodeValue == 0) {
            document.getElementById("lotto").disabled = false;
            document.getElementById("qta").focus();
        } else {
            document.getElementById("lotto").focus();
        }
        return true;
    }
}

function checkLotto(obj) {
    var lotto = cleanCode(obj.value.trim().toUpperCase());
    obj.value = lotto;
    if (lotto == "") return true;
    detailLotto = $.parseJSON(getLottiArti(codArt, lotto));
    if (detailLotto == "*error*") {
        alert("Lotto " + lotto + " Not available");
        obj.value = "";
        obj.focus();
        return false;
    }
    // console.log(detailLotto[0].u_noce);
    if (soloceCli && detailLotto[0].u_noce) {
        alert(
            "Lotto " +
            lotto +
            " non di origine CEE. Non utilizzabile per questo cliente."
        );
        obj.value = "";
        obj.focus();
        return false;
    } else {
        var isIndustria =
            $.parseJSON(getSetInd(settoreCli)) != "*error*" ? true : false;
        if (isIndustria && detailLotto[0].u_noindus) {
            alert(
                "Lotto " +
                lotto +
                " non per industria. Non utilizzabile per questo cliente."
            );
            obj.value = "";
            obj.focus();
            return false;
        } else {
            document.getElementById("qta").focus();
            return true;
        }
    }
}

function checkQta(obj) {
    var qta = obj.value;
    var qtaRes = $("#qtaRes").val();
    var oldFatt = $("#fattDoc").val();
    var umBox = document.getElementById("um");
    var newFatt = umBox.options[umBox.selectedIndex].value;
    $("#newFatt").val(newFatt);
    qtaRes = (qtaRes * oldFatt) / newFatt;
    var lotto = $("#lotto")
        .val()
        .trim();
    if (
        $("#lottoobb")
            .text()
            .trim() == "Obbligatorio" &&
        lotto == ""
    ) {
        alert("Lotto OBBLIGATORIO");
        $("#lotto").val("");
        $("#lotto").focus();
        return false;
    }
    if (qta != 0) {
        var giac = checkGiacArtix(codArt, lotto, esercizio);
        var fatt = newFatt;
        var um = umBox.options[umBox.selectedIndex].innerHTML;
        giac = giac / fatt;
        if (qta > qtaRes) {
            alert("Qta Superiore a Qta Residua");
            return false;
        } else {
            if (qta > giac) {
                if (lotto != "") {
                    var message = "Qta Superiore a GIACENZA LOTTO : " + giac + " " + um;
                } else {
                    var message = "Qta Superiore a GIACENZA: " + giac + " " + um;
                }
                alert(message);
                obj.focus();
                return false;
            } else {
                return true;
            }
        }
    }
}

function checkCollo(obj) {
    var idpl = $("#idtesta").val();
    soloNumeri("collo");
    var collo = obj.value;
    var close = $("#close").is(":checked");
    var nColli = $("#ncolli").val();
    var collo2Reserved = false;
    if (collo == 0) {
        return true;
    }
    //Controllo collo sia disponibile
    {
        var url =
            window.basePATH + "plColloGetByNcollo.php?id=" + idpl + "&collo=" + collo;
        var milliseconds = new Date().getTime();
        url += "&x=" + milliseconds;
        makeHttpXml();
        httpXml.open("GET", url, false);
        httpXml.send(null);
        var cRet = httpXml.responseText;
        if (cRet == -1) {
            //collo disponibile e non prenotato
            colloReserved = false;
            console.log("collo disponibile e non prenotato", colloReserved);
        } else if (cRet != termid) {
            //collo prenotato da altro utente
            var url = window.basePATH + "plColloNew.php?id=" + idpl;
            var milliseconds = new Date().getTime();
            url += "&x=" + milliseconds;
            makeHttpXml();
            httpXml.open("GET", url, false);
            httpXml.send(null);
            var newCollo = httpXml.responseText;
            alert(
                "Numero collo utilizzato da altro operatore\n" +
                "Primo numero disponibile: " +
                newCollo
            );
            obj.value = newCollo;
            collo = newCollo;
            colloReserved = false;
            console.log("nuovo collo preso", colloReserved);
        } else {
            // già prenotato da me
            colloReserved = true;
            console.log("già prenotato", colloReserved);
        }
    }
    //Controllo che in caso di doppio collo sia libero il collo+1
    {
        if (nColli > 1) {
            var url =
                window.basePATH +
                "plColloGetByNcollo.php?id=" +
                idpl +
                "&collo=" +
                collo +
                1;
            var milliseconds = new Date().getTime();
            url += "&x=" + milliseconds;
            makeHttpXml();
            httpXml.open("GET", url, false);
            httpXml.send(null);
            var cRet = httpXml.responseText;
            if (cRet == -1) {
                //collo disponibile e non prenotato
                collo2Reserved = false;
                console.log("Secondo collo disponibile e non prenotato", colloReserved);
            } else if (cRet != termid) {
                //collo prenotato da altro utente
                alert("Attenzione Problema a prenotare Secondo Collo...Riprovare");
                return false;
            } else {
                // già prenotato da me
                collo2Reserved = true;
                console.log("Secondo collo già prenotato", colloReserved);
            }
        }
    }

    //prenoto il collo/i
    if (!colloReserved) {
        var url =
            window.basePATH +
            "plColloIns.php?id=" +
            idpl +
            "&termid=" +
            termid +
            "&collo=" +
            collo;
        var milliseconds = new Date().getTime();
        url += "&x=" + milliseconds;
        //alert(url);
        makeHttpXml();
        httpXml.open("GET", url, false);
        httpXml.send(null);
        var cRet = httpXml.responseText;
        if (cRet != "success") {
            alert("Something Wrong appened!");
            return false;
        }
        colloReserved = true;
    }
    if (nColli > 1 && !collo2Reserved) {
        var url =
            window.basePATH +
            "plColloIns.php?id=" +
            idpl +
            "&termid=" +
            termid +
            "&collo=" +
            collo +
            1;
        var milliseconds = new Date().getTime();
        url += "&x=" + milliseconds;
        //alert(url);
        makeHttpXml();
        httpXml.open("GET", url, false);
        httpXml.send(null);
        var cRet = httpXml.responseText;
        if (cRet != "success") {
            alert("Something Wrong appened!");
            return false;
        }
        collo2Reserved = true;
    }
    //chiudo il collo
    if (close && colloReserved) {
        var url =
            window.basePATH +
            "plColloChiudi.php?id=" +
            idpl +
            "&termid=" +
            termid +
            "&collo=" +
            collo;
        var milliseconds = new Date().getTime();
        url += "&x=" + milliseconds;
        //alert(url);
        makeHttpXml();
        httpXml.open("GET", url, false);
        httpXml.send(null);
        var cRet = httpXml.responseText;
        if (cRet != "success") {
            alert("Something Wrong appened!");
            return false;
        }
    }
    if (close && collo2Reserved) {
        var url =
            window.basePATH +
            "plColloChiudi.php?id=" +
            idpl +
            "&termid=" +
            termid +
            "&collo=" +
            collo +
            1;
        var milliseconds = new Date().getTime();
        url += "&x=" + milliseconds;
        //alert(url);
        makeHttpXml();
        httpXml.open("GET", url, false);
        httpXml.send(null);
        var cRet = httpXml.responseText;
        if (cRet != "success") {
            alert("Something Wrong appened!");
            return false;
        }
    }
    return true;
}

function checkRespForm() {
    var check = true;
    var lotto = $("#lotto")
        .val()
        .trim();
    //controllo che sia stato sparato il codice a barre dell'articolo
    if (
        $("#lottoobb")
            .text()
            .trim() == "Obbligatorio" &&
        $("#controllo")
            .val()
            .trim() == ""
    ) {
        alert("Controllo Articolo OBBLIGATORIO!");
        $("#controllo").val("");
        $("#controllo").focus();
        return false;
    }
    //controllo che sia stato sparato il codice lotto
    if (
        $("#lottoobb")
            .text()
            .trim() == "Obbligatorio" &&
        lotto == ""
    ) {
        alert("Lotto OBBLIGATORIO");
        $("#lotto").val("");
        $("#lotto").focus();
        return false;
    }
    //controllo la Giacenza
    check = checkQta(document.getElementById("qta"));
    if (check == false) {
        return false;
    } else {
        check = recapRespForm();
        if (check == false) {
            return false;
        } else {
            //controllo un ultima volta che il collo sia OK
            return checkCollo(document.getElementById("collo"));
        }
    }
}

function recapRespForm() {
    var qta = $("#qta").val();
    var qtaRes = $("#qtaRes").val();
    var oldFatt = $("#fattDoc").val();
    var umBox = document.getElementById("um");
    var newFatt = umBox.options[umBox.selectedIndex].value;
    var collo = parseInt($("#collo").val());
    var lotto = new String($("#lotto").val());
    var umOld = new String($("#umDoc").val());
    var umNew = new String(umBox.options[umBox.selectedIndex].innerHTML);
    var close = $("#close").is(":checked") ? "\n*Collo Chiuso" : "";
    var stampo = $("#print").is(":checked") ? "\n*Stampo Collo" : "";

    var message = "Collo n." + collo + "\nArt: " + codArt + "\n";
    if (lotto.trim() != "") {
        message = message + "Lotto: " + lotto + "\n";
    }
    if (umOld.trim() == umNew.trim() || oldFatt == 0) {
        message = message + "Qta: " + qta + " " + umNew + "" + close + "" + stampo;
    } else {
        var qta2 = (qta * newFatt) / oldFatt;
        message =
            message +
            "Qta: " +
            qta +
            " " +
            umNew +
            "  ==>  " +
            qta2 +
            " " +
            umOld +
            "" +
            close +
            "" +
            stampo;
    }
    message = message + "\nPROCEDO?";
    var r = confirm(message);
    if (r == true) {
        return true;
    } else {
        $("#qta").focus();
        return false;
    }
}

function chkColloPann_click() {
    if ($("#chkColloPann").is(":checked")) {
        $("#colloPann").text($("#colloPann").text() - 1);
        $("#ncolli").val(1);
    } else {
        $("#colloPann").text($("#colloPann").text() * 1 + 1);
        $("#ncolli").val(2);
    }
}

// SEZIONE IMBALLI
function decodeImb(obj, isSecondo) {
    var val = cleanCode(obj.value.trim().toUpperCase());
    var colloDoppio = isSecondo ? "" : "2";
    if (val != "") {
        var oArt = checkCodiceArtix(val);
        var codImb = String(oArt.codice).trim();
        var isImballo = oArt.xml.getElementsByTagName("imballo")[0].firstChild
            .nodeValue;
        if (codImb != "" && isImballo == 0) {
            alert("L'articolo non &egrave; un imballo.");
            obj.value = "";
            obj.focus();
        } else if (codImb == "NOTA IMBALLO") {
            if (isSecondo) {
                setMisuraImb(checkCodiceArtix($("#artcollo").val()), colloDoppio);
            } else {
                var oRiga = getRigaDocx($("#id").val() * 1);
                var artPL = oRiga.getElementsByTagName("codicearti")[0].firstChild
                    .nodeValue;
                setMisuraImb(checkCodiceArtix(artPL), colloDoppio);
            }
        } else {
            setMisuraImb(oArt, colloDoppio);
        }
    }

    //SE FILIALE INSERISCO SEMPRE PESO - ALTRIMENTI SOLO SE FASCIO
    if ($("#isFiliale").val()) {
        if (isSecondo) {
            var peso = getPesoCollo(
                $("#id_pl").val(),
                $("#id").val(),
                $("#collo").val() * 1 + 1
            );
            $("#pesocollo2").val(peso);
        } else {
            var peso = getPesoCollo(
                $("#id_pl").val(),
                $("#id").val(),
                $("#collo").val()
            );
            $("#pesocollo").val(peso);
        }
    } else {
        if (codImb == "#KZ-SCG(009)") {
            if (isSecondo) {
                var peso = getPesoCollo(
                    $("#id_pl").val(),
                    $("#id").val(),
                    $("#collo").val() * 1 + 1
                );
                $("#pesocollo2").val(peso);
            } else {
                var peso = getPesoCollo(
                    $("#id_pl").val(),
                    $("#id").val(),
                    $("#collo").val()
                );
                $("#pesocollo").val(peso);
            }
        }
    }

    obj.value = codImb;
}

function chkPesoCollo(obj, isSecondo) {
    if (isSecondo) {
        var peso = getPesoCollo(
            $("#id_pl").val(),
            $("#id").val(),
            $("#collo").val() * 1 + 1
        );
    } else {
        var peso = getPesoCollo(
            $("#id_pl").val(),
            $("#id").val(),
            $("#collo").val()
        );
    }
    if (obj.value < peso) {
        alert("Attenzione:\nPeso inferiore al peso netto");
    }
    if (obj.value > peso * 1.1) {
        alert("Attenzione:\nDifferenza con il peso teorico\nsuperiore al 10%");
    }
}

function clickBancale() {
    var isBanc = $("#hasbanc").is(":checked");
    if (isBanc) {
        $("#askbanc").show();
        return checkBanc(false, false);
    } else {
        $("#askbanc").hide();
    }
}

function checkBanc(reserve, close, quiet) {
    soloNumeri("bancnum");
    var nBanc = $("#bancnum").val();
    if (nBanc == 0) {
        alert("Numero bancale non specificato!");
        return false;
    }
    var idpl = $("#id_pl").val();
    var rep = $("#rep option:selected").val();
    var bancReserved = false;

    //Contr ollo bancale sia disponibile
    {
        var url =
            window.basePATH +
            "plBancGetByRep.php?id=" +
            idpl +
            "&rep=" +
            rep +
            noCache();
        makeHttpXml();
        httpXml.open("GET", url, false);
        httpXml.send(null);
        var cRet = httpXml.responseText;
        if (cRet == -1) {
            //nessun bancale prenotato da reparto
            bancReserved = false;
            console.log("NO banc prenotato da rep ", rep);
            //prendo nuovo bancale
            url = window.basePATH + "plBancNew.php?id=" + idpl + noCache();
            makeHttpXml();
            httpXml.open("GET", url, false);
            httpXml.send(null);
            var newBanc = httpXml.responseText;
            $("#bancnum").val(newBanc);
            nBanc = newBanc;
            bancReserved = false;
            console.log("nuovo bancale preso", bancReserved);
        } else {
            // già prenotato da questo reparto
            $("#bancnum").val(cRet);
            nBanc = cRet;
            bancReserved = false;
            bancReserved = true;
            console.log("già prenotato", bancReserved);
        }
    }

    //prenoto il bancale
    if (!bancReserved && reserve) {
        var url =
            window.basePATH +
            "plBancIns.php?id=" +
            idpl +
            "&rep=" +
            rep +
            "&banc=" +
            nBanc +
            noCache();
        makeHttpXml();
        httpXml.open("GET", url, false);
        httpXml.send(null);
        var cRet = httpXml.responseText;
        if (cRet != "success") {
            alert("Something Wrong appened!");
            return false;
        }
        bancReserved = true;
    }

    //chiudo il collo
    if (close && bancReserved) {
        var url =
            window.basePATH +
            "plBancChiudi.php?id=" +
            idpl +
            "&rep=" +
            rep +
            "&banc=" +
            nBanc +
            noCache();
        makeHttpXml();
        httpXml.open("GET", url, false);
        httpXml.send(null);
        var cRet = httpXml.responseText;
        if (cRet != "success") {
            alert("Something Wrong appened!");
            return false;
        }
    }
    return true;
}

function showAskBanc() {
    var isClose = $("#closebanc").is(":checked");
    checkBanc(true, false); //intanto lo riservo
    if (isClose) {
        $("#askcodbanc").show();
    } else {
        $("#askcodbanc").hide();
    }
}

function decodeBanc(obj) {
    var val = cleanCode(obj.value.trim().toUpperCase());
    if (val == "" || val == "NONE") {
        alert("L'articolo non e' un bancale.");
        return;
    }
    var oArt = checkCodiceArtix(val);
    $("#pal_u_misural").val(
        oArt.xml.getElementsByTagName("u_misural")[0].firstChild.nodeValue
    );
    $("#pal_u_misuras").val(
        oArt.xml.getElementsByTagName("u_misuras")[0].firstChild.nodeValue
    );

    //SE FILIALE INSERISCO SEMPRE PESO - ALTRIMENTI SOLO SE FASCIO
    if ($("#isFiliale").val()) {
        var pesoColli = getPesoBanc(
            $("#id_pl").val(),
            $("#id").val(),
            $("#bancnum").val(),
            "lordo"
        );
        var pesoThisCollo = $("#pesocollo").val();
        var pesoNetBanc = oArt.xml.getElementsByTagName("pesounit")[0].firstChild
            .nodeValue;
        var peso = (pesoColli*1) + (pesoThisCollo*1) + (pesoNetBanc*1);
        $("#pesobanc").val(peso);
    } else {
        if (codImb == "#KZ-SCG(009)") {
            var pesoColli = getPesoBanc(
                $("#id_pl").val(),
                $("#id").val(),
                $("#bancnum").val(),
                "lordo"
            );
            var pesoThisCollo = $("#pesocollo").val();
            var pesoNetBanc = oArt.xml.getElementsByTagName("pesounit")[0].firstChild
                .nodeValue;
            var peso = pesoColli * 1 + pesoThisCollo * 1 + pesoNetBanc * 1;
            $("#pesobanc").val(peso);
        }
    }
}

function chkPesoBanc(obj) {
    var pesoColli = getPesoBanc(
        $("#id_pl").val(),
        $("#id").val(),
        $("#bancnum").val(),
        "lordo"
    );
    var pesoThisCollo = $("#pesocollo").val();
    var pesoNetBanc = oArt.xml.getElementsByTagName("pesounit")[0].firstChild
        .nodeValue;
    var peso = pesoColli * 1 + pesoThisCollo * 1 + pesoNetBanc * 1;
    if (obj.value < peso) {
        alert("Attenzione:\nPeso inferiore al peso netto");
    }
    if (obj.value > peso * 1.1) {
        alert("Attenzione:\nDifferenza con il peso teorico\nsuperiore al 10%");
    }
}

function checkImbForm() {
    var codImb = $("#art").val();
    if (codImb == "") {
        alert("Specificare l'imballo");
        $("#art").focus();
        return false;
    }
    if ($("#hasbanc").is(":checked")) {
        if (
            $("#closebanc").is(":checked") &&
            $("#codbanc option:selected").val() == "NONE"
        ) {
            alert("L'articolo non e' un bancale.");
            $("#codbanc").focus();
            return false;
        }
        if (
            $("#closebanc").is(":checked") &&
            ($("#pal_u_misural").val() == 0 ||
                $("#pal_u_misuras").val() == 0 ||
                $("#altezza").val() == 0)
        ) {
            alert("Una o piu' misure pallet mancanti");
            $("#altezza").focus();
            return false;
        }
        if ($("#closebanc").is(":checked") && $("#pesobanc").val() == 0) {
            alert("Peso bancale mancante");
            $("#pesobanc").focus();
            return false;
        }
    }
    if (
        $("#imb_u_misural").val() == 0 ||
        $("#imb_u_misurah").val() == 0 ||
        $("#imb_u_misuras").val() == 0
    ) {
        alert("Una o piu' misure imballo mancanti");
        $("#imb_u_misural").focus();
        return false;
    }
    if ($("#pesocollo").val() == 0) {
        alert("Peso collo mancante");
        $("#pesocollo").focus();
        return false;
    }

    // Gestione pesi e misure secondo collo
    if ($("#ncolli").val() > 1) {
        var codImb2 = $("#art2").val();
        if (codImb2 == "") {
            alert("Specificare l'imballo del secondo collo");
            $("#art2").focus();
            return false;
        }
        if (
            $("#imb_u_misural2").val() == 0 ||
            $("#imb_u_misurah2").val() == 0 ||
            $("#imb_u_misuras2").val() == 0
        ) {
            alert("Una o piu' misure imballo del secondo collo mancanti");
            $("#imb_u_misural2").focus();
            return false;
        }
        if ($("#pesocollo2").val() == 0) {
            alert("Peso secondo collo mancante");
            $("#pesocollo2").focus();
            return false;
        }
    }

    if ($("#closebanc").is(":checked")) {
        checkBanc(true, true);
    }
    return true;
}

// UTILITY IMBALLO

function setMisuraImb(oArt, collo) {
    $("#imb_u_misural" + collo).val(
        oArt.xml.getElementsByTagName("u_misural")[0].firstChild.nodeValue
    );
    $("#imb_u_misurah" + collo).val(
        oArt.xml.getElementsByTagName("u_misurah")[0].firstChild.nodeValue
    );
    $("#imb_u_misuras" + collo).val(
        oArt.xml.getElementsByTagName("u_misuras")[0].firstChild.nodeValue
    );
}

function copiaVal(orig, dest) {
    if ($("#art").val() == "#KZ-SCG(009)") {
        dest.val(orig.val());
    }
}

function getRigaDocx(cId) {
    var url = window.basePATH + "docRigGet.php?cod=" + encodeURIComponent(cId);
    var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;
    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    return httpXml.responseXML;
}

function getPesoCollo(idTespl, idRowPl, nCollo) {
    var url =
        window.basePATH +
        "plPesoColloGet.php?idTesPl=" +
        idTespl +
        "&idRowPl=" +
        idRowPl +
        "&collo=" +
        nCollo +
        noCache();
    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    return httpXml.responseText;
}

function getPesoBanc(idTespl, idRowPl, nBanc, mode) {
    var url =
        window.basePATH +
        "plPesoBancGet.php?idTesPl=" +
        idTespl +
        "&idRowPl=" +
        idRowPl +
        "&banc=" +
        nBanc +
        "&mode=" +
        mode +
        noCache();
    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    return httpXml.responseText;
}

// switch (cRet) {
//     case x:
//         // code block
//         break;
//     case y:
//         // code block
//         break;
//     default:
//     // code block
// }
