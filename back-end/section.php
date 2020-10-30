<?php

if(!defined('ABSPATH'))
    exit;

class Section extends acf_field_repeater {
    
    public $repeater = '';
    
    function __construct() {
        parent::initialize();
        
        // Retrieve Flexible Content
        $this->repeater = acf_get_field_type('repeater');
        
        // Remove Inherit Actions
        remove_action('acf/render_field/type=repeater',                     array($this->repeater, 'render_field'), 9);
        
        // // Field Action
        // $this->add_action('acf/render_field',                                       array($this, 'renderLayoutsSettingsBefore'), 1);
        // $this->add_action('acf/render_field/key=layout_settings',                   array($this, 'renderLayoutsSettingsAfter'), 10);
        
        $this->add_field_action('acf/render_field',                                 array($this, 'render_field'), 9);
        
        // $this->add_field_filter('acf/load_value',                                   array($this, 'load_value_toggle'), 10, 3);
        
        // // General Filters
        // $this->add_filter('widgets_acf/flexible/layouts/handle',                    array($this, 'addLayoutHandle'), 10, 3);
        
        // $this->add_filter('acf/fields/flexible_content/no_value_message',           array($this, 'add_empty_message'), 10, 2);
        // $this->add_filter('acf/fields/flexible_content/layout_title',               array($this, 'add_layout_title'), 0, 4);
        
        // // General Actions
        // $this->add_action('wp_ajax_widgets_acf/flexible/models',                           array($this, 'ajaxLayoutModel'));
        // $this->add_action('wp_ajax_nopriv_widgets_acf/flexible/models',                    array($this, 'ajaxLayoutModel'));
    }

    function render_field( $field ) {
        if($field['key'] != 'widgets'):
            parent::render_field($field);
            return;
        endif;
		
		// vars
		$sub_fields = $field['sub_fields'];
		
		// bail early if no sub fields
        if(empty($sub_fields)) 
            return;
		
		// value
		$value = is_array($field['value']) ? $field['value'] : array();	
		
		// div
		$div = array(
			'class' 		=> 'acf-repeater',
			'data-min' 		=> $field['min'],
			'data-max'		=> $field['max']
		);
		
		// empty
		if(empty($value))
			$div['class'] .= ' -empty';
		// If there are less values than min, populate the extra values
		if($field['min'])
			$value = array_pad($value, $field['min'], array());
		
		// If there are more values than man, remove some values
		if($field['max'])
			$value = array_slice($value, 0, $field['max']);
		
		// setup values for row clone
		$value['acfcloneindex'] = array();
		
		// button label
        if($field['button_label'] === '')
            $field['button_label'] = __('Add Row', 'acf');
		
		// field wrap
		$el = 'td';
		$before_fields = '';
		$after_fields = '';
		
		if($field['layout'] == 'row'):
			$el = 'div';
			$before_fields = '<td class="acf-fields -left">';
			$after_fields = '</td>';
	    elseif($field['layout'] == 'block'):
			$el = 'div';
			$before_fields = '<td class="acf-fields">';
			$after_fields = '</td>';
        endif;
		
		// layout
		$div['class'] .= ' -' . $field['layout'];
		
		
		// collapsed
		if( $field['collapsed'] ) {
			
			// loop
			foreach( $sub_fields as &$sub_field ) {
				
				// add target class
				if( $sub_field['key'] == $field['collapsed'] ) {
					$sub_field['wrapper']['class'] .= ' -collapsed-target';
				}
			}
			unset( $sub_field );
		}
		
?>
<div <?php acf_esc_attr_e($div); ?>>
	<?php acf_hidden_input(array( 'name' => $field['name'], 'value' => '' )); ?>
<table class="acf-table acf-widgets-table">
	
	<?php if( $field['layout'] == 'table' ): ?>
		<thead>
			<tr>
				<th class="acf-row-handle"></th>
				
				<?php foreach( $sub_fields as $sub_field ): 
					
					// prepare field (allow sub fields to be removed)
					$sub_field = acf_prepare_field($sub_field);
					
					
					// bail ealry if no field
					if( !$sub_field ) continue;
					
					
					// vars
					$atts = array();
					$atts['class'] = 'acf-th';
					$atts['data-name'] = $sub_field['_name'];
					$atts['data-type'] = $sub_field['type'];
					$atts['data-key'] = $sub_field['key'];
					
					
					// Add custom width
					if( $sub_field['wrapper']['width'] ) {
					
						$atts['data-width'] = $sub_field['wrapper']['width'];
						$atts['style'] = 'width: ' . $sub_field['wrapper']['width'] . '%;';
						
					}
					
					?>
					<th <?php echo acf_esc_attr( $atts ); ?>>
						<?php echo acf_get_field_label( $sub_field ); ?>
						<?php if( $sub_field['instructions'] ): ?>
							<p class="description"><?php echo $sub_field['instructions']; ?></p>
						<?php endif; ?>
					</th>
				<?php endforeach; ?>

				<th class="acf-row-handle"></th>
			</tr>
		</thead>
	<?php endif; ?>
	
	<tbody>
		<?php foreach($value as $i => $row): 
			// Generate row id.
			$id = ( $i === 'acfcloneindex' ) ? 'acfcloneindex' : "row-$i";

			$section_title = $sub_fields[0]['sub_fields'][0];
			
			if(!empty($section_title)):
				$title = wp_unslash($section_title['value']);

				if(empty($title))
					$title = wp_unslash($section_title['default_value']);
			endif;
			
			?>
			<tr class="acf-row<?php if( $i === 'acfcloneindex' ){ echo ' acf-clone'; } ?>" data-id="<?php echo $id; ?>">
				<td class="acf-row-handle order" title="<?php _e('Drag to reorder','acf'); ?>">
				<div>
					<span class="acf-fc-layout-order"><?php echo intval($i) + 1; ?></span>
					<span class="widgets-acf-section-title acf-js-tooltip" title="Clique para renomear">
						<span class="widgets-acf-section-title-text"><?= $title ?></span>
					</span>

					<?php 
						// Title Edition
						//$this->render_layout_title_edition($section_title, $i);
					?>
				</div>
					

					<div>
						<a class="acf-icon small acf-js-tooltip" href="#" title="Ajustes de layout" data-event="settings-layout"><span class="dashicons dashicons-admin-generic"></span></a>
						<a class="acf-icon -plus small acf-js-tooltip" href="#" data-event="add-row" title="<?php _e('Add row','acf'); ?>"></a>

						<?php if(acf_version_compare(acf_get_setting('version'),  '>=', '5.9')): ?>
							<a class="acf-icon -duplicate small acf-js-tooltip show-on-shift" href="#" data-event="duplicate-row" title="<?php _e('Duplicate row','acf'); ?>"></a>
						<?php endif; ?>

						<a class="acf-icon -minus small acf-js-tooltip" href="#" data-event="remove-row" title="<?php _e('Remove row','acf'); ?>"></a>
						<a class="acf-icon -collapse small" href="#" data-event="collapse-row" title="<?php _e('Click to toggle','acf'); ?>"></a>
					</div>
				</td>
				
				<?php echo $before_fields; ?>

				<?php foreach($sub_fields as $sub_field): 
					// add value
					if(isset($row[$sub_field['key']]))
						// this is a normal value
						$sub_field['value'] = $row[$sub_field['key']];
					elseif(isset($sub_field['default_value']))
						// no value, but this sub field has a default value
						$sub_field['value'] = $sub_field['default_value'];
					
					// update prefix to allow for nested values
					$sub_field['prefix'] = $field['name'] . '[' . $id . ']';
					
					// render input
					acf_render_field_wrap($sub_field, $el); ?>
				<?php endforeach; ?>
				
				<?php echo $after_fields; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
	
<div class="acf-actions">
	<a class="acf-button button button-primary" href="#" data-event="add-row"><?php echo $field['button_label']; ?></a>
</div>
			
</div>
<?php
		
	}

	/**
	 *  Render Title Edition
	 */
    function render_layout_title_edition($field, $index) {
        // // add value
        // if(isset($value[$title['key']]))
        //     // this is a normal value
        //     $field['value'] = $value[$title['key']];
        // elseif(isset($title['default_value']))    
        //     // no value, but this sub field has a default value
        //     $title['value'] = $title['default_value'];
        
        // update prefix to allow for nested values
        // $title['prefix'] = $prefix;
        
        $field['class'] = 'widgets-acf-flexible-control-title';
        $field['data-widgets-acf-flexible-control-title-input'] = 1;
        
        $field = acf_prepare_field($field);
        
		$input_attrs = array();
        foreach(array('type', 'id', 'class', 'name', 'value', 'placeholder', 'maxlength', 'pattern', 'readonly', 'disabled', 'data-widgets-acf-flexible-control-title-input') as $k):
            if(isset($field[$k]))
                $input_attrs[$k] = $field[$k];
        endforeach;
		
		$input_attrs['id'] = "acf-widgets-row-{$index}-layout_settings-layout_name";

        // render input
        echo acf_get_text_input($input_attrs);
    }
    
}

acf_register_field_type('Section');

// endif;