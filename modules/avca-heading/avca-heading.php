<?php
if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly

/*
 * AVCA Module: AVCA Heading
 * Description: Custom heading experiment
 * Author Name: Nikko Khresna
 * Author URL: https://github.com/nikhresna/
 * Version: 1.0
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
				"name"  		=> __( "Avca Heading", AVCA_SLUG ),
				"base" 			=> self::base,
				"class" 		=> "heading",
	            "icon" => plugins_url('assets/icon.png', __FILE__),
				"custom_markup" => "",
				"category"		=> "AVCA",
				"params" 		=> array(
					array(
						"type"  		=> "textfield",
						"class" 		=> "",
						"heading" 		=> __( "Heading Text", AVCA_SLUG ),
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
											),
					),

					array(
						'type'			=> 'font_container',
						'param_name'	=> 'font_container',
						'value'			=> '',
						'settings'		=>array(
										    'fields'=>array(
										        'font_size',
										        'line_height',
										        'color',
										        'font_style',
										        'font_family',
										    	),
											),
					),

					array(
						"type"			=> "dropdown",
						"heading"		=> __( "Text Align", AVCA_SLUG),
						"param_name"	=> "text_align",
						"value"			=> array(
												"Left" => "text-left",
												"Center" => "text-center",
												"Right" => "text-right"
											),

					),

					array(
						"type"			=> "checkbox",
						"heading"		=> __( "Background Clip", AVCA_SLUG ),
						"param_name"	=> "background_clip",
						"value" 		=> array(__("Yes, please", AVCA_SLUG) => 'avca-heading'),
						"description"	=> __( "Selecting background-size from tab positioning will override some of AVCA Heading styles", AVCA_SLUG ),
					),

					array(
						"type"			=> "dropdown",
						"heading"		=> __( "Background Clip Attachment", AVCA_SLUG ),
						"param_name"	=> "background_clip_attachment",
						"value"			=> array(
												"scroll" => "",
												"fixed"	=> "background-fixed",
											),
					),

					// array(
					// 	"type"			=> "checkbox",
					// 	"heading"		=> __( "Would you like some shadow?", AVCA_SLUG ),
					// 	"param_name"	=> "mega_heading_shadow",
					// 	"value"			=> array(__("Yes, please", AVCA_SLUG ) => 'mega-heading-shadow'),
					// ),

					array(
						"type"			=> "textfield",
						"heading"		=> __( "Custom Element Class", AVCA_SLUG ),
						"param_name"	=> "custom_class",
						"value"			=> "",
						"description"	=> __( "Extra class for further modification written in your css file", AVCA_SLUG ),
					),

					array(
						"type"			=> "textfield",
						"heading"		=> __( "Custom Element Id", AVCA_SLUG ),
						"param_name"	=> "custom_id",
						"value"			=> "",
						"description"	=> __( "Extra id for further modification written in your css file", AVCA_SLUG ),
					),


					array(
						'type'			=> 'css_editor',
						'heading'		=> __( 'Heading Styling', AVCA ),
						'param_name'	=> 'heading_class',
						"group"			=> "Positioning",
			        ),

				)
			)
		);
	}

	public function build_shortcode( $atts ) {
		extract(
			shortcode_atts( array(
				'text_align'	=> 'text-center',
				'background_clip'	=> '',
				'background_clip_image' => '',
				'background_clip_attachment' => '',
				'custom_class'	=> '',
				'custom_id'		=> '',
				'mega_heading_shadow'		=> '',
				'heading_type'	=> 'h2',
				'heading_text'	=> '',
				'font_container' => '',
				'heading_class' => ''
			), $atts )
		);
		
		$heading_class = apply_filters( 
			VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 
			vc_shortcode_custom_css_class( $heading_class, '' ), 
			self::slug, 
			$atts
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
				'font_container'	=> $font_container,
		);

		// declare class to be put ' class="" '
		$classes = array(
				'background-attachment'	=> $background_clip_attachment,
				'text-align'	=> $text_align,
				'background_clip'	=> $background_clip,
				'mega_heading_shadow'	=> $mega_heading_shadow,
				'custom-class'	=> $custom_class,
				'heading_class'		=> $heading_class,
		);

		// declare id to be put ' id="" '
		$ids = array(
				'custom_id'	=> $custom_id,
		);


		$element_id		= output_id( $ids );
		$element_style 	= output_style( $styles );
		$element_class 	= output_class( $classes );

		$font_container_obj = new Vc_Font_Container();
		$font_container_data = $font_container_obj->_vc_font_container_parse_attributes(
			array(
				'font_size',
				'line_height',
				'color',
				'font_style_italic',
				'font_style_bold',
				'background_attachment',
			), 
			$font_container 
		);

		$styles = array();
		if ( ! empty( $font_container_data ) && isset( $font_container_data['values'] ) ) {
			foreach ( $font_container_data['values'] as $key => $value ) {
				if ( $key != 'tag' && strlen( $value ) > 0 ) {
					if ( preg_match( '/description/', $key ) ) {
						continue;
					}
					if ( $key == 'font_size' || $key == 'line_height' ) {
						$value = preg_replace( '/\s+/', '', $value );
					}
					if ( $key == 'font_size' ) {
						$pattern = '/^(\d*(?:\.\d+)?)\s*(px|\%|in|cm|mm|em|rem|ex|pt|pc|vw|vh|vmin|vmax)?$/';
						// allowed metrics: http://www.w3schools.com/cssref/css_units.asp
						$regexr = preg_match( $pattern, $value, $matches );
						$value = isset( $matches[1] ) ? (float) $matches[1] : (float) $value;
						$unit = isset( $matches[2] ) ? $matches[2] : 'px';
						$value = $value . $unit;
					}
					if ( strlen( $value ) > 0 ) {
						if(array_key_exists($key, $font_container_data['fields'])){
							switch ($key) {
								case 'font_style_italic':
										if($value == 1){
											$styles[$key] = 'font-style: italic';
										}
									break;

								case 'font_style_bold':
										if($value == 1){
											$styles[$key] = 'font-weight: bold';
										}
									break;
								
								default:
										$styles[$key] = str_replace( '_', '-', $key ) . ': ' . $value;
									break;
							}
						}
					}
				}
			}
		}
		$output = '';
		$output .= '
			<'. $heading_type .' '. $element_id .' '. $element_class .' style=" '.implode("; ", $styles).' ">'. $heading_text .'</ '. $heading_type. ' >
		';
		return $output;
	}

}

new AvcaHeading();