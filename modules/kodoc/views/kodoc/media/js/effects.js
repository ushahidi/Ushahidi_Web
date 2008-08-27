// $Id: effects.js 486 2007-09-04 01:49:02Z Shadowhand $
$(document).ready(function(){
	// Opacity animations in an element with an opacity of 1.0 cause Firefox bugs
	$('#menu').css('opacity', 0.9999);
	// Apply menu sliding effect
	$('#menu li.first').click(function(){
		// Clicks to the same menu will do nothing
		if ($(this).is('.active') == false){
			// Hide the current submenu
			$('#menu li.active').removeClass('active')
			.find('ul').not('.expanded')
			.animate({height: 'hide', opacity: 'hide'}, 200, 'easeOutQuad');
			// Show the clicked submenu
			$(this).addClass('active')
			.find('ul').not('.expanded')
			.slideDown({height: 'show', opacity: 'show'}, 200, 'easeInQuad');
		}
	})
	// Find and hide the sub menus that are not in the active menu
	.not('.active').find('ul').hide();
	$('#menu li ul.expanded').each(function(i)
	{
		var sub = $(this).hide();
		var top = sub.parents('li:first');

		top.hover(function()
		{
			sub.show();
		},
		function()
		{
			sub.hide();
		});
	});
});