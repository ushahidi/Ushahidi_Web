<?php echo $open; ?>
<table class="<?php echo $class ?>">
<?php if ($title != ''): ?>
<caption><?php echo $title ?></caption>
<?php endif ?>
<?php
foreach($inputs as $input):

$sub_inputs = array();
if ($input->type == 'group'):
	$sub_inputs = $input->inputs;

?>
<tr>
<th colspan="2"><?php echo $input->label() ?></th>
</tr>
<?php

	if ($message = $input->message()):

?>
<tr>
<td colspan="2"><p class="group_message"><?php echo $message ?></p></td>
</tr>
<?php

	endif;

else:
	$sub_inputs = array($input);	
endif;

foreach($sub_inputs as $input):

?>
<tr>
<th><?php echo $input->label() ?></th>
<td>
<?php

echo $input->html();

if ($message = $input->message()):

?>
<p class="message"><?php echo $message ?></p>
<?php

endif;

foreach ($input->error_messages() as $error):

?>
<p class="error"><?php echo $error ?></p>
<?php

endforeach;

?>
</td>
</tr>
<?php

endforeach;

endforeach;
?>
</table>
<?php echo $close ?>