/**
 * Internationalization: Croatian language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: Boris Strahija (bstrahija) <boris@creolab.hr>
 *
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.hr.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.hr.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.hr = {
		controls: {
			"Bold": "Podebljano",
			"Colorpicker": "Izbor boje",
			"Copy": "Kopiraj",
			"Create link": "Umetni link",
			"Cut": "Izreži",
			"Decrease font size": "Smanji font",
			"Fullscreen": "Cijeli ekran",
			"Header 1": "Naslov 1",
			"Header 2": "Naslov 2",
			"Header 3": "Naslov 3",
			"Header 4": "Naslov 4",
			"Header 5": "Naslov 5",
			"Header 6": "Naslov 6",
			"View source code": "Kod",
			"Increase font size": "Povećaj font",
			"Indent": "Uvuci",
			"Insert Horizontal Rule": "Horizontalna linija",
			"Insert image": "Umetni sliku",
			"Insert Ordered List": "Numerirana lista",
			"Insert table": "Umetni tabelu",
			"Insert Unordered List": "Nenumerirana lista",
			"Italic": "Ukošeno",
			"Justify Center": "Centriraj",
			"Justify Full": "Poravnaj obostrano",
			"Justify Left": "Poravnaj lijevo",
			"Justify Right": "Poravnaj desno",
			"Left to Right": "Lijevo na desno",
			"Outdent": "Izvuci",
			"Paste": "Zalijepi",
			"Redo": "Ponovi",
			"Remove formatting": "Poništi oblikovanje",
			"Right to Left": "Desno na lijevo",
			"Strike-through": "Precrtano",
			"Subscript": "Indeks",
			"Superscript": "Eksponent",
			"Underline": "Podcrtano",
			"Undo": "Poništi",
			"Code snippet": "Isječak koda"
		},

		dialogs: {
			// for all
			"Apply": "Primjeni",
			"Cancel": "Odustani",

			colorpicker: {
				"Colorpicker": "Izbor boje",
				"Color": "Boja"
			},

			image: {
				"Insert Image": "Umetni sliku",
				"Preview": "Predprikaz",
				"URL": "URL",
				"Title": "Naslov",
				"Description": "Opis",
				"Width": "Širina",
				"Height": "Visina",
				"Original W x H": "Originalna Š x V",
				"Float": "",
				"None": "Nema",
				"Left": "Lijevo",
				"Right": "Desno"
			},

			link: {
				"Insert Link": "Umetni link",
				"Link URL": "URL linka",
				"Link Title": "Naslov linka",
				"Link Target": "Meta linka"
			},

			table: {
				"Insert table": "Umetni tabelu",
				"Count of columns": "Broj kolona",
				"Count of rows": "Broj redova"
			}
		}
	};
})(jQuery);
