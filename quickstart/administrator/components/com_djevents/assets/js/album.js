
/**
 * @version $Id$
 * @package DJ-MediaTools
 * @subpackage DJ-MediaTools galleryGrid layout
 * @copyright Copyright (C) 2017  DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 */

function startUpload(up,files) {

	//up.settings.buttons.start = false;
	up.start();
	//console.log(up);
}
function injectUploaded(up,file,info) {

	var response = jQuery.parseJSON(info.response);
	if(response.error) {
		//console.log(file.status);
		file.status = plupload.FAILED;
		file.name += ' - ' + response.error.message;
		jQuery('#'+file.id).addClass('ui-state-error');
		jQuery('#'+file.id).find('td.plupload_file_name').first().append(' - ' + response.error.message);
		//up.removeFile(file);
		return false;
	}

	var root = jQuery('#albumItemsWrap').attr('data-root');
	var item = jQuery('#albumItemsWrap').find('.albumItem').first().clone();
	item.find('img').first().attr('src', root+'/media/djevents/upload/'+file.target_name);
	item.find('[name="item_poster"]').first().val(file.target_name+';'+file.name);
	item.find('[name="item_image[]"]').first().val(file.target_name+';'+file.name);
	item.find('[name="item_title[]"]').first().val(stripExt(file.name));
	item.find('.video-icon').remove();
	item.css('display','');

	if(jQuery('.albumItem').length == 1) {
		item.find('[name="item_poster"]').first().prop('checked', true);
	}

	initItemEvents(item);
	// add uploaded image to the list and make it sortable
	item.appendTo(window.djalbum);
	up.removeFile(file);

	return true;
}

window.injectAlbumVideo = function injectVideo(video, video_id) {

	var thumb = video.thumbnail.replace(/^administrator\//, '');

	var item = jQuery('#albumItemsWrap').find('.albumItem').first().clone();

	item.find('img').first().attr('src', thumb);
	item.find('[name="item_poster"]').first().val(video.thumbnail+';;'+video.embed);
	item.find('[name="item_image[]"]').first().val(video.thumbnail+';;'+video.embed);
	item.find('[name="item_title[]"]').first().val(video.title);
	item.css('display','');

	initItemEvents(item);
	// add video to the list and make it sortable
	item.appendTo(window.djalbum);

	jQuery('#'+video_id).val('').focus();

	return true;
};

function initItemEvents(item) {

	if(!item.length) return;
	item.find('.delBtn').on('click',function(e){
		e.preventDefault();
		item.fadeOut(200, function(){ item.remove(); });
	});
}

function stripExt(filename) {

	var pattern = /\.[^.]+$/;
	return filename.replace(pattern, "");
}

jQuery(document).ready(function(){

	window.djalbum = jQuery('#albumItems');

	window.djalbum.sortable({
		cursor: 'move',
		items: '.albumItem',
		handle: 'img',
		cancel: 'a,.btn,input'
	});

	window.djalbum.find('.albumItem').each(function(){
		initItemEvents(jQuery(this));
	});
});
