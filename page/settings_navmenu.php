<?php

/*
 *
 *      Last Modified:                  September 29, 2011
 *      --------------------------------------------------
 *      Change Log 
 *      --------------------------------------------------
 *
 *      2011-09-29
        	- File first created. (eThan)
 *
 */

global $store;
$orderedStoresList = $store->getStoresInOrder();
if ($store->getSetting('topspin_navmenu') == 'on') { 
	$menu_set_on = true;
} else {
	$menu_set_on = false;
}
$template = $store->getSetting('topspin_template_mode');

// if the form has been submitted...
if (isset($_POST['storesList'])) {

	// check to see if the menu has been newly activated or deactivated
	if ( $menu_set_on && !isset($_POST['navmenu-on']) ) {
		$store->setSetting('topspin_navmenu','off'); 
		$menu_set_on = false;
		$menu_set_to = 'off'; 
	}
	if ( !$menu_set_on && isset($_POST['navmenu-on']) ) { 
		$store->setSetting('topspin_navmenu','on');
		$menu_set_on = true;
		$menu_set_to = 'on';
	}
	
	// if the menu is on check the order
	if ( $menu_set_on ) { 

		// check to see if the order has changed
		$newStoresList = $_POST['storesList'];
		$orderChanged = false;
		foreach ($newStoresList as $index => $storeName) {
			if ($storeName != $orderedStoresList[$index]->post_title) {
				$orderChanged = true;
				break;
			}
		}
	
		// if the order changed update the stores with their new position
		if ($orderChanged) {  
			foreach ($orderedStoresList as $s) {
				foreach ($newStoresList as $index => $storeName) {
					if ($s->post_title == $storeName) {
						$s->navmenu_position = $index+1;
						$sToUpdate = (array)$s;
                        	        	$success = $store->updateStoreNavMenuPosition($sToUpdate['navmenu_position'],$sToUpdate['store_id']);
					}
				}
			}
			$orderedStoresList = $store->getStoresInOrder();
		} 
	}	
}
?>

<script language="javascript">

jQuery(function($) {
	// make the list of stores sortable
	var smpSortable;
	var smpSortableInit = function() {
		try { // a hack to make sortables work in jQuery 1.2+ and IE7
			$('.group-sortable').sortabledestroy();
		} catch(e) {}
  			smpSortable = $('.group-sortable').sortable({
  			accept: 'sortable',
  			onStop: smpSortableInit
  		});
 	}
	// initialize sortable
	smpSortableInit();

	// when the document is ready disable/enable ordered list based on menu toggle checkbox
	if ( $('#navmenu-toggle').is(':checked') ) { 
		$('.group-sortable').sortable('enable');	
	}
	else {  $('.group-sortable').sortable('disable'); }

	// when the menu toggle checkbox is clicked activate/deactivate sortable list
	$('#navmenu-toggle').click(function() {
		var checked = $('#navmenu-toggle').is(':checked');
		if (checked) {
			$('.group-sortable').sortable('enable');
		}
		else { $('.group-sortable').sortable('disable'); }
	});
});
</script>

<?php if ($template == 'simplified') : ?>
<div class="wrap">
	<h2>Menu Settings</h2>

	<span class="description">
	The menu feature currently only works with the topspin-standard template.  If you would like to use the menu feature, you will need to switch to the topspin-standard template in the "Settings" menu.
	</span>
</div>
<?php else : ?>
<div class="wrap">
	<h2>Menu Settings</h2>

	<?php if ( $orderChanged && $menu_set_to == 'on' ) : ?>
		<div class="updated settings-error"><p><strong>Menu turned on and store order updated.</strong></p></div> 
	<?php elseif ( $orderChanged ) : ?>
		<div class="updated settings-error"><p><strong>Store order updated.</strong></p></div>
	<?php elseif ( $menu_set_to == 'off' ) : ?>
		<div class="updated settings-error"><p><strong>The Menu is now off.</strong></p></div>
	<?php elseif ( $menu_set_to == 'on' ) : ?>
		<div class="updated settings-error"><p><strong>The Menu is now on.</strong></p></div>	
	<?php endif; ?>
	
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<table class="form-table">
		<tbody>
			<tr valign="top">
			<th scope="row"><label for="navmenu-toggle">Activate Menu</label></th>
			<td>
	<input type="checkbox" id="navmenu-toggle" name="navmenu-on" <?php if ($menu_set_on) { echo 'checked'; } ?> ></input><br/>
	<span class="description" width="500px">
	Activating this menu will automatically create a menu on the top of each store page just after the page/post title.  To make this menu look the way you want will most likely require that you modify the appearance of your store page with a wordpress page template and/or the topspin.css file in the topspin-standard template.  You may, for example, want to remove the page/post title that normally appears at the top of each store page.
	</span>
			</td>
			</tr>
			
			<tr valign="top">
			<th scope="row"><label for="store-order-list">Store Order</label></th>
			<td>
	<ul id="store-order-list"class="group-sortable">
	<?php foreach ($orderedStoresList as $item) : ?>
		<li class="menu-item" id="<?php echo $item->post_title; ?>"><input type="hidden" name="storesList[]" value="<?php echo $item->post_title; ?>"><?php echo $item->post_title; ?></li>
	<?php endforeach; ?>
	</ul>
	<span class="description">Drag items to change the store order.</span>
	<span class="description">** Store must be activated first **</span>
			</td>
			</tr>
			
			<tr valign="top">
			<td>
	<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
			</td>
			</tr>
		</tbody>
	</table>
	</form>
</div> 
<?php endif; ?>
