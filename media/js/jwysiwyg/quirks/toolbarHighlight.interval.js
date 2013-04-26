
(function($) {
	$.wysiwyg.quirk.register({
		/**
		 * @link https://github.com/jwysiwyg/jwysiwyg/issues/251
		 * @param {Wysiwyg} editor
		 */
		init: function(editor) {
			setInterval(function () {
				var offset = null;
				try {
					var range = editor.getInternalRange();
					if (!range) return;
					offset = {
						range: range,
						parent: range.endContainer ? range.endContainer.parentNode : range.parentElement(),
						width: (range.startOffset ? (range.startOffset - range.endOffset) : range.boundingWidth) || 0
					};
				}
				catch (e) { console.error(e); }
				
				if (offset && offset.width == 0 && !editor.editorDoc.rememberCommand) {
					editor.ui.checkTargets(offset.parent);
				}
			}, 400);
		},
	});
})(jQuery);