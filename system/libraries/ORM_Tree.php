<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Object Relational Mapping (ORM) "tree" extension. Allows ORM objects to act
 * as trees, with parents and children.
 *
 * $Id: ORM_Tree.php 3122 2008-07-16 14:19:34Z Shadowhand $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class ORM_Tree_Core extends ORM {

	// Name of the child
	protected $children;

	// Parent keyword name
	protected $parent_key = 'parent_id';

	/**
	 * Overload ORM::__get to support "parent" and "children" properties.
	 *
	 * @param   string  column name
	 * @return  mixed
	 */
	public function __get($column)
	{
		if ($column === 'parent')
		{
			if (empty($this->related[$column]))
			{
				// Load child model
				$model = ORM::factory(inflector::singular($this->children));

				if (isset($this->object[$this->parent_key]))
				{
					// Find children of this parent
					$model->where($this->parent_key, $this->object[$this->parent_key])->find();
				}

				$this->related[$column] = $model;
			}

			return $this->related[$column];
		}
		elseif ($column === 'children')
		{
			if (empty($this->related[$column]))
			{
				$model = ORM::factory(inflector::singular($this->children));

				if ($this->children === $this->table_name)
				{
					// Load children within this table
					$this->related[$column] = $model
						->where($this->parent_key, $this->object[$this->primary_key])
						->find_all();
				}
				else
				{
					// Find first selection of children
					$this->related[$column] = $model
						->where($this->foreign_key(), $this->object[$this->primary_key])
						->where($this->parent_key, NULL)
						->find_all();
				}
			}

			return $this->related[$column];
		}

		return parent::__get($column);
	}

} // End ORM Tree