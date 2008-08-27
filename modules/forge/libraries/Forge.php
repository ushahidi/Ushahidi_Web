<?php defined('SYSPATH') or die('No direct script access.');
/**
 * FORGE (FORm GEneration) library.
 *
 * $Id$
 *
 * @package    Forge
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Forge_Core {

	// Template variables
	protected $template = array
	(
		'title' => '',
		'class' => '',
		'open'  => '',
		'close' => '',
	);

	// Form attributes
	protected $attr = array();

	// Form inputs and hidden inputs
	public $inputs = array();
	public $hidden = array();

	// Error message format, only used with custom templates
	public $error_format = '<p class="error">{message}</p>';
	public $newline_char = "\n";

	/**
	 * Form constructor. Sets the form action, title, method, and attributes.
	 *
	 * @return  void
	 */
	public function __construct($action = '', $title = '', $method = NULL, $attr = array())
	{
		// Set form attributes
		$this->attr['action'] = $action;
		$this->attr['method'] = empty($method) ? 'post' : $method;

		// Set template variables
		$this->template['title'] = $title;

		// Empty attributes sets the class to "form"
		empty($attr) and $attr = array('class' => 'form');

		// String attributes is the class name
		is_string($attr) and $attr = array('class' => $attr);

		// Extend the template with the attributes
		$this->attr += $attr;
	}

	/**
	 * Magic __get method. Returns the specified form element.
	 *
	 * @param   string   unique input name
	 * @return  object
	 */
	public function __get($key)
	{
		if (isset($this->inputs[$key]))
		{
			return $this->inputs[$key];
		}
		elseif (isset($this->hidden[$key]))
		{
			return $this->hidden[$key];
		}
	}

	/**
	 * Magic __call method. Creates a new form element object.
	 *
	 * @throws  Kohana_Exception
	 * @param   string   input type
	 * @param   string   input name
	 * @return  object
	 */
	public function __call($method, $args)
	{
		// Class name
		$input = 'Form_'.ucfirst($method);

		// Create the input
		switch(count($args))
		{
			case 1:
				$input = new $input($args[0]);
			break;
			case 2:
				$input = new $input($args[0], $args[1]);
			break;
			default:
				throw new Kohana_Exception('forge.invalid_input', $input);
		}

		if ( ! ($input instanceof Form_Input) AND ! ($input instanceof Forge))
			throw new Kohana_Exception('forge.unknown_input', get_class($input));

		$input->method = $this->attr['method'];

		if ($name = $input->name)
		{
			// Assign by name
			if ($method == 'hidden')
			{
				$this->hidden[$name] = $input;
			}
			else
			{
				$this->inputs[$name] = $input;
			}
		}
		else
		{
			// No name, these are unretrievable
			$this->inputs[] = $input;
		}

		return $input;
	}

	/**
	 * Set a form attribute. This method is chainable.
	 *
	 * @param   string|array  attribute name, or an array of attributes
	 * @param   string        attribute value
	 * @return  object
	 */
	public function set_attr($key, $val = NULL)
	{
		if (is_array($key))
		{
			// Merge the new attributes with the old ones
			$this->attr = array_merge($this->attr, $key);
		}
		else
		{
			// Set the new attribute
			$this->attr[$key] = $val;
		}

		return $this;
	}

	/**
	 * Validates the form by running each inputs validation rules.
	 *
	 * @return  bool
	 */
	public function validate()
	{
		$status = TRUE;
		foreach($this->inputs as $input)
		{
			if ($input->validate() == FALSE)
			{
				$status = FALSE;
			}
		}

		return $status;
	}

	/**
	 * Returns the form as an array of input names and values.
	 *
	 * @return  array
	 */
	public function as_array()
	{
		$data = array();
		foreach(array_merge($this->hidden, $this->inputs) as $input)
		{
			if ($name = $input->name)
			{
				// Return only named inputs
				$data[$name] = $input->value;
			}
		}
		return $data;
	}

	/**
	 * Changes the error message format. Your message formatting must
	 * contain a {message} placeholder.
	 *
	 * @throws  Kohana_Exception
	 * @param   string   new message format
	 * @return  void
	 */
	public function error_format($string = '')
	{
		if (strpos((string) $string, '{message}') === FALSE)
			throw new Kohana_Exception('validation.error_format');

		$this->error_format = $string;
	}

	/**
	 * Creates the form HTML
	 *
	 * @param   string   form view template name
	 * @param   boolean  use a custom view
	 * @return  string
	 */
	public function html($template = 'forge_template', $custom = FALSE)
	{
		// Load template
		$form = new View($template);

		if ($custom)
		{
			// Using a custom view

			$data = array();
			foreach (array_merge($this->hidden, $this->inputs) as $input)
			{
				$data[$input->name] = $input;

				// Groups will never have errors, so skip them
				if ($input instanceof Form_Group)
					continue;

				// Compile the error messages for this input
				$messages = '';
				$errors = $input->error_messages();
				if (is_array($errors) AND ! empty($errors))
				{
					foreach($errors as $error)
					{
						// Replace the message with the error in the html error string
						$messages .= str_replace('{message}', $error, $this->error_format).$this->newline_char;
					}
				}

				$data[$input->name.'_errors'] = $messages;
			}

			$form->set($data);
		}
		else
		{
			// Using a template view

			$form->set($this->template);
			$hidden = array();
			if ( ! empty($this->hidden))
			{
				foreach($this->hidden as $input)
				{
					$hidden[$input->name] = $input->value;
				}
			}

			$form_type = 'open';
			// See if we need a multipart form
			foreach ($this->inputs as $input)
			{
				if ($input instanceof Form_Upload)
				{
					$form_type = 'open_multipart';
				}
			}

			// Set the form open and close
			$form->open  = form::$form_type(arr::remove('action', $this->attr), $this->attr, $hidden);
			$form->close = form::close();

			// Set the inputs
			$form->inputs = $this->inputs;
		}

		return $form;
	}

	/**
	 * Returns the form HTML
	 */
	public function __toString()
	{
		return $this->html();
	}

} // End Forge