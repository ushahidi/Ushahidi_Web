<div class="orig-report">
	<div class="discussion">
		<h5>ADDITIONAL REPORTS AND DISCUSSION&nbsp;&nbsp;&nbsp;(<a href="#comments">Add</a>)</h5>
		<?php
		foreach($incident_comments as $comment)
		{
			echo "<div class=\"discussion-box\">";
			echo "<p><strong>" . $comment->comment_author . "</strong>&nbsp;(" . date('M j Y', strtotime($comment->comment_date)) . ")</p>";
			echo "<p>" . $comment->comment_description . "</p>";
			echo "<div class=\"report_rating\">";
			echo "	<div>";
			echo "	Credibility:&nbsp;";
			echo "	<a href=\"javascript:rating('" . $comment->id . "','add','comment','cloader_" . $comment->id . "')\"><img id=\"cup_" . $comment->id . "\" src=\"" . url::base() . 'media/img/' . "up.png\" alt=\"UP\" title=\"UP\" border=\"0\" /></a>&nbsp;";
			echo "	<a href=\"javascript:rating('" . $comment->id . "','subtract','comment','cloader_" . $comment->id . "')\"><img id=\"cdown_" . $comment->id . "\" src=\"" . url::base() . 'media/img/' . "down.png\" alt=\"DOWN\" title=\"DOWN\" border=\"0\" /></a>&nbsp;";
			echo "	</div>";
			echo "	<div class=\"rating_value\" id=\"crating_" . $comment->id . "\">" . $comment->comment_rating . "</div>";
			echo "	<div id=\"cloader_" . $comment->id . "\" class=\"rating_loading\" ></div>";
			echo "</div>";
			echo "</div>";
		}
		?>
	</div>
</div>