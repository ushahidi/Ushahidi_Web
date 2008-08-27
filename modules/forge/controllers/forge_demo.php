<?php defined('SYSPATH') or die('No direct script access.');

class Forge_demo_Controller extends Controller {

	public function index()
	{
		$profiler = new Profiler;

		$foods = array
		(
			'tacos' => array('tacos', FALSE),
			'burgers' => array('burgers', FALSE),
			'spaghetti' => array('spaghetti (checked)', TRUE),
			'cookies' => array('cookies (checked)', TRUE),
		);

		$form = new Forge(NULL, 'New User');

		// Create each input, following this format:
		//
		//   type($name)->attr(..)->attr(..);
		//
		$form->hidden('hideme')->value('hiddenz!');
		$form->input('email')->label(TRUE)->rules('required|valid_email');
		$form->input('username')->label(TRUE)->rules('required|length[5,32]');
		$form->password('password')->label(TRUE)->rules('required|length[5,32]');
		$form->password('confirm')->label(TRUE)->matches($form->password);
		$form->checkbox('remember')->label('Remember Me');
		$form->checklist('foods')->label('Favorite Foods')->options($foods)->rules('required');
		$form->dropdown('state')->label('Home State')->options(locale_US::states())->rules('required');
		$form->dateselect('birthday')->label(TRUE)->minutes(15)->years(1950, date('Y'));
		$form->submit('Save');

		if ($form->validate())
		{
			echo Kohana::debug($form->as_array());
		}

		echo $form->html();

		// Using a custom template:
		// echo $form->html('custom_view', TRUE);
		// Inside the view access the inputs using $input_id->html(), ->label() etc
		//
		// To get the errors use $input_id_errors.
		// Set the error format with $form->error_format('<div>{message}</div>');
		// Defaults to <p class="error">{message}</p>
		//
		// Examples:
		//   echo $username->html(); echo $password_errors;
	}

	public function upload()
	{
		$profiler = new Profiler;

		$form = new Forge;
		$form->input('hello')->label(TRUE);
		$form->upload('file')->label(TRUE)->rules('required|size[100KB]|allow[jpg,png,gif]');
		$form->submit('Upload');

		if ($form->validate())
		{
			echo Kohana::debug($form->as_array());
		}

		echo $form->html();
	}

} // End Forge Demo Controller