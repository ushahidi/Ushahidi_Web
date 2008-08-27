<?php

if ( ! empty($source)):
	list ($option, $value) = $source;

?>
<h4 class="option"><?php echo $option ?></h4>
<?php

endif;

?>
<div class="about">
<?php echo $about ?>
<?php

if ( ! empty($note)):
	foreach ($note as $n):

?>
<p class="note"><?php echo $n ?></p>
<?php

	endforeach;
endif;

?>
</div>
<?php

if ( ! empty($source)):

?>
<p class="value">Default value: <code><?php echo $value ?></code></p>
<?php

endif;

?>