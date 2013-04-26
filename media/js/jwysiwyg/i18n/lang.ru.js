/**
 * Internationalization: Russian language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 * 
 * By: frost-nzcr4 on github.com
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.ru.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.ru.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.ru = {
		controls: {
			"Bold": "Жирный",
			"Colorpicker": "Выбор цвета",
			"Copy": "Копировать",
			"Create link": "Создать ссылку",
			"Cut": "Вырезать",
			"Decrease font size": "Уменьшить шрифт",
			"File Manager": "Управление файлами",
			"Fullscreen": "На весь экран",
			"Header 1": "Заголовок 1",
			"Header 2": "Заголовок 2",
			"Header 3": "Заголовок 3",
			"View source code": "Посмотреть исходный код",
			"Increase font size": "Увеличить шрифт",
			"Indent": "Отступ",
			"Insert Horizontal Rule": "Вставить горизонтальную прямую",
			"Insert image": "Вставить изображение",
			"Insert Ordered List": "Вставить нумерованный список",
			"Insert table": "Вставить таблицу",
			"Insert Unordered List": "Вставить ненумерованный список",
			"Italic": "Курсив",
			"Justify Center": "Выровнять по центру",
			"Justify Full": "Выровнять по ширине",
			"Justify Left": "Выровнять по левой стороне",
			"Justify Right": "Выровнять по правой стороне",
			"Left to Right": "Слева направо",
			"Outdent": "Убрать отступ",
			"Paste": "Вставить",
			"Redo": "Вернуть действие",
			"Remove formatting": "Убрать форматирование",
			"Right to Left": "Справа налево",
			"Strike-through": "Зачёркнутый",
			"Subscript": "Нижний регистр",
			"Superscript": "Верхний регистр",
			"Underline": "Подчёркнутый",
			"Undo": "Отменить действие"
		},

		dialogs: {
			// for all
			"Apply": "Применить",
			"Cancel": "Отмена",

			colorpicker: {
				"Colorpicker": "Выбор цвета",
				"Color": "Цвет"
			},

			fileManager: {
				"file_manager": 		"Управление файлами",
				"upload_title":			"Загрузить файл",
				"rename_title":			"Переименовать файл",
				"remove_title":			"Удалить файл",
				"mkdir_title":			"Создать папку",
				"upload_action": 		"Загружает новый файл в текущую папку",
				"mkdir_action": 		"Создаёт новую папку",
				"remove_action": 		"Удалить этот файл",
				"rename_action": 		"Переименовать этот файл" ,	
				"delete_message": 		"Хотите удалить этот файл?",
				"new_directory": 		"Новая папка",
				"previous_directory": 	"Вернуться к предыдущей папке",
				"rename":				"Переименовать",
				"select": 				"Выбрать",
				"create": 				"Создать",
				"submit": 				"Послать",
				"cancel": 				"Отмена",
				"yes":					"Да",
				"no":					"Нет"
			},

			image: {
				"Insert Image": "Вставить изображение",
				"Preview": "Просмотр",
				"URL": "URL адрес",
				"Title": "Название",
				"Description": "Альт. текст",
				"Width": "Ширина",
				"Height": "Высота",
				"Original W x H": "Оригинальные Ш x В",
				"Float": "Положение",
				"None": "Не выбрано",
				"Left": "Слева",
				"Right": "Справа",
				"Select file from server": "Выбрать файл с сервера"
			},

			link: {
				"Insert Link": "Вставить ссылку",
				"Link URL": "URL адрес",
				"Link Title": "Название",
				"Link Target": "Цель"
			},

			table: {
				"Insert table": "Вставить таблицу",
				"Count of columns": "Кол-во колонок",
				"Count of rows": "Кол-во строк"
			}
		}
	};
})(jQuery);