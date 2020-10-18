<?php

// Cria e define a grid de widget via ADM
Class Widgets {

	public function __construct() {
		require("acf/widgets-base.php");

		$this->setWidgetsList();
	}

	private function setWidgetsList() {
		if(!function_exists('acf_add_local_field_group'))
			return;
			
		add_action('admin_head', array($this, 'widgetCSS'), 1);

		$acf_base = AcfWidget::get_base();
		$directory_widgets = 
			function_exists('\App\template') || function_exists('\Roots\view') ? 
				$this->getWidgets(get_template_directory() . '/views/widgets-templates') :
				$this->getWidgets(get_template_directory() . '/widgets-templates');

		if(empty($directory_widgets))
			$directory_widgets = $this->getWidgets(plugin_dir_path(__FILE__) . '../more-widgets-templates');

		$widget_adm = $this->getPagesSelected();

		Painel::field_color($widget_adm['taxonomy']);

		// Define widget em post_type selecionados
		if(!empty($widget_adm['post_type'])):
			foreach($widget_adm['post_type'] as $page):
				$acf_base['location'][][] = array(
					'param'=>'post_type',
					'operator'=>'==',
					'value'=>$page,
				);

				$posts = get_post(
					array(
						'post_type' => $page,
						'posts_per_page' => -1,
					)
				);

				foreach($posts as $post):
					if($post->post_content != '[acf_widgets]'):
						$my_post = array(
							'ID' => $post->ID, 
							'post_content' => '[acf_widgets]',
						);

						wp_update_post($my_post);
					endif;
				endforeach;
			endforeach;
		endif;

		// Define widget em  paginas selecionadas
		if(!empty($widget_adm['page'])):
			foreach($widget_adm['page'] as $page):
				$acf_base['location'][][] = array(
					'param' => 'page',
					'operator' => '==',
					'value' => $page,
				);

				$post = get_post($page);
				if($post->post_content != '[acf_widgets]'):
					$my_post = array(
						'ID' => $post->ID, 
						'post_content' => '[acf_widgets]',
					);

					wp_update_post($my_post);
				endif;
			endforeach;
		endif;

		// Define widget em  modelos selecionadas
		if(!empty($widget_adm['models'])):
			foreach($widget_adm['models'] as $model):
				$acf_base['location'][][] = array(
					'param' => 'page_template',
					'operator' => '==',
					'value' => $model,
				);

				$pages = get_posts(
					array(
						'post_type' => 'page',
						'meta_key' => '_wp_page_template',
						'meta_value' => $model,
					)
				);

				foreach($pages as $page):
					if($page->post_content != '[acf_widgets]'):
						$my_post = array(
							'ID' => $page->ID, 
							'post_content' => '[acf_widgets]',
						);

						wp_update_post($my_post);
					endif;
				endforeach;
			endforeach;
		endif;

		// Define widget em categorias selecionadas
		if(!empty($widget_adm['taxonomy'])):
			foreach($widget_adm['taxonomy'] as $page):
				$acf_base['location'][][] = array(
					'param' => 'taxonomy',
					'operator' => '==',
					'value' => $page,
				);

				$terms = get_terms($page, array('hide_empty' => false));

				foreach($terms as $term):
					if($term->description!='[acf_widgets]')
						wp_update_term($term->term_id, $page, array('name' => $term->name, 'description' => '[acf_widgets]'));
				endforeach;
			endforeach;
		endif;

		$acf_base['fields'][0]['sub_fields'][2]['wrapper']['class'] = 'column_3';

		foreach($directory_widgets as $widget):
			$widget['sub_fields'] = $this->setMobileFields($widget['sub_fields'], $widget['key']);
			$acf_base['fields'][0]['sub_fields'][2]['layouts'][] = $widget;
		endforeach;

		acf_add_local_field_group($acf_base);
	}

	public function getPagesSelected() {
		$posttypes_admin = get_field('type_widget_acf', 'options');
		$pages_admin = get_field('page_widget_acf', 'options');
		$models_admin = get_field('models_widget_acf', 'options');
		$taxonomies_admin = get_field('tax_widget_acf', 'options');

		$pgs = array();

		$pgs['post_type'] = $posttypes_admin;
		$pgs['page'] = $pages_admin;
		$pgs['models'] = $models_admin;
		$pgs['taxonomy'] = $taxonomies_admin;

		return $pgs;
	}

	public function setMobileFields($sub_fields, $prefixed_widget=null) {
		$sb = $sub_fields;
		$sub_fields = array();

		include "fixed-fields/mobile.php";
		include "fixed-fields/layouts.php";

		$sub_fields = array_merge($sub_fields, $sb);

		return $sub_fields;
	}
	
	/**
	 * getWidgets Recupera todos os widgets do caminho informado
	 *
	 * @param  mixed $path Caminho dos widgets
	 * @return array Array de widgets encontrados no diretório informado
	 */
	public function getWidgets($path) {
		$widgets = array();

		if(!is_dir($path))
			return $widgets;

		$dir = new DirectoryIterator($path);

		foreach($dir as $fileinfo):
			if($fileinfo->isDir() && !$fileinfo->isDot()):
				$file = "{$path}/{$fileinfo->getFilename()}/functions.php";
				
				if(!file_exists($file))
					continue;

				$fields=array();
				$widget = Utils::parseWidgetHeaders($file);

				require($file);

				$widgets[$widget['name']] = Utils::getFields($widget, $fields);
			endif;
		endforeach;

		return $widgets;
	}

	public function widgetCSS() {
		$path =
        	function_exists('\App\template') || function_exists('\Roots\view') ? 
				get_template_directory() . '/views/widgets-templates' :
				get_template_directory() . '/widgets-templates';
		
		$temp =
			function_exists('\App\template') || function_exists('\Roots\view') ? 
				get_template_directory_uri() . '/views/widgets-templates/' :
				get_template_directory_uri() . '/widgets-templates/';

		if(!is_dir($path)):
			$path = plugin_dir_path( __FILE__ ) . "../more-widgets-templates";
			$temp = plugins_url('/more-widgets-templates/' , dirname(__FILE__));
		endif;

		$dir = new DirectoryIterator($path);

		foreach($dir as $fileinfo):
			$widget_name = $fileinfo->getFilename();
			$widget_class = str_replace('-', '_', str_replace('-', '_', str_replace('-', '_', $widget_name)));

			echo 
				'<style>
					.acf-field-widgets .acf-flexible-content .values .layout[data-layout="' . $widget_class . '_widget_acf"] .widgets-acf-fc-placeholder, .acf-fc-popup a[data-layout="' . $widget_class . '_widget_acf"] {
						background-image: url("' . $temp . $widget_name . '/main.png") !important;
					}
				</style>';
		endforeach;
	}

}