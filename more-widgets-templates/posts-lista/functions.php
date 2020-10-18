<?php

/*
    Title: Múltiplos posts
    Description: Exibe uma lista de posts
    Category: Post
    Keywords: post multiple
*/

if ( !class_exists('post_lista_widgets_acf') ){

	Class post_lista_widgets_acf {

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


$fields['icon'] = 'fa fa-list-ul';

$fields['select']['label'] = 'Selecione os posts';
$fields['select']['choices'] = post_lista_widgets_acf::get_posts();
$fields['select']['multiple'] = 1;

?>