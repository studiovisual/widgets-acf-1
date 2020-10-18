<?php

if(!defined('ABSPATH'))
    exit;

class Widget extends acf_field_flexible_content {
    
    public $flexible = '';
    
    function __construct() {
        parent::initialize();
        
        // Retrieve Flexible Content
        $this->flexible = acf_get_field_type('flexible_content');
        
        // Remove Inherit Actions
        remove_action('acf/render_field/type=flexible_content',                     array($this->flexible, 'render_field'), 9);
        
        // Field Action
        $this->add_action('acf/render_field',                                       array($this, 'renderLayoutsSettingsBefore'), 1);
        $this->add_action('acf/render_field/key=layout_settings',                   array($this, 'renderLayoutsSettingsAfter'), 10);
        
        $this->add_field_action('acf/render_field',                                 array($this, 'render_field'), 9);
        
        $this->add_field_filter('acf/load_value',                                   array($this, 'load_value_toggle'), 10, 3);
        
        // General Filters
        $this->add_filter('widgets_acf/flexible/layouts/handle',                    array($this, 'addLayoutHandle'), 10, 3);
        
        $this->add_filter('acf/fields/flexible_content/no_value_message',           array($this, 'add_empty_message'), 10, 2);
        $this->add_filter('acf/fields/flexible_content/layout_title',               array($this, 'add_layout_title'), 0, 4);
        
        // General Actions
        $this->add_action('wp_ajax_widgets_acf/flexible/models',                           array($this, 'ajaxLayoutModel'));
        $this->add_action('wp_ajax_nopriv_widgets_acf/flexible/models',                    array($this, 'ajaxLayoutModel'));
    }

    function renderLayoutsSettingsBefore($field) {
        if($field['key'] != 'layout_settings')
            return;
        ?>

        <div class="widgets-acf-modal -settings">
        <div class="widgets-acf-modal-wrapper">
        <div class="widgets-acf-modal-content">
            <div class="acf-fields -top">
        <?php
    }
    
    /**
	 *  Layout Settings
	 */
    function renderLayoutsSettingsAfter($field) {
        if($field['key'] != 'layout_settings')
            return;
        ?>
            
            </div>
        </div>
        </div>
        </div>
        
        <?php
    }
    
    /**
	 *  Ajax Layout Model
	 */
    function ajaxLayoutModel() {
        // options
        $options = acf_parse_args($_POST, array(
            'field_key' => '',
            'layout'    => '',
        ));
        
        $field = acf_get_field($options['field_key']);

        if(!$field)
            die;
        
        $field = acf_prepare_field($field);
        
        foreach($field['layouts'] as $layout):
            if($layout['name'] !== $options['layout'])
                continue;
            
            $this->render_layout($field, $layout, 'acfcloneindex', array());
            die;
        endforeach;
        
        die;
    }

    function isAdminScreen() {
        // bail early if not defined
        if(!function_exists('get_current_screen'))
            return false;
    
        // vars
        $screen = get_current_screen();
    
        // no screen
        if(!$screen)
            return false;
        
        $post_types = array(
            'acf-field-group',  // ACF
        );
        
        $field_group_category = false;
        
        
        if(in_array($screen->post_type, $post_types) || $field_group_category)
            return true;
        
        return false;        
    }
    
    /**
	 *  Render Field
	 */
    function render_field($field) {
        // defaults
        if(empty($field['button_label']))
            $field['button_label'] = $this->defaults['button_label'];
        
        // sort layouts into names
        $layouts = array();
        
        foreach($field['layouts'] as $layout)
            $layouts[$layout['name']] = $layout;
        
        // vars
        $div = array(
            'class'		=> 'acf-flexible-content',
            'data-min'	=> $field['min'],
            'data-max'	=> $field['max']
        );
        
        // empty
        if(empty($field['value']))
            $div['class'] .= ' -empty';
        
        // no value message
        $no_value_message = __('Click the "%s" button below to start creating your layout', 'acf');
        $no_value_message = apply_filters('acf/fields/flexible_content/no_value_message', $no_value_message, $field);

    ?>
    <div <?php acf_esc_attr_e( $div ); ?>>
        <?php acf_hidden_input(array('name' => $field['name'])); ?>

        <div class="no-value-message">
            <?php printf($no_value_message, $field['button_label']); ?>
        </div>

        <div class="clones">
            <?php foreach($layouts as $layout):
                $this->render_layout($field, $layout, 'acfcloneindex', array());
            endforeach; ?>
        </div>

        <div class="values">
            <?php if(!empty($field['value'])): 
                foreach($field['value'] as $i => $value):
                    // validate
                    if(empty($layouts[$value['acf_fc_layout']]))
                        continue;
                    
                    // render
                    $this->render_layout($field, $layouts[$value['acf_fc_layout']], $i, $value);
                endforeach;
            endif; ?>
        </div>

        <?php
            $button = array(
                'class'     => 'acf-button button button-primary',
                'href'      => '#',
                'data-name' => 'add-layout',
            );
            $button_settings = array(
                'class'     => 'acf-button button button-secondary acf-js-tooltip',
                'href'      => '#',
                'title'     => 'Ajustes de layout',
                'data-name' => 'settings-layout',
            );
        ?>
            
            <div class="acf-actions">
                <a <?php echo acf_esc_attr($button_settings); ?>><span class="dashicons dashicons-admin-generic"></span></a>
                <a <?php echo acf_esc_attr($button); ?>><?php echo $field['button_label']; ?></a>
            </div>
        </div>
        <script type="text-html" class="tmpl-popup">
            

            <ul data-action="results">
                <li class="search">
                    <span class="dashicons dashicons-search"></span>
                    <input type="search" data-action="search" placeholder="Buscar widget" />
                </li>
            <?php foreach( $layouts as $layout ): 
                
                $atts = array(
                    'href'			=> '#',
                    'data-layout'	=> $layout['name'],
                    'data-min' 		=> $layout['min'],
                    'data-max' 		=> $layout['max'],
                );
                
                ?><li><a <?php acf_esc_attr_e($atts); ?>><span><?php echo $layout['label']; ?></span></a></li><?php 
            
            endforeach; ?>
            </ul>
            
        </script>
    <?php
    
    }
    
    /**
	 *  Render Layout
	 */
	function render_layout($field, $layout, $i, $value) {
		// vars
		$sub_fields = $layout['sub_fields'];
		$id = ($i === 'acfcloneindex') ? 'acfcloneindex' : "row-$i";
        $prefix = $field['name'] . '[' . $id . ']';
		
		// div
		$div = array(
			'class'			=> 'layout',
			'data-id'		=> $id,
			'data-layout'	=> $layout['name']
		);
		
		// is clone?
		if(!is_numeric($i))
			$div['class'] .= ' acf-clone';
        
        // handle
        $handle = array(
            'class'     => 'acf-fc-layout-handle',
            'title'     => __('Drag to reorder', 'acf'),
            'data-name' => 'collapse-layout',
        );
        
        foreach($sub_fields as $sub_field):
            switch($sub_field['name']):
                case 'columns_mobile':
                    $div['data-columns-mobile'] = !empty($sub_field['value']) ? $sub_field['value'] : $sub_field['default_value'];
                    break;
                case 'columns_tablet':
                    $div['data-columns-tablet'] = !empty($sub_field['value']) ? $sub_field['value'] : $sub_field['default_value'];
                    break;
                case 'columns_desktop':
                    $div['data-columns-desktop'] = !empty($sub_field['value']) ? $sub_field['value'] : $sub_field['default_value'];
                    break;
            endswitch;
        endforeach;
        
        $handle = apply_filters('widgets_acf/flexible/layouts/handle',                                                         $handle, $layout, $field);
        $handle = apply_filters('widgets_acf/flexible/layouts/handle/name=' . $field['_name'],                                 $handle, $layout, $field);
        $handle = apply_filters('widgets_acf/flexible/layouts/handle/key=' . $field['key'],                                    $handle, $layout, $field);
        $handle = apply_filters('widgets_acf/flexible/layouts/handle/name=' . $field['_name'] . '&layout=' . $layout['name'],  $handle, $layout, $field);
        $handle = apply_filters('widgets_acf/flexible/layouts/handle/key=' . $field['key'] . '&layout=' . $layout['name'],     $handle, $layout, $field);
		
        // title
		$title = $this->get_layout_title($field, $layout, $i, $value);
        
		// remove row
		reset_rows();
        ?>
        <div <?php echo acf_esc_attr($div); ?>>
            <?php acf_hidden_input(array( 'name' => $prefix.'[acf_fc_layout]', 'value' => $layout['name'] )); ?>

            <div <?php echo acf_esc_attr($handle); ?>><?php echo $title; ?></div>
            
            <?php 
            
            // Title Edition
            $this->render_layout_title_edition($layout, $sub_fields, $value, $field, $prefix);
            // Icons
            $this->render_layout_icons($layout, $field);
            // Placeholder
            $this->render_layout_placeholder($value, $layout, $field, $i);
            
            add_filter('acf/prepare_field/type=wysiwyg', array($this, 'field_editor_delay'));            
            // Layouts settings
            // $this->render_layout_settings($layout, $sub_fields, $value, $field, $prefix);
            // Layouts fields
            $this->render_layout_fields($layout, $sub_fields, $value, $field, $prefix);
            
            remove_filter('acf/prepare_field/type=wysiwyg', array($this, 'field_editor_delay'));
            ?>
        </div>
        <?php
	}
    
    /**
	 *  Render Title Edition
	 */
    function render_layout_title_edition($layout, &$sub_fields, $value, $field, $prefix) {
        if(empty($sub_fields))
            return false;
        
        $title_key = false;
        foreach($sub_fields as $sub_key => $sub_field):
            if($sub_field['name'] !== 'widget_name')
                continue;
            
            // Remove other potential duplicate
            if($title_key !== false):
                unset($sub_fields[$sub_key]);
                continue;
            endif;
            
            $title_key = $sub_key;
        endforeach;
        
        if($title_key === false)
            return false;
        
        // Extract
        $title = acf_extract_var($sub_fields, $title_key);
        
        // Reset keys
        $sub_fields = array_values($sub_fields);
        
        // add value
        if(isset($value[$title['key']]))
            // this is a normal value
            $title['value'] = $value[$title['key']];
        elseif(isset($title['default_value']))    
            // no value, but this sub field has a default value
            $title['value'] = $title['default_value'];
        
        // update prefix to allow for nested values
        $title['prefix'] = $prefix;
        
        $title['class'] = 'widgets-acf-flexible-control-title';
        $title['data-widgets-acf-flexible-control-title-input'] = 1;
        
        $title = acf_prepare_field($title);
        
        $input_attrs = array();
        foreach(array('type', 'id', 'class', 'name', 'value', 'placeholder', 'maxlength', 'pattern', 'readonly', 'disabled', 'data-widgets-acf-flexible-control-title-input') as $k):
            if(isset($title[$k]))
                $input_attrs[$k] = $title[$k];
        endforeach;
        
        // render input
        echo acf_get_text_input($input_attrs);
    }
    
    /**
	 *  Render Layout Icons
	 */
    function render_layout_icons($layout, $field) {
        if(acf_version_compare(acf_get_setting('version'),  '<', '5.9')):
            // icons
            $icons = array(
                'clone'     => '<a class="acf-icon -copy small light acf-js-tooltip widgets-acf-flexible-icon" href="#" title="Clonar widget" data-widgets-acf-flexible-control-clone="' . $layout['name'] . '"><span class="dashicons dashicons-admin-page"></span></a>',
                // 'add'       => '<a class="acf-icon -plus small light acf-js-tooltip" href="#" data-name="add-layout" title="' . __('Add layout','acf') . '"></a>',
                'delete'    => '<a class="acf-icon -minus small light acf-js-tooltip" href="#" data-name="remove-layout" title="' . __('Remove layout','acf') . '"></a>',
                'collapse'  => '<a class="acf-icon -collapse small acf-js-tooltip" href="#" data-name="collapse-layout" title="' . __('Click to toggle','acf') . '"></a>'
            );
        else:    
            // icons
            $icons = array(
                // 'add'       => '<a class="acf-icon -plus small light acf-js-tooltip" href="#" data-name="add-layout" title="' . __('Add layout','acf') . '"></a>',
                'duplicate' => '<a class="acf-icon -duplicate small light acf-js-tooltip" href="#" data-name="duplicate-layout" title="' . __('Duplicate layout','acf') . '"></a>',
                'delete'    => '<a class="acf-icon -minus small light acf-js-tooltip" href="#" data-name="remove-layout" title="' . __('Remove layout','acf') . '"></a>',
                'collapse'  => '<a class="acf-icon -collapse small acf-js-tooltip" href="#" data-name="collapse-layout" title="' . __('Click to toggle','acf') . '"></a>'
            );
        endif;
        
        if(!empty($icons)): ?>
            <div class="acf-fc-layout-controls">
                <?php foreach($icons as $icon): ?>
                    <?php echo $icon; ?>
                <?php endforeach; ?>
            </div>
        <?php endif;
    }
    
    /**
	 *  Render Layout Placeholder
	 */
    function render_layout_placeholder($value, $layout, $field, $i) {
        $placeholder = array(
            'class' => 'widgets-acf-fc-placeholder',
            'title' => __('Edit layout', 'acf'),
        );

        $placeholder['data-action'] = 'widgets-acf-flexible-modal-edit';
        
        ?>
        
        <div <?php echo acf_esc_attr($placeholder); ?>>

        </div>

        <?php
    }
    
    /**
	 *  Render Layout Fields
	 */
    function render_layout_fields($layout, $sub_fields, $value, $field, $prefix) {
        if(empty($sub_fields))
            return false;
        
        // el
        $el = 'div';
        
		if($layout['display'] == 'table')
			$el = 'td';
    
        ?>
        
        <div class="widgets-acf-modal -fields">
        <div class="widgets-acf-modal-wrapper">
        <div class="widgets-acf-modal-content">
    
        <div class="acf-fields <?php if($layout['display'] == 'row'): ?>-left<?php endif; ?>">
            <?php
            // loop though sub fields
            foreach($sub_fields as $sub_field):
                // add value
                if(isset($value[ $sub_field['key']]))
                    // this is a normal value
                    $sub_field['value'] = $value[$sub_field['key']];
                elseif(isset($sub_field['default_value']))
                    // no value, but this sub field has a default value
                    $sub_field['value'] = $sub_field['default_value'];
                
                // update prefix to allow for nested values
                $sub_field['prefix'] = $prefix;
                
                // render input
                acf_render_field_wrap($sub_field, $el);
            endforeach;
            ?>
        </div>
        
        </div>
        </div>
        </div>
        
        <?php 
    }
    
    /**
	 *  Add Layout Handle
	 */
    function addLayoutHandle($handle, $layout, $field) {
        // Data
        $handle['data-action'] = 'widgets-acf-flexible-modal-edit';
        
        return $handle;
    }
    
    /**
	 *  Add Empty Message
	 */
    function add_empty_message($message, $field) {
        return $message;
    }
    
    /**
	 *  Add Layout Title
	 */
    function add_layout_title($title, $field, $layout, $i){
        // Get Layout Title
        $flexible_layout_title = get_sub_field('widget_name');
        
        if(!empty($flexible_layout_title))
            $title = wp_unslash($flexible_layout_title);
        
        // Return
        return '<span class="widgets-acf-layout-title acf-js-tooltip" title="' . __('Widget', 'acf') . ': ' . esc_attr(strip_tags($layout['label'])) . '"><span class="widgets-acf-layout-title-text">' . $title . '</span></span>';
    }
    
    /**
	 *  Wysiwyg Editor Delay
	 */
    function field_editor_delay($field) {
        $field['delay'] = 1;
        
        return $field;
    }
    
    /**
     *  Load Value Toggle
     */
    function load_value_toggle($value, $post_id, $field){
        // Bail early if admin
        if(is_admin() && !wp_doing_ajax())
            return $value;
        if(empty($field['layouts']))
            return $value;
        
        $models = array();
        
        foreach($field['layouts'] as $layout) {
            $models[$layout['name']] = array(
                'key' => $layout['key'],
                'name' => $layout['name'],
                'toggle' => 'field_' . $layout['key'] . '_toggle'
            );
        }
        
        $value = acf_get_array($value);
        
        foreach($value as $k => $layout):
            if(!isset($models[$layout['acf_fc_layout']]))
                continue;
            if(!acf_maybe_get($layout, $models[$layout['acf_fc_layout']]['toggle']))
                continue;
            
            unset($value[$k]);
        endforeach;
    
        return $value;
    }
    
}

acf_register_field_type('Widget');

// endif;