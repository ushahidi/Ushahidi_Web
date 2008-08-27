<?php defined('SYSPATH') or die('No direct script access.');
/**
 * FORGE upload input library.
 *
 * $Id$
 *
 * @package    Forge
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Form_Upload_Core extends Form_Input {

	protected $data = array
	(
		'class' => 'upload',
		'value' => '',
	);

	protected $protect = array('type', 'label', 'value');

	// Upload data
	protected $upload;

	// Upload directory and filename
	protected $directory;
	protected $filename;

	public function __construct($name, $filename = FALSE)
	{
		parent::__construct($name);

		if ( ! empty($_FILES[$name]))
		{
			if (empty($_FILES[$name]['tmp_name']) OR is_uploaded_file($_FILES[$name]['tmp_name']))
			{
				// Cache the upload data in this object
				$this->upload = $_FILES[$name];

				// Hack to allow file-only inputs, where no POST data is present
				$_POST[$name] = $this->upload['name'];

				// Set the filename
				$this->filename = empty($filename) ? FALSE : $filename;
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

	/**
	 * Sets the upload directory.
	 *
	 * @param   string   upload directory
	 * @return  void
	 */
	public function directory($dir = NULL)
	{
		// Use the global upload directory by default
		empty($dir) and $dir = Config::item('upload.upload_directory');

		// Make the path asbolute and normalize it
		$dir = str_replace('\\', '/', realpath($dir)).'/';

		// Make sure the upload director is valid and writable
		if ($dir === '/' OR ! is_dir($dir) OR ! is_writable($dir))
			throw new Kohana_Exception('upload.not_writable', $dir);

		$this->directory = $dir;
	}

	public function validate()
	{
		// The upload directory must always be set
		empty($this->directory) and $this->directory();

		// By default, there is no uploaded file
		$filename = '';

		if ($status = parent::validate() AND $this->upload['error'] === UPLOAD_ERR_OK)
		{
			// Set the filename to the original name
			$filename = $this->upload['name'];

			if (Config::item('upload.remove_spaces'))
			{
				// Remove spaces, due to global upload configuration
				$filename = preg_replace('/\s+/', '_', $this->data['value']);
			}

			if (file_exists($filepath = $this->directory.$filename))
			{
				if ($this->filename !== TRUE OR ! is_writable($filepath))
				{
					// Prefix the file so that the filename is unique
					$filepath = $this->directory.'uploadfile-'.uniqid(time()).'-'.$this->upload['name'];
				}
			}

			// Move the uploaded file to the upload directory
			move_uploaded_file($this->upload['tmp_name'], $filepath);
		}

		if ( ! empty($_POST[$this->data['name']]))
		{
			// Reset the POST value to the new filename
			$this->data['value'] = $_POST[$this->data['name']] = empty($filepath) ? '' : $filepath;
		}

		return $status;
	}

	protected function rule_required()
	{
		if (empty($this->upload) OR $this->upload['error'] === UPLOAD_ERR_NO_FILE)
		{
			$this->errors['required'] = TRUE;
		}
	}

	public function rule_allow()
	{
		if (empty($this->upload['tmp_name']) OR count($types = func_get_args()) == 0)
			return;

		if (defined('FILEINFO_MIME'))
		{
			$info = new finfo(FILEINFO_MIME);

			// Get the mime type using Fileinfo
			$mime = $info->file($this->upload['tmp_name']);

			$info->close();
		}
		elseif (ini_get('magic.mime') AND function_exists('mime_content_type'))
		{
			// Get the mime type using magic.mime
			$mime = mime_content_type($this->upload['tmp_name']);
		}
		else
		{
			// Trust the browser
			$mime = $this->upload['type'];
		}

		// Allow nothing by default
		$allow = FALSE;

		foreach ($types as $type)
		{
			if (in_array($mime, Config::item('mimes.'.$type)))
			{
				// Type is valid
				$allow = TRUE;
				break;
			}
		}

		if ($allow === FALSE)
		{
			$this->errors['invalid_type'] = TRUE;
		}
	}

	public function rule_size($size)
	{
		// Skip the field if it is empty
		if (empty($this->upload) OR $this->upload['error'] === UPLOAD_ERR_NO_FILE)
			return;

		$bytes = (int) $size;

		switch (substr($size, -2))
		{
			case 'GB': $bytes *= 1024;
			case 'MB': $bytes *= 1024;
			case 'KB': $bytes *= 1024;
			default: break;
		}

		if (empty($this->upload['size']) OR $this->upload['size'] > $bytes)
		{
			$this->errors['max_size'] = array($size);
		}
	}

	protected function html_element()
	{
		return form::upload($this->data);
	}

} // End Form Upload