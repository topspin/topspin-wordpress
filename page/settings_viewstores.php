<?php
global $store;
$storesList = $store->getStores();
?>

<div class="wrap">
	<h2>
    	Stores
        <a class="button add-new-h2" href="<?php bloginfo('home'); ?>/wp-admin/admin.php?page=topspin/page/settings_edit">Add New</a>
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
            <tr id="store-<?=$storeItem->store_id;?>" valign="top">
            	<td class="store-id"><?=$storeItem->store_id;?></td>
            	<td class="store-title">
                	<strong><a href="<?=get_bloginfo('home');?>/wp-admin/admin.php?page=topspin/page/settings_edit&amp;action=edit&amp;id=<?=$storeItem->store_id;?>"><?=$storeItem->post_title;?></a></strong>
                    <div class="row-actions">
                    	<span class="edit"><a href="<?=get_bloginfo('home');?>/wp-admin/admin.php?page=topspin/page/settings_edit&amp;action=edit&amp;id=<?=$storeItem->store_id;?>">Edit</a> |</span>
                        <span class="trash"><a class="submitdelete" href="<?=get_bloginfo('home');?>/wp-admin/admin.php?page=topspin/page/settings_edit&amp;action=delete&amp;id=<?=$storeItem->store_id;?>">Trash</a></span>
                    </div>
                </td>
                <td class="store-shortcode">[topspin_buy_buttons id=<?=$storeItem->store_id;?>]</td>
                <td class="store-created-date"><?=date("F j, Y h:i:sa",strtotime($storeItem->created_date));?></td>
            	<td class="store-manage">
					<a href="<?=get_bloginfo('home');?>/wp-admin/admin.php?page=topspin/page/settings_edit&amp;action=edit&amp;id=<?=$storeItem->store_id;?>">Edit</a> |
					<a href="<?=get_permalink($storeItem->ID);?>" target="_blank">View</a>
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