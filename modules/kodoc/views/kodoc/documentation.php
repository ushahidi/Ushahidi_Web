<?php

if (empty($this->kodoc) OR count($docs = $this->kodoc->get()) < 1):

?>
<p><strong>Kodoc not loaded</strong></p>
<?php

return;
endif;

?>

<h2><?php echo $docs['file'] ?></h2>

<?php

if ( ! empty($docs['comments'])):

	foreach ($docs['comments'] as $comment):
		if ($docs['type'] === 'config'):

			echo new View('kodoc/file_config', $comment);

		elseif ( ! empty($comment['about'])):

			echo $comment['about'];

		endif;
	endforeach;
endif;
if ( ! empty($docs['classes'])):

	foreach ($docs['classes'] as $class):

		echo new View('kodoc/class', $class);

	endforeach;
endif;
?>