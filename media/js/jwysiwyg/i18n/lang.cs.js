/**
 * Internationalization: czech language
 *
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: deepj on github.com
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.cs.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.cs.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.cs = {
		controls: {
			"Bold": "Tučné",
			"Colorpicker": "Výběr barvy",
			"Copy": "Kopírovat",
			"Create link": "Vytvořit odkaz",
			"Cut": "Vyjmout",
			"Decrease font size": "Zmenšit velikost písma",
			"Fullscreen": "Celá obrazovka",
			"Header 1": "Nadpis 1",
			"Header 2": "Nadpis 2",
			"Header 3": "Nadpis 3",
			"View source code": "Zobrazit zdrojový kód",
			"Increase font size": "Zvětšit velikost písma",
			"Indent": "Zvětšit odsazení",
			"Insert Horizontal Rule": "Vložit horizontální čáru",
			"Insert image": "Vložit obrázek",
			"Insert Ordered List": "Vložit číslovaný seznam",
			"Insert table": "Vložit tabulku",
			"Insert Unordered List": "Vložit odrážkový seznam",
			"Italic": "Kurzíva",
			"Justify Center": "Zarovnat na střed",
			"Justify Full": "Zarovnat do bloku",
			"Justify Left": "Zarovnat doleva",
			"Justify Right": "Zarovnat doprava",
			"Left to Right": "Zleva doprava",
			"Outdent": "Zmenšit odsazení",
			"Paste": "Vložit",
			"Redo": "Znovu",
			"Remove formatting": "Odstranit formátování",
			"Right to Left": "Zprava doleva",
			"Strike-through": "Přeškrnuté",
			"Subscript": "Dolní index",
			"Superscript": "Horní index",
			"Underline": "Potržené",
			"Undo": "Zpět"
		},

		dialogs: {
			// for all
			"Apply": "Použij",
			"Cancel": "Zrušit",

			colorpicker: {
				"Colorpicker": "Výběr barvy",
				"Color": "Barva"
			},

			fileManager: {
				"file_manager": "Správce souborů",
				"upload_title": "Nahrát soubor",
				"rename_title": "Přejmenovat soubor",
				"remove_title": "Odstranit soubor",
				"mkdir_title": "Vytvořit adresář",
				"upload_action": "Nahrát nový soubor do aktualního adresáře",
				"mkdir_action": "Vytvořit nový adresář",
				"remove_action": "Odstranit tento soubor",
				"rename_action": "Přejmenovat tento soubor" ,
				"delete_message": "Jste si jist, že chcete smazat tento soubor?",
				"new_directory": "Nový adresář",
				"previous_directory": "Vrať se do přechozího adresáře",
				"rename": "Přejmenovat",
				"select": "Vybrat",
				"create": "Vytvořit",
				"submit": "Vložit",
				"cancel": "Zrušit",
				"yes": "Ano",
				"no": "Ne"
			},

			image: {
				"Insert Image": "Vložit obrázek",
				"Preview": "Náhled",
				"URL": "Odkaz",
				"Title": "Název",
				"Description": "Popis",
				"Width": "Šířka",
				"Height": "Výška",
				"Original W x H": "Původní šířka a výška",
				"Float": "Obtékání",
				"None": "Žádné",
				"Left": "Doleva",
				"Right": "Doprava",
				"Select file from server": "Vybrat soubor ze serveru"
			},

			link: {
				"Insert Link": "Vložit odkaz",
				"Link URL": "Odkaz",
				"Link Title": "Název odkazu",
				"Link Target": "Cíl odkazu"
			},

			table: {
				"Insert table": "Vložit tabulku",
				"Count of columns": "Počet sloupců",
				"Count of rows": "Počet řádků"
			}
		}
	};
})(jQuery);