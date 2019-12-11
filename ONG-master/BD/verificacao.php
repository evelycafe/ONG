<?php
   // echo $_SERVER["PHP_SELF"];   
   // /ONG/BD/listar.php
    
    $v = explode("/", $_SERVER["PHP_SELF"]);
    $p = sizeof($v)-1;
    $arquivo = $v[$p];

    session_start();
	
	if(!isset($_SESSION["login"]["permissao"]) && ($arquivo == "form_cadastro.php")){

	}	
    else if(!isset($_SESSION["login"]["permissao"]) && ($arquivo != "form_login.php")){
        $_SESSION["msg_erro"] = "Você não tem permissão para acessar esta página. Realize o login.";
  		header("location: form_login.php");
    }
?>
