<?php

	include("conexao.php");
	include("classeControllerBD.php");
	
	$c = new ControllerBD($conexao);
	
	$colunas = array("COUNT(*) as qtd");
	$t[0][0] = $_POST["tabela"];
	$t[0][1] = null;
	$r = $c->selecionar($colunas,$t,null,null,null);
	
	$linha = $r->fetch(PDO::FETCH_ASSOC);
	
	$b = (int) ($linha["qtd"]/5);
	
	if($linha["qtd"]%5!=0){
		$b++;
	}
	
	echo $b;
?>