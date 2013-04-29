/**
 * Internationalization: italian language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: Mauro Franceschini <mauro.franceschini@gmail.com>
 *
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.it.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.it.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.it = {
		controls: {
			"Bold": "Grassetto",
			"Colorpicker": "Scegli un colore",
			"Copy": "Copia",
			"Create link": "Crea collegamento",
			"Cut": "Taglia",
			"Decrease font size": "Diminuisci dimensione testo",
			"Fullscreen": "Schermo intero",
			"Header 1": "Titolo 1",
			"Header 2": "Titolo 2",
			"Header 3": "Titolo 3",
			"View source code": "Visualizza codice sorgente",
			"Increase font size": "Aumenta dimensione testo",
			"Indent": "Aumenta il rientro",
			"Insert Horizontal Rule": "Inserisci separatore orizzontale",
			"Insert image": "Inserisci immagine",
			"Insert Ordered List": "Inserisci lista ordinata",
			"Insert table": "Inserisci tabella",
			"Insert Unordered List": "Inserisci lista non ordinata",
			"Italic": "Corsivo",
			"Justify Center": "Centrato",
			"Justify Full": "Giustificato",
			"Justify Left": "Allineato a sinistra",
			"Justify Right": "Allineato a destra",
			"Left to Right": "Da sinistra a destra",
			"Outdent": "Riduci il rientro",
			"Paste": "Incolla",
			"Redo": "Ripristina",
			"Remove formatting": "Cancella formattazione",
			"Right to Left": "Da destra a sinistra",
			"Strike-through": "Barrato",
			"Subscript": "Pedice",
			"Superscript": "Apice",
			"Underline": "Sottolineato",
			"Undo": "Annulla"
		},

		dialogs: {
			// for all
			"Apply": "Applica",
			"Cancel": "Annulla",

			colorpicker: {
				"Colorpicker": "Scegli un colore",
				"Color": "Colore"
			},

			image: {
				"Insert Image": "Inserisci immagine",
				"Preview": "Anteprima",
				"URL": "Indirizzo internet (URL)",
				"Title": "Titolo",
				"Description": "Descrizione",
				"Width": "Larghezza",
				"Height": "Altezza",
				"Original W x H": "Dimensioni originali (L x A)",
				"Float": "",
				"None": "",
				"Left": "",
				"Right": ""
			},

			link: {
				"Insert Link": "Inserisci collegamento",
				"Link URL": "Indirizzo internet (URL)",
				"Link Title": "Titolo",
				"Link Target": "Destinazione"
			},

			table: {
				"Insert table": "Inserisci tabella",
				"Count of columns": "Numero di colonne",
				"Count of rows": "Numero di righe"
			}
		}
	};
})(jQuery);