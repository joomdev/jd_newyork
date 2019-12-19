/**
 * @package DJ-Events
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 */
(function($){
	
	var ModDJEventsCalendarScroll = function(element){
		var self = this;
		
		self.scroll = jQuery(element);
		self.loader = self.scroll.find('.djev_loader').first();
		
		self.responseContainer = $('<div style="display: none; visibility: hidden;" />');
		self.responseContainer.appendTo(self.scroll);
		
		self.days = self.scroll.find('.active-day');
		self.allDays = self.scroll.find('.djev_calendar_day');
		
		self.dayLimit = self.allDays.length;

		self.tip_position = self.scroll.attr('data-tooltip-position') || 'top';
		self.tip_open = self.scroll.attr('data-tooltip-open') || 'mouseenter';
		self.tip_close = self.scroll.attr('data-tooltip-close') || 'click';
		
		self.attachDaysEvents(self.days);
		
		//self.allDays.on('click', function(e){ e.preventDefault(); });
		
		self.scrolling = self.scroll.find('.djev_calendar_scroll_in').first();
		
		self.scroll.find('.next-days').on('click', function(e){
			e.preventDefault();
			var step = self.scrolling.innerWidth() * 0.8,
				pos = step,
				scrollWidth = self.scrolling.prop('scrollWidth'),
				containerWidth = self.scrolling.width();
			for(var i=0; i < self.allDays.length; i++) {
				var day = jQuery(self.allDays[i]);
				var dPos = day.position().left;
				if(dPos > step) break;
				pos = dPos;
			}
			
			var newScroll = self.scrolling.scrollLeft() + pos;
			
			self.scrolling.animate({scrollLeft: newScroll}, function(){
				if (newScroll + containerWidth > scrollWidth) {
					self.load(1);
				}
			});
		});
		self.scroll.find('.prev-days').on('click', function(e){
			e.preventDefault();
			var step = - self.scrolling.innerWidth() * 0.8,
				pos = step,
				scrollWidth = self.scrolling.prop('scrollWidth'),
				containerWidth = self.scrolling.width();
			
			for(var i=0; i < self.allDays.length; i++) {
				var day = jQuery(self.allDays[i]);
				var dPos = day.position().left;
				if(dPos > step) break;
				pos = dPos;
			}
			var newScroll = self.scrolling.scrollLeft() + pos;
			
			self.scrolling.animate({scrollLeft: newScroll}, function(){
				/*if (newScroll <= pos) {
					//self.load(-1);
				}*/
			});
		});
	};
	
	ModDJEventsCalendarScroll.prototype.attachDaysEvents = function(days) {
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
			self.scroll.on('mouseleave', function(e){
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
	
	ModDJEventsCalendarScroll.prototype.xhr = null;
	
	ModDJEventsCalendarScroll.prototype.sendXhr = function(direction, lastDate) {
		var self = this;
		
		if (self.xhr && self.xhr.readyState != 4) {
			return;
		}
		
		self.loader.css('display', 'block');
		
		var url = self.scroll.attr('data-baseurl');
		var mod = self.scroll.attr('data-module');
		
		self.xhr = $.ajax({
			url: url,
			method: 'post',
			data: 'option=com_djevents&task=getDays&format=raw&last_date=' + lastDate + '&direction=' + direction + '&days=' + self.dayLimit + '&module=' + mod
		}).done(function(response) {
			if (response != '') {
				self.appendDays(direction, response);
				if(direction > 0) {
					self.scroll.find('.next-days').trigger('click');
				} else {
					self.scroll.find('.prev-days').trigger('click');
				}
			}
		}).always(function() {
			self.loader.css('display', 'none');
		});
	};
	
	ModDJEventsCalendarScroll.prototype.load = function(direction){
		
		var self = this;
		
		var lastDate = (direction > 0) ? $(self.allDays[self.allDays.length-1]).attr('data-date') : $(self.allDays[0]).attr('data-date');
		self.sendXhr(direction, lastDate);
	};
	
	ModDJEventsCalendarScroll.prototype.appendDays = function(direction, response) {
		var self = this;
		
		self.responseContainer.html(response);
		var days = self.responseContainer.find('.active-day');
		self.attachDaysEvents(days);
		
		if (direction > 0) {
			self.scrolling.append(self.responseContainer.find('.djev_calendar_day'));
			//self.scroll.find('.next-days').first().trigger('click');
		} else {
			self.scrolling.prepend(self.responseContainer.find('.djev_calendar_day'));
		}
		
		self.responseContainer.empty();
		self.allDays = self.scroll.find('.djev_calendar_day');
		self.days = self.scroll.find('.active-day');
	};
	

	$(document).ready(function(){
		$('.djev_calendar_days').each(function(){
			new ModDJEventsCalendarScroll(this);
		});
	});
	
})(jQuery);
