<?php defined('SYSPATH') or die('No direct script access.');
class Validate_Core extends ArrayObject{

	// Errors
	protected $errors = array();

	protected $js_validate;
	
	protected $js_validator;
	/**
	 * Pass the array you need to validate, e.g. $_POST
	 * 
	 *
	 * @param   array   array to validate
	 * @return  void
	 */
	public function __construct(array $array)
	{
		//For each field make an object
		$array_object=array();
		
		$this->setFlags(ArrayObject::STD_PROP_LIST );
		
		foreach($array as $key=>$value)
		{	
			//Create object for each key in array
			$this->offsetSet($key,$value);
		}
		
		//Register auto load for rules and elements
		spl_autoload_register(array('Validate', 'auto_load'));
		
		//Log::add('debug', 'Validation library initialized');
			
	}
	/**
	 * Autoloader so elements can be stuffed in an subdir
	 *
	 * @param class $class
	 * @return bool
	 */
	public static function auto_load($class)
	{
		if((substr($class, 0, 7))!='Element' && substr($class, 0, 4)!='Rule' )
			return FALSE;
			
		static $prefix;
		
		// Set the extension prefix
		empty($prefix) and $prefix = Kohana::config('core.extension_prefix');

		if (class_exists($class, FALSE))
			return TRUE;
		
		$file=strpos($class,'Core') ? substr($class, 0, -5) : $class;
	
		$type = (substr($class, 0, 7)=='Element') ? 	'libraries/elements' : 'libraries/rules';		
	
		// If the file doesn't exist, just return
		if (($filepath = Kohana::find_file($type, $file)) === FALSE)
			return FALSE;
		
		// Load the requested file
		require_once $filepath;
		
		if ($extension = Kohana::find_file($type, $prefix.$class))
		{
			// Load the class extension
			require_once $extension;
		}
		elseif (substr($class, -5) !== '_Core' AND class_exists($class.'_Core', FALSE))
		{
			// Transparent class extensions are handled using eval. This is
			// a disgusting hack, but it works very well.
			eval('class '.$class.' extends '.$class.'_Core { }');
		}
		

		return class_exists($class, FALSE);			
	}
	/**
	 * offsetGet
	 *
	 * @param unknown_type $key
	 * @return unknown
	 */ 		
	function offsetGet($key)
	{
		//So you can do this $_POST['user']->add_rule() 
		//and the user key will be an object of type Field 
		//without ever declaring it so
		if(!isset($this[$key]))
		{
			$this->offsetSet($key,null);
		}
		return parent::offsetGet($key);
	}
	/**
	 * offsetset
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function offsetSet($key,$value)
	{
		if(!($value instanceof Element_Input) && !($value instanceof Element_Group))
		{
			$value=new Form_Field($key,$value);
		}
		parent::offsetSet($key,$value);
		
	}
	/**
	 * Returns the ArrayObject array values.
	 *
	 * @return  array
	 */
	public function as_array()
	{
		$array=array();
		//Only name/value pairs
		foreach($this as $field)
		{
			$array[$field->get_name()]=$field->get_value();
		}		
		return $array;
	}
	/**
	 * Get all rules
	 *
	 * @return object
	 */
	public function get_rules()
	{
		$rules=array();
		foreach($this as $field)
		{
			$rules[$field->name]=$field->get_rules();
		}
		return $rules;
	}	
	/**
	 * Add rule to all fields
	 * 
	 * @chainable
	 * @param callback rule
	 * @return object
	 */
	public function add_rule($rule)
	{
		//Add rule to every field
		foreach($this as $field)
		{
			$field->add_rule($rule);
		}
		return $this;
	}
	/**
	 * Add rules to all fields
	 *
	 * @param array $rules
	 * @return object
	 */
	public function add_rules(array $rules)
	{
		foreach($this as $field)
		{
			$field->add_rules($rules);
		}
		return $this;
	}
	/**
	 * Clear all rules
	 *
	 * @return object
	 */
	public function clear_rules()
	{
		foreach($this as $field)
		{
			$field->clear_rules();
		}
		return $this;
	}	
	/**
	 * Get all pre filters
	 *
	 * @return object
	 */
	public function get_pre_filters()
	{
		$pre_filters=array();
		foreach($this as $field)
		{
			$pre_filters[$field->name]=$field->get_pre_filters();
		}
		return $pre_filters;
	}
	/**
	 * Add pre filter to all fields
	 * 
	 * @chainable	 
	 * @param  callback filter
	 * @return object
	 */
	public function add_pre_filter($filter)
	{
		//Add pre filter to every field
		foreach($this as $field)
		{
			$field->add_pre_filter($filter);
		}
		return $this;
	}
	/**
	 * Add pre filters to all fields
	 *
	 * @param array $filters
	 * @return object
	 */
	public function add_pre_filters(array $filters)
	{
		foreach($this as $field)
		{
			$field->add_pre_filters($filters);
		}
		return $this;
	}	
	/**
	 * Clear all pre filters
	 *
	 * @return object
	 */
	public function clear_pre_filters()
	{
		foreach($this as $field)
		{
			$field->clear_pre_filters();
		}
		return $this;
	}	
	/**
	 * Get all post filters
	 *
	 * @return object
	 */
	public function get_post_filters()
	{
		$post_filters=array();
		foreach($this as $field)
		{
			$post_filters[$field->name]=$field->get_post_filters();
		}
		return $post_filters;
	}	
	/**
	 * Add post filter to all fields
	 * 
	 * @chainable	 
	 * @param  callback filter
	 * @return object
	 */
	public function add_post_filter($filter)
	{
		//Add post filter to every field
		foreach($this as $field)
		{
			$field->add_post_filter($filter);
		}
		return $this;
	}
	/**
	 * Add post filters to all fields
	 *
	 * @param array $filters
	 * @return object
	 */
	public function add_post_filters(array $filters)
	{
		foreach($this as $field)
		{
			$field->add_post_filters($filters);
		}
		return $this;
	}	
	/**
	 * Clear all post filters
	 *
	 * @return object
	 */
	public function clear_post_filters()
	{
		foreach($this as $field)
		{
			$field->clear_post_filters();
		}
		return $this;
	}
	/**
	 * Get all callbacks
	 *
	 * @return object
	 */
	public function get_callbacks()
	{
		$callbacks=array();
		foreach($this as $field)
		{
			$callbacks[$field->name]=$field->get_callbacks();
		}
		return $callbacks;
	}
	/**
	 * Add callback to all fields
	 * 
	 * @chainable	 
	 * @param  callback callback
	 * @return object
	 */	
	public function add_callback($callback)
	{
		//Add callback to every field
		foreach($this as $field)
		{
			$field->add_callback($callback);
		}
		return $this;
	}
	/**
	 * Add callbacks to all fields
	 * 
	 * @chainable	 
	 * @param  callback callback
	 * @return object
	 */	
	public function add_callbacks(array $callbacks)
	{
		//Add callback to every field
		foreach($this as $field)
		{
			$field->add_callbacks($callbacks);
		}
		return $this;
	}	
	/**
	 * Clear all callbacks
	 *
	 * @return object
	 */
	public function clear_callbacks()
	{
		foreach($this as $field)
		{
			$field->clear_callbacks();
		}
		return $this;
	}
	/**
	 * Set the format of message strings.
	 *
	 * @chainable
	 * @param   string   new message format
	 * @return  object
	 */
	public function error_format($str)
	{
		if (strpos($str, '{message}') === FALSE)
			throw new Kohana_Exception('validation.error_format');

		foreach($this as $field)
		{
			$field->error_format($str);
		}
		return $this;
	}
	/**
	 * Validate by processing pre-filters, rules, callbacks, and post-filters.
	 * All fields that have filters, rules, or callbacks will be initialized if
	 * they are undefined. Validation will only be run if there is data already
	 * in the array.
	 *
	 * @return bool
	 */
	public function validate()
	{	
		//Iterate over all fields and collect errors and error messages
		foreach($this as $key=>$field)
		{
			//Will validate fields and the Element_Group and load its values as well
			$is_valid=$field->validate();
			
			if($field instanceof Element_Group)
			{
				$this->errors=array_merge($field->errors(),$this->errors);
			}
			else
			{
				if($is_valid==false)
				{
					$this->errors[$key]=$field->errors();
				}
			}
		}		
		
		// Return TRUE if there are no errors
		return (count($this->errors) === 0);
	}
	public function validate_partial($partial)
	{
		foreach((array) $partial as $key=>$field)
		{
	
			if(isset($this[$key]))
			{	
				//Will validate the Element_Group and load its values as well
				$this[$key]->validate();
				
				if($this[$key] instanceof Element_Group)
				{
					$this->errors=array_merge($this[$key]->errors(),$this->errors);
				}
				else
				{	
					if($this[$key]->errors()!= null)
					{
						$this->errors[$this[$key]->name]=$this[$key]->errors();
					}
				}	
			}
		}
		
		return (count($this->errors) === 0);
	}
	/**
	 * Validate partial json
	 *
	 * @param array fields you want to validate
	 * @return json boolean
	 */
	public function validate_partial_json($array)
	{
		return jsonencode($this->validate_partial($array));
	}
	/**
	 * Return the errors and messages array.
	 *
	 * @return array
	 */
	public function errors()
	{
		return $this->errors;
	}
	public function set_language_file($file)
	{
		foreach($this as $field)
		{
			$field->set_language_file($file);
		}
	}
	public function register_js_validator($validator)
	{
		if(is_object($validator))
		{
			$this->js_validate=$validator;
		}		
		else
		{
			$this->js_validate=new $validator($this);
		}
		
	}
	public function js_validate()
	{
		return $this->js_validate;
	}
} // End Validation

