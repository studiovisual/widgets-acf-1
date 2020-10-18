<?php

Class WidgetTemplate {

	public function __construct() {
		add_shortcode('acf_widgets', array($this, 'init'));
	}

	public function init($attr = null) {
		$p_id = get_queried_object();
		$widgets = $this->widgets($p_id, $attr);

		return $widgets;
	}

	public function widgets($type, $attr = null) {
		$widgets = array();
		$grid = 0;

		if(have_rows('widgets', $type)):
			while(have_rows('widgets', $type)):
				$columns = the_row();

				if(have_rows('widgets_contents')):
					while(have_rows('widgets_contents')):
						the_row();

						$class = $this->getClassWidget(get_row());

						$widgets[$grid]['attr'] = $columns['layout_settings'];
						$widgets[$grid][] = array(
							'layout' => get_row_layout(), 
							'content' => get_row(), 
							'class'=>$class, 
						);
					endwhile;
				endif;

				$grid++;
			endwhile;
		endif;

		return Utils::getTemplates($widgets, $attr);
	}

	public function getClassWidget($fields) {
		$layout_widget = $fields['acf_fc_layout'];
		$class = '';

		$class = empty($fields['display_mobile_'.$layout_widget.'_key']) ? ' d-none' : ' d-block';
		$class .= empty($fields['display_tablet_'.$layout_widget.'_key']) ? ' d-md-none' : ' d-md-block';
		$class .= empty($fields['display_desktop_'.$layout_widget.'_key']) ? ' d-lg-none' : ' d-lg-block';

		return trim($class);
	}

}

?>