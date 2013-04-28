<?php
/**
 * Pages js file.
 *
 * Handles javascript stuff related to pages function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Pages Javascript
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
// Pages JS


function fillFields(id, page_title, page_tab,
 page_description )
{
	$("#page_id").attr("value", decodeURIComponent(id));
	page_title = decodeURIComponent(escape($.base64.decode(page_title)));
	$("#page_title").attr("value", decodeURIComponent(page_title));
	page_tab = decodeURIComponent(escape($.base64.decode(page_tab)));
	$("#page_tab").attr("value", decodeURIComponent(page_tab));
	page_description = decodeURIComponent(escape($.base64.decode(page_description)));
	$("#page_description").attr("value", decodeURIComponent(page_description));
	$("#page_description").wysiwyg("setContent",decodeURIComponent(page_description));
}

// Ajax Submission
function pageAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
	if (answer){
		// Set Category ID
		$("#page_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#pageListing").submit();
	}
}

//Load jwysiwyg editor
var hb_full ;
$(document).ready(function(){
	hb_full = $("#page_description").wysiwyg({
		resizeOptions: {},
		
		controls: {
			bold: { visible : true, groupIndex: 0 },
			italic: { visible : true, groupIndex: 0 },
			underline: { visible: false },
			strikeThrough: { visible: false },
			
			justifyLeft:{ visible: true },
			justifyCenter: { visible: true },
			justifyRight: { visible: true },
			justifyFull: { visible: false },
			
			subscript: { visible: false },
			superscript: { visible: false },
			
			undo: { visible: false },
			redo: { visible: false },
			
			insertOrderedList    : { visible : true },
			insertUnorderedList  : { visible : true },
			insertHorizontalRule : { visible : false },
			
			h1: {
				visible: true,
				className: 'h1',
				command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
				arguments: ($.browser.msie || $.browser.safari) ? '<h1>' : 'h1',
				tags: ['h1'],
				tooltip: 'Header 1',
				groupIndex: 7
			},
			h2: {
				visible: true,
				className: 'h2',
				command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
				arguments: ($.browser.msie || $.browser.safari) ? '<h2>' : 'h2',
				tags: ['h2'],
				tooltip: 'Header 2',
				groupIndex: 7
			},
			h3: {
				visible: true,
				className: 'h3',
				command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
				arguments: ($.browser.msie || $.browser.safari) ? '<h3>' : 'h3',
				tags: ['h3'],
				tooltip: 'Header 3',
				groupIndex: 7
			},
			h4: {
				visible: true,
				className: 'h4',
				command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
				arguments: ($.browser.msie || $.browser.safari) ? '<h4>' : 'h4',
				tags: ['h4'],
				tooltip: 'Header 4',
				groupIndex: 7
			},
			h5: {
				visible: true,
				className: 'h5',
				command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
				arguments: ($.browser.msie || $.browser.safari) ? '<h5>' : 'h5',
				tags: ['h5'],
				tooltip: 'Header 5',
				groupIndex: 7
			},
			paragraph: { visible: true },
			
			fileManager: { 
				visible: false,
				groupIndex: 12,
				tooltip: "File Manager",
				exec: function () {
					$.wysiwyg.fileManager.init(this, function (file) {
						file ? alert(file) : alert("No file selected.");
					});
				}
			},
			
			cut   : { visible : false },
			copy  : { visible : false },
			paste : { visible : false },
			
			html: { visible: true },
			code: { visible: false },
			fullscreen: {
				groupIndex: 12,
				visible: true,
				exec: function () {
					if ($.wysiwyg.fullscreen) {
						$.wysiwyg.fullscreen.init(this);
					}
				},
				tooltip: "Fullscreen"
			}
		},
	});
	$.wysiwyg.fileManager.setAjaxHandler("<?php echo url::site('admin/jwysiwyg/filemanager') ?>");
});

