<div class="wrap">

	<h2>Menu Settings</h2>
	
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
	<input type="hidden" name="topspin_post_action" value="navmenus_save" />
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label for="navmenu-toggle">Activate Menu</label></th>
				<td>
					<input type="checkbox" id="navmenu-toggle" name="topspin[menu_activated]" <?php echo (TOPSPIN_MENU_ACTIVATED) ? 'checked="checked"' : ''; ?>></input><br/>
					<div class="description">
						<p>Activating this menu will automatically create a menu on the top of each store page just after the page/post title. To make this menu look the way you want will most likely require that you modify the appearance of your store page with a wordpress page template and/or the topspin.css file in the topspin-standard template.  You may, for example, want to remove the page/post title that normally appears at the top of each store page.</p>
					</div>
				</td>
			</tr>
			<tr id="store-sortable-row" valign="top">
				<?php $storesList = WP_Topspin::getStores(); ?>
				<th scope="row"><label for="topspin-navmenus-list">Store Order</label></th>
				<td>
					<ul id="topspin-navmenus-list" class="group-sortable">
					<?php foreach($storesList as $store) : ?>
						<?php echo WP_Topspin::MenuItemAdmin($store); ?>
					<?php endforeach; ?>
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