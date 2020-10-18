<div class="row">
	<?php foreach ($widget->fields['field_select_posts_lista_key'] as $key => $post): ?>
		<?php $post = get_post($post); ?>
		<div class="col-sm-12">
			<div class="col-sm-4">
				<div class="image_widget_post_lista">
					<?php $img = get_the_post_thumbnail( $post->ID, 'medium', array('class'=>'img-responsive') ); ?>
					<?php echo $img; ?>			
				</div>
			</div>
			<div class="col-sm-8">
				<div class="title_widget_post_lista">
					<h4>
						<?php echo $post->post_title; ?>
					</h4>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>