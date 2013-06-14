<?php if($tsQuery->have_offers()) : ?>
	<?php while($tsQuery->have_offers()) : $tsQuery->the_offer(); ?>
		<table class="topspin-item topspin-item-featured topspin-item-id-<?php ts_the_id(); ?>">
			<tr class="topspin-item-inner">
				<?php if(ts_get_the_offer_type()=='buy_button') : ?>
					<td class="topspin-item-thumb"><a href="<?php ts_the_permalink(); ?>"><?php ts_the_thumbnail('topspin-default-single-thumb'); ?></a></div>
					<td class="topspin-item-body">
						<div class="topspin-item-desc"><?php ts_the_content(); ?></div>
						<div class="topspin-item-footer">
							<?php if(ts_is_new()) : ?><div class="topspin-item-new">NEW!</div><?php endif; ?>
							<?php if(ts_is_on_sale()) : ?><div class="topspin-item-onsale">ON SALE!</div><?php endif; ?>
							<div class="topspin-item-price">Price: <?php ts_the_price(); ?></div>
							<div class="topspin-item-purchase">
								<?php if(ts_is_sold_out()) : ?>
									<div class="topspin-item-soldout">SOLD OUT!</div>
								<?php else : ?>
									<a class="topspin-item-purchase-anchor" href="<?php ts_the_purchaselink(); ?>">PURCHASE</a>
								<?php endif; ?>
							</div>
						</div>
					</td>
				<?php else : ?>
					<td class="topspin-item-embed"><?php ts_the_embed_code(); ?></td>
				<?php endif; ?>
			</tr>
		</table>
	<?php endwhile; ?>
<?php endif; ?>