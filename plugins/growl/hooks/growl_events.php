<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Growl Events Hook - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module     Growl Hook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class growl_events {
	
	public function __construct()
	{
		Event::add('ushahidi_action.report_add', array($this, 'report_add'));
		Event::add('ushahidi_action.comment_add', array($this, 'comment_add'));
		Event::add('ushahidi_action.report_delete ', array($this, 'report_delete'));
	}
	
	public function report_add(){
		$incident = Event::$data;
		new growl('New Report: '.$incident->incident_title);
	}
	
	public function comment_add(){
		$comment = Event::$data;
		new growl('New Comment on Incident ID '.$comment->incident_id.': '.$comment->comment_author.' ('.$comment->comment_email.') - '.$comment->comment_description);
	}
	
	public function report_delete(){
		// Doesn't work.
		new growl('Report Deleted');
	}
	
}

new growl_events;