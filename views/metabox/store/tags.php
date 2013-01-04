<?php

// Get store meta
$storeMeta = WP_Topspin::getStoreMeta();
// Get spin tags
$spinTags = WP_Topspin::getSpinTags();
// Merge types
$mergeSpinTags = WP_Topspin::mergeSpinTags($spinTags, $storeMeta->tags);

?>

<ol id="topspin-spin-tags-list">
	<?php foreach($mergeSpinTags as $slug) : ?>
	<li class="topspin-spin-tags-item">
		<input class="topspin-tag-checkbox" type="checkbox" name="topspin[tags][]" value="<?php echo $slug; ?>" value="1" <?php echo (in_array($slug, $storeMeta->tags)) ? 'checked="checked"' : ''; ?> />
		<label><?php echo $spinTags[$slug]->name; ?></label>
	</li>
	<?php endforeach; ?>
</ol>