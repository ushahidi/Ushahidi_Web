/*
		* Categories Javascript
		*/
		// Categories JS
		function fillFields(id, category_title, category_description, category_color)
		{
			$("#category_id").attr("value", unescape(id));
			$("#category_title").attr("value", unescape(category_title));
			$("#category_description").attr("value", unescape(category_description));
			$("#category_color").attr("value", unescape(category_color));
		}