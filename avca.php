<?php

if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly

/**
 * Plugin Name:       Advanced Visual Composer Addons
 * Plugin URI:        https://github.com/sofyansitorus/Advanced-Visual-Composer-Addons
 * Description:       Advanced Addons for <a href="http://goo.gl/QNA0Fb" target="_blank">WPBakery Visual Composer</a>.
 * Version:           1.0.0
 * Author:            Sofyan Sitorus
 * Author URI:        https://github.com/sofyansitorus/
 * Text Domain:       avca
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( !defined( 'AVCA_PATH' ) )
	define( 'AVCA_PATH', plugin_dir_url( __FILE__ ) );

if ( !defined( 'AVCA_URL' ) )
	define( 'AVCA_URL', plugin_dir_url( __FILE__ ) );

if ( !defined( 'AVCA_SLUG' ) )
	define( 'AVCA_SLUG', 'avca' );

require_once( dirname(__FILE__).'/lib/class-avca-param.php' );
require_once( dirname(__FILE__).'/lib/class-avca-module.php' );

class AVCA{

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	const module_dir = 'modules';
	const param_dir = 'params';
	const min_vc_version = '4.0';

	/**
	 * Single Instance
	 */
	private static $_instance = null;
	
	private $_params = array();

	private $_modules_activated = array();
	private $_modules_installed = array();

	private $_module;
	private $_action;

	public $_plugin_data = array();
	
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
		// Initialize plugin data
		if( !function_exists( 'get_plugin_data' ) ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$this->plugin_data = get_plugin_data(__FILE__);

		$this->load_modules();

		$this->_action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : false;
		$this->_module = isset($_GET['module']) ? sanitize_text_field($_GET['module']) : false;

		// Register an activation hook for the plugin
		register_activation_hook( __FILE__, array( $this, 'activation_hook' ) );

		// Register a deactivation hook for the plugin
		register_deactivation_hook( __FILE__, array( $this, 'deactivation_hook' ) );

		// Run plugins
		add_action( 'plugins_loaded', array( $this, 'init' ) );

	}

	/**
	 * Run plugins
	 */
	public function init(){

		// Check dependencies
		if(!$this->is_vc_activated()) return false;

		// Check ompatibilities
		if(!$this->is_vc_version_compatible()) return false;

		$this->run_modules();

		$this->setup_localization();

		add_action( 'admin_init', array( $this, 'admin_init' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );

	}

	public function admin_init(){
		if($this->_module){
			switch ($this->_action) {

				case 'activate':
						if( check_admin_referer( 'activate_module' ) ) {
							$this->activate_module($this->_module);
						}
					break;

				case 'deactivate':
						if( check_admin_referer( 'deactivate_module' ) ) {
							$this->deactivate_module($this->_module);
						}
					break;
				
				default:
					# code...
					break;
			}
		}
	}

	/**
	 * Add menu page
	 */
	public function admin_notices(){

		switch ($this->_action) {

			case 'activate':
					if($this->_module && $this->is_module_active($this->_module)){	
						echo '<div class="updated"><p>'; 
				        echo __('Module activated.', AVCA_SLUG);
				        echo "</p></div>";
					}
				break;

			case 'deactivate':
					if($this->_module && !$this->is_module_active($this->_module)){
						echo '<div class="updated"><p>'; 
				        echo __('Module deactivated.', AVCA_SLUG);
				        echo "</p></div>";
			    	}
				break;
			
			default:
				# code...
				break;
		}
	}

	/**
	 * Add admin page
	 */
	public function add_admin_page(){
		add_menu_page( 
			$this->plugin_data['Name'], 
			'AVCA', 
			'manage_options', 
			AVCA_SLUG, 
			array($this, 'render_admin_page'), 
			'', 
			75
		);
	}

	/**
	 * Render admin page
	 */
	public function render_admin_page(){
		$this->run_modules();
	?>
	<div class="wrap">
	<h2><?php echo $this->plugin_data['Name']; ?></h2>
	<ul class="subsubsub">
		<li class="all"><a href="<?php echo add_query_arg(array('page' => AVCA_SLUG), admin_url( 'admin.php' )) ;?>">All <span class="count">(<?php echo count($this->_modules_installed); ?>)</span></a> |</li>
		<li class="active"><a href="<?php echo add_query_arg(array('page' => AVCA_SLUG, 'filter' => 'active'), admin_url( 'admin.php' )) ;?>">Active <span class="count">(<?php echo count($this->_modules_activated); ?>)</span></a> |</li>
		<li class="inactive"><a href="<?php echo add_query_arg(array('page' => AVCA_SLUG, 'filter' => 'inactive'), admin_url( 'admin.php' )) ;?>">Inactive <span class="count">(<?php echo (count($this->_modules_installed) - count($this->_modules_activated)); ?>)</span></a></li>
	</ul>
	<table class="wp-list-table widefat plugins">
		<thead>
		<tr>
			<th scope="col" id="name" class="manage-column column-name" style=""><?php _e('Module', AVCA_SLUG); ?></th>
			<th scope="col" id="description" class="manage-column column-description" style=""><?php _e('Description', AVCA_SLUG); ?></th>
		</tr>
		</thead>
		<tbody id="the-list">
			<?php
			foreach ($this->_modules_installed as $key => $module) {
				$show = true;
				$filter = isset($_GET['filter']) ? $_GET['filter'] : false;

				if($filter){
					switch ($filter) {
						case 'active':
								if(FALSE === $this->is_module_active($key)){
									$show = FALSE;
								}
							break;

						case 'inactive':
								if(TRUE === $this->is_module_active($key)){
									$show = FALSE;
								}
							break;
						
						default:
							# code...
							break;
					}
				}
				if(!$show){
					continue;
				}
				$name = (!empty($module['data']['name'])) ? $module['data']['name'] : $key;
				$description = (!empty($module['data']['description'])) ? $module['data']['description'] : __('No description available', AVCA_SLUG);
				$version = (!empty($module['data']['version'])) ? $module['data']['version'] : $this->plugin_data['Version'];
				$author_name = (!empty($module['data']['author_name'])) ? $module['data']['author_name'] : $this->plugin_data['Author'];
				$author_url = (!empty($module['data']['author_url'])) ? $module['data']['author_url'] : $this->plugin_data['AuthorURI'];
				$row_actions = array();
				if($this->is_module_active($key)){
					$row_actions[] = sprintf('<span class="deactivate"><a href="%s">%s</a></span>', 
						wp_nonce_url( 
							add_query_arg( 
								array(
									'page' => AVCA_SLUG,
									'module' => $key,
									'action' => 'deactivate'
								),
								admin_url( 'admin.php' )
							), 
							'deactivate_module'
						), 
						__('Deactivate', AVCA_SLUG) 
					);
					$row_class = 'active';
				}else{
					$row_actions[] = sprintf('<span class="activate"><a href="%s">%s</a></span>', 
						wp_nonce_url( 
							add_query_arg( 
								array(
									'page' => AVCA_SLUG,
									'module' => $key,
									'action' => 'activate'
								),
								admin_url( 'admin.php' )
							), 
							'activate_module'
						), 
						__('Activate', AVCA_SLUG) 
					);
					$row_class = 'inactive';
				}
				if($this->is_module_active($key)){
					$row_actions = apply_filters('avca_module_row_actions', $row_actions, $key);
				}
			?>
			<tr id="advanced-visual-composer-addons" class="<?php echo $row_class; ?>" data-slug="">
				<td class="plugin-title"><strong><?php echo $name; ?></strong>
				<div class="row-actions visible">
				<?php echo implode( " | ", $row_actions ); ?>
				</div>
				</td>
				<td class="column-description desc">
					<div class="plugin-description"><p><?php echo $description; ?></p></div>
					<div class="active second plugin-version-author-uri"><?php _e('Version', AVCA_SLUG); ?> <?php echo $version; ?> | By <a href="<?php echo $author_url; ?>"><?php echo $author_name; ?></a></div>
				</td>
			</tr>
			<?php
			}
			?>
		</tbody>
		<tfoot>
		<tr>
			<th scope="col" class="manage-column column-name" style=""><?php _e('Module', AVCA_SLUG); ?></th>
			<th scope="col" class="manage-column column-description" style=""><?php _e('Description', AVCA_SLUG); ?></th>
		</tr>
		</tfoot>
	</table>
	</div>
	<?php
	}
  
	/**
	 * Setup localization
	 */
	public function setup_localization() {
		load_plugin_textdomain( $this->plugin_data['TextDomain'], false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	/**
	 * Load params
	 */
	public function load_params(){
		foreach(glob($this->get_param_dir()."/*", GLOB_ONLYDIR) as $dir){
			$param_file = trailingslashit($dir).basename($dir).'.php';
			if(file_exists($param_file)){
				//require_once($param_file);
			}
		}
	}

	/**
	 * Load modules
	 */
	private function load_modules(){
		$this->_modules_activated = get_option( 'avca_modules', array() );
		foreach(glob($this->get_module_dir()."/*", GLOB_ONLYDIR) as $dir){
			$module_name = basename($dir);
			$module_file = trailingslashit($dir).$module_name.'.php';
			if(file_exists($module_file)){
				$this->_modules_installed[$module_name] = array(
					'data' => get_file_data($module_file, $this->module_data()),
					'path' => $module_file
				);
			}
		}		
	}

	private function run_modules(){
		foreach ($this->_modules_activated as $key => $module) {
			if(file_exists($module['path'])){
				require_once($module['path']);
			}else{
				unset($this->_modules_activated[$key]);
			}
		}
	}

	private function is_module_active($module){
		return isset($this->_modules_activated[$module]);
	}

	private function activate_module( $module ){
		$this->_modules_activated[$module] = $this->_modules_installed[$module];
		update_option( 'avca_modules', $this->_modules_activated );
	}

	private function deactivate_module( $module ){
		unset($this->_modules_activated[$module]);
		update_option( 'avca_modules', $this->_modules_activated );
	}

	private function module_data(){
		return array(
			'name' => 'Name', 
			'description' => 'Description', 
			'version' => 'Version',
			'author_name' => 'Author Name',
			'author_url' => 'Author URL'
		);
	}
  
	/**
	 * Runs when the plugin is activated
	 */  
	public function activation_hook() {

		// Check dependencise
		if( !$this->is_vc_activated() ) {
			die( sprintf( __( 'You must install and activate WPBakery Visual Composer plugin before activating this plugin.', AVCA_SLUG ) ) );
		}

		//Check compatibility
		if( !$this->is_vc_version_compatible() ) {
			die( sprintf( __( 'This plugin requires WPBakery Visual Composer plugin version %s or greater', AVCA_SLUG ), self::min_vc_version ) );
		}

		if( FALSE === get_option( 'avca_first_install_time' ) ){
			if($this->_modules_installed){
				foreach ( $this->_modules_installed as $key => $value ) {
					$this->_modules_activated[$key] = $value;
				}
			}
			update_option( 'avca_modules', $this->_modules_activated );
			update_option( 'avca_first_install_time', current_time('timestamp') );
		}
	}
  
	/**
	 * Runs when the plugin is deactivated
	 */  
	public function deactivation_hook() {
		
	}

  
	/**
	 * Check if VC plugin is activated
	 */  
	private function is_vc_activated() {
		return is_plugin_active( 'js_composer/js_composer.php' );
	}
  
	/**
	 * Check if VC plugin version is compatible
	 */  
	private function is_vc_version_compatible() {
		if( !defined('WPB_VC_VERSION') ) return false;
		return version_compare( WPB_VC_VERSION,  self::min_vc_version, '>' );
	}

	/**
	 * Get params directory
	 */
	protected function get_param_dir(){
		return untrailingslashit( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . self::param_dir;	
	}

	/**
	 * Get modules directory
	 */
	protected function get_module_dir(){
		return untrailingslashit( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . self::module_dir;	
	}
  
} // end class

AVCA::getInstance();