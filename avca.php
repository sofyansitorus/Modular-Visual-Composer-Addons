<?php

if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly

/**
 * Plugin Name:       Advanced Visual Composer Addons
 * Plugin URI:        https://github.com/sofyansitorus/Advanced-Visual-Composer-Addons
 * Description:       Advanced Addons for <a href="http://goo.gl/QNA0Fb" target="_blank">WPBakery Visual Composer</a>.
 * Version:           1.1.2
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

require_once( dirname(__FILE__).'/lib/class-avca-base.php' );
require_once( dirname(__FILE__).'/lib/class-avca-module.php' );

final class AVCA extends AvcaBase{

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	const module_dir = 'modules';
	const min_vc_version = '4.0';

	/**
	 * Single Instance
	 */
	private static $_instance = null;

	private $_modules_activated = array();
	private $_modules_installed = array();

	private $_module;
	private $_action;

	private $_admin_page;

	private $_plugin_data = array();
	
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
		$this->_plugin_data = get_plugin_data(__FILE__);

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

		$this->setup_localization();

		$this->load_modules(true);

		add_action( 'admin_init', array( $this, 'admin_init' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	public function admin_init(){
		$nonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : false;

		switch ($this->_action) {

			case 'activate_module':
					if( wp_verify_nonce( $nonce, 'activate_module' ) && $this->_module ) {
						$this->activate_module($this->_module);
					}else{
						wp_die( __('Invalid request!', AVCA_SLUG) );
					}
				break;

			case 'deactivate_module':
					if( wp_verify_nonce( $nonce, 'deactivate_module' ) && $this->_module ) {
						$this->deactivate_module($this->_module);
					}else{
						wp_die( __('Invalid request!', AVCA_SLUG) );
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
	public function admin_menu(){
		$this->_admin_page = add_menu_page( 
			$this->_plugin_data['Name'], 
			'AVCA', 
			'manage_options', 
			AVCA_SLUG, 
			array($this, 'render_admin_page'), 
			'', 
			75
		);

		add_action('load-'.$this->_admin_page, array($this, 'flush_modules'));
	}

	/**
	 * Render admin page
	 */
	public function render_admin_page(){
	?>
	<div class="wrap">
	<h2><?php echo $this->_plugin_data['Name']; ?></h2>
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

				$row_actions = array();
				if($this->is_module_active($key)){
					$row_actions['deactivate'] = sprintf('<span class="deactivate"><a href="%s">%s</a></span>', 
						wp_nonce_url( 
							add_query_arg( 
								array(
									'page' => AVCA_SLUG,
									'module' => $key,
									'action' => 'deactivate_module'
								),
								admin_url( 'admin.php' )
							), 
							'deactivate_module'
						), 
						__('Deactivate', AVCA_SLUG) 
					);
					$row_class = 'active';
				}else{
					$row_actions['activate'] = sprintf('<span class="activate"><a href="%s">%s</a></span>', 
						wp_nonce_url( 
							add_query_arg( 
								array(
									'page' => AVCA_SLUG,
									'module' => $key,
									'action' => 'activate_module',
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
				
				$row_metas = array();
				$version = (!empty($module['data']['version'])) ? $module['data']['version'] : false;
				if($version){
					$row_metas['version'] = sprintf('<span class="version">%s %s</span>', 
						__('Version', AVCA_SLUG),
						$version
					);
				}
				$author_name = (!empty($module['data']['author_name'])) ? $module['data']['author_name'] : false;
				$author_url = (!empty($module['data']['author_url'])) ? $module['data']['author_url'] : false;
				if($author_name){
					if($author_url){
						$row_metas['author'] = sprintf('<span class="author">%s <a href="%s">%s</a></span>', 
							__('By', AVCA_SLUG),
							$author_url, 
							$author_name
						);
					}else{
						$row_metas['author'] = sprintf('<span class="author">%s %s</span>', 
							__('By', AVCA_SLUG),
							$author_name
						);
					}
				}
				$row_metas = apply_filters('avca_module_row_metas', $row_metas, $key);
			?>
			<tr id="advanced-visual-composer-addons" class="<?php echo $row_class; ?>" data-slug="">
				<td class="plugin-title"><strong><?php echo $name; ?></strong>
				<div class="row-actions visible">
				<?php echo implode( " | ", $row_actions ); ?>
				</div>
				</td>
				<td class="column-description desc">
					<div class="plugin-description"><p><?php echo $description; ?></p></div>
					<div class="row-metas visible">
					<?php echo implode( " | ", $row_metas ); ?>
					</div>
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
		load_plugin_textdomain( AVCA_SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	/**
	 * Load modules
	 */
	private function load_modules( $run = false ){
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
		if($run){
			$this->run_modules();
		}		
	}

	private function run_modules(){
		foreach ($this->_modules_activated as $key => $module) {
			if(file_exists($module['path'])){
				require_once($module['path']);
			}
		}
	}

	public function flush_modules(){
		foreach ($this->_modules_activated as $key => $module) {
			if(isset($this->_modules_installed[$key]) && file_exists($module['path'])){
				$this->_modules_activated[$key] = $this->_modules_installed[$key];
			}else{
				unset($this->_modules_activated[$key]);
				$this->add_admin_notice('error', sprintf(__('The module %s has been deactivated due to an error: Module file does not exist.', AVCA_SLUG), $module['data']['name']));
			}
		}
		$this->save_activated_modules(  );
	}

	private function save_activated_modules(){
		return update_option( 'avca_modules', $this->_modules_activated );
	}

	private function is_module_active($module){
		return isset($this->_modules_activated[$module]);
	}

	private function activate_module( $module ){
		if(!$this->is_module_active( $module )){
			$this->_modules_activated[$module] = $this->_modules_installed[$module];
			$this->save_activated_modules( );
			$this->add_admin_notice('updated', __('Module activated.', AVCA_SLUG));
			$this->run_modules();
			do_action('avca_module_activated', $module, $this->_modules_activated[$module]);
			return true;
		}else{
			return false;
		}
	}

	private function deactivate_module( $module ){
		if($this->is_module_active( $module )){
			unset($this->_modules_activated[$module]);
			$this->save_activated_modules( );
			$this->add_admin_notice('updated', __('Module deactivated.', AVCA_SLUG));
			do_action('avca_module_deactivated', $module, $this->_modules_activated[$module]);
			$this->run_modules();
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Get modules header data
	 */
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
	 * Get modules directory
	 */
	private function get_module_dir(){
		return untrailingslashit( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . self::module_dir;	
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
			$this->load_modules();
			if($this->_modules_installed){
				foreach ( $this->_modules_installed as $key => $value ) {
					$this->_modules_activated[$key] = $value;
				}
			}
			$this->save_activated_modules( );
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
  
} // end class

AVCA::getInstance();