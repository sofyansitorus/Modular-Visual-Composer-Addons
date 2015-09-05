<?php
if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly

/*
 * AVCA Module: AVCA Heading
 * Description: Custom heading experiment
 * Author Name: Sofyan Sitorus
 * Author URL: https://github.com/sofyansitorus/
 * Version: Waiting pull request
 */

class AvcaHeading extends AvcaModule{

	const slug = 'avca_heading';
	const base = 'avca_heading';

	public function __construct(){
		add_action( 'vc_before_init', array( $this, 'vc_before_init' ) );
		add_shortcode( self::slug, array( $this, 'build_shortcode' ) );
	}

	public function vc_before_init(){
		vc_map(
			array(
				"name"  		=> __( "Avca Heading" ),
				"base" 			=> self::base,
				"class" 		=> "heading",
	            "icon" => plugins_url('assets/icon.png', __FILE__), // or css class name which you can reffer in your css file later. Example: "vc_extend_my_class"
				"custom_markup" => "",
				"params" 		=> array(
					array(
						"type"  		=> "textfield",
						"class" 		=> "",
						"heading" 		=> __( "Heading Text" ),
						"param_name" 	=> "heading_text",
						"admin_label"	=> "true",
						"value"			=> "lorem",
					),

					array(
						"type"			=> "dropdown",
						"class"			=> "",
						"param_name"	=> "heading_type",
						"param"			=> "heading_type",
						"value"			=> array(
												"H1" => "h1",
												"H2" => "h2",
												"H3" => "h3",
												"H4" => "h4",
												"H5" => "h5",
												"H6" => "h6"
											)
					),

					array(
						"type"  		=> "textfield",
						"class" 		=> "",
						"heading" 		=> __( "Heading Size" ),
						"param_name" 	=> "heading_size",
						"description"	=> "You must input the px/em/vh/whatever-font-size-your-browser-can-render.",
					),

					array(
						"type"			=> "checkbox",
						"heading"		=> "Background Clip",
						"param_name"	=> "background_clip",
						"value" 		=> array(__("Yes, please", "js_composer") => 'avca-heading'),
						"description"	=> "Recommended font size for this option is =>100px",
					),

					array(
						"type"			=> "attach_image",
						"heading"		=> "Background Clip Image",
						"param_name"	=> "background_clip_image",
						"value"			=> "",
					),

					array(
						"type"			=> "dropdown",
						"heading"		=> "Background Clip Attachment",
						"param_name"	=> "background_clip_attachment",
						"value"			=> array(
												"scroll" => "",
												"fixed"	=> "fixed",
											),
					),

					array(
						"type"			=> "checkbox",
						"heading"		=> "Would you like some shadow?",
						"param_name"	=> "mega_heading_shadow",
						"value"			=> array(__("Yes, please", "js_composer") => 'mega-heading-shadow'),
					),

					array(
						"type"			=> "textfield",
						"heading"		=> "Custom Element Class",
						"param_name"	=> "custom_class",
						"value"			=> "",
						"description"	=> "Extra class for further modification written in you css file",
					),

					array(
						"type"			=> "textfield",
						"heading"		=> "Custom Element Id",
						"param_name"	=> "custom_id",
						"value"			=> "",
						"description"	=> "Extra id for further modification written in you css file",
					),

					array(
						"type"			=> "dropdown",
						"heading"		=> "Text Align",
						"param_name"	=> "text_align",
						"value"			=> array(
												"Left" => "text-left",
												"Center" => "text-center",
												"Right" => "text-right"
											),
						"group"			=> "Positioning",
					),

					array(
						"type"			=> "textfield",
						"class"			=> "",
						"heading"		=> "Padding top",
						"param_name"	=> "padding_top",
						"description"	=> "Use of px/em/else is a must",
						"group"			=> "Positioning",
					),

					array(
						"type"			=> "textfield",
						"class"			=> "",
						"heading"		=> "Padding right",
						"param_name"	=> "padding_right",
						"group"			=> "Positioning",
						"description"	=> "Use of px/em/else is must",
					),

					array(
						"type"			=> "textfield",
						"class"			=> "",
						"heading"		=> "Padding bottom",
						"param_name"	=> "padding_bottom",
						"group"			=> "Positioning",
						"description"	=> "Use of px/em/else is must",
					),

					array(
						"type"			=> "textfield",
						"class"			=> "",
						"heading"		=> "Padding left",
						"param_name"	=> "padding_left",
						"group"			=> "Positioning",
						"description"	=> "Use of px/em/else is must",
					),
				)
			)
		);
	}

	public function build_shortcode( $atts ) {
		extract(
			shortcode_atts( array(
				'text_align'	=> 'text-left',
				'background_clip'	=> '',
				'background_clip_image' => '',
				'background_clip_attachment' => '',
				'custom_class'	=> '',
				'custom_id'		=> '',
				'mega_heading_shadow'		=> '',
				'heading_type'	=> 'h2',
				'heading_text'	=> '',
				'heading_size'	=> '',
				'padding_top'	=> '',
				'padding_right'	=> '',
				'padding_bottom'	=> '',
				'padding_left'	=> '',
				'row_type'		=> '',
			), $atts )
		);
		

		/*
			add ' style="" ' to holder if necessary
		*/
		function output_style( $styles ) {

			$all_style = '';

			$style_output = '';

			$check_all = false;
			
			foreach ($styles as $style => $key) {
				if ( ! $key == '' ) {
					$all_style .= $style;
					$all_style .= ': '; //add ':' every after css property
					$all_style .= $key;
					$all_style .= '; '; //add ';' every after css value
					$check_all = true;
				}
			}

			if ( $check_all ) {
				$style_output = 'style="'. $all_style .'"';
			}

			return $style_output;
		}

		/*
			add ' class="" ' to holder if necessary
		*/
		function output_class( $classes ) {

			$all_class = '';

			$class_output = '';

			$check_all = false;
			
			foreach ($classes as $class => $key) {
				if ( ! $key == '' ) {
					$all_class .= $key;
					$all_class .= ' '; // add space avery after class
					$check_all = true;
				}
			}

			if ( $check_all ) {
				$class_output = 'class="'. $all_class .'"';
			}

			return $class_output;
		}

		/*
			add ' id="" ' to holder if necessary
		*/
		function output_id( $ids ) {

			$all_id = '';

			$id_output = '';

			$check_all = false;
			
			foreach ($ids as $id => $key) {
				if ( ! $key == '' ) {
					$all_id .= $key;
					$all_id .= ' '; // add space avery after id
					$check_all = true;
				}
			}

			if ( $check_all ) {
				$id_output = 'id="'. $all_id .'"';
			}

			return $id_output;
		}

		$img_id = $background_clip_image;
		$background_clip_image = wp_get_attachment_image_src( $img_id, 'full');
		$background_clip_image = 'url(' . $background_clip_image[0] . ')';
		
		// declare property to be put in ' style="" '
		$styles = array(
				'font-size'	=> $heading_size,
				'padding-top'	=> $padding_top,
				'padding-right'	=> $padding_right,
				'padding-bottom'	=> $padding_bottom,
				'padding-left'	=> $padding_left,
				'background-image'	=> $background_clip_image,
				'background-attachment'	=> $background_clip_attachment,
		);

		// declare class to be put ' class="" '
		$classes = array(
				'text-align'	=> $text_align,
				'background_clip'	=> $background_clip,
				'mega_heading_shadow'	=> $mega_heading_shadow,
				'custom-class'	=> $custom_class,
		);

		// declare id to be put ' id="" '
		$ids = array(
				'custom_id'	=> $custom_id,
		);


		$element_id		= output_id( $ids );
		$element_style 	= output_style( $styles );
		$element_class 	= output_class( $classes );


		$output = '';
		$output .= '
			<'. $heading_type .' '. $element_id .' '. $element_class .' '. $element_style .' >'. $heading_text .'</ '. $heading_type. ' >
		';
		return $output;
	}

}

new AvcaHeading();