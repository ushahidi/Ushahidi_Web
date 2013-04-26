/**
 * Internationalization: Slovenian language
 *
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: Peter Zlatnar <peter.zlatnar@gmail.com>
 *
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.sl.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.sl.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.sl = {
		controls: {
			"Bold": "Krepko",
			"Colorpicker": "Izbirnik barv",
			"Copy": "Kopiraj",
			"Create link": "Dodaj povezavo",
			"Cut": "Izreži",
			"Decrease font size": "Zmanjšaj pisavo",
			"Fullscreen": "Celozaslonski način",
			"Header 1": "Naslov 1",
			"Header 2": "Naslov 2",
			"Header 3": "Naslov 3",
			"View source code": "Prikaži izvorno kodo",
			"Increase font size": "Povečaj pisavo",
			"Indent": "Zamik v desno",
			"Insert Horizontal Rule": "Vstavi vodoravno črto ",
			"Insert image": "Vstavi sliko",
			"Insert Ordered List": "Vstavi oštevilčen seznam",
			"Insert table": "Vstavi tabelo",
			"Insert Unordered List": "Vstavi označen seznam",
			"Italic": "Ležeče",
			"Justify Center": "Sredinska poravnava",
			"Justify Full": "Obojestranska poravnava",
			"Justify Left": "Leva poravnava",
			"Justify Right": "Desna poravnava",
			"Left to Right": "Od leve proti desni",
			"Outdent": "Zamik v levo",
			"Paste": "Prilepi",
			"Redo": "Ponovi",
			"Remove formatting": "Odstrani oblikovanje",
			"Right to Left": "Od desne proti levi",
			"Strike-through": "Prečrtano",
			"Subscript": "Podpisano",
			"Superscript": "Nadpisano",
			"Underline": "Podčrtano",
			"Undo": "Razveljavi"
		},

		dialogs: {
			// for all
			"Apply": "Uporabi",
			"Cancel": "Prekliči",
			
			colorpicker: {
				"Colorpicker": "Izbirnik barv",
				"Color": "Barva"
			},

			image: {
				"Insert Image": "Vstavi sliko",
				"Preview": "Predogled",
				"URL": "URL",
				"Title": "Naslov",
				"Description": "Opis",
				"Width": "Širina",
				"Height": "Višina",
				"Original W x H": "Prvotna Š x V",
				"Float": "",
				"None": "",
				"Left": "",
				"Right": ""
			},

			link: {
				"Insert Link": "Vstavi povezavo",
				"Link URL": "URL povezave",
				"Link Title": "Naslov povezave",
				"Link Target": "Cilj povezave"
			},

			table: {
				"Insert table": "Vstavi tabelo",
				"Count of columns": "Število stolpcev",
				"Count of rows": "Število vrstic"
			}
		}
	};
})(jQuery);
