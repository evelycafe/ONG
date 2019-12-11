<?php error_reporting(-1);

    ini_set("display_errors", 1); 

if(isset($_GET["t"])){

	if($_GET["t"]=="login"){
		
		$colunas = array(   "ID_LOGIN as ID",
							"NOME as NOME",
							"ENDERECO as 'ENDEREÇO'",
							"TELEFONE as TELEFONE",
							"EMAIL as 'E-MAIL'"
						);				
				$t[0][0] = "LOGIN";
				$t[0][1] = null;
	}
	
	else if($_GET["t"]=="veterinario"){
		
		$colunas = array(   "ID_VETERINARIO as ID",
							"NOME as NOME",
							"ENDERECO as 'ENDEREÇO'",
							"TELEFONE as TELEFONE",
							"CRV as CRV"
						);				
				$t[0][0] = "VETERINARIO";
				$t[0][1] = null;
	}

	else if($_GET["t"]=="especie"){
		
		$colunas = array(   "ID_ESPECIE as ID",
							"NOME as NOME"
						);				
				$t[0][0] = "ESPECIE";
				$t[0][1] = null;
	}
	
	else if($_GET["t"]=="historico_atendimento"){
		
		$colunas = array(   "ID_HISTORICO_ATENDIMENTO as ID",
							"ANIMAL.NOME  AS ANIMAL",
							"DATA_ATENDIMENTO as 'DATA DE ATENDIMENTO'",
							"MEDICACAO as 'MEDICAÇÃO'",
							"HISTORICO_ATENDIMENTO.OBSERVACAO as 'OBSERVAÇÃO'"
						);
				$t[0][0] = "HISTORICO_ATENDIMENTO";
				$t[0][1] = "ANIMAL";
	}
	
	else if($_GET["t"]=="raca"){
		
		$colunas = array(   "ID_RACA as 'ID'",
							"RACA.NOME as 'NOME'",
							"ESPECIE.NOME as 'ESPECIE'"
						);				
				$t[0][0] = "RACA";
				$t[0][1] = "ESPECIE";
			
	}
	
	else if($_GET["t"]=="animal"){
		
		$colunas = array(   "ID_ANIMAL as ID",
							"ANIMAL.NOME as NOME",
							"IDADE as IDADE",
							"OBSERVACAO as 'OBSERVAÇÃO'",
							"RACA.NOME as 'RAÇA'",
							"LOGIN.NOME as 'LOGIN'",
							"DATA_ADOCAO as 'DATA DE ADOÇÃO'"
						);
				$t[0][0] = "ANIMAL";
				$t[0][1] = "RACA";
				$t[1][0] = "ANIMAL";
				$t[1][1] = "LOGIN";
	}
	
	else if($_GET["t"]=="postagem"){
		
		$colunas = array(   "ID_POSTAGEM as ID",
							"LOGIN.NOME as 'LOGIN'",
							"TEXTO as TEXTO",
							"DATA_POSTAGEM as 'DATA DE POSTAGEM'",
							"LOGIN.NOME as 'LOGIN'"
						);
				$t[0][0] = "POSTAGEM";
				$t[0][1] = "LOGIN";
	}
	else if($_GET["t"]=="consulta"){
		
		$colunas = array(   "ID_CONSULTA as ID",
							"ANIMAL.NOME as ANIMAL",
							"VETERINARIO.NOME as 'VETERINÁRIO'",
							"HISTORICO_ATENDIMENTO.OBSERVACAO as 'HISTÓRICO DE ATENDIMENTO'"
						);
				$t[0][0] = "CONSULTA";
				$t[0][1] = "ANIMAL";
				$t[1][0] = "CONSULTA";
				$t[1][1] = "VETERINARIO";
				$t[2][0] = "CONSULTA";
				$t[2][1] = "HISTORICO_ATENDIMENTO";
	}
	else if($_GET["t"]=="comentario"){
		
		$colunas = array(   "ID_COMENTARIO as ID",
							"COMENTARIO.TEXTO as 'CONTEÚDO'",
							"DATA_COMENTARIO as 'DATA'",
							"POSTAGEM.TEXTO as 'POSTAGEM'",
							"ID_LOGIN AS ID_LOGIN"
						);
				$t[0][0] = "COMENTARIO";
				$t[0][1] = "POSTAGEM ";
	}
	else if($_GET["t"]=="doacao"){
		
		$colunas = array(   "ID_DOACAO as ID",
							"DESCRICAO as 'DESCRIÇÃO'",
							"QUANTIDADE as QUANTIDADE",
							"TIPO.TIPO_DOACAO as TIPO",
							"DATA_DOACAO as 'DATA DA DOAÇÃO'",
							"LOGIN.NOME as 'LOGIN'"
						);
				$t[0][0] = "DOACAO";
				$t[0][1] = "LOGIN";
				$t[1][0] = "DOACAO";
				$t[1][1] = "TIPO";
	}
	else if($_GET["t"]=="tipo"){

		$colunas = array(   "ID_TIPO as ID",
							"TIPO_DOACAO as 'DESCRIÇÃO'"
						);
				$t[0][0] = "TIPO";
				$t[0][1] = null;
	}
	else if($_GET["t"]=="historico_animal"){

		$colunas = array(
							"ID_HISTORICO_ATENDIMENTO as ID",   
							"HISTORICO_ATENDIMENTO.DATA_ATENDIMENTO AS DATA ATENDIMENTO",
							"ANIMAL.NOME AS ANIMAL",
							"VETERINARIO.NOME AS VETERINARIO",
							"HISTORICO_ATENDIMENTO.MEDICACAO AS 'MEDICAÇÃO'",
							"HISTORICO_ATENDIMENTO.OBSERVACAO AS 'OBSERVAÇÃO'"	
						);
				$t[0][0] = "CONSULTA";
				$t[0][1] = "HISTORICO_ATENDIMENTO";
				
				$t[1][0] = "CONSULTA";
				$t[1][1] = "ANIMAL";
				
				$t[2][0] = "CONSULTA";
				$t[2][1] = "VETERINARIO";
	}

}
?>
