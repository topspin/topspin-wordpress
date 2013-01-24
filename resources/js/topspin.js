var topspin = {
	opts : {},
	init : function(opts) {
		// Merge options
		this.opts = jQuery.extend(opts, opts);
		// Register events
		this.registerEvents();
	},
	registerEvents : function() {
		// Bind lightbox
		jQuery(document).on('click', '.topspin-viewbox', topspin.events.onClickViewBox);
		// Bind Gallery Pager
		jQuery(document).on('click', '.topspin-gallery-pager-image', topspin.events.onClickGalleryPagerImage);
	},
	events : {
		/**
		 * Binds the onclick event for view offer lightbox
		 *
		 * @return void
		 */
		onClickViewBox : function(e) {
			e.preventDefault();
			var theAnchor = jQuery(this);
			var offer_id = theAnchor.data('offer-id');
			topspin.viewMore(offer_id);
		},
		/**
		 * Binds the onclick event for gallery page images
		 *
		 * @return void
		 */	
		onClickGalleryPagerImage : function(e) {
			e.preventDefault();
			var image = jQuery(this);
			var pagerItem = image.parents('.topspin-gallery-pager-item');
			var fullSrc = image.data('full-src');
			// Toggle active
			pagerItem.addClass('topspin-gallery-pager-active');
			pagerItem.siblings('.topspin-gallery-pager-item').removeClass('topspin-gallery-pager-active');
			// Fade switch image
			jQuery('.topspin-item-thumbnail-image').fadeOut('fast', function() {
				jQuery(this).attr('src', fullSrc).fadeIn('fast');
			});
		},
		/**
		 * Callback function for topspin.viewMore when the JSON is loaded
		 */
		onViewMoreJSONCallback : function(ret) {
			if(ret.status=='success') {
				jQuery.fancybox.open({
					content : ret.response,
					afterShow : topspin.events.onViewAfterShow
				});
			}
		},
		/**
		 * Callback function after the fancybox has finished loading and showing when the view more lightbox is launched
		 */
		onViewAfterShow : function() {
			if(TSPF) {
				var fancyBoxes = document.getElementsByClassName('fancybox-inner');
				TSPF.BuyButton.initializeBuyButtons(fancyBoxes ? fancyBoxes[0] : null);
			}
		}
	},
	/**
	 * Launches the lightbox for the offer ID
	 *
	 * @param int offer_id
	 * @return void
	 */
	viewMore : function(offer_id) {
		var data = {
			action : 'topspin_view_more',
			offer_id : offer_id
		};
		jQuery.getJSON(topspin.opts.ajaxurl, data, topspin.events.onViewMoreJSONCallback);
	}
};