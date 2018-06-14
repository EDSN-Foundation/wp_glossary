jQuery(document).ready(function ($) {

	// MixItUp
	if ($('.wpg-list-wrapper').length) {
		$('.wpg-list-wrapper').each(function () {
			var $elem = $(this);

			var $active_filter = $elem.find('.wpg-list-filter .filter.active').data('filter');
			if ($active_filter == '' || typeof $active_filter == 'undefined') {
				$active_filter = 'all';
			}

			mixitup($elem, {
				animation: {
					enable: wpg.animation
				},
				load: {
					filter: $active_filter
				},
				controls: {
					scope: 'local'
				}
			});
		});
	}

	// ToolTipSter
	if (typeof wpg != "undefined" && wpg.is_tooltip) {

		// Initiate Tooltipster
		$('.wpg-tooltip').tooltipster({
			//trigger: 'click',
			contentAsHTML: true,
			interactive: true,
			theme: 'tooltipster-' + wpg.tooltip_theme,
			animation: wpg.tooltip_animation,
			arrow: wpg.tooltip_is_arrow,
			minWidth: parseInt(wpg.tooltip_min_width),
			maxWidth: parseInt(wpg.tooltip_max_width),
			position: wpg.tooltip_position,
			speed: parseInt(wpg.tooltip_speed),
			delay: parseInt(wpg.tooltip_delay),
			touchDevices: wpg.tooltip_is_touch_devices,
			functionReady: function (origin) {
				$('img').load(function () {
					origin.tooltipster('reposition');
				});
			}
		});

		// Fix: Tootltip requires double click sometimes to trigger click
		$('.wpg-tooltip').click(function (e) {
			if (typeof $(this).attr('href') != 'undefined') {
				e.preventDefault();

				if (typeof $(this).attr('target') != 'undefined') {
					window.open($(this).attr('href'), $(this).attr('target'));
				} else {
					window.location.href = $(this).attr('href');
				}
			}
		});

		// Touch Device Double Click Hack 
		if (wpg.tooltip_is_touch_devices) {
			if (wpg_glossary_touch_device()) {
				var touchtime = 0;
				$('.wpg-tooltip').on('click', function (e) {
					if (touchtime == 0) {
						e.preventDefault();
						touchtime = new Date().getTime();
					} else {
						if (((new Date().getTime()) - touchtime) < 800) {
							touchtime = 0;
						} else {
							e.preventDefault();
							touchtime = new Date().getTime();
						}
					}
				});
			}

			function wpg_glossary_touch_device() {
				if ('ontouchstart' in window || navigator.maxTouchPoints) {
					return true;
				} else {
					return false;
				}
			}
		}

	}
});
