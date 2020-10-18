<?php

$fields_layout_settings = array(
	'key' => 'layout_settings',
	'label' => '',
	'name' => 'layout_settings',
	'type' => 'group',
	'layout' => 'block',
	'sub_fields' => array(
		array(
			'key' => 'layout_display_mobile',
			'label' => 'Mobile',
			'name' => 'display_mobile',
			'type' => 'true_false',
			'ui' => 1,
			'ui_on_text' => 'Exibir',
			'ui_off_text' => 'Esconder',
			'default_value' => true,
			'wrapper' => array (
				'width' => 33,
			),
		),
		array(
			'key' => 'layout_display_tablet',
			'label' => 'Tablet',
			'name' => 'display_tablet',
			'type' => 'true_false',
			'ui' => 1,
			'ui_on_text' => 'Exibir',
			'ui_off_text' => 'Esconder',
			'default_value' => true,
			'wrapper' => array (
				'width' => 33,
			),
		),
		array(
			'key' => 'layout_display_desktop',
			'label' => 'Desktop',
			'name' => 'display_desktop',
			'type' => 'true_false',
			'ui' => 1,
			'ui_on_text' => 'Exibir',
			'ui_off_text' => 'Esconder',
			'default_value' => true,
			'wrapper' => array (
				'width' => 33,
			),
		),

		// Exibição
		array(
			'key' => 'layout_accordion_exhibition',
			'label' => 'Exibição',
			'name' => 'accordion_exhibition',
			'type' => 'accordion',
		),
		array(
			'key' => 'layout_id_class',
			'label' => 'ID e classes',
			'name' => 'id_class',
			'type' => 'group',
			'layout' => 'block',
			'sub_fields' => array(
				array(
					'key' => 'layout_id',
					'label' => 'ID',
					'name' => 'id',
					'type' => 'text',
					'prepend' => '#',
					'wrapper' => array(
						'width' => 50,
					),
				),
				array(
					'key' => 'layout_class',
					'label' => 'Classe',
					'name' => 'class',
					'type' => 'text',
					'prepend' => '.',
					'wrapper' => array(
						'width' => 50,
					),
				),
			),
		),
		array(
			'key' => 'layout_width',
			'label' => 'Largura',
			'name' => 'width',
			'type' => 'button_group',
			'wrapper' => array(
				'width' => 33,
			),
			'choices' => array(
				'container' => 'Container',
				'full' => 'Expandido',
			),
			'layout' => 'horizontal',
			'return_format' => 'value',
		),
		array(
			'key' => 'layout_background',
			'label' => 'Background',
			'name' => 'background',
			'type' => 'button_group',
			'wrapper' => array(
				'width' => 33,
			),
			'choices' => array(
				'transparent' => 'Transparente',
				'color' => 'Cor',
				'image' => 'Imagem',
			),
			'default_value' => 'transparent',
			'return_format' => 'value',
		),
		array(
			'key' => 'layout_background_image',
			'label' => 'Imagem de fundo',
			'name' => 'layout_background_image',
			'type' => 'image',
			'conditional_logic' => array(
				array(
					array(
						'field' => 'layout_background',
						'operator' => '==',
						'value' => 'image',
					),
				),
			),
			'return_format' => 'array',
			'preview_size' => 'thumbnail',
			'library' => 'all',
			'wrapper' => array(
				'width' => 33,
			),
		),
		array(
			'key' => 'layout_background_color',
			'label' => 'Cor de fundo',
			'name' => 'layout_background_color',
			'type' => 'color_picker',
			'conditional_logic' => array(
				array(
					array(
						'field' => 'layout_background',
						'operator' => '==',
						'value' => 'color',
					),
				),
			),
			'wrapper' => array(
				'width' => 33,
			),
		),

		array(
			'key' => 'layout_accordion_position',
			'label' => 'Posicionamento',
			'name' => 'accordion_position',
			'type' => 'accordion',
		),
		array(
			'key' => 'layout_padding',
			'label' => 'Padding',
			'name' => 'padding',
			'type' => 'group',
			'layout' => 'block',
			'sub_fields' => array(
				array(
					'key' => 'layout_padding_top',
					'label' => 'Top',
					'name' => 'padding_top',
					'type' => 'number',
					'placeholder' => '0',
					'append' => 'px',
					'wrapper' => array(
						'width' => 25,
					),
				),
				array(
					'key' => 'layout_padding_left',
					'label' => 'Left',
					'name' => 'padding_left',
					'type' => 'number',
					'placeholder' => '0',
					'append' => 'px',
					'wrapper' => array(
						'width' => 25,
					),
				),
				array(
					'key' => 'layout_padding_bottom',
					'label' => 'Bottom',
					'name' => 'padding_bottom',
					'type' => 'number',
					'placeholder' => '0',
					'append' => 'px',
					'wrapper' => array(
						'width' => 25,
					),
				),
				array(
					'key' => 'layout_padding_right',
					'label' => 'Right',
					'name' => 'padding_right',
					'type' => 'number',
					'placeholder' => '0',
					'append' => 'px',
					'wrapper' => array(
						'width' => 25,
					),
				),
			),
		),
		array(
			'key' => 'layout_align',
			'label' => 'Alinhamento',
			'name' => 'align',
			'type' => 'group',
			'layout' => 'block',
			'sub_fields' => array(
				array(
					'key' => 'layout_align_horizontal',
					'label' => 'Horizontal',
					'name' => 'horizontal',
					'type' => 'radio',
					'layout' => 'horizontal',
					'choices' => array(
						'start' => 'Inicial',
						'center' => 'Centralizado',
						'end' => 'Final',
						'between' => 'Espaçado',
						'around' => 'Em torno',
					),
					'default_value' => 'start',
					'wrapper' => array(
						'width' => 50,
					),
				),
				array(
					'key' => 'layout_align_vertical',
					'label' => 'Vertical',
					'name' => 'vertical',
					'type' => 'radio',
					'layout' => 'horizontal',
					'choices' => array(
						'stretch' => 'Estendido',
						'start' => 'Inicial',
						'center' => 'Centralizado',
						'end' => 'Final',
					),
					'default_value' => 'stretch',
					'wrapper' => array(
						'width' => 50,
					),
				),
			),
		),
	),
);