/**
 * rmFormat plugin
 *
 * Depends on jWYSIWYG
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "wysiwyg.rmFormat.js depends on $.wysiwyg";
	}

	/*
	 * Wysiwyg namespace: public properties and methods
	 */
	var rmFormat = {
		name: "rmFormat",
		version: "",
		defaults: {
			rules: {
				heading: false,
				table: false,
				inlineCSS: false,
				/*
				 * rmAttr       - { "all" | object with names } remove all
				 *                attributes or attributes with following names
				 *
				 * rmWhenEmpty  - if element contains no text or { \s, \n, <br>, <br/> }
				 *                then it will be removed
				 *
				 * rmWhenNoAttr - if element contains no attributes (i.e. <span>Some Text</span>)
				 *                then it will be removed
				 */
				msWordMarkup: {
					enabled: false,
					tags: {
						"a": {
							rmWhenEmpty: true
						},

						"b": {
							rmWhenEmpty: true
						},

						"div": {
							rmWhenEmpty: true,
							rmWhenNoAttr: true
						},

						"em": {
							rmWhenEmpty: true
						},

						"font": {
							rmAttr: {
								"face": "",
								"size": ""
							},
							rmWhenEmpty: true,
							rmWhenNoAttr: true
						},

						"h1": {
							rmAttr: "all",
							rmWhenEmpty: true
						},
						"h2": {
							rmAttr: "all",
							rmWhenEmpty: true
						},
						"h3": {
							rmAttr: "all",
							rmWhenEmpty: true
						},
						"h4": {
							rmAttr: "all",
							rmWhenEmpty: true
						},
						"h5": {
							rmAttr: "all",
							rmWhenEmpty: true
						},
						"h6": {
							rmAttr: "all",
							rmWhenEmpty: true
						},

						"i": {
							rmWhenEmpty: true
						},

						"p": {
							rmAttr: "all",
							rmWhenEmpty: true
						},

						"span": {
							rmAttr: {
								lang: ""
							},
							rmWhenEmpty: true,
							rmWhenNoAttr: true
						},

						"strong": {
							rmWhenEmpty: true
						},

						"u": {
							rmWhenEmpty: true
						}
					}
				}
			}
		},
		options: {},
		enabled: false,
		debug:	false,

		domRemove: function (node) {
			// replace h1-h6 with p
			if (this.options.rules.heading) {
				if (node.nodeName.toLowerCase().match(/^h[1-6]$/)) {
					// in chromium change this to
					// $(node).replaceWith($('<p/>').html(node.innerHTML));
					// to except DOM error: also try in other browsers
					$(node).replaceWith($('<p/>').html($(node).contents()));

					return true;
				}
			}

			// remove tables not smart enough )
			if (this.options.rules.table) {
				if (node.nodeName.toLowerCase().match(/^(table|t[dhr]|t(body|foot|head))$/)) {
					$(node).replaceWith($(node).contents());

					return true;
				}
			}

			return false;
		},
		
		removeStyle: function(node) {
		  if (this.options.rules.inlineCSS) {
		    $(node).removeAttr('style');
		  }
		},

		domTraversing: function (el, start, end, canRemove, cnt) {
			if (null === canRemove) {
				canRemove = false;
			}

			var isDomRemoved, p;

			while (el) {
				if (this.debug) { console.log(cnt, "canRemove=", canRemove); }
				
				this.removeStyle(el);

				if (el.firstElementChild) {

					if (this.debug) { console.log(cnt, "domTraversing", el.firstElementChild); }

					return this.domTraversing(el.firstElementChild, start, end, canRemove, cnt + 1);
				} else {

					if (this.debug) { console.log(cnt, "routine", el); }

					isDomRemoved = false;

					if (start === el) {
						canRemove = true;
					}

					if (canRemove) {
						if (el.previousElementSibling) {
							p = el.previousElementSibling;
						} else {
							p = el.parentNode;
						}

						if (this.debug) { console.log(cnt, el.nodeName, el.previousElementSibling, el.parentNode, p); }

						isDomRemoved = this.domRemove(el);
						if (this.domRemove(el)) {

							if (this.debug) { console.log("p", p); }

							// step back to parent or previousElement to traverse again then element is removed
							el = p;
						}

						if (start !== end && end === el) {
							return true;
						}
					}

					if (false === isDomRemoved) {
						el = el.nextElementSibling;
					}
				}
			}

			return false;
		},

		msWordMarkup: function (text) {
			var tagName, attrName, rules, reg, regAttr, found, attrs;

			// @link https://github.com/akzhan/jwysiwyg/issues/165
			text = text.replace(/&lt;/g, "<").replace(/&gt;/g, ">");

			text = text.replace(/<meta\s[^>]+>/g, "");
			text = text.replace(/<link\s[^>]+>/g, "");
			text = text.replace(/<title>[\s\S]*?<\/title>/g, "");
			text = text.replace(/<style(?:\s[^>]*)?>[\s\S]*?<\/style>/gm, "");
			text = text.replace(/<w:([^\s>]+)(?:\s[^\/]+)?\/>/g, "");
			text = text.replace(/<w:([^\s>]+)(?:\s[^>]+)?>[\s\S]*?<\/w:\1>/gm, "");
			text = text.replace(/<m:([^\s>]+)(?:\s[^\/]+)?\/>/g, "");
			text = text.replace(/<m:([^\s>]+)(?:\s[^>]+)?>[\s\S]*?<\/m:\1>/gm, "");

			// after running the above.. it still needed these
			text = text.replace(/<xml>[\s\S]*?<\/xml>/g, "");
			text = text.replace(/<object(?:\s[^>]*)?>[\s\S]*?<\/object>/g, "");
			text = text.replace(/<o:([^\s>]+)(?:\s[^\/]+)?\/>/g, "");
			text = text.replace(/<o:([^\s>]+)(?:\s[^>]+)?>[\s\S]*?<\/o:\1>/gm, "");
			text = text.replace(/<st1:([^\s>]+)(?:\s[^\/]+)?\/>/g, "");
			text = text.replace(/<st1:([^\s>]+)(?:\s[^>]+)?>[\s\S]*?<\/st1:\1>/gm, "");
			// ----
			text = text.replace(/<!--[^>]+>\s*<[^>]+>/gm, ""); // firefox needed this 1

						
			text = text.replace(/^[\s\n]+/gm, "");

			if (this.options.rules.msWordMarkup.tags) {
				for (tagName in this.options.rules.msWordMarkup.tags) {
					rules = this.options.rules.msWordMarkup.tags[tagName];
					
					if ("string" === typeof (rules)) {
						if ("" === rules) {
							reg = new RegExp("<" + tagName + "(?:\\s[^>]+)?/?>", "gmi");
							text = text.replace(reg, "<" + tagName + ">");
						}
					} else if ("object" === typeof (rules)) {
						reg = new RegExp("<" + tagName + "(\\s[^>]+)?/?>", "gmi");
						found = reg.exec(text);
						attrs = "";

						if (found && found[1]) {
							attrs = found[1];
						}

						if (rules.rmAttr) {
							if ("all" === rules.rmAttr) {
								attrs = "";
							} else if ("object" === typeof (rules.rmAttr) && attrs) {
								for (attrName in rules.rmAttr) {
									regAttr = new RegExp(attrName + '="[^"]*"', "mi");
									attrs = attrs.replace(regAttr, "");
								}
							}
						}

						if (attrs) {
							attrs = attrs.replace(/[\s\n]+/gm, " ");
							
							if (" " === attrs) {
								attrs = "";
							}
						}

						text = text.replace(reg, "<" + tagName + attrs + ">");
					}
				}

				for (tagName in this.options.rules.msWordMarkup.tags) {
					rules = this.options.rules.msWordMarkup.tags[tagName];

					if ("string" === typeof (rules)) {
						//
					} else if ("object" === typeof (rules)) {
						if (rules.rmWhenEmpty) {
							reg = new RegExp("<" + tagName + "(\\s[^>]+)?>(?:[\\s\\n]|<br/?>)*?</" + tagName + ">", "gmi");
							text = text.replace(reg, "");
						}

						if (rules.rmWhenNoAttr) {
							reg = new RegExp("<" + tagName + ">(?!<" + tagName + ">)([\\s\\S]*?)</" + tagName + ">", "mi");
							found = reg.exec(text);
							while (found) {
								text = text.replace(reg, found[1]);

								found = reg.exec(text);
							}
						}
					}
				}
			}

			return text;
		},

		run: function (Wysiwyg, options) {
			options = options || {};
			this.options = $.extend(true, this.defaults, options);

			if (this.options.rules.heading || this.options.rules.table) {
				var r = Wysiwyg.getInternalRange(),
					start,
					end,
					node,
					traversing;

				start = r.startContainer;
				if (start.nodeType === 3) {
					start = start.parentNode;
				}

				end = r.endContainer;
				if (end.nodeType === 3) {
					end = end.parentNode;
				}

				if (this.debug) {
					console.log("start", start, start.nodeType, start.nodeName, start.parentNode);
					console.log("end", end, end.nodeType, end.nodeName, end.parentNode);
				}

				node = r.commonAncestorContainer;
				if (node.nodeType === 3) {
					node = node.parentNode;
				}

				if (this.debug) {
					console.log("node", node, node.nodeType, node.nodeName.toLowerCase(), node.parentNode, node.firstElementChild);
					console.log(start === end);
				}

				traversing = null;
				if (start === end) {
					traversing = this.domTraversing(node, start, end, true, 1);
				} else {
					traversing = this.domTraversing(node.firstElementChild, start, end, null, 1);
				}

				if (this.debug) { console.log("DomTraversing=", traversing); }
			}

			if (this.options.rules.msWordMarkup.enabled) {
				Wysiwyg.setContent(this.msWordMarkup(Wysiwyg.getContent()));
			}
		}
	};

	$.wysiwyg.plugin.register(rmFormat);
})(jQuery);
