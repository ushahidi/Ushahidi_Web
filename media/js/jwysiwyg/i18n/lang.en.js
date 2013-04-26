/**
 * Internationalization: English language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 * 
 * By: frost-nzcr4 on github.com
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.en.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.en.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.en = {
		controls: {
			"Bold": "",
			"Colorpicker": "",
			"Copy": "",
			"Create link": "",
			"Cut": "",
			"Decrease font size": "",
			"File Manager": "",
			"Fullscreen": "",
			"Header 1": "",
			"Header 2": "",
			"Header 3": "",
			"View source code": "",
			"Increase font size": "",
			"Indent": "",
			"Insert Horizontal Rule": "",
			"Insert image": "",
			"Insert Ordered List": "",
			"Insert table": "",
			"Insert Unordered List": "",
			"Italic": "",
			"Justify Center": "",
			"Justify Full": "",
			"Justify Left": "",
			"Justify Right": "",
			"Left to Right": "",
			"Outdent": "",
			"Paste": "",
			"Redo": "",
			"Remove formatting": "",
			"Right to Left": "",
			"Strike-through": "",
			"Subscript": "",
			"Superscript": "",
			"Underline": "",
			"Undo": ""
		},

		dialogs: {
			// for all
			"Apply": "",
			"Cancel": "",

			colorpicker: {
				"Colorpicker": "",
				"Color": ""
			},

			fileManager: {
				"file_manager": 		"File Manager",
				"upload_title":			"Upload File",
				"rename_title":			"Rename File",
				"remove_title":			"Remove File",
				"mkdir_title":			"Create Directory",
				"upload_action": 		"Upload new file to current directory",
				"mkdir_action": 		"Create new directory",
				"remove_action": 		"Remove this file",
				"rename_action": 		"Rename this file" ,	
				"delete_message": 		"Are you sure you want to delete this file?",
				"new_directory": 		"New Directory",
				"previous_directory": 	"Go to previous directory",
				"rename":				"Rename",
				"select": 				"Select",
				"create": 				"Create",
				"submit": 				"Submit",
				"cancel": 				"Cancel",
				"yes":					"Yes",
				"no":					"No"
			},

			image: {
				"Insert Image": "",
				"Preview": "",
				"URL": "",
				"Title": "",
				"Description": "",
				"Width": "",
				"Height": "",
				"Original W x H": "",
				"Float": "",
				"None": "",
				"Left": "",
				"Right": "",
				"Select file from server": ""
			},

			link: {
				"Insert Link": "",
				"Link URL": "",
				"Link Title": "",
				"Link Target": ""
			},

			table: {
				"Insert table": "",
				"Count of columns": "",
				"Count of rows": ""
			}
		}
	};
})(jQuery);