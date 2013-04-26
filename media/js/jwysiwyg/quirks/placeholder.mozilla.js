(function($) {
	"use strict";
	$.wysiwyg.quirk.register({
		defaultBlock: 'p',
		placeholder: 'br',
		
		/**
		 * @author stianlik@github
		 * @link https://github.com/jwysiwyg/jwysiwyg/issues/345
		 * @param {Wysiwyg} editor Editor instance
		 */
		init: function(editor) {
			if ($.browser.mozilla) {
				var that = this;
				$(editor.editorDoc).on('input cut paste keyup', function() {
					var context = {
						editor: editor,
						container: editor.editorDoc.body,
						selection: editor.getInternalSelection()
					};
					// A bit hacky, but this should push apply() to end of execution
					// queue so that it is executed after the event has been processed.
					// http://ejohn.org/blog/how-javascript-timers-work/
					setTimeout( function() {that.apply(context);}, 0);
				});
			}
		},
		
		apply: function(context) {
			var range = context.editor.getInternalRange();
			if (!range) return;
			
			if (this.isNotEnclosed(context, range)) {
				// Avoid empty root node by enclosing range with block element
				this.enclose(context, range);
			}
			else if(this.isRootNode(context, range.startContainer) && range.endOffset === 0) {
				// Avoid writing directly to root node by jumping to existing block element
				// Handles cases where users focus the editor by clicking TAB
				range.selectNodeContents(context.container.firstChild);
				range.collapse(true);
			}
		},
		
		
		enclose: function(context, range) {
			var el = context.editor.editorDoc.createElement(this.defaultBlock);
			
			// Append non-enclosed content to container
			for (var i = 0; i < context.container.childNodes.length; ++i) {
				el.appendChild(context.container.childNodes[i]);
			}
			
			// Append placeholder if there are no content
			if (el.childNodes.length === 0) {
				el.appendChild(this.createPlaceholder(context));
			}
			
			// Replace mozilla placeholder if found
			else if (this.isPlaceholder(el.lastChild)) {
				el.replaceChild(this.createPlaceholder(context), el.lastChild);
			}
			
			context.container.appendChild(el);
			
			// Move cursor into block element
			context.selection.removeAllRanges();
			range.selectNode(el.lastChild);
			range.collapse(el.lastChild.tagName === 'BR');
			context.selection.addRange(range);
		},
		
		createPlaceholder: function(context) {
			return context.editor.editorDoc.createElement(this.placeholder);
		},
		
		isNotEnclosed: function(context, range) {
			return (this.isRootNode(context, range.startContainer) || this.isTextNode(range.startContainer)) &&
			(this.hasNoElements(context.container) || this.hasOnlyPlaceholderElement(context.container));
		},
		
		isTextNode: function(node) {
			return node.nodeType === 3;
		},
		
		isRootNode: function(context, node) {
			return node === context.container;
		},
		
		isPlaceholder: function(node) {
			return node.tagName && node.tagName === 'BR';
		},
		
		hasNoElements: function(element) {
			return element.children.length === 0;
		},
		
		hasOnlyPlaceholderElement: function(element) {
			return element.children.length === 1 && this.isPlaceholder(element.lastChild);
		}
	});
})(jQuery);