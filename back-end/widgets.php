<?php

// Cria e define a grid de widget via ADM
Class Widgets {

	public function __construct() {
		add_filter('wp_insert_post_data', array($this, 'filter_post_data'), '99', 2);
		require("acf/widgets-base.php");

		$this->setWidgetsList();
	}

	private function setWidgetsList() {
		global $widgets;
		
		if(!function_exists('acf_add_local_field_group'))
			return;

		$acf_base = AcfWidget::get_base();
		$directory_widgets = 
			function_exists('\App\template') || function_exists('\Roots\view') ? 
				$this->getWidgets(get_template_directory() . '/views/widgets-templates') :
				$this->getWidgets(get_template_directory() . '/widgets-templates');

		$widgets = array_merge($directory_widgets, $this->getWidgets(plugin_dir_path(__FILE__) . '../widgets-templates'));
		uasort($widgets, array($this, 'sortWidgets'));

		$widget_adm = $this->getPagesSelected();

		Painel::field_color($widget_adm['taxonomy']);

		if(is_admin()):
			session_start();

			if(!empty($_GET['post']) && get_post_type() == 'widget-reusable')
				$_SESSION['session_' . $_GET['post']] = true;

			// Define widget em post_type selecionados
			if(!empty($widget_adm['post_type']) && !empty($_GET['post'])):
				$post_type = get_post_type($_GET['post']);

				if(in_array($post_type, $widget_adm['post_type'])):
					$acf_base['location'][][] = array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => $post_type,
					);

					if(!empty($_GET['post']))
						$_SESSION['session_' . $_GET['post']] = true;
				endif;
			endif;

			// Define widget em paginas selecionadas
			if(!empty($widget_adm['page']) && !empty($_GET['post'])):
				if(in_array($_GET['post'], $widget_adm['page'])):
					$acf_base['location'][][] = array(
						'param' => 'page',
						'operator' => '==',
						'value' => $_GET['post'],
					);

					$_SESSION['session_' . $_GET['post']] = true;
				endif;
			endif;

			// Define widget em modelos selecionadas
			if(!empty($widget_adm['models']) && !empty($_GET['post']) && get_post_type($_GET['post']) == 'page'):
				$current_model = get_post_meta($_GET['post'], '_wp_page_template', true);

				if(in_array($current_model, $widget_adm['models'])):
					$acf_base['location'][][] = array(
						'param' => 'page_template',
						'operator' => '==',
						'value' => $current_model,
					);

					$_SESSION['session_' . $_GET['post']] = true;
				endif;
			endif;

			// Define widget em categorias selecionadas
			if(!empty($widget_adm['taxonomy']) && !empty($_GET['taxonomy']) && !empty($_GET['tag_ID'])):
				if(in_array($_GET['taxonomy'], $widget_adm['taxonomy'])):
					$acf_base['location'][][] = array(
						'param' => 'taxonomy',
						'operator' => '==',
						'value' => $_GET['taxonomy'],
					);

					$term = get_term($_GET['tag_ID'], $_GET['taxonomy']);
					wp_update_term($_GET['tag_ID'], $_GET['taxonomy'], array('name' => $term->name, 'description' => '[acf_widgets id="' . $_GET['tag_ID'] . '" taxonomy="' . $_GET['taxonomy'] . '"]'));
				endif;
			endif;
		endif;

		$acf_base['fields'][0]['sub_fields'][2]['wrapper']['class'] = 'column_3';

		foreach($widgets as $widget):
			$widget['fields']['sub_fields'] = $this->setMobileFields($widget['fields']['sub_fields'], $widget['fields']['key']);
			$acf_base['fields'][0]['sub_fields'][2]['layouts'][] = $widget['fields'];
		endforeach;

		acf_add_local_field_group($acf_base);
	}

	public function sortWidgets($a, $b) {
		return strcmp($a['data']['title'], $b['data']['title']);
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

				$widgets[$widget['name']]['data'] = $widget;
				$widgets[$widget['name']]['fields'] = Utils::getFields($widget, $fields);
			endif;
		endforeach;

		return $widgets;
	}

	function filter_post_data($data, $post) {
		if(isset($_SESSION['session_' . $post['ID']]) || $post['post_type'] == 'widget-reusable'):
			$data['post_content'] = '[acf_widgets id="' . $post['ID'] . '"]';
			unset($_SESSION['session_' . $post['ID']]);
		endif;

		return $data;
	}

}