<?php defined('SYSPATH') OR die('No direct access allowed.');

class notifications_Core 
{
	
	public static function notify_admins($message)	
	{
		
		// Don't show the exceptions for this operation to the user. Log them
		// instead
		try
		{ 
			$settings = kohana::config('settings');
			$from = $settings['site_email'];
			$subject = $settings['site_name']."
				".Kohana::lang('users.notification');
			
			$admins = ORM::factory('user')->where('can_notify', 1)->find_all();

			foreach($admins as $admin) 
			{
				$address = $admin->email;

				if ( ! email::send($address, $from, $subject, $message, TRUE))
				{
					Kohana::log('error', "email to $address could not be sent");
				}
			}
		}
		catch (Exception $e)
		{
			Kohana::log('error', "An exception occured ".$e->__toString());

		}

	}
}
