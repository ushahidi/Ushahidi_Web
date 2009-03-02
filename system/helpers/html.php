<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * HTML helper class.
 *
 * $Id: html.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class html_Core {

	// Enable or disable automatic setting of target="_blank"
	public static $windowed_urls = FALSE;

	/**
	 * Convert special characters to HTML entities
	 *
	 * @param   string   string to convert
	 * @param   boolean  encode existing entities
	 * @return  string
	 */
	public static function specialchars($str, $double_encode = TRUE)
	{
		// Force the string to be a string
		$str = (string) $str;

		// Do encode existing HTML entities (default)
		if ($double_encode === TRUE)
		{
			$str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
		}
		else
		{
			// Do not encode existing HTML entities
			// From PHP 5.2.3 this functionality is built-in, otherwise use a regex
			if (version_compare(PHP_VERSION, '5.2.3', '>='))
			{
				$str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8', FALSE);
			}
			else
			{
				$str = preg_replace('/&(?!(?:#\d++|[a-z]++);)/ui', '&amp;', $str);
				$str = str_replace(array('<', '>', '\'', '"'), array('&lt;', '&gt;', '&#39;', '&quot;'), $str);
			}
		}

		return $str;
	}

	/**
	 * Create HTML link anchors.
	 *
	 * @param   string  URL or URI string
	 * @param   string  link text
	 * @param   array   HTML anchor attributes
	 * @param   string  non-default protocol, eg: https
	 * @return  string
	 */
	public static function anchor($uri, $title = NULL, $attributes = NULL, $protocol = NULL)
	{
		if ($uri === '')
		{
			$site_url = url::base(FALSE);
		}
		elseif (strpos($uri, '://') === FALSE AND strpos($uri, '#') !== 0)
		{
			$site_url = url::site($uri, $protocol);
		}
		else
		{
			if (html::$windowed_urls === TRUE AND empty($attributes['target']))
			{
				$attributes['target'] = '_blank';
			}

			$site_url = $uri;
		}

		return
		// Parsed URL
		'<a href="'.html::specialchars($site_url, FALSE).'"'
		// Attributes empty? Use an empty string
		.(is_array($attributes) ? html::attributes($attributes) : '').'>'
		// Title empty? Use the parsed URL
		.(($title === NULL) ? $site_url : $title).'</a>';
	}

	/**
	 * Creates an HTML anchor to a file.
	 *
	 * @param   string  name of file to link to
	 * @param   string  link text
	 * @param   array   HTML anchor attributes
	 * @param   string  non-default protocol, eg: ftp
	 * @return  string
	 */
	public static function file_anchor($file, $title = NULL, $attributes = NULL, $protocol = NULL)
	{
		return
		// Base URL + URI = full URL
		'<a href="'.html::specialchars(url::base(FALSE, $protocol).$file, FALSE).'"'
		// Attributes empty? Use an empty string
		.(is_array($attributes) ? html::attributes($attributes) : '').'>'
		// Title empty? Use the filename part of the URI
		.(($title === NULL) ? end(explode('/', $file)) : $title) .'</a>';
	}

	/**
	 * Similar to anchor, but with the protocol parameter first.
	 *
	 * @param   string  link protocol
	 * @param   string  URI or URL to link to
	 * @param   string  link text
	 * @param   array   HTML anchor attributes
	 * @return  string
	 */
	public static function panchor($protocol, $uri, $title = FALSE, $attributes = FALSE)
	{
		return html::anchor($uri, $title, $attributes, $protocol);
	}

	/**
	 * Create an array of anchors from an array of link/title pairs.
	 *
	 * @param   array  link/title pairs
	 * @return  array
	 */
	public static function anchor_array(array $array)
	{
		$anchors = array();
		foreach ($array as $link => $title)
		{
			// Create list of anchors
			$anchors[] = html::anchor($link, $title);
		}
		return $anchors;
	}

	/**
	 * Generates an obfuscated version of an email address.
	 *
	 * @param   string  email address
	 * @return  string
	 */
	public static function email($email)
	{
		$safe = '';
		foreach (str_split($email) as $letter)
		{
			switch (($letter === '@') ? rand(1, 2) : rand(1, 3))
			{
				// HTML entity code
				case 1: $safe .= '&#'.ord($letter).';'; break;
				// Hex character code
				case 2: $safe .= '&#x'.dechex(ord($letter)).';'; break;
				// Raw (no) encoding
				case 3: $safe .= $letter;
			}
		}

		return $safe;
	}

	/**
	 * Creates an email anchor.
	 *
	 * @param   string  email address to send to
	 * @param   string  link text
	 * @param   array   HTML anchor attributes
	 * @return  string
	 */
	public static function mailto($email, $title = NULL, $attributes = NULL)
	{
		if (empty($email))
			return $title;

		// Remove the subject or other parameters that do not need to be encoded
		if (strpos($email, '?') !== FALSE)
		{
			// Extract the parameters from the email address
			list ($email, $params) = explode('?', $email, 2);

			// Make the params into a query string, replacing spaces
			$params = '?'.str_replace(' ', '%20', $params);
		}
		else
		{
			// No parameters
			$params = '';
		}

		// Obfuscate email address
		$safe = html::email($email);

		// Title defaults to the encoded email address
		empty($title) and $title = $safe;

		// Parse attributes
		empty($attributes) or $attributes = html::attributes($attributes);

		// Encoded start of the href="" is a static encoded version of 'mailto:'
		return '<a href="&#109;&#097;&#105;&#108;&#116;&#111;&#058;'.$safe.$params.'"'.$attributes.'>'.$title.'</a>';
	}

	/**
	 * Generate a "breadcrumb" list of anchors representing the URI.
	 *
	 * @param   array   segments to use as breadcrumbs, defaults to using Router::$segments
	 * @return  string
	 */
	public static function breadcrumb($segments = NULL)
	{
		empty($segments) and $segments = Router::$segments;

		$array = array();
		while ($segment = array_pop($segments))
		{
			$array[] = html::anchor
			(
				// Complete URI for the URL
				implode('/', $segments).'/'.$segment,
				// Title for the current segment
				ucwords(inflector::humanize($segment))
			);
		}

		// Retrun the array of all the segments
		return array_reverse($array);
	}

	/**
	 * Creates a meta tag.
	 *
	 * @param   string|array   tag name, or an array of tags
	 * @param   string         tag "content" value
	 * @return  string
	 */
	public static function meta($tag, $value = NULL)
	{
		if (is_array($tag))
		{
			$tags = array();
			foreach ($tag as $t => $v)
			{
				// Build each tag and add it to the array
				$tags[] = html::meta($t, $v);
			}

			// Return all of the tags as a string
			return implode("\n", $tags);
		}

		// Set the meta attribute value
		$attr = in_array(strtolower($tag), Kohana::config('http.meta_equiv')) ? 'http-equiv' : 'name';

		return '<meta '.$attr.'="'.$tag.'" content="'.$value.'" />';
	}

	/**
	 * Creates a stylesheet link.
	 *
	 * @param   string|array  filename, or array of filenames to match to array of medias
	 * @param   string|array  media type of stylesheet, or array to match filenames
	 * @param   boolean       include the index_page in the link
	 * @return  string
	 */
	public static function stylesheet($style, $media = FALSE, $index = FALSE)
	{
		return html::link($style, 'stylesheet', 'text/css', '.css', $media, $index);
	}

	/**
	 * Creates a link tag.
	 *
	 * @param   string|array  filename
	 * @param   string|array  relationship
	 * @param   string|array  mimetype
	 * @param   string        specifies suffix of the file
	 * @param   string|array  specifies on what device the document will be displayed
	 * @param   boolean       include the index_page in the link
	 * @return  string
	 */
	public static function link($href, $rel, $type, $suffix = FALSE, $media = FALSE, $index = FALSE)
	{
		$compiled = '';

		if (is_array($href))
		{
			foreach ($href as $_href)
			{
				$_rel   = is_array($rel) ? array_shift($rel) : $rel;
				$_type  = is_array($type) ? array_shift($type) : $type;
				$_media = is_array($media) ? array_shift($media) : $media;

				$compiled .= html::link($_href, $_rel, $_type, $suffix, $_media, $index);
			}
		}
		else
		{
			if (strpos($href, '://') === FALSE)
			{
				// Make the URL absolute
				$href = url::base($index).$href;
			}

			$length = strlen($suffix);

			if ( $length > 0 AND substr_compare($href, $suffix, -$length, $length, FALSE) !== 0)
			{
				// Add the defined suffix
				$href .= $suffix;
			}

			$attr = array
			(
				'rel' => $rel,
				'type' => $type,
				'href' => $href,
			);

			if ( ! empty($media))
			{
				// Add the media type to the attributes
				$attr['media'] = $media;
			}

			$compiled = '<link'.html::attributes($attr).' />';
		}

		return $compiled."\n";
	}

	/**
	 * Creates a script link.
	 *
	 * @param   string|array  filename
	 * @param   boolean       include the index_page in the link
	 * @return  string
	 */
	public static function script($script, $index = FALSE)
	{
		$compiled = '';

		if (is_array($script))
		{
			foreach ($script as $name)
			{
				$compiled .= html::script($name, $index);
			}
		}
		else
		{
			if (strpos($script, '://') === FALSE)
			{
				// Add the suffix only when it's not already present
				$script = url::base((bool) $index).$script;
			}

			if (substr_compare($script, '.js', -3, 3, FALSE) !== 0)
			{
				// Add the javascript suffix
				$script .= '.js';
			}

			$compiled = '<script type="text/javascript" src="'.$script.'"></script>';
		}

		return $compiled."\n";
	}

	/**
	 * Creates a image link.
	 *
	 * @param   string        image source, or an array of attributes
	 * @param   string|array  image alt attribute, or an array of attributes
	 * @param   boolean       include the index_page in the link
	 * @return  string
	 */
	public static function image($src = NULL, $alt = NULL, $index = FALSE)
	{
		// Create attribute list
		$attributes = is_array($src) ? $src : array('src' => $src);

		if (is_array($alt))
		{
			$attributes += $alt;
		}
		elseif ( ! empty($alt))
		{
			// Add alt to attributes
			$attributes['alt'] = $alt;
		}

		if (strpos($attributes['src'], '://') === FALSE)
		{
			// Make the src attribute into an absolute URL
			$attributes['src'] = url::base($index).$attributes['src'];
		}

		return '<img'.html::attributes($attributes).' />';
	}

	/**
	 * Compiles an array of HTML attributes into an attribute string.
	 *
	 * @param   string|array  array of attributes
	 * @return  string
	 */
	public static function attributes($attrs)
	{
		if (empty($attrs))
			return '';

		if (is_string($attrs))
			return ' '.$attrs;

		$compiled = '';
		foreach ($attrs as $key => $val)
		{
			$compiled .= ' '.$key.'="'.html::specialchars($val).'"';
		}

		return $compiled;
	}

} // End html
