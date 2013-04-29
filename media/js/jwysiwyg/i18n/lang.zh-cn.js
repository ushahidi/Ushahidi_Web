/**
 * Internationalization: Chinese (Simplified) language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 * 
 * By: https://github.com/mengxy
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.zh-cn.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.zh-cn.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang['zh-cn'] = {
		controls: {
			"Bold": "加粗",
			"Colorpicker": "取色器",
			"Copy": "复制",
			"Create link": "创建链接",
			"Cut": "剪切",
			"Decrease font size": "减小字号",
			"Fullscreen": "全屏",
			"Header 1": "标题1",
			"Header 2": "标题2",
			"Header 3": "标题3",
			"View source code": "查看源码",
			"Increase font size": "增大字号",
			"Indent": "缩进",
			"Insert Horizontal Rule": "插入水平线",
			"Insert image": "插入图片",
			"Insert Ordered List": "插入有序列表",
			"Insert table": "插入表格",
			"Insert Unordered List": "插入无序列表",
			"Italic": "斜体",
			"Justify Center": "居中对齐",
			"Justify Full": "填充整行",
			"Justify Left": "左对齐",
			"Justify Right": "右对齐",
			"Left to Right": "从左到右",
			"Outdent": "取消缩进",
			"Paste": "粘贴",
			"Redo": "前进",
			"Remove formatting": "清除格式",
			"Right to Left": "从右到左",
			"Strike-through": "删除线",
			"Subscript": "上角标",
			"Superscript": "下角标",
			"Underline": "下划线",
			"Undo": "撤销"
		},

		dialogs: {
			// for all
			"Apply": "应用",
			"Cancel": "取消",

			colorpicker: {
				"Colorpicker": "取色器",
				"Color": "颜色"
			},

			image: {
				"Insert Image": "插入图片",
				"Preview": "预览",
				"URL": "URL",
				"Title": "标题",
				"Description": "描述",
				"Width": "宽度",
				"Height": "高度",
				"Original W x H": "原始宽高",
				"Float": "浮动",
				"None": "无",
				"Left": "左",
				"Right": "右"
			},

			link: {
				"Insert Link": "插入链接",
				"Link URL": "链接URL",
				"Link Title": "链接Title",
				"Link Target": "链接Target"
			},

			table: {
				"Insert table": "插入表格",
				"Count of columns": "列数",
				"Count of rows": "行数"
			}
		}
	};
})(jQuery);
