<?php

/*
 *
 *	Last Modified:			September 7, 2011
 *
 *	--------------------------------------
 *	Change Log
 *	--------------------------------------
 *	2011-09-23
 		- Updated Topspin_Store::getStores() to Topspin_Store::stores_get_nested_list()
 *	2011-09-07
 		- Updated list to display in a nested format
 		- Added permalink and internal name for administrative purposes
 *	2011-04-06
 		- Updated bloginfo('home') and get_home_url() to bloginfo('wpurl');
 *	2011-03-23
 		- Updated get_bloginfo('home') to get_home_url();
 *
 */

global $store;
$storesList = $store->stores_get_nested_list('publish',0);

function topspin_viewstore_item($storeItem,$level=0) { ?>
			<tr id="store-<?php echo $storeItem->store_id;?>" class="store-level-<?php echo $level; ?>" valign="top">
				<td class="store-id"><?php echo $storeItem->store_id;?></td>
				<td class="store-title">
					<?php for($i=0;$i<$level;$i++) { echo '&mdash; '; } ?>
			    	<strong><a href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=topspin/page/settings_edit&amp;action=edit&amp;id=<?php echo $storeItem->store_id;?>"><?php echo $storeItem->post_title;?></a></strong>
					<?php if(strlen($storeItem->internal_name)) : ?>
					(<?php echo $storeItem->internal_name; ?>)
					<?php endif; ?>
			    	<div class="row-permalink"><em><?php echo get_permalink($storeItem->ID);?></em></div>
			        <div class="row-actions">
			        	<span class="edit"><a href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=topspin/page/settings_edit&amp;action=edit&amp;id=<?php echo $storeItem->store_id;?>">Edit</a> |</span>
			            <span class="trash"><a class="submitdelete" href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=topspin/page/settings_edit&amp;action=delete&amp;id=<?php echo $storeItem->store_id;?>">Trash</a></span>
			        </div>
			    </td>
			    <td class="store-shortcode">[topspin_buy_buttons id=<?php echo $storeItem->store_id;?>]</td>
			    <td class="store-created-date"><?php echo date("F j, Y h:i:sa",strtotime($storeItem->created_date));?></td>
				<td class="store-manage">
					<a href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=topspin/page/settings_edit&amp;action=edit&amp;id=<?php echo $storeItem->store_id;?>">Edit</a> |
					<a href="<?php echo get_permalink($storeItem->ID);?>" target="_blank">View</a>
			    </td>
			</tr>
<?php
	if($storeItem->store_childs) {
		foreach($storeItem->store_childs as $storeChild) {
			topspin_viewstore_item($storeChild,$level+1);
		}
	}
} //end topspin_viewstore_item

?>

<div class="wrap">
	<h2>
    	Stores
        <a class="button add-new-h2" href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=topspin/page/settings_edit">Add New</a>
    </h2>

    <table class="topspin-stores-list wp-list-table widefat fixed" cellspacing="0">
        <thead>
            <tr>
            	<th scope="col" id="id" class="id-column">ID</th>
            	<th scope="col" id="title" class="title-column">Title</th>
            	<th scope="col" id="shortcode" class="shortcode-column">Shortcode</th>
                <th scope="col" id="created_date" class="date-column">Created Date</th>
            	<th scope="col" id="manage" class="manage-column">Manage</th>
            </tr>
        </thead>
        <?php if(count($storesList)) : ?>
        <tbody id="the-list">
        	<?php foreach($storesList as $storeItem) : ?>
        		<?php topspin_viewstore_item($storeItem); ?>
            <?php endforeach; ?>
        </tbody>
        <?php else : ?>
        <tbody id="the-list">
        	<tr class="no-items"><td colspan="5">There are no stores created yet.</td></tr>
        </tbody>
        <?php endif; ?>
        <tfoot>
            <tr>
            	<th scope="col" id="id" class="id-column">ID</th>
            	<th scope="col" id="title" class="title-column">Title</th>
            	<th scope="col" id="shortcode" class="shortcode-column">Shortcode</th>
                <th scope="col" id="created_date" class="date-column">Created Date</th>
            	<th scope="col" id="manage" class="manage-column">Manage</th>
            </tr>
        </tfoot>
    </table>
</div>
