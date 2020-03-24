<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");

$dbTest = getODBCSocket();

$test = 1459380;
$test2 = false;
$qta = 4;
/*$Update = "Update Docrig SET quantita = ($qta / 2) Where id = ($test -1) AND omiva = ".(convertBoll($test2))."";
if (!$db->Execute($Update)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die;*/
//$Query = "SELECT * FROM Docrig Where id = ($test -1) AND omiva = ".(convertBoll($test2))."";
//$Query = "SELECT * FROM Ubicaz";
/*$Query = "Select docrig.codicearti, docrig.quantita, docrig.u_costk as collo";
	$Query .= ", docrig.unmisura, docrig.fatt, magart.pesounit ";
	$Query .= " from docrig left join magart on magart.codice = docrig.codicearti ";
	$Query .= " where docrig.id_testa = 236228 AND docrig.u_costk = 1 ";
	$Query .= " UNION Select iif(EMPTY(u_plmod.articolo), docrig.codicearti, u_plmod.articolo) as codicearti, u_plmod.quantita, u_plmod.collo";
	$Query .= ", u_plmod.unmisura, u_plmod.fatt, magart.pesounit ";
	$Query .= " from u_plmod left join docrig on docrig.id = u_plmod.id";
	$Query .= "  left join magart on magart.codice = iif(EMPTY(u_plmod.articolo), docrig.codicearti, u_plmod.articolo)  ";
	$Query .= " where u_plmod.id = 1461569 AND u_plmod.collo = 1"; */
$Query = "select fogliomis from docrig where id=5134126";


print $Query;
//print $Update;
$result = $dbTest->execute($Query);

if(!$result){
	print "<p>There was an error : " . $dbTest ->errorMsg() . "</p>";
} else {

	print "<p>TUTTO OK!!!!!!</p>";
	/*
	print "<table>";
	while (!$db->EOF){
		print "<tr>";
		print "<td>". $db->descrizion ."</td>";
		print "<td>". $db->ubicazione ."</td>";
		print "<td>". $db->listino1 ."</td>";
		print "</tr>";
		$db->moveNext();
	}
	print "</table>";
	*/
	//$db->moveFirst();
	print "<table border=\"1\">";

	while (!$dbTest->EOF){
		print "<tr>";
		//Stampo l'header della tabella
		if($dbTest->getCurrentRow() == 0){
			foreach ($dbTest->getFieldNames() as $fieldname){
				print ("<th> ".$fieldname."</th>");
			}
			print "</tr>";
			print "<tr>";
		}
		//stampo tutte le Rows
		foreach ($dbTest->getFieldNames() as $fieldname){
			print "<td>  ". $dbTest->getField($fieldname) ."  </td>";
		}
		print "</tr>";
		$dbTest->moveNext();
	}
	print "</table>";

}

goMain();
footer();
?>
