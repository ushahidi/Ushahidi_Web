/**
 * Internationalization: Turkish language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: Kadir Atesoglu <kadir.atesoglu@gmail.com>
 *
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.tr.js, $.wysiwyg olmadan çalışamaz";
	}
	if (undefined === $.wysiwyg.i18n) {
	    throw "lang.tr.js, $.wysiwyg.i18n olmadan çalışamaz";
	}

	$.wysiwyg.i18n.lang.de = {
		controls: {
			"Bold": "Kalın",
			"Colorpicker": "Renk Seçimi",
			"Copy": "Kopyala",
			"Create link": "Link Oluştur",
			"Cut": "Kes",
			"Decrease font size": "Font Küçült",
			"Fullscreen": "Tam Ekran",
			"Header 1": "Başlık 1",
			"Header 2": "Başlık 2",
			"Header 3": "Başlık 3",
			"View source code": "Kaynak Kod",
			"Increase font size": "Font Büyült",
			"Indent": "Girinti",
			"Insert Horizontal Rule": "Yatay Çizgi Ekle",
			"Insert image": "Resim Ekle",
			"Insert Ordered List": "Sıralı Liste Ekle",
			"Insert table": "Tablo Ekle",
			"Insert Unordered List": "Sırasız Liste Ekle",
			"Italic": "İtalik",
			"Justify Center": "Ortala",
			"Justify Full": "İki Yana Dayalı",
			"Justify Left": "Sola Dayalı",
			"Justify Right": "Sağa Dayalı",
			"Left to Right": "Soldan Sağa",
			"Outdent": "Çıkıntı",
			"Paste": "Yapıştır",
			"Redo": "Yinele",
			"Remove formatting": "Format Temizle",
			"Right to Left": "Sağdan Sola",
			"Strike-through": "Üstçizgi",
			"Subscript": "Kök",
			"Superscript": "Kare",
			"Underline": "Altçizgi",
			"Undo": "Geri Al"
		},

		dialogs: {
			// for all
			"Apply": "Uygula",
			"Cancel": "Vazgeç",

			colorpicker: {
				"Colorpicker": "Renk Seç",
				"Color": "Renk"
			},

			image: {
				"Insert Image": "Resim Ekle",
				"Preview": "Önizleme",
				"URL": "Link",
				"Title": "Başlık",
				"Description": "Açıklama",
				"Width": "Genişlik",
				"Height": "Yükseklik",
				"Original W x H": "Orjinal Genişlik * Yükseklik",
				"Float": "Hizalama",
				"None": "Yok",
				"Left": "Sol",
				"Right": "Sağ"
			},

			link: {
				"Insert Link": "Link Ekle",
				"Link URL": "Link Adresi",
				"Link Title": "Link Başlığı",
				"Link Target": "Link Davranışı"
			},

			table: {
				"Insert table": "Tablo Ekle",
				"Count of columns": "Sütun Sayısı",
				"Count of rows": "Satır Sayısı"
			}
		}
	};
})(jQuery);
