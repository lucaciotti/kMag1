// ***********************************************************************
// Project ArcaWeb
// ===========================
//
// Copyright (c) 2003-2011 by Roberto Ceccarelli
//
// **********************************************************************

var isNumber = function(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

var checkValue = function(n, bool) {
  if(!isNumber(n)) {
    alert("Non hai inserito un numero valido");
    return false;
  }
  if( n <0 ) {
    alert("Non si possono inserire numeri negativi");
	return false;
  }
  return conferma(n, bool);
}

var conferma = function(n, bool) {
	var qta = n;
	var um = document.getElementById("unmisura").value;
	var temp = document.getElementById("um");
	var um2 = temp.options[temp.selectedIndex].innerHTML;
	//var message = n+" "+um+" "+um2+"TEST";
	var fatt2 = temp.options[temp.selectedIndex].value;
	//if (um.trim() == um2.trim()){
	if (um == um2){
		var message = um+" "+qta+"\n PROCEDO?";
	} else {
		var qta2 = qta*fatt2;
		var message = um2+" "+qta+"  ==>  "+um+" "+qta2+"\n PROCEDO?";
	}
	var r=window.confirm(message);
	showHideDiv(0,"ok1");
	document.getElementById("mess").innerHTML = message;
	showHideDiv(1,"mess");
	showHideDiv(1,"ok2");
	if (bool == 0){
		document.getElementById("qtanew").focus();
		return false;
	} else {
		return true;
	}
	/*if (r==true){
		return true;
	} else {
		document.getElementById("qtanew").focus();
		return false;
	}*/
}

var showHideDiv = function(bool,id) {
	var elm = document.getElementById(id);
	elm.style.display = bool ? "block" : "none";
};

/*
 ?>
 <script type="text/javascript">
//<![CDATA[
var isNumber = function(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

var checkValue = function(n) {
  if(!isNumber(n)) {
    alert("Non hai inserito un numero valido");
    return false;
  }
  if( n <0 ) {
    alert("Non si possono inserire numeri negativi");
	return false;
  }
  return conferma(n);
}

var conferma = function(n) {
	var qta = n;
	var um = new String(document.getElementById("unmisura").value);
	var temp = document.getElementById("um");
	var um2 = new String(temp.options[temp.selectedIndex].innerHTML);
	//var message = n+" "+um+" "+um2+"TEST";
	var fatt2 = temp.options[temp.selectedIndex].value;
	if (um.trim() == um2.trim()){
		var message = um+" "+qta+"\n PROCEDO?";
	} else {
		var qta2 = qta*fatt2;
		var message = um2+" "+qta+"  ==>  "+um+" "+qta2+"\n PROCEDO?";
	}
	var r=window.confirm(message);
	if (r==true){
		return true;
	} else {
		document.getElementById("qtanew").focus();
		return false;
	}
}
//]]>
 <?php
*/