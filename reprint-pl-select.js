// ***********************************************************************
// Project ArcaWeb
// ===========================
//
// Copyright (c) 2003-2014 by Roberto Ceccarelli
//
// **********************************************************************

var toggleAll = function(status) {
	$("INPUT[type='checkbox'].linked").each( function() {
		$(this).prop("checked",status);
	})
};

var toggleClass = function(status, group) {
	$("INPUT[type='checkbox']."+group).each( function() {
		$(this).prop("checked",status);
	})
};

var doPrint = function() {
	var idTesta = $("#id_testa").prop("value");
	var prt = $("#prt").prop("value");
	var peso = ($("#label_peso").prop("checked") ? 1 : 0);
	var url = "pl_07_print.php?id="+idTesta+"&prt="+prt+"&warnpeso="+peso+"&prtbanc=0&collo=";
	$("INPUT[type='checkbox']:checked.collo").each( function() {
		var collo = $(this).prop("value");
		$.get(url+collo);
	})
	alert("Stampe inviate");
};

var doPrintBanc = function() {
	var idTesta = $("#id_testa").prop("value");
	var prt = $("#prt").prop("value");
	var url = "pl_07_print.php?id="+idTesta+"&prt="+prt+"&prtbanc=1&collo=0&banc=";
	$("INPUT[type='checkbox']:checked.collo").each( function() {
		var collo = $(this).prop("value");
		$.get(url+collo);
	})
	alert("Stampe inviate");
};


var createCookie = function(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
};

var setPrinter = function() {
	createCookie("plprinter", document.getElementById("prt").value, 10);
	return true;
};
