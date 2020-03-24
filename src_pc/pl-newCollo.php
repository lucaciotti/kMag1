<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
include("odbcSocketLib.php");
checkPermission();

$script = isset($script) ? $script : "";
head_jquery_pc("Nuovo Collo PL/PB",$script);
disableCR();

$db = getODBCSocket();

$id = (isset($_GET['id']) ? $_GET['id'] : 0 );
$id_pl = (isset($_GET['idtesta']) ? $_GET['idtesta'] : 0 );

$collo = getNewCollo($id_pl);

print("<h3 class='title'>Strumento Aggiunta Nuovo Collo Packing List.</h3>");
?>

<script type="text/javascript">
	function noCache() {
		var milliseconds = (new Date()).getTime();
		return "&x=" + milliseconds;
	};
	
	function checkCodiceArtix (cCodice) {
		var url = "getcodiceartix.php?cod=" + encodeURIComponent(cCodice) + noCache();
		makeHttpXml();
		httpXml.open("GET", url, false);
		httpXml.send(null);
		var xRet = httpXml.responseXML;
		var oRet = new Object();
		oRet.codice = xRet.getElementsByTagName("codice")[0].firstChild.nodeValue;
		if ("*error*" == oRet.codice) {
			alert("Codice non riconosciuto");
			oRet.codice = "";
			oRet.lottoob = 0;
			oRet.isImballo = 0;
		} else {
			oRet.lottoob = xRet.getElementsByTagName("lottoob")[0].firstChild.nodeValue;
			oRet.xml = xRet;
			oRet.isImballo = xRet.getElementsByTagName("imballo")[0].firstChild.nodeValue;
		}
		return oRet;
	};
	
	function setMisuraImb (oArt) {
		document.getElementById("imb_u_misural").value = oArt.xml.getElementsByTagName("u_misural")[0].firstChild.nodeValue;
		document.getElementById("imb_u_misurah").value = oArt.xml.getElementsByTagName("u_misurah")[0].firstChild.nodeValue;
		document.getElementById("imb_u_misuras").value = oArt.xml.getElementsByTagName("u_misuras")[0].firstChild.nodeValue;
	};

	function decodeImb (obj) {
		if ("" == obj.value) {
			return;
		}
		var oArt = checkCodiceArtix(obj.value);
		setMisuraImb(oArt);
		
		var peso = getPeso("collo", document.getElementById("id").value, document.getElementById("collo").value );
		document.getElementById("pesocollo").value = peso;
	};
	
	function getPeso (mode, id_riga, item) {
		var url = "getpeso.php?mode=" + mode + "&id_riga=" + id_riga + "&item=" + item + noCache();
		makeHttpXml();
		httpXml.open("GET", url, false);
		httpXml.send(null);
		return httpXml.responseText;
	}

	function checkNewImb () {
		var obj = document.getElementById("art");
		if ("" == obj.value) {
			alert("Specificare l'imballo");
			obj.focus();
			return false;
		}
		if (document.getElementById("collo").value == 0 ||
			document.getElementById("collo").value == "" ) {

			alert("Inserire numero Collo");
			document.getElementById("collo").focus();
			return false;
		}
		if (document.getElementById("rep").value == "" ) {
			alert("Inserire Repart");
			document.getElementById("rep").focus();
			return false;
		}
		if (document.getElementById("imb_u_misural").value == 0 ||
			document.getElementById("imb_u_misurah").value == 0 ||
			document.getElementById("imb_u_misuras").value == 0) {

			alert("Una o piu' misure imballo mancanti");
			document.getElementById("imb_u_misural").focus();
			return false;
		}
		if (document.getElementById("pesocollo").value == 0) {

			alert("Peso collo mancante");
			document.getElementById("pesocollo").focus();
			return false;
		}
		return true;
	};
</script>

<div style="text-align:center;">
	<form name="plimb" method="get" action="pl-apdImb.php" onsubmit="return checkNewImb();">	
		<?php
			hiddenField("id",$id);
			hiddenField("id_pl",$id_pl);
		?>
		<label>Numero del Collo</label>
			<input type="text" size="6" name="collo" id="collo" value='<?php print($collo); ?>'> 
				
		<br><br>
		
		<label for="art">Imballo</label>
		<select id="art" name="art" onchange="decodeImb(this);">
			<option value=""> - Scegli Imballo - </option>
			<?php
				$Query = "select codice, descrizion from magart where magart.danger AND magart.codice not in (select u_pallet.codice from u_pallet where codice!='#KZ-SCG(009)') order by codice";
				if (!$db->Execute($Query)) {
					print($db->errorMsg() . "$Query<br>");
					return -1;
				}
				$current = isset($current) ? $current : "";
				while(!$db->EOF) {
					$cod= trim($db->getField('codice'));
					print("<option value=\"$cod\"");
					if ($cod == $current) {
						print(" selected=\"selected\""); 
					}
					print(">$cod - " . trim($db->getField('descrizion')) . "</option>\n");
					$db->MoveNext();
				}
			?>	
		</select>
		
		<br>	<br>	
		
		<label for="rep">Reparto</label>
		<select id="rep" name="rep">
			<option value=""> - Scegli Reparto - </option>
			<?php
				$Query = "select * from u_reparti order by codice";
				if (!$db->Execute($Query)) {
					print($db->errorMsg() . "$Query<br>");
					return -1;
				}
				$current = isset($current) ? $current : "";
				while(!$db->EOF) {
					$cod= trim($db->getField('codice'));
					print("<option value=\"$cod\"");
					if ($cod == $current) {
						print(" selected=\"selected\""); 
					}
					print(">$cod - " . trim($db->getField('descrizion')) . "</option>\n");
					$db->MoveNext();
				}
			?>	
		</select>
		
		<br><br>
		
		<label>Dimensioni</label><br>	
			<input style="width: 40px" type="text" size="4" name="imb_u_misural" id="imb_u_misural" >&nbsp;X 
			<input style="width: 40px" type="text" size="4" name="imb_u_misuras" id="imb_u_misuras" >&nbsp;X 
			<input style="width: 40px" type="text" size="4" name="imb_u_misurah" id="imb_u_misurah" >&nbsp;in mm 
			</br>
			Largh. x Profondita' x (H)Altezza
		
		<br>	<br>	
		
		<label>Peso collo in kg</label>
			<input type="text" size="6" name="pesocollo" id="pesocollo" value='0'> 
		
		<br><br><br>

		<input style="float: center" type="submit" value="Procedi">
	</form>
</div>

<br><br>

<?php


setFocus("collo");
	
logOut();
goMain();
footer();

function getNewCollo($id_pl) {
	global $db;
	$Query = "select max(collo) as lastcollo from u_termpl where id_pl = $id_pl group by id_pl";
	if (!$db->Execute($Query)) {
		print($db->errorMsg() . "$Query<br>");
		return -1;
	}
	if (!$db->EOF) {
		$tlast = $db->getField('lastcollo');
		$last = (integer)$tlast;
		$last++;
		return $last;
	} else {
		return 1;
	}
}
?>