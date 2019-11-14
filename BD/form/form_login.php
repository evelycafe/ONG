<?php
	require_once("../classeLayout/classeCabecalhoHTML.php");
	require_once("cabecalho.php");
	
	require_once("../classeForm/classeInput.php");
	require_once("../classeForm/classeForm.php");
	require_once("../classeForm/classeButton.php");

	if(isset($_POST["id"])){
		require_once("classeControllerBD.php");
		require_once("conexao.php");

		$c = new ControllerBD($conexao);
		$colunas = array("ID_LOGIN", "NOME", "SENHA", "ENDERECO", "TELEFONE", "EMAIL");
		$tabelas[0][0] = null;
		$tabelas[0][1] = null;
		$ordenacao = null;
		$condicao = $_POST["id"];

		$stmt = $c->selecionar($colunas, $tabelas, $ordenacao, $condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);

		$value_id_login = $linha["ID_LOGIN"];
        $value_nome_login = $linha["NOME"];
        $senha = $linha["SENHA"];
        $endereco = $linha["ENDERECO"];
        $telefone = $linha["TELEFONE"];
        $email = $linha["EMAIL"];
        $action = "alterar.php?tabela=LOGIN";
        $disabled = true;
	}
	else{
        $disabled = false;
		$action = "insere.php?tabela=LOGIN";
		$value_id_login = null;
		$value_nome_login = null;
		$senha = null;
		$endereco = null;
		$telefone = null;
		$email = null;
	}


	$v = array("action"=>$action,"method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"number","name"=>"ID_LOGIN","placeholder"=>"ID DO USUARIO...", "value"=>$value_id_login);
    $f->add_input($v);
    
    if($disabled){
        $v = array("type"=>"hidden", "name"=>"ID_LOGIN", "value"=>$value_id_login);
        $f->add_Input($v);
    }
	
	$v = array("type"=>"text","name"=>"NOME","placeholder"=>"NOME DE USUARIO...", "value"=>$value_nome_login);
    $f->add_input($v);

    $v = array("type"=>"text","name"=>"SENHA","placeholder"=>"SENHA...","value"=>$senha);
    $f->add_input($v);
	
    $v = array("type"=>"text","name"=>"ENDERECO","placeholder"=>"ENDEREÇO...","value"=>$endereco);
    $f->add_input($v);
	
    $v = array("type"=>"NUMBER","name"=>"TELEFONE","placeholder"=>"TELEFONE...","value"=>$telefone);
    $f->add_input($v);
	
    $v = array("type"=>"EMAIL","name"=>"EMAIL","placeholder"=>"EMAIL...","value"=>$email);
	$f->add_input($v);
	
	
	$v = array("type"=>"button", "texto"=>"ENVIAR");
	$f->add_button($v);	
?>
<!DOCTYPE html>

<h3>Formulário - Inserir Login</h3>
<div id="status"></div>

<hr />
<?php
	$f->exibe();

?>
<script>
pagina_atual = 0;
	//quando o documento estiver pronto...
	$(function(){
		
		carrega_botoes();
		
		function carrega_botoes(){
			
			$.ajax({
				url: "quantidade_botoes.php",
				type: "post",
				data: {tabela: "LOGIN"},
				success: function(q){
					console.log(q);
					$("#botoes").html("");
					for(i=1;i<=q;i++){
						botao = " <button type='button' class='pg'>" + i + "</button>";
						$("#botoes").append(botao);
					}
				}
			});
		}

		$(document).on("click", ".remover", function(){
			id_remover = $(this).val();

			$.ajax({
				url: "remover.php",
				type: "post",
				data: {
					id: id_remover,
					tabela: "LOGIN"
				},
				success: function(d){
					if(d == 1){
						$("#status").html("Removido com sucesso");
						carrega_botoes();
						qtd = $("tbody tr").length;
						if(qtd == "1"){
							pagina_atual--;
						}
						paginacao(pagina_atual);
					}
				}
			});
		});
		
		$(document).on("click",".pg",function(){
			valor_botao = $(this).html();
			pagina_atual = valor_botao;
			paginacao(valor_botao);
		});
		
		function paginacao(b){
			$.ajax({
				url: "carrega_dados.php",
				type: "post",
				data: {
						tabelas:{
									0:{0:"LOGIN",1:null}
								},
						colunas:{0:"ID_LOGIN",1:"NOME_LOGIN"}, 
						pagina: b
					  },
				success: function(matriz){
					$("tbody").html("");
					for(i=0;i<matriz.length;i++){
						tr = "<tr>";
						tr += "<td>"+matriz[i].id_LOGIN+"</td>";
						tr += "<td>"+matriz[i].nome_LOGIN+"</td>";
						tr += "<td><button value='"+matriz[i].id_LOGIN+"' class='remover'>Remover</button>";
						tr += "<button value='"+matriz[i].id_LOGIN+"' class='alterar'>Alterar</button></td>";
						tr += "</tr>";	
						$("tbody").append(tr);
					}
				}
			});
		}
		
		$(document).on("click",".alterar",function(){
		//$(".alterar").click(function(){ 
			id_alterar = $(this).val();			
			$.ajax({
				url: "get_dados_form.php",
				type: "post",
				data: {id: id_alterar, tabela: "LOGIN"},
				success: function(dados){
					$("input[name='ID_LOGIN']").val(dados.ID_LOGIN);
					$("input[name='NOME_LOGIN']").val(dados.NOME_LOGIN);
					$(".cadastrar").attr("class","alterando");
					$(".alterando").html("ALTERAR");
				}
			});
		});
			
			$(document).on("click",".alterando",function(){
				
				$.ajax({
					url:"altera.php?tabela=LOGIN",
					type: "post",
					data: {
						ID_LOGIN: $("input[name='ID_LOGIN']").val(),
						NOME_LOGIN: $("input[name='NOME_LOGIN']").val()
					 },
					beforeSend:function(){
						$("button").attr("disabled",true);
					},
					success: function(d){
						$("button").attr("disabled",false);
						if(d=='1'){
							$("#status").html("Região Alterada com sucesso!");
							$("#status").css("color","green");
							$(".alterando").attr("class","cadastrar");
							$(".cadastrar").html("CADASTRAR");
							$("input[name='ID_LOGIN']").val("");
							$("input[name='NOME_LOGIN']").val("");
							paginacao(pagina_atual);
						}
						else{
							console.log(d);
							$("#status").html("Login Não Alterado! Código já existe!");
							$("#status").css("color","red");
						}
					}
				});
			});
			
			//defina a seguinte regra para o botao de envio
			$(document).on("click",".cadastrar",function(){
			
			$.ajax({
				url: "insere.php?tabela=LOGIN",
				type: "post",
				data: {
						ID_LOGIN: $("input[name='ID_LOGIN']").val(),
						NOME_LOGIN: $("input[name='NOME_LOGIN']").val()
					 },
				beforeSend:function(){
					$("button").attr("disabled",true);
				},
				success: function(d){
					$("button").attr("disabled",false);
					if(d=='1'){
						$("#status").html("Login inserido com sucesso!");
						$("#status").css("color","green");
						carrega_botoes();
						paginacao(pagina_atual);
					}
					else{
						console.log(d);
						$("#status").html("Login Não Alterado! Código já existe!");
						$("#status").css("color","red");
					}
				}
			});
		});
		
	});
</script>
</body>
</html>