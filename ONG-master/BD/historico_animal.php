<!DOCTYPE html>

<h3>Histórico do seu PET</h3>
<div id="status"></div>

<hr />

<?php error_reporting(-1);

    ini_set("display_errors", 1); 
	require_once("../classeLayout/classeCabecalhoHTML.php");
	require_once("cabecalho.php");
	
	require_once("../classeForm/classeInput.php");
	require_once("../classeForm/classeForm.php");
	require_once("../classeForm/classeButton.php");
	require_once("../classeForm/classeSelect.php");
	require_once("../classeForm/classeOption.php");
	
	$select  = "SELECT HISTORICO_ATENDIMENTO.ID_HISTORICO_ATENDIMENTO as ID, HISTORICO_ATENDIMENTO.DATA_ATENDIMENTO, ANIMAL.NOME AS ANIMAL, VETERINARIO.NOME AS VETERINARIO,     HISTORICO_ATENDIMENTO.MEDICACAO, HISTORICO_ATENDIMENTO.OBSERVACAO 
		FROM CONSULTA INNER JOIN HISTORICO_ATENDIMENTO ON CONSULTA.ID_HISTORICO_ATENDIMENTO=HISTORICO_ATENDIMENTO.ID_HISTORICO_ATENDIMENTO 
		INNER JOIN ANIMAL ON CONSULTA.ID_ANIMAL=ANIMAL.ID_ANIMAL 
		INNER JOIN VETERINARIO ON CONSULTA.ID_VETERINARIO=VETERINARIO.ID_VETERINARIO
		WHERE CONSULTA.ID_ANIMAL = (SELECT ID_ANIMAL FROM ANIMAL WHERE ID_LOGIN = ".$_SESSION["login"]["id"].");";
		
		
	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	$select = array();
	
	while($linha=$stmt->fetch(PDO::FETCH_ASSOC)){
		$select[] = $linha;
	}	
	
	$t = new Tabela($select,"CONSULTA");
	$t->exibe();
	
?>

<script>
	$(function(){
		
		carrega_botoes();
		
		function carrega_botoes(){
			
			$.ajax({
				url: "quantidade_botoes.php",
				type: "post",
				data: {tabela: "HISTORICO_ATENDIMENTO"},
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
									0:{0:"CONSULTA", 1:"HISTORICO_ATENDIMENTO"},
									1:{0:"CONSULTA", 1:"ANIMAL"},
									2:{0:"CONSULTA", 1:"VETERINARIO"}
								},
						colunas:{0:"HISTORICO_ATENDIMENTO.DATA_ATENDIMENTO AS DATA",1:"ANIMAL.NOME AS ANIMAL",2:"VETERINARIO.NOME AS VETERINARIO",3:"HISTORICO_ATENDIMENTO.MEDICACAO AS MEDICACAO", 4:"HISTORICO_ATENDIMENTO.OBSERVACAO AS OBSERVACAO", 5: "ID_HISTORICO_ATENDIMENTO AS ID"}, 
						pagina: b
					  },
				success: function(matriz){
					
					$("tbody").html("");
					for(i=0;i<matriz.length;i++){
						tr = "<tr>";
						tr += "<td>"+matriz[i]+ID+"</td>";
						tr += "<td>"+matriz[i].DATA+"</td>";
						tr += "<td>"+matriz[i].DATA+"</td>";
						tr += "<td>"+matriz[i].ANIMAL+"</td>";
						tr += "<td>"+matriz[i].VETERINARIO+"</td>";
						tr += "<td>"+matriz[i].MEDICACAO+"</td>";
						tr += "<td>"+matriz[i].OBSERVACAO+"</td>";
					
						tr += "</tr>";	
						$("tbody").append(tr);
					}
				}
			});
		}
	});
</script>
