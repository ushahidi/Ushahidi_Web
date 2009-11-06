<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Themes Controller
 * Select site theme
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Themes Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Themes_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'settings';
	}
	
	
	function index()
	{
		$this->template->content = new View('admin/themes');
		$this->template->content->title = 'Settings';

		// setup and initialize form field names
		$form = array
	    (
			'site_style' => ''
	    );
        //  Copy the form as errors, so the errors will be stored with keys
        //  corresponding to the form field names
        $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
            // Instantiate Validation, use $post, so we don't overwrite $_POST
            // fields with our own things
            $post = new Validation($_POST);

	        // Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order

			$post->add_rules('site_style', 'length[1,50]');
			
			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {
	            // Yes! everything is valid
				$settings = new Settings_Model(1);
				$settings->site_style = $post->site_style;
				$settings->save();
				
				//add details to application/config/email.php
				//$this->_add_email_settings($settings);
				
				// Delete Settings Cache
				$cache = Cache::instance();
				$cache->delete('settings');
				$cache->delete_tag('settings');
				
				
				// Everything is A-Okay!
				$form_saved = TRUE;

				// repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	        }

            // No! We have validation errors, we need to show the form again,
            // with the errors
            else
	        {
	            // repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());
				
	            // populate the error fields, if any
	            $errors = arr::overwrite($errors, $post->errors('settings'));
				$form_error = TRUE;
	        }
	    }
		else
		{
			// Retrieve Current Settings
			$settings = ORM::factory('settings', 1);

			$form = array
		    (
		        'site_style' => $settings->site_style
		    );
		}
		
		$this->template->content->form = $form;
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->themes = $this->_get_themes();
	}
	
	
	/**
	 * Retrieve list of themes with theme data in theme directory
	 */
	private function _get_themes()
	{
		$themes = array();
		$theme_files = array();
		
		$themes_path = THEMEPATH;
		
		// Files ushahidi/themes directory and one subdir down
		$themes_dir = @ opendir($themes_path);
		if ( !$themes_dir )
			return false;
			
		while ( ($theme_dir = readdir($themes_dir)) !== false )
		{			
			if ( is_dir($themes_path.$theme_dir) && is_readable($themes_path.$theme_dir) )
			{				
				// Strip out .  and .. and any other stuff
				if ( $theme_dir{0} == '.' || $theme_dir == '..'
				 	|| $theme_dir ==  '.DS_Store' || $theme_dir == '.git')
					continue;
				
				$stylish_dir = @ opendir($themes_path.$theme_dir);
				$found_stylesheet = false;
				while ( ($theme_file = readdir($stylish_dir)) !== false )
				{
					if ( $theme_file == 'style.css' )
					{
						$theme_files[] = $theme_dir . '/' . $theme_file;
						$found_stylesheet = true;
						break;
					}
				}
				
				@closedir($stylish_dir);
			}
		}
		
		if ( is_dir( $theme_dir ) )
			@closedir( $theme_dir );

		if ( !$themes_dir || !$theme_files || !$found_stylesheet )
			return $themes;
		
		sort($theme_files);
		
		// Now we're going to go through each theme file and get data
		foreach ( (array) $theme_files as $theme_file )
		{
			if ( !is_readable($themes_path.$theme_file) )
			{
				// Stylesheet can't be read... error?
				continue;
			}

			$theme_data = $this->_get_theme_data($themes_path.$theme_file);

			$name        = $theme_data['Name'];
			$title       = $theme_data['Title'];
			$description = $theme_data['Description'];
			$demo  		 = $theme_data['Demo'];
			$version     = $theme_data['Version'];
			$author      = $theme_data['Author'];
			$author_email= $theme_data['Author_Email'];
			$stylesheet  = dirname($theme_file);

			$screenshot = false;
			foreach ( array('png', 'gif', 'jpg', 'jpeg') as $ext )
			{
				if (file_exists($themes_path.$stylesheet."/screenshot.$ext"))
				{
					$screenshot = "screenshot.$ext";
					break;
				}
			}

			if ( empty($name) )
			{
				$name = dirname($theme_file);
			}
			
			// Check for theme name collision.
			if ( isset($themes[$name]) )
			{
				$name = "$name/$stylesheet";
			}

			$themes[$name] = array('Name' => $name, 'Title' => $title, 'Description' => $description, 'Demo' => $demo, 'Version' => $version, 'Author' => $author, 'Author_Email' => $author_email, 'Screenshot' => $screenshot, 'Template_Dir' => $stylesheet);
		}
		
		return $themes;		
	}
	
	/**
	 * Parse the styles.css file for Meta-Data
	 */
	private function _get_theme_data( $theme_file )
	{		
		$theme_data = implode( '', file( $theme_file ) );
		$theme_data = str_replace ( '\r', '\n', $theme_data );
		if ( preg_match( '|Theme Name:(.*)$|mi', $theme_data, $theme_name ) )
			$name = $theme = trim(text::html2txt($theme_name[1]));
		else
			$name = $theme = '';

		if ( preg_match( '|Description:(.*)$|mi', $theme_data, $description ) )
			$description = trim(text::html2txt($description[1]));
		else
			$description = '';

		if ( preg_match( '|Demo:(.*)$|mi', $theme_data, $demo_url ) )
			$demo_url = trim(text::html2txt($demo_url[1]));
		else
			$demo_url = '';

		if ( preg_match( '|Version:(.*)|i', $theme_data, $version ) )
			$version = trim(text::html2txt($version[1]));
		else
			$version = '';
			
		if ( preg_match( '|Author:(.*)|i', $theme_data, $author ) )
			$author = trim(text::html2txt($author[1]));
		else
			$author = 'Anonymous';
			
		if ( preg_match( '|Author Email:(.*)|i', $theme_data, $author_email ) )
			$author_email = trim(text::html2txt($author_email[1]));
		else
			$author_email = '';

		return array( 'Name' => $name, 'Title' => $theme, 'Description' => $description, 'Demo' => $demo_url, 'Version' => $version, 'Author' => $author, 'Author_Email' => $author_email );
	}
}