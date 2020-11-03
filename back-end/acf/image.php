<?php

if(!defined('ABSPATH'))
    exit;

class Image extends acf_field_image {

    public $image = '';
    
    function __construct() {
        parent::initialize();
        
        // Retrieve Flexible Content
        $this->image = acf_get_field_type('image');
        
        // Remove Inherit Actions
        remove_action('acf/render_field/type=image',                     array($this->image, 'render_field'), 9);

        $this->add_field_action('acf/render_field',                                 array($this, 'render_field'), 9);
    }

    function render_field($field) {
		$uploader = acf_get_setting('uploader');
		
		// Enqueue uploader scripts
		if($uploader === 'wp')
			acf_enqueue_uploader();

		// Elements and attributes.
		$value = '';
		$div_attrs = array(
			'class'				=> 'acf-image-uploader',
			'data-preview_size'	=> $field['preview_size'],
			'data-library'		=> $field['library'],
			'data-mime_types'	=> $field['mime_types'],
			'data-uploader'		=> $uploader
		);
		$img_attrs = array(
			'data-src'		=> '',
			'alt'		=> '',
			'data-name'	=> 'image'
		);
		
		// Detect value.
		if($field['value'] && is_numeric($field['value'])):
            $image = wp_get_attachment_image_src($field['value'], $field['preview_size']);
            
			if($image):
				$value = $field['value'];
				$img_attrs['data-src'] = $image[0];
				$img_attrs['alt'] = get_post_meta($field['value'], '_wp_attachment_image_alt', true);
				$div_attrs['class'] .= ' has-value';
            endif;	
		endif;
		
		// Add "preview size" max width and height style.
		// Apply max-width to wrap, and max-height to img for max compatibility with field widths.
		$size = acf_get_image_size($field['preview_size']);
		$size_w = $size['width'] ? $size['width'] . 'px' : '100%';
		$size_h = $size['height'] ? $size['height'] . 'px' : '100%';
		$img_attrs['style'] = sprintf('max-height: %s;', $size_h);

		// Render HTML.
		?>
<div <?php echo acf_esc_attrs($div_attrs); ?>>
	<?php acf_hidden_input(array( 
		'name' => $field['name'],
		'value' => $value
	)); ?>
	<div class="show-if-value image-wrap" style="max-width: <?php echo esc_attr($size_w); ?>">
		<img <?php echo acf_esc_attrs($img_attrs); ?> />
		<div class="acf-actions -hover">
			<?php if($uploader !== 'basic'): ?>
			<a class="acf-icon -pencil dark" data-name="edit" href="#" title="<?php _e('Edit', 'acf'); ?>"></a>
			<?php endif; ?>
			<a class="acf-icon -cancel dark" data-name="remove" href="#" title="<?php _e('Remove', 'acf'); ?>"></a>
		</div>
	</div>
	<div class="hide-if-value">
		<?php if($uploader === 'basic'): ?>
			<?php if($field['value'] && !is_numeric($field['value'])): ?>
				<div class="acf-error-message"><p><?php echo acf_esc_html($field['value']); ?></p></div>
			<?php endif; ?>
			<label class="acf-basic-uploader">
				<?php acf_file_input(array(
					'name' => $field['name'], 
					'id' => $field['id']
				)); ?>
			</label>
		<?php else: ?>
			<p><?php _e('No image selected', 'acf'); ?> <a data-name="add" class="acf-button button" href="#"><?php _e('Add Image', 'acf'); ?></a></p>
		<?php endif; ?>
	</div>
</div>
		<?php
	}

}

acf_register_field_type('Image');