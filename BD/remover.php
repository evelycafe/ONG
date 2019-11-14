<?php
	include("../classeLayout/classeCabecalhoHTML.php");
	include("cabecalho.php");
	
	include("conexao.php");
	include("classeControllerBD.php");
	$ctrl = New ControllerBD($conexao);
	$ctrl->remover($_POST["id"],$_POST["tabela"]);

?>
<hr />
<a href='lista_<?=$_POST["tabela"];?>.php'>Voltar Para a listagem</a>
</body>
</html>