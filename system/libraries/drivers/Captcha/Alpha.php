<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Captcha driver for "alpha" style.
 *
 * $Id: Alpha.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Captcha
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Captcha_Alpha_Driver extends Captcha_Driver {

	/**
	 * Generates a new Captcha challenge.
	 *
	 * @return  string  the challenge answer
	 */
	public function generate_challenge()
	{
		// Complexity setting is used as character count
		return text::random('distinct', max(1, Captcha::$config['complexity']));
	}

	/**
	 * Outputs the Captcha image.
	 *
	 * @param   boolean  html output
	 * @return  mixed
	 */
	public function render($html)
	{
		// Creates $this->image
		$this->image_create(Captcha::$config['background']);

		// Add a random gradient
		if (empty(Captcha::$config['background']))
		{
			$color1 = imagecolorallocate($this->image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
			$color2 = imagecolorallocate($this->image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
			$this->image_gradient($color1, $color2);
		}

		// Add a few random circles
		for ($i = 0, $count = mt_rand(10, Captcha::$config['complexity'] * 3); $i < $count; $i++)
		{
			$color = imagecolorallocatealpha($this->image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), mt_rand(80, 120));
			$size = mt_rand(5, Captcha::$config['height'] / 3);
			imagefilledellipse($this->image, mt_rand(0, Captcha::$config['width']), mt_rand(0, Captcha::$config['height']), $size, $size, $color);
		}

		// Calculate character font-size and spacing
		$default_size = min(Captcha::$config['width'], Captcha::$config['height'] * 2) / strlen($this->response);
		$spacing = (int) (Captcha::$config['width'] * 0.9 / strlen($this->response));

		// Background alphabetic character attributes
		$color_limit = mt_rand(96, 160);
		$chars = 'ABEFGJKLPQRTVY';

		// Draw each Captcha character with varying attributes
		for ($i = 0, $strlen = strlen($this->response); $i < $strlen; $i++)
		{
			// Use different fonts if available
			$font = Captcha::$config['fontpath'].Captcha::$config['fonts'][array_rand(Captcha::$config['fonts'])];

			$angle = mt_rand(-40, 20);
			// Scale the character size on image height
			$size = $default_size / 10 * mt_rand(8, 12);
			$box = imageftbbox($size, $angle, $font, $this->response[$i]);

			// Calculate character starting coordinates
			$x = $spacing / 4 + $i * $spacing;
			$y = Captcha::$config['height'] / 2 + ($box[2] - $box[5]) / 4;

			// Draw captcha text character
			// Allocate random color, size and rotation attributes to text
			$color = imagecolorallocate($this->image, mt_rand(150, 255), mt_rand(200, 255), mt_rand(0, 255));

			// Write text character to image
			imagefttext($this->image, $size, $angle, $x, $y, $color, $font, $this->response[$i]);

			// Draw "ghost" alphabetic character
			$text_color = imagecolorallocatealpha($this->image, mt_rand($color_limit + 8, 255), mt_rand($color_limit + 8, 255), mt_rand($color_limit + 8, 255), mt_rand(70, 120));
			$char = substr($chars, mt_rand(0, 14), 1);
			imagettftext($this->image, $size * 2, mt_rand(-45, 45), ($x - (mt_rand(5, 10))), ($y + (mt_rand(5, 10))), $text_color, $font, $char);
		}

		// Output
		return $this->image_render($html);
	}

} // End Captcha Alpha Driver Class