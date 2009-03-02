<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Captcha driver for "black" style.
 *
 * $Id: Black.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Captcha
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Captcha_Black_Driver extends Captcha_Driver {

	/**
	 * Generates a new Captcha challenge.
	 *
	 * @return  string  the challenge answer
	 */
	public function generate_challenge()
	{
		// Complexity setting is used as character count
		return text::random('distinct', max(1, ceil(Captcha::$config['complexity'] / 1.5)));
	}

	/**
	 * Outputs the Captcha image.
	 *
	 * @param   boolean  html output
	 * @return  mixed
	 */
	public function render($html)
	{
		// Creates a black image to start from
		$this->image_create(Captcha::$config['background']);

		// Add random white/gray arcs, amount depends on complexity setting
		$count = (Captcha::$config['width'] + Captcha::$config['height']) / 2;
		$count = $count / 5 * min(10, Captcha::$config['complexity']);
		for ($i = 0; $i < $count; $i++)
		{
			imagesetthickness($this->image, mt_rand(1, 2));
			$color = imagecolorallocatealpha($this->image, 255, 255, 255, mt_rand(0, 120));
			imagearc($this->image, mt_rand(-Captcha::$config['width'], Captcha::$config['width']), mt_rand(-Captcha::$config['height'], Captcha::$config['height']), mt_rand(-Captcha::$config['width'], Captcha::$config['width']), mt_rand(-Captcha::$config['height'], Captcha::$config['height']), mt_rand(0, 360), mt_rand(0, 360), $color);
		}

		// Use different fonts if available
		$font = Captcha::$config['fontpath'].Captcha::$config['fonts'][array_rand(Captcha::$config['fonts'])];

		// Draw the character's white shadows
		$size = (int) min(Captcha::$config['height'] / 2, Captcha::$config['width'] * 0.8 / strlen($this->response));
		$angle = mt_rand(-15 + strlen($this->response), 15 - strlen($this->response));
		$x = mt_rand(1, Captcha::$config['width'] * 0.9 - $size * strlen($this->response));
		$y = ((Captcha::$config['height'] - $size) / 2) + $size;
		$color = imagecolorallocate($this->image, 255, 255, 255);
		imagefttext($this->image, $size, $angle, $x + 1, $y + 1, $color, $font, $this->response);

		// Add more shadows for lower complexities
		(Captcha::$config['complexity'] < 10) and imagefttext($this->image, $size, $angle, $x - 1, $y - 1, $color, $font , $this->response);
		(Captcha::$config['complexity'] < 8)  and imagefttext($this->image, $size, $angle, $x - 2, $y + 2, $color, $font , $this->response);
		(Captcha::$config['complexity'] < 6)  and imagefttext($this->image, $size, $angle, $x + 2, $y - 2, $color, $font , $this->response);
		(Captcha::$config['complexity'] < 4)  and imagefttext($this->image, $size, $angle, $x + 3, $y + 3, $color, $font , $this->response);
		(Captcha::$config['complexity'] < 2)  and imagefttext($this->image, $size, $angle, $x - 3, $y - 3, $color, $font , $this->response);

		// Finally draw the foreground characters
		$color = imagecolorallocate($this->image, 0, 0, 0);
		imagefttext($this->image, $size, $angle, $x, $y, $color, $font, $this->response);

		// Output
		return $this->image_render($html);
	}

} // End Captcha Black Driver Class