jQuery(document).ready(function( $ ) {

	// TOGGLE DROPDOWN
	$('.header_nav_dropdown .header_nav_cancel').live('click', function(e) {
		$(this).closest('.header_nav_dropdown').fadeOut('fast');
		$(this).closest('.header_nav_dropdown').siblings('p').removeClass('active');
	});
	$('.header_nav_has_dropdown > a, .header_nav_actions .header_nav_button_delete, .header_nav_actions .header_nav_button_change').live('click', function(e) {
		$(this).toggleClass('active');
		$(this).siblings('.header_nav_dropdown').fadeToggle('fast')
		e.stopPropagation();
		return false;
	});
	$('.header_nav_actions .header_nav_dropdown').live('click', function(e) {
		e.stopPropagation();
	});
	
	$('#header_nav_forgot').click(function() {
		$('#header_nav_userforgot_form').toggle('fast');
	});

});