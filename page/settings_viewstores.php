<?php

/*
 *
 *	Last Modified:			April 6, 2011
 *
 *	--------------------------------------
 *	Change Log
 *	--------------------------------------
 *	2011-04-06
 		- Updated bloginfo('home') and get_home_url() to bloginfo('wpurl');
 *	2011-03-23
 		- Updated get_bloginfo('home') to get_home_url();
 *
 */

global $store;
$storesList = $store->getStores();

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
            <tr id="store-<?php echo $storeItem->store_id;?>" valign="top">
            	<td class="store-id"><?php echo $storeItem->store_id;?></td>
            	<td class="store-title">
                	<strong><a href="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=topspin/page/settings_edit&amp;action=edit&amp;id=<?php echo $storeItem->store_id;?>"><?php echo $storeItem->post_title;?></a></strong>
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
