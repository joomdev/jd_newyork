/*!
 * @version   $Id: multiselect.js 10889 2013-05-30 07:48:35Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2018 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
 ((function(){

	if (typeof this.RokSprocket == 'undefined') this.RokSprocket = {};

	this.MultiSelect = new Class({

		Implements: [Options, Events],

		initialize: function(options){
			this.setOptions(options);

			this.elements = this.reload();
			this.attach();
		},

		reattach: function(){
			this.elements = this.reload();
			this.attach();
		},

		attach: function(){
			this.elements.each(function(container){
				if (!container.retrieve('tags:field:attached', false)){
					container.store('multiselect:field:attached', true);

					var relay = {
						tags: {
							click: container.retrieve('multiselect:field:click', function(event, element){
								if (event.target.get('data-multiselect-holder') === null) return true;

								container.getElement('[data-multiselect-maininput]').focus();
							}.bind(this)),

							unselect: container.retrieve('multiselect:field:remove', function(event, element){
								this.unselect.call(this, container, element);
							}.bind(this)),

							select: container.retrieve('multiselect:feeds:select', function(event, element){
								this.select.call(this, container, element);
							}.bind(this)),

							mouseenter: container.retrieve('multiselect:feeds:mouseenter', function(event, element){
								this.mouseenter.call(this, container, element);
							}.bind(this)),

							keydown: container.retrieve('multiselect:feeds:keydown', function(event, element){
								this.keydown.call(this, event, container, element);
							}.bind(this)),

							keyup: container.retrieve('multiselect:feeds:keyup', function(event, element){
								this.keyup.call(this, event, container, element);
							}.bind(this))
						},
						feeds: {
							mouseenter: container.retrieve('multiselect:feeds:mouseenter', function(event, element){
								this.refresh.call(this, container);
							}.bind(this)),

							focus: container.retrieve('multiselect:feeds:focus', function(event, element){
								this.focus.call(this, container, element);
							}.bind(this)),

							blur: container.retrieve('multiselect:feeds:blur', function(event, element){
								this.blur.delay(100, this, container, element);
							}.bind(this))
						}
					};

					container.addEvents({
						'click:relay([data-multiselect-holder])': relay.tags.click,
						'click:relay([data-multiselect-value])': relay.tags.select,
						'mouseenter:relay([data-multiselect-value])': relay.tags.mouseenter,
						'click:relay([data-multiselect-remove])': relay.tags.unselect,
						'mouseenter': relay.feeds.mouseenter,
						'keydown:relay([data-multiselect-maininput])': relay.tags.keydown,
						'keyup:relay([data-multiselect-maininput])': relay.tags.keyup,
						'focus:relay([data-multiselect-maininput])': relay.feeds.focus,
						'blur:relay([data-multiselect-maininput])': relay.feeds.blur
					});

					this.maininput = new ResizableTextbox(container.getElement('[data-multiselect-maininput]'), {min: 1, max: 500, step: 10});
				}
			}, this);
		},

		focus: function(container, element){
			var feeds = container.getElement('[data-multiselect-feeds]');

			this.refresh(container, element ? element.get('value') : null);
			container.addClass('multiselect-showing-feeds');
			feeds.setStyle('display', 'block');
		},

		blur: function(container, element){
			var feeds = container.getElement('[data-multiselect-feeds]');

			container.removeClass('multiselect-showing-feeds');
			feeds.setStyle('display', 'none');
		},

		keydown: function(event, container, element){
			var feeds = container.getElement('[data-multiselect-feed]'),
				focus = feeds.getElement('[data-multiselect-value].hover'),
				newActive;

			switch(event.key){
				case 'down':
					newActive = focus.getNext();
					if (newActive) this.mouseenter(container, newActive);
					break;
				case 'up':
					newActive = focus.getPrevious();
					if (newActive) this.mouseenter(container, focus.getPrevious());
					break;
				case 'enter':
					newActive = feeds.getElement('[data-multiselect-value].hover');
					if (newActive){
						this.select(container, feeds.getElement('[data-multiselect-value].hover'));
					}
					break;
				default:

			}
		},

		keyup: function(event, container, element){
			if (event.key != 'up' && event.key != 'down' && event.key != 'enter') this.refresh(container, element.get('value'));
			else if (event.key == 'enter') this.focus(container);
		},

		mouseenter: function(container, element){
			if (!element) return;

			element.getSiblings().removeClass('hover').removeClass('last-item');
			element.addClass('hover');
		},

		select: function(container, element){
			var select = container.getElement('[data-multiselect-select]'),
				value = element.get('data-multiselect-value'),
				text = element.get('text').clean(),
				box = new Element('li.multiselect-box[data-multiselect-box='+value+']', {
					'html': '<span class="multiselect-title">'+text+'</span><span class="multiselect-remove" data-multiselect-remove>&times;</span>',
					'style': {opacity: 0, 'visibility': 'hidden'}
				});

			select.getElement('option[value='+value+']').set('selected', 'selected');
			container.getElement('[data-multiselect-maininput]').set('value', '');
			box.inject(container.getElement('[data-multiselect-holder] .main-input'), 'before').set('tween', {duration: 200}).fade('in');
			this.focus(container);
		},

		unselect: function(container, element){
			var select = container.getElement('[data-multiselect-select]'),
			box = element.getParent('[data-multiselect-box]'),
			value = box.get('data-multiselect-box');

			select.getElement('option[value='+value+']').set('selected', null);
			box.set('tween', {duration: 200, onComplete: function(){ box.dispose(); }}).fade('out');
		},

		refresh: function(container, highlight){
			var options = container.getElements('[data-multiselect-select] option').filter(function(opt){ return !opt.get('selected'); }),
				feeds = container.getElement('[data-multiselect-feed]'),
				feedsList = [], text, elements;

			options.each(function(option, i){
				text = this.highlight(option.get('text'), highlight);
				feedsList.push(new Element('li[data-multiselect-value='+option.get('value')+']').set('html', text));
			}, this);

			feedsList = new Elements(feedsList);

			feeds.empty().adopt(feedsList.setStyle('display', 'block')).setStyle('width', container.getElement('[data-multiselect-holder]').offsetWidth - 2);

			elements = feedsList.filter(function(feed) { return !feed.get('text').test(highlight || '', 'i'); });
			if (elements.length) elements.setStyle('display', 'none');

			elements = feedsList.filter(function(feed) { return feed.getStyle('display') != 'none'; });
			if (elements.length){
				elements[0].addClass('hover');
				elements[elements.length - 1].addClass('last-item');
			}
		},

		highlight: function(html, highlight) {
			return html.replace(new RegExp(highlight, 'gi'), function(match) {
				return '<em>' + match + '</em>';
			});
		},

		reload: function(assign){
			if (!assign) return document.getElements('[data-multiselect]');

			this.elements = document.getElements('[data-multiselect]');
			return this.elements;
		}

	});

	window.addEvent('domready', function(){
		this.RokSprocket.multiselect = new MultiSelect();
	});

})());
