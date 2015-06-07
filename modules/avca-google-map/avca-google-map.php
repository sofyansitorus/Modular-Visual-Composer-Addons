<?php
if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly

/*
 * Name: AVCA Google Map
 * Description: Advanced google map shortcode for Visual Composer
 * Author Name: Sofyan Sitorus
 * Autor URL: https://github.com/sofyansitorus/
 * Version: 1.0.2
 */

class AvcaGoogleMap extends AvcaModule{

	const slug = 'avca_google_map';
	const base = 'avca_google_map';

	private $load_css;
	private $load_map_api_js;
	private $load_infobox_js;
	

	public function __construct(){
		$this->set_options();
		add_action( 'admin_init', array($this, 'admin_init') );
		add_action( 'admin_menu', array($this, 'admin_menu') );
		add_filter( 'avca_module_row_actions', array( $this, 'add_row_actions' ), 10, 2 );
		add_action( 'vc_before_init', array( $this, 'vc_before_init' ) );
		add_action( 'the_posts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( self::slug, array( $this, 'build_shortcode' ) );
	}

	private function set_options(){
		if( FALSE === get_option( 'avca_google_map_install_time' ) ){

			update_option( 'avca_google_map_install_time', current_time('timestamp') );

			update_option( 'avca_google_map_load_css', 1 );
			$this->load_css = 1;

			update_option( 'avca_google_map_load_map_api_js', 1 );
			$this->load_map_api_js = 1;

			update_option( 'avca_google_map_load_infobox_js', 1 );
			$this->load_infobox_js = 1;

		}else{

			$this->load_css = get_option( 'avca_google_map_load_css', 1 );
			$this->load_map_api_js = get_option( 'avca_google_map_load_map_api_js', 1 );
			$this->load_infobox_js = get_option( 'avca_google_map_load_infobox_js', 1 );
		
		}
	}

	public function vc_before_init(){
		vc_map( array(
			"name" => __( "AVCA Google Map", AVCA_SLUG ),
			"base" => self::base,
			"class" => "",
			"category" => "AVCA",
			"params" => array(
				array(
					'type' => 'textfield',
					'heading' => __('Latitude', AVCA_SLUG),
					'param_name' => 'lat',
					'admin_label' => true,
					'value' => '-6.175392',
					'description' => sprintf(__('<a href="%s" target="_blank">Click here</a> to find Latitude of your location.', AVCA_SLUG), 'http://www.mapcoordinates.net/en'),
					'group' => __('General Settings', AVCA_SLUG)
				),
				array(
					'type' => 'textfield',
					'heading' => __('Longitude', AVCA_SLUG),
					'param_name' => 'lng',
					'admin_label' => true,
					'value' => '106.827153',
					'description' => sprintf(__('<a href="%s" target="_blank">Click here</a> to find Longitude of your location.', AVCA_SLUG), 'http://www.mapcoordinates.net/en'),
					'group' => __('General Settings', AVCA_SLUG)
				),
				array(
					'type' => 'dropdown',
					'heading' => __('Map Zoom Level', AVCA_SLUG),
					'param_name' => 'zoom',
					'admin_label' => true,
					'value' => $this->get_values_zoom(),
					'std' => 16,
					'group' => __('General Settings', AVCA_SLUG)
				),
				array(
					'type' => 'textfield',
					'heading' => __('Width', AVCA_SLUG),
					'description' => __('Set your map width in % or px. Default is %.', AVCA_SLUG),
					'param_name' => 'width',
					'admin_label' => true,
					'value' => '100%',
					'group' => __('General Settings', AVCA_SLUG)
				),
				array(
					'type' => 'textfield',
					'heading' => __('Height', AVCA_SLUG),
					'description' => __('Set your map height in % or px. Default is px.', AVCA_SLUG),
					'param_name' => 'height',
					'admin_label' => true,
					'value' => '300px',
					'group' => __('General Settings', AVCA_SLUG)
				),
				array(
					'type' => 'dropdown',
					'heading' => __('Map Type', AVCA_SLUG),
					'param_name' => 'map_type',
					'admin_label' => true,
					'value' => array(__('Roadmap', AVCA_SLUG) => 'ROADMAP', __('Satellite', AVCA_SLUG) => 'SATELLITE', __('Hybrid', AVCA_SLUG) => 'HYBRID', __('Terrain', AVCA_SLUG) => 'TERRAIN'),
					'group' => __('General Settings', AVCA_SLUG)
				),
				array(
					'type' => 'textarea_raw_html',
					'class' => '',
					'heading' => __('Google Styled Map JSON', AVCA_SLUG),
					'param_name' => 'map_style',
					'value' => '',
					'description' => sprintf(__('<a href="%s" target="_blank">Click here</a> to get the style JSON code for styling your map.', AVCA_SLUG), 'https://snazzymaps.com/'),
					'dependency' => Array('element' => 'map_type','value' => array('ROADMAP')),
					'group' => __('General Settings', AVCA_SLUG)
				),
				array(
					'type' => 'dropdown',
					'heading' => __('Type Control', AVCA_SLUG),
					'param_name' => 'maptypecontrol',
					'value' => array(__('Disable', AVCA_SLUG) => 'false', __('Enable', AVCA_SLUG) => 'true'),
					'group' => __('Map Control', AVCA_SLUG)
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __('Pan Control', AVCA_SLUG),
					'param_name' => 'pancontrol',
					'value' => array(__('Disable', AVCA_SLUG) => 'false', __('Enable', AVCA_SLUG) => 'true'),
					'group' => __('Map Control', AVCA_SLUG)
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __('Street View Control', AVCA_SLUG),
					'param_name' => 'streetviewcontrol',
					'value' => array(__('Disable', AVCA_SLUG) => 'false', __('Enable', AVCA_SLUG) => 'true'),
					'group' => __('Map Control', AVCA_SLUG)
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __('Drag Control', AVCA_SLUG),
					'param_name' => 'draggable',
					'value' => array(__('Disable', AVCA_SLUG) => 'false', __('Enable', AVCA_SLUG) => 'true'),
					'group' => __('Map Control', AVCA_SLUG)
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __('Zoom Level Control', AVCA_SLUG),
					'param_name' => 'zoomcontrol',
					'value' => array(__('Disable', AVCA_SLUG) => 'false', __('Enable', AVCA_SLUG) => 'true'),
					'group' => __('Map Control', AVCA_SLUG)
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __('Scrollwheel Zooming', AVCA_SLUG),
					'param_name' => 'scrollwheel',
					'value' => array(__('Disable', AVCA_SLUG) => 'false', __('Enable', AVCA_SLUG) => 'true'),
					'dependency' => Array('element' => 'zoomcontrol','value' => array('true')),
					'group' => __('Map Control', AVCA_SLUG)
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __('Icon', AVCA_SLUG),
					'param_name' => 'marker',
					'value' => array(__('Default', AVCA_SLUG) => 'default', __('Custom', AVCA_SLUG) => 'custom'),
					'group' => __('Marker', AVCA_SLUG)
				),
				array(
					'type' => 'attach_image',
					'class' => '',
					'param_name' => 'marker_custom_icon',
					'value' => '',
					'description' => __('Upload the custom marker icon.', AVCA_SLUG),
					'dependency' => Array('element' => 'marker','value' => array('custom')),
					'group' => __('Marker', AVCA_SLUG)
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __('Animation', AVCA_SLUG),
					'param_name' => 'marker_animation',
					'value' => array(__('Disabled', AVCA_SLUG) => '', __('Bounce', AVCA_SLUG) => 'BOUNCE', __('Drop', AVCA_SLUG) => 'DROP'),
					'group' => __('Marker', AVCA_SLUG)
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __('On Click Event', AVCA_SLUG),
					'param_name' => 'marker_onclick',
					'value' => array(
						__('Disabled', AVCA_SLUG) => '', 
						__('Toggle Info Window', AVCA_SLUG) => 'toggle_infowindow',
						__('Redirect to URL', AVCA_SLUG) => 'enabled_redirect'
					),
					'group' => __('Marker', AVCA_SLUG)
				),
				array(
					'type' => 'textfield',
					'heading' => __('Redirect URL', AVCA_SLUG),
					'param_name' => 'redirect_url',
					'value' => '#',
					'group' => __('Marker', AVCA_SLUG),
					'dependency' => array(
						'element' => 'marker_onclick',
						'value' => array('enabled_redirect')
					),
				),
				array(
					'type' => 'textarea_html',
					'heading' => __('Content', AVCA_SLUG),
					'param_name' => 'content',
					'value' => '',
					'dependency' => array(
						'element' => 'marker_onclick',
						'value' => array('disabled_infowindow', 'toggle_infowindow')
					),
					'group' => __('Info Window', AVCA_SLUG)
				),
				array(
					'type' => 'textfield',
					'heading' => __('Width', AVCA_SLUG),
					'description' => __('Set your info window width in px.', AVCA_SLUG),
					'param_name' => 'info_window_width',
					'admin_label' => true,
					'value' => '200px',
					'group' => __('Info Window', AVCA_SLUG),
					'dependency' => array(
						'element' => 'marker_onclick',
						'value' => array('disabled_infowindow', 'toggle_infowindow')
					)
				),
				array(
					'type' => 'textfield',
					'heading' => __('Height', AVCA_SLUG),
					'description' => __('Set your info window height in px.', AVCA_SLUG),
					'param_name' => 'info_window_height',
					'admin_label' => true,
					'value' => '80px',
					'group' => __('Info Window', AVCA_SLUG),
					'dependency' => array(
						'element' => 'marker_onclick',
						'value' => array('disabled_infowindow', 'toggle_infowindow')
					)
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __('Styling', AVCA_SLUG),
					'param_name' => 'info_window_type',
					'value' => array(
						__('Default', AVCA_SLUG) => 'default', 
						__('Custom', AVCA_SLUG) => 'custom'
					),
					'dependency' => array(
						'element' => 'marker_onclick',
						'value' => array('disabled_infowindow', 'toggle_infowindow')
					),
					'group' => __('Info Window', AVCA_SLUG)
				),
				array(
					'type' => 'dropdown',
					'class' => '',
					'heading' => __('Arrow', AVCA_SLUG),
					'param_name' => 'info_window_arrow',
					'value' => array(
						__('Disabled', AVCA_SLUG) => '', 
						__('Top', AVCA_SLUG) => 'top',
						__('Right', AVCA_SLUG) => 'right',
						__('Bottom', AVCA_SLUG) => 'bottom',
						__('Left', AVCA_SLUG) => 'left'
					),
					'dependency' => array(
						'element' => 'info_window_type',
						'value' => array('custom')
					),
					'group' => __('Info Window', AVCA_SLUG)
				),
				array(
		            'type' => 'colorpicker',
		            'heading' => __('Arrow Color', AVCA_SLUG),
		            'param_name' => 'info_window_arrow_color',
		            'group' => __( 'Info Window', AVCA_SLUG),
					'dependency' => array(
						'element' => 'info_window_arrow',
						'value' => array('top','right','bottom','left')
					)
		        ),
				array(
					'type' => 'textfield',
					'heading' => __('Arrow Size', AVCA_SLUG),
					'description' => __('Set arrow size in px unit. Default is 30px.', AVCA_SLUG),
					'param_name' => 'info_window_arrow_size',
					'admin_label' => true,
					'value' => '30px',
					'group' => __('Info Window', AVCA_SLUG),
					'dependency' => array(
						'element' => 'info_window_arrow',
						'value' => array('top','right','bottom','left')
					)
				),
				array(
		            'type' => 'css_editor',
		            'heading' => '',
		            'param_name' => 'info_window_class',
		            'group' => __( 'Info Window', AVCA_SLUG),
					'dependency' => array(
						'element' => 'info_window_type',
						'value' => array('custom')
					),
		        )
			)
		) );
	}

	public function enqueue_scripts($posts) {
	    if ( empty($posts) )
	        return $posts;

	    // false because we have to search through the posts first
	    $found = false;

	    // search through each post
	    foreach ($posts as $post) {
	    	if ( has_shortcode( $post->post_content, self::slug ) ) {
	            // we have found a post with the short code
	            $found = true;
	            // stop the search
	            break;
	    	}
		}

	    if ($found){
	    	if($this->load_css){
				wp_enqueue_style( 'avca-google-map', AVCA_URL . 'modules/avca-google-map/assets/css/avca-google-map.css' );
			}
	    	if($this->load_map_api_js){
				wp_enqueue_script( 'googlemap', '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', array(), '3.0', false );
			}
	    	if($this->load_infobox_js){
				wp_enqueue_script( 'infobox', AVCA_URL . 'modules/avca-google-map/assets/js/infobox.js', array(), '3.0', false );
			}
			wp_enqueue_script( 'avca-google-map', AVCA_URL . 'modules/avca-google-map/assets/js/avca-google-map.js', array('jquery'), '3.0', false );
	    }
	    return $posts;
	}

	public function build_shortcode( $atts, $content = null ){	
					
		extract(shortcode_atts(array(
			'lat' => '-6.175392',
			'lng' => '106.827153',
			'zoom' => '16',
			'width' => '100%',
			'height' => '300px',
			'map_type' => 'ROADMAP',
			'map_style' => '',
			'streetviewcontrol' => 'false',
			'maptypecontrol' => 'false',
			'pancontrol' => 'false',
			'zoomcontrol' => 'false',
			'scrollwheel' => 'false',
			'draggable' => 'false',
			'marker' => 'default',
			'marker_custom_icon' => '',
			'marker_animation' => '',
			'marker_onclick' => '',
			'redirect_url' => '#',
			'info_window_text' => '',
			'info_window_width' => '200px',
			'info_window_height' => '80px',
			'info_window_type' => '',
			'info_window_arrow' => '',
			'info_window_arrow_color' => '#000000',
			'info_window_arrow_size' => '30px',
			'info_window_class' => ''
		), $atts));

		$output = '';

		$width = (substr($width, -1) != '%' && substr($width, -2)!='px' ? $width . '%' : $width);
		$height = (substr($height, -1) != '%' && substr($height, -2)!='px' ? $height . 'px' : $height);

		if($marker == "default"){
			$marker_custom_icon = '';
		}else{
			$attachment_image_src = wp_get_attachment_image_src( $marker_custom_icon, 'full');
			if(isset($attachment_image_src[0])){
				$marker_custom_icon = $attachment_image_src[0];
			}
		}

		$info_window_class = apply_filters( 
			VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 
			vc_shortcode_custom_css_class( $info_window_class, '' ), 
			self::slug, 
			$atts
		);

		$map_id = $this->generate_map_id();

		$output .= '<div id="'.$map_id.'" class="avca-google-map-wrapper" style="width:'.$width.';height:'.$height.';"></div>'."\n";
		$output .= '<script type="text/javascript">'."\n";
		$output .= '(function($) {'."\n";

		/**
		 * Initialize Map
		 */
		$args_map = array();
		$args_map['coords'] = array('value' => '['.$lat.', '.$lng.']', 'type' => 'array');
		if($map_style){
			$args_map['map_style'] = array('value' => rawurldecode(base64_decode($map_style)), 'type' => 'array');
		}
		$args_map['map_type'] = array('value' => $map_type);
		$args_map['mapTypeControl'] = array('value' => $maptypecontrol, 'type' => 'boolean');
		$args_map['panControl'] = array('value' => $pancontrol, 'type' => 'boolean');
		$args_map['streetViewControl'] = array('value' => $streetviewcontrol, 'type' => 'boolean');
		$args_map['zoom'] = array('value' => $zoom, 'type' => 'integer');
		$args_map['zoomControl'] = array('value' => $zoomcontrol, 'type' => 'boolean');
		$args_map['scrollwheel'] = array('value' => $scrollwheel, 'type' => 'boolean');
		$args_map['draggable'] = array('value' => $draggable, 'type' => 'boolean');				
		
		$output .= '$("#'.$map_id.'").AVCA_GoogleMap({'."\n";
		$output .= $this->render_jquery_options($args_map);
		$output .= '});'."\n";

		/**
		 * Add Marker
		 */
		$args_marker = array();
		$args_marker['coords'] = array('value' => '['.$lat.', '.$lng.']', 'type' => 'array');
		if($marker_custom_icon){
			$args_marker['icon'] = array('value' => $marker_custom_icon);
		}
		$args_marker['animation'] = array('value' => $marker_animation);
		$args_marker['marker_onclick'] = array('value' => $marker_onclick);
		$args_marker['redirect_url'] = array('value' => $redirect_url);

		$content = wpb_js_remove_wpautop( $content, true );
		
		if($content){
			$args_marker['info_window_text'] = array('value' => addslashes(preg_replace('#\R+#', '', $content)));
			$args_marker['info_window_width'] = array('value' => $info_window_width);
			$args_marker['info_window_height'] = array('value' => $info_window_height);
			$args_marker['info_window_type'] = array('value' => $info_window_type);
			$args_marker['info_window_arrow'] = array('value' => $info_window_arrow);
			$args_marker['info_window_class'] = array('value' => $info_window_class);
		}

		$output .= '$("#'.$map_id.'").AVCA_AddMapMarker({'."\n";
		$output .= $this->render_jquery_options($args_marker);
		$output .= '});'."\n";

		$output .= '})(jQuery)'."\n";
		$output .= '</script>'."\n";

		if($info_window_arrow){
			$output .= '<style type="text/css">'."\n";
			$output .= '.info-window-custom.has-arrow:after{'."\n";
			$output .= 'border-width: '.$info_window_arrow_size.';'."\n";
			$output .= '}'."\n";
			$output .= '.info-window-custom.has-arrow.'.$info_window_arrow.':after{'."\n";
			switch ($info_window_arrow) {
				case 'top':
					$output .= 'bottom: 100%;'."\n";
					$output .= 'left: 50%;'."\n";
					$output .= 'margin-left: -'.$info_window_arrow_size.';'."\n";
					$output .= 'border-bottom-color: '.$info_window_arrow_color.';'."\n";
					break;

				case 'right':
					$output .= 'left: 100%;'."\n";
					$output .= 'top: 50%;'."\n";
					$output .= 'margin-top: -'.$info_window_arrow_size.';'."\n";
					$output .= 'border-left-color: '.$info_window_arrow_color.';'."\n";
					break;

				case 'bottom':
					$output .= 'top: 100%;'."\n";
					$output .= 'left: 50%;'."\n";
					$output .= 'margin-left: -'.$info_window_arrow_size.';'."\n";
					$output .= 'border-top-color: '.$info_window_arrow_color.';'."\n";
					break;

				case 'left':
					$output .= 'right: 100%;'."\n";
					$output .= 'top: 50%;'."\n";
					$output .= 'margin-top: -'.$info_window_arrow_size.';'."\n";
					$output .= 'border-right-color: '.$info_window_arrow_color.';'."\n";
					break;
				
				default:
					# code...
					break;
			}
			
			$output .= '}'."\n";
			$output .= '</style>'."\n";
		}

		return $output;
	}

	private function generate_map_id($len = 20){
		$result = '';
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$charArray = str_split($chars);
		for($i = 0; $i < $len; $i++){
			$randItem = array_rand($charArray);
			$result .= $charArray[$randItem];
		}
		return $result;
	}

	private function get_values_zoom(){
		$values = array();
		for ($i=20; $i >= 1 ; $i--) { 
			$values[$i] = $i;
		}
		return $values;
	}

	public function add_row_actions($actions, $module){
		if('avca-google-map' == $module){
			$actions[] = $this->create_row_actions_link(
				'setting', 
				add_query_arg( 
					array(
						'page' => self::slug,
					),
					admin_url( 'admin.php' )
				), 
				__('Settings', AVCA_SLUG)
			);
		}
		return $actions;
	}

	function admin_menu(  ) { 
		add_submenu_page( 
			NULL, 
			__('AVCA Google Map Module', AVCA_SLUG), 
			__('AVCA Google Map Module', AVCA_SLUG), 
			'manage_options', 
			self::slug, 
			array($this, 'avca_google_map_admin_page') 
		);
	}


	function avca_google_map_admin_page(  ) { 
		?>
		<form action='options.php' method='post'>
			<h2><?php _e('AVCA Google Map Module', AVCA_SLUG); ?></h2>
			<?php
			settings_fields( self::slug );
			do_settings_sections( self::slug );
			submit_button();
			?>
		</form>
		<?php
	}


	function admin_init(  ) { 

		add_settings_section(
			'css_load_settings', 
			__( 'CSS File Settings', 'ocon' ), 
			array($this, 'settings_section_callback'), 
			self::slug
		);

		register_setting( self::slug, 'avca_google_map_load_css' );

		add_settings_field( 
			'avca_google_map_load_css', 
			__( 'Load Built-in CSS', 'ocon' ), 
			array($this, 'settings_field_callback'), 
			self::slug, 
			'css_load_settings',
			array(
				'name' => 'avca_google_map_load_css',
				'type' => 'checkbox'
			)
		);

		add_settings_section(
			'js_load_settings', 
			__( 'Javascript File Settings', 'ocon' ), 
			array($this, 'settings_section_callback'), 
			self::slug
		);

		register_setting( self::slug, 'avca_google_map_load_map_api_js' );

		add_settings_field( 
			'avca_google_map_load_map_api_js', 
			__( 'Load Google Map API JS', 'ocon' ), 
			array($this, 'settings_field_callback'), 
			self::slug, 
			'js_load_settings',
			array(
				'name' => 'avca_google_map_load_map_api_js',
				'type' => 'checkbox'
			)
		);

		register_setting( self::slug, 'avca_google_map_load_infobox_js' );

		add_settings_field( 
			'avca_google_map_load_infobox_js', 
			__( 'Load InfoBox JS', 'ocon' ), 
			array($this, 'settings_field_callback'), 
			self::slug, 
			'js_load_settings',
			array(
				'name' => 'avca_google_map_load_infobox_js',
				'type' => 'checkbox'
			)
		);
	}
}

new AvcaGoogleMap();