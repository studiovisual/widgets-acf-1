<?php

class WidgetReusable {

    protected $title = "Widget reutilizável";
    protected $slug = "widget-reusable";
    protected $args = [
        'labels' => [
            'name' => 'Widgets reutilizáveis',
            'singular_name' => 'Widget reutilizável',
            'add_new' => 'Criar Novo',
            'add_new_item' => 'Criar novo widget reutizável',
            'edit_item' => 'Editar widget reutilizável',
            'all_items' => 'Todos widgets',
        ],
        'public' => false,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'exclude_from_search' => true,
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'rewrite' => false,
        'taxonomies' => [],
        'menu_position' => 23,
        'supports' => [
            'title',
            'editor',
            'author',
            'thumbnail',
        ],
        'menu_icon' => 'dashicons-update-alt',
    ];

    public function __construct() {
        $this->registerPostType();
    }

    protected function registerPostType() {
        register_post_type($this->slug, $this->args);
    }

}
