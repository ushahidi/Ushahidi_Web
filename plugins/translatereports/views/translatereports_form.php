<!-- translatereports -->
<div class="row">
	<h4>Translations <span>Delete translation from textarea to remove it.</span></h4>
</div>

<?php
	$lang_dropdown = '<select id="" name="">';
	foreach($locales as $abbv => $locale) {
		$lang_dropdown .= '<option value="'.$abbv.'">'.$locale.'</option>';
	}
	$lang_dropdown .= '</select>';
?>

<?php
	$i = 0;
	foreach($translations as $translation_id => $item) {
	$lang = $item['lang'];
	// Delete our lang from the list since we are already using it
	$locale_lang = isset($locales[$lang]) ? $locales[$lang] : $lang;
	$lang_dropdown = str_replace('<option value="'.$lang.'">'.$locale_lang.'</option>', '', $lang_dropdown);
?>
	<div class="row"<?php if($i != 0) { ?> style="padding-top:10px;"<?php } ?>>
		<strong><?php echo $locale_lang; ?></strong>
		<textarea name="incident_translation[<?php echo $lang; ?>]" id="incident_translation[<?php echo $lang; ?>]" style="height: 60px;"><?php echo $item['incident_description']; ?></textarea>
	</div>

<?php
	$i++;
	}
	
	$potential_max_new_translations = count($locales) - $i;
	if($potential_max_new_translations < $max_new_translations)
	{
		$max_new_translations = $potential_max_new_translations;
	}
?>

<?php
	$i = 0;
	while($i < $max_new_translations){
?>
		<div id="translation_add_<?php echo $i; ?>" style="display:none;overflow:hidden;clear:both;padding-top:15px;">
			<?php echo str_replace('<select id="" name="">', '<select id="translation_lang['.$i.']" name="translation_lang['.$i.']">', $lang_dropdown); ?>
			<textarea name="incident_translation[<?php echo $i; ?>]" id="incident_translation[<?php echo $i; ?>]" style="height: 60px;"></textarea>
		</div>
<?php
		$i++;
	}
?>

<?php
	$i = 0;
	while($i < $max_new_translations){
?>
		<a href="#" id="translation_toggle_<?php echo $i; ?>" class="new-cat" <?php if($i != 0) { ?>style="display:none;overflow:hidden;clear:both"<?php } ?>>Add New Translation</a>
<?php
		$i++;
	}
?>

<script type="text/javascript">
    $(document).ready(function() {
    
    <?php
		$i = 0;
		while($i < $max_new_translations){
	?>
		$('a#translation_toggle_<?php echo $i; ?>').click(function() {
		    $('#translation_add_<?php echo $i; ?>').toggle(400);
		    $('#translation_toggle_<?php echo $i; ?>').toggle(0);
		    $('#translation_toggle_<?php echo ($i+1); ?>').toggle(0);
		    return false;
		});
	<?php
			$i++;
		}
	?>

	});
</script>



<!-- / translatereports -->