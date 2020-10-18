<?php 
if(function_exists('acf_add_local_field_group')):
	global $plugin_nome;

	$types = Utils::getPostTypes();
	$pages = Utils::getPages();
	$models = Utils::getModels();
	$categories = Utils::getTaxonomies();
	$fonts = Utils::getFonts();

	acf_add_local_field_group(array(
		'key' => 'group_widget',
		'title' => 'Widget config',
		'fields' => array(
			array(
				'key' => 'widget_acf_enquee_js',
				'label' => 'Incorporar widgets.js',
				'name' => 'widgets_acf_enquee_js',
				'type' => 'true_false',
				'ui' => 1,
				'ui_on_text' => 'Sim',
				'ui_off_text' => 'Não',
			),
			array(
				'key' => 'widget_acf_enquee_css',
				'label' => 'Incorporar widgets.css',
				'name' => 'widgets_acf_enquee_css',
				'type' => 'true_false',
				'ui' => 1,
				'ui_on_text' => 'Sim',
				'ui_off_text' => 'Não',
			),
			array(
				'key' => 'widget_acf_show_bootstrap',
				'label' => 'Incorporar Bootstrap via plugin',
				'name' => 'widgets_acf_show_bootstrap',
				'type' => 'true_false',
				'ui' => 1,
				'ui_on_text' => 'Sim',
				'ui_off_text' => 'Não',
			),
			array(
				'key' => 'widget_key_credenticalgoogle_widget_acf',
				'label' => 'Key Credencial do Google',
				'name' => 'widget_key_credenticalgoogle_widget_acf',
				'type' => 'text',
				'conditional_logic' => array(
					array(
						array(
							'field' => 'widgets_acf_show_fonts',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
			),
			array(
				'key' => 'widget_fonts_types_widget_acf',
				'label' => 'Google Fonts',
				'name' => 'fonts_types_widget_acf',
				'type' => 'select',
				'instructions' => 'Selecione as fontes para seu site *Clique em atualizar',
				'conditional_logic' => array(
					array(
						array(
							'field' => 'widgets_acf_show_fonts',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'choices' => $fonts,
				'multiple' => 1,
				'ui' => 1,
				'ajax' => 0,
				'return_format' => 'value',
			),
			array(
				'key' => 'widgets_acf_show_fonts',
				'label' => 'Incorporar Google Fonts',
				'name' => 'widgets_acf_show_fonts',
				'type' => 'true_false',
				'ui' => 1,
				'ui_on_text' => 'Sim',
				'ui_off_text' => 'Não',
			),
			array(
				'key' => 'widget_type_widget_acf',
				'label' => 'Post type',
				'name' => 'type_widget_acf',
				'type' => 'select',
				'instructions' => 'Widgets em todos os posts',
				'choices' => $types,
				'multiple' => 1,
				'ui' => 1,
				'ajax' => 0,
				'return_format' => 'value',
			),
			array(
				'key' => 'widget_page_widget_acf',
				'label' => 'Páginas',
				'name' => 'page_widget_acf',
				'type' => 'select',
				'instructions' => 'Widgets em página especifica',
				'choices' => $pages,
				'multiple' => 1,
				'ui' => 1,
				'ajax' => 0,
				'return_format' => 'value',
			),
			array(
				'key' => 'widget_models_widget_acf',
				'label' => 'Modelos',
				'name' => 'models_widget_acf',
				'type' => 'select',
				'instructions' => 'Widgets em modelos de páginas',
				'choices' => $models,
				'multiple' => 1,
				'ui' => 1,
				'ajax' => 0,
				'return_format' => 'value',
			),
			array(
				'key' => 'widget_tax_widget_acf',
				'label' => 'Tipo de Categoria (taxonomy)',
				'name' => 'tax_widget_acf',
				'type' => 'select',
				'instructions' => 'Categoria que terá MetaBox de Widgets',
				'choices' => $categories,
				'multiple' => 1,
				'ui' => 1,
				'ajax' => 0,
				'return_format' => 'value',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'acf-options-' . sanitize_title($plugin_nome),
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'active' => 1,
	));
endif;