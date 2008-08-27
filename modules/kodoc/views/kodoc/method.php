<?php

// Recreate method declaration
$declaration = implode('', array
(
	$abstract   ? html::anchor('http://www.php.net/manual/language.oop5.abstract.php', 'abstract').' ' : '',
	$final      ? html::anchor('http://www.php.net/manual/language.oop5.final.php', 'final').' ' : '',
	html::anchor('http://www.php.net/manual/language.oop5.visibility.php', $visibility).' ',
	$static     ? html::anchor('http://www.php.net/manual/language.oop5.static.php', 'static').' ' : '',
	'function ',
	$name
));

?>
<h4><?php echo $class ?><?php echo $static ? ' :: ' : ' -> ' ?><?php echo $name ?></h4>

<code class="declaration"><?php echo $declaration ?></code>

<?php

if ( ! empty($comment['about'])):

	echo arr::remove('about', $comment);

endif;
if ( ! empty($parameters)):

?>
<p class="parameters"><strong>Parameters:</strong></p>
<dl>
<?php

	foreach ($parameters as $i => $param):

	if ( ! empty($comment['param'][$i])):

		// Extract the type and information
		list ($type, $info) = explode(' ', $comment['param'][$i], 2);

		$type = Kodoc::humanize_type($type).' ';
		$info = trim($info);

	else:

		$type = '';
		$info = '';

	endif;

?>
<dt><?php echo $type, $param['name'] ?></dt>
<dd><?php

	if (array_key_exists('default', $param)):

		// Parameter default value
		echo '<tt>('.Kodoc::humanize_value($param['default']).')</tt> ';

	endif;

	// Parameter information
	echo $info;

?></dd>

<?php

	endforeach;

	if (isset($comment['param']))
	{
		// Remove parameter information from the comment
		unset($comment['param']);
	}

?>
</dl>
<?php

endif;
if ( ! empty($comment)):
	foreach ($comment as $tag => $vals):

		switch ($tag):
			case 'throws':
			case 'return':
				foreach ($vals as $i => $val):
					if (strpos($val, ' ') !== FALSE):

						// Extract the type from the val
						list ($type, $val) = explode(' ', $val, 2);

						// Add the type to the val
						$val = Kodoc::humanize_type($type).' '.$val;
					else:
						$val = '<tt>'.$val.'</tt>';
					endif;

					// Put the val back into the array
					$vals[$i] = $val;

				endforeach;
			break;
		endswitch;

?>
<p class="<?php echo $tag ?>"><strong><?php echo ucfirst($tag) ?>:</strong><?php


		if (count($vals) === 1):

?> <?php echo current($vals) ?></p>
<?php

		else:

?></p>
<ul>
<li><?php echo implode("</li>\n<li>", $vals) ?></li>
</ul>
<?php

		endif;


	endforeach;
endif;

// echo Kohana::debug($comment);

?>
<hr/>
