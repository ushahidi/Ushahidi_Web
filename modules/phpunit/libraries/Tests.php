<?php

class Tests
{
	public static function whitelist()
	{
		$folders = Kohana::config('phpunit.whitelist_folders');

		foreach ($folders as $folder)
		{
			$files = Kohana::list_files($folder, TRUE);
			foreach ($files as $file)
			{
				if (is_file($file))
				{
					if ($file == __FILE__)
					{
						continue;
					}
					else
					{
						PHPUnit_Util_Filter::addFileToWhitelist($file);
					}
				}
			}
		}
	}

	public static function suite()
	{
		if ( ! class_exists('Kohana'))
		{
			throw new Exception('Please include the kohana bootstrap file.');
		}

		$files = Kohana::list_files('tests');

		// Files to include in code coverage
		self::whitelist();

		$suite = new PHPUnit_Framework_TestSuite();

		$folders = Kohana::config('phpunit.filter_folders');

		foreach ($folders as $folder)
		{
			PHPUnit_Util_Filter::addDirectoryToFilter($folder);
		}

		self::addTests($suite, $files);

		return $suite;
	}

	public static function addTests($suite, $files)
	{
		foreach($files as $file)
		{
			if(is_array($file))
			{
				self::addTests($suite, $file);
			}
			else
			{
				if(is_file($file))
				{
					// The default PHPUnit TestCase extension
					if ( ! strpos($file, 'TestCase'.EXT))
					{
						$suite->addTestFile($file);
					}
					else
					{
						require_once($file);
					}
					PHPUnit_Util_Filter::addFileToFilter($file);
				}
			}
		}
	}
}
