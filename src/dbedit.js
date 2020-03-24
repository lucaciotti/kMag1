/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

function validateQta(obj, orVal) {
  if (obj.value > orVal) {
    alert("Quantit� superiore alla richiesta");
    obj.focus();
	return false;
  }
  if (obj.value < orVal) {
//    alert("Inferiore");
	obj.setAttribute('onblur','validateQta(this,'+obj.value+');');
	var rownum = obj.id.substr(3);
    alert(rownum);
	var tbl = document.getElementById('tblbody');

      var maxrow = tbl.rows.length +1;
      var newval = orVal - obj.value;
      var artobj = document.getElementById('code'+rownum);
      var clonedRow = tbl.rows[rownum-1].cloneNode(true);
      tbl.insertBefore(clonedRow, tbl.rows[rownum]);
      clonedRow.cells[0].innerHTML = '<div id="num"'+maxrow+' style="font-size :10; text-align:center;">'+rownum+'.B</div>\n';
      //clonedRow.cells[0].innerHTML.setAttribute('id', 'num'+maxrow);

      clonedRow.cells[1].innerHTML = '<input type="text" readonly="readonly" size="16" name="code'+maxrow+'" id="code'+maxrow+'" value="' + artobj.value + '">'

      clonedRow.cells[2].innerHTML = '<input type="text" size="3" name="qta'+maxrow+'" id="qta'+maxrow+'" onblur="validateQta(this,' + newval + ');" value="' + newval + '">';

      clonedRow.cells[3].innerHTML.setAttribute('id', 'lotto'+maxrow);

      clonedRow.cells[4].innerHTML.setAttribute('id', 'giac'+maxrow);

	count.setAttribute('value', (count.getAttribute('value') * 1) +1 );
	return false;
  }
  return true;
}

function stampaEtichetta(row) {
  var artobj = document.getElementById('code'+row);
  var lottoobj = document.getElementById('lotto'+row);
  var url = "writeetich.php?codicearti=" + artobj.value.trim() + "&lotto=" + lottoobj.value.trim() + "&reparto=&quantita=1";
  sendUrl(url);
}

function validateLotto(obj, row) {
    var lotto = obj.value.trim();
    if("" == lotto) {
        document.getElementById('giac'+row).value = 0;
        return true;
    }
    var cCodice = document.getElementById('code'+row).value.trim();
    var giac = checkGiacArtix(cCodice, lotto).giacenza;
    //alert(lotto+cCodice+giac);
    document.getElementById('giac'+row).value = giac;
    return true;
}

function checkGiacArtix(cCodice, cLotto)  {
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

function checkForm(){
    var nRighe = document.getElementById('count').value;
    var msg = '';
    for (var i = 1; i <= nRighe; i++){
        var code = document.getElementById('code'+i).value.trim();
        var giac = parseFloat(document.getElementById('giac'+i).value);
        var qta = parseFloat(document.getElementById('qta'+i).value);
        if (giac < qta){
            msg += "Comp. "+code+" con Quantita' maggiore alla Giacenza\n";
        }
    }
    if(msg != ""){
        alert("Attenzione!\n"+msg)
        return false;
    } else {
        return true;
    }
}
