/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
(function(){var a=this.RokPad={init:function(){a.fixOverflow();},fixOverflow:function(){var c=document.getElement(".rokpad-break"),b;if(c){b=c.getParent(".pane-slider");
}if(b){b.setStyle("overflow","visible");}}};window.addEvent("load",a.init);})();