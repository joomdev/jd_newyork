/*!
 * @version   $Id: resizable-textbox.js 10889 2013-05-30 07:48:35Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2018 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
((function(){
	this.ResizableTextbox = new Class({

		Implements: Options,

		options: {
			min: 1,
			max: 180,
			step: 8
		},

		initialize: function(element, options) {
			this.setOptions(options);
			this.element = document.id(element);
			this.width = this.element.offsetWidth;

			this.element.addEvents({
				'keydown': function(){
					var element = this.element,
						newsize = this.options.step * element.get('value').length;

					if (newsize < 25) newsize = 25;
					if (newsize >= this.options.max) newsize = this.options.max;
					element.setStyle('width', newsize);

				}.bind(this),
				'keyup': function() {
					var element = this.element,
						newsize = this.options.step * element.get('value').length;

					if (newsize <= this.options.min) newsize = this.width;
					if (newsize >= this.options.max) newsize = this.options.max;
					if (!(element.get('value').length == element.retrieve('rt-value') || newsize <= this.options.min || newsize >= this.options.max)){
						element.setStyle('width', newsize);

					}

				}.bind(this)
			});
		},

		toElement: function(){
			return this.element;
		}

	});
})());
