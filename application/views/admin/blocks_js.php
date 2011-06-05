/**
 * Blocks js file.
 * 
 * Handles javascript stuff related to category function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Blocks Javascript
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

$(document).ready(function() {
    // Initialise the table
	$("#blockSort").tableDnD({
		dragHandle: "col-drag-handle",
		onDragClass: "col-drag",
		onDrop: function(table, row) {
			var rows = table.tBodies[0].rows;
			var blocksArray = [];
			for (var i=0; i<rows.length; i++) {
				blocksArray[i] = rows[i].id;
			}
			var blocks = blocksArray.join(',');
			$.post("<?php echo url::site() . 'admin/manage/blocks/sort' ?>", { blocks: blocks },
				function(data){
					$("#blockSort"+" tbody tr td").effect("highlight", {}, 500);
			});
		}
	});
	
	$("#blockSort tr").hover(function() {
		$(this.cells[0]).addClass('col-show-handle');
	}, function() {
		$(this.cells[0]).removeClass('col-show-handle');
	});
});

function blockAction ( action, confirmAction, block )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
	if (answer){
		// Set Category ID
		$("#block").attr("value", block);
		// Set Submit Type
		$("#action").attr("value", action);
		// Submit Form
		$("#blockListing").submit();
	}
}