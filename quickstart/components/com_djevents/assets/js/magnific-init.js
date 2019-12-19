// initialization of magnific popup for all album instances
!function($){

$(document).ready(function(){
	$('.djev_gallery').each(function() {
		
		$(this).magnificPopup({
	        delegate: '.djev_media_link', // the selector for gallery item
	        type: 'image',
	        mainClass: 'mfp-img-mobile',
	        gallery: {
	          enabled: true
	        },
			image: {
				verticalFit: true,
				titleSrc: 'data-title'
			},
			iframe: {
				patterns: {
					youtube: null,
					vimeo: null,
					link: {
						index: '/',
						src: '%id%'
					}
				}
			}
	    });
	});
});

}(jQuery);