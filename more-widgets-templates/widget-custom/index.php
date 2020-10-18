<?php//var_dump($fields); ?>
<div class="row">
	<div class="col-sm-12">
		<div class="image_widget_custom">
			<?php $img = $widget->fields['field_image_widget_custom_key']; ?>
			<?php if(!empty($img)): ?>

				<?php if(is_array($img)){
					$img = $img['sizes']['medium'];
				}else{
					$img = wp_get_attachment_url($img);
				} ?>

				<img src="<?php echo $img; ?>" class="img-responsive" alt="<?php echo $widget->fields['field_text_widget_custom_key']; ?>">
			<?php endif; ?>
			<div class="caption_widget_custom" 
			style="background: <?php echo $widget->fields['field_color_picker_widget_custom_key']; ?>;">
			<a href="<?php echo $widget->fields['field_text__1_widget_custom_key']; ?>">
				<?php echo $widget->fields['field_text_widget_custom_key']; ?>
			</a>
		</div>
	</div>
</div>
</div>