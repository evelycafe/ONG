<?php 
	error_reporting(-1);

    ini_set("display_errors", 1); 
	
	require_once("../classeLayout/classeCabecalhoHTML.php");
	require_once("cabecalho.php");
	
	require_once("../classeForm/classeInput.php");
	require_once("../classeForm/classeForm.php");
	require_once("../classeForm/classeButton.php");

	if(isset($_POST["id"])){
		require_once("classeControllerBD.php");
		require_once("conexao.php");

		$c = new ControllerBD($conexao);
		$colunas = array("ID_VETERINARIO", "NOME", "ENDERECO", "TELEFONE", "CRV");
		$tabelas[0][0] = "VETERINARIO";
		$tabelas[0][1] = null;
		$ordenacao = null;
		$condicao = $_POST["id"];

		$stmt = $c->selecionar($colunas, $tabelas, $ordenacao, $condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);

		$value_id_veterinario = $linha["ID_VETERINARIO"];
        $value_nome = $linha["NOME"];
        $endereco = $linha["ENDERECO"];
        $telefone = $linha["TELEFONE"];
        $crv = $linha["CRV"];
        $action = "altera.php?tabela=veterinario";
        $disabled = true;
	}
	else{
        $disabled = false;
		$action = "insere.php?tabela=veterinario";
		$value_id_veterinario = null;
		$value_nome = null;
		$endereco = null;
		$telefone = null;
		$crv = null;
	}


	$v = array("action"=>$action,"method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"number","name"=>"ID_VETERINARIO","placeholder"=>"ID DO VETERINARIO...", "value"=>$value_id_veterinario);
    $f->add_input($v);
    
    if($disabled){
        $v = array("type"=>"hidden", "name"=>"ID_VETERINARIO", "value"=>$value_id_veterinario);
        $f->add_Input($v);
    }
	
	$v = array("type"=>"text","name"=>"NOME","placeholder"=>"NOME DO VETERINARIO...", "value"=>$value_nome);
    $f->add_input($v);
	
    $v = array("type"=>"text","name"=>"ENDERECO","placeholder"=>"ENDEREÇO...","value"=>$endereco);
    $f->add_input($v);
	
    $v = array("type"=>"NUMBER","name"=>"TELEFONE","placeholder"=>"TELEFONE...","value"=>$telefone);
    $f->add_input($v);
	
    $v = array("type"=>"text","name"=>"CRV","placeholder"=>"CRV...","value"=>$crv);
	$f->add_input($v);
	
	
	$v = array("type"=>"button","class"=>"cadastrar","texto"=>"CADASTRAR");
	$f->add_button($v);	
?>
<!DOCTYPE html>

<h3>Veterinário</h3>
<div id="status"></div>

<hr />
<?php
	$f->exibe();

?>
<script>
<?php 
	// permissao:
	// 1: root
	// 2: veterinario
	// 3: usr
	if($_SESSION["login"]["permissao"] == 1){
		echo "permissao=1;";
	}
	else if($_SESSION["login"]["permissao"] == 2){
		echo "permissao=2;";
	}
	else{
		echo "permissao=3;";
	}
?>
pagina_atual = 1;
	//quando o documento estiver pronto...
	$(function(){
		
		carrega_botoes();
		
		function carrega_botoes(){
			
			$.ajax({
				url: "quantidade_botoes.php",
				type: "post",
				data: {tabela: "VETERINARIO"},
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
					tabela: "VETERINARIO"
				},
				success: function(d){
					if(d == 1){
						$("#status").html("Removido com sucesso!");
						carrega_botoes();
						qtd = $("tbody tr").length;
						if(qtd == "1"){
							pagina_atual--;
						}
						paginacao(pagina_atual);
					}
					else if(d == '0'){
						$('#status').html("Você não tem permissão para remover.")
					}
					else if(d == "-1"){
						$('#status').html("Você não está logado.")
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
									0:{0:"VETERINARIO",1:null}
								},
						colunas:{0:"ID_VETERINARIO",1:"NOME",3:"ENDERECO",4:"TELEFONE",5:"CRV"}, 
						pagina: b
					  },
				success: function(matriz){
					
					$("tbody").html("");
					for(i=0;i<matriz.length;i++){
						tr = "<tr>";
						tr += "<td>"+matriz[i].ID_VETERINARIO+"</td>";
						tr += "<td>"+matriz[i].NOME+"</td>";
						tr += "<td>"+matriz[i].ENDERECO+"</td>";
						tr += "<td>"+matriz[i].TELEFONE+"</td>";
						tr += "<td>"+matriz[i].CRV+"</td>";
						tr += "<td><button value='"+matriz[i].id_veterinario+"' class='remover'>Remover</button>";
						tr += "<button value='"+matriz[i].id_veterinario+"' class='alterar'>Alterar</button></td>";
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
				data: {id: id_alterar, tabela: "VETERINARIO"},
				success: function(dados){
					$("input[name='ID_VETERINARIO']").val(dados.ID_VETERINARIO);
					$("input[name='NOME']").val(dados.NOME);
					$("input[name='ENDERECO']").val(dados.ENDERECO);
					$("input[name='TELEFONE']").val(dados.TELEFONE);
					$("input[name='CRV']").val(dados.CRV);
					$(".cadastrar").attr("class","alterando");
					$(".alterando").html("ALTERAR");
				}
			});
		});
			
			$(document).on("click",".alterando",function(){
				
				$.ajax({
					url:"altera.php?tabela=VETERINARIO",
					type: "post",
					data: {
						ID_VETERINARIO: $("input[name='ID_VETERINARIO']").val(),
						NOME: $("input[name='NOME']").val(),
						ENDERECO: $("input[name='ENDERECO']").val(),
						TELEFONE: $("input[name='TELEFONE']").val(),
						CRV: $("input[name='CRV']").val()
					 },
					beforeSend:function(){
						$("button").attr("disabled",true);
					},
					success: function(d){
						$("button").attr("disabled",false);
						if(d=='1'){
							$("#status").html("Veterinário Alterado com sucesso!");
							$("#status").css("color","green");
							$(".alterando").attr("class","cadastrar");
							$(".cadastrar").html("CADASTRAR");
							$("input[name='ID_VETERINARIO']").val("");
                            $("input[name='NOME']").val("");
                            $("input[name='ENDERECO']").val("");
                            $("input[name='TELEFONE']").val("");
                            $("input[name='CRV']").val("");
							
							paginacao(pagina_atual);
						}
						else{
							console.log(d);
							$("#status").html("Veternário Não Alterado! Código já existe!");
							$("#status").css("color","red");
						}
					}
				});
			});
			
			//defina a seguinte regra para o botao de envio
			$(document).on("click",".cadastrar",function(){
			
			$.ajax({
				url: "insere.php?tabela=VETERINARIO",
				type: "post",
				data: {
						ID_VETERINARIO: $("input[name='ID_VETERINARIO']").val(),
						NOME: $("input[name='NOME']").val(),
						ENDERECO: $("input[name='ENDERECO']").val(),
						TELEFONE: $("input[name='TELEFONE']").val(),
						CRV: $("input[name='CRV']").val()
					 },
				beforeSend:function(){
					$("button").attr("disabled",true);
				},
				success: function(d){
					$("button").attr("disabled",false);
					if(d=='1'){
						$("#status").html("Veterinário inserido com sucesso!");
						$("#status").css("color","green");
						carrega_botoes();
						paginacao(pagina_atual);
					}
					else{
						console.log(d);
						$("#status").html("Veterinário Não Alterado! Código já existe!");
						$("#status").css("color","red");
					}
				}
			});
		});
		
	});
</script>
</body>
</html>
