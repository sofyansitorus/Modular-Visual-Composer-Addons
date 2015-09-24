<?php
if ( ! defined( "ABSPATH" ) )  exit; // Exit if accessed directly

/*
 * AVCA Module: AVCA Circular Progress
 * Description: Doughnut chart
 * Author Name: Nikko Khresna
 * Author URL: https://github.com/nikhresna/
 * Version: 1.0.0
 */

class AvcaCircularProgress extends AvcaModule {

	const slug = "avca_circular_progress";
	const base = "avca_circular_progress";

	public function __construct(){
		add_action( "vc_before_init", array( $this, "vc_before_init" ) );
		add_shortcode( self::slug, array( $this, "build_shortcode" ) );
	}

	public function vc_before_init(){
		vc_map( array(
			"name" => __( "Circular Bar", AVCA_SLUG ),
			"base" => self::base,
			"category" => __( "Content", AVCA_SLUG ),
			"params" => array(

				array(
				  	"type" => "dropdown",
				  	"heading" => __( "Circular Progress Type", AVCA_SLUG ),
				  	"param_name" => "check_circular_type",
				  	"value" => array(
				  		__( "With Icon", AVCA_SLUG ) => "icon_mode", 
				  		__( "Custom Field", AVCA_SLUG ) => "field_mode",
				  		__( "Animated Percentage", AVCA_SLUG ) => "ani_percentage"
				  	),
				  	"description" => __( "Select the type of your circular progress.", AVCA_SLUG ),
				  	"admin_label" => false
				),

				array(
				  	"type" => "dropdown",
				  	"heading" => __( "Icon", AVCA_SLUG ),
				  	"param_name" => "checkicon",
				  	"value" => array(
				  		__( "No Icon", AVCA_SLUG ) => "no_icon", 
				  		__( "Yes, Display Icon", AVCA_SLUG ) => "custom_icon"
				  	),
				  	"description" => __( "Should an icon be displayed at the left side of the progress bar.", AVCA_SLUG ),
				  	"admin_label" => false,
				  	"dependency" => Array( "element" => "check_circular_type", "value" => array( "icon_mode" ) )
				),

				array(
			      	"type" => "dropdown",
			      	"heading" => __( "Icon", AVCA_SLUG ),
			      	"param_name" => "icon",
			      	"value" => $fonticon_arr,
			      	"description" => __( "Select your icon.", AVCA_SLUG ),
			      	"dependency" => Array( "element" => "checkicon", "value" => array( "custom_icon" ) )
			    ),

				array(
				  	"type" => "dropdown",
				  	"heading" => __( "Icon Color", AVCA_SLUG ),
				  	"param_name" => "icon_color",
				  	"value" => array(__( "Theme Color Default", AVCA_SLUG ) => "", __("Custom Color", AVCA_SLUG ) => "custom"),
				  	"description" => __( "Choose a color for your icon.", AVCA_SLUG ),
				  	"admin_label" => false,
				  	"dependency" => Array( "element" => "check_circular_type", "value" => array( "icon_mode" ) )
				),

				array(
				  	"type" => "colorpicker",
				  	"heading" => __( "Icon Custom Color", AVCA_SLUG ),
				  	"param_name" => "custom_icon_color",
				  	"description" => __( "Select custom color for icon.", AVCA_SLUG ),
				  	"dependency" => Array( "element" => "icon_color", "value" => array( "custom" ) )
				),
				
				// Field
				array(
				  	"type" => "textfield",
				  	"heading" => __( "Circular Progress Bar Field", AVCA_SLUG ),
				  	"param_name" => "circular_field",
				  	"description" => __( "Enter the Circular Progress Bar Field title here.", AVCA_SLUG ),
				  	"admin_label" => false,
				  	"dependency" => Array( "element" => "check_circular_type", "value" => array( "field_mode" ) )
				),

				array(
				  	"type" => "dropdown",
				  	"heading" => __( "Field Color", AVCA_SLUG ),
				  	"param_name" => "field_color",
				  	"value" => array(__( "Theme Color Default", AVCA_SLUG ) => "", __( "Custom Color", AVCA_SLUG ) => "custom" ),
				  	"description" => __( "Choose a color for your field.", AVCA_SLUG ),
				  	"admin_label" => false,
				  	"dependency" => Array( "element" => "check_circular_type", "value" => array( "field_mode" ))
				),

				array(
				  	"type" => "colorpicker",
				  	"heading" => __( "Field Custom Color", AVCA_SLUG ),
				  	"param_name" => "custom_field_color",
				  	"description" => __( "Select custom color for field text.", AVCA_SLUG ),
				  	"dependency" => Array( "element" => "field_color", "value" => array( "custom" ) )
				),

				// Percentage
				array(
				  	"type" => "textfield",
				  	"heading" => __( "Circular Progress in %", AVCA_SLUG ),
				  	"param_name" => "circular_percentage",
				  	"description" => __( "Enter a number between 0 and 100.", AVCA_SLUG ),
				  	"admin_label" => false
				),

				array(
				  	"type" => "textfield",
				  	"heading" => __( "TextField", AVCA_SLUG ),
				  	"param_name" => "circular_percentage_text",
				  	"description" => __( "Enter a text.", AVCA_SLUG ),
				  	"value" => "Text",
				  	"admin_label" => false,
				  	"dependency" => Array( "element" => "check_circular_type", "value" => array( "ani_percentage" ) )
				),

				array(
				  	"type" => "dropdown",
				  	"heading" => __( "Percentage Text Color", AVCA_SLUG ),
				  	"param_name" => "percentage_color",
				  	"value" => array(__( "Theme Color Default", AVCA_SLUG ) => "", __( "Custom Color", AVCA_SLUG ) => "custom"),
				  	"description" => __( "Choose a color for your percentage text.", AVCA_SLUG ),
				  	"admin_label" => false,
				  	"dependency" => Array( "element" => "check_circular_type", "value" => array( "ani_percentage" ) )
				),

				array(
				  	"type" => "colorpicker",
				  	"heading" => __( "Percentage Animate Text Custom Color", AVCA_SLUG ),
				  	"param_name" => "custom_percentage_color",
				  	"description" => __( "Select custom color for animate percentage text.", AVCA_SLUG ),
				  	"dependency" => Array( "element" => "percentage_color", "value" => array( "custom" ) )
				),

				array(
				  	"type" => "colorpicker",
				  	"heading" => __( "Percentage Text Custom Color", AVCA_SLUG ),
				  	"param_name" => "custom_percentage_text_color",
				  	"value" => "#000000",
				  	"description" => __( "Select custom color for percentage text.", AVCA_SLUG ),
				  	"dependency" => Array( "element" => "percentage_color", "value" => array( "custom" ) )
				),

				// Circular Graph Settings
				array(
				  	"type" => "colorpicker",
				  	"heading" => __( "Circular Bar Color", AVCA_SLUG ),
				  	"param_name" => "circular_bgcolor",
				  	"value" => "#2ABB9B",
				  	"description" => __( "Select custom color for circular bar.", AVCA_SLUG )
				),

				array(
				  	"type" => "colorpicker",
				  	"heading" => __( "Circular Track Color", AVCA_SLUG ),
				  	"param_name" => "circular_trackcolor",
				  	"value" => "#EBEDEF",
				  	"description" => __( "Select custom color of the track for the bar.", AVCA_SLUG )
				),

				array(
				  	"type" => "textfield",
				  	"heading" => __( "Circular Progress Size", AVCA_SLUG ),
				  	"param_name" => "circular_size",
				  	"description" => __( "Enter a number for the size of your circle progress in px. Default size is 170.", AVCA_SLUG ),
				  	"admin_label" => false
				),

				array(
				  	"type" => "textfield",
				  	"heading" => __( "Line Width Circle Progress", AVCA_SLUG ),
				  	"param_name" => "circular_line",
				  	"description" => __( "Enter a number for the width of the bar line in px. Default size is 6.", AVCA_SLUG ),
				  	"admin_label" => false
				),

				$animated_choice,
				$animated_effects,
				$animated_delay,

				array(
				  	"type" => "textfield",
				  	"heading" => __( "Extra class name", AVCA_SLUG ),
				  	"param_name" => "el_class",
				  	"description" => __( "If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", AVCA_SLUG )
				)
			)
		));
	}
	public function build_shortcode( $atts, $content = null ){
		extract(shortcode_atts( array(
			'animation_loading' => '',
			'animation_loading_effects' => '',
			'animation_delay' => '',
			'el_class' => '',
			'check_circular_type' => '',
			'checkicon' => '',
			'icon' => '',
			'icon_color' => '',
			'custom_icon_color' => '',
			'circular_field' => '',
			'field_color' => '',
			'custom_field_color' => '',
			'circular_percentage' => '',
			'circular_percentage_text' => '',
			'percentage_color' => '',
			'custom_percentage_color' => '',
			'custom_percentage_text_color' => '',
			'circular_bgcolor' => '',
			'circular_trackcolor' => '',
			'circular_size' => '',
			'circular_line' => ''
		), $atts ) );

		$el_class = $this->getExtraClass($el_class);

		$animation_loading_class = null;
		if ($animation_loading == "yes") {
			$animation_loading_class = 'animated-content';
		}

		$animation_effect_class = null;
		if ($animation_loading == "yes") {
			$animation_effect_class = $animation_loading_effects;
		} else {
			$animation_effect_class = '';
		}

		$animation_delay_class = null;
		if ($animation_loading == "yes" && !empty($animation_delay)) {
			$animation_delay_class = ' data-delay="'.$animation_delay.'"';
		}

		// Control Size and Line Width of Circle Progress Bar
		if( !empty($circular_size)) {
		  $size_output = $circular_size;
		} else {
		  $size_output = 170;
		}

		if( !empty($circular_line)) {
		  $line_output = $circular_line;
		} else {
		  $line_output = 6;
		}

		// Output
		$circular_output = null;
		$color_icon = null;
		$color_field = null;
		$color_percentage = null;
		$color_text_percentage = null;
		$circular_animate_text_output = null;

		// Check Colors
		if ($icon_color=="custom") { 
			$color_icon = ' style="color: '.$custom_icon_color.';"';  
		}
		if ($field_color=="custom") { 
			$color_field = ' style="color: '.$custom_field_color.';"';  
		}
		if ($percentage_color=="custom") { 
			$color_percentage = ' style="color: '.$custom_percentage_color.';"';  
			$color_text_percentage = ' style="color: '.$custom_percentage_text_color.';"';
		}

		if ($check_circular_type=="field_mode") { 
			$circular_output = '<span class="field-text"'.$color_field.'>'.$circular_field.'</span>';  
		}

		if ($check_circular_type=="ani_percentage") { 
			$circular_output = '<span class="percentage no-field"'.$color_percentage.'>'.$circular_percentage.'</span>';
			$circular_animate_text_output = '<span class="field-animate-text"'.$color_text_percentage.'>'.$circular_percentage_text.'</span>';  
		}

		if ($check_circular_type=="icon_mode" && $checkicon=="custom_icon") { $circular_output = '<span class="field-icon"'.$color_icon.'><i class="'.$icon.'"></i></span>'; }


		$class = setClass(array('progress-circle', $el_class, $animation_loading_class, $animation_effect_class));

		$output .= '<div'.$class.''.$animation_delay_class.'>';
		$output .= '<div class="chart" data-bgcolor="'.$circular_bgcolor.'" data-trackcolor ="'.$circular_trackcolor.'" data-size="'.$size_output.'" data-line="'.$line_output.'" data-percent="'.$circular_percentage.'" style="line-height: '.$size_output.'px;">'.$circular_output.'</div>';
		$output .= $circular_animate_text_output;
		$output .= '</div>';

		echo $output.$this->endBlockComment('az_circular_progress_bar');
	}
}