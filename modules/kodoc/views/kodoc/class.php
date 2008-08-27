<?php

// Re-create class declaration
$declaration = implode('', array
(
	$final      ? html::anchor('http://www.php.net/manual/language.oop5.final.php', 'final').' ' : '',
	$abstract   ? html::anchor('http://www.php.net/manual/language.oop5.abstract.php', 'abstract').' ' : '',
	$interface  ? html::anchor('http://www.php.net/manual/language.oop5.interfaces.php', 'interface').' ': '',
	'class ',
	$name,
	$extends    ? ' extends '.$extends : '',
	$implements ? ' implements '.implode(', ', $implements) : '',
));

?>

<h3>Class: <?php echo $name ?></h3>
<code class="declaration"><?php echo $declaration ?></code>

<?php

if ( ! empty($comment['about'])):

	echo $comment['about'];

endif;

if ( ! empty($methods)):

?>
<div class="methods">
<?php

	foreach ($methods as $method):

		echo new View('kodoc/method', $method);

	endforeach;

?>
</div>
<?php

endif;

?>