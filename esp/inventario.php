<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Lettura inventario");
disableCR();

print("<script type=\"text/javascript\" src=\"../js/json2.js\"></script>\n");
print("<script type=\"text/javascript\" src=\"../js/inventario-utils.js\"></script>\n");

$labelStyle = "style=\"display: block; float: left; width: 80px;\"";
print("<form name=\"input\" action=\"inv_setqta.php\" method=\"get\" onsubmit=\"return validateForm();\">\n");
print("<label for=\"id\" $labelStyle>Cartellino:</label>\n");
print("<input type=\"text\" id=\"id\" name=\"id\" onchange=\"getCartellino(this);\"/>\n");
setFocus("id");

print("<img id='semaforo' style='display: none;' noborder src='../img/stop.gif' height=20 >");

print("<br>\n<label for=\"art\" $labelStyle >Articolo:</label>\n");
print("<input type=\"text\" id=\"art\" name=\"art\" onchange=\"decode(this);\">\n");

print("<img id='flag' style='display: none;' noborder src='../img/redFlag.gif' height=20 >");

print("<br>\n<label for=\"lotto\" $labelStyle >Lotto:</label>\n");
print("<input type=\"text\" id=\"lotto\" name=\"lotto\" disabled=\"disabled\">\n<br>\n");

print("<label for=\"qta\" $labelStyle >\n");
print("<select name=\"um\" id=\"um\"></select>\n");
print("</label>\n");
print("<input type=\"text\" id=\"qta\" name=\"qta\" onchange=\"checkValue(this.value);\">\n<br>\n");

print("<label for=\"ubicaz\" $labelStyle >Ubicazione:</label>\n");
print("<input type=\"text\" id=\"ubicaz\" name=\"ubicaz\" onchange=\"\">\n<br>\n");

print("<label for=\"maga\" $labelStyle >Magazzino:</label>\n");
print("<input type=\"text\" id=\"maga\" disabled=\"disabled\">\n<br>\n");
print("<br>\n");

print("<input type=\"hidden\" name=\"unmisura\" id=\"unmisura\">\n");
print("<input type=\"submit\" id=\"Ok\" value=\"Ok\">\n");
print("</form>\n");

print("<br>");
print("<button id='reload' style='display: none;' onclick='reloadPage()'>Ricarica Pagina</button>\n");
print("<button id='segnala' style='display: none;' onclick='segnalaArt()'>Segnala Errato!</button>\n");
print("<button style='display: block;' onclick='riepilogoArt()'>Riepilogo Articolo</button>\n");
print("<br>");

print("<div id='detail' style='display: none;'>");
print("</div>");

goMain();
footer();
?>