<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Kohana - The Swift PHP Framework
 *
 *  License:
 *  author    - Kohana Team
 *  copyright - (c) 2007 Kohana Team
 *  license   - <http://kohanaphp.com/license.html>
 */

/**
 * Provides self-generating documentation about Kohana.
 */
class Kodoc_Core {

	protected static $types = array
	(
		'core',
		'config',
		'helpers',
		'libraries',
		'models',
		'views'
	);

	public static function get_types()
	{
		return self::$types;
	}

	public static function get_files()
	{
		// Extension length
		$ext_len = -(strlen(EXT));

		$files = array();
		foreach (self::$types as $type)
		{
			$files[$type] = array();
			foreach (Kohana::list_files($type, TRUE) as $file)
			{
				// Not a source file
				if (substr($file, $ext_len) !== EXT)
					continue;

				// Remove the dirs from the filename
				$file = preg_replace('!^.+'.$type.'/(.+)'.EXT.'$!', '$1', $file);

				// Skip utf8 function files
				if ($type === 'core' AND substr($file, 0, 5) === 'utf8/')
					continue;

				if ($type === 'libraries' AND substr($file, 0, 8) === 'drivers/')
				{
					// Remove the drivers directory from the file
					$file = explode('_', substr($file, 8));

					if (count($file) === 1)
					{
						// Driver interface
						$files[$type][current($file)][] = current($file);
					}
					else
					{
						// Driver is class suffix
						$driver = array_pop($file);

						// Library is everything else
						$library = implode('_', $file);

						// Library driver
						$files[$type][$library][] = $driver;
					}
				}
				else
				{
					$files[$type][$file] = NULL;
				}
			}
		}

		return $files;
	}

	public static function remove_docroot($file)
	{
		return preg_replace('!^'.preg_quote(DOCROOT, '!').'!', '', $file);
	}

	public static function humanize_type($types)
	{
		$types = is_array($types) ? $types : explode('|', $types);

		$output = array();
		while ($t = array_shift($types))
		{
			$output[] = '<tt>'.trim($t).'</tt>';
		}

		return implode(' or ', $output);
	}

	public static function humanize_value($value)
	{
		if ($value === NULL)
		{
			return 'NULL';
		}
		elseif (is_bool($value))
		{
			return $value ? 'TRUE' : 'FALSE';
		}
		elseif (is_string($value))
		{
			return 'string '.$value;
		}
		elseif (is_numeric($value))
		{
			return (is_int($value) ? 'int' : 'float').' '.$value;
		}
		elseif (is_array($value))
		{
			return 'array';
		}
		elseif (is_object($value))
		{
			return 'object '.get_class($value);
		}
	}

	// All files to be parsed
	protected $file = array();

	public function __construct($type, $filename)
	{
		// Parse the file
		$this->file = $this->parse($type, $filename);
	}

	/**
	 * Fetch documentation for all files parsed.
	 *
	 * Returns:
	 *  array: file documentation
	 */
	public function get()
	{
		return $this->file;
	}

	/**
	 * Parse a file for Kodoc commands, classes, and methods.
	 *
	 * Parameters:
	 *  string: file type
	 *  string: absolute filename path
	 */
	protected function parse($type, $filename)
	{
		// File definition
		$file = array
		(
			'type'      => $type,
			'comment'   => '',
			'file'      => self::remove_docroot($filename),
		);

		// Read the entire file into an array
		$data = file($filename);

		foreach ($data as $line)
		{
			if (strpos($line, 'class') !== FALSE AND preg_match('/(?:class|interface)\s+([a-z0-9_]+).+{$/i', $line, $matches))
			{
				// Include the file if it has not already been included
				class_exists($matches[1], FALSE) or include_once $filename;

				// Add class to file info
				$file['classes'][] = $this->parse_class($matches[1]);
			}
		}

		if (empty($file['classes']))
		{
			$block  = NULL;
			$source = NULL;

			foreach ($data as $line)
			{
				switch (substr(trim($line), 0, 2))
				{
					case '/*':
						$block = '';
						continue 2;
					break;
					case '*/':
						$source = TRUE;
						continue 2;
					break;
				}

				if ($source === TRUE)
				{
					if (preg_match('/\$config\[\'(.+?)\'\]\s+=\s+([^;].+)/', $line, $matches))
					{
						$source = array
						(
							$matches[1],
							$matches[2]
						);
					}
					else
					{
						$source = array();
					}

					$file['comments'][] = array_merge($this->parse_comment($block), array('source' => $source));

					$block  = NULL;
					$source = FALSE;
				}
				elseif (is_string($block))
				{
					$block .= $line;
				}
			}

		}

		return $file;
	}

	protected function parse_comment($block)
	{
		if (($block = trim($block)) == '')
			return $block;

		// Explode the lines into an array and trim them
		$block = array_map('trim', explode("\n", $block));

		if (current($block) === '/**')
		{
			// Remove comment opening
			array_shift($block);
		}

		if (end($block) === '*/')
		{
			// Remove comment closing
			array_pop($block);
		}

		// Start comment
		$comment = array();

		while ($line = array_shift($block))
		{
			// Remove * from the line
			$line = trim(substr($line, 2));

			if (substr($line, 0, 1) === '$' AND substr($line, -1) === '$')
			{
				// Skip SVN property inserts
				continue;
			}

			if (substr($line, 0, 1) === '@')
			{
				if (preg_match('/^@(.+?)\s+(.+)$/', $line, $matches))
				{
					$comment[$matches[1]][] = $matches[2];
				}
			}
			else
			{
				$comment['about'][] = $line;
			}
		}

		if ( ! empty($comment['about']))
		{
			$token = '';
			$block = '';
			$about = '';

			foreach ($comment['about'] as $line)
			{
				if (strpos($line, '`') !== FALSE)
				{
					$line = preg_replace('/`([^`].+?)`/', '<tt>$1</tt>', $line);
				}

				if (substr($line, 0, 2) === '- ')
				{
					if ($token !== 'ul')
					{
						$about .= $this->comment_block($token, $block);
						$block  = '';
					}

					$token = 'ul';
					$line  = '<li>'.trim(substr($line, 2)).'</li>'."\n";
				}
				elseif (preg_match('/(.+?)\s+-\s+(.+)/', $line, $matches))
				{
					if ($token !== 'dl')
					{
						$about .= $this->comment_block($token, $block);
						$block  = '';
					}

					$token = 'dl';
					$line = '<dt>'.$matches[1].'</dt>'."\n".'<dd>'.$matches[2].'</dd>'."\n";
				}
				else
				{
					$token = 'p';
					$line .= ' ';
				}

				if (trim($line) === '')
				{
					$about .= $this->comment_block($token, $block);
					$block = '';
				}
				else
				{
					$block .= $line;
				}
			}

			if ( ! empty($block))
			{
				$about .= $this->comment_block($token, $block);
			}

			$comment['about'] = $about;
		}

		return $comment;
	}

	protected function comment_block($token, $block)
	{
		if (empty($token) OR empty($block))
			return '';

		$block = trim($block);

		if (substr($block, 0, 1) === '<')
		{
			// Insert newlines before and after the block
			$block = "\n".$block."\n";
		}

		return '<'.$token.'>'.$block.'</'.$token.'>'."\n";
	}

	protected function parse_class($class)
	{
		// Use reflection to find information
		$reflection = new ReflectionClass($class);

		// Class definition
		$class = array
		(
			'name'       => $reflection->getName(),
			'comment'    => $this->parse_comment($reflection->getDocComment()),
			'final'      => $reflection->isFinal(),
			'abstract'   => $reflection->isAbstract(),
			'interface'  => $reflection->isInterface(),
			'extends'    => '',
			'implements' => array(),
			'methods'    => array()
		);

		if ($implements = $reflection->getInterfaces())
		{
			foreach ($implements as $interface)
			{
				// Get implemented interfaces
				$class['implements'][] = $interface->getName();
			}
		}

		if ($parent = $reflection->getParentClass())
		{
			// Get parent class
			$class['extends'] = $parent->getName();
		}

		if ($methods = $reflection->getMethods())
		{
			foreach ($methods as $method)
			{
				// Don't try to document internal methods
				if ($method->isInternal()) continue;

				$class['methods'][] = array
				(
					'name'       => $method->getName(),
					'comment'    => $this->parse_comment($method->getDocComment()),
					'class'      => $class['name'],
					'final'      => $method->isFinal(),
					'static'     => $method->isStatic(),
					'abstract'   => $method->isAbstract(),
					'visibility' => $this->visibility($method),
					'parameters' => $this->parameters($method)
				);
			}
		}

		return $class;
	}

	/**
	 * Finds the parameters for a ReflectionMethod.
	 *
	 * @param   object   ReflectionMethod
	 * @return  array
	 */
	protected function parameters(ReflectionMethod $method)
	{
		$params = array();

		if ($parameters = $method->getParameters())
		{
			foreach ($parameters as $param)
			{
				// Parameter data
				$data = array
				(
					'name' => $param->getName()
				);

				if ($param->isOptional())
				{
					// Set default value
					$data['default'] = $param->getDefaultValue();
				}

				$params[] = $data;
			}
		}

		return $params;
	}

	/**
	 * Finds the visibility of a ReflectionMethod.
	 *
	 * @param   object   ReflectionMethod
	 * @return  string
	 */
	protected function visibility(ReflectionMethod $method)
	{
		$vis = array_flip(Reflection::getModifierNames($method->getModifiers()));

		if (isset($vis['public']))
		{
			return 'public';
		}

		if (isset($vis['protected']))
		{
			return 'protected';
		}

		if (isset($vis['private']))
		{
			return 'private';
		}

		return FALSE;
	}

} // End Kodoc
class Kodoc_xCore {

	/**
	 * libraries, helpers, etc
	 */
	protected $files = array
	(
		'core'      => array(),
		'config'    => array(),
		'helpers'   => array(),
		'libraries' => array(),
		'models'    => array(),
		'views'     => array()
	);

	/**
	 * $classes[$name] = array $properties;
	 * $properties = array
	 * (
	 *     'drivers'    => array $drivers
	 *     'properties' => array $properties
	 *     'methods'    => array $methods
	 * )
	 */
	protected $classes = array();

	// Holds the current data until parsed
	protected $current_class;

	// $packages[$name] = array $files;
	protected $packages = array();

	// PHP's visibility types
	protected static $php_visibility = array
	(
		'public',
		'protected',
		'private'
	);

	public function __construct()
	{
		if (isset(self::$php_visibility[0]))
		{
			self::$php_visibility = array_flip(self::$php_visibility);
		}

		foreach ($this->files as $type => $files)
		{
			foreach (Kohana::list_files($type) as $filepath)
			{
				// Get the filename with no extension
				$file = pathinfo($filepath, PATHINFO_FILENAME);

				// Skip indexes and drivers
				if ($file === 'index' OR strpos($filepath, 'libraries/drivers') !== FALSE)
					continue;

				// Add the file
				$this->files[$type][$file] = $filepath;

				// Parse the file
				$this->parse_file($filepath);
			}
		}

		Kohana::log('debug', 'Kodoc Library initialized');
	}

	public function get_docs($format = 'html')
	{
		switch ($format)
		{
			default:
				// Generate HTML via a View
				$docs = new View('kodoc_html');

				$docs->set('classes', $this->classes)->render();
			break;
		}

		return $docs;
	}

	protected function parse_file($file)
	{
		$file = fopen($file, 'r');

		$i = 1;
		while ($line = fgets($file))
		{
			if (substr(trim($line), 0, 2) === '/*')
			{
				// Reset vars
				unset($current_doc, $section, $p);

				// Prepare for a new doc section
				$current_doc = array();
				$closing_tag = '*/';

				$current_block = 'description';
				$p = 0;

				// Assign the current doc
				$this->current_doc =& $current_doc;
			}
			elseif (isset($closing_tag))
			{
				if (substr(trim($line), 0, 1) === '*')
				{
					// Remove the leading comment
					$line = substr(ltrim($line), 2);

					if (preg_match('/^([a-z ]+):/i', $line, $matches))
					{
						$current_block = trim($matches[1]);
					}
					elseif (isset($current_doc))
					{
						$line = ltrim($line);

						if (preg_match('/^\-\s+(.+)/', $line, $matches))
						{
							// An unordered list
							$current_doc['html'][$current_block]['ul'][] = $matches[1];
						}
						elseif (preg_match('/^[0-9]+\.\s+(.+)/', $line, $matches))
						{
							// An ordered list
							$current_doc['html'][$current_block]['ol'][] = $matches[1];
						}
						elseif (preg_match('/^([a-zA-Z ]+)\s+\-\s+(.+)/', $line, $matches))
						{
							// Definition list
							$current_doc['html'][$current_block]['dl'][trim($matches[1])] = trim($matches[2]);
						}
						else
						{
							if (trim($line) === '')
							{
								// Start a new paragraph
								$p++;
							}
							else
							{
								// Make sure the current paragraph is set
								if ( ! isset($current_doc['html'][$current_block]['p'][$p]))
								{
									$current_doc['html'][$current_block]['p'][$p] = '';
								}

								// Add to the current paragraph
								$current_doc['html'][$current_block]['p'][$p] .= str_replace("\n", ' ', $line);
							}
						}
					}
				}
				else
				{
					switch (substr(trim($line), 0, 2))
					{
						case '//':
						case '* ': break;
						default:
							$line = trim($line);

							if ($this->is_function($line) OR $this->is_property($line) OR $this->is_class($line))
							{
								$clear = NULL;
								$this->current_doc =& $clear;

								// Restarts searching
								unset($closing_tag, $current_doc);
							}
						break;
					}
				}
			}

			$i++;
		}

		// Close the file
		fclose($file);
	}

	/**
	 * Method:
	 *  Checks if a line is a class, and parses the data out.
	 *
	 * Parameters:
	 *  line - a line from a file
	 *
	 * Returns:
	 *  TRUE or FALSE.
	 */
	protected function is_class($line)
	{
		if (strpos($line, 'class') === FALSE)
		{
			return FALSE;
		}

		$line = explode(' ', trim($line));

		$class = array
		(
			'name'    => '',
			'final'   => FALSE,
			'extends' => FALSE,
			'drivers' => FALSE
		);

		if (current($line) === 'final')
		{
			$class['final'] = (bool) array_shift($line);
		}

		if (current($line) === 'class')
		{
			// Remove "class"
			array_shift($line);

			$name = array_shift($line);
		}

		if (count($line) > 1)
		{
			// Remove "extends"
			array_shift($line);

			$class['extends'] = array_shift($line);
		}

		if (isset($name))
		{
			// Add the class into the docs
			$this->classes[$name] = array_merge($this->current_doc, $class);

			// Set the current class
			$this->current_class =& $this->classes[$name];

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Method:
	 *  Checks if a line is a property, and parses the data out.
	 *
	 * Parameters:
	 *  line - a line from a file
	 *
	 * Returns:
	 *  TRUE or FALSE.
	 */
	protected function is_property($line)
	{
		static $preg_vis;

		if ($preg_vis === NULL)
		{
			$preg_vis = 'var|'.implode('|', self::$php_visibility);
		}

		if (strpos($line, '$') === FALSE OR ! preg_match('/^(?:'.$preg_vis.')/', $line))
			return FALSE;

		$line = explode(' ', $line);

		$var = array
		(
			'visibility' => FALSE,
			'static'     => FALSE,
			'default'    => NULL
		);

		if (current($line) === 'var')
		{
			// Remove "var"
			array_shift($line);

			$var['visibility'] = 'public';
		}

		if (current($line) === 'static')
		{
			$var['visibility'] = (bool) array_shift($line);
		}

		// If the visibility is not set, this is not a
		if ($var['visibility'] === FALSE)
			return FALSE;

		if (substr(current($line), 0, 1) === '$')
		{
			$name = substr(array_shift($line), 1);
			$name = rtrim($name, ';');
		}

		if (count($line) AND current($line) === '=')
		{
			array_shift($line);

			$var['default'] = implode(' ', $line);
		}

		if (isset($name))
		{
			// Add property to class
			$this->current_class['properties'][$name] = array_merge($this->current_doc, $var);

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Method:
	 *  Checks if a line is a function, and parses the data out.
	 *
	 * Parameters:
	 *  line - a line from a file
	 *
	 * Returns:
	 *  TRUE or FALSE.
	 */
	protected function is_function($line)
	{
		if (strpos($line, 'function') === FALSE)
		{
			return FALSE;
		}

		$line = explode(' ', trim(strtolower($line)));

		$func = array
		(
			'final'      => FALSE,
			'visibility' => 'public',
			'static'     => FALSE,
		);

		if (current($line) === 'final')
		{
			$func['final'] = TRUE;
		}

		if (isset(self::$php_visibility[current($line)]))
		{
			$func['visibility'] = array_shift($line);
		}

		if (current($line) === 'static')
		{
			$func['static'] = (bool) array_shift($line);
		}

		if (current($line) === 'function')
		{
			// Remove "function"
			array_shift($line);

			// Get name
			$name = array_shift($line);

			// Remove arguments
			if (strpos($name, '(') !== FALSE)
			{
				$name = current(explode('(', $name, 2));
			}

			// Register the method
			$this->current_class['methods'][$name] = array_merge($this->current_doc, $func);

			return TRUE;
		}

		return FALSE;
	}

} // End Kodoc