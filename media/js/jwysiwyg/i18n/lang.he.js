/**
 * Internationalization: English language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 * 
 * By: 	Tudmotu, frost-nzcr4 on github.com
 * 		Yotam Bar-On
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.he.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.he.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.he = {
		controls: {
			"Bold": "מודגש",
			"Colorpicker": "פלטת צבעים",
			"Copy": "העתק",
			"Create link": "צור קישור",
			"Cut": "חתוך",
			"Decrease font size": "הקטן גופן",
			"Fullscreen": "מסך מלא",
			"Header 1": "כותרת 1",
			"Header 2": "כותרת 2",
			"Header 3": "כותרת 3",
			"View source code": "הצג קוד מקור",
			"Increase font size": "הגדל גופן",
			"Indent": "הגדל הזחה",
			"Insert Horizontal Rule": "הכנס קו אופקי",
			"Insert image": "הוסף תמונה",
			"Insert Ordered List": "הוספך רשימה ממוספרת",
			"Insert table": "הוסף טבלה",
			"Insert Unordered List": "הוספת רשימה בלתי ממוספרת",
			"Italic": "נטוי",
			"Justify Center": "מרכז",
			"Justify Full": "יישור לשוליים",
			"Justify Left": "הצמד לשמאל",
			"Justify Right": "הצמד לימין",
			"Left to Right": "שמאל לימין",
			"Outdent": "הורד הזחה",
			"Paste": "הדבק",
			"Redo": "עשה שוב",
			"Remove formatting": "הסר עיצוב",
			"Right to Left": "ימין לשמאל",
			"Strike-through": "כיתוב מחוק",
			"Subscript": "כתיב עילי",
			"Superscript": "כתיב תחתי",
			"Underline": "קו תחתון",
			"Undo": "בטל פעולה"
		},

		dialogs: {
			// for all
			"Apply": "החל",
			"Cancel": "בטל",

			colorpicker: {
				"Colorpicker": "פלטת צבעים",
				"Color": "צבע"
			},

			image: {
				"Insert Image": "הכנס תמונה",
				"Preview": "תצוגה מקדימה",
				"URL": "כתובת רשת",
				"Title": "כותרת",
				"Description": "תיאור",
				"Width": "רוחב",
				"Height": "גובה",
				"Original W x H": "מימדים מקוריים",
				"Float": "צף",
				"None": "שום כיוון",
				"Left": "שמאל",
				"Right": "ימין"
			},

			link: {
				"Insert Link": "צור קישור",
				"Link URL": "כתובת רשת",
				"Link Title": "כותרת",
				"Link Target": "מטרה"
			},

			table: {
				"Insert table": "הוסף טבלה",
				"Count of columns": "מספר עמודות",
				"Count of rows": "מספר שורות"
			}
		}
	};
})(jQuery);
