<?php
/**
 * Category helper. Displays categories on the front-end.
 *
 * @package    Category
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class category_Core {

		/**
		 * Displays a single category checkbox.
		 */
	public static function display_category_checkbox($category, $selected_categories, $form_field, $enable_parents = FALSE)
	{
		$html = '';

		$cid = $category->id;

		// Get locale
		$l = Kohana::config('locale.language.0');

		$translated_title = Category_Lang_Model::category_title($cid, $l);
		
		$category_title = ($translated_title)? $translated_title :  $category->category_title;

		//$category_title = $category->category_title;
		$category_color = $category->category_color;

		// Category is selected.
		$category_checked = in_array($cid, $selected_categories);

		$disabled = "";
		if (!$enable_parents AND $category->children->count() > 0)
		{
			$disabled = " disabled=\"disabled\"";
		}

		$html .= form::checkbox($form_field.'[]', $cid, $category_checked, ' class="check-box"'.$disabled);
		$html .= $category_title;

		return $html;
	}


	/**
	 * Display category tree with input checkboxes.
	 */
	public static function tree($categories, array $selected_categories, $form_field, $columns = 1, $enable_parents = FALSE)
	{
		$html = '';

		// Validate columns
		$columns = (int) $columns;
		if ($columns == 0)
		{
			$columns = 1;
		}

		$categories_total = $categories->count();

		// Format categories for column display.
		$this_col = 1; // column number
		$maxper_col = round($categories_total/$columns); // Maximum number of elements per column
		$i = 1;  // Element Count
		foreach ($categories as $category)
		{

			// If this is the first element of a column, start a new UL
			if ($i == 1)
			{
				$html .= '<ul id="category-column-'.$this_col.'">';
			}

			// Display parent category.
			$html .= '<li>';
			$html .= category::display_category_checkbox($category, $selected_categories, $form_field, $enable_parents);

			// Display child categories.
			if ($category->children->count() > 0)
			{
				$html .= '<ul>';
				foreach ($category->children as $child)
				{
					$html .= '<li>';
					$html .= category::display_category_checkbox($child, $selected_categories, $form_field, $enable_parents);
				}
				$html .= '</ul>';
			}
			$i++;

			// If this is the last element of a column, close the UL
			if ($i > $maxper_col || $i == $categories_total)
			{
				$html .= '</ul>';
				$i = 1;
				$this_col++;
			}
		}

		return $html;
	}
	
	/**
	 * Generates a category tree view - recursively iterates
	 *
	 * @return string
	 */
	public static function get_category_tree_view()
	{
		// To hold the category data
		$category_data = array();
		
		// Fetch all the top level parent categories
		foreach (Category_Model::get_categories() as $category)
		{
			self::_extend_category_data($category_data, $category);
		}
		
		// Get the table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		// Fetch the other categories
		$sql = "SELECT c.id, c.parent_id, c.category_title, c.category_color, COUNT(ic.incident_id) report_count "
			. "FROM ".$table_prefix."category c "
			. "INNER JOIN ".$table_prefix."incident_category ic ON (ic.category_id = c.id) "
			. "WHERE c.category_visible = 1 "
			. "GROUP BY c.category_title "
			. "ORDER BY c.category_title ASC";
		
		// Add child categories
		foreach (Database::instance()->query($sql) as $category)
		{
			// Extend the category data array
			if (self::_extend_category_data($category_data, $category))
			{
				// Add children
				$category_data[$category->parent_id]['children'][$category->id] = array(
					'category_title' => $category->category_title,
					'parent_id' => $category->parent_id,
					'category_color' => $category->category_color,
					'report_count' => $category->report_count,
					'children' => array()
				);
			
				// Update the report count
				Kohana::log('debug', Kohana::debug($category));
				$category_data[$category->parent_id]['report_count'] += $category->report_count;
			}
		}
		
		// Generate and return the HTML
		return self::_generate_treeview_html($category_data);
	}
	
	/**
	 * Helper method for adding parent categories to the category data
	 *
	 * @param array $array Pointer to the array containing the category data
	 * @param mixed $category Object Category object to be added tot he array
	 */
	private static function _extend_category_data(array & $array, $category)
	{
		// Check if the category is a top-level parent category
		$temp_category = ($category->parent_id == 0)? $category : ORM::factory('category', $category->parent_id);
		
		if ( ! $temp_category->loaded)
			return FALSE;
		
		// Extend the array
		if ( ! array_key_exists($temp_category->id, $array))
		{
			$report_count = property_exists($temp_category, 'report_count')? $temp_category->report_count : 0;
			$array[$temp_category->id] = array(
				'category_title' => $temp_category->category_title,
				'parent_id' => $temp_category->parent_id,
				'category_color' => $temp_category->category_color,
				'report_count' => $report_count,
				'children' => array()
			);
		}
		
		// Garbage collection
		unset ($temp_category);
		
		return TRUE;
	}
	
	/**
	 * Traverses an array containing category data and returns a tree view
	 *
	 * @param array $category_data
	 * @return string
	 */
	private static function _generate_treeview_html($category_data)
	{
		// To hold the treeview HTMl
		$tree_html = "";
		
		foreach ($category_data as $id => $category)
		{
			// Determine the category class
			$category_class = ($category['parent_id'] > 0)? " class=\"report-listing-category-child\"" : "";
			
			$tree_html .= "<li".$category_class.">"
							. "<a href=\"#\" class=\"cat_selected\" id=\"filter_link_cat_".$id."\">"
							. "<span class=\"item-swatch\" style=\"background-color: #".$category['category_color']."\">&nbsp;</span>"
							. "<span class=\"item-title\">".htmlspecialchars($category['category_title'])."</span>"
							. "<span class=\"item-count\">".$category['report_count']."</span>"
							. "</a></li>";
							
			$tree_html .= self::_generate_treeview_html($category['children']);
		}
		
		// Return
		return $tree_html;
	}
}
