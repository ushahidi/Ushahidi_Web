<?php
/**
 * Object Relational Mapping (ORM) "versioned" extension. Allows ORM objects to
 * be revisioned instead of updated.
 *
 * $Id$
 *
 * @package    ORM
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class ORM_Versioned_Core extends ORM {

	protected $last_version = NULL;

	/**
	 * Overload ORM::save() to support versioned data
	 *
	 * @chainable
	 * @return  ORM
	 */
	public function save()
	{
		$this->last_version = 1 + ($this->last_version === NULL ? $this->object['version'] : $this->last_version);
		$this->__set('version', $this->last_version);

		parent::save();

		if ($this->saved)
		{
			$data = array();
			foreach ($this->object as $key => $value)
			{
				if ($key === 'id')
					continue;

				$data[$key] = $value;
			}
			$data[$this->foreign_key()] = $this->id;

			$this->db->insert($this->table_name.'_versions', $data);
		}

		return $this;
	}

	/**
	 * Loads previous version from current object
	 *
	 * @chainable
	 * @return  ORM
	 */
	public function previous()
	{
		if ( ! $this->loaded)
			return $this;

		$this->last_version = ($this->last_version === NULL) ? $this->object['version'] : $this->last_version;
		$version = $this->last_version - 1;

		$query = $this->db
			->where($this->foreign_key(), $this->object[$this->primary_key])
			->where('version', $version)
			->limit(1)
			->get($this->table_name.'_versions');

		if ($query->count())
		{
			$this->load_values($query->result(FALSE)->current());
		}

		return $this;
	}

	/**
	 * Restores the object with data from stored version
	 *
	 * @param   integer  version number you want to restore
	 * @return  ORM
	 */
	public function restore($version)
	{
		if ( ! $this->loaded)
			return $this;

		$query = $this->db
			->where($this->foreign_key(), $this->object[$this->primary_key])
			->where('version', $version)
			->limit(1)
			->get($this->table_name.'_versions');

		if ($query->count())
		{
			$row = $query->result(FALSE)->current();

			foreach ($row as $key => $value)
			{
				if ($key === $this->primary_key OR $key === $this->foreign_key())
				{
					// Do not overwrite the primary key
					continue;
				}

				if ($key === 'version')
				{
					// Always use the current version
					$value = $this->version;
				}

				$this->__set($key, $value);
			}

			$this->save();
		}

		return $this;
	}

	/**
	 * Overloads ORM::delete() to delete all versioned entries of current object
	 * and the object itself
	 *
	 * @param   integer  id of the object you want to delete
	 * @return  ORM
	 */
	public function delete($id = NULL)
	{
		if ($id === NULL)
		{
			// Use the current object id
			$id = $this->object[$this->primary_key];
		}

		if ($status = parent::delete($id))
		{
			$this->db->where($this->foreign_key(), $id)->delete($this->table_name.'_versions');
		}

		return $status;
	}

}