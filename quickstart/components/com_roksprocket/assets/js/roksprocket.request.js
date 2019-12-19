/*!
 * @version   $Id: roksprocket.request.js 10889 2013-05-30 07:48:35Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2019 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
((function(){

	if (typeof this.RokSprocket == 'undefined') this.RokSprocket = {};
	else Object.merge(this.RokSprocket, {Request: null});

var defined = function(value){
	return value != null;
};

var hasOwnProperty = Object.prototype.hasOwnProperty;

Object.extend({

	getFromPath: function(source, parts){
		if (typeof parts == 'string') parts = parts.split('.');
		for (var i = 0, l = parts.length; i < l; i++){
			if (hasOwnProperty.call(source, parts[i])) source = source[parts[i]];
			else return null;
		}
		return source;
	},

	cleanValues: function(object, method){
		method = method || defined;
		for (var key in object) if (!method(object[key])){
			delete object[key];
		}
		return object;
	},

	erase: function(object, key){
		if (hasOwnProperty.call(object, key)) delete object[key];
		return object;
	},

	run: function(object){
		var args = Array.slice(arguments, 1);
		for (var key in object) if (object[key].apply){
			object[key].apply(object, args);
		}
		return object;
	}

});

var empty = function(){},
	progressSupport = ('onprogress' in new Browser.Request()),
	Request = new Class({
		Extends: this.Request,

		options: {
			method: 'post',
			model: '',
			model_action: '',
			params: {}
		},

		initialize: function(options){
			this.options.url = RokSprocket.AjaxURL.replace(/&amp;/g, '&');

			this.parent(options);
		},

		processScripts: function(text){
			return text;
		},

		onStateChange: function(){
			var xhr = this.xhr;
			if (xhr.readyState != 4 || !this.running) return;
			this.running = false;
			this.status = 0;
			Function.attempt(function(){
				var status = xhr.status;
				this.status = (status == 1223) ? 204 : status;
			}.bind(this));
			xhr.onreadystatechange = empty;
			if (progressSupport) xhr.onprogress = xhr.onloadstart = empty;
			clearTimeout(this.timer);

			this.response = new Response(this.xhr.responseText || '', {onError: this.onResponseError.bind(this)});
			if (this.options.isSuccess.call(this, this.status)){
				if (this.response.getPath('status') == 'success') this.success(this.response);
				else this.onResponseError(this.response);
			} else {
				this.failure();
				this.onResponseError(this.response);
			}
		},

		onResponseError: function(xhr){
			var d = this.options.data,
				message = 'RokSprocket Error [model: '+d.model+', model_action: '+d.model_action+', params: '+d.params+']: ';
			message += (xhr.status ? xhr.status + ' - ' + xhr.statusText : xhr);
			this.fireEvent('onResponseError', xhr, message);
			throw new Error(message);
		},

		setParams: function(params){
			var data = Object.merge(this.options.data || {}, {params: params || {}});

			data.params = JSON.encode(data.params);
			this.options.data = data;

			['model', 'model_action'].each(function(type){
				this.options.data[type] = this.options[type];
			}, this);

			return this;
		}
	});


	var Response = new Class({

		Implements: [Options, Events],

		options:{
			/*
				onParse: function(data){},
				onSuccess: function(data){},
				onError: function(data){}
			*/
		},

		initialize: function(data, options){
			this.setOptions(options);
			this.setData(data);

			return this;
		},

		setData: function(data){
			if (typeOf(data) == 'string') data = data.trim();

			this.data = data;
		},

		getData: function(){
			return (typeOf(this.data) != 'object') ? this.parseData(this.data) : this.data;
		},

		parseData: function(){
			if (!JSON.validate(this.data)) return this.error('Invalid JSON data <hr /> ' + this.data);

			this.data = JSON.decode(this.data);

			if (this.data.status != 'success') return this.error(this.data.message);

			this.fireEvent('parse', this.data);

			return this.success(this.data);

		},

		getPath: function(path){
			var data = this.getData();

			if (typeOf(data) == 'object') return Object.getFromPath(data, path || '');
			else return null;
		},

		success: function(data){
			this.data = data;

			this.fireEvent('success', this.data);
			return this.data;
		},

		error: function(message){
			this.data = message;

			this.fireEvent('error', this.data);
			return this.data;
		}

	});

	this.RokSprocket.Request = Request;

})());
