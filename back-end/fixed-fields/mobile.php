<?php 

$sub_fields[] = array(
	'key' => 'accordion_settings_' . $prefixed_widget,
	'label' => 'Configurações do widget',
	'name' => 'accordion_settings',
	'type' => 'accordion',
);

$sub_fields[] = array(
	'key' => 'display_mobile_' . $prefixed_widget,
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
);

$sub_fields[] = array(
	'key' => 'display_tablet_' . $prefixed_widget,
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
);

$sub_fields[] = array(
	'key' => 'display_desktop_' . $prefixed_widget,
	'label' => 'Desktop',
	'name' => 'display_desktop_',
	'type' => 'true_false',
	'ui' => 1,
	'ui_on_text' => 'Exibir',
	'ui_off_text' => 'Esconder',
	'default_value' => true,
	'wrapper' => array (
		'width' => 33,
	),
);

$sub_fields[] = array(
	'key' => 'class_' . $prefixed_widget,
	'label' => 'Classes',
	'name' => 'class',
	'type' => 'text',
	'prepend' => '.',
);

$sub_fields[] = array(
	'key' => 'margin_' . $prefixed_widget,
	'label' => 'Margin',
	'name' => 'margin',
	'type' => 'group',
	'sub_fields' => array(
		array(
			'key' => 'margin_top',
			'label' => 'Top',
			'name' => 'margin_top',
			'type' => 'number',
			'append' => 'px',
			'placeholder' => '0',
			'wrapper' => array (
				'width' => 50,
			),
		),
		array(
			'key' => 'margin_bottom',
			'label' => 'Bottom',
			'name' => 'margin_bottom',
			'type' => 'number',
			'append' => 'px',
			'placeholder' => '0',
			'wrapper' => array (
				'width' => 50,
			),
		)
	)
);

$sub_fields[] = array(
	'key' => 'accordion_settings_endpoint_' . $prefixed_widget,
	'type' => 'accordion',
	'endpoint' => 1,
);