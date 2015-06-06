<?php
if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly

abstract class AvcaModule {

	protected function create_row_actions_link($class, $url, $label){
		return sprintf('<span class="%s"><a href="%s">%s</a></span>',
			$class, 
			$url, 
			$label
		);
	}

	public function settings_section_callback($args){
		do_action('avca_module_settings_section_callback', $args);
	}

	public function settings_field_callback($args = array()){
		$this->render_setting_field($args);
	}

	protected function render_setting_field($args){
		$field = $this->normalize_setting_field($args);

		if(!$field['type']){
			echo __('Error: Setting field type is empty!', AVCA_SLUG);
			return;
		}

		if(!$field['name']){
			echo __('Error: Setting field name is empty!', AVCA_SLUG);
			return;
		}

		$this->render_setting_before_field($field);

		switch ($field['type']) {
			case 'checkbox':
					$this->render_setting_field_checkbox($field);
				break;
			
			default:
					do_action('avca_module_render_setting_field', $field);
				break;
		}

		$this->render_setting_after_field($field);
	}

	protected function render_setting_field_checkbox($field){
	?>
	<input type="checkbox" name="<?php echo $field['name']; ?>" value="1" <?php checked( get_option($field['name']), 1, true ); ?> />
	<?php
	}

	protected function render_setting_before_field($field){
		
	}

	protected function render_setting_after_field($field){
		
	}

	protected function normalize_setting_field($args){
		$defaults = array(
			'type' => 'text',
			'name' => ''
		);

		$args = wp_parse_args( $args, $defaults );

		return $args;
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
}