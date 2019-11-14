<?php
	include("../classeLayout/classeCabecalhoHTML.php");
	include("cabecalho.php");
	
	require_once("../classeForm/InterfaceExibicao.php");
	require_once("../classeForm/classeInput.php");
	require_once("../classeForm/classeForm.php");
	require_once("../classeForm/classeButton.php");

	if(isset($_POST["id"])){
		require_once("classeControllerBD.php");
		require_once("conexao.php");

		$c = new ControllerBD($conexao);
		$colunas = array("ID_ESPECIE", "NOME");
		$tabelas[0][0] = null;
		$tabelas[0][1] = null;
		$ordenacao = null;
		$condicao = $_POST["id"];

		$stmt = $c->selecionar($colunas, $tabelas, $ordenacao, $condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);

		$value_especie = $linha["ID_ESPECIE"];
        $value_nome = $linha["NOME"];
        $action = "alterar.php?tabela=ESPECIE";
        $disabled = true;
	}
	else{
        $disabled = false;
		$action = "insere.php?tabela=ESPECIE";
		$value_especie = null;
		$value_nome = null;
	}


	$v = array("action"=>$action,"method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"number","name"=>"ID_LOGIN","placeholder"=>"ID DA ESPÉCIE...", "value"=>$value_especie);
    $f->add_input($v);
    
    if($disabled){
        $v = array("type"=>"hidden", "name"=>"ID_LOGIN", "value"=>$value_especie);
        $f->add_Input($v);
    }
	
	$v = array("type"=>"text","name"=>"NOME","placeholder"=>"NOME DA ESPÉCIE...", "value"=>$value_nome);
    $f->add_input($v);

	$v = array("type"=>"button", "texto"=>"ENVIAR");
	$f->add_button($v);	
?>
<!DOCTYPE html>

<h3>Formulário - Inserir Espécie</h3>
<div id="status"></div>

<hr />
<?php
	$f->exibe();

?>
<script>
// quando o documento estiver pronto
	//$(document).ready(function(){ OOOuuu
	$(function(){
		//defina a seginte regra para o botao
		$("button").click(function(){
			$.ajax({
				url: "insere.php?tabela=ESPECIE",
				type: "post",
				data: {
						// dentro desse arquivo existe um input com name='ID_REGIAO'
						ID_ESPECIE: $("input[name='ID_ESPECIE']").val(),
						NOME: $("input[name='NOME']").val(),
					},
				beforeSend: function(){
					$("button").attr("disabled", true)
				},
				success: function(d){
					$("button").attr("disabled", false)
					if(d == '1'){
						$("#status").html("Espécie Inserida com sucesso!");
						$("#status").css("color", "green");
					}
					else{
						console.log(d);
						$("#status").html("Espécie não Inserida. Código já existe!");
						$("#status").css("color", "red");
					}
				}
			});
		});
	});
</script>
</body>
</html>