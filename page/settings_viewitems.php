<?php

/*
 *
 *	Last Modified:		July 26, 2011
 *
 *	--------------------------------------
 *	Change Log
 *	--------------------------------------
 *	2011-07-26
 		- Created page
 *
 */

global $store;
$itemsList = $store->getArtistItems();

?>

<div class="wrap">
	<h2>
    	Items
        <a class="button add-new-h2" href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=topspin/page/settings_edit">Add New</a>
    </h2>

    <table class="topspin-stores-list wp-list-table widefat fixed" cellspacing="0">
        <thead>
            <tr>
            	<th scope="col" id="id" class="id-column">ID</th>
            	<th scope="col" id="thumb" class="thumb-column">Thumb</th>
            	<th scope="col" id="title" class="title-column">Title</th>
            	<th scope="col" id="price" class="price-column">Price</th>
            	<th scope="col" id="offertype" class="offertype-column">Offer Type</th>
            	<th scope="col" id="tags" class="tags-column">Tags</th>
            	<th scope="col" id="shortcode" class="shortcode-column">Shortcode</th>
            </tr>
        </thead>
        <?php if(count($itemsList)) : ?>
        <tbody id="the-list">
        	<?php foreach($itemsList as $item) : ?>
            <tr id="id-<?php echo $item->id;?>" valign="top">
            	<td class="item-id"><?php echo $item->id; ?></td>
            	<td class="item-thumb"><img src="<?php echo $item->default_image; ?>" width="60" alt="" /></td>
            	<td class="item-title"><strong><?php echo $item->name; ?></strong></td>
            	<td class="item-price"><?php echo $item->symbol; ?><?php echo $item->price; ?></td>
            	<td class="item-offertype"><?php echo $item->offer_type_name; ?></td>
            	<td class="item-tags"><?php echo $item->tags; ?></td>
                <td class="item-shortcode">[topspin_store_item id=<?php echo $item->id; ?>]</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <?php else : ?>
        <tbody id="the-list">
        	<tr class="no-items"><td colspan="5">There are no items cached yet.</td></tr>
        </tbody>
        <?php endif; ?>
        <tfoot>
            <tr>
            	<th scope="col" id="id" class="id-column">ID</th>
            	<th scope="col" id="thumb" class="thumb-column">Thumb</th>
            	<th scope="col" id="title" class="title-column">Title</th>
            	<th scope="col" id="price" class="price-column">Price</th>
            	<th scope="col" id="offertype" class="offertype-column">Offer Type</th>
            	<th scope="col" id="tags" class="tags-column">Tags</th>
            	<th scope="col" id="shortcode" class="shortcode-column">Shortcode</th>
            </tr>
        </tfoot>
    </table>
</div>
