<div class="row">
<?php $post = get_post($widget->fields['field_select_post_unico_key']); ?>
	<div class="col-sm-12">
		<div class="image_widget_post_unico">
			<?php $img = get_the_post_thumbnail( $post->ID, 'medium', array('class'=>'img-responsive') ); ?>
			<?php echo $img; ?>
		</div>
	</div>
	<div class="col-sm-12">
		<div class="title_widget_post_unico">
			<h2>
				<?php echo $post->post_title; ?>
			</h2>
		</div>
	</div>
</div>