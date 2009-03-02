<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Captcha driver class.
 *
 * $Id: Captcha.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Captcha
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
abstract class Captcha_Driver {

	// The correct Captcha challenge answer
	protected $response;

	// Image resource identifier and type ("png", "gif" or "jpeg")
	protected $image;
	protected $image_type = 'png';

	/**
	 * Constructs a new challenge.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// Generate a new challenge
		$this->response = $this->generate_challenge();

		// Store the correct Captcha response in a session
		Event::add('system.post_controller', array($this, 'update_response_session'));
	}

	/**
	 * Generate a new Captcha challenge.
	 *
	 * @return  string  the challenge answer
	 */
	abstract public function generate_challenge();

	/**
	 * Output the Captcha challenge.
	 *
	 * @param   boolean  html output
	 * @return  mixed    the rendered Captcha (e.g. an image, riddle, etc.)
	 */
	abstract public function render($html);

	/**
	 * Stores the response for the current Captcha challenge in a session so it is available
	 * on the next page load for Captcha::valid(). This method is called after controller
	 * execution (in the system.post_controller event) in order not to overwrite itself too soon.
	 *
	 * @return  void
	 */
	public function update_response_session()
	{
		Session::instance()->set('captcha_response', sha1(strtoupper($this->response)));
	}

	/**
	 * Validates a Captcha response from a user.
	 *
	 * @param   string   captcha response
	 * @return  boolean
	 */
	public function valid($response)
	{
		return (sha1(strtoupper($response)) === Session::instance()->get('captcha_response'));
	}

	/**
	 * Returns the image type.
	 *
	 * @param   string        filename
	 * @return  string|FALSE  image type ("png", "gif" or "jpeg")
	 */
	public function image_type($filename)
	{
		switch (strtolower(substr(strrchr($filename, '.'), 1)))
		{
			case 'png':
				return 'png';

			case 'gif':
				return 'gif';

			case 'jpg':
			case 'jpeg':
				// Return "jpeg" and not "jpg" because of the GD2 function names
				return 'jpeg';

			default:
				return FALSE;
		}
	}

	/**
	 * Creates an image resource with the dimensions specified in config.
	 * If a background image is supplied, the image dimensions are used.
	 *
	 * @throws  Kohana_Exception  if no GD2 support
	 * @param   string  path to the background image file
	 * @return  void
	 */
	public function image_create($background = NULL)
	{
		// Check for GD2 support
		if ( ! function_exists('imagegd2'))
			throw new Kohana_Exception('captcha.requires_GD2');

		// Create a new image (black)
		$this->image = imagecreatetruecolor(Captcha::$config['width'], Captcha::$config['height']);

		// Use a background image
		if ( ! empty($background))
		{
			// Create the image using the right function for the filetype
			$function = 'imagecreatefrom'.$this->image_type($background);
			$this->background_image = $function($background);

			// Resize the image if needed
			if (imagesx($this->background_image) !== Captcha::$config['width']
			    OR imagesy($this->background_image) !== Captcha::$config['height'])
			{
				imagecopyresampled
				(
					$this->image, $this->background_image, 0, 0, 0, 0,
					Captcha::$config['width'], Captcha::$config['height'],
					imagesx($this->background_image), imagesy($this->background_image)
				);
			}

			// Free up resources
			imagedestroy($this->background_image);
		}
	}

	/**
	 * Fills the background with a gradient.
	 *
	 * @param   resource  gd image color identifier for start color
	 * @param   resource  gd image color identifier for end color
	 * @param   string    direction: 'horizontal' or 'vertical', 'random' by default
	 * @return  void
	 */
	public function image_gradient($color1, $color2, $direction = NULL)
	{
		$directions = array('horizontal', 'vertical');

		// Pick a random direction if needed
		if ( ! in_array($direction, $directions))
		{
			$direction = $directions[array_rand($directions)];

			// Switch colors
			if (mt_rand(0, 1) === 1)
			{
				$temp = $color1;
				$color1 = $color2;
				$color2 = $temp;
			}
		}

		// Extract RGB values
		$color1 = imagecolorsforindex($this->image, $color1);
		$color2 = imagecolorsforindex($this->image, $color2);

		// Preparations for the gradient loop
		$steps = ($direction === 'horizontal') ? Captcha::$config['width'] : Captcha::$config['height'];

		$r1 = ($color1['red'] - $color2['red']) / $steps;
		$g1 = ($color1['green'] - $color2['green']) / $steps;
		$b1 = ($color1['blue'] - $color2['blue']) / $steps;

		if ($direction === 'horizontal')
		{
			$x1 =& $i;
			$y1 = 0;
			$x2 =& $i;
			$y2 = Captcha::$config['height'];
		}
		else
		{
			$x1 = 0;
			$y1 =& $i;
			$x2 = Captcha::$config['width'];
			$y2 =& $i;
		}

		// Execute the gradient loop
		for ($i = 0; $i <= $steps; $i++)
		{
			$r2 = $color1['red'] - floor($i * $r1);
			$g2 = $color1['green'] - floor($i * $g1);
			$b2 = $color1['blue'] - floor($i * $b1);
			$color = imagecolorallocate($this->image, $r2, $g2, $b2);

			imageline($this->image, $x1, $y1, $x2, $y2, $color);
		}
	}

	/**
	 * Returns the img html element or outputs the image to the browser.
	 *
	 * @param   boolean  html output
	 * @return  mixed    html string or void
	 */
	public function image_render($html)
	{
		// Output html element
		if ($html)
			return '<img alt="Captcha" src="'.url::site('captcha/'.Captcha::$config['group']).'" width="'.Captcha::$config['width'].'" height="'.Captcha::$config['height'].'" />';

		// Send the correct HTTP header
		header('Content-Type: image/'.$this->image_type);

		// Pick the correct output function
		$function = 'image'.$this->image_type;
		$function($this->image);

		// Free up resources
		imagedestroy($this->image);
	}

} // End Captcha Driver