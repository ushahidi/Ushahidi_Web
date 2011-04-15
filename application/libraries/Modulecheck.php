<?php
class Modulecheck {

	public $Modules;
	
	//function parseModules() {
	function __construct()
	{
		ob_start(); // Stop output of the code and hold in buffer
		phpinfo(INFO_MODULES); // get loaded modules and their respective settings.
		$data = ob_get_contents(); // Get the buffer contents and store in $data variable
		ob_end_clean(); // Clear buffer
	
		$data = strip_tags($data,'<h2><th><td>'); // Keep only the items in the <h2>,<th> and <td> tags
		
		// Use regular expressions to filter out needed data
		// Replace everything in the <th> tags and put in <info> tags
		$data = preg_replace('/<th[^>]*>([^<]+)<\/th>/',"<info>\\1</info>",$data);
		
		// Replace everything in <td> tags and put in <info> tags
		$data = preg_replace('/<td[^>]*>([^<]+)<\/td>/',"<info>\\1</info>",$data);
		
		// Split the data into an array
		$vTmp = preg_split('/(<h2>[^<]+<\/h2>)/',$data,-1,PREG_SPLIT_DELIM_CAPTURE);
		$vModules = array();
		$count = count($vTmp);
		for ($i=1;$i<$count; $i+=2)
		{ // Loop through array and add 2 instead of 1
	
			// Check to make sure value is a module	
			if (preg_match('/<h2>([^<]+)<\/h2>/',$vTmp[$i],$vMat))
			{
	
				$moduleName = trim($vMat[1]); // Get the module name 
				$vTmp2 = explode("\n",$vTmp[$i+1]);
				foreach ($vTmp2 AS $vOne)
				{
					$vPat = '<info>([^<]+)<\/info>'; // Specify the pattern we created above
					$vPat3 = "/$vPat\s*$vPat\s*$vPat/"; // Pattern for 2 settings (Local and Master values)
					$vPat2 = "/$vPat\s*$vPat/"; // Pattern for 1 settings
					if (preg_match($vPat3,$vOne,$vMat))
					{ // This setting has a Local and Master value
						$vModules[$moduleName][trim($vMat[1])] = array(trim($vMat[2]),trim($vMat[3]));
					}
					elseif (preg_match($vPat2,$vOne,$vMat))
					{ // This setting only has a value
						$vModules[$moduleName][trim($vMat[1])] = trim($vMat[2]);
					}
				}
		
			}
		}
		$this->Modules = $vModules; // Store modules in Modules variable
	}
	
	
	// Quick check if module is loaded
	// Returns true if loaded, false if not
	public function isLoaded($moduleName)
	{
		if(isset($this->Modules[$moduleName]))
		{ 
			return true;
		}
		return false;
	} // End function isLoaded
	
	
	// Get a module setting
	// Can be a single setting by specifying $setting value or all settings by not specifying $setting value
	public function getModuleSetting($moduleName, $setting = '')
	{
		// check if module is loaded before continuing
		if($this->isLoaded($moduleName)==false)
		{
			return 'Module not loaded'; // Module not loaded so return error
		}
	
		if($this->Modules[$moduleName][$setting])
		{ // You requested an individual setting
			return $this->Modules[$moduleName][$setting];
		}
		elseif(empty($setting))
		{ // List all settings
			return $this->Modules[$moduleName];
		}
		// If setting specified and no value found return error
		return 'Setting not found';
	} // End function getModuleSetting
	
	
	// List all php modules installed with no settings
	public function listModules()
	{
		foreach($this->Modules as $moduleName=>$values)
		{ // Loop through modules
			// $moduleName is the key of $this->Modules, which is also module name
			$onlyModules[] = $moduleName;
		}
		return $onlyModules; // Return array of all module names
	} // End function listModules();
}
?>