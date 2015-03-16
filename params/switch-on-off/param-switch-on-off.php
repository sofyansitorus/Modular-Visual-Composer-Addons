<?php
if(!class_exists('Param_Switch_On_Off')){
	class Param_Switch_On_Off extends AVCA{
		function __construct(){	
			add_shortcode_param('switch_on_of' , array($this, 'build_param'));
		}
	
		function build_param($settings, $value){
			return '';
		}
		
	}
}
if(class_exists('Param_Switch_On_Off')){
	new Param_Switch_On_Off();
}