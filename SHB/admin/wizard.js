/* global SHB_DATA, ajaxurl */
jQuery(function ($) {
	// Open modal
	$(document).on('click', 'a[href="#shb-wizard"]', function (e) {
		e.preventDefault();
		$('#shb-wizard').fadeIn();
	});

	// Close modal (click X or outside)
	$('.shb-close, #shb-wizard').on('click', function (e) {
		if ($(e.target).is('.shb-close') || $(e.target).is('#shb-wizard')) {
			$('#shb-wizard').fadeOut();
		}
	});

	// Apply recommended
	$('#shb-apply-suggested').on('click', function () {
		const $btn = $(this).prop('disabled', true).text('Applying…');
		$.post(
			ajaxurl,
			{ action: 'shb_apply_recommended', nonce: SHB_DATA.nonce },
			function () { location.reload(); }
		);
	});
});
