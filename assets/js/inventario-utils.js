/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

var oArt;
var checkCodiceArtix = function(cCodice, cCF)  {
	var url = "getcodiceartixx.php?cod=" + encodeURIComponent(cCodice);
	if (cCF != "") {
		url = url + "&cf=" + cCF;
	}
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
		oRet.unmisura  = xRet.getElementsByTagName("unmisura")[0].firstChild.nodeValue;
		oRet.unmisura1  = xRet.getElementsByTagName("unmisura1")[0].firstChild.nodeValue;
		oRet.unmisura2  = xRet.getElementsByTagName("unmisura2")[0].firstChild.nodeValue;
		oRet.unmisura3  = xRet.getElementsByTagName("unmisura3")[0].firstChild.nodeValue;
		oRet.fatt1  = xRet.getElementsByTagName("fatt1")[0].firstChild.nodeValue;
		oRet.fatt2  = xRet.getElementsByTagName("fatt2")[0].firstChild.nodeValue;
		oRet.fatt3  = xRet.getElementsByTagName("fatt3")[0].firstChild.nodeValue;
	}
	return oRet;
};

var decode = function(obj) {
	if(obj.value.trim()==="") return;

	oArt = checkCodiceArtix(obj.value);

	var tbox = document.getElementById('art');
	tbox.value = oArt.codice;

	if(oArt.codice != ""){
		var ubox = document.getElementById('um');
		ubox.options.length = 0;
		var hiddenUM = document.getElementById("unmisura");
		hiddenUM.value = oArt.unmisura;
		var opt = document.createElement("option");
		opt.text = oArt.unmisura;
		opt.value = 1;
		opt.selected = "selected";
		ubox.add(opt);
		if(oArt.unmisura1 != "" && oArt.unmisura1 != oArt.unmisura && oArt.fatt1 != 0) {
			var opt1 = document.createElement("option");
			opt1.text = oArt.unmisura1;
			opt1.value = oArt.fatt1;
			ubox.add(opt1);
		}
		if(oArt.unmisura2 != "" && oArt.unmisura2 != oArt.unmisura && oArt.fatt2 != 0) {
			var opt2 = document.createElement("option");
			opt2.text = oArt.unmisura2;
			opt2.value = oArt.fatt2;
			ubox.add(opt2);
		}
		if(oArt.unmisura3 != "" && oArt.unmisura3 != oArt.unmisura && oArt.fatt3 != 0) {
			var opt3 = document.createElement("option");
			opt3.text = oArt.unmisura3;
			opt3.value = oArt.fatt3;
			ubox.add(opt3);
		}
		
		
		if(1 == oArt.lottoob) {
			document.getElementById('lotto').disabled = false;
		} else {
			document.getElementById('lotto').disabled = true;
		}

	} else {
		tbox.focus();
		return false;
	}
};

var validateForm = function() {
	var ret = true;
	if("" == document.getElementById("id").value) {
		alert("Cartellino non selezionato");
		document.getElementById("id").focus();
		return false;
	}
	if("" == document.getElementById("art").value) {
		alert("Articolo non selezionato");
		document.getElementById("art").focus();
		return false;
	}
	if(document.getElementById("lotto").disabled == false) {
		ret = true;
		lotto = document.getElementById("lotto").value.trim();
		if("" == lotto) {
			alert("Lotto obbligatorio");
			document.getElementById("lotto").focus();
			return false;
		}
		/*oList = oArt.xml.getElementsByTagName("lotto");
		for( var j=0; j < oList.length; j++) {
			if(lotto == oList[j].firstChild.nodeValue) {
				ret = true;
			}
		}*/
		if (ret==false){
			alert("Codice lotto non valido");
			document.getElementById("lotto").focus();
			return false;
		}
	}

	var qta = document.getElementById("qta").value;
	var um = document.getElementById("unmisura").value;
	var temp = document.getElementById("um");
	var um2 = temp.options[temp.selectedIndex].text;
	var fatt2 = temp.options[temp.selectedIndex].value;
	if (um == um2){
		var message = um+" "+qta+"\n PROCEDO?";
	} else {
		var qta2 = qta*fatt2;
		var message = um2+" "+qta+"  ==>  "+um+" "+qta2+"\n PROCEDO?";
	}
	var r=window.confirm(message);
	if (r == false){
		document.getElementById("qta").focus();
		ret=false;
	}
	return ret;
};

//GESTIONE XML
var getCartellinox = function(code) {
	var url = "getinvx.php?cod=" + code;
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;
	
	var oRet = new Object();
	oRet.codice = xRet.getElementsByTagName("codicearti")[0].firstChild.nodeValue;
	if ("*error*" == oRet.codice)  {
		return "{}";
	} else {
		if (xRet.getElementsByTagName("lotto")[0].firstChild.nodeValue=='*none*'){
			oRet.lotto = '';
		} else {
			oRet.lotto = xRet.getElementsByTagName("lotto")[0].firstChild.nodeValue;
		}
		oRet.unmisura  = xRet.getElementsByTagName("unmisura")[0].firstChild.nodeValue;
		oRet.quantita  = xRet.getElementsByTagName("quantita")[0].firstChild.nodeValue;
		if (xRet.getElementsByTagName("ubicaz")[0].firstChild.nodeValue=='*none*'){
			oRet.ubicaz  = '';
		} else {
			oRet.ubicaz  = xRet.getElementsByTagName("ubicaz")[0].firstChild.nodeValue;
		}
		oRet.warn  = xRet.getElementsByTagName("warn")[0].firstChild.nodeValue;
	}
	return oRet;
};


var getMagax = function(code) {
	var url = "getMagax.php?mag=" + code;
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;
	
	var oRet = new Object();
	oRet.maga = xRet.getElementsByTagName("maga")[0].firstChild.nodeValue;
	if ("*error*" == oRet.maga)  {
		return "{}";
	} else {
		oRet.descrizion = xRet.getElementsByTagName("descrizion")[0].firstChild.nodeValue;
		oRet.fiscale  = xRet.getElementsByTagName("fiscale")[0].firstChild.nodeValue;
	}
	return oRet;
};
//FINE GESTIONE XML

//GESTIONE JSON
var getCartellinoj = function(code) {
	var url = "getinv.php?cod=" + code;
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var jRet = httpXml.responseText.trim();
	if("{}" != jRet){
		var cart = JSON.parse(jRet);
		return cart;
	} else {
		return "{}";
	}
};
//FINE GESTIONE JSON

var getCartellino = function(obj) {
	var code = obj.value.trim();
	if(code === "") return;
	if(code.substring(0,2) != "24") {
		alert("Codice cartellino non valido.");
		obj.focus();
		return;
	}
	//document.getElementById("maga").value = "00"+code.substring(4,7);
	
	var mag = getMagax(code.substring(4,7));
	if("{}" != mag){
		document.getElementById("maga").value = mag.maga;
	} else {
		alert("Attenzione: MAGAZZINO non riconosciuto!\nContattare immediatamente il CED.");
		document.getElementById("art").disabled = true;
		document.getElementById("lotto").disabled = true;
		document.getElementById("um").disabled = true;
		document.getElementById("qta").disabled = true;
		document.getElementById("Ok").disabled = true;
		//Rendo visibile Reload
		document.getElementById("semaforo").style.display = "inline";
		document.getElementById("reload").style.display = "block";
		document.getElementById("Ok").style.display = "none";
	}
	if("{}" != mag){
		var cart = getCartellinox(code);
		if("{}" != cart){	
			alert("Attenzione: cartellino già sparato.");
			document.getElementById("art").value = cart.codice;
			document.getElementById("lotto").value = cart.lotto;
			//UM
			var ubox = document.getElementById('um');
			ubox.options.length = 0;
			var opt = document.createElement("option");
			opt.text = cart.unmisura;
			opt.value = 1;
			opt.selected = "selected";
			ubox.add(opt);
			document.getElementById("qta").value = cart.quantita;
			document.getElementById("ubicaz").value = cart.ubicaz.trim();
			//NON si può modificare
			document.getElementById("art").disabled = true;
			document.getElementById("lotto").disabled = true;
			document.getElementById("um").disabled = true;
			document.getElementById("qta").disabled = true;
			document.getElementById("Ok").disabled = true;
			//Rendo visibile Reload
			document.getElementById("semaforo").style.display = "inline";
			if(cart.warn==1){
				document.getElementById("flag").style.display = "inline";
				document.getElementById("ubicaz").disabled = true;
			} else {
				document.getElementById("segnala").style.display = "inline";			
			}
			document.getElementById("reload").style.display = "block";
			document.getElementById("Ok").style.display = "none";
		} else {
			return true;
		}
	}
};

var isNumber = function(n) { 
  return !isNaN(parseFloat(n)) && isFinite(n); 
}; 

var checkValue = function(n) {
  if(!isNumber(n)) {
    alert("Non hai inserito un numero valido");
    return false;
  }
  if( n <0 ) {
    alert("Non si possono inserire numeri negativi");
	return false;
  }
  return true;
};

function reloadPage() {
    location.reload();
};

function riepilogoArt(){
	var art = document.getElementById("art").value.trim();
	var maga = document.getElementById("maga").value;
	var lotto = document.getElementById("lotto").value.trim();
	
	var cBox = 'detail';
	//console.log(cBox);

	var url = "getInvDetailArt.php?articolo=" + encodeURIComponent(art);
	url += "&maga=" + maga;
	url += "&lotto=" + lotto;

	var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;

	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var cRet = httpXml.responseText;

	//console.log(cRet);

	document.getElementById(cBox).innerHTML = cRet;
	document.getElementById(cBox).style.display = "block";
};

function segnalaArt(){
	var id = document.getElementById("id").value.trim();
	var ubi = document.getElementById("ubicaz").value.trim();

	if(ubi==""){
		alert("Inserire Ubicazione!");
		document.getElementById("ubicaz").focus();
		return false;
	} else {
		var url = "setInvWarn.php?id=" + encodeURIComponent(id);
		url += "&ubi=" +  encodeURIComponent(ubi);

		var milliseconds = new Date().getTime();
	    url += "&x=" + milliseconds;

		makeHttpXml();
		httpXml.open("GET", url, false);
		httpXml.send(null);
		var cRet = httpXml.responseText;

		if(cRet.trim()=='OK'){
			alert("Ok Cartellino Segnalato!");
			document.getElementById("flag").style.display = "inline";
			document.getElementById("segnala").style.display = "none";
		}
	}
};


//Funzioni per invTable!! PC
function magaTable(obj) {
	var code = obj.value.trim();
	if(code === "") return;
	var url = "invTable.php?maga="+code;
	window.location.assign(url);
	return true;
};

function deleteCart(id){
	id = String(id);
	var maga = "00"+id.substring(4,7);
	var num = id.substring(7,12);
	var ubi = document.getElementById('ubi').innerHTML.trim();

	var message = "ATTENZIONE!!\nCancellazione Cartellino n. "+num+" con Ubicazione: "+ubi+" in Magazzino: "+maga+"\nProcedo?";

	var r=window.confirm(message);
	if (r == false){
		return false;
	} else {
		var url = "invDelCart.php?id="+id+"&ubi="+ubi;
		window.location.assign(url);
		return true;
	}
};

/*
function submitForm(){
	if(validateForm()==true){
		var id = document.getElementById("id").value;
		var art = document.getElementById("art").value.trim();
		var lotto = document.getElementById("lotto").value.trim();
		var qta = document.getElementById("qta").value;
		var temp = document.getElementById("um");
		var fatt = temp.options[temp.selectedIndex].value;

		var url = "inv_setqta.php?art=" + encodeURIComponent(art);
		url += "&lotto=" + encodeURIComponent(lotto);
		url += "&id=" + encodeURIComponent(id);
		url += "&um=" + encodeURIComponent(fatt);
		url += "&qta=" + encodeURIComponent(qta);

		console.log(url);

		window.location.assign(url);
	} else {
		return false;
	}
}*/