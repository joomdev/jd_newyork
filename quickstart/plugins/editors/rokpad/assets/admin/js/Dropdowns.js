/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
((function(){this.Dropdowns=new Class({Implements:[Options,Events],options:{data:"rokpad"},initialize:function(a){this.selects=document.getElements(".dropdown-original select");
this.setOptions(a);this.bounds={document:this.hideAll.bind(this)};this.attach();},attach:function(a){var b=(a?new Elements([a]).flatten():this.selects);
this.fireEvent("beforeAttach",b);b.each(function(c){var f=c.retrieve(this.options.data+":selects:click",function(g){this.click.call(this,g,c);}.bind(this)),e=c.retrieve(this.options.data+":selects:selection",function(g){this.selection.call(this,g,c);
}.bind(this)),d=c.getParent("."+this.options.data+"-dropdown");if(d){if(typeof jQuery=="undefined"||!jQuery.fn.dropdown){d.addEvent("click",f);}d.getElements(".dropdown-menu > :not([data-divider])").addEvent("click",e);
if(!c.getElement("option[selected]")){this.selection({target:d.getElement("[data-value]")},c);}}},this);if(!document.retrieve(this.options.data+":selects:document",false)){document.addEvent("click",this.bounds.document);
document.store(this.options.data+":selects:document",true);}this.fireEvent("afterAttach",b);return this;},detach:function(a){var b=(a?new Elements([a]).flatten():this.selects);
this.fireEvent("beforeDetach",b);b.each(function(c){var f=c.retrieve(this.options.data+":selects:click"),e=c.retrieve(this.options.data+":selects:selection"),d=c.getParent("."+this.options.data+"-dropdown");
if(d){d.removeEvent("click",f);d.getElements(".dropdown-menu >").removeEvent("click",e);}},this);if(!a){document.store(this.options.data+":selects:document",false).removeEvent("click",this.bounds.document);
}this.fireEvent("afterDetach",b);return this;},click:function(b,a){b.preventDefault();if(a.retrieve(this.options.data+":selects:open",false)){this.hide(a);
}else{this.show(a);}},selection:function(e,a){if(e&&e.preventDefault){e.preventDefault();}if(!e.target){return;}var d=(e.target.get("tag")=="li")?e.target:e.target.getParent("li"),c=d.getParent("."+this.options.data+"-dropdown").getElement("[data-toggle=dropdown]"),g=d.get("data-text"),b=d.get("data-icon"),f=d.get("data-value");
a.fireEvent("beforeChange",[e,a,f,c]);c.getElement("span").set("text",g);if(b&&b.length){c.getElement("i").set("class","icon "+b);}a.set("value",f).fireEvent("change");
a.fireEvent("click");a.fireEvent("afterChange",[e,a,f,c]);this.fireEvent("selection",[e,a,f,c]);},show:function(a){this.hideAll();var b=a.getParent("."+this.options.data+"-dropdown:not(.open)");
a.store(this.options.data+":selects:open",true);this.fireEvent("beforeShow",[a,b]);if(b){b.addClass("open");}this.fireEvent("afterShow",[a,b]);},hide:function(a){var b=a.getParent("."+this.options.data+"-dropdown.open");
a.store(this.options.data+":selects:open",false);this.fireEvent("beforeHide",[a,b]);if(b){b.removeClass("open");}this.fireEvent("afterHide",[a,b]);},hideAll:function(b){if(b){var a=this.selects.getParent("."+this.options.data+"-dropdown");
if(a.contains(b.target)||a.getElement(b.target).clean().length){return true;}}var c=this.selects.getParent("."+this.options.data+"-dropdown.open").clean();
this.selects.store(this.options.data+":selects:open",false);this.fireEvent("beforeHideAll",[this.selects,c]);if(c.length){document.getElements(c).removeClass("open");
}this.fireEvent("afterHideAll",[this.selects,c]);},redraw:function(a){var b=a.getChildren(),c=a.getParent("."+this.options.data+"-dropdown"),e=c.getElement(".dropdown-menu"),f=c.getElement("[data-toggle]").getFirst("span");
e.empty();b.each(function(g){var j=g.get("text"),i=g.get("value"),h;h=new Element("li[data-dynamic=false][data-text="+j+"][data-value="+i+"]").adopt(new Element("a",{href:"#"}).adopt(new Element("span",{text:j})));
e.adopt(h);},this);if(!a.getElement("option[selected]")){if(a.getElements("option").length){a.set("value",a.getFirst().get("value"));}}this.attach(a);var d=e.getElement("[data-value="+a.get("value")+"]");
this.selection({target:d},a);}});})());