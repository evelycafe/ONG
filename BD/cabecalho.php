<?php

	$c = new CabecalhoHTML();
	$v = array(				
				"login"=>"Login",
				"veterinario"=>"Veterinário",
				"animal"=>"Animal",
				"raca"=>"Raça",
				"especie"=>"Espécie",
				"consulta"=>"Consulta",
				"doacao"=>"Doação",
				"tipo"=>"Tipo",
				"historico_atendimento"=>"Histórico de Atendimento",
				"postagem"=>"Postagem",
				"comentario"=>"Comentário"
				);
				
	$c->add_menu($v);
	$c->exibe();

?>