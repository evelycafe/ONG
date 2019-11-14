<?php
	header("Content-type: application/json");

	include("conexao.php");
	include("classeControllerBD.php");

	$c = new ControllerBD($conexao);

	$l = ($_POST["pagina"] - 1) * 5;

	$limite = "LIMIT $l,5";

	$colunas = $_POST["colunas"];

	$tabelas = $_POST["tabelas"];

	$r = $c->selecionar($colunas,$tabelas,null,null,$limite);

	while($linha=$r->fetch(PDO::FETCH_ASSOC)){
		$matriz[] = $linha;
	}

	echo json_encode($matriz);
?>