<?php if($tsQuery->have_offers()) : ?>
<ul class="topspin-grid">
	<?php while($tsQuery->have_offers()) : $tsQuery->the_offer(); ?>
		<?php ts_get_template_part('item', ts_get_the_offer_type()); ?>
	<?php endwhile; ?>
</ul>
<?php endif; ?>