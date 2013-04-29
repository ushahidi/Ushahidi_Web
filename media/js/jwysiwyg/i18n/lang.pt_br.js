/**
 * Internationalization: Brazilian Portugese language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: Marcelo Wergles <mwergles@gmail.com>
 *
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.pt_br.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.pt_br.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.pt_br = {
		controls: {
			"Bold": "Negrito",
			"Colorpicker": "Escolher uma cor",
			"Copy": "Copiar",
			"Create link": "Criar link",
			"Cut": "Recortar",
			"Decrease font size": "Diminuir o tamanho da fonte",
			"Fullscreen": "Tela cheia",
			"Header 1": "Título 1",
			"Header 2": "Título 2",
			"Header 3": "Título 3",
			"View source code": "Ver código fonte",
			"Increase font size": "Aumentar o tamanho da fonte",
			"Indent": "Aumentar recuo",
			"Insert Horizontal Rule": "Inserir linha horizontal",
			"Insert image": "Inserir imagem",
			"Insert Ordered List": "Inserir numeração",
			"Insert table": "Inserir tabela",
			"Insert Unordered List": "Inserir marcadores",
			"Italic": "Itálico",
			"Justify Center": "Centralizar",
			"Justify Full": "Justificar",
			"Justify Left": "Alinhar à esquerda",
			"Justify Right": "Alinhar à direita",
			"Left to Right": "Esquerda à direita",
			"Outdent": "Diminuir recuo",
			"Paste": "Colar",
			"Redo": "Refazer",
			"Remove formatting": "Remover formatação",
			"Right to Left": "Direita à esquerda",
			"Strike-through": "Riscar",
			"Subscript": "Subscrito",
			"Superscript": "Sobrescrito",
			"Underline": "Sublinhar",
			"Undo": "Desfazer"
		},

		dialogs: {
			// for all
			"Apply": "Aplicar",
			"Cancel": "Cancelar",

			colorpicker: {
				"Colorpicker": "Escolher uma cor",
				"Color": "Cor"
			},

			image: {
				"Insert Image": "Inserir Imagem",
				"Preview": "Pré-visualizar",
				"URL": "URL",
				"Title": "Título",
				"Description": "Descrição",
				"Width": "Largura",
				"Height": "Altura",
				"Original W x H": "L x A original",
				"Float": "",
				"None": "",
				"Left": "",
				"Right": ""
			},

			link: {
				"Insert Link": "Inserir Link",
				"Link URL": "URL do link",
				"Link Title": "Título do link",
				"Link Target": "Alvo do link"
			},

			table: {
				"Insert table": "Inserir tabela",
				"Count of columns": "Número de colunas",
				"Count of rows": "Número de linhas"
			}
		}
	};
})(jQuery);
