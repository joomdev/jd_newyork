/**
 * @package DJ-Events
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 */
(function($){
	
	var ModDJEventsCalendarMonth = function(element){
		var self = this;
		
		self.container = jQuery(element);

		self.tablewrap = self.container.find('.djev_calendar_wrap').first();
		self.loader = self.container.find('.djev_loader').first();
		
		self.responseContainer = $('<div style="display: none; visibility: hidden;" />');
		self.responseContainer.appendTo(self.container);
		
		self.days = self.tablewrap.find('.active-day a');

		self.tip_position = self.container.attr('data-tooltip-position') || 'top';
		self.tip_open = self.container.attr('data-tooltip-open') || 'mouseenter';
		self.tip_close = self.container.attr('data-tooltip-close') || 'click';

		self.attachDaysEvents(self.days);
		
		self.container.find('.next-month').on('click', function(e){
			e.preventDefault();
			self.sendXhr(1, self.tablewrap.attr('data-month'));
		});
		
		self.container.find('.prev-month').on('click', function(e){
			e.preventDefault();
			self.sendXhr(-1, self.tablewrap.attr('data-month'));
		});
	};
	
	ModDJEventsCalendarMonth.prototype.xhr = null;
	
	ModDJEventsCalendarMonth.prototype.sendXhr = function(direction, lastDate) {
		var self = this;
		
		if (self.xhr && self.xhr.readyState != 4) {
			return;
		}
		
		self.loader.css('display', 'block');
		
		var url = self.container.attr('data-baseurl');
		var mod = self.container.attr('data-module');
		
		self.xhr = $.ajax({
			url: url,
			method: 'post',
			data: 'option=com_djevents&task=getMonth&format=raw&last_date=' + lastDate + '&direction=' + direction + '&module=' + mod
		}).done(function(response) {
			if (response != '') {
				self.setMonth(response);
			}
		}).always(function() {
			self.loader.css('display', 'none');
		});
	};
	
	ModDJEventsCalendarMonth.prototype.setMonth = function(response) {
		var self = this;
		self.responseContainer.html(response);
		var days = self.responseContainer.find('.active-day a');
		self.attachDaysEvents(days);
		
		self.tablewrap.replaceWith(self.responseContainer.find('.djev_calendar_wrap'));
		self.responseContainer.empty();
		self.tablewrap = self.container.find('.djev_calendar_wrap').first();
		
		self.days = self.tablewrap.find('.active-day a');
	};
	
	ModDJEventsCalendarMonth.prototype.attachDaysEvents = function(days) {
		var self = this;
		
		days.popover({animation: false, html: true, placement: self.tip_position, trigger: 'manual', container: 'body'});
		
		//console.log(self.tip_open, self.tip_close, self.tip_position);

		//open tooltip
		if( 'click' == self.tip_open ) {
			days.on('click', function(e){
				e.stopPropagation();
				var day = $(this);
				if( ! day.hasClass('open-day') ) {
					e.preventDefault();
				}
				days.popover('hide').removeClass('open-day');
				day.popover('show').addClass('open-day');
			});
		} else {
			days.on('mouseenter', function(e){
				e.stopPropagation();
				var day = $(this);
				if(day.hasClass('open-day')) return;
				days.popover('hide').removeClass('open-day');
				day.popover('show').addClass('open-day');
			});
			days.on('click focus', function(e){
				e.stopPropagation();
				$(this).trigger('mouseenter');
			});
		}

		//close tooltip
		if( 'click' == self.tip_close ) {
			$(document.body).on('click', function(e){
				if(!$(e.target).parents('.popover').length) {
					days.popover('hide').removeClass('open-day');
				}
			});
		} else {
			self.container.on('mouseleave', function(e){
				e.stopPropagation();
				if( ! days.hasClass('open-day') || $('.popover:hover').length ) return;
				days.popover('hide').removeClass('open-day');
			});
			$(document).on('mouseleave','.popover-content',function(){
				if( ! days.hasClass('open-day') ) return;
				days.popover('hide').removeClass('open-day');
			});
		}

	};

	$(document).ready(function(){
		$('.djev_calendar_month').each(function(){
			new ModDJEventsCalendarMonth(this);
		});
	});
	
})(jQuery);
