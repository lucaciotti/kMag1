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
head_jquery_pc("Nuovo Bancale PL/PB",$script);
disableCR();

$db = getODBCSocket();

$id = (isset($_GET['id']) ? $_GET['id'] : 0 );
$id_pl = (isset($_GET['idtesta']) ? $_GET['idtesta'] : 0 );

$banc = getNewBanc($id_pl);

print("<h3 class='title'>Strumento Aggiunta Nuovo Bancale Packing List.</h3>");
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
	
	function setMisuraBanc (oArt) {
		document.getElementById("pal_u_misural").value = oArt.xml.getElementsByTagName("u_misural")[0].firstChild.nodeValue;
		document.getElementById("pal_u_misuras").value = oArt.xml.getElementsByTagName("u_misuras")[0].firstChild.nodeValue;
	};

	function decodeBanc (obj) {
		if ("" == obj.value) {
			return;
		}
		var oArt = checkCodiceArtix(obj.value);
		setMisuraBanc(oArt);
	};

	function checkNewBanc () {
		var obj = document.getElementById("codbanc");
		if ("" == obj.value) {
			alert("Specificare l'imballo");
			obj.focus();
			return false;
		}
		if (document.getElementById("bancnum").value == 0 ||
			document.getElementById("bancnum").value == "" ) {

			alert("Inserire numero Bancale");
			document.getElementById("bancnum").focus();
			return false;
		}
		if (document.getElementById("rep").value == "" ) {
			alert("Inserire Reparto");
			document.getElementById("rep").focus();
			return false;
		}
		if (document.getElementById("pal_u_misural").value == 0 ||
			document.getElementById("altezza").value == 0 ||
			document.getElementById("pal_u_misuras").value == 0) {

			alert("Una o piu' misure imballo mancanti");
			document.getElementById("pal_u_misural").focus();
			return false;
		}
		if (document.getElementById("pesobanc").value == 0) {

			alert("Peso collo mancante");
			document.getElementById("pesobanc").focus();
			return false;
		}
		return true;
	};
</script>

<div style="text-align:center;">
	<form name="plimb" method="get" action="pl-apdImb.php" onsubmit="return checkNewBanc();">	
		<?php
			hiddenField("id",$id);
			hiddenField("id_pl",$id_pl);
		?>
		<label for="bancnum"> Bancale </label>		
		<input type="text" id="bancnum" name="bancnum" size="2" value="<?php print($banc); ?>">
			
		<br><br>
		
		<label for="codbanc"> Tipo Bancale </label>
		<select id="codbanc" name="codbanc" onchange='decodeBanc(this);'>
			<option value=""> - Scegli bancale - </option>
			<?php
				$Query = "select magart.codice, magart.descrizion from magart where magart.codice in (select u_pallet.codice from u_pallet)";
				if (!$db->Execute($Query)) {
					print($db->errorMsg() . "$Query<br>");
					return -1;
				}
				if (!isset($current)) {
					$current="";
				}
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
				if (!isset($current)) {
					$current="";
				}
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
		
		<label> Dimensioni </label>
		<input style="width: 40px" type="text" size="4" name="pal_u_misural" id="pal_u_misural">&nbsp;X 
		<input style="width: 40px" type="text" size="4" name="pal_u_misuras" id="pal_u_misuras">&nbsp;X
		<input style="width: 40px" type="text" size="4" name="altezza" id="altezza"> in mm
		</br> (Largh. x Profondità x (H)Altezza)
		
		
		<br><br>
	
		<label> Peso bancale in kg </legend>
		<input type="text" size="4" name="pesobanc" id="pesobanc">
		
		
		<br><br><br>

		<input style="float: center" type="submit" value="Procedi">
	</form>
</div>

<br><br>

<?php
setFocus("bancnum");
	
logOut();
goMain();
footer();

function getNewBanc($id_pl) {
	global $db;
	$Query = "select max(bancale) as lastcollo from u_bancpl where id_pl = $id_pl group by id_pl";
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