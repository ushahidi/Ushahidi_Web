<?php
/**
 * Auth library configuration. By default, Auth will use the controller
 * database connection. If Database is not loaded, it will use use the default
 * database group.
 *
 * In order to log a user in, a user must have the `login` role. You may create
 * and assign any other role to your users.
 */

/**
 * Driver to use for authentication. By default, file and ORM are available.
 */
$config['driver'] = 'ORM';

/**
 * Type of hash to use for passwords. Any algorithm supported by the hash function
 * can be used here. Note that the length of your password is determined by the
 * hash type + the number of salt characters.
 * Note: This is unrelated to the hash settings in the Openid library.
 * @see http://php.net/hash
 * @see http://php.net/hash_algos
 */
$config['hash_method'] = 'sha1';

/**
 * Defines the hash offsets to insert the salt at. The password hash length
 * will be increased by the total number of offsets.
 */
$config['salt_pattern'] = '3, 5, 6, 10, 24, 26, 35, 36, 37, 40';

/**
 * Set the auto-login (remember me) cookie lifetime, in seconds. The default
 * lifetime is two weeks.
 */
$config['lifetime'] = 1209600;

/**
 * Set the session key that will be used to store the current user.
 */
$config['session_key'] = 'auth_user';

/**
 * Usernames (keys) and hashed passwords (values) used by the File driver.
 */
$config['users'] = array
(
	// 'admin' => '4ccd0e25c2a7ffefd4b92ecbbd4781752920145f826a881073',
);