<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * ImageMagick Image Driver.
 *
 * $Id: ImageMagick.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Image
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Image_ImageMagick_Driver extends Image_Driver {

	// Directory that IM is installed in
	protected $dir = '';

	// Command extension (exe for windows)
	protected $ext = '';

	// Temporary image filename
	protected $tmp_image;

	/**
	 * Attempts to detect the ImageMagick installation directory.
	 *
	 * @throws  Kohana_Exception
	 * @param   array   configuration
	 * @return  void
	 */
	public function __construct($config)
	{
		if (empty($config['directory']))
		{
			// Attempt to locate IM by using "which" (only works for *nix!)
			if ( ! is_file($path = exec('which convert')))
				throw new Kohana_Exception('image.imagemagick.not_found');

			$config['directory'] = dirname($path);
		}

		// Set the command extension
		$this->ext = (PHP_SHLIB_SUFFIX === 'dll') ? '.exe' : '';

		// Check to make sure the provided path is correct
		if ( ! is_file(realpath($config['directory']).'/convert'.$this->ext))
			throw new Kohana_Exception('image.imagemagick.not_found', 'convert'.$this->ext);

		// Set the installation directory
		$this->dir = str_replace('\\', '/', realpath($config['directory'])).'/';
	}

	/**
	 * Creates a temporary image and executes the given actions. By creating a
	 * temporary copy of the image before manipulating it, this process is atomic.
	 */
	public function process($image, $actions, $dir, $file, $render = FALSE)
	{
		// We only need the filename
		$image = $image['file'];

		// Unique temporary filename
		$this->tmp_image = $dir.'k2img--'.sha1(time().$dir.$file).substr($file, strrpos($file, '.'));

		// Copy the image to the temporary file
		copy($image, $this->tmp_image);

		// Quality change is done last
		$quality = (int) arr::remove('quality', $actions);

		// Use 95 for the default quality
		empty($quality) and $quality = 95;

		// All calls to these will need to be escaped, so do it now
		$this->cmd_image = escapeshellarg($this->tmp_image);
		$this->new_image = ($render)? $this->cmd_image : escapeshellarg($dir.$file);

		if ($status = $this->execute($actions))
		{
			// Use convert to change the image into its final version. This is
			// done to allow the file type to change correctly, and to handle
			// the quality conversion in the most effective way possible.
			if ($error = exec(escapeshellcmd($this->dir.'convert'.$this->ext).' -quality '.$quality.'% '.$this->cmd_image.' '.$this->new_image))
			{
				$this->errors[] = $error;
			}
			else
			{
				// Output the image directly to the browser
				if ($render !== FALSE)
				{
					$contents = file_get_contents($this->tmp_image);
					switch (substr($file, strrpos($file, '.') + 1))
					{
						case 'jpg':
						case 'jpeg':
							header('Content-Type: image/jpeg');
						break;
						case 'gif':
							header('Content-Type: image/gif');
						break;
						case 'png':
							header('Content-Type: image/png');
						break;
 					}
					echo $contents;
				}
			}
		}

		// Remove the temporary image
		unlink($this->tmp_image);
		$this->tmp_image = '';

		return $status;
	}

	public function crop($prop)
	{
		// Sanitize and normalize the properties into geometry
		$this->sanitize_geometry($prop);

		// Set the IM geometry based on the properties
		$geometry = escapeshellarg($prop['width'].'x'.$prop['height'].'+'.$prop['left'].'+'.$prop['top']);

		if ($error = exec(escapeshellcmd($this->dir.'convert'.$this->ext).' -crop '.$geometry.' '.$this->cmd_image.' '.$this->cmd_image))
		{
			$this->errors[] = $error;
			return FALSE;
		}

		return TRUE;
	}

	public function flip($dir)
	{
		// Convert the direction into a IM command
		$dir = ($dir === Image::HORIZONTAL) ? '-flop' : '-flip';

		if ($error = exec(escapeshellcmd($this->dir.'convert'.$this->ext).' '.$dir.' '.$this->cmd_image.' '.$this->cmd_image))
		{
			$this->errors[] = $error;
			return FALSE;
		}

		return TRUE;
	}

	public function resize($prop)
	{
		switch ($prop['master'])
		{
			case Image::WIDTH:  // Wx
				$dim = escapeshellarg($prop['width'].'x');
			break;
			case Image::HEIGHT: // xH
				$dim = escapeshellarg('x'.$prop['height']);
			break;
			case Image::AUTO:   // WxH
				$dim = escapeshellarg($prop['width'].'x'.$prop['height']);
			break;
			case Image::NONE:   // WxH!
				$dim = escapeshellarg($prop['width'].'x'.$prop['height'].'!');
			break;
		}

		// Use "convert" to change the width and height
		if ($error = exec(escapeshellcmd($this->dir.'convert'.$this->ext).' -resize '.$dim.' '.$this->cmd_image.' '.$this->cmd_image))
		{
			$this->errors[] = $error;
			return FALSE;
		}

		return TRUE;
	}

	public function rotate($amt)
	{
		if ($error = exec(escapeshellcmd($this->dir.'convert'.$this->ext).' -rotate '.escapeshellarg($amt).' -background transparent '.$this->cmd_image.' '.$this->cmd_image))
		{
			$this->errors[] = $error;
			return FALSE;
		}

		return TRUE;
	}

	public function sharpen($amount)
	{
		// Set the sigma, radius, and amount. The amount formula allows a nice
		// spread between 1 and 100 without pixelizing the image badly.
		$sigma  = 0.5;
		$radius = $sigma * 2;
		$amount = round(($amount / 80) * 3.14, 2);

		// Convert the amount to an IM command
		$sharpen = escapeshellarg($radius.'x'.$sigma.'+'.$amount.'+0');

		if ($error = exec(escapeshellcmd($this->dir.'convert'.$this->ext).' -unsharp '.$sharpen.' '.$this->cmd_image.' '.$this->cmd_image))
		{
			$this->errors[] = $error;
			return FALSE;
		}

		return TRUE;
	}

	protected function properties()
	{
		return array_slice(getimagesize($this->tmp_image), 0, 2, FALSE);
	}

} // End Image ImageMagick Driver