<?php $featuredItems = WP_Topspin::getFeaturedItems($post->ID); ?>
<?php if(is_array($featuredItems) && count($featuredItems) && $featuredItems[0]!=0) : ?>
	<?php foreach($featuredItems as $item) : ?>
		<?php if($item!=0) : ?>
			<div class="topspin-metabox-row">
				<label>Select Item</label>
				<select class="topspin-featured-item" name="topspin[featured][]">
					<option value="0">-- Select Offer --</option>
						<option value="<?php echo $item; ?>" selected="selected"><?php echo get_the_title($item); ?></option>
				</select>
				<button class="topspin-featured-delete" type="button">Remove</button>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>

<div class="topspin-metabox-row">
	<label>Select Item</label>
	<select class="topspin-featured-item" name="topspin[featured][]">
		<option value="0">-- Select Offer --</option>
		<?php
		$args = array(
			'posts_per_page' => -1,
			'order' => 'ASC',
			'orderby' => 'title',
			'post_type' => TOPSPIN_CUSTOM_POST_TYPE_OFFER,
			'post_status' => 'publish'
		);
		$items = new WP_Query($args);
		if($items->have_posts()) : ?>
			<?php while($items->have_posts()) : $items->the_post(); ?>
				<option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
			<?php endwhile; ?>
		<?php endif; ?>
	</select>
</div>