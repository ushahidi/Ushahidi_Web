<?php echo "<?xml version=\"1.0\"?>"; ?>
<rss version="2.0">
	<channel>
		<title><?php echo $feed_title; ?></title>
		<link><?php echo $feed_url; ?></link>
		<pubDate><?php echo $feed_date; ?></pubDate>
		<generator>Ushahidi Engine</generator>
		<?php echo $feeds; ?>
	</channel>
</rss>