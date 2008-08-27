<?php
class Model_Formation_Core extends Formation{
	
	//carries name of the model or instance of it
	protected $_model;

	//Which fields to exclude 
	protected $exclude=array('id');
	
	//Which fields to include
	protected $form_fields=array();
	
	protected $disabled=array();
	
	public static function factory($model=false,$build=false)
	{
		return new self($model,$build);
	}
	public function __construct($model=false,$build=true)
	{
		parent::__construct();
		
		//If $model is false, create empty model,
		//if it is not an object use $argument to find a model (pass id for example)
		//else the model passed is an object 
		if($model==false)
		{
			$this->_model=new $this->_model;
		}
		elseif(!is_object($model))
		{
			if(substr($model,-6) == '_Model')
			{
				$this->_model= new $model;
						
			}
			else
			{
				$this->_model= ORM::factory($model);
			}
			
		}
		else
		{
			$this->_model=$model;				
		}
		
		$build and $this->build_form();
	}
	/**
	 * Set form fields
	 *
	 * @param array $form_fields
	 * @return unknown
	 */
	public function set_form_fields(array $form_fields)
	{
		$this->form_fields=$form_fields;
		return $this;
	}
	/**
	 * Set form fields
	 *
	 * @param array $form_fields
	 * @return unknown
	 */
	public function set_exclude(array $form_fields)
	{
		$this->exclude=$form_fields;
		return $this;
	}
	/**
	 * Set form fields
	 *
	 * @param array $form_fields
	 * @return unknown
	 */
	public function set_disabled(array $form_fields)
	{
		$this->exclude=$form_fields;
		return $this;
	}	
	/**
	 * Build form
	 *
	 * @param boolean $guess_fields, automatic field determination
	 */
	public function build_form()
	{
		//Get field types and rules, filters everything
		$validate=$this->_model->get_validate();
		$relationships=$this->_model->get_relationships();
		
		
		foreach($this->_model->table_columns as $name=>$property)
		{
			if(in_array($name,$this->exclude))
				continue;

			if($this->form_fields!=array() and !in_array($name,$this->form_fields))
				continue;
			
			//By default all fields are input
			$type='input';
			if(isset($validate[$name]['type']))
			{
				$type=$validate[$name]['type'];

			}
			else
			{
				//Try to guess field type given the database information
				if($property['type']=='string' AND !isset($property['length']))
				{
					$type='textarea';
				}
				if(isset($property['format'])&&$property['format']=='0000-00-00 00:00:00')
				{
					$type='input';
				}
				
				if(substr($name,0,3)=='is_')
				{
					$type='checkbox';
				}
				$foreign_name=substr($name,0,-3);
				
				if(!empty($relationships['belongs_to'])&&in_array($foreign_name,$relationships['belongs_to']))
				{
					$array=(Ext_ORM::factory($foreign_name)->find_all());
					$options=array();
					foreach($array as $record){
						$options[$record->id]=$record->__toString();
					}
					$type='dropdown';
				}		
			
			}
			//Adding elements
			$this->add_element($type,$name);
			
			if(isset($options)&&!empty($options))
			{
				$this[$name]->set_options($options);

			}
			$options=array();

			if(in_array($name,$this->disabled))
			{
				$this[$name]->set_attr('disabled','disabled');
			}
			if(isset($validate[$name]['pre_filters']))
			{
				foreach($validate[$name]['pre_filters'] as $filter)
				{
						$this[$name]->add_pre_filter($filter);
				}
			}				
			if(isset($validate[$name]['rules']))
			{
				foreach($validate[$name]['rules'] as $rule)
				{
					//array for when you give arguments to a rule
					if(is_array($rule))
					{
						$this[$name]->add_rule($rule[0],$rule[1]);
					}else{
						$this[$name]->add_rule($rule);
					}
				}
			}
			if(isset($validate[$name]['callbacks']))
			{
				foreach($validate[$name]['callbacks'] as $callback)
				{
					$this[$name]->add_callback($callback);
				}
			}	
			if(isset($validate[$name]['post_filters']))
			{
				foreach($validate[$name]['post_filters'] as $filter)
				{
						$this[$name]->add_post_filter($filter);
				}
			}	

			//Additional rules retrieved from database
			if(isset($property['length']))
			{
				$this[$name]->add_rule('Rule_Max_Length',$property['length']);
			}
						
			//If model exists add its values to the fields
			if($this->_model->exists())
			{
				$this->set_values($this->_model->as_array());
			}
			
		}

		$this->add_element('submit','Submit');
		return $this;
	}
	/**
	 * Retrieve model, might be handy sometime
	 *
	 * @return unknown
	 */
	public function model()
	{
		return $this->_model;
	}
	/**
	 * Save form, validate first
	 *
	 * @param unknown_type $commit
	 * @return unknown
	 */
	public function save($commit=true,$values = array()){
		if($values == array())
		{
			$values = $_POST;
		}
		if($this->validate($values))
		{
			
			$this->_model->populate($this->as_array());
			if($commit==true)
			{
				return $this->_model->save();
			}
			return $this->_model;
		}
		return false;
	}
}
