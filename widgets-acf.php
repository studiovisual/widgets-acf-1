<?php
/***************************************************************************
Plugin Name:  Widgets ACF
Plugin URI:   https://github.com/claudioweb/widgets-acf
Description:  Plugin dependente do ACF (Add Custom Fields)
Version:      2.0
Author:       Claudio Web (claudioweb)
Author URI:   http://www.claudioweb.com.br/
Text Domain:  widgets-acf
**************************************************************************/

class WidgetsACF {
	
	public function __construct() {
		add_action('wp_enqueue_scripts', array($this, 'enqueueFront') , 999);
		
		add_filter('acf/location/rule_types', array($this, 'registerLocation'));
		add_filter('acf/location/rule_values/widget_acf', array($this, 'registerLocationFields'));
		
		add_action('acf/input/admin_enqueue_scripts', array($this, 'enqueue'));
		add_action('acf/init', array($this, 'includes'), 99);
		add_action('acf/init', array($this, 'initialize'), 100);
	}

	public function enqueue() {
		wp_enqueue_script('widgets-ckeditor-js', plugins_url('back-end/assets/js/ckeditor/ckeditor.js', __FILE__));
		wp_enqueue_script('widgets-modal-js', plugins_url('back-end/assets/js/modal.js', __FILE__));
		wp_enqueue_script('widgets-js', plugins_url('back-end/assets/js/widgets.js', __FILE__));
		wp_enqueue_script('widgets-admin-js', plugins_url('back-end/assets/js/admin.js', __FILE__));

		wp_enqueue_style('widgets-modal-css', plugins_url('back-end/assets/css/modal.css', __FILE__));
		wp_enqueue_style('widgets-css', plugins_url('back-end/assets/css/widgets.css', __FILE__));
		wp_enqueue_style('widgets-codemirrordark-css', plugins_url('back-end/assets/css/codemirror-dark.css', __FILE__));

		$cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));
		wp_localize_script('jquery', 'cm_settings', $cm_settings);
		wp_enqueue_script('wp-theme-plugin-editor');
		wp_enqueue_style('wp-codemirror');
		
		if(get_field('widgets_acf_show_fonts','options')):
			$fonts_selected = get_field('fonts_types_widget_acf', 'options');

			if(!empty($fonts_selected)):
				foreach($fonts_selected as $font):
					$font_string = explode('--', $font);
					wp_enqueue_style('font_google_widgets_acf' . $font_string[0], 'https://fonts.googleapis.com/css?family=' . $font_string[0] . '|' . $font_string[0] . ':' . $font_string[1] . '');
				endforeach;
			endif;
		endif;
	}

	/*
     * Includes
     */
	public function includes() {
		include_once('back-end/utils.php');
		include_once('back-end/widgets.php');
		include_once('back-end/widget.php');
		include_once('back-end/painel.php');
		include_once('back-end/admin.php');
		include_once('back-end/acf/widgets-location.php');
		include_once('back-end/duplicate-widgets.php');

		include_once('front-end/widget-template.php');
	}

	public function initialize() {
		global $widgets, $actions, $duplicate, $plugin_nome;
		$plugin_nome = 'Widgets ACF';
		
		new Widgets();
		new Admin();
		
		$actions = new WidgetTemplate();
		$duplicate = new DuplicateWidgets();
	}
	
	/**
	* registerLocation Registra um novo local para cadastrar grupos de campos ACF
	*
	* @return void
	*/
	public function registerLocation($choices) {
		$location = new WidgetsLocation;		
		$label = $location->initialize();
		
		$choices[__($label->category, 'acf')][$label->name] = $label->label;
		
		return $choices; 
	}
	
	public function registerLocationFields($choices) {	
		$location = new WidgetsLocation;
		$choices = $location->get_values();
		
		return $choices;
	}
	
	public function enqueueFront() {
		Utils::getCodes();

		if(!empty(get_field('widgets_acf_show_bootstrap', 'options'))):
			wp_enqueue_style( 'front_end_widget_acf_bootstrap_css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
			wp_enqueue_script( 'front_end_widget_acf_bootstrap_popper_js', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js');
			wp_enqueue_script( 'front_end_widget_acf_bootstrap_js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js');
		endif;
		
		if(!empty(get_field('widgets_acf_show_fonts', 'options'))):
			$fonts_selected = get_field('fonts_types_widget_acf', 'options');
			
			foreach ($fonts_selected as $font):
				$font_string  = explode('--', $font);
				wp_enqueue_style('font_google_widgets_acf' . $font_string[0], 'https://fonts.googleapis.com/css?family=' . $font_string[0] . '|' . $font_string[0] . ':' . $font_string[1] . '');
			endforeach;
		endif;
		
		if(!empty(get_field('widgets_acf_show_css','options')))
			wp_enqueue_style('front_end_widget_acf', plugins_url('front-end/css/widget-acf.css', __FILE__));
	}
}

new WidgetsACF();
		
		
		// if( function_exists('acf_add_local_field_group') ){
			
		// 	add_action( 'init', 'widgetsWidgets_init', 9999999 );
		// }else{
			
		// 	add_action( 'admin_notices', 'append_meta_links_plugin_widget_widgets' );
		// }
		
		function append_meta_links_plugin_widget_widgets() {
			echo '<div class="error notice">Para o plugin <b>Widgets ACF</b> funcionar corretamente, precisa ser efetuado a instalação do plugin <a href="'.admin_url('plugins.php').'">(ACF PRO)</a>  / Site Plugin: <a href="https://www.advancedcustomfields.com/pro/" target="_blank">Advanced Custom Fields</a></div>';
		}
		define('CONCATENATE_SCRIPTS', false);
		?>