<?php
if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly

/*
 * Name: AVCA Formidable
 * Description: Formidable form selector for Visual Composer
 * Author Name: Sofyan Sitorus
 * Author URL: https://github.com/sofyansitorus/
 * Version: 1.0.0
 */

class AvcaFormidable extends AvcaModule{

	const slug = 'avca_formidable';
	const base = 'formidable';

	public function __construct(){
		if ( !is_plugin_active( "formidable/formidable.php" )) {
			return false;
		}
		add_action( 'vc_before_init', array( $this, 'vc_before_init' ) );
	}

	public function vc_before_init(){
		vc_map( array(
			'name' => __('AVCA Formidable', AVCA_SLUG),
			"base" => self::base,
			"class" => "",
			"category" => "AVCA",
				'params' => array(
					array(
						'type' => 'dropdown',
						'class' => '',
						'heading' => __('Form', AVCA_SLUG),
						'param_name' => 'id',
						'value' => $this->get_forms(),
						'admin_label' => true
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Display form title', AVCA_SLUG ),
						'param_name' => 'title',
						'value' => array( __( 'Yes', 'js_composer' ) => 'true' )
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Display form description', AVCA_SLUG ),
						'param_name' => 'description',
						'value' => array( __( 'Yes', 'js_composer' ) => 'true' )
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Minimize form HTML', AVCA_SLUG ),
						'param_name' => 'minimize',
						'value' => array( __( 'Yes', 'js_composer' ) => 'true' )
					)
				)
			)
		);
	}

	private function get_forms(){
		$forms = array();
		$formidable = new FrmForm();
		foreach ($formidable->get_published_forms() as $form) {
			$forms[$form->name] = $form->id;
		}
		return $forms;
	}
}

new AvcaFormidable();