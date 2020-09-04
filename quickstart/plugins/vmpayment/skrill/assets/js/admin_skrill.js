jQuery().ready(function ($) {
    var payment_method = [
    	"skrill_apm",
		"skrill_wlt",
		"skrill_psc",
		"skrill_pch",
		"skrill_acc",
		"skrill_vsa",
		"skrill_msc",
		"skrill_mae",
		"skrill_obt",
		"skrill_gir",
		"skrill_ebt",
		"skrill_npy",
		"skrill_pli",
		"skrill_pwy",
		"skrill_epy",
		"skrill_ntl",
		"skrill_ali",
		"skrill_adb",
		"skrill_aob",
		"skrill_aci",
		"skrill_aup",
		"skrill_btc",
		"skrill_idl"
	];
	var reset_cursor_style = function()
	{
		$.each(payment_method, function(i, method_code) {
			$('#params_'+method_code+'_active-lbl').css('cursor', 'default');
			$('#params_'+method_code+'_sort_order-lbl').css('cursor', 'default');
		});
	};

	var removeHeaderSection = function()
	{
		var controlField = $('.control-field').closest('.tabs');
		controlField.find('h2').hide();
		$(controlField.find('div')[0]).hide();
	}

	Joomla.submitbutton = function(a){

		var options = { path: '/', expires: 2}
		if (a == 'apply') {
			var idx = jQuery('#tabs li.current').index();
			jQuery.cookie('vmapply', idx, options);
		} else {
			jQuery.cookie('vmapply', '0', options);
		}
		jQuery( '#media-dialog' ).remove();

		form = document.getElementById('adminForm');
		var requiredField = form.querySelectorAll('[required]');
		var isValidate = true;
		[].forEach.call(requiredField, function(elm) {
			if (!$(elm).val()) {
				form.reportValidity();
				isValidate = false;
			}
		});

		if (isValidate) {
			form.task.value = a;
			form.submit();
		}

		return false;
	};

	function init() {
		removeHeaderSection();
		reset_cursor_style();
	}

	init();
});