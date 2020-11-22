<?php
Class AcfWidget {

	static function get_base(){
		include_once('fields-layout-settings.php');

		$base_fields = array(
			'key' => 'group_widgets_acf',
			'title' => 'Widgets',
			'fields' => array(
				array(
					'key' => 'widgets',
					'label' => 'Layout de widgets',
					'name' => 'widgets',
					'type' => 'repeater',
					'min' => 1,
					'layout' => 'block',
					'button_label' => 'Adicionar seção',
					'sub_fields' => array (
						array(
							// 'key' => 'field_tamanho_grid',
							// 'label' => 'Tamanhos',
							// 'name' => 'tamanho-grid',
							// 'type' => 'radio',
							// 'instructions' => '',
							// 'required' => 0,
							// 'conditional_logic' => 0,
							// 'wrapper' => array (
							// 	'width' => '',
							// 	'class' => '',
							// 	'id' => '',
							// 	),
							// 'choices' => array (
							// 	'full' => '',
							// 	'1' => '',
							// 	'2' => '',
							// 	'2_1' => '',
							// 	'2_2' => '',
							// 	'3' => '',
							// 	'3_1' => '',
							// 	'4' => '',
							// 	// '5' => '',
							// 	'6' => ''
							// 	),
							// 'allow_null' => 0,
							// 'other_choice' => 0,
							// 'save_other_choice' => 0,
							// 'default_value' => 3,
							// 'layout' => 'vertical',
							// 'return_format' => 'value',
							),
						1 => array(),
						1.5 => $fields_layout_settings,
						2 => array (
							'key' => 'widgets_contents',
							'label' => '',
							'name' => 'widgets_contents',
							'type' => 'flexible_content',
							'instructions' => 'Insira, pegue e arraste os widgets selecionados.',
							'button_label' => '<span class="dashicons dashicons-plus-alt2 acf-js-tooltip" title="Adicionar Widget"></span>',
							'layouts' => array (),//Layout widgets
						),
					),
				)
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'widget-reusable',
					),	
				),
			),
			'menu_order' => 0,
			'position' => 'acf_after_title',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => array (
				0 => 'the_content'
			),
			'active' => 1,
			'description' => '',
		);

		return $base_fields;
	}

}
?>