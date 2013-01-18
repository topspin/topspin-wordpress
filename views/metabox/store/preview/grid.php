<?php if($tsQuery->have_offers()) : ?>
	<?php while($tsQuery->have_offers()) : $tsQuery->the_offer(); ?>
	<li class="topspin-preview-item <?php echo ts_is_visible() ? 'topspin-item-show' : ''; ?>" data-id="<?php ts_the_ID(); ?>" data-order-number="<?php ts_the_order_number(); ?>">
		<input type="hidden" name="topspin[items_order][]" value="<?php ts_the_ID(); ?>" />
		<input class="topspin-item-is-visible" type="hidden" name="topspin[items_visible][<?php ts_the_ID(); ?>]" value="<?php echo ts_is_visible(); ?>" />
		<?php echo ts_the_thumbnail('wp-list-table-thumb'); ?>
		<div class="topspin-preview-name"><?php ts_the_name(); ?></div>
	</li>
	<?php endwhile; ?>
<?php endif; ?>