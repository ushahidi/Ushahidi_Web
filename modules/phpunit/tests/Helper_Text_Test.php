<?php
/**
 * Text Helper Unit Tests
 *
 * @package  Core
 * @author   Peter Aba
 * @group    core
 * @group    core.helpers
 * @group    core.helpers.text
 */
class Helper_Text_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * DataProvider for the text::limit_words() test
	 */
	public function limit_words_provider()
	{
		return array(
				//wiki example
				array('The rain in Spain falls mainly in the plain.', 4, '&nbsp;', 'The rain in Spain&nbsp;'),

				//stress preparation
				array('  ', 100, NULL, '  '),
				array('The rain in Spain falls mainly in the plain.', -2, NULL, '&#8230;'),
				array('The rain in Spain falls mainly in the plain.', -2, 'end.', 'end.'),
				
				//stress regular expression
				array('The rain in Spain falls mainly in the plain.', 9, NULL, 'The rain in Spain falls mainly in the plain.'),
				array('Tabbed 		sentence.', 2, NULL, 'Tabbed 		sentence.'),
				array("Sentence with \nnewline\ncharacters.", 4, NULL, "Sentence with \nnewline\ncharacters."),
				array("UTF-8 chars: Å‘Å±.", 4, NULL, 'UTF-8 chars: Å‘Å±.'),
				array("The special regular expression characters are: . \ + * ? [ ^ ] $ ( ) { } = ! < > | : -", 100, NULL, 'The special regular expression characters are: . \ + * ? [ ^ ] $ ( ) { } = ! < > | : -'),
				
				//stress the return expression
				array('The rain in Spain falls mainly in the plain.', 100, '&nbsp;', 'The rain in Spain falls mainly in the plain.'),
				array('		The rain in Spain 		falls mainly in the plain', 4, '?', '		The rain in Spain?'),
			);
	}
	
	/**
	 * Tests the text::limit_words() function.
	 * @dataProvider limit_words_provider
	 * @group core.helpers.text.limit_words
	 * @test
	 */
	public function limit_words($str, $limit, $end_char, $expected_result)
	{
		$result = text::limit_words($str, $limit, $end_char);
		$this->assertEquals($expected_result, $result);
	}
	
	
	
	/**
	 * DataProvider for the text::limit_chars() test
	 */
	public function limit_chars_provider()
	{
		return array(
				//wiki example
				array('The rain in Spain falls mainly in the plain.', 4,  '&amp;nbsp;', FALSE, TRUE, 'The&amp;nbsp;'),
				
				//stress preparation
				array('  ', 100, NULL, TRUE, '  '),
				array('The rain in Spain falls mainly in the plain.', -2, NULL, TRUE, '&#8230;'),
				array('The rain in Spain falls mainly in the plain.', -2, 'end.', TRUE, 'end.'),
				array('The rain in Spain falls mainly in the plain.', 5, NULL, TRUE, 'The rain&#8230;'),
				array('The rain in Spain falls mainly in the plain.', 5, NULL, FALSE, 'The r&#8230;'),
				
				//stress regular expression
				array('The rain in Spain falls mainly in the plain.', 44, NULL, TRUE, 'The rain in Spain falls mainly in the plain.'),
				array('Tabbed 		sentence.', 100, NULL, TRUE, 'Tabbed 		sentence.'),
				array("Sentence with \nnewline\ncharacters.", 100, NULL, TRUE, "Sentence with \nnewline\ncharacters."),
				array("UTF-8 chars: Å‘Å±.", 100, NULL, TRUE, 'UTF-8 chars: Å‘Å±.'),
				array("UTF-8 chars: Å‘Å±.", 14, NULL, TRUE, 'UTF-8 chars: Å‘Å±.'),
				array('UTF-8 chars: Å‘Å±.', 14, NULL, FALSE, 'UTF-8 chars: Å&#8230;'),
				array("The special regular expression characters are: . \ + * ? [ ^ ] $ ( ) { } = ! < > | : -", 100, NULL, TRUE, 'The special regular expression characters are: . \ + * ? [ ^ ] $ ( ) { } = ! < > | : -'),
				
				//stress the return expression
				array('The rain in Spain falls mainly in the plain.', 100, '&nbsp;', TRUE, 'The rain in Spain falls mainly in the plain.'),
				array('		The rain in Spain 		falls mainly in the plain.', 22, '?', TRUE, '		The rain in Spain?'),
			);
	}
	
	/**
	 * Tests the text::limit_chars() function.
	 * @dataProvider limit_chars_provider
	 * @group core.helpers.text.limit_chars
	 * @test
	 */
	public function limit_chars($str, $limit, $end_char, $preserve_words, $expected_result)
	{
		$result = text::limit_chars($str, $limit, $end_char, $preserve_words);
		$this->assertEquals($expected_result, $result);
	}
	
	

	
	
	/**
	 * DataProvider for the text::alternate() test
	 */
	public function alternate_provider()
	{
		return array(
				//wiki example
				array('1','2','boom', 5, '12boom12'),
				
				//more
				array(10, ''),
				array('This','is', 'a', 'basic', 'test', 2, 'Thisis'),
				array('This','is', 'a', 'basic', 'test', 6, 'ThisisabasictestThis'),
			);
	}
	
	/**
	 * Tests the text::alternate() function.
	 * @dataProvider alternate_provider
	 * @group core.helpers.text.alternate
	 * @test
	 */
	public function alternate()
	{
		$args = func_get_args();
		$expected_result = array_pop($args);
		$alternate_num = array_pop($args);
		
		$result = '';
		for ($i=0; $i<$alternate_num; $i++) $result .= call_user_func_array('text::alternate', $args);
		
		$this->assertEquals($expected_result, $result);
		
		//We have to reset alternate manually!!!
		text::alternate();
	}
	
	

	
	
	/**
	 * DataProvider for the text::random() test
	 */
	public function random_provider()
	{
		return array(
				array('alnum'),
				array('alpha'),
				array('hexdec', 7),
				array('numeric', 9),
				array('nozero', 13),
				array('distinct', 11),
				array('abcDEF13579', 6),
				//array('Å‘Å±Ã¡Ã©-+!%', 23),		// As of PHP 5.3, bug #47229 has been fixed and preg_quote *will* escape a hyphen (-).
				array('Å‘Å±Ã¡Ã©+!%-', 23),
				array(FALSE, 6),
				array(array(), 6),
			);
	}
	
	/**
	 * Tests the text::random() function.
	 * @dataProvider random_provider
	 * @group core.helpers.text.random
	 * @test
	 */
	public function random($type, $length=8)
	{
		$this->markTestIncomplete('Test for PHP 5.3 bug needs to be counted, Kohana is still supporting 5.2');
		$result = text::random($type, $length);
		
		if ((string) $type) 
		{
			// Checking length
			$this->assertEquals(utf8::strlen($result), $length);
	
			$pool = '';
			switch ($type)
			{
				case 'alnum':
					$this->assertTrue(valid::alpha_numeric($result));
				break;
				case 'alpha':
					$this->assertTrue(valid::alpha($result));
				break;
				case 'numeric':
					$this->assertTrue(valid::numeric($result));
				break;
				case 'nozero':
					$this->assertTrue(is_numeric($result));
				break;
				case 'hexdec':
					$pool = '0123456789abcdef';
				break;
				case 'distinct':
					$pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
				break;
				default:
					$pool = (string) $type;
			}
			
			if ($pool) 
			{
				if (preg_match('/['.preg_quote((string) $pool, '/').']*/u', $result, $match))
					$this->assertEquals($match[0], $result);
				else $this->assertTrue(FALSE);				
			}
		}
		else
		{
			// Checking length
			$this->assertEquals($result, '');
		}
	}
	

	
	/**
	 * DataProvider for the text::reduce_slashes() test
	 */
	public function reduce_slashes_provider()
	{
		return array(
				//wiki example
				array('path/to//something', 'path/to/something'),
				
				//stress regex
				array('http://www.kohanaphp.com/lovely//framework////that//is//', 'http://www.kohanaphp.com/lovely/framework/that/is/'),
			);
	}
	
	/**
	 * Tests the text::reduce_slashes() function.
	 * @dataProvider reduce_slashes_provider
	 * @group core.helpers.text.reduce_slashes
	 * @test
	 */
	public function reduce_slashes($str, $expected_result)
	{
		$result = text::reduce_slashes($str);
		$this->assertEquals($expected_result, $result);
	}
	

	
	/**
	 * DataProvider for the text::censor() test
	 */
	public function censor_provider()
	{
		return array(
				//wiki example
				array('The income tax is a three letter word, but telemarketers are scum.', array('tax', 'scum'), '*', TRUE, 'The income *** is a three letter word, but telemarketers are ****.'),
				
				//more
				array('Tax is a bad word, but taxi isn\'t.', array('tax'), '*', FALSE, '*** is a bad word, but taxi isn\'t.'),
				array('Tax, taxi, and post-tax are censored.', array('tax'), '*', TRUE, '***, ***i, and post-*** are censored.'),
			);
	}
	
	/**
	 * Tests the text::censor() function.
	 * @dataProvider censor_provider
	 * @group core.helpers.text.censor
	 * @test
	 */
	public function censor($str, $badwords, $replacement = '#', $replace_partial_words = FALSE, $expected_result)
	{
		$result = text::censor($str, $badwords, $replacement, $replace_partial_words);
		$this->assertEquals($expected_result, $result);
	}
	

	
	/**
	 * Thanks to "drantin" for the examples :)
	 * DataProvider for the text::similar() test
	 */
	public function similar_provider()
	{
		return array(
				array(array('blast','blastenhopf'), 'blast'),
				array(array('blast','blastenhopf','blatherscypes'), 'bla'),
			);
	}
	
	/**
	 * Tests the text::similar() function.
	 * @dataProvider similar_provider
	 * @group core.helpers.text.similar
	 * @test
	 */
	public function similar($words, $expected_result)
	{
		$result = text::similar($words);
		$this->assertEquals($expected_result, $result);
	}
	
	/**
	 * DataProvider for the text::auto_link_urls() test
	 */
	public function auto_link_urls_provider()
	{
		return array(
				array(
					'Visit www.example.com!',
					'Visit '.html::anchor('http://www.example.com', 'www.example.com').'!'),
				array(
					'Visiting http://www.example.com.',
					'Visiting '.html::anchor('http://www.example.com').'.'),
				array(
					'ftps://www.example.com/ is a secured ftp server.',
					html::anchor('ftps://www.example.com/').' is a secured ftp server.'),
				array(
					html::anchor('http://www.example.com').' remains the same.',
					html::anchor('http://www.example.com').' remains the same.'),
			);
	}
	
	/**
	 * Tests the text::auto_link_urls() function.
	 * @dataProvider auto_link_urls_provider
	 * @group core.helpers.text.auto_link_urls
	 * @test
	 */
	public function auto_link_urls($text, $expected_result)
	{
		$result = text::auto_link_urls($text);
		$this->assertEquals($expected_result, $result);
	}
	

	
	/**
	 * DataProvider for the text::auto_link_emails() test
	 */
	public function auto_link_emails_provider()
	{
		return array(
				array('My primary email address is example@example.com, example2@example.com is not.', 2),
				array('My primary email address is <a href="mailto:example@example.com">example@example.com</a>.', 0),
			);
	}
	
	/**
	 * Tests the text::auto_link_emails() function.
	 * @dataProvider auto_link_emails_provider
	 * @group core.helpers.text.auto_link_emails
	 * @test
	 */
	public function auto_link_emails($text, $emails_count)
	{
		$result = text::auto_link_emails($text);
		
		$this->assertEquals(
			preg_match_all("/<a href=\"&#109;&#097;&#105;&#108;&#116;&#111;&#058;(.*)\">(\\1)<\/a>/U", $result, $matches),
			$emails_count);
	}
	

	
	/**
	 * DataProvider for the text::auto_p() test
	 */
	public function auto_p_provider()
	{
		return array(
				//stress preparation
				array('		', ''),
				array("line one.\r\nline two.\rline three.\nline four.", "<p>line one.<br />\nline two.<br />\nline three.<br />\nline four.</p>"),
				
				//trim whitespace on each line
				array("line one.		\n  line			two   .		", "<p>line one.<br />\nline			two   .</p>"),
				
				//stress regular expressions
				array("\ + * ? [ ^ ] $ ( ) { } = ! < > | : - \ + * ? [ ^ ] $ ( ) { } = ! < > | : -", "<p>\ + * ? [ ^ ] $ ( ) { } = ! < > | : - \ + * ? [ ^ ] $ ( ) { } = ! < > | : -</p>"),
				array("line one.<br />\nline two.", "<p>line one.<br /><br />\nline two.</p>"),
				array("<p>line one.\nline two.</p>", "<p>line one.<br />\nline two.</p>"),
				array("line one.\n<div>line three.</div>", "<p>line one.</p>\n\n<div>line three.</div>"),
				array("line one.\n<div>line three.\nline four.</div>\nline six.", "<p>line one.</p>\n\n<div>line three.<br />\nline four.</div>\n\n<p>line six.</p>"),
				
			);
	}
	
	/**
	 * Tests the text::auto_p() function.
	 * @dataProvider auto_p_provider
	 * @group core.helpers.text.auto_p
	 * @test
	 */
	public function auto_p($str, $expected_result)
	{
		$result = text::auto_p($str);
		$this->assertEquals($expected_result, $result);
	}
	

	
	/**
	 * DataProvider for the text::bytes() test
	 */
	public function bytes_provider()
	{
		return array(
				//wiki examples
				array('2048', NULL, NULL, TRUE, '2.05 kB'),
				array('4194304', 'kB', NULL, TRUE, '4194.30 kB'),
				array('4194304', 'GiB', NULL, TRUE, '0.00 GiB'),
				array('4194304',NULL, NULL, FALSE, '4.00 MiB'),
				
				//more
				array('4194600',NULL, '%01.4f (%s)', FALSE, '4.0003 (MiB)'),
				array('4194600','mph', '%01.4f (%s)', FALSE, '4.0003 (MiB)'),
			);
	}
	
	/**
	 * Tests the text::bytes() function.
	 * @dataProvider bytes_provider
	 * @group core.helpers.text.bytes
	 * @test
	 */
	public function bytes($bytes, $force_unit, $format, $si, $expected_result)
	{
		$result = text::bytes($bytes, $force_unit, $format, $si);
		$this->assertEquals($expected_result, $result);
	}
	

	
	/**
	 * DataProvider for the text::widont() test
	 */
	public function widont_provider()
	{
		return array(
				//wiki example
				array(
					'Returns a string without widow words by inserting a non-breaking space between the last two words.',
					'Returns a string without widow words by inserting a non-breaking space between the last two&nbsp;words.'),
				
				//stress preparation
				array(
					'	Rtrimmed paragraph.	',
					'	Rtrimmed&nbsp;paragraph.',
					)
			);
	}
	
	/**
	 * Tests the text::widont() function.
	 * @dataProvider widont_provider
	 * @group core.helpers.text.widont
	 * @test
	 */
	public function widont($str, $expected_result)
	{
		$result = text::widont($str);
		$this->assertEquals($expected_result, $result);
	}
}
