<?php

/*
    Title: Widgets reutilizáveis
    Description: 
    Category: Custom
    Keywords: custom
    Author: Lorde Aleister
*/

acf_add_local_field_group(array(
    'key' => 'group_widget_reusable',
    'title' => 'Widget config',
    'fields' => array(
        array(
            'key' => 'widgets_reusable',
            'label' => 'Widgets',
            'name' => 'widgets_reusable',
            'type' => 'relationship',
            'post_type' => array(
                'widget-reusable',
            ),
            'filters' => array(
                'search',
            ),
            'elements' => array(
                'featured_image',
            ),
        ),
    ),
    'location' => array (
        array (
            array (
                'param' => 'widget_acf',
                'operator' => '==',
                'value' => 'widget_reusable',
            ),
        ),
    ),
));