<?php
if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly

/*
 * Name: AVCA Formidable
 */

class AvcaFormidable extends AvcaModule{

	const slug = 'avca_formidable';

	public function __construct(){
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( !is_plugin_active( "formidable/formidable.php" )) {
			return false;
		}
		add_action( 'vc_before_init', array( $this, 'vc_before_init' ) );
		add_shortcode( self::slug, array( $this, 'build_shortcode' ) );
	}

	public function vc_before_init(){
		vc_map( array(
			'name' => __('AVCA Formidable', AVCA_SLUG),
			"base" => self::slug,
			"class" => "",
			"category" => "AVCA",
				'params' => array(
					array(
						'type' => 'dropdown',
						'class' => '',
						'heading' => __('Form', AVCA_SLUG),
						'param_name' => 'form_id',
						'value' => $this->get_forms(),
						'admin_label' => true
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Display form title', AVCA_SLUG ),
						'param_name' => 'title',
						'value' => array( __( 'Yes', 'js_composer' ) => '1' )
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Display form description', AVCA_SLUG ),
						'param_name' => 'description',
						'value' => array( __( 'Yes', 'js_composer' ) => '1' )
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Minimize form HTML', AVCA_SLUG ),
						'param_name' => 'minimize',
						'value' => array( __( 'Yes', 'js_composer' ) => '1' )
					)
				)
			)
		);
	}

	public function build_shortcode( $atts, $content = null ){
		extract(shortcode_atts(array(
			'form_id' => '',
			'title' => false,
			'description' => false,
			'minimize' => false
		), $atts));

		return FrmFormsController::get_form_shortcode( array( 'id' => $form_id, 'title' => $title, 'description' => $description, 'minimize' => $minimize ) );;
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