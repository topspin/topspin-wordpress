var topspin_admin = {

	/**
	 * Holds admin options
	 *
	 * @param object opts
	 */
	opts : {
		/**
		 * Holds custom post types
		 *
		 * @param object post_types
		 */
		post_types : {
			/**
			 * The custom offer post type
			 *
			 * @param string
			 */
			offer : false,
			/**
			 * The custom product post type
			 *
			 * @param string
			 */
			product : false,
			/**
			 * The custom store post type
			 *
			 * @param string
			 */
			store : false
		}
	},
	/**
	 * Holds all stored DOM elements
	 *
	 * @param object doms
	 */
	doms : {
		/**
		 *	Contains the loader HTML DOM object
		 *
		 * @param object|bool
		 */
		loader : false
	},
	/**
	 * Initiate the Topspin admin
	 *
	 * @return void
	 */
	init : function(opts) {
		// Merge options
		topspin_admin.opts = jQuery.extend(topspin_admin.opts, opts);

		// Hide some elements
		jQuery('a[href="post-new.php?post_type=' + topspin_admin.opts.post_types.offer + '"]').remove();
		jQuery('a[href="post-new.php?post_type=' + topspin_admin.opts.post_types.product + '"]').remove();

		// Initialize the menus
		switch(topspin_admin.getQueryValue('page')) {
			case 'topspin/page/menus':
				topspin_admin.menus.init();
				break;
		}
		topspin_admin.registerEvents();
		topspin_admin.loader.init();
		topspin_admin.store.init();
		topspin_admin.offer.init();
		topspin_admin.product.init();
	},
	/**
	 * Register admin JS events
	 *
	 * @return void
	 */
	registerEvents : function() {
	},
	/* !----- Events ----- */
	events : {
	},	
	/**
	 * Retrieves the value of the query string variable
	 *
	 * @param string name
	 * @return string The value of the query variable
	 */
	getQueryValue : function(name) {
		name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
		var regexS = "[\\?&]" + name + "=([^&#]*)";
		var regex = new RegExp(regexS);
		var results = regex.exec(window.location.search);
		if(results == null) return "";
		else return decodeURIComponent(results[1].replace(/\+/g, " "));
	},
	/* !----- Store ----- */
	store : {
		sortingEnabled : false,
		init : function() {
			topspin_admin.store.registerEvents();

			jQuery('#topspin-offer-types-list').sortable({
				stop : topspin_admin.store.events.onSortableStop
			});
	
			jQuery('#topspin-spin-tags-list').sortable({
				stop : topspin_admin.store.events.onSortableStop
			});

			topspin_admin.store.checkSortingBy();
			topspin_admin.store.updatePreview();

		},
		registerEvents : function() {
			// Bind Sort By select change
			jQuery('select[name="topspin[default_sorting_by]"]').live('change', topspin_admin.store.events.onChangeDefaultSortingBy);
	
			// Bind Live Preview
			jQuery('.topspin-tag-checkbox, .topspin-offertype-checkbox').live('click', topspin_admin.store.events.onClickOfferTypesTagCheckbox);
			jQuery('.topspin-sortby-selectbox, .topspin-sorting-selecbox').live('change', topspin_admin.store.events.onChangeSorting);
	
			// Bind Featured Item Delete
			jQuery('.topspin-featured-delete').live('click', topspin_admin.store.events.onClickDeleteFeaturedItem);
			
			// Bind Manual Sorting Hide Toggle
			jQuery('.topspin-item-thumbnail-image').live('click', topspin_admin.store.events.onClickManualItemThumbnailImage);
		},
		events : {
			/**
			 * onclick callback for manual item hide/show toggling
			 */
			onClickManualItemThumbnailImage : function(e) {
				e.preventDefault();
				if(topspin_admin.store.sortingEnabled) {
					var theImage = jQuery(this);
					var theItem = theImage.parents('.topspin-preview-item');
					var theVisibleInput = jQuery('.topspin-item-is-visible', theItem);
					var isVisible = theVisibleInput.val();
					var newVisibility = (isVisible == 'true') ? false : true;
					theVisibleInput.val(newVisibility);
					if(newVisibility) { theItem.addClass('topspin-item-show'); }
					else { theItem.removeClass('topspin-item-show'); }
				}
			},
			/**
			 * onChange callback for Sort By select dropdown
			 *
			 * @param Event e
			 * @return void
			 */
			onChangeDefaultSortingBy : function(e) {
				switch(e.target.value) {
					case 'offertype':
					case 'tags':
						topspin_admin.store.disableManualSorting();
						break;
					case 'manual':
						topspin_admin.store.enableManualSorting();
						break;
				}
			},
			/**
			 * OnChange callback for Sorting dropdown
			 *
			 * @param Event e
			 * @return void
			 */
			onChangeSorting : function(e) {
				topspin_admin.store.updatePreview();
			},
			/**
			 * OnClick callback for Featured Item row delete button
			 *
			 * @param Event e
			 * @return void
			 */
			onClickDeleteFeaturedItem : function(e) {
				jQuery(this).parents('.topspin-metabox-row').remove();
			},
			/**
			 * OnClick callback for offer types or tags checkboxes
			 *
			 * @param Event e
			 * @return void
			 */
			onClickOfferTypesTagCheckbox : function(e) {
				topspin_admin.store.updatePreview();
			},
			/**
			 * sortstop callback for jQuery sortable object
			 *
			 * @param Event e
			 * @param object ui
			 * @return void
			 */
			onSortableStop : function(e, ui) {
				topspin_admin.store.updatePreview();
			}
		},
		/**
		 * Enables manual sorting
		 *
		 * @return void
		 */
		enableManualSorting : function() {
			jQuery('#topspin-preview-grid').sortable().disableSelection();
			jQuery('#topspin-preview-grid').sortable('enable');
			topspin_admin.store.sortingEnabled = true;
		},
		/**
		 * Disables manual sorting
		 *
		 * @return void
		 */
		disableManualSorting : function() {
			jQuery('#topspin-preview-grid').sortable('disable');
			topspin_admin.store.sortingEnabled = false;
		},
		checkSortingBy : function() {
			// Check sorting
			jQuery('select[name="topspin[default_sorting_by]"]').trigger('change');
		},
		/**
		 * Updates the preview according to the current site settings
		 *
		 * @return void
		 */
		updatePreview : function() {
			if(jQuery('#topspin-preview-grid').length) {
				var settings = {
					post_ID : jQuery('input[name="post_ID"]').val(),
					artist_id : jQuery('select[name="topspin[artist_id]"]').val(),
					default_sorting : jQuery('select[name="topspin[default_sorting]"]').val(),
					default_sorting_by : jQuery('select[name="topspin[default_sorting_by]"]').val(),
					offer_type : jQuery('.topspin-offertype-checkbox:checked').map(topspin_admin.store.getCheckedValues).get(),
					tags : jQuery('.topspin-tag-checkbox:checked').map(topspin_admin.store.getCheckedValues).get()
				};
				var data = {
					action : 'topspin_store_preview_update',
					settings : settings
				};
				// Make the request
				jQuery.getJSON(ajaxurl, data, function(ret) {
					if(ret.status=='success') {
						jQuery('#topspin-preview-grid').html(ret.response);
					}
				});
			}
		},
		/**
		 * Retrieves the value of the element
		 *
		 * @return string The value of the element
		 */
		getCheckedValues : function() { return this.value }
	},
	/* !----- Offer ----- */
	offer : {
		/**
		 * Initializes the offer JS
		 *
		 * @global string typenow
		 * @return void
		 */
		init : function() {
			// Remove row-actions
			jQuery('.type-' + topspin_admin.opts.post_types.offer + ' .row-actions').remove();
			// Remove elements in the Edit page
			if(typenow == topspin_admin.opts.post_types.offer) {
				jQuery('#major-publishing-actions, #postimagediv, #postdivrich, #wp-fullscreen-body').remove();
			}
			topspin_admin.offer.registerEvents();
		},
		registerEvents : function() {
			// Bind Re-sync Offer button
			jQuery(document).on('click', '.topspin-resync-offer', topspin_admin.offer.events.onClickResyncOffer);
			// Bind Re-sync Inventory button
			jQuery(document).on('click', '.topspin-resync-offer-products', topspin_admin.offer.events.onClickResyncOfferInventory);
		},
		/* !----- Offer > Events ----- */
		events : {
			/**
			 * OnClick callback for Re-sync Offer button
			 *
			 * @param Event e
			 * @return void
			 */
			onClickResyncOffer : function(e) {
				e.preventDefault();
				var offer_id = jQuery(this).data('offer-id');
				topspin_admin.offer.resync(offer_id);
			},
			/**
			 * OnClick callback for Re-sync Inventory button
			 *
			 * @param Event e
			 * @return void
			 */
			onClickResyncOfferInventory : function(e) {
				e.preventDefault();
				var offer_id = jQuery(this).data('offer-id');
				topspin_admin.offer.resyncInventory(offer_id);
			}
		},
		/**
		 * Re-syncs the offer
		 *
		 * @global string ajaxurl
		 * @param int offer_id
		 * @return void
		 */	
		resync : function(offer_id) {
			var data = {
				action : 'topspin_resync_offer',
				offer_id : offer_id
			};
			topspin_admin.loader.show();
			jQuery.post(ajaxurl, data, function(response) {
				if(response.status=='success') { window.location.reload(); }
				else {
					alert('Error trying to re-sync offer.');
					topspin_admin.loader.hide();
				}
			});
		},
		/**
		 * Re-syncs the offer's product and inventory data
		 *
		 * @global string ajaxurl
		 * @param int offer_id
		 * @return void
		 */
		resyncInventory : function(offer_id) {
			var data = {
				action : 'topspin_resync_offer_inventory',
				offer_id : offer_id
			};
			topspin_admin.loader.show();
			jQuery.post(ajaxurl, data, function(response) {
				if(response.status=='success') { window.location.reload(); }
				else {
					alert('Error trying to re-sync offer\'s inventory.');
					topspin_admin.loader.hide();
				}
			});
		}
	},
	/* !----- Product ----- */
	product : {
		/**
		 * Initializes the product JS
		 *
		 * @global string typenow
		 * @return void
		 */
		init : function() {
			// Remove row-actions
			jQuery('.type-' + topspin_admin.opts.post_types.product + ' .row-actions').remove();
			// Remove elements in the Edit page
			if(typenow == topspin_admin.opts.post_types.product) {
				jQuery('#major-publishing-actions, #postimagediv').remove();
			}
		}
	},
	/* !----- Menus ----- */
	menus : {
		/**
		 * Initializes the Topspin menus
		 *
		 * @return void
		 */
		init : function() {
			topspin_admin.menus.registerEvents();
		},
		registerEvents : function() {
			// Bind toggling of nav menus
			jQuery('#navmenu-toggle').live('click', topspin_admin.menus.events.onClickToggle);
	
			// Bind sortable
			jQuery('.group-sortable').sortable({
				'containment' : 'parent'
			});
		},
		/* !---- Menus > Events ----- */
		events : {
			onClickToggle : function(e) {
				if(e.target.checked) { jQuery('#store-sortable-row').show(); }
				else { jQuery('#store-sortable-row').hide(); }
			}
		}
	},
	/* !----- Loader ----- */
	loader : {
		/**
		 * Initializes the loader
		 *
		 * @return void
		 */
		init : function() {
			topspin_admin.loader.check();
		},
		/**
		 * Checks to see if the topspin loader exists.
		 * If it doesn't, create it and append it to the DOM
		 *
		 * @return void
		 */
		check : function() {
			if(!topspin_admin.doms.loader) {
				var loaderParent = jQuery('<div />');
				var loaderContainer = jQuery('<div />');
				var loaderImg = jQuery('<div />');
				var loaderText = jQuery('<div />');
				loaderContainer
					.addClass('topspin-loader-container')
					.appendTo(loaderParent);
				loaderImg
					.addClass('topspin-loader-img')
					.appendTo(loaderContainer);
				loaderText
					.addClass('topspin-loader-text')
					.html('Loading...')
					.appendTo(loaderContainer);
				loaderParent
					.addClass('topspin-loader')
					.appendTo('body');
					topspin_admin.doms.loader = loaderParent;
			}
		},
		/**
		 * Displays the loader
		 *
		 * @return void
		 */
		show : function() {
			topspin_admin.doms.loader.show();
		},
		/**
		 * Hide the loader
		 *
		 * @return void
		 */
		hide : function() {
			topspin_admin.doms.loader.hide();
		}
	}
};