<?php
class Element_Upload_core extends Element_Input {
	
	protected $attr = array	(	);
	// Upload data
	protected $upload;

	// Upload directory and filename
	protected $directory;
	protected $rel_directory;
	protected $filename = FALSE;

	protected $filepath;
	
	protected $upload_prefix='uploadfile-';
	
	public function __construct($name)
	{
		parent::__construct($name,null);

		if ( ! empty($_FILES[$name]))
		{
			if (empty($_FILES[$name]['tmp_name']) OR is_uploaded_file($_FILES[$name]['tmp_name']))
			{
				// Cache the upload data in this object
				$this->upload = $_FILES[$name];
				
				// Hack to allow file-only inputs, where no POST data is present
				$_POST[$name] = $this->upload['name'];


			}
			else
			{
				// Attempt to delete the invalid file
				is_writable($_FILES[$name]['tmp_name']) and unlink($_FILES[$name]['tmp_name']);

				// Invalid file upload, possible hacking attempt
				unset($_FILES[$name]);
			}
		}
	}	
	/*
	 * Maximum size rule, shortcut
	 */
	public function set_max_size($size)
	{
		$this->add_rule('upload_Size',$size);
		
		return $this;
	}
	/**
	 * Sets the upload directory.
	 *
	 * @param   string   upload directory
	 * @return  void
	 */
	public function set_directory($dir = NULL)
	{
		// Use the global upload directory by default
		empty($dir) and $dir = Config::item('upload.upload_directory');

		$realpath=realpath($dir);
		$reldir = str_replace('\\', '/', rtrim($dir,'/')).'/';
		// Make the path asbolute and normalize it
		$dir = str_replace('\\', '/', $realpath).'/';
		
		
		
		// Make sure the upload director is valid and writable
		if ($dir === '/' OR ! is_dir($dir) OR ! is_writable($dir))
			throw new Kohana_Exception('upload.not_writable', $dir);
		
		$this->rel_directory=$reldir;
		$this->directory = $dir;
		return $this;
	}
	/**
	 * Get upload directory
	 *
	 * @return unknown
	 */
	public function get_directory()
	{
		if(empty($this->directory))
		{
			$this->set_directory();
		}
		return $this->directory;
	}
	/**
	 * Get filename
	 *
	 * @return unknown
	 */
	public function get_filename()
	{
		if(empty($this->filename))
		{
			$this->set_filename($this->upload['name']);
		}
		return $this->filename;
	}
	public function set_filename($filename){
		return $this->filename=$filename;
	}
	public function get_prefix(){
		return $this->upload_prefix;
	}
	public function set_prefix($prefix)
	{
		$this->upload_prefix=$prefix;
	}
	/**
	 * Validate upload
	 *
	 * @return unknown
	 */
	public function validate()
	{

		// The upload directory must always be set
		empty($this->directory) and $this->set_directory();

		// By default, there is no uploaded file
		$filename = '';

		if ($status = parent::validate() AND $this->upload['error'] === UPLOAD_ERR_OK)
		{
			
			// Set the filename to the original name
			$filename = $this->get_filename();

			if (Config::item('upload.remove_spaces'))
			{
				// Remove spaces, due to global upload configuration
				$filename = preg_replace('/\s+/', '_', $this->get_filename());
			}

			if (file_exists($filepath = $this->directory.$filename))
			{
				if ($this->get_filename() !== TRUE OR ! is_writable($filepath))
				{
					$filename=$this->upload_prefix.uniqid(time()).'-'.$this->upload['name'];
					// Prefix the file so that the filename is unique
					$filepath = $this->directory.$filename;
				}
			}
			$this->filename=$filename;
			$this->filepath=$filepath;
			// Move the uploaded file to the upload directory
			move_uploaded_file($this->upload['tmp_name'], $filepath);
			
			if ( ! empty($_POST[$this->name]))
			{
				// Reset the POST value to the new filename
				$this->value = $_POST[$this->name] = $filepath;
			}			
		}
		

		return $status;
	}

	protected function html_element()
	{
		$data = $this->attr;
		$data['name']=$this->name;
		
		return form::upload($data);
	}	
}
