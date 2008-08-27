<?php defined('SYSPATH') or die('No direct access allowed.');


class Formation_Core extends Validate{
	
	// Form attributes
	protected $attr = array
	(
		'method'=>'POST'
	);
	
	//Template with the form to load
	protected $template='formation_template';
	
	//Key/value pairs passed onto the template
	protected $template_vars=array();

	protected $order=array();
	
	protected $order_updated=true;
	/**
	 * Constructor
	 *
	 * @param string $legend
	 */
	public function __construct($legend='Form')
	{
		$this->template_vars['legend']=$legend;
		// Set the new attribute
		$this->set_attr('id',$legend);		
		// Set element autoloader
		spl_autoload_register(array('Validate', 'auto_load'));		
		
		//Log::add('debug', 'Formation library initialized');

	}
	/**
	 * Set variables for form template
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key,$value)
	{
		$this->set_template_var($key,$value);
	}
	/**
	 * Get template variables
	 *
	 * @param unknown_type $key
	 * @return unknown
	 */
	public function __get($key)
	{
		if(isset($this->template_vars[$key]))
			return $this->template_vars[$key];
		
		return false;
	}
	public function set_template_var($key,$value)
	{
		$this->template_vars[$key]=$value;
		return $this;
	}
	/**
	 * Validate the form
	 *
	 * @return unknown
	 */
	public function validate($values = array())
	{	
		if($this->load_values($values))
		{
			//Validate form
			return parent::validate();
		}
		return false;
	}
	/**
	 * Validate partial
	 *
	 * @param unknown_type $partial
	 * @return unknown
	 */
	public function validate_partial($partial)
	{
		if($this->load_values($parial))
		{
			//Validate form
			return parent::validate_partial($partial);			
		}
		return false;

	}	
	/**
	 * Load values from POST,GET
	 *
	 * @return bool
	 */
	protected function load_values($values = array())
	{
		if(empty($values))
			return false;
			
		//Load values from a post
		foreach($this as $name=>$field)
		{
			if(!($field instanceof Element_Group))
			{
				//Prevent disabled elements from being set
				if($this[$name]->get_attr('disabled')!='disabled')
				{
					//Load value if present
					if(isset($values[$name]))
					{
						$this[$name]->load_value($values[$name]);	
					}
												
				}

			}
		}
		return true;
	}
	/**
	 * Add element to form
	 *
	 * @param Field object, name $type
	 * @param unknown_type $name
	 * @return unknown
	 */
	public function add_element($type,$name=null)
	{
		
		$this->order_updated=true;

		if($type instanceof Form_Field && $name==null)
		{
			$name=$type->get_name();
			
			$this[$name]=$type;
		}
		else
		{
			if ($name==null)
				throw new Kohana_Exception('formation.invalid_rule', get_class($rule));

			if(count($args=func_get_args())>2)
			{
				$args=array_slice($args,2);
			}
			else
			{
				$args=null;
			}
			
			$type='Element_'.ucfirst(strtolower($type));
			$this[$name]=new $type($name,$args);	
		}
		
		$this[$name]->set_order(10+count($this)*10);
		$this->order[$name]=$this[$name]->get_order();
		
		return $this[$name];
	}
	/**
	 * Remove element from form
	 *
	 * @param unknown_type $element_name
	 */
	public function remove_element($element_name)
	{
		if(isset($this[$element_name]))
		{
			unset($this[$element_name]);
			$this->order_updated=true;
			return $this;
		}
		return false;
	}
	/**
	 * Returns element object
	 * @return mixed
	 */
	public function get_element($element_name)
	{
		if(isset($this[$element_name]))
		{
			return ($this[$element_name]);
		}
		return false;
	}
	/**
	 * Clear elements from form 
	 * 
	 * @return object
	 */
	public function clear_elements()
	{
		$this->order_updated=true;
		foreach($this as $element_name=>$object)
		{
			if($object instanceof Element_Input)
			{
				$elements[]=$element_name;

			}
		}
		foreach($elements as $element)
		{
			$this->remove_element($element);
		}
		return $this;
	}	
	/**
	 * Add display group to form
	 *
	 * @param array element names to add or element objects
	 * @param name of the group $name
	 */
	public function add_group($group_element=null,$name)
	{
		$this->order_updated=true;
		$this[$name]=new Element_Group($name);

		
		$this[$name]->set_order(10+count($this)*10);
		$this->order[$name]=$this[$name]->get_order();
				
		foreach((array) $group_element as $element)
		{
			if(isset($this[$element]))
			{
				$element=$this[$element];
			}
							
			if($element instanceof Element_Input)
			{
				$this[$name]->add_element($element);
				$this->remove_element($element->name);						
			}
			else
			{
				throw new Kohana_Exception('formation.invalid_input', get_class($element));
			}
		}	
		
		return $this[$name];
	}
	/**
	 * Remove group and its elements from form
	 *
	 * @param string $group_name
	 * @return object
	 */
	public function remove_group($group_name)
	{
		$this->order_updated=true;
		if(isset($this[$group_name]))
		{
			unset($this[$group_name]);
		}
		return $this;
	}
	/**
	 * Returns element object
	 * @return mixed
	 * 
	 */
	public function get_group($group_name)
	{
		if(isset($this[$group_name]))
		{
			return ($this[$group_name]);
		}
		return false;
	}	
	/**
	 * Clears all groups
	 * @return mixed
	 */
	public function clear_groups()
	{
		$this->order_updated=true;
		foreach($this as $group_name=>$object)
		{
			if($object instanceof Element_Group)
			{
				$groups[]=$group_name;

			}
		}
		foreach($groups as $group)
		{
			$this->remove_group($group);
		}
		return $this;
	}
	/**
	 * Set the template for the form
	 *
	 * @param unknown_type $template
	 * @return object
	 */
	public function set_template($template)
	{
		$this->template=$template;
		return $this;
	}
	/**
	 * Get template of the form
	 *
	 * @return string
	 */
	public function get_template()
	{
		return $this->template;
	}
	/**
	 * Test for this form being multipart
	 */
	public function is_multipart()
	{
		// See if we need a multipart form
		foreach ($this as $input)
		{
			if ($input instanceof Element_Upload)
			{
				return true;
			}
		}	
		return false;
	}
	/**
	 * Render the form with the given template
	 *
	 * @return string
	 */
	public function render($template=null)
	{
		if($template!=null)
		{
			$this->set_template($template);
		}
		
		$form=new View($this->template);
		
		$form_type= $this->is_multipart() ? 'open_multipart' : 'open';

		//Form open and close
		$form->open  = form::$form_type(arr::remove('action', $this->attr), $this->attr);
		$form->close = form::close();

		//Errors and messages passed on to the form, not used in formation_template.php
		$form->errors= $this->errors();
		
		$this->update_order();
		// Set the inputs
		$form->inputs = $this->ordered_elements();
		
		//Set any template vars set using __set()
		$form->set($this->template_vars);
		
		return $form;	
	}
	protected function ordered_elements()
	{
		$inputs=array();
		foreach($this->order as $order =>$value)
		{
			$inputs[$order]=$this[$order];
		}
		return $inputs;
	}
	/**
	 * Returns the form HTML
	 */
	public function __toString()
	{
		return (string) $this->render();
	}
	/**
	 * Set a form attribute. This method is chainable.
	 *
	 * @param   string        attribute name, or an array of attributes
	 * @param   string        attribute value
	 * @return  object
	 */
	public function set_attr($key, $val = NULL)
	{
		// Set the new attribute
		$this->attr[$key] = $val;
		return $this;
	}
	/**
	 * Return attribute of <form>
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get_attr($key)
	{
		if(isset($this->attr[$key]))
		{
			return $this->attr[$key];
		}
		return false;
	}
	/**
	 * Set the method for the form
	 *
	 * @param string $method
	 * @return object
	 */
	public function set_method($method)
	{
		return $this->set_attr('method',$method);
	}
	/**
	 * Get the method of the form
	 *
	 * @return mixed
	 */
	public function get_method()
	{
		return $this->get_attr('method');
	}
	/**
	 * Set action of the form
	 *
	 * @param string $action
	 * @return object
	 */
	public function set_action($action)
	{
		return $this->set_attr('action',$action);
	}
	/**
	 * Get action of the form
	 *
	 * @return string
	 */
	public function get_action()
	{
		return $this->get_attr('action');
	}	
	public function set_class($class)
	{
		$this->set_attr('class',$class);
		return $this;
	}
	public function set_id($id)
	{
		$this->set_attr('id',$id);
		return $this;
	}
	/**
	 * set values of the form e.g. form db
	 * deprecated: use populate_form
	 *
	 * @param array $data
	 * @return object
	 */
	public function set_values(array $data)
	{
		return $this->populate_form($data);
	}	
	/**
	 * set values of the form e.g. form db
	 *
	 * @param array $data
	 * @return object
	 */	
	public function populate_form(array $data)
	{
		foreach($data as $key=>$value)
		{			
			if(isset($this[$key]))
			{			
				$this[$key]->set_value($value);
			}
		}
		return $this;		
		
	}
	public function update_order()
	{
		foreach($this as $name=>$field)
		{
			$this->order[$name]=$this[$name]->get_order();
		}
		$this->sort();
		$this->order_updated=true;
		return $this;
	}
	/**
     * Sort items according to their order
     * 
     * @return void
     */
    public function sort()
    {
    	if($this->order_updated==true)
    	{
    		asort($this->order);
    	}
    	
		$this->order_updated=false;
    }	
    /**
     * Current element
     * 
     * @return
     */
    public function current()
    {
        $this->sort();
        current($this->order);
        $key = key($this->order);
		
        if(isset($this[$key]))
        	return $this[$key];
    }
    /**
     * Current element group name
     * 
     * @return string
     */
    public function key()
    {
        $this->sort();
        return key($this->order);
    }
    /**
     * Move pointer to next element group
     * 
     * @return void
     */
    public function next()
    {
        $this->sort();
        next($this->order);
    }
    /**
     * Move pointer to beginning of element group loop
     * 
     * @return void
     */
    public function rewind()
    {
        $this->_sort();
        reset($this->order);
    }
    /**
     * Determine if current element group is valid
     * 
     * @return bool
     */
    public function valid()
    {
        $this->sort();
        return (current($this->order) !== false);
    }
    /**
     * Count of elements that are iterable
     * 
     * @return int
     */
    public function count()
    {
        return count($this->order);
    }    
}
