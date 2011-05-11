/**
 * Categories js file.
 * 
 * Handles javascript stuff related to category function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

$(document).ready(function() {
    // Initialise the table
	$("#categorySort").tableDnD({
		dragHandle: "col-drag-handle",
		onDragClass: "col-drag",
		onDrop: function(table, row) {
			var rows = table.tBodies[0].rows;
			var categoriesArray = [];
			for (var i=0; i<rows.length; i++) {
				categoriesArray[i] = rows[i].id;
			}
			var categories = categoriesArray.join(',');
			$.post("<?php echo url::site() . 'admin/manage/category_sort/' ?>", { categories: categories },
				function(data){
					if (data == "ERROR") {
						alert("Invalid Placement!!\n You cannot place a subcategory on top of a category.");
					} else {
						$("#categorySort"+" tbody tr td").effect("highlight", {}, 500);
					}
			});
		}
	});
	
	$("#categorySort tr").hover(function() {
		$(this.cells[0]).addClass('col-show-handle');
	}, function() {
		$(this.cells[0]).removeClass('col-show-handle');
	});
});

// Categories JS
function fillFields(id, parent_id, category_title, category_description, category_color, locale<?php foreach($locale_array as $lang_key => $lang_name) echo ', '.$lang_key; ?>)
{
	show_addedit();
	$("#category_id").attr("value", unescape(id));
	$("#parent_id").attr("value", unescape(parent_id));
	$("#category_title").attr("value", unescape(category_title));
	$("#category_description").attr("value", unescape(category_description));
	$("#category_color").attr("value", unescape(category_color));
	$("#locale").attr("value", unescape(locale));
	<?php
		foreach($locale_array as $lang_key => $lang_name) {
			echo '$("#category_title_'.$lang_key.'").attr("value", unescape('.$lang_key.'));'."\n";
		}
	?>
}

// Ajax Submission
function catAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
	if (answer){
		// Set Category ID
		$("#category_id_action").attr("value", id);
		// Set Submit Type
		$("#category_action").attr("value", action);
		// Submit Form
		$("#catListing").submit();
	}
}