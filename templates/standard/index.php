<?php if($tsQuery->have_offers()) : ?>
<ul class="topspin-grid">
	<?php while($tsQuery->have_offers()) : $tsQuery->the_offer(); ?>
		<?php include(WP_Topspin_Template::getFile('item.php')); ?>
	<?php endwhile; ?>
</ul>
<?php endif; ?>