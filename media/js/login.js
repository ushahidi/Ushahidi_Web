/**
 * various functions and code used for the installation process
 * 
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

$(function(){
	
	// Hove behavior for the forms
	$(".fields input").focus(function(){
		//clear previous hover states
		$(".fields tr").removeClass("hover");
		//grab the row and add "hover" to it
        $(this).parent().parent().addClass("hover");
	});
	
	// Map API Interactions
	$("#select_map_provider option").click(function(){
			
			//set the labels
			$("#map-provider-label span").text($(this).text());
			$("#map-provider-title").text($(this).text());
			//set the API URL
			$("#api-link").attr("href",$(this).attr("url"));
	});
	
	//Close button for system messages
	$("a.btn-close").click(function(){$(this).parent().slideUp(); return false;});

});
