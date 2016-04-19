<?php
/*
 * Quick and dirty script to find unused translation strings
 */

function check_lang($lang, $lang_key)
{
	foreach ($lang as $key => $string)
	{
		if (is_array($string))
		{
			check_lang($string, $lang_key.'.'.$key);
		}
		else
		{
			$search_key = "$lang_key.$key";
			exec("grep \"$search_key\" ../../../ -R", $out, $ret);
			if ($ret == 1)
				echo $search_key . " not found\n";
			//exit();
		}
	}
}

$error_files = array(
	'alerts','report','bug','category','comments','contact','settings','auth','layer','message','reporters','page','mhi','sharing','roles','core','feedback','feeds','form',
);

$files = scandir('../en_US');
foreach($files as $k => $file)
{
	if ($file == '.' OR $file == '..')
		continue;
	if (in_array(str_replace('.php','',$file), $error_files))
		continue;
	
	if (strpos($file,'.php') !== FALSE)
	{
		$lang_key = str_replace('.php','',$file);
		include ('../en_US/'.$file);
		check_lang($lang, $lang_key);
	}
}