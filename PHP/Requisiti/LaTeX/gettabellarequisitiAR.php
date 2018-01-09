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
	header('Content-Disposition: attachment; filename="tabellaRequisiti_AR.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$tipi=array('Funzionale','Prestazionale','Qualita','Vincolo');
	$sections=array('Requisiti Funzionali','Requisiti Prestazionali','Requisiti di Qualità','Requisiti di Vincolo');
	$headers=array('Id Requisito','Importanza','Descrizione','Fonti');
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	for($i=0;$i<4;$i++){
        $query="SELECT r1.CodAuto,r1.IdRequisito,r1.Importanza,r1.Descrizione,f.Nome
				FROM (_MapRequisiti h JOIN Requisiti r1 ON 
                h.CodAuto=r1.CodAuto) JOIN Fonti f ON r1.Fonte=f.CodAuto
				WHERE r1.Tipo='$tipi[$i]'
				ORDER BY h.Position";
		$requi=mysql_query($query,$conn) or fail("Query fallita: ".mysql_error($conn));
		$row=mysql_fetch_row($requi);
		if($row[0]!=null){
echo<<<END
\\subsection{{$sections[$i]}}
\\normalsize
\begin{longtabu} to \\textwidth {cc>{\centering}m{7cm}m{3cm}}
\\caption[$sections[$i]]{{$sections[$i]}}
\\label{tabella:req$i}
\\endlastfoot
\\rowfont{\bfseries\sffamily\leavevmode\color{white}}
\\rowcolor{tableHeader}
%\hline 
\\textbf{{$headers[0]}} & \\textbf{{$headers[1]}} & \\textbf{{$headers[2]}} & \\textbf{{$headers[3]}}\\\
%\hline
\\endhead
END;
			//$query_ord="CALL sortForest('UseCase')";
			//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
			requisitiArTex($conn, $row);
			while($row=mysql_fetch_row($requi)){
				requisitiArTex($conn, $row);
			}
echo<<<END


\\end{longtabu}
\\clearpage

END;
		}
	}
}
?>