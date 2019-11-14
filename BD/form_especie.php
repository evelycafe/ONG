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
		$colunas = array("ID_ESPECIE", "NOME");
		$tabelas[0][0] = "especie";
		$tabelas[0][1] = null;
		$ordenacao = null;
		$condicao = $_POST["id"];

		$stmt = $c->selecionar($colunas, $tabelas, $ordenacao, $condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);

		$value_id_especie = $linha["ID_ESPECIE"];
        $value_nome = $linha["NOME"];
        $action = "altera.php?tabela=especie";
        $disabled = true;
	}
	else{
        $disabled = false;
		$action = "insere.php?tabela=especie";
		$value_id_especie = null;
		$value_nome = null;
	}

	$v = array("action"=>$action,"method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"number","name"=>"ID_ESPECIE","placeholder"=>"ID DA ESPECIE...", "value"=>$value_id_especie);
    $f->add_input($v);
    
    if($disabled){
        $v = array("type"=>"hidden", "name"=>"ID_ESPECIE", "value"=>$value_id_especie);
        $f->add_Input($v);
    }
	
	$v = array("type"=>"text","name"=>"NOME","placeholder"=>"NOME DA ESPECIE...", "value"=>$value_nome);
    $f->add_input($v);	
	
	$v = array("type"=>"button","class"=>"cadastrar","texto"=>"CADASTRAR");
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
pagina_atual = 1;
	//quando o documento estiver pronto...
	$(function(){
		
		carrega_botoes();
		
		function carrega_botoes(){
			
			$.ajax({
				url: "quantidade_botoes.php",
				type: "post",
				data: {tabela: "ESPECIE"},
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
					tabela: "ESPECIE"
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
									0:{0:"ESPECIE",1:null}
								},
						colunas:{0:"ID_ESPECIE",1:"NOME"}, 
						pagina: b
					  },
				success: function(matriz){
					
					$("tbody").html("");
					for(i=0;i<matriz.length;i++){
						tr = "<tr>";
						tr += "<td>"+matriz[i].ID_ESPECIE+"</td>";
						tr += "<td>"+matriz[i].NOME+"</td>";
						tr += "<td><button value='"+matriz[i].id_especie+"' class='remover'>Remover</button>";
						tr += "<button value='"+matriz[i].id_especie+"' class='alterar'>Alterar</button></td>";
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
				data: {id: id_alterar, tabela: "ESPECIE"},
				success: function(dados){
					$("input[name='ID_ESPECIE']").val(dados.ID_ESPECIE);
					$("input[name='NOME']").val(dados.NOME);
					$(".cadastrar").attr("class","alterando");
					$(".alterando").html("ALTERAR");
				}
			});
		});
			
			$(document).on("click",".alterando",function(){
				
				$.ajax({
					url:"altera.php?tabela=ESPECIE",
					type: "post",
					data: {
						ID_ESPECIE: $("input[name='ID_ESPECIE']").val(),
						NOME: $("input[name='NOME']").val()
					 },
					beforeSend:function(){
						$("button").attr("disabled",true);
					},
					success: function(d){
						$("button").attr("disabled",false);
						if(d=='1'){
							$("#status").html("Espécie Alterada com sucesso!");
							$("#status").css("color","green");
							$(".alterando").attr("class","cadastrar");
							$(".cadastrar").html("CADASTRAR");
							$("input[name='ID_ESPECIE']").val("");
							$("input[name='NOME']").val("");
							
							paginacao(pagina_atual);
						}
						else{
							console.log(d);
							$("#status").html("Espécie Não Alterada! Código já existe!");
							$("#status").css("color","red");
						}
					}
				});
			});
			
			//defina a seguinte regra para o botao de envio
			$(document).on("click",".cadastrar",function(){
			
			$.ajax({
				url: "insere.php?tabela=ESPECIE",
				type: "post",
				data: {
						ID_ESPECIE: $("input[name='ID_ESPECIE']").val(),
						NOME: $("input[name='NOME']").val()
					 },
				beforeSend:function(){
					$("button").attr("disabled",true);
				},
				success: function(d){
					$("button").attr("disabled",false);
					if(d=='1'){
						$("#status").html("Espécie inserida com sucesso!");
						$("#status").css("color","green");
						carrega_botoes();
						paginacao(pagina_atual);
					}
					else{
						console.log(d);
						$("#status").html("Espécie Não Alterada! Código já existe!");
						$("#status").css("color","red");
					}
				}
			});
		});
		
	});
</script>
</body>
</html>