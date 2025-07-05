/* global SHB_DATA, ajaxurl */
jQuery(function ($) {
	$('.button-primary[href="#shb-wizard"]').on('click', function (e) {
		e.preventDefault();
		$('#shb-wizard').fadeIn();
	});
	$('.shb-close').on('click', function () {
		$('#shb-wizard').fadeOut();
	});
	$('#shb-apply-suggested').on('click', function () {
		const data = {
			action: 'shb_apply_recommended',
			nonce: SHB_DATA.nonce
		};
		$(this).prop('disabled', true).text('Applying...');
		$.post(ajaxurl, data, () => location.reload());
	});
});
