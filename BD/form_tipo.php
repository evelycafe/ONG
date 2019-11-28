<?php 
	error_reporting(-1);

    ini_set("display_errors", 1); 

	include("../classeLayout/classeCabecalhoHTML.php");
	include("cabecalho.php");
	
	require_once("../classeForm/InterfaceExibicao.php");
	require_once("../classeForm/classeInput.php");
	require_once("../classeForm/classeOption.php");
	require_once("../classeForm/classeSelect.php");
	require_once("../classeForm/classeForm.php");
	require_once("../classeForm/classeButton.php");

	include("conexao.php");
	
	//////////////		//////////////////		//////////////////
	
	if (isset($_POST["id"])) {
		require_once("classeControllerBD.php");
		
		$c = new ControllerBD($conexao);
		
		$colunas=array("ID_TIPO","TIPO_DOACAO");
		$tabelas[0][0]="tipo";
		$tabelas[0][1]=null;
		$ordenacao = null;
		$condicao = $_POST["id"];
		
		$stmt = $c->selecionar($colunas,$tabelas,$ordenacao,$condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$value_id_tipo = $linha["ID_TIPO"];
		$value_tipo_doacao = $linha["TIPO_DOACAO"];
		$selected_id_doacao = $linha["ID_DOACAO"];
		$disabled=true;
		
		$action = "altera.php?tabela=tipo";
	}
	
	else{
		$disabled = false;
		$action = "insere.php?tabela=tipo";
		$value_id_tipo = null;
		$value_tipo_doacao = null;
		$selected_id_doacao = null;
	}
	
	//////////////		//////////////////		//////////////

	$v = array("action"=>"insere.php?tabela=tipo","method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"text","name"=>"ID_TIPO","placeholder"=>"ID DO TIPO DE DOACAO", "value"=>$value_id_tipo,"disabled"=>$disabled);
	$f->add_input($v);
	
	if($disabled){
		$v = array("type"=>"hidden","name"=>"ID_TIPO","value"=>$value_id_tipo);
		$f->add_input($v);
	}
	
	$v = array("type"=>"text","name"=>"TIPO_DOACAO", "value"=>$value_tipo_doacao);
	$f->add_input($v);
	
	$v = array("type"=>"button","texto"=>"ENVIAR");
	$f->add_button($v);	
?>

	<h3>Tipo de Doação</h3>
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
	$(function(){
		carrega_botoes();
		
		function carrega_botoes(){
			
			$.ajax({
				url: "quantidade_botoes.php",
				type: "post",
				data: {tabela: "TIPO"},
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
					tabela: "TIPO"
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
									0:{0:"TIPO",1:null}
								},
						colunas:{0:"ID_TIPO",1:"TIPO_DOACAO"}, 
						pagina: b
					  },
				success: function(matriz){
					
					$("tbody").html("");
					for(i=0;i<matriz.length;i++){
						tr = "<tr>";
						tr += "<td>"+matriz[i].ID_TIPO+"</td>";
                        tr += "<td>"+matriz[i].TIPO_DOACAO+"</td>";
						tr += "<td><button value='"+matriz[i].ID_TIPO+"' class='remover'>Remover</button>";
						tr += "<button value='"+matriz[i].ID_TIPO+"' class='alterar'>Alterar</button></td>";
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
				data: {id: id_alterar, tabela: "TIPO"},
				success: function(dados){
					$("input[name='ID_TIPO']").val(dados.ID_TIPO);
                    $("input[name='TIPO_DOACAO']").val(dados.TIPO_DOACAO);
					$(".cadastrar").attr("class","alterando");
					$(".alterando").html("ALTERAR");
				}
			});
		});
			
			$(document).on("click",".alterando",function(){
				
				$.ajax({
					url:"altera.php?tabela=TIPO",
					type: "post",
					data: {
						ID_RACA: $("input[name='ID_TIPO']").val(),
                        NOME: $("input[name='TIPO_DOACAO']").val(),
					 },
					beforeSend:function(){
						$("button").attr("disabled",true);
					},
					success: function(d){
						$("button").attr("disabled",false);
						if(d=='1'){
							$("#status").html("Tipo Alterado com sucesso!");
							$("#status").css("color","green");
							$(".alterando").attr("class","cadastrar");
							$(".cadastrar").html("CADASTRAR");
							$("input[name='ID_TIPO']").val("");
							$("input[name='TIPO_DOACAO']").val("");
							
							paginacao(pagina_atual);
						}
						else{
							console.log(d);
							$("#status").html("Tipo Não Alterado! Código já existe!");
							$("#status").css("color","red");
						}
					}
				});
			});
			
			//defina a seguinte regra para o botao de envio
			$(document).on("click",".cadastrar",function(){
			
			$.ajax({
				url: "insere.php?tabela=TIPO",
				type: "post",
				data: {
						ID_RACA: $("input[name='ID_TIPO']").val(),
                        NOME: $("input[name='TIPO_DOACAO']").val()
					 },
				beforeSend:function(){
					$("button").attr("disabled",true);
				},
				success: function(d){
					$("button").attr("disabled",false);
					if(d=='1'){
						$("#status").html("Tipo inserido com sucesso!");
						$("#status").css("color","green");
						carrega_botoes();
						paginacao(pagina_atual);
					}
					else{
						console.log(d);
						$("#status").html("Tipo Não Alterado! Código já existe!");
						$("#status").css("color","red");
					}
				}
			});
		});
		
	});
	</script>
	</body>
</html>

