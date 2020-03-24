//
function detailReparto(cIdTesta){
	console.log(cIdTesta);
	var cBox = cIdTesta+'.detail';
	//var cBox2 = cIdTesta+'.detail2';
	console.log(cBox);
	//console.log(cBox2);

	var url = "getPLdetailReparto.php?id=" + encodeURIComponent(cIdTesta);
	var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var cRet = httpXml.responseText;

	console.log(cRet);

	document.getElementById(cBox).innerHTML = cRet;
	document.getElementById('detail').innerHTML = "Detagli: Preparato Reparto";
	//document.getElementById('detail').style.display = "block";
	document.getElementById(cBox).style.display = "block";

	totReparto(cIdTesta);

	return true;
};

function totReparto(cIdTesta){
	console.log(cIdTesta);
	var cBoxTot = cIdTesta+'.tot';
	console.log(cBoxTot);

	var url = "getPLtotReparto.php?id=" + encodeURIComponent(cIdTesta);
	var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var cRet = httpXml.responseText;

	console.log(cRet);

	document.getElementById(cBoxTot).innerHTML = cRet;

	return true;
};

function findBO(cIdOC, cIdPL){
	console.log(cIdPL);
	console.log(cIdOC);
	var cBox = cIdPL+'.detail';
	//var cBox2 = cIdTesta+'.detail2';
	console.log(cBox);
	//console.log(cBox2);

	var url = "getPLtoBO.php?id=" + encodeURIComponent(cIdOC);
	var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var cRet = httpXml.responseText;

	console.log(cRet);

	document.getElementById(cBox).innerHTML = cRet;
	document.getElementById('detail').innerHTML = "Detagli: Bolla di Riferimento";
	//document.getElementById('detail').style.display = "block";
	document.getElementById(cBox).style.display = "block";

	return true;
};

function excelFile(myData, codNation){
	var url = "http://172.16.2.102:3000?data="+myData+"&nat="+codNation;
	var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;
	console.log(url);
	var message = "ATTENZIONE!!\nQuesta Procedura avvierà la compilazione di un file Excel di Pronto Consegna.\nLa Procedura potrebbe richiedere diversi secondi.\nProcedo?";

	var r=window.confirm(message);
	if (r == false){
		return false;
	} else {
		//window.location.assign(url);
		window.open(url,'_blank');
		return true;
	}
};

function excelFile2(myData, codNation){
	var url = "http://172.16.2.102:3001?data="+myData+"&nat="+codNation;
	var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;
	console.log(url);
	var message = "ATTENZIONE!!\nQuesta Procedura avvierà la compilazione di un file Excel di Pronto Consegna.\nLa Procedura potrebbe richiedere diversi secondi.\nProcedo?";

	var r=window.confirm(message);
	if (r == false){
		return false;
	} else {
		//window.location.assign(url);
		window.open(url,'_blank');
		return true;
	}
};

function prontoMerce(myId, myRestore){
	var url = "pl-setReady.php?id_testa="+myId+"&res="+myRestore;
	var milliseconds = new Date().getTime();
    url += "&x=" + milliseconds;
	console.log(url);
	if(myRestore > 0){
		var message = "ATTENZIONE!!\nRIPRISTINO la PL per la modifica dei colli?";
	} else {
		var message = "ATTENZIONE!!\nDichiaro PRONTA la PL per la spedizione?";		
	}

	var r=window.confirm(message);
	if (r == false){
		return false;
	} else {
		window.location.assign(url);
		//window.open(url,'_blank');
		return true;
	}
};