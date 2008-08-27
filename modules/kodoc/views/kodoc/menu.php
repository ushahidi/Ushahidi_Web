<div id="menu">
<ul>
<?php

foreach (Kodoc::get_files() as $group => $files):

?>
<li class="first<?php echo ($active == $group) ? ' active': '' ?>"><?php echo ucfirst($group) ?><ul>
<?php

foreach ($files as $name => $drivers):

?>
<li><?php echo html::anchor('kodoc/'.$group.'/'.$name, $name) ?>
<?php

if (is_array($drivers)):

?>
<ul class="expanded">
<?php

foreach ($drivers as $driver):

	$file = ($name === $driver) ? $driver : $name.'_'.$driver;

?>
<li><?php echo html::anchor('kodoc/'.$group.'/drivers/'.$file, $driver) ?></li>
<?php

endforeach;

?>
</ul>
<?php

endif;

?>
</li>
<?php

endforeach;

?>
</ul></li>
<?php

endforeach;

?>
<div style="clear:both;"></div>
</div>