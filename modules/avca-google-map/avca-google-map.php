<?php
if(!class_exists('AVCA_Google_Map')){
	class AVCA_Google_Map extends AVCA{

		private $base = 'avca_google_map';

		function __construct(){
			add_action('init',array($this,'init'));
			add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'),1);
			add_shortcode($this->base,array($this,'build_shortcode'));
		}

		function enqueue_scripts(){
			global $wp_scripts;
			$google_map_api_registered = false;
			foreach ($wp_scripts as $registered_script) {
				if(strpos($registered_script->src, 'maps.googleapis.com') !== FALSE){
					$google_map_api_registered = true;
					break;
				}
			}
			$this->register_enqueue_styles('avca-google-map', 'modules/avca-google-map/assets/css/avca-google-map.css');
			if(!$google_map_api_registered){
				$this->register_enqueue_scripts('maps.googleapis.com','//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false',array('jquery'),'3.0',false);
			}
			$this->register_enqueue_scripts('infobox', 'modules/avca-google-map/assets/js/infobox.js',array('jquery', 'maps.googleapis.com'),'3.0',false);
			$this->register_enqueue_scripts('avca-google-map', 'modules/avca-google-map/assets/js/avca-google-map.js',array('jquery', 'maps.googleapis.com'),'3.0',false);
		}
		
		function init(){
			vc_map( array(
				'name' => __('AVCA Google Map', self::slug),
				'base' => $this->base,
				'class' => $this->base,
				'category' => self::category,
				'controls' => 'full',
				'show_settings_on_create' => true,
				'icon' => '',
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __('Latitude', self::slug),
						'param_name' => 'lat',
						'admin_label' => true,
						'value' => '',
						'description' => sprintf(__('<a href="%s" target="_blank">Click here</a> to find Latitude of your location', self::slug), 'http://www.mapcoordinates.net/en'),
						'group' => __('General Settings', self::slug)
					),
					array(
						'type' => 'textfield',
						'heading' => __('Longitude', self::slug),
						'param_name' => 'lng',
						'admin_label' => true,
						'value' => '',
						'description' => sprintf(__('<a href="%s" target="_blank">Click here</a> to find Longitude of your location', self::slug), 'http://www.mapcoordinates.net/en'),
						'group' => __('General Settings', self::slug)
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Map Zoom Level', self::slug),
						'param_name' => 'zoom',
						'admin_label' => true,
						'value' => $this->get_values_zoom(),
						'group' => __('General Settings', self::slug)
					),
					array(
						'type' => 'dropdown',
						'class' => '',
						'heading' => __('Marker Icon', self::slug),
						'param_name' => 'marker',
						'value' => array(__('Default', self::slug) => 'default', __('Custom', self::slug) => 'custom'),
						'group' => __('Marker', self::slug)
					),
					array(
						'type' => 'attach_image',
						'class' => '',
						'param_name' => 'marker_custom_icon',
						'value' => '',
						'description' => __('Upload the custom marker icon.', self::slug),
						'dependency' => Array('element' => 'marker','value' => array('custom')),
						'group' => __('Marker', self::slug)
					),
					array(
						'type' => 'dropdown',
						'class' => '',
						'heading' => __('Marker Icon Animation', self::slug),
						'param_name' => 'marker_animation',
						'value' => array(__('Disabled', self::slug) => '', __('Bounce', self::slug) => 'BOUNCE', __('Drop', self::slug) => 'DROP'),
						'group' => __('Marker', self::slug)
					),
					array(
						'type' => 'dropdown',
						'class' => '',
						'heading' => __('Marker Icon Click', self::slug),
						'param_name' => 'marker_click',
						'value' => array(__('Do Nothing', self::slug) => '', __('Redirect to URL', self::slug) => 'url', __('Show Info Window', self::slug) => 'info_window'),
						'group' => __('Marker', self::slug)
					),
					array(
						'type' => 'vc_link',
						'param_name' => 'url',
						'value' => '',
						'dependency' => Array('element' => 'marker_click','value' => array('url')),
						'group' => __('Marker', self::slug)
					),
					array(
						'type' => 'dropdown',
						'class' => '',
						'heading' => __('Type', self::slug),
						'param_name' => 'info_window_type',
						'value' => array(__('Default', self::slug) => 'default', __('Custom', self::slug) => 'custom'),
						'dependency' => Array('element' => 'marker_click','value' => array('info_window')),
						'group' => __('Info Window', self::slug)
					),
					array(
						'type' => 'textfield',
						'heading' => __('Heading', self::slug),
						'param_name' => 'info_window_heading',
						'value' => '',
						'dependency' => Array('element' => 'marker_click','value' => array('info_window')),
						'group' => __('Info Window', self::slug)
					),
					array(
						'type' => 'textarea',
						'heading' => __('Address', self::slug),
						'param_name' => 'info_window_address',
						'value' => '',
						'dependency' => Array('element' => 'marker_click','value' => array('info_window')),
						'group' => __('Info Window', self::slug)
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Map Type Control', self::slug),
						'param_name' => 'maptypecontrol',
						'value' => array(__('Disable', self::slug) => 'false', __('Enable', self::slug) => 'true'),
						'group' => __('Map Control', self::slug)
					),
					array(
						'type' => 'dropdown',
						'class' => '',
						'heading' => __('Map Pan Control', self::slug),
						'param_name' => 'pancontrol',
						'value' => array(__('Disable', self::slug) => 'false', __('Enable', self::slug) => 'true'),
						'group' => __('Map Control', self::slug)
					),
					array(
						'type' => 'dropdown',
						'class' => '',
						'heading' => __('Street View Control', self::slug),
						'param_name' => 'streetviewcontrol',
						'value' => array(__('Disable', self::slug) => 'false', __('Enable', self::slug) => 'true'),
						'group' => __('Map Control', self::slug)
					),
					array(
						'type' => 'dropdown',
						'class' => '',
						'heading' => __('Drag Control', self::slug),
						'param_name' => 'draggable',
						'value' => array(__('Disable', self::slug) => 'false', __('Enable', self::slug) => 'true'),
						'group' => __('Map Control', self::slug)
					),
					array(
						'type' => 'dropdown',
						'class' => '',
						'heading' => __('Zoom Level Control', self::slug),
						'param_name' => 'zoomcontrol',
						'value' => array(__('Disable', self::slug) => 'false', __('Enable', self::slug) => 'true'),
						'group' => __('Map Control', self::slug)
					),
					array(
						'type' => 'dropdown',
						'class' => '',
						'heading' => __('Scrollwheel Zooming', self::slug),
						'param_name' => 'scrollwheel',
						'value' => array(__('Disable', self::slug) => 'false', __('Enable', self::slug) => 'true'),
						'dependency' => Array('element' => 'zoomcontrol','value' => array('true')),
						'group' => __('Map Control', self::slug)
					),
					array(
						'type' => 'textfield',
						'heading' => __('Width', self::slug),
						'description' => __('Set your map width in % or px. Default is %.', self::slug),
						'param_name' => 'width',
						'admin_label' => true,
						'value' => '100%',
						'group' => __('Styling', self::slug)
					),
					array(
						'type' => 'textfield',
						'heading' => __('Height', self::slug),
						'description' => __('Set your map width in % or px. Default is px.', self::slug),
						'param_name' => 'height',
						'admin_label' => true,
						'value' => '300px',
						'group' => __('Styling', self::slug)
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Map Type', self::slug),
						'param_name' => 'map_type',
						'admin_label' => true,
						'value' => array(__('Roadmap', self::slug) => 'ROADMAP', __('Satellite', self::slug) => 'SATELLITE', __('Hybrid', self::slug) => 'HYBRID', __('Terrain', self::slug) => 'TERRAIN'),
						'group' => __('Styling', self::slug)
					),
					array(
						'type' => 'textarea_raw_html',
						'class' => '',
						'heading' => __('Google Styled Map JSON', self::slug),
						'param_name' => 'map_style',
						'value' => '',
						'description' => sprintf(__('<a href="%s" target="_blank">Click here</a> to get the style JSON code for styling your map.', self::slug), 'https://snazzymaps.com/'),
						'dependency' => Array('element' => 'map_type','value' => array('ROADMAP')),
						'group' => __('Styling', self::slug)
					)
				)
			)
			);
		}

		function build_shortcode($atts,$content = null){	
					
			extract(shortcode_atts(array(
				'width' => '100%',
				'height' => '300px',
				'lat' => '18.591212',
				'lng' => '73.741261',
				'map_type' => 'ROADMAP',
				'zoom' => '14',
				'streetviewcontrol' => 'false',
				'maptypecontrol' => 'false',
				'pancontrol' => 'false',
				'zoomcontrol' => 'false',
				'scrollwheel' => 'false',
				'draggable' => 'false',
				'marker' => 'default',
				'marker_custom_icon' => '',
				'marker_animation' => '',
				'marker_click' => '',
				'url' => '',
				'info_window' => '',
				'info_window_type' => '',
				'info_window_heading' => '',
				'info_window_address' => '',
				'map_style' => ''
			), $atts));

			$map_width = (substr($width, -1) != '%' && substr($width, -2)!='px' ? $width . '%' : $width);
			$map_height = (substr($height, -1) != '%' && substr($height, -2)!='px' ? $height . 'px' : $height);

			if($marker == "default"){
				$marker_custom_icon = '';
			}else{
				$attachment_image_src = wp_get_attachment_image_src( $marker_custom_icon, 'thumbnail');
				if(isset($attachment_image_src[0])){
					$marker_custom_icon = $attachment_image_src[0];
				}
			}

			switch ($marker_click) {
				case 'url':
						$link = vc_build_link( $url );
						if($link){
							$url = $link['url'];
						}
						$info_window_heading = '';
						$info_window_address = '';
					break;
				case 'info_window':
						if($info_window_heading){
							$info_window_heading = strip_tags($info_window_heading);
						}
						if($info_window_address){
							$info_window_address = preg_replace('#\R+#', '<br />', strip_tags($info_window_address));
						}
						$url = '';
					break;
				
				default:
						$url = '';
						$info_window_heading = '';
						$info_window_address = '';
					break;
			}

			$map_id = $this->get_map_id();

			$output = '<div id="'.$map_id.'" class="advanced-map-wrapper" style="width:'.$map_width.';height:'.$height.';"></div>'."\n";

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
			$args_marker['info_window_type'] = array('value' => $info_window_type);
			$args_marker['info_window_heading'] = array('value' => $info_window_heading);
			$args_marker['info_window_address'] = array('value' => $info_window_address);
			$args_marker['url'] = array('value' => $url);
			$output .= '$("#'.$map_id.'").AVCA_AddMapMarker({'."\n";
			$output .= $this->render_jquery_options($args_marker);
			$output .= '});'."\n";
				
			$output .= '})(jQuery)'."\n";
			$output .= '</script>'."\n";

			return $output;
		}

		function get_map_id($len = 20){
			$result = '';
			$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
			$charArray = str_split($chars);
			for($i = 0; $i < $len; $i++){
				$randItem = array_rand($charArray);
				$result .= $charArray[$randItem];
			}
			return $result;
		}

		function get_values_zoom(){
			$values = array();
			for ($i=20; $i >= 1 ; $i--) { 
				$values[$i] = $i;
			}
			return $values;
		}
	}
	new AVCA_Google_Map;
}