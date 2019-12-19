var lightboxopen    =   false;
var besley_lightbox = function () {
    if(jQuery('.tplBesley').length > 0) {
        jQuery('.tplBesley .popup-item').on('click', function(event) {
            event.preventDefault();
            var $pic        = jQuery('.tplBesley');
            var $clickid    = jQuery(this).attr('data-id');
            var $index      = 0;

            var getItems = function() {
                var items = [],
                    $el = '';
                $el = $pic.find('.TzArticleMedia');
                $el.each(function() {
                    var thumb       =   jQuery(this).find('.popup-item').attr('data-thumb'),
                        $href       =   jQuery(this).find('.popup-item').attr('href'),
                        $dataid     =   jQuery(this).find('.popup-item').attr('data-id'),
                        $datatype   =   jQuery(this).find('.popup-item').attr('data-type');
                    if ($dataid !== 'undefined' && $dataid !== null) {
                        if ($datatype === 'iframe') {
                            var item = {
                                src     : $href,
                                type    : 'iframe',
                                opts    : {
                                    thumb   : thumb
                                }
                            };
                        } else {
                            var item = {
                                src     : $href,
                                opts    : {
                                    thumb   : thumb
                                }
                            };
                        }

                        items.push(item);
                        if ($clickid === $dataid) $index = items.length-1;
                    }
                });
                return items;
            };

            if (lightboxopen === false) {
                var items       = getItems();
                if (jQuery(window).width()<768) {
                    var instance    = jQuery.fancybox.open(items, {
                        loop : true,
                        thumbs : {
                            autoStart : false
                        },
                        beforeShow: function( instance, slide ) {
                            lightboxopen = true;
                        },
                        afterClose: function( instance, slide ) {
                            lightboxopen = false;
                        }
                    }, $index);
                } else {
                    var instance    = jQuery.fancybox.open(items, {
                        loop : true,
                        thumbs : {
                            autoStart : true
                        },
                        beforeShow: function( instance, slide ) {
                            lightboxopen = true;
                        },
                        afterClose: function( instance, slide ) {
                            lightboxopen = false;
                        }
                    }, $index);
                }
            }
        });
    }
};