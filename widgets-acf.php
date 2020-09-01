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

class WidgetsWidgets {
	
	public function __construct() {
		
		add_action('admin_init', array($this, 'start'));
		
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array($this, 'load_custom_wp_admin_style') );
		
		add_action( 'wp_enqueue_scripts', array($this, 'load_theme_widget_style') );
		
		// Definindo action ajax
		add_action('wp_ajax_fonts_widgets_acf', array('WidgetsAdmin','get_fonts_ajax'));
		// Definindo action para acesso público
		add_action('wp_ajax_nopriv_fonts_widgets_acf', array('WidgetsAdmin','get_fonts_ajax')); 
		
		add_filter('acf/location/rule_types', array($this, 'registerLocation'));
		add_filter('acf/location/rule_values/widget_acf', array($this, 'registerLocationFields'));
	}
	
	public function start(){
		
		$screen = $_GET['page'];
		if (strpos($screen, "options-todos-os-widgets") == true) {
			
			$option_external = get_field("widgets_acf_fields_external","options");
			if($option_external==true){
				
				add_filter( 'pre_update_option', array($this, 'editor_external'));
			}
		}
	}
	
	public function editor_external(){
		
		// $filename = 'teste.txt';
		// if (!is_writable($filename)) {
		// 	$alert = 'Servidor sem permissão de escrita!';
		// }
		
		$path =
		function_exists('\App\template') || function_exists('\Roots\view') ? 
		get_template_directory() . '/views/widgets-templates' :
		get_template_directory() . '/widgets-templates';
		
		
		if(!is_dir($path)){
			$path = plugin_dir_path(__FILE__) . '../../more-widgets-templates';
		}
		
		$widgets = $_POST['acf'];
		
		foreach($widgets as $key => $widget){
			
			delete_field($key, 'option');

			if(!$alert){
				
				$dir_widget = $path.'/'.str_replace('_','-',str_replace('field_group_','',$key));
				
				$style = $dir_widget."/style.scss";
				
				$this->fwrite_widget($style, $widget['field_style']);
				
				$index = glob("{$dir_widget}/index.*");
				if(!$index){
					$index = $dir_widget."/index.php";
				}else{
					$index = $index[0];
				}
				$this->fwrite_widget($index, $widget['field_index']);
				
				$js = $dir_widget."/app.js";
				$this->fwrite_widget($js, $widget['field_javascript']);

				$functions = $dir_widget."/functions.php";
				$this->fwrite_widget($functions, $widget['field_functions']);

				$alert = 'Widgets atualizados com sucesso!';

			}
			
		}
		
		echo '<script>alert("'.$alert.'");window.location="'.admin_url('admin.php?page=acf-options-todos-os-widgets').'";</script>';
		die();
	}
	
	public function fwrite_widget($file, $text){
		
		$fp = fopen($file, 'w');
		fwrite($fp, stripslashes($text) );
		fclose($fp);
	}
	
	/**
	* registerLocation Registra um novo local para cadastrar grupos de campos ACF
	*
	* @return void
	*/
	public function registerLocation($choices) {
		
		include_once('back-end/acf/WidgetsLocation.php');
		
		// Check function exists, then include and register the custom location type class.
		
		$location = new WidgetsLocation;
		
		$label = $location->initialize();
		
		$choices[__($label->category, 'acf')][$label->name] = $label->label;
		
		
		return $choices; 
		
	}
	
	public function registerLocationFields($choices){
		
		include_once('back-end/acf/WidgetsLocation.php');
		
		$location = new WidgetsLocation;
		
		$choices = $location->get_values();
		
		return $choices;
	}
	
	/*=========================================================
	=            Criando o Menu e os campos em acf            =
	=========================================================*/
	
	static function add_admin_menu() {
		
		$plugin_nome = 'Widgets ACF';
		
		$parent = acf_add_options_page(array(
			'page_title' 	=> 'Configurações - '.$plugin_nome,
			'menu_title' 	=> $plugin_nome,
			'icon_url'		=> 'dashicons-layout',
			'redirect' 		=> false
		));
		
		$child = acf_add_options_sub_page(array(
			'page_title'  => 'Todos os Widgets',
			'menu_title'  => 'Todos os Widgets',
			'parent_slug' => 'acf-options-'.sanitize_title($plugin_nome),
		));
		
		add_theme_support('post-thumbnails');
		
		return $plugin_nome;
	}
	
	/*=====  End of Criando o Menu e os campos em acf  ======*/
	
	public function load_custom_wp_admin_style() {
		wp_enqueue_style( 'custom_wp_admin_codemirrordark_css', plugins_url('back-end/css/codemirror-dark.css', __FILE__) );
		wp_enqueue_style( 'custom_wp_admin_icon_css', plugins_url('back-end/css/icons.css', __FILE__) );
		wp_enqueue_style( 'custom_wp_admin_css', plugins_url('back-end/css/widgets.css', __FILE__) );
		// wp_enqueue_script( 'ckeditor_widgets-acf',  plugins_url('back-end/js/ckeditor/ckeditor.js', __FILE__));
		wp_enqueue_script( 'custom_wp_admin_js', plugins_url('back-end/js/admin.js', __FILE__) );
		wp_enqueue_script( 'custom_wp_widgets_js', plugins_url('back-end/js/widgets.js', __FILE__) );
		
		$cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));
		wp_localize_script('jquery', 'cm_settings', $cm_settings);
		wp_enqueue_script('wp-theme-plugin-editor');
		wp_enqueue_style('wp-codemirror');
		
		$show_fonts = get_field('widgets_acf_show_fonts','options');
		
		if($show_fonts==true){
			$fonts_selected = get_field('fonts_types_widget_acf', 'options');
			foreach ($fonts_selected as $key => $font) {
				$font_string  = explode('--',$font);
				wp_enqueue_style('font_google_widgets_acf'.$font_string[0],'https://fonts.googleapis.com/css?family='.$font_string[0].'|'.$font_string[0].':'.$font_string[1].'');
			}
		}
		
	}
	
	public function load_theme_widget_style() {
		
		$show_bootstrap = get_field('widgets_acf_show_bootstrap','options');
		$show_fonts = get_field('widgets_acf_show_fonts','options');
		
		if($show_bootstrap==true){
			wp_enqueue_style( 'front_end_widget_acf_bootstrap_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
			wp_enqueue_script( 'front_end_widget_acf_bootstrap_js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
		}
		
		if($show_fonts==true){
			$fonts_selected = get_field('fonts_types_widget_acf', 'options');
			foreach ($fonts_selected as $key => $font) {
				$font_string  = explode('--',$font);
				wp_enqueue_style('font_google_widgets_acf'.$font_string[0],'https://fonts.googleapis.com/css?family='.$font_string[0].'|'.$font_string[0].':'.$font_string[1].'');
			}
		}
		
		$show_css = get_field('widgets_acf_show_css','options');
		if($show_css==true){
			wp_enqueue_style( 'front_end_widget_acf', plugins_url('front-end/css/widget-acf.css', __FILE__) );
		}
	}
	
	/**
	* parseWidgetHeaders Realiza o parse do header com informações do widget
	*
	* @param  mixed $widgetController Widget que será realizado o parse
	* @return array Array com as informações do widget
	*/
	public static function parseWidgetHeaders($widgetController) {
		$name = basename(dirname($widgetController));
		$name = self::cleanWidgetName($name);
		
		$headers['name'] = $name;
		
		return array_merge(
			$headers, 
			get_file_data(
				$widgetController, [
					'title' => 'Title',
					'description' => 'Description',
					'category' => 'Category',
					'icon' => 'Icon',
					'keywords' => 'Keywords',
					// 'mode' => 'Mode',
					// 'align' => 'Align',
					// 'post_types' => 'PostTypes',
					// 'supports_align' => 'SupportsAlign',
					// 'supports_anchor' => 'SupportsAnchor',
					// 'supports_mode' => 'SupportsMode',
					// 'supports_multiple' => 'SupportsMultiple',
					// 'enqueue_style'     => 'EnqueueStyle',
					// 'enqueue_script'    => 'EnqueueScript',
					// 'enqueue_assets'    => 'EnqueueAssets',
					]
					)
				);
			}
			
			/**
			* cleanWidgetName Limpa o nome do widget
			*
			* @param  mixed $widgetName Nome do widget
			* @return string Nome do widget sem espaços, hífens e em letras minúsculas
			*/
			public static function cleanWidgetName($widgetName) {
				$widgetName = str_replace(' ', '_', $widgetName);
				$widgetName = str_replace('-', '_', $widgetName);
				$widgetName = strtolower($widgetName); // Convert to lowercase
				
				return $widgetName;
			}
			
		}
		
		function widgetsWidgets_init() {
			
			global $widgets, $acf_action, $actions, $duplicate;
			
			$widgets = new WidgetsWidgets();
			$plugin_nome = $widgets::add_admin_menu();
			
			require("back-end/functions.php");
			require("back-end/acf/fields_admin.php");
			require("back-end/acf/fields_code_external.php");
			
			require("back-end/actions.php");
			require("back-end/painel.php");
			$acf_action = new AcfAction();
			
			require("front-end/actions.php");
			$actions = new ActionWidgets();
			
			require("back-end/duplicate.php");
			$duplicate = new Duplicate_acf_widgets();
			
		}
		
		if( function_exists('acf_add_local_field_group') ){
			
			add_action( 'init', 'widgetsWidgets_init', 9999999 );
		}else{
			
			add_action( 'admin_notices', 'append_meta_links_plugin_widget_widgets' );
		}
		
		function append_meta_links_plugin_widget_widgets() {
			echo '<div class="error notice">Para o plugin <b>Widgets ACF</b> funcionar corretamente, precisa ser efetuado a instalação do plugin <a href="'.admin_url('plugins.php').'">(ACF PRO)</a>  / Site Plugin: <a href="https://www.advancedcustomfields.com/pro/" target="_blank">Advanced Custom Fields</a></div>';
		}
		define('CONCATENATE_SCRIPTS', false);
		?>