<?php
if(!class_exists('AVCA_Formidable')){
	class AVCA_Formidable extends AVCA{

		private $base = 'avca_formidable';

		function __construct(){
			// include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			// if ( is_plugin_active( "formidable/formidable.php" )) {
			// 	return false;
		 //    } 
			add_action('init',array($this,'init'));
			add_shortcode($this->base,array($this,'build_shortcode'));
		}
		
		function init(){
			//var_dump($this->base);
			vc_map( array(
				'name' => __('AVCA Formidable', self::slug),
				'base' => $this->base,
				'class' => $this->base,
				'category' => self::category,
				'controls' => 'full',
				'show_settings_on_create' => true,
				'icon' => '',
				'params' => array(
					array(
						'type' => 'dropdown',
						'class' => '',
						'heading' => __('Form', self::slug),
						'param_name' => 'form_id',
						'value' => $this->get_forms(),
						'admin_label' => true
					),
				)
			)
			);
		}

		function build_shortcode($atts,$content = null){	
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			extract(shortcode_atts(array(
				'form_id' => ''
			), $atts));

			if(!$form_id || !is_plugin_active( "formidable/formidable.php" )) return;

			$formidable = new FrmFormsController();
			return $formidable->show_form($form_id);
		}

		function get_forms(){
			$forms = array();
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( "formidable/formidable.php" )) {
				$formidable = new FrmForm();
				foreach ($formidable->getAll() as $form) {
					$forms[$form->name] = $form->id;
				}
			}
			return $forms;
		}
	}
	new AVCA_Formidable;
}