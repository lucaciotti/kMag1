<?php
$pwderror = "";
$pwderrornumber = $_GET["error"];

include("header.php");
head_jquery_pc("Login",'');

if ($_GET['logOut'] == 'yes') {
	session_start();
	session_unset($_SESSION['UserPL']);
	session_destroy();
	Header("Location: login.php?error=3");
}

if(!empty($pwderrornumber)) {
	if($pwderrornumber == 1) {
		$pwderror = "UTENTE NON ABILITATO";
	}
	if($pwderrornumber == 2) {
		$pwderror = "PASSWORD ERRATA";
	}
	if($pwderrornumber == 3) {
		$pwderror = "LOGUT EFFETTUATO";
	}
}
print("<h3 class='title'>Login</h3>");

$labelStyle = "style=\"background-color: #CCFFFF; float: left; text-align: right; width: 150px;\"";
echo "<div style=\"text-align: center;\">\n";
echo "<div style=\"width: 320px; display: block; margin-left: auto; margin-right: auto;\">\n";
echo "<form action='enter.php' method='POST'>\n" ;
echo "<label for=\"codice\" $labelStyle>User:</label>\n";
echo "<input name=\"codice\" id=\"codice\" type=\"text\" size=\"15\">\n";
echo "<br>\n";
echo "<label for=\"password\" $labelStyle>Password:</label>\n";
echo "<input name=\"password\" id=\"password\" type=\"password\" size=\"15\">\n";
if(!empty($pwderror))
{
	echo "<br>\n";
	echo "<p style=\"background-color: #ff6633; display: block; text-align: center;\"><b>$pwderror</b></p>";
}
echo "<br>\n";
echo "<input type=\"submit\" value=\"Entra\">\n";
echo "</form>\n" ;
echo "</div>\n</div>\n";

logOut();
goMainPc();
footer();
?>