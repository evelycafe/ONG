<?php error_reporting(-1);
	session_start();

    ini_set("display_errors", 1);
    
	require_once("conexao.php");
	
	require_once("classeControllerBD.php");
	

	$c = new ControllerBD($conexao);

	$FK = $_POST['FK'];
	$ID_LOGIN = $_POST["ID_LOGIN"];

	$sql = "SELECT COMENTARIO.TEXTO, COMENTARIO.ID_COMENTARIO AS ID_COMENTARIO,
	 COMENTARIO.ID_LOGIN AS ID_LOGIN, COMENTARIO.DATA_COMENTARIO AS DATA, LOGIN.NOME AS LOGIN
FROM COMENTARIO LEFT JOIN LOGIN ON COMENTARIO.ID_LOGIN = LOGIN.ID_LOGIN WHERE COMENTARIO.ID_POSTAGEM = $FK";
	
	$stmt = $conexao->prepare($sql);
			
	$stmt->execute();
	
	$divs = "";

	while($linha=$stmt->fetch(PDO::FETCH_ASSOC)){
		$divs .= "<div class='MeuDiv' style='background-color:orange;'>
				<p>".$linha["DATA"]." ".$linha["LOGIN"].":</p>
	 			<p>".$linha["TEXTO"]."</p>";
	 	if($_SESSION["login"]["id"] == $linha["ID_LOGIN"]){
	 		$divs .= "<button value='".$linha["ID_COMENTARIO"]."' class='remover_comentario'>Remover</button>";
	 		$divs .= "<button value='".$linha["ID_COMENTARIO"]."' class='alterar_comentario'>Alterar</button>";
	 		$divs .= "<div id='id_post".$linha["ID_COMENTARIO"]."' value='".$FK."'></div>";
	 		
	 	}
			 $divs .= "</div>";	
	}
   echo $divs;
?>
