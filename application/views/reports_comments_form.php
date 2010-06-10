<!-- start submit comments block -->
<a name="comments"></a>
<div class="big-block">
	<div id="comments" class="report_comment">
		<h2>Leave a Comment</h2>
		<?php
			if ($form_error) {
		?>
		<!-- red-box -->
		<div class="red-box">
			<h3>Error!</h3>
			<ul>
				<?php
					foreach ($errors as $error_item => $error_description)
					{
						print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
					}
				?>
			</ul>
		</div>
		<?php
		}
		?>
		<?php print form::open(NULL, array('id' => 'commentForm', 'name' => 'commentForm')); ?>
		<div class="report_row">
			<strong>Name:</strong><br />
			<?php print form::input('comment_author', $form['comment_author'], ' class="text"'); ?>
			</div>

			<div class="report_row">
			<strong>E-Mail:</strong><br />
			<?php print form::input('comment_email', $form['comment_email'], ' class="text"'); ?>
		</div>
		<div class="report_row">
			<strong>Comments:</strong><br />
			<?php print form::textarea('comment_description', $form['comment_description'], ' rows="4" cols="40" class="textarea long" ') ?>
		</div>
		<div class="report_row">
			<strong>Security Code:</strong><br />
			<?php print $captcha->render(); ?><br />
			<?php print form::input('captcha', $form['captcha'], ' class="text"'); ?>
		</div>
		<div class="report_row">
			<input name="submit" type="submit" value="Submit Comment" class="btn_blue" />
		</div>
		<?php print form::close(); ?>
	</div>
</div>
<!-- end submit comments block -->