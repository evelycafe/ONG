<?php error_reporting(-1);

    ini_set("display_errors", 1); 
	//include("../classeLayout/classeCabecalhoHTML.php");
	//include("cabecalho.php");
	
	include("conexao.php");

	if(!empty($_POST)){

		include("classeControllerBD.php");
		
		$c = new ControllerBD($conexao);
		
		$c->inserir($_POST,$_GET["tabela"]) or die("0");
		echo "1";
	}
	
	/*else{
		header("location: form_pais.php");
	}*/
?>
