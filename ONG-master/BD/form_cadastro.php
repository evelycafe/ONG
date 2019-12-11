<?php
	require_once("../classeLayout/classeCabecalhoHTML.php");
	require_once("cabecalho.php");
	
	require_once("../classeForm/classeInput.php");
	require_once("../classeForm/classeForm.php");
	require_once("../classeForm/classeButton.php");
	require_once("../classeForm/classeSelect.php");
	require_once("../classeForm/classeOption.php");
	require_once("classeControllerBD.php");
	require_once("conexao.php");
		
		
	if(isset($_POST["id"])){
		
		$c = new ControllerBD($conexao);
		$colunas = array("ID_LOGIN", "NOME", "SENHA", "ENDERECO", "TELEFONE", "EMAIL");
		$tabelas[0][0] = "login";
		$tabelas[0][1] = null;
		$ordenacao = null;
		$condicao = $_POST["id"];
		$stmt = $c->selecionar($colunas, $tabelas, $ordenacao, $condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);
		$value_id_LOGIN = $linha["ID_LOGIN"];
        $value_NOME = $linha["NOME"];
        $senha = $linha["SENHA"];
        $endereco = $linha["ENDERECO"];
        $telefone = $linha["TELEFONE"];
        $email = $linha["EMAIL"];
        $action = "altera.php?tabela=LOGIN";
        $disabled = true;
	}
	else{
        $disabled = false;
		$action = "insere.php?tabela=LOGIN";
		$value_id_LOGIN = null;
		$value_NOME = null;
		$senha = null;
		$endereco = null;
		$telefone = null;
		$email = null;
	}
	
	/////////////////////////////////////////////////////////////////////////
	
    $select = "SELECT ID_PERMISSAO AS value, NOME AS texto FROM PERMISSAO ORDER BY ID_PERMISSAO";
	
	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	while($linha=$stmt->fetch()){
		$permissao[] = $linha;
	}	
	
    /////////////////////		/////////////////////////		/////////////////
	$v = array("action"=>$action,"method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"number","name"=>"ID_LOGIN","placeholder"=>"ID DO USUARIO...", "value"=>$value_id_LOGIN);
    $f->add_input($v);
    
    if($disabled){
        $v = array("type"=>"hidden", "name"=>"ID_LOGIN", "value"=>$value_id_LOGIN);
        $f->add_Input($v);
    }
	
	$v = array("type"=>"text","name"=>"NOME","placeholder"=>"NOME DE USUARIO...", "value"=>$value_NOME);
    $f->add_input($v);
    $v = array("type"=>"text","name"=>"SENHA","placeholder"=>"SENHA...","value"=>$senha);
    $f->add_input($v);
	
    $v = array("type"=>"text","name"=>"ENDERECO","placeholder"=>"ENDEREÇO...","value"=>$endereco);
    $f->add_input($v);
	
    $v = array("type"=>"NUMBER","name"=>"TELEFONE","placeholder"=>"TELEFONE...","value"=>$telefone);
    $f->add_input($v);
	
    $v = array("type"=>"EMAIL","name"=>"EMAIL","placeholder"=>"EMAIL...","value"=>$email);
	$f->add_input($v);
	
	if(isset($_SESSION["login"]["permissao"]) ){
		if($_SESSION["login"]["permissao"] == 1){
			$v = array("name"=>"PERMISSAO","selected"=>$selected_permissao);
			$f->add_select($v,$permissao );
    	}
	}
	else{
		$v = array("type"=>"hidden","name"=>"PERMISSAO","value"=>'3');
		$f->add_input($v);
	}
	
	
	$v = array("type"=>"button","class"=>"cadastrar","texto"=>"CADASTRAR");
	$f->add_button($v);	
?>
<!DOCTYPE html>

<h3>Formulário - Inserir LOGIN</h3>
<div id="status"></div>

<hr />
<?php
	$f->exibe();
?>
<script>
	/////////////////////  codigo ja existk
	<?php 
		if(isset($_SESSION["login"]["permissao"]) && ($_SESSION["login"]["permissao"] == 1)){
			echo "tipo='select';";		
		}
		else{
			echo "tipo='input';";
		}
	?>
	//quando o documento estiver pronto...
	$(function(){
		
			
		//defina a seguinte regra para o botao de envio
		$(document).on("click",".cadastrar",function(){
		
		$.ajax({
			url: "insere.php?tabela=LOGIN",
			type: "post",
			data: {
					ID_LOGIN: $("input[name='ID_LOGIN']").val(),
					NOME: $("input[name='NOME']").val(),
					SENHA: $("input[name='SENHA']").val(),
					ENDERECO: $("input[name='ENDERECO']").val(),
					TELEFONE: $("input[name='TELEFONE']").val(),
					EMAIL: $("input[name='EMAIL']").val(),
					ID_PERMISSAO: $(tipo+"[name='PERMISSAO']").val()
				 },
			beforeSend:function(){
				$("button").attr("disabled",true);
			},
			success: function(d){
				$("button").attr("disabled",false);
				if(d=='1'){
					$("#status").html("LOGIN inserido com sucesso! <a href='form_login.php'>Logar</a>");
					$("#status").css("color","green");
					
				}
				else{
					console.log(d);
					$("#status").html("LOGIN Não inserido! Código já existe!");
					$("#status").css("color","red");
				}
			}
		});
	});
});
</script>
</body>
</html>
