<?php
class ORM_Tree_Core{
	protected $model;
	
	protected $class;
	
	protected $parent_column='parent_id';
	protected $pk;
	
	public function __construct(ORM $model)
	{
		$this->model=$model;
		$this->class=get_class($model);
		$this->pk=$this->model->primary_key;
	}
	public function set_parent_column($value)
	{
		$this->parent_column=$value;
	}
	public function get_parent_column()
	{
		return $this->parent_column;
	}
	public function get_children()
	{
		$method='find_all_by_'.$this->parent_column;
		return $this->model->where($this->parent_column,(int) $this->model->{$this->pk})->find_all();
	}
	public function has_parent()
	{
		
		return (is_integer($this->model->{$this->parent_column})&&$this->model->{$this->parent_column}!=0);
	}
	public function get_parent(){
		if($this->has_parent())
			return $this->model->find($this->model->{$this->parent_column},true);
			
		return false;
	}
	public function get_ancestors()
	{
		if($this->has_parent()){
			return array();
		}
		$last=$this->get_parent();
		$ancestors=array($last);
				
		while($last->tree->has_parent())
		{
			$last=$last->tree->get_parent();
			$ancestors[]=$last;
		}
		return $ancestors;
	}
	public function get_siblings(){
		$method='find_all_by_'.$this->parent_column;		
		$this->model->where(array('id!='=>$this->model->id));
		return $this->model->$method($this->model->{$this->parent_column});
	}
	public function get_self_and_siblings()
	{
		$method='find_all_by_'.$this->parent_column;
		return $this->model->$method($this->model->{$this->parent_column});
	}
	public function add_child( $child)
	{
		if(get_class($child)!=$this->class)
			throw new Kohana_Exception('database.class_of_wrong_type', get_class($child));
		
		if(!$this->model->id>0)
		{
			$this->model->save();
		}
		
		$child->{$this->parent_column}=$this->model->id;
		return $child->save();	

	}
}
