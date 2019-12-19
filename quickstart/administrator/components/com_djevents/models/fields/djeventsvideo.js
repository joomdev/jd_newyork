/**
 * @version $Id$
 * @package DJ-Events
 * @subpackage DJ-Events galleryGrid layout
 * @copyright Copyright (C) 2017  DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 */

function parseVideo(video_id, image, title, callback) {
				
	var videoField = jQuery('#'+video_id);
	var imageField = image.length ? jQuery('#'+image) : null;
	var titleField = title.length ? jQuery('#'+title) : null;
	var preview = jQuery('#'+videoField.attr('id')+'_preview');
	var loader = jQuery('<span class="djev-ajax-loader"></span>');
	videoField.after(loader);
	videoField.trigger('blur');
	preview.empty();
	videoField.prop('readonly', true);
	
	jQuery.ajax('index.php?option=com_djevents&tmpl=component', {
		method: 'post',
		data: 'task=getvideo&video='+encodeURIComponent(videoField.val())
	}).done(function(response){

		if(response) {
			var video = jQuery.parseJSON(response);
			//console.log(video);
			if(!video.error){
				
				// if callback function is set pass the video object, otherwise do default action
				if(callback) {
					callback(video, video_id);
				} else {
					
					videoField.val(video.embed);
					// put video preview
					
					preview.after(jQuery('<ifarme src="'+video.embed.replace('autoplay=1','')+'" height="180" width="320" frameborder="0" allowfullscreen></iframe>'));
										
					if(titleField.length && (!titleField.val() || confirm(COM_DJEVENTS_CONFIRM_UPDATE_TITLE_FIELD))) {
						titleField.val(video.title);
					}
					if(imageField.length && (!imageField.val() || confirm(COM_DJEVENTS_CONFIRM_UPDATE_IMAGE_FIELD))) {
						imageField.val(video.thumbnail);
						// set thumbnail preview
						preview.after(jQuery('<img src="'+video.thumbnail+'" style="height: 180px;" alt="" />'));
					}
				}
			} else {
				videoField.val('');
				var msg = jQuery('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>'
						+ video.error +'</div>');
				preview.after(jQuery(msg));
        		//alert(video.error);
        	}
		}
		
	}).fail(function( jqXHR, textStatus ) {
		  alert( "Request failed: " + textStatus );
	}).always(function(){
		loader.remove();
		videoField.prop('readonly', false);
	});
	
	return false;
	
}