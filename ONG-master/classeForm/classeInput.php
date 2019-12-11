<?php

	class Input {//implements Exibicao{
		private $type;
		private $name;
		private $placeholder;
		private $value;
		private $disabled=false;
		
		public function __construct($vetor){
			$this->type=$vetor["type"];
			$this->name=$vetor["name"];			
			$this->value=$vetor["value"];
			if(isset($vetor["placeholder"])){
				$this->placeholder=$vetor["placeholder"];
			}
			if(isset($vetor["disabled"])){
				$this->disabled = $vetor["disabled"];
			}
			
		}
		
		public function exibe(){
			echo 
			"<input type='$this->type'
					name='$this->name' value='$this->value' ";
			if($this->placeholder!=null){
				echo "placeholder='$this->placeholder' ";
			}
			if($this->disabled){
				echo "disabled ";
			}
			echo " />";
		}
		
	}

?>