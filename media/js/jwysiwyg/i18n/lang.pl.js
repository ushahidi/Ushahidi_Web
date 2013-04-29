/**
 * Internationalization: Polish language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: Andrzej Herok
 *
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.pl.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.pl.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.pl = {
		controls: {
			"Bold": "Pogrubienie",
			"Colorpicker": "Wybór koloru",
			"Copy": "Kopiuj",
			"Create link": "Utwórz łącze",
			"Cut": "Wytnij",
			"Decrease font size": "Zmniejsz rozmiar czcionki",
			"Fullscreen": "Pełny ekran",
			"Header 1": "Nagłówek 1",
			"Header 2": "Nagłówek 2",
			"Header 3": "Nagłówek 3",
			"View source code": "Pokaż kod źródłowy",
			"Increase font size": "Zwiększ rozmiar czcionki",
			"Indent": "Zwiększ wcięcie",
			"Insert Horizontal Rule": "Wstaw poziomą linię",
			"Insert image": "Wstaw obrazek",
			"Insert Ordered List": "Lista numerowana",
			"Insert table": "Wstaw tabelę",
			"Insert Unordered List": "Lista nienumerowana",
			"Italic": "Kursywa",
			"Justify Center": "Wyśrodkuj",
			"Justify Full": "Justowanie",
			"Justify Left": "Do lewej",
			"Justify Right": "Do prawej",
			"Left to Right": "Od lewej do prawej",
			"Outdent": "Zmniejsz wcięcie",
			"Paste": "Wklej",
			"Redo": "Powtórz",
			"Remove formatting": "Usuń formatowanie",
			"Right to Left": "Od prawej do lewej",
			"Strike-through": "Przekreślenie",
			"Subscript": "Indeks dolny",
			"Superscript": "Indeks górny",
			"Underline": "Podkreślenie",
			"Undo": "Cofnij"
		},

		dialogs: {
			// for all
			"Apply": "Zastosuj",
			"Cancel": "Anuluj",

			colorpicker: {
				"Colorpicker": "Próbnik koloru",
				"Color": "Kolor"
			},

			image: {
				"Insert Image": "Wstaw obrazek",
				"Preview": "Podgląd",
				"URL": "URL",
				"Title": "Tytuł",
				"Description": "Opis",
				"Width": "Szerokość",
				"Height": "Wysokość",
				"Original W x H": "Oryginalne wymiary",
				"Float": "Przyleganie",
				"None": "Brak",
				"Left": "Do lewej",
				"Right": "Do prawej"
			},

			link: {
				"Insert Link": "Wstaw łącze",
				"Link URL": "URL łącza",
				"Link Title": "Tytuł łącza",
				"Link Target": "Target"
			},

			table: {
				"Insert table": "Wstaw tabelę",
				"Count of columns": "Liczba kolumn",
				"Count of rows": "Liczba wierszy"
			}
		}
	};
})(jQuery);
