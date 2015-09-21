<?php
if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly

/*
 * AVCA Module: AVCA Social
 * Description: Social media schortcode
 * Author Name: Nikko Khresna
 * Author URL: https://github.com/nikhresna/
 * Version: 1.0.0
 */

class AvcaSocial extends AvcaModule{

	const slug = 'avca_social';
	const base = 'avca_social';

	public function __construct(){
		add_action( 'vc_before_init', array( $this, 'vc_before_init' ) );
		add_shortcode( self::slug, array( $this, 'build_shortcode' ) );
	}

	public function vc_before_init(){
		vc_map(array(
				"name" => __("AVCA Social", AVCA_SLUG),
			  	"base" => self::base,
			  	"category" => "AVCA",
			  	"params" => array(
					
			  		array(
			  			"type" => "dropdown",
			  			"heading" => __( "Orientation", AVCA_SLUG ),
			  			"param_name" => "orientation",
			  			"description" => __( "Vertical or horizontal list", AVCA_SLUG ),
			  			"group" => __( "Mods", AVCA_SLUG ),
			  			"value" => array(
			  					__( "vertical", AVCA_SLUG ) => "vertical-social",
			  					__( "horizontal", AVCA_SLUG ) => "horizontal-social"
			  				)
			  		),

			  		array(
			  			"type" => "dropdown",
			  			"heading" => __( "Background", AVCA_SLUG ),
			  			"param_name" => "background",
			  			"description" => __( "Background for each icons", AVCA_SLUG ),
			  			"group" => __( "Mods", AVCA_SLUG ),
			  			"value" => array(
			  					__( "circle", AVCA_SLUG ) => "circle",
			  					__( "circle outline", AVCA_SLUG ) => "circle-outline"
			  				)
			  		),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "500PX Profile", AVCA_SLUG ),
					  	"param_name" => "px",
					  	"description" => __( "Please enter in your 500PX URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Add This Profile", AVCA_SLUG ),
					  	"param_name" => "addthis",
					  	"description" => __( "Please enter in your Add This URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Behance Profile", AVCA_SLUG ),
					  	"param_name" => "behance",
					  	"description" => __( "Please enter in your Behance URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Bebo Profile", AVCA_SLUG ),
					  	"param_name" => "bebo",
					  	"description" => __( "Please enter in your Bebo URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Blogger Profile", AVCA_SLUG ),
					  	"param_name" => "blogger",
					  	"description" => __( "Please enter in your Blogger URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Deviant Art Profile", AVCA_SLUG ),
					  	"param_name" => "deviantart",
					  	"description" => __( "Please enter in your Deviant Art URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Digg Profile", AVCA_SLUG ),
					  	"param_name" => "digg",
					  	"description" => __( "Please enter in your Digg URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Dribbble Profile", AVCA_SLUG ),
					  	"param_name" => "dribbble",
					  	"description" => __( "Please enter in your Dribbble URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Email Profile", AVCA_SLUG ),
					  	"param_name" => "email",
					  	"description" => __( "Please enter in your Email URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Envato Profile", AVCA_SLUG ),
					  	"param_name" => "envato",
					  	"description" => __( "Please enter in your Envato URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Evernote Profile", AVCA_SLUG ),
					  	"param_name" => "evernote",
					  	"description" => __( "Please enter in your Evernote URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Facebook Profile", AVCA_SLUG ),
					  	"param_name" => "facebook",
					  	"description" => __( "Please enter in your Facebook URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Flickr Profile", AVCA_SLUG ),
					  	"param_name" => "flickr",
					  	"description" => __( "Please enter in your Flickr URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Forrst Profile", AVCA_SLUG ),
					  	"param_name" => "forrst",
					  	"description" => __( "Please enter in your Forrst URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Github Profile", AVCA_SLUG ),
					  	"param_name" => "github",
					  	"description" => __( "Please enter in your Github URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Google Plus Profile", AVCA_SLUG ),
					  	"param_name" => "googleplus",
					  	"description" => __( "Please enter in your Google Plus URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Grooveshark Profile", AVCA_SLUG ),
					  	"param_name" => "grooveshark",
					  	"description" => __( "Please enter in your Grooveshark URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Instagram Profile", AVCA_SLUG ),
					  	"param_name" => "instagram",
					  	"description" => __( "Please enter in your Instagram URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Last Fm Profile", AVCA_SLUG ),
					  	"param_name" => "lastfm",
					  	"description" => __( "Please enter in your Last Fm URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Linked In Profile", AVCA_SLUG ),
					  	"param_name" => "linkedin",
					  	"description" => __( "Please enter in your Linked In URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "My Space Profile", AVCA_SLUG ),
					  	"param_name" => "myspace",
					  	"description" => __( "Please enter in your My Space URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "PayPal Profile", AVCA_SLUG ),
					  	"param_name" => "paypal",
					  	"description" => __( "Please enter in your PayPal URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Photobucket Profile", AVCA_SLUG ),
					  	"param_name" => "photobucket",
					  	"description" => __( "Please enter in your Photobucket URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Pinterest Profile", AVCA_SLUG ),
					  	"param_name" => "pinterest",
					  	"description" => __( "Please enter in your Pinterest URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Quora Profile", AVCA_SLUG ),
					  	"param_name" => "quora",
					  	"description" => __( "Please enter in your Quora URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Share This Profile", AVCA_SLUG ),
					  	"param_name" => "sharethis",
					  	"description" => __( "Please enter in your Share This URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Skype Profile", AVCA_SLUG ),
					  	"param_name" => "skype",
					  	"description" => __( "Please enter in your Skype URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Soundcloud Profile", AVCA_SLUG ),
					  	"param_name" => "soundcloud",
					  	"description" => __( "Please enter in your Soundcloud URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "StumbleUpon Profile", AVCA_SLUG ),
					  	"param_name" => "stumbleupon",
					  	"description" => __( "Please enter in your StumbleUpon URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Tumblr Profile", AVCA_SLUG ),
					  	"param_name" => "tumblr",
					  	"description" => __( "Please enter in your Tumblr URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Twitter Profile", AVCA_SLUG ),
					  	"param_name" => "twitter",
					  	"description" => __( "Please enter in your Twitter URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Viddler Profile", AVCA_SLUG ),
					  	"param_name" => "viddler",
					  	"description" => __( "Please enter in your Viddler URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Vimeo Profile", AVCA_SLUG ),
					  	"param_name" => "vimeo",
					  	"description" => __( "Please enter in your Vimeo URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Virb Profile", AVCA_SLUG ),
					  	"param_name" => "virb",
					  	"description" => __( "Please enter in your Virb URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Wordpress Profile", AVCA_SLUG ),
					  	"param_name" => "wordpress",
					  	"description" => __( "Please enter in your Wordpress URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Yahoo Profile", AVCA_SLUG ),
					  	"param_name" => "yahoo",
					  	"description" => __( "Please enter in your Yahoo URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Yelp Profile", AVCA_SLUG ),
					  	"param_name" => "yelp",
					  	"description" => __( "Please enter in your Yelp URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "YouTube Profile", AVCA_SLUG ),
					  	"param_name" => "youtube",
					  	"description" => __( "Please enter in your You Tube URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array( 
					  	"type" => "textfield",
					  	"heading" => __( "Zerply Profile", AVCA_SLUG ),
					  	"param_name" => "zerply",
					  	"description" => __( "Please enter in your Zerply URL.", AVCA_SLUG ),
					  	"value" => ""
					),

					array(
				      	"type" => "textfield",
				      	"heading" => __( "Extra class name", AVCA_SLUG ),
				      	"param_name" => "el_class",
				      	"description" => __( "If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", AVCA_SLUG )
					    )
				  	)
				));
	}
	
	public function build_shortcode( $atts, $content = null ) {		

		$output = $el_class = '';

		extract(shortcode_atts( array(
			'el_class' => '',
			'orientation' => 'vertical-social'
		), $atts ) );

		// $el_class = $this->getExtraClass($el_class);

		// $class = setClass(array('avca-social', $el_class));
		
		$output .= '<div class="'.$el_class.' '.$orientation.'">
					<ul class="social-icons">';

		if( isset( $atts['px'] ) )
		$output .= '<li><a href="' . $atts['px'] . '" target="_blank"><i class="font-icon-social-500px"></i></a></li>';

		if( isset( $atts['addthis'] ) )
		$output .= '<li><a href="' . $atts['addthis'] . '" target="_blank"><i class="font-icon-social-addthis"></i></a></li>';

		if( isset( $atts['behance'] ) )
		$output .= '<li><a href="' . $atts['behance'] . '" target="_blank"><i class="font-icon-social-behance"></i></a></li>';

		if( isset( $atts['bebo'] ) )
		$output .= '<li><a href="' . $atts['bebo'] . '" target="_blank"><i class="font-icon-social-bebo"></i></a></li>';

		if( isset( $atts['blogger'] ) )
		$output .= '<li><a href="' . $atts['blogger'] . '" target="_blank"><i class="font-icon-social-blogger"></i></a></li>';

		if( isset( $atts['deviantart'] ) )
		$output .= '<li><a href="' . $atts['deviantart'] . '" target="_blank"><i class="font-icon-social-deviant-art"></i></a></li>';

		if( isset( $atts['digg'] ) )
		$output .= '<li><a href="' . $atts['digg'] . '" target="_blank"><i class="font-icon-social-digg"></i></a></li>';

		if( isset( $atts['dribbble'] ) )
		$output .= '<li><a href="' . $atts['dribbble'] . '" target="_blank"><i class="font-icon-social-dribbble"></i></a></li>';

		if( isset( $atts['email'] ) )
		$output .= '<li><a href="' . $atts['email'] . '" target="_blank"><i class="font-icon-social-email"></i></a></li>';

		if( isset( $atts['envato'] ) )
		$output .= '<li><a href="' . $atts['envato'] . '" target="_blank"><i class="font-icon-social-envato"></i></a></li>';

		if( isset( $atts['evernote'] ) )
		$output .= '<li><a href="' . $atts['evernote'] . '" target="_blank"><i class="font-icon-social-evernote"></i></a></li>';

		if( isset( $atts['facebook'] ) )
		$output .= '<li><a href="' . $atts['facebook'] . '" target="_blank"><i class="font-icon-social-facebook"></i></a></li>';

		if( isset( $atts['flickr'] ) )
		$output .= '<li><a href="' . $atts['flickr'] . '" target="_blank"><i class="font-icon-social-flickr"></i></a></li>';

		if( isset( $atts['forrst'] ) )
		$output .= '<li><a href="' . $atts['forrst'] . '" target="_blank"><i class="font-icon-social-forrst"></i></a></li>';

		if( isset( $atts['github'] ) )
		$output .= '<li><a href="' . $atts['github'] . '" target="_blank"><i class="font-icon-social-github"></i></a></li>';

		if( isset( $atts['googleplus'] ) )
		$output .= '<li><a href="' . $atts['googleplus'] . '" target="_blank"><i class="font-icon-social-google-plus"></i></a></li>';

		if( isset( $atts['grooveshark'] ) )
		$output .= '<li><a href="' . $atts['grooveshark'] . '" target="_blank"><i class="font-icon-social-grooveshark"></i></a></li>';

		if( isset( $atts['instagram'] ) )
		$output .= '<li><a href="' . $atts['instagram'] . '" target="_blank"><i class="font-icon-social-instagram"></i></a></li>';

		if( isset( $atts['lastfm'] ) )
		$output .= '<li><a href="' . $atts['lastfm'] . '" target="_blank"><i class="font-icon-social-last-fm"></i></a></li>';

		if( isset( $atts['linkedin'] ) )
		$output .= '<li><a href="' . $atts['linkedin'] . '" target="_blank"><i class="font-icon-social-linkedin"></i></a></li>';

		if( isset( $atts['myspace'] ) )
		$output .= '<li><a href="' . $atts['myspace'] . '" target="_blank"><i class="font-icon-social-myspace"></i></a></li>';

		if( isset( $atts['paypal'] ) )
		$output .= '<li><a href="' . $atts['paypal'] . '" target="_blank"><i class="font-icon-social-paypal"></i></a></li>';

		if( isset( $atts['photobucket'] ) )
		$output .= '<li><a href="' . $atts['photobucket'] . '" target="_blank"><i class="font-icon-social-photobucket"></i></a></li>';

		if( isset( $atts['pinterest'] ) )
		$output .= '<li><a href="' . $atts['pinterest'] . '" target="_blank"><i class="font-icon-social-pinterest"></i></a></li>';

		if( isset( $atts['quora'] ) )
		$output .= '<li><a href="' . $atts['quora'] . '" target="_blank"><i class="font-icon-social-quora"></i></a></li>';

		if( isset( $atts['sharethis'] ) )
		$output .= '<li><a href="' . $atts['sharethis'] . '" target="_blank"><i class="font-icon-social-share-this"></i></a></li>';

		if( isset( $atts['skype'] ) )
		$output .= '<li><a href="' . $atts['skype'] . '" target="_blank"><i class="font-icon-social-skype"></i></a></li>';

		if( isset( $atts['soundcloud'] ) )
		$output .= '<li><a href="' . $atts['soundcloud'] . '" target="_blank"><i class="font-icon-social-soundcloud"></i></a></li>';

		if( isset( $atts['stumbleupon'] ) )
		$output .= '<li><a href="' . $atts['stumbleupon'] . '" target="_blank"><i class="font-icon-social-stumbleupon"></i></a></li>';

		if( isset( $atts['tumblr'] ) )
		$output .= '<li><a href="' . $atts['tumblr'] . '" target="_blank"><i class="font-icon-social-tumblr"></i></a></li>';

		if( isset( $atts['twitter'] ) )
		$output .= '<li><a href="' . $atts['twitter'] . '" target="_blank"><i class="font-icon-social-twitter"></i></a></li>';

		if( isset( $atts['viddler'] ) )
		$output .= '<li><a href="' . $atts['viddler'] . '" target="_blank"><i class="font-icon-social-viddler"></i></a></li>';

		if( isset( $atts['vimeo'] ) )
		$output .= '<li><a href="' . $atts['vimeo'] . '" target="_blank"><i class="font-icon-social-vimeo"></i></a></li>';

		if( isset( $atts['virb'] ) )
		$output .= '<li><a href="' . $atts['virb'] . '" target="_blank"><i class="font-icon-social-virb"></i></a></li>';

		if( isset( $atts['wordpress'] ) )
		$output .= '<li><a href="' . $atts['wordpress'] . '" target="_blank"><i class="font-icon-social-wordpress"></i></a></li>';

		if( isset( $atts['yahoo'] ) )
		$output .= '<li><a href="' . $atts['yahoo'] . '" target="_blank"><i class="font-icon-social-yahoo"></i></a></li>';

		if( isset( $atts['yelp'] ) )
		$output .= '<li><a href="' . $atts['yelp'] . '" target="_blank"><i class="font-icon-social-yelp"></i></a></li>';

		if( isset( $atts['youtube'] ) )
		$output .= '<li><a href="' . $atts['youtube'] . '" target="_blank"><i class="font-icon-social-youtube"></i></a></li>';

		if( isset( $atts['zerply'] ) )
		$output .= '<li><a href="' . $atts['zerply'] . '" target="_blank"><i class="font-icon-social-zerply"></i></a></li>';

		$output .= '</ul>
					</div>';


		echo $output;
	}
// ------------------
// class end
}

new AvcaSocial();