/**
 * Internationalization: German language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: Markus Schirp (mbj) <mbj@seonic.net>
 *
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.de.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.de.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.de = {
		controls: {
			"Bold": "Fett",
			"Colorpicker": "Farbe wählen",
			"Copy": "Kopieren",
			"Create link": "Link erstellen",
			"Cut": "Ausschneiden",
			"Decrease font size": "Schriftgröße verkleinern",
			"Fullscreen": "Vollbild",
			"Header 1": "Überschrift 1",
			"Header 2": "Überschrift 2",
			"Header 3": "Überschrift 3",
			"View source code": "Quellcode anzeigen",
			"Increase font size": "Schriftgröße vergrößern",
			"Indent": "Einrücken",
			"Insert Horizontal Rule": "Horizontalen Trennbalken einfügen",
			"Insert image": "Bild einfügen",
			"Insert Ordered List": "Nummerierte Liste einfügen",
			"Insert table": "Tabelle einfügen",
			"Insert Unordered List": "Unnummerierte Liste einfügen",
			"Italic": "Kursiv",
			"Justify Center": "Zentrieren",
			"Justify Full": "Blocksatz",
			"Justify Left": "Links ausrichten",
			"Justify Right": "Rechts ausrichten",
			"Left to Right": "Links nach Rechts",
			"Outdent": "Einrückung zurücknehmen",
			"Paste": "Einfügen",
			"Redo": "Wiederherstellen",
			"Remove formatting": "Formatierung entfernen",
			"Right to Left": "Rechts nach Links",
			"Strike-through": "Durchstreichen",
			"Subscript": "Tiefstellen",
			"Superscript": "Hochstellen",
			"Underline": "Unterstreichen",
			"Undo": "Rückgängig"
		},

		dialogs: {
			// for all
			"Apply": "Anwenden",
			"Cancel": "Abbrechen",

			colorpicker: {
				"Colorpicker": "Farbwähler",
				"Color": "Farbe"
			},

			image: {
				"Insert Image": "Bild einfügen",
				"Preview": "Vorschau",
				"URL": "URL",
				"Title": "Titel",
				"Description": "Beschreibung",
				"Width": "Breite",
				"Height": "Höhe",
				"Original W x H": "Original W x H",
				"Float": "",
				"None": "",
				"Left": "",
				"Right": ""
			},

			link: {
				"Insert Link": "Link einfügen",
				"Link URL": "Link URL",
				"Link Title": "Link Titel",
				"Link Target": "Link Ziel"
			},

			table: {
				"Insert table": "Tabelle einfügen",
				"Count of columns": "Spaltenanzahl",
				"Count of rows": "Zeilenanzahl"
			}
		}
	};
})(jQuery);
