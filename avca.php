<?php
/**
 * Plugin Name:       Advanced Visual Composer Addons
 * Plugin URI:        https://github.com/sofyansitorus/Advanced-Visual-Composer-Addons
 * Description:       Advanced Addons for <a href="http://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431?ref=sofyansitorus" target="_blank">WPBakery Visual Composer</a>.
 * Version:           0.1
 * Author:            Sofyan Sitorus
 * Author URI:        https://github.com/sofyansitorus/
 * Text Domain:       avca
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/sofyansitorus/Advanced-Visual-Composer-Addons
 */

class AVCA {

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	const name = 'Advanced Visual Composer Addons';
	const category = 'AVCA';
	const slug = 'avca';
	const ver = '0.1';
	const min_vc_version = '4.0';
	const param_dir = 'params';
	const module_dir = 'modules';
	const aff_link = 'http://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431?ref=sofyansitorus';

	/**
	 * Single Instance
	 */
	private static $_instance = null;
	
	/**
	 * Get Instance
	 */
	public static function getInstance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct() {

		//register an activation hook for the plugin
		register_activation_hook( __FILE__, array( $this, 'activation_hook' ) );

		//register a deactivation hook for the plugin
		register_deactivation_hook( __FILE__, array( $this, 'deactivation_hook' ) );

		//register an extra row meta for the plugin
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2);

		//run the plugin
		add_action( 'plugins_loaded', array($this, 'run') );

	}

	/**
	 * Run plugins
	 */
	public function run(){

		// Check dependencies
		if(!$this->is_vc_activated()) return false;

		// Check ompatibilities
		if(!$this->is_vc_version_compatible()) return false;

		// Load transalation
		add_action('after_setup_theme', array($this, 'setup_localization'));

		//Hook up to the after_setup_theme action to load params
		add_action( 'after_setup_theme', array( $this, 'load_params' ) );

		//Hook up to the after_setup_theme action to load modules
		add_action( 'after_setup_theme', array( $this, 'load_modules' ) );

	}
  
	/**
	 * Setup localization
	 */
	public function setup_localization() {
		load_plugin_textdomain( self::slug, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	/**
	 * Load params
	 */
	public function load_params(){
		foreach(glob($this->get_param_dir()."/*", GLOB_ONLYDIR) as $dir){
			$param_file = trailingslashit($dir).basename($dir).'.php';
			if(file_exists($param_file)){
				require_once($param_file);
			}
		}
	}

	/**
	 * Load modules
	 */
	public function load_modules(){
		foreach(glob($this->get_module_dir()."/*", GLOB_ONLYDIR) as $dir){
			$module_file = trailingslashit($dir).basename($dir).'.php';
			if(file_exists($module_file)){
				require_once($module_file);
			}
		}		
	}

	/**
	 * Get params directory
	 */
	protected function get_param_dir(){
		return untrailingslashit(plugin_dir_path( __FILE__ )).DIRECTORY_SEPARATOR.self::param_dir;	
	}

	/**
	 * Get modules directory
	 */
	protected function get_module_dir(){
		return untrailingslashit(plugin_dir_path( __FILE__ )).DIRECTORY_SEPARATOR.self::module_dir;	
	}
  
	/**
	 * Registers and enqueues scripts
	 */
	protected function register_enqueue_scripts($handle, $src, $deps=array(), $ver=false, $in_footer=true) {
		$external_file = (strpos($src, 'http') === 0 || strpos($src, '//') === 0) ? true : false;
		if($external_file){
			wp_register_script( $handle, $src, $deps, $ver, $in_footer );
			wp_enqueue_script( $handle );
		}else{
			if( file_exists( plugin_dir_path(__FILE__) . $src ) ) {
				wp_register_script( $handle, plugins_url($src, __FILE__), $deps, $ver, $in_footer );
				wp_enqueue_script( $handle );
			} // end if
		} // end if/else
	} // end register_enqueue_scripts
  
	/**
	 * Registers and enqueues styles
	 */
	protected function register_enqueue_styles($handle, $src, $deps=array(), $ver=false, $media='all') {
		$external_file = (strpos($src, 'http') === 0 || strpos($src, '//') === 0) ? true : false;
		if($external_file){
			wp_register_style( $handle, $src, $deps, $ver, $media );
			wp_enqueue_style( $handle );
		}else{
			if( file_exists( plugin_dir_path(__FILE__) . $src ) ) {
				wp_register_style( $handle, plugins_url($src, __FILE__), $deps, $ver, $media );
				wp_enqueue_style( $handle );
			} // end if
		} // end if/else
	} // end register_enqueue_scripts
  
	/**
	 * Runs when the plugin is activated
	 */  
	public function activation_hook() {

		// Check dependencise
		if( !$this->is_vc_activated() ) {
			die(sprintf(__('You must install and activate <a href="%s" target="_blank">WPBakery Visual Composer</a> plugin before activating this plugin.', self::slug), self::aff_link));
		}
		

		//Check compatibility
		if( !$this->is_vc_version_compatible() ) {
			die(sprintf(__('This plugin requires <a href="%s" target="_blank">WPBakery Visual Composer</a> plugin version %s or greater', self::slug), self::aff_link, self::min_vc_version));
		}
	}
  
	/**
	 * Runs when the plugin is deactivated
	 */  
	public function deactivation_hook() {
		// do not generate any output here
	}

	/**
	 * Render jQuery options
	 */ 
	protected function render_jquery_options($options = array()){
		$i = 1;
		$options_length = count($options);
		$result = '';
		foreach ($options as $key => $option) {
			if($i < $options_length){
				$append = ','."\n";
			}else{
				$append = "\n";
			}
			$type = isset($option['type']) ? $option['type'] : 'string';
			switch ($type) {
				case 'integer':
				case 'boolean':
				case 'array':
						$result .= $key.': '.$option['value'].$append;
					break;				
				default:
						$result .= $key.': "'.$option['value'].'"'.$append;
					break;
			}
			$i++;
		}
		return $result;
	}
  
	/**
	 * Check if VC plugin is activated
	 */  
	private function is_vc_activated() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return is_plugin_active( 'js_composer/js_composer.php' );
	}
  
	/**
	 * Check if VC plugin version is compatible
	 */  
	private function is_vc_version_compatible() {
		return version_compare(WPB_VC_VERSION,  self::min_vc_version, '>');
	}
  
	/**
	 * Add extra actions link for the plugin
	 */

	function plugin_row_meta( $links, $file ) {
		if (strpos( $file, basename( __FILE__) ) !== false ) {
			$links[] = sprintf(__('<a href="%s" target="_blank">Buy WPBakery Visual Composer</a>', self::slug), self::aff_link);
		}
		return $links;
	}
  
} // end class

AVCA::getInstance();