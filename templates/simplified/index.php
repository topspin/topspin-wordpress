<?php if($tsQuery->have_offers()) : ?>
<?php $columns = ts_grid_columns(); ?>
<table class="topspin-table">
	<?php while($tsQuery->have_offers()) : $tsQuery->the_offer(); ?>
		<?php if(ts_item_column()==1) : ?><tr class="topspin-item-row"><?php endif; ?>
		<?php ts_get_template_part('item', ts_get_the_offer_type()); ?>
		<?php if(ts_item_column()==ts_grid_columns()) : ?></tr><?php endif; ?>
	<?php endwhile; ?>
</table>
<?php endif; ?>