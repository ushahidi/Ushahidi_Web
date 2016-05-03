<?php
$lang = array(
	'parent_id' => array(
		'numeric' => 'Parent ID must be numeric',
	) ,
	'private_to' => array(
		'required' => 'The To field is required',
		'exists' => 'The user you are try to send a message to does not exist',
	) ,
	'private_subject' => array(
		'required' => 'The subject field is required',
		'length' => 'Subject must be between 3 and 150 characters'
	) ,
	'private_message' => array(
		'required' => 'The message field is required',
	) ,
);
