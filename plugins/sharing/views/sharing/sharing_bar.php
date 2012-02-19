<div class="cat-filters clearingfix" style="margin-top:20px;">
			<strong><?php echo Kohana::lang('ui_main.other_ushahidi_instances');?> <span>[<a href="javascript:toggleLayer('sharing_switch_link','sharing_switch')" id="sharing_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
</div>
		<ul id="sharing_switch" class="category-filters">
			<?php
			
			foreach ($shares as $share => $share_info)
			{
				$sharing_name = $share_info[0];
				$sharing_color = $share_info[1];
				echo '<li><a href="#" id="share_'. $share .'"><div class="swatch" style="background-color:#'.$sharing_color.'"></div>
				<div>'.$sharing_name.'</div></a></li>';
			}
			?>
		</ul>
