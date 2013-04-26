/**
 * Internationalization: french language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: Tom Barbette <mappam0@gmail.com>
 *
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.fr.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.fr.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.fr = {
		controls: {
			"Bold": "Gras",
			"Colorpicker": "Choisir une couleur",
			"Copy": "Copier",
			"Create link": "Créer un lien",
			"Cut": "Couper",
			"Decrease font size": "Diminuer la taille du texte",
			"Fullscreen": "Plein écran",
			"Header 1": "Titre 1",
			"Header 2": "Titre 2",
			"Header 3": "Titre 3",
			"View source code": "Voir le code source",
			"Increase font size": "Augmenter la taille du texte",
			"Indent": "Augmenter le retrait",
			"Insert Horizontal Rule": "Insérer une règle horyzontale",
			"Insert image": "Insérer une image",
			"Insert Ordered List": "Insérer une liste ordonnée",
			"Insert table": "Insérer un tableau",
			"Insert Unordered List": "Insérer une liste",
			"Italic": "Italique",
			"Justify Center": "Centré",
			"Justify Full": "Justifié",
			"Justify Left": "Aligné à gauche",
			"Justify Right": "Aligné à droite",
			"Left to Right": "Gauche à droite",
			"Outdent": "Réduire le retrait",
			"Paste": "Coller",
			"Redo": "Restaurer",
			"Remove formatting": "Supprimer le formatage",
			"Right to Left": "Droite à gauche",
			"Strike-through": "Barré",
			"Subscript": "Indice",
			"Superscript": "Exposant",
			"Underline": "Souligné",
			"Undo": "Annuler"
		},

		dialogs: {
			// for all
			"Apply": "Appliquer",
			"Cancel": "Annuler",

			colorpicker: {
				"Colorpicker": "Choisir une couleur",
				"Color": "Couleur"
			},

			image: {
				"Insert Image": "Insérer une image",
				"Preview": "Prévisualiser",
				"URL": "URL",
				"Title": "Titre",
				"Description": "Description",
				"Width": "Largeur",
				"Height": "Hauteur",
				"Original W x H": "L x H originale",
				"Float": "",
				"None": "",
				"Left": "",
				"Right": ""
			},

			link: {
				"Insert Link": "Insérer un lien",
				"Link URL": "URL du lien",
				"Link Title": "Titre du lien",
				"Link Target": "Cible du lien"
			},

			table: {
				"Insert table": "Insérer un tableau",
				"Count of columns": "Nombre de colonnes",
				"Count of rows": "Nombre de lignes"
			}
		}
	};
})(jQuery);