<?php

// Set the nonce
wp_nonce_field(plugin_basename(TOPSPIN_PLUGIN_FILE), 'topspin_nonce');

// Retrieve the store meta
$storeMeta = WP_Topspin::getStoreMeta();
?>

<div class="topspin-settings-artist-id topspin-metabox-row">
	<label>Artist</label>
	<select name="topspin[artist_id]">
		<?php
		global $post;
		$_post = $post;
		$artistQuery = WP_Topspin::getArtists();
		if($artistQuery->have_posts()) : ?>
			<?php while($artistQuery->have_posts()) : $artistQuery->the_post(); ?>
				<?php
				$artistMeta = WP_Topspin::getArtistMeta();
				$artistChecked = WP_Topspin::artistIsChecked($artistMeta->id);
				if($artistChecked) : ?>
			<option value="<?php echo $artistMeta->id; ?>" <?php echo ($storeMeta->artist_id==$artistMeta->id) ? 'selected="selected"' : ''; ?>><?php the_title(); ?></option>
				<?php endif; ?>
			<?php endwhile; ?>
		<?php endif; $post = $_post; ?>
	</select>
</div>

<div class="topspin-settings-items-per-page topspin-metabox-row">
	<label>Items Per Page</label>
	<input name="topspin[items_per_page]" type="number" value="<?php echo $storeMeta->items_per_page; ?>" />
</div>

<div class="topspin-settings-show-all-items topspin-metabox-row">
	<label>Show All Items</label>
	<input name="topspin[show_all_items]" type="checkbox" value="1" <?php echo ($storeMeta->show_all_items) ? 'checked="checked"' : ''; ?> />
</div>

<div class="topspin-settings-desc-length topspin-metabox-row">
	<label>Item Description Length</label>
	<input name="topspin[desc_length]" type="number" value="<?php echo $storeMeta->desc_length; ?>" />
</div>

<div class="topspin-settings-items-sale-tag topspin-metabox-row">
	<label>Sale Tag</label>
	<select name="topspin[sale_tag]">
		<option value="">None</option>
		<?php
		$spinTags = WP_Topspin::getSpinTags();
		foreach($spinTags as $tag) : ?>
			<option value="<?php echo $tag->slug; ?>" <?php echo ($storeMeta->sale_tag==$tag->slug) ? 'selected="selected"' : ''; ?>><?php echo $tag->name; ?></option>
		<?php endforeach; ?>
	</select>
</div>

<div class="topspin-settings-grid-columns topspin-metabox-row">
	<label>Grid Columns</label>
	<input name="topspin[grid_columns]" type="number" value="<?php echo $storeMeta->grid_columns; ?>" />
</div>

<div class="topspin-settings-default-sorting topspin-metabox-row">
	<label>Sorting</label>
	<select class="topspin-sorting-selectbox" name="topspin[default_sorting]">
		<option value="alphabetical" <?php echo ($storeMeta->default_sorting=='alphabetical') ? 'selected="selected"' : ''; ?>>Alphabetical</option>
		<option value="chronological" <?php echo ($storeMeta->default_sorting=='chronological') ? 'selected="selected"' : ''; ?>>Chronological</option>
	</select>
</div>

<div class="topspin-settings-default-sorting-by topspin-metabox-row">
	<label>Sort By</label>
	<select class="topspin-sorting_by-selectbox" name="topspin[default_sorting_by]">
		<option value="offertype" <?php echo ($storeMeta->default_sorting_by=='offertype') ? 'selected="selected"' : ''; ?>>Offer Types</option>
		<option value="tags" <?php echo ($storeMeta->default_sorting_by=='tags') ? 'selected="selected"' : ''; ?>>Tags</option>
		<option value="manual" <?php echo ($storeMeta->default_sorting_by=='manual') ? 'selected="selected"' : ''; ?>>Manual</option>
	</select>
</div>