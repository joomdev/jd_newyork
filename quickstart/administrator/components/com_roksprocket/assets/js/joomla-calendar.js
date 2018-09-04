/*!
 * @version   $Id: joomla-calendar.js 10889 2013-05-30 07:48:35Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2018 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
((function(){

	window.addEvent('domready', function(){
		if (typeof Calendar != 'undefined' && typeof Calendar.prototype.showAtElement != 'undefined'){
			Calendar.prototype.showAtElement = function (el, opts) {
				var self = this;

				var p = Calendar.getAbsolutePos(el),
					IF = document.id(this.params.inputField);

				if (IF){
					p = IF.getPosition();
					this.showAt(p.x, p.y + IF.offsetHeight + 2);
					return true;
				}

				if (!opts || typeof opts != "string") {
					this.showAt(p.x, p.y + el.offsetHeight);
					return true;
				}
				function fixPosition(box) {
					if (box.x < 0)
					box.x = 0;
					if (box.y < 0)
					box.y = 0;
					var cp = document.createElement("div");
					var s = cp.style;
					s.position = "absolute";
					s.right = s.bottom = s.width = s.height = "0px";
					document.body.appendChild(cp);
					var br = Calendar.getAbsolutePos(cp);
					document.body.removeChild(cp);
					if (Calendar.is_ie) {
						br.y += document.body.scrollTop;
						br.x += document.body.scrollLeft;
					} else {
						br.y += window.scrollY;
						br.x += window.scrollX;
					}
					var tmp = box.x + box.width - br.x;
					if (tmp > 0) box.x -= tmp;
					tmp = box.y + box.height - br.y;
					if (tmp > 0) box.y -= tmp;
				}
				this.element.style.display = "block";
				Calendar.continuation_for_the_khtml_browser = function() {
					var w = self.element.offsetWidth;
					var h = self.element.offsetHeight;
					self.element.style.display = "none";
					var valign = opts.substr(0, 1);
					var halign = "l";
					if (opts.length > 1) {
						halign = opts.substr(1, 1);
					}
					// vertical alignment
					switch (valign) {
						case "T": p.y -= h; break;
						case "B": p.y += el.offsetHeight; break;
						case "C": p.y += (el.offsetHeight - h) / 2; break;
						case "t": p.y += el.offsetHeight - h; break;
						case "b": break; // already there
					}
					// horizontal alignment
					switch (halign) {
						case "L": p.x -= w; break;
						case "R": p.x += el.offsetWidth; break;
						case "C": p.x += (el.offsetWidth - w) / 2; break;
						case "l": p.x += el.offsetWidth - w; break;
						case "r": break; // already there
					}
					p.width = w;
					p.height = h + 40;
					self.monthsCombo.style.display = "none";
					fixPosition(p);
					self.showAt(p.x, p.y);
				};
				if (Calendar.is_khtml)
					setTimeout("Calendar.continuation_for_the_khtml_browser()", 10);
				else
					Calendar.continuation_for_the_khtml_browser();
			};
		}
	});
})());
