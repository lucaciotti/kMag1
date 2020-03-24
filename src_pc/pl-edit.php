<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
include("../models/PackingList.php");
// include("odbcSocketLib.php");
checkPermission();

$script="";
head_jquery_pc("Modifiche PL/PB",$script);
disableCR();

$db = getODBCSocket();
$plInfo = new PackingList;
$pl = new PackingList;
$pb = new PackingList;
/*
$conn = new COM("ADODB.Connection");
$conn->Open($connectionstring);
*/
$num = isset($_GET['num']) ? $_GET['num'] : "";
$num = str_pad(trim($num), 6, " ", STR_PAD_LEFT) . "  ";
$anno = isset($_GET['anno']) ? trim($_GET['anno']) : "";
$id_testa = (isset($_GET['id_testa']) ? $_GET['id_testa'] : 0 );
$notify = (isset($_GET['notify']) ? $_GET['notify'] : 0 );

if($anno==''){
	$anno=current_year();
}

if(0 == $id_testa) {
	$result = $plInfo->findByNumAnno((int)$num, (int)$anno, $db);
} else {
	$result = $plInfo->findByIdTesta($id_testa, $db);
}
if($result<0){
	print("<h3>ERRORE NELL'ELABORAZIONE.</h3>");
	print("<p>Errore n°:".$result."</p>");
	print("<p>".$plInfo->getErrorMsg()."</p>");
}

$plInfo->moveFirst();
$id_testa = $plInfo->getField('id_testa');

if(0 == $id_testa) {

	print("<h3>Documento non trovato.</h3>");
	print("<p>".$plInfo->getErrorMsg()."</p>");
} else {
	$artRes = $pl->findByRowPL($id_testa, $db);
	$artOk = $pb->findByRowPB($id_testa, $db);
	
	$pl->orderBy(array('quantitare', 'numeroriga'));
	$pb->orderBy(array('bancale','collo', 'imballo')); 

	$pl->moveFirst();

	print("<h3 class='title'>Strumento modifica Packing List.</h3>");
	printNotify();

	print("<div style='text-align:center;'>PL / PB n: <b>".$plInfo->getField('numerodoc')."</b><br> del <b>".$plInfo->getField('datadoc')."</b></div></br>");

	print("<div style='text-align:center;'></br>");
	print("Filtro: <input id='searchInput' value='Type To Filter'>");
	print("</div></br>");

	print("<table id='maintable2'>\n");
	print("<caption><b>ARTICOLI DA PREPARARE (PL)</b></caption>");
	print("<thead>\n");
		print("<tr>");
		    print("<th>Riga</th>");
		    print("<th>Codice</th>");
		    print("<th>Descrizione</th>");
		    print("<th>U.M.</th>");
		    print("<th>Q.ta</th>");
		    print("<th>Q.ta Res</th>");
		    print("<th>Reparto</th>");
		    print("<th>OC</th>");
		    print("<th>&nbsp;</th>");
	    print("</tr>\n");
    print("</thead>\n");

	$bancale = -1;
	$collo = -1;
	$dettagli = false;
	$entrato=false;
	$entratoBanc=false;
	$pesoBanc=0;
	$pesoBancEff=0;
	$pasoCollo=0;
  	print("<tbody id='fbody2'>\n");
  
    while(!$pl->EOF) {
    	print("<tr>\n");

    	if ((int)$pl->getField('quantita')==0){			//RIGHE DI DESCRIZIONE PACKINGLIST
    		if(!$dettagli){
    			$dettagli =true;
	    		celleVuote(2);
		    	print("<td style='text-align: center;'><b>DETTAGLI</b></td>");
		    	celleVuote(6);
	    		print("</tr>\n");
	    		print("<tr>\n");
	    	}
    		celleVuote(2);
			print("<td style='text-align: center;'>".$pl->getField('descrizion')."</td>");
			celleVuote(6);

    	} else if ($pl->getField('codicearti')!='' ) {

			if($pl->getField('quantitare')>0 && !$entrato){
				$entrato = true;
				print("<td style='text-align: center;'></td>");
	    		print("<td colspan='1'><b>  NON ANCORA PREPARATI </b></td>");
	    		celleVuote(6);
	    		print("</tr>\n");
	    		print("<tr>\n");
			}
			//print("<td style='text-align: center;'><a href='response-pl.php?id=292".$pl->getField(id_riga)."0'>".$pl->getField(numeroriga)."</a></td>");
			print("<td style='text-align: center;'>".$pl->getField('numeroriga')."</td>");
			print("<td style='text-align: center;'><b>".$pl->getField('codicearti')."</b></td>");
			print("<td style='text-align: center;'>".$pl->getField('descrizion')."</td>");
			print("<td style='text-align: center;'>".$pl->getField('unmisura')."</td>");
			print("<td style='text-align: center;'>".$pl->getField('quantita')."</td>");
			print("<td style='text-align: center;'>".$pl->getField('quantitare')."</td>");
			print("<td style='text-align: center;'>".$pl->getField('reparto')."</td>");
			print("<td style='text-align: center;'>OC ".$pl->getField('numord')."</td>");
			print("<td></td>");
    	}
		$id_pl = $pl->getField('id_riga');
        print("</tr>\n");
		$pl->MoveNext();
    }
    print("</tbody>\n</table>\n");
	
	print("<br/>");
	
	$pb->moveFirst();
	print("<table id='maintable'>\n");
	print("<caption><b>ARTICOLI PREPARATI  (PB)</b></caption>");
	print("<thead>\n");
		print("<tr>");
		    print("<th>Riga</th>");
		    print("<th>Codice</th>");
		    print("<th>Descrizione</th>");
		    print("<th>U.M.</th>");
		    print("<th>Q.ta</th>");
		    print("<th>Lotto</th>");
		    print("<th>Collo</th>");
		    print("<th>Bancale</th>");
		    print("<th>Peso Arca</th>");
		    print("<th>Peso Calcolato</th>");
		    print("<th>Reparto</th>");
		    print("<th>&nbsp;</th>");
		    print("<th>&nbsp;</th>");
	    print("</tr>\n");
    print("</thead>\n");

	$bancale = -1;
	$collo = -1;
	$dettagli = false;
	$entrato=false;
	$entratoBanc=false;
	$pesoBanc=0;
	$pesoBancEff=0;
	$pesoCollo=0;
	$totPesoNetto=0;
	$totPesoLordo=0;
	$ncolli=0;
  	print("<tbody id='fbody'>\n");
  
    while(!$pb->EOF) {
    	print("<tr>\n");

    	if ((int)$pb->getField('quantita')==0){			//RIGHE DI DESCRIZIONE PACKINGLIST
    		if(!$dettagli){
    			$dettagli =true;
	    		celleVuote(2);
		    	print("<td style='text-align: center;'><b>DETTAGLI</b></td>");
		    	celleVuote(8);
	    		print("</tr>\n");
	    		print("<tr>\n");
	    	}
    		celleVuote(2);
			print("<td style='text-align: center;'>".$pb->getField('descrizion')."</td>");
			celleVuote(8);

    	} else {

			if ((int)$bancale != (int)$pb->getField('bancale')) {  
				// OLD --> strnatcmp($bancale, $pb->getField(bancale)!=0)){
				$bancale = $pb->getField('bancale');
				if($bancale == 0){
					// NON BANCALATO
		    		print("<td style='text-align: center;'><img src='../img/pallet-icon.gif' alt='Bancale'></td>");
		    		print("<td colspan='1'><b>  NON BANCALATI</b></td>");
		    		celleVuote(11);
		    		print("</tr>\n");
		    		print("<tr>\n");
		    		print("<td style='text-align: center;'>".$pb->getField('numeroriga')."</td>");
					print("<td style='text-align: center;'><b>".$pb->getField('codicearti')."</b></td>");
					print("<td style='text-align: center;'>".$pb->getField('descrizion')."</td>");
					print("<td style='text-align: center;'>".$pb->getField('unmisura')."</td>");
					print("<td style='text-align: center;'>".$pb->getField('quantita')."</td>");
					print("<td style='text-align: center;'>".$pb->getField('lotto')."</td>");
					print("<td style='text-align: center;'>" .$pb->getField('collo')."</td>");
					print("<td style='text-align: center;'>" .$pb->getField('bancale')."</td>");
					print("<td style='text-align: center;'>".$pb->getField('peso')*$pb->getField('quantita')."</td>");
					print("<td style='text-align: center;'> - </td>");
					print("<td style='text-align: center;'>".$pb->getField('reparto')."</td>");
					print("<td></td>");
					if($pb->getField('quantitare')>0) print("<td><a href='pl-delrow.php?id=".$pb->getField('id_riga')."&type=ROW'><img noborder src='../img/delete.png' height=32></a></td>\n");
					$pesoCollo=$pesoCollo+$pb->getField('peso')*$pb->getField('quantita');
					$pesoBancEff=0;
					$entratoBanc = true;
				} else {
					if($entratoBanc){
						// SOLO SE SONO A FINE BANCALE
						celleVuote(5);
						print("<td colspan='3'><b>TOTALE PESO BANCALE: </b></td>");
			    		//celleVuote(1);
			    		print("<td style='text-align: center;'><b>".$pesoBanc."</b></td>");
						print("<td style='text-align: center;'><b>".$pesoBancEff."</b></td>");
						celleVuote(3);
						print("</tr>\n");
		    			print("<tr>\n");
						$totPesoNetto=$totPesoNetto+$pesoBanc;
						$totPesoLordo=$totPesoLordo+$pesoBancEff;
						$pesoBanc=0;
						$pesoCollo=0;
					}
					if($bancale != 0 && $pb->getField('collo') == 0){
						$pesoCollo=0;
						$pesoBanc=0;
						$pesoBanc=$pesoBanc+($pb->getField('peso')*$pb->getField('quantita'));
						$pesoBancEff=$pb->getField('prezzoacq');
						$entratoBanc=true;
						print("<td style='text-align: center;'><img src='../img/pallet-icon.gif' alt='Bancale'></td>");
			    		print("<td colspan='1'><b>  Bancale n: $bancale </br>".$pb->getField('descrizion')." </b></td>");
			    		celleVuote(6);
			    		print("<td style='text-align: center;'>".$pb->getField('peso')*$pb->getField('quantita')."</td>");
						print("<td style='text-align: center;'> - </td>");
						celleVuote(1);
						print("<td><a href='pl-banc.php?num=".trim($plInfo->getField('numerodoc'))."&anno=$anno&gestbanc=Bancali&id_testa=0' target='_blank'><img noborder src='../img/modify.png' height=32></a></td>\n");
						if($pb->getField('quantitare')>0) print("<td><a href='pl-delrow.php?id=".$pb->getField('id_riga')."&type=IMB'><img noborder src='../img/delete.png' height=32></a></td>\n");
					} else {
						$pesoBanc=0;
						$pesoCollo=0;
						$pesoBanc=$pesoBanc+($pb->getField('peso')*$pb->getField('quantita'));
						$pesoBancEff=$pb->getField('prezzoacq');
						$entratoBanc=true;
						print("<td style='text-align: center;'><img src='../img/pallet-icon.gif' alt='Bancale'></td>");
						print("<td style='color: red;' colspan='1'><b>  n: $bancale </br> ATTENZIONE!!!! TIPO BANCALE SCONOSCIUTO O NON SPARATO</b></td>");
						celleVuote(6);
			    		print("<td style='text-align: center;'>".$pb->getField('peso')*$pb->getField('quantita')."</td>");
						print("<td style='text-align: center;'> - </td>");
						celleVuote(1);
						print("<td><a href='" . CONFIG::$BASE_URL . "pl-banc.php?num=".trim($plInfo->getField('numerodoc'))."&anno=$anno&gestbanc=Bancali&id_testa=0' target='_blank'><img noborder src='../img/modify.png' height=32></a></td>\n");
						if($pb->getField('quantitare')>0) print("<td><a href='pl-delrow.php?id=".$pb->getField('id_riga')."&type=IMB'><img noborder src='../img/delete.png' height=32></a></td>\n");
						print("</tr>\n");
						print("<tr>\n");
						$pesoCollo=$pesoCollo+$pb->getField('peso')*$pb->getField('quantita');
						print("<td style='text-align: center;'>".$pb->getField('numeroriga')."</td>");
						print("<td style='text-align: center;'><b>".$pb->getField('codicearti')."</b></td>");
						print("<td style='text-align: center;'>".$pb->getField('descrizion')."</td>");
						print("<td style='text-align: center;'>".$pb->getField('unmisura')."</td>");
						print("<td style='text-align: center;'>".$pb->getField('quantita')."</td>");
						print("<td style='text-align: center;'>".$pb->getField('lotto')."</td>");
						print("<td style='text-align: center;'>" .$pb->getField('collo')."</td>");
						print("<td style='text-align: center;'>" .$pb->getField('bancale')."</td>");
						print("<td style='text-align: center;'>".$pb->getField('peso')*$pb->getField('quantita')."</td>");
						print("<td style='text-align: center;'> - </td>");
						print("<td style='text-align: center;'>".$pb->getField('reparto')."</td>");
						print("<td></td>");
						if($pb->getField('quantitare')>0) print("<td><a href='pl-delrow.php?id=".$pb->getField('id_riga')."&type=ROW'><img noborder src='../img/delete.png' height=32></a></td>\n");
					}
				}
			}
			else
			{
				if($pb->getField('imballo')){
					$pesoCollo=$pesoCollo+$pb->getField('peso')*$pb->getField('quantita');
					$pesoBanc+=$pesoCollo;
					if( $pb->getField('bancale')==0) $pesoBancEff+=$pb->getField('prezzoacq');
					// $totPesoNetto=$totPesoNetto+$pesoCollo;
					// $totPesoLordo=$totPesoLordo+$pb->getField('prezzoacq');
					$collo = $pb->getField('collo');
					celleVuote(1);
					print("<td style='text-align: center;'><img src='../img/box.ico' alt='Bancale' height='44'></td>");
					print("<td colspan='1'>Collo n:<b> $collo -	".$pb->getField('codicearti')."</b></br>".$pb->getField('descrizion')." </td>");
		    		celleVuote(5);
		    		print("<td style='text-align: center;'><b>".$pesoCollo."</b></td>");
					print("<td style='text-align: center;'><b>".$pb->getField('prezzoacq')."</b></td>");
					celleVuote(1);
					print("<td><a href='" . CONFIG::$BASE_URL . "pl-banc.php?num=".$plInfo->getField('numerodoc')."&anno=$anno&btnok=Colli&id_testa=0' target='_blank'><img noborder src='../img/modify.png' height=32></a></td>\n");
					if($pb->getField('quantitare')>0) print("<td><a href='pl-delrow.php?id=".$pb->getField('id_riga')."&type=IMB'><img noborder src='../img/delete.png' height=32></a></td>\n");
					$pesoCollo=0;
				} else {
					$pesoCollo=$pesoCollo+$pb->getField('peso')*$pb->getField('quantita');
		    		print("<td style='text-align: center;'>".$pb->getField('numeroriga')."</td>");
					print("<td style='text-align: center;'><b>".$pb->getField('codicearti')."</b></td>");
					print("<td style='text-align: center;'>".$pb->getField('descrizion')."</td>");
					print("<td style='text-align: center;'>".$pb->getField('unmisura')."</td>");
					print("<td style='text-align: center;'>".$pb->getField('quantita')."</td>");
					print("<td style='text-align: center;'>".$pb->getField('lotto')."</td>");
					print("<td style='text-align: center;'>" .$pb->getField('collo')."</td>");
					print("<td style='text-align: center;'>" .$pb->getField('bancale')."</td>");
					print("<td style='text-align: center;'>".$pb->getField('peso')*$pb->getField('quantita')."</td>");
					print("<td style='text-align: center;'> - </td>");
					print("<td style='text-align: center;'>".$pb->getField('reparto')."</td>");
					print("<td></td>");
					if($pb->getField('quantitare')>0) print("<td><a href='pl-delrow.php?id=".$pb->getField('id_riga')."&type=ROW'><img noborder src='../img/delete.png' height=32></a></td>\n");
				}
			}
		}

        print("</tr>\n");
		$pb->MoveNext();
    }
    if($entratoBanc){
		print("<tr>\n");
		celleVuote(1);
		print("<td colspan='1'><b>TOTALE PESO BANCALE: </b></td>");
		celleVuote(6);
		print("<td style='text-align: center;'><b>".$pesoBanc."</b></td>");
		print("<td style='text-align: center;'><b>".$pesoBancEff."</b></td>");
		celleVuote(3);
		print("</tr>\n");
		$totPesoNetto=$totPesoNetto+$pesoBanc;
		$totPesoLordo=$totPesoLordo+$pesoBancEff;
	}
	print("<tr>\n");
	celleVuote(13);
	print("</tr>\n");
	print("<tr>\n");
	print("<td colspan='2'><b>==> TOTALE PESO PB: </b></td>");
	celleVuote(6);
	print("<td style='text-align: center;'><b>".$totPesoNetto."</b></td>");
	print("<td style='text-align: center;'><b>".$totPesoLordo."</b></td>");
	celleVuote(3);
	print("</tr>\n");
	
    print("</tbody>\n</table>\n");
	
	hiddenField("ncolli",$ncolli);
	
	//PULSANTI DI UTILITA'
	print("<div style='text-align:center;'></br><p style='font-weight:bold; text-align:center; padding:0;'>Utility Packing List</p>");
	print("<button onclick='location.reload(true);'>Aggiorna Pagina</button>");
	print("<br><br>");
	print("<a href='pl-newCollo.php?id=$id_pl&idtesta=$id_testa' target=\"_blank\"><button>Nuovo Collo</button></a>  &nbsp;&nbsp;");
	print("<a href=\"pl-newBanc.php?id=$id_pl&idtesta=$id_testa\" target=\"_blank\"><button>Nuovo Bancale</button></a>");
	print("<br><br>");
	print("<a href='" . CONFIG::$BASE_URL . "reprint-pl-select.php?num=".$plInfo->getField('numerodoc')."&anno=$anno' target=\"_blank\"><button>Ristampa Colli</button></a>  &nbsp;&nbsp;");
	print("<a href='" . CONFIG::$BASE_URL . "reprint-banc-select.php?num=".$plInfo->getField('numerodoc')."&anno=$anno' target=\"_blank\"><button>Ristampa Bancali</button></a>");
	print("<br><br>");
	print("<a href=\"pl-changeDate.php?id_testa=$id_testa\" target=\"_blank\"><button>Cambia Data Evasione</button></a>");
	print("</div></br>");

}

print ("<br><br>\n<a class=\"menu\" href=\"ask_pl_edit.php\">Altra ricerca</a>\n<br>\n");

print("<script type=\"text/javascript\" src=\"../tableFilter.js\"></script>\n");
	
logOut();
goMain();
footer();

function celleVuote($n){
	for($i=0; $i<$n; $i++){
		print("<td></td>"); // style='border: 0px;'
	}
}

function printNotify(){
	global $notify;

	if($notify != 0){
		print("<div style=\"margin: 0 auto 0;\" id=\"avviso\">\n");
		print("<fieldset style=\"width: 50%; margin: 0 auto 0;\"><legend><h3> MODIFICHE APPORTATE </h3></legend>\n");
		if($notify==1)	print("<p>Riga in PL RIPRISTINATA</br>Procedere Normalmente</p></fieldset>\n");
		if($notify==3)	print("<p>Riga in PL RIPRISTINATA</br>Riga in ORDINE sistemata</p></fieldset>\n");
		if($notify==4)	print("<p>Riga dell'Imballo Cancellata</p></fieldset>\n");
		print("</div>\n");
	}
}
?>