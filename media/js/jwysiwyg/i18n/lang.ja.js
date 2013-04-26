/**
 * Internationalization: japanese language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: https://github.com/rosiro
 *
 */

(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.ja.js depends on $.wysiwyg";
		return false;
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.ja.js depends on $.wysiwyg.i18n";
		return false;
	}

	$.wysiwyg.i18n.lang.ja = {
		controls: {
			"Bold": "ボールド",
			"Copy": "コピー",
			"Create link": "リンク作成",
			"Cut": "切り取り",
			"Decrease font size": "フォントサイズを小さく",
			"Header 1": "見出し１",
			"Header 2": "見出し２",
			"Header 3": "見出し３",
			"View source code": "ソースコードを見る",
			"Increase font size": "フォントサイズを大きく",
			"Indent": "インデント",
			"Insert Horizontal Rule": "水平線<HR>を挿入",
			"Insert image": "画像を挿入",
			"Insert Ordered List": "順序付きリストの追加",
			"Insert table": "テーブルを挿入",
			"Insert Unordered List": "順序なしリストを追加",
			"Italic": "イタリック",
			"Justify Center": "中央寄せ",
			"Justify Full": "左右一杯に揃える",
			"Justify Left": "左寄せ",
			"Justify Right": "右寄せ",
			"Left to Right": "左から右へ",
			"Outdent": "インデント解除",
			"Paste": "貼り付け",
			"Redo": "やり直し",
			"Remove formatting": "書式設定を削除",
			"Right to Left": "右から左へ",
			"Strike-through": "取り消し線",
			"Subscript": "下付き文字",
			"Superscript": "上付き文字",
			"Underline": "下線",
			"Undo": "元に戻す"
		},

		dialogs: {
			// for all
			"Apply": "適用",
			"Cancel": "キャンセル",

			colorpicker: {
				"Colorpicker": "カラーピッカー",
				"Color": "カラー"
			},

			image: {
				"Insert Image": "画像を挿入",
				"Preview": "プレビュー",
				"URL": "URL",
				"Title": "タイトル",
				"Description": "概要",
				"Width": "横幅",
				"Height": "高さ",
				"Original W x H": "オリジナル 横 x 高",
				"Float": "フロート",
				"None": "画像無し",
				"Left": "左寄せ",
				"Right": "右寄せ"
			},

			link: {
				"Insert Link": "リンクの挿入",
				"Link URL": "リンク URL",
				"Link Title": "リンク タイトル",
				"Link Target": "リンク ターゲット"
			},

			table: {
				"Insert table": "テーブルを挿入",
				"Count of columns": "列数",
				"Count of rows": "行数"
			}
		}
	};
})(jQuery);