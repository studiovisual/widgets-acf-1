<?php

Class Utils {

    /**
	* parseWidgetHeaders Realiza o parse do header com informações do widget
	*
	* @param  mixed $widgetController Widget que será realizado o parse
	* @return array Array com as informações do widget
	*/
	public static function parseWidgetHeaders($widgetController) {
		$url = '';
		
		if(strpos(dirname($widgetController), 'themes') !== false):
			$url =
				function_exists('\App\template') || function_exists('\Roots\view') ? 
					get_template_directory_uri() . '/views/widgets-templates/' :
					get_template_directory_uri() . '/widgets-templates/';
		else:
			$url = plugins_url('/widgets-templates/' , dirname(__FILE__));
		endif;

		$name = basename(dirname($widgetController));
		$name = self::cleanWidgetName($name);
		
		$headers['name'] = $name;
		$headers['cover'] = '';

		$images = glob(dirname($widgetController) . "/main.*");
		if(count($images) > 0)
			$headers['cover'] = $url . basename(dirname($widgetController)) . '/' . basename($images[0]);
		
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

    public static function getPostTypes() {
		$all_types = get_post_types();

		unset(
			// $all_types['post'],
			$all_types['page'],
			$all_types['attachment'],
			$all_types['revision'],
			$all_types['nav_menu_item'],
			$all_types['custom_css'],
			$all_types['customize_changeset'],
			$all_types['acf-field-group'],
			$all_types['acf-field']
		);

		return $all_types;
	}

	public static function getModels(){
		$templates = ['default' => 'Modelo padrão'];
		$templates = array_merge($templates, wp_get_theme()->get_page_templates());

		$models = array();

		foreach($templates as $template_name => $template_filename ) {
			$models[$template_name] = $template_filename;
		}

		return $models;
	}

	public static function getPages() {
        $all_pages = array();
        $pages = get_posts(
            array(
                'post_type' => 'page',
                'posts_per_page' => '-1'
            )
        );

		foreach($pages as $page)
			$all_pages[$page->ID] = $page->post_title;

		return $all_pages;
	}

	public static function getFonts() {
		$credencial_default = 'AIzaSyApghjlJklghhHAwfHTPsqbEimDUIvdEXM';

		if(!empty(get_field('widget_key_credenticalgoogle_widget_acf', 'options')))
			$credencial_default = get_field('widget_key_credenticalgoogle_widget_acf', 'options');

		$url = "https://www.googleapis.com/webfonts/v1/webfonts?key=" . $credencial_default;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
		curl_close($ch);
		$result_fonts = array();

		$result = json_decode($result);

		if(!empty($result->items)):
			foreach($result->items as $font)
				$result_fonts[$font->family . '--' . implode(';', $font->variants)] = $font->family;
        endif;

		return $result_fonts;
	}

	public static function getFontsAjax() {
		header( "Content-type: application/json");
		$show_fonts = get_field('widgets_acf_show_fonts', 'options');
		
		if(!$show_fonts)
			die(json_encode(array()));

		$fonts_selected = get_field('fonts_types_widget_acf', 'options');
		$fonts_selected_strings = array();
		$weights = array();

		foreach($fonts_selected as $font):
			$font_string = explode('--', $font);
			$weights[] = array(str_replace(' ', '_', $font_string[0]) => $font_string[1]);
			$fonts_selected_strings[] = $font_string[0];
        endforeach;

		die(
            json_encode(
                array(
                    'fonte' => $fonts_selected_strings, 
                    'weights' => $weights
                )
            )
        );
	}

	public static function getTaxonomies(){
		$all_taxonomies = get_taxonomies();
		$all_taxonomies['category'] = 'Categoria Padrão / Category';

		unset(
			$all_taxonomies['post_tag'],
			$all_taxonomies['nav_menu'],
			$all_taxonomies['link_category'],
			$all_taxonomies['post_format']
		);

		return $all_taxonomies;
    }
    
    public static function getCodes() {
		// global $widgets;
        $widgets = array();
        $path =
            function_exists('\App\template') || function_exists('\Roots\view') ? 
                get_template_directory() . '/views/widgets-templates' :
                get_template_directory() . '/widgets-templates';
        
        if(!is_dir($path))
            return $widgets;
        
        $path_uri =
            function_exists('\App\template') || function_exists('\Roots\view') ? 
                get_template_directory_uri() . '/views/widgets-templates' :
                get_template_directory_uri() . '/widgets-templates';
        
        $dir = new DirectoryIterator($path);
        $css_widgets = '';
        $js_widgets = '';

        foreach($dir as $fileinfo):
            if($fileinfo->isDir() && !$fileinfo->isDot()):
                $widget_name =  str_replace('-', '_', $fileinfo->getFilename());
                $widget_label = Utils::parseWidgetHeaders($path . '/' . $fileinfo->getFilename() . '/functions.php');
				$widgets[$widget_name]['title'] = $widget_label['title'];
				$widgets[$widget_name]['cover'] = $widget_label['cover'];
                $dir_widget = $path . '/' . $fileinfo->getFilename();
                $style = glob("{$dir_widget}/style.*");

                if($style):
                    $widgets[$widget_name]['style'] = self::openFile($style[0]);
                    $css_widgets .= $widgets[$widget_name]['style'];
                endif;
                
                $index = glob("{$dir_widget}/index.*");
                if($index)
                    $widgets[$widget_name]['index'] = self::openFile($index[0]);
                
                $js = glob("{$dir_widget}/app.js");
                if($js):
                    $widgets[$widget_name]['js'] = self::openFile($js[0]);
                    $js_widgets .= $widgets[$widget_name]['js'];
                endif;
                
                $functions = glob("{$dir_widget}/functions.php");
                if($functions)
                    $widgets[$widget_name]['functions'] = self::openFile($functions[0]);
                
                $json = glob("{$dir_widget}/fields.json");
                if($json):
                    $json = json_decode(self::openFile($json[0]), true);
                    foreach($json as $field_group):
                        // Search database for existing field group.
                        $post = acf_get_field_group_post( $field_group['key'] );
                        if($post)
                            $field_group['ID'] = $post->ID;
                        
                        // Import field group.
                        $field_group = acf_import_field_group( $field_group );
                    endforeach;
                endif;
                
                $widgets[$widget_name]['name'] = $widget_label['title'];
            endif;
        endforeach;

        return $widgets;
    }

    public static function openFile($file) {
        $file  = fopen($file, 'r');
        $lines_file = '';

        while(!feof($file)):
            $line = fgets($file);
            $lines_file .= $line;
        endwhile;
        
        fclose($file);
        
        return $lines_file;
    }

    public static function getFields($widget, $fields) {
		$fields = self::getFieldsGroups($widget, acf_get_local_store('groups')->data);

		if(empty($fields))
			$fields = self::getFieldsGroups($widget, acf_get_field_groups());

		$fields_base = array (
			'key' => $widget['name'].'_widget_acf_key',
			'name' => $widget['name'].'_widget_acf',
			'label' => !empty($widget['title']) ? $widget['title'] : str_replace('-', ' ', str_replace('_', ' ', $widget['name'])),
			'display' => 'line',
			'sub_fields' => $fields,
		);

		return $fields_base;
    }
    
    /**
	 * getFieldsGroups Retorna todos os campos de grupos cadastrados para o widget
	 *
	 * @param  mixed $widget Nome do widget
	 * @return array Array de campos cadastrados
	 */
	public static function getFieldsGroups($widget, $data) {	
		$result = array();
		// $all_field_groups = acf_get_field_groups();
		// $all_field_groups = acf_get_local_store('groups')->data;

		foreach($data as $field_group):
			foreach($field_group['location'] as $group_location_rules):
				foreach($group_location_rules as $rule):
					if($rule['param'] == 'widget_acf' && $rule['operator'] == '==' && $rule['value'] == $widget['name']):

						$fields = acf_get_fields($field_group['key']);

						foreach($fields as $field):
							$field['wrapper']['class'] = $field['key'];
							$field['key'] = $field['name'] . '_' . $widget['name'];

							array_push($result, $field);
						endforeach;
					endif;
				endforeach;
			endforeach;
		endforeach;
		
		return $result;
	}

	static function getTemplates($layout_content) {
		$html = '';
		$css_widgets = '';
		global $js_widgets;
		
		foreach($layout_content as $layout):
			$style_attr = array();
			$style_bg = array();

			if(!empty($layout['attr']['layout_padding']['layout_padding_top']))
				$style_attr['padding_top'] = 'padding-top: ' . $layout['attr']['layout_padding']['layout_padding_top'] . 'px;';

			if(!empty($layout['attr']['layout_padding']['layout_padding_right']))
				$style_attr['padding_right'] = 'padding-right: ' . $layout['attr']['layout_padding']['layout_padding_right'] . 'px;';

			if(!empty($layout['attr']['layout_padding']['layout_padding_bottom']))
				$style_attr['padding_bottom'] = 'padding-bottom: ' . $layout['attr']['layout_padding']['layout_padding_bottom'] . 'px;';

			if(!empty($layout['attr']['layout_padding']['layout_padding_left']))
				$style_attr['padding_left'] = 'padding-left: ' . $layout['attr']['layout_padding']['layout_padding_left'] . 'px;';

			if($layout['attr']['layout_background'] == 'image' && !empty($layout['attr']['layout_background_image'])): 
				$img_bg = wp_get_attachment_url($layout['attr']['layout_background_image']);

				$style_bg['layout_background_image'] = 'background-image: url(' . $img_bg . ');';
				$style_bg['background_size'] = 'background-size: cover;';
				$style_bg['background_repeat'] = 'background-repeat: no-repeat;';
				$style_bg['background_position'] = 'background-position: center center;';
			endif;

			if($layout['attr']['layout_background'] == 'color' && !empty($layout['attr']['layout_background_color']))
				$style_bg['layout_background_color'] = 'background-color: ' . $layout['attr']['layout_background_color'] . ';';

			$class_setting = $layout['attr']['layout_id_class']['layout_class'];
			$id_setting = trim($layout['attr']['layout_id_class']['layout_id']);
			
			$class_setting .= empty($layout['attr']['layout_display_mobile']) ? ' d-none' : ' d-flex';
			$class_setting .= empty($layout['attr']['layout_display_tablet']) ? ' d-md-none' : ' d-md-flex';
			$class_setting .= empty($layout['attr']['layout_display_desktop']) ? ' d-lg-none' : ' d-lg-flex';
			$class_setting = trim($class_setting);
			
			$html .= "<section";
			if(!empty($id_setting))
				$html .= " id=\"{$id_setting}\"";
			if(!empty($class_setting))
				$html .= " class=\"{$class_setting}\"";
			if(!empty($style_bg) || !empty($style_attr) || !empty($layout['attr']['layout_custom_css']))
				$html .= " style=\"" . implode(' ', $style_bg) . ' ' . implode(' ', $style_attr) . $layout['attr']['layout_custom_css'] . "\"";
			
			$html .= ">";
			
			if($layout['attr']['layout_width'] == 'container')
				$html .= '<div class="container">';
			else
				$html .= '<div class="container-fluid px-0">';

			$align = ' align-items-' . $layout['attr']['layout_align']['layout_align_vertical'];
			$align .= ' justify-content-' . $layout['attr']['layout_align']['layout_align_horizontal'];
			
			$html .= '<div class="row' . $align . ($layout['attr']['layout_width'] != 'container' ? ' mx-0' : '') .'">';
			
			$count_column = 0;
			
			foreach($layout as $w_content):
				if(!array_key_exists('content', $w_content))
					continue;
				
				$layout_widget = str_replace('_widget_acf', '', $w_content['layout']);
				$widget_name = str_replace('_', '-', $layout_widget);
				$dir_widget = get_template_directory() . "/widgets-templates/{$widget_name}";
				$url_widget = get_template_directory_uri() . "/widgets-templates/{$widget_name}";
				$plugin_widget = false;
				
				if(function_exists('\App\template') || function_exists('\Roots\view')):
					$dir_widget = get_template_directory() . "/views/widgets-templates/{$widget_name}";
					$url_widget = get_template_directory_uri() . "/views/widgets-templates/{$widget_name}";
				endif;
				
				if(!is_dir($dir_widget)):
					$dir_widget = plugin_dir_path(__FILE__) . "../widgets-templates/{$widget_name}";
					$plugin_widget = true;
				endif;
				
				$files = glob("{$dir_widget}/index.*");
				
				if(empty($files))
					continue;
				
				$columns = $w_content['content']['field_grid_columns_mobile_' . $layout_widget . '_widget_acf_key'];
				$columns .= ' ' . $w_content['content']['field_grid_columns_tablet_' . $layout_widget . '_widget_acf_key'];
				$columns .= ' ' . $w_content['content']['field_grid_columns_desktop_' . $layout_widget . '_widget_acf_key'];
				$style = '';
				$margin_top = $w_content['content']['margin_' . $layout_widget . '_widget_acf_key']['margin_top'];
				$margin_bottom = $w_content['content']['margin_' . $layout_widget . '_widget_acf_key']['margin_bottom'];

				if(!empty($margin_top))
					$style .= "margin-top: {$margin_top}px;";
				if(!empty($margin_bottom))
					$style .= "margin-bottom: {$margin_bottom}px;";

				if(!empty($style))
					$style = "style=\"{$style}\"";
				
				$html .= sprintf(
					"<div class=\"widget-acf {$widget_name} {$columns}%s\"%s>", 
					empty($w_content['class']) ? "" : " " . $w_content['class'], 
					empty($style) ? "" : " " . $style
				);
				
				ob_start();
				
				$fields = $w_content['content'];
				global $widget;
				$widget = new stdClass();
				$widget->fields = self::parseFields($fields);

				$widget->layout = $layout['attr'];
				foreach($widget->layout as $key => $layout_attr):
					$widget->layout[str_replace('layout_', '', $key)] = $layout_attr;
					unset($widget->layout[$key]);
				endforeach;

				$widget = apply_filters("widgetsacf_{$widget_name}_data", $widget);
				
				if(function_exists('\App\template') && !$plugin_widget):
					$template = "widgets-templates.{$widget_name}.index";
					
					echo \App\template($template, ['widget' => $widget]);
				elseif(function_exists('\Roots\view') && !$plugin_widget):
					$template = "widgets-templates.{$widget_name}.index";
					
					echo \Roots\view($template, ['widget' => $widget]);
				else:
					include "{$files[0]}";
				endif;
					
				$html .= ob_get_clean();
				$html .= '</div>';
				
				$css_widgets .= self::enqueueStyle($dir_widget, $url_widget, $widget_name);
				$js_widgets .= self::enqueueScript($dir_widget, $url_widget, $widget_name);

				$count_column++;
			endforeach;
				
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</section>';
		endforeach;
			
		$url_widgets = get_template_directory();

        if(get_field('widgets_acf_enquee_css', 'options')):
            // css widgets
            require  plugin_dir_path(__FILE__) . "../scssphp/scss.inc.php";
            $scss = new scssc();
            $scss->setFormatter("scss_formatter_compressed");
            $css_widgets = $scss->compile($css_widgets);
            $dir_css_widget = $url_widgets . '/widgets_acf.css';
            $fp = fopen($dir_css_widget, 'w');
            fwrite($fp, $css_widgets);
            fclose($fp);

            // Enqueue style
            wp_enqueue_style("widget/widgets_acf", get_template_directory_uri() . '/widgets_acf.css', array(), false, 'all');
        endif;

		if(get_field('widgets_acf_enquee_js', 'options')):
            // js widgets
            require_once(plugin_dir_path(__FILE__) . '../JShrink/Minifier.php');
			$js_widgets = \JShrink\Minifier::minify($js_widgets);

			$dir_js_widget = $url_widgets . '/widgets_acf.js';
	
			if(wp_script_is('widget/widgets_acf')):
				wp_dequeue_script('widget/widgets_acf');
			endif;
	
    		$fp = fopen($dir_js_widget, 'w');        
            fwrite($fp, $js_widgets);
			fclose($fp);

            // Enqueue script
			wp_enqueue_script("widget/widgets_acf", get_template_directory_uri() . '/widgets_acf.js', array(), false, true);
        endif;

		return $html;
	}

	/**
	* enqueueStyle Adiciona o css do widget em páginas que ele for utilizado
	*
	* @param  mixed $dir_widget Caminho completo do widget
	* @param  mixed $url_widget URL do widget a partir da raiz do Wordpress
	* @param  mixed $widget_name Nome do widget
	* @return void
	*/
	private static function enqueueStyle($dir_widget, $url_widget, $widget_name) {
		$style = "/style.scss";
		
		if(!file_exists($dir_widget . $style))
			return;
		
		return self::openFile($dir_widget . $style);
	}

	/**
	* enqueueScript Adiciona o script do widget em páginas que ele for utilizado
	*
	* @param  mixed $dir_widget Caminho completo do widget
	* @param  mixed $url_widget URL do widget a partir da raiz do Wordpress
	* @param  mixed $widget_name Nome do widget
	* @return void
	*/
	private static function enqueueScript($dir_widget, $url_widget, $widget_name) {
		$script = "/app.js";
		
		if(!file_exists($dir_widget . $script))
			return;
		
		return self::openFile($dir_widget . $script);
	}

	static function parseFields(&$fields) {
		$final = array();

		foreach($fields as $field_key => $field):
			$field_object = get_field_object($field_key);

			if(!empty($field_object)) {
				$final[$field_object['name']] = $field;
				
				if(is_array($final[$field_object['name']]))
					$final[$field_object['name']] = self::parseFields($final[$field_object['name']]);
			}
			else {
				$final[$field_key] = $field;
				if(is_array($final[$field_key]))
					$final[$field_key] = self::parseFields($final[$field_key]);
			}
		endforeach;
		
		return $final;
	}

}