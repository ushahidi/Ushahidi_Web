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
	 * @param int $parent_id Parent category whose tree is to be generated
	 * @param bool $show_incident_count When TRUE, shows the no. of reports under each category
	 * @return string
	 */
	public static function get_category_tree_view($parent_id = 0, $show_report_count = FALSE)
	{
		// To hold the return string
		$category_tree_html = "";
		
		// Get the the child categories
		$categories = Category_Model::get_categories($parent_id);
		foreach ($categories as $category)
		{
			// Get the category class
			$category_class = ($category->parent_id > 0)? " class=\"report-listing-category-child\"" : "";
			
			$category_tree_html .= "<li".$category_class.">"
							. "<a href=\"#\" class=\"cat_selected\" id=\"filter_link_cat_".$category->id."\">"
							. "<span class=\"item-swatch\" style=\"background-color: #".$category->category_color."\">&nbsp;</span>"
							. "<span class=\"item-title\">".$category->category_title."</span>";
			
			// Check if the report count is to be shown alongside each category
			if ($show_report_count)
			{
				$category_tree_html .= "<span class=\"item-count\" id=\"report_cat_count_".$category->id."\">".Category_Model::get_report_count($category->id)."</span>";
			}
			
			// Close the category link
			$category_tree_html .= "</a></li>";
			
			// Fetch the children
			$category_tree_html .= self::get_category_tree_view($category->id, $show_report_count);
			
			// $category_tree_html .= "</li>";
		}
		// Return the listing
		return $category_tree_html;
	}
}
