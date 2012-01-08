
<div id="content">
	<div class="content-bg">
		<div style="padding:25px;">

			<h1><?php echo Kohana::lang('ui_main.browse_profiles'); ?></h1>
			<ul>
			<?php foreach($users as $user){ ?>
				<li><a href="<?php echo url::site();?>profile/user/<?php echo $user->username; ?>"><?php echo $user->username; ?></a></li>
			<?php } ?>
			</ul>

		</div>
	</div>
</div>
