<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Encrypt
 *
 * Encrypt configuration is defined in groups which allows you to easily switch
 * between different encryption settings for different uses.
 * Note: all groups inherit and overwrite the default group.
 *
 * Group Options:
 *  key    - Encryption key used to do encryption and decryption. The default option
 *           should never be used for a production website.
 *
 *           For best security, your encryption key should be at least 16 characters
 *           long and contain letters, numbers, and symbols.
 *           @note Do not use a hash as your key. This significantly lowers encryption entropy.
 *
 *  mode   - MCrypt encryption mode. By default, MCRYPT_MODE_NOFB is used. This mode
 *           offers initialization vector support, is suited to short strings, and
 *           produces the shortest encrypted output.
 *           @see http://php.net/mcrypt
 *
 *  cipher - MCrypt encryption cipher. By default, the MCRYPT_RIJNDAEL_128 cipher is used.
 *           This is also known as 128-bit AES.
 *           @see http://php.net/mcrypt
 */
// CHANGE ME: THIS SHOULD BE UNIQUE TO YOUR DEPLOYMENT
$config['default']['key'] = 'USHAHIDI-INSECURE';
$config['default']['mode'] = MCRYPT_MODE_NOFB;
$config['default']['cipher'] = MCRYPT_RIJNDAEL_128;
