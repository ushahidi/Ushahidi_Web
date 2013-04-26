/**
 * Internationalization: Dutch
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: Erik van Dongen <dongen@connexys.com>
 *
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.nl.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.nl.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.nl = {
		controls: {
			"Bold": "Vet",
			"Colorpicker": "Kleur kiezen",
			"Copy": "Kopiëren",
			"Create link": "Link maken",
			"Cut": "Knippen",
			"Decrease font size": "Lettergrootte verkleinen",
			"Fullscreen": "Volledig scherm",
			"Header 1": "Kop 1",
			"Header 2": "Kop 2",
			"Header 3": "Kop 3",
			"View source code": "Broncode bekijken",
			"Increase font size": "Lettergrootte vergroten",
			"Indent": "Inspringen",
			"Insert Horizontal Rule": "Horizontale lijn invoegen",
			"Insert image": "Afbeelding invoegen",
			"Insert Ordered List": "Genummerde lijst",
			"Insert table": "Tabel invoegen",
			"Insert Unordered List": "Lijst met opsommingstekens",
			"Italic": "Cursief",
			"Justify Center": "Centreren",
			"Justify Full": "Uitvullen",
			"Justify Left": "Links uitlijnen",
			"Justify Right": "Rechts uitlijnen",
			"Left to Right": "Links naar Rechts",
			"Outdent": "Uitspringen",
			"Paste": "Plakken",
			"Redo": "Opnieuw uitvoeren",
			"Remove formatting": "Opmaak verwijderen",
			"Right to Left": "Rechts naar Links",
			"Strike-through": "Doorstrepen",
			"Subscript": "Subscript",
			"Superscript": "Superscript",
			"Underline": "Onderstrepen",
			"Undo": "Ongedaan maken"
		},

		dialogs: {
			// for all
			"Apply": "Toepassen",
			"Cancel": "Annuleren",

			colorpicker: {
				"Colorpicker": "Kleur kiezen",
				"Color": "Kleur"
			},

			image: {
				"Insert Image": "Afbeeldingen invoegen",
				"Preview": "Voorbeeld",
				"URL": "URL",
				"Title": "Titel",
				"Description": "Beschrijving",
				"Width": "Breedte",
				"Height": "Hoogte",
				"Original W x H": "Originele B x H",
				"Float": "Float",
				"None": "None",
				"Left": "Left",
				"Right": "Right"
			},

			link: {
				"Insert Link": "Link invoegen",
				"Link URL": "Link URL",
				"Link Title": "Linktitel",
				"Link Target": "Link target"
			},

			table: {
				"Insert table": "Tabel invoegen",
				"Count of columns": "Aantal kolommen",
				"Count of rows": "Aantal rijen"
			}
		}
	};
})(jQuery);
