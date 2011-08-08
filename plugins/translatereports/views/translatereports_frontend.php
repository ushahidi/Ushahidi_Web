<?php foreach($translations as $lang => $translation) { ?>
<div class="content">
	<div><strong>Translation: <em><?php echo isset($locales[$lang]) ? $locales[$lang] : $lang; ?></em></strong></div>
	<div><?php echo $translation; ?></div>
</div>
<?php } ?>