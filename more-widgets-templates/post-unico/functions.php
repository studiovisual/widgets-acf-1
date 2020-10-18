<?php

/*
    Title: Post único
    Description: Exibe um post único definido
    Category: Post
    Keywords: post single
*/

if ( !class_exists('post_unico_widgets_acf') ){

	Class post_unico_widgets_acf {

		public function get_posts(){

			$posts_return = array();

			$args = array('posts_per_page'=>-1,	'order'=>'ASC', 'orderby'=>'post_date');

			$posts = get_posts($args);

			foreach ($posts as $key => $post) {
				$posts_return[$post->ID] = $post->post_title;
			}

			return $posts_return;
		}

	}
}


$fields['icon'] = 'fa fa-file-image-o';

$fields['select']['label'] = 'Selecione um post';
$fields['select']['choices'] = post_unico_widgets_acf::get_posts();

?>