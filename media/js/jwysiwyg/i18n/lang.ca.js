/**
 * Internationalization: Catalan language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: Josep Anguera Peralta <josep.anguera@gmail.com>
 *
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.ca.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.ca.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.ca = {
		controls: {
			"Bold": "Negreta",
			"Colorpicker": "Triar color",
			"Copy": "Copiar",
			"Create link": "Crear link",
			"Cut": "Tallar",
			"Decrease font size": "Disminuir tamany font",
			"Fullscreen": "Pantalla completa",
			"Header 1": "Títol 1",
			"Header 2": "Títol 2",
			"Header 3": "Títol 3",
			"View source code": "Veure codi",
			"Increase font size": "Aumentar tamany font",
			"Indent": "Afegir Sangrat",
			"Insert Horizontal Rule": "Insertar línia horitzontal",
			"Insert image": "Insertar imatge",
			"Insert Ordered List": "Insertar llista numèrica",
			"Insert table": "Insertar taula",
			"Insert Unordered List": "Insertar llista sense ordre",
			"Italic": "Cursiva",
			"Justify Center": "Centrar",
			"Justify Full": "Justificar",
			"Justify Left": "Alinear a la esquerra",
			"Justify Right": "Alinear a la dreta",
			"Left to Right": "Esquerra a dreta",
			"Outdent": "Treure sangrat",
			"Paste": "Enganxar",
			"Redo": "Restaurar",
			"Remove formatting": "Treure format",
			"Right to Left": "Dreta a esquerra",
			"Strike-through": "Invertir",
			"Subscript": "Subíndex",
			"Superscript": "Superíndex",
			"Underline": "Subratllar",
			"Undo": "Desfer"
		},

		dialogs: {
			// for all
			"Apply": "Aplicar",
			"Cancel": "Cancelar",

			colorpicker: {
				"Colorpicker": "Triar color",
				"Color": "Color"
			},

			image: {
				"Insert Image": "Insertar imatge",
				"Preview": "Previsualització",
				"URL": "URL",
				"Title": "Títol",
				"Description": "Descripció",
				"Width": "Amplada",
				"Height": "Alçada",
				"Original W x H": "Amplada x Alçada original",
				"Float": "Flotant",
				"None": "No",
				"Left": "Esquerra",
				"Right": "Dreta"
			},

			link: {
				"Insert Link": "Insertar link",
				"Link URL": "URL del link",
				"Link Title": "Títol del link",
				"Link Target": "Target de link"
			},

			table: {
				"Insert table": "Insertar taula",
				"Count of columns": "Número de columnes",
				"Count of rows": "Número de files"
			}
		}
	};
})(jQuery);
