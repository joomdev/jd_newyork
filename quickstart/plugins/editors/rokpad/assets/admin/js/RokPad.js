(function(){

	var RokPad = this.RokPad = {

		init: function() {
			RokPad.fixOverflow();
		},

		fixOverflow: function(){
			var hook = document.getElement('.rokpad-break'),
				slider;

			if (hook) slider = hook.getParent('.pane-slider');
			if (slider) slider.setStyle('overflow', 'visible');
		}

	};



	window.addEvent('load', RokPad.init);

})();
