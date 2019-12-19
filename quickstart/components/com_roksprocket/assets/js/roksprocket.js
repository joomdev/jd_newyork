/*!
 * @version   $Id: roksprocket.js 10889 2013-05-30 07:48:35Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2019 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
((function(){

	var sprocket = {
		instances: {}
	};

	if (typeof this.RokSprocket == 'undefined') this.RokSprocket = sprocket;
	else Object.merge(this.RokSprocket, {instances: {}});


	/* Ugly workaround for data-sets issue for IE < 9 on Moo < 1.4.4 */
	if (MooTools.version < "1.4.4" && (Browser.name == 'ie' && Browser.version < 9)){
		((function(){
			var dataList = [
				'rel', 'data-next', 'data-previous',
				'data-tabs', 'data-tabs-navigation', 'data-tabs-panel', 'data-tabs-next', 'data-tabs-previous',
				'data-lists', 'data-lists-items', 'data-lists-item', 'data-lists-toggler', 'data-lists-content', 'data-lists-page', 'data-lists-next', 'data-lists-previous',
				'data-headlines', 'data-headlines-item', 'data-headlines-next', 'data-headlines-previous',
				'data-features', 'data-features-pagination', 'data-features-next', 'data-features-previous',
				'data-slideshow', 'data-slideshow-pagination', 'data-slideshow-next', 'data-slideshow-previous',
				'data-showcase', 'data-showcase-pagination', 'data-showcase-next', 'data-showcase-previous'
			];

			dataList.each(function(data){
				Element.Properties[data] = {get: function(){ return this.getAttribute(data); }};
			});
		})());
	}

})());
