<?php

class Form_Field_Core {

	//Field name
	protected $name;
	
	//Friendly name for in error messages
	protected $screen_name;

	//Field value
	protected $value;	
	
	//The first error for a field ends up here
	protected $errors;
	//The arguments for the rule that triggered the error 
	protected $args;
		
	// Filters
	protected $pre_filters 	= array();
	protected $post_filters = array();

	// Rules and callbacks
	protected $rules 		= array();
	protected $callbacks 	= array();
	
	// Message output format
	protected $message_format = '<p class="error">{message}</p>';
	
	//bool whether field is validated
	protected $is_valid;

	//Unfiltered value, also no rules or callbacks are applied to this
	protected $unfiltered_value;

	//Custom error messages, if none defaults to Kohana i18n validation.php
	protected $error_messages=array();
	
	/**
	 * Construct 
	 *
	 * @param unknown_type $name
	 * @param unknown_type $value
	 * @param Validate $validation_object
	 */
	public function __construct($name,$value=null)
	{
		
		$this->name=$name;
		$this->value=$value;
		$this->unfiltered_value=$value;

	}

	/**
	 * Convert object to string when echoed
	 *
	 * @return value
	 */
	public function __toString()
	{
		return (string) $this->value;
	}
	/**
	 * Magically gets vars
	 *
	 * @param string $key
	 * @return string|bool
	 */
	public function __get($key)
	{
		if(isset($this->$key))
		{
			return $this->$key;
		}
		return false;
	}
	/**
	 * Magically sets vars
	 *
	 * @param string  $key
	 * @param string $value
	 */
	public function __set($key,$value)
	{
		//Cannot change name
		if($key=='name')
			return false;
			
		if($this->$key!=$value)
		{	
			$this->$key=$value;
		}
		
	}
	/**
	 * Validate the object
	 *
	 * @return boolean
	 */
	public function validate()
	{
		if(is_bool($this->is_valid))
			return $this->is_valid;
			
		//Iterate over filters, rules and callbacks
		foreach ($this->pre_filters as $filter)
		{
			//Filter value, or every value of an array
			$this->value =  call_user_func($filter, $this->value);
		}	
		foreach($this->rules as $name=>$rule)
		{
			if ( ! empty($this->errors)) break;

			$empty_rules=array('Rule_Required','Rule_Upload_Required','Rule_Upload_Size','Rule_Upload_Allowed');
			
			if(empty($this->value)&&!in_array($name,$empty_rules))
				continue;
			
			$field_name=($this->screen_name==null) ? $this->name : $this->screen_name;

			if($rule instanceof Rule_Upload_Required_Core)
			{
				
				if(!$rule->is_valid($this->upload))
				{
					$rule->name=$field_name;
					$this->add_error($name,$rule->get_message());
				}						
				
			}
			else
			{
				if(!$rule->is_valid($this->value))
				{
					$rule->name=$field_name;
					$this->add_error($name,$rule->get_message());
				}	

			}

		}	
		foreach ($this->callbacks as $callback)
		{
			// Execute the callback, $this is passed so you can access entire validation procedure
			// Mind that when the validation is done some rules might be carried out, some not 
			call_user_func($callback, $this);

			// Stop after an error is found
			if ( ! empty($this->errors)) break;
		}		
		foreach ($this->post_filters as $filter)
		{
			//Filter value, or every value of an array
			$this->value = call_user_func($filter, $this->value);
		}	
		
		$this->is_valid=(count($this->errors) === 0);

		// Return TRUE if there are no errors
		return $this->is_valid;
	}
	/**
	 * Get unfiltered value
	 * return mixed
	 */
	public function get_unfiltered_value()
	{
		return $this->unfiltered_value;
	}
	/**
	 * Get value of field
	 *
	 * @return unknown
	 */
	public function get_value()
	{
		return $this->value;
	}
	/**
	 * Set value of field
	 *
	 * @param unknown_type $value
	 * @return unknown
	 */
	public function set_value($value)
	{
		$this->value=$value;
		return $this;
	}	
	/**
	 * Get name of field
	 *
	 * @return unknown
	 */
	public function get_name()
	{
		return $this->name;
	}	
	/**
	 * Set screen name
	 */
	public function set_screen_name($name)
	{
		$this->screen_name=$name;
		return $this;
	}
	/**
	 * Retrieve screen name
	 *
	 * @return string
	 */
	public function get_screen_name()
	{
		return $this->screen_name;
	}
	/**
	 * Return pre filters
	 *
	 * @return array
	 */
	public function get_pre_filters()
	{
		return $this->pre_filters;
	}	
	/**
	 * Add a pre-filter to the object
	 *
	 * @chainable
	 * @param   callback  filter
	 * @return  object
	 */
	public function add_pre_filter($filter)
	{
		if ( ! is_callable($filter))
			throw new Kohana_Exception('validation.filter_not_callable');

		$filter = (is_string($filter) AND strpos($filter, '::') !== FALSE) ? explode('::', $filter) : $filter;			

		// Add the filter to specified field
		$this->pre_filters[] = $filter;

		return $this;
	}
	/**
	 * Add a pre-filter to the object
	 *
	 * @param array callback filters
	 * @return object
	 */
	public function add_pre_filters($filters)
	{
		foreach($filters as $filter)
		{
			$this->add_pre_filter($filter);
		}
		return $this;
	}	
	/**
	 * Remove pre filter
	 *
	 * @param callback $filter
	 * @return object
	 */
	public function remove_pre_filter($filter)
	{
		$filter=array_search($filter,$this->pre_filters);
		if($filter !== FALSE)
		{
			unset($this->pre_filters[$filter]);
			return $this;
		}
		
		return false;
	}
	/**
	 * Clear all pre filters
	 *
	 * @return object
	 */
	public function clear_pre_filters()
	{
		$this->pre_filters=array();
		return $this;
	}

	/**
	 * Return post filters
	 *
	 * @return array
	 */
	public function get_rules()
	{
		return $this->rules;
	}
	/**
	 * Add rule to the object
	 *
	 * @chainable
	 * @param   object  rule
	 * @return  object
	 */
	public function add_rule($rule,$arguments=null,$name=null)
	{
		if(!($rule instanceof Rule))
		{
			if(substr($rule,0,5)!='Rule_')
			{
				$rule='Rule_'.ucfirst($rule);
			}
			$rule=new $rule($arguments);
		}

		//Custom names for fields
		$name=($name==null)? get_class($rule) : $name;
		
		$this->rules[$name]=$rule;
		return $this;
	}	
	/**
	 * Add array of rules
	 *
	 * @param array $rules
	 * @return object
	 */
	public function add_rules(array $rules)
	{
		foreach($rules as $rule)
		{
			$this->add_rule($rule);
		}
		return $this;
	}
	/**
	 * Remove rule
	 * Problem when removing callbacks with $this
	 *
	 * @param unknown_type $rule_name
	 * @return unknown
	 */
	public function remove_rule($rule)
	{
		if(isset($this->rules[$rule]))
		{
			unset($this->rules[$rule]);
			return $this;
		}
		return false;
	}
	/**
	 * Clear all rules
	 *
	 * @return unknown
	 */
	public function clear_rules()
	{
		$this->rules=array();
		return $this;
	}
	/**
	 * Return post filters
	 *
	 * @return array
	 */
	public function get_post_filters()
	{
		return $this->post_filters;
	}	
	/**
	 * Add a pre-filter to the object
	 *
	 * @chainable
	 * @param   callback  filter
	 * @return  object
	 */
	public function add_post_filter($filter)
	{
		if ( ! is_callable($filter))
			throw new Kohana_Exception('validation.filter_not_callable');

		$filter = (is_string($filter) AND strpos($filter, '::') !== FALSE) ? explode('::', $filter) : $filter;			

		// Add the filter to specified field
		$this->post_filters[] = $filter;

		return $this;
	}	
	/**
	 * Add a post-filter to the object
	 *
	 * @param array callback filters
	 * @return object
	 */
	public function add_post_filters($filters)
	{
		foreach($filters as $filter)
		{
			$this->add_post_filter($filter);
		}
		return $this;
	}
	/**
	 * Remove post filter
	 *
	 * @param callback $filter
	 * @return object
	 */
	public function remove_post_filter($filter)
	{
		$filter=array_search($filter,$this->post_filters);
		if($filter !== FALSE)
		{
			unset($this->post_filters[$filter]);
			return $this;
		}
		
		return false;
	}
	/**
	 * Clear all post filters
	 *
	 * @return object
	 */
	public function clear_post_filters()
	{
		$this->post_filters=array();
		return $this;
	}	
	/**
	 * Return callbacks
	 *
	 * @return array
	 */
	public function get_callbacks()
	{
		return $this->callbacks;
	}
	/**
	 * Add a callback to the object
	 *
	 * @param callback callback
	 * @return object
	 */
	public function add_callback($callback)
	{

		if ( ! is_callable($callback, TRUE))
			throw new Kohana_Exception('validation.callback_not_callable');

		$callback = (is_string($callback) AND strpos($callback, '::') !== FALSE) ? explode('::', $callback) : $callback;							

		// Add the filter to specified field
		$this->callbacks[] = $callback;

		return $this;
	}	
	/**
	 * Add array of callbacks to the object
	 *
	 * @param array callbacks
	 * @return object
	 */
	public function add_callbacks(array $callbacks)
	{
		foreach($callbacks as $callback)
		{
			$this->add_callback($callback);	
		}
		return $this;
	}	
	/**
	 * Remove post filter
	 *
	 * @param callback $filter
	 * @return object
	 */
	public function remove_callback($callback)
	{
		$callback=array_search($callback,$this->callbacks);
		if($callback !== FALSE)
		{
			unset($this->callbacks[$callback]);
			return $this;
		}
		
		return false;
	}
	/**
	 * Clear all post filters
	 *
	 * @return object
	 */
	public function clear_callbacks()
	{
		$this->callbacks=array();
		return $this;
	}		

	/**
	 * Return the errors array.
	 *
	 * @return array
	 */
	public function errors()
	{
		return $this->errors;
	}	
	/**
	 * Add an error to an input.
	 *
	 * @chainable
	 * @param   string  error
	 * @param   array  error arguments
	 * @return  object
	 */
	public function add_error($error,$message=null)
	{
		$this->errors[$error] = $message;
		return $this;
	}	
	/**
	 * Remove error from current field
	 * Might be used by callbacks 
	 * @return object
	 */
	public function remove_error()
	{
		$this->errors = null;
	
		return $this;
	}
	/**
	 * Set the format of message strings for his field
	 *
	 * @chainable
	 * @param   string   new message format
	 * @return  object
	 */
	public function error_format($str)
	{
		if (strpos($str, '{message}') === FALSE)
			throw new Kohana_Exception('validation.error_format');

		// Set the new message format
		$this->message_format = $str;

		return $this;
	}	
	/**
	 * Returns error	 * format
	 *
	 * @return string
	 */
	public function get_error_format()
	{
		return $this->message_format;
	}
	/**
	 * Returns the message for an input. 
	 *
	 * @return  string
	 */
	public function error_message()
	{
		//No errors, no messages
		if (empty($this->errors))
			return false;
		
		return str_replace('{message}', current($this->errors), $this->message_format);
	}	
	public function set_language_file($file)
	{
		foreach($this->rules as $rule)
		{
			$rule->set_language_file($file);
		}
		return $this;
	}

}
?>