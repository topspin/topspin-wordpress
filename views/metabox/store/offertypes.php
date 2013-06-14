<?php

// Get store meta
$storeMeta = WP_Topspin::getStoreMeta();
// Get offer types
$offerTypes = Topspin_API::getOfferTypes();
// Merge types
$mergeOfferTypes = WP_Topspin::mergeOfferTypes($offerTypes, $storeMeta->offer_type);

?>

<ol id="topspin-offer-types-list">
	<?php foreach($mergeOfferTypes as $value) : ?>
	<li class="topspin-offer-types-item">
		<input class="topspin-offertype-checkbox" type="checkbox" name="topspin[offer_type][]" value="<?php echo $value; ?>" value="1" <?php echo (in_array($value, $storeMeta->offer_type)) ? 'checked="checked"' : ''; ?> />
		<label><?php echo $offerTypes[$value]; ?></label>
	</li>
	<?php endforeach; ?>
</ol>