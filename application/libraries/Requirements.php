<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Requirements tracker, for javascript and css.
 * 
 * Based on Requirements class from sapphire framework
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
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   https://github.com/silverstripe/sapphire Modified BSD License
 */
class Requirements {

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
	public $combine_files = array();

	/**
	 * Using the JSMin library to minify any
	 * javascript file passed to {@link combine_files()}.
	 *
	 * @var boolean
	 */
	public $combine_js_with_jsmin = true;

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
		$this->set_suffix_requirements(Kohana::config('requirements.suffix_requirements'));
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
		return array_keys(array_diff_key($this->js,$this->blocked));
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
		return array_diff_key($this->css, $this->blocked);
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
	function renderJS()
	{
		$content = '';
		if($this->js || $this->customJS) {
			foreach(array_diff_key($this->js,$this->blocked) as $id => $file) {
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
	function renderHeadTags()
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
	function renderCSS()
	{
		$content = '';
		if($this->css || $this->customCSS) {
			foreach(array_diff_key($this->css,$this->blocked) as $id => $params) {
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
			if (file_exists(DOCROOT . $fileOrUrl)) {
				// Get url prefix, either site base url or CDN url
				$prefix = url::file_loc($type);
				
				$mtimesuffix = "";
				$suffix = '';
				if(strpos($fileOrUrl, '?') !== false) {
					$suffix = '&' . substr($fileOrUrl, strpos($fileOrUrl, '?')+1);
					$fileOrUrl = substr($fileOrUrl, 0, strpos($fileOrUrl, '?'));
				}
				if($this->suffix_requirements) {
					$mtimesuffix = "?m=" . filemtime(DOCROOT . $fileOrUrl);
				}
				return "{$prefix}{$fileOrUrl}{$mtimesuffix}{$suffix}";
			}
		}
		Kohana::log('alert', "Requirments: file $fileOrUrl not found");
		return false;
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
			$path  = THEMEPATH . "$theme/css/$name.css";
			if (file_exists($path)) {
				return "themes/$theme/css/$name.css";
			}
		}

		// Try to include from fall back module
		if ($module AND file_exists(DOCROOT . "$module/css/$name.css")) {
			return "$module/css/$name.css";
		}
		
		// Try to include from global media
		if (file_exists(DOCROOT . "media/css/$name.css")) {
			return "media/css/$name.css";
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
