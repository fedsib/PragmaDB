<?php

require('../../Functions/get_tex.php');
require('../../Functions/mysql_fun.php');
require('../../Functions/urlLab.php');

session_start();

date_default_timezone_set("Europe/Rome");

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	header('Content-type: application/x-tex');
	header('Content-Disposition: attachment; filename="tabelleTest.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$tipi= ['Validazione','Sistema','Integrazione','Unità'];
	$hook= ['validazione','sistema','integrazione','unita'];
	$sections= ['Test di Validazione','Test di Sistema','Test di Integrazione','Test di Unità'];
	$headers= ['Id Test','Descrizione','Stato'];
	//$query_ord="CALL sortForest('Requisiti')";
    $conn=sql_conn();
	$queries[]="SELECT t.CodAuto, CONCAT('TV',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito
				FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
				WHERE t.Tipo='Validazione'
				ORDER BY h.Position";
	$queries[]="SELECT t.CodAuto, CONCAT('TS',SUBSTRING(r.IdRequisito,2)), t.Descrizione, t.Implementato, t.Eseguito, t.Esito
				FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
				WHERE t.Tipo='Sistema'
				ORDER BY h.Position";
	$queries[]="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito
				FROM Test t
				WHERE t.Tipo='Integrazione'
				ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$queries[]="SELECT t.CodAuto, t.IdTest, t.Descrizione, t.Implementato, t.Eseguito, t.Esito
				FROM Test t
				WHERE t.Tipo='Unita'
				ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	//$ord=mysqli_query($conn, $query_ord) or fail("Query fallita: ".mysqli_error($conn));
	foreach($queries as $ind => $query){
		$test=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		$row=mysqli_fetch_row($test);
		if($row[0]!=null){
echo<<<END
\\subsection{{$sections[$ind]}}
\\input{sezioni/test_{$hook[$ind]}.tex}
\\normalsize
\\begin{longtabu} to \\textwidth {c>{}m{8cm}c}
\\caption[$sections[$ind]]{{$sections[$ind]}}
\\label{tabella:test$ind}
\\endlastfoot
\\rowfont{\bfseries\sffamily\leavevmode\color{white}}
\\rowcolor{tableHeader}
%\hline
\\textbf{{$headers[0]}} & \\textbf{{$headers[1]}} & \\textbf{{$headers[2]}}\\\
%\hline
\\endhead
END;
			testTex($conn, $row);
			while($row=mysqli_fetch_row($test)){
				testTex($conn, $row);
			}
echo<<<END

\\end{longtabu}
\\clearpage


END;
		}
	}
}
?>