<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Core
 *
 * Captcha configuration is defined in groups which allows you to easily switch
 * between different Captcha settings for different forms on your website.
 * Note: all groups inherit and overwrite the default group.
 *
 * Group Options:
 *  style      - Captcha type, e.g. basic, alpha, word, math, riddle
 *  width      - Width of the Captcha image
 *  height     - Height of the Captcha image
 *  complexity - Difficulty level (0-10), usage depends on chosen style
 *  background - Path to background image file
 *  fontpath   - Path to font folder
 *  fonts      - Font files
 *  promote    - Valid response count threshold to promote user (FALSE to disable)
 */
$config['default'] = array
(
	'style'      => 'basic',
	'width'      => 150,
	'height'     => 50,
	'complexity' => 4,
	'background' => '',
	'fontpath'   => SYSPATH.'fonts/',
	'fonts'      => array('DejaVuSerif.ttf'),
	'promote'    => FALSE,
);