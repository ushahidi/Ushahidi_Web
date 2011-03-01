<?php
/**
 * Request Helper Unit Tests
 *
 * @package     Core
 * @subpackage  Helpers
 * @author      Chris Bandy
 * @group   core
 * @group   core.helpers
 * @group   core.helpers.request
 */
class Helper_Request_Test extends PHPUnit_Framework_TestCase
{
	protected $server_vars;

	protected function setUp()
	{
		// Save $_SERVER
		$this->server_vars = $_SERVER;
	}

	protected function tearDown()
	{
		// Restore $_SERVER
		$_SERVER = $this->server_vars;

		Helper_Request_Test_Wrapper::reset();
	}

	public function accepts_provider()
	{
		return array(
			array(NULL, NULL, array('*' => array('*' => 1))),
			array(NULL, 'text/plain', TRUE),

			array('', NULL, array('*' => array('*' => 1))),

			array('text/plain', NULL, array('text' => array('plain' => 1))),
			array('text/plain', 'text/plain', TRUE),
			array('text/plain', 'text/html', FALSE),

			array('text/*, text/html', NULL, array('text' => array('*' => 1, 'html' => 1))),
			array('text/*, text/html', 'text/plain', TRUE),

			array('text/plain, text/html;q=0', NULL, array('text' => array('plain' => 1, 'html' => 0))),
			array('text/plain, text/html;q=0', 'text/html', FALSE),

			array('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', NULL,
				array(
					'text' => array('html' => 1),
					'application' => array('xhtml+xml' => 1, 'xml' => 0.9),
					'*' => array('*' => 0.8),
				)
			),
			array('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'application/xml', TRUE),
		);
	}

	/**
	 * @dataProvider accepts_provider
	 * @test
	 */
	public function accepts($accept_header, $arg, $expected)
	{
		$_SERVER['HTTP_ACCEPT'] = $accept_header;

		$this->assertEquals($expected, request::accepts($arg));
	}

	public function preferred_accept_provider()
	{
		return array(
			array(NULL, array(), FALSE, FALSE),
			array(NULL, array(), TRUE, FALSE),
			array(NULL, array('text/plain'), FALSE, 'text/plain'),
			array(NULL, array('text/plain'), TRUE, FALSE),

			array('text/plain', array(), FALSE, FALSE),
			array('text/plain', array(), TRUE, FALSE),
			array('text/plain', array('text/plain'), FALSE, 'text/plain'),
			array('text/plain', array('text/plain'), TRUE, 'text/plain'),
			array('text/plain', array('text/html'), FALSE, FALSE),
			array('text/plain', array('text/html'), TRUE, FALSE),

			array('text/*, text/html', array('text/plain'), FALSE, 'text/plain'),
			array('text/*, text/html', array('text/plain'), TRUE, FALSE),
			array('text/*, text/html', array('text/html'), FALSE, 'text/html'),
			array('text/*, text/html', array('text/html'), TRUE, 'text/html'),
			array('text/*, text/html', array('text/plain', 'text/html'), FALSE, 'text/plain'),
			array('text/*, text/html', array('text/plain', 'text/html'), TRUE, 'text/html'),
			array('text/*, text/html', array('text/html', 'text/plain'), FALSE, 'text/html'),
			array('text/*, text/html', array('text/html', 'text/plain'), TRUE, 'text/html'),

			array('text/plain, text/html;q=0', array('text/html'), FALSE, FALSE),
			array('text/plain, text/html;q=0', array('text/html'), TRUE, FALSE),

			array('text/html,application/xml;q=0.9,*/*;q=0.8', array('text/plain', 'text/html', 'application/xml'), FALSE, 'text/html'),
			array('text/html,application/xml;q=0.9,*/*;q=0.8', array('text/plain', 'text/html', 'application/xml'), TRUE, 'text/html'),
			array('text/html,application/xml;q=0.9,*/*;q=0.8', array('text/plain', 'application/xml'), FALSE, 'application/xml'),
			array('text/html,application/xml;q=0.9,*/*;q=0.8', array('text/plain', 'application/xml'), TRUE, 'application/xml'),
			array('text/html,application/xml;q=0.9,*/*;q=0.8', array('text/plain'), FALSE, 'text/plain'),
			array('text/html,application/xml;q=0.9,*/*;q=0.8', array('text/plain'), TRUE, FALSE),
		);
	}

	/**
	 * @dataProvider preferred_accept_provider
	 * @test
	 */
	public function preferred_accept($accept_header, $types, $explicit, $expected)
	{
		$_SERVER['HTTP_ACCEPT'] = $accept_header;

		$this->assertEquals($expected, request::preferred_accept($types, $explicit));
	}

	public function accepts_charset_provider()
	{
		return array(
			array(NULL, NULL, array('*' => 1)),
			array(NULL, 'utf-8', TRUE),

			array('', NULL, array('*' => 1)),

			array('utf-8', NULL, array('utf-8' => 1)),
			array('utf-8', 'utf-8', TRUE),
			array('utf-8', 'iso-8859-1', TRUE),
			array('utf-8', 'gbk', FALSE),

			array('utf-8,*;q=0', NULL, array('utf-8' => 1, '*' => 0)),
			array('utf-8,*;q=0', 'utf-8', TRUE),
			array('utf-8,*;q=0', 'iso-8859-1', FALSE),

			array('ISO-8859-1,utf-8;q=0.8,*;q=0.7', NULL,
				array(
					'iso-8859-1' => 1,
					'utf-8' => 0.8,
					'*' => 0.7,
				)
			),
			array('ISO-8859-1,utf-8;q=0.8,*;q=0.7', 'gbk', TRUE),
		);
	}

	/**
	 * @dataProvider accepts_charset_provider
	 * @test
	 */
	public function accepts_charset($accept_header, $arg, $expected)
	{
		$_SERVER['HTTP_ACCEPT_CHARSET'] = $accept_header;

		$this->assertEquals($expected, request::accepts_charset($arg));
	}

	public function preferred_charset_provider()
	{
		return array(
			array(NULL, array(), FALSE),
			array(NULL, array('utf-8'), 'utf-8'),

			array('utf-8', array(), FALSE),
			array('utf-8', array('utf-8'), 'utf-8'),
			array('utf-8', array('iso-8859-1'), 'iso-8859-1'),
			array('utf-8', array('gbk'), FALSE),

			array('utf-8,*;q=0', array(), FALSE),
			array('utf-8,*;q=0', array('utf-8'), 'utf-8'),
			array('utf-8,*;q=0', array('iso-8859-1'), FALSE),

			array('ISO-8859-1,utf-8;q=0.8,*;q=0.7', array('iso-8859-1', 'utf-8', 'gbk'), 'iso-8859-1'),
			array('ISO-8859-1,utf-8;q=0.8,*;q=0.7', array('utf-8', 'gbk'), 'utf-8'),
			array('ISO-8859-1,utf-8;q=0.8,*;q=0.7', array('gbk'), 'gbk'),
		);
	}

	/**
	 * @dataProvider preferred_charset_provider
	 * @test
	 */
	public function preferred_charset($accept_header, $charsets, $expected)
	{
		$_SERVER['HTTP_ACCEPT_CHARSET'] = $accept_header;

		$this->assertEquals($expected, request::preferred_charset($charsets));
	}

	public function accepts_encoding_provider()
	{
		return array(
			array(NULL, NULL, array('*' => 1)),
			array(NULL, 'identity', TRUE),
			array(NULL, 'gzip', TRUE),

			array('', NULL, array('identity' => 1)),
			array('', 'identity', TRUE),
			array('', 'gzip', FALSE),

			array('gzip,deflate;q=0.8', NULL, array('gzip' => 1, 'deflate' => 0.8)),
			array('gzip,deflate;q=0.8', 'identity', TRUE),
			array('gzip,deflate;q=0.8', 'deflate', TRUE),
			array('gzip,deflate;q=0.8', 'gzip', TRUE),

			array('gzip,deflate;q=0', NULL, array('gzip' => 1, 'deflate' => 0)),
			array('gzip,deflate;q=0', 'identity', TRUE),
			array('gzip,deflate;q=0', 'deflate', FALSE),
			array('gzip,deflate;q=0', 'gzip', TRUE),
		);
	}

	/**
	 * @dataProvider accepts_encoding_provider
	 * @test
	 */
	public function accepts_encoding($accept_encoding_header, $arg, $expected)
	{
		$_SERVER['HTTP_ACCEPT_ENCODING'] = $accept_encoding_header;

		$this->assertEquals($expected, request::accepts_encoding($arg));
	}

	public function preferred_encoding_provider()
	{
		return array(
			array(NULL, array(), FALSE, FALSE),
			array(NULL, array(), TRUE, FALSE),
			array(NULL, array('identity'), FALSE, 'identity'),
			array(NULL, array('identity'), TRUE, FALSE),

			array('', array(), FALSE, FALSE),
			array('', array(), TRUE, FALSE),
			array('', array('identity'), FALSE, 'identity'),
			array('', array('identity'), TRUE, 'identity'),

			array('gzip,deflate;q=0.8', array('identity', 'gzip'), FALSE, 'identity'),
			array('gzip,deflate;q=0.8', array('identity', 'gzip'), TRUE, 'gzip'),
			array('gzip,deflate;q=0.8', array('gzip', 'identity'), FALSE, 'gzip'),
			array('gzip,deflate;q=0.8', array('gzip', 'identity'), TRUE, 'gzip'),

			array('gzip,deflate;q=0.8', array('deflate'), FALSE, 'deflate'),
			array('gzip,deflate;q=0.8', array('deflate'), TRUE, 'deflate'),

			array('gzip,deflate;q=0', array('deflate'), FALSE, FALSE),
			array('gzip,deflate;q=0', array('deflate'), TRUE, FALSE),
		);
	}

	/**
	 * @dataProvider preferred_encoding_provider
	 * @test
	 */
	public function preferred_encoding($accept_header, $encodings, $explicit, $expected)
	{
		$_SERVER['HTTP_ACCEPT_ENCODING'] = $accept_header;

		$this->assertEquals($expected, request::preferred_encoding($encodings, $explicit));
	}

	public function accepts_language_provider()
	{
		return array(
			array(NULL, NULL, array('*' => 1)),
			array(NULL, 'en', TRUE),

			array('', NULL, array('*' => 1)),

			array('en,fr;q=0', NULL,
				array('en' => array('*' => 1), 'fr' => array('*' => 0))
			),
			array('en,fr;q=0', 'en', TRUE),
			array('en,fr;q=0', 'fr', FALSE),
			array('en,fr;q=0', 'fr-ca', FALSE),

			array('fr-ca,en-us,en;q=0.5', NULL,
				array(
					'fr' => array('ca' => 1),
					'en' => array('us' => 1, '*' => 0.5),
				)
			),
			array('fr-ca,en-us,en;q=0.5', 'en', TRUE),
			array('fr-ca,en-us,en;q=0.5', 'fr', FALSE),
			array('fr-ca,en-us,en;q=0.5', 'fr-ca', TRUE),
		);
	}

	/**
	 * @dataProvider accepts_language_provider
	 * @test
	 */
	public function accepts_language($accept_language_header, $arg, $expected)
	{
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = $accept_language_header;

		$this->assertEquals($expected, request::accepts_language($arg));
	}

	public function preferred_language_provider()
	{
		return array(
			array(NULL, array(), FALSE, FALSE),
			array(NULL, array(), TRUE, FALSE),
			array(NULL, array('en'), FALSE, 'en'),
			array(NULL, array('en'), TRUE, FALSE),

			array('en,fr', array('en','fr'), FALSE, 'en'),
			array('en,fr', array('en-us','fr'), FALSE, 'en-us'),
			array('en,fr', array('en','fr-ca'), FALSE, 'en'),
			array('en,fr', array('en-us','fr-ca'), FALSE, 'en-us'),

			array('en,fr', array('fr','en'), FALSE, 'fr'),
			array('en,fr', array('fr-ca','en'), FALSE, 'fr-ca'),
			array('en,fr', array('fr','en-us'), FALSE, 'fr'),
			array('en,fr', array('fr-ca','en-us'), FALSE, 'fr-ca'),

			array('en,fr', array('en','fr'), TRUE, 'en'),
			array('en,fr', array('en-us','fr'), TRUE, 'fr'),
			array('en,fr', array('en','fr-ca'), TRUE, 'en'),
			array('en,fr', array('en-us','fr-ca'), TRUE, FALSE),

			array('en,fr;q=0.5', array('fr','en'), FALSE, 'en'),
			array('en,fr;q=0.5', array('fr-ca','en'), FALSE, 'en'),
			array('en,fr;q=0.5', array('fr','en-us'), FALSE, 'en-us'),
			array('en,fr;q=0.5', array('fr-ca','en-us'), FALSE, 'en-us'),

			array('en,fr;q=0.5', array('fr','en'), TRUE, 'en'),
			array('en,fr;q=0.5', array('fr-ca','en'), TRUE, 'en'),
			array('en,fr;q=0.5', array('fr','en-us'), TRUE, 'fr'),
			array('en,fr;q=0.5', array('fr-ca','en-us'), TRUE, FALSE),

			array('en,fr;q=0', array('fr','en'), TRUE, 'en'),
			array('en,fr;q=0', array('fr-ca','en'), TRUE, 'en'),
			array('en,fr;q=0', array('fr','en-us'), TRUE, FALSE),
			array('en,fr;q=0', array('fr-ca','en-us'), TRUE, FALSE),
		);
	}

	/**
	 * @dataProvider preferred_language_provider
	 * @test
	 */
	public function preferred_language($accept_header, $encodings, $explicit, $expected)
	{
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = $accept_header;

		$this->assertEquals($expected, request::preferred_language($encodings, $explicit));
	}
}


class Helper_Request_Test_Wrapper extends request
{
	public static function reset()
	{
		request::$accept_charsets = NULL;
		request::$accept_encodings = NULL;
		request::$accept_languages = NULL;
		request::$accept_types = NULL;
		request::$user_agent = NULL;
	}
}
