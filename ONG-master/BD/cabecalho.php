<?php
    include("verificacao.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="UTF-8">
		<link href="css/styles.css" type="text/css" rel="stylesheet" />
	</head>	
	<body>
		<div class="content">
			<header>
				<h1>ONG</h1>
			</header>
			
<?php
	error_reporting(-1);

    ini_set("display_errors", 1); 
    
    // permissao:
	// 1: root
	// 2: veterinario
	// 3: usr
	//4: Premium
    

	$c = new CabecalhoHTML();
	if(isset($_SESSION["login"]["permissao"])){
		if(($_SESSION["login"]["permissao"]) == 1){
			$v = array(		
					//"cadastro"=>"Cadastrar",	
					"veterinario"=>"Veterinário",
					"animal"=>"Animal",
					"raca"=>"Raça",
					"especie"=>"Espécie",
					"consulta"=>"Consulta",
					"tipo"=>"Tipo de Doação",
					"doacao"=>"Doação",
					"historico_atendimento"=>"Histórico de Atendimento",
					"postagem"=>"Postagem"
					);
		}
		else if($_SESSION["login"]["permissao"] == 2){
			$v = array(			
					"animal"=>"Animal",
					"raca"=>"Raça",
					"especie"=>"Espécie",
					"consulta"=>"Consulta",
					"tipo"=>"Tipo de Doação",
					"doacao"=>"Doação",
					"historico_atendimento"=>"Histórico de Atendimento",
					"postagem"=>"Postagem"
					);
		}
		else if($_SESSION["login"]["permissao"] == 3){	
			$v = array(							
					"postagem"=>"Postagem"
					);
		}
		else{
			$v = array(							
					"postagem"=>"Postagem",
					"historico_animal"=>"Histórico de Atendimento"
					);
		}
	}else{
		$v = array("   "=>"   ");
	}
				
	$c->add_menu($v);
	$c->exibe();


?>
