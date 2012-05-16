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
		
		$category_title = Category_Lang_Model::category_title($cid, $l);

		//$category_title = $category->category_title;
		$category_color = $category->category_color;

		// Category is selected.
		$category_checked = in_array($cid, $selected_categories);
		
		// Visible Child Count
		$vis_child_count = 0;
		foreach ($category->children as $child)
		{
			$child_visible = $child->category_visible;
			if ($child_visible)
			{
				// Increment Visible Child count
				++$vis_child_count;
			}
		}

		$disabled = "";
		if (!$enable_parents AND $category->children->count() > 0 AND $vis_child_count >0)
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
	public static function tree($categories,$hide_children = TRUE, array $selected_categories, $form_field, $columns = 1, $enable_parents = FALSE)
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
			$html .= '<li title="'.$category->category_description.'">';
			$html .= category::display_category_checkbox($category, $selected_categories, $form_field, $enable_parents);
			
			// Visible Child Count
			$vis_child_count = 0;
			foreach ($category->children as $child)
			{
				// If we don't want to show a category's hidden children
				if($hide_children == TRUE)
				{
					$child_visible = $child->category_visible;
					if ($child_visible)
					{
						// Increment Visible Child count
						++$vis_child_count;
					}
				}
				else
				{
					++$vis_child_count;
				}
			}
			// Display child categories.
			if ($category->children->count() > 0 AND $vis_child_count > 0)
			{
				$html .= '<ul>';
				foreach ($category->children as $child)
				{
					if($hide_children)
					{
						$child_visible = $child->category_visible;
						if ($child_visible)
						{
							$html .= '<li>';
							$html .= category::display_category_checkbox($child, $selected_categories, $form_field, $enable_parents);
						}
					}
					else
					{
						$html .= '<li title="'.$child->category_description.'">';
						$html .= category::display_category_checkbox($child, $selected_categories, $form_field, $enable_parents);
					}
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
		
		// Database table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		// Database instance
		$db = new Database();
		
		// Fetch all the top level parent categories
		foreach (Category_Model::get_categories(0,FALSE,TRUE) as $category)
		{
			self::_extend_category_data($category_data, $category);
		}
		
		// NOTES: Emmanuel Kala - Aug 5, 2011
		// Initialize the report totals for the parent categories just in case
		// the deployment does not have sub-categories in which case the query
		// below won't return a result
		self::_init_parent_category_report_totals($category_data, $table_prefix);
		
		// Query to fetch the report totals for the parent categories
		$sql = "SELECT c_parent.id,  COUNT(DISTINCT ic.incident_id)  AS report_count "
			. "FROM ".$table_prefix."category c "
			. "INNER JOIN ".$table_prefix."category c_parent ON (c.parent_id = c_parent.id) "
			. "INNER JOIN ".$table_prefix."incident_category ic ON (ic.category_id = c.id OR ic.category_id = c_parent.id) "
			. "INNER JOIN ".$table_prefix."incident i ON (ic.incident_id = i.id) "
			. "WHERE i.incident_active = 1 "
			. "AND c_parent.category_visible = 1 "
			. "AND c.category_visible = 1 "
			. "AND c_parent.parent_id = 0 "
			. "AND c_parent.category_title != \"NONE\""
			. "GROUP BY c_parent.id "
			. "ORDER BY c_parent.id ASC";
		
		// Update the report_count field of each top-level category
		foreach ($db->query($sql) as $category_total)
		{
			// Check if the category exists
			if (array_key_exists($category_total->id, $category_data))
			{
				// Update
				$category_data[$category_total->id]['report_count'] = $category_total->report_count;
			}
		}
		
		// Fetch the other categories
		$sql = "SELECT c.id, c.parent_id, c.category_title, c.category_color, c.category_image, c.category_image_thumb, COUNT(i.id) report_count "
			. "FROM ".$table_prefix."category c "
			. "LEFT JOIN ".$table_prefix."incident_category ic ON (ic.category_id = c.id) "
			. "LEFT JOIN ".$table_prefix."incident i ON (ic.incident_id = i.id AND i.incident_active = 1 ) "
			. "WHERE c.category_visible = 1 "
			. "AND c.category_title != \"NONE\" "
			. "GROUP BY c.id "
			. "ORDER BY c.category_title ASC";
		
		// Add child categories
		foreach ($db->query($sql) as $category)
		{
			// Extend the category data array
			self::_extend_category_data($category_data, $category);
			
			if (array_key_exists($category->parent_id, $category_data))
			{
				// Add children
				$category_data[$category->parent_id]['children'][$category->id] = array(
					'category_title' => Category_Lang_Model::category_title($category->id,Kohana::config('locale.language.0')),
					'parent_id' => $category->parent_id,
					'category_color' => $category->category_color,
					'category_image' => $category->category_image,
					'category_image_thumb' => $category->category_image_thumb,
					'report_count' => $category->report_count,
					'children' => array()
				);
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
		$temp_category = ($category->parent_id == 0) ? $category : ORM::factory('category', $category->parent_id);

		if ($temp_category instanceof Category_Model AND ! $temp_category->loaded)
			return FALSE;

		// Extend the array
		if ( ! array_key_exists($temp_category->id, $array))
		{
			// Get the report count
			$report_count = property_exists($temp_category, 'report_count')? $temp_category->report_count : 0;
			
			$array[$temp_category->id] = array(
				'category_title' => Category_Lang_Model::category_title($temp_category->id,Kohana::config('locale.language.0')),
				'parent_id' => $temp_category->parent_id,
				'category_color' => $temp_category->category_color,
				'category_image_thumb' => $temp_category->category_image_thumb,
				'category_image' => $temp_category->category_image,
				'report_count' => $report_count,
				'children' => array()
			);

			return TRUE;
		}

		return FALSE;
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
			
			$category_image = $category['category_image_thumb'] ? html::image(array('src'=> url::convert_uploaded_to_abs($category['category_image_thumb']), 'style'=>'float:left;padding-right:5px;')) : NULL;
			
			$tree_html .= "<li".$category_class.">"
							. "<a href=\"#\" class=\"cat_selected\" id=\"filter_link_cat_".$id."\">"
							. "<span class=\"item-swatch\" style=\"background-color: #".$category['category_color']."\">$category_image</span>"
							. "<span class=\"item-title\">".strip_tags($category['category_title'])."</span>"
							. "<span class=\"item-count\">".$category['report_count']."</span>"
							. "</a></li>";
							
			$tree_html .= self::_generate_treeview_html($category['children']);
		}
		
		// Return
		return $tree_html;
	}
	
	/**
	 * Initializes the report totals for the parent categories
	 *
	 * @param array $category_data Array of the parent categories
	 * @param string $table_prefix Database table prefix
	 */
	private static function _init_parent_category_report_totals(array & $category_data, $table_prefix)
	{
		// Query to fetch the report totals for the parent categories
		$sql = "SELECT c.id, COUNT(DISTINCT ic.incident_id) AS report_count "
			. "FROM ".$table_prefix."category c "
			. "INNER JOIN ".$table_prefix."incident_category ic ON (ic.category_id = c.id) "
			. "INNER JOIN ".$table_prefix."incident i ON (ic.incident_id = i.id) "
			. "WHERE c.category_visible = 1 "
			. "AND i.incident_active = 1 "
			. "AND c.parent_id = 0 "
			. "GROUP BY c.id";
		
		// Fetch the records
		$result = Database::instance()->query($sql);
		
		// Set the report totals for each of the parent categorie
		foreach ($result as $category)
		{
			if (array_key_exists($category->id, $category_data))
			{
				$category_data[$category->id]['report_count'] = $category->report_count;
			}
		}
		
		// Garbage collection
		unset ($sql, $result);
	}
}
