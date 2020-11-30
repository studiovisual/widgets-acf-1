<?php

$posts = get_posts(
    array(
      'orderby' => 'post__in',
      'post__in' => $widget->fields['widgets_reusable'],
      'post_type' => 'widget-reusable',
    )
);

foreach($posts as $post):
    echo do_shortcode($post->post_content);
endforeach;