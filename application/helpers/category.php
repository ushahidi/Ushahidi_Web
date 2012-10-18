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
	private static function display_category_checkbox($category, $selected_categories, $form_field, $enable_parents = FALSE)
	{
		$html = '';
		
		$cid = $category['category_id'];

		// Category is selected.
		$category_checked = in_array($cid, $selected_categories);

		$disabled = "";
		if (!$enable_parents AND count($category['children']) > 0)
		{
			$disabled = " disabled=\"disabled\"";	
		}

		$html .= "<label>";
		$html .= form::checkbox($form_field.'[]', $cid, $category_checked, ' class="check-box"'.$disabled);
		$html .= $category['category_title'];
		$html .= "</label>";

		return $html;
	}

	/**
	 * Display category tree with input checkboxes.
	 * @deprecated replaced by form_tree()
	 **/
	public static function tree($categories, $hide_children, array $selected_categories = array(), $form_field, $columns = 1, $enable_parents = FALSE)
	{
		Kohana::log('alert', 'category::tree() in deprecated and replaced by category::form_tree()');
		return self::form_tree($form_field, $selected_categories, $columns, $enable_parents, ! $hide_children);
	}

	/**
	 * Display category tree with input checkboxes for forms
	 * 
	 * @param string $form_field form field name
	 * @param array $selected_categories Categories that should be already selected
	 * @param int $columns number of columns to display
	 * @param bool $enable_parents Can parent categoires be select
	 * @param bool $show_hidden Show hidden categories
	 */
	public static function form_tree($form_field, array $selected_categories = array(), $columns = 1, $enable_parents = FALSE, $show_hidden = FALSE)
	{
		$category_data = self::get_category_tree_data(FALSE, $show_hidden);
		
		$html = '';

		// Validate columns
		$columns = (int) $columns;
		if ($columns == 0)
		{
			$columns = 1;
		}

		$categories_total = count($category_data);

		// Format categories for column display.
		// Column number
		$this_col = 1;

		// Maximum number of elements per column
		$maxper_col = round($categories_total / $columns);

		$i = 1;  // Element Count
		foreach ($category_data as $category)
		{

			// If this is the first element of a column, start a new UL
			if ($i == 1)
			{
				$html .= '<ul class="category-column category-column-'.$this_col.'">';
			}

			// Display parent category.
			$html .= '<li title="'.$category['category_description'].'">';
			$html .= category::display_category_checkbox($category, $selected_categories, $form_field, $enable_parents);
			
			// Display child categories.
			if (count($category['children']) > 0)
			{
				$html .= '<ul>';
				foreach ($category['children'] as $child)
				{
					$html .= '<li title="'.$child['category_description'].'">';
					$html .= category::display_category_checkbox($child, $selected_categories, $form_field, $enable_parents);
				}
				$html .= '</ul>';
			}

			// If this is the last element of a column, close the UL
			if ($i > $maxper_col OR $i == $categories_total)
			{
				$html .= '</ul>';
				$i = 1;
				$this_col++;
			}
			else
			{
				$i++;
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
		$category_data = self::get_category_tree_data(TRUE);
		
		// Generate and return the HTML
		return self::_generate_treeview_html($category_data);
	}
	
	/**
	 * Get categories as an tree array
	 * @param bool Get category count?
	 * @param bool Include hidden categories
	 * @return array
	 **/
	public static function get_category_tree_data($count = FALSE, $include_hidden = FALSE)
	{
		
		// To hold the category data
		$category_data = array();
		
		// Database table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		// Database instance
		$db = new Database();
		
		// Fetch the other categories
		if ($count)
		{
			$sql = "SELECT c.id, c.parent_id, c.category_title, c.category_color, c.category_image, c.category_image_thumb, COUNT(i.id) report_count "
				. "FROM ".$table_prefix."category c "
				. "LEFT JOIN ".$table_prefix."category c_parent ON (c.parent_id = c_parent.id) "
				. "LEFT JOIN ".$table_prefix."incident_category ic ON (ic.category_id = c.id) "
				. "LEFT JOIN ".$table_prefix."incident i ON (ic.incident_id = i.id AND i.incident_active = 1 ) "
				. "WHERE 1=1 "
				. (!$include_hidden ? "AND c.category_visible = 1 " : "")
				. (!$include_hidden ? "AND (c_parent.category_visible = 1 OR c.parent_id = 0)" : "") // Parent must be visible, or must be top level
				. "GROUP BY c.id "
				. "ORDER BY c.category_position ASC";
		}
		else
		{
			$sql = "SELECT c.id, c.parent_id, c.category_title, c.category_color, c.category_image, c.category_image_thumb "
				. "FROM ".$table_prefix."category c "
				. "LEFT JOIN ".$table_prefix."category c_parent ON (c.parent_id = c_parent.id) "
				. "WHERE 1=1 "
				. (!$include_hidden ? "AND c.category_visible = 1 " : "")
				. (!$include_hidden ? "AND (c_parent.category_visible = 1 OR c.parent_id = 0)" : "") // Parent must be visible, or must be top level
				. "ORDER BY c.category_position ASC";
		}
		
		// Create nested array - all in one pass
		foreach ($db->query($sql) as $category)
		{
			// If we didn't fetch report_count set fake value
			if (!$count)
			{
			$category->report_count = 0;
			}
			
			// If this is a parent category, just add it to the array
			if ($category->parent_id == 0)
			{
				// save children and report_count if already been created.
				$children = isset($category_data[$category->id]['children']) ? $category_data[$category->id]['children'] : array();
				$report_count = isset($category_data[$category->id]['report_count']) ? $category_data[$category->id]['report_count'] : 0;
				
				$category_data[$category->id] = array(
					'category_id' => $category->id,
					'category_title' => htmlentities(Category_Lang_Model::category_title($category->id), ENT_QUOTES, "UTF-8"),
					'category_description' => htmlentities(Category_Lang_Model::category_description($category->id), ENT_QUOTES, "UTF-8"),
					'category_color' => $category->category_color,
					'category_image' => $category->category_image,
					'children' => $children,
					'category_image_thumb' => $category->category_image_thumb,
					'parent_id' => $category->parent_id,
					'report_count' => $category->report_count + $report_count
				);
			}
			// If this is a child, add it underneath its parent category
			else
			{
				// If we haven't processed the parent yet, add placeholder parent category
				if (! array_key_exists($category->parent_id, $category_data))
				{
					$category_data[$category->parent_id] = array(
						'category_id' => $category->parent_id,
						'category_title' => '',
						'category_description' => '',
						'parent_id' => 0,
						'category_color' => '',
						'category_image' => '',
						'category_image_thumb' => '',
						'children' => array(),
						'report_count' => 0
					);
				}
				
				// Add children
				$category_data[$category->parent_id]['children'][$category->id] = array(
					'category_id' => $category->id,
					'category_title' => htmlentities(Category_Lang_Model::category_title($category->id), ENT_QUOTES, "UTF-8"),
					'category_description' => htmlentities(Category_Lang_Model::category_description($category->id), ENT_QUOTES, "UTF-8"),
					'parent_id' => $category->parent_id,
					'category_color' => $category->category_color,
					'category_image' => $category->category_image,
					'category_image_thumb' => $category->category_image_thumb,
					'report_count' => $category->report_count,
					'children' => array()
				);
				// Add to parent report count too
				$category_data[$category->parent_id]['report_count'] += $category->report_count;
			}
		}

		return $category_data;
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
							. "<a href=\"#\" class=\"cat_selected\" id=\"filter_link_cat_".$id."\" title=\"{$category['category_description']}\">"
							. "<span class=\"item-swatch\" style=\"background-color: #".$category['category_color']."\">$category_image</span>"
							. "<span class=\"item-title\">".strip_tags($category['category_title'])."</span>"
							. "<span class=\"item-count\">".$category['report_count']."</span>"
							. "</a></li>";
							
			$tree_html .= self::_generate_treeview_html($category['children']);
		}
		
		// Return
		return $tree_html;
	}
}
