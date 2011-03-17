<?php
##
##	Topspin Items Listing
##
##	Usage: [topspin_buy_buttons]
##
##	Available template variables
##		storedata	(array)
##		storeitems	(array)

?>

<?php if(count($storeitems)) : ?>
	<ul class="topspin-item-listings">
	<?php foreach($storeitems as $key=>$item) : ?>
    	<?php $item_classes = array(); ?>
		<?php if($key && $key%$storedata['grid_columns']==0) : ?>
        <li class="topspin-clear"></li>
        <?php endif; ?>
        <?php if($key==0) { $item_classes[] = 'first'; } ?>
        <?php if($key%$storedata['grid_columns']==0) { $item_classes[] = 'row-start'; } ?>
		<?php if(($key+1)%$storedata['grid_columns']==0) { $item_classes[] = 'row-end'; } ?>
		<li class="topspin-item <?=$item['offer_type'];?> <?=implode(' ',$item_classes);?>" style="width:<?=$storedata['grid_item_width'];?>%">
        	<div class="topspin-item-canvas">
        	<?php ## BEGIN SWITCH OFFER TYPE
            switch($item['offer_type']) {
				case 'buy_button': ?>
					<h2 class="topspin-item-title"><?=$item['name'];?></h2>
                    <div class="topspin-item-image"><a class="topspin-colorbox" href="#topspin-view-more-<?=$item['id'];?>"><img src="<?=$item['poster_image'];?>" /></a></div>
					<div class="topspin-item-price">Price: <?=$item['symbol'];?><?=$item['price'];?></div>
					<div class="topspin-item-buy"><a class="topspin-buy" href="<?=$item['offer_url'];?>">Buy</a></div>
                    <div id="topspin-view-more-<?=$item['id']; ?>" class="topspin-view-more-canvas">
                        <div class="topspin-view-more-image"><img src="<?=$item['poster_image'];?>" /></div>
                        <h2 class="topspin-view-more-title"><?=$item['name'];?></h2>
                        <div class="topspin-view-more-desc"><?=$item['description'];?></div>
                        <div class="topspin-view-more-buy">
                        	<a class="topspin-buy" href="<?=$item['offer_url'];?>">Buy</a>
	                        <div class="topspin-view-more-price">Price: <?=$item['symbol'];?><?=$item['price'];?></div>
 						</div>
                    </div>
					<?php break;
				case 'email_for_media':
				case 'bundle_widget':
				case 'single_track_player_widget': ?>
                	<div class="topspin-item-embed"><?=$item['embed_code'];?></div>
					<?php break;
			} ## END SWITCH OFFER TYPE ?>
        	</div>
	    </li>
	<?php endforeach; ?>
	</ul>

	<?php ## BEGIN PAGINATION
	if(!$storedata['show_all_items'] && $storedata['curr_page']<=$storedata['total_pages']) { ?>
    	<div class="topspin-pagination">
    	Page <?=$storedata['curr_page'];?> of <?=$storedata['total_pages'];?>
		<?php if($storedata['prev_page']) : ?><a class="topspin-prev" href="<?=$storedata['prev_page'];?>">Previous</a><?php endif; ?>
		<?php if($storedata['next_page']) : ?><a class="topspin-next" href="<?=$storedata['next_page'];?>">Next</a><?php endif; ?>
        </div>
	<?php } ## END PAGINATION ?>

<?php endif; ?>