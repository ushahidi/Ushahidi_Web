(function($) {
	$.wysiwyg.quirk.register({
		/**
		 * @author rmlmedia@github
		 * @link https://github.com/jwysiwyg/jwysiwyg/issues/251
		 * @param {Wysiwyg} editor
		 */
		init: function(editor) {
			$(editor.editorDoc).bind("keyup.wysiwyg", function(event) {
				var node = null;
				var selection = editor.getInternalSelection();
				node = selection.extentNode || selection.focusNode;
				// Allow for older versions of IE (8 or lower)
				if ($.browser.msie && node == null) node = editor.getInternalRange().parentElement();
	
				while (node.style === undefined) {
					node = node.parentNode;
					if (node.tagName && node.tagName.toLowerCase() === "body") {
						return;
					}
				}
				
				if (node != null) editor.ui.checkTargets(node);
			});
		}
	});
})(jQuery);