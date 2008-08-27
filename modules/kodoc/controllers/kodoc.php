<?php defined('SYSPATH') or die('No direct script access.');

class Kodoc_Controller extends Template_Controller {

	// Do not allow to run in production
	const ALLOW_PRODUCTION = FALSE;

	public $template = 'kodoc/template';

	// Kodoc instance
	protected $kodoc;

	public function __construct()
	{
		parent::__construct();

		$active = $this->uri->segment(2) ? $this->uri->segment(2) : 'core';

		// Add the menu to the template
		$this->template->menu = new View('kodoc/menu', array('active' => $active));
	}

	public function index()
	{
		$this->template->content = 'hi';
	}

	public function media()
	{
		if (isset($this->profiler)) $this->profiler->disable();

		// Get the filename
		$file = implode('/', $this->uri->segment_array(1));
		$ext = strrchr($file, '.');

		if ($ext !== FALSE)
		{
			$file = substr($file, 0, -strlen($ext));
			$ext = substr($ext, 1);
		}

		// Disable auto-rendering
		$this->auto_render = FALSE;

		try
		{
			// Attempt to display the output
			echo new View('kodoc/'.$file, NULL, $ext);
		}
		catch (Kohana_Exception $e)
		{
			Event::run('system.404');
		}
	}

	public function __call($method, $args)
	{
		if (count($segments = $this->uri->segment_array(1)) > 1)
		{
			// Find directory (type) and filename
			$type = array_shift($segments);
			$file = implode('/', $segments);

			if (substr($file, -(strlen(EXT))) === EXT)
			{
				// Remove extension
				$file = substr($file, 0, -(strlen(EXT)));
			}

			if ($type === 'config')
			{
				if ($file === 'config')
				{
					// This file can only exist in one location
					$file = APPPATH.$type.'/config'.EXT;
				}
				else
				{
					foreach (array_reverse(Kohana::include_paths()) as $path)
					{
						if (is_file($path.$type.'/'.$file.EXT))
						{
							// Found the file
							$file = $path.$type.'/'.$file.EXT;
							break;
						}
					}
				}
			}
			else
			{
				// Get absolute path to file
				$file = Kohana::find_file($type, $file);
			}

			if (in_array($type, Kodoc::get_types()))
			{
				// Load Kodoc
				$this->kodoc = new Kodoc($type, $file);

				// Set the title
				$this->template->title = implode('/', $this->uri->segment_array(1));

				// Load documentation for this file
				$this->template->content = new View('kodoc/documentation');

				// Exit this method
				return;
			}
		}

		// Nothing to document
		url::redirect('kodoc');
	}

} // End Kodoc Controller