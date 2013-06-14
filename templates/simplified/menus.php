<?php $storesList = WP_Topspin::getStores(); ?>

<div class="topspin-store-navmenu">
	<ul class="topspin-store-navmenu">
		<?php foreach($storesList as $store) : WP_Topspin::MenuItem($store); endforeach; ?>
	</ul>
</div>

<script type="text/javascript">
jQuery(function($) {
   var path = '/' + location.pathname.substring(1);
   if ( path ) { 
     $('a[href$="' + path + '"]').attr('id','selected');
   }
});
</script>