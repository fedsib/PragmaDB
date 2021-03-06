<?php

require('../Functions/mysql_fun.php');
require('../Functions/page_builder.php');
require('../Functions/urlLab.php');

session_start();

date_default_timezone_set("Europe/Rome");

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	$id=$_GET['id'];
    $conn=sql_conn();
	$id=mysqli_escape_string($conn, $id);
	$query="SELECT a.CodAuto, a.Nome, a.Descrizione, a.Time
			FROM Attori a
			WHERE a.CodAuto='$id'";
	$att=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	$row=mysqli_fetch_row($att);
	if($row[0]==$id){
		$title="Dettaglio Attore - $row[1]";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Dettaglio - $row[1]</h2>
				<dl class="widget">
					<dt class="widget-title">Nome:</dt>
					<dd>$row[1]</dd>
					<dt class="widget-title">Descrizione:</dt>
END;
		if($row[2]!=null){
echo<<<END

					<dd>$row[2]</td>
END;
		}
		else{
echo<<<END

					<dd>N/D</td>
END;
		}
		//$query_ord="CALL sortForest('UseCase')";
		$query="SELECT u.CodAuto, u.IdUC, u.Nome
				FROM AttoriUC auc JOIN (_MapUseCase h JOIN UseCase u ON h.CodAuto=u.CodAuto) ON auc.UC=u.CodAuto
				WHERE auc.Attore='$id'
				ORDER BY h.Position";
		//$ord=mysqli_query($conn,$query_ord) or fail("Query fallita: ".mysqli_error($conn));
		$uc=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		$row = mysqli_fetch_row($uc);
		if($row[0]!=null){
echo<<<END

					<dt class="widget-title">Use Case:</dt>
					<dd><a class="link-color-pers" href="$absurl/UseCase/dettagliousecase.php?id=$row[0]">$row[1] - $row[2]</a></dd>
END;
		}
		while($row = mysqli_fetch_row($uc)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/UseCase/dettagliousecase.php?id=$row[0]">$row[1] - $row[2]</a></dd>
END;
		}
echo<<<END

				</dl>
END;
	}
	else{
		$title="Dettaglio Attore - Attore Non Trovato";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>L'attore con id "$id" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>