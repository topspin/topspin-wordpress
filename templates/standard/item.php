<li class="<?php ts_item_class(); ?>">
	<div class="topspin-item-inner">
		<div class="topspin-item-name"><a href="<?php ts_the_permalink(); ?>"><?php ts_the_title(); ?></a></div>
		<div class="topspin-item-thumb"><a class="topspin-viewbox" data-offer-id="<?php ts_the_id(); ?>" href="<?php ts_the_permalink(); ?>"><?php ts_the_thumbnail(TOPSPIN_DEFAULT_GRID_THUMB_SIZE); ?></a></div>
		<div class="topspin-item-footer">
			<?php if(ts_is_new()) : ?><div class="topspin-item-new">NEW!</div><?php endif; ?>
			<?php if(ts_is_on_sale()) : ?><div class="topspin-item-onsale">ON SALE!</div><?php endif; ?>
			<div class="topspin-item-price"><?php ts_the_price(); ?></div>
			<div class="topspin-item-purchase">
				<?php if(ts_is_sold_out()) : ?>
					<div class="topspin-item-soldout">SOLD OUT!</div>
				<?php else : ?>
					<a class="topspin-item-purchase-anchor" href="<?php ts_the_purchaselink(); ?>">PURCHASE</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</li>