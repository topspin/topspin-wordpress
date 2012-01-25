<?php

/*
 *
 *      Last Modified:                  January 24, 2012
 *      --------------------------------------------------
 *      Change Log 
 *      --------------------------------------------------
 *
 *      2012-01-24
        	- Nav menus @eThan
 *
 */

global $store;
$parentStores = $store->stores_get_nested_list();

$navmenuStatus = $store->getSetting('topspin_navmenu');

$success = '';

//If form is submitted
if($_SERVER['REQUEST_METHOD']=='POST') {
	//Set activation status
	$navmenuStatus = (isset($_POST['navmenu-on']) && $_POST['navmenu-on']=='on') ? 1 : 0;
	$store->setSetting('topspin_navmenu',$navmenuStatus);

	//Set menu orders
	if(count($_POST['storesList'])) {
		foreach($_POST['storesList'] as $orderNumber=>$postID) {
			$postArgs = array(
				'menu_order' => $orderNumber,
				'ID' => $postID
			);
			wp_update_post($postArgs);
		}
	}

	$parentStores = $store->stores_get_nested_list();
	$success = 'Settings saved.';
}


function topspin_store_sortable_item($store,$level=0) { ?>
	<li class="menu-item" id="<?php echo $store->post_title; ?>">
		<input type="hidden" name="storesList[]" value="<?php echo $store->ID; ?>">
		<label>
			<?php echo $store->post_title; ?>
			<?php if(strlen($store->internal_name)) : ?>(<?php echo $store->internal_name; ?>)<?php endif; ?>	
		</label>
		<?php if(count($store->store_childs)) : ?>
		<ul class="group-sortable">
			<?php foreach($store->store_childs as $child) : topspin_store_sortable_item($child,$level+1); endforeach; ?>
		</ul>
		<?php endif; ?>
	</li>
<?php } ?>

<script language="javascript">

jQuery(function($) {
	// make the list of stores sortable
	var smpSortable;
	var smpSortableInit = function() {
		try { $('.group-sortable').sortabledestroy(); } catch(e) { } // a hack to make sortables work in jQuery 1.2+ and IE7 
		smpSortable = $('.group-sortable').sortable({
			accept: 'sortable',
			onStop: smpSortableInit,
			change : function(e,ui) {
			}
  		});
 	};
	var enableSort = function() {
		$('#store-sortable-row').show();
		$('.group-sortable').sortable('enable');
	};
	var disableSort = function() {
		$('#store-sortable-row').hide();
		$('.group-sortable').sortable('disable');
	};

	// when the document is ready disable/enable ordered list based on menu toggle checkbox
	if($('#navmenu-toggle').is(':checked')) { enableSort(); }
	else { disableSort(); }

	// when the menu toggle checkbox is clicked activate/deactivate sortable list
	$('#navmenu-toggle').click(function() {
		var checked = $('#navmenu-toggle').is(':checked');
		if(checked) { enableSort(); }
		else { disableSort(); }
	});

	// initialize sortable
	smpSortableInit();

});
</script>

<div class="wrap">
	<h2>Menu Settings</h2>
	
	<?php if($success) : ?><div class="updated settings-error"><p><strong><?php echo $success; ?></strong></p></div><?php endif; ?>
	
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="navmenu-toggle">Activate Menu</label></th>
				<td>
					<input type="checkbox" id="navmenu-toggle" name="navmenu-on" <?php echo ($navmenuStatus) ? 'checked="checked"' : ''; ?>></input><br/>
					<div class="description">
						<p>Activating this menu will automatically create a menu on the top of each store page just after the page/post title. To make this menu look the way you want will most likely require that you modify the appearance of your store page with a wordpress page template and/or the topspin.css file in the topspin-standard template.  You may, for example, want to remove the page/post title that normally appears at the top of each store page.</p>
						<p><strong><em>If you've created your store to version 3.3.3, you must add this template tag in your theme file to display the menu: &lt;?php topspin_get_nav_menu(); ?&gt;</em></strong></p>
					</div>
				</td>
			</tr>
			<tr id="store-sortable-row" valign="top">
				<th scope="row"><label for="store-order-list">Store Order</label></th>
				<td>
					<ul id="store-order-list" class="group-sortable">
					<?php foreach($parentStores as $item) : topspin_store_sortable_item($item); endforeach; ?>
					</ul>
					<span class="description">Drag items to change the store order.</span>
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