<?php defined('SYSPATH') OR die('No direct access allowed.');

class notifications_Core 
{
	
	public function notify_admins($subject = NULL, $message = NULL)	
	{
		
		// Don't show the exceptions for this operation to the user. Log them
		// instead
		try
		{
			if ($subject && $message)
			{
				$settings = kohana::config('settings');
				$from = array();
					$from[] = $settings['site_email'];
					$from[] = $settings['site_name'];
				$users = ORM::factory('user')->where('notify', 1)->find_all();

				foreach($users as $user) 
				{
					if ($user->has(ORM::factory('role', 'admin')))
					{
						$address = $user->email;
						
						$message .= "\n\n\n\n~~~~~~~~~~~~\n".Kohana::lang('notifications.admin_footer')
							."\n".url::base()
							."\n\n".Kohana::lang('notifications.admin_login_url')
							."\n".url::base()."admin";
												
						if ( ! email::send($address, $from, $subject, $message, FALSE))
						{
							Kohana::log('error', "email to $address could not be sent");
						}
					}
				}
			}
			else
			{
				Kohana::log('error', "email to $address could not be sent
				 - Missing Subject or Message");
			}
		}
		catch (Exception $e)
		{
			Kohana::log('error', "An exception occured ".$e->__toString());

		}

	}
}
