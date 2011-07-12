<?php
/**
 * Modulecheck
 *
 * This library is used to check on PHP modules installed. Users are notified of modules required
 * during installation of their deployment.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 *
 */
class Modulecheck {
	/**
	 * PHP Modules
	 * @var array
	 */
	public $Modules;
	
	function __construct()
	{
		// Stop output of the code and hold in buffer
		ob_start();
		// Get loaded modules and their respective settings
		phpinfo(INFO_MODULES); 
		// Get the buffer contents and store in $data variable
		$data = ob_get_contents();
		// Clear buffer 
		ob_end_clean(); 
		// Keep only the items in the <h2>,<th> and <td> tags
		$data = strip_tags($data,'<h2><th><td>'); 
		
		// Use regular expressions to filter out needed data
		// Replace everything in the <th> tags and put in <info> tags
		$data = preg_replace('/<th[^>]*>([^<]+)<\/th>/',"<info>\\1</info>",$data);
		
		// Replace everything in <td> tags and put in <info> tags
		$data = preg_replace('/<td[^>]*>([^<]+)<\/td>/',"<info>\\1</info>",$data);
		
		// Split the data into an array
		$vTmp = preg_split('/(<h2>[^<]+<\/h2>)/',$data,-1,PREG_SPLIT_DELIM_CAPTURE);
		$vModules = array();
		$count = count($vTmp);
		// Loop through array and add 2 instead of 1
		for ($i=1;$i<$count; $i+=2)
		{ 
			// Check to make sure value is a module	
			if (preg_match('/<h2>([^<]+)<\/h2>/',$vTmp[$i],$vMat))
			{
				// Get the module name
				$moduleName = trim($vMat[1]);  
				$vTmp2 = explode("\n",$vTmp[$i+1]);
				foreach ($vTmp2 AS $vOne)
				{
					// Specify the pattern we created above
					$vPat = '<info>([^<]+)<\/info>'; 
					// Pattern for 2 settings (Local and Master values)
					$vPat3 = "/$vPat\s*$vPat\s*$vPat/";
					// Pattern for 1 settings 
					$vPat2 = "/$vPat\s*$vPat/";
					// This setting has a Local and Master value 
					if (preg_match($vPat3,$vOne,$vMat))
					{ 
						$vModules[$moduleName][trim($vMat[1])] = array(trim($vMat[2]),trim($vMat[3]));
					}
					// This setting only has a value
					elseif (preg_match($vPat2,$vOne,$vMat))
					{ 
						$vModules[$moduleName][trim($vMat[1])] = trim($vMat[2]);
					}
				}
			}
		}
		// Store modules in Modules variable
		$this->Modules = $vModules; 
	}
	
	/**
	 * Quick check if module is loaded. Returns true if loaded, false if not
	 * @param string $moduleName
	 * @return bool
	 */
	public function isLoaded($moduleName)
	{
		if (isset($this->Modules[$moduleName]))
		{ 
			return true;
		}
		return false;
	} 
	
	/**
	 * Get a module setting
	 *
	 * Can be a single setting by specifying $setting value or all settings by not specifying $setting value
	 * @param string $moduleName
	 * @param string $setting
	 * @return string
	 */
	public function getModuleSetting($moduleName, $setting = '')
	{
		// check if module is loaded before continuing
		if($this->isLoaded($moduleName)==false)
		{
			// Module not loaded so return error
			return 'Module not loaded'; 
		}
	
		if($this->Modules[$moduleName][$setting])
		{ 
			// You requested an individual setting
			return $this->Modules[$moduleName][$setting];
		}
		elseif(empty($setting))
		{ 
			// List all settings
			return $this->Modules[$moduleName];
		}
		// If setting specified and no value found return error
		return 'Setting not found';
	} 
	
	/**
	 * List all php modules installed with no settings
	 * @param array
	 * @return array
	 */
	public function listModules()
	{
		// Loop through modules
		foreach($this->Modules as $moduleName=>$values)
		{ 
			// $moduleName is the key of $this->Modules, which is also module name
			$onlyModules[] = $moduleName;
		}
		// Return array of all module names
		return $onlyModules; 
	} 
}
?>