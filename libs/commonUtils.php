<?php

$termid = 0;
if( isset($_COOKIE["tid"]) ) {
	$termid = $_COOKIE["tid"];
} else {
	$termid = date("His") . "0";
	setcookie("tid",$termid,time()+3600*24*365);
}

//Indica se la connessione viene da dispositivo Mobile
function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

//Indica se la connessione viene da FILIALE
function isFiliale()
{
	return CONFIG::$IS_FILIALE;
}

// Calcolo dell'anno corrente
function current_year() {
	return date("Y");
}

// Formatta le date in modo che siano piu' leggibili
function format_date($foxdate) {
	return strftime("%d/%m/%Y", mktime(0,0,0,substr($foxdate,5,2),substr($foxdate,8,2),substr($foxdate,0,4) )) ;
}

// Script per l'impostazione del focus
function setFocus($id) {
	print("<script type='text/javascript'>\n");
	print("//<![CDATA[\n");
	print("document.getElementById('$id').focus();\n");
	print("//]]>\n");
	print("</script>\n");
}

// Script per disabilitare il CR
function disableCR() {
	print("<script type='text/javascript'>\n");
	print("//<![CDATA[\n");
	print("document.onkeypress = stopRKey;\n");
	print("//]]>\n");
	print("</script>\n");
}

// ritorna la url completa della pagina
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

// Link per il ritorno al menu principale
function goMain() {
	print ("<br>\n<a class='menu' href='../menus/main.php'><img noborder src='../../assets/images/b_home.gif'>Menu principale</a>\n");
}

// prende il valore numerico di un parametro controllandone l'esistenza
function getNumeric($param) {
	return isset($_GET[$param]) ? ($_GET[$param] == "" ? 0 : $_GET[$param]) : 0;
}

function getString($param) {
	return isset($_GET[$param]) ? $_GET[$param] : "";
}

// Campi hidden per passaggio parametri
function hiddenField($id, $val) {
	print("<input type='hidden' id='$id' name='$id' value='$val'>\n");
}

//popUp Errori, Warning o Msg generici
function popupMsg($msg, $type){
	if($type == "E"){
		echo "<script type='text/javascript'>alert('FATAL ERROR: $msg  Contattare Amministratore!');  history.go(-1);</script>";
	} else {
		echo "<script type='text/javascript'>alert('WARNING $msg ');</script>";
	}
}

//FUNZIONI LOGIN
function logOut(){
	if(isset($_SESSION['UserPL'])){
		print ("<br>\n<a class=\"menu\" href=\"login.php?logOut=yes\"><img noborder src=\"../img/b_drop.png\">LogOut</a>\n");
	}
}

function checkPermission(){
	if(!isset($_SESSION['UserPL'])){
		Header("Location: login.php?error=1");
	}
}

?>
