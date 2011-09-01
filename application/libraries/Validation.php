<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Validation library.
 *
 * $Id: Validation.php 3127 2008-07-16 14:43:52Z Shadowhand $
 *
 * @package    Validation
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Validation_Core extends ArrayObject {

	// Unique "any field" key
	protected $any_field;

	// Array fields
	protected $array_fields = array();

	// Filters
	protected $pre_filters = array();
	protected $post_filters = array();

	// Rules and callbacks
	protected $rules = array();
	protected $callbacks = array();

	// Rules that are allowed to run on empty fields
	protected $empty_rules = array('required', 'matches');

	// Errors
	protected $errors = array();
	protected $messages = array();

	// Checks if there is data to validate.
	protected $submitted;

	/**
	 * Creates a new Validation instance.
	 *
	 * @param   array   array to use for validation
	 * @return  object
	 */
	public static function factory($array = NULL)
	{
		return new Validation( ! is_array($array) ? $_POST : $array);
	}

	/**
	 * Sets the unique "any field" key and creates an ArrayObject from the
	 * passed array.
	 *
	 * @param   array   array to validate
	 * @return  void
	 */
	public function __construct(array $array)
	{
		// Set a dynamic, unique "any field" key
		$this->any_field = uniqid(NULL, TRUE);

		// Test if there is any actual data
		$this->submitted = (count($array) > 0);

		parent::__construct($array, ArrayObject::ARRAY_AS_PROPS | ArrayObject::STD_PROP_LIST);
	}

	/**
	 * Test if the data has been submitted.
	 *
	 * @return  boolean
	 */
	public function submitted($value = NULL)
	{
		if (is_bool($value))
		{
			$this->submitted = $value;
		}

		return $this->submitted;
	}

	/**
	 * Returns the ArrayObject values.
	 *
	 * @return  array
	 */
	public function as_array()
	{
		return $this->getArrayCopy();
	}

	/**
	 * Returns the ArrayObject values, removing all inputs without rules.
	 * To choose specific inputs, list the field name as arguments.
	 *
	 * @return  array
	 */
	public function safe_array()
	{
		// All the fields that are being validated
		$all_fields = array_unique(array_merge
		(
			array_keys($this->pre_filters),
			array_keys($this->rules),
			array_keys($this->callbacks),
			array_keys($this->post_filters)
		));

		// Load choices
		$choices = func_get_args();
		$choices = empty($choices) ? NULL : array_combine($choices, $choices);
		
		$safe = array();
		foreach ($all_fields as $i => $field)
		{
			// Ignore "any field" key
			if ($field === $this->any_field) continue;

			if (isset($this->array_fields[$field]))
			{
				// Use the key field
				$field = $this->array_fields[$field];
			}

			if ($choices === NULL OR isset($choices[$field]))
			{
				// Make sure all fields are defined
				$safe[$field] = isset($this[$field]) ? $this[$field] : NULL;
			}
		}

		return $safe;
	}

	/**
	 * Add additional rules that will forced, even for empty fields. All arguments
	 * passed will be appended to the list.
	 *
	 * @chainable
	 * @param   string   rule name
	 * @return  object
	 */
	public function allow_empty_rules($rules)
	{
		// Any number of args are supported
		$rules = func_get_args();

		// Merge the allowed rules
		$this->empty_rules = array_merge($this->empty_rules, $rules);

		return $this;
	}

	/**
	 * Add a pre-filter to one or more inputs.
	 *
	 * @chainable
	 * @param   callback  filter
	 * @param   string    fields to apply filter to, use TRUE for all fields
	 * @return  object
	 */
	public function pre_filter($filter, $field = TRUE)
	{
		if ( ! is_callable($filter))
			throw new Kohana_Exception('validation.filter_not_callable');

		$filter = (is_string($filter) AND strpos($filter, '::') !== FALSE) ? explode('::', $filter) : $filter;

		if ($field === TRUE)
		{
			// Handle "any field" filters
			$fields = array($this->any_field);
		}
		else
		{
			// Add the filter to specific inputs
			$fields = func_get_args();
			$fields = array_slice($fields, 1);
		}

		foreach ($fields as $field)
		{
			if (strpos($field, '.') > 0)
			{
				// Field keys
				$keys = explode('.', $field);

				// Add to array fields
				$this->array_fields[$field] = $keys[0];
			}

			// Add the filter to specified field
			$this->pre_filters[$field][] = $filter;
		}

		return $this;
	}

	/**
	 * Add a post-filter to one or more inputs.
	 *
	 * @chainable
	 * @param   callback  filter
	 * @param   string    fields to apply filter to, use TRUE for all fields
	 * @return  object
	 */
	public function post_filter($filter, $field = TRUE)
	{
		if ( ! is_callable($filter, TRUE))
			throw new Kohana_Exception('validation.filter_not_callable');

		$filter = (is_string($filter) AND strpos($filter, '::') !== FALSE) ? explode('::', $filter) : $filter;

		if ($field === TRUE)
		{
			// Handle "any field" filters
			$fields = array($this->any_field);
		}
		else
		{
			// Add the filter to specific inputs
			$fields = func_get_args();
			$fields = array_slice($fields, 1);
		}

		foreach ($fields as $field)
		{
			if (strpos($field, '.') > 0)
			{
				// Field keys
				$keys = explode('.', $field);

				// Add to array fields
				$this->array_fields[$field] = $keys[0];
			}

			// Add the filter to specified field
			$this->post_filters[$field][] = $filter;
		}

		return $this;
	}

	/**
	 * Add rules to a field. Rules are callbacks or validation methods. Rules can
	 * only return TRUE or FALSE.
	 *
	 * @chainable
	 * @param   string    field name
	 * @param   callback  rules (unlimited number)
	 * @return  object
	 */
	public function add_rules($field, $rules)
	{
		// Handle "any field" filters
		($field === TRUE) and $field = $this->any_field;

		// Get the rules
		$rules = func_get_args();
		$rules = array_slice($rules, 1);

		foreach ($rules as $rule)
		{
			// Rule arguments
			$args = NULL;

			if (is_string($rule))
			{
				if (preg_match('/^([^\[]++)\[(.+)\]$/', $rule, $matches))
				{
					// Split the rule into the function and args
					$rule = $matches[1];
					$args = preg_split('/(?<!\\\\),\s*/', $matches[2]);

					// Replace escaped comma with comma
					$args = str_replace('\,', ',', $args);
				}

				if (method_exists($this, $rule))
				{
					// Make the rule a valid callback
					$rule = array($this, $rule);
				}
				elseif (method_exists('valid', $rule))
				{
					// Make the rule a callback for the valid:: helper
					$rule = array('valid', $rule);
				}
			}

			if ( ! is_callable($rule, TRUE))
				throw new Kohana_Exception('validation.rule_not_callable');

			$rule = (is_string($rule) AND strpos($rule, '::') !== FALSE) ? explode('::', $rule) : $rule;

			if (strpos($field, '.') > 0)
			{
				// Field keys
				$keys = explode('.', $field);

				// Add to array fields
				$this->array_fields[$field] = $keys[0];
			}

			// Add the rule to specified field
			$this->rules[$field][] = array($rule, $args);
		}

		return $this;
	}

	/**
	 * Add callbacks to a field. Callbacks must accept the Validation object
	 * and the input name. Callback returns are not processed.
	 *
	 * @chainable
	 * @param   string     field name
	 * @param   callbacks  callbacks (unlimited number)
	 * @return  object
	 */
	public function add_callbacks($field, $callbacks)
	{
		// Handle "any field" filters
		($field === TRUE) and $field = $this->any_field;

		if (func_get_args() > 2)
		{
			// Multiple callback
			$callbacks = array_slice(func_get_args(), 1);
		}
		else
		{
			// Only one callback
			$callbacks = array($callbacks);
		}

		foreach ($callbacks as $callback)
		{
			if ( ! is_callable($callback, TRUE))
				throw new Kohana_Exception('validation.callback_not_callable');

			$callback = (is_string($callback) AND strpos($callback, '::') !== FALSE) ? explode('::', $callback) : $callback;

			if (strpos($field, '.') > 0)
			{
				// Field keys
				$keys = explode('.', $field);

				// Add to array fields
				$this->array_fields[$field] = $keys[0];
			}

			// Add the callback to specified field
			$this->callbacks[$field][] = $callback;
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
		// All the fields that are being validated
		$all_fields = array_unique(array_merge
		(
			array_keys($this->pre_filters),
			array_keys($this->rules),
			array_keys($this->callbacks),
			array_keys($this->post_filters)
		));

		// Copy the array from the object, to optimize multiple sets
		$object_array = $this->getArrayCopy();

		foreach ($all_fields as $i => $field)
		{
			if ($field === $this->any_field)
			{
				// Remove "any field" from the list of fields
				unset($all_fields[$i]);
				continue;
			}

			if (substr($field, -2) === '.*')
			{
				// Set the key to be an array
				Kohana::key_string_set($object_array, substr($field, 0, -2), array());
			}
			else
			{
				// Set the key to be NULL
				Kohana::key_string_set($object_array, $field, NULL);
			}
		}

		// Swap the array back into the object
		$this->exchangeArray($object_array);

		// Reset all fields to ALL defined fields
		$all_fields = array_keys($this->getArrayCopy());

		foreach ($this->pre_filters as $field => $calls)
		{
			foreach ($calls as $func)
			{
				if ($field === $this->any_field)
				{
					foreach ($all_fields as $f)
					{
						// Process each filter
						$this[$f] = is_array($this[$f]) ? arr::map_recursive($func, $this[$f]) : call_user_func($func, $this[$f]);
					}
				}
				else
				{
					// Process each filter
					$this[$field] = is_array($this[$field]) ? arr::map_recursive($func, $this[$field]) : call_user_func($func, $this[$field]);
				}
			}
		}

		if ($this->submitted === FALSE)
			return FALSE;

		foreach ($this->rules as $field => $calls)
		{
			foreach ($calls as $call)
			{
				// Split the rule into function and args
				list($func, $args) = $call;

				if ($field === $this->any_field)
				{
					foreach ($all_fields as $f)
					{
						if (isset($this->array_fields[$f]))
						{
							// Use the field key
							$f_key = $this->array_fields[$f];

							// Prevent other rules from running when this field already has errors
							if ( ! empty($this->errors[$f_key])) break;

							// Don't process rules on empty fields
							if ( ! in_array($func[1], $this->empty_rules, TRUE) AND $this[$f_key] == NULL)
								continue;

							foreach ($this[$f_key] as $k => $v)
							{
								if ( ! call_user_func($func, $this[$f_key][$k], $args))
								{
									// Run each rule
									$this->errors[$f_key] = is_array($func) ? $func[1] : $func;
								}
							}
						}
						else
						{
							// Prevent other rules from running when this field already has errors
							if ( ! empty($this->errors[$f])) break;

							// Don't process rules on empty fields
							if ( ! in_array($func[1], $this->empty_rules, TRUE) AND $this[$f] == NULL)
								continue;

							if ( ! call_user_func($func, $this[$f], $args))
							{
								// Run each rule
								$this->errors[$f] = is_array($func) ? $func[1] : $func;
							}
						}
					}
				}
				else
				{
					if (isset($this->array_fields[$field]))
					{
						// Use the field key
						$field_key = $this->array_fields[$field];

						// Prevent other rules from running when this field already has errors
						if ( ! empty($this->errors[$field_key])) break;

						// Don't process rules on empty fields
						if ( ! in_array($func[1], $this->empty_rules, TRUE) AND $this[$field_key] == NULL)
							continue;

						foreach ($this[$field_key] as $k => $val)
						{
							if ( ! call_user_func($func, $this[$field_key][$k], $args))
							{
								// Run each rule
								$this->errors[$field_key] = is_array($func) ? $func[1] : $func;

								// Stop after an error is found
								break 2;
							}
						}
					}
					else
					{
						// Prevent other rules from running when this field already has errors
						if ( ! empty($this->errors[$field])) break;

						// Don't process rules on empty fields
						if ( ! in_array($func[1], $this->empty_rules, TRUE) AND $this[$field] == NULL)
							continue;

						if ( ! call_user_func($func, $this[$field], $args))
						{
							// Run each rule
							$this->errors[$field] = is_array($func) ? $func[1] : $func;

							// Stop after an error is found
							break;
						}
					}
				}
			}
		}

		foreach ($this->callbacks as $field => $calls)
		{
			foreach ($calls as $func)
			{
				if ($field === $this->any_field)
				{
					foreach ($all_fields as $f)
					{
						// Execute the callback
						call_user_func($func, $this, $f);

						// Stop after an error is found
						if ( ! empty($errors[$f])) break 2;
					}
				}
				else
				{
					// Execute the callback
					call_user_func($func, $this, $field);

					// Stop after an error is found
					if ( ! empty($errors[$field])) break;
				}
			}
		}

		foreach ($this->post_filters as $field => $calls)
		{
			foreach ($calls as $func)
			{
				if ($field === $this->any_field)
				{
					foreach ($all_fields as $f)
					{
						if (isset($this->array_fields[$f]))
						{
							// Use the field key
							$f = $this->array_fields[$f];
						}

						// Process each filter
						$this[$f] = is_array($this[$f]) ? array_map($func, $this[$f]) : call_user_func($func, $this[$f]);
					}
				}
				else
				{
					if (isset($this->array_fields[$field]))
					{
						// Use the field key
						$field = $this->array_fields[$field];
					}

					// Process each filter
					$this[$field] = is_array($this[$field]) ? array_map($func, $this[$field]) : call_user_func($func, $this[$field]);
				}
			}
		}

		// Return TRUE if there are no errors
		return (count($this->errors) === 0);
	}

	/**
	 * Add an error to an input.
	 *
	 * @chainable
	 * @param   string  input name
	 * @param   string  unique error name
	 * @return  object
	 */
	public function add_error($field, $name)
	{
		if (isset($this[$field]))
		{
			$this->errors[$field] = $name;
		}

		return $this;
	}

	/**
	 * Sets or returns the message for an input.
	 *
	 * @chainable
	 * @param   string   input key
	 * @param   string   message to set
	 * @return  string|object
	 */
	public function message($input = NULL, $message = NULL)
	{
		if ($message === NULL)
		{
			if ($input === NULL)
			{
				$messages = array();
				$keys     = array_keys($this->messages);

				foreach ($keys as $input)
				{
					$messages[] = $this->message($input);
				}

				return implode("\n", $messages);
			}

			// Return nothing if no message exists
			if (empty($this->messages[$input]))
				return '';

			// Return the HTML message string
			return $this->messages[$input];
		}
		else
		{
			$this->messages[$input] = $message;
		}

		return $this;
	}

	/**
	 * Return the errors array.
	 *
	 * @param   boolean  load errors from a lang file
	 * @return  array
	 */
	public function errors($file = NULL)
	{
		if ($file === NULL)
		{
			return $this->errors;
		}
		else
		{
			$errors = array();
			foreach ($this->errors as $input => $error)
			{
				// Key for this input error
				$key = "$file.$input.$error";

				if (($errors[$input] = Kohana::lang($key)) === $key)
				{
					// Get the default error message
					$errors[$input] = Kohana::lang("$file.$input.default");
				}
			}
			
			return $errors;
		}
	}

	/**
	 * Rule: required. Generates an error if the field has an empty value.
	 *
	 * @param   mixed   input value
	 * @return  bool
	 */
	public function required($str)
	{
		return ! ($str === '' OR $str === NULL OR $str === FALSE OR (is_array($str) AND empty($str)));
	}

	/**
	 * Rule: matches. Generates an error if the field does not match one or more
	 * other fields.
	 *
	 * @param   mixed   input value
	 * @param   array   input names to match against
	 * @return  bool
	 */
	public function matches($str, array $inputs)
	{
		foreach ($inputs as $key)
		{
			if ($str !== (isset($this[$key]) ? $this[$key] : NULL))
				return FALSE;
		}

		return TRUE;
	}

	/**
	 * Rule: length. Generates an error if the field is too long or too short.
	 *
	 * @param   mixed   input value
	 * @param   array   minimum, maximum, or exact length to match
	 * @return  bool
	 */
	public function length($str, array $length)
	{
		if ( ! is_string($str))
			return FALSE;

		$size = utf8::strlen($str);
		$status = FALSE;

		if (count($length) > 1)
		{
			list ($min, $max) = $length;

			if ($size >= $min AND $size <= $max)
			{
				$status = TRUE;
			}
		}
		else
		{
			$status = ($size === (int) $length[0]);
		}

		return $status;
	}

	/**
	 * Rule: depends_on. Generates an error if the field does not depend on one
	 * or more other fields.
	 *
	 * @param   mixed   field name
	 * @param   array   field names to check dependency
	 * @return  bool
	 */
	public function depends_on($field, array $fields)
	{
		foreach ($fields as $depends_on)
		{
			if ( ! isset($this[$depends_on]) OR $this[$depends_on] == NULL)
				return FALSE;
		}

		return TRUE;
	}

	/**
	 * Rule: chars. Generates an error if the field contains characters outside of the list.
	 *
	 * @param   string  field value
	 * @param   array   allowed characters
	 * @return  bool
	 */
	public function chars($value, array $chars)
	{
		return ! preg_match('![^'.preg_quote(implode(',', $chars)).']!', $value);
	}

} // End Validation
