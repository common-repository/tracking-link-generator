jQuery(function($) {

	$('td[data-copy]').on('click', function(e) {
		e.preventDefault();
		var prompt = window.prompt('Copy/paste', $(this).text());
	});

	$('.alti_tlg_tabs li a, a.alti_tlg_tab_link').on('click', function(e) {
		$('.alti_tlg_tab, .alti_tlg_tabs li a').removeClass('active');
		$(this).addClass('active');
		$('.alti_tlg_tab.' + $(this).attr('href').substr(1) + ', .alti_tlg_tabs li a[href="' + $(this).attr('href') + '"]').addClass('active');
	});

	if (window.location.hash.substr(0,14) == '#alti_tlg_tab-') {
		$('.alti_tlg_tabs li a[href="' + window.location.hash + '"]').trigger('click');
	}

	if( $('.alti_tlg_promote_container').length == 0 || $('.alti_tlg_promote_container').text().length == 0 ) {
		$('#alti_tlg form p.submit').each(function(i) {
			$(this).find('input[type="submit"]').remove();
			$(this).html('<div class="alti_tlg_error">The plugin is <strong>now inactive</strong> because it has been modified to hide original author information.<br>Please contact your administrator.</div>')
		});
	}

});