/**
 * Internationalization plugin
 * 
 * Depends on jWYSIWYG
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "wysiwyg.i18n.js depends on $.wysiwyg";
	}

	/*
	 * Wysiwyg namespace: public properties and methods
	 */
	var i18n = {
		name: "i18n",
		version: "",
		defaults: {
			lang: "en",			// change to your language by passing lang option
			wysiwygLang: "en"	// default WYSIWYG language
		},
		lang: {},
		options: {},

		init: function (Wysiwyg, lang) {
			if (!Wysiwyg.options.plugins[this.name]) {
				return true;
			}

			this.options = $.extend(true, this.defaults, Wysiwyg.options.plugins[this.name]);

			if (lang) {
				this.options.lang = lang;
			} else {
				lang = this.options.lang;
			}

			if ((lang !== this.options.wysiwygLang) && (undefined === $.wysiwyg.i18n.lang[lang])) {
				if ($.wysiwyg.autoload) {
					$.wysiwyg.autoload.lang("lang." + lang + ".js", function () {
						$.wysiwyg.i18n.init(Wysiwyg, lang);
					});
				} else {
					throw 'Language "' + lang + '" not found in $.wysiwyg.i18n. You need to include this language file';
				}
			}

			this.translateControls(Wysiwyg, lang);
		},

		translateControls: function (Wysiwyg, lang) {
			Wysiwyg.ui.toolbar.find("li").each(function () {
				if (Wysiwyg.options.controls[$(this).attr("class")] && Wysiwyg.options.controls[$(this).attr("class")].visible) {
					$(this).attr("title", $.wysiwyg.i18n.t(Wysiwyg.options.controls[$(this).attr("class")].tooltip, "controls", lang));
				}
			});
		},

		run: function (object, lang) {
			return object.each(function () {
				var oWysiwyg = $(this).data("wysiwyg");

				if (!oWysiwyg) {
					return this;
				}

				$.wysiwyg.i18n.init(oWysiwyg, lang);
			});
		},

		t: function (phrase, section, lang) {
			var i, section_array, transObject;

			if (!lang) {
				lang = this.options.lang;
			}

			if ((lang === this.options.wysiwygLang) || (!this.lang[lang])) {
				return phrase;
			}

			transObject = this.lang[lang];
			section_array = section.split(".");
			for (i = 0; i < section_array.length; i += 1) {
				if (transObject[section_array[i]]) {
					transObject = transObject[section_array[i]];
				}
			}

			if (transObject[phrase]) {
				return transObject[phrase];
			}

			return phrase;
		}
	};

	$.wysiwyg.plugin.register(i18n);
	$.wysiwyg.plugin.listen("initFrame", "i18n.init");
})(jQuery);
