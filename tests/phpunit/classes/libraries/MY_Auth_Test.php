<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Auth library Unit Test
 *
 * @author 		Ushahidi Team
 * @package 	Ushahidi
 * @category 	Unit Tests
 * @copyright 	(c) 2008-2011 Ushahidi Inc <http://www.ushahidi.com>
 * @license 	For license information, see License.txt
 */
class MY_Auth_Test extends PHPUnit_Framework_TestCase {
	
	public function setUp()
	{
		$_SESSION = array();
		
		// create test permission
		$permission = ORM::factory('permission');
		$permission->name = 'phpunit_access';
		$permission->id = 99999;
		$permission->save();
		
		// create test role
		$role = ORM::factory('role');
		$role->name = 'phpunit';
		$role->id = 99999;
		$role->add($permission);
		$role->save();
		
		// Custom admin role
		$role = ORM::factory('role');
		$role->name = 'phpunit_admin';
		$role->id = 99998;
		$role->add(ORM::factory('permission','admin_ui'));
		$role->save();
	}
	
	public function tearDown()
	{
		//delete fake roles & permissions
		ORM::factory('permission',99999)->delete();
		ORM::factory('role',99999)->delete();
		ORM::factory('role',99998)->delete();
	}
	
	public function testPermissions()
	{
		// not logged in: return false
		$this->assertFalse( Auth::instance()->has_permission('admin_ui') );
		
		// Login role checkin permission
		$user = new User_Model();
		$user->add(ORM::factory('role','login'));
		$this->assertTrue( Auth::instance()->has_permission('checkin', $user) );
		
		// member: return false
		$user = new User_Model();
		$user->add(ORM::factory('role','member'));
		$this->assertFalse( Auth::instance()->has_permission('admin_ui', $user) );
		$this->assertTrue( Auth::instance()->has_permission('member_ui', $user) );
		
		// admin: return true
		$user = new User_Model();
		$user->add(ORM::factory('role','admin'));
		$this->assertTrue( Auth::instance()->has_permission('admin_ui', $user) );
		
		// superadmin: return false
		$user = new User_Model();
		$user->add(ORM::factory('role','superadmin'));
		$this->assertTrue( Auth::instance()->has_permission('admin_ui', $user) );
		$this->assertTrue( Auth::instance()->has_permission('any_permission', $user) );
		
		// Test custom role/permission
		$user = new User_Model();
		$user->add(ORM::factory('role','phpunit'));
		$this->assertTrue( Auth::instance()->has_permission('phpunit_access', $user) );
		$this->assertFalse( Auth::instance()->has_permission('checkin', $user) );
		
	}
	
	public function testAdminAccess()
	{
		// not logged in: return false
		$this->assertFalse( Auth::instance()->admin_access() );
		
		// Login role : return false
		$user = new User_Model();
		$user->add(ORM::factory('role','login'));
		$this->assertFalse( Auth::instance()->admin_access($user) );
		
		// member: return false
		$user = new User_Model();
		$user->add(ORM::factory('role','member'));
		$this->assertFalse( Auth::instance()->admin_access($user) );
		
		// admin: return true
		$user = new User_Model();
		$user->add(ORM::factory('role','admin'));
		$this->assertTrue( Auth::instance()->admin_access($user) );
		
		// superadmin: return false
		$user = new User_Model();
		$user->add(ORM::factory('role','superadmin'));
		$this->assertTrue( Auth::instance()->admin_access($user) );
		
		// Test custom role/permission
		$user = new User_Model();
		$user->add(ORM::factory('role','phpunit'));
		$this->assertFalse( Auth::instance()->admin_access($user) );
		$user->add(ORM::factory('role','phpunit_admin'));
		$this->assertTrue( Auth::instance()->admin_access($user) );
	}
}
	