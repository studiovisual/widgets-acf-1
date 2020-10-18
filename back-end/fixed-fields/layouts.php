<?php 

$sub_fields[] = array(
	'key' => 'widget_name_' . $prefixed_widget,
	'name' => 'widget_name',
	'type' => 'text',
);
$sub_fields[] = array(
	'key' => 'field_grid_columns_desktop_' . $prefixed_widget,
	'label' => '<span class="dashicons dashicons-desktop acf-js-tooltip" title="Colunas no desktop"></span>',
	'name' => 'columns_desktop',
	'type' => 'select',
	'wrapper' => array (
		'class' => 'grid-widget-settings grid-widget-settings--desktop',		
	),
	'choices' => array (
		'col-lg-12 px-lg-0' => '100%',
		'col-lg-12 px-lg-3' => '12/12',
		'col-lg-11 px-lg-3' => '11/12',
		'col-lg-10 px-lg-3' => '10/12',
		'col-lg-9 px-lg-3' => '9/12',
		'col-lg-8 px-lg-3' => '8/12',
		'col-lg-7 px-lg-3' => '7/12',
		'col-lg-6 px-lg-3' => '6/12',
		'col-lg-5 px-lg-3' => '5/12',
		'col-lg-4 px-lg-3' => '4/12',
		'col-lg-3 px-lg-3' => '3/12',
		'col-lg-2 px-lg-3' => '2/12',
		'col-lg-1 px-lg-3' => '1/12'
	),
	'default_value' => 'col-lg-12 px-lg-3',
	'return_format' => 'value',
);

$sub_fields[] = array(
	'key' => 'field_grid_columns_tablet_' . $prefixed_widget,
	'label' => '<span class="dashicons dashicons-tablet acf-js-tooltip" title="Colunas no tablet"></span>',
	'name' => 'columns_tablet',
	'type' => 'select',
	'wrapper' => array (
		'class' => 'grid-widget-settings grid-widget-settings--tablet',
	),
	'choices' => array (
		'col-md-12 px-md-0' => '100%',
		'col-md-12 px-md-3' => '12/12',
		'col-md-11 px-md-3' => '11/12',
		'col-md-10 px-md-3' => '10/12',
		'col-md-9 px-md-3' => '9/12',
		'col-md-8 px-md-3' => '8/12',
		'col-md-7 px-md-3' => '7/12',
		'col-md-6 px-md-3' => '6/12',
		'col-md-5 px-md-3' => '5/12',
		'col-md-4 px-md-3' => '4/12',
		'col-md-3 px-md-3' => '3/12',
		'col-md-2 px-md-3' => '2/12',
		'col-md-1 px-md-3' => '1/12'
	),
	'default_value' => 'col-md-12 px-md-3',
	'return_format' => 'value',
);

$sub_fields[] = array(
	'key' => 'field_grid_columns_mobile_' . $prefixed_widget,
	'label' => '<span class="dashicons dashicons-smartphone acf-js-tooltip" title="Colunas no mobile"></span>',
	'name' => 'columns_mobile',
	'type' => 'select',
	'wrapper' => array (
		'class' => 'grid-widget-settings grid-widget-settings--mobile',
	),
	'choices' => array (
		'col-12 px-0' => '100%',
		'col-12 px-3' => '12/12',
		'col-11 px-3' => '11/12',
		'col-10 px-3' => '10/12',
		'col-9 px-3' => '9/12',
		'col-8 px-3' => '8/12',
		'col-7 px-3' => '7/12',
		'col-6 px-3' => '6/12',
		'col-5 px-3' => '5/12',
		'col-4 px-3' => '4/12',
		'col-3 px-3' => '3/12',
		'col-2 px-3' => '2/12',
		'col-1 px-3' => '1/12'
	),
	'default_value' => 'col-12 px-3',
	'return_format' => 'value',
);