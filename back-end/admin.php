<?php

Class Admin {

    public function __construct() {
        add_action('admin_init', array($this, 'initialize'));
        add_action('admin_menu', array($this, 'addAdminMenu'));

        add_action('publish_acf-field-group', function() {
			add_action('updated_post_meta', array($this, 'exportJsonFields'), 10, 4); 
        });
        
        // Definindo action ajax
		add_action('wp_ajax_fonts_widgets_acf', array('Utils', 'getFontsAjax'));
		// Definindo action para acesso público
		add_action('wp_ajax_nopriv_fonts_widgets_acf', array('Utils', 'getFontsAjax')); 
    }

    public function initialize() {
		if(!isset($_GET['page']))
			return;

		$screen = $_GET['page'];

		if(strpos($screen, "options-todos-os-widgets")):
			include_once('acf/fields-code-external.php');

			if(isset($_GET['export'])):
				$location = new WidgetsLocation;
				$location->export_zip($_GET['export']);
			endif;
				
			if(isset($_GET['del_group_field'])):
				$post = get_post($_GET['del_group_field']);
				
				if($post->post_type="acf-field-group"):
					$groups = acf_get_field_groups();
					
					foreach($groups as $group):
						if($_GET['del_group_field'] != $group['ID'])
							continue;
							
						$widget_location = $group['location'][0][0]['value'];
						wp_delete_post($_GET['del_group_field'], true);
						$this->exportJsonFields(null, null, null, null, $widget_location);

						echo '<script>alert("Grupo de Campos deletado com sucesso!"); window.location.href="'.admin_url('admin.php?page=acf-options-todos-os-widgets').'";</script>';
					endforeach;
				endif;
			endif;
				
			if(isset($_GET['del'])):
				$path =
					function_exists('\App\template') || function_exists('\Roots\view') ? 
						get_template_directory() . '/views/widgets-templates/'.$_GET['del'] :
						get_template_directory() . '/widgets-templates/'.$_GET['del'];
				
				if(is_dir($path)):
					$arquivos = glob($path . "/*.*");

					foreach($arquivos as $arquivo)
						unlink($arquivo);
					
					$handle = opendir($path);
					closedir($handle);
					rmdir($path);
					
					echo '<script>alert("Widget deletado com sucesso!");window.location="'.admin_url('admin.php?page=acf-options-todos-os-widgets').'";</script>';
					die();
				endif;
			endif;
			
			add_filter('pre_update_option', array($this, 'editorExternal'));
		else:
			include_once('acf/fields-admin.php');
		endif;
    }
    
    public function editorExternal() {
		if(!$_POST)
			return;

		$path =
			function_exists('\App\template') || function_exists('\Roots\view') ? 
				get_template_directory() . '/views/widgets-templates' :
				get_template_directory() . '/widgets-templates';
		
		if(!is_dir($path))
			mkdir($path, 0777, true);
		
		$widgets = $_POST['acf'];
		
		if(!empty($widgets['field_salvar_widget_acf'])):
			$dir_widget = $path."/".sanitize_title($widgets['field_salvar_widget_acf']);
			mkdir($dir_widget, 0777, true);
			
			// index.php
			$new_widget = $dir_widget."/index.php";
			$title_widget = $widgets['field_salvar_widget_acf'];
			$file_default = '<?php var_dump($widget); ?>';

			if(function_exists('\App\template') || function_exists('\Roots\view')):
				$new_widget = $dir_widget."/index.blade.php";
				$file_default = '@php var_dump($widget); @endphp';
			endif;
			$this->writeWidget($new_widget, $file_default);
			
			// app.js
			$new_widget = $dir_widget."/app.js";
			$title_widget = $widgets['field_salvar_widget_acf'];
			$file_default = '';
			$this->writeWidget($new_widget, $file_default);
			
			// functions.php
			$new_widget = $dir_widget."/functions.php";
			$title_widget = $widgets['field_salvar_widget_acf'];
			$file_default = '<?php //... ?>';
			$this->writeWidget($new_widget, $file_default);
			
			// style.scss
			$new_widget = $dir_widget."/style.scss";
			$title_widget = $widgets['field_salvar_widget_acf'];
			$file_default = '.widget-acf.'.sanitize_title($title_widget).' {}';
			$this->writeWidget($new_widget, $file_default);
		endif;
		
		unset($widgets['field_salvar_widget_acf']);
		delete_field('field_salvar_widget_acf', 'option');
		
		unset($widgets['field_button_salvar_widget_acf']);
		delete_field('field_button_salvar_widget_acf', 'option');
		
		foreach($widgets as $key => $widget):
			delete_field($key, 'option');
			
			// if(!$alert){
				
				$dir_widget = $path.'/'.str_replace('_','-',str_replace('field_group_','',$key));
				$key = str_replace('field_group_','',$key);
				
				// style.scss
				$style = $dir_widget."/style.scss";
				$this->writeWidget($style, stripslashes($widget['field_style_'.$key]) );
				
				// index.blade.php
				$index = glob("{$dir_widget}/index.*");
				if(function_exists('\App\template') || function_exists('\Roots\view')){
					
					$index = $dir_widget."/index.blade.php";
				}else{
					
					$index = $dir_widget."/index.php";
				}
				
				$this->writeWidget($index, stripslashes($widget['field_index_'.$key]) );
				
				// app.js
				$js = $dir_widget."/app.js";
				$this->writeWidget($js, stripslashes($widget['field_javascript_'.$key]) );
				
				// functions.php
				$functions = $dir_widget."/functions.php";
				$this->writeWidget($functions, stripslashes($widget['field_functions_'.$key]) );
				
				// capa.png
				$capa = $dir_widget."/main.png";
				$url_capa = wp_get_attachment_image_src($widget['field_capa_'.$key], 'full');
				if(!empty($url_capa)){
					$capa_img = $this->openWidget($url_capa[0]);
					
					$this->writeWidget($capa, $capa_img);
				}
				
				// name
				$name = $widget['field_name_'.$key];
				rename($dir_widget, $path.'/'.sanitize_title($name) );
				
				$alert = 'Widgets atualizados com sucesso!';
				
			// }
		endforeach;
		
		echo '<script>alert("'.$alert.'");window.location="'.admin_url('admin.php?page=acf-options-todos-os-widgets').'";</script>';
		die();
	}
	
	public function writeWidget($file, $text) {
		$fp = fopen($file, 'w');
		fwrite($fp, $text );
		fclose($fp);
	}
	
	public function openWidget($file) {
		$file  = fopen($file, 'r');
		$lines_file = '';

		while(! feof($file)):
			$line = fgets($file);
			$lines_file .= $line;
		endwhile;
		
		fclose($file);
		
		return $lines_file;
	}
	
	public function exportJsonFields($meta_id, $id_post, $meta_key, $number, $widget_location='') {
		$groups = acf_get_field_groups();
		$json = [];
		
		foreach ($groups as $key => $group) {
			
			if($id_post==$group['ID'] && empty($widget_location)){
				
				$widget_location = $group['location'][0][0]['value'];
				
			}
		}
		
		foreach ($groups as $key => $group) {
			
			if($group['location'][0][0]['param']=='widget_acf'){
				
				if($widget_location == $group['location'][0][0]['value']){
					
					// Fetch the fields for the given group key
					$fields = acf_get_fields($group['key']);
					
					// Remove unecessary key value pair with key "ID"
					unset($group['ID']);
					
					foreach ($fields as $key_f => $field) {
						unset($fields[$key_f]['ID']);
						unset($fields[$key_f]['parent']);
					}
					
					// Add the fields as an array to the group
					$group['fields'] = $fields;
					
					// Add this group to the main array
					$json[] = $group;
				}
			}
		}
		if(!empty($json)){
			
			$json = json_encode($json, JSON_PRETTY_PRINT);
			
			$widget_dir = str_replace('_', '-', $widget_location);
			
			$path =
			function_exists('\App\template') || function_exists('\Roots\view') ? 
			get_template_directory() . '/views/widgets-templates/' :
			get_template_directory() . '/widgets-templates/';
			
			$file = $path . $widget_dir . '/fields.json';
			
			file_put_contents($file, $json );
		}
    }
    
    /*=========================================================
	=            Criando o Menu e os campos em acf            =
	=========================================================*/
	
	public function addAdminMenu() {
		global $plugin_nome;
		acf_add_options_page(array(
			'page_title' 	=> 'Configurações - ' . $plugin_nome,
			'menu_title' 	=> $plugin_nome,
			'icon_url'		=> 'dashicons-layout',
			'redirect' 		=> false
		));
		
		acf_add_options_sub_page(array(
			'page_title'  => 'Todos os Widgets',
			'menu_title'  => 'Todos os Widgets',
			'parent_slug' => 'acf-options-' . sanitize_title($plugin_nome),
		));
		
		add_theme_support('post-thumbnails');
		
		return $plugin_nome;
	}

}