<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Provides a table layout for sections in the Profiler library.
 *
 * $Id$
 *
 * @package    Profiler
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Profiler_Table_Core {

	protected $columns = array();
	protected $rows = array();

	/**
	 * Get styles for table.
	 *
	 * @return  string
	 */
	public function styles()
	{
		static $styles_output;

		if ( ! $styles_output)
		{
			$styles_output = TRUE;
			return file_get_contents(Kohana::find_file('views', 'kohana_profiler_table', FALSE, 'css'));
		}

		return '';
	}

	/**
	 * Add column to table.
	 *
	 * @param  string  CSS class
	 * @param  string  CSS style
	 */
	public function add_column($class = '', $style = '')
	{
		$this->columns[] = array('class' => $class, 'style' => $style);
	}

	/**
	 * Add row to table.
	 *
	 * @param  array   data to go in table cells
	 * @param  string  CSS class
	 * @param  string  CSS style
	 */
	public function add_row($data, $class = '', $style = '')
	{
		$this->rows[] = array('data' => $data, 'class' => $class, 'style' => $style);
	}

	/**
	 * Render table.
	 *
	 * @return  string
	 */
	public function render()
	{
		$data['rows'] = $this->rows;
		$data['columns'] = $this->columns;
		return View::factory('kohana_profiler_table', $data)->render();
	}
}