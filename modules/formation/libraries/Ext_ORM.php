<?php
 
class Ext_ORM extends ORM {

	// Database field rules
	protected $validate=array();

	//Acts as behaviour
	protected $acts_as;
	
	public function __construct($id=false)
	{
		parent::__construct($id);
		
		//Handles the acts as behaviour so you can use it
		if(!empty($this->acts_as))
		{
			$class='ORM_'.ucfirst($this->acts_as);
			
			$this->acts_as=new $class($this);

		}
	}
	/**
	 * Magic method for calling ORM methods. This handles:
	 *  - as_array
	 *  - find_by_*
	 *  - find_all_by_*
	 *  - find_related_*
	 *  - has_*
	 *  - add_*
	 *  - remove_*
	 *
	 * @throws  Kohana_Exception
	 * @param   string  method name
	 * @param   array   method arguments
	 * @return  mixed
	 */
	public function __call($method, $args)
	{
		if($this->acts_as!=null AND method_exists($method,$this->acts_as))
		{
			return $this->acts_as->$method(explode($args));
		}
		return parent::__call($method,$args);
	}	

	/**
	 * Saves the current object. If the object is new, it will be reloaded
	 * after being saved.
	 *
	 * @chainable
	 * @return  ORM
	 */
	public function save()
	{
	    if($this->before_save()==false)
        {
        	return false;
        }
        return parent::save();

	}

    protected function before_save()
    {
    	
    	foreach ($this->table_columns as $field => $data)
    	{
    		
    	
    		if($field=='modified' && $data['format']=='0000-00-00 00:00:00')
    		{
    			$this->modified=gmdate("Y-m-d H:i:s", time());
    		}
    		
			if($field=='created' && $data['format']=='0000-00-00 00:00:00' && $this->object['id'] == '')
    		{
    			$this->created=gmdate("Y-m-d H:i:s", time());
    		}

    	}
    	
    	return true;
    }

	public function with(array $table)
	{
	
	
	}
	/**
	 * Magic method for getting object and model keys.
	 *
	 * @param   string  key name
	 * @return  mixed
	 */
	public function __get($key)
	{
		if($key=='modified' || $key=='created')
		{
			if (isset($this->object[$key]))
			{
				return strtotime($this->object[$key]);
			}
		}
		return parent::__get($key);
	}
	public function as_array(){
		return $this->object;
	}
	/**
	 * Load array of values into ORM object
	 *
	 * @param array $data
	 */
	public function populate(array $data)
	{
		foreach ($data as $field=>$value)
		{
			if(array_key_exists($field,$this->table_columns))
			{
				$this->$field=$value;
			}
		}		

	}	

	/**
	 * Get validation rules from ORM
	 */
	public function get_validate()
	{
		return $this->validate;
	}
	/**
	 * Simple exists method to see if model exists
	 *
	 * @return boolean
	 */
	public function exists()
	{
		return $this->object['id'] > 0;
	}
	public function set_datetime()
	{
		$time=time();
	   if(!$this->exists())
	   {
			//if record doesn't exist it must be created with a time of creation
	       $this->created=gmdate("Y-m-d H:i:s", $time);
	   }
		//Always set a new modifed time
	   $this->modified=gmdate("Y-m-d H:i:s", $time);
	}
	public function get_relationships()
	{
		return array(
			'has_one'=>$this->has_one,
			'has_many'=>$this->has_many,
			'belongs_to'=>$this->belongs_to,
			'has_and_belongs_to_many'=>$this->has_and_belongs_to_many,		
		);
	}
}