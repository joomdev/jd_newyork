/*
---
provides: moofx
version: 3.1.0
description: A CSS3-enabled javascript animation library
homepage: http://moofx.it
author: Valerio Proietti <@kamicane> (http://mad4milk.net)
license: MIT (http://mootools.net/license.txt)
includes: cubic-bezier by Arian Stolwijk (https://github.com/arian/cubic-bezier)
...
*/

(function(modules) {
    var cache = {}, require = function(id) {
        var module = cache[id];
        if (!module) {
            module = cache[id] = {};
            var exports = module.exports = {};
            modules[id].call(exports, require, module, exports, window);
        }
        return module.exports;
    };
    window["moofx"] = require("0");
})({
    "0": function(require, module, exports, global) {
        /*          .-   3
.-.-..-..-.-|-._.
' ' '`-'`-' ' ' '
*/
                "use strict";

        // color and timer
        var color = require("1"), frame = require("2");

        // if we're in a browser we need ./browser, otherwise ./fx
        var moofx = typeof document !== "undefined" ? require("7") : require("b");

        moofx.requestFrame = function(callback) {
            frame.request(callback);
            return this;
        };

        moofx.cancelFrame = function(callback) {
            frame.cancel(callback);
            return this;
        };

        moofx.color = color;

        // and export moofx
        module.exports = moofx;
    },
    "1": function(require, module, exports, global) {
        /*
color
*/
                "use strict";

        var colors = {
            maroon: "#800000",
            red: "#ff0000",
            orange: "#ffA500",
            yellow: "#ffff00",
            olive: "#808000",
            purple: "#800080",
            fuchsia: "#ff00ff",
            white: "#ffffff",
            lime: "#00ff00",
            green: "#008000",
            navy: "#000080",
            blue: "#0000ff",
            aqua: "#00ffff",
            teal: "#008080",
            black: "#000000",
            silver: "#c0c0c0",
            gray: "#808080",
            transparent: "#0000"
        };

        var RGBtoRGB = function(r, g, b, a) {
            if (a == null || a === "") a = 1;
            r = parseFloat(r);
            g = parseFloat(g);
            b = parseFloat(b);
            a = parseFloat(a);
            if (!(r <= 255 && r >= 0 && g <= 255 && g >= 0 && b <= 255 && b >= 0 && a <= 1 && a >= 0)) return null;
            return [ Math.round(r), Math.round(g), Math.round(b), a ];
        };

        var HEXtoRGB = function(hex) {
            if (hex.length === 3) hex += "f";
            if (hex.length === 4) {
                var h0 = hex.charAt(0), h1 = hex.charAt(1), h2 = hex.charAt(2), h3 = hex.charAt(3);
                hex = h0 + h0 + h1 + h1 + h2 + h2 + h3 + h3;
            }
            if (hex.length === 6) hex += "ff";
            var rgb = [];
            for (var i = 0, l = hex.length; i < l; i += 2) rgb.push(parseInt(hex.substr(i, 2), 16) / (i === 6 ? 255 : 1));
            return rgb;
        };

        // HSL to RGB conversion from:
        // http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript
        // thank you!
        var HUEtoRGB = function(p, q, t) {
            if (t < 0) t += 1;
            if (t > 1) t -= 1;
            if (t < 1 / 6) return p + (q - p) * 6 * t;
            if (t < 1 / 2) return q;
            if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
            return p;
        };

        var HSLtoRGB = function(h, s, l, a) {
            var r, b, g;
            if (a == null || a === "") a = 1;
            h = parseFloat(h) / 360;
            s = parseFloat(s) / 100;
            l = parseFloat(l) / 100;
            a = parseFloat(a) / 1;
            if (h > 1 || h < 0 || s > 1 || s < 0 || l > 1 || l < 0 || a > 1 || a < 0) return null;
            if (s === 0) {
                r = b = g = l;
            } else {
                var q = l < .5 ? l * (1 + s) : l + s - l * s;
                var p = 2 * l - q;
                r = HUEtoRGB(p, q, h + 1 / 3);
                g = HUEtoRGB(p, q, h);
                b = HUEtoRGB(p, q, h - 1 / 3);
            }
            return [ r * 255, g * 255, b * 255, a ];
        };

        var keys = [];

        for (var c in colors) keys.push(c);

        var shex = "(?:#([a-f0-9]{3,8}))", sval = "\\s*([.\\d%]+)\\s*", sop = "(?:,\\s*([.\\d]+)\\s*)?", slist = "\\(" + [ sval, sval, sval ] + sop + "\\)", srgb = "(?:rgb)a?", shsl = "(?:hsl)a?", skeys = "(" + keys.join("|") + ")";

        var xhex = RegExp(shex, "i"), xrgb = RegExp(srgb + slist, "i"), xhsl = RegExp(shsl + slist, "i");

        var color = function(input, array) {
            if (input == null) return null;
            input = (input + "").replace(/\s+/, "");
            var match = colors[input];
            if (match) {
                return color(match, array);
            } else if (match = input.match(xhex)) {
                input = HEXtoRGB(match[1]);
            } else if (match = input.match(xrgb)) {
                input = match.slice(1);
            } else if (match = input.match(xhsl)) {
                input = HSLtoRGB.apply(null, match.slice(1));
            } else return null;
            if (!(input && (input = RGBtoRGB.apply(null, input)))) return null;
            if (array) return input;
            if (input[3] === 1) input.splice(3, 1);
            return "rgb" + (input.length === 4 ? "a" : "") + "(" + input + ")";
        };

        color.x = RegExp([ skeys, shex, srgb + slist, shsl + slist ].join("|"), "gi");

        module.exports = color;
    },
    "2": function(require, module, exports, global) {
        /*
requestFrame / cancelFrame
*/
                "use strict";

        var array = require("3");

        var requestFrame = global.requestAnimationFrame || global.webkitRequestAnimationFrame || global.mozRequestAnimationFrame || global.oRequestAnimationFrame || global.msRequestAnimationFrame || function(callback) {
            return setTimeout(callback, 1e3 / 60);
        };

        var callbacks = [];

        var iterator = function(time) {
            var split = callbacks.splice(0, callbacks.length);
            for (var i = 0, l = split.length; i < l; i++) split[i](time || (time = +new Date()));
        };

        var cancel = function(callback) {
            var io = array.indexOf(callbacks, callback);
            if (io > -1) callbacks.splice(io, 1);
        };

        var request = function(callback) {
            var i = callbacks.push(callback);
            if (i === 1) requestFrame(iterator);
            return function() {
                cancel(callback);
            };
        };

        exports.request = request;

        exports.cancel = cancel;
    },
    "3": function(require, module, exports, global) {
        /*
array
 - array es5 shell
*/
                "use strict";

        var array = require("4")["array"];

        var names = ("pop,push,reverse,shift,sort,splice,unshift,concat,join,slice,toString,indexOf,lastIndexOf,forEach,every,some" + ",filter,map,reduce,reduceRight").split(",");

        for (var methods = {}, i = 0, name, method; name = names[i++]; ) if (method = Array.prototype[name]) methods[name] = method;

        if (!methods.filter) methods.filter = function(fn, context) {
            var results = [];
            for (var i = 0, l = this.length >>> 0; i < l; i++) if (i in this) {
                var value = this[i];
                if (fn.call(context, value, i, this)) results.push(value);
            }
            return results;
        };

        if (!methods.indexOf) methods.indexOf = function(item, from) {
            for (var l = this.length >>> 0, i = from < 0 ? Math.max(0, l + from) : from || 0; i < l; i++) {
                if (i in this && this[i] === item) return i;
            }
            return -1;
        };

        if (!methods.map) methods.map = function(fn, context) {
            var length = this.length >>> 0, results = Array(length);
            for (var i = 0, l = length; i < l; i++) {
                if (i in this) results[i] = fn.call(context, this[i], i, this);
            }
            return results;
        };

        if (!methods.every) methods.every = function(fn, context) {
            for (var i = 0, l = this.length >>> 0; i < l; i++) {
                if (i in this && !fn.call(context, this[i], i, this)) return false;
            }
            return true;
        };

        if (!methods.some) methods.some = function(fn, context) {
            for (var i = 0, l = this.length >>> 0; i < l; i++) {
                if (i in this && fn.call(context, this[i], i, this)) return true;
            }
            return false;
        };

        if (!methods.forEach) methods.forEach = function(fn, context) {
            for (var i = 0, l = this.length >>> 0; i < l; i++) {
                if (i in this) fn.call(context, this[i], i, this);
            }
        };

        var toString = Object.prototype.toString;

        array.isArray = Array.isArray || function(self) {
            return toString.call(self) === "[object Array]";
        };

        module.exports = array.implement(methods);
    },
    "4": function(require, module, exports, global) {
        /*
shell
*/
                "use strict";

        var prime = require("5"), type = require("6");

        var slice = Array.prototype.slice;

        var ghost = prime({
            constructor: function ghost(self) {
                this.valueOf = function() {
                    return self;
                };
                this.toString = function() {
                    return self + "";
                };
                this.is = function(object) {
                    return self === object;
                };
            }
        });

        var shell = function(self) {
            if (self == null || self instanceof ghost) return self;
            var g = shell[type(self)];
            return g ? new g(self) : self;
        };

        var register = function() {
            var g = prime({
                inherits: ghost
            });
            return prime({
                constructor: function(self) {
                    return new g(self);
                },
                define: function(key, descriptor) {
                    var method = descriptor.value;
                    this[key] = function(self) {
                        return arguments.length > 1 ? method.apply(self, slice.call(arguments, 1)) : method.call(self);
                    };
                    g.prototype[key] = function() {
                        return shell(method.apply(this.valueOf(), arguments));
                    };
                    prime.define(this.prototype, key, descriptor);
                    return this;
                }
            });
        };

        for (var types = "string,number,array,object,date,function,regexp".split(","), i = types.length; i--; ) shell[types[i]] = register();

        module.exports = shell;
    },
    "5": function(require, module, exports, global) {
        /*
prime
 - prototypal inheritance
*/
                "use strict";

        var has = function(self, key) {
            return Object.hasOwnProperty.call(self, key);
        };

        var each = function(object, method, context) {
            for (var key in object) if (method.call(context, object[key], key, object) === false) break;
            return object;
        };

        if (!{
            valueOf: 0
        }.propertyIsEnumerable("valueOf")) {
            // fix for stupid IE enumeration bug
            var buggy = "constructor,toString,valueOf,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString".split(",");
            var proto = Object.prototype;
            each = function(object, method, context) {
                for (var key in object) if (method.call(context, object[key], key, object) === false) return object;
                for (var i = 0; key = buggy[i]; i++) {
                    var value = object[key];
                    if ((value !== proto[key] || has(object, key)) && method.call(context, value, key, object) === false) break;
                }
                return object;
            };
        }

        var create = Object.create || function(self) {
            var constructor = function() {};
            constructor.prototype = self;
            return new constructor();
        };

        var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

        var define = Object.defineProperty;

        try {
            var obj = {
                a: 1
            };
            getOwnPropertyDescriptor(obj, "a");
            define(obj, "a", {
                value: 2
            });
        } catch (e) {
            getOwnPropertyDescriptor = function(object, key) {
                return {
                    value: object[key]
                };
            };
            define = function(object, key, descriptor) {
                object[key] = descriptor.value;
                return object;
            };
        }

        var implement = function(proto) {
            each(proto, function(value, key) {
                if (key !== "constructor" && key !== "define" && key !== "inherits") this.define(key, getOwnPropertyDescriptor(proto, key) || {
                    writable: true,
                    enumerable: true,
                    configurable: true,
                    value: value
                });
            }, this);
            return this;
        };

        var prime = function(proto) {
            var superprime = proto.inherits;
            // if our nice proto object has no own constructor property
            // then we proceed using a ghosting constructor that all it does is
            // call the parent's constructor if it has a superprime, else an empty constructor
            // proto.constructor becomes the effective constructor
            var constructor = has(proto, "constructor") ? proto.constructor : superprime ? function() {
                return superprime.apply(this, arguments);
            } : function() {};
            if (superprime) {
                var superproto = superprime.prototype;
                // inherit from superprime
                var cproto = constructor.prototype = create(superproto);
                // setting constructor.parent to superprime.prototype
                // because it's the shortest possible absolute reference
                constructor.parent = superproto;
                cproto.constructor = constructor;
            }
            // inherit (kindof inherit) define
            constructor.define = proto.define || superprime && superprime.define || function(key, descriptor) {
                define(this.prototype, key, descriptor);
                return this;
            };
            // copy implement (this should never change)
            constructor.implement = implement;
            // finally implement proto and return constructor
            return constructor.implement(proto);
        };

        prime.has = has;

        prime.each = each;

        prime.create = create;

        prime.define = define;

        module.exports = prime;
    },
    "6": function(require, module, exports, global) {
        /*
type
*/
                "use strict";

        var toString = Object.prototype.toString, types = /number|object|array|string|function|date|regexp|boolean/;

        var type = function(object) {
            if (object == null) return "null";
            var string = toString.call(object).slice(8, -1).toLowerCase();
            if (string === "number" && isNaN(object)) return "null";
            if (types.test(string)) return string;
            return "object";
        };

        module.exports = type;
    },
    "7": function(require, module, exports, global) {
        /*
MooFx
*/
                "use strict";

        // requires
        var color = require("1"), frame = require("2");

        var cancelFrame = frame.cancel, requestFrame = frame.request;

        var prime = require("5"), array = require("3"), string = require("8");

        var camelize = string.camelize, clean = string.clean, capitalize = string.capitalize;

        var map = array.map, forEach = array.forEach, indexOf = array.indexOf;

        var elements = require("a");

        var fx = require("b");

        // util
        var hyphenated = {};

        var hyphenate = function(self) {
            return hyphenated[self] || (hyphenated[self] = string.hyphenate(self));
        };

        var round = function(n) {
            return Math.round(n * 1e3) / 1e3;
        };

        // compute > node > property
        var compute = global.getComputedStyle ? function(node) {
            var cts = getComputedStyle(node);
            return function(property) {
                return cts ? cts.getPropertyValue(hyphenate(property)) : "";
            };
        } : /*(css3)?*/ function(node) {
            var cts = node.currentStyle;
            return function(property) {
                return cts ? cts[camelize(property)] : "";
            };
        };

        /*:null*/
        // pixel ratio retriever
        var test = document.createElement("div");

        var cssText = "border:none;margin:none;padding:none;visibility:hidden;position:absolute;height:0;";

        // returns the amount of pixels that takes to make one of the unit
        var pixelRatio = function(element, u) {
            var parent = element.parentNode, ratio = 1;
            if (parent) {
                test.style.cssText = cssText + ("width:100" + u + ";");
                parent.appendChild(test);
                ratio = test.offsetWidth / 100;
                parent.removeChild(test);
            }
            return ratio;
        };

        // mirror 4 values
        var mirror4 = function(values) {
            var length = values.length;
            if (length === 1) values.push(values[0], values[0], values[0]); else if (length === 2) values.push(values[0], values[1]); else if (length === 3) values.push(values[1]);
            return values;
        };

        // regular expressions strings
        var sLength = "([-.\\d]+)(%|cm|mm|in|px|pt|pc|em|ex|ch|rem|vw|vh|vm)", sLengthNum = sLength + "?", sBorderStyle = "none|hidden|dotted|dashed|solid|double|groove|ridge|inset|outset|inherit";

        // regular expressions
        var rgLength = RegExp(sLength, "g"), rLengthNum = RegExp(sLengthNum), rgLengthNum = RegExp(sLengthNum, "g"), rBorderStyle = RegExp(sBorderStyle);

        // normalize > css
        var parseString = function(value) {
            return value == null ? "" : value + "";
        };

        var parseOpacity = function(value, normalize) {
            if (value == null || value === "") return normalize ? "1" : "";
            return isFinite(value = +value) ? value < 0 ? "0" : value + "" : "1";
        };

        try {
            test.style.color = "rgba(0,0,0,0.5)";
        } catch (e) {}

        var rgba = /^rgba/.test(test.style.color);

        var parseColor = function(value, normalize) {
            var black = "rgba(0,0,0,1)", c;
            if (!value || !(c = color(value, true))) return normalize ? black : "";
            if (normalize) return "rgba(" + c + ")";
            var alpha = c[3];
            if (alpha === 0) return "transparent";
            return !rgba || alpha === 1 ? "rgb(" + c.slice(0, 3) + ")" : "rgba(" + c + ")";
        };

        var parseLength = function(value, normalize) {
            if (value == null || value === "") return normalize ? "0px" : "";
            var match = string.match(value, rLengthNum);
            return match ? match[1] + (match[2] || "px") : value;
        };

        var parseBorderStyle = function(value, normalize) {
            if (value == null || value === "") return normalize ? "none" : "";
            var match = value.match(rBorderStyle);
            return match ? value : normalize ? "none" : "";
        };

        var parseBorder = function(value, normalize) {
            var normalized = "0px none rgba(0,0,0,1)";
            if (value == null || value === "") return normalize ? normalized : "";
            if (value === 0 || value === "none") return normalize ? normalized : value + "";
            var c;
            value = value.replace(color.x, function(match) {
                c = match;
                return "";
            });
            var s = value.match(rBorderStyle), l = value.match(rgLengthNum);
            return clean([ parseLength(l ? l[0] : "", normalize), parseBorderStyle(s ? s[0] : "", normalize), parseColor(c, normalize) ].join(" "));
        };

        var parseShort4 = function(value, normalize) {
            if (value == null || value === "") return normalize ? "0px 0px 0px 0px" : "";
            return clean(mirror4(map(clean(value).split(" "), function(v) {
                return parseLength(v, normalize);
            })).join(" "));
        };

        var parseShadow = function(value, normalize, len) {
            var transparent = "rgba(0,0,0,0)", normalized = len === 3 ? transparent + " 0px 0px 0px" : transparent + " 0px 0px 0px 0px";
            if (value == null || value === "") return normalize ? normalized : "";
            if (value === "none") return normalize ? normalized : value;
            var colors = [], value = clean(value).replace(color.x, function(match) {
                colors.push(match);
                return "";
            });
            return map(value.split(","), function(shadow, i) {
                var c = parseColor(colors[i], normalize), inset = /inset/.test(shadow), lengths = shadow.match(rgLengthNum) || [ "0px" ];
                lengths = map(lengths, function(m) {
                    return parseLength(m, normalize);
                });
                while (lengths.length < len) lengths.push("0px");
                var ret = inset ? [ "inset", c ] : [ c ];
                return ret.concat(lengths).join(" ");
            }).join(", ");
        };

        var parse = function(value, normalize) {
            if (value == null || value === "") return "";
            // cant normalize "" || null
            return value.replace(color.x, function(match) {
                return parseColor(match, normalize);
            }).replace(rgLength, function(match) {
                return parseLength(match, normalize);
            });
        };

        // get && set
        var getters = {}, setters = {}, parsers = {}, aliases = {};

        var getter = function(key) {
            return getters[key] || (getters[key] = function() {
                var alias = aliases[key] || key, parser = parsers[key] || parse;
                return function() {
                    return parser(compute(this)(alias), true);
                };
            }());
        };

        var setter = function(key) {
            return setters[key] || (setters[key] = function() {
                var alias = aliases[key] || key, parser = parsers[key] || parse;
                return function(value) {
                    this.style[alias] = parser(value, false);
                };
            }());
        };

        // parsers
        var trbl = [ "Top", "Right", "Bottom", "Left" ], tlbl = [ "TopLeft", "TopRight", "BottomRight", "BottomLeft" ];

        forEach(trbl, function(d) {
            var bd = "border" + d;
            forEach([ "margin" + d, "padding" + d, bd + "Width", d.toLowerCase() ], function(n) {
                parsers[n] = parseLength;
            });
            parsers[bd + "Color"] = parseColor;
            parsers[bd + "Style"] = parseBorderStyle;
            // borderDIR
            parsers[bd] = parseBorder;
            getters[bd] = function() {
                return [ getter(bd + "Width").call(this), getter(bd + "Style").call(this), getter(bd + "Color").call(this) ].join(" ");
            };
        });

        forEach(tlbl, function(d) {
            parsers["border" + d + "Radius"] = parseLength;
        });

        parsers.color = parsers.backgroundColor = parseColor;

        parsers.width = parsers.height = parsers.minWidth = parsers.minHeight = parsers.maxWidth = parsers.maxHeight = parsers.fontSize = parsers.backgroundSize = parseLength;

        // margin + padding
        forEach([ "margin", "padding" ], function(name) {
            parsers[name] = parseShort4;
            getters[name] = function() {
                return map(trbl, function(d) {
                    return getter(name + d).call(this);
                }, this).join(" ");
            };
        });

        // borders
        // borderDIRWidth, borderDIRStyle, borderDIRColor
        parsers.borderWidth = parseShort4;

        parsers.borderStyle = function(value, normalize) {
            if (value == null || value === "") return normalize ? mirror4([ "none" ]).join(" ") : "";
            value = clean(value).split(" ");
            return clean(mirror4(map(value, function(v) {
                parseBorderStyle(v, normalize);
            })).join(" "));
        };

        parsers.borderColor = function(value, normalize) {
            if (!value || !(value = string.match(value, color.x))) return normalize ? mirror4([ "rgba(0,0,0,1)" ]).join(" ") : "";
            return clean(mirror4(map(value, function(v) {
                return parseColor(v, normalize);
            })).join(" "));
        };

        forEach([ "Width", "Style", "Color" ], function(name) {
            getters["border" + name] = function() {
                return map(trbl, function(d) {
                    return getter("border" + d + name).call(this);
                }, this).join(" ");
            };
        });

        // borderRadius
        parsers.borderRadius = parseShort4;

        getters.borderRadius = function() {
            return map(tlbl, function(d) {
                return getter("border" + d + "Radius").call(this);
            }, this).join(" ");
        };

        // border
        parsers.border = parseBorder;

        getters.border = function() {
            var pvalue;
            for (var i = 0; i < trbl.length; i++) {
                var value = getter("border" + trbl[i]).call(this);
                if (pvalue && value !== pvalue) return null;
                pvalue = value;
            }
            return pvalue;
        };

        // zIndex
        parsers.zIndex = parseString;

        // opacity
        parsers.opacity = parseOpacity;

        /*(css3)?*/
        var filterName = test.style.MsFilter != null && "MsFilter" || test.style.filter != null && "filter";

        if (filterName && test.style.opacity == null) {
            var matchOp = /alpha\(opacity=([\d.]+)\)/i;
            setters.opacity = function(value) {
                value = (value = parseOpacity(value)) === "1" ? "" : "alpha(opacity=" + Math.round(value * 100) + ")";
                var filter = compute(this)(filterName);
                return this.style[filterName] = matchOp.test(filter) ? filter.replace(matchOp, value) : filter + " " + value;
            };
            getters.opacity = function() {
                var match = compute(this)(filterName).match(matchOp);
                return (!match ? 1 : match[1] / 100) + "";
            };
        }

        /*:*/
        var parseBoxShadow = parsers.boxShadow = function(value, normalize) {
            return parseShadow(value, normalize, 4);
        };

        var parseTextShadow = parsers.textShadow = function(value, normalize) {
            return parseShadow(value, normalize, 3);
        };

        // Aliases
        forEach([ "Webkit", "Moz", "ms", "O", null ], function(prefix) {
            forEach([ "transition", "transform", "transformOrigin", "transformStyle", "perspective", "perspectiveOrigin", "backfaceVisibility" ], function(style) {
                var cc = prefix ? prefix + capitalize(style) : style;
                if (prefix === "ms") hyphenated[cc] = "-ms-" + hyphenate(style);
                if (test.style[cc] != null) aliases[style] = cc;
            });
        });

        var transitionName = aliases.transition, transformName = aliases.transform;

        // manually disable css3 transitions in Opera, because they do not work properly.
        if (transitionName === "OTransition") transitionName = null;

        // this takes care of matrix decomposition on browsers that support only 2d transforms but no CSS3 transitions.
        // basically, IE9 (and Opera as well, since we disabled CSS3 transitions manually)
        var parseTransform2d, Transform2d;

        /*(css3)?*/
        if (!transitionName && transformName) (function() {
            var unmatrix = require("d");
            var v = "\\s*([-\\d\\w.]+)\\s*";
            var rMatrix = RegExp("matrix\\(" + [ v, v, v, v, v, v ] + "\\)");
            var decomposeMatrix = function(matrix) {
                var d = unmatrix.apply(null, matrix.match(rMatrix).slice(1)) || [ [ 0, 0 ], 0, 0, [ 0, 0 ] ];
                return [ "translate(" + map(d[0], function(v) {
                    return round(v) + "px";
                }) + ")", "rotate(" + round(d[1] * 180 / Math.PI) + "deg)", "skewX(" + round(d[2] * 180 / Math.PI) + "deg)", "scale(" + map(d[3], round) + ")" ].join(" ");
            };
            var def0px = function(value) {
                return value || "0px";
            }, def1 = function(value) {
                return value || "1";
            }, def0deg = function(value) {
                return value || "0deg";
            };
            var transforms = {
                translate: function(value) {
                    if (!value) value = "0px,0px";
                    var values = value.split(",");
                    if (!values[1]) values[1] = "0px";
                    return map(values, clean) + "";
                },
                translateX: def0px,
                translateY: def0px,
                scale: function(value) {
                    if (!value) value = "1,1";
                    var values = value.split(",");
                    if (!values[1]) values[1] = values[0];
                    return map(values, clean) + "";
                },
                scaleX: def1,
                scaleY: def1,
                rotate: def0deg,
                skewX: def0deg,
                skewY: def0deg
            };
            Transform2d = prime({
                constructor: function(transform) {
                    var names = this.names = [];
                    var values = this.values = [];
                    transform.replace(/(\w+)\(([-.\d\s\w,]+)\)/g, function(match, name, value) {
                        names.push(name);
                        values.push(value);
                    });
                },
                identity: function() {
                    var functions = [];
                    forEach(this.names, function(name) {
                        var fn = transforms[name];
                        if (fn) functions.push(name + "(" + fn() + ")");
                    });
                    return functions.join(" ");
                },
                sameType: function(transformObject) {
                    return this.names.toString() === transformObject.names.toString();
                },
                // this is, basically, cheating.
                // retrieving the matrix value from the dom, rather than calculating it
                decompose: function() {
                    var transform = this.toString();
                    test.style.cssText = cssText + hyphenate(transformName) + ":" + transform + ";";
                    document.body.appendChild(test);
                    var m = compute(test)(transformName);
                    if (!m || m === "none") m = "matrix(1, 0, 0, 1, 0, 0)";
                    document.body.removeChild(test);
                    return decomposeMatrix(m);
                }
            });
            Transform2d.prototype.toString = function(clean) {
                var values = this.values, functions = [];
                forEach(this.names, function(name, i) {
                    var fn = transforms[name];
                    if (!fn) return;
                    var value = fn(values[i]);
                    if (!clean || value !== fn()) functions.push(name + "(" + value + ")");
                });
                return functions.length ? functions.join(" ") : "none";
            };
            Transform2d.union = function(from, to) {
                if (from === to) return;
                // nothing to do
                var fromMap, toMap;
                if (from === "none") {
                    toMap = new Transform2d(to);
                    to = toMap.toString();
                    from = toMap.identity();
                    fromMap = new Transform2d(from);
                } else if (to === "none") {
                    fromMap = new Transform2d(from);
                    from = fromMap.toString();
                    to = fromMap.identity();
                    toMap = new Transform2d(to);
                } else {
                    fromMap = new Transform2d(from);
                    from = fromMap.toString();
                    toMap = new Transform2d(to);
                    to = toMap.toString();
                }
                if (from === to) return;
                // nothing to do
                if (!fromMap.sameType(toMap)) {
                    from = fromMap.decompose();
                    to = toMap.decompose();
                }
                if (from === to) return;
                // nothing to do
                return [ from, to ];
            };
            // this parser makes sure it never gets "matrix"
            parseTransform2d = parsers.transform = function(transform) {
                if (!transform || transform === "none") return "none";
                return new Transform2d(rMatrix.test(transform) ? decomposeMatrix(transform) : transform).toString(true);
            };
            // this getter makes sure we read from the dom only the first time
            // this way we save the actual transform and not "matrix"
            // setting matrix() will use parseTransform2d as well, thus setting the decomposed matrix
            getters.transform = function() {
                var s = this.style;
                return s[transformName] || (s[transformName] = parseTransform2d(compute(this)(transformName)));
            };
        })();

        /*:*/
        // tries to match from and to values
        var prepare = function(node, property, to) {
            var parser = parsers[property] || parse, from = getter(property).call(node), // "normalized" by the getter
            to = parser(to, true);
            // normalize parsed property
            if (from === to) return;
            if (parser === parseLength || parser === parseBorder || parser === parseShort4) {
                var toAll = to.match(rgLength), i = 0;
                // this should always match something
                if (toAll) from = from.replace(rgLength, function(fromFull, fromValue, fromUnit) {
                    var toFull = toAll[i++], toMatched = toFull.match(rLengthNum), toUnit = toMatched[2];
                    if (fromUnit !== toUnit) {
                        var fromPixels = fromUnit === "px" ? fromValue : pixelRatio(node, fromUnit) * fromValue;
                        return round(fromPixels / pixelRatio(node, toUnit)) + toUnit;
                    }
                    return fromFull;
                });
                if (i > 0) setter(property).call(node, from);
            } else if (parser === parseTransform2d) {
                // IE9/Opera
                return Transform2d.union(from, to);
            }
            /*:*/
            return from !== to ? [ from, to ] : null;
        };

        // BrowserAnimation
        var BrowserAnimation = prime({
            inherits: fx,
            constructor: function BrowserAnimation(node, property) {
                var _getter = getter(property), _setter = setter(property);
                this.get = function() {
                    return _getter.call(node);
                };
                this.set = function(value) {
                    return _setter.call(node, value);
                };
                BrowserAnimation.parent.constructor.call(this, this.set);
                this.node = node;
                this.property = property;
            }
        });

        var JSAnimation;

        /*(css3)?*/
        JSAnimation = prime({
            inherits: BrowserAnimation,
            constructor: function JSAnimation() {
                return JSAnimation.parent.constructor.apply(this, arguments);
            },
            start: function(to) {
                this.stop();
                if (this.duration === 0) {
                    this.cancel(to);
                    return this;
                }
                var fromTo = prepare(this.node, this.property, to);
                if (!fromTo) {
                    this.cancel(to);
                    return this;
                }
                JSAnimation.parent.start.apply(this, fromTo);
                if (!this.cancelStep) return this;
                // the animation would have started but we need additional checks
                var parser = parsers[this.property] || parse;
                // complex interpolations JSAnimation can't handle
                // even CSS3 animation gracefully fail with some of those edge cases
                // other "simple" properties, such as `border` can have different templates
                // because of string properties like "solid" and "dashed"
                if ((parser === parseBoxShadow || parser === parseTextShadow || parser === parse) && this.templateFrom !== this.templateTo) {
                    this.cancelStep();
                    delete this.cancelStep;
                    this.cancel(to);
                }
                return this;
            },
            parseEquation: function(equation) {
                if (typeof equation === "string") return JSAnimation.parent.parseEquation.call(this, equation);
            }
        });

        /*:*/
        // CSSAnimation
        var remove3 = function(value, a, b, c) {
            var index = indexOf(a, value);
            if (index !== -1) {
                a.splice(index, 1);
                b.splice(index, 1);
                c.splice(index, 1);
            }
        };

        var CSSAnimation = prime({
            inherits: BrowserAnimation,
            constructor: function CSSAnimation(node, property) {
                CSSAnimation.parent.constructor.call(this, node, property);
                this.hproperty = hyphenate(aliases[property] || property);
                var self = this;
                this.bSetTransitionCSS = function(time) {
                    self.setTransitionCSS(time);
                };
                this.bSetStyleCSS = function(time) {
                    self.setStyleCSS(time);
                };
                this.bComplete = function() {
                    self.complete();
                };
            },
            start: function(to) {
                this.stop();
                if (this.duration === 0) {
                    this.cancel(to);
                    return this;
                }
                var fromTo = prepare(this.node, this.property, to);
                if (!fromTo) {
                    this.cancel(to);
                    return this;
                }
                this.to = fromTo[1];
                // setting transition styles immediately will make good browsers behave weirdly
                // because DOM changes are always deferred, so we requestFrame
                this.cancelSetTransitionCSS = requestFrame(this.bSetTransitionCSS);
                return this;
            },
            setTransitionCSS: function(time) {
                delete this.cancelSetTransitionCSS;
                this.resetCSS(true);
                // firefox flickers if we set css for transition as well as styles at the same time
                // so, other than deferring transition styles we defer actual styles as well on a requestFrame
                this.cancelSetStyleCSS = requestFrame(this.bSetStyleCSS);
            },
            setStyleCSS: function(time) {
                delete this.cancelSetStyleCSS;
                var duration = this.duration;
                // we use setTimeout instead of transitionEnd because some browsers (looking at you foxy)
                // incorrectly set event.propertyName, so we cannot check which animation we are canceling
                this.cancelComplete = setTimeout(this.bComplete, duration);
                this.endTime = time + duration;
                this.set(this.to);
            },
            complete: function() {
                delete this.cancelComplete;
                this.resetCSS();
                this.callback(this.endTime);
            },
            stop: function(hard) {
                if (this.cancelExit) {
                    this.cancelExit();
                    delete this.cancelExit;
                } else if (this.cancelSetTransitionCSS) {
                    // if cancelSetTransitionCSS is set, means nothing is set yet
                    this.cancelSetTransitionCSS();
                    //so we cancel and we're good
                    delete this.cancelSetTransitionCSS;
                } else if (this.cancelSetStyleCSS) {
                    // if cancelSetStyleCSS is set, means transition css has been set, but no actual styles.
                    this.cancelSetStyleCSS();
                    delete this.cancelSetStyleCSS;
                    // if its a hard stop (and not another start on top of the current animation)
                    // we need to reset the transition CSS
                    if (hard) this.resetCSS();
                } else if (this.cancelComplete) {
                    // if cancelComplete is set, means style and transition css have been set, not yet completed.
                    clearTimeout(this.cancelComplete);
                    delete this.cancelComplete;
                    // if its a hard stop (and not another start on top of the current animation)
                    // we need to reset the transition CSS set the current animation styles
                    if (hard) {
                        this.resetCSS();
                        this.set(this.get());
                    }
                }
                return this;
            },
            resetCSS: function(inclusive) {
                var rules = compute(this.node), properties = (rules(transitionName + "Property").replace(/\s+/g, "") || "all").split(","), durations = (rules(transitionName + "Duration").replace(/\s+/g, "") || "0s").split(","), equations = (rules(transitionName + "TimingFunction").replace(/\s+/g, "") || "ease").match(/cubic-bezier\([\d-.,]+\)|([a-z-]+)/g);
                remove3("all", properties, durations, equations);
                remove3(this.hproperty, properties, durations, equations);
                if (inclusive) {
                    properties.push(this.hproperty);
                    durations.push(this.duration + "ms");
                    equations.push("cubic-bezier(" + this.equation + ")");
                }
                var nodeStyle = this.node.style;
                nodeStyle[transitionName + "Property"] = properties;
                nodeStyle[transitionName + "Duration"] = durations;
                nodeStyle[transitionName + "TimingFunction"] = equations;
            },
            parseEquation: function(equation) {
                if (typeof equation === "string") return CSSAnimation.parent.parseEquation.call(this, equation, true);
            }
        });

        // elements methods
        var BaseAnimation = transitionName ? CSSAnimation : JSAnimation;

        var moofx = function(x, y) {
            return typeof x === "function" ? fx(x) : elements(x, y);
        };

        elements.implement({
            // {properties}, options or
            // property, value options
            animate: function(A, B, C) {
                var styles = A, options = B;
                if (typeof A === "string") {
                    styles = {};
                    styles[A] = B;
                    options = C;
                }
                if (options == null) options = {};
                var type = typeof options;
                options = type === "function" ? {
                    callback: options
                } : type === "string" || type === "number" ? {
                    duration: options
                } : options;
                var callback = options.callback || function() {}, completed = 0, length = 0;
                options.callback = function(t) {
                    if (++completed === length) callback(t);
                };
                for (var property in styles) {
                    var value = styles[property], property = camelize(property);
                    this.forEach(function(node) {
                        length++;
                        var self = elements(node), anims = self._animations || (self._animations = {});
                        var anim = anims[property] || (anims[property] = new BaseAnimation(node, property));
                        anim.setOptions(options).start(value);
                    });
                }
                return this;
            },
            // {properties} or
            // property, value
            style: function(A, B) {
                var styles = A;
                if (typeof A === "string") {
                    styles = {};
                    styles[A] = B;
                }
                for (var property in styles) {
                    var value = styles[property], set = setter(property = camelize(property));
                    this.forEach(function(node) {
                        var self = elements(node), anims = self._animations, anim;
                        if (anims && (anim = anims[property])) anim.stop(true);
                        set.call(node, value);
                    });
                }
                return this;
            },
            compute: function(property) {
                property = camelize(property);
                var node = this[0];
                // return default matrix for transform, instead of parsed (for consistency)
                if (property === "transform" && parseTransform2d) return compute(node)(transformName);
                var value = getter(property).call(node);
                // unit conversion to `px`
                return value != null ? value.replace(rgLength, function(match, value, unit) {
                    return unit === "px" ? match : pixelRatio(node, unit) * value + "px";
                }) : "";
            }
        });

        moofx.parse = function(property, value, normalize) {
            return (parsers[camelize(property)] || parse)(value, normalize);
        };

        module.exports = moofx;
    },
    "8": function(require, module, exports, global) {
        /*
string methods
 - string shell
*/
                "use strict";

        var string = require("9");

        string.implement({
            clean: function() {
                return string.trim((this + "").replace(/\s+/g, " "));
            },
            camelize: function() {
                return (this + "").replace(/-\D/g, function(match) {
                    return match.charAt(1).toUpperCase();
                });
            },
            hyphenate: function() {
                return (this + "").replace(/[A-Z]/g, function(match) {
                    return "-" + match.toLowerCase();
                });
            },
            capitalize: function() {
                return (this + "").replace(/\b[a-z]/g, function(match) {
                    return match.toUpperCase();
                });
            },
            escape: function() {
                return (this + "").replace(/([-.*+?^${}()|[\]\/\\])/g, "\\$1");
            },
            number: function() {
                return parseFloat(this);
            }
        });

        if (typeof JSON !== "undefined") string.implement({
            decode: function() {
                return JSON.parse(this);
            }
        });

        module.exports = string;
    },
    "9": function(require, module, exports, global) {
        /*
string
 - string es5 shell
*/
                "use strict";

        var string = require("4")["string"];

        var names = ("charAt,charCodeAt,concat,contains,endsWith,indexOf,lastIndexOf,localeCompare,match,replace,search,slice,split" + ",startsWith,substr,substring,toLocaleLowerCase,toLocaleUpperCase,toLowerCase,toString,toUpperCase,trim,valueOf").split(",");

        for (var methods = {}, i = 0, name, method; name = names[i++]; ) if (method = String.prototype[name]) methods[name] = method;

        if (!methods.trim) methods.trim = function() {
            return (this + "").replace(/^\s+|\s+$/g, "");
        };

        module.exports = string.implement(methods);
    },
    a: function(require, module, exports, global) {
        /*
elements
*/
                "use strict";

        var prime = require("5"), array = require("3").prototype;

        // uniqueID
        var uniqueIndex = 0;

        var uniqueID = function(n) {
            return n === global ? "global" : n.uniqueNumber || (n.uniqueNumber = "n:" + (uniqueIndex++).toString(36));
        };

        var instances = {};

        // elements prime
        var $ = prime({
            constructor: function $(n, context) {
                if (n == null) return this && this.constructor === $ ? new elements() : null;
                var self = n;
                if (n.constructor !== elements) {
                    self = new elements();
                    var uid;
                    if (typeof n === "string") {
                        if (!self.search) return null;
                        self[self.length++] = context || document;
                        return self.search(n);
                    }
                    if (n.nodeType || n === global) {
                        self[self.length++] = n;
                    } else if (n.length) {
                        // this could be an array, or any object with a length attribute,
                        // including another instance of elements from another interface.
                        var uniques = {};
                        for (var i = 0, l = n.length; i < l; i++) {
                            // perform elements flattening
                            var nodes = $(n[i], context);
                            if (nodes && nodes.length) for (var j = 0, k = nodes.length; j < k; j++) {
                                var node = nodes[j];
                                uid = uniqueID(node);
                                if (!uniques[uid]) {
                                    self[self.length++] = node;
                                    uniques[uid] = true;
                                }
                            }
                        }
                    }
                }
                if (!self.length) return null;
                // when length is 1 always use the same elements instance
                if (self.length === 1) {
                    uid = uniqueID(self[0]);
                    return instances[uid] || (instances[uid] = self);
                }
                return self;
            }
        });

        var elements = prime({
            inherits: $,
            constructor: function elements() {
                this.length = 0;
            },
            unlink: function() {
                return this.map(function(node, i) {
                    delete instances[uniqueID(node)];
                    return node;
                });
            },
            // straight es5 prototypes (or emulated methods)
            forEach: array.forEach,
            map: array.map,
            filter: array.filter,
            every: array.every,
            some: array.some
        });

        module.exports = $;
    },
    b: function(require, module, exports, global) {
        /*
fx
*/
                "use strict";

        var prime = require("5"), requestFrame = require("2").request, bezier = require("c");

        var map = require("3").map;

        var sDuration = "([\\d.]+)(s|ms)?", sCubicBezier = "cubic-bezier\\(([-.\\d]+),([-.\\d]+),([-.\\d]+),([-.\\d]+)\\)";

        var rDuration = RegExp(sDuration), rCubicBezier = RegExp(sCubicBezier), rgCubicBezier = RegExp(sCubicBezier, "g");

        // equations collection
        var equations = {
            "default": "cubic-bezier(0.25, 0.1, 0.25, 1.0)",
            linear: "cubic-bezier(0, 0, 1, 1)",
            "ease-in": "cubic-bezier(0.42, 0, 1.0, 1.0)",
            "ease-out": "cubic-bezier(0, 0, 0.58, 1.0)",
            "ease-in-out": "cubic-bezier(0.42, 0, 0.58, 1.0)"
        };

        equations.ease = equations["default"];

        var compute = function(from, to, delta) {
            return (to - from) * delta + from;
        };

        var divide = function(string) {
            var numbers = [];
            var template = (string + "").replace(/[-.\d]+/g, function(number) {
                numbers.push(+number);
                return "@";
            });
            return [ numbers, template ];
        };

        var Fx = prime({
            constructor: function Fx(render, options) {
                // set options
                this.setOptions(options);
                // renderer
                this.render = render || function() {};
                // bound functions
                var self = this;
                this.bStep = function(t) {
                    return self.step(t);
                };
                this.bExit = function(time) {
                    self.exit(time);
                };
            },
            setOptions: function(options) {
                if (options == null) options = {};
                if (!(this.duration = this.parseDuration(options.duration || "500ms"))) throw new Error("invalid duration");
                if (!(this.equation = this.parseEquation(options.equation || "default"))) throw new Error("invalid equation");
                this.callback = options.callback || function() {};
                return this;
            },
            parseDuration: function(duration) {
                if (duration = (duration + "").match(rDuration)) {
                    var time = +duration[1], unit = duration[2] || "ms";
                    if (unit === "s") return time * 1e3;
                    if (unit === "ms") return time;
                }
            },
            parseEquation: function(equation, array) {
                var type = typeof equation;
                if (type === "function") {
                    // function
                    return equation;
                } else if (type === "string") {
                    // cubic-bezier string
                    equation = equations[equation] || equation;
                    var match = equation.replace(/\s+/g, "").match(rCubicBezier);
                    if (match) {
                        equation = map(match.slice(1), function(v) {
                            return +v;
                        });
                        if (array) return equation;
                        if (equation.toString() === "0,0,1,1") return function(x) {
                            return x;
                        };
                        type = "object";
                    }
                }
                if (type === "object") {
                    // array
                    return bezier(equation[0], equation[1], equation[2], equation[3], 1e3 / 60 / this.duration / 4);
                }
            },
            cancel: function(to) {
                this.to = to;
                this.cancelExit = requestFrame(this.bExit);
            },
            exit: function(time) {
                this.render(this.to);
                delete this.cancelExit;
                this.callback(time);
            },
            start: function(from, to) {
                this.stop();
                if (this.duration === 0) {
                    this.cancel(to);
                    return this;
                }
                this.isArray = false;
                this.isNumber = false;
                var fromType = typeof from, toType = typeof to;
                if (fromType === "object" && toType === "object") {
                    this.isArray = true;
                } else if (fromType === "number" && toType === "number") {
                    this.isNumber = true;
                }
                var from_ = divide(from), to_ = divide(to);
                this.from = from_[0];
                this.to = to_[0];
                this.templateFrom = from_[1];
                this.templateTo = to_[1];
                if (this.from.length !== this.to.length || this.from.toString() === this.to.toString()) {
                    this.cancel(to);
                    return this;
                }
                delete this.time;
                this.length = this.from.length;
                this.cancelStep = requestFrame(this.bStep);
                return this;
            },
            stop: function() {
                if (this.cancelExit) {
                    this.cancelExit();
                    delete this.cancelExit;
                } else if (this.cancelStep) {
                    this.cancelStep();
                    delete this.cancelStep;
                }
                return this;
            },
            step: function(now) {
                this.time || (this.time = now);
                var factor = (now - this.time) / this.duration;
                if (factor > 1) factor = 1;
                var delta = this.equation(factor), from = this.from, to = this.to, tpl = this.templateTo;
                for (var i = 0, l = this.length; i < l; i++) {
                    var f = from[i], t = to[i];
                    tpl = tpl.replace("@", t !== f ? compute(f, t, delta) : t);
                }
                this.render(this.isArray ? tpl.split(",") : this.isNumber ? +tpl : tpl, factor);
                if (factor !== 1) {
                    this.cancelStep = requestFrame(this.bStep);
                } else {
                    delete this.cancelStep;
                    this.callback(now);
                }
            }
        });

        var fx = function(render) {
            var ffx = new Fx(render);
            return {
                start: function(from, to, options) {
                    var type = typeof options;
                    ffx.setOptions(type === "function" ? {
                        callback: options
                    } : type === "string" || type === "number" ? {
                        duration: options
                    } : options).start(from, to);
                    return this;
                },
                stop: function() {
                    ffx.stop();
                    return this;
                }
            };
        };

        fx.prototype = Fx.prototype;

        module.exports = fx;
    },
    c: function(require, module, exports, global) {
                module.exports = function(x1, y1, x2, y2, epsilon) {
            var curveX = function(t) {
                var v = 1 - t;
                return 3 * v * v * t * x1 + 3 * v * t * t * x2 + t * t * t;
            };
            var curveY = function(t) {
                var v = 1 - t;
                return 3 * v * v * t * y1 + 3 * v * t * t * y2 + t * t * t;
            };
            var derivativeCurveX = function(t) {
                var v = 1 - t;
                return 3 * (2 * (t - 1) * t + v * v) * x1 + 3 * (-t * t * t + 2 * v * t) * x2;
            };
            return function(t) {
                var x = t, t0, t1, t2, x2, d2, i;
                // First try a few iterations of Newton's method -- normally very fast.
                for (t2 = x, i = 0; i < 8; i++) {
                    x2 = curveX(t2) - x;
                    if (Math.abs(x2) < epsilon) return curveY(t2);
                    d2 = derivativeCurveX(t2);
                    if (Math.abs(d2) < 1e-6) break;
                    t2 = t2 - x2 / d2;
                }
                t0 = 0, t1 = 1, t2 = x;
                if (t2 < t0) return curveY(t0);
                if (t2 > t1) return curveY(t1);
                // Fallback to the bisection method for reliability.
                while (t0 < t1) {
                    x2 = curveX(t2);
                    if (Math.abs(x2 - x) < epsilon) return curveY(t2);
                    if (x > x2) t0 = t2; else t1 = t2;
                    t2 = (t1 - t0) * .5 + t0;
                }
                // Failure
                return curveY(t2);
            };
        };
    },
    d: function(require, module, exports, global) {
        /*
Unmatrix 2d
 - a crude implementation of the slightly bugged pseudo code in http://www.w3.org/TR/css3-2d-transforms/#matrix-decomposition
*/
                "use strict";

        // returns the length of the passed vector
        var length = function(a) {
            return Math.sqrt(a[0] * a[0] + a[1] * a[1]);
        };

        // normalizes the length of the passed point to 1
        var normalize = function(a) {
            var l = length(a);
            return l ? [ a[0] / l, a[1] / l ] : [ 0, 0 ];
        };

        // returns the dot product of the passed points
        var dot = function(a, b) {
            return a[0] * b[0] + a[1] * b[1];
        };

        // returns the principal value of the arc tangent of
        // y/x, using the signs of both arguments to determine
        // the quadrant of the return value
        var atan2 = Math.atan2;

        var combine = function(a, b, ascl, bscl) {
            return [ ascl * a[0] + bscl * b[0], ascl * a[1] + bscl * b[1] ];
        };

        module.exports = function(a, b, c, d, tx, ty) {
            // Make sure the matrix is invertible
            if (a * d - b * c === 0) return false;
            // Take care of translation
            var translate = [ tx, ty ];
            // Put the components into a 2x2 matrix
            var m = [ [ a, b ], [ c, d ] ];
            // Compute X scale factor and normalize first row.
            var scale = [ length(m[0]) ];
            m[0] = normalize(m[0]);
            // Compute shear factor and make 2nd row orthogonal to 1st.
            var skew = dot(m[0], m[1]);
            m[1] = combine(m[1], m[0], 1, -skew);
            // Now, compute Y scale and normalize 2nd row.
            scale[1] = length(m[1]);
            // m[1] = normalize(m[1]) //
            skew /= scale[1];
            // Now, get the rotation out
            var rotate = atan2(m[0][1], m[0][0]);
            return [ translate, rotate, skew, scale ];
        };
    }
});
/*! matchMedia() polyfill - Test a CSS media type/query in JS. Authors & copyright (c) 2012: Scott Jehl, Paul Irish, Nicholas Zakas. Dual MIT/BSD license */
/*! NOTE: If you're already including a window.matchMedia polyfill via Modernizr or otherwise, you don't need this part */
window.matchMedia=window.matchMedia||(function(e,f){var c,a=e.documentElement,b=a.firstElementChild||a.firstChild,d=e.createElement("body"),g=e.createElement("div");g.id="mq-test-1";g.style.cssText="position:absolute;top:-100em";d.style.background="none";d.appendChild(g);return function(h){g.innerHTML='&shy;<style media="'+h+'"> #mq-test-1 { width: 42px; }</style>';a.insertBefore(d,b);c=g.offsetWidth==42;a.removeChild(d);return{matches:c,media:h}}})(document);

/*! Respond.js v1.1.0: min/max-width media query polyfill. (c) Scott Jehl. MIT/GPLv2 Lic. j.mp/respondjs  */
(function(e){e.respond={};respond.update=function(){};respond.mediaQueriesSupported=e.matchMedia&&e.matchMedia("only all").matches;if(respond.mediaQueriesSupported){return}var w=e.document,s=w.documentElement,i=[],k=[],q=[],o={},h=30,f=w.getElementsByTagName("head")[0]||s,g=w.getElementsByTagName("base")[0],b=f.getElementsByTagName("link"),d=[],a=function(){var D=b,y=D.length,B=0,A,z,C,x;for(;B<y;B++){A=D[B],z=A.href,C=A.media,x=A.rel&&A.rel.toLowerCase()==="stylesheet";if(!!z&&x&&!o[z]){if(A.styleSheet&&A.styleSheet.rawCssText){m(A.styleSheet.rawCssText,z,C);o[z]=true}else{if((!/^([a-zA-Z:]*\/\/)/.test(z)&&!g)||z.replace(RegExp.$1,"").split("/")[0]===e.location.host){d.push({href:z,media:C})}}}}u()},u=function(){if(d.length){var x=d.shift();n(x.href,function(y){m(y,x.href,x.media);o[x.href]=true;u()})}},m=function(I,x,z){var G=I.match(/@media[^\{]+\{([^\{\}]*\{[^\}\{]*\})+/gi),J=G&&G.length||0,x=x.substring(0,x.lastIndexOf("/")),y=function(K){return K.replace(/(url\()['"]?([^\/\)'"][^:\)'"]+)['"]?(\))/g,"$1"+x+"$2$3")},A=!J&&z,D=0,C,E,F,B,H;if(x.length){x+="/"}if(A){J=1}for(;D<J;D++){C=0;if(A){E=z;k.push(y(I))}else{E=G[D].match(/@media *([^\{]+)\{([\S\s]+?)$/)&&RegExp.$1;k.push(RegExp.$2&&y(RegExp.$2))}B=E.split(",");H=B.length;for(;C<H;C++){F=B[C];i.push({media:F.split("(")[0].match(/(only\s+)?([a-zA-Z]+)\s?/)&&RegExp.$2||"all",rules:k.length-1,hasquery:F.indexOf("(")>-1,minw:F.match(/\(min\-width:[\s]*([\s]*[0-9\.]+)(px|em)[\s]*\)/)&&parseFloat(RegExp.$1)+(RegExp.$2||""),maxw:F.match(/\(max\-width:[\s]*([\s]*[0-9\.]+)(px|em)[\s]*\)/)&&parseFloat(RegExp.$1)+(RegExp.$2||"")})}}j()},l,r,v=function(){var z,A=w.createElement("div"),x=w.body,y=false;A.style.cssText="position:absolute;font-size:1em;width:1em";if(!x){x=y=w.createElement("body");x.style.background="none"}x.appendChild(A);s.insertBefore(x,s.firstChild);z=A.offsetWidth;if(y){s.removeChild(x)}else{x.removeChild(A)}z=p=parseFloat(z);return z},p,j=function(I){var x="clientWidth",B=s[x],H=w.compatMode==="CSS1Compat"&&B||w.body[x]||B,D={},G=b[b.length-1],z=(new Date()).getTime();if(I&&l&&z-l<h){clearTimeout(r);r=setTimeout(j,h);return}else{l=z}for(var E in i){var K=i[E],C=K.minw,J=K.maxw,A=C===null,L=J===null,y="em";if(!!C){C=parseFloat(C)*(C.indexOf(y)>-1?(p||v()):1)}if(!!J){J=parseFloat(J)*(J.indexOf(y)>-1?(p||v()):1)}if(!K.hasquery||(!A||!L)&&(A||H>=C)&&(L||H<=J)){if(!D[K.media]){D[K.media]=[]}D[K.media].push(k[K.rules])}}for(var E in q){if(q[E]&&q[E].parentNode===f){f.removeChild(q[E])}}for(var E in D){var M=w.createElement("style"),F=D[E].join("\n");M.type="text/css";M.media=E;f.insertBefore(M,G.nextSibling);if(M.styleSheet){M.styleSheet.cssText=F}else{M.appendChild(w.createTextNode(F))}q.push(M)}},n=function(x,z){var y=c();if(!y){return}y.open("GET",x,true);y.onreadystatechange=function(){if(y.readyState!=4||y.status!=200&&y.status!=304){return}z(y.responseText)};if(y.readyState==4){return}y.send(null)},c=(function(){var x=false;try{x=new XMLHttpRequest()}catch(y){x=new ActiveXObject("Microsoft.XMLHTTP")}return function(){return x}})();a();respond.update=a;function t(){j(true)}if(e.addEventListener){e.addEventListener("resize",t,false)}else{if(e.attachEvent){e.attachEvent("onresize",t)}}})(this);
/*
 * ----------------------------- JSTORAGE -------------------------------------
 * Simple local storage wrapper to save data on the browser side, supporting
 * all major browsers - IE6+, Firefox2+, Safari4+, Chrome4+ and Opera 10.5+
 *
 * Copyright (c) 2010 Andris Reinman, andris.reinman@gmail.com
 * Project homepage: www.jstorage.info
 *
 * Licensed under MIT-style license:
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * $.jStorage
 *
 * USAGE:
 *
 * jStorage requires Prototype, MooTools or jQuery! If jQuery is used, then
 * jQuery-JSON (http://code.google.com/p/jquery-json/) is also needed.
 * (jQuery-JSON needs to be loaded BEFORE jStorage!)
 *
 * Methods:
 *
 * -set(key, value)
 * $.jStorage.set(key, value) -> saves a value
 *
 * -get(key[, default])
 * value = $.jStorage.get(key [, default]) ->
 *    retrieves value if key exists, or default if it doesn't
 *
 * -deleteKey(key)
 * $.jStorage.deleteKey(key) -> removes a key from the storage
 *
 * -flush()
 * $.jStorage.flush() -> clears the cache
 *
 * -storageObj()
 * $.jStorage.storageObj() -> returns a read-ony copy of the actual storage
 *
 * -storageSize()
 * $.jStorage.storageSize() -> returns the size of the storage in bytes
 *
 * -index()
 * $.jStorage.index() -> returns the used keys as an array
 *
 * -storageAvailable()
 * $.jStorage.storageAvailable() -> returns true if storage is available
 *
 * -reInit()
 * $.jStorage.reInit() -> reloads the data from browser storage
 *
 * <value> can be any JSON-able value, including objects and arrays.
 *
 **/

(function($){
    if(!$ || !($.toJSON || Object.toJSON || window.JSON)){
        throw new Error("jQuery, MooTools or Prototype needs to be loaded before jStorage!");
    }

    var
        /* This is the object, that holds the cached values */
        _storage = {},

        /* Actual browser storage (localStorage or globalStorage['domain']) */
        _storage_service = {jStorage:"{}"},

        /* DOM element for older IE versions, holds userData behavior */
        _storage_elm = null,

        /* How much space does the storage take */
        _storage_size = 0,

        /* function to encode objects to JSON strings */
        json_encode = $.toJSON || Object.toJSON || (window.JSON && (JSON.encode || JSON.stringify)),

        /* function to decode objects from JSON strings */
        json_decode = $.evalJSON || (window.JSON && (JSON.decode || JSON.parse)) || function(str){
            return String(str).evalJSON();
        },

        /* which backend is currently used */
        _backend = false,

        /* Next check for TTL */
        _ttl_timeout,

        /**
         * XML encoding and decoding as XML nodes can't be JSON'ized
         * XML nodes are encoded and decoded if the node is the value to be saved
         * but not if it's as a property of another object
         * Eg. -
         *   $.jStorage.set("key", xmlNode);        // IS OK
         *   $.jStorage.set("key", {xml: xmlNode}); // NOT OK
         */
        _XMLService = {

            /**
             * Validates a XML node to be XML
             * based on jQuery.isXML function
             */
            isXML: function(elm){
                var documentElement = (elm ? elm.ownerDocument || elm : 0).documentElement;
                return documentElement ? documentElement.nodeName !== "HTML" : false;
            },

            /**
             * Encodes a XML node to string
             * based on http://www.mercurytide.co.uk/news/article/issues-when-working-ajax/
             */
            encode: function(xmlNode) {
                if(!this.isXML(xmlNode)){
                    return false;
                }
                try{ // Mozilla, Webkit, Opera
                    return new XMLSerializer().serializeToString(xmlNode);
                }catch(E1) {
                    try {  // IE
                        return xmlNode.xml;
                    }catch(E2){}
                }
                return false;
            },

            /**
             * Decodes a XML node from string
             * loosely based on http://outwestmedia.com/jquery-plugins/xmldom/
             */
            decode: function(xmlString){
                var dom_parser = ("DOMParser" in window && (new DOMParser()).parseFromString) ||
                        (window.ActiveXObject && function(_xmlString) {
                    var xml_doc = new ActiveXObject('Microsoft.XMLDOM');
                    xml_doc.async = 'false';
                    xml_doc.loadXML(_xmlString);
                    return xml_doc;
                }),
                resultXML;
                if(!dom_parser){
                    return false;
                }
                resultXML = dom_parser.call("DOMParser" in window && (new DOMParser()) || window, xmlString, 'text/xml');
                return this.isXML(resultXML)?resultXML:false;
            }
        };

    ////////////////////////// PRIVATE METHODS ////////////////////////

    /**
     * Initialization function. Detects if the browser supports DOM Storage
     * or userData behavior and behaves accordingly.
     * @returns undefined
     */
    function _init(){
        /* Check if browser supports localStorage */
        var localStorageReallyWorks = false;
        if("localStorage" in window){
            try {
                window.localStorage.setItem('_tmptest', 'tmpval');
                localStorageReallyWorks = true;
                window.localStorage.removeItem('_tmptest');
            } catch(BogusQuotaExceededErrorOnIos5) {
                // Thanks be to iOS5 Private Browsing mode which throws
                // QUOTA_EXCEEDED_ERRROR DOM Exception 22.
            }
        }
        if(localStorageReallyWorks){
            try {
                if(window.localStorage) {
                    _storage_service = window.localStorage;
                    _backend = "localStorage";
                }
            } catch(E3) {/* Firefox fails when touching localStorage and cookies are disabled */}
        }
        /* Check if browser supports globalStorage */
        else if("globalStorage" in window){
            try {
                if(window.globalStorage) {
                    _storage_service = window.globalStorage[window.location.hostname];
                    _backend = "globalStorage";
                }
            } catch(E4) {/* Firefox fails when touching localStorage and cookies are disabled */}
        }
        /* Check if browser supports userData behavior */
        else {
            _storage_elm = document.createElement('link');
            if(_storage_elm.addBehavior){

                /* Use a DOM element to act as userData storage */
                _storage_elm.style.behavior = 'url(#default#userData)';

                /* userData element needs to be inserted into the DOM! */
                document.getElementsByTagName('head')[0].appendChild(_storage_elm);

                _storage_elm.load("jStorage");
                var data = "{}";
                try{
                    data = _storage_elm.getAttribute("jStorage");
                }catch(E5){}
                _storage_service.jStorage = data;
                _backend = "userDataBehavior";
            }else{
                _storage_elm = null;
                return;
            }
        }

        _load_storage();

        // remove dead keys
        _handleTTL();
    }

    /**
     * Loads the data from the storage based on the supported mechanism
     * @returns undefined
     */
    function _load_storage(){
        /* if jStorage string is retrieved, then decode it */
        if(_storage_service.jStorage){
            try{
                _storage = json_decode(String(_storage_service.jStorage));
            }catch(E6){_storage_service.jStorage = "{}";}
        }else{
            _storage_service.jStorage = "{}";
        }
        _storage_size = _storage_service.jStorage?String(_storage_service.jStorage).length:0;
    }

    /**
     * This functions provides the "save" mechanism to store the jStorage object
     * @returns undefined
     */
    function _save(){
        try{
            _storage_service.jStorage = json_encode(_storage);
            // If userData is used as the storage engine, additional
            if(_storage_elm) {
                _storage_elm.setAttribute("jStorage",_storage_service.jStorage);
                _storage_elm.save("jStorage");
            }
            _storage_size = _storage_service.jStorage?String(_storage_service.jStorage).length:0;
        }catch(E7){/* probably cache is full, nothing is saved this way*/}
    }

    /**
     * Function checks if a key is set and is string or numberic
     */
    function _checkKey(key){
        if(!key || (typeof key != "string" && typeof key != "number")){
            throw new TypeError('Key name must be string or numeric');
        }
        if(key == "__jstorage_meta"){
            throw new TypeError('Reserved key name');
        }
        return true;
    }

    /**
     * Removes expired keys
     */
    function _handleTTL(){
        var curtime, i, TTL, nextExpire = Infinity, changed = false;

        clearTimeout(_ttl_timeout);

        if(!_storage.__jstorage_meta || typeof _storage.__jstorage_meta.TTL != "object"){
            // nothing to do here
            return;
        }

        curtime = +new Date();
        TTL = _storage.__jstorage_meta.TTL;
        for(i in TTL){
            if(TTL.hasOwnProperty(i)){
                if(TTL[i] <= curtime){
                    delete TTL[i];
                    delete _storage[i];
                    changed = true;
                }else if(TTL[i] < nextExpire){
                    nextExpire = TTL[i];
                }
            }
        }

        // set next check
        if(nextExpire != Infinity){
            _ttl_timeout = setTimeout(_handleTTL, nextExpire - curtime);
        }

        // save changes
        if(changed){
            _save();
        }
    }

    ////////////////////////// PUBLIC INTERFACE /////////////////////////

    $.jStorage = {
        /* Version number */
        version: "0.1.6.1",

        /**
         * Sets a key's value.
         *
         * @param {String} key - Key to set. If this value is not set or not
         *              a string an exception is raised.
         * @param value - Value to set. This can be any value that is JSON
         *              compatible (Numbers, Strings, Objects etc.).
         * @returns the used value
         */
        set: function(key, value){
            _checkKey(key);
            if(_XMLService.isXML(value)){
                value = {_is_xml:true,xml:_XMLService.encode(value)};
            }else if(typeof value == "function"){
                value = null; // functions can't be saved!
            }else if(value && typeof value == "object"){
                // clone the object before saving to _storage tree
                value = json_decode(json_encode(value));
            }
            _storage[key] = value;
            _save();
            return value;
        },

        /**
         * Looks up a key in cache
         *
         * @param {String} key - Key to look up.
         * @param {mixed} def - Default value to return, if key didn't exist.
         * @returns the key value, default value or <null>
         */
        get: function(key, def){
            _checkKey(key);
            if(key in _storage){
                if(_storage[key] && typeof _storage[key] == "object" &&
                        _storage[key]._is_xml &&
                            _storage[key]._is_xml){
                    return _XMLService.decode(_storage[key].xml);
                }else{
                    return _storage[key];
                }
            }
            return typeof(def) == 'undefined' ? null : def;
        },

        /**
         * Deletes a key from cache.
         *
         * @param {String} key - Key to delete.
         * @returns true if key existed or false if it didn't
         */
        deleteKey: function(key){
            _checkKey(key);
            if(key in _storage){
                delete _storage[key];
                // remove from TTL list
                if(_storage.__jstorage_meta &&
                  typeof _storage.__jstorage_meta.TTL == "object" &&
                  key in _storage.__jstorage_meta.TTL){
                    delete _storage.__jstorage_meta.TTL[key];
                }
                _save();
                return true;
            }
            return false;
        },

        /**
         * Sets a TTL for a key, or remove it if ttl value is 0 or below
         *
         * @param {String} key - key to set the TTL for
         * @param {Number} ttl - TTL timeout in milliseconds
         * @returns true if key existed or false if it didn't
         */
        setTTL: function(key, ttl){
            var curtime = +new Date();
            _checkKey(key);
            ttl = Number(ttl) || 0;
            if(key in _storage){

                if(!_storage.__jstorage_meta){
                    _storage.__jstorage_meta = {};
                }
                if(!_storage.__jstorage_meta.TTL){
                    _storage.__jstorage_meta.TTL = {};
                }

                // Set TTL value for the key
                if(ttl>0){
                    _storage.__jstorage_meta.TTL[key] = curtime + ttl;
                }else{
                    delete _storage.__jstorage_meta.TTL[key];
                }

                _save();

                _handleTTL();
                return true;
            }
            return false;
        },

        /**
         * Deletes everything in cache.
         *
         * @return true
         */
        flush: function(){
            _storage = {};
            _save();
            return true;
        },

        /**
         * Returns a read-only copy of _storage
         *
         * @returns Object
        */
        storageObj: function(){
            function F() {}
            F.prototype = _storage;
            return new F();
        },

        /**
         * Returns an index of all used keys as an array
         * ['key1', 'key2',..'keyN']
         *
         * @returns Array
        */
        index: function(){
            var index = [], i;
            for(i in _storage){
                if(_storage.hasOwnProperty(i) && i != "__jstorage_meta"){
                    index.push(i);
                }
            }
            return index;
        },

        /**
         * How much space in bytes does the storage take?
         *
         * @returns Number
         */
        storageSize: function(){
            return _storage_size;
        },

        /**
         * Which backend is currently in use?
         *
         * @returns String
         */
        currentBackend: function(){
            return _backend;
        },

        /**
         * Test if storage is available
         *
         * @returns Boolean
         */
        storageAvailable: function(){
            return !!_backend;
        },

        /**
         * Reloads the data from browser storage
         *
         * @returns undefined
         */
        reInit: function(){
            var new_storage_elm, data;
            if(_storage_elm && _storage_elm.addBehavior){
                new_storage_elm = document.createElement('link');

                _storage_elm.parentNode.replaceChild(new_storage_elm, _storage_elm);
                _storage_elm = new_storage_elm;

                /* Use a DOM element to act as userData storage */
                _storage_elm.style.behavior = 'url(#default#userData)';

                /* userData element needs to be inserted into the DOM! */
                document.getElementsByTagName('head')[0].appendChild(_storage_elm);

                _storage_elm.load("jStorage");
                data = "{}";
                try{
                    data = _storage_elm.getAttribute("jStorage");
                }catch(E5){}
                _storage_service.jStorage = data;
                _backend = "userDataBehavior";
            }

            _load_storage();
        }
    };

    // Initialize jStorage
    _init();

})(!window.MooTools ? window.jQuery : window.$);
/*

 Style HTML
---------------

	Written by Nochum Sossonko, (nsossonko@hotmail.com)

	Based on code initially developed by: Einar Lielmanis, <elfz@laacz.lv>
	http://jsbeautifier.org/


	You are free to use this in any way you want, in case you find this useful or working for you.

	Usage:
	style_html(html_source);

	style_html(html_source, options);

	The options are:
	indent_size (default 4)					— indentation size,
	indent_char (default space)			— character to indent with,
	max_char (default 70)						-	maximum amount of characters per line,
	brace_style (default "collapse") - "collapse" | "expand" | "end-expand"
			put braces on the same line as control statements (default), or put braces on own line (Allman / ANSI style), or just put end braces on own line.
	unformatted (default ['a'])			- list of tags, that shouldn't be reformatted
	indent_scripts (default normal)	- "keep"|"separate"|"normal"

	e.g.

	style_html(html_source, {
		'indent_size': 2,
		'indent_char': ' ',
		'max_char': 78,
		'brace_style': 'expand',
		'unformatted': ['a', 'sub', 'sup', 'b', 'i', 'u']
	});
*/

function style_html(html_source, options) {
//Wrapper function to invoke all the necessary constructors and deal with the output.

var multi_parser,
indent_size,
indent_character,
max_char,
brace_style;

options = options || {};
indent_size = options.indent_size || 4;
indent_character = options.indent_char || ' ';
brace_style = options.brace_style || 'collapse';
max_char = options.max_char === 0 ? Infinity : options.max_char || 70;
unformatted = options.unformatted || ['a'];

function Parser() {

	this.pos = 0; //Parser position
	this.token = '';
	this.current_mode = 'CONTENT'; //reflects the current Parser mode: TAG/CONTENT
	this.tags = { //An object to hold tags, their position, and their parent-tags, initiated with default values
		parent: 'parent1',
		parentcount: 1,
		parent1: ''
	};
	this.tag_type = '';
	this.token_text = this.last_token = this.last_text = this.token_type = '';

	this.Utils = { //Uilities made available to the various functions
		whitespace: "\n\r\t ".split(''),
		single_token: 'br,input,link,meta,!doctype,basefont,base,area,hr,wbr,param,img,isindex,?xml,embed'.split(','), //all the single tags for HTML
		extra_liners: 'head,body,/html'.split(','), //for tags that need a line of whitespace before them
		in_array: function (what, arr) {
			for (var i=0; i<arr.length; i++) {
				if (what === arr[i]) {
					return true;
				}
			}
			return false;
		}
	};

	this.get_content = function () { //function to capture regular content between tags

		var input_char = '';
		var content = [];
		var space = false; //if a space is needed
		while (this.input.charAt(this.pos) !== '<') {
			if (this.pos >= this.input.length) {
				return content.length?content.join(''):['', 'TK_EOF'];
			}

			input_char = this.input.charAt(this.pos);
			this.pos++;
			this.line_char_count++;

			if (this.Utils.in_array(input_char, this.Utils.whitespace)) {
				if (content.length) {
					space = true;
				}
				this.line_char_count--;
			continue; //don't want to insert unnecessary space
		}
		else if (space) {
			if (this.line_char_count >= this.max_char) { //insert a line when the max_char is reached
				content.push('\n');
				for (var i=0; i<this.indent_level; i++) {
					content.push(this.indent_string);
				}
				this.line_char_count = 0;
			}
			else{
				content.push(' ');
				this.line_char_count++;
			}
			space = false;
		}
		content.push(input_char); //letter at-a-time (or string) inserted to an array
	}
	return content.length?content.join(''):'';
};

	this.get_contents_to = function (name) { //get the full content of a script or style to pass to js_beautify
		if (this.pos == this.input.length) {
			return ['', 'TK_EOF'];
		}
		var input_char = '';
		var content = '';
		var reg_match = new RegExp('<\/' + name + '\\s*>', 'igm');
		reg_match.lastIndex = this.pos;
		var reg_array = reg_match.exec(this.input);
		var end_script = reg_array?reg_array.index:this.input.length; //absolute end of script
		if(this.pos < end_script) { //get everything in between the script tags
			content = this.input.substring(this.pos, end_script);
			this.pos = end_script;
		}
		return content;
	};

	this.record_tag = function (tag){ //function to record a tag and its parent in this.tags Object
		if (this.tags[tag + 'count']) { //check for the existence of this tag type
			this.tags[tag + 'count']++;
		this.tags[tag + this.tags[tag + 'count']] = this.indent_level; //and record the present indent level
	}
		else { //otherwise initialize this tag type
			this.tags[tag + 'count'] = 1;
		this.tags[tag + this.tags[tag + 'count']] = this.indent_level; //and record the present indent level
	}
		this.tags[tag + this.tags[tag + 'count'] + 'parent'] = this.tags.parent; //set the parent (i.e. in the case of a div this.tags.div1parent)
		this.tags.parent = tag + this.tags[tag + 'count']; //and make this the current parent (i.e. in the case of a div 'div1')
	};

	this.retrieve_tag = function (tag) { //function to retrieve the opening tag to the corresponding closer
		if (this.tags[tag + 'count']) { //if the openener is not in the Object we ignore it
		var temp_parent = this.tags.parent; //check to see if it's a closable tag.
		while (temp_parent) { //till we reach '' (the initial value);
			if (tag + this.tags[tag + 'count'] === temp_parent) { //if this is it use it
				break;
			}
			temp_parent = this.tags[temp_parent + 'parent']; //otherwise keep on climbing up the DOM Tree
		}
		if (temp_parent) { //if we caught something
			this.indent_level = this.tags[tag + this.tags[tag + 'count']]; //set the indent_level accordingly
			this.tags.parent = this.tags[temp_parent + 'parent']; //and set the current parent
		}
		delete this.tags[tag + this.tags[tag + 'count'] + 'parent']; //delete the closed tags parent reference...
		delete this.tags[tag + this.tags[tag + 'count']]; //...and the tag itself
		if (this.tags[tag + 'count'] == 1) {
			delete this.tags[tag + 'count'];
		}
		else {
			this.tags[tag + 'count']--;
		}
	}
};

	this.get_tag = function () { //function to get a full tag and parse its type
		var input_char = '';
		var content = [];
		var space = false;

		do {
			if (this.pos >= this.input.length) {
				return content.length?content.join(''):['', 'TK_EOF'];
			}

			input_char = this.input.charAt(this.pos);
			this.pos++;
			this.line_char_count++;

		if (this.Utils.in_array(input_char, this.Utils.whitespace)) { //don't want to insert unnecessary space
			space = true;
		this.line_char_count--;
		continue;
	}

	if (input_char === "'" || input_char === '"') {
			if (!content[1] || content[1] !== '!') { //if we're in a comment strings don't get treated specially
				input_char += this.get_unformatted(input_char);
			space = true;
		}
	}

		if (input_char === '=') { //no space before =
			space = false;
		}

		if (content.length && content[content.length-1] !== '=' && input_char !== '>' && space) { //no space after = or before >
			if (this.line_char_count >= this.max_char) {
				this.print_newline(false, content);
				this.line_char_count = 0;
			}
			else {
				content.push(' ');
				this.line_char_count++;
			}
			space = false;
		}
		content.push(input_char); //inserts character at-a-time (or string)
	} while (input_char !== '>');

	var tag_complete = content.join('');
	var tag_index;
		if (tag_complete.indexOf(' ') != -1) { //if there's whitespace, thats where the tag name ends
			tag_index = tag_complete.indexOf(' ');
	}
		else { //otherwise go with the tag ending
			tag_index = tag_complete.indexOf('>');
		}
		var tag_check = tag_complete.substring(1, tag_index).toLowerCase();
		if (tag_complete.charAt(tag_complete.length-2) === '/' ||
			this.Utils.in_array(tag_check, this.Utils.single_token)) { //if this tag name is a single tag type (either in the list or has a closing /)
			this.tag_type = 'SINGLE';
	}
		else if (tag_check === 'script') { //for later script handling
			this.record_tag(tag_check);
			this.tag_type = 'SCRIPT';
		}
		else if (tag_check === 'style') { //for future style handling (for now it justs uses get_content)
			this.record_tag(tag_check);
			this.tag_type = 'STYLE';
		}
		else if (this.Utils.in_array(tag_check, unformatted)) { // do not reformat the "unformatted" tags
		var comment = this.get_unformatted('</'+tag_check+'>', tag_complete); //...delegate to get_unformatted function
	content.push(comment);
	this.tag_type = 'SINGLE';
}
		else if (tag_check.charAt(0) === '!') { //peek for <!-- comment
		if (tag_check.indexOf('[if') != -1) { //peek for <!--[if conditional comment
			if (tag_complete.indexOf('!IE') != -1) { //this type needs a closing --> so...
			var comment = this.get_unformatted('-->', tag_complete); //...delegate to get_unformatted
			content.push(comment);
		}
		this.tag_type = 'START';
	}
		else if (tag_check.indexOf('[endif') != -1) {//peek for <!--[endif end conditional comment
			this.tag_type = 'END';
			this.unindent();
		}
		else if (tag_check.indexOf('[cdata[') != -1) { //if it's a <[cdata[ comment...
			var comment = this.get_unformatted(']]>', tag_complete); //...delegate to get_unformatted function
		content.push(comment);
			this.tag_type = 'SINGLE'; //<![CDATA[ comments are treated like single tags
		}
		else {
			var comment = this.get_unformatted('-->', tag_complete);
			content.push(comment);
			this.tag_type = 'SINGLE';
		}
	}
	else {
		if (tag_check.charAt(0) === '/') { //this tag is a double tag so check for tag-ending
			this.retrieve_tag(tag_check.substring(1)); //remove it and all ancestors
			this.tag_type = 'END';
		}
		else { //otherwise it's a start-tag
			this.record_tag(tag_check); //push it on the tag stack
		this.tag_type = 'START';
	}
		if (this.Utils.in_array(tag_check, this.Utils.extra_liners)) { //check if this double needs an extra line
			this.print_newline(true, this.output);
		}
	}
		return content.join(''); //returns fully formatted tag
	};

	this.get_unformatted = function (delimiter, orig_tag) { //function to return unformatted content in its entirety

		if (orig_tag && orig_tag.indexOf(delimiter) != -1) {
			return '';
		}
		var input_char = '';
		var content = '';
		var space = true;
		do {

			if (this.pos >= this.input.length) {
				return content;
			}

			input_char = this.input.charAt(this.pos);
			this.pos++;

			if (this.Utils.in_array(input_char, this.Utils.whitespace)) {
				if (!space) {
					this.line_char_count--;
					continue;
				}
				if (input_char === '\n' || input_char === '\r') {
					content += '\n';
			/*	Don't change tab indention for unformatted blocks.	If using code for html editing, this will greatly affect <pre> tags if they are specified in the 'unformatted array'
			for (var i=0; i<this.indent_level; i++) {
				content += this.indent_string;
			}
			space = false; //...and make sure other indentation is erased
			*/
			this.line_char_count = 0;
			continue;
		}
	}
	content += input_char;
	this.line_char_count++;
	space = true;


} while (content.indexOf(delimiter) == -1);
return content;
};

	this.get_token = function () { //initial handler for token-retrieval
		var token;

		if (this.last_token === 'TK_TAG_SCRIPT' || this.last_token === 'TK_TAG_STYLE') { //check if we need to format javascript
			var type = this.last_token.substr(7);
			token = this.get_contents_to(type);
			if (typeof token !== 'string') {
				return token;
			}
			return [token, 'TK_' + type];
		}
		if (this.current_mode === 'CONTENT') {
			token = this.get_content();
			if (typeof token !== 'string') {
				return token;
			}
			else {
				return [token, 'TK_CONTENT'];
			}
		}

		if (this.current_mode === 'TAG') {
			token = this.get_tag();
			if (typeof token !== 'string') {
				return token;
			}
			else {
				var tag_name_type = 'TK_TAG_' + this.tag_type;
				return [token, tag_name_type];
			}
		}
	};

	this.get_full_indent = function (level) {
		level = this.indent_level + level || 0;
		if (level < 1)
			return '';

		return Array(level + 1).join(this.indent_string);
	};


	this.printer = function (js_source, indent_character, indent_size, max_char, brace_style) { //handles input/output and some other printing functions

		this.input = js_source || ''; //gets the input for the Parser
		this.output = [];
		this.indent_character = indent_character;
		this.indent_string = '';
		this.indent_size = indent_size;
		this.brace_style = brace_style;
		this.indent_level = 0;
		this.max_char = max_char;
		this.line_char_count = 0; //count to see if max_char was exceeded

		for (var i=0; i<this.indent_size; i++) {
			this.indent_string += this.indent_character;
		}

		this.print_newline = function (ignore, arr) {
			this.line_char_count = 0;
			if (!arr || !arr.length) {
				return;
			}
		if (!ignore) { //we might want the extra line
			while (this.Utils.in_array(arr[arr.length-1], this.Utils.whitespace)) {
				arr.pop();
			}
		}
		arr.push('\n');
		for (var i=0; i<this.indent_level; i++) {
			arr.push(this.indent_string);
		}
	};

	this.print_token = function (text) {
		this.output.push(text);
	};

	this.indent = function () {
		this.indent_level++;
	};

	this.unindent = function () {
		if (this.indent_level > 0) {
			this.indent_level--;
		}
	};
};
return this;
}

/*_____________________--------------------_____________________*/

	multi_parser = new Parser(); //wrapping functions Parser
	multi_parser.printer(html_source, indent_character, indent_size, max_char, brace_style); //initialize starting values

	while (true) {
		var t = multi_parser.get_token();
		multi_parser.token_text = t[0];
		multi_parser.token_type = t[1];

		if (multi_parser.token_type === 'TK_EOF') {
			break;
		}

		switch (multi_parser.token_type) {
			case 'TK_TAG_START':
			multi_parser.print_newline(false, multi_parser.output);
			multi_parser.print_token(multi_parser.token_text);
			multi_parser.indent();
			multi_parser.current_mode = 'CONTENT';
			break;
			case 'TK_TAG_STYLE':
			case 'TK_TAG_SCRIPT':
			multi_parser.print_newline(false, multi_parser.output);
			multi_parser.print_token(multi_parser.token_text);
			multi_parser.current_mode = 'CONTENT';
			break;
			case 'TK_TAG_END':
		//Print new line only if the tag has no content and has child
		if (multi_parser.last_token === 'TK_CONTENT' && multi_parser.last_text === '') {
			var tag_name = multi_parser.token_text.match(/\w+/)[0];
			var tag_extracted_from_last_output = multi_parser.output[multi_parser.output.length -1].match(/<\s*(\w+)/);
			if (tag_extracted_from_last_output === null || tag_extracted_from_last_output[1] !== tag_name)
				multi_parser.print_newline(true, multi_parser.output);
		}
		multi_parser.print_token(multi_parser.token_text);
		multi_parser.current_mode = 'CONTENT';
		break;
		case 'TK_TAG_SINGLE':
		multi_parser.print_newline(false, multi_parser.output);
		multi_parser.print_token(multi_parser.token_text);
		multi_parser.current_mode = 'CONTENT';
		break;
		case 'TK_CONTENT':
		if (multi_parser.token_text !== '') {
			multi_parser.print_token(multi_parser.token_text);
		}
		multi_parser.current_mode = 'TAG';
		break;
		case 'TK_STYLE':
		case 'TK_SCRIPT':
		if (multi_parser.token_text !== '') {
			multi_parser.output.push('\n');
			var text = multi_parser.token_text;
			if (multi_parser.token_type == 'TK_SCRIPT') {
				var _beautifier = typeof js_beautify == 'function' && js_beautify;
			} else if (multi_parser.token_type == 'TK_STYLE') {
				var _beautifier = typeof css_beautify == 'function' && css_beautify;
			}

			if (options.indent_scripts == "keep") {
				var script_indent_level = 0;
			} else if (options.indent_scripts == "separate") {
				var script_indent_level = -multi_parser.indent_level;
			} else {
				var script_indent_level = 1;
			}

			var indentation = multi_parser.get_full_indent(script_indent_level);
			if (_beautifier) {
			// call the Beautifier if avaliable
			text = _beautifier(text.replace(/^\s*/, indentation), options);
		} else {
			// simply indent the string otherwise
			var white = text.match(/^\s*/)[0];
			var _level = white.match(/[^\n\r]*$/)[0].split(multi_parser.indent_string).length - 1;
			var reindent = multi_parser.get_full_indent(script_indent_level -_level);
			text = text.replace(/^\s*/, indentation)
			.replace(/\r\n|\r|\n/g, '\n' + reindent)
			.replace(/\s*$/, '');
		}
		if (text) {
			multi_parser.print_token(text);
			multi_parser.print_newline(true, multi_parser.output);
		}
	}
	multi_parser.current_mode = 'TAG';
	break;
}
multi_parser.last_token = multi_parser.token_type;
multi_parser.last_text = multi_parser.token_text;
}
return multi_parser.output.join('');
}
(function (doc, win, udef) {

	var
		firstEl = function (el) {
			return doc[el] || doc.getElementsByTagName(el)[0];
		},
		maybeCall = function(thing, ctx, args) {
			return typeof thing == 'function' ? thing.apply(ctx, args) : thing;
		},
		transitionEndEventName = null,

		stylesAreInjected = false,
		injectStyleSheet = function() {
			if (!stylesAreInjected) {

				stylesAreInjected = true;

				var stylesText =
					'.twipsy { display: block; position: absolute; visibility: visible; padding: 5px; font-size: 12px; z-index: 11000;}\
					.twipsy.above .twipsy-arrow { bottom: 0; left: 50%; margin-left: -5px; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #000000;}\
					.twipsy.above-left .twipsy-arrow { bottom: 0; left: 18px; margin-left: -5px; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #000000;}\
					.twipsy.above-right .twipsy-arrow { bottom: 0; right: 18px; margin-left: -5px; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #000000;}\
					.twipsy.left .twipsy-arrow { top: 50%; right: 0; margin-top: -5px; border-top: 5px solid transparent; border-bottom: 5px solid transparent; border-left: 5px solid #000000;}\
					.twipsy.below .twipsy-arrow { top: 0; left: 50%; margin-left: -5px; border-left: 5px solid transparent; border-right: 5px solid transparent; border-bottom: 5px solid #000000;}\
					.twipsy.below-left .twipsy-arrow { top: 0; left: 18px; margin-left: -5px; border-left: 5px solid transparent; border-right: 5px solid transparent; border-bottom: 5px solid #000000;}\
					.twipsy.below-right .twipsy-arrow { top: 0; right: 18px; margin-left: -5px; border-left: 5px solid transparent; border-right: 5px solid transparent; border-bottom: 5px solid #000000;}\
					.twipsy.right .twipsy-arrow { top: 50%; left: 0; margin-top: -5px; border-top: 5px solid transparent; border-bottom: 5px solid transparent; border-right: 5px solid #000000;}\
					.twipsy-inner { padding: 3px 8px; background-color: #000000; color: white; text-align: center; max-width: 200px; text-decoration: none; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px;}\
					.twipsy-arrow { position: absolute; width: 0; height: 0;}',
					stylesContainer = new Element("style", {"type":"text/css"}).inject(firstEl("head"), "bottom");

				stylesContainer.styleSheet
					? stylesContainer.styleSheet.cssText = stylesText
					: stylesContainer.innerHTML = stylesText;
			}
		};

	// Determine browser support for CSS transitions
	if (typeOf(Browser.Features.transition) != "boolean") {
		Browser.Features.transition = (function () {
			var styles = (doc.body || doc.documentElement).style;

			if (styles.transition !== udef || styles.MsTransition !== udef) {
				transitionEndEventName = "TransitionEnd";
			}
			else if (styles.WebkitTransition !== udef) {
				transitionEndEventName = "webkitTransitionEnd";
			}
			else if (styles.MozTransition !== udef) {
				transitionEndEventName = "transitionend";
			}
			else if (styles.OTransition !== udef) {
				transitionEndEventName = "oTransitionEnd";
			}

			return transitionEndEventName != null;
		})();
	}



	var Twipsy = new Class({

		/**
		* Construct the twipsy
		*
		* @param element Element
		* @param options object
		*/
		initialize:function (element, options) {
			this.options = Object.merge({}, Twipsy.defaults, options);
			this.element = doc.id(element);
			this.enabled = true;
			if (options.injectStyles) {
				injectStyleSheet();
			}
			this.fixTitle();
		},

		/**
		* Display the twipsy
		*
		* @return Twipsy
		*/
		show: function() {
			var pos, actualWidth, actualHeight, placement, twipsyElement, position,
				offset, size, twipsySize, leftPosition;
			if (this.hasContent() && this.enabled) {
				twipsyElement = this.setContent().getTip();

				if (this.options.animate) {
					moofx(twipsyElement).animate({'opacity': 0.8}, {
						duration: '150ms',
						equation: 'ease-in',
						callback: function(){
							this.isShown = true;
						}.bind(this)
					});//.addClass('twipsy-fade');
				}

				twipsyElement
					.setStyles({top: 0, left: 0, display: 'block'})
					.inject(document.body, 'top');

				offset = this.element.getPosition();
				size   = this.element.getSize();
				pos    = {
					left:   offset.x,
					top:    offset.y,
					width:  size.x,
					height: size.y
				};

				twipsySize = twipsyElement.getSize();
				actualWidth = twipsySize.x;
				actualHeight = twipsySize.y;

				placement = maybeCall(this.options.placement, this, [twipsyElement, this.element]);
				leftPosition = pos.left - actualWidth - this.options.offset;

				if (leftPosition < 0 && placement == 'left') placement = 'right';

				switch (placement) {
					case 'below':
						position = {top: pos.top + pos.height + this.options.offset, left: pos.left + pos.width / 2 - actualWidth / 2};
						break;

					case 'below-left':
						position = {top: pos.top + pos.height + this.options.offset, left: pos.left - this.options.offset};
						break;

					case 'below-right':
						position = {top: pos.top + pos.height + this.options.offset, left: pos.left + pos.width - actualWidth + this.options.offset};
						break;

					case 'above':
						position = {top: pos.top - actualHeight - this.options.offset, left: pos.left + pos.width / 2 - actualWidth / 2};
						break;

					case 'above-left':
						position = {top: pos.top - actualHeight - this.options.offset, left: pos.left - this.options.offset};
						break;

					case 'above-right':
						position = {top: pos.top - actualHeight - this.options.offset, left: pos.left + pos.width - actualWidth + this.options.offset};
						break;

					case 'left':
						position = {top: pos.top + pos.height / 2 - actualHeight / 2, left: leftPosition};
						break;

					case 'right':
						position = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width + this.options.offset};
						break;
				}

				twipsyElement
					.setStyles(position)
					.addClass(placement);
			}
			return this;
		},

		/**
		* Remove the twipsy from screen
		*
		* @return Twipsy
		*/
		hide: function() {
			var twipsyElement = this.getTip(),
				removeTwipsy = function(){
					this.isShown = false;
					twipsyElement.dispose();
				}.bind(this);

			if (!this.hasContent()){
				removeTwipsy();
				return this;
			}

			moofx(twipsyElement).animate({'opacity': 0}, {
				duration: '150ms',
				equation: 'ease-in',
				callback: removeTwipsy
			});

			return this;
		},

		/**
		* Set the readable content of the twipsy
		*
		* @return Twipsy
		*/
		setContent: function () {
			this.getTip().getElement('.twipsy-inner').set(this.options.html ? 'html' : 'text', this.getTitle());
			return this;
		},

		/**
		* Test if we have a content to put in the twipsy
		*
		@return boolean
		*/
		hasContent: function() {
			return this.getTitle().replace(/\s+/g, "") !== "";
		},

		/**
		* Get the title string
		*
		* @return String
		*/
		getTitle: function() {
			var title,
				e = this.element,
				o = this.options;

			this.fixTitle();

			if (typeof o.title == 'string') {
				title = e.getProperty(o.title == 'title' ? 'data-original-title' : o.title);
			}
			else if (typeof o.title == 'function') {
				title = o.title.call(e);
			}

			title = ('' + title).clean();
			return title || o.fallback;
		},

		/**
		* Get the twipsy HTML Element, construct it if not yet available
		*
		* @return Element
		*/
		getTip: function() {
			if (!this.tip) {
				this.tip = new Element("div.twipsy", {html: this.options.template});
			}
			return this.tip;
		},

		/**
		* Check if the given element is on screen
		*
		* @return boolean
		*/
		validate:function () {
			if (!this.element.parentNode) {
				this.hide();
				this.element = null;
				this.options = null;
				return false;
			}
			return true;
		},

		/**
		* Set enabled status to true
		*
		* @return Twipsy
		*/
		enable: function() {
			this.enabled = true;
			return this;
		},

		/**
		* Set enabled status to false
		*
		* @return twipsy
		*/
		disable: function() {
			this.enabled = false;
			return this;
		},

		/**
		* Toggle the enabled status
		*
		* @return Twipsy
		*/
		toggleEnabled: function() {
			this.enabled = !this.enabled;
			return this;
		},

		/**
		* Toggle the twipsy
		*
		* @return Twipsy
		*/
		toggle: function() {
			this[this.getTip().hasClass('in') ? 'hide' : 'show']();
			return this;
		},

		/**
		* Fix the title attribute of the trigger element, if not done yet
		*
		* @return Twipsy
		*/
		fixTitle:function () {
			var el = this.element;
			if (el.getProperty("title") || !el.getProperty("data-original-title")) {
				el.setProperty('data-original-title', el.getProperty("title") || '').removeProperty('title');
			}
			return this;
		}
	});

	Twipsy.defaults = {
		placement:    "above",
		animate:      true,
		delayIn:      0,
		delayOut:     0,
		html:         false,
		live:         false,
		offset:       0,
		title:        'title',
		trigger:      'hover',
		injectStyles: true,
		fallback:     "",
		template:     '<div class="twipsy-inner"></div><div class="twipsy-arrow"></div>'
	};

	Twipsy.rejectAttrOptions = ['title'];

	Twipsy.elementOptions = function (el, options) {
		var data = {},
			rejects = Twipsy.rejectAttrOptions,
			i = rejects.length;

		[
			"placement", "animate", "delay-in", "delay-out", "html",
			"offset", "title", "trigger", "template", "inject-styles"
		].each(function(item) {
			var res = null,lower;
			if (el.dataset) {
				res = el.dataset[item.camelCase()];
			}
			else {
				res = el.getProperty("data-" + item);
			}
			if (res) {
				lower = res.toLowerCase().clean();
				if (lower === "true") res = true;
				else if (lower === "false") res = false;
				else if (/^[0-9]+$/.test(lower)) lower = parseInt(lower, 10);
				data[item.camelCase()] = res;
			}
		});

		while (i--) {
			delete data[rejects[i]];
		}

		return Object.merge({}, options, data);
	};

	Element.implement({
		twipsy:function (options) {
			var twipsy, binder, eventIn, eventOut, name = 'twipsy';

			if (options === true) {
				return this.retrieve(name);
			}
			else if (typeof options == 'string') {
				twipsy = this.retrieve(name);
				if (twipsy) {
					twipsy[options]();
				}
				return this;
			}

			options = Object.merge({}, Twipsy.defaults, options);

			function get(ele) {
				var twipsy = ele.retrieve(name);

				if (!twipsy) {
					twipsy = new Twipsy(ele, Twipsy.elementOptions(ele, options));
					ele.store(name, twipsy);
				}

				return twipsy;
			}

			function enter() {
				var twipsy = get(this);
				twipsy.hoverState = 'in';

				if (options.delayIn == 0) {
					twipsy.show();
				} else {
					twipsy.fixTitle();
					setTimeout(function () {
						if (twipsy.hoverState == 'in') {
							twipsy.show();
						}
					}, options.delayIn);
				}
			}

			function leave() {
				var twipsy = get(this);
				twipsy.hoverState = 'out';
				if (options.delayOut == 0) {
					twipsy.hide();
				} else {
					setTimeout(function () {
						if (twipsy.hoverState == 'out') {
							twipsy.hide();
						}
					}, options.delayOut);
				}
			}

			if (options.trigger != 'manual') {
				eventIn = options.trigger == 'hover' ? 'mouseenter' : 'focus';
				eventOut = options.trigger == 'hover' ? 'mouseleave' : 'blur';
				get(this);

				document.id(this).addEvent(eventIn, enter).addEvent(eventOut, leave);
			}
			return this;
		}
	});

	Elements.implement({
		twipsy:function (options) {
			this.each(function(el) {
				el.twipsy(options);
			});
			return this;
		}
	});

	win.Twipsy = Twipsy;

})(document, self, undefined);
/**
* @version   $Id: rokpad.js 18685 2014-02-11 05:14:08Z djamil $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2018 RocketTheme, LLC
* @license   http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
*/

((function(){

	if (typeof this.RokPadData == 'undefined') this.RokPadData = {};

//'Ambience': 'ambience', 'Chaos': 'chaos', 'GitHub': 'github', 'Terminal': 'terminal', 'Xcode': 'xcode'
	var RokPadThemes = {
		'theme': {'Ambiance': 'ambiance', 'Chaos': 'chaos', 'Chrome': 'chrome', 'Clouds': 'clouds', 'Clouds Midnight': 'clouds_midnight', 'Cobalt': 'cobalt', 'Crimson Editor': 'crimson_editor', 'Dawn': 'dawn', 'Dreamweaver': 'dreamweaver', 'Eclipse': 'eclipse', 'Fluidvision': 'fluidvision', 'GitHub': 'github', 'idleFingers': 'idle_fingers', 'krTheme': 'kr', 'Merbivore': 'merbivore', 'Merbivore Soft': 'merbivore_soft', 'Mono Industrial': 'mono_industrial', 'Monokai': 'monokai', 'Pastel on dark': 'pastel_on_dark', 'Solarized Dark': 'solarized_dark', 'Solarized Light': 'solarized_light', 'Terminal': 'terminal', 'TextMate': 'textmate', 'Tomorrow': 'tomorrow', 'Tomorrow Night': 'tomorrow_night', 'Tomorrow Night Blue': 'tomorrow_night_blue', 'Tomorrow Night Bright': 'tomorrow_night_bright', 'Tomorrow Night 80s': 'tomorrow_night_eighties', 'Twilight': 'twilight', 'Vibrant Ink': 'vibrant_ink', 'Xcode': 'xcode' },
		'font-size': ['7px', '8px', '9px', '10px', '11px', '12px', '13px', '14px', '15px', '16px', '20px', '24px']
	};

	this.RokPadClass = new Class({

		Implements: [Options, Events],

		initialize: function(){
			this.elements = document.getElements('[data-rokpad-editor]');
			this.editors = {};

			// fixing paths
			if (window.ace){
				window.ace.config.set('workerPath', RokPadAcePath);
				window.ace.config.set('modePath', RokPadAcePath);
				window.ace.config.set('themePath', RokPadAcePath);
			}

			this.attach();
			this._startAutoSave();

			return this;
		},

		attach: function(){
			this.elements.each(function(element){
				var attached = element.retrieve('rokpad:attached', false),
					options = element.retrieve('rokpad:options', false);

				if (!attached){
					element.store('rokpad:attached', true);
					var id = element.get('data-rokpad-editor'),
						container = element.getElement('[data-rokpad-container]');

					this.editors[id] = this._getACE(element);

					if (!options){
						this._populateOptions(id, element);
						element.store('rokpad:options', true);
					}

					this._restoreSettings(id);
					this._restoreOptions(id);
					this._extrasFixes(id, element);

					container.makeResizable({
						handle: element.getElement('.rokpad-statusbar'),
						modifiers: {x: false, y: 'height'},
						limit: {x: false, y: [container.getStyle('min-height').toInt(), false]},
						onStart: function(){
							document.removeEvent('click', relay.document);
						}.bind(this),
						onDrag: function(){
							this.editors[id].getEditor().resize();
						}.bind(this),
						onComplete: function(){
							document.addEvent('click', relay.document);
							this._store('height', container.getStyle('height').toInt());
						}.bind(this)
					});

					var relay = {
						undo: element.retrieve('rokpad:events:undo', function(event, target){
							this.undo.call(this, event, id, element, target);
						}.bind(this)),

						redo: element.retrieve('rokpad:events:redo', function(event, target){
							this.redo.call(this, event, id, element, target);
						}.bind(this)),

						fullscreen: element.retrieve('rokpad:events:fullscreen', function(event, target){
							this.fullscreen.call(this, event, id, element, target);
						}.bind(this)),

						shortcode: element.retrieve('rokpad:events:shortcode', function(event, target){
							this.insertShortCode.call(this, event, id, element, target);
						}.bind(this)),

						softtabs: element.retrieve('rokpad:events:softtabs', function(event, target){
							event.preventDefault();

							this.setUseSoftTabs.call(this, id, target, element);
						}.bind(this)),

						tabsize: element.retrieve('rokpad:events:tabsize', function(event, target){
							event.preventDefault();

							this.setTabSize.call(this, id, target, element);
						}.bind(this)),

						mode: element.retrieve('rokpad:events:mode', function(event, target){
							event.preventDefault();

							this.setMode.call(this, id, target, element);
						}.bind(this)),

						dropdownToggle: element.retrieve('rokpad:events:dropdownToggle', function(event, target){
							var dropdownType = target.get('data-rokpad-toggle'),
								dropdownElement = element.getElement('[data-rokpad-dropdown='+dropdownType+']') || element.getElement('[data-rokpad-popover='+dropdownType+']');

							element.getElements('[data-rokpad-dropdown], [data-rokpad-popover]').setStyle('display', 'none');
							dropdownElement.setStyle('display', 'block');
						}.bind(this)),

						toggleActionSettings: element.retrieve('rokpad:events:actionSettings', function(event, target){
							event.preventDefault();
							this.toggleActionSettings.call(this, id, target.get('data-rokpad-action-setting'));
						}.bind(this)),

						saveButton: element.retrieve('rokpad:events:saveAction', function(event, target){
							event.preventDefault();
							this.save.call(this);
						}.bind(this)),

						findButton: element.retrieve('rokpad:events:find', function(event, target){
							event.preventDefault();
							var input = element.getElement('[data-rokpad-action-method=find] input');

							this._showActionbar.call(this, id);
							this._setActionbar.call(this, id, (event.shift ? 'replace' : 'find'));
							input.select(); input.focus();
						}.bind(this)),

						findReplaceButton: element.retrieve('rokpad:events:findreplace', function(event, target){
							event.preventDefault();
							var input = element.getElement('[data-rokpad-action-method=find] input');

							this._showActionbar.call(this, id);
							this._setActionbar.call(this, id, 'replace');
							input.select(); input.focus();
							this._hideDropDowns();
						}.bind(this)),

						beautifyHTML: element.retrieve('rokpad:events:beautifyhtml', function(event, target){
							event.preventDefault();

							this.editors[id].setValue(style_html(this.editors[id].getValue(), {
								'indent_size': this.editors[id].getEditor().getSession().getTabSize(),
								'indent_char': ' ',
								'max_char': 0,
								'unformatted': ['a', 'sub', 'sup', 'b', 'i', 'u']
							}));

							this._hideDropDowns();
						}.bind(this)),

						splitVertical: element.retrieve('rokpad:events:splitvertical', function(event, target){
							event.preventDefault();

						}.bind(this)),

						gotoButton: element.retrieve('rokpad:events:goto', function(event, target){
							event.preventDefault();
							var input = element.getElement('[data-rokpad-action-method=goto] input');

							this._showActionbar.call(this, id);
							this._setActionbar.call(this, id, 'goto');
							input.select(); input.focus();
							this._hideDropDowns.call(this);
						}.bind(this)),

						showFindOrReplace: element.retrieve('rokpad:events:showfind', function(event, target){
							var input;

							if (event.key == 'esc'){
								this._hideActionbar.call(this, id);
								return true;
							}

							if (event.key == 'enter'){
								var settings = this._getRange.call(this, id, this._retrieve('actionSettings'));
								if (target.getParent('[data-rokpad-action-method]').get('data-rokpad-action-method') == 'goto'){
									input = element.getElement('[data-rokpad-action-method=goto] input');
									this.editors[id].getEditor().gotoLine(input.get('value'), 0, true);
								} else {
									this.editors[id].getEditor().find(settings.needle, settings, true);
								}

								event.preventDefault();
								return true;
							}

							if (event.key == 'l' && event[Browser.Platform.mac ? 'meta' : 'control']){
								input = element.getElement('[data-rokpad-action-method=goto] input');
								event.preventDefault();
								this._showActionbar.call(this, id);
								this._setActionbar.call(this, id, 'goto');
								input.select(); input.focus();

								event.preventDefault();
								return true;
							}

							if (event.key == 'g' && event[Browser.Platform.mac ? 'meta' : 'control']){
								event.preventDefault();
								this.editors[id].getEditor()[event.shift ? 'findPrevious' : 'findNext'](this._getRange(id, this._retrieve('actionSettings')));
								this.editors[id].getEditor().focus();

								event.preventDefault();
								return true;
							}

							if (event.key != 'f' || !event[Browser.Platform.mac ? 'meta' : 'control']) return true;

							input = element.getElement('[data-rokpad-action-method=find] input');
							event.preventDefault();
							this._showActionbar.call(this, id);
							this._setActionbar.call(this, id, (event.shift ? 'replace' : 'find'));
							input.select(); input.focus();
						}.bind(this)),


						actionInput: element.retrieve('rokpad:events:actioninput', function(event, target){
							this.performAction.call(this, id, target, element);
						}.bind(this)),

						document: function(event, target){
							this._hideDropDowns.call(this, event, target);
						}.bind(this),

						inputWrapper: function(event, target){
							target.getElement('input').focus();
						},

						changeOptions: function(event, target){
							this.changeOptions.call(this, id, target, 'store');
						}.bind(this),

						enableAutoSave: function(event, target){
							if (target.get('checked')) this._startAutoSave();
							else this._stopAutoSave();
						}.bind(this),

						dragover: function(event, target){
							this.dragOver.call(this, event, id, target);
						}.bind(this),

						drop: function(event, target){
							this.drop.call(this, event, id, target);
						}.bind(this)
					};

					element.addEvents({
						'click:relay([data-rokpad-undo])': relay.undo,
						'click:relay([data-rokpad-redo])': relay.redo,
						'click:relay([data-rokpad-fullscreen])': relay.fullscreen,
						'click:relay([data-rokpad-shortcode])': relay.shortcode,
						'click:relay([data-rokpad-toggle])': relay.dropdownToggle,
						'click:relay([data-rokpad-softtabs])': relay.softtabs,
						'click:relay([data-rokpad-tabsize])': relay.tabsize,
						'click:relay([data-rokpad-mode])': relay.mode,
						'click:relay([data-rokpad-action-setting])': relay.toggleActionSettings,
						'click:relay([data-rokpad-save])': relay.saveButton,
						'click:relay([data-rokpad-find])': relay.findButton,
						'click:relay([data-rokpad-find-replace])': relay.findReplaceButton,
						'click:relay([data-rokpad-beautify])': relay.beautifyHTML,
						'click:relay([data-rokpad-split])': relay.splitVertical,
						'click:relay([data-rokpad-goto])': relay.gotoButton,
						'keyup:relay([class*=rok-input-row-] input)': relay.actionInput,
						'click:relay([data-rokpad-action])': relay.actionInput,
						'keydown:relay([class*=rok-input-row-] input)': relay.showFindOrReplace,
						'click:relay(.rok-input-wrapper)': relay.inputWrapper,
						'click:relay(input[data-rokpad-options])': relay.changeOptions,
						'change:relay(select[data-rokpad-options])': relay.changeOptions,
						'keyup:relay(input[type=text][data-rokpad-options])': relay.changeOptions,
						'click:relay(input[data-rokpad-options=autosave-enabled])': relay.enableAutoSave
					});

					document.id(document.body).addEvents({
						'dragover': relay.dragover,
						'drop': relay.drop
					});

					if (!document.retrieve('rokpad:events:document', false)){
						document.store('rokpad:events:document', true);
						document.addEvent('click', relay.document);
					}

				}
			}, this);
		},

		attachResize: function(id, element){
			var resize = window.retrieve('rokpad:events:resize', function(){
				var xtdButtons = document.getElement('.btn-toolbar'),
					height = this._calculateHeight(element),
					xtdButtonsHeight = xtdButtons ? (xtdButtons.getSize().y + 18) || 0 : 0;

				element.getElement('.rokpad-editor-container').setStyle('height', window.getSize().y - height - xtdButtonsHeight);
				this.editors[id].getEditor().resize.delay(1, this.editors[id].getEditor());
			}.bind(this));

			window.addEvent('resize', resize);
		},

		detachResize: function(){
			window.removeEvent('resize', window.retrieve('rokpad:events:resize'));
		},

		dragOver: function(event, id, target){
			//console.log('drag');
		},

		drop: function(event, id, target){
			var file;
			try {
				file = event.event.dataTransfer.files[0];
				if (window.FileReader) {
					var reader = new FileReader();
					reader.onload = function() {
						var mode = this._getModeFromPath(file.name);
						this.editors[id].setValue(reader.result);
						this.setMode(id, this.editors[id].wrapper.getElement('[data-rokpad-mode='+mode+']'), this.editors[id].wrapper, true);
					}.bind(this);
					reader.readAsText(file);
				}
				return event.preventDefault(event);
			} catch(err) {
				return event.preventDefault(event);
			}
		},

		_getModeFromPath: function(path) {
			var mode = 'text',
				extRe;

			Object.each(RokPadData.modesList, function(data, key) {
				extRe = new RegExp("^.*\\.(" + data[1] + ")$", "g");

				if (path.match(extRe)) {
					mode = data[2];
				}
			});

			return mode;
		},

//	event.addListener(container, "dragover", function(e) {
//     return event.preventDefault(e);
// });

// event.addListener(container, "drop", function(e) {
//     var file;
//     try {
//         file = e.dataTransfer.files[0];
//         if (window.FileReader) {
//             var reader = new FileReader();
//             reader.onload = function() {
//                 var mode = getModeFromPath(file.name);

//                 env.editor.session.doc.setValue(reader.result);
//                 modeEl.value = mode.name;
//                 env.editor.session.setMode(mode.mode);
//                 env.editor.session.modeName = mode.name;
//             };
//             reader.readAsText(file);
//         }
//         return event.preventDefault(e);
//     } catch(err) {
//         return event.stopEvent(e);
//     }
// });

		undo: function(event, id, element, target){
			if (target.hasClass('rok-button-disabled')) return true;

			this.editors[id].getEditor().undo(true);
			// to not have the selection after an undo
			// this.editors[id].getUndoManager().undo(true);
		},

		redo: function(event, id, element, target){
			if (target.hasClass('rok-button-disabled')) return true;

			this.editors[id].getEditor().redo(true);
			// to not have the selection after a redo
			//this.editors[id].getUndoManager().redo(true);
		},

		setMode: function(id, target, element, dontstore){
			element = element || this.editors[id].wrapper;
			target = target || 'text';
			target = typeOf(target) == 'element' ? target : element.getElement('[data-rokpad-mode='+target+']');
			var value = target.get('data-rokpad-mode'),
				siblings = target.getSiblings('[data-rokpad-mode]');

			if (!value) return true;

			siblings.removeClass('rokpad-checked');
			target.addClass('rokpad-checked');

			element.getElement('[data-rokpad-toggle=mode] span').set('text', target.get('text'));

			this.editors[id].getEditor().getSession().clearAnnotations();
			this.editors[id].getEditor().getSession().setMode('ace/mode/' + value);
			this._hideDropDowns();
			if (!dontstore) this._store('mode', value);
			return this;
		},

		getMode: function(id){
			return this.editors[id].getEditor().getSession().getMode().$id.replace(/^ace\/mode\//g, '');
		},

		setTabSize: function(id, target, element){
			element = element || this.editors[id].wrapper;
			target = typeOf(target) == 'element' ? target : element.getElement('[data-rokpad-tabsize='+target+']');
			var value = target.get('data-rokpad-tabsize'),
				siblings = target.getSiblings('[data-rokpad-tabsize]');

			if (!value) return true;

			siblings.removeClass('rokpad-checked');
			target.addClass('rokpad-checked');

			element.getElement('[data-rokpad-toggle=tabs] span').set('text', value);

			/* To possibly live-update tabs
			var text = this.editors[id].getValue(),
				tabSize = this.getTabSize(id),
				regexp = new RegExp('\n' + "".pad(tabSize), 'g');

			this.editors[id].setValue(text.replace(regexp, '\n' + "".pad(value)));*/

			/* To Reindent

			this.editors[id].setValue(style_html(this.editors[id].getValue(), {
				'indent_size': value,
				'indent_char': ' ',
				'max_char': 0,
				'unformatted': ['a', 'sub', 'sup', 'b', 'i', 'u']
			}));*/

			value = value.toInt() || 0;
			if (!value) return;

			this.editors[id].getEditor().getSession().setTabSize(value);
			this._hideDropDowns();
			this._store('tabSize', value);
			return this;
		},

		getTabSize: function(id){
			return this.editors[id].getEditor().getSession().getTabSize();
		},

		setUseSoftTabs: function(id, target, element){
			element = element || this.editors[id].wrapper;
			var restore = typeOf(target) == 'string' || typeOf(target) == 'number' ? target : null;
			target = typeOf(target) == 'element' ? target : element.getElement('[data-rokpad-softtabs]');

			var value = restore !== null ? restore : target.get('data-rokpad-softtabs');
			value = (!value || value == '0' ? 1 : 0);

			target[!value ? 'removeClass' : 'addClass']('rokpad-checked').set('data-rokpad-softtabs', value);

			this.editors[id].getEditor().getSession().setUseSoftTabs(!value ? 1 : 0);
			this._hideDropDowns();
			this._store('useSoftTabs', !value ? 1 : 0);
			return this;
		},

		getUseSoftTabs: function(id){
			return this.editors[id].getEditor().getSession().getUseSoftTabs();
		},

		toggleActionSettings: function(id, setting, value){
			var item = this.editors[id].wrapper.getElement('[data-rokpad-action-setting='+setting+']'),
				settings = this._getRange(id, this._retrieve('actionSettings')) || {},
				current = settings[setting];

			if (typeof current == 'undefined') {
				item[setting == 'wrap' ? 'removeClass' : 'addClass']('rok-button-unchecked');
				settings[setting] = (setting == 'wrap') ? true : false;
			} else {
				if (typeof value == 'undefined'){
					value = !!item.hasClass('rok-button-unchecked');
				}

				item[value === false ? 'addClass' : 'removeClass']('rok-button-unchecked');
				settings[setting] = value;
			}

			this._store('actionSettings', settings);
			this.editors[id].getEditor().$search.set(settings);
		},

		performAction: function(id, target, element){
			var settings = this._getRange(id, this._retrieve('actionSettings')),
				editor = this.editors[id].getEditor(),
				gotoLine = null;

			var method = target.getParent('[data-rokpad-action-method]');
			if (method){
				method = method.get('data-rokpad-action-method');
				switch(method){
					case 'goto':
						break;
					case 'find':
						settings.needle = target.get('value');
						this._store('actionSettings', settings);
						break;
					case 'replace':
						settings.replacement = target.get('value');
						this._store('actionSettings', settings);
				}
			}

			var action = target.get('data-rokpad-action');
			if (action){
				var session = editor.$search.getOptions().needle;
				if (session != settings.needle && (action == 'findNext' || action == 'findPrevious')){
					action = 'find';
				}

				switch(action){
					case 'goto':
						gotoLine = this.editors[id].wrapper.getElement('[data-rokpad-action-method=' + action + '] input').get('value').toInt();

						if (!isNaN(gotoLine)) editor.gotoLine(gotoLine, 0, true);

						break;
					case 'find':
						editor.find(settings.needle, settings, true);
						break;
					case 'findAll':
						editor.findAll(settings.needle, settings);
						break;
					case 'findNext':
						editor.findNext(settings, true);
						break;
					case 'findPrevious':
						editor.findPrevious(settings, true);
						break;
					case 'replace':
						editor.replace(settings.replacement, settings);
						break;
					case 'replaceAll':
						editor.replaceAll(settings.replacement, settings);
						break;
				}

				editor.focus();
			}

		},

		changeOptions: function(id, target, store){
			var value = target.get('tag') == 'select' || target.get('type') == 'text' ? target.get('value') : target.checked,
				property = target.get('data-rokpad-options'),
				setting = 'set' + property.camelCase().capitalize(),
				editor = this.editors[id].getEditor();

			switch(property){
				case 'theme':
				case 'font-size':
					this.editors[id][setting](value);
					break;
				case 'highlight-active-line':
				case 'show-invisibles':
				case 'highlight-selected-word':
				case 'fade-fold-widgets':
					editor[setting](value);
					break;
				case 'show-gutter':
				case 'show-print-margin':
					editor.renderer[setting](value);
					break;
				case 'fold-style':
					editor.getSession()[setting](value);
					editor.setShowFoldWidgets(value !== 'manual');
					break;
				case 'selection-style':
					editor[setting](value ? 'line' : 'text');
					break;
				case 'use-wrap-mode':
					var session = editor.getSession(),
						renderer = editor.renderer;

					switch(value){
						case 'off':
							session[setting](false);
							renderer.setPrintMarginColumn(80);
							break;
						case '40':
							session[setting](true);
							session.setWrapLimitRange(40, 40);
							renderer.setPrintMarginColumn(40);
							break;
						case '80':
							session[setting](true);
							session.setWrapLimitRange(80, 80);
							renderer.setPrintMarginColumn(80);
							break;
						case 'free':
							session[setting](true);
							session.setWrapLimitRange(null, null);
							renderer.setPrintMarginColumn(80);
							break;
					}

				break;
			}

			if (store) this._store(property, value);
		},

		fullscreen: function(event, id, element, target){
			var resizer, xtdButtons;

			if (element.retrieve('rokpad:states:fullscreen', false)){
				// back to normal state
				var styles = element.retrieve('rokpad:states:styles'),
					containerstyles = element.retrieve('rokpad:states:containerheight'),
					location = element.retrieve('rokpad:states:location'),
					doc = element.retrieve('rokpad:states:document'),
					scrollLocation = element.retrieve('rokpad:states:documentscroll'),

					xtdButtonsWrapper = document.getElement('#editor-xtd-buttons');

				xtdButtons = document.getElement('.btn-toolbar.rokpad-fullscreen');

				element.setStyles(styles).inject(location.element, location.position);
				if (xtdButtons) xtdButtons.removeClass('rokpad-fullscreen').inject(xtdButtonsWrapper);
				element.getElement('.rokpad-editor-container').setStyles(containerstyles);
				if (doc.html['overflow-y'] == 'scroll' || doc.html.overflow == 'auto') document.id(document.html).setStyle('overflow', doc.html.overflow);
				document.id(document.body).setStyle('overflow', doc.body);
				window.scrollTo(scrollLocation.x, scrollLocation.y);

				this.editors[id].getEditor().resize.delay(1, this.editors[id].getEditor());

				this.detachResize();

				element.removeClass('rokpad-cantresize');
				resizer = element.getElement('[data-rokpad-container]').retrieve('resizer');
				resizer.attach();

				target.getElement('i').className = 'rokpad-icon-fullscreen';
				element.store('rokpad:states:fullscreen', false);
			} else {
				// full screen mode
				xtdButtons = element.getNext('#editor-xtd-buttons .btn-toolbar');

				element.store('rokpad:states:location', this._getLocation(element));
				element.store('rokpad:states:styles', element.getStyles('position', 'left', 'top', 'right', 'bottom', 'z-index', 'height'));
				element.store('rokpad:states:containerheight', element.getElement('.rokpad-editor-container').getStyles('height', 'min-height'));
				element.store('rokpad:states:document', {html: document.id(document.html).getStyles('overflow', 'overflow-y', 'overflow-x'), body: document.id(document.body).getStyle('overflow')});
				element.store('rokpad:states:documentscroll', window.getScroll());


				element.inject(document.id(document.body));
				if (xtdButtons) xtdButtons.inject(element, 'after');

				var height = this._calculateHeight(element),
					xtdButtonsHeight = xtdButtons ? (xtdButtons.getSize().y + 13) || 0 : 0;

				element.setStyles({
					position: 'fixed', left: 0, top: 0, right: 0, bottom: xtdButtonsHeight, 'z-index': 10000, height: 'auto'
				});
				if (xtdButtons) xtdButtons.addClass('rokpad-fullscreen');

				element.getElement('.rokpad-editor-container').setStyles({height: window.getSize().y - height - xtdButtonsHeight, 'min-height': '0'});
				if (document.id(document.html).getStyle('overflow-y') == 'scroll' || document.id(document.html).getStyle('overflow') == 'auto') document.id(document.html).setStyle('overflow', 'hidden');
				document.id(document.body).setStyle('overflow', 'hidden');

				this.editors[id].getEditor().resize.delay(5, this.editors[id].getEditor());
/*

			var resize = window.retrieve('rokpad:events:resize', function(){
				var xtdButtons = element.getNext('#editor-xtd-buttons .btn-toolbar'),
					height = this._calculateHeight(element),
					xtdButtonsHeight = xtdButtons.getSize().y + 13;

				element.getElement('.rokpad-editor-container').setStyle('height', window.getSize().y - height - xtdButtonsHeight);
				this.editors[id].getEditor().resize.delay(1, this.editors[id].getEditor());
			}.bind(this));
*/
				this.attachResize(id, element);

				element.addClass('rokpad-cantresize');
				resizer = element.getElement('[data-rokpad-container]').retrieve('resizer');
				resizer.detach();

				target.getElement('i').className = 'rokpad-icon-windowed';
				element.store('rokpad:states:fullscreen', true);
			}

			target.retrieve('twipsy').hide();
			this.editors[id].getEditor().focus();
		},

		insertShortCode: function(event, id, element, target){
			var shortcode = target.get('data-rokpad-shortcode').replace(/'/g, '"'),
				editor = this.editors[id].getEditor(),
				session = editor.getSession(),
				selection, string, newRanges = [], cursorPosition = [];

			if (!shortcode) return;

			if (RokPadDevice == 'portable'){
				shortcode = shortcode.substitute({cur: '', data: '', n: "\n", t: "\t"});
				insertAtCursor(document.id(id), shortcode);
				document.id(id).focus();
				return;
			}

			ranges = Array.from(editor.getSelection()[editor.getSelection().inMultiSelectMode ? 'getAllRanges' : 'getRange']());

			editor.clearSelection();

			ranges.each(function(range, i){
				selection = session.getTextRange(range);
				string = shortcode.substitute({cur: '{cur}', data: selection, n: "\n", t: "\t"});
				cursorPosition = [];

				(string.split('\n')).each(function(line, i){
					var lineIndexes = line.indexOfAll('{cur}');

					if (lineIndexes.length){
						lineIndexes.each(function(lineIndex, j){
							cursorPosition.push({
								row: range.start.row + i,
								column: (!i ? range.start.column : 0) + (lineIndex - (5 * j))
							});
						});
					}
				}, this);

				string = string.substitute({cur: ''});

				session.replace(range, string);

				var currentSelection = editor.getSelection();
				cursorPosition.each(function(position, i){
					var rangeClone = range.clone();

					rangeClone.setStart(position);
					rangeClone.setEnd(position);
					rangeClone.cursor = position;

					if (currentSelection.inMultiSelectMode || cursorPosition.length > 1) currentSelection.addRange(rangeClone);
					else editor.moveCursorToPosition(position);
				});

			}, this);

			// clear extra cursors not needed
			var allRanges = editor.getSelection().getAllRanges(),
				expected = cursorPosition.length * ranges.length;

			for (i = cursorPosition.length, l = allRanges.length; i < l; i+=cursorPosition.length + 1){
				editor.getSelection().substractPoint(allRanges[i].cursor);
			}

			editor.focus();
		},

		save: function(){
			if (!this._canSave()) return false;

			if (!document.retrieve('rokpad:ajax:save', false)){
				var request = new Request({
					url: document.getElement('form[name=adminForm]').get('action'),
					onRequest: this.saveRequest,
					onSuccess: this.saveSuccess
				});
				document.store('rokpad:ajax:save', request);
			}

			var task = this._getTask(),
				form = document.getElement('form[name=adminForm]'),
				ajax = document.retrieve('rokpad:ajax:save');

			form.task.value = task;
			ajax.cancel().post(form.toQueryString());
		},

		saveRequest: function(){
			document.getElements('[data-rokpad-save] i').addClass('spinner');
		},

		saveSuccess: function(response){
			var dummy = new Element('div').set('html', response),
				form = document.getElement('form[name=adminForm'),
				inputs = dummy.getElements('form[name=adminForm] input, form[name=adminForm] select, form[name=adminForm] textarea'),
				input, type, action, request;

			// update inputs
			for (var i = inputs.length - 1; i >= 0; i--) {
				type = inputs[i].get('type');
				input = document.id(inputs[i].get('id')) || document.getElement(inputs[i].get('tag') + '[name='+inputs[i].get('name')+']');

				if (input) input.set('value', inputs[i].get('value'));
				else if (inputs[i].get('name')) form.adopt(inputs[i].setStyle('display', 'none'));
			}

			// update form action
			action = dummy.getElement('form[name=adminForm]');
			action = action ? action.get('action') : false;

			if (action){
				form.set('action', action != 'index.php' ? action : location.href);
				request = document.retrieve('rokpad:ajax:save');
				request.setOptions({url: form.get('action')});
			}

			document.getElements('[data-rokpad-savedate]').set('text', (new Date()).format('%d %b, %T'));
			document.getElements('[data-rokpad-save] i').removeClass('spinner');
		},

		/* private methods */
		_getACE: function(element){
			var id = element.get('data-rokpad-editor'),
				container = document.id(id + '-rokpad-editor'),
				textarea = element.getElement('[data-rokpad-original]');

			element.setStyle('height', 'auto');
			return new RokPadACE(id, {wrapper: element, container: container, id: id});
		},

		_populateOptions: function(id, element){
			var option;
			Object.each(RokPadThemes, function(value, key){
				option = element.getElement('[data-rokpad-options=' + key + ']');
				var type = typeOf(value);

				(type == 'object' ? Object : Array).each(value, function(data, name){
					//console.log(RokPadDefaultSettings[key], data);
					option.adopt(new Element('option[value='+data+']').set('text', (type == 'object' ? name : data)));
				});

			});
		},

		_restoreSettings: function(id){
			this.setMode(id, (this._retrieve('mode') || 'html'));
			this.setUseSoftTabs(id, this._retrieve('useSoftTabs')); // 1 = tabs | 0 = spaces
			this.setTabSize(id, this._retrieve('tabSize') || 4);
			this[this._retrieve('showActionbar', false) ? '_showActionbar' : '_hideActionbar'](id);
			this._setActionbar(id, this._retrieve('actionbarMode', 'find'), false);

			var actionSettings = this._getRange(id, this._retrieve('actionSettings'));
			if (!actionSettings){
				this._store('actionSettings', {});
				actionSettings = {};
			}

			['regExp', 'caseSensitive', 'wholeWord', 'backwards', 'wrap', 'scope'].each(function(setting){
				var value = (setting == 'wrap') ? true : false;
				this.toggleActionSettings(id, setting, actionSettings[setting] ? actionSettings[setting] : value);
			}, this);
		},

		_restoreOptions: function(id){
			var defaults = RokPadDefaultSettings,
				wrapper = this.editors[id].wrapper,
				element, setting;

			Object.each(defaults, function(value, key){
				element = wrapper.getElement('[data-rokpad-options=' + key + ']');

				if (element){
					value = (value == '0' || value == '1' || element.get('type') == 'text') ? value.toInt() : value;
					setting = this._retrieve(key);
					element.set((element.get('type') == 'checkbox' ? 'checked' : 'value'), typeof setting != 'undefined' ? setting : value);
					this.changeOptions(id, element);
				}
			}, this);
		},

		_extrasFixes: function(id, element){
			// ZOO
			// ; add new textarea / remove texarea require refresh of rokpad
			var hiddenTextAreas = element.getParent('.repeatable-list .repeatable-element.hidden'),
				addLink = element.getElement('! > .repeatable-list ~ p.add a'),
				removeLink = element.getElements('! > .repeatable-list .repeatable-element > .delete');

			if (hiddenTextAreas && addLink){
				if (!addLink.retrieve('rokpad:zoo:add', false)){
					addLink.store('rokpad:zoo:add', true);
					addLink.addEvent('click', function(){
						var child = element.getParent('.repeatable-list').getElements('.repeatable-element:not(.hidden)').getLast(),
							rokpad = child.getElement('[data-rokpad-editor]'),
							id = rokpad.get('data-rokpad-editor');

						if (!this.editors[id].textarea.get('name')) this.editors[id].textarea.set('name', this.editors[id].textarea.get('id'));

						this.editors[id].getEditor().resize();
						this.editors[id].getEditor().focus.delay(10, this.editors[id].getEditor());
					}.bind(this));
				}
			}

			if (hiddenTextAreas && removeLink.length){
				removeLink.addEvent('click', function(){
					var child = element.getParent('.repeatable-list').getElements('.repeatable-element:not(.hidden)').getLast(),
						rokpad = child.getElement('[data-rokpad-editor]'),
						id = rokpad.get('data-rokpad-editor');

					this.editors[id].textarea.set('name', null);
				}.bind(this));
			}

			// JEvents
			// ; tabs click requires refresh of rokpad
			var jevents = document.getElement('.jevconfig'),
				jevents_tabs = (jevents) ? jevents.getElements('#configs.tabs > dt') : [];
			if (jevents && jevents_tabs.length){
				jevents_tabs.addEvent('click', function(){
					this.editors[id].getEditor().resize.delay(5, this.editors[id].getEditor());
				}.bind(this));
			}

			// BreezingForms
			// ; the editor in the popup is not inside a form
			// ; requires some special save function override
			if(window.parent && typeof saveText != 'undefined' && !document.getElement('form')){
				this.save = function(){
					saveText();
					window.parent.SqueezeBox.close();
				};

				this._stopAutoSave();
				this._startAutoSave = function(){
					return this._stopAutoSave();
				};
			}
		},

		_rearrangeHeight: function(id, value){
			var wrapper = this.editors[id].wrapper,
				container = wrapper.getElement('[data-rokpad-container]'),
				actionbar = wrapper.getElement('[data-rokpad-actionbar]'),
				height = actionbar.getSize().y - Math.abs(value || 0);

			container.setStyle('height', Math.max(250, (container.getSize().y || container.getStyle('height')) - 2 - height));
			this.editors[id].getEditor().resize();
		},

		_showActionbar: function(id, force){
			var wrapper = this.editors[id].wrapper,
				container = wrapper.getElement('[data-rokpad-container]'),
				actionbar = wrapper.getElement('[data-rokpad-actionbar]'),
				input = wrapper.getElement('[data-rokpad-action-method=find] input'),
				inputReplacement = wrapper.getElement('[data-rokpad-action-method=replace] input');
				inputGoto = wrapper.getElement('[data-rokpad-action-method=goto] input');

			if (!actionbar.retrieve('rokpad:actionbar:shown', false) || force){
				actionbar.setStyle('display', 'block');
				/*height = actionbar.getSize().y;
				container.setStyle('height', container.getSize().y - height - 2);
				this.editors[id].getEditor().resize();*/
				container.setStyle('height', this._retrieve('height') || container.getStyle('min-height').toInt());
				this._store('showActionbar', true);
				input.set('value', this._retrieve('actionSettings').needle);
				inputReplacement.set('value', this._retrieve('actionSettings').replacement);
				inputGoto.set('value', '');
				this._rearrangeHeight(id);
				actionbar.store('rokpad:actionbar:shown', true);
			}
		},

		_hideActionbar: function(id){
			var wrapper = this.editors[id].wrapper,
				container = wrapper.getElement('[data-rokpad-container]'),
				actionbar = wrapper.getElement('[data-rokpad-actionbar]'),
				height = 0;
			if (actionbar.retrieve('rokpad:actionbar:shown', true)){
				height = actionbar.getSize().y;
				container.setStyle('height', container.getSize().y + height - 2);
				this.editors[id].getEditor().resize();
				actionbar.setStyle('display', 'none');
				this._store('showActionbar', false);
				actionbar.store('rokpad:actionbar:shown', false);
			}
		},

		_setActionbar: function(id, mode, rearrange){
			var wrapper = this.editors[id].wrapper,
				actionbar = wrapper.getElement('[data-rokpad-actionbar]'),
				row2 = actionbar.getElement('.rok-input-row-2'),
				height1 = 0,
				height2 = 0;

			switch(mode){
				case 'goto':
					height1 = actionbar.getSize().y;
					row2.setStyle('display', 'none');
					height2 = actionbar.getSize().y;
					wrapper.getElement('.rokpad-column-1').setStyle('display', 'none');
					wrapper.getElement('[data-rokpad-action-method=goto]').setStyle('display', 'block');
					wrapper.getElement('[data-rokpad-action=goto]').setStyle('display', 'inline-block');
					wrapper.getElement('[data-rokpad-action-method=find]').setStyle('display', 'none');
					wrapper.getElement('.rokpad-column-3 .rok-input-row-2').setStyle('display', 'none');
					wrapper.getElement('[data-rokpad-action=find]').setStyle('width', 'auto');
					wrapper.getElements('[data-rokpad-action=findNext]').getParent('.rok-buttons-group').setStyle('display', 'none');
					wrapper.getElements('[data-rokpad-action=find], [data-rokpad-action=findNext], [data-rokpad-action=findPrevious], [data-rokpad-action=findAll]').setStyle('display', 'none');
					this._store('actionbarMode', 'goto');
					break;
				case 'find':
					height1 = actionbar.getSize().y;
					row2.setStyle('display', 'none');
					height2 = actionbar.getSize().y;
					wrapper.getElement('.rokpad-column-1').setStyle('display', 'table-cell');
					wrapper.getElements('[data-rokpad-action-method=goto], [data-rokpad-action=goto]').setStyle('display', 'none');
					wrapper.getElement('[data-rokpad-action-method=find]').setStyle('display', 'block');
					wrapper.getElement('.rokpad-column-3 .rok-input-row-2').setStyle('display', 'none');
					wrapper.getElement('[data-rokpad-action=find]').setStyle('width', 'auto');
					wrapper.getElements('[data-rokpad-action=findNext]').getParent('.rok-buttons-group').setStyle('display', 'inline-block');
					wrapper.getElements('[data-rokpad-action=find], [data-rokpad-action=findNext], [data-rokpad-action=findPrevious], [data-rokpad-action=findAll]').setStyle('display', 'inline-block');
					this._store('actionbarMode', 'find');
					break;
				case 'replace':
					height1 = actionbar.getSize().y;
					row2.setStyle('display', 'block');
					height2 = actionbar.getSize().y;
					wrapper.getElement('.rokpad-column-1').setStyle('display', 'table-cell');
					wrapper.getElements('[data-rokpad-action-method=goto], [data-rokpad-action=goto]').setStyle('display', 'none');
					wrapper.getElement('[data-rokpad-action-method=find]').setStyle('display', 'block');
					wrapper.getElement('.rokpad-column-3 .rok-input-row-2').setStyle('display', 'block');
					wrapper.getElement('[data-rokpad-action=find], [data-rokpad-action=findAll]').setStyle('display', 'inline-block');
					wrapper.getElements('[data-rokpad-action=findNext], [data-rokpad-action=findPrevious]').setStyle('display', 'none');
					wrapper.getElement('[data-rokpad-action=find]').setStyle('width', wrapper.getElement('[data-rokpad-action=replace]').getComputedSize().width || 47);
					this._store('actionbarMode', 'replace');
			}

			if (rearrange !== false) this._rearrangeHeight(id, height1);


			if (wrapper.retrieve('rokpad:states:fullscreen')){
				window.fireEvent('resize', null, 1);
			}
		},

		_store: function(key, value){
			if (!$.jStorage.get('rokpad')) $.jStorage.set('rokpad', {});
			$.jStorage.setTTL('rokpad', 0);

			var storage = $.jStorage.get('rokpad');
			storage[key] = value;

			return $.jStorage.set('rokpad', storage);
		},

		_retrieve: function(key){
			if (!$.jStorage.get('rokpad')) $.jStorage.set('rokpad', {});
			$.jStorage.setTTL('rokpad', 0);

			return $.jStorage.get('rokpad')[key];
		},

		_getRange: function(id, settings){
			if (!settings) return settings;

			var selectionRange = this.editors[id].getEditor().getSelectionRange(),
				selection = this.editors[id].getEditor().getSelection();

			if (selection.isEmpty()) settings.range = null;
			if (settings.scope && !selection.isEmpty()){
				if (selectionRange.start.row == selectionRange.end.row && Math.abs(selectionRange.start.column - selectionRange.end.column) == settings.needle.length) return settings;
				settings.range = selectionRange;
			}
			return settings;
		},

		_getLocation: function(element){
			var location = {};

			if (!element.getSiblings().length) location = {element: element.getParent(), position: 'inside'};
			else if (element.getPrevious()) location = {element: element.getPrevious(), position: 'after'};
			else if (element.getNext()) location = {element: element.getNext(), position: 'before'};

			return location;
		},

		_calculateHeight: function(element){
			var height = 0;
			element.getElements('> :not(.rokpad-editor-container)').each(function(child){
				if (child.getStyle('display') != 'none') height += child.getSize().y;
			});

			return height;
		},

		_hideDropDowns: function(event, target){
			target = target || (event && event.target) || null;
			if (!target || typeOf(target) == 'document') return document.getElements('[data-rokpad-dropdown], [data-rokpad-popover]').setStyle('display', 'none');
			else {
				if (target &&
					(target.get('data-rokpad-dropdown') || target.getParent('[data-rokpad-dropdown]') ||
					target.get('data-rokpad-popover') || target.getParent('[data-rokpad-popover]') ||
					target.get('data-rokpad-toggle') || target.getParent('[data-rokpad-toggle]'))
					) return true;

				document.getElements('[data-rokpad-dropdown], [data-rokpad-popover]').setStyle('display', 'none');
			}
		},

		_canSave: function(){
			if (!document.retrieve('rokpad:document:submitform', false)){
				document.store('rokpad:document:submitform', Joomla.submitform);
			}

			var passed = true,
				_submitform = document.retrieve('rokpad:document:submitform'),
				task = this._getTask();

			submitform = Joomla.submitform = function(pressbutton){
				//if (passed) this.save(pressbutton);
				return false;
			};

			var required = document.getElements('form input.required, form select.required, .message-name !~ input, .message-name !~ select, #k2AdminContainer #title, #k2AdminContainer #catid, #k2AdminContainer #name');

			// ZOO
			if (document.getElement('[data-zooversion]') && required.length){
				var siblings = new Elements(required.getSiblings('.message-name').flatten()),
					nameElement = document.getElement('input[name="name"]');

				if (nameElement && nameElement.get('value') === '') passed = false;
				if (!passed){
					nameElement.focus();
					siblings.setStyle('display', 'block');
				}
			}

			// K2
			if (typeof $K2 !== 'undefined' && required.length){
				var values = required.get('value');
				if (values.contains('0') || values.contains('')) passed = false;
			}

			// Joomla!
			if (document.formvalidator) passed = document.formvalidator.isValid(document.getElement('[name=adminForm]'));

			Joomla.submitbutton(task);
			Joomla.submitform = _submitform;
			submitform = _submitform;

			return passed;
		},

		_getTask: function(){
			var toolbar = document.getElement('#toolbar-apply a') || document.getElement('#toolbar-apply button') || document.getElement('[name=adminForm] .btn-toolbar .btn-primary') || document.getElement('form[name=adminForm] .formelm-buttons > button'),
				task = (toolbar && toolbar.get('onclick') || '').toString().replace(/.*submitbutton\(['|"](.*)['|"]\).*/g, "$1");

			return task || 'apply';
		},

		_startAutoSave: function(){
			var autosave = this._retrieve('autosave-enabled'),
				time = this._retrieve('autosave-time') || 5;

			time = (isNaN(time) ? 5 : time.toInt()) * 60 * 1000;

			this._stopAutoSave();

			if (autosave){
				this.autosaveTimer = function(){
					if (this._retrieve('autosave-enabled')){
						this.save();
						this._startAutoSave();
					}
				}.delay(time, this);
			}
		},

		_stopAutoSave: function(){
			clearTimeout(this.autosaveTimer);
		}

	});

})());
/**
* @version   $Id: rokpad.js 18685 2014-02-11 05:14:08Z djamil $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2018 RocketTheme, LLC
* @license   http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
*/

((function(){
	var ACE = new Class({

		Implements: [Options, Events],

		options:{
			wrapper: null,
			container: null,
			id: null,

			onChange: function(editor){
				var undomanager = this.editor.getSession().getUndoManager();

				['undo', 'redo'].each(function(type){
					var elements = '[data-rokpad-' + type + ']',
						classname = '[class*=rok-button-disabled]',
						not = ':not('+classname+')';

					if (undomanager['has' + type.capitalize()]()) this.wrapper.getElements(elements + classname).removeClass('rok-button-disabled');
					else this.wrapper.getElements(elements + not).addClass('rok-button-disabled');

				}, this);
			}
		},

		initialize: function(editor, options){
			this.setOptions(options);

			this.wrapper = options.wrapper ? document.id(options.wrapper) || document.getElement(options.wrapper) || null : null;
			this.container = options.container ? document.id(options.container) || document.getElement(options.container) || null : null;
			this.textarea = document.id(editor) || document.getElement(editor) || null;

			if (!this.container) throw new Error('Container for injecting ACE "'+options.container+'" not found in the DOM.');
			if (!this.wrapper) throw new Error('Wrapper "'+options.wrapper+'" not found in the DOM.');
			if (!this.textarea) throw new Error('Original textarea "'+options.textarea+'" not found in the DOM.');

			if (this.textarea.getParent('form')) this.textarea.inject(this.textarea.getParent('form'));
			if (!matchMedia('(max-device-width: 1024px)').matches){
				//this.textarea.setStyles({visibility: 'hidden', zIndex: -1, position: 'absolute', 'top': -20000, opacity: 0});
				this.textarea.setStyle('display', 'none');
			}
			this.editor = ace.edit(this.container);

			// mirrors textarea content
			this.insert(this.textarea.get('value'));
			// reset undomanager stack
			var undoManager = this.editor.getSession().getUndoManager();
			undoManager.reset.delay(1, undoManager);

			// split
			/*var Split = ace.require("ace/split").Split,
				split = new Split(this.container, this.editor.getTheme() || 'ace/theme/fluidvision', 1);

			//this.editor.env.editor = split.getEditor(0);
			split.on("focus", function(editor) {
				this.editor.env.editor = editor;
			}.bind(this));
			this.editor.env.split = split;*/

			this.attachEvents();

			this.container.getElement('.ace_layer.ace_text-layer').set('contenteditable', '');

			return this;
		},

		getEditor: function(){
			return this.editor;
		},

		attachEvents: function(){
			var options = this.options;

			this.textarea.addEvent('blur', function(){
				this.setValue(this.textarea.get('value'));
			}.bind(this));

			this.editor.on('blur', function(){
				this.textarea.set('value', this.getValue());
			}.bind(this));

			this.editor.on('change', function(editor){
				this.fireEvent('change', editor, 1);
			}.bind(this));

			// adds the shadow to the gutter when scrolling
			document.id(this.editor.renderer.scroller).addEvents({
				'click': function(){
					this.editor.blur();
					this.editor.focus();
				}.bind(this),
				'scroll': function(){
					var renderer = this.editor.renderer,
						gutter = renderer.$gutter,
						scroller = renderer.scroller,
						scrollLeft = scroller.scrollLeft;

					document.id(gutter)[scrollLeft < 5 ? 'removeClass' : 'addClass']('horscroll');
				}.bind(this)
			});

			this.editor.commands.addCommand({
				name: 'find',
				bindKey: {
					win: 'Ctrl-F',
					mac: 'Command-F|Command-Alt-F'
				},
				exec: function(editor) {
					var input = this.wrapper.getElement('[data-rokpad-action-method=find] input');

					RokPad._setActionbar(options.id, 'find');
					RokPad._showActionbar(options.id);
					input.select(); input.focus();
				}.bind(this)
			});

			this.editor.commands.addCommand({
				name: 'findreplace',
				bindKey: {
					win: 'Ctrl-Shift-F',
					mac: 'Command-Shift-F'
				},
				exec: function(editor) {
					var input = this.wrapper.getElement('[data-rokpad-action-method=find] input');

					RokPad._setActionbar(options.id, 'replace');
					RokPad._showActionbar(options.id);
					input.select(); input.focus();
				}.bind(this)
			});

			this.editor.commands.addCommand({
				name: 'gotoline',
				bindKey: {
					win: 'Ctrl-L',
					mac: 'Command-L'
				},
				exec: function(editor) {
					var input = this.wrapper.getElement('[data-rokpad-action-method=goto] input');

					RokPad._setActionbar(options.id, 'goto');
					RokPad._showActionbar(options.id);
					input.select(); input.focus();
				}.bind(this)
			});

			this.editor.commands.addCommand({
				name: 'findnext',
				bindKey: {
					win: 'Ctrl-K',
					mac: 'Command-G'
				},
				exec: function(editor) {
					var settings = RokPad._getRange(options.id, RokPad._retrieve('actionSettings'));
					editor.findNext(settings);
				}.bind(this)
			});

			this.editor.commands.addCommand({
				name: 'findprevious',
				bindKey: {
					win: 'Ctrl-Shift-K',
					mac: 'Command-Shift-G'
				},
				exec: function(editor) {
					var settings = RokPad._getRange(options.id, RokPad._retrieve('actionSettings'));
					editor.findPrevious(settings);
				}.bind(this)
			});

			this.editor.commands.addCommand({
				name: 'escape',
				bindKey: {
					win: 'Esc',
					mac: 'Esc'
				},
				exec: function(editor) {
					RokPad._hideActionbar(options.id);
				}
			});

			this.editor.commands.addCommand({
				name: 'save',
				bindKey: {
					win: 'Ctrl-S',
					mac: 'Command-S'
				},
				exec: function(editor) {
					RokPad.save();
				}
			});

			this.editor.commands.addCommand({
				name: 'findselection',
				bindKey: {
					win: 'Ctrl-E',
					mac: 'Command-E'
				},
				exec: function(editor) {
					var input = this.wrapper.getElement('[data-rokpad-action-method=find] input'),
						action = this.wrapper.getElement('[data-rokpad-action=find]'),
						selection, settings = RokPad._retrieve('actionSettings');

					editor.selection.selectWord();

					selection = RokPad.editors[options.id].getSelection();
					RokPad._setActionbar(options.id, 'find');
					RokPad._showActionbar(options.id);
					input.set('value', selection);
					settings['needle'] = selection;

					RokPad._store('actionSettings', settings);

					input.select(); input.focus();
					action.fireEvent('click');
				}.bind(this)
			});
		},

		insert: function(text){
			this.editor.insert(text);

			return this;
		},

		getValue: function(){
			return this.editor.getSession().getValue();
		},

		setValue: function(value){
			this.editor.getSession().setValue(value);
			return this;
		},

		getSelection: function(){
			return this.editor.getSession().getTextRange(this.editor.getSelectionRange());
		},

		setTheme: function(theme){
			var editor = this.editor;
			if (!theme) theme = 'github';

			if (!theme.match(/^ace\/theme/)) theme = 'ace/theme/' + theme;
			editor.setTheme(theme);
			return this;
		},

		setFontSize: function(fontsize){
			var editor = this.editor;
			if (!fontsize) throw new Error('Second argument "fontsize" of ACE::setFontSize must be passed in.');

			if (typeof fontsize == 'number') fontsize += 'px';
			editor.setFontSize(fontsize);
			return this;
		},

		setMode: function(mode){
			var editor = this.editor;
			if (!mode) throw new Error('Second argument "mode" of ACE::setMode must be passed in.');

			editor.getSession().setMode(mode);
			return this;
		},

		setUseSoftTabs: function(value){
			var editor = this.editor;

			editor.getSession().setUseSoftTabs(value);
			return this;
		},

		replaceSelection: function(text){
			var editor = this.editor,
				session = editor.getSession(),
				selection, ranges, string, newRanges = [], cursorPosition = [];

			ranges = Array.from(editor.getSelection()[editor.getSelection().inMultiSelectMode ? 'getAllRanges' : 'getRange']());

			ranges.each(function(range, i){
				selection = session.getTextRange(range);
				string = selection ? text.replace(/\{text\}/g, selection) : text;
				session.replace(range, string);
			}, this);
		}
	});

	this.RokPadACE = ACE;
})());
/**
* @version   $Id: rokpad.js 18685 2014-02-11 05:14:08Z djamil $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2018 RocketTheme, LLC
* @license   http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
*/

((function(){
	if (typeof this.RokPadData == 'undefined') this.RokPadData = {};

	this.RokPadData.insertion = {};
	var setRokPadInsertion = function(mq){
		if (mq.matches) RokPadDevice = 'portable';
		else RokPadDevice = 'desktop';

		// Selection/Insertion
		RokPadData.insertion = {
			onSave: function(id){
				if (mq.matches) return;
				document.id(id).set('value', RokPad.editors[id].getValue());
			},
			onGetContent: function(id){
				if (mq.matches) return document.id(id).get('value');
				else return RokPad.editors[id].getValue();
			},
			onSetContent: function(id, content){
				if (mq.matches) document.id(id).set('value', content);
				else RokPad.editors[id].setValue(content);
			},
			onGetInsertMethod: function(text, editor){
				if (mq.matches) insertAtCursor(document.id(editor), text);
				else {
					RokPad.editors[editor].replaceSelection(text);
					RokPad.editors[editor].getEditor().focus();
				}
			}
		};
	};

	// media query event handler
	if (respond.mediaQueriesSupported && !Browser.ie8) {
		var mq = window.matchMedia("only screen and (max-device-width: 1024px)");
		if (mq.addListener) mq.addListener(setRokPadInsertion);
		else {
			window.addEvent('resize', function(){
				if (this.getSize().x <= 1024) mq = {matches: true};
				else mq = {matches: false};
				setRokPadInsertion(mq);
			});
		}

		setRokPadInsertion(mq);
	} else {
		if (Browser.ie8) setRokPadInsertion({matches: true});
		else setRokPadInsertion({});
	}

	// implement insertAtCursor
	if (typeof insertAtCursor !== 'function'){
		this.insertAtCursor = function(myField, myValue) {
			if (document.selection) {
				// IE support
				myField.focus();
				sel = document.selection.createRange();
				sel.text = myValue;
			} else if (myField.selectionStart || myField.selectionStart == '0') {
				// MOZILLA/NETSCAPE support
				var startPos = myField.selectionStart;
				var endPos = myField.selectionEnd;
				myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);
			} else {
				myField.value += myValue;
			}
		};
	}

	// Reverting MooTools bind to latest version due to Joomla! MooTools compat version
	Function.implement({
		bind: function(that){
			var self = this,
				args = arguments.length > 1 ? Array.slice(arguments, 1) : null,
				F = function(){};

			var bound = function(){
				var context = that, length = arguments.length;
				if (this instanceof bound){
					F.prototype = self.prototype;
					context = new F();
				}
				var result = (!args && !length) ? self.call(context) : self.apply(context, args && length ? args.concat(Array.slice(arguments)) : args || arguments);
				return context == that ? result : context;
			};
			return bound;
		}
	});

	String.implement({
		indexOfAll: function(search){
			var regexp = new RegExp(search, 'g');
			var match, matches = [];

			while ((match = regexp.exec(this)) !== null) {
				matches.push(match.index);
			}

			return matches;
		}
	});

	Object.merge(Element.NativeEvents, {
		dragstart: 2, drag: 2, dragenter: 2, dragleave: 2, dragover: 2, drop: 2, dragend: 2 // drag and drop
	});

	String.implement({
		substitute: function(object, regexp){
			return String(this).replace(regexp || (/\\?\{([^{}]+)\}/g), function(match, name){
				if (match.charAt(0) == '\\') return match.slice(1);
				return (object[name] != null) ? object[name] : match;
			});
		}
	});

	RokPadData.modesList = [];
	RokPadData.modesByName = {
		c9search:   ["C9Search"     , "c9search_results"],
		coffee:     ["CoffeeScript" , "coffee|^Cakefile"],
		coldfusion: ["ColdFusion"   , "cfm"],
		csharp:     ["C#"           , "cs"],
		css:        ["CSS"          , "css"],
		diff:       ["Diff"         , "diff|patch"],
		golang:     ["Go"           , "go"],
		groovy:     ["Groovy"       , "groovy"],
		haxe:       ["haXe"         , "hx"],
		html:       ["HTML"         , "htm|html|xhtml"],
		c_cpp:      ["C/C++"        , "c|cc|cpp|cxx|h|hh|hpp"],
		clojure:    ["Clojure"      , "clj"],
		java:       ["Java"         , "java"],
		javascript: ["JavaScript"   , "js"],
		json:       ["JSON"         , "json"],
		jsx:        ["JSX"          , "jsx"],
		latex:      ["LaTeX"        , "latex|tex|ltx|bib"],
		less:       ["LESS"         , "less"],
		liquid:     ["Liquid"       , "liquid"],
		lua:        ["Lua"          , "lua"],
		luapage:    ["LuaPage"      , "lp"], // http://keplerproject.github.com/cgilua/manual.html#templates
		markdown:   ["Markdown"     , "md|markdown"],
		ocaml:      ["OCaml"        , "ml|mli"],
		perl:       ["Perl"         , "pl|pm"],
		pgsql:      ["pgSQL"        , "pgsql"],
		php:        ["PHP"          , "php|phtml"],
		powershell: ["Powershell"   , "ps1"],
		python:     ["Python"       , "py"],
		ruby:       ["Ruby"         , "ru|gemspec|rake|rb"],
		scad:       ["OpenSCAD"     , "scad"],
		scala:      ["Scala"        , "scala"],
		scss:       ["SCSS"         , "scss|sass"],
		sh:         ["SH"           , "sh|bash|bat"],
		sql:        ["SQL"          , "sql"],
		svg:        ["SVG"          , "svg"],
		text:       ["Text"         , "txt"],
		textile:    ["Textile"      , "textile"],
		xml:        ["XML"          , "xml|rdf|rss|wsdl|xslt|atom|mathml|mml|xul|xbl"],
		xquery:     ["XQuery"       , "xq"],
		yaml:       ["YAML"         , "yaml"]
	};

	for (var name in RokPadData.modesByName) {
		var mode = RokPadData.modesByName[name];
		mode.push(name);
		RokPadData.modesList.push(mode);
	}

	window.addEvent('domready', function(){
		// implementing tips
		document.getElements('.rokpad-tip').twipsy({placement: 'left', offset: 5});

		// setting up keyboard shortcuts documentation
		var platform = Browser.Platform.mac ? 'mac' : 'win';
		document.getElements('.rokpad-keyboard-' + platform).setStyle('display', 'block');
		document.getElements('.rokpad-kbd-' + platform).setStyle('display', 'inline-block');

		this.RokPad = new RokPadClass();

		// sorry but ie8 cant handle this
		if (Browser.ie8) document.getElements('.rokpad-editor-wrapper').addClass('rokpad-ie8');
	}.bind(this));

})());
