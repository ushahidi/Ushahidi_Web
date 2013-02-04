<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Requirements library Unit Test
 *
 * @author 		Ushahidi Team
 * @package 	Ushahidi
 * @category 	Unit Tests
 * @copyright 	(c) 2008-2011 Ushahidi Inc <http://www.ushahidi.com>
 * @license 	For license information, see License.txt
 */
class Requirements_Test extends PHPUnit_Framework_TestCase {
	
	public function setUp()
	{
		
	}
	
	public function tearDown()
	{
		
	}
	
	function testExternalUrls() {
		$backend = Requirements::backend();
		$backend->set_combined_files_enabled(true);

		$backend->js('http://www.mydomain.com/test.js');
		$backend->js('https://www.mysecuredomain.com/test2.js');
		$backend->css('http://www.mydomain.com/test.css');
		$backend->css('https://www.mysecuredomain.com/test2.css');
		
		$html = $backend->render();
		
		$this->assertContains(
			'http://www.mydomain.com/test.js',
			$html,
			'Load external javascript URL'
		);
		$this->assertContains(
			'https://www.mysecuredomain.com/test2.js',
			$html,
			'Load external secure javascript URL'
		);
		$this->assertContains(
			'http://www.mydomain.com/test.css', 
			$html,
			'Load external CSS URL'
		);
		$this->assertContains(
			'https://www.mysecuredomain.com/test2.css', 
			$html,
			'Load external secure CSS URL'
		);
		
		// This should replace test.css above
		// @todo move to seperate test
		$backend->css('https://www.anything.com/test.css');
		$html = $backend->render();
		$this->assertContains(
			'https://www.anything.com/test.css', 
			$html,
			'Load external CSS URL'
		);
		$this->assertNotContains(
			'https://www.mydomain.com/test.css', 
			$html,
			'Load external CSS URL'
		);
	}

	protected function setupCombinedRequirements($backend) {
		$backend->clear();
		//$backend->setCombinedFilesFolder('assets');

		// clearing all previously generated requirements (just in case)
		$backend->clear_combined_files();
		$backend->delete_combined_files('RequirementsTest_bc.js');

		// require files normally (e.g. called from a FormField instance)
		$backend->js('tests/phpunit/data/RequirementsTest_a.js');
		$backend->js('tests/phpunit/data/RequirementsTest_b.js');
		$backend->js('tests/phpunit/data/RequirementsTest_c.js');

		// require two of those files as combined includes
		$backend->combine_files(
			'RequirementsTest_bc.js',
			array(
				'tests/phpunit/data/RequirementsTest_b.js',
				'tests/phpunit/data/RequirementsTest_c.js'
			)
		);
	}
	
	protected function setupCombinedNonrequiredRequirements($backend) {
			$backend->clear();
			//$backend->setCombinedFilesFolder('assets');
	
			// clearing all previously generated requirements (just in case)
			$backend->clear_combined_files();
			$backend->delete_combined_files('RequirementsTest_bc.js');
	
			// require files as combined includes
			$backend->combine_files(
				'RequirementsTest_bc.js',
				array(
					'data/forms/RequirementsTest_b.js',
					'data/forms/RequirementsTest_c.js'
				)
			);
		}

	function testCombinedJavascript() {
		$backend = new Requirements_Backend;
		$backend->set_combined_files_enabled(TRUE);
		//$backend->setCombinedFilesFolder('/media/uploads');

		$this->setupCombinedRequirements($backend);
		
		$combinedFilePath = DOCROOT . 'media/uploads/' . 'RequirementsTest_bc.js';

		$html = $backend->render();

		/* COMBINED JAVASCRIPT FILE IS INCLUDED IN HTML HEADER */
		$this->assertContains('media/uploads/RequirementsTest_bc.js', $html, 'combined javascript file is included in html header');
		
		/* COMBINED JAVASCRIPT FILE EXISTS */
		$this->assertFileExists($combinedFilePath, 'combined javascript file exists');
		
		/* COMBINED JAVASCRIPT HAS CORRECT CONTENT */
		$combinedFileContents = @file_get_contents($combinedFilePath);
		$this->assertContains("alert('b')", $combinedFileContents, 'combined javascript has correct content');
		$this->assertContains("alert('c')", $combinedFileContents, 'combined javascript has correct content');
		
		/* COMBINED FILES ARE NOT INCLUDED TWICE */
		$this->assertNotContains('RequirementsTest_b.js', $html, 'combined files are not included twice');
		$this->assertNotContains('RequirementsTest_c.js', $html, 'combined files are not included twice');
		
		/* NORMAL REQUIREMENTS ARE STILL INCLUDED */
		$this->assertContains('RequirementsTest_a.js', $html, 'normal requirements are still included');

		$backend->delete_combined_files('RequirementsTest_bc.js');
		
		// Then do it again, this time not requiring the files beforehand
		$backend = new Requirements_Backend;
		$backend->set_combined_files_enabled(true);
		$backend->setCombinedFilesFolder('assets');

		$this->setupCombinedNonrequiredRequirements($backend);
		
		$combinedFilePath = DOCROOT . 'media/uploads/' . 'RequirementsTest_bc.js';

		$html = $backend->render();

		/* COMBINED JAVASCRIPT FILE IS NOT INCLUDED IN HTML HEADER */
		$this->assertNotContains('media/uploads/RequirementsTest_bc.js', $html, 'combined javascript file is included in html header');
		
		/* COMBINED JAVASCRIPT FILE EXISTS */
		clearstatcache(); // needed to get accurate file_exists() results
		$this->assertFileNotExists($combinedFilePath, 'combined javascript file exists');
		
		/* COMBINED FILES ARE NOT INCLUDED TWICE */
		$this->assertNotContains('RequirementsTest_b.js', $html, 'combined files are not included twice');
		$this->assertNotContains('RequirementsTest_c.js', $html, 'combined files are not included twice');

		$backend->delete_combined_files('RequirementsTest_bc.js');
	}
	
	function testBlockedCombinedJavascript() {
		$backend = new Requirements_Backend;
		$backend->set_combined_files_enabled(true);
		$backend->setCombinedFilesFolder('assets');
		$combinedFilePath = DOCROOT . '/media/uploads/' . 'RequirementsTest_bc.js';

		/* BLOCKED COMBINED FILES ARE NOT INCLUDED */
		$this->setupCombinedRequirements($backend);
		$backend->block('RequirementsTest_bc.js');
		$backend->delete_combined_files('RequirementsTest_bc.js');

		clearstatcache(); // needed to get accurate file_exists() results
		$html = $backend->render();

		$this->assertNotContains('RequirementsTest_bc.js', $html, 'blocked combined files are not included ');
		$backend->unblock('RequirementsTest_bc.js');

		/* BLOCKED UNCOMBINED FILES ARE NOT INCLUDED */
		$this->setupCombinedRequirements($backend);
		$backend->block('tests/phpunit/data/RequirementsTest_b.js');
		$backend->delete_combined_files('RequirementsTest_bc.js');
		clearstatcache(); // needed to get accurate file_exists() results
		$html = $backend->render();
		$combinedFileContents = @file_get_contents($combinedFilePath);
		$this->assertNotContains("alert('b')", (string)$combinedFileContents, 'blocked uncombined files are not included');
		$backend->unblock('RequirementsTest_b.js');
		
		/* A SINGLE FILE CAN'T BE INCLUDED IN TWO COMBINED FILES */
		$this->setupCombinedRequirements($backend);
		clearstatcache(); // needed to get accurate file_exists() results

		// This throws a notice-level error, so we prefix with @
		@$backend->combine_files(
			'RequirementsTest_ac.js',
			array(
				'tests/phpunit/data/RequirementsTest_a.js',
				'tests/phpunit/data/RequirementsTest_c.js'
			)
		);

		$combinedFiles = $backend->get_combine_files();

		$this->assertEquals(
			array_keys($combinedFiles['js']),
			array('RequirementsTest_bc.js'),
			"A single file can't be included in two combined files"
		);
		
		$backend->delete_combined_files('RequirementsTest_bc.js');
	}
	
	function testArgsInUrls() {
		$backend = new Requirements_Backend;
		$backend->set_combined_files_enabled(TRUE);
		$backend->set_suffix_requirements(TRUE);

		$backend->js('tests/phpunit/data/RequirementsTest_a.js?test=1&test=2&test=3');
		$backend->css('tests/phpunit/data/RequirementsTest_a.css?test=1&test=2&test=3');
		$backend->delete_combined_files('RequirementsTest_bc.js');

		$html = $backend->render();

		/* Javascript has correct path */
		$this->assertRegexp('#src=".*\/RequirementsTest_a\.js\?m=\d\d+&test=1&test=2&test=3#', $html, 'javascript has correct path and mtime suffix');

		/* CSS has correct path */
		$this->assertRegexp('#href=".*\/RequirementsTest_a\.css\?m=\d\d+&test=1&test=2&test=3#', $html, 'css has correct path and mtime suffix');
		
		// Testing again without mtime suffix
		$backend->set_suffix_requirements(FALSE);

		$html = $backend->render();

		/* Javascript has correct path */
		$this->assertRegexp('#src=".*\/RequirementsTest_a\.js\?test=1&test=2&test=3#', $html, 'javascript has correct path');

		/* CSS has correct path */
		$this->assertRegexp('#href=".*\/RequirementsTest_a\.css\?test=1&test=2&test=3#', $html, 'css has correct path');
	}
	
	function testRequirementsBackend() {
		$backend = new Requirements_Backend();
		$backend->js('tests/phpunit/data/a.js');
		
		$this->assertCount(1, $backend->get_js(), "There should be only 1 file included in required javascript.");
		$this->assertContains('tests/phpunit/data/a.js', $backend->get_js(), "/tests/phpunit/data/a.js should be included in required javascript.");
		
		$backend->js('tests/phpunit/data/b.js');
		$this->assertCount(2, $backend->get_js(), "There should be 2 files included in required javascript.");
		
		$backend->block('tests/phpunit/data/a.js');
		$this->assertCount(1, $backend->get_js(), "There should be only 1 file included in required javascript.");
		$this->assertNotContains('tests/phpunit/data/a.js', $backend->get_js(), "/tests/phpunit/data/a.js should not be included in required javascript after it has been blocked.");
		$this->assertContains('tests/phpunit/data/b.js', $backend->get_js(), "/tests/phpunit/data/b.js should be included in required javascript.");
		
		$backend->css('tests/phpunit/data/a.css');
		$this->assertCount(1, $backend->get_css(), "There should be only 1 file included in required css.");
		$this->assertArrayHasKey('a.css', $backend->get_css(), "/tests/phpunit/data/a.css should be in required css.");
		$this->assertContains(array('file' => 'tests/phpunit/data/a.css', 'media' => null), $backend->get_css(), "/tests/phpunit/data/a.css should be in required css.");
		
		$backend->block('tests/phpunit/data/a.css');
		$this->assertCount(0, $backend->get_css(), "There should be nothing in required css after file has been blocked.");
		
		// Test unblock_all()
		$backend->unblock_all();
		$this->assertCount(2, $backend->get_js(), "There should be only 2 files included in required css.");
		$this->assertCount(1, $backend->get_css(), "There should be only 1 file included in required javascript.");
		
		// Testing clear()
		$backend->js('tests/phpunit/data/c.css');
		$backend->clear();
		$this->assertCount(0, $backend->get_css(), "There should be nothing in required css after requirements cleared.");
		$this->assertCount(0, $backend->get_js(), "There should be nothing in required js after requirements cleared.");
		
		// Testing js block by id
		$backend->js('tests/phpunit/data/a.js');
		$backend->block('a.js');
		$this->assertCount(0, $backend->get_js(), "There should be nothing in required js after file has be blocked.");
		
		// Testing css block by id
		$backend->css('tests/phpunit/data/a.css');
		$backend->block('a.css');
		$this->assertCount(0, $backend->get_css(), "There should be nothing in required css after file has been blocked.");
		
		// Testing unblock
		$backend->unblock('a.js');
		$this->assertCount(1, $backend->get_js(), "There should be only 1 file included in required javascript.");
	}
	
	function testBlockedInRender() {
		$backend = new Requirements_Backend();
		$backend->js('tests/phpunit/data/RequirementsTest_a.js');
		$backend->js('tests/phpunit/data/RequirementsTest_a.css');
		$backend->js('tests/phpunit/data/RequirementsTest_b.js');
		$backend->js('tests/phpunit/data/RequirementsTest_b.css');
		$backend->js('tests/phpunit/data/RequirementsTest_c.js');
		$backend->js('tests/phpunit/data/RequirementsTest_c.css');
		
		$backend->block('tests/phpunit/data/RequirementsTest_a.js');
		$backend->block('tests/phpunit/data/RequirementsTest_a.css');
		$backend->block('RequirementsTest_b.js');
		$backend->block('RequirementsTest_b.css');
		
		$html = $backend->render();
		$this->assertNotContains('tests/phpunit/data/RequirementsTest_a.js', $html, 'RequirementsTest_a.js was blocked and should not appear in rendered HTML');
		$this->assertNotContains('tests/phpunit/data/RequirementsTest_a.css', $html, 'RequirementsTest_a.css was blocked and should not appear in rendered HTML');
		$this->assertNotContains('tests/phpunit/data/RequirementsTest_b.js', $html, 'RequirementsTest_b.js was blocked and should not appear in rendered HTML');
		$this->assertNotContains('tests/phpunit/data/RequirementsTest_b.css', $html, 'RequirementsTest_b.css was blocked and should not appear in rendered HTML');
		$this->assertContains('tests/phpunit/data/RequirementsTest_c.js', $html, 'RequirementsTest_c.js should appear in rendered HTML');
		$this->assertContains('tests/phpunit/data/RequirementsTest_c.css', $html, 'RequirementsTest_c.css should appear in rendered HTML');
	}
}

