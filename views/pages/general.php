<div class="wrap">

	<form method="post" action="options.php">
	<?php settings_fields('topspin_general'); ?>

	<div id="topspin-general-api-settings">
		<h2>API Settings</h2>
		<table class="form-table">
			<tr id="topspin-general-api-settings-api-username" valign="top">
				<th scope="row">API Username:</th>
				<td>
					<input type="text" name="topspin_api_username" value="<?php echo TOPSPIN_API_USERNAME; ?>" />
					<span class="description">Go to <a href="http://app.topspin.net/account/profile/" target="_blank">Account Settings</a> to obtain your API credentials.</span>
				</td>
			</tr>
			<tr id="topspin-general-api-settings-api-key" valign="top">
				<th scope="row">API Key:</th>
				<td>
					<input type="text" name="topspin_api_key" value="<?php echo TOPSPIN_API_KEY; ?>" />
					<span class="description">Go to <a href="http://app.topspin.net/account/profile/" target="_blank">Account Settings</a> to obtain your API credentials.</span>
				</td>
			</tr>
		</table>
	</div>

	<div id="topspin-general-general-settings">
		<h2>General Settings</h2>
		<table class="form-table">
			<tr id="topspin-general-general-settings-default-store-page" valign="top">
				<th scope="row">Default Store Page:</th>
				<td>
					<select name="topspin_default_store_page_id">
						<option value="0">None</option>
						<?php
						$storePages = new WP_Query('post_type='.TOPSPIN_CUSTOM_POST_TYPE_STORE);
						if($storePages->have_posts()) : ?>
							<?php while($storePages->have_posts()) : $storePages->the_post(); ?>
							<option value="<?php the_ID(); ?>" <?php echo (TOPSPIN_DEFAULT_STORE_PAGE_ID==get_the_ID()) ? 'selected="selected"' : ''; ?>><?php the_title(); ?></option>
							<?php endwhile; wp_reset_postdata(); ?>
						<?php endif; ?>
					</select>
				</td>
			</tr>
			<tr id="topspin-general-general-settings-new-items-timeout" valign="top">
				<th scope="row">New Items Timeout:</th>
				<td>
					<?php $newTimeouts = WP_Topspin::getTimeoutOptions(); ?>
					<select id="topspin_new_timeout" name="topspin_new_items_timeout">
						<option value="">None</option>
						<?php foreach($newTimeouts as $optionValue=>$optionName) : ?>
						<option value="<?php echo $optionValue; ?>" <?php echo ($optionValue==TOPSPIN_NEW_ITEMS_TIMEOUT) ? 'selected="selected="' : ''; ?>><?php echo $optionName; ?></option>
						<?php endforeach; ?>
					</select>
					<span class="description">Specify the amount of time for an item to be marked as new when it is first synced into the website.</span>
				</td>
			</tr>
			<tr id="topspin-general-general-settings-default-template" valign="top">
				<th scope="row">Default Template:</th>
				<td>
					<select id="topspin_template_mode" name="topspin_template_mode">
						<option value="standard" <?php echo (TOPSPIN_TEMPLATE_MODE=='standard') ? 'selected="selected"' : ''; ?>>Standard</option>
						<option value="simplified" <?php echo (TOPSPIN_TEMPLATE_MODE=='simplified') ? 'selected="selected"' : ''; ?>>Simplified</option>
					</select>
					<div class="description">
						
					</div>
				</td>
			</tr>
			<tr id="topspin-general-general-settings-disable-wpadmin-shortcut" valign="top">
				<td scope="row">Disable Admin Bar Shortcut:</td>
				<td>
					<input type="checkbox" name="topspin_disable_wpadminbar_shortcut" <?php echo (TOPSPIN_DISABLE_WPADMINBAR_SHORTCUT) ? 'checked="checked"' : '' ; ?>>
					<span class="description">Check this box to disable the WordPress admin bar shortcut.</span>
				</td>
			</tr>
			<tr id="topspin-general-general-settings-group-panels" valign="top">
				<td scope="row">Group Panels:</td>
				<td>
					<input type="checkbox" name="topspin_group_panels" <?php echo (TOPSPIN_GROUP_PANELS) ? 'checked="checked"' : ''; ?>>
					<span class="description">Check this box to group the Offers, Stores, and Product panel under the main Topspin panel in the sidebar.</span>
				</td>
			</tr>
			<tr id="topspin-general-general-settings-default-grid-thumb-size" valign="top">
				<td scope="row">Default Grid Thumb Size:</td>
				<td>
					<select id="topspin_default_grid_thumb_size" name="topspin_default_grid_thumb_size">
						<option value="topspin-default-grid-thumb" <?php echo (TOPSPIN_DEFAULT_GRID_THUMB_SIZE=='topspin-default-grid-thumb') ? 'selected="selected"' : ''; ?>>Default (205x205)</option>
						<option value="topspin-small-grid-thumb" <?php echo (TOPSPIN_DEFAULT_GRID_THUMB_SIZE=='topspin-small-grid-thumb') ? 'selected="selected"' : ''; ?>>Small (125x125)</option>
						<option value="topspin-medium-grid-thumb" <?php echo (TOPSPIN_DEFAULT_GRID_THUMB_SIZE=='topspin-medium-grid-thumb') ? 'selected="selected"' : ''; ?>>Medium (225x225)</option>
						<option value="topspin-large-grid-thumb" <?php echo (TOPSPIN_DEFAULT_GRID_THUMB_SIZE=='topspin-medium-grid-thumb') ? 'selected="selected"' : ''; ?>>Large (300x300)</option>
					</select>
					<div class="description">Specify the default grid thumb size for store grid templates. If you have a narrow content area for your stores, you may need to set this to "Small (125x125)".</div>
				</td>
			</tr>
		</table>
	</div>

	<div id="topspin-general-custom-post-type-settings">
		<h2>Custom Post Type Settings</h2>
		<table class="form-table">
			<tr id="topspin-general-custom-post-type-settings-artists" valign="top">
				<th scope="row">Artists:</th>
				<td>
					<input type="text" name="topspin_post_type_artist" value="<?php echo TOPSPIN_CUSTOM_POST_TYPE_ARTIST; ?>" />
					<span class="description">Set a custom post type for artists. (Default: topspin-artist)</span>
				</td>
			</tr>
			<tr id="topspin-general-custom-post-type-settings-offers" valign="top">
				<th scope="row">Offers:</th>
				<td>
					<input type="text" name="topspin_post_type_offer" value="<?php echo TOPSPIN_CUSTOM_POST_TYPE_OFFER; ?>" />
					<span class="description">Set a custom post type for offers. (Default: topspin-offer)</span>
				</td>
			</tr>
			<tr id="topspin-general-custom-post-type-settings-stores" valign="top">
				<th scope="row">Stores:</th>
				<td>
					<input type="text" name="topspin_post_type_store" value="<?php echo TOPSPIN_CUSTOM_POST_TYPE_STORE; ?>" />
					<span class="description">Set a custom post type for store pages. (Default: topspin-store)</span>
				</td>
			</tr>
			<tr id="topspin-general-custom-post-type-settings-products" valign="top">
				<th scope="row">Products:</th>
				<td>
					<input type="text" name="topspin_post_type_product" value="<?php echo TOPSPIN_CUSTOM_POST_TYPE_PRODUCT; ?>" />
					<span class="description">Set a custom post type for products. (Default: topspin-product)</span>
				</td>
			</tr>
		</table>
	</div>

	<?php if(TOPSPIN_API_VERIFIED && TOPSPIN_POST_TYPE_DEFINED && TOPSPIN_HAS_ARTISTS) : ?>
	<div id="topspin-general-cache-settings">
		<h2>Cache Settings</h2>
		<table class="form-table">
	        <tr id="topspin-general-cache-settings-topspin-artist-id" valign="top">
	            <th scope="row"><label for="topspin_artist_id">Topspin Artist</label></th>
	            <td>
		            <?php
		            $artistQuery = WP_Topspin::getArtists();
		            if($artistQuery->have_posts()) : ?>
						<ul class="topspin-artists-list">
			            <?php while($artistQuery->have_posts()) : $artistQuery->the_post(); ?>
			            	<?php
			            	$artistMeta = WP_Topspin::getArtistMeta();
			            	$artistChecked = (WP_Topspin::artistIsChecked($artistMeta->id)) ? 'checked="checked"' : '';
			            	?>
	            			<li class="topspin-artist-item">
	            				<div class="topspin-artist-item-thumb"><?php the_post_thumbnail(); ?></div>
	            				<div class="topspin-artist-item-footer">
	                				<input id="topspin_artist_<?php echo $artistMeta->id; ?>" type="checkbox" name="topspin_artist_ids[]" value="<?php echo $artistMeta->id;?>" <?php echo $artistChecked; ?> />
	                				&nbsp; <label for="topspin_artist_<?php echo $artistMeta->id; ?>"><?php the_title(); ?> (<?php echo $artistMeta->id ?>)</label>
								</div>
	            			</li>
	            		<?php endwhile; wp_reset_postdata(); ?>
						</ul>
	            	<?php endif; ?>
	            </td>
	        </tr>
		</table>
	</div>
	<?php endif; ?>

	<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
	</form>

</div>