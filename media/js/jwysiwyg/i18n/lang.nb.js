/**
 * Internationalization: norwegian (bokmål) language
 *
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: strauman on github.com / strauman.net
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.nb.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.nb.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.nb = {
		controls: {
			"Bold": "Fet",
			"Colorpicker": "Fargevelger",
			"Copy": "Kopier",
			"Create link": "Lag lenke",
			"Cut": "Klipp ut",
			"Decrease font size": "Reduser skriftstørrelse",
			"Fullscreen": "Fullskjerm",
			"Header 1": "Overskrift 1",
			"Header 2": "Overskrift 2",
			"Header 3": "Overskrift 3",
			"View source code": "Vis kildekode",
			"Increase font size": "Øk skriftstørrelse",
			"Indent": "Innrykk",
			"Insert Horizontal Rule": "Sett inn horisontal linje",
			"Insert image": "Sett inn bilde",
			"Insert Ordered List": "Sett inn sortert liste",
			"Insert table": "Sett inn tabell",
			"Insert Unordered List": "Sett inn usortert liste",
			"Italic": "Kursiv",
			"Justify Center": "Midtstillt",
			"Justify Full": "Blokkjustert",
			"Justify Left": "Ventrejustert",
			"Justify Right": "Høyrejustert",
			"Left to Right": "Venstre til høyre",
			"Outdent": "Rykk ut",
			"Paste": "Lim inn",
			"Redo": "Gjør om",
			"Remove formatting": "Fjern formatering",
			"Right to Left": "Høyre til venstre",
			"Strike-through": "Gjennomstreking",
			"Subscript": "Hevet skrift",
			"Superscript": "Senket skrift",
			"Underline": "Understrek",
			"Undo": "Angre"
		},

		dialogs: {
			// for all
			"Apply": "Bruk",
			"Cancel": "Avbryt",

			colorpicker: {
				"Colorpicker": "Fargevelger",
				"Color": "Farge"
			},

			fileManager: {
				"file_manager": 		"Filbehandler",
				"upload_title":			"Last opp fil",
				"rename_title":			"Gi nytt navn",
				"remove_title":			"Slett fil",
				"mkdir_title":			"Ny mappe",
				"upload_action": 		"Last opp fil til denne mappen",
				"mkdir_action": 		"Lag ny mappe",
				"remove_action": 		"Slett filen",
				"rename_action": 		"Nytt navn" ,	
				"delete_message": 		"Er du sikker på at du vil slette denne filen?",
				"new_directory": 		"Mappe uten navn",
				"previous_directory": 	"Opp",
				"rename":				"Gi nytt navn",
				"select": 				"Velg",
				"create": 				"Lag",
				"submit": 				"Send",
				"cancel": 				"Avbryt",
				"yes":					"Ja",
				"no":					"Nei"
			},

			image: {
				"Insert Image": "Sett inn bilde",
				"Preview": "Forhåndsvisning",
				"URL": "URL",
				"Title": "Tittel",
				"Description": "Beskrivelse",
				"Width": "Bredde",
				"Height": "Høyde",
				"Original W x H": "Original B x H",
				"Float": "Flyt",
				"None": "Ingen",
				"Left": "Venstre",
				"Right": "Høyre",
				"Select file from server": "Velg fil fra tjener"
			},

			link: {
				"Insert Link": "Sett inn lenke",
				"Link URL": "Lenke-URL",
				"Link Title": "Lenketittel",
				"Link Target": "Lenkemål"
			},

			table: {
				"Insert table": "Sett inn tabell",
				"Count of columns": "Antall kolonner",
				"Count of rows": "Antall rader"
			}
		}
	};
})(jQuery);
