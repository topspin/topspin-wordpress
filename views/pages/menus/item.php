<li class="menu-item" id="store-<?php echo $store->ID; ?>">
	<input type="checkbox" name="topspin[menus][]" value="<?php echo $store->ID; ?>" <?php echo ($store->menu_order) ? 'checked="checked"' : ''; ?> />
	<label><?php echo $store->post_title; ?></label>
	<?php
	$args = array(
		'post_parent' => $store->ID
	);
	$childStores = WP_Topspin::getStores($args);
	if(count($childStores)) : ?>
	<ul class="group-sortable">
		<?php foreach($childStores as $child) : ?>
			<?php echo WP_Topspin::MenuItemAdmin($child); ?>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</li>