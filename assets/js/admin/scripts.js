jQuery( document ).ready(function($) {

	// Color Picker
	if( $('.wpg_cpick').length ) {
		$('.wpg_cpick').wpColorPicker();
	}
	
	// Tooltip
	$('.wpg-tooltip').tooltipster({
		contentAsHTML: true,
		interactive: true,
		theme: 'tooltipster-punk',
		minWidth: 400,
		maxWidth: 600,
	});
	
	// Tabs
	$('.wpg-tabs').tabs();
	$('.wpg-tabs').css("visibility","");
});
