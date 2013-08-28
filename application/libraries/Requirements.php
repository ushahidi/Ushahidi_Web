<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Requirements tracker, for javascript and css.
 * 
 * Based on Requirements class from sapphire framework (but heavily modified)
 * https://github.com/silverstripe/sapphire/blob/master/view/Requirements.php
 * 
 * LICENSE: This source file is subject to BSD license
 *
 * Copyright (c) 2007-2011, SilverStripe Limited - www.silverstripe.com
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 * * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the 
 *   documentation and/or other materials provided with the distribution.
 * * Neither the name of SilverStripe nor the names of its contributors may be used to endorse or promote products derived from this software 
 *   without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE 
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY 
 * OF SUCH DAMAGE.
 * 
 * @package	   Ushahidi - https://github.com/ushahidi/Ushahidi_Web
 * @license	   https://github.com/silverstripe/sapphire Modified BSD License
 */
class Requirements {
	
	/**
	 * Enable combining of css/javascript files.
	 * @param boolean $enable
	 */
	public static function set_combined_files_enabled($enable) {
		self::backend()->set_combined_files_enabled($enable);
	}

	/**
	 * Checks whether combining of css/javascript files is enabled.
	 * @return boolean
	 */
	public static function get_combined_files_enabled() {
	  return self::backend()->get_combined_files_enabled();
	}

	/**
	 * Set the relative folder e.g. "assets" for where to store combined files
	 * @param string $folder Path to folder
	 */
	public static function set_combined_files_folder($folder) {
		self::backend()->setCombinedFilesFolder($folder);
	}

	/**
	 * Set whether we want to suffix requirements with the time / 
	 * location on to the requirements
	 * 
	 * @param bool
	 */
	public static function set_suffix_requirements($var) {
		self::backend()->set_suffix_requirements($var);
	}
	
	/**
	 * Return whether we want to suffix requirements
	 * 
	 * @return bool
	 */
	public static function get_suffix_requirements() {
		return self::backend()->get_suffix_requirements();
	}
	
	/**
	 * Instance of requirements for storage
	 *
	 * @var Requirements
	 */
	private static $backend = null;
	
	public static function backend() {
		if(!self::$backend) {
			self::$backend = new Requirements_Backend();
		}
		return self::$backend;
	}
	
	/**
	 * Setter method for changing the Requirements backend
	 *
	 * @param Requirements $backend
	 */
	public static function set_backend(Requirements_Backend $backend) {
		self::$backend = $backend;
	}
	
	/**
	 * Register the given javascript file as required.
	 * 
	 * See {@link Requirements_Backend::javascript()} for more info
	 * 
	 */
	static function js($file, $uniquenessID = null) {
		self::backend()->js($file, $uniquenessID);
	}
	
	/**
	 * Add the javascript code to the header of the page
	 * 
	 * See {@link Requirements_Backend::customJS()} for more info
	 * @param script The script content
	 * @param uniquenessID Use this to ensure that pieces of code only get added once.
	 */
	static function customJS($script, $uniquenessID) {
		self::backend()->customJS($script, $uniquenessID);
	}

	/**
	 * Include custom CSS styling to the header of the page.
	 * 
	 * See {@link Requirements_Backend::customCSS()}
	 * 
	 * @param string $script CSS selectors as a string (without <style> tag enclosing selectors).
	 * @param int $uniquenessID Group CSS by a unique ID as to avoid duplicate custom CSS in header
	 */
	static function customCSS($script, $uniquenessID, $media = null) {
		self::backend()->customCSS($script, $uniquenessID);
	}
	
	/**
	 * Add the following custom code to the <head> section of the page.
	 * See {@link Requirements_Backend::customHeadTags()}
	 * 
	 * @param string $html
	 * @param string $uniquenessID
	 */
	static function customHeadTags($html, $uniquenessID) {
		self::backend()->customHeadTags($html, $uniquenessID);
	}
	
	/**
	 * Register the given stylesheet file as required.
	 * See {@link Requirements_Backend::css()}
	 * 
	 * @param $file String Filenames should be relative to the base, eg, 'framework/javascript/tree/tree.css'
	 * @param $media String Comma-separated list of media-types (e.g. "screen,projector") 
	 * @see http://www.w3.org/TR/REC-CSS2/media.html
	 */
	static function css($file, $uniquenessID = null, $media = null) {
		self::backend()->css($file, $uniquenessID, $media);
	}
	
	/**
	 * Registers the given themeable stylesheet as required.
	 *
	 * A CSS file in the current theme path name "themename/css/$name.css" is
	 * first searched for, and it that doesn't exist and the module parameter is
	 * set then a CSS file with that name in the module is used.
	 * If neither theme nor module css exists, then a file from media/css will
	 * be used.
	 *
	 * @param string $name The name of the file - e.g. "/css/File.css" would have
	 *        the name "File".
	 * @param string $module The module to fall back to if the css file does not
	 *        exist in the current theme.
	 * @param string $media The CSS media attribute.
	 */
	public static function themedCSS($name, $module = null, $media = null) {
		return self::backend()->themedCSS($name, $module, $media);
	}
	
	/**
	 * Registers the given stylesheet in an IE conditional comment
	 *
	 * @param string $version The conditional IE version string e.g. "lt IE 7"
	 * @param string $name The name of the file - e.g. "/css/File.css" would have
	 *        the name "File".
	 * @param string $media The CSS media attribute.
	 */
	public static function ieCSS($version, $name, $media = null) {
		return self::backend()->ieCSS($version, $name, $media);
	}
	
	/**
	 * Registers an IE themeable stylesheet 
	 *
	 * @param string $version The conditional IE version string e.g. "lt IE 7"
	 * @param string $name The name of the file - e.g. "/css/File.css" would have
	 *        the name "File".
	 * @param string $module The module to fall back to if the css file does not
	 *        exist in the current theme.
	 * @param string $media The CSS media attribute.
	 */
	public static function ieThemedCSS($version, $name, $module = null, $media = null) {
		return self::backend()->ieThemedCSS($version, $name, $module, $media);
	}

	/**
	 * Clear either a single or all requirements.
	 * Caution: Clearing single rules works only with customCSS and customJS if you specified a {@uniquenessID}. 
	 * 
	 * See {@link Requirements_Backend::clear()}
	 * 
	 * @param $file String
	 */
	static function clear($fileOrID = null) {
		self::backend()->clear($fileOrID);
	}

	/**
	 * Blocks inclusion of a specific file
	 * See {@link Requirements_Backend::block()}
	 *
	 * @param unknown_type $fileOrID
	 */
	static function block($fileOrID) {
		self::backend()->block($fileOrID);
	}

	/**
	 * Removes an item from the blocking-list.
	 * See {@link Requirements_Backend::unblock()}
	 * 
	 * @param string $fileOrID
	 */
	static function unblock($fileOrID) {
		self::backend()->unblock($fileOrID);
	}

	/**
	 * Removes all items from the blocking-list.
	 * See {@link Requirements_Backend::unblock_all()}
	 */
	static function unblock_all() {
		self::backend()->unblock_all();
	}
	
	/**
	 * Restore requirements cleared by call to Requirements::clear
	 * See {@link Requirements_Backend::restore()}
	 */
	static function restore() {
		self::backend()->restore();
	}
	
	/**
	 * Render the appropriate include tags for the registered requirements. 
	 * See {@link Requirements_Backend::render()} for more information.
	 * 
	 * @param string $type which requirements to render? accepts values 'all' 'css' 'js' or 'headtag'
	 * @return string HTML include tags for inclusion in template
	 */
	static function render($type = 'all') {
		return self::backend()->render($type);
	}
	
	/**
	 * Concatenate several css or javascript files into a single dynamically generated file.
	 * See {@link Requirements_Backend::combine_files()} for more info.
	 *
	 * @param string $combinedFileName
	 * @param array $files
	 */
	static function combine_files($combinedFileName, $files) {
		self::backend()->combine_files($combinedFileName, $files);
	}
	
	/**
	 * Returns all combined files.
	 * See {@link Requirements_Backend::get_combine_files()}
	 * 
	 * @return array
	 */
	static function get_combine_files() {
		return self::backend()->get_combine_files();
	}
	
	/**
	 * Deletes all dynamically generated combined files from the filesystem. 
	 * See {@link Requirements_Backend::delete_combine_files()}
	 * 
	 * @param string $combinedFileName If left blank, all combined files are deleted.
	 */
	static function delete_combined_files($combinedFileName = null) {
		return self::backend()->delete_combined_files($combinedFileName);
	}
	

	/**
	 * Re-sets the combined files definition. See {@link Requirements_Backend::clear_combined_files()}
	 */
	static function clear_combined_files() {
		self::backend()->clear_combined_files();
	}
		
	/**
	 * See {@link combine_files()}.
 	 */
	static function process_combined_files() {
		return self::backend()->process_combined_files();
	}

	/**
	 * Returns all custom scripts
	 * See {@link Requirements_Backend::get_custom_scripts()}
	 *
	 * @return array
	 */
	static function get_custom_scripts() {
		return self::backend()->get_custom_scripts();
	}
	
	static function debug() {
		return self::backend()->debug();
	}

}

/**
 * @package framework
 * @subpackage view
 */
class Requirements_Backend {

	/**
	 * Do we want requirements to suffix onto the requirement link
	 * tags for caching or is it disabled. Getter / Setter available
	 * through {@link Requirements::set_suffix_requirements()}
	 *
	 * @var bool
	 */
	protected $suffix_requirements = true;

	/**
	 * Enable combining of css/javascript files.
	 *
	 * @var boolean
	 */
	protected $combined_files_enabled = true;

	/**
	 * Paths to all required .js files relative to the webroot.
	 *
	 * @var array $js
	 */
	protected $js = array();

	/**
	 * Paths to all required .css files relative to the webroot.
	 *
	 * @var array $css
	 */
	protected $css = array();

	/**
	 * All custom javascript code that is inserted
	 * directly at the bottom of the HTML <head> tag.
	 *
	 * @var array $customJS
	 */
	protected $customJS = array();

	/**
	 * All custom CSS rules which are inserted
	 * directly at the bottom of the HTML <head> tag.
	 *
	 * @var array $customCSS
	 */
	protected $customCSS = array();

	/**
	 * All custom HTML markup which is added before
	 * the closing <head> tag, e.g. additional metatags.
	 * This is preferred to entering tags directly into
	 */
	protected $customHeadTags = array();

	/**
	 * Remembers the filepaths of all cleared Requirements
	 * through {@link clear()}.
	 *
	 * @var array $disabled
	 */
	protected $disabled = array();

	/**
	 * The filepaths (relative to webroot) or
	 * uniquenessIDs of any included requirements
	 * which should be blocked when executing {@link inlcudeInHTML()}.
	 * This is useful to e.g. prevent core classes to modifying
	 * Requirements without subclassing the entire functionality.
	 * Use {@link unblock()} or {@link unblock_all()} to revert changes.
	 *
	 * @var array $blocked
	 */
	protected $blocked = array();

	/**
	 * See {@link combine_files()}.
	 *
	 * @var array $combine_files
	 */
	public $combine_files = array('js' => array(), 'css' => array());

	/**
	 * Using the JSMin library to minify any
	 * javascript file passed to {@link combine_files()}.
	 *
	 * @var boolean
	 */
	public $combine_js_with_jsmin = true;
	

	/**
	 * Using the CSSMin library to minify any
	 * css file passed to {@link combine_files()}.
	 *
	 * @var boolean
	 */
	public $combine_css_with_cssmin = true;

	/**
	 * @var string By default, combined files are stored in assets/_combinedfiles.
	 * Set this by calling Requirements::set_combined_files_folder()
	 */
	protected $combinedFilesFolder = null;

	/**
	 * Put all javascript includes at the bottom of the template
	 * before the closing <body> tag instead of the <head> tag.
	 * This means script downloads won't block other HTTP-requests,
	 * which can be a performance improvement.
	 * Caution: Doesn't work when modifying the DOM from those external
	 * scripts without listening to window.onload/document.ready
	 * (e.g. toplevel document.write() calls).
	 *
	 * @see http://developer.yahoo.com/performance/rules.html#js_bottom
	 *
	 * @var boolean
	 */
	public $write_js_to_body = true;

	public function __construct()
	{
		// Set up defaults from config file
		$this->combine_js_with_jsmin = Kohana::config('requirements.combine_js_with_jsmin');
		$this->combine_css_with_cssmin = Kohana::config('requirements.combine_css_with_cssmin');
		$this->set_suffix_requirements(Kohana::config('requirements.suffix_requirements'));
		$this->set_combined_files_enabled(Kohana::config('requirements.combined_files_enabled'));
	}

	function set_combined_files_enabled($enable) {
		$this->combined_files_enabled = (bool) $enable;
	}
	
	function get_combined_files_enabled() {
		return $this->combined_files_enabled;
	}

	/**
	 * @param String $folder
	 */
	function setCombinedFilesFolder($folder) {
		$this->combinedFilesFolder = $folder;
	}
	
	/**
	 * @return String Folder relative to the webroot
	 */
	function getCombinedFilesFolder() {
		$default = Kohana::config('upload.relative_directory', FALSE);
		
		if (Kohana::config("cdn.cdn_store_dynamic_content") AND Kohana::config("requirements.cdn_store_combined_files"))
		{
			$subdomain_dir = '';
			if (Kohana::config('settings.subdomain') != '') {
				// Make sure there's a slash on the end
				$subdomain_dir = rtrim(Kohana::config('settings.subdomain'), '/').'/';
			}

			$default = $subdomain_dir . Kohana::config('upload.relative_directory', FALSE);
		}
		
		return ($this->combinedFilesFolder) ? $this->combinedFilesFolder : $default;
	}
	
	/**
	 * Set whether we want to suffix requirements with the time / 
	 * location on to the requirements
	 * 
	 * @param bool
	 */
	function set_suffix_requirements($var) {
		$this->suffix_requirements = $var;
	}
	
	/**
	 * Return whether we want to suffix requirements
	 * 
	 * @return bool
	 */
	function get_suffix_requirements() {
		return $this->suffix_requirements;
	}
	
	/**
	 * Register the given javascript file as required.
	 * Filenames should be relative to the base, eg, 'framework/javascript/loader.js'
	 * @param $file String|Array Filenames should be relative to the base, eg, 'media/js/style.js'
	 * @param string $uniquenessID
	 */
	
	public function js($file, $uniquenessID = null) {
		// If array, loop over array and add individual js files
		if (is_array($file))
		{
			foreach($file as $name)
			{
				$this->js($name);
			}
			return;
		}
		
		if (! $uniquenessID)
		{
			$uniquenessID = substr( $file, strrpos( $file, '/' ) +1 );
		}
		
		$this->js[$uniquenessID] = $file;
	}
	
	/**
	 * Returns an array of all included javascript
	 *
	 * @return array
	 */
	public function get_js() {
		$js = array_diff_key($this->js,$this->blocked); // removed blocked js by id
		$js = array_diff($js, $this->blocked); // remove blocked js by filename
		return $js;
	}
	
	/**
	 * Add the javascript code to the header of the page
	 * @todo Make Requirements automatically put this into a separate file :-)
	 * @param script The script content
	 * @param uniquenessID Use this to ensure that pieces of code only get added once.
	 */
	public function customJS($script, $uniquenessID) {
		$script .= "\n";
		$this->customJS[$uniquenessID] = $script;
	}
	
	/**
	 * Include custom CSS styling to the header of the page.
	 *
	 * @param string $script CSS selectors as a string (without <style> tag enclosing selectors).
	 * @param int $uniquenessID Group CSS by a unique ID as to avoid duplicate custom CSS in header
	 */
	function customCSS($script, $uniquenessID) {
		$this->customCSS[$uniquenessID] = $script;
	}
	
	/**
	 * Add the following custom code to the <head> section of the page.
	 *
	 * @param string $html
	 * @param string $uniquenessID
	 */
	function customHeadTags($html, $uniquenessID) {
		$this->customHeadTags[$uniquenessID] = $html;
	}

	
	/**
	 * Register the given stylesheet file as required.
	 * 
	 * @param $file String|Array Filenames should be relative to the base, eg, 'meida/css/tree.css'
	 * @param string $uniquenessID
	 * @param $media String Comma-separated list of media-types (e.g. "screen,projector") 
	 * @see http://www.w3.org/TR/REC-CSS2/media.html
	 */
	function css($file, $uniquenessID = null, $media = null) {
		// If array, loop over array and add individual js files
		if (is_array($file))
		{
			foreach($file as $name)
			{
				$this->css($name);
			}
			return;
		}
		
		if (! $uniquenessID)
		{
			$uniquenessID = substr( $file, strrpos( $file, '/' ) +1 );
		}
		
		$this->css[$uniquenessID] = array(
			"file" => $file,
			"media" => $media
		);
	}
	
	function get_css() {
		$css = array_diff_key($this->css,$this->blocked); // removed blocked css by id
		// Remove blocked css by filename
		foreach($css as $id => $params)
		{
			if (in_array($params['file'], $this->blocked)) unset($css[$id]);
		}
		
		return $css;
	}
	
	/**
	 * Needed to actively prevent the inclusion of a file,
	 * e.g. when using your own prototype.js.
	 * Blocking should only be used as an exception, because
	 * it is hard to trace back. You can just block items with an
	 * ID, so make sure you add an unique identifier to customCSS() and customJS().
	 * 
	 * @param string $fileOrID
	 */
	function block($fileOrID) {
		$this->blocked[$fileOrID] = $fileOrID;
	}
	
	/**
	 * Clear either a single or all requirements.
	 * Caution: Clearing single rules works only with customCSS and customJS if you specified a {@uniquenessID}. 
	 * 
	 * @param $file String
	 */
	function clear($fileOrID = null) {
		if($fileOrID) {
			foreach(array('js','css', 'customJS', 'customCSS', 'customHeadTags') as $type) {
				if(isset($this->{$type}[$fileOrID])) {
					$this->disabled[$type][$fileOrID] = $this->{$type}[$fileOrID];
					unset($this->{$type}[$fileOrID]);
				}
			}
		} else {
			$this->disabled['js'] = $this->js;
			$this->disabled['css'] = $this->css;
			$this->disabled['customJS'] = $this->customJS;
			$this->disabled['customCSS'] = $this->customCSS;
			$this->disabled['customHeadTags'] = $this->customHeadTags;
		
			$this->js = array();
			$this->css = array();
			$this->customJS = array();
			$this->customCSS = array();
			$this->customHeadTags = array();
		}
	}
	
	/**
	 * Removes an item from the blocking-list.
	 * CAUTION: Does not "re-add" any previously blocked elements.
	 * @param string $fileOrID
	 */
	function unblock($fileOrID) {
		if(isset($this->blocked[$fileOrID])) unset($this->blocked[$fileOrID]);
	}
	/**
	 * Removes all items from the blocking-list.
	 */
	function unblock_all() {
		$this->blocked = array();
	}
	
	/**
	 * Restore requirements cleared by call to Requirements::clear
	 */
	function restore() {
		$this->js = $this->disabled['js'];
		$this->css = $this->disabled['css'];
		$this->customJS = $this->disabled['customJS'];
		$this->customCSS = $this->disabled['customCSS'];
		$this->customHeadTags = $this->disabled['customHeadTags'];
	}
	
	/**
	 * Generate the appropriate include tags for the registered requirements. 
	 * 
	 * @param string $type which type of requirement to render? accepts values 'all' 'css' 'js' or 'headtags'
	 * @return string HTML include tags for inclusion in template
	 */
	function render($type = 'all') {
		$content = '';
		
		switch ($type)
		{
			case 'css':
				$content .= $this->renderCSS();
				break;
			case 'js':
				$content .= $this->renderJS();
				break;
			case 'headtags':
				$content .= $this->renderHeadTags();
				break;
			case 'all':
			default:
				$content .= $this->renderCSS();
				$content .= $this->renderHeadTags();
				$content .= $this->renderJS();
				break;
		}
		
		return $content;
	}

	/**
	 * Generate the appropriate include tags for the registered JS requirements. 
	 * 
	 * @return string HTML include tags for inclusion in template
	 */
	private function renderJS()
	{
		$content = '';
		if($this->js || $this->customJS) {
			// Combine files - updates $this->js
			$this->process_combined_files('js');
		
			foreach($this->get_js() as $id => $file) {
				$path = $this->path_for_file($file, 'js');
				if($path) {
					//$jsRequirements .= html::script($path, TRUE);
					$content .= "<script type=\"text/javascript\" src=\"$path\"></script>\n";
				}
			}
			
			// add all inline javascript *after* including external files which
			// they might rely on
			if($this->customJS) {
				foreach(array_diff_key($this->customJS,$this->blocked) as $script) { 
					$content .= "<script type=\"text/javascript\">\n//<![CDATA[\n";
					$content .= "$script\n";
					$content .= "\n//]]>\n</script>\n";
				}
			}
		}
		
		return $content;
	}

	
	/**
	 * Generate the appropriate include tags for the registered Head Tag requirements. 
	 * 
	 * @return string HTML include tags for inclusion in template
	 */
	private function renderHeadTags()
	{
		$content = '';
		if($this->customHeadTags) {
			foreach(array_diff_key($this->customHeadTags,$this->blocked) as $customHeadTag) { 
				$content .= "$customHeadTag\n"; 
			}
		}
		
		return $content;
	}

	
	/**
	 * Generate the appropriate include tags for the registered CSS requirements. 
	 * 
	 * @return string HTML include tags for inclusion in template
	 */
	private function renderCSS()
	{
		
		$content = '';
		if($this->css || $this->customCSS) {
			// Combine files - updates $this->css 
			$this->process_combined_files('css');
			
			foreach($this->get_css() as $id => $params) {
				$file = $params['file'];
				$path = $this->path_for_file($file, 'css');
				if($path) {
					//$media = (isset($params['media']) && !empty($params['media'])) ? $params['media'] : "";
					//$requirements .= html::stylesheet($path, $media, TRUE);
					$media = (isset($params['media']) && !empty($params['media'])) ? " media=\"{$params['media']}\"" : "";
					$content .= "<link rel=\"stylesheet\" type=\"text/css\"{$media} href=\"$path\" />\n";
					
				}
			}
			
			foreach(array_diff_key($this->customCSS, $this->blocked) as $css) { 
				$content .= "<style type=\"text/css\">\n$css\n</style>\n";
			}
		}
		
		return $content;
	}
	
	/**
	 * Finds the path for specified file.
	 *
	 * @param string $fileOrUrl
	 * @param string $type file type ie. css or js
	 * @return string|boolean 
	 */
	protected function path_for_file($fileOrUrl, $type) {
		if(preg_match('/^http[s]?/', $fileOrUrl)) {
			return $fileOrUrl;
		} else {
			// Split query from filename
			$suffix = '';
			if(strpos($fileOrUrl, '?') !== false) {
				$suffix = substr($fileOrUrl, strpos($fileOrUrl, '?')+1);
				$fileOrUrl = substr($fileOrUrl, 0, strpos($fileOrUrl, '?'));
			}
			
			if (file_exists(DOCROOT . $fileOrUrl)) {
				// Check for RTL replacements
				if ($type == 'css' AND ush_locale::is_rtl_language())
				{
					$rtlFile = substr($fileOrUrl, 0, strpos($fileOrUrl, ".$type")) . "-rtl" . substr($fileOrUrl, strpos($fileOrUrl, ".$type"));
					if (file_exists(DOCROOT . $rtlFile))
					{
						$fileOrUrl = $rtlFile;
					}
				}
				
				// Get url prefix, either site base url or CDN url
				$prefix = url::file_loc($type);
				
				$mtimesuffix = "?";
				if($this->suffix_requirements) {
					$mtimesuffix .= "m=" . filemtime(DOCROOT . $fileOrUrl);
					$suffix = '&'.$suffix;
				}
				return "{$prefix}{$fileOrUrl}{$mtimesuffix}{$suffix}";
			}
		}
		Kohana::log('alert', "Requirements: file $fileOrUrl not found");
		return false;
	}
	
	/**
	 * Concatenate several css or javascript files into a single dynamically generated
	 * file. This increases performance by fewer HTTP requests.
	 * 
	 * The combined file is only included if one or more of the individual files was included
	 * with Requirements::js() or Requirements::css() already.
	 * 
	 * The combined file is regenerated based on every file modification time.
	 * Optionally a rebuild can be triggered by appending ?flush=1 to the URL.
	 * If all files to be combined are javascript, we use the external JSMin library
	 * to minify the javascript. This can be controlled by {@link $combine_js_with_jsmin}.
	 * 
	 * CAUTION: You're responsible for ensuring that the load order for combined files
	 * is retained - otherwise combining javascript files can lead to functional errors
	 * in the javascript logic, and combining css can lead to wrong styling inheritance.
	 * Depending on the javascript logic, you also have to ensure that files are not included
	 * in more than one combine_files() call.
	 * 
	 * Best practice is to include every javascript file in exactly *one* combine_files()
	 * directive to avoid the issues mentioned above - this is enforced by this function.
	 * 
	 * CAUTION: Combining CSS Files discards any "media" information.
	 *
	 * Example for combined JavaScript:
	 * <code>
	 * Requirements::combine_files(
	 *  'foobar.js',
	 *  array(
	 * 		'mysite/javascript/foo.js',
	 * 		'mysite/javascript/bar.js',
	 * 		'baz.min' => 'mysite/javascript/baz.min',
	 * 	)
	 * );
	 * </code>
	 *
	 * Example for combined CSS:
	 * <code>
	 * Requirements::combine_files(
	 *  'foobar.css',
	 * 	array(
	 * 		'mysite/javascript/foo.css',
	 * 		'mysite/javascript/bar.css',
	 * 	)
	 * );
	 * </code>
	 *
	 * @see http://code.google.com/p/jsmin-php/
	 * 
	 * @todo Should we enforce unique inclusion of files, or leave it to the developer? Can auto-detection cause breaks?
	 * 
	 * @param string $combinedFileName Filename of the combined file
	 * @param array $files Array of filenames relative to the webroot
	 */
	function combine_files($combinedFileName, $files) {
		$type = stripos($combinedFileName, '.js') ? 'js' : 'css';
		
		// duplicate check
		foreach($this->combine_files[$type] as $_combinedFileName => $_files) {
			$duplicates = array_intersect($_files, $files);
			if($duplicates && $combinedFileName != $_combinedFileName) {
				Kohana::log('info', "Requirements_Backend::combine_files(): Already included files " . implode(',', $duplicates) . " in combined file '{$_combinedFileName}'");
				return false;
			}
		}
		
		// If the $files array is indexed, generate uniquenessID from last part of filename
		if (array_values($files) === $files)
		{
			$new_files = array();
			foreach($files as $file)
			{
				$uniquenessID = substr( $file, strrpos( $file, '/' ) +1 );
				$new_files[$uniquenessID] = $file;
			}
			$files = $new_files;
		}
		
		$this->combine_files[$type][$combinedFileName] = $files;
	}
	
	/**
	 * Returns all combined files.
	 * @return array
	 */
	function get_combine_files() {
		return $this->combine_files;
	}
	
	/**
	 * Deletes all dynamically generated combined files from the filesystem. 
	 * 
	 * @param string $combinedFileName If left blank, all combined files are deleted.
	 */
	function delete_combined_files($combinedFileName = null) {
		$combinedFiles = ($combinedFileName) ? array($combinedFileName => null) : array_merge($this->combine_files['css'], $this->combine_files['js']);
		$combinedFolder = DOCROOT . $this->getCombinedFilesFolder();
		foreach($combinedFiles as $combinedFile => $sourceItems) {
			$filePath = $combinedFolder . '/' . $combinedFile;
			if(file_exists($filePath)) {
				unlink($filePath);
			}
		}
	}
	
	function clear_combined_files() {
		$this->combine_files = array('js' => array(), 'css' => array());
	}

	/**
	 * See {@link combine_files()}
	 *
	 */
	function process_combined_files($type = 'all') {
		if( !$this->combined_files_enabled) {
			return;
		}
		
		switch ($type)
		{
			case 'js':
				$this->_process_combined_files('js');
				break;
			case 'css':
				$this->_process_combined_files('css');
				break;
			case 'all':
			default:
				$this->_process_combined_files('js');
				$this->_process_combined_files('css');
				break;
		}
	}

	/**
	 * See {@link combine_files()}
	 *
	 */
	private function _process_combined_files($type) {
		// Make a map of files that could be potentially combined
		$combinerCheck = array();
		foreach($this->combine_files[$type] as $combinedFile => $sourceItems) {
			foreach($sourceItems as $id => $sourceItem) {
				if(isset($combinerCheck[$sourceItem]) && $combinerCheck[$sourceItem] != $combinedFile){ 
					Kohana::log('alert',"Requirements_Backend::process_combined_files - file '$sourceItem' appears in two combined files:" .	" '{$combinerCheck[$sourceItem]}' and '$combinedFile'");
				}
				$combinerCheck[$sourceItem] = $combinedFile;
				$combinerCheck[$id] = $combinedFile;
			}
		}

		// Work out the relative URL for the combined files from the base folder
		$combinedFilesFolder = ($this->getCombinedFilesFolder()) ? ($this->getCombinedFilesFolder() . '/') : '';

		// Figure out which ones apply to this pageview
		$combinedFiles = array();
		$newRequirements = array();
		foreach($this->$type as $id => $params) {
			$file = ($type == 'js') ? $params : $params['file'];
			if(isset($combinerCheck[$file])) {
				$newRequirements[$combinerCheck[$file]] = ($type == 'js') ? $combinedFilesFolder . $combinerCheck[$file] : array('file' => $combinedFilesFolder . $combinerCheck[$file]);
				$combinedFiles[$combinerCheck[$file]] = true;
			} elseif(isset($combinerCheck[$id])) {
				$newRequirements[$combinerCheck[$id]] = ($type == 'js') ? $combinedFilesFolder . $combinerCheck[$id] : array('file' => $combinedFilesFolder . $combinerCheck[$id]);
				$combinedFiles[$combinerCheck[$id]] = true;
			} else {
				$newRequirements[$id] = $params;
			}
		}

		// Process the combined files
		$base = DOCROOT;
		foreach(array_diff_key($combinedFiles, $this->blocked) as $combinedFile => $dummy) {
			$fileList = $this->combine_files[$type][$combinedFile];
			$combinedFilePath = $base . $combinedFilesFolder . $combinedFile;

			// Check for RTL alternatives
			if ($type == 'css' AND ush_locale::is_rtl_language())
			{
				$has_rtl_files = FALSE;
				foreach($fileList as $index => $file)
				{
					$rtlFile = substr($file, 0, strpos($file, ".$type")) . "-rtl" . substr($file, strpos($file, ".$type"));
					if (file_exists(DOCROOT . $rtlFile))
					{
						$fileList[$index] = $rtlFile;
						$has_rtl_files = TRUE;
					}
				}
				
				// Update combined files details, only if the include RTL alternatives
				// We store the RTL version separate from the LTR version, so we don't regenerate every time someone changes language
				if ($has_rtl_files)
				{
					$combinedFile = substr($combinedFile, 0, -4).'-rtl.css';
					$combinedFilePath = $base . $combinedFilesFolder . $combinedFile;
					$newRequirements[$combinedFile] = ($type == 'js') ? $combinedFilesFolder . $combinedFile : array('file' => $combinedFilesFolder . $combinedFile);
				}
			}

			// Make the folder if necessary
			if(!file_exists(dirname($combinedFilePath))) {
				mkdir(dirname($combinedFilePath));
			}

			// If the file isn't writebale, don't even bother trying to make the combined file
			// Complex test because is_writable fails if the file doesn't exist yet.
			if((file_exists($combinedFilePath) && !is_writable($combinedFilePath)) ||
				(!file_exists($combinedFilePath) && !is_writable(dirname($combinedFilePath)))) {
				Kohana::log('alert', "Requirements_Backend::process_combined_files(): Couldn't create '$combinedFilePath'");
				continue;
			}

			 // Determine if we need to build the combined include
			if(file_exists($combinedFilePath) && !isset($_GET['flush'])) {
				// file exists, check modification date of every contained file
				$srcLastMod = 0;
				foreach($fileList as $file) {
					$srcLastMod = max(filemtime($base . $file), $srcLastMod);
				}
				$refresh = $srcLastMod > filemtime($combinedFilePath);
			} else {
				// file doesn't exist, or refresh was explicitly required
				$refresh = true;
			}

			if(!$refresh) continue;

			$combinedData = "";
			foreach(array_diff($fileList, $this->blocked) as $id => $file) {
				$fileContent = file_get_contents($base . $file);
				
				// if we have a javascript file and jsmin is enabled, minify the content
				if($type == 'js' && $this->combine_js_with_jsmin) {
					$fileContent = JSMin::minify($fileContent);
				}
				
				if($type == 'css') {
					// Rewrite urls in css to be relative to the docroot
					// Docroot param needs to be actual server docroot, not root of ushahidi site.
					// Using $_SERVER['DOCUMENT_ROOT']. Should possibly use DOCROOT, but remove Kohana::config('core.site_domain', TRUE);
					$fileContent = Minify_CSS_UriRewriter::rewrite($fileContent, pathinfo($base . $file, PATHINFO_DIRNAME), $_SERVER['DOCUMENT_ROOT']);
					// compress css (if enabled)
					if ($this->combine_css_with_cssmin)
					{
						$fileContent = CSSmin::go($fileContent);
					}
				}
				
				// write a header comment for each file for easier identification and debugging
				// also the semicolon between each file is required for jQuery to be combinable properly
				$combinedData .= "/****** FILE: $file *****/\n" . $fileContent . "\n" . ($type == 'js' ? ';' : '') . "\n";
			}

			$successfulWrite = false;
			$fh = fopen($combinedFilePath, 'wb');
			if($fh) {
				if(fwrite($fh, $combinedData) == strlen($combinedData)) $successfulWrite = true;
				fclose($fh);
				unset($fh);
			}
			
			// Should we push this to the CDN too?
			if (Kohana::config("cdn.cdn_store_dynamic_content") AND Kohana::config("requirements.cdn_store_combined_files"))
			{
				$cdn_combined_path = cdn::upload($combinedFilesFolder . $combinedFile, FALSE);
			}

			// Unsuccessful write - just include the regular JS files, rather than the combined one
			if(!$successfulWrite) {
				Kohana::log('alert', "Requirements_Backend::process_combined_files(): Couldn't create '$combinedFilePath'");
				continue;
			}
		}

		// @todo Alters the original information, which means you can't call this
		// method repeatedly - it will behave different on the second call!
		$this->$type = $newRequirements;
	}
	
	function get_custom_scripts() {
		$requirements = "";
		
		if($this->customJS) {
			foreach($this->customJS as $script) {
				$requirements .= "$script\n";
			}
		}
		
		return $requirements;
	}

	/**
	 * @see Requirements::themedCSS()
	 */
	public function themedCSS($name, $module = null, $media = null) {
		$this->css($this->themedCSSPath($name, $module), $name, $media);
		return;
	}

	/**
	 * @see Requirements::ieCSS()
	 */
	public function ieCSS($version, $name, $media = null) {
		$this->customHeadTags("<!--[if $version]>".html::stylesheet(url::file_loc('css').$name,$media,TRUE)."<![endif]-->",'iecss-'.$name);
		return;
	}

	/**
	 * @see Requirements::ieThemedCSS()
	 */
	public function ieThemedCSS($version, $name, $module = null, $media = null) {
		$this->ieCSS($version, $this->themedCSSPath($name, $module), $media, FALSE);
		return;
	}
	
	private function themedCSSPath($name, $module = null)
	{
		// try to include from a loaded theme
		foreach (Themes::loaded_themes() as $theme)
		{
			$path  = THEMEPATH . "$theme/css/$name";
			if (file_exists($path)) {
				return "themes/$theme/css/$name";
			}
		}

		// Try to include from fall back module
		if ($module AND file_exists(DOCROOT . "$module/css/$name")) {
			return "$module/css/$name";
		}
		
		// Try to include from global media
		if (file_exists(DOCROOT . "media/css/$name")) {
			return "media/css/$name";
		}
	}
	
	function debug() {
		Debug::show($this->js);
		Debug::show($this->css);
		Debug::show($this->customCSS);
		Debug::show($this->customJS);
		Debug::show($this->customHeadTags);
		Debug::show($this->combine_files);
	}
	
}
