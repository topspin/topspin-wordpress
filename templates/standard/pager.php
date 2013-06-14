<div class="topspin-pager">
	<?php
	global $tsQuery;
	?>
	<?php if($tsQuery->current_page>1) : ?><a class="topspin-pager-previous" href="<?php echo ts_prev_link(); ?>">Previous</a><?php endif; ?>
	<?php if($tsQuery->current_page<$tsQuery->max_num_pages) : ?><a class="topspin-pager-next" href="<?php echo ts_next_link(); ?>">Next</a><?php endif; ?>
</div>