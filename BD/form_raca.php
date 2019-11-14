<?php
	require_once("../classeLayout/classeCabecalhoHTML.php");
	require_once("cabecalho.php");
	
	require_once("../classeForm/classeInput.php");
	require_once("../classeForm/classeForm.php");
	require_once("../classeForm/classeButton.php");
	require_once("../classeForm/classeSelect.php");
	require_once("../classeForm/classeOption.php");

	if(isset($_POST["id"])){
		require_once("classeControllerBD.php");
		require_once("conexao.php");

		$c = new ControllerBD($conexao);
		$colunas = array("ID_RACA", "NOME", "ID_ESPECIE");
		$tabelas[0][0] = "raca";
		$tabelas[0][1] = null;
		//$tabelas[0][1] = "especie";
		$ordenacao = null;
		$condicao = $_POST["id"];

		$stmt = $c->selecionar($colunas, $tabelas, $ordenacao, $condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);

		$value_id_raca = $linha["ID_RACA"];
        $value_nome = $linha["NOME"];
        $selected_especie = $linha["ID_ESPECIE"];
        $action = "altera.php?tabela=raca";
        $disabled = true;
	}
	else{
        $disabled = false;
		$action = "insere.php?tabela=raca";
		$value_id_raca = null;
        $value_nome = null;
        $selected_especie = null;
    }
    ////////////////////    PK  ////////////////////////////////////////////////////////
    $select = "SELECT ID_ESPECIE AS value, NOME AS texto FROM especie ORDER BY NOME";
	
	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	while($linha=$stmt->fetch()){
		$matriz[] = $linha;
	}	
    /////////////////////////////////////////////////////////////////////////////
	$v = array("action"=>$action,"method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"number","name"=>"ID_RACA","placeholder"=>"ID DA RAÇA...", "value"=>$value_id_raca);
    $f->add_input($v);
    
    if($disabled){
        $v = array("type"=>"hidden", "name"=>"ID_RACA", "value"=>$value_id_raca);
        $f->add_Input($v);
    }
	
	$v = array("type"=>"text","name"=>"NOME","placeholder"=>"NOME DA RAÇA...", "value"=>$value_nome);
    $f->add_input($v);	

    $v = array("name"=>"ID_ESPECIE","selected"=>$selected_especie);
	$f->add_select($v,$matriz);
	
	$v = array("type"=>"button","class"=>"cadastrar","texto"=>"CADASTRAR");
	$f->add_button($v);	
?>
<!DOCTYPE html>

<h3>Formulário - Inserir Raça</h3>
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
				data: {tabela: "RACA"},
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
					tabela: "RACA"
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
									0:{0:"RACA",1:null}
								},
						colunas:{0:"ID_RACA",1:"NOME", 2: "ID_ESPECIE"}, 
						pagina: b
					  },
				success: function(matriz){
					
					$("tbody").html("");
					for(i=0;i<matriz.length;i++){
						tr = "<tr>";
						tr += "<td>"+matriz[i].ID_RACA+"</td>";
                        tr += "<td>"+matriz[i].NOME+"</td>";
                        tr += "<td>"+matriz[i].ID_ESPECIE+"</td>";
						tr += "<td><button value='"+matriz[i].ID_RACA+"' class='remover'>Remover</button>";
						tr += "<button value='"+matriz[i].ID_RACA+"' class='alterar'>Alterar</button></td>";
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
				data: {id: id_alterar, tabela: "RACA"},
				success: function(dados){
					$("input[name='ID_RACA']").val(dados.ID_RACA);
                    $("input[name='NOME']").val(dados.NOME);
                    $("input[name='ID_ESPECIE']").val(dados.ID_ESPECIE);
					$(".cadastrar").attr("class","alterando");
					$(".alterando").html("ALTERAR");
				}
			});
		});
			
			$(document).on("click",".alterando",function(){
				
				$.ajax({
					url:"altera.php?tabela=RACA",
					type: "post",
					data: {
						ID_RACA: $("input[name='ID_RACA']").val(),
                        NOME: $("input[name='NOME']").val(),
                        ID_ESPECIE: $("input[name='ID_ESPECIE']").val()
					 },
					beforeSend:function(){
						$("button").attr("disabled",true);
					},
					success: function(d){
						$("button").attr("disabled",false);
						if(d=='1'){
							$("#status").html("Raça Alterada com sucesso!");
							$("#status").css("color","green");
							$(".alterando").attr("class","cadastrar");
							$(".cadastrar").html("CADASTRAR");
							$("input[name='ID_RACA']").val("");
							$("input[name='ID_ESPECIE']").val("");
							$("input[name='NOME']").val("");
							
							paginacao(pagina_atual);
						}
						else{
							console.log(d);
							$("#status").html("Raça Não Alterada! Código já existe!");
							$("#status").css("color","red");
						}
					}
				});
			});
			
			//defina a seguinte regra para o botao de envio
			$(document).on("click",".cadastrar",function(){
			
			$.ajax({
				url: "insere.php?tabela=RAÇA",
				type: "post",
				data: {
						ID_RACA: $("input[name='ID_RACA']").val(),
                        NOME: $("input[name='NOME']").val(),
                        ID_ESPECIE: $("input[name='ID_ESPECIE']").val(),
					 },
				beforeSend:function(){
					$("button").attr("disabled",true);
				},
				success: function(d){
					$("button").attr("disabled",false);
					if(d=='1'){
						$("#status").html("Raça inserida com sucesso!");
						$("#status").css("color","green");
						carrega_botoes();
						paginacao(pagina_atual);
					}
					else{
						console.log(d);
						$("#status").html("Raça Não Alterada! Código já existe!");
						$("#status").css("color","red");
					}
				}
			});
		});
		
	});
</script>
</body>
</html>