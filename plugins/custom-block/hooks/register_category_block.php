<?php defined('SYSPATH') or die('No direct script access.');

// Start category block
class category_block { // CHANGE THIS FOR OTHER BLOCKS

	public function __construct()
	{
		// Array of block params
		$block = array(
			"classname" => "category_block", // Must match class name aboce
			"name" => "Custom Category Reports",
			"description" => "List the 10 latest reports in a custom category (defined on code)"
		);
		// register block with core, this makes it available to users
		blocks::register($block);
	}

	public function block()
	{
		// Load the reports block view
		$content = new View('category_block'); // CHANGE THIS IF YOU WANT A DIFFERENT VIEW

		// ID of the category we're looking for
		$category_id = 1; // CHANGE THIS
		$ids = array();
		$ids[] = $category_id;

		$categories = ORM::factory('category')
			->where('parent_id', $category_id)
			->find_all();

		foreach ($categories as $category) {
	        $ids[] = $category->id;
	    }

		$content->category = $category_id;

		// Get Reports
		$content->incidents = ORM::factory('incident')
			->with('location')
			->join('incident_category', 'incident.id', 'incident_category.incident_id')
			->where('incident_active', '1')
			->where('category_id', 'IN', $ids)
			->limit('10')
			->orderby('incident_date', 'desc')
			->find_all();
		echo $content;
	}
}
new category_block;
