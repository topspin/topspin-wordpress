jQuery(function($) {

	var viewProduct = function(productID) {
		var href = '#topspin-view-more-' + productID;
		if($(href).length) {
			$.colorbox({
				inline : true,
				href : href
			});
		}
	};

	if(window.location.hash) {
		if(window.location.hash.indexOf('#!/')!=-1) {
			var productID = (window.location.hash) ? window.location.hash.replace('#!/','') : 0;
			if(productID) { viewProduct(productID); }
		}
	}

	$('a.topspin-view-item').live('click',function(e) {
		var pid = $(this).attr('href').replace('#!/','');
		setTimeout(function() { viewProduct(pid); },10);
	});
	
	$('.topspin-view-more-image-pager .topspin-view-more-image-pager-item a').live('click',function(e) {
		e.preventDefault();
		var img = $('img',this);
		var defaultImage  = $('.topspin-view-more-image-default img',$(this).parent().parent().parent().parent());
		defaultImage.attr('src',img.attr('src'));
	});
	
	$('.topspin-view-more-image .topspin-view-more-image-default .topspin-view-more-image-default-cell a').live('click',function(e) {
		$(this).parent().parent().parent().toggleClass('topspin-view-more-image-zoom');
	});
	
	//Pre 3.2
	$('a.topspin-colorbox').live('click',function(e) {
		e.preventDefault();
		var pid = $(this).attr('href').replace('#topspin-view-more-','');
		viewProduct(pid);
	});

});